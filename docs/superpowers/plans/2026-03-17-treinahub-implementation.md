# TreinaHub Implementation Plan

> **For agentic workers:** REQUIRED: Use superpowers:subagent-driven-development (if subagents available) or superpowers:executing-plans to implement this plan. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a multi-tenant SaaS corporate LMS where companies register, create video trainings, assign them to employee groups, track completion, run quizzes, and generate PDF certificates — all paid via Asaas subscriptions.

**Architecture:** Laravel 11 monolith with Blade + TailwindCSS frontend. Single MySQL database with tenant isolation via `company_id` Global Scope. Synchronous processing only (shared hosting). AJAX endpoints for video progress tracking.

**Tech Stack:** Laravel 11, Breeze, TailwindCSS, MySQL, DomPDF, Chart.js, Asaas API, maatwebsite/excel

**Spec:** `docs/superpowers/specs/2026-03-17-treinahub-design.md`

---

## Task 1: Project Scaffolding

**Files:**
- Create: Laravel 11 project in current directory
- Create: `.env` with app config
- Modify: `config/app.php` (timezone, locale)

- [ ] **Step 1: Create Laravel project**

```bash
composer create-project laravel/laravel . "11.*"
```

- [ ] **Step 2: Install Breeze with Blade**

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
```

- [ ] **Step 3: Install additional packages**

```bash
composer require barryvdh/laravel-dompdf
composer require maatwebsite/excel
```

- [ ] **Step 4: Configure .env**

Set in `.env`:
```
APP_NAME=TreinaHub
APP_URL=http://localhost:8000
DB_DATABASE=treinahub
DB_USERNAME=root
DB_PASSWORD=
ASAAS_API_KEY=
ASAAS_WEBHOOK_TOKEN=
ASAAS_BASE_URL=https://sandbox.asaas.com/api/v3
```

- [ ] **Step 5: Configure app timezone and locale**

In `config/app.php`:
```php
'timezone' => 'America/Sao_Paulo',
'locale' => 'pt_BR',
'faker_locale' => 'pt_BR',
```

- [ ] **Step 6: Create database and run initial migration**

```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS treinahub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate
```

- [ ] **Step 7: Build frontend assets**

```bash
npm install
npm run build
```

- [ ] **Step 8: Commit**

```bash
git add -A
git commit -m "chore: scaffold Laravel 11 project with Breeze, DomPDF, Excel"
```

---

## Task 2: Database Migrations

**Files:**
- Create: `database/migrations/xxxx_create_companies_table.php`
- Create: `database/migrations/xxxx_add_company_fields_to_users_table.php`
- Create: `database/migrations/xxxx_create_groups_table.php`
- Create: `database/migrations/xxxx_create_group_user_table.php`
- Create: `database/migrations/xxxx_create_trainings_table.php`
- Create: `database/migrations/xxxx_create_training_assignments_table.php`
- Create: `database/migrations/xxxx_create_training_views_table.php`
- Create: `database/migrations/xxxx_create_quizzes_table.php`
- Create: `database/migrations/xxxx_create_quiz_questions_table.php`
- Create: `database/migrations/xxxx_create_quiz_options_table.php`
- Create: `database/migrations/xxxx_create_quiz_attempts_table.php`
- Create: `database/migrations/xxxx_create_certificates_table.php`
- Create: `database/migrations/xxxx_create_plans_table.php`
- Create: `database/migrations/xxxx_create_subscriptions_table.php`
- Create: `database/migrations/xxxx_create_payments_table.php`

- [ ] **Step 1: Create companies migration**

```bash
php artisan make:migration create_companies_table
```

```php
Schema::create('companies', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->string('asaas_customer_id')->nullable();
    $table->string('logo_path')->nullable();
    $table->string('primary_color', 7)->default('#3B82F6');
    $table->string('secondary_color', 7)->default('#1E40AF');
    $table->timestamps();
    $table->softDeletes();
});
```

- [ ] **Step 2: Create migration to modify users table**

```bash
php artisan make:migration add_company_fields_to_users_table
```

```php
// up()
Schema::table('users', function (Blueprint $table) {
    $table->foreignId('company_id')->nullable()->after('id')->constrained()->nullOnDelete();
    $table->enum('role', ['super_admin', 'admin', 'instructor', 'employee'])->default('employee')->after('password');
    $table->boolean('active')->default(true)->after('role');
    $table->softDeletes();
    $table->index(['company_id', 'role']);
});
```

- [ ] **Step 3: Create groups and group_user migrations**

```bash
php artisan make:migration create_groups_table
php artisan make:migration create_group_user_table
```

groups:
```php
Schema::create('groups', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained()->cascadeOnDelete();
    $table->string('name');
    $table->text('description')->nullable();
    $table->timestamps();
});
```

group_user:
```php
Schema::create('group_user', function (Blueprint $table) {
    $table->id();
    $table->foreignId('group_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->timestamps();
    $table->unique(['group_id', 'user_id']);
});
```

- [ ] **Step 4: Create trainings migration**

```bash
php artisan make:migration create_trainings_table
```

```php
Schema::create('trainings', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained()->cascadeOnDelete();
    $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
    $table->string('title');
    $table->text('description')->nullable();
    $table->string('video_url', 500);
    $table->enum('video_provider', ['youtube', 'vimeo']);
    $table->unsignedInteger('duration_minutes');
    $table->unsignedInteger('passing_score')->nullable();
    $table->boolean('has_quiz')->default(false);
    $table->boolean('active')->default(true);
    $table->timestamps();
    $table->softDeletes();
});
```

- [ ] **Step 5: Create training_assignments migration**

```bash
php artisan make:migration create_training_assignments_table
```

```php
Schema::create('training_assignments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained()->cascadeOnDelete();
    $table->foreignId('training_id')->constrained()->cascadeOnDelete();
    $table->foreignId('group_id')->constrained()->cascadeOnDelete();
    $table->date('due_date')->nullable();
    $table->timestamps();
    $table->unique(['training_id', 'group_id']);
    $table->index(['company_id', 'group_id']);
});
```

- [ ] **Step 6: Create training_views migration**

```bash
php artisan make:migration create_training_views_table
```

```php
Schema::create('training_views', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained()->cascadeOnDelete();
    $table->foreignId('training_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->unsignedTinyInteger('progress_percent')->default(0);
    $table->timestamp('started_at')->nullable();
    $table->timestamp('completed_at')->nullable();
    $table->timestamps();
    $table->unique(['training_id', 'user_id']);
    $table->index(['company_id', 'user_id']);
});
```

- [ ] **Step 7: Create quiz tables migrations**

```bash
php artisan make:migration create_quizzes_table
php artisan make:migration create_quiz_questions_table
php artisan make:migration create_quiz_options_table
php artisan make:migration create_quiz_attempts_table
```

quizzes:
```php
Schema::create('quizzes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('training_id')->unique()->constrained()->cascadeOnDelete();
    $table->foreignId('company_id')->constrained()->cascadeOnDelete();
    $table->timestamps();
});
```

quiz_questions:
```php
Schema::create('quiz_questions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
    $table->text('question');
    $table->unsignedInteger('order')->default(0);
    $table->timestamps();
});
```

quiz_options:
```php
Schema::create('quiz_options', function (Blueprint $table) {
    $table->id();
    $table->foreignId('quiz_question_id')->constrained()->cascadeOnDelete();
    $table->string('option_text', 500);
    $table->boolean('is_correct')->default(false);
    $table->unsignedInteger('order')->default(0);
});
```

quiz_attempts:
```php
Schema::create('quiz_attempts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('company_id')->constrained()->cascadeOnDelete();
    $table->unsignedTinyInteger('score');
    $table->boolean('passed');
    $table->timestamp('completed_at');
    $table->timestamps();
});
```

- [ ] **Step 8: Create certificates migration**

```bash
php artisan make:migration create_certificates_table
```

```php
Schema::create('certificates', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('training_id')->constrained()->cascadeOnDelete();
    $table->string('certificate_code', 20)->unique();
    $table->string('pdf_path');
    $table->timestamp('generated_at');
    $table->timestamps();
    $table->index('certificate_code');
});
```

- [ ] **Step 9: Create plans, subscriptions, payments migrations**

```bash
php artisan make:migration create_plans_table
php artisan make:migration create_subscriptions_table
php artisan make:migration create_payments_table
```

plans:
```php
Schema::create('plans', function (Blueprint $table) {
    $table->id();
    $table->string('name', 100);
    $table->decimal('price', 10, 2);
    $table->unsignedInteger('max_users')->nullable();
    $table->unsignedInteger('max_trainings')->nullable();
    $table->json('features')->nullable();
    $table->boolean('active')->default(true);
    $table->timestamps();
});
```

subscriptions:
```php
Schema::create('subscriptions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->unique()->constrained()->cascadeOnDelete();
    $table->foreignId('plan_id')->constrained();
    $table->string('asaas_subscription_id')->nullable();
    $table->enum('status', ['trial', 'active', 'past_due', 'cancelled', 'expired'])->default('trial');
    $table->timestamp('trial_ends_at')->nullable();
    $table->timestamp('current_period_start')->nullable();
    $table->timestamp('current_period_end')->nullable();
    $table->timestamps();
});
```

payments:
```php
Schema::create('payments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('company_id')->constrained()->cascadeOnDelete();
    $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
    $table->string('asaas_payment_id')->nullable();
    $table->decimal('amount', 10, 2);
    $table->enum('status', ['pending', 'confirmed', 'received', 'overdue', 'refunded'])->default('pending');
    $table->enum('payment_method', ['boleto', 'pix', 'credit_card']);
    $table->timestamp('paid_at')->nullable();
    $table->date('due_date');
    $table->timestamps();
    $table->index(['company_id', 'status']);
});
```

- [ ] **Step 10: Run all migrations**

```bash
php artisan migrate
```

Expected: All migrations run successfully, 0 errors.

- [ ] **Step 11: Commit**

```bash
git add database/migrations/
git commit -m "feat: add all database migrations for TreinaHub schema"
```

---

## Task 3: Eloquent Models, Traits, and Relationships

**Files:**
- Create: `app/Models/Traits/BelongsToCompany.php`
- Create: `app/Models/Company.php`
- Modify: `app/Models/User.php`
- Create: `app/Models/Group.php`
- Create: `app/Models/Training.php`
- Create: `app/Models/TrainingAssignment.php`
- Create: `app/Models/TrainingView.php`
- Create: `app/Models/Quiz.php`
- Create: `app/Models/QuizQuestion.php`
- Create: `app/Models/QuizOption.php`
- Create: `app/Models/QuizAttempt.php`
- Create: `app/Models/Certificate.php`
- Create: `app/Models/Plan.php`
- Create: `app/Models/Subscription.php`
- Create: `app/Models/Payment.php`
- Create: `database/seeders/PlanSeeder.php`

- [ ] **Step 1: Create BelongsToCompany trait**

Create `app/Models/Traits/BelongsToCompany.php`:

```php
<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToCompany
{
    protected static function bootBelongsToCompany(): void
    {
        static::addGlobalScope('company', function (Builder $query) {
            if (auth()->check() && auth()->user()->company_id) {
                $query->where($query->getModel()->getTable() . '.company_id', auth()->user()->company_id);
            }
        });

        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->company_id && !$model->company_id) {
                $model->company_id = auth()->user()->company_id;
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
}
```

- [ ] **Step 2: Create Company model**

```bash
php artisan make:model Company
```

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'asaas_customer_id',
        'logo_path', 'primary_color', 'secondary_color',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public function trainings()
    {
        return $this->hasMany(Training::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    public function isOnTrial(): bool
    {
        return $this->subscription
            && $this->subscription->status === 'trial'
            && $this->subscription->trial_ends_at
            && $this->subscription->trial_ends_at->isFuture();
    }

    public function hasActiveSubscription(): bool
    {
        if (!$this->subscription) {
            return false;
        }

        return $this->isOnTrial()
            || in_array($this->subscription->status, ['active', 'past_due']);
    }

    public function hasReachedUserLimit(): bool
    {
        if (!$this->subscription || !$this->subscription->plan->max_users) {
            return false;
        }

        return $this->users()->whereIn('role', ['instructor', 'employee'])->count()
            >= $this->subscription->plan->max_users;
    }
}
```

- [ ] **Step 3: Modify User model**

Update `app/Models/User.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password', 'company_id', 'role', 'active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class)->withTimestamps();
    }

    public function trainingViews()
    {
        return $this->hasMany(TrainingView::class);
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function assignedTrainings()
    {
        $groupIds = $this->groups()->pluck('groups.id');

        return Training::whereHas('assignments', function ($query) use ($groupIds) {
            $query->whereIn('group_id', $groupIds);
        })->where('active', true);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isInstructor(): bool
    {
        return $this->role === 'instructor';
    }

    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }
}
```

- [ ] **Step 4: Create Group model**

```bash
php artisan make:model Group
```

```php
<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = ['company_id', 'name', 'description'];

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function trainings()
    {
        return $this->belongsToMany(Training::class, 'training_assignments')
            ->withPivot('due_date')
            ->withTimestamps();
    }

    public function assignments()
    {
        return $this->hasMany(TrainingAssignment::class);
    }
}
```

- [ ] **Step 5: Create Training model**

```bash
php artisan make:model Training
```

```php
<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Training extends Model
{
    use HasFactory, BelongsToCompany, SoftDeletes;

    protected $fillable = [
        'company_id', 'created_by', 'title', 'description',
        'video_url', 'video_provider', 'duration_minutes',
        'passing_score', 'has_quiz', 'active',
    ];

    protected function casts(): array
    {
        return [
            'has_quiz' => 'boolean',
            'active' => 'boolean',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function quiz()
    {
        return $this->hasOne(Quiz::class);
    }

    public function views()
    {
        return $this->hasMany(TrainingView::class);
    }

    public function assignments()
    {
        return $this->hasMany(TrainingAssignment::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'training_assignments')
            ->withPivot('due_date')
            ->withTimestamps();
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function completionRate(): float
    {
        $totalAssigned = $this->assignments()
            ->join('group_user', 'training_assignments.group_id', '=', 'group_user.group_id')
            ->distinct('group_user.user_id')
            ->count('group_user.user_id');

        if ($totalAssigned === 0) {
            return 0;
        }

        $completed = $this->views()->whereNotNull('completed_at')->count();
        return round(($completed / $totalAssigned) * 100, 1);
    }

    public static function detectProvider(string $url): string
    {
        if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
            return 'youtube';
        }

        return 'vimeo';
    }
}
```

- [ ] **Step 6: Create TrainingAssignment and TrainingView models**

```bash
php artisan make:model TrainingAssignment
php artisan make:model TrainingView
```

TrainingAssignment:
```php
<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class TrainingAssignment extends Model
{
    use BelongsToCompany;

    protected $fillable = ['company_id', 'training_id', 'group_id', 'due_date'];

    protected function casts(): array
    {
        return ['due_date' => 'date'];
    }

    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
```

TrainingView:
```php
<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class TrainingView extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id', 'training_id', 'user_id',
        'progress_percent', 'started_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

- [ ] **Step 7: Create Quiz, QuizQuestion, QuizOption, QuizAttempt models**

```bash
php artisan make:model Quiz
php artisan make:model QuizQuestion
php artisan make:model QuizOption
php artisan make:model QuizAttempt
```

Quiz:
```php
<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use BelongsToCompany;

    protected $fillable = ['training_id', 'company_id'];

    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }
}
```

QuizQuestion:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    protected $fillable = ['quiz_id', 'question', 'order'];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function options()
    {
        return $this->hasMany(QuizOption::class)->orderBy('order');
    }
}
```

QuizOption:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizOption extends Model
{
    public $timestamps = false;

    protected $fillable = ['quiz_question_id', 'option_text', 'is_correct', 'order'];

    protected function casts(): array
    {
        return ['is_correct' => 'boolean'];
    }

    public function question()
    {
        return $this->belongsTo(QuizQuestion::class, 'quiz_question_id');
    }
}
```

QuizAttempt:
```php
<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'quiz_id', 'user_id', 'company_id',
        'score', 'passed', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'passed' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

- [ ] **Step 8: Create Certificate model**

```bash
php artisan make:model Certificate
```

```php
<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id', 'user_id', 'training_id',
        'certificate_code', 'pdf_path', 'generated_at',
    ];

    protected function casts(): array
    {
        return ['generated_at' => 'datetime'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function training()
    {
        return $this->belongsTo(Training::class);
    }
}
```

- [ ] **Step 9: Create Plan, Subscription, Payment models**

```bash
php artisan make:model Plan
php artisan make:model Subscription
php artisan make:model Payment
```

Plan:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = ['name', 'price', 'max_users', 'max_trainings', 'features', 'active'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'features' => 'array',
            'active' => 'boolean',
        ];
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
```

Subscription:
```php
<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id', 'plan_id', 'asaas_subscription_id', 'status',
        'trial_ends_at', 'current_period_start', 'current_period_end',
    ];

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
            'current_period_start' => 'datetime',
            'current_period_end' => 'datetime',
        ];
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
```

Payment:
```php
<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id', 'subscription_id', 'asaas_payment_id',
        'amount', 'status', 'payment_method', 'paid_at', 'due_date',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'due_date' => 'date',
        ];
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
```

- [ ] **Step 10: Create PlanSeeder with default plans**

```bash
php artisan make:seeder PlanSeeder
```

```php
<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::create([
            'name' => 'Basic',
            'price' => 99.90,
            'max_users' => 50,
            'max_trainings' => 20,
        ]);

        Plan::create([
            'name' => 'Pro',
            'price' => 199.90,
            'max_users' => 200,
            'max_trainings' => 100,
        ]);

        Plan::create([
            'name' => 'Enterprise',
            'price' => 499.90,
            'max_users' => null,
            'max_trainings' => null,
        ]);
    }
}
```

- [ ] **Step 11: Register seeder and run**

Add to `database/seeders/DatabaseSeeder.php`:
```php
$this->call(PlanSeeder::class);
```

```bash
php artisan db:seed --class=PlanSeeder
```

- [ ] **Step 12: Write model relationship tests**

Create `tests/Feature/Models/CompanyTest.php`:

```php
<?php

namespace Tests\Feature\Models;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_has_active_subscription_when_on_trial(): void
    {
        $plan = Plan::create(['name' => 'Basic', 'price' => 99.90, 'max_users' => 50, 'max_trainings' => 20]);
        $company = Company::create(['name' => 'Test Co', 'slug' => 'test-co']);
        Subscription::create([
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'status' => 'trial',
            'trial_ends_at' => now()->addDays(7),
        ]);

        $company->load('subscription');

        $this->assertTrue($company->isOnTrial());
        $this->assertTrue($company->hasActiveSubscription());
    }

    public function test_company_has_no_active_subscription_when_expired(): void
    {
        $plan = Plan::create(['name' => 'Basic', 'price' => 99.90, 'max_users' => 50, 'max_trainings' => 20]);
        $company = Company::create(['name' => 'Test Co', 'slug' => 'test-co']);
        Subscription::create([
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'status' => 'expired',
            'trial_ends_at' => now()->subDay(),
        ]);

        $company->load('subscription');

        $this->assertFalse($company->hasActiveSubscription());
    }

    public function test_company_detects_user_limit_reached(): void
    {
        $plan = Plan::create(['name' => 'Basic', 'price' => 99.90, 'max_users' => 2, 'max_trainings' => 20]);
        $company = Company::create(['name' => 'Test Co', 'slug' => 'test-co']);
        Subscription::create([
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'status' => 'active',
        ]);

        User::create(['name' => 'E1', 'email' => 'e1@test.com', 'password' => 'password', 'company_id' => $company->id, 'role' => 'employee']);
        User::create(['name' => 'E2', 'email' => 'e2@test.com', 'password' => 'password', 'company_id' => $company->id, 'role' => 'employee']);

        $company->load('subscription.plan');

        $this->assertTrue($company->hasReachedUserLimit());
    }
}
```

- [ ] **Step 13: Run tests**

```bash
php artisan test tests/Feature/Models/CompanyTest.php
```

Expected: 3 tests pass.

- [ ] **Step 14: Commit**

```bash
git add app/Models/ database/seeders/ tests/Feature/Models/
git commit -m "feat: add all Eloquent models, BelongsToCompany trait, PlanSeeder, and model tests"
```

---

## Task 4: Middleware

**Files:**
- Create: `app/Http/Middleware/RoleMiddleware.php`
- Create: `app/Http/Middleware/CheckSubscription.php`
- Create: `app/Http/Middleware/InjectCompanyTheme.php`
- Modify: `bootstrap/app.php` (register middleware aliases)
- Create: `tests/Feature/Middleware/RoleMiddlewareTest.php`
- Create: `tests/Feature/Middleware/CheckSubscriptionTest.php`

- [ ] **Step 1: Create RoleMiddleware**

```bash
php artisan make:middleware RoleMiddleware
```

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user() || !in_array($request->user()->role, $roles)) {
            abort(403, 'Acesso negado.');
        }

        return $next($request);
    }
}
```

- [ ] **Step 2: Create CheckSubscription middleware**

```bash
php artisan make:middleware CheckSubscription
```

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || $user->isSuperAdmin()) {
            return $next($request);
        }

        $company = $user->company;

        if (!$company || !$company->hasActiveSubscription()) {
            return redirect()->route('subscription.plans')
                ->with('warning', 'Sua assinatura expirou. Escolha um plano para continuar.');
        }

        return $next($request);
    }
}
```

- [ ] **Step 3: Create InjectCompanyTheme middleware**

```bash
php artisan make:middleware InjectCompanyTheme
```

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InjectCompanyTheme
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->company) {
            $company = $user->company;
            view()->share('currentCompany', $company);
            view()->share('primaryColor', $company->primary_color ?? '#3B82F6');
            view()->share('secondaryColor', $company->secondary_color ?? '#1E40AF');
            view()->share('companyLogo', $company->logo_path);
        }

        return $next($request);
    }
}
```

- [ ] **Step 4: Register middleware aliases in bootstrap/app.php**

In `bootstrap/app.php`, inside the `withMiddleware` callback:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
        'subscription' => \App\Http\Middleware\CheckSubscription::class,
        'theme' => \App\Http\Middleware\InjectCompanyTheme::class,
    ]);
})
```

- [ ] **Step 5: Write middleware tests**

Create `tests/Feature/Middleware/RoleMiddlewareTest.php`:

```php
<?php

namespace Tests\Feature\Middleware;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_routes(): void
    {
        $company = Company::create(['name' => 'Test', 'slug' => 'test']);
        $admin = User::create([
            'name' => 'Admin', 'email' => 'admin@test.com',
            'password' => 'password', 'company_id' => $company->id, 'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_employee_cannot_access_admin_routes(): void
    {
        $company = Company::create(['name' => 'Test', 'slug' => 'test']);
        $employee = User::create([
            'name' => 'Emp', 'email' => 'emp@test.com',
            'password' => 'password', 'company_id' => $company->id, 'role' => 'employee',
        ]);

        $response = $this->actingAs($employee)->get('/users');
        $response->assertStatus(403);
    }
}
```

Create `tests/Feature/Middleware/CheckSubscriptionTest.php`:

```php
<?php

namespace Tests\Feature\Middleware;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_active_trial_can_access(): void
    {
        $plan = Plan::create(['name' => 'Basic', 'price' => 99.90, 'max_users' => 50, 'max_trainings' => 20]);
        $company = Company::create(['name' => 'Test', 'slug' => 'test']);
        Subscription::create([
            'company_id' => $company->id, 'plan_id' => $plan->id,
            'status' => 'trial', 'trial_ends_at' => now()->addDays(7),
        ]);
        $admin = User::create([
            'name' => 'Admin', 'email' => 'admin@test.com',
            'password' => 'password', 'company_id' => $company->id, 'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_user_with_expired_subscription_is_redirected(): void
    {
        $plan = Plan::create(['name' => 'Basic', 'price' => 99.90, 'max_users' => 50, 'max_trainings' => 20]);
        $company = Company::create(['name' => 'Test', 'slug' => 'test']);
        Subscription::create([
            'company_id' => $company->id, 'plan_id' => $plan->id,
            'status' => 'expired', 'trial_ends_at' => now()->subDay(),
        ]);
        $admin = User::create([
            'name' => 'Admin', 'email' => 'admin@test.com',
            'password' => 'password', 'company_id' => $company->id, 'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertRedirect(route('subscription.plans'));
    }
}
```

Note: These tests depend on routes being defined (Task 5). They should be run after Task 5 is complete.

- [ ] **Step 6: Commit**

```bash
git add app/Http/Middleware/ bootstrap/app.php tests/Feature/Middleware/
git commit -m "feat: add Role, CheckSubscription, and InjectCompanyTheme middleware"
```

---

## Task 5: Routes and Base Controllers

**Files:**
- Create: `routes/web.php` (replace default)
- Create: `app/Http/Controllers/DashboardController.php`
- Create: `app/Http/Controllers/Auth/RegisteredUserController.php` (override Breeze)

- [ ] **Step 1: Define all routes in routes/web.php**

```php
<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\TrainingController as AdminTrainingController;
use App\Http\Controllers\Admin\TrainingAssignmentController;
use App\Http\Controllers\Admin\CertificateController as AdminCertificateController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\CompanySettingsController;
use App\Http\Controllers\Instructor\TrainingController as InstructorTrainingController;
use App\Http\Controllers\Employee\TrainingController as EmployeeTrainingController;
use App\Http\Controllers\Employee\CertificateController as EmployeeCertificateController;
use App\Http\Controllers\Employee\QuizController;
use App\Http\Controllers\Api\TrainingProgressController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\CertificateVerificationController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperDashboardController;
use App\Http\Controllers\SuperAdmin\CompanyController as SuperCompanyController;
use App\Http\Controllers\SuperAdmin\SubscriptionController as SuperSubscriptionController;
use App\Http\Controllers\SuperAdmin\PaymentController as SuperPaymentController;
use App\Http\Controllers\SuperAdmin\PlanController as SuperPlanController;
use App\Http\Controllers\AsaasWebhookController;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/certificate/verify', [CertificateVerificationController::class, 'show'])
    ->name('certificate.verify');
Route::post('/certificate/verify', [CertificateVerificationController::class, 'verify'])
    ->name('certificate.verify.post');

// Asaas Webhook (excluded from CSRF)
Route::post('/asaas/webhook', [AsaasWebhookController::class, 'handle'])
    ->name('asaas.webhook')
    ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

// Auth (Breeze) — Breeze routes are registered via auth.php, we override register
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
});

require __DIR__.'/auth.php';

// Authenticated routes
Route::middleware(['auth', 'theme'])->group(function () {

    // Subscription plans (accessible even with expired subscription)
    Route::get('/subscription/plans', [SubscriptionController::class, 'plans'])
        ->name('subscription.plans');
    Route::post('/subscription/subscribe', [SubscriptionController::class, 'subscribe'])
        ->name('subscription.subscribe');

    // Routes that require active subscription
    Route::middleware('subscription')->group(function () {

        // Dashboard (single controller, dispatches by role)
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Admin routes
        Route::middleware('role:admin')->group(function () {
            Route::resource('users', UserController::class)->except('show');
            Route::resource('groups', GroupController::class);
            Route::resource('trainings', AdminTrainingController::class);
            Route::resource('training-assignments', TrainingAssignmentController::class)
                ->only(['index', 'create', 'store', 'destroy']);
            Route::get('certificates', [AdminCertificateController::class, 'index'])
                ->name('admin.certificates.index');
            Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
            Route::get('reports/export/pdf', [ReportController::class, 'exportPdf'])
                ->name('reports.export.pdf');
            Route::get('reports/export/excel', [ReportController::class, 'exportExcel'])
                ->name('reports.export.excel');
            Route::get('subscription', [SubscriptionController::class, 'show'])
                ->name('subscription.show');
            Route::get('company/settings', [CompanySettingsController::class, 'edit'])
                ->name('company.settings');
            Route::put('company/settings', [CompanySettingsController::class, 'update'])
                ->name('company.settings.update');
        });

        // Instructor routes
        Route::middleware('role:instructor')->prefix('instructor')->name('instructor.')->group(function () {
            Route::resource('trainings', InstructorTrainingController::class);
        });

        // Employee routes
        Route::middleware('role:employee')->group(function () {
            Route::get('trainings/{training}', [EmployeeTrainingController::class, 'show'])
                ->name('employee.trainings.show');
            Route::post('trainings/{training}/complete', [EmployeeTrainingController::class, 'complete'])
                ->name('employee.trainings.complete');
            Route::get('trainings/{training}/quiz', [QuizController::class, 'show'])
                ->name('employee.quiz.show');
            Route::post('trainings/{training}/quiz', [QuizController::class, 'submit'])
                ->name('employee.quiz.submit');
            Route::get('certificates', [EmployeeCertificateController::class, 'index'])
                ->name('employee.certificates.index');
            Route::get('certificates/{certificate}/download', [EmployeeCertificateController::class, 'download'])
                ->name('employee.certificates.download');
            Route::post('certificates/{training}/generate', [EmployeeCertificateController::class, 'generate'])
                ->name('employee.certificates.generate');
        });
    });
});

// API routes (AJAX, auth via session)
Route::middleware('auth')->prefix('api')->group(function () {
    Route::post('training-progress', [TrainingProgressController::class, 'update'])
        ->name('api.training-progress')
        ->middleware('throttle:30,1');
});

// Super Admin routes
Route::middleware(['auth', 'role:super_admin'])->prefix('super')->name('super.')->group(function () {
    Route::get('dashboard', [SuperDashboardController::class, 'index'])->name('dashboard');
    Route::resource('companies', SuperCompanyController::class);
    Route::get('subscriptions', [SuperSubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('payments', [SuperPaymentController::class, 'index'])->name('payments.index');
    Route::resource('plans', SuperPlanController::class);
});
```

- [ ] **Step 2: Create DashboardController**

Create `app/Http/Controllers/DashboardController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Training;
use App\Models\TrainingView;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return redirect()->route('super.dashboard');
        }

        return match ($user->role) {
            'admin' => $this->adminDashboard(),
            'instructor' => $this->instructorDashboard(),
            'employee' => $this->employeeDashboard(),
        };
    }

    private function adminDashboard()
    {
        $companyId = auth()->user()->company_id;

        $metrics = Cache::remember("dashboard_metrics_{$companyId}", 300, function () {
            return [
                'total_employees' => User::where('company_id', auth()->user()->company_id)
                    ->where('role', 'employee')->count(),
                'trainings_created' => Training::count(),
                'trainings_completed' => TrainingView::whereNotNull('completed_at')->count(),
                'trainings_pending' => TrainingView::whereNull('completed_at')->count(),
                'certificates_issued' => Certificate::count(),
            ];
        });

        return view('admin.dashboard', compact('metrics'));
    }

    private function instructorDashboard()
    {
        $trainings = Training::where('created_by', auth()->id())
            ->withCount([
                'views',
                'views as completed_count' => fn ($q) => $q->whereNotNull('completed_at'),
            ])
            ->latest()
            ->paginate(15);

        return view('instructor.dashboard', compact('trainings'));
    }

    private function employeeDashboard()
    {
        $user = auth()->user();

        $assignedTrainings = $user->assignedTrainings()
            ->with(['views' => fn ($q) => $q->where('user_id', $user->id)])
            ->get();

        $pending = $assignedTrainings->filter(function ($training) {
            $view = $training->views->first();
            return !$view || !$view->completed_at;
        });

        $completed = $assignedTrainings->filter(function ($training) {
            $view = $training->views->first();
            return $view && $view->completed_at;
        });

        $certificates = $user->certificates()->with('training')->latest()->get();

        return view('employee.dashboard', compact('pending', 'completed', 'certificates'));
    }
}
```

- [ ] **Step 3: Override Breeze RegisteredUserController for company onboarding**

Replace or create `app/Http/Controllers/Auth/RegisteredUserController.php`:

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $slug = Str::slug($request->company_name);
        $originalSlug = $slug;
        $counter = 1;
        while (Company::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        $company = Company::create([
            'name' => $request->company_name,
            'slug' => $slug,
        ]);

        $basicPlan = Plan::where('name', 'Basic')->first();

        Subscription::create([
            'company_id' => $company->id,
            'plan_id' => $basicPlan->id,
            'status' => 'trial',
            'trial_ends_at' => now()->addDays(7),
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => $company->id,
            'role' => 'admin',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard'));
    }
}
```

- [ ] **Step 4: Commit**

```bash
git add routes/web.php app/Http/Controllers/DashboardController.php app/Http/Controllers/Auth/RegisteredUserController.php
git commit -m "feat: add routes, DashboardController, and company registration flow"
```

---

## Task 6: Blade Layout and UI Components

**Files:**
- Create: `resources/views/components/layout/app.blade.php`
- Create: `resources/views/components/layout/guest.blade.php`
- Create: `resources/views/components/ui/card.blade.php`
- Create: `resources/views/components/ui/table.blade.php`
- Create: `resources/views/components/ui/modal.blade.php`
- Create: `resources/views/components/ui/alert.blade.php`
- Create: `resources/views/components/ui/video-player.blade.php`
- Create: `resources/views/components/forms/input.blade.php`
- Create: `resources/views/components/forms/select.blade.php`
- Create: `resources/views/components/forms/button.blade.php`
- Modify: `resources/views/auth/register.blade.php` (add company_name field)

- [ ] **Step 1: Create app layout with sidebar**

Create `resources/views/components/layout/app.blade.php` with:
- HTML head with TailwindCSS, Chart.js CDN
- CSS variables from `$primaryColor` / `$secondaryColor`
- Sidebar with navigation links per role (`auth()->user()->role`)
- Company logo in sidebar (or TreinaHub default)
- Topbar with user name and logout
- `{{ $slot }}` for main content area
- Subscription warning banner if `past_due`

- [ ] **Step 2: Create guest layout**

Create `resources/views/components/layout/guest.blade.php` with:
- Minimal centered layout for login/register
- TreinaHub logo
- `{{ $slot }}` for form content

- [ ] **Step 3: Create UI components**

Create each component:

`ui/card.blade.php` — Props: `$title`, `$value`, `$icon` (optional). Displays metric card.

`ui/alert.blade.php` — Props: `$type` (success, error, warning, info). Reads from session flash.

`ui/modal.blade.php` — Props: `$id`, `$title`. Alpine.js for open/close.

`ui/table.blade.php` — Wrapper with TailwindCSS table styling + pagination slot.

`ui/video-player.blade.php` — Props: `$videoUrl`, `$provider`, `$trainingId`. Embeds YouTube/Vimeo iframe with Player API JavaScript for progress tracking. Sends AJAX to `/api/training-progress`. Shows "Marcar como concluido" button when progress >= 90%.

- [ ] **Step 4: Create form components**

`forms/input.blade.php` — Props: `$name`, `$label`, `$type` (default text), `$value`. Includes error display.

`forms/select.blade.php` — Props: `$name`, `$label`, `$options`. Includes error display.

`forms/button.blade.php` — Props: `$type` (submit/button), `$variant` (primary/secondary/danger).

- [ ] **Step 5: Update register view to include company_name**

Modify `resources/views/auth/register.blade.php` to add a "Nome da Empresa" field before the user name field.

- [ ] **Step 6: Create dashboard views**

Create `resources/views/admin/dashboard.blade.php`:
- Uses app layout
- 4 metric cards (employees, trainings created, completed, pending)
- Chart.js bar chart for completion over last 30 days

Create `resources/views/instructor/dashboard.blade.php`:
- Uses app layout
- Table of instructor's trainings with completion stats

Create `resources/views/employee/dashboard.blade.php`:
- Uses app layout
- "Treinamentos Pendentes" section with cards
- "Treinamentos Concluidos" section with cards
- "Meus Certificados" section with download links

- [ ] **Step 7: Commit**

```bash
git add resources/views/
git commit -m "feat: add Blade layouts, UI components, and dashboard views"
```

---

## Task 7: Admin — User Management (CRUD)

**Files:**
- Create: `app/Http/Controllers/Admin/UserController.php`
- Create: `app/Http/Requests/StoreUserRequest.php`
- Create: `app/Http/Requests/UpdateUserRequest.php`
- Create: `resources/views/admin/users/index.blade.php`
- Create: `resources/views/admin/users/create.blade.php`
- Create: `resources/views/admin/users/edit.blade.php`
- Create: `tests/Feature/Admin/UserControllerTest.php`

- [ ] **Step 1: Create UserController**

```bash
php artisan make:controller Admin/UserController --resource
```

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('company_id', auth()->user()->company_id)
            ->with('groups')
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $groups = Group::all();
        return view('admin.users.create', compact('groups'));
    }

    public function store(StoreUserRequest $request)
    {
        $company = auth()->user()->company;

        if ($company->hasReachedUserLimit()) {
            return back()->with('error', 'Limite de usuarios do plano atingido.');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => $company->id,
            'role' => $request->role,
        ]);

        if ($request->has('groups')) {
            $user->groups()->sync($request->groups);
        }

        return redirect()->route('users.index')->with('success', 'Usuario criado com sucesso.');
    }

    public function edit(User $user)
    {
        $this->authorizeCompany($user);
        $groups = Group::all();
        return view('admin.users.edit', compact('user', 'groups'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorizeCompany($user);

        $data = $request->only('name', 'email', 'role', 'active');
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        if ($request->has('groups')) {
            $user->groups()->sync($request->groups);
        }

        return redirect()->route('users.index')->with('success', 'Usuario atualizado.');
    }

    public function destroy(User $user)
    {
        $this->authorizeCompany($user);
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuario removido.');
    }

    private function authorizeCompany(User $user): void
    {
        if ($user->company_id !== auth()->user()->company_id) {
            abort(403);
        }
    }
}
```

- [ ] **Step 2: Create Form Requests**

```bash
php artisan make:request StoreUserRequest
php artisan make:request UpdateUserRequest
```

StoreUserRequest:
```php
public function authorize(): bool { return true; }

public function rules(): array
{
    return [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8|confirmed',
        'role' => 'required|in:instructor,employee',
        'groups' => 'nullable|array',
        'groups.*' => 'exists:groups,id',
    ];
}
```

UpdateUserRequest:
```php
public function authorize(): bool { return true; }

public function rules(): array
{
    return [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $this->route('user')->id,
        'password' => 'nullable|min:8|confirmed',
        'role' => 'required|in:instructor,employee',
        'active' => 'boolean',
        'groups' => 'nullable|array',
        'groups.*' => 'exists:groups,id',
    ];
}
```

- [ ] **Step 3: Create user views (index, create, edit)**

`admin/users/index.blade.php`: Table with Name, Email, Role, Groups, Active, Actions (edit/delete). Pagination.

`admin/users/create.blade.php`: Form with name, email, password, password_confirmation, role (select), groups (multi-select checkboxes).

`admin/users/edit.blade.php`: Same form pre-filled. Password optional.

- [ ] **Step 4: Write controller tests**

Create `tests/Feature/Admin/UserControllerTest.php`:

```php
<?php

namespace Tests\Feature\Admin;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createAdminWithSubscription(): User
    {
        $plan = Plan::create(['name' => 'Basic', 'price' => 99.90, 'max_users' => 50, 'max_trainings' => 20]);
        $company = Company::create(['name' => 'Test', 'slug' => 'test']);
        Subscription::create([
            'company_id' => $company->id, 'plan_id' => $plan->id,
            'status' => 'active',
        ]);
        return User::create([
            'name' => 'Admin', 'email' => 'admin@test.com',
            'password' => 'password', 'company_id' => $company->id, 'role' => 'admin',
        ]);
    }

    public function test_admin_can_list_users(): void
    {
        $admin = $this->createAdminWithSubscription();
        $response = $this->actingAs($admin)->get('/users');
        $response->assertStatus(200);
    }

    public function test_admin_can_create_user(): void
    {
        $admin = $this->createAdminWithSubscription();
        $response = $this->actingAs($admin)->post('/users', [
            'name' => 'New Employee',
            'email' => 'employee@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'employee',
        ]);
        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', ['email' => 'employee@test.com']);
    }

    public function test_admin_cannot_create_user_beyond_plan_limit(): void
    {
        $plan = Plan::create(['name' => 'Basic', 'price' => 99.90, 'max_users' => 1, 'max_trainings' => 20]);
        $company = Company::create(['name' => 'Test', 'slug' => 'test']);
        Subscription::create([
            'company_id' => $company->id, 'plan_id' => $plan->id, 'status' => 'active',
        ]);
        $admin = User::create([
            'name' => 'Admin', 'email' => 'admin@test.com',
            'password' => 'password', 'company_id' => $company->id, 'role' => 'admin',
        ]);
        User::create([
            'name' => 'E1', 'email' => 'e1@test.com',
            'password' => 'password', 'company_id' => $company->id, 'role' => 'employee',
        ]);

        $response = $this->actingAs($admin)->post('/users', [
            'name' => 'E2', 'email' => 'e2@test.com',
            'password' => 'password123', 'password_confirmation' => 'password123',
            'role' => 'employee',
        ]);

        $response->assertSessionHas('error');
    }
}
```

- [ ] **Step 5: Run tests**

```bash
php artisan test tests/Feature/Admin/UserControllerTest.php
```

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/Admin/UserController.php app/Http/Requests/ resources/views/admin/users/ tests/Feature/Admin/
git commit -m "feat: add admin user management CRUD with validation and tests"
```

---

## Task 8: Admin — Group Management (CRUD)

**Files:**
- Create: `app/Http/Controllers/Admin/GroupController.php`
- Create: `resources/views/admin/groups/index.blade.php`
- Create: `resources/views/admin/groups/create.blade.php`
- Create: `resources/views/admin/groups/edit.blade.php`

- [ ] **Step 1: Create GroupController**

```bash
php artisan make:controller Admin/GroupController --resource
```

Standard CRUD:
- `index()`: List groups with user count, paginated
- `create()`: Form with name, description, user multi-select
- `store()`: Validate, create group, sync users
- `edit()`: Pre-filled form
- `update()`: Validate, update, sync users
- `destroy()`: Delete group

- [ ] **Step 2: Create group views (index, create, edit)**

`admin/groups/index.blade.php`: Table with Name, Description, Users count, Actions.
`admin/groups/create.blade.php`: Form with name, description, user checkboxes.
`admin/groups/edit.blade.php`: Same pre-filled.

- [ ] **Step 3: Commit**

```bash
git add app/Http/Controllers/Admin/GroupController.php resources/views/admin/groups/
git commit -m "feat: add admin group management CRUD"
```

---

## Task 9: Admin — Training Management (CRUD) + Quiz

**Files:**
- Create: `app/Http/Controllers/Admin/TrainingController.php`
- Create: `app/Http/Requests/StoreTrainingRequest.php`
- Create: `resources/views/admin/trainings/index.blade.php`
- Create: `resources/views/admin/trainings/create.blade.php`
- Create: `resources/views/admin/trainings/edit.blade.php`
- Create: `resources/views/admin/trainings/show.blade.php`

- [ ] **Step 1: Create StoreTrainingRequest**

```bash
php artisan make:request StoreTrainingRequest
```

```php
public function rules(): array
{
    return [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'video_url' => ['required', 'url', function ($attribute, $value, $fail) {
            if (!str_contains($value, 'youtube.com') && !str_contains($value, 'youtu.be') && !str_contains($value, 'vimeo.com')) {
                $fail('A URL deve ser do YouTube ou Vimeo.');
            }
        }],
        'duration_minutes' => 'required|integer|min:1',
        'has_quiz' => 'boolean',
        'passing_score' => 'nullable|required_if:has_quiz,1|integer|min:1|max:100',
        'questions' => 'nullable|required_if:has_quiz,1|array|min:1',
        'questions.*.question' => 'required_with:questions|string',
        'questions.*.options' => 'required_with:questions|array|min:2',
        'questions.*.options.*.text' => 'required|string|max:500',
        'questions.*.correct' => 'required_with:questions|integer|min:0',
    ];
}
```

- [ ] **Step 2: Create TrainingController**

```bash
php artisan make:controller Admin/TrainingController --resource
```

Key logic in `store()`:
```php
public function store(StoreTrainingRequest $request)
{
    $training = Training::create([
        'title' => $request->title,
        'description' => $request->description,
        'video_url' => $request->video_url,
        'video_provider' => Training::detectProvider($request->video_url),
        'duration_minutes' => $request->duration_minutes,
        'has_quiz' => $request->boolean('has_quiz'),
        'passing_score' => $request->passing_score,
        'created_by' => auth()->id(),
    ]);

    if ($request->boolean('has_quiz') && $request->has('questions')) {
        $quiz = $training->quiz()->create([
            'company_id' => auth()->user()->company_id,
        ]);

        foreach ($request->questions as $i => $questionData) {
            $question = $quiz->questions()->create([
                'question' => $questionData['question'],
                'order' => $i,
            ]);

            foreach ($questionData['options'] as $j => $optionData) {
                $question->options()->create([
                    'option_text' => $optionData['text'],
                    'is_correct' => $j === (int) $questionData['correct'],
                    'order' => $j,
                ]);
            }
        }
    }

    return redirect()->route('trainings.index')->with('success', 'Treinamento criado.');
}
```

- [ ] **Step 3: Create training views**

`admin/trainings/index.blade.php`: Table with Title, Duration, Quiz, Active, Completion %, Actions.
`admin/trainings/create.blade.php`: Form with fields. Dynamic quiz section (add/remove questions with JavaScript).
`admin/trainings/edit.blade.php`: Pre-filled form with existing quiz questions.
`admin/trainings/show.blade.php`: Training details, assigned groups, completion stats.

- [ ] **Step 4: Commit**

```bash
git add app/Http/Controllers/Admin/TrainingController.php app/Http/Requests/StoreTrainingRequest.php resources/views/admin/trainings/
git commit -m "feat: add admin training management with quiz creation"
```

---

## Task 10: Admin — Training Assignments

**Files:**
- Create: `app/Http/Controllers/Admin/TrainingAssignmentController.php`
- Create: `resources/views/admin/assignments/index.blade.php`
- Create: `resources/views/admin/assignments/create.blade.php`

- [ ] **Step 1: Create TrainingAssignmentController**

```bash
php artisan make:controller Admin/TrainingAssignmentController
```

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Training;
use App\Models\TrainingAssignment;
use Illuminate\Http\Request;

class TrainingAssignmentController extends Controller
{
    public function index()
    {
        $assignments = TrainingAssignment::with(['training', 'group'])
            ->paginate(15);

        return view('admin.assignments.index', compact('assignments'));
    }

    public function create()
    {
        $trainings = Training::where('active', true)->get();
        $groups = Group::all();
        return view('admin.assignments.create', compact('trainings', 'groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'training_id' => 'required|exists:trainings,id',
            'group_ids' => 'required|array|min:1',
            'group_ids.*' => 'exists:groups,id',
            'due_date' => 'nullable|date|after:today',
        ]);

        foreach ($request->group_ids as $groupId) {
            TrainingAssignment::firstOrCreate(
                ['training_id' => $request->training_id, 'group_id' => $groupId],
                ['due_date' => $request->due_date]
            );
        }

        return redirect()->route('training-assignments.index')
            ->with('success', 'Treinamento atribuido aos grupos.');
    }

    public function destroy(TrainingAssignment $trainingAssignment)
    {
        $trainingAssignment->delete();
        return back()->with('success', 'Atribuicao removida.');
    }
}
```

- [ ] **Step 2: Create assignment views**

`admin/assignments/index.blade.php`: Table with Training, Group, Due Date, Actions (remove).
`admin/assignments/create.blade.php`: Select training, multi-select groups (checkboxes), optional due date.

- [ ] **Step 3: Commit**

```bash
git add app/Http/Controllers/Admin/TrainingAssignmentController.php resources/views/admin/assignments/
git commit -m "feat: add training assignment management"
```

---

## Task 11: Employee — Watch Training + Video Progress

**Files:**
- Create: `app/Http/Controllers/Employee/TrainingController.php`
- Create: `app/Http/Controllers/Api/TrainingProgressController.php`
- Create: `app/Services/VideoProgressService.php`
- Create: `resources/views/employee/trainings/show.blade.php`
- Create: `tests/Feature/Services/VideoProgressServiceTest.php`

- [ ] **Step 1: Create VideoProgressService**

```php
<?php

namespace App\Services;

use App\Models\TrainingView;

class VideoProgressService
{
    public function updateProgress(int $trainingId, int $userId, int $companyId, int $percent): TrainingView
    {
        $view = TrainingView::updateOrCreate(
            ['training_id' => $trainingId, 'user_id' => $userId],
            ['company_id' => $companyId]
        );

        if (!$view->started_at) {
            $view->started_at = now();
        }

        if ($percent > $view->progress_percent) {
            $view->progress_percent = min($percent, 100);
        }

        $view->save();

        return $view;
    }

    public function markCompleted(int $trainingId, int $userId): ?TrainingView
    {
        $view = TrainingView::where('training_id', $trainingId)
            ->where('user_id', $userId)
            ->first();

        if (!$view || $view->progress_percent < 90) {
            return null;
        }

        $view->update([
            'completed_at' => now(),
            'progress_percent' => 100,
        ]);

        return $view;
    }
}
```

- [ ] **Step 2: Create TrainingProgressController (API)**

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\VideoProgressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrainingProgressController extends Controller
{
    public function update(Request $request, VideoProgressService $service): JsonResponse
    {
        $request->validate([
            'training_id' => 'required|exists:trainings,id',
            'progress_percent' => 'required|integer|min:0|max:100',
        ]);

        $user = $request->user();
        $view = $service->updateProgress(
            $request->training_id,
            $user->id,
            $user->company_id,
            $request->progress_percent
        );

        return response()->json([
            'progress_percent' => $view->progress_percent,
            'can_complete' => $view->progress_percent >= 90,
        ]);
    }
}
```

- [ ] **Step 3: Create Employee TrainingController**

```php
<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Training;
use App\Models\TrainingView;
use App\Services\VideoProgressService;

class TrainingController extends Controller
{
    public function show(Training $training)
    {
        $user = auth()->user();

        // Verify user has access (is in an assigned group)
        $hasAccess = $user->groups()
            ->whereHas('assignments', fn ($q) => $q->where('training_id', $training->id))
            ->exists();

        if (!$hasAccess) {
            abort(403);
        }

        $view = TrainingView::firstOrCreate(
            ['training_id' => $training->id, 'user_id' => $user->id],
            ['company_id' => $user->company_id, 'started_at' => now()]
        );

        $canComplete = $view->progress_percent >= 90 && !$view->completed_at;
        $isCompleted = (bool) $view->completed_at;

        $quizPassed = false;
        if ($training->has_quiz && $isCompleted) {
            $quizPassed = $user->quizAttempts()
                ->where('quiz_id', $training->quiz?->id)
                ->where('passed', true)
                ->exists();
        }

        $canGenerateCertificate = $isCompleted && (!$training->has_quiz || $quizPassed);
        $existingCertificate = $user->certificates()
            ->where('training_id', $training->id)
            ->first();

        return view('employee.trainings.show', compact(
            'training', 'view', 'canComplete', 'isCompleted',
            'quizPassed', 'canGenerateCertificate', 'existingCertificate'
        ));
    }

    public function complete(Training $training, VideoProgressService $service)
    {
        $result = $service->markCompleted($training->id, auth()->id());

        if (!$result) {
            return back()->with('error', 'Voce precisa assistir pelo menos 90% do video.');
        }

        if ($training->has_quiz) {
            return redirect()->route('employee.quiz.show', $training);
        }

        return back()->with('success', 'Treinamento concluido!');
    }
}
```

- [ ] **Step 4: Create training watch view**

`employee/trainings/show.blade.php`:
- Video player component (YouTube/Vimeo embed with progress tracking JS)
- Progress bar
- "Marcar como concluido" button (shown when progress >= 90%, hidden until then)
- If completed and has_quiz: link to quiz
- If can generate certificate: "Gerar Certificado" button
- If certificate exists: "Download Certificado" link

- [ ] **Step 5: Write VideoProgressService tests**

```php
<?php

namespace Tests\Feature\Services;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\TrainingView;
use App\Models\User;
use App\Services\VideoProgressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VideoProgressServiceTest extends TestCase
{
    use RefreshDatabase;

    private function setup_data(): array
    {
        $plan = Plan::create(['name' => 'Basic', 'price' => 99.90, 'max_users' => 50, 'max_trainings' => 20]);
        $company = Company::create(['name' => 'Test', 'slug' => 'test']);
        Subscription::create(['company_id' => $company->id, 'plan_id' => $plan->id, 'status' => 'active']);
        $user = User::create(['name' => 'Emp', 'email' => 'e@test.com', 'password' => 'pw', 'company_id' => $company->id, 'role' => 'employee']);
        $admin = User::create(['name' => 'Admin', 'email' => 'a@test.com', 'password' => 'pw', 'company_id' => $company->id, 'role' => 'admin']);
        $training = Training::create([
            'company_id' => $company->id, 'created_by' => $admin->id,
            'title' => 'Test', 'video_url' => 'https://youtube.com/watch?v=123',
            'video_provider' => 'youtube', 'duration_minutes' => 30,
        ]);

        return [$company, $user, $training];
    }

    public function test_update_progress_creates_view(): void
    {
        [$company, $user, $training] = $this->setup_data();
        $service = new VideoProgressService();

        $view = $service->updateProgress($training->id, $user->id, $company->id, 50);

        $this->assertEquals(50, $view->progress_percent);
        $this->assertNotNull($view->started_at);
    }

    public function test_progress_never_decreases(): void
    {
        [$company, $user, $training] = $this->setup_data();
        $service = new VideoProgressService();

        $service->updateProgress($training->id, $user->id, $company->id, 70);
        $view = $service->updateProgress($training->id, $user->id, $company->id, 40);

        $this->assertEquals(70, $view->progress_percent);
    }

    public function test_mark_completed_requires_90_percent(): void
    {
        [$company, $user, $training] = $this->setup_data();
        $service = new VideoProgressService();

        $service->updateProgress($training->id, $user->id, $company->id, 50);
        $result = $service->markCompleted($training->id, $user->id);

        $this->assertNull($result);
    }

    public function test_mark_completed_succeeds_at_90_percent(): void
    {
        [$company, $user, $training] = $this->setup_data();
        $service = new VideoProgressService();

        $service->updateProgress($training->id, $user->id, $company->id, 92);
        $result = $service->markCompleted($training->id, $user->id);

        $this->assertNotNull($result);
        $this->assertNotNull($result->completed_at);
    }
}
```

- [ ] **Step 6: Run tests**

```bash
php artisan test tests/Feature/Services/VideoProgressServiceTest.php
```

- [ ] **Step 7: Commit**

```bash
git add app/Services/VideoProgressService.php app/Http/Controllers/Employee/TrainingController.php app/Http/Controllers/Api/TrainingProgressController.php resources/views/employee/trainings/ tests/Feature/Services/
git commit -m "feat: add video progress tracking and employee training view"
```

---

## Task 12: Employee — Quiz System

**Files:**
- Create: `app/Http/Controllers/Employee/QuizController.php`
- Create: `resources/views/employee/quiz/show.blade.php`
- Create: `resources/views/employee/quiz/result.blade.php`
- Create: `tests/Feature/Employee/QuizControllerTest.php`

- [ ] **Step 1: Create QuizController**

```php
<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\QuizAttempt;
use App\Models\Training;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function show(Training $training)
    {
        $quiz = $training->quiz()->with('questions.options')->firstOrFail();

        return view('employee.quiz.show', compact('training', 'quiz'));
    }

    public function submit(Request $request, Training $training)
    {
        $quiz = $training->quiz()->with('questions.options')->firstOrFail();

        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|integer',
        ]);

        $totalQuestions = $quiz->questions->count();
        $correctAnswers = 0;

        foreach ($quiz->questions as $question) {
            $selectedOptionId = $request->answers[$question->id] ?? null;
            $correctOption = $question->options->where('is_correct', true)->first();

            if ($correctOption && $selectedOptionId == $correctOption->id) {
                $correctAnswers++;
            }
        }

        $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;
        $passed = $score >= ($training->passing_score ?? 70);

        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => auth()->id(),
            'company_id' => auth()->user()->company_id,
            'score' => $score,
            'passed' => $passed,
            'completed_at' => now(),
        ]);

        return view('employee.quiz.result', compact('training', 'attempt', 'score', 'passed'));
    }
}
```

- [ ] **Step 2: Create quiz views**

`employee/quiz/show.blade.php`: Quiz form with radio buttons per question. Submit button.

`employee/quiz/result.blade.php`: Score display, pass/fail message. If passed: link to generate certificate. If failed: "Tentar novamente" button.

- [ ] **Step 3: Write quiz tests**

```php
<?php

namespace Tests\Feature\Employee;

use App\Models\Company;
use App\Models\Group;
use App\Models\Plan;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\TrainingAssignment;
use App\Models\TrainingView;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizControllerTest extends TestCase
{
    use RefreshDatabase;

    private function setupQuizScenario(): array
    {
        $plan = Plan::create(['name' => 'Basic', 'price' => 99.90, 'max_users' => 50, 'max_trainings' => 20]);
        $company = Company::create(['name' => 'Test', 'slug' => 'test']);
        Subscription::create(['company_id' => $company->id, 'plan_id' => $plan->id, 'status' => 'active']);

        $admin = User::create(['name' => 'Admin', 'email' => 'admin@t.com', 'password' => 'pw', 'company_id' => $company->id, 'role' => 'admin']);
        $employee = User::create(['name' => 'Emp', 'email' => 'emp@t.com', 'password' => 'pw', 'company_id' => $company->id, 'role' => 'employee']);

        $group = Group::create(['company_id' => $company->id, 'name' => 'Team']);
        $group->users()->attach($employee);

        $training = Training::create([
            'company_id' => $company->id, 'created_by' => $admin->id,
            'title' => 'Test Training', 'video_url' => 'https://youtube.com/watch?v=x',
            'video_provider' => 'youtube', 'duration_minutes' => 10,
            'has_quiz' => true, 'passing_score' => 70,
        ]);

        TrainingAssignment::create(['company_id' => $company->id, 'training_id' => $training->id, 'group_id' => $group->id]);
        TrainingView::create(['company_id' => $company->id, 'training_id' => $training->id, 'user_id' => $employee->id, 'progress_percent' => 100, 'started_at' => now(), 'completed_at' => now()]);

        $quiz = Quiz::create(['training_id' => $training->id, 'company_id' => $company->id]);
        $q1 = QuizQuestion::create(['quiz_id' => $quiz->id, 'question' => 'Q1?', 'order' => 0]);
        $correct = QuizOption::create(['quiz_question_id' => $q1->id, 'option_text' => 'Correct', 'is_correct' => true, 'order' => 0]);
        $wrong = QuizOption::create(['quiz_question_id' => $q1->id, 'option_text' => 'Wrong', 'is_correct' => false, 'order' => 1]);

        return [$employee, $training, $quiz, $q1, $correct, $wrong];
    }

    public function test_employee_passes_quiz_with_correct_answer(): void
    {
        [$employee, $training, $quiz, $q1, $correct, $wrong] = $this->setupQuizScenario();

        $response = $this->actingAs($employee)->post(route('employee.quiz.submit', $training), [
            'answers' => [$q1->id => $correct->id],
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('quiz_attempts', [
            'user_id' => $employee->id, 'quiz_id' => $quiz->id, 'passed' => true,
        ]);
    }

    public function test_employee_fails_quiz_with_wrong_answer(): void
    {
        [$employee, $training, $quiz, $q1, $correct, $wrong] = $this->setupQuizScenario();

        $response = $this->actingAs($employee)->post(route('employee.quiz.submit', $training), [
            'answers' => [$q1->id => $wrong->id],
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('quiz_attempts', [
            'user_id' => $employee->id, 'quiz_id' => $quiz->id, 'passed' => false,
        ]);
    }
}
```

- [ ] **Step 4: Run tests**

```bash
php artisan test tests/Feature/Employee/QuizControllerTest.php
```

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/Employee/QuizController.php resources/views/employee/quiz/ tests/Feature/Employee/
git commit -m "feat: add quiz system for employees with pass/fail logic"
```

---

## Task 13: Certificate Generation

**Files:**
- Create: `app/Services/CertificateService.php`
- Create: `app/Http/Controllers/Employee/CertificateController.php`
- Create: `app/Http/Controllers/Admin/CertificateController.php`
- Create: `app/Http/Controllers/CertificateVerificationController.php`
- Create: `resources/views/certificates/template.blade.php`
- Create: `resources/views/certificates/verify.blade.php`
- Create: `resources/views/employee/certificates/index.blade.php`
- Create: `resources/views/admin/certificates/index.blade.php`
- Create: `tests/Feature/Services/CertificateServiceTest.php`

- [ ] **Step 1: Create CertificateService**

```php
<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Training;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class CertificateService
{
    public function canGenerate(User $user, Training $training): bool
    {
        $view = $user->trainingViews()
            ->where('training_id', $training->id)
            ->whereNotNull('completed_at')
            ->first();

        if (!$view) {
            return false;
        }

        if ($training->has_quiz) {
            $passed = $user->quizAttempts()
                ->whereHas('quiz', fn ($q) => $q->where('training_id', $training->id))
                ->where('passed', true)
                ->exists();

            if (!$passed) {
                return false;
            }
        }

        return true;
    }

    public function generate(User $user, Training $training): Certificate
    {
        $existing = Certificate::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->where('training_id', $training->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        $code = $this->generateUniqueCode();
        $company = $user->company;

        $pdf = Pdf::loadView('certificates.template', [
            'userName' => $user->name,
            'trainingTitle' => $training->title,
            'durationMinutes' => $training->duration_minutes,
            'completionDate' => now()->format('d/m/Y'),
            'companyName' => $company->name,
            'companyLogo' => $company->logo_path,
            'certificateCode' => $code,
        ])->setPaper('a4', 'landscape');

        $directory = "certificates/{$company->id}";
        $filename = "{$code}.pdf";
        $path = "{$directory}/{$filename}";

        if (!file_exists(storage_path("app/{$directory}"))) {
            mkdir(storage_path("app/{$directory}"), 0755, true);
        }

        $pdf->save(storage_path("app/{$path}"));

        return Certificate::create([
            'company_id' => $company->id,
            'user_id' => $user->id,
            'training_id' => $training->id,
            'certificate_code' => $code,
            'pdf_path' => $path,
            'generated_at' => now(),
        ]);
    }

    private function generateUniqueCode(): string
    {
        do {
            $code = 'TH-' . date('Y') . '-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4));
        } while (Certificate::withoutGlobalScopes()->where('certificate_code', $code)->exists());

        return $code;
    }
}
```

- [ ] **Step 2: Create certificate PDF template**

`resources/views/certificates/template.blade.php`:
- Landscape A4 layout with CSS styling (no TailwindCSS, use inline CSS for DomPDF compatibility)
- Company logo (if exists) centered at top
- Title: "CERTIFICADO DE CONCLUSAO"
- Body: "Certificamos que **{userName}** concluiu com sucesso o treinamento **{trainingTitle}** com carga horaria de {durationHours}h, na empresa {companyName}."
- Completion date
- Certificate code at bottom
- Footer: "Verifique este certificado em treinahub.com.br/certificate/verify"

- [ ] **Step 3: Create Employee CertificateController**

```php
<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Training;
use App\Services\CertificateService;

class CertificateController extends Controller
{
    public function index()
    {
        $certificates = auth()->user()->certificates()
            ->with('training')
            ->latest()
            ->paginate(15);

        return view('employee.certificates.index', compact('certificates'));
    }

    public function generate(Training $training, CertificateService $service)
    {
        $user = auth()->user();

        if (!$service->canGenerate($user, $training)) {
            return back()->with('error', 'Voce nao pode gerar este certificado ainda.');
        }

        $certificate = $service->generate($user, $training);

        return redirect()->route('employee.certificates.download', $certificate);
    }

    public function download(Certificate $certificate)
    {
        if ($certificate->user_id !== auth()->id()) {
            abort(403);
        }

        return response()->download(storage_path("app/{$certificate->pdf_path}"));
    }
}
```

- [ ] **Step 4: Create Admin CertificateController**

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;

class CertificateController extends Controller
{
    public function index()
    {
        $certificates = Certificate::with(['user', 'training'])
            ->latest()
            ->paginate(15);

        return view('admin.certificates.index', compact('certificates'));
    }
}
```

- [ ] **Step 5: Create CertificateVerificationController**

```php
<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;

class CertificateVerificationController extends Controller
{
    public function show()
    {
        return view('certificates.verify');
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|string|max:20']);

        $certificate = Certificate::withoutGlobalScopes()
            ->with(['user', 'training', 'company'])
            ->where('certificate_code', $request->code)
            ->first();

        return view('certificates.verify', compact('certificate'));
    }
}
```

- [ ] **Step 6: Create verification view and certificate list views**

`certificates/verify.blade.php`: Form with code input. If certificate found, display details.
`employee/certificates/index.blade.php`: Table with Training, Code, Date, Download link.
`admin/certificates/index.blade.php`: Table with Employee, Training, Code, Date.

- [ ] **Step 7: Write CertificateService tests**

```php
<?php

namespace Tests\Feature\Services;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\TrainingView;
use App\Models\User;
use App\Services\CertificateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_generate_certificate_when_training_completed(): void
    {
        $plan = Plan::create(['name' => 'Basic', 'price' => 99.90, 'max_users' => 50, 'max_trainings' => 20]);
        $company = Company::create(['name' => 'Test', 'slug' => 'test']);
        Subscription::create(['company_id' => $company->id, 'plan_id' => $plan->id, 'status' => 'active']);
        $admin = User::create(['name' => 'A', 'email' => 'a@t.com', 'password' => 'pw', 'company_id' => $company->id, 'role' => 'admin']);
        $employee = User::create(['name' => 'E', 'email' => 'e@t.com', 'password' => 'pw', 'company_id' => $company->id, 'role' => 'employee']);

        $training = Training::create([
            'company_id' => $company->id, 'created_by' => $admin->id,
            'title' => 'Test', 'video_url' => 'https://youtube.com/watch?v=x',
            'video_provider' => 'youtube', 'duration_minutes' => 30,
        ]);

        TrainingView::create([
            'company_id' => $company->id, 'training_id' => $training->id,
            'user_id' => $employee->id, 'progress_percent' => 100,
            'started_at' => now(), 'completed_at' => now(),
        ]);

        $service = new CertificateService();
        $this->assertTrue($service->canGenerate($employee, $training));

        $this->actingAs($employee);
        $certificate = $service->generate($employee, $training);

        $this->assertNotNull($certificate);
        $this->assertStringStartsWith('TH-', $certificate->certificate_code);
        $this->assertFileExists(storage_path("app/{$certificate->pdf_path}"));
    }

    public function test_cannot_generate_certificate_without_completion(): void
    {
        $plan = Plan::create(['name' => 'Basic', 'price' => 99.90, 'max_users' => 50, 'max_trainings' => 20]);
        $company = Company::create(['name' => 'Test', 'slug' => 'test']);
        Subscription::create(['company_id' => $company->id, 'plan_id' => $plan->id, 'status' => 'active']);
        $admin = User::create(['name' => 'A', 'email' => 'a@t.com', 'password' => 'pw', 'company_id' => $company->id, 'role' => 'admin']);
        $employee = User::create(['name' => 'E', 'email' => 'e@t.com', 'password' => 'pw', 'company_id' => $company->id, 'role' => 'employee']);
        $training = Training::create([
            'company_id' => $company->id, 'created_by' => $admin->id,
            'title' => 'Test', 'video_url' => 'https://youtube.com/watch?v=x',
            'video_provider' => 'youtube', 'duration_minutes' => 30,
        ]);

        $service = new CertificateService();
        $this->assertFalse($service->canGenerate($employee, $training));
    }
}
```

- [ ] **Step 8: Run tests**

```bash
php artisan test tests/Feature/Services/CertificateServiceTest.php
```

- [ ] **Step 9: Commit**

```bash
git add app/Services/CertificateService.php app/Http/Controllers/Employee/CertificateController.php app/Http/Controllers/Admin/CertificateController.php app/Http/Controllers/CertificateVerificationController.php resources/views/certificates/ resources/views/employee/certificates/ resources/views/admin/certificates/ tests/Feature/Services/
git commit -m "feat: add certificate generation, download, and public verification"
```

---

## Task 14: Admin — Reports

**Files:**
- Create: `app/Http/Controllers/Admin/ReportController.php`
- Create: `app/Exports/TrainingCompletionExport.php`
- Create: `resources/views/admin/reports/index.blade.php`
- Create: `resources/views/admin/reports/pdf.blade.php`

- [ ] **Step 1: Create TrainingCompletionExport**

```php
<?php

namespace App\Exports;

use App\Models\TrainingView;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TrainingCompletionExport implements FromQuery, WithHeadings, WithMapping
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = TrainingView::with(['user', 'training'])
            ->limit(1000);

        if ($this->request->filled('training_id')) {
            $query->where('training_id', $this->request->training_id);
        }

        if ($this->request->filled('group_id')) {
            $groupUserIds = \App\Models\Group::find($this->request->group_id)
                ?->users()->pluck('users.id') ?? collect();
            $query->whereIn('user_id', $groupUserIds);
        }

        if ($this->request->filled('status')) {
            if ($this->request->status === 'completed') {
                $query->whereNotNull('completed_at');
            } else {
                $query->whereNull('completed_at');
            }
        }

        if ($this->request->filled('date_from')) {
            $query->where('created_at', '>=', $this->request->date_from);
        }

        if ($this->request->filled('date_to')) {
            $query->where('created_at', '<=', $this->request->date_to);
        }

        return $query;
    }

    public function headings(): array
    {
        return ['Funcionario', 'Treinamento', 'Progresso (%)', 'Status', 'Data de Conclusao'];
    }

    public function map($view): array
    {
        return [
            $view->user->name ?? 'N/A',
            $view->training->title ?? 'N/A',
            $view->progress_percent . '%',
            $view->completed_at ? 'Concluido' : 'Pendente',
            $view->completed_at?->format('d/m/Y') ?? '-',
        ];
    }
}
```

- [ ] **Step 2: Create ReportController**

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Exports\TrainingCompletionExport;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Training;
use App\Models\TrainingView;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $trainings = Training::all();
        $groups = Group::all();

        $query = TrainingView::with(['user', 'training']);

        if ($request->filled('training_id')) {
            $query->where('training_id', $request->training_id);
        }

        if ($request->filled('group_id')) {
            $groupUserIds = Group::find($request->group_id)
                ?->users()->pluck('users.id') ?? collect();
            $query->whereIn('user_id', $groupUserIds);
        }

        if ($request->filled('status')) {
            if ($request->status === 'completed') {
                $query->whereNotNull('completed_at');
            } else {
                $query->whereNull('completed_at');
            }
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        $views = $query->paginate(15);

        return view('admin.reports.index', compact('views', 'trainings', 'groups'));
    }

    public function exportPdf(Request $request)
    {
        $query = TrainingView::with(['user', 'training'])->limit(1000);

        if ($request->filled('training_id')) {
            $query->where('training_id', $request->training_id);
        }
        if ($request->filled('group_id')) {
            $groupUserIds = Group::find($request->group_id)
                ?->users()->pluck('users.id') ?? collect();
            $query->whereIn('user_id', $groupUserIds);
        }
        if ($request->filled('status')) {
            $request->status === 'completed'
                ? $query->whereNotNull('completed_at')
                : $query->whereNull('completed_at');
        }
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        $views = $query->get();

        $pdf = Pdf::loadView('admin.reports.pdf', compact('views'));
        return $pdf->download('relatorio-treinamentos.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new TrainingCompletionExport($request), 'relatorio-treinamentos.xlsx');
    }
}
```

- [ ] **Step 3: Create report views**

`admin/reports/index.blade.php`: Filters (training select, status select, date range), table with results, export buttons (PDF, Excel).
`admin/reports/pdf.blade.php`: Simple table layout for PDF export.

- [ ] **Step 4: Commit**

```bash
git add app/Http/Controllers/Admin/ReportController.php app/Exports/ resources/views/admin/reports/
git commit -m "feat: add training completion reports with PDF and Excel export"
```

---

## Task 15: Subscription and Asaas Integration

**Files:**
- Create: `app/Services/AsaasService.php`
- Create: `app/Http/Controllers/SubscriptionController.php`
- Create: `app/Http/Controllers/AsaasWebhookController.php`
- Create: `resources/views/subscription/plans.blade.php`
- Create: `resources/views/subscription/show.blade.php`
- Create: `tests/Feature/Services/AsaasServiceTest.php`

- [ ] **Step 1: Create AsaasService**

```php
<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AsaasService
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.asaas.base_url', 'https://sandbox.asaas.com/api/v3');
        $this->apiKey = config('services.asaas.api_key', '');
    }

    public function createCustomer(Company $company, string $email): ?string
    {
        $response = Http::withHeaders(['access_token' => $this->apiKey])
            ->post("{$this->baseUrl}/customers", [
                'name' => $company->name,
                'email' => $email,
                'externalReference' => $company->id,
            ]);

        if ($response->successful()) {
            $customerId = $response->json('id');
            $company->update(['asaas_customer_id' => $customerId]);
            return $customerId;
        }

        Log::error('Asaas createCustomer failed', ['response' => $response->json()]);
        return null;
    }

    public function createSubscription(Company $company, Plan $plan, string $paymentMethod): ?string
    {
        $billingType = match ($paymentMethod) {
            'boleto' => 'BOLETO',
            'pix' => 'PIX',
            'credit_card' => 'CREDIT_CARD',
        };

        $response = Http::withHeaders(['access_token' => $this->apiKey])
            ->post("{$this->baseUrl}/subscriptions", [
                'customer' => $company->asaas_customer_id,
                'billingType' => $billingType,
                'value' => $plan->price,
                'cycle' => 'MONTHLY',
                'description' => "TreinaHub - Plano {$plan->name}",
                'externalReference' => $company->id,
            ]);

        if ($response->successful()) {
            $subscriptionId = $response->json('id');

            $company->subscription()->update([
                'plan_id' => $plan->id,
                'asaas_subscription_id' => $subscriptionId,
                'status' => 'active',
                'current_period_start' => now(),
                'current_period_end' => now()->addMonth(),
            ]);

            return $subscriptionId;
        }

        Log::error('Asaas createSubscription failed', ['response' => $response->json()]);
        return null;
    }

    public function handleWebhook(array $payload): void
    {
        $event = $payload['event'] ?? null;
        $payment = $payload['payment'] ?? [];
        $externalReference = $payment['externalReference'] ?? null;

        if (!$event || !$externalReference) {
            return;
        }

        $subscription = Subscription::withoutGlobalScopes()
            ->where('company_id', $externalReference)
            ->first();

        if (!$subscription) {
            return;
        }

        // Idempotency check
        $asaasPaymentId = $payment['id'] ?? null;
        if ($asaasPaymentId && Payment::withoutGlobalScopes()->where('asaas_payment_id', $asaasPaymentId)->exists()) {
            return;
        }

        match ($event) {
            'PAYMENT_CONFIRMED', 'PAYMENT_RECEIVED' => $this->handlePaymentConfirmed($subscription, $payment),
            'PAYMENT_OVERDUE' => $this->handlePaymentOverdue($subscription, $payment),
            default => null,
        };
    }

    private function mapPaymentMethod(string $billingType): string
    {
        return match (strtolower($billingType)) {
            'boleto' => 'boleto',
            'pix' => 'pix',
            'credit_card' => 'credit_card',
            default => 'pix',
        };
    }

    private function handlePaymentConfirmed(Subscription $subscription, array $payment): void
    {
        $subscription->update([
            'status' => 'active',
            'current_period_start' => now(),
            'current_period_end' => now()->addMonth(),
        ]);

        try {
            Payment::create([
                'company_id' => $subscription->company_id,
                'subscription_id' => $subscription->id,
                'asaas_payment_id' => $payment['id'] ?? null,
                'amount' => $payment['value'] ?? 0,
                'status' => 'confirmed',
                'payment_method' => $this->mapPaymentMethod($payment['billingType'] ?? 'PIX'),
                'paid_at' => now(),
                'due_date' => $payment['dueDate'] ?? now()->toDateString(),
            ]);
        } catch (\Exception $e) {
            Log::error('Asaas webhook payment create failed', ['error' => $e->getMessage()]);
        }
    }

    private function handlePaymentOverdue(Subscription $subscription, array $payment): void
    {
        $subscription->update(['status' => 'past_due']);

        try {
            Payment::create([
                'company_id' => $subscription->company_id,
                'subscription_id' => $subscription->id,
                'asaas_payment_id' => $payment['id'] ?? null,
                'amount' => $payment['value'] ?? 0,
                'status' => 'overdue',
                'payment_method' => $this->mapPaymentMethod($payment['billingType'] ?? 'PIX'),
                'due_date' => $payment['dueDate'] ?? now()->toDateString(),
            ]);
        } catch (\Exception $e) {
            Log::error('Asaas webhook payment create failed', ['error' => $e->getMessage()]);
        }
    }

    public function cancelSubscription(Subscription $subscription): bool
    {
        if (!$subscription->asaas_subscription_id) {
            return false;
        }

        $response = Http::withHeaders(['access_token' => $this->apiKey])
            ->delete("{$this->baseUrl}/subscriptions/{$subscription->asaas_subscription_id}");

        if ($response->successful()) {
            $subscription->update(['status' => 'cancelled']);
            return true;
        }

        return false;
    }
}
```

- [ ] **Step 2: Add Asaas config**

Add to `config/services.php`:
```php
'asaas' => [
    'api_key' => env('ASAAS_API_KEY'),
    'base_url' => env('ASAAS_BASE_URL', 'https://sandbox.asaas.com/api/v3'),
    'webhook_token' => env('ASAAS_WEBHOOK_TOKEN'),
],
```

- [ ] **Step 3: Create SubscriptionController**

```php
<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Services\AsaasService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function plans()
    {
        $plans = Plan::where('active', true)->get();
        $currentSubscription = auth()->user()->company->subscription;

        return view('subscription.plans', compact('plans', 'currentSubscription'));
    }

    public function subscribe(Request $request, AsaasService $asaas)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'payment_method' => 'required|in:boleto,pix,credit_card',
        ]);

        $company = auth()->user()->company;
        $plan = Plan::findOrFail($request->plan_id);

        if (!$company->asaas_customer_id) {
            $asaas->createCustomer($company, auth()->user()->email);
        }

        $result = $asaas->createSubscription($company, $plan, $request->payment_method);

        if ($result) {
            return redirect()->route('dashboard')
                ->with('success', "Assinatura do plano {$plan->name} ativada!");
        }

        return back()->with('error', 'Erro ao processar pagamento. Tente novamente.');
    }

    public function show()
    {
        $company = auth()->user()->company;
        $subscription = $company->subscription()->with('plan')->first();
        $payments = $subscription?->payments()->latest()->paginate(10) ?? collect();

        return view('subscription.show', compact('subscription', 'payments'));
    }
}
```

- [ ] **Step 4: Create AsaasWebhookController**

```php
<?php

namespace App\Http\Controllers;

use App\Services\AsaasService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AsaasWebhookController extends Controller
{
    public function handle(Request $request, AsaasService $asaas): JsonResponse
    {
        $token = $request->header('asaas-access-token');

        if ($token !== config('services.asaas.webhook_token')) {
            return response()->json(['status' => 'ok']);
        }

        $asaas->handleWebhook($request->all());

        return response()->json(['status' => 'ok']);
    }
}
```

- [ ] **Step 5: Create subscription views**

`subscription/plans.blade.php`: Plan cards with price, features, select button. Payment method selection.
`subscription/show.blade.php`: Current plan info, status, payment history table.

- [ ] **Step 6: Commit**

```bash
git add app/Services/AsaasService.php app/Http/Controllers/SubscriptionController.php app/Http/Controllers/AsaasWebhookController.php config/services.php resources/views/subscription/
git commit -m "feat: add Asaas subscription management and webhook handling"
```

---

## Task 16: Admin — Company Settings (Logo + Colors)

**Files:**
- Create: `app/Http/Controllers/Admin/CompanySettingsController.php`
- Create: `resources/views/admin/settings/edit.blade.php`

- [ ] **Step 1: Create CompanySettingsController**

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CompanySettingsController extends Controller
{
    public function edit()
    {
        $company = auth()->user()->company;
        return view('admin.settings.edit', compact('company'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'primary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $company = auth()->user()->company;

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store("public/logos/{$company->id}");
            $company->logo_path = str_replace('public/', 'storage/', $path);
        }

        $company->primary_color = $request->primary_color;
        $company->secondary_color = $request->secondary_color;
        $company->save();

        return back()->with('success', 'Configuracoes atualizadas.');
    }
}
```

- [ ] **Step 2: Create settings view**

`admin/settings/edit.blade.php`: Form with logo upload (preview current), color pickers for primary/secondary.

- [ ] **Step 3: Create storage symlink**

```bash
php artisan storage:link
```

- [ ] **Step 4: Commit**

```bash
git add app/Http/Controllers/Admin/CompanySettingsController.php resources/views/admin/settings/
git commit -m "feat: add company settings for logo and color customization"
```

---

## Task 17: Instructor Module

**Files:**
- Create: `app/Http/Controllers/Instructor/TrainingController.php`
- Create: `app/Policies/TrainingPolicy.php`
- Create: `resources/views/instructor/trainings/index.blade.php`
- Create: `resources/views/instructor/trainings/create.blade.php`
- Create: `resources/views/instructor/trainings/edit.blade.php`

- [ ] **Step 1: Create TrainingPolicy**

```bash
php artisan make:policy TrainingPolicy --model=Training
```

```php
<?php

namespace App\Policies;

use App\Models\Training;
use App\Models\User;

class TrainingPolicy
{
    public function update(User $user, Training $training): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isInstructor() && $training->created_by === $user->id;
    }

    public function delete(User $user, Training $training): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isInstructor() && $training->created_by === $user->id;
    }
}
```

Register in `app/Providers/AppServiceProvider.php` boot() or via auto-discovery.

- [ ] **Step 2: Create Instructor TrainingController**

Similar to Admin TrainingController but scoped to `created_by = auth()->id()`. Uses TrainingPolicy for authorization.

- [ ] **Step 3: Create instructor training views**

Reuse similar structure to admin training views but with instructor layout/navigation.

- [ ] **Step 4: Commit**

```bash
git add app/Http/Controllers/Instructor/ app/Policies/ resources/views/instructor/trainings/
git commit -m "feat: add instructor training management with ownership policy"
```

---

## Task 18: Super Admin Panel

**Files:**
- Create: `app/Http/Controllers/SuperAdmin/DashboardController.php`
- Create: `app/Http/Controllers/SuperAdmin/CompanyController.php`
- Create: `app/Http/Controllers/SuperAdmin/SubscriptionController.php`
- Create: `app/Http/Controllers/SuperAdmin/PaymentController.php`
- Create: `app/Http/Controllers/SuperAdmin/PlanController.php`
- Create: `resources/views/super-admin/dashboard.blade.php`
- Create: `resources/views/super-admin/companies/index.blade.php`
- Create: `resources/views/super-admin/companies/show.blade.php`
- Create: `resources/views/super-admin/subscriptions/index.blade.php`
- Create: `resources/views/super-admin/payments/index.blade.php`
- Create: `resources/views/super-admin/plans/index.blade.php`
- Create: `resources/views/super-admin/plans/create.blade.php`
- Create: `resources/views/super-admin/plans/edit.blade.php`
- Create: `database/seeders/SuperAdminSeeder.php`

- [ ] **Step 1: Create SuperAdmin DashboardController**

```php
<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Payment;
use App\Models\Subscription;

class DashboardController extends Controller
{
    public function index()
    {
        $metrics = [
            'total_companies' => Company::count(),
            'active_subscriptions' => Subscription::withoutGlobalScopes()
                ->whereIn('status', ['active', 'trial'])->count(),
            'monthly_revenue' => Payment::withoutGlobalScopes()
                ->where('status', 'confirmed')
                ->whereMonth('paid_at', now()->month)
                ->sum('amount'),
            'trial_companies' => Subscription::withoutGlobalScopes()
                ->where('status', 'trial')->count(),
        ];

        return view('super-admin.dashboard', compact('metrics'));
    }
}
```

- [ ] **Step 2: Create CompanyController (Super Admin)**

CRUD for companies — list all companies (withoutGlobalScopes), view details, activate/deactivate.

- [ ] **Step 3: Create SubscriptionController and PaymentController (Super Admin)**

Read-only listings of all subscriptions and payments (withoutGlobalScopes).

- [ ] **Step 4: Create PlanController (Super Admin)**

Full CRUD for plans — create, edit, deactivate plans.

- [ ] **Step 5: Create SuperAdminSeeder**

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@treinahub.com.br',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
        ]);
    }
}
```

```bash
php artisan db:seed --class=SuperAdminSeeder
```

- [ ] **Step 6: Create all super admin views**

Views for dashboard, companies list/show, subscriptions list, payments list, plans CRUD.

- [ ] **Step 7: Commit**

```bash
git add app/Http/Controllers/SuperAdmin/ resources/views/super-admin/ database/seeders/SuperAdminSeeder.php
git commit -m "feat: add super admin panel with company, subscription, and plan management"
```

---

## Task 19: Landing Page

**Files:**
- Modify: `resources/views/welcome.blade.php`

- [ ] **Step 1: Create landing page**

Replace `welcome.blade.php` with a marketing landing page:
- Hero section: "Treine sua equipe. Certifique com seguranca."
- Features section: 3-4 feature cards
- Plans section: pricing cards from database
- CTA: "Comece gratis por 7 dias"
- Footer

- [ ] **Step 2: Commit**

```bash
git add resources/views/welcome.blade.php
git commit -m "feat: add marketing landing page"
```

---

## Task 20: Email Notifications

**Files:**
- Create: `app/Notifications/WelcomeNotification.php`
- Create: `app/Notifications/TrialExpiringNotification.php`
- Create: `app/Notifications/PaymentConfirmedNotification.php`
- Create: `app/Notifications/PaymentOverdueNotification.php`

- [ ] **Step 1: Create WelcomeNotification**

```bash
php artisan make:notification WelcomeNotification
```

```php
<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Bem-vindo ao TreinaHub!')
            ->greeting("Ola, {$notifiable->name}!")
            ->line('Sua empresa foi cadastrada com sucesso no TreinaHub.')
            ->line('Voce tem 7 dias de teste gratuito para explorar todas as funcionalidades.')
            ->action('Acessar Dashboard', url('/dashboard'))
            ->line('Comece criando seus treinamentos e cadastrando sua equipe.');
    }
}
```

- [ ] **Step 2: Create TrialExpiringNotification**

```bash
php artisan make:notification TrialExpiringNotification
```

```php
<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrialExpiringNotification extends Notification
{
    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Seu periodo de teste expira em 2 dias')
            ->greeting("Ola, {$notifiable->name}!")
            ->line('Seu periodo de teste no TreinaHub expira em 2 dias.')
            ->line('Para continuar usando a plataforma, escolha um plano de assinatura.')
            ->action('Escolher Plano', url('/subscription/plans'))
            ->line('Se tiver duvidas, entre em contato conosco.');
    }
}
```

- [ ] **Step 3: Create PaymentConfirmedNotification and PaymentOverdueNotification**

```bash
php artisan make:notification PaymentConfirmedNotification
php artisan make:notification PaymentOverdueNotification
```

PaymentConfirmedNotification:
```php
<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentConfirmedNotification extends Notification
{
    public function __construct(private float $amount) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pagamento confirmado - TreinaHub')
            ->greeting("Ola, {$notifiable->name}!")
            ->line('Seu pagamento de R$ ' . number_format($this->amount, 2, ',', '.') . ' foi confirmado.')
            ->line('Sua assinatura esta ativa.')
            ->action('Acessar Dashboard', url('/dashboard'));
    }
}
```

PaymentOverdueNotification:
```php
<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentOverdueNotification extends Notification
{
    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pagamento atrasado - TreinaHub')
            ->greeting("Ola, {$notifiable->name}!")
            ->line('Seu pagamento esta atrasado.')
            ->line('Voce tem 7 dias de carencia antes do bloqueio do acesso.')
            ->action('Atualizar Pagamento', url('/subscription/plans'))
            ->line('Entre em contato se precisar de ajuda.');
    }
}
```

- [ ] **Step 4: Dispatch notifications from registration and webhook**

In `RegisteredUserController::store()`, after creating the user:
```php
$user->notify(new \App\Notifications\WelcomeNotification());
```

In `AsaasService::handlePaymentConfirmed()`, after creating payment:
```php
$admin = \App\Models\User::withoutGlobalScopes()
    ->where('company_id', $subscription->company_id)
    ->where('role', 'admin')
    ->first();
$admin?->notify(new \App\Notifications\PaymentConfirmedNotification($payment['value'] ?? 0));
```

In `AsaasService::handlePaymentOverdue()`, after creating payment:
```php
$admin = \App\Models\User::withoutGlobalScopes()
    ->where('company_id', $subscription->company_id)
    ->where('role', 'admin')
    ->first();
$admin?->notify(new \App\Notifications\PaymentOverdueNotification());
```

- [ ] **Step 5: Commit**

```bash
git add app/Notifications/ app/Http/Controllers/Auth/RegisteredUserController.php app/Services/AsaasService.php
git commit -m "feat: add email notifications for welcome, trial, payment confirmed/overdue"
```

---

## Task 21: Scheduled Commands (Trial Expiry + Subscription Expiry)

**Files:**
- Create: `app/Console/Commands/CheckTrialExpiring.php`
- Create: `app/Console/Commands/ExpireOverdueSubscriptions.php`
- Modify: `routes/console.php` (register schedule)

- [ ] **Step 1: Create CheckTrialExpiring command**

```bash
php artisan make:command CheckTrialExpiring
```

```php
<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\User;
use App\Notifications\TrialExpiringNotification;
use Illuminate\Console\Command;

class CheckTrialExpiring extends Command
{
    protected $signature = 'subscriptions:check-trial-expiring';
    protected $description = 'Notify companies whose trial expires in 2 days';

    public function handle(): int
    {
        $expiringSubscriptions = Subscription::withoutGlobalScopes()
            ->where('status', 'trial')
            ->whereDate('trial_ends_at', now()->addDays(2)->toDateString())
            ->get();

        foreach ($expiringSubscriptions as $subscription) {
            $admin = User::withoutGlobalScopes()
                ->where('company_id', $subscription->company_id)
                ->where('role', 'admin')
                ->first();

            $admin?->notify(new TrialExpiringNotification());
        }

        $this->info("Notified {$expiringSubscriptions->count()} companies.");
        return Command::SUCCESS;
    }
}
```

- [ ] **Step 2: Create ExpireOverdueSubscriptions command**

```bash
php artisan make:command ExpireOverdueSubscriptions
```

```php
<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Console\Command;

class ExpireOverdueSubscriptions extends Command
{
    protected $signature = 'subscriptions:expire-overdue';
    protected $description = 'Expire subscriptions that have been past_due for more than 7 days';

    public function handle(): int
    {
        $pastDueSubscriptions = Subscription::withoutGlobalScopes()
            ->where('status', 'past_due')
            ->get();

        $expired = 0;

        foreach ($pastDueSubscriptions as $subscription) {
            $latestOverduePayment = Payment::withoutGlobalScopes()
                ->where('subscription_id', $subscription->id)
                ->where('status', 'overdue')
                ->latest('due_date')
                ->first();

            if ($latestOverduePayment && $latestOverduePayment->due_date->addDays(7)->isPast()) {
                $subscription->update(['status' => 'expired']);
                $expired++;
            }
        }

        $this->info("Expired {$expired} subscriptions.");
        return Command::SUCCESS;
    }
}
```

- [ ] **Step 3: Also expire trials that have ended**

Add to `ExpireOverdueSubscriptions` handle() (or create separate command):
```php
// Expire ended trials
$expiredTrials = Subscription::withoutGlobalScopes()
    ->where('status', 'trial')
    ->where('trial_ends_at', '<', now())
    ->update(['status' => 'expired']);

$this->info("Expired {$expiredTrials} trials.");
```

- [ ] **Step 4: Register scheduled commands**

In `routes/console.php`:
```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('subscriptions:check-trial-expiring')->dailyAt('09:00');
Schedule::command('subscriptions:expire-overdue')->dailyAt('00:00');
```

Note for shared hosting: add cron job `* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1`

- [ ] **Step 5: Commit**

```bash
git add app/Console/Commands/ routes/console.php
git commit -m "feat: add scheduled commands for trial expiry and overdue subscription expiry"
```

---

## Task 22: Final Integration Tests and Cleanup

**Files:**
- Create: `tests/Feature/RegistrationFlowTest.php`
- Modify: various files for cleanup

- [ ] **Step 1: Write full registration flow test**

```php
<?php

namespace Tests\Feature;

use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_registration_creates_company_user_and_trial(): void
    {
        Plan::create(['name' => 'Basic', 'price' => 99.90, 'max_users' => 50, 'max_trainings' => 20]);

        $response = $this->post('/register', [
            'company_name' => 'Minha Empresa',
            'name' => 'Joao Admin',
            'email' => 'joao@empresa.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');

        $this->assertDatabaseHas('companies', ['name' => 'Minha Empresa', 'slug' => 'minha-empresa']);
        $this->assertDatabaseHas('users', ['email' => 'joao@empresa.com', 'role' => 'admin']);
        $this->assertDatabaseHas('subscriptions', ['status' => 'trial']);
    }
}
```

- [ ] **Step 2: Run all tests**

```bash
php artisan test
```

Expected: All tests pass.

- [ ] **Step 3: Final cleanup**

- Remove unused Breeze views/routes if any
- Ensure all routes are protected with correct middleware
- Verify `.env.example` has all required keys
- Verify `storage/` directories have correct permissions

- [ ] **Step 4: Commit**

```bash
git add -A
git commit -m "feat: add integration tests and final cleanup"
```

---

## Task Summary

| Task | Description | Dependencies |
|------|-------------|-------------|
| 1 | Project Scaffolding | None |
| 2 | Database Migrations | Task 1 |
| 3 | Eloquent Models + Trait + Seeder | Task 2 |
| 4 | Middleware | Task 3 |
| 5 | Routes + Base Controllers | Task 4 |
| 6 | Blade Layout + UI Components | Task 5 |
| 7 | Admin User CRUD | Task 6 |
| 8 | Admin Group CRUD | Task 6 |
| 9 | Admin Training CRUD + Quiz | Task 6 |
| 10 | Admin Training Assignments | Task 9 |
| 11 | Employee Watch Training + Progress | Task 6 |
| 12 | Employee Quiz System | Task 11 |
| 13 | Certificate Generation | Task 12 |
| 14 | Admin Reports | Task 7, 8, 11 |
| 15 | Subscription + Asaas | Task 5 |
| 16 | Company Settings | Task 6 |
| 17 | Instructor Module | Task 9 |
| 18 | Super Admin Panel | Task 5 |
| 19 | Landing Page | Task 1 |
| 20 | Email Notifications | Task 5, 15 |
| 21 | Scheduled Commands | Task 20 |
| 22 | Integration Tests + Cleanup | All |

**Parallel groups (tasks that can run concurrently):**
- After Task 6: Tasks 7, 8, 9, 11, 15, 16, 18, 19 can all start
- After their deps: Tasks 10, 12, 14, 17, 20 can start
- Sequential finish: Tasks 13, 21, 22
