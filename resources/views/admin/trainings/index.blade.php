<x-layout.app title="Treinamentos">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <p class="text-sm text-gray-500">Gerencie os treinamentos disponíveis para a equipe</p>
        <a href="{{ route('trainings.create') }}"
           class="inline-flex items-center gap-2 bg-primary hover:bg-secondary text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Novo Treinamento
        </a>
    </div>

    @if($totalAll === 0)
        {{-- Nenhum treinamento cadastrado no sistema --}}
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="text-gray-500 text-sm font-medium mb-1">Nenhum treinamento cadastrado</p>
            <p class="text-xs text-gray-400 mb-4">Crie treinamentos em vídeo, texto ou documento para sua equipe.</p>
            <a href="{{ route('trainings.create') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:text-secondary transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Criar primeiro treinamento
            </a>
        </div>
    @else
        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            {{-- Ativos --}}
            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Ativos</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalActive }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $totalActive === 1 ? 'treinamento publicado' : 'treinamentos publicados' }}</p>
            </div>

            {{-- Com quiz --}}
            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Com Avaliação</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalWithQuiz }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $totalWithQuiz === 1 ? 'treinamento com quiz' : 'treinamentos com quiz' }}</p>
            </div>

            {{-- Conclusão média --}}
            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Conclusão Média</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $avgRate }}%</p>
                <div class="mt-2 w-full bg-gray-100 rounded-full h-1.5">
                    <div class="h-1.5 rounded-full bg-primary transition-all" style="width: {{ $avgRate }}%"></div>
                </div>
            </div>
        </div>

        {{-- Table with server-side filter --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            {{-- Header do card --}}
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">Lista de Treinamentos</h3>
                        <p class="text-xs text-gray-400">Busque, visualize e gerencie os treinamentos da empresa</p>
                    </div>
                </div>
            </div>

            {{-- Search --}}
            <form method="GET" action="{{ route('trainings.index') }}" x-data="{ timer: null }"
                  class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="relative flex-1 max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar treinamento..."
                           @input="clearTimeout(timer); timer = setTimeout(() => $el.closest('form').submit(), 400)"
                           class="w-full pl-10 pr-4 py-2 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                @if(request('search'))
                    <a href="{{ route('trainings.index') }}" class="text-xs text-gray-500 hover:text-gray-700 transition">Limpar</a>
                @endif
            </form>

            @if($trainings->isEmpty())
                {{-- Busca sem resultados --}}
                <div class="px-6 py-12 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500">Nenhum treinamento encontrado</p>
                    <p class="text-xs text-gray-400 mt-1">Nenhum resultado para "<strong>{{ request('search') }}</strong>". Tente outro termo.</p>
                    <a href="{{ route('trainings.index') }}" class="inline-block mt-3 text-xs font-medium text-primary hover:underline">Limpar busca</a>
                </div>
            @else
            <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Treinamento</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider hidden sm:table-cell">Duração</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider hidden md:table-cell">Quiz</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider hidden md:table-cell">Grupos</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider hidden lg:table-cell">Conclusão</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($trainings as $training)
                        @php $rate = $training->completionRate(); @endphp
                        <tr class="hover:bg-gray-50 transition cursor-pointer" onclick="window.location.href='{{ route('trainings.show', $training) }}'">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                                         style="background: linear-gradient(135deg, var(--primary), var(--secondary))">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd" d="M4.5 5.653c0-1.426 1.529-2.33 2.779-1.643l11.54 6.348c1.295.712 1.295 2.573 0 3.285L7.28 19.991c-1.25.687-2.779-.217-2.779-1.643V5.653z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $training->title }}</p>
                                        @if($training->description)
                                            <p class="text-xs text-gray-400 truncate">{{ $training->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 hidden sm:table-cell">
                                <span class="inline-flex items-center gap-1 text-xs text-gray-500">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $training->calculatedDuration() }} min
                                </span>
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell">
                                @if($training->has_quiz)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-100">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        Com Quiz
                                    </span>
                                @else
                                    <span class="text-xs text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell">
                                @if($training->assignments_count > 0)
                                    <span class="inline-flex items-center gap-1 text-xs text-gray-600">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        {{ $training->assignments_count }} {{ $training->assignments_count !== 1 ? 'grupos' : 'grupo' }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium {{ $training->active ? 'text-green-700' : 'text-red-500' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $training->active ? 'bg-green-500' : 'bg-red-400' }}"></span>
                                    {{ $training->active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 hidden lg:table-cell">
                                <div class="flex items-center gap-2 w-32">
                                    <div class="flex-1 bg-gray-100 rounded-full h-1.5">
                                        <div class="h-1.5 rounded-full {{ $rate >= 70 ? 'bg-green-500' : ($rate >= 30 ? 'bg-yellow-400' : 'bg-primary') }}"
                                             style="width: {{ $rate }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium text-gray-600 flex-shrink-0 w-8 text-right">{{ $rate }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
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
            </div>
            @endif
        </div>
    @endif

</x-layout.app>
