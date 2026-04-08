<x-layout.app title="{{ $path->title }}">

    <div class="mb-6">
        <a href="{{ route('paths.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar para trilhas
        </a>
    </div>

    {{-- Hero --}}
    <div class="rounded-xl p-6 mb-6 text-white relative overflow-hidden"
         style="background: linear-gradient(135deg, {{ $path->color }}, {{ $path->color }}cc)">
        {{-- Decorative circles --}}
        <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-white/5"></div>
        <div class="absolute -bottom-16 -left-16 w-56 h-56 rounded-full bg-white/5"></div>

        <div class="relative flex items-start justify-between gap-4">
            <div class="flex items-start gap-4 flex-1 min-w-0">
                <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center flex-shrink-0">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <p class="text-xs text-white/70 uppercase tracking-wider">Trilha de Aprendizagem</p>
                        <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full bg-white/20 backdrop-blur">
                            <span class="w-1.5 h-1.5 rounded-full {{ $path->active ? 'bg-green-300' : 'bg-gray-300' }}"></span>
                            {{ $path->active ? 'Ativa' : 'Inativa' }}
                        </span>
                    </div>
                    <h1 class="text-2xl font-bold mb-1 break-words">{{ $path->title }}</h1>
                    @if($path->description)
                        <p class="text-sm text-white/80 max-w-2xl">{{ $path->description }}</p>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <a href="{{ route('paths.edit', $path) }}"
                   class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 backdrop-blur text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar
                </a>
            </div>
        </div>
    </div>

    @php
        $totalMin = $path->trainings->sum('duration_minutes');
        $durLabel = $totalMin >= 60
            ? floor($totalMin/60).'h'.($totalMin%60 > 0 ? ' '.($totalMin%60).'min' : '')
            : $totalMin.'min';
        $withQuiz = $path->trainings->where('has_quiz', true)->count();
        $activeTrainings = $path->trainings->where('active', true)->count();
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-6xl">

        {{-- Coluna principal: Treinamentos --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
                             style="background-color: {{ $path->color }}15">
                            <svg class="w-5 h-5" style="color: {{ $path->color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-gray-800">Conteúdo da Trilha</h3>
                            <p class="text-xs text-gray-400">Treinamentos que compõem esta jornada em ordem</p>
                        </div>
                    </div>
                </div>

                @if($path->trainings->isEmpty())
                    <div class="px-6 py-12 text-center">
                        <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500 font-medium mb-1">Nenhum treinamento vinculado</p>
                        <p class="text-xs text-gray-400 mb-4">Adicione treinamentos para montar a jornada dos colaboradores.</p>
                        <a href="{{ route('paths.edit', $path) }}"
                            class="inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:text-secondary transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Adicionar treinamentos
                        </a>
                    </div>
                @else
                    <div class="divide-y divide-gray-50">
                        @foreach($path->trainings as $index => $training)
                            <div class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50 transition group">
                                <span class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-white flex-shrink-0 shadow-sm"
                                      style="background: linear-gradient(135deg, {{ $path->color }}, {{ $path->color }}cc)">
                                    {{ $index + 1 }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 group-hover:text-primary transition truncate">{{ $training->title }}</p>
                                    <div class="flex items-center gap-3 mt-1 text-xs text-gray-500">
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $training->duration_minutes }} min
                                        </span>
                                        @if($training->has_quiz)
                                            <span class="inline-flex items-center gap-1 text-primary">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                </svg>
                                                Com quiz
                                            </span>
                                        @endif
                                        @if(!$training->active)
                                            <span class="inline-flex items-center gap-1 text-red-500">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                                Inativo
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <a href="{{ route('trainings.show', $training) }}"
                                   class="flex-shrink-0 inline-flex items-center gap-1 text-xs font-medium text-gray-400 hover:text-primary transition opacity-0 group-hover:opacity-100">
                                    Ver treinamento
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Coluna lateral: Informações --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- Métricas --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Resumo</p>

                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                             style="background-color: {{ $path->color }}15">
                            <svg class="w-5 h-5" style="color: {{ $path->color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-2xl font-bold text-gray-800 leading-none">{{ $path->trainings->count() }}</p>
                            <p class="text-xs text-gray-400 mt-1">Treinamento{{ $path->trainings->count() !== 1 ? 's' : '' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-2xl font-bold text-gray-800 leading-none">{{ $durLabel }}</p>
                            <p class="text-xs text-gray-400 mt-1">Duração total</p>
                        </div>
                    </div>

                    @if($withQuiz > 0)
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-2xl font-bold text-gray-800 leading-none">{{ $withQuiz }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $withQuiz === 1 ? 'Com avaliação' : 'Com avaliações' }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Detalhes --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Detalhes</p>

                <div class="space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Status</span>
                        <span class="inline-flex items-center gap-1 font-medium {{ $path->active ? 'text-green-600' : 'text-gray-400' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $path->active ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                            {{ $path->active ? 'Ativa' : 'Inativa' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Cor</span>
                        <div class="flex items-center gap-1.5">
                            <span class="w-4 h-4 rounded border border-gray-200" style="background-color: {{ $path->color }}"></span>
                            <span class="font-mono text-xs text-gray-600">{{ $path->color }}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Criada em</span>
                        <span class="font-medium text-gray-800">{{ $path->created_at->format('d/m/Y') }}</span>
                    </div>
                    @if($path->updated_at->gt($path->created_at))
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Atualizada</span>
                            <span class="font-medium text-gray-800">{{ $path->updated_at->diffForHumans() }}</span>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

</x-layout.app>
