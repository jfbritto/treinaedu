<x-layout.app title="Resultado do Quiz">

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
        @endphp

        {{-- Result Hero --}}
        <div class="rounded-xl overflow-hidden shadow-sm mb-6 text-white relative"
             @if($passed)
                 style="background: linear-gradient(135deg, #10b981, #059669)"
             @else
                 style="background: linear-gradient(135deg, #ef4444, #dc2626)"
             @endif>
            {{-- Decorative circles --}}
            <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-16 -left-16 w-56 h-56 rounded-full bg-white/10"></div>

            <div class="relative p-8 text-center">
                {{-- Icon --}}
                <div class="w-20 h-20 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center mx-auto mb-4">
                    @if($passed)
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    @else
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    @endif
                </div>

                <p class="text-xs text-white/80 uppercase tracking-wider mb-1">Resultado do quiz</p>
                <h2 class="text-2xl font-bold mb-1">{{ $passed ? 'Parabéns, você foi aprovado!' : 'Você ainda não foi aprovado' }}</h2>
                <p class="text-sm text-white/80">{{ $training->title }}</p>
            </div>

            {{-- Score section --}}
            <div class="relative bg-white/10 backdrop-blur px-8 py-5 border-t border-white/20">
                <div class="grid grid-cols-2 gap-4 text-center">
                    <div>
                        <p class="text-xs text-white/70 uppercase tracking-wide mb-1">Sua pontuação</p>
                        <p class="text-3xl font-bold">{{ $score }}%</p>
                    </div>
                    <div>
                        <p class="text-xs text-white/70 uppercase tracking-wide mb-1">Mínimo para aprovação</p>
                        <p class="text-3xl font-bold">{{ $passingScore }}%</p>
                    </div>
                </div>

                {{-- Progress bar --}}
                <div class="mt-4">
                    <div class="w-full rounded-full h-2" style="background-color: rgba(255,255,255,0.25)">
                        <div class="bg-white h-2 rounded-full transition-all" style="width: {{ $score }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions card --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg {{ $passed ? 'bg-green-50' : 'bg-amber-50' }} flex items-center justify-center flex-shrink-0">
                        @if($passed)
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-gray-800">{{ $passed ? 'Próximos passos' : 'O que fazer agora?' }}</h3>
                        <p class="text-xs text-gray-400">{{ $passed ? 'Continue avançando no treinamento' : 'Revise o conteúdo e tente novamente' }}</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                @if($passed)
                    @if($quizLevel === 'training')
                        {{-- Final training quiz passed --}}
                        <div class="flex items-start gap-3 mb-5 p-4 bg-green-50 border border-green-100 rounded-xl">
                            <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-green-800">Treinamento completo!</p>
                                <p class="text-xs text-green-700 mt-0.5">Você finalizou todas as etapas. Gere seu certificado para registrar esta conquista.</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('employee.certificates.generate', $training) }}" class="space-y-3">
                            @csrf
                            <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 text-white font-semibold px-4 py-3 rounded-lg text-sm transition shadow-sm"
                                style="background: linear-gradient(135deg, var(--primary), var(--secondary))">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                </svg>
                                Gerar Certificado
                            </button>
                        </form>
                    @elseif($nextLessonUrl)
                        {{-- Lesson/module quiz passed, go to next lesson --}}
                        <div class="flex items-start gap-3 mb-5 p-4 bg-green-50 border border-green-100 rounded-xl">
                            <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-green-800">Quiz concluído com sucesso</p>
                                <p class="text-xs text-green-700 mt-0.5">A próxima aula foi liberada. Continue seu aprendizado.</p>
                            </div>
                        </div>
                        <a href="{{ $nextLessonUrl }}"
                            class="w-full inline-flex items-center justify-center gap-2 text-white font-semibold px-4 py-3 rounded-lg text-sm transition shadow-sm"
                            style="background-color: var(--primary)">
                            Ir para a próxima aula
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @elseif($nextQuizUrl)
                        {{-- Last lesson quiz passed, training has a final quiz available --}}
                        <div class="flex items-start gap-3 mb-5 p-4 rounded-xl text-white"
                             style="background: linear-gradient(135deg, var(--primary), var(--secondary))">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm font-semibold">Você desbloqueou o quiz final!</p>
                                <p class="text-xs opacity-90 mt-0.5">Todas as aulas e quizzes foram concluídos. Faça o quiz final para finalizar o treinamento.</p>
                            </div>
                        </div>
                        <a href="{{ $nextQuizUrl }}"
                            class="w-full inline-flex items-center justify-center gap-2 text-white font-semibold px-4 py-3 rounded-lg text-sm transition shadow-sm"
                            style="background: linear-gradient(135deg, var(--primary), var(--secondary))">
                            {{ $nextQuizLabel }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @else
                        {{-- Last lesson/module quiz passed, no final quiz --}}
                        <div class="flex items-start gap-3 mb-5 p-4 bg-green-50 border border-green-100 rounded-xl">
                            <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-green-800">Quiz concluído com sucesso</p>
                                <p class="text-xs text-green-700 mt-0.5">Volte ao treinamento para finalizar sua jornada.</p>
                            </div>
                        </div>
                        <a href="{{ route('employee.trainings.show', $training) }}"
                            class="w-full inline-flex items-center justify-center gap-2 text-white font-semibold px-4 py-3 rounded-lg text-sm transition shadow-sm"
                            style="background-color: var(--primary)">
                            Voltar ao treinamento
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @endif
                @else
                    <div class="flex items-start gap-3 mb-5 p-4 bg-amber-50 border border-amber-100 rounded-xl">
                        <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-amber-800">Faltaram {{ max(0, $passingScore - $score) }}% para a aprovação</p>
                            <p class="text-xs text-amber-700 mt-0.5">Revise a aula novamente e tente o quiz até conseguir a pontuação mínima de {{ $passingScore }}%.</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <a href="{{ route('employee.quiz.show', array_filter(['training' => $training, 'module' => $moduleId, 'lesson' => $lessonId])) }}"
                            class="w-full inline-flex items-center justify-center gap-2 text-white font-semibold px-4 py-3 rounded-lg text-sm transition shadow-sm"
                            style="background-color: var(--primary)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Tentar novamente
                        </a>
                        <a href="{{ route('employee.trainings.show', $training) }}"
                            class="w-full inline-flex items-center justify-center gap-2 text-gray-700 font-semibold px-4 py-3 rounded-lg text-sm transition border border-gray-200 hover:bg-gray-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            Revisar aula
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

</x-layout.app>
