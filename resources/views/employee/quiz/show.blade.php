<x-layout.app :title="'Quiz: ' . $training->title">

    <div class="max-w-3xl mx-auto">

        {{-- Back link --}}
        <div class="mb-6">
            <a href="{{ route('employee.trainings.show', $training) }}" class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Voltar ao treinamento
            </a>
        </div>

        @php
            $passingScore = $training->passing_score ?? 70;
            $totalQuestions = $quiz->questions->count();
        @endphp

        {{-- Hero --}}
        <div class="rounded-xl p-6 mb-6 text-white relative overflow-hidden"
             style="background: linear-gradient(135deg, var(--secondary), var(--primary))">
            <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-16 -left-16 w-56 h-56 rounded-full bg-white/10"></div>

            <div class="relative flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center flex-shrink-0">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-white/70 uppercase tracking-wider mb-1">Quiz</p>
                    <h1 class="text-2xl font-bold mb-1 break-words">{{ $training->title }}</h1>
                    <p class="text-sm text-white/80">Responda todas as perguntas para concluir o quiz.</p>
                </div>
            </div>

            <div class="relative mt-5 grid grid-cols-2 gap-4 pt-4 border-t border-white/20">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-lg bg-white/20 backdrop-blur flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-white/70">Perguntas</p>
                        <p class="text-sm font-bold">{{ $totalQuestions }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-lg bg-white/20 backdrop-blur flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-white/70">Nota mínima</p>
                        <p class="text-sm font-bold">{{ $passingScore }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('employee.quiz.submit', $training) }}?{{ http_build_query(request()->query()) }}"
              x-data="{ answered: 0, total: {{ $totalQuestions }} }"
              @change="answered = $el.querySelectorAll('input[type=radio]:checked').length"
              class="space-y-4">
            @csrf

            @foreach ($quiz->questions as $index => $question)
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                                <span class="text-sm font-bold text-primary">{{ $index + 1 }}</span>
                            </div>
                            <div class="flex-1 min-w-0 pt-1.5">
                                <p class="text-sm font-semibold text-gray-800 leading-snug">{{ $question->question }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 space-y-2">
                        @foreach ($question->options as $option)
                            <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 hover:border-primary/30 hover:bg-primary/5 cursor-pointer transition has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                                <input
                                    type="radio"
                                    name="answers[{{ $question->id }}]"
                                    value="{{ $option->id }}"
                                    class="w-4 h-4 text-primary border-gray-300 focus:ring-primary cursor-pointer"
                                    required
                                >
                                <span class="text-sm text-gray-700 flex-1">{{ $option->option_text }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach

            {{-- Submit bar --}}
            <div class="bg-white rounded-xl shadow-sm p-5 sticky bottom-4">
                <div class="flex items-center gap-4">
                    <div class="flex-1">
                        <div class="flex justify-between text-xs text-gray-400 mb-1">
                            <span>Progresso</span>
                            <span class="font-semibold text-gray-600" x-text="answered + ' de ' + total"></span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full bg-primary transition-all" :style="'width: ' + (answered / total * 100) + '%'"></div>
                        </div>
                    </div>
                    <button type="submit"
                        :disabled="answered < total"
                        :class="answered < total ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'text-white shadow-sm hover:shadow-md'"
                        :style="answered < total ? '' : 'background-color: var(--primary)'"
                        class="flex-shrink-0 inline-flex items-center gap-2 font-semibold px-5 py-2.5 rounded-lg text-sm transition">
                        Enviar respostas
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </form>
    </div>

</x-layout.app>
