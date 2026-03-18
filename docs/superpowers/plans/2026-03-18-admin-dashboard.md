# Admin Dashboard Redesign — Implementation Plan

> **For agentic workers:** REQUIRED: Use superpowers:subagent-driven-development (if subagents available) or superpowers:executing-plans to implement this plan. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the minimal admin dashboard (4 emoji cards + 3 quick links) with a rich, data-dense dashboard featuring KPI cards, a donut chart, top trainings, recent activity, and improved quick actions.

**Architecture:** Two-file change only: (1) extend `DashboardController::adminDashboard()` to collect more data inside the existing 5-minute cache, and (2) rewrite `admin/dashboard.blade.php` with four sections using the data. Chart.js (already loaded in `app.blade.php`) renders one donut chart via `@push('scripts')`.

**Tech Stack:** Laravel 11, Blade, TailwindCSS (CDN), Chart.js (CDN), Alpine.js (CDN — already loaded). PHP binary: `/opt/homebrew/Cellar/php@8.3/8.3.30/bin/php`. Run tests with `/opt/homebrew/Cellar/php@8.3/8.3.30/bin/php artisan test`.

---

## File Map

| Action | File | Responsibility |
|--------|------|----------------|
| Modify | `app/Http/Controllers/DashboardController.php` | Extend `adminDashboard()` with new cached queries |
| Rewrite | `resources/views/admin/dashboard.blade.php` | Full 4-section dashboard view |
| Create | `tests/Feature/Admin/DashboardControllerTest.php` | Feature tests for admin dashboard data |

---

## Task 1: Extend `adminDashboard()` with new data + tests

**Files:**
- Modify: `app/Http/Controllers/DashboardController.php` (method `adminDashboard`, lines 28–48)
- Create: `tests/Feature/Admin/DashboardControllerTest.php`

### Context for the implementer

The controller at `app/Http/Controllers/DashboardController.php` has a private method `adminDashboard()` (line 28) that currently returns 5 keys in a cached array. We need to extend it to also return: `completion_rate`, `top_trainings` (Collection of 5 Training models), `recent_employees` (Collection of 5 User models), `recent_completions` (Collection of 5 TrainingView models with loaded `user` and `training` relations).

`plan_user_limit` must be fetched **outside** the cache closure (it uses `auth()->user()` which must not be serialized into cache).

The `Training` model at `app/Models/Training.php` has a `completionRate()` method that correctly joins through `group_user` to count assigned users. Use it for per-training progress bars.

Existing test helper pattern (copy from `tests/Feature/Admin/UserControllerTest.php`):
```php
private function createAdminWithSubscription(int $maxUsers = 50): User
{
    $plan = Plan::create(['name' => 'Basic', 'price' => 99.90, 'max_users' => $maxUsers, 'max_trainings' => 20]);
    $company = Company::create(['name' => 'Test', 'slug' => 'test']);
    Subscription::create(['company_id' => $company->id, 'plan_id' => $plan->id, 'status' => 'active']);
    return User::create([
        'name' => 'Admin', 'email' => 'admin@test.com',
        'password' => 'password', 'company_id' => $company->id, 'role' => 'admin', 'active' => true,
    ]);
}
```

---

- [ ] **Step 1.1: Write the failing test file**

Create `tests/Feature/Admin/DashboardControllerTest.php`:

```php
<?php

namespace Tests\Feature\Admin;

use App\Models\Certificate;
use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\TrainingView;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createAdminWithSubscription(int $maxUsers = 50): User
    {
        $plan = Plan::create(['name' => 'Basic', 'price' => 99.90, 'max_users' => $maxUsers, 'max_trainings' => 20]);
        $company = Company::create(['name' => 'Test Co', 'slug' => 'test-co']);
        Subscription::create(['company_id' => $company->id, 'plan_id' => $plan->id, 'status' => 'active']);
        return User::create([
            'name' => 'Admin User', 'email' => 'admin@test.com',
            'password' => 'password', 'company_id' => $company->id, 'role' => 'admin', 'active' => true,
        ]);
    }

    public function test_admin_dashboard_returns_200(): void
    {
        $admin = $this->createAdminWithSubscription();
        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_admin_dashboard_passes_all_required_metric_keys(): void
    {
        $admin = $this->createAdminWithSubscription();
        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertViewHas('metrics');
        $metrics = $response->viewData('metrics');

        foreach ([
            'total_employees', 'trainings_created', 'trainings_completed',
            'trainings_pending', 'certificates_issued', 'completion_rate',
            'top_trainings', 'recent_employees', 'recent_completions', 'plan_user_limit',
        ] as $key) {
            $this->assertArrayHasKey($key, $metrics, "Missing key: {$key}");
        }
    }

    public function test_completion_rate_is_zero_with_no_views(): void
    {
        $admin = $this->createAdminWithSubscription();
        $response = $this->actingAs($admin)->get('/dashboard');
        $metrics = $response->viewData('metrics');
        $this->assertSame(0.0, (float) $metrics['completion_rate']);
    }

    public function test_completion_rate_calculated_correctly(): void
    {
        $admin = $this->createAdminWithSubscription();
        $emp1 = User::create(['name' => 'Emp1', 'email' => 'emp1@test.com', 'password' => 'x', 'company_id' => $admin->company_id, 'role' => 'employee', 'active' => true]);
        $emp2 = User::create(['name' => 'Emp2', 'email' => 'emp2@test.com', 'password' => 'x', 'company_id' => $admin->company_id, 'role' => 'employee', 'active' => true]);
        $training = Training::create([
            'company_id' => $admin->company_id, 'created_by' => $admin->id,
            'title' => 'Test Training', 'video_url' => 'https://youtube.com/watch?v=test',
            'video_provider' => 'youtube', 'active' => true,
        ]);
        // 2 completed, 1 pending = 66.7%
        TrainingView::create(['company_id' => $admin->company_id, 'training_id' => $training->id, 'user_id' => $admin->id, 'completed_at' => now()]);
        TrainingView::create(['company_id' => $admin->company_id, 'training_id' => $training->id, 'user_id' => $emp1->id, 'completed_at' => now()]);
        TrainingView::create(['company_id' => $admin->company_id, 'training_id' => $training->id, 'user_id' => $emp2->id, 'completed_at' => null]);

        \Illuminate\Support\Facades\Cache::flush();
        $response = $this->actingAs($admin)->get('/dashboard');
        $metrics = $response->viewData('metrics');
        $this->assertSame(66.7, $metrics['completion_rate']);
    }

    public function test_plan_user_limit_reflects_plan(): void
    {
        $admin = $this->createAdminWithSubscription(25);
        $response = $this->actingAs($admin)->get('/dashboard');
        $metrics = $response->viewData('metrics');
        $this->assertSame(25, $metrics['plan_user_limit']);
    }
}
```

- [ ] **Step 1.2: Run tests to verify they fail**

```bash
cd /Users/joaofilipibritto/Projetos/treinaedu/.worktrees/implementation
/opt/homebrew/Cellar/php@8.3/8.3.30/bin/php artisan test tests/Feature/Admin/DashboardControllerTest.php
```

Expected: FAIL (missing metric keys like `completion_rate`, `top_trainings`, etc.)

- [ ] **Step 1.3: Update `adminDashboard()` in `DashboardController.php`**

Replace the entire `adminDashboard()` method (lines 28–48) with:

```php
private function adminDashboard()
{
    $companyId = auth()->user()->company_id;
    $planUserLimit = auth()->user()->company->subscription?->plan?->max_users;

    $metrics = Cache::remember("dashboard_metrics_{$companyId}", 300, function () use ($companyId) {
        $completed = TrainingView::withoutGlobalScope('company')
            ->where('company_id', $companyId)->whereNotNull('completed_at')->count();
        $pending = TrainingView::withoutGlobalScope('company')
            ->where('company_id', $companyId)->whereNull('completed_at')->count();
        $total = $completed + $pending;

        return [
            'total_employees'     => User::where('company_id', $companyId)->where('role', 'employee')->count(),
            'trainings_created'   => Training::withoutGlobalScope('company')->where('company_id', $companyId)->count(),
            'trainings_completed' => $completed,
            'trainings_pending'   => $pending,
            'certificates_issued' => Certificate::withoutGlobalScope('company')->where('company_id', $companyId)->count(),
            'completion_rate'     => $total > 0 ? round(($completed / $total) * 100, 1) : 0.0,
            'top_trainings'       => Training::withoutGlobalScope('company')
                ->where('company_id', $companyId)
                ->withCount([
                    'views',
                    'views as completed_count' => fn($q) => $q->whereNotNull('completed_at'),
                ])
                ->orderByDesc('completed_count')
                ->limit(5)
                ->get(),
            'recent_employees'    => User::where('company_id', $companyId)
                ->where('role', 'employee')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get(),
            'recent_completions'  => TrainingView::withoutGlobalScope('company')
                ->where('company_id', $companyId)
                ->whereNotNull('completed_at')
                ->with(['user', 'training'])
                ->orderByDesc('completed_at')
                ->limit(5)
                ->get(),
        ];
    });

    $metrics['plan_user_limit'] = $planUserLimit;
    return view('admin.dashboard', compact('metrics'));
}
```

Make sure the existing `use` imports at the top of the file already include `Certificate`, `Training`, `TrainingView`, and `User`. They do (lines 5–8).

- [ ] **Step 1.4: Run tests — expect them to pass**

```bash
/opt/homebrew/Cellar/php@8.3/8.3.30/bin/php artisan test tests/Feature/Admin/DashboardControllerTest.php
```

Expected: All 4 tests PASS. If `test_completion_rate_calculated_correctly` fails with a user_id collision, adjust the employee user IDs.

- [ ] **Step 1.5: Commit**

```bash
cd /Users/joaofilipibritto/Projetos/treinaedu/.worktrees/implementation
git add app/Http/Controllers/DashboardController.php tests/Feature/Admin/DashboardControllerTest.php
git commit -m "feat: extend adminDashboard with completion rate, top trainings, recent activity"
```

---

## Task 2: Rewrite `admin/dashboard.blade.php`

**Files:**
- Modify: `resources/views/admin/dashboard.blade.php` (full rewrite)

### Context for the implementer

The layout component `<x-layout.app>` is at `resources/views/components/layout/app.blade.php`. It:
- Accepts a `title` prop (`$title ?? config('app.name')`)
- Renders `{{ $slot }}` inside `<main class="flex-1 overflow-y-auto p-6">`
- Has `@stack('scripts')` at the bottom — use `@push('scripts')` for Chart.js initialization

The existing `<x-ui.card>` component at `resources/views/components/ui/card.blade.php` accepts `title`, `value`, `icon` (emoji), `color`. We will NOT use it for the new KPI cards — the new cards need a sub-text line which `<x-ui.card>` doesn't support. Write inline card HTML instead.

CSS variables `var(--primary)` and `var(--secondary)` are set by the layout from company theme colors (default blue). Use `text-primary` (defined in layout styles) for icon accents.

Chart.js is available globally as `Chart` (loaded via CDN in layout head).

---

- [ ] **Step 2.1: Write the full new view**

Replace the entire content of `resources/views/admin/dashboard.blade.php` with:

```blade
<x-layout.app title="Dashboard">

    {{-- ===================== --}}
    {{-- Section 1: KPI Cards  --}}
    {{-- ===================== --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">

        {{-- Colaboradores --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-500 font-medium">Colaboradores</p>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $metrics['total_employees'] }}</p>
            <p class="text-xs text-gray-400 mt-1">
                @if($metrics['plan_user_limit'])
                    de {{ $metrics['plan_user_limit'] }} disponíveis
                @else
                    Ilimitado
                @endif
            </p>
        </div>

        {{-- Treinamentos --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-500 font-medium">Treinamentos</p>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $metrics['trainings_created'] }}</p>
            <p class="text-xs text-gray-400 mt-1">treinamentos criados</p>
        </div>

        {{-- Em Andamento --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-lg bg-yellow-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-500 font-medium">Em Andamento</p>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $metrics['trainings_pending'] }}</p>
            <p class="text-xs text-gray-400 mt-1">aguardando conclusão</p>
        </div>

        {{-- Taxa de Conclusão --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-lg bg-teal-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-500 font-medium">Taxa de Conclusão</p>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $metrics['completion_rate'] }}%</p>
            <p class="text-xs text-gray-400 mt-1">dos treinamentos concluídos</p>
        </div>

        {{-- Certificados --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-500 font-medium">Certificados</p>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $metrics['certificates_issued'] }}</p>
            <p class="text-xs text-gray-400 mt-1">certificados emitidos</p>
        </div>

    </div>

    {{-- =========================================== --}}
    {{-- Section 2: Donut Chart + Top Treinamentos   --}}
    {{-- =========================================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-6">

        {{-- Donut Chart (3/5) --}}
        <div class="lg:col-span-3 bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Status dos Treinamentos</h3>
            @if($metrics['trainings_completed'] + $metrics['trainings_pending'] > 0)
                <div class="flex flex-col items-center">
                    <div class="relative w-48 h-48">
                        <canvas id="trainingStatusChart"></canvas>
                    </div>
                    <div class="flex items-center gap-6 mt-4 text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                            <span class="text-gray-600">Concluídos <span class="font-semibold text-gray-800">{{ $metrics['trainings_completed'] }}</span></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                            <span class="text-gray-600">Em Andamento <span class="font-semibold text-gray-800">{{ $metrics['trainings_pending'] }}</span></span>
                        </div>
                    </div>
                </div>
            @else
                <div class="flex flex-col items-center justify-center h-48 text-gray-400">
                    <svg class="w-12 h-12 mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <p class="text-sm">Nenhum dado ainda</p>
                </div>
            @endif
        </div>

        {{-- Top Treinamentos (2/5) --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Top Treinamentos</h3>
            @if($metrics['top_trainings']->isEmpty())
                <p class="text-sm text-gray-400 mt-8 text-center">Nenhum treinamento criado ainda</p>
            @else
                <div class="space-y-4">
                    @foreach($metrics['top_trainings'] as $training)
                        @php $rate = $training->completionRate(); @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <p class="text-sm text-gray-700 truncate max-w-[160px]" title="{{ $training->title }}">{{ $training->title }}</p>
                                <span class="text-xs font-semibold text-gray-500 ml-2 flex-shrink-0">{{ $training->completed_count }} concl.</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1.5">
                                <div class="bg-blue-500 h-1.5 rounded-full transition-all" style="width: {{ $rate }}%"></div>
                            </div>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $rate }}% de conclusão</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- =========================================== --}}
    {{-- Section 3: Últimos Usuários + Conclusões    --}}
    {{-- =========================================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        {{-- Últimos Colaboradores --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-700">Últimos Colaboradores</h3>
                <a href="{{ route('users.index') }}" class="text-xs text-blue-600 hover:underline">Ver todos</a>
            </div>
            @if($metrics['recent_employees']->isEmpty())
                <p class="text-sm text-gray-400 text-center py-6">Nenhum colaborador cadastrado</p>
            @else
                <div class="space-y-3">
                    @foreach($metrics['recent_employees'] as $employee)
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-xs font-semibold text-blue-700">{{ strtoupper(substr($employee->name, 0, 2)) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $employee->name }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ $employee->email }}</p>
                            </div>
                            <span class="text-xs text-gray-400 flex-shrink-0">{{ $employee->created_at->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Conclusões Recentes --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-700">Conclusões Recentes</h3>
                <a href="{{ route('reports.index') }}" class="text-xs text-blue-600 hover:underline">Ver relatório</a>
            </div>
            @if($metrics['recent_completions']->isEmpty())
                <p class="text-sm text-gray-400 text-center py-6">Nenhuma conclusão registrada</p>
            @else
                <div class="space-y-3">
                    @foreach($metrics['recent_completions'] as $view)
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $view->user?->name ?? '—' }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ $view->training?->title ?? '—' }}</p>
                            </div>
                            <span class="text-xs text-gray-400 flex-shrink-0">{{ $view->completed_at->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- ====================== --}}
    {{-- Section 4: Quick Actions --}}
    {{-- ====================== --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

        <a href="{{ route('users.create') }}" class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition group text-center">
            <div class="w-10 h-10 rounded-xl bg-blue-50 group-hover:bg-blue-100 flex items-center justify-center mx-auto mb-3 transition">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-700">Novo Colaborador</p>
            <p class="text-xs text-gray-400 mt-0.5">Adicionar usuário</p>
        </a>

        <a href="{{ route('trainings.create') }}" class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition group text-center">
            <div class="w-10 h-10 rounded-xl bg-green-50 group-hover:bg-green-100 flex items-center justify-center mx-auto mb-3 transition">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-700">Novo Treinamento</p>
            <p class="text-xs text-gray-400 mt-0.5">Criar conteúdo</p>
        </a>

        <a href="{{ route('training-assignments.create') }}" class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition group text-center">
            <div class="w-10 h-10 rounded-xl bg-yellow-50 group-hover:bg-yellow-100 flex items-center justify-center mx-auto mb-3 transition">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-700">Atribuir Treinamento</p>
            <p class="text-xs text-gray-400 mt-0.5">Vincular a grupos</p>
        </a>

        <a href="{{ route('reports.index') }}" class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition group text-center">
            <div class="w-10 h-10 rounded-xl bg-purple-50 group-hover:bg-purple-100 flex items-center justify-center mx-auto mb-3 transition">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-700">Ver Relatórios</p>
            <p class="text-xs text-gray-400 mt-0.5">Análises detalhadas</p>
        </a>

    </div>

</x-layout.app>

@push('scripts')
<script>
    @if($metrics['trainings_completed'] + $metrics['trainings_pending'] > 0)
    (function () {
        const ctx = document.getElementById('trainingStatusChart');
        if (!ctx) return;
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Concluídos', 'Em Andamento'],
                datasets: [{
                    data: [{{ $metrics['trainings_completed'] }}, {{ $metrics['trainings_pending'] }}],
                    backgroundColor: ['#10B981', '#3B82F6'],
                    borderWidth: 0,
                    hoverOffset: 4,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${ctx.label}: ${ctx.parsed}`,
                        },
                    },
                },
            },
        });
    })();
    @endif
</script>
@endpush
```

- [ ] **Step 2.2: Run the full test suite to verify nothing broke**

```bash
/opt/homebrew/Cellar/php@8.3/8.3.30/bin/php artisan test
```

Expected: All existing tests pass + the 4 new DashboardControllerTest tests pass. Pay attention to any failures mentioning `admin.dashboard` or missing view variables.

- [ ] **Step 2.3: Commit**

```bash
git add resources/views/admin/dashboard.blade.php
git commit -m "feat: redesign admin dashboard with KPIs, chart, top trainings, recent activity"
```
