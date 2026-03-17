<x-layout.app title="Novo Treinamento">
    <div class="max-w-3xl">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('trainings.index') }}" class="text-gray-400 hover:text-gray-600">← Voltar</a>
            <h2 class="text-xl font-semibold text-gray-800">Novo Treinamento</h2>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <form method="POST" action="{{ route('trainings.store') }}" class="space-y-6" x-data="trainingForm()">
                @csrf

                <x-forms.input name="title" label="Título" required />
                <div class="space-y-1">
                    <label class="block text-sm font-medium text-gray-700">Descrição</label>
                    <textarea name="description" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                </div>
                <x-forms.input name="video_url" label="URL do Vídeo (YouTube ou Vimeo)" type="url" required />
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
