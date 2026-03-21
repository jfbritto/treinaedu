<x-layout.app title="Trilhas">

    @if($paths->isEmpty())
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
            <p class="text-gray-400 text-sm font-medium">Nenhuma trilha disponível.</p>
            <p class="text-gray-300 text-xs mt-1">As trilhas serão exibidas quando forem criadas pelo administrador.</p>
        </div>
    @else
        {{-- Resumo --}}
        <div class="bg-white rounded-xl shadow-sm p-5 mb-6 flex items-center gap-5">
            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $paths->count() }}</p>
                <p class="text-sm text-gray-400">Trilha{{ $paths->count() !== 1 ? 's' : '' }} de aprendizagem</p>
            </div>
            <p class="ml-auto text-xs text-gray-400 hidden sm:block">
                Acompanhe sua evolução em cada trilha.
            </p>
        </div>

        {{-- Grid de trilhas --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($paths as $path)
                <a href="{{ route('employee.paths.show', $path) }}"
                   class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow flex flex-col group">
                    <div class="h-2" style="background: {{ $path->color }}"></div>
                    <div class="p-5 flex-1 flex flex-col gap-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background-color: {{ $path->color }}15">
                                <svg class="w-5 h-5" style="color: {{ $path->color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-800 leading-snug group-hover:text-primary transition">{{ $path->title }}</h3>
                                @if($path->description)
                                    <p class="text-xs text-gray-400 mt-0.5 line-clamp-2">{{ $path->description }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="text-xs text-gray-500">
                            {{ $path->completed_trainings }}/{{ $path->trainings_count }} treinamento{{ $path->trainings_count !== 1 ? 's' : '' }} concluído{{ $path->completed_trainings !== 1 ? 's' : '' }}
                        </div>

                        <div class="mt-auto">
                            <div class="flex justify-between text-xs text-gray-400 mb-1">
                                <span>Progresso</span>
                                <span class="font-medium" style="color: {{ $path->color }}">{{ $path->progress_percent }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="h-2 rounded-full transition-all" style="width: {{ $path->progress_percent }}%; background-color: {{ $path->color }}"></div>
                            </div>
                        </div>

                        @if($path->progress_percent === 100)
                            <span class="inline-flex items-center justify-center gap-1 text-xs font-medium text-green-700 bg-green-100 rounded-full px-3 py-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                Trilha concluída
                            </span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @endif

</x-layout.app>
