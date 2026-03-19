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
                <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
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
                <div class="w-10 h-10 rounded-xl bg-yellow-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-500" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zM12.75 6a.75.75 0 00-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 000-1.5h-3.75V6z" clip-rule="evenodd"/></svg>
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
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd"/></svg>
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
                    <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M5.166 2.621v.858c-1.035.148-2.059.33-3.071.543a.75.75 0 00-.584.859 6.753 6.753 0 006.138 5.6 6.73 6.73 0 002.743 1.346A6.707 6.707 0 019.279 15H8.54c-1.036 0-1.875.84-1.875 1.875V19.5h-.002A2.627 2.627 0 009.29 22.124c.262.02.526.03.79.037V22.5h3.84v-.339a18.353 18.353 0 00.79-.037 2.627 2.627 0 002.627-2.624h-.002v-2.625c0-1.036-.84-1.875-1.875-1.875h-.74a6.707 6.707 0 00-1.112-3.173 6.73 6.73 0 002.743-1.347 6.753 6.753 0 006.139-5.6.75.75 0 00-.585-.858 47.077 47.077 0 00-3.07-.543V2.62a.75.75 0 00-.658-.744 49.22 49.22 0 00-6.093-.377c-2.063 0-4.096.128-6.093.377a.75.75 0 00-.657.744z" clip-rule="evenodd"/></svg>
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
                <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24"><path d="M8.25 6.75a3.75 3.75 0 117.5 0 3.75 3.75 0 01-7.5 0zM15.75 9.75a3 3 0 116 0 3 3 0 01-6 0zM2.25 9.75a3 3 0 116 0 3 3 0 01-6 0zM6.31 15.117A6.745 6.745 0 0112 12a6.745 6.745 0 016.709 7.498.75.75 0 01-.372.568A12.696 12.696 0 0112 21.75c-2.305 0-4.47-.612-6.337-1.684a.75.75 0 01-.372-.568 6.787 6.787 0 011.019-4.38z"/><path d="M5.082 14.254a8.287 8.287 0 00-1.308 5.135 9.687 9.687 0 01-1.764-.44l-.115-.04a.563.563 0 01-.373-.487l-.01-.121a3.75 3.75 0 013.57-4.047zM20.226 19.389a8.287 8.287 0 00-1.308-5.135 3.75 3.75 0 013.57 4.047l-.01.121a.563.563 0 01-.373.486l-.115.04c-.567.2-1.156.349-1.764.441z"/></svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-700">Novo Colaborador</p>
                <p class="text-xs text-gray-400">Adicionar usuário</p>
            </div>
        </a>

        <a href="{{ route('trainings.create') }}" class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition group flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-green-50 group-hover:bg-green-100 flex items-center justify-center flex-shrink-0 transition">
                <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm14.024-.983a1.125 1.125 0 010 1.966l-5.603 3.113A1.125 1.125 0 019 15.113V8.887c0-.857.921-1.4 1.671-.983l5.603 3.113z" clip-rule="evenodd"/></svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-700">Novo Treinamento</p>
                <p class="text-xs text-gray-400">Criar conteúdo</p>
            </div>
        </a>

        <a href="{{ route('trainings.index') }}" class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition group flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-yellow-50 group-hover:bg-yellow-100 flex items-center justify-center flex-shrink-0 transition">
                <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V7.875L14.25 1.5H5.625zM7.5 15a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5A.75.75 0 017.5 15zm.75 2.25a.75.75 0 000 1.5H12a.75.75 0 000-1.5H8.25z" clip-rule="evenodd"/><path d="M12.971 1.816A5.23 5.23 0 0114.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 013.434 1.279 9.768 9.768 0 00-6.963-6.963z"/></svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-700">Atribuir Treinamento</p>
                <p class="text-xs text-gray-400">Vincular a grupos</p>
            </div>
        </a>

        <a href="{{ route('reports.index') }}" class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition group flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-purple-50 group-hover:bg-purple-100 flex items-center justify-center flex-shrink-0 transition">
                <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24"><path d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75zM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 01-1.875-1.875V8.625zM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 013 19.875v-6.75z"/></svg>
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
