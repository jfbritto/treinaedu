<x-layout.app title="Desafios e Engajamento">

    {{-- Header --}}
    <div class="mb-6">
        <p class="text-sm text-gray-500">Incentive o aprendizado com dados de engajamento e ranking</p>
    </div>

    {{-- Period Filter --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 px-6 py-4 mb-8">
        <form method="GET" action="{{ route('engagement.index') }}" class="flex flex-col sm:flex-row gap-4 items-end">
            <div class="flex-1 min-w-[150px]">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">
                    Data Início
                </label>
                <input type="date" name="date_from" value="{{ $dateFrom->format('Y-m-d') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            <div class="flex-1 min-w-[150px]">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">
                    Data Fim
                </label>
                <input type="date" name="date_to" value="{{ $dateTo->format('Y-m-d') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="inline-flex items-center gap-2 bg-primary hover:bg-secondary text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filtrar
                </button>
                @if(request('date_from') || request('date_to'))
                    <a href="{{ route('engagement.index') }}" class="inline-flex items-center gap-2 border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-semibold transition">
                        Limpar
                    </a>
                @endif
            </div>
        </form>

        <div class="mt-4 inline-block bg-primary/10 text-primary px-3 py-1.5 rounded-full text-xs font-semibold">
            Período: {{ $dateFrom->format('d/m/Y') }} até {{ $dateTo->format('d/m/Y') }}
        </div>
    </div>

    {{-- Overall Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
            <p class="text-xs font-semibold text-primary uppercase tracking-wider mb-2">Usuários Ativos</p>
            <p class="text-3xl font-bold text-primary">{{ $stats['users_engaged'] }}</p>
            <p class="text-xs text-gray-500 mt-1">de {{ $stats['total_users'] }}</p>
        </div>

        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
            <p class="text-xs font-semibold text-primary uppercase tracking-wider mb-2">Taxa de Conclusão</p>
            <p class="text-3xl font-bold text-primary">{{ $stats['overall_completion_rate'] }}%</p>
            <p class="text-xs text-gray-500 mt-1">{{ $stats['total_completed'] }} completos</p>
        </div>

        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
            <p class="text-xs font-semibold text-primary uppercase tracking-wider mb-2">Progresso Médio</p>
            <p class="text-3xl font-bold text-primary">{{ $stats['avg_progress'] }}%</p>
            <p class="text-xs text-gray-500 mt-1">em andamento</p>
        </div>

        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
            <p class="text-xs font-semibold text-primary uppercase tracking-wider mb-2">Treinamentos</p>
            <p class="text-3xl font-bold text-primary">{{ $stats['total_trainings_assigned'] }}</p>
            <p class="text-xs text-gray-500 mt-1">atribuídos</p>
        </div>

        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
            <p class="text-xs font-semibold text-primary uppercase tracking-wider mb-2">Em Risco</p>
            <p class="text-3xl font-bold text-primary">{{ count($atRiskUsers) }}</p>
            <p class="text-xs text-gray-500 mt-1">período analisado</p>
        </div>
    </div>

    {{-- Top Users and Groups Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        {{-- Top Users --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 bg-primary/5 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2zm3.6 8.04a1.5 1.5 0 11-2.12 2.12L12 11.66l-1.48 1.5a1.5 1.5 0 11-2.12-2.12L9.88 9.5 8.4 8.04a1.5 1.5 0 112.12-2.12L12 7.34l1.48-1.5a1.5 1.5 0 112.12 2.12L14.12 9.5z"/>
                    </svg>
                    <h3 class="text-sm font-semibold text-gray-800">Top Funcionários 🏆</h3>
                </div>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($topUsers as $index => $user)
                    <div class="px-6 py-4 hover:bg-gray-50 transition">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3 flex-1">
                                <div class="flex-shrink-0">
                                    @if($index < 3)
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full font-bold text-white
                                            {{ $index === 0 ? 'bg-yellow-400' : ($index === 1 ? 'bg-gray-300' : 'bg-orange-400') }}">
                                            {{ $index + 1 }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full font-semibold text-gray-600 bg-gray-100">
                                            {{ $index + 1 }}
                                        </span>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-800">{{ $user['name'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $user['completed'] }}/{{ $user['total_trainings'] }} completos</p>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="text-lg font-bold text-primary">{{ $user['completion_rate'] }}%</p>
                                <p class="text-xs text-gray-500">concluído</p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div class="bg-primary h-1.5 rounded-full transition-all" style="width: {{ $user['completion_rate'] }}%"></div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-400">
                        <p class="text-sm">Nenhum funcionário com treinamentos</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Group Rankings --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 bg-primary/5 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <h3 class="text-sm font-semibold text-gray-800">Ranking de Grupos</h3>
                </div>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($groupRankings as $index => $group)
                    <div class="px-6 py-4 hover:bg-gray-50 transition">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full font-bold text-xs text-white bg-primary">
                                        {{ $index + 1 }}
                                    </span>
                                    <p class="text-sm font-semibold text-gray-800">{{ $group['name'] }}</p>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ $group['members'] }} membros • {{ $group['completed'] }}/{{ $group['total_trainings'] }} completos</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-primary">{{ $group['completion_rate'] }}%</p>
                                <p class="text-xs text-gray-500">conclusão</p>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div class="bg-primary h-1.5 rounded-full transition-all" style="width: {{ $group['completion_rate'] }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-400">
                        <p class="text-sm">Nenhum grupo cadastrado</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- At Risk Users --}}
    @if(count($atRiskUsers) > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 bg-primary/5 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zM7 12a.75.75 0 01.75-.75h8.5a.75.75 0 010 1.5h-8.5A.75.75 0 017 12z" clip-rule="evenodd" />
                    </svg>
                    <h3 class="text-sm font-semibold text-gray-800">Funcionários em Risco ⚠️</h3>
                    <span class="ml-auto inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary/10 text-primary">
                        {{ count($atRiskUsers) }}
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left px-6 py-3 font-semibold text-gray-700">Funcionário</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-700">E-mail</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-700">Último Acesso</th>
                            <th class="text-center px-6 py-3 font-semibold text-gray-700">Dias Inativo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($atRiskUsers as $user)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-gray-800">{{ $user['name'] }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-xs text-gray-600">{{ $user['email'] }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    @if($user['last_activity'])
                                        <p class="text-xs text-gray-600">{{ $user['last_activity']->format('d/m/Y H:i') }}</p>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary/10 text-primary">
                                            Nunca iniciou
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full font-bold text-white {{ $user['days_inactive'] >= 60 ? 'bg-primary' : 'bg-primary/70' }}">
                                        {{ min($user['days_inactive'], 99) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</x-layout.app>
