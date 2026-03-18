<x-layout.app title="Novo Treinamento">
    <div class="max-w-3xl">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('trainings.index') }}" class="text-gray-400 hover:text-gray-600">← Voltar</a>
            <h2 class="text-xl font-semibold text-gray-800">Novo Treinamento</h2>
        </div>

        {{-- Como funciona --}}
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-4">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-blue-900 mb-3">Como funciona o vídeo na plataforma</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="flex gap-2.5">
                            <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">1</span>
                            <div>
                                <p class="text-sm font-medium text-blue-800">Suba o vídeo</p>
                                <p class="text-xs text-blue-600 mt-0.5">Suba no <a href="https://youtube.com/upload" target="_blank" rel="noopener" class="underline font-medium">YouTube</a> ou <a href="https://vimeo.com/upload" target="_blank" rel="noopener" class="underline font-medium">Vimeo</a>, ou use qualquer vídeo já publicado (público, não listado ou até de canais externos). Recomendamos <em>não listado</em> para conteúdo exclusivo da empresa.</p>
                            </div>
                        </div>
                        <div class="flex gap-2.5">
                            <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">2</span>
                            <div>
                                <p class="text-sm font-medium text-blue-800">Copie o link</p>
                                <p class="text-xs text-blue-600 mt-0.5">Copie a URL do vídeo diretamente da barra de endereço ou do botão "Compartilhar" da plataforma de vídeo.</p>
                            </div>
                        </div>
                        <div class="flex gap-2.5">
                            <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">3</span>
                            <div>
                                <p class="text-sm font-medium text-blue-800">Cole aqui e pronto</p>
                                <p class="text-xs text-blue-600 mt-0.5">Cole no campo abaixo. A plataforma incorpora o player automaticamente para os colaboradores assistirem.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <form method="POST" action="{{ route('trainings.store') }}" class="space-y-6" x-data="trainingForm()">
                @csrf

                <x-forms.input name="title" label="Título" required />
                <div class="space-y-1">
                    <label class="block text-sm font-medium text-gray-700">Descrição</label>
                    <textarea name="description" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                </div>

                <div class="space-y-1">
                    <label class="block text-sm font-medium text-gray-700">URL do Vídeo <span class="text-red-500">*</span></label>
                    <input type="url" name="video_url" value="{{ old('video_url') }}" required
                        placeholder="https://www.youtube.com/watch?v=... ou https://vimeo.com/..."
                        class="w-full rounded-lg border @error('video_url') border-red-400 @else border-gray-300 @enderror px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-400 mt-1">
                        Aceito: <code class="bg-gray-100 px-1 rounded">youtube.com/watch?v=…</code>,
                        <code class="bg-gray-100 px-1 rounded">youtu.be/…</code>,
                        <code class="bg-gray-100 px-1 rounded">vimeo.com/…</code>
                    </p>
                    @error('video_url')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <x-forms.input name="duration_minutes" label="Duração (minutos)" type="number" required />

                {{-- Quiz section --}}
                <div class="border-t pt-4">
                    <label class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-4">
                        <input type="checkbox" name="has_quiz" value="1" x-model="hasQuiz"
                            {{ old('has_quiz') ? 'checked' : '' }}
                            class="rounded border-gray-300">
                        Este treinamento possui quiz
                    </label>

                    <div x-show="hasQuiz" class="space-y-4">
                        <x-forms.input name="passing_score" label="Nota mínima de aprovação (%)" type="number" />

                        <div class="space-y-4" id="questions-container">
                            <template x-for="(q, qi) in questions" :key="qi">
                                <div class="border rounded-lg p-4 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-700" x-text="'Questão ' + (qi + 1)"></span>
                                        <button type="button" @click="removeQuestion(qi)" class="text-red-500 text-sm hover:underline" x-show="questions.length > 1">Remover</button>
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
</x-layout.app>

@push('scripts')
<script>
function trainingForm() {
    return {
        hasQuiz: {{ old('has_quiz') ? 'true' : 'false' }},
        questions: [{ text: '', correct: 0, options: [{ text: '' }, { text: '' }] }],
        addQuestion() {
            this.questions.push({ text: '', correct: 0, options: [{ text: '' }, { text: '' }] });
        },
        removeQuestion(qi) {
            this.questions.splice(qi, 1);
        },
        addOption(qi) {
            this.questions[qi].options.push({ text: '' });
        },
        removeOption(qi, oi) {
            this.questions[qi].options.splice(oi, 1);
        }
    }
}
</script>
@endpush
