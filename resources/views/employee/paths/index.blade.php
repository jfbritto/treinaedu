<x-layout.app title="Trilhas">

    @php
        $totalPaths = $paths->count();
        $completedPaths = $paths->where('progress_percent', 100)->count();
        $inProgressPaths = $paths->filter(fn ($p) => $p->progress_percent > 0 && $p->progress_percent < 100)->count();
    @endphp

    <p class="text-sm text-gray-500 mb-6">Jornadas de aprendizagem atribuídas a você</p>

    @if($paths->isEmpty())
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
            </div>
            <p class="text-gray-500 text-sm font-medium mb-1">Nenhuma trilha disponível</p>
            <p class="text-xs text-gray-400">As trilhas serão exibidas quando forem criadas pelo administrador.</p>
        </div>
    @else
        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                    </div>
                </div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Total</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalPaths }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $totalPaths === 1 ? 'trilha disponível' : 'trilhas disponíveis' }}</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Em Andamento</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $inProgressPaths }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $inProgressPaths === 1 ? 'trilha iniciada' : 'trilhas iniciadas' }}</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Concluídas</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $completedPaths }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $completedPaths === 1 ? 'trilha finalizada' : 'trilhas finalizadas' }}</p>
            </div>
        </div>

        {{-- Card container com header + grid --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-gray-800">Trilhas de Aprendizagem</h3>
                        <p class="text-xs text-gray-400">Acompanhe sua evolução em cada trilha</p>
                    </div>
                </div>
            </div>

            <div class="p-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($paths as $path)
                    @php
                        $isComplete = $path->progress_percent === 100;
                        $isStarted = $path->progress_percent > 0;
                    @endphp
                    <a href="{{ route('employee.paths.show', $path) }}"
                       class="border border-gray-100 rounded-xl overflow-hidden hover:shadow-md hover:border-primary/20 transition flex flex-col group">
                        <div class="h-1.5" style="background: linear-gradient(to right, var(--secondary), var(--primary))"></div>
                        <div class="p-5 flex-1 flex flex-col gap-4">
                            <div class="flex items-start gap-3">
                                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                                     style="background: linear-gradient(135deg, var(--primary), var(--secondary))">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2">
                                        <h3 class="font-semibold text-gray-800 leading-snug group-hover:text-primary transition truncate">{{ $path->title }}</h3>
                                        @if($isComplete)
                                            <span class="flex-shrink-0 inline-flex items-center gap-1 text-xs font-medium text-green-700 bg-green-50 border border-green-200 rounded-full px-2 py-0.5">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Concluída
                                            </span>
                                        @elseif($isStarted)
                                            <span class="flex-shrink-0 text-xs rounded-full px-2 py-0.5" style="background-color: color-mix(in srgb, var(--secondary) 15%, transparent); color: var(--secondary); border: 1px solid color-mix(in srgb, var(--secondary) 30%, transparent)">Em andamento</span>
                                        @else
                                            <span class="flex-shrink-0 text-xs bg-gray-50 text-gray-400 border border-gray-200 rounded-full px-2 py-0.5">Não iniciada</span>
                                        @endif
                                    </div>
                                    @if($path->description)
                                        <p class="text-xs text-gray-400 mt-1 line-clamp-2">{{ $path->description }}</p>
                                    @else
                                        <p class="text-xs text-gray-300 mt-1 italic">Sem descrição</p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5 text-xs text-gray-500">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                {{ $path->completed_trainings }}/{{ $path->trainings_count }} treinamento{{ $path->trainings_count !== 1 ? 's' : '' }} concluído{{ $path->completed_trainings !== 1 ? 's' : '' }}
                            </div>

                            <div class="mt-auto">
                                <div class="flex justify-between text-xs text-gray-400 mb-1">
                                    <span>Progresso</span>
                                    <span class="font-semibold text-gray-600">{{ $path->progress_percent }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full bg-primary transition-all" style="width: {{ $path->progress_percent }}%"></div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

</x-layout.app>
