# Multi-Video Training (Modules & Lessons) Implementation Plan

> **For agentic workers:** REQUIRED: Use superpowers:subagent-driven-development (if subagents available) or superpowers:executing-plans to implement this plan. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Transform single-video trainings into modular courses with modules, multi-type lessons, per-lesson progress, configurable sequential progression, and flexible quiz placement.

**Architecture:** Three new tables (`training_modules`, `training_lessons`, `lesson_views`) extend the existing training system. Existing trainings are auto-migrated to 1-module/1-lesson. Admin gets a dynamic module builder; employee gets a course player with accordion sidebar.

**Tech Stack:** Laravel 11, Blade, Tailwind CSS, Alpine.js, MySQL 8, DomPDF

**Spec:** `docs/superpowers/specs/2026-03-19-multi-video-training-design.md`

---

## Task 1: Database Migrations — New Tables

**Files:**
- Create: `database/migrations/2026_03_19_000001_create_training_modules_table.php`
- Create: `database/migrations/2026_03_19_000002_create_training_lessons_table.php`
- Create: `database/migrations/2026_03_19_000003_create_lesson_views_table.php`

- [ ] **Step 1: Create training_modules migration**

```php
<?php
// database/migrations/2026_03_19_000001_create_training_modules_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(1);
            $table->boolean('is_sequential')->default(true);
            $table->timestamps();
            $table->index(['training_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_modules');
    }
};
```

- [ ] **Step 2: Create training_lessons migration**

```php
<?php
// database/migrations/2026_03_19_000002_create_training_lessons_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('training_modules')->cascadeOnDelete();
            $table->string('title');
            $table->enum('type', ['video', 'document', 'text']);
            $table->string('video_url', 500)->nullable();
            $table->enum('video_provider', ['youtube', 'vimeo'])->nullable();
            $table->text('content')->nullable();
            $table->string('file_path')->nullable();
            $table->unsignedInteger('duration_minutes')->default(0);
            $table->unsignedInteger('sort_order')->default(1);
            $table->timestamps();
            $table->index(['module_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_lessons');
    }
};
```

- [ ] **Step 3: Create lesson_views migration**

```php
<?php
// database/migrations/2026_03_19_000003_create_lesson_views_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('training_lessons')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('progress_percent')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['lesson_id', 'user_id']);
            $table->index(['company_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_views');
    }
};
```

- [ ] **Step 4: Run migrations**

Run: `php artisan migrate`
Expected: 3 tables created successfully

- [ ] **Step 5: Commit**

```bash
git add database/migrations/2026_03_19_00000*
git commit -m "feat: create training_modules, training_lessons, lesson_views tables"
```

---

## Task 2: Database Migrations — Alter Existing Tables

**Files:**
- Create: `database/migrations/2026_03_19_000004_add_module_support_to_trainings_table.php`
- Create: `database/migrations/2026_03_19_000005_modify_quizzes_for_modules.php`

- [ ] **Step 1: Create trainings alteration migration**

```php
<?php
// database/migrations/2026_03_19_000004_add_module_support_to_trainings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->unsignedInteger('duration_minutes_override')->nullable()->after('duration_minutes');
            $table->boolean('is_sequential')->default(true)->after('active');
            $table->string('video_url', 500)->nullable()->change();
            $table->enum('video_provider', ['youtube', 'vimeo'])->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->dropColumn(['duration_minutes_override', 'is_sequential']);
            $table->string('video_url', 500)->nullable(false)->change();
            $table->enum('video_provider', ['youtube', 'vimeo'])->nullable(false)->change();
        });
    }
};
```

**Note:** This migration requires `doctrine/dbal` for column changes. If not installed, run `composer require doctrine/dbal` first.

- [ ] **Step 2: Create quizzes modification migration**

```php
<?php
// database/migrations/2026_03_19_000005_modify_quizzes_for_modules.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            // Remove unique constraint on training_id (allows multiple quizzes per training)
            $table->dropUnique(['training_id']);

            // Add module_id (nullable = training-level quiz)
            $table->foreignId('module_id')->nullable()->after('training_id')
                ->constrained('training_modules')->nullOnDelete();

            // Composite unique: one quiz per module, one training-level quiz per training
            $table->unique(['training_id', 'module_id']);
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropUnique(['training_id', 'module_id']);
            $table->dropConstrainedForeignId('module_id');
            $table->unique('training_id');
        });
    }
};
```

- [ ] **Step 3: Run migrations**

Run: `php artisan migrate`
Expected: 2 tables altered successfully

- [ ] **Step 4: Commit**

```bash
git add database/migrations/2026_03_19_00000[45]*
git commit -m "feat: add module support columns to trainings and quizzes tables"
```

---

## Task 3: Data Migration — Convert Existing Trainings

**Files:**
- Create: `database/migrations/2026_03_19_000006_migrate_existing_trainings_to_modules.php`

- [ ] **Step 1: Create data migration**

```php
<?php
// database/migrations/2026_03_19_000006_migrate_existing_trainings_to_modules.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $trainings = DB::table('trainings')->whereNotNull('video_url')->get();

        foreach ($trainings as $training) {
            // Create module
            $moduleId = DB::table('training_modules')->insertGetId([
                'training_id' => $training->id,
                'title' => $training->title,
                'sort_order' => 1,
                'is_sequential' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create lesson
            $lessonId = DB::table('training_lessons')->insertGetId([
                'module_id' => $moduleId,
                'title' => $training->title,
                'type' => 'video',
                'video_url' => $training->video_url,
                'video_provider' => $training->video_provider,
                'duration_minutes' => $training->duration_minutes,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Migrate training views to lesson views
            $views = DB::table('training_views')
                ->where('training_id', $training->id)
                ->get();

            foreach ($views as $view) {
                DB::table('lesson_views')->insert([
                    'lesson_id' => $lessonId,
                    'user_id' => $view->user_id,
                    'company_id' => $view->company_id,
                    'progress_percent' => $view->progress_percent,
                    'started_at' => $view->started_at,
                    'completed_at' => $view->completed_at,
                    'created_at' => $view->created_at,
                    'updated_at' => $view->updated_at,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Data migration — truncate new tables to reverse
        DB::table('lesson_views')->truncate();
        DB::table('training_lessons')->truncate();
        DB::table('training_modules')->truncate();
    }
};
```

- [ ] **Step 2: Run migration**

Run: `php artisan migrate`
Expected: Existing trainings converted to modules/lessons with progress preserved

- [ ] **Step 3: Verify data migration**

Run: `php artisan tinker --execute="echo 'Modules: ' . DB::table('training_modules')->count() . ', Lessons: ' . DB::table('training_lessons')->count() . ', Views: ' . DB::table('lesson_views')->count();"`
Expected: Counts should match number of trainings and training_views

- [ ] **Step 4: Commit**

```bash
git add database/migrations/2026_03_19_000006*
git commit -m "feat: migrate existing single-video trainings to module/lesson structure"
```

---

## Task 4: Eloquent Models — TrainingModule, TrainingLesson, LessonView

**Files:**
- Create: `app/Models/TrainingModule.php`
- Create: `app/Models/TrainingLesson.php`
- Create: `app/Models/LessonView.php`

- [ ] **Step 1: Create TrainingModule model**

```php
<?php
// app/Models/TrainingModule.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TrainingModule extends Model
{
    protected $fillable = [
        'training_id', 'title', 'description', 'sort_order', 'is_sequential',
    ];

    protected function casts(): array
    {
        return [
            'is_sequential' => 'boolean',
        ];
    }

    public function training(): BelongsTo
    {
        return $this->belongsTo(Training::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(TrainingLesson::class, 'module_id')->orderBy('sort_order');
    }

    public function quiz(): HasOne
    {
        return $this->hasOne(Quiz::class, 'module_id');
    }
}
```

- [ ] **Step 2: Create TrainingLesson model**

```php
<?php
// app/Models/TrainingLesson.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingLesson extends Model
{
    protected $fillable = [
        'module_id', 'title', 'type', 'video_url', 'video_provider',
        'content', 'file_path', 'duration_minutes', 'sort_order',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(TrainingModule::class, 'module_id');
    }

    public function lessonViews(): HasMany
    {
        return $this->hasMany(LessonView::class, 'lesson_id');
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    public function isDocument(): bool
    {
        return $this->type === 'document';
    }

    public function isText(): bool
    {
        return $this->type === 'text';
    }

    public function completionThreshold(): int
    {
        return $this->isVideo() ? 90 : 100;
    }

    public static function detectProvider(string $url): string
    {
        return Training::detectProvider($url);
    }
}
```

- [ ] **Step 3: Create LessonView model**

```php
<?php
// app/Models/LessonView.php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonView extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'lesson_id', 'user_id', 'company_id',
        'progress_percent', 'started_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(TrainingLesson::class, 'lesson_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

- [ ] **Step 4: Commit**

```bash
git add app/Models/TrainingModule.php app/Models/TrainingLesson.php app/Models/LessonView.php
git commit -m "feat: add TrainingModule, TrainingLesson, LessonView Eloquent models"
```

---

## Task 5: Update Existing Models — Training, Quiz

**Files:**
- Modify: `app/Models/Training.php`
- Modify: `app/Models/Quiz.php`

- [ ] **Step 1: Update Training model with new relationships**

Add these to `app/Models/Training.php`:

Add to `$fillable` array: `'duration_minutes_override', 'is_sequential'`

Add to `casts()`: `'is_sequential' => 'boolean'`

Add new relationships and methods:

```php
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

public function modules(): HasMany
{
    return $this->hasMany(TrainingModule::class)->orderBy('sort_order');
}

public function lessons(): HasManyThrough
{
    return $this->hasManyThrough(
        TrainingLesson::class,
        TrainingModule::class,
        'training_id', // FK on training_modules
        'module_id',   // FK on training_lessons
    );
}

// Rename existing quiz() to quizzes() for all quizzes
public function quizzes(): HasMany
{
    return $this->hasMany(Quiz::class);
}

// Training-level quiz only (module_id is null)
public function trainingQuiz(): HasOne
{
    return $this->hasOne(Quiz::class)->whereNull('module_id');
}

public function calculatedDuration(): int
{
    return $this->duration_minutes_override
        ?? $this->lessons()->sum('duration_minutes');
}
```

**Important:** Keep the existing `quiz()` method but update it:
```php
// Keep for backward compatibility during transition
public function quiz(): HasOne
{
    return $this->hasOne(Quiz::class)->whereNull('module_id');
}
```

- [ ] **Step 2: Update Quiz model**

In `app/Models/Quiz.php`, add `'module_id'` to `$fillable` and add the module relationship:

```php
protected $fillable = ['training_id', 'company_id', 'module_id'];

public function module()
{
    return $this->belongsTo(TrainingModule::class, 'module_id');
}
```

- [ ] **Step 3: Commit**

```bash
git add app/Models/Training.php app/Models/Quiz.php
git commit -m "feat: add module/lesson relationships to Training and Quiz models"
```

---

## Task 6: LessonProgressService

**Files:**
- Create: `app/Services/LessonProgressService.php`
- Create: `app/Http/Controllers/Api/LessonProgressController.php`
- Modify: `routes/api.php`

- [ ] **Step 1: Create LessonProgressService**

```php
<?php
// app/Services/LessonProgressService.php

namespace App\Services;

use App\Models\LessonView;
use App\Models\Training;
use App\Models\TrainingLesson;
use App\Models\TrainingView;
use Illuminate\Support\Facades\DB;

class LessonProgressService
{
    public function updateProgress(int $lessonId, int $userId, int $companyId, int $percent): LessonView
    {
        $cappedPercent = min($percent, 100);

        DB::table('lesson_views')->updateOrInsert(
            ['lesson_id' => $lessonId, 'user_id' => $userId],
            [
                'company_id' => $companyId,
                'started_at' => DB::raw('COALESCE(started_at, NOW())'),
                'progress_percent' => DB::raw("GREATEST(COALESCE(progress_percent, 0), {$cappedPercent})"),
                'updated_at' => now(),
                'created_at' => DB::raw('COALESCE(created_at, NOW())'),
            ]
        );

        $lessonView = LessonView::withoutGlobalScope('company')
            ->where('lesson_id', $lessonId)
            ->where('user_id', $userId)
            ->first();

        // Auto-complete lesson if threshold reached
        $lesson = TrainingLesson::find($lessonId);
        if ($lesson && !$lessonView->completed_at && $lessonView->progress_percent >= $lesson->completionThreshold()) {
            $lessonView->update(['completed_at' => now()]);
        }

        // Recalculate training progress
        $training = $lesson->module->training;
        $this->recalculateTrainingProgress($training, $userId, $companyId);

        return $lessonView->fresh();
    }

    public function recalculateTrainingProgress(Training $training, int $userId, int $companyId): void
    {
        $lessonIds = $training->lessons()->pluck('training_lessons.id');

        if ($lessonIds->isEmpty()) {
            return;
        }

        $avgProgress = LessonView::withoutGlobalScope('company')
            ->where('user_id', $userId)
            ->whereIn('lesson_id', $lessonIds)
            ->avg('progress_percent') ?? 0;

        TrainingView::withoutGlobalScope('company')->updateOrCreate(
            ['training_id' => $training->id, 'user_id' => $userId],
            [
                'company_id' => $companyId,
                'progress_percent' => (int) round($avgProgress),
                'started_at' => DB::raw('COALESCE(started_at, NOW())'),
            ]
        );
    }
}
```

- [ ] **Step 2: Create LessonProgressController**

```php
<?php
// app/Http/Controllers/Api/LessonProgressController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrainingLesson;
use App\Services\LessonProgressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LessonProgressController extends Controller
{
    public function __invoke(Request $request, LessonProgressService $service): JsonResponse
    {
        $validated = $request->validate([
            'lesson_id' => 'required|integer|exists:training_lessons,id',
            'progress_percent' => 'required|integer|min:0|max:100',
        ]);

        $user = $request->user();
        $lesson = TrainingLesson::find($validated['lesson_id']);

        // Verify lesson belongs to user's company via training
        $training = $lesson->module->training;
        if ($training->company_id !== $user->company_id) {
            abort(403);
        }

        $lessonView = $service->updateProgress(
            $validated['lesson_id'],
            $user->id,
            $user->company_id,
            $validated['progress_percent']
        );

        // Calculate module progress
        $moduleLessons = $lesson->module->lessons;
        $moduleLessonIds = $moduleLessons->pluck('id');
        $moduleViews = \App\Models\LessonView::withoutGlobalScope('company')
            ->where('user_id', $user->id)
            ->whereIn('lesson_id', $moduleLessonIds)
            ->get()
            ->keyBy('lesson_id');
        $moduleProgress = (int) round($moduleLessons->avg(fn ($l) => $moduleViews[$l->id]->progress_percent ?? 0));

        // Get training progress
        $trainingView = \App\Models\TrainingView::withoutGlobalScope('company')
            ->where('training_id', $training->id)
            ->where('user_id', $user->id)
            ->first();

        return response()->json([
            'progress_percent' => $lessonView->progress_percent,
            'lesson_completed' => $lessonView->completed_at !== null,
            'module_progress' => $moduleProgress,
            'training_progress' => $trainingView?->progress_percent ?? 0,
        ]);
    }
}
```

- [ ] **Step 3: Add API route**

In `routes/api.php`, add:

```php
use App\Http\Controllers\Api\LessonProgressController;

Route::middleware(['auth:sanctum', 'throttle:30,1'])->group(function () {
    // ... existing routes
    Route::post('/lesson-progress', LessonProgressController::class);
});
```

If the existing `/api/training-progress` route uses `auth` middleware instead of `auth:sanctum`, match that pattern.

- [ ] **Step 4: Commit**

```bash
git add app/Services/LessonProgressService.php app/Http/Controllers/Api/LessonProgressController.php routes/api.php
git commit -m "feat: add LessonProgressService and API endpoint for lesson progress tracking"
```

---

## Task 7: Admin Module Builder — Controller

**Files:**
- Modify: `app/Http/Controllers/Admin/TrainingController.php`

- [ ] **Step 1: Update store method**

The `store` method needs to handle the nested modules/lessons structure. Replace the single video_url processing with module/lesson loop inside a DB transaction.

Key changes:
- Accept `modules` array from request with nested `lessons`
- Create TrainingModules with sort_order
- Create TrainingLessons for each module
- Handle file uploads for document lessons (store in `lessons/{company_id}/`)
- Auto-detect video provider for video lessons
- Handle quiz creation per module and/or training level
- Set `has_quiz = true` if any quiz exists

- [ ] **Step 2: Update update method**

The `update` method needs to sync modules/lessons:
- Delete removed modules/lessons
- Update existing ones
- Create new ones
- Reorder based on sort_order
- Handle file replacement for document lessons
- Clean up deleted document files

- [ ] **Step 3: Create StoreTrainingRequest or update existing**

Validation rules for the new structure:
```php
'title' => 'required|string|max:255',
'description' => 'nullable|string',
'duration_minutes_override' => 'nullable|integer|min:1',
'is_sequential' => 'boolean',
'modules' => 'required|array|min:1',
'modules.*.title' => 'required|string|max:255',
'modules.*.description' => 'nullable|string',
'modules.*.is_sequential' => 'boolean',
'modules.*.lessons' => 'required|array|min:1',
'modules.*.lessons.*.title' => 'required|string|max:255',
'modules.*.lessons.*.type' => 'required|in:video,document,text',
'modules.*.lessons.*.video_url' => 'required_if:modules.*.lessons.*.type,video|nullable|url',
'modules.*.lessons.*.duration_minutes' => 'required|integer|min:0',
'modules.*.lessons.*.content' => 'required_if:modules.*.lessons.*.type,text|nullable|string',
'modules.*.lessons.*.file' => 'required_if:modules.*.lessons.*.type,document|nullable|file|max:10240|mimes:pdf,pptx,docx',
```

- [ ] **Step 4: Commit**

```bash
git add app/Http/Controllers/Admin/TrainingController.php
git commit -m "feat: update admin TrainingController for module/lesson CRUD"
```

---

## Task 8: Admin Module Builder — Views

**Files:**
- Rewrite: `resources/views/admin/trainings/create.blade.php`
- Rewrite: `resources/views/admin/trainings/edit.blade.php`

- [ ] **Step 1: Create the module builder view (create)**

The view uses Alpine.js for dynamic module/lesson management:

**Structure:**
```
Section 1: Training basics (title, description, duration override, sequential toggle)
Section 2: Module builder
  - Each module is a card with:
    - Title, description, sequential toggle
    - Lessons list (each with type selector, type-specific fields)
    - Up/down arrows for reorder
    - Add/remove lesson buttons
    - Optional module quiz toggle
  - Add module button
Section 3: Training-level quiz (optional, same builder as today)
Submit button
```

Alpine.js data structure:
```javascript
{
    modules: [
        {
            title: '',
            description: '',
            is_sequential: true,
            has_quiz: false,
            lessons: [
                { title: '', type: 'video', video_url: '', duration_minutes: 0, content: '', file: null }
            ],
            quiz: { questions: [] }
        }
    ],
    addModule() { ... },
    removeModule(index) { ... },
    moveModule(index, direction) { ... },
    addLesson(moduleIndex) { ... },
    removeLesson(moduleIndex, lessonIndex) { ... },
    moveLesson(moduleIndex, lessonIndex, direction) { ... },
}
```

- [ ] **Step 2: Create the module builder view (edit)**

Same structure as create, pre-populated with existing data from `$training->modules->lessons`.

- [ ] **Step 3: Commit**

```bash
git add resources/views/admin/trainings/create.blade.php resources/views/admin/trainings/edit.blade.php
git commit -m "feat: admin module builder views for training create/edit"
```

---

## Task 9: Employee Course Player — Controller

**Files:**
- Modify: `app/Http/Controllers/Employee/TrainingController.php`

- [ ] **Step 1: Update show method**

```php
public function show(Training $training)
{
    // Verify access (same as today)
    // ...

    // Load modules with lessons and user's progress
    $training->load([
        'modules.lessons',
        'modules.quiz.questions.options',
        'trainingQuiz.questions.options',
    ]);

    $user = auth()->user();
    $lessonIds = $training->lessons->pluck('id');

    $lessonViews = LessonView::withoutGlobalScope('company')
        ->where('user_id', $user->id)
        ->whereIn('lesson_id', $lessonIds)
        ->get()
        ->keyBy('lesson_id');

    // Determine current lesson (from query param or first incomplete)
    $currentLessonId = request('lesson');
    if (!$currentLessonId) {
        // Find first incomplete lesson
        foreach ($training->modules as $module) {
            foreach ($module->lessons as $lesson) {
                $view = $lessonViews[$lesson->id] ?? null;
                if (!$view || !$view->completed_at) {
                    $currentLessonId = $lesson->id;
                    break 2;
                }
            }
        }
        // If all complete, show last lesson
        $currentLessonId = $currentLessonId ?? $training->lessons->last()?->id;
    }

    $currentLesson = TrainingLesson::find($currentLessonId);

    // Create lesson view if first access
    if ($currentLesson) {
        LessonView::withoutGlobalScope('company')->firstOrCreate(
            ['lesson_id' => $currentLesson->id, 'user_id' => $user->id],
            ['company_id' => $user->company_id, 'started_at' => now()]
        );
    }

    // Calculate unlock states
    $unlockStates = $this->calculateUnlockStates($training, $lessonViews, $user);

    // Training view
    $trainingView = TrainingView::where('training_id', $training->id)
        ->where('user_id', $user->id)
        ->first();

    // Quiz states
    $moduleQuizStates = [];
    foreach ($training->modules as $module) {
        if ($module->quiz) {
            $moduleQuizStates[$module->id] = [
                'passed' => $user->quizAttempts()
                    ->where('quiz_id', $module->quiz->id)
                    ->where('passed', true)
                    ->exists(),
            ];
        }
    }

    $canComplete = /* all lessons done + all quizzes passed */;
    $isCompleted = $trainingView?->completed_at !== null;
    $canGenerateCertificate = $isCompleted && /* all quizzes passed */;

    return view('employee.trainings.show', compact(
        'training', 'currentLesson', 'lessonViews', 'unlockStates',
        'trainingView', 'moduleQuizStates', 'canComplete', 'isCompleted',
        'canGenerateCertificate'
    ));
}
```

- [ ] **Step 2: Add unlock state calculation helper**

```php
private function calculateUnlockStates(Training $training, $lessonViews, $user): array
{
    $states = ['modules' => [], 'lessons' => []];
    $prevModuleComplete = true;

    foreach ($training->modules as $module) {
        $moduleUnlocked = !$training->is_sequential || $prevModuleComplete;
        $states['modules'][$module->id] = $moduleUnlocked;

        $prevLessonComplete = true;
        $allLessonsComplete = true;

        foreach ($module->lessons as $lesson) {
            $view = $lessonViews[$lesson->id] ?? null;
            $lessonComplete = $view && $view->completed_at;

            $lessonUnlocked = $moduleUnlocked && (!$module->is_sequential || $prevLessonComplete);
            $states['lessons'][$lesson->id] = $lessonUnlocked;

            if (!$lessonComplete) $allLessonsComplete = false;
            $prevLessonComplete = $lessonComplete;
        }

        // Module complete = all lessons + quiz passed
        $quizPassed = !$module->quiz || $user->quizAttempts()
            ->where('quiz_id', $module->quiz->id)
            ->where('passed', true)->exists();
        $prevModuleComplete = $allLessonsComplete && $quizPassed;
    }

    return $states;
}
```

- [ ] **Step 3: Update complete method**

Update the `complete` method to verify all modules/lessons are done before setting `completed_at`.

- [ ] **Step 4: Commit**

```bash
git add app/Http/Controllers/Employee/TrainingController.php
git commit -m "feat: update employee TrainingController for course player with modules"
```

---

## Task 10: Employee Course Player — Views

**Files:**
- Create: `resources/views/components/ui/course-sidebar.blade.php`
- Create: `resources/views/components/ui/lesson-player.blade.php`
- Rewrite: `resources/views/employee/trainings/show.blade.php`

- [ ] **Step 1: Create course sidebar component**

Accordion-style sidebar showing modules, lessons with progress indicators, lock states, and quiz status. Uses Alpine.js for expand/collapse.

- [ ] **Step 2: Create lesson player component**

Handles rendering based on lesson type:
- Video: existing `<x-ui.video-player>` component with adapted props
- Document: PDF embed via `<iframe>` + download button, auto-marks 100% progress
- Text: rendered HTML content, tracks scroll/time for completion

- [ ] **Step 3: Rewrite training show page**

Two-column layout:
- Left: `<x-ui.course-sidebar>` with training data
- Right: `<x-ui.lesson-player>` with current lesson
- Bottom: completion/certificate actions

- [ ] **Step 4: Update video-player component**

Modify `resources/views/components/ui/video-player.blade.php` to accept `lessonId` instead of `trainingId` and post to `/api/lesson-progress` instead of `/api/training-progress`.

- [ ] **Step 5: Commit**

```bash
git add resources/views/components/ui/course-sidebar.blade.php resources/views/components/ui/lesson-player.blade.php resources/views/employee/trainings/show.blade.php resources/views/components/ui/video-player.blade.php
git commit -m "feat: employee course player with module sidebar and lesson player"
```

---

## Task 11: Quiz Updates for Module Support

**Files:**
- Modify: `app/Http/Controllers/Employee/QuizController.php`
- Modify: quiz-related views if needed

- [ ] **Step 1: Update QuizController for module-level quizzes**

Add support for `?module={id}` query param to load module-level quiz. Update access checks:
- Module quiz: all lessons in module must be completed
- Training quiz: all modules must be completed (lessons + module quizzes)

- [ ] **Step 2: Commit**

```bash
git add app/Http/Controllers/Employee/QuizController.php
git commit -m "feat: support module-level quiz access and validation"
```

---

## Task 12: Certificate Updates

**Files:**
- Modify: `app/Services/CertificateService.php`
- Modify: `resources/views/certificates/template.blade.php`
- Modify: `resources/views/certificates/show.blade.php`

- [ ] **Step 1: Update CertificateService.canGenerate**

Check module quizzes in addition to training quiz:

```php
public function canGenerate(User $user, Training $training): bool
{
    $view = $user->trainingViews()
        ->where('training_id', $training->id)
        ->whereNotNull('completed_at')
        ->first();

    if (!$view) return false;

    // Check all quizzes (module + training level)
    $quizzes = $training->quizzes;
    foreach ($quizzes as $quiz) {
        $passed = $user->quizAttempts()
            ->where('quiz_id', $quiz->id)
            ->where('passed', true)
            ->exists();
        if (!$passed) return false;
    }

    return true;
}
```

- [ ] **Step 2: Update generate method**

Pass modules data to the PDF template:

```php
$modules = $training->modules()->with('lessons')->orderBy('sort_order')->get();
// Add to Pdf::loadView data array:
'modules' => $modules,
```

- [ ] **Step 3: Update certificate PDF template**

Add "Conteúdo Programático" section listing modules with lesson counts.

- [ ] **Step 4: Update certificate share page**

Add modules listing below the certificate visual.

- [ ] **Step 5: Commit**

```bash
git add app/Services/CertificateService.php resources/views/certificates/template.blade.php resources/views/certificates/show.blade.php
git commit -m "feat: update certificates with module quiz validation and content listing"
```

---

## Task 13: Instructor Controller Support

**Files:**
- Modify: `app/Http/Controllers/Instructor/TrainingController.php`

- [ ] **Step 1: Update instructor controller**

Mirror the admin controller changes for module/lesson CRUD. The instructor uses the same module builder views (shared Blade components).

- [ ] **Step 2: Commit**

```bash
git add app/Http/Controllers/Instructor/TrainingController.php
git commit -m "feat: add module builder support to instructor training controller"
```

---

## Task 14: Deploy and Verify

- [ ] **Step 1: Commit all remaining changes**

```bash
git add -A
git status
# Review and commit any remaining files
```

- [ ] **Step 2: Merge to main and push**

```bash
cd /Users/joaofilipibritto/Projetos/treinaedu
git merge feature/implementation
git push origin main
```

- [ ] **Step 3: Verify on server**

After deploy completes:
- Check migrations ran: `php artisan migrate:status`
- Verify existing trainings were migrated to modules
- Test creating a new training with multiple modules/lessons
- Test employee course player
- Test quiz at module level
- Test certificate generation with module listing

---

## Implementation Order

Tasks must be executed in this order due to dependencies:

```
Task 1 (new tables) → Task 2 (alter tables) → Task 3 (data migration)
    ↓
Task 4 (new models) → Task 5 (update models) → Task 6 (progress service)
    ↓
Task 7 (admin controller) → Task 8 (admin views)
    ↓
Task 9 (employee controller) → Task 10 (employee views)
    ↓
Task 11 (quiz updates) → Task 12 (certificate updates)
    ↓
Task 13 (instructor) → Task 14 (deploy)
```

Tasks 7-8 and 9-10 could theoretically be parallelized, but the employee views depend on the admin being able to create multi-module trainings for testing.
