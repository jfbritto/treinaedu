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
| training_id | foreignId → trainings | on delete cascade (via model event, not DB — Training uses SoftDeletes) |
| title | string | Module name |
| description | text, nullable | Module description |
| sort_order | unsignedInteger | Display order (1-based) |
| is_sequential | boolean, default: true | Whether lessons must be completed in order |
| timestamps | | |

**Index:** `[training_id, sort_order]`

**Note:** No `company_id` column. Multi-tenant isolation is enforced through the Training relationship (Training has `company_id` + `BelongsToCompany` trait). All module queries must go through `$training->modules()`.

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
| file_path | string, nullable | Uploaded file path in `public` disk (when type=document) |
| duration_minutes | unsignedInteger, default: 0 | Lesson duration |
| sort_order | unsignedInteger | Order within module (1-based) |
| timestamps | | |

**Index:** `[module_id, sort_order]`

**Note:** No `company_id`. Isolation via TrainingModule → Training chain. Document uploads stored on `Storage::disk('public')` under `lessons/{company_id}/`. When a lesson is deleted, its file is cleaned up via model `deleting` event.

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

**Note:** Uses `BelongsToCompany` trait for multi-tenant global scope.

### 1.4 Alterations to Existing Tables

**`trainings`** — add columns:
- `duration_minutes_override` (unsignedInteger, nullable) — manual override for total duration; when null, sum of all lesson durations is used
- `is_sequential` (boolean, default: true) — whether modules must be completed in order

**`trainings`** — make nullable:
- `video_url` → nullable (new trainings use lessons)
- `video_provider` → nullable

**`quizzes`** — changes:
- Add `module_id` (foreignId → training_modules, nullable) — when null: quiz applies to entire training; when set: quiz applies to that module only
- **Remove** the existing `UNIQUE` constraint on `training_id` — a training can now have multiple quizzes (one per module + one training-level)
- Add composite unique: `[training_id, module_id]` where module_id is nullable (ensures max one quiz per module and one training-level quiz)

### 1.5 Relationships

```
Training hasMany TrainingModule (ordered by `sort_order`)
Training hasMany Quiz — all quizzes (module-level + training-level)
Training hasOne Quiz (scope: where module_id is null) — via trainingQuiz() method
Training hasMany Certificate
Training hasManyThrough TrainingLesson (via TrainingModule) — via lessons() method

TrainingModule belongsTo Training
TrainingModule hasMany TrainingLesson (ordered by `sort_order`)
TrainingModule hasOne Quiz (where module_id = this.id)

TrainingLesson belongsTo TrainingModule
TrainingLesson hasMany LessonView

LessonView belongsTo TrainingLesson
LessonView belongsTo User

Quiz belongsTo Training
Quiz optionally belongsTo TrainingModule (nullable)
```

**Changes to Training model:**
- `hasOne Quiz` → `hasMany Quiz` (renamed to `quizzes()`)
- New `trainingQuiz()` → `hasOne(Quiz::class)->whereNull('module_id')` for the training-level quiz
- The `has_quiz` boolean on `trainings` table is **kept** but its meaning changes to: "this training has at least one quiz (module or training level)". Updated automatically when quizzes are added/removed.

### 1.6 Duration Calculation

```php
// In Training model — uses hasManyThrough for efficient query
public function calculatedDuration(): int
{
    return $this->duration_minutes_override
        ?? $this->lessons()->sum('duration_minutes');
}

// hasManyThrough relationship
public function lessons(): HasManyThrough
{
    return $this->hasManyThrough(TrainingLesson::class, TrainingModule::class, 'training_id', 'module_id');
}
```

### 1.7 Soft Delete Handling

Training uses `SoftDeletes`. Since DB-level `CASCADE ON DELETE` doesn't fire on soft deletes, the following approach is used:

```php
// In Training model — boot method or observer
protected static function booted()
{
    static::deleting(function (Training $training) {
        if ($training->isForceDeleting()) {
            // Hard delete: DB cascade handles it
            return;
        }
        // Soft delete: manually delete children
        // Modules, lessons, lesson_views cascade via DB when modules are force-deleted
        // But since we soft-delete training, we just leave modules/lessons intact
        // They become inaccessible because Training scope filters them out
    });
}
```

Since `training_modules` and `training_lessons` don't have `SoftDeletes` and are only accessed through `$training->modules()`, a soft-deleted Training naturally hides all its modules and lessons. No additional handling needed.

---

## 2. Data Migration (Backward Compatibility)

A data migration converts every existing training into the new structure:

```
For each Training (all have video_url since column was NOT NULL):
  1. Create TrainingModule:
     - training_id = training.id
     - title = training.title
     - sort_order = 1
     - is_sequential = true
  2. Create TrainingLesson:
     - module_id = new module's id
     - title = training.title
     - type = 'video'
     - video_url = training.video_url
     - video_provider = training.video_provider
     - duration_minutes = training.duration_minutes
     - sort_order = 1
  3. For each TrainingView of this training:
     Create LessonView:
     - lesson_id = new lesson's id
     - user_id = training_view.user_id
     - company_id = training_view.company_id
     - progress_percent = training_view.progress_percent
     - started_at = training_view.started_at
     - completed_at = training_view.completed_at
  4. If training has a Quiz:
     - Keep as-is (training-level quiz with module_id = null)
```

This migration is **non-destructive** — original columns (`video_url`, `video_provider`, `duration_minutes`) remain but become unused for migrated trainings. New trainings will leave them null.

**UX transition:** After migration, ALL trainings use the module builder UI when editing. There is no "legacy mode" — the migrated 1-module/1-lesson structure appears in the builder and can be expanded by the admin.

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

`training_views.progress_percent` is updated by `LessonProgressService` whenever a `lesson_view` changes:

```php
// In LessonProgressService, after updating a lesson_view:
public function recalculateTrainingProgress(Training $training, User $user): void
{
    $lessons = $training->lessons; // hasManyThrough
    $lessonViews = LessonView::where('user_id', $user->id)
        ->whereIn('lesson_id', $lessons->pluck('id'))
        ->get()
        ->keyBy('lesson_id');

    $totalProgress = $lessons->avg(fn ($lesson) => $lessonViews[$lesson->id]->progress_percent ?? 0);

    TrainingView::updateOrCreate(
        ['training_id' => $training->id, 'user_id' => $user->id],
        ['progress_percent' => (int) round($totalProgress), 'company_id' => $user->company_id]
    );
}
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

### 3.7 Data Loading for Course Player

The course player page (`employee/trainings/show`) is **server-rendered via Blade**. The controller loads all data in a single query and passes it to the view:

```php
$training->load([
    'modules.lessons',
    'modules.quiz',
    'trainingQuiz',
    'modules.lessons.lessonViews' => fn($q) => $q->where('user_id', auth()->id()),
]);
```

No additional API endpoint needed for listing modules/lessons — all state is embedded in the Blade template and managed by Alpine.js for interactivity (accordion, navigation, progress updates).

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
  - Lesson list (up/down arrows for reorder):
    - Lesson title
    - Type selector (Video | Document | Text)
    - Type-specific fields (URL, file upload, or text editor)
    - Duration field
  - "Add Quiz to this Module" toggle → quiz builder (same as today)
- Modules are reorderable (up/down arrows)

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

### 4.4 Instructor Support

The `InstructorTrainingController` follows the same module builder pattern as the admin. Instructors can create/edit trainings with modules and lessons within their scope. The views are shared (same Blade components).

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

- Clicking a lesson in sidebar loads the page with `?lesson={id}` query param (full page reload for simplicity)
- Locked lessons show tooltip: "Complete a aula anterior primeiro"
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

### 5.4 Quiz Access Changes

The `QuizController` is updated to handle module-level quizzes:
- **Module quiz:** accessible when all lessons in that module are completed
- **Training quiz:** accessible when all modules are completed (all lessons + all module quizzes passed)
- Route changes: `GET /employee/trainings/{training}/quiz?module={module_id}` for module quizzes, existing route for training quiz

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

Updated to check module quizzes:
```php
canGenerate(User $user, Training $training): bool
{
    // Training must be completed
    $completed = TrainingView where completed_at is not null;

    // All module quizzes must be passed (if any exist)
    $moduleQuizzes = Quiz::where('training_id', $training->id)->whereNotNull('module_id')->get();
    $moduleQuizzesPassed = $moduleQuizzes->every(fn($quiz) =>
        $user->quizAttempts()->where('quiz_id', $quiz->id)->where('passed', true)->exists()
    );

    // Training-level quiz must be passed (if exists)
    $trainingQuiz = Quiz::where('training_id', $training->id)->whereNull('module_id')->first();
    $trainingQuizPassed = !$trainingQuiz || $user->quizAttempts()->where('quiz_id', $trainingQuiz->id)->where('passed', true)->exists();

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
- `database/migrations/xxxx_modify_quizzes_for_modules.php` (add module_id, drop unique on training_id, add composite unique)
- `database/migrations/xxxx_migrate_existing_trainings_to_modules.php`
- `app/Models/TrainingModule.php`
- `app/Models/TrainingLesson.php`
- `app/Models/LessonView.php` (with `BelongsToCompany` trait)
- `app/Services/LessonProgressService.php`
- `app/Http/Controllers/Api/LessonProgressController.php`
- `resources/views/employee/trainings/show.blade.php` (major rewrite)
- `resources/views/admin/trainings/create.blade.php` (major rewrite)
- `resources/views/admin/trainings/edit.blade.php` (major rewrite)
- `resources/views/components/ui/lesson-player.blade.php`
- `resources/views/components/ui/course-sidebar.blade.php`

### Modified Files
- `app/Models/Training.php` — new relationships (modules, lessons, quizzes, trainingQuiz), calculatedDuration(), hasManyThrough
- `app/Models/Quiz.php` — add module_id to fillable, add module() relationship
- `app/Services/VideoProgressService.php` — deprecate, replaced by LessonProgressService
- `app/Services/CertificateService.php` — update canGenerate for module quizzes, pass module data to PDF
- `app/Http/Controllers/Admin/TrainingController.php` — rewrite store/update for modules/lessons
- `app/Http/Controllers/Instructor/TrainingController.php` — same module builder support
- `app/Http/Controllers/Employee/TrainingController.php` — rewrite show (course player), complete
- `app/Http/Controllers/Employee/QuizController.php` — handle module-level quiz access
- `resources/views/certificates/template.blade.php` — add module listing
- `resources/views/certificates/show.blade.php` — add module listing
- `routes/web.php` — quiz route changes
- `routes/api.php` — new lesson-progress route

---

## 8. Reports Impact

The existing `ReportController` shows training completion data. With the new structure:
- Training-level reports remain the same (completion rate, progress)
- No per-module/per-lesson reporting in this iteration
- Future enhancement: drill-down into module/lesson completion rates

---

## 9. Out of Scope

- SCORM/xAPI integration
- Video hosting (continues using YouTube/Vimeo)
- Live/synchronous lessons
- Discussion forums per lesson
- Gamification (badges, points)
- Per-module/per-lesson reporting (future enhancement)
- Drag & drop reorder (uses up/down arrows for simplicity)
