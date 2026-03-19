<x-layout.app title="Dashboard">

    @php
        $usagePercent = $metrics['plan_user_limit']
            ? min(100, round(($metrics['total_employees'] / $metrics['plan_user_limit']) * 100))
            : null;
    @endphp

    {{-- Banner de boas-vindas --}}
    <div class="rounded-xl p-6 mb-6 text-white" style="background: linear-gradient(to right, var(--secondary), var(--primary))">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold mb-0.5">Painel de Gestão</h2>
                <p class="text-white/70 text-sm">Acompanhe o desempenho da equipe em tempo real.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('users.create') }}"
                   class="inline-flex items-center gap-2 bg-white hover:bg-white/90 transition text-sm font-semibold px-4 py-2 rounded-lg"
                   style="color: var(--primary)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Novo Colaborador
                </a>
                <a href="{{ route('trainings.create') }}"
                   class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 border border-white/30 text-white transition text-sm font-semibold px-4 py-2 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Novo Treinamento
                </a>
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">

        <div class="bg-white rounded-xl shadow-sm p-5 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <a href="{{ route('users.index') }}" class="text-xs text-primary hover:underline">Ver →</a>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $metrics['total_employees'] }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Colaboradores</p>
            </div>
            @if($usagePercent !== null)
                <div>
                    <div class="flex justify-between text-xs text-gray-400 mb-1">
                        <span>Uso do plano</span>
                        <span class="font-medium {{ $usagePercent >= 90 ? 'text-red-500' : 'text-gray-500' }}">{{ $usagePercent }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div class="h-1.5 rounded-full {{ $usagePercent >= 90 ? 'bg-red-400' : 'bg-primary' }}" style="width: {{ $usagePercent }}%"></div>
                    </div>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M4.5 5.653c0-1.426 1.529-2.33 2.779-1.643l11.54 6.348c1.295.712 1.295 2.573 0 3.285L7.28 19.991c-1.25.687-2.779-.217-2.779-1.643V5.653z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <a href="{{ route('trainings.index') }}" class="text-xs text-primary hover:underline">Ver →</a>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $metrics['trainings_created'] }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Treinamentos</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-xl bg-yellow-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $metrics['trainings_pending'] }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Em Andamento</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-xl bg-teal-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <a href="{{ route('reports.index') }}" class="text-xs text-primary hover:underline">Ver →</a>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $metrics['completion_rate'] }}%</p>
                <p class="text-xs text-gray-400 mt-0.5">Taxa de Conclusão</p>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-1.5">
                <div class="bg-teal-500 h-1.5 rounded-full" style="width: {{ $metrics['completion_rate'] }}%"></div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $metrics['certificates_issued'] }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Certificados</p>
            </div>
        </div>

    </div>

    {{-- Gráfico + Top Treinamentos --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-6">

        {{-- Donut Chart (3/5) --}}
        <div class="lg:col-span-3 bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-sm font-semibold text-gray-700">Status dos Treinamentos</h3>
                <a href="{{ route('reports.index') }}" class="text-xs text-primary hover:underline">Ver relatório →</a>
            </div>
            @if($metrics['trainings_completed'] + $metrics['trainings_pending'] > 0)
                <div class="flex flex-col sm:flex-row items-center gap-8">
                    <div class="relative w-52 h-52 flex-shrink-0">
                        <canvas id="trainingStatusChart"></canvas>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <p class="text-3xl font-bold text-gray-800">{{ $metrics['completion_rate'] }}%</p>
                            <p class="text-xs text-gray-400">concluídos</p>
                        </div>
                    </div>
                    <div class="flex-1 space-y-4 w-full">
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                                    <span class="text-sm text-gray-600">Concluídos</span>
                                </div>
                                <span class="text-sm font-bold text-gray-800">{{ $metrics['trainings_completed'] }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                @php $completedPct = $metrics['trainings_completed'] + $metrics['trainings_pending'] > 0 ? round($metrics['trainings_completed'] / ($metrics['trainings_completed'] + $metrics['trainings_pending']) * 100) : 0; @endphp
                                <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $completedPct }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-2.5 h-2.5 rounded-full bg-primary"></div>
                                    <span class="text-sm text-gray-600">Em Andamento</span>
                                </div>
                                <span class="text-sm font-bold text-gray-800">{{ $metrics['trainings_pending'] }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-primary h-2 rounded-full" style="width: {{ 100 - $completedPct }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="flex flex-col items-center justify-center h-48 text-gray-400">
                    <svg class="w-12 h-12 mb-2 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <p class="text-sm">Nenhum dado ainda</p>
                </div>
            @endif
        </div>

        {{-- Top Treinamentos (2/5) --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-sm font-semibold text-gray-700">Top Treinamentos</h3>
                <a href="{{ route('trainings.index') }}" class="text-xs text-primary hover:underline">Ver todos →</a>
            </div>
            @if(empty($metrics['top_trainings']))
                <p class="text-sm text-gray-400 mt-8 text-center">Nenhum treinamento criado ainda</p>
            @else
                <div class="space-y-4">
                    @foreach($metrics['top_trainings'] as $i => $training)
                        <div>
                            <div class="flex items-center gap-2 mb-1.5">
                                <span class="w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0
                                    {{ $i === 0 ? 'bg-yellow-100 text-yellow-700' : ($i === 1 ? 'bg-gray-100 text-gray-600' : 'bg-orange-50 text-orange-600') }}">
                                    {{ $i + 1 }}
                                </span>
                                <p class="text-sm text-gray-700 truncate flex-1" title="{{ $training['title'] }}">{{ $training['title'] }}</p>
                                <span class="text-xs font-semibold text-gray-500 flex-shrink-0">{{ $training['completed_count'] }} concl.</span>
                            </div>
                            <div class="pl-7">
                                <div class="w-full bg-gray-100 rounded-full h-1.5">
                                    <div class="bg-primary h-1.5 rounded-full" style="width: {{ min(100, max(0, $training['completion_rate'])) }}%"></div>
                                </div>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $training['completion_rate'] }}% de conclusão</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- Últimos Colaboradores + Conclusões Recentes --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-primary/60"></div>
                    <h3 class="text-sm font-semibold text-gray-700">Últimos Colaboradores</h3>
                </div>
                <a href="{{ route('users.index') }}" class="text-xs text-primary hover:underline">Ver todos →</a>
            </div>
            @if(empty($metrics['recent_employees']))
                <p class="text-sm text-gray-400 text-center py-10">Nenhum colaborador cadastrado</p>
            @else
                <div class="divide-y divide-gray-50">
                    @foreach($metrics['recent_employees'] as $employee)
                        <div class="flex items-center gap-3 px-6 py-3.5">
                            <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                <span class="text-xs font-bold text-primary">{{ strtoupper(substr($employee['name'] ?? '?', 0, 2)) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $employee['name'] ?? '—' }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ $employee['email'] }}</p>
                            </div>
                            <span class="text-xs text-gray-400 flex-shrink-0">{{ optional($employee['created_at'])->diffForHumans() ?? '—' }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-green-400"></div>
                    <h3 class="text-sm font-semibold text-gray-700">Conclusões Recentes</h3>
                </div>
                <a href="{{ route('reports.index') }}" class="text-xs text-primary hover:underline">Ver relatório →</a>
            </div>
            @if(empty($metrics['recent_completions']))
                <p class="text-sm text-gray-400 text-center py-10">Nenhuma conclusão registrada</p>
            @else
                <div class="divide-y divide-gray-50">
                    @foreach($metrics['recent_completions'] as $completion)
                        <div class="flex items-center gap-3 px-6 py-3.5">
                            <div class="w-9 h-9 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $completion['user_name'] ?? '—' }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ $completion['training_title'] ?? '—' }}</p>
                            </div>
                            <span class="text-xs text-gray-400 flex-shrink-0">{{ optional($completion['completed_at'])->diffForHumans() ?? '—' }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- Ações Rápidas --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('users.create') }}" class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition group flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-primary/10 group-hover:bg-primary/20 flex items-center justify-center flex-shrink-0 transition">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-700">Novo Colaborador</p>
                <p class="text-xs text-gray-400">Adicionar usuário</p>
            </div>
        </a>

        <a href="{{ route('trainings.create') }}" class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition group flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-green-50 group-hover:bg-green-100 flex items-center justify-center flex-shrink-0 transition">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-700">Novo Treinamento</p>
                <p class="text-xs text-gray-400">Criar conteúdo</p>
            </div>
        </a>

        <a href="{{ route('trainings.index') }}" class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition group flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-yellow-50 group-hover:bg-yellow-100 flex items-center justify-center flex-shrink-0 transition">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-700">Atribuir Treinamento</p>
                <p class="text-xs text-gray-400">Vincular a grupos</p>
            </div>
        </a>

        <a href="{{ route('reports.index') }}" class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition group flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-purple-50 group-hover:bg-purple-100 flex items-center justify-center flex-shrink-0 transition">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-700">Ver Relatórios</p>
                <p class="text-xs text-gray-400">Análises detalhadas</p>
            </div>
        </a>
    </div>

</x-layout.app>

@push('scripts')
<script>
    @if($metrics['trainings_completed'] + $metrics['trainings_pending'] > 0)
    (function () {
        const ctx = document.getElementById('trainingStatusChart');
        if (!ctx) return;
        const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--primary').trim() || '#3B82F6';
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Concluídos', 'Em Andamento'],
                datasets: [{
                    data: [{{ $metrics['trainings_completed'] }}, {{ $metrics['trainings_pending'] }}],
                    backgroundColor: ['#10B981', primaryColor],
                    borderWidth: 0,
                    hoverOffset: 6,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: tooltipItem => ` ${tooltipItem.label}: ${tooltipItem.parsed}`,
                        },
                    },
                },
            },
        });
    })();
    @endif
</script>
@endpush
