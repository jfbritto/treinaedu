<x-layout.app title="Trilha">

    <div class="max-w-4xl">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('paths.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-xl font-semibold text-gray-800">{{ $path->title }}</h2>
                        <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full {{ $path->active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $path->active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                            {{ $path->active ? 'Ativa' : 'Inativa' }}
                        </span>
                    </div>
                    @if($path->description)
                        <p class="text-sm text-gray-500 mt-1">{{ $path->description }}</p>
                    @endif
                </div>
            </div>
            <a href="{{ route('paths.edit', $path) }}"
               class="inline-flex items-center gap-2 bg-primary hover:bg-secondary text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar
            </a>
        </div>

        {{-- Resumo --}}
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background-color: {{ $path->color }}15">
                    <svg class="w-5 h-5" style="color: {{ $path->color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $path->trainings->count() }}</p>
                    <p class="text-xs text-gray-400">Treinamento{{ $path->trainings->count() !== 1 ? 's' : '' }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    @php $totalMin = $path->trainings->sum('duration_minutes'); @endphp
                    <p class="text-2xl font-bold text-gray-800">{{ $totalMin >= 60 ? floor($totalMin/60).'h' : $totalMin.'min' }}</p>
                    <p class="text-xs text-gray-400">Duração total</p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3">
                <div class="w-3 h-10 rounded-full" style="background-color: {{ $path->color }}"></div>
                <div>
                    <p class="text-sm font-bold text-gray-800">{{ $path->color }}</p>
                    <p class="text-xs text-gray-400">Cor da trilha</p>
                </div>
            </div>
        </div>

        {{-- Treinamentos --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-700">Treinamentos na Trilha</h3>
            </div>

            @if($path->trainings->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-sm text-gray-400">Nenhum treinamento vinculado. <a href="{{ route('paths.edit', $path) }}" class="text-primary hover:underline">Adicionar treinamentos</a></p>
                </div>
            @else
                <div class="divide-y divide-gray-50">
                    @foreach($path->trainings as $index => $training)
                        <div class="flex items-center gap-4 px-5 py-4">
                            <span class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold text-white flex-shrink-0" style="background-color: {{ $path->color }}">
                                {{ $index + 1 }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800">{{ $training->title }}</p>
                                <div class="flex items-center gap-3 mt-1 text-xs text-gray-400">
                                    <span>{{ $training->duration_minutes }} min</span>
                                    @if($training->has_quiz)
                                        <span class="text-primary">Com quiz</span>
                                    @endif
                                    <span>{{ $training->active ? 'Ativo' : 'Inativo' }}</span>
                                </div>
                            </div>
                            <a href="{{ route('trainings.show', $training) }}" class="text-xs text-primary hover:underline flex-shrink-0">Ver</a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</x-layout.app>
