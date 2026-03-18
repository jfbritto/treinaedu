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
            @if(empty($metrics['top_trainings']))
                <p class="text-sm text-gray-400 mt-8 text-center">Nenhum treinamento criado ainda</p>
            @else
                <div class="space-y-4">
                    @foreach($metrics['top_trainings'] as $training)
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <p class="text-sm text-gray-700 truncate max-w-[160px]" title="{{ $training['title'] }}">{{ $training['title'] }}</p>
                                <span class="text-xs font-semibold text-gray-500 ml-2 flex-shrink-0">{{ $training['completed_count'] }} concl.</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1.5">
                                <div class="bg-blue-500 h-1.5 rounded-full transition-all" style="width: {{ $training['completion_rate'] }}%"></div>
                            </div>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $training['completion_rate'] }}% de conclusão</p>
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
            @if(empty($metrics['recent_employees']))
                <p class="text-sm text-gray-400 text-center py-6">Nenhum colaborador cadastrado</p>
            @else
                <div class="space-y-3">
                    @foreach($metrics['recent_employees'] as $employee)
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-xs font-semibold text-blue-700">{{ strtoupper(substr($employee['name'], 0, 2)) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $employee['name'] }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ $employee['email'] }}</p>
                            </div>
                            <span class="text-xs text-gray-400 flex-shrink-0">{{ $employee['created_at']->diffForHumans() }}</span>
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
            @if(empty($metrics['recent_completions']))
                <p class="text-sm text-gray-400 text-center py-6">Nenhuma conclusão registrada</p>
            @else
                <div class="space-y-3">
                    @foreach($metrics['recent_completions'] as $completion)
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $completion['user_name'] ?? '—' }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ $completion['training_title'] ?? '—' }}</p>
                            </div>
                            <span class="text-xs text-gray-400 flex-shrink-0">{{ $completion['completed_at']->diffForHumans() }}</span>
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
