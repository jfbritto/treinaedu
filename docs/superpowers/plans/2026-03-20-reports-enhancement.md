# Reports Enhancement Implementation Plan

> **For agentic workers:** REQUIRED: Use superpowers:subagent-driven-development or superpowers:executing-plans to implement this plan. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Transform the reports page from static filtered list to a dynamic multi-tab dashboard with real-time filtering, global metrics, and comparative analytics via charts.

**Architecture:**
- Backend: Add aggregation methods to `TrainingView` model + new AJAX endpoint `/admin/reports/filter` that returns JSON
- Frontend: Alpine.js handles filter state + debounced AJAX calls + Chart.js renders graphics
- Components: Reusable Blade components for filters, KPI cards, tabs, and chart containers
- Database: Optimize queries with proper indexing (if needed) for performance

**Tech Stack:** Laravel 11, Blade, Alpine.js 3, Chart.js, Tailwind CSS 3, MySQL

---

## Phase 1: Backend Models & Query Methods

### Task 1.1: Add aggregation methods to TrainingView model

**Files:**
- Modify: `app/Models/TrainingView.php`

- [ ] **Step 1: Review current TrainingView model structure**

Run: `grep -n "class TrainingView" app/Models/TrainingView.php -A 30`

Expected: See current relationships and methods. Note any existing scopes/methods to reuse.

- [ ] **Step 2: Add global statistics scope**

Add to `TrainingView` model:

```php
// In app/Models/TrainingView.php

public function scopeWithFilters($query, array $filters = [])
{
    if (!empty($filters['training_id'])) {
        $query->where('training_id', $filters['training_id']);
    }
    if (!empty($filters['group_id'])) {
        $query->whereHas('user.groups', fn($q) => $q->where('groups.id', $filters['group_id']));
    }
    if ($filters['status'] === 'completed') {
        $query->whereNotNull('completed_at');
    } elseif ($filters['status'] === 'pending') {
        $query->whereNull('completed_at');
    }
    if (!empty($filters['date_from'])) {
        $query->where('created_at', '>=', $filters['date_from']);
    }
    if (!empty($filters['date_to'])) {
        $query->where('created_at', '<=', $filters['date_to']);
    }
    return $query;
}

public static function getGlobalStats(array $filters = []): array
{
    $query = static::withFilters($filters);

    return [
        'total' => $query->count(),
        'completed' => $query->whereNotNull('completed_at')->count(),
        'pending' => $query->whereNull('completed_at')->count(),
        'avg_progress' => round($query->avg('progress_percent') ?? 0),
    ];
}
```

- [ ] **Step 3: Add group analysis method**

```php
public static function getGroupAnalysis(array $filters = []): array
{
    return static::withFilters($filters)
        ->join('group_user', 'training_views.user_id', '=', 'group_user.user_id')
        ->join('groups', 'group_user.group_id', '=', 'groups.id')
        ->groupBy('groups.id', 'groups.name')
        ->selectRaw('groups.id, groups.name, COUNT(*) as total,
                     COUNT(CASE WHEN completed_at IS NOT NULL THEN 1 END) as completed,
                     COUNT(CASE WHEN completed_at IS NULL THEN 1 END) as pending,
                     ROUND(AVG(progress_percent)) as avg_progress')
        ->get()
        ->toArray();
}
```

- [ ] **Step 4: Add instructor analysis method**

```php
public static function getInstructorAnalysis(array $filters = []): array
{
    return static::withFilters($filters)
        ->join('trainings', 'training_views.training_id', '=', 'trainings.id')
        ->join('users as instructors', 'trainings.instructor_id', '=', 'instructors.id')
        ->groupBy('instructors.id', 'instructors.name')
        ->selectRaw('instructors.id, instructors.name, COUNT(*) as total,
                     COUNT(CASE WHEN completed_at IS NOT NULL THEN 1 END) as completed,
                     ROUND(AVG(progress_percent)) as avg_progress')
        ->get()
        ->toArray();
}
```

- [ ] **Step 5: Add period analysis method**

```php
public static function getPeriodAnalysis(array $filters = []): array
{
    return static::withFilters($filters)
        ->selectRaw("DATE_TRUNC('week', created_at) as period,
                     COUNT(CASE WHEN completed_at IS NOT NULL THEN 1 END) as completed,
                     COUNT(*) as total")
        ->groupByRaw("DATE_TRUNC('week', created_at)")
        ->orderBy('period')
        ->get()
        ->map(fn($row) => [
            'period' => $row->period,
            'completed' => $row->completed,
            'total' => $row->total,
            'growth_percent' => $row->total > 0 ? round(($row->completed / $row->total) * 100) : 0,
        ])
        ->toArray();
}
```

- [ ] **Step 6: Run tests to verify no breaking changes**

Run: `php artisan test --filter TrainingView`

Expected: All existing tests pass (or create basic ones if none exist)

- [ ] **Step 7: Commit**

```bash
git add app/Models/TrainingView.php
git commit -m "feat: add aggregation methods to TrainingView model for analytics"
```

---

## Phase 2: Controller & Routes

### Task 2.1: Create filter AJAX endpoint

**Files:**
- Modify: `app/Http/Controllers/Admin/ReportsController.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Review existing ReportsController**

Run: `cat app/Http/Controllers/Admin/ReportsController.php | head -50`

Expected: See current index() method structure

- [ ] **Step 2: Add filter() method to ReportsController**

```php
// In app/Http/Controllers/Admin/ReportsController.php

public function filter(Request $request)
{
    $filters = $request->validate([
        'training_id' => 'nullable|exists:trainings,id',
        'group_id' => 'nullable|exists:groups,id',
        'status' => 'nullable|in:completed,pending',
        'date_from' => 'nullable|date',
        'date_to' => 'nullable|date',
        'tab' => 'nullable|in:general,group,instructor,period',
    ]);

    $tab = $filters['tab'] ?? 'general';
    unset($filters['tab']);

    // Get global stats (always)
    $stats = TrainingView::getGlobalStats($filters);

    // Get tab-specific data
    $data = match($tab) {
        'group' => TrainingView::getGroupAnalysis($filters),
        'instructor' => TrainingView::getInstructorAnalysis($filters),
        'period' => TrainingView::getPeriodAnalysis($filters),
        default => TrainingView::withFilters($filters)->paginate(15),
    };

    return response()->json([
        'stats' => $stats,
        'data' => $data,
        'tab' => $tab,
    ]);
}
```

- [ ] **Step 3: Add route for filter endpoint**

In `routes/web.php`, add under admin routes:

```php
Route::get('/admin/reports/filter', [ReportsController::class, 'filter'])->name('reports.filter');
```

- [ ] **Step 4: Test endpoint manually**

Run: `curl "http://localhost:8000/admin/reports/filter?tab=general&training_id=1" -H "Accept: application/json"`

Expected: JSON response with stats and paginated data

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/Admin/ReportsController.php routes/web.php
git commit -m "feat: add AJAX filter endpoint for reports"
```

---

## Phase 3: Blade Components

### Task 3.1: Create reusable Blade components

**Files:**
- Create: `resources/views/components/reports/filter-sticky.blade.php`
- Create: `resources/views/components/reports/kpi-card.blade.php`
- Create: `resources/views/components/reports/tab-panel.blade.php`
- Create: `resources/views/components/reports/chart-container.blade.php`

- [ ] **Step 1: Create filter-sticky component**

```blade
{{-- resources/views/components/reports/filter-sticky.blade.php --}}
<div class="sticky top-0 z-40 bg-white border-b border-gray-100 shadow-sm"
     x-data="filterForm()"
     @submit.prevent="applyFilters()">
    <div class="px-6 py-4">
        <form @change="debounceFilter()" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                    Treinamento
                </label>
                <select name="training_id" x-model="filters.training_id"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:ring-2 focus:ring-primary">
                    <option value="">Todos</option>
                    @foreach($trainings as $training)
                        <option value="{{ $training->id }}">{{ $training->title }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                    Grupo
                </label>
                <select name="group_id" x-model="filters.group_id"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:ring-2 focus:ring-primary">
                    <option value="">Todos</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                    Status
                </label>
                <select name="status" x-model="filters.status"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:ring-2 focus:ring-primary">
                    <option value="">Todos</option>
                    <option value="completed">Concluído</option>
                    <option value="pending">Pendente</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                    Data início
                </label>
                <input type="date" name="date_from" x-model="filters.date_from"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:ring-2 focus:ring-primary">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                    Data fim
                </label>
                <input type="date" name="date_to" x-model="filters.date_to"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:ring-2 focus:ring-primary">
            </div>
        </form>

        <div class="mt-4 flex gap-2">
            <button type="button" @click="clearFilters()"
                    class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2 rounded-lg text-sm font-semibold transition">
                Limpar filtros
            </button>
            <div x-show="hasActiveFilters()" class="text-sm text-gray-600 flex items-center">
                <span class="inline-block bg-primary text-white px-2 py-1 rounded-full text-xs mr-2"
                      x-text="countActiveFilters() + ' filtro(s)'"></span>
            </div>
            <div x-show="isLoading" class="text-sm text-gray-500 flex items-center">
                <svg class="w-4 h-4 animate-spin mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Carregando...
            </div>
        </div>
    </div>
</div>
```

- [ ] **Step 2: Create KPI card component**

```blade
{{-- resources/views/components/reports/kpi-card.blade.php --}}
<div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-4">
    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
        {{ $slot }}
    </div>
    <div>
        <p class="text-2xl font-bold text-gray-800" x-text="$stats?.{{ $key }}"></p>
        <p class="text-xs text-gray-400">{{ $label }}</p>
    </div>
</div>
```

- [ ] **Step 3: Create tab-panel component**

```blade
{{-- resources/views/components/reports/tab-panel.blade.php --}}
<div x-show="activeTab === '{{ $name }}'"
     x-transition
     class="min-h-96">
    {{ $slot }}
</div>
```

- [ ] **Step 4: Create chart-container component**

```blade
{{-- resources/views/components/reports/chart-container.blade.php --}}
<div class="bg-white rounded-xl shadow-sm p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ $title }}</h3>
    <div class="relative" style="height: {{ $height ?? '300px' }}">
        <canvas id="{{ $chartId }}"></canvas>
    </div>
</div>
```

- [ ] **Step 5: Commit**

```bash
git add resources/views/components/reports/
git commit -m "feat: add reusable Blade components for reports page"
```

---

## Phase 4: Rewrite Main Reports View

### Task 4.1: Refactor reports/index.blade.php

**Files:**
- Modify: `resources/views/admin/reports/index.blade.php`

- [ ] **Step 1: Backup current view**

Run: `cp resources/views/admin/reports/index.blade.php resources/views/admin/reports/index.blade.php.bak`

- [ ] **Step 2: Rewrite main view with new structure**

```blade
{{-- resources/views/admin/reports/index.blade.php --}}
<x-layout.app title="Relatórios">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <p class="text-sm text-gray-500">Acompanhe o progresso e conclusões da equipe</p>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('reports.export.pdf', request()->query()) }}"
               class="inline-flex items-center gap-2 bg-primary hover:bg-secondary text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Exportar PDF
            </a>
            <a href="{{ route('reports.export.excel', request()->query()) }}"
               class="inline-flex items-center gap-2 border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                Exportar Excel
            </a>
        </div>
    </div>

    {{-- Sticky Filters --}}
    <x-reports.filter-sticky :trainings="$trainings" :groups="$groups" />

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6 relative z-30"
         x-data="{ stats: @json($stats ?? []) }"
         @filter-updated.window="stats = $event.detail.stats">

        <x-reports.kpi-card key="total" label="Registros totais">
            <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24"><path d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75zM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 01-1.875-1.875V8.625zM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 013 19.875v-6.75z"/></svg>
        </x-reports.kpi-card>

        <x-reports.kpi-card key="completed" label="Concluídos (total)">
            <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd"/></svg>
        </x-reports.kpi-card>

        <x-reports.kpi-card key="pending" label="Pendentes (total)">
            <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zM12.75 6a.75.75 0 00-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 000-1.5h-3.75V6z" clip-rule="evenodd"/></svg>
        </x-reports.kpi-card>

        <x-reports.kpi-card key="avg_progress" label="Progresso médio">
            <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24"><path d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75zM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 01-1.875-1.875V8.625zM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 013 19.875v-6.75z"/></svg>
        </x-reports.kpi-card>
    </div>

    {{-- Tabs Navigation --}}
    <div class="mb-6" x-data="reportsTabs()">
        <div class="flex gap-1 border-b border-gray-200">
            <button @click="setTab('general')"
                    :class="activeTab === 'general' ? 'border-b-2 border-primary text-primary' : 'text-gray-600 hover:text-gray-800'"
                    class="px-4 py-3 font-medium text-sm transition">
                Geral
            </button>
            <button @click="setTab('group')"
                    :class="activeTab === 'group' ? 'border-b-2 border-primary text-primary' : 'text-gray-600 hover:text-gray-800'"
                    class="px-4 py-3 font-medium text-sm transition">
                Por Grupo
            </button>
            <button @click="setTab('instructor')"
                    :class="activeTab === 'instructor' ? 'border-b-2 border-primary text-primary' : 'text-gray-600 hover:text-gray-800'"
                    class="px-4 py-3 font-medium text-sm transition">
                Por Instrutor
            </button>
            <button @click="setTab('period')"
                    :class="activeTab === 'period' ? 'border-b-2 border-primary text-primary' : 'text-gray-600 hover:text-gray-800'"
                    class="px-4 py-3 font-medium text-sm transition">
                Por Período
            </button>
        </div>
    </div>

    {{-- Tab Content --}}
    <div x-data="reportsContent()">
        {{-- General Tab --}}
        <x-reports.tab-panel name="general">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div x-show="isLoading" class="p-12 text-center">
                    <div class="inline-block animate-spin">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </div>
                </div>
                <div x-show="!isLoading" x-html="generalTableHtml"></div>
            </div>
        </x-reports.tab-panel>

        {{-- Group Tab --}}
        <x-reports.tab-panel name="group">
            <x-reports.chart-container chart-id="groupChart" title="Progresso por Grupo" height="350px" />
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div x-html="groupTableHtml"></div>
            </div>
        </x-reports.tab-panel>

        {{-- Instructor Tab --}}
        <x-reports.tab-panel name="instructor">
            <x-reports.chart-container chart-id="instructorChart" title="Performance dos Instrutores" height="350px" />
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div x-html="instructorTableHtml"></div>
            </div>
        </x-reports.tab-panel>

        {{-- Period Tab --}}
        <x-reports.tab-panel name="period">
            <x-reports.chart-container chart-id="periodChart" title="Progressão ao Longo do Tempo" height="350px" />
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div x-html="periodTableHtml"></div>
            </div>
        </x-reports.tab-panel>
    </div>

    <script src="{{ asset('js/pages/reports.js') }}"></script>

</x-layout.app>
```

- [ ] **Step 3: Test view loads without errors**

Run: `php artisan serve` and visit `/admin/reports`

Expected: Page loads with filters sticky, KPI cards show, tabs visible (but no data yet)

- [ ] **Step 4: Commit**

```bash
git add resources/views/admin/reports/index.blade.php
git commit -m "refactor: redesign reports page with tabs and modern layout"
```

---

## Phase 5: Frontend JavaScript

### Task 5.1: Create reports.js with AJAX logic

**Files:**
- Create: `resources/js/pages/reports.js`

- [ ] **Step 1: Create reports.js with Alpine data components**

```javascript
// resources/js/pages/reports.js
import Chart from 'chart.js/auto';

window.filterForm = function() {
    return {
        filters: {
            training_id: new URLSearchParams(window.location.search).get('training_id') || '',
            group_id: new URLSearchParams(window.location.search).get('group_id') || '',
            status: new URLSearchParams(window.location.search).get('status') || '',
            date_from: new URLSearchParams(window.location.search).get('date_from') || '',
            date_to: new URLSearchParams(window.location.search).get('date_to') || '',
        },
        isLoading: false,
        debounceTimer: null,

        debounceFilter() {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => {
                this.applyFilters();
            }, 400);
        },

        hasActiveFilters() {
            return Object.values(this.filters).some(v => v !== '');
        },

        countActiveFilters() {
            return Object.values(this.filters).filter(v => v !== '').length;
        },

        async applyFilters() {
            this.isLoading = true;
            const tab = document.querySelector('[x-data="reportsTabs()"]')?.__x?.getUnobservedData?.activeTab || 'general';

            try {
                const params = new URLSearchParams(this.filters);
                params.append('tab', tab);

                const response = await fetch(`/admin/reports/filter?${params}`);
                const json = await response.json();

                // Update stats globally
                window.dispatchEvent(new CustomEvent('filter-updated', {
                    detail: { stats: json.stats }
                }));

                // Notify tabs to update content
                window.dispatchEvent(new CustomEvent('data-updated', {
                    detail: { data: json.data, tab: json.tab }
                }));

                // Update URL
                window.history.replaceState({}, '', `?${params}`);
            } catch (error) {
                console.error('Filter error:', error);
            } finally {
                this.isLoading = false;
            }
        },

        clearFilters() {
            this.filters = {
                training_id: '',
                group_id: '',
                status: '',
                date_from: '',
                date_to: '',
            };
            this.applyFilters();
        }
    };
};

window.reportsTabs = function() {
    return {
        activeTab: 'general',

        setTab(tab) {
            this.activeTab = tab;
            document.querySelector('[x-data="filterForm()"]')?.__x?.getUnobservedData?.applyFilters?.();
        }
    };
};

window.reportsContent = function() {
    return {
        activeTab: 'general',
        isLoading: false,
        generalTableHtml: '',
        groupTableHtml: '',
        groupChart: null,
        instructorTableHtml: '',
        instructorChart: null,
        periodTableHtml: '',
        periodChart: null,

        init() {
            window.addEventListener('data-updated', (e) => {
                this.handleDataUpdate(e.detail.data, e.detail.tab);
            });
        },

        handleDataUpdate(data, tab) {
            this.isLoading = false;

            switch(tab) {
                case 'general':
                    this.renderGeneralTable(data);
                    break;
                case 'group':
                    this.renderGroupChart(data);
                    this.renderGroupTable(data);
                    break;
                case 'instructor':
                    this.renderInstructorChart(data);
                    this.renderInstructorTable(data);
                    break;
                case 'period':
                    this.renderPeriodChart(data);
                    this.renderPeriodTable(data);
                    break;
            }
        },

        renderGeneralTable(data) {
            // This would render the general table - implementation depends on data structure
            // For now, using placeholder
            this.generalTableHtml = `<div class="p-4">Table content</div>`;
        },

        renderGroupChart(data) {
            const ctx = document.getElementById('groupChart');
            if (!ctx) return;

            if (this.groupChart) this.groupChart.destroy();

            this.groupChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(row => row.name),
                    datasets: [{
                        label: 'Progresso Médio (%)',
                        data: data.map(row => row.avg_progress),
                        backgroundColor: data.map(row =>
                            row.avg_progress >= 75 ? '#10b981' :
                            row.avg_progress >= 50 ? '#3b82f6' : '#fbbf24'
                        ),
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                }
            });
        },

        renderGroupTable(data) {
            this.groupTableHtml = `
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400">Grupo</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400">Total</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400">Concluídos</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400">Pendentes</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400">% Médio</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        ${data.map(row => `
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">${row.name}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">${row.total}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">${row.completed}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">${row.pending}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-800">${row.avg_progress}%</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
        },

        renderInstructorChart(data) {
            const ctx = document.getElementById('instructorChart');
            if (!ctx) return;

            if (this.instructorChart) this.instructorChart.destroy();

            this.instructorChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(row => row.name),
                    datasets: [{
                        label: 'Progresso Médio (%)',
                        data: data.map(row => row.avg_progress),
                        backgroundColor: '#3b82f6',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                }
            });
        },

        renderInstructorTable(data) {
            this.instructorTableHtml = `
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400">Instrutor</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400">Total Alunos</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400">Concluídos</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400">% Médio</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        ${data.map(row => `
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">${row.name}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">${row.total}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">${row.completed}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-800">${row.avg_progress}%</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
        },

        renderPeriodChart(data) {
            const ctx = document.getElementById('periodChart');
            if (!ctx) return;

            if (this.periodChart) this.periodChart.destroy();

            this.periodChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(row => new Date(row.period).toLocaleDateString('pt-BR')),
                    datasets: [{
                        label: 'Conclusões',
                        data: data.map(row => row.completed),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                }
            });
        },

        renderPeriodTable(data) {
            this.periodTableHtml = `
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400">Período</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400">Conclusões</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400">% Crescimento</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        ${data.map((row, idx) => `
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">${new Date(row.period).toLocaleDateString('pt-BR')}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">${row.completed}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-800">
                                    ${row.growth_percent > 0 ? '↑' : row.growth_percent < 0 ? '↓' : '—'} ${row.growth_percent}%
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
        }
    };
};
```

- [ ] **Step 2: Update vite.config.js to include new JS file**

Run: `grep -n "input:" vite.config.js`

Expected: Find where JS inputs are defined. Add reports.js if not already included via glob pattern.

- [ ] **Step 3: Test reports.js compiles without errors**

Run: `npm run build`

Expected: Build succeeds, no errors

- [ ] **Step 4: Commit**

```bash
git add resources/js/pages/reports.js
git commit -m "feat: add AJAX and charting logic to reports page"
```

---

## Phase 6: Testing & Polish

### Task 6.1: Test filtering and data updates

**Files:**
- Test manually in browser
- Create (optional): `tests/Feature/ReportsControllerTest.php`

- [ ] **Step 1: Manual test - Load reports page**

Open: `http://localhost:8000/admin/reports`

Expected: Page loads, filters visible, KPIs show totals, tabs visible

- [ ] **Step 2: Test filter by training**

Click: Training dropdown → select a training → wait 400ms

Expected: Page updates without reload, KPIs update, tab content updates, URL changes

- [ ] **Step 3: Test multiple filters**

Select: Training + Group + Status date range

Expected: All filters apply simultaneously, data narrows, URL reflects all filters

- [ ] **Step 4: Test clear filters**

Click: "Limpar filtros"

Expected: All filter inputs clear, page resets to show all data, URL resets

- [ ] **Step 5: Test tab switching**

Click: "Por Grupo" tab

Expected: Chart renders, table shows group data, filters stay applied

- [ ] **Step 6: Create feature test (optional but recommended)**

```php
// tests/Feature/ReportsControllerTest.php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{Training, Group, User, TrainingView};

class ReportsControllerTest extends TestCase
{
    public function test_reports_page_loads()
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('reports.index'))
            ->assertStatus(200);
    }

    public function test_filter_endpoint_returns_json()
    {
        $training = Training::factory()->create();
        TrainingView::factory(5)->create(['training_id' => $training->id]);

        $this->actingAs(User::factory()->admin()->create())
            ->getJson(route('reports.filter', ['training_id' => $training->id]))
            ->assertStatus(200)
            ->assertJsonStructure(['stats', 'data', 'tab']);
    }

    public function test_global_stats_reflect_all_data()
    {
        TrainingView::factory(10)->create();
        TrainingView::factory(5)->create(['completed_at' => now()]);

        $this->actingAs(User::factory()->admin()->create())
            ->getJson(route('reports.filter'))
            ->assertJsonPath('stats.total', 15)
            ->assertJsonPath('stats.completed', 5)
            ->assertJsonPath('stats.pending', 10);
    }
}
```

Run: `php artisan test tests/Feature/ReportsControllerTest.php`

Expected: All tests pass

- [ ] **Step 7: Test responsiveness on mobile**

Open: DevTools → Toggle device toolbar → Mobile size

Expected: Filters stack vertically, tabs scroll horizontally, charts responsive

- [ ] **Step 8: Commit tests**

```bash
git add tests/Feature/ReportsControllerTest.php
git commit -m "test: add feature tests for reports filtering"
```

---

### Task 6.2: Performance optimization

**Files:**
- Modify: `app/Models/TrainingView.php`

- [ ] **Step 1: Add database indexes for common queries**

```php
// In a new migration: database/migrations/YYYY_MM_DD_XXXXXX_add_reports_indexes.php
Schema::table('training_views', function (Blueprint $table) {
    $table->index('training_id');
    $table->index('user_id');
    $table->index('completed_at');
    $table->index('created_at');
});
```

Run: `php artisan migrate`

Expected: Migration runs successfully

- [ ] **Step 2: Add query caching (optional)**

In `ReportsController::filter()`, wrap expensive queries:

```php
$stats = Cache::remember(
    'reports_stats_' . md5(json_encode($filters)),
    now()->addMinutes(5),
    fn() => TrainingView::getGlobalStats($filters)
);
```

- [ ] **Step 3: Test load time**

Run: DevTools → Network tab → Filter requests

Expected: `/admin/reports/filter` responds in <500ms

- [ ] **Step 4: Commit**

```bash
git add database/migrations/ app/Http/Controllers/Admin/ReportsController.php
git commit -m "perf: add indexes and caching to reports queries"
```

---

## Summary

**6 Phases, 12 Tasks:**

1. ✅ Backend Models (Task 1.1)
2. ✅ Controller & Routes (Task 2.1)
3. ✅ Blade Components (Task 3.1)
4. ✅ Redesigned View (Task 4.1)
5. ✅ Frontend JS (Task 5.1)
6. ✅ Testing & Performance (Task 6.1 + 6.2)

**Total estimate:** ~4-6 hours of focused development

**Key decisions:**
- Alpine.js for state (aligns with project)
- Chart.js for visualizations (lightweight, no build complexity)
- Blade components for reusability
- Query scopes for DRY data access
- Debounced AJAX for UX

---

**Ready to execute?**
