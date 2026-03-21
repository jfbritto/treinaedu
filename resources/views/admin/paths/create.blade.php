<x-layout.app title="Nova Trilha">

    <div class="mb-6">
        <a href="{{ route('paths.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-600 transition mb-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar para trilhas
        </a>
    </div>

    <div class="max-w-2xl space-y-6">
        <form method="POST" action="{{ route('paths.store') }}">
            @csrf

            {{-- Dados da trilha --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">Dados da Trilha</h3>
                        <p class="text-xs text-gray-400">Informações da trilha de aprendizagem</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="space-y-1.5">
                        <label for="title" class="block text-sm font-medium text-gray-700">Nome da trilha <span class="text-red-500">*</span></label>
                        <input type="text" id="title" name="title"
                            value="{{ old('title') }}" required
                            placeholder="Ex: Onboarding 2026"
                            class="w-full rounded-lg border @error('title') border-red-400 @else border-gray-300 @enderror px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        @error('title') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="description" class="block text-sm font-medium text-gray-700">Descrição</label>
                        <textarea id="description" name="description" rows="3"
                            placeholder="Descreva o objetivo desta trilha..."
                            class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">{{ old('description') }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label for="color" class="block text-sm font-medium text-gray-700">Cor</label>
                            <div class="flex items-center gap-3">
                                <input type="color" id="color" name="color"
                                    value="{{ old('color', '#3B82F6') }}"
                                    class="w-10 h-10 rounded-lg border border-gray-300 cursor-pointer p-0.5">
                                <span class="text-xs text-gray-400">Cor de destaque da trilha</span>
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label for="sort_order" class="block text-sm font-medium text-gray-700">Ordem</label>
                            <input type="number" id="sort_order" name="sort_order"
                                value="{{ old('sort_order', 0) }}" min="0"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                    </div>

                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="active" value="0">
                        <input type="checkbox" name="active" value="1"
                            {{ old('active', true) ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="text-sm text-gray-700">Trilha ativa</span>
                    </label>
                </div>
            </div>

            {{-- Treinamentos --}}
            @if($trainings->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm p-6 mt-6" x-data="{ search: '' }">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">Treinamentos da Trilha</h3>
                        <p class="text-xs text-gray-400">Selecione os treinamentos que fazem parte desta trilha</p>
                    </div>
                </div>

                <div class="relative mb-4">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" x-model="search" placeholder="Buscar treinamento..."
                        class="w-full pl-10 pr-4 py-2 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>

                <div class="space-y-1 max-h-64 overflow-y-auto pr-1">
                    @foreach($trainings as $training)
                        <label class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-gray-50 cursor-pointer transition"
                               x-show="!search || '{{ strtolower($training->title) }}'.includes(search.toLowerCase())"
                               x-cloak>
                            <input type="checkbox" name="trainings[]" value="{{ $training->id }}"
                                {{ in_array($training->id, old('trainings', [])) ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer">
                            <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-primary" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M4.5 5.653c0-1.426 1.529-2.33 2.779-1.643l11.54 6.348c1.295.712 1.295 2.573 0 3.285L7.28 19.991c-1.25.687-2.779-.217-2.779-1.643V5.653z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-700">{{ $training->title }}</p>
                                <p class="text-xs text-gray-400">{{ $training->duration_minutes }} min{{ $training->has_quiz ? ' · Com quiz' : '' }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="flex justify-end mt-6">
                <div class="flex items-center gap-3">
                    <a href="{{ route('paths.index') }}" class="px-4 py-2.5 text-sm text-gray-600 hover:text-gray-800 transition">Cancelar</a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 bg-primary hover:bg-secondary text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Criar Trilha
                    </button>
                </div>
            </div>
        </form>
    </div>

</x-layout.app>
