<x-layout.app title="Novo Treinamento">

    <script>
        function trainingForm() {
            return {
                hasQuiz: {{ old('has_quiz') ? 'true' : 'false' }},
                videoUrl: @json(old('video_url', '')),
                embedUrl: null,
                init() {
                    this.$watch('videoUrl', url => this.updateEmbed(url));
                    this.updateEmbed(this.videoUrl);
                },
                updateEmbed(url) {
                    if (!url) { this.embedUrl = null; return; }
                    const yt = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([A-Za-z0-9_-]{11})/);
                    if (yt) { this.embedUrl = 'https://www.youtube.com/embed/' + yt[1]; return; }
                    const vm = url.match(/vimeo\.com\/(\d+)/);
                    if (vm) { this.embedUrl = 'https://player.vimeo.com/video/' + vm[1]; return; }
                    this.embedUrl = null;
                },
                questions: [{ text: '', correct: 0, options: [{ text: '' }, { text: '' }] }],
                addQuestion() { this.questions.push({ text: '', correct: 0, options: [{ text: '' }, { text: '' }] }); },
                removeQuestion(qi) { this.questions.splice(qi, 1); },
                addOption(qi) { this.questions[qi].options.push({ text: '' }); },
                removeOption(qi, oi) { this.questions[qi].options.splice(oi, 1); },
            }
        }
    </script>

    <div x-data="trainingForm()">

        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('trainings.index') }}"
               class="inline-flex items-center gap-1 text-sm text-gray-400 hover:text-gray-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Voltar
            </a>
            <h2 class="text-lg font-bold text-gray-800">Novo Treinamento</h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Coluna principal (2/3) --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Dados principais --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <form method="POST" action="{{ route('trainings.store') }}" class="space-y-5" id="training-form">
                        @csrf

                        @if($errors->any())
                            <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-700">
                                <ul class="list-disc list-inside space-y-0.5">
                                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Título <span class="text-red-500">*</span></label>
                            <input type="text" name="title" value="{{ old('title') }}" required
                                   class="w-full rounded-lg border @error('title') border-red-400 @else border-gray-300 @enderror px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Descrição</label>
                            <textarea name="description" rows="3"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">{{ old('description') }}</textarea>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">URL do Vídeo <span class="text-red-500">*</span></label>
                            <input type="url" name="video_url" x-model="videoUrl"
                                   value="{{ old('video_url') }}" required
                                   placeholder="https://www.youtube.com/watch?v=..."
                                   class="w-full rounded-lg border @error('video_url') border-red-400 @else border-gray-300 @enderror px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            @error('video_url')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">Duração (minutos) <span class="text-red-500">*</span></label>
                                <input type="number" name="duration_minutes" value="{{ old('duration_minutes') }}" required min="1"
                                       class="w-full rounded-lg border @error('duration_minutes') border-red-400 @else border-gray-300 @enderror px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                                @error('duration_minutes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div class="flex flex-col justify-end pb-0.5 space-y-1">
                                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                                    <input type="checkbox" name="active" value="1" checked
                                           class="rounded border-gray-300 text-primary">
                                    Treinamento ativo
                                </label>
                                <p class="text-xs text-gray-400">Visível para os colaboradores</p>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Quiz --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-800">Quiz de avaliação</h3>
                            <p class="text-xs text-gray-400">Opcional — aplicado ao final do treinamento</p>
                        </div>
                        <label class="ml-auto flex items-center gap-2 cursor-pointer">
                            <span class="text-xs text-gray-500">Ativar quiz</span>
                            <div class="relative">
                                <input type="checkbox" name="has_quiz" value="1" x-model="hasQuiz"
                                       {{ old('has_quiz') ? 'checked' : '' }}
                                       class="sr-only peer" form="training-form">
                                <div class="w-10 h-5 bg-gray-200 peer-checked:bg-primary rounded-full transition"></div>
                                <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition peer-checked:translate-x-5"></div>
                            </div>
                        </label>
                    </div>

                    <div x-show="hasQuiz" x-cloak class="space-y-5">
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Nota mínima de aprovação (%)</label>
                            <input type="number" name="passing_score" form="training-form"
                                   value="{{ old('passing_score', 70) }}" min="1" max="100"
                                   class="w-32 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>

                        <div class="space-y-4">
                            <template x-for="(q, qi) in questions" :key="qi">
                                <div class="border border-gray-200 rounded-xl p-4 space-y-3 bg-gray-50">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide" x-text="'Questão ' + (qi + 1)"></span>
                                        <button type="button" @click="removeQuestion(qi)" x-show="questions.length > 1"
                                            class="text-xs text-red-500 hover:text-red-700 transition">Remover</button>
                                    </div>
                                    <input type="text" :name="'questions[' + qi + '][question]'"
                                           x-model="q.text" placeholder="Digite a pergunta..."
                                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary" form="training-form">
                                    <div class="space-y-2">
                                        <p class="text-xs font-medium text-gray-500">Opções <span class="text-gray-400 font-normal">(marque a correta)</span></p>
                                        <template x-for="(opt, oi) in q.options" :key="oi">
                                            <div class="flex items-center gap-2">
                                                <input type="radio" :name="'questions[' + qi + '][correct]'" :value="oi"
                                                       x-model="q.correct" class="text-primary border-gray-300" form="training-form">
                                                <input type="text" :name="'questions[' + qi + '][options][' + oi + '][text]'"
                                                       x-model="opt.text" placeholder="Opção..."
                                                       class="flex-1 rounded-lg border border-gray-300 px-3 py-1.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary" form="training-form">
                                                <button type="button" @click="removeOption(qi, oi)" x-show="q.options.length > 2"
                                                    class="text-gray-400 hover:text-red-500 transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>
                                        <button type="button" @click="addOption(qi)"
                                            class="text-xs text-primary hover:text-secondary transition font-medium">+ Adicionar opção</button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <button type="button" @click="addQuestion()"
                            class="inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:text-secondary transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Adicionar questão
                        </button>
                    </div>
                </div>

                {{-- Botões --}}
                <div class="flex gap-3">
                    <button type="submit" form="training-form"
                            class="inline-flex items-center gap-2 bg-primary hover:bg-secondary text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Criar treinamento
                    </button>
                    <a href="{{ route('trainings.index') }}"
                       class="px-4 py-2.5 text-sm font-medium text-gray-500 hover:text-gray-700 transition">Cancelar</a>
                </div>
            </div>

            {{-- Sidebar (1/3) --}}
            <div class="space-y-4">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden sticky top-6">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <p class="text-sm font-semibold text-gray-700">Preview do vídeo</p>
                        <p class="text-xs text-gray-400 mt-0.5">Atualiza ao digitar a URL</p>
                    </div>
                    <div class="aspect-video bg-gray-50 flex items-center justify-center" x-show="!embedUrl">
                        <div class="text-center text-gray-300 p-6">
                            <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-xs">Cole uma URL do YouTube ou Vimeo</p>
                        </div>
                    </div>
                    <div class="aspect-video" x-show="embedUrl">
                        <iframe x-bind:src="embedUrl" class="w-full h-full" frameborder="0" allowfullscreen
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
                        </iframe>
                    </div>
                    <div x-show="videoUrl && !embedUrl" class="px-4 py-3 bg-red-50 border-t border-red-100">
                        <p class="text-xs text-red-600">URL não reconhecida. Use YouTube ou Vimeo.</p>
                    </div>
                </div>

                {{-- Como funciona --}}
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-6 h-6 rounded-md bg-primary flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-blue-900">Como funciona</p>
                    </div>
                    <div class="space-y-3">
                        <div class="flex gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-primary text-white text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">1</span>
                            <div>
                                <p class="text-xs font-semibold text-secondary">Suba o vídeo</p>
                                <p class="text-xs text-primary mt-0.5">No YouTube ou Vimeo. Pode ser público, não listado ou um vídeo existente.</p>
                            </div>
                        </div>
                        <div class="flex gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-primary text-white text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">2</span>
                            <div>
                                <p class="text-xs font-semibold text-secondary">Copie o link</p>
                                <p class="text-xs text-primary mt-0.5">Copie a URL da barra de endereço ou do botão "Compartilhar".</p>
                            </div>
                        </div>
                        <div class="flex gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-primary text-white text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">3</span>
                            <div>
                                <p class="text-xs font-semibold text-secondary">Cole e visualize</p>
                                <p class="text-xs text-primary mt-0.5">O preview aparece ao lado automaticamente antes de salvar.</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-blue-200">
                        <p class="text-xs text-blue-500">Recomendamos <strong>não listado</strong> para conteúdo exclusivo — só acessa quem tiver o link.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-layout.app>
