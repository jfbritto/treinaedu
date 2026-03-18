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

    <div x-data="trainingForm()" class="max-w-6xl">

        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('trainings.index') }}" class="text-gray-400 hover:text-gray-600">← Voltar</a>
            <h2 class="text-xl font-semibold text-gray-800">Novo Treinamento</h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Coluna principal: formulário --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <form method="POST" action="{{ route('trainings.store') }}" class="space-y-5">
                        @csrf

                        <x-forms.input name="title" label="Título" required />

                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Descrição</label>
                            <textarea name="description" rows="3"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">
                                URL do Vídeo <span class="text-red-500">*</span>
                            </label>
                            <input type="url" name="video_url" x-model="videoUrl"
                                value="{{ old('video_url') }}" required
                                placeholder="https://www.youtube.com/watch?v=..."
                                class="w-full rounded-lg border @error('video_url') border-red-400 @else border-gray-300 @enderror px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-400 mt-1">
                                Aceito:
                                <code class="bg-gray-100 px-1 rounded">youtube.com/watch?v=…</code>
                                <code class="bg-gray-100 px-1 rounded">youtu.be/…</code>
                                <code class="bg-gray-100 px-1 rounded">vimeo.com/…</code>
                            </p>
                            @error('video_url')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <x-forms.input name="duration_minutes" label="Duração (minutos)" type="number" required />

                        {{-- Quiz section --}}
                        <div class="border-t pt-4">
                            <label class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-4 cursor-pointer">
                                <input type="checkbox" name="has_quiz" value="1" x-model="hasQuiz"
                                    {{ old('has_quiz') ? 'checked' : '' }}
                                    class="rounded border-gray-300">
                                Este treinamento possui quiz
                            </label>

                            <div x-show="hasQuiz" class="space-y-4">
                                <x-forms.input name="passing_score" label="Nota mínima de aprovação (%)" type="number" />

                                <div class="space-y-4">
                                    <template x-for="(q, qi) in questions" :key="qi">
                                        <div class="border rounded-lg p-4 space-y-3">
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm font-medium text-gray-700" x-text="'Questão ' + (qi + 1)"></span>
                                                <button type="button" @click="removeQuestion(qi)"
                                                    class="text-red-500 text-sm hover:underline"
                                                    x-show="questions.length > 1">Remover</button>
                                            </div>
                                            <input type="text" :name="'questions[' + qi + '][question]'"
                                                x-model="q.text" placeholder="Digite a pergunta..."
                                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                            <div class="space-y-2">
                                                <p class="text-xs text-gray-500">Opções (marque a correta):</p>
                                                <template x-for="(opt, oi) in q.options" :key="oi">
                                                    <div class="flex items-center gap-2">
                                                        <input type="radio" :name="'questions[' + qi + '][correct]'" :value="oi" x-model="q.correct" class="border-gray-300">
                                                        <input type="text" :name="'questions[' + qi + '][options][' + oi + '][text]'"
                                                            x-model="opt.text" placeholder="Opção..."
                                                            class="flex-1 rounded-lg border border-gray-300 px-3 py-1.5 text-sm">
                                                        <button type="button" @click="removeOption(qi, oi)" class="text-red-400 text-sm" x-show="q.options.length > 2">✕</button>
                                                    </div>
                                                </template>
                                                <button type="button" @click="addOption(qi)" class="text-blue-600 text-sm hover:underline">+ Adicionar opção</button>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <button type="button" @click="addQuestion()" class="flex items-center gap-1 text-blue-600 text-sm hover:underline">
                                    + Adicionar questão
                                </button>
                            </div>
                        </div>

                        <div class="flex gap-3 pt-2">
                            <x-forms.button type="submit">Criar Treinamento</x-forms.button>
                            <a href="{{ route('trainings.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Coluna lateral --}}
            <div class="space-y-4">

                {{-- Preview do vídeo --}}
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="px-4 pt-4 pb-3 border-b">
                        <p class="text-sm font-semibold text-gray-700">Preview do vídeo</p>
                        <p class="text-xs text-gray-400 mt-0.5">Cole a URL ao lado para visualizar</p>
                    </div>
                    <div class="aspect-video bg-gray-100 flex items-center justify-center" x-show="!embedUrl">
                        <div class="text-center text-gray-300">
                            <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-xs">Nenhum vídeo</p>
                        </div>
                    </div>
                    <div class="aspect-video" x-show="embedUrl">
                        <iframe x-bind:src="embedUrl" class="w-full h-full"
                            frameborder="0" allowfullscreen
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
                        <div class="w-6 h-6 rounded-md bg-blue-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-blue-900">Como funciona</p>
                    </div>
                    <div class="space-y-3">
                        <div class="flex gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">1</span>
                            <div>
                                <p class="text-xs font-semibold text-blue-800">Suba o vídeo</p>
                                <p class="text-xs text-blue-600 mt-0.5">
                                    No <a href="https://youtube.com/upload" target="_blank" rel="noopener" class="underline font-medium">YouTube</a>
                                    ou <a href="https://vimeo.com/upload" target="_blank" rel="noopener" class="underline font-medium">Vimeo</a>.
                                    Pode ser público, não listado ou até um vídeo externo já existente.
                                </p>
                            </div>
                        </div>
                        <div class="flex gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">2</span>
                            <div>
                                <p class="text-xs font-semibold text-blue-800">Copie o link</p>
                                <p class="text-xs text-blue-600 mt-0.5">Copie a URL da barra de endereço ou do botão "Compartilhar".</p>
                            </div>
                        </div>
                        <div class="flex gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">3</span>
                            <div>
                                <p class="text-xs font-semibold text-blue-800">Cole e visualize</p>
                                <p class="text-xs text-blue-600 mt-0.5">O preview aparece ao lado automaticamente antes de salvar.</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-blue-200">
                        <p class="text-xs text-blue-500">
                            💡 Recomendamos <strong>não listado</strong> para conteúdo exclusivo da empresa — só acessa quem tiver o link.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layout.app>

