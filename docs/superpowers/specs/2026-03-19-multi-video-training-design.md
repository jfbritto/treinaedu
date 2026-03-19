# Multi-Video Training (Modules & Lessons) — Design Spec

## Goal

Transform the current single-video training model into a modular structure with modules, lessons (video/document/text), per-lesson progress tracking, configurable sequential progression, and flexible quiz placement — while maintaining full backward compatibility with existing trainings.

## Architecture

Three new database tables (`training_modules`, `training_lessons`, `lesson_views`) extend the existing training system. Quizzes gain optional module-level attachment. Existing single-video trainings are migrated into a 1-module/1-lesson structure automatically. The admin gets a dynamic builder UI; the employee gets an accordion-based course player.

## Tech Stack

- Laravel 11 (Eloquent, migrations, controllers, form requests)
- Blade + Tailwind CSS + Alpine.js (frontend)
- Existing YouTube/Vimeo player component
- DomPDF (certificate generation)

---

## 1. Data Model

### 1.1 New Table: `training_modules`

| Column | Type | Description |
|--------|------|-------------|
| id | bigIncrements | PK |
| training_id | foreignId → trainings | cascade on delete |
| title | string | Module name |
| description | text, nullable | Module description |
| order | unsignedInteger | Display order (1-based) |
| is_sequential | boolean, default: true | Whether lessons must be completed in order |
| timestamps | | |

**Index:** `[training_id, order]`

### 1.2 New Table: `training_lessons`

| Column | Type | Description |
|--------|------|-------------|
| id | bigIncrements | PK |
| module_id | foreignId → training_modules | cascade on delete |
| title | string | Lesson name |
| type | enum: 'video', 'document', 'text' | Content type |
| video_url | string(500), nullable | Video URL (when type=video) |
| video_provider | enum: 'youtube','vimeo', nullable | Provider (when type=video) |
| content | text, nullable | Rich text content (when type=text) |
| file_path | string, nullable | Uploaded file path (when type=document) |
| duration_minutes | unsignedInteger, default: 0 | Lesson duration |
| order | unsignedInteger | Order within module (1-based) |
| timestamps | | |

**Index:** `[module_id, order]`

### 1.3 New Table: `lesson_views`

| Column | Type | Description |
|--------|------|-------------|
| id | bigIncrements | PK |
| lesson_id | foreignId → training_lessons | cascade on delete |
| user_id | foreignId → users | cascade on delete |
| company_id | foreignId → companies | cascade on delete |
| progress_percent | unsignedTinyInteger, default: 0 | 0-100 |
| started_at | timestamp, nullable | First access |
| completed_at | timestamp, nullable | When progress reached threshold |
| timestamps | | |

**Unique:** `[lesson_id, user_id]`
**Index:** `[company_id, user_id]`

### 1.4 Alterations to Existing Tables

**`trainings`** — add columns:
- `duration_minutes_override` (unsignedInteger, nullable) — manual override for total duration; when null, sum of all lesson durations is used
- `is_sequential` (boolean, default: true) — whether modules must be completed in order

**`trainings`** — make nullable:
- `video_url` → nullable (new trainings use lessons)
- `video_provider` → nullable

**`quizzes`** — add column:
- `module_id` (foreignId → training_modules, nullable) — when null: quiz applies to entire training; when set: quiz applies to that module only

### 1.5 Relationships

```
Training hasMany TrainingModule (ordered by `order`)
Training hasOne Quiz (where module_id is null) — training-level quiz
Training hasMany Certificate

TrainingModule belongsTo Training
TrainingModule hasMany TrainingLesson (ordered by `order`)
TrainingModule hasOne Quiz (where module_id = this.id)

TrainingLesson belongsTo TrainingModule
TrainingLesson hasMany LessonView

LessonView belongsTo TrainingLesson
LessonView belongsTo User

Quiz optionally belongsTo TrainingModule (nullable)
```

### 1.6 Duration Calculation

```php
// In Training model
public function calculatedDuration(): int
{
    return $this->duration_minutes_override
        ?? $this->modules()->with('lessons')->get()
            ->flatMap->lessons->sum('duration_minutes');
}
```

---

## 2. Data Migration (Backward Compatibility)

A data migration converts every existing training (that has `video_url`) into the new structure:

```
For each Training where video_url is not null:
  1. Create TrainingModule:
     - training_id = training.id
     - title = training.title
     - order = 1
     - is_sequential = true
  2. Create TrainingLesson:
     - module_id = new module's id
     - title = training.title
     - type = 'video'
     - video_url = training.video_url
     - video_provider = training.video_provider
     - duration_minutes = training.duration_minutes
     - order = 1
  3. For each TrainingView of this training:
     Create LessonView:
     - lesson_id = new lesson's id
     - user_id = training_view.user_id
     - company_id = training_view.company_id
     - progress_percent = training_view.progress_percent
     - started_at = training_view.started_at
     - completed_at = training_view.completed_at
  4. If training has a Quiz (where module_id is null):
     - Keep as-is (training-level quiz, unchanged)
```

This migration is **non-destructive** — original columns (`video_url`, `video_provider`, `duration_minutes`) remain but become unused for migrated trainings. New trainings will leave them null.

---

## 3. Progress Tracking

### 3.1 Per-Lesson Progress

**Video lessons:** Same mechanism as today — frontend posts to `/api/lesson-progress` with `lesson_id` and `progress_percent`. Uses `GREATEST()` SQL to never decrease.

**Document lessons:** Marked 100% when the user opens/downloads the file. Single POST on first access.

**Text lessons:** Marked 100% when the user has viewed the content (frontend tracks scroll-to-bottom or 30-second minimum viewing time, then POSTs).

### 3.2 Lesson Completion

A lesson is considered complete when `lesson_views.progress_percent >= 90` (for video) or `= 100` (for document/text). `completed_at` is set at that point.

### 3.3 Module Progress

Calculated on-the-fly (not stored):
```
module_progress = average(lesson.progress_percent for each lesson in module)
module_completed = all lessons completed + module quiz passed (if exists)
```

### 3.4 Training Progress

`training_views.progress_percent` is updated whenever a `lesson_view` changes:
```
training_progress = average(module_progress for each module)
```

`training_views.completed_at` is set when:
- All modules are completed (all lessons + module quizzes)
- Training-level quiz passed (if exists)

### 3.5 Sequential Unlocking

**Module level (`trainings.is_sequential`):**
- Module N is locked until Module N-1 is completed (all lessons + quiz)
- Module 1 is always unlocked

**Lesson level (`training_modules.is_sequential`):**
- Lesson N is locked until Lesson N-1 is completed (progress >= 90% for video, 100% for others)
- Lesson 1 is always unlocked

**When `is_sequential = false`:** all items are unlocked from the start.

### 3.6 API Changes

**New endpoint:** `POST /api/lesson-progress`
```json
Request:  { "lesson_id": 42, "progress_percent": 75 }
Response: { "progress_percent": 75, "lesson_completed": false, "module_progress": 60, "training_progress": 45 }
```

**Existing endpoint** `POST /api/training-progress` — deprecated but still functional for backward compatibility during transition.

---

## 4. Admin Flow

### 4.1 Training Create/Edit — Module Builder

The current single-form becomes a multi-section builder:

**Section 1: Training basics**
- Title, description (same as today)
- Duration override (optional — "leave blank to auto-calculate from lessons")
- Sequential modules toggle

**Section 2: Module builder** (dynamic, Alpine.js)
- "Add Module" button
- Each module card:
  - Module title, description (collapsible)
  - Sequential lessons toggle
  - "Add Lesson" button
  - Lesson list (draggable or up/down arrows for reorder):
    - Lesson title
    - Type selector (Video | Document | Text)
    - Type-specific fields (URL, file upload, or text editor)
    - Duration field
  - "Add Quiz to this Module" toggle → quiz builder (same as today)
- Modules are reorderable (drag or arrows)

**Section 3: Training-level quiz** (optional)
- Same quiz builder as today
- Appears at the bottom, after all modules

### 4.2 Validation Rules

- At least 1 module with at least 1 lesson
- Video lessons: valid YouTube or Vimeo URL
- Document lessons: max 10MB, allowed types (pdf, pptx, docx)
- Text lessons: content cannot be empty
- Quiz: at least 1 question with at least 2 options (1 correct)

### 4.3 Storage

Form submitted as a single POST with nested arrays:
```
modules[0][title] = "Introdução"
modules[0][is_sequential] = true
modules[0][lessons][0][title] = "Boas-vindas"
modules[0][lessons][0][type] = "video"
modules[0][lessons][0][video_url] = "https://youtube.com/..."
modules[0][lessons][0][duration_minutes] = 10
...
```

Server processes in a transaction: create/update modules → create/update lessons → handle quiz changes.

---

## 5. Employee Flow

### 5.1 Training Show Page — Course Player

**Layout:** Two-column (sidebar + content area)

**Left sidebar (collapsible on mobile):**
```
Training Title
Progress: 45% [========-------]

▼ Module 1: Introdução (3/3 complete)
  ✓ 1.1 Boas-vindas [video] 5min
  ✓ 1.2 Material de apoio [doc]
  ✓ 1.3 Visão geral [text]
  ✓ Quiz do módulo — Aprovado

▼ Module 2: Prática (1/4 complete)
  ✓ 2.1 Demonstração [video] 15min
  ► 2.2 Exercício prático [video] 20min  ← current
  🔒 2.3 Revisão [text]
  🔒 2.4 Estudo de caso [doc]
  🔒 Quiz do módulo

▶ Module 3: Avaliação (locked)
  🔒 3.1 Preparação [text]
  🔒 3.2 Prova final [video] 10min
  🔒 Quiz final
```

**Right content area:**
- Renders current lesson based on type:
  - Video: existing YouTube/Vimeo player component
  - Document: embedded PDF viewer + download button
  - Text: rendered HTML content
- Progress bar below content (for video lessons)
- "Next Lesson →" button when current lesson is complete
- "Take Module Quiz" button when all module lessons are done
- "Complete Training" button when everything is done

### 5.2 Navigation

- Clicking a lesson in sidebar loads it in the content area (SPA-like with Alpine.js, or page reload — simpler)
- Locked lessons show tooltip: "Complete the previous lesson first"
- Completed lessons show green checkmark

### 5.3 Completion Flow

```
1. Employee completes all lessons in all modules
2. Passes all module quizzes (if any)
3. Passes training-level quiz (if any)
4. "Complete Training ✓" button appears
5. Click → training_views.completed_at = now()
6. Certificate generation unlocked
```

---

## 6. Certificate Changes

### 6.1 PDF Template

Add a "Conteúdo Programático" section after the main certificate body:

```
Conteúdo Programático:
  • Módulo 1: Introdução (3 aulas)
  • Módulo 2: Prática (4 aulas + avaliação)
  • Módulo 3: Avaliação Final (2 aulas + avaliação)

Carga horária total: 2h 30min
```

### 6.2 Public Share Page

Same addition — list modules below the certificate visual.

### 6.3 Generation Logic

No conceptual change:
```php
canGenerate(User $user, Training $training): bool
{
    // Training must be completed
    $completed = training_views.completed_at is not null;

    // All module quizzes must be passed
    $moduleQuizzesPassed = every module quiz has a passed attempt;

    // Training quiz must be passed (if exists)
    $trainingQuizPassed = !has_quiz || quiz_attempt.passed;

    return $completed && $moduleQuizzesPassed && $trainingQuizPassed;
}
```

---

## 7. File Structure (New/Modified)

### New Files
- `database/migrations/xxxx_create_training_modules_table.php`
- `database/migrations/xxxx_create_training_lessons_table.php`
- `database/migrations/xxxx_create_lesson_views_table.php`
- `database/migrations/xxxx_add_module_support_to_trainings.php`
- `database/migrations/xxxx_add_module_id_to_quizzes.php`
- `database/migrations/xxxx_migrate_existing_trainings_to_modules.php`
- `app/Models/TrainingModule.php`
- `app/Models/TrainingLesson.php`
- `app/Models/LessonView.php`
- `app/Services/LessonProgressService.php`
- `app/Http/Controllers/Api/LessonProgressController.php`
- `resources/views/employee/trainings/show.blade.php` (major rewrite)
- `resources/views/admin/trainings/create.blade.php` (major rewrite)
- `resources/views/admin/trainings/edit.blade.php` (major rewrite)
- `resources/views/components/ui/lesson-player.blade.php`
- `resources/views/components/ui/course-sidebar.blade.php`

### Modified Files
- `app/Models/Training.php` — new relationships, calculatedDuration()
- `app/Models/Quiz.php` — add module relationship
- `app/Services/VideoProgressService.php` — adapt or deprecate
- `app/Services/CertificateService.php` — update canGenerate, add module data to PDF
- `app/Http/Controllers/Admin/TrainingController.php` — rewrite store/update for modules
- `app/Http/Controllers/Employee/TrainingController.php` — rewrite show, complete
- `resources/views/certificates/template.blade.php` — add module listing
- `resources/views/certificates/show.blade.php` — add module listing
- `routes/web.php` or `routes/api.php` — new lesson-progress route

---

## 8. Out of Scope

- SCORM/xAPI integration
- Video hosting (continues using YouTube/Vimeo)
- Live/synchronous lessons
- Discussion forums per lesson
- Gamification (badges, points)
- Instructor-specific views for module management (uses admin views)
