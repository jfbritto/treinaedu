<x-layout.app title="Trilhas">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <p class="text-sm text-gray-500">Organize treinamentos em trilhas de aprendizagem</p>
        <a href="{{ route('paths.create') }}"
           class="inline-flex items-center gap-2 bg-primary hover:bg-secondary text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nova Trilha
        </a>
    </div>

    @if($paths->isEmpty())
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
            <p class="text-gray-400 text-sm font-medium">Nenhuma trilha cadastrada.</p>
            <a href="{{ route('paths.create') }}" class="inline-block mt-3 text-sm text-primary hover:underline">Criar primeira trilha</a>
        </div>
    @else
        {{-- Summary bar --}}
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6 flex items-center gap-5">
            <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $paths->total() }}</p>
                <p class="text-sm text-gray-400">Trilha{{ $paths->total() !== 1 ? 's' : '' }} cadastrada{{ $paths->total() !== 1 ? 's' : '' }}</p>
            </div>
            <p class="ml-auto text-xs text-gray-400 hidden sm:block">Trilhas agrupam treinamentos em jornadas de aprendizagem.</p>
        </div>

        {{-- Grid de cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 mb-6">
            @foreach($paths as $path)
                <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow flex flex-col">
                    <div class="h-1.5" style="background: {{ $path->color }}"></div>
                    <div class="p-5 flex-1 flex flex-col gap-4">
                        <div class="flex items-start justify-between gap-2">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background-color: {{ $path->color }}15">
                                <svg class="w-5 h-5" style="color: {{ $path->color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-800 leading-snug">{{ $path->title }}</h3>
                                @if($path->description)
                                    <p class="text-xs text-gray-400 mt-0.5 line-clamp-2">{{ $path->description }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center gap-1 text-xs text-gray-500">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                {{ $path->trainings_count }} treinamento{{ $path->trainings_count !== 1 ? 's' : '' }}
                            </span>
                            <span class="inline-flex items-center gap-1 text-xs {{ $path->active ? 'text-green-600' : 'text-gray-400' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $path->active ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                                {{ $path->active ? 'Ativa' : 'Inativa' }}
                            </span>
                        </div>

                        <div class="flex items-center gap-2 mt-auto pt-3 border-t border-gray-100">
                            <a href="{{ route('paths.show', $path) }}"
                               class="flex-1 flex items-center justify-center gap-1.5 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 transition rounded-lg px-3 py-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Visualizar
                            </a>
                            <a href="{{ route('paths.edit', $path) }}"
                               class="flex-1 flex items-center justify-center gap-1.5 text-xs font-medium text-primary bg-primary/10 hover:bg-primary/20 transition rounded-lg px-3 py-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Editar
                            </a>
                            <form method="POST" action="{{ route('paths.destroy', $path) }}" data-confirm="Remover esta trilha?">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="flex items-center justify-center gap-1.5 text-xs font-medium text-red-500 bg-red-50 hover:bg-red-100 transition rounded-lg px-3 py-2">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($paths->hasPages())
            {{ $paths->links() }}
        @endif
    @endif

</x-layout.app>
