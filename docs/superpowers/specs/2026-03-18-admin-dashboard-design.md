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
- Chart.js donut chart with 3 segments:
  - Concluídos (`trainings_completed`) — green (#10B981)
  - Em Andamento (`trainings_pending`) — blue (#3B82F6)
  - Não Iniciados (`trainings_not_started`) — gray (#E5E7EB)
- `trainings_not_started` = total training-user assignments that have no TrainingView record yet
- Legend below the chart (3 colored dots + labels + counts)
- If all values are 0, show a centered "Nenhum dado ainda" message instead of chart

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

Add to the cached closure:

```php
'trainings_not_started' => TrainingAssignment::withoutGlobalScope('company')
    ->where('company_id', $companyId)
    ->whereDoesntHave('training.views', fn($q) => $q->where('user_id', ...) )
    // simpler: count assignments with no TrainingView for that user/training pair
    ->count(), // see implementation note below
'completion_rate'       => ... (computed from completed/pending),
'top_trainings'         => Training::withoutGlobalScope('company')
    ->where('company_id', $companyId)
    ->withCount([
        'views',
        'views as completed_count' => fn($q) => $q->whereNotNull('completed_at'),
    ])
    ->orderByDesc('completed_count')
    ->limit(5)
    ->get(),
'recent_employees'      => User::where('company_id', $companyId)
    ->where('role', 'employee')
    ->orderByDesc('created_at')
    ->limit(5)
    ->get(),
'recent_completions'    => TrainingView::withoutGlobalScope('company')
    ->where('company_id', $companyId)
    ->whereNotNull('completed_at')
    ->with(['user', 'training'])
    ->orderByDesc('completed_at')
    ->limit(5)
    ->get(),
'plan_user_limit'       => auth()->user()->company->subscription?->plan?->max_users,
```

**Implementation note for `trainings_not_started`:** count TrainingAssignment rows (one per user-group-training combo) that have no matching TrainingView. Use a subquery or `whereDoesntHave`. This may require adjusting depending on TrainingAssignment model relationships.

The existing cache key stays at 300 seconds. All new queries are appended to the same `Cache::remember` closure.

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
