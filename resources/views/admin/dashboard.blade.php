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
                    <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24"><path d="M8.25 6.75a3.75 3.75 0 117.5 0 3.75 3.75 0 01-7.5 0zM15.75 9.75a3 3 0 116 0 3 3 0 01-6 0zM2.25 9.75a3 3 0 116 0 3 3 0 01-6 0zM6.31 15.117A6.745 6.745 0 0112 12a6.745 6.745 0 016.709 7.498.75.75 0 01-.372.568A12.696 12.696 0 0112 21.75c-2.305 0-4.47-.612-6.337-1.684a.75.75 0 01-.372-.568 6.787 6.787 0 011.019-4.38z"/><path d="M5.082 14.254a8.287 8.287 0 00-1.308 5.135 9.687 9.687 0 01-1.764-.44l-.115-.04a.563.563 0 01-.373-.487l-.01-.121a3.75 3.75 0 013.57-4.047zM20.226 19.389a8.287 8.287 0 00-1.308-5.135 3.75 3.75 0 013.57 4.047l-.01.121a.563.563 0 01-.373.486l-.115.04c-.567.2-1.156.349-1.764.441z"/></svg>
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
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm14.024-.983a1.125 1.125 0 010 1.966l-5.603 3.113A1.125 1.125 0 019 15.113V8.887c0-.857.921-1.4 1.671-.983l5.603 3.113z" clip-rule="evenodd"/></svg>
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
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zM12.75 6a.75.75 0 00-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 000-1.5h-3.75V6z" clip-rule="evenodd"/></svg>
                </div>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $metrics['trainings_pending'] }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Em Andamento</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd"/></svg>
                </div>
                <a href="{{ route('reports.index') }}" class="text-xs text-primary hover:underline">Ver →</a>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $metrics['completion_rate'] }}%</p>
                <p class="text-xs text-gray-400 mt-0.5">Taxa de Conclusão</p>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-1.5">
                <div class="bg-primary h-1.5 rounded-full" style="width: {{ $metrics['completion_rate'] }}%"></div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005z" clip-rule="evenodd"/></svg>
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
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">Status dos Treinamentos</h3>
                        <p class="text-xs text-gray-400">Distribuição entre concluídos e em andamento</p>
                    </div>
                </div>
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
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">Top Treinamentos</h3>
                        <p class="text-xs text-gray-400">Mais concluídos pela equipe</p>
                    </div>
                </div>
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
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">Últimos Colaboradores</h3>
                        <p class="text-xs text-gray-400">Novos cadastros na empresa</p>
                    </div>
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
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">Conclusões Recentes</h3>
                        <p class="text-xs text-gray-400">Treinamentos finalizados recentemente</p>
                    </div>
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

</x-layout.app>

@push('scripts')
<script>
    @if($metrics['trainings_completed'] + $metrics['trainings_pending'] > 0)
    (function () {
        const ctx = document.getElementById('trainingStatusChart');
        if (!ctx) return;
        const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--primary').trim() || '#4f46e5';
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
