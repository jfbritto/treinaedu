<x-layout.app title="Dashboard">

    @php
        $totalViews    = $trainings->getCollection()->sum('views_count');
        $totalCompleted = $trainings->getCollection()->sum('completed_count');
        $totalTrainings = $trainings->total();
        $avgRate = $totalViews > 0 ? round(($totalCompleted / $totalViews) * 100) : 0;
        $user = auth()->user();
    @endphp

    {{-- Banner --}}
    <div class="bg-gradient-to-r from-blue-700 to-blue-500 rounded-xl p-6 mb-6 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold mb-0.5">Olá, {{ explode(' ', $user->name)[0] }}! 👋</h2>
                <p class="text-blue-100 text-sm">
                    @if($totalTrainings === 0)
                        Comece criando seu primeiro treinamento.
                    @elseif($avgRate >= 80)
                        Excelente! Seus treinamentos têm ótima taxa de conclusão.
                    @elseif($avgRate >= 50)
                        Bom desempenho! Continue engajando os colaboradores.
                    @else
                        Acompanhe o progresso dos seus treinamentos abaixo.
                    @endif
                </p>
            </div>
            <a href="{{ route('instructor.trainings.create') }}"
               class="inline-flex items-center gap-2 bg-white text-blue-700 hover:bg-blue-50 transition text-sm font-semibold px-4 py-2 rounded-lg flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Novo Treinamento
            </a>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $totalTrainings }}</p>
                <p class="text-xs text-gray-400">Treinamento{{ $totalTrainings !== 1 ? 's' : '' }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-yellow-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $totalViews }}</p>
                <p class="text-xs text-gray-400">Visualizaç{{ $totalViews !== 1 ? 'ões' : 'ão' }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $totalCompleted }}</p>
                <p class="text-xs text-gray-400">Concluído{{ $totalCompleted !== 1 ? 's' : '' }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 flex flex-col gap-2">
            <div class="flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-teal-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $avgRate }}%</p>
                    <p class="text-xs text-gray-400">Taxa de conclusão</p>
                </div>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-1.5">
                <div class="h-1.5 rounded-full {{ $avgRate >= 70 ? 'bg-teal-500' : ($avgRate >= 40 ? 'bg-yellow-400' : 'bg-blue-400') }}"
                     style="width: {{ $avgRate }}%"></div>
            </div>
        </div>
    </div>

    {{-- Tabela de treinamentos --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-blue-400"></div>
                <h3 class="text-sm font-semibold text-gray-700">Meus Treinamentos</h3>
            </div>
            <a href="{{ route('instructor.trainings.index') }}" class="text-xs text-blue-600 hover:underline">Ver todos →</a>
        </div>

        @if($trainings->isEmpty())
            <div class="p-12 text-center">
                <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <p class="text-gray-400 text-sm font-medium">Nenhum treinamento criado ainda.</p>
                <a href="{{ route('instructor.trainings.create') }}" class="inline-block mt-3 text-sm text-blue-600 hover:underline">Criar primeiro treinamento →</a>
            </div>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Treinamento</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider hidden sm:table-cell">Visualizações</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider hidden md:table-cell">Concluídos</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider hidden lg:table-cell">Taxa</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($trainings as $training)
                        @php
                            $rate = $training->views_count > 0
                                ? round(($training->completed_count / $training->views_count) * 100)
                                : null;
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-800">{{ $training->title }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 hidden sm:table-cell">
                                <span class="text-sm text-gray-600">{{ $training->views_count }}</span>
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell">
                                <span class="inline-flex items-center gap-1.5 text-sm font-medium {{ $training->completed_count > 0 ? 'text-green-700' : 'text-gray-400' }}">
                                    @if($training->completed_count > 0)
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    @endif
                                    {{ $training->completed_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 hidden lg:table-cell">
                                @if($rate !== null)
                                    <div class="flex items-center gap-2 w-28">
                                        <div class="flex-1 bg-gray-100 rounded-full h-1.5">
                                            <div class="h-1.5 rounded-full {{ $rate >= 70 ? 'bg-green-500' : ($rate >= 30 ? 'bg-yellow-400' : 'bg-blue-400') }}"
                                                 style="width: {{ $rate }}%"></div>
                                        </div>
                                        <span class="text-xs font-medium text-gray-600 w-8 text-right">{{ $rate }}%</span>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end">
                                    <a href="{{ route('instructor.trainings.edit', $training) }}"
                                       class="text-xs font-medium text-blue-600 hover:text-blue-800 transition">Editar</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($trainings->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $trainings->links() }}
                </div>
            @endif
        @endif
    </div>

</x-layout.app>
