# Admin Dashboard Redesign — Spec

## Goal

Replace the minimal admin dashboard (4 metric cards + 3 emoji quick links) with a rich, data-dense dashboard that gives an admin a complete operational picture of their company: KPIs, charts, top content, recent activity, and fast actions.

## Scope

- File modified: `resources/views/admin/dashboard.blade.php`
- File modified: `app/Http/Controllers/DashboardController.php` (`adminDashboard()` method only)
- No new routes, no new models, no new migrations
- Zero new frontend dependencies (Chart.js and Alpine.js already loaded in `app.blade.php`)

## Architecture

All data is fetched in `DashboardController::adminDashboard()` and passed to the Blade view. The existing 5-minute cache key `dashboard_metrics_{$companyId}` is extended to cover all new data. The view uses Chart.js (already loaded) for one donut chart and plain Tailwind for all other elements.

## Sections

### 1. KPI Row — 5 Cards

Displayed as a `grid grid-cols-2 lg:grid-cols-5` row at the top.

| Card | Value | Sub-text |
|------|-------|----------|
| Colaboradores | `total_employees` | "de {plan_user_limit} disponíveis" (show "Ilimitado" if null) |
| Treinamentos | `trainings_created` | "treinamentos criados" |
| Em Andamento | `trainings_pending` | "aguardando conclusão" |
| Taxa de Conclusão | `completion_rate`% | "dos treinamentos concluídos" |
| Certificados | `certificates_issued` | "certificados emitidos" |

`completion_rate` = `(trainings_completed / (trainings_completed + trainings_pending)) * 100`, rounded to 1 decimal. Returns 0 when denominator is 0.

`plan_user_limit` comes from `auth()->user()->company->subscription->plan->max_users` (nullable chain — null = unlimited).

### 2. Middle Row — 2 Columns

**Left (60%): Donut Chart — Status dos Treinamentos**
- Chart.js donut chart with 2 segments (both from TrainingView table — same unit):
  - Concluídos (`trainings_completed`) — green (#10B981)
  - Em Andamento (`trainings_pending`) — blue (#3B82F6)
- Legend below the chart (2 colored dots + labels + counts)
- If both values are 0, show a centered "Nenhum dado ainda" message instead of chart

**Right (40%): Top 5 Treinamentos por Conclusão**
- Each row: training title (truncated), completion count, inline progress bar
- `completion_rate_per_training` = completed views / total assigned users for that training
- Sorted descending by completion count
- If no trainings, show "Nenhum treinamento criado ainda"

### 3. Bottom Row — 2 Columns

**Left: Últimos Usuários Cadastrados**
- Last 5 `employees` ordered by `created_at DESC`
- Each row: avatar (initials, colored circle), name, email (truncated), relative date ("há 2 dias")
- Link "Ver todos" → `route('users.index')`

**Right: Conclusões Recentes**
- Last 5 TrainingView records where `completed_at IS NOT NULL`, ordered by `completed_at DESC`
- Each row: user name, training title (truncated), formatted date
- Link "Ver relatório completo" → `route('reports.index')`

### 4. Quick Actions Row

4 cards in a `grid-cols-2 md:grid-cols-4` grid:

| Label | Icon | Route |
|-------|------|-------|
| Novo Colaborador | user-plus SVG | `route('users.create')` |
| Novo Treinamento | video SVG | `route('trainings.create')` |
| Atribuir Treinamento | clipboard SVG | `route('training-assignments.create')` |
| Ver Relatórios | chart SVG | `route('reports.index')` |

Each card: white bg, rounded-xl, shadow-sm, hover:shadow-md, icon in primary color, label below.

## Controller Changes — `adminDashboard()`

The method is refactored as follows:

```php
private function adminDashboard()
{
    $companyId = auth()->user()->company_id;
    // plan_user_limit fetched outside cache (requires auth context)
    $planUserLimit = auth()->user()->company->subscription?->plan?->max_users;

    $metrics = Cache::remember("dashboard_metrics_{$companyId}", 300, function () use ($companyId) {
        $completed = TrainingView::withoutGlobalScope('company')
            ->where('company_id', $companyId)->whereNotNull('completed_at')->count();
        $pending   = TrainingView::withoutGlobalScope('company')
            ->where('company_id', $companyId)->whereNull('completed_at')->count();
        $total     = $completed + $pending;

        return [
            'total_employees'     => User::where('company_id', $companyId)->where('role', 'employee')->count(),
            'trainings_created'   => Training::withoutGlobalScope('company')->where('company_id', $companyId)->count(),
            'trainings_completed' => $completed,
            'trainings_pending'   => $pending,
            'certificates_issued' => Certificate::withoutGlobalScope('company')->where('company_id', $companyId)->count(),
            'completion_rate'     => $total > 0 ? round(($completed / $total) * 100, 1) : 0,
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

**`completion_rate_per_training` for Top 5 bars:** Each training in `top_trainings` has `completed_count` (from withCount). For the progress bar percentage, use `$training->completionRate()` which does the correct join through `group_user`. This is N+1 for 5 items — acceptable for a cached dashboard.

The existing cache key stays at 300 seconds.

## Data passed to view

`compact('metrics')` — all data nested under `$metrics` array key for consistency.

## Styling Constraints

- Follow existing Tailwind patterns in the codebase (no custom CSS beyond what's in app.blade.php)
- Cards use white bg + rounded-xl + shadow-sm
- Primary color via CSS variable `var(--primary)` for accents
- Chart.js canvas initialized in `@push('scripts')` block
- All text in Portuguese (pt-BR)

## Out of Scope

- Notifications bell / real-time updates
- Date range filters on the dashboard
- Export from dashboard
- Mobile sidebar toggle (already handled by app.blade.php)
