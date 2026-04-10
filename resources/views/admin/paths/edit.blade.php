<x-layout.app title="Editar Trilha">

    <div class="mb-6">
        <a href="{{ route('paths.show', $path) }}" class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar
        </a>
    </div>

    @php $pathTrainingIds = $path->trainings->pluck('id')->toArray(); @endphp

    <style>
        @keyframes ai-shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .ai-loading-field {
            border-color: rgba(139, 92, 246, 0.6) !important;
            background: linear-gradient(90deg, #f5f3ff 25%, #ede9fe 50%, #f5f3ff 75%) !important;
            background-size: 200% 100%;
            animation: ai-shimmer 1.5s ease-in-out infinite;
            color: transparent !important;
        }
        .ai-loading-field::placeholder { color: transparent !important; }
    </style>

    <script>
        window.__pathSuggestInfo = function(ctx) {
            if (ctx.selected.length === 0) return;
            const names = ctx.selected.map(id => ctx.trainingNames[id]).filter(Boolean);
            if (names.length === 0) return;

            const titleInput = document.getElementById('title');
            const descInput = document.getElementById('description');
            const headers = { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content };

            if (!ctx.title.trim()) {
                titleInput.classList.add('ai-loading-field');
                fetch('/api/ai/suggest-title', {
                    method: 'POST',
                    headers: headers,
                    body: JSON.stringify({ level: 'training', input: names.join(', '), context: 'Esta é uma TRILHA DE APRENDIZAGEM que agrupa treinamentos. O título deve representar a jornada completa.' }),
                })
                .then(r => r.json())
                .then(data => { if (data.title && !ctx.title.trim()) ctx.title = data.title; })
                .catch(() => {})
                .finally(() => {
                    titleInput.classList.remove('ai-loading-field');
                    if (ctx.title.trim() && !ctx.description.trim()) window.__pathSuggestDescription(ctx);
                });
            } else if (!ctx.description.trim()) {
                window.__pathSuggestDescription(ctx);
            }
        };

        window.__pathSuggestDescription = function(ctx) {
            if (ctx.description.trim()) return;
            const names = ctx.selected.map(id => ctx.trainingNames[id]).filter(Boolean);
            const descInput = document.getElementById('description');
            descInput.classList.add('ai-loading-field');
            const headers = { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content };
            fetch('/api/ai/generate-description', {
                method: 'POST',
                headers: headers,
                body: JSON.stringify({ title: ctx.title || names.join(', '), context: 'Treinamentos incluídos: ' + names.join(', '), type: 'path' }),
            })
            .then(r => r.json())
            .then(data => { if (data.description && !ctx.description.trim()) ctx.description = data.description; })
            .catch(() => {})
            .finally(() => { descInput.classList.remove('ai-loading-field'); });
        };
    </script>

    <form method="POST" action="{{ route('paths.update', $path) }}"
          x-data="{
              title: '{{ old('title', addslashes($path->title)) }}',
              description: '{{ old('description', addslashes($path->description ?? '')) }}',
              active: {{ old('active', $path->active) ? 'true' : 'false' }},
              search: '',
              selected: @js(old('trainings', $pathTrainingIds)),
              trainingNames: @js($trainings->pluck('title', 'id')),
              _suggestTimer: null,
              get selectedCount() { return this.selected.length; },
              isSelected(id) { return this.selected.includes(id); },
              toggle(id) {
                  if (this.isSelected(id)) {
                      this.selected = this.selected.filter(i => i != id);
                  } else {
                      this.selected.push(id);
                  }
                  clearTimeout(this._suggestTimer);
                  const self = this;
                  this._suggestTimer = setTimeout(() => {
                      if (!self.title.trim() || !self.description.trim()) {
                          window.__pathSuggestInfo(self);
                      }
                  }, 500);
              }
          }">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-6xl">

            {{-- Coluna principal --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Dados da trilha --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-800">Dados da Trilha</h3>
                            <p class="text-xs text-gray-400">Informações da jornada de aprendizagem</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label for="title" class="block text-sm font-medium text-gray-700">Nome da trilha <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                    </svg>
                                </div>
                                <input type="text" id="title" name="title"
                                    x-model="title" required
                                    placeholder="Ex: Onboarding 2026"
                                    class="w-full pl-10 pr-3 rounded-lg border @error('title') border-red-400 @else border-gray-300 @enderror px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                            @error('title') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="description" class="block text-sm font-medium text-gray-700">Descrição <span class="text-xs text-gray-400 font-normal">(opcional)</span></label>
                            <textarea id="description" name="description" rows="3"
                                x-model="description"
                                placeholder="Descreva o objetivo desta trilha e para quem ela é voltada..."
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                            <p class="text-xs text-gray-400">A descrição ajuda os colaboradores a entenderem o objetivo da trilha.</p>
                        </div>


                        <label class="flex items-center gap-2 cursor-pointer pt-2">
                            <input type="hidden" name="active" value="0">
                            <input type="checkbox" name="active" value="1"
                                x-model="active"
                                class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary">
                            <span class="text-sm text-gray-700">Trilha ativa</span>
                            <span class="text-xs text-gray-400">(visível para os colaboradores)</span>
                        </label>
                    </div>
                </div>

                {{-- Treinamentos --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-gray-800">Treinamentos da Trilha <span class="text-xs text-gray-400 font-normal">(opcional)</span></h3>
                            <p class="text-xs text-gray-400">Selecione quais treinamentos fazem parte desta jornada</p>
                        </div>
                        <span x-show="selectedCount > 0" x-cloak
                            class="text-xs font-semibold text-primary bg-primary/10 px-2.5 py-1 rounded-full"
                            x-text="selectedCount + ' selecionado' + (selectedCount > 1 ? 's' : '')"></span>
                    </div>

                    @if($trainings->isEmpty())
                        <div class="text-center py-8 px-4 border-2 border-dashed border-gray-200 rounded-lg">
                            <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm text-gray-500 font-medium">Nenhum treinamento cadastrado</p>
                            <p class="text-xs text-gray-400 mt-1">Crie treinamentos antes de montar uma trilha.</p>
                            <a href="{{ route('trainings.create') }}" class="inline-block mt-3 text-xs text-primary hover:underline font-medium">+ Criar primeiro treinamento</a>
                        </div>
                    @else
                        <div class="relative mb-4">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text" x-model="search" placeholder="Buscar treinamento..."
                                class="w-full pl-10 pr-4 py-2.5 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>

                        <div class="space-y-1 max-h-80 overflow-y-auto pr-1 -mr-1">
                            @foreach($trainings as $training)
                                <label class="flex items-center gap-3 p-3 rounded-lg cursor-pointer transition border border-transparent"
                                       :class="isSelected({{ $training->id }}) ? 'bg-primary/5 border-primary/20' : 'hover:bg-gray-50'"
                                       x-show="!search || '{{ strtolower($training->title) }}'.includes(search.toLowerCase())"
                                       x-cloak>
                                    <input type="checkbox" name="trainings[]" value="{{ $training->id }}"
                                        @click="toggle({{ $training->id }})"
                                        {{ in_array($training->id, old('trainings', $pathTrainingIds)) ? 'checked' : '' }}
                                        class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer">
                                    <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-primary" fill="currentColor" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd" d="M4.5 5.653c0-1.426 1.529-2.33 2.779-1.643l11.54 6.348c1.295.712 1.295 2.573 0 3.285L7.28 19.991c-1.25.687-2.779-.217-2.779-1.643V5.653z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-700 truncate">{{ $training->title }}</p>
                                        <p class="text-xs text-gray-400">{{ $training->calculatedDuration() }} min{{ $training->has_quiz ? ' · Com quiz' : '' }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

            {{-- Coluna lateral: Preview --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm p-6 sticky top-6">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Pré-visualização</p>

                    <div class="flex flex-col items-center text-center pb-5 border-b border-gray-100">
                        <div class="w-20 h-20 rounded-2xl flex items-center justify-center mb-3"
                             style="background: linear-gradient(135deg, var(--primary), var(--secondary))">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                        </div>
                        <p class="text-base font-semibold text-gray-800 break-words" x-text="title || 'Nome da trilha'"></p>
                        <p class="text-xs text-gray-400 mt-1 line-clamp-3 break-words" x-text="description || 'Sem descrição'"></p>
                    </div>

                    <div class="pt-4 space-y-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Treinamentos</span>
                            <span class="font-semibold text-gray-800" x-text="selectedCount"></span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Status</span>
                            <template x-if="active">
                                <span class="inline-flex items-center gap-1 text-green-600 font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    Ativa
                                </span>
                            </template>
                            <template x-if="!active">
                                <span class="inline-flex items-center gap-1 text-gray-400 font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                                    Inativa
                                </span>
                            </template>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Criada em</span>
                            <span class="font-medium text-gray-800">{{ $path->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>

                    <div class="mt-5 pt-5 border-t border-gray-100 space-y-3">
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 bg-primary hover:bg-secondary text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Salvar Alterações
                        </button>
                        <a href="{{ route('paths.show', $path) }}" class="block text-center text-sm text-gray-500 hover:text-gray-700 transition py-1">
                            Cancelar
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </form>

</x-layout.app>
