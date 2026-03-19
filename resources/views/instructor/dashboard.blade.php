<x-layout.app title="Dashboard">

    @php
        $totalViews    = $trainings->getCollection()->sum('views_count');
        $totalCompleted = $trainings->getCollection()->sum('completed_count');
        $totalTrainings = $trainings->total();
        $avgRate = $totalViews > 0 ? round(($totalCompleted / $totalViews) * 100) : 0;
        $user = auth()->user();
    @endphp

    {{-- Banner --}}
    <div class="rounded-xl p-6 mb-6 text-white" style="background: linear-gradient(to right, var(--secondary), var(--primary))">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold mb-0.5">Olá, {{ explode(' ', $user->name)[0] }}! 👋</h2>
                <p class="text-white/70 text-sm">
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
               class="inline-flex items-center gap-2 bg-white hover:bg-white/90 transition text-sm font-semibold px-4 py-2 rounded-lg flex-shrink-0"
               style="color: var(--primary)">
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
            <div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M4.5 4.5a3 3 0 00-3 3v9a3 3 0 003 3h8.25a3 3 0 003-3v-9a3 3 0 00-3-3H4.5zM19.94 18.75l-2.69-2.69V7.94l2.69-2.69c.944-.945 2.56-.276 2.56 1.06v11.38c0 1.336-1.616 2.005-2.56 1.06z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $totalTrainings }}</p>
                <p class="text-xs text-gray-400">Treinamento{{ $totalTrainings !== 1 ? 's' : '' }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 15a3 3 0 100-6 3 3 0 000 6z"/>
                    <path fill-rule="evenodd" d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 010-1.113zM17.25 12a5.25 5.25 0 11-10.5 0 5.25 5.25 0 0110.5 0z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $totalViews }}</p>
                <p class="text-xs text-gray-400">Visualizaç{{ $totalViews !== 1 ? 'ões' : 'ão' }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $totalCompleted }}</p>
                <p class="text-xs text-gray-400">Concluído{{ $totalCompleted !== 1 ? 's' : '' }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 flex flex-col gap-2">
            <div class="flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75zM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 01-1.875-1.875V8.625zM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 013 19.875v-6.75z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $avgRate }}%</p>
                    <p class="text-xs text-gray-400">Taxa de conclusão</p>
                </div>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-1.5">
                <div class="h-1.5 rounded-full {{ $avgRate >= 70 ? 'bg-teal-500' : ($avgRate >= 40 ? 'bg-yellow-400' : 'bg-primary/60') }}"
                     style="width: {{ $avgRate }}%"></div>
            </div>
        </div>
    </div>

    {{-- Tabela de treinamentos --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-primary/60"></div>
                <h3 class="text-sm font-semibold text-gray-700">Meus Treinamentos</h3>
            </div>
            <a href="{{ route('instructor.trainings.index') }}" class="text-xs text-primary hover:underline">Ver todos →</a>
        </div>

        @if($trainings->isEmpty())
            <div class="p-12 text-center">
                <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <p class="text-gray-400 text-sm font-medium">Nenhum treinamento criado ainda.</p>
                <a href="{{ route('instructor.trainings.create') }}" class="inline-block mt-3 text-sm text-primary hover:underline">Criar primeiro treinamento →</a>
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
                                    <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm14.024-.983a1.125 1.125 0 010 1.966l-5.603 3.113A1.125 1.125 0 019 15.113V8.887c0-.857.921-1.4 1.671-.983l5.603 3.113z" clip-rule="evenodd"/>
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
                                            <div class="h-1.5 rounded-full {{ $rate >= 70 ? 'bg-green-500' : ($rate >= 30 ? 'bg-yellow-400' : 'bg-primary/60') }}"
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
                                       class="text-xs font-medium text-primary hover:text-secondary transition">Editar</a>
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
