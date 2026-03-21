<x-layout.app title="Resultado do Quiz">

    <div class="max-w-2xl mx-auto space-y-6">

        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $training->title }}</h2>
            <p class="mt-1 text-gray-600">Resultado do Quiz</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6 text-center space-y-4">

            <p class="text-4xl font-bold text-gray-900">Sua pontuação: {{ $score }}%</p>

            @if ($passed)
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 space-y-3">
                    <p class="text-lg font-semibold">Parabéns! Você foi aprovado!</p>

                    @if($quizLevel === 'training')
                        {{-- Final training quiz passed --}}
                        <a
                            href="{{ route('employee.certificates.generate', $training) }}"
                            class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-lg transition"
                            onclick="event.preventDefault(); document.getElementById('certificate-form').submit();"
                        >
                            Gerar Certificado
                        </a>
                        <form id="certificate-form" method="POST" action="{{ route('employee.certificates.generate', $training) }}" class="hidden">
                            @csrf
                        </form>
                    @elseif($nextLessonUrl)
                        {{-- Lesson/module quiz passed, go to next lesson --}}
                        <a
                            href="{{ $nextLessonUrl }}"
                            class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-lg transition"
                        >
                            Próxima Aula
                        </a>
                    @else
                        {{-- Last lesson/module quiz passed --}}
                        <a
                            href="{{ route('employee.trainings.show', $training) }}"
                            class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-lg transition"
                        >
                            Voltar ao Treinamento
                        </a>
                    @endif
                </div>
            @else
                <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-4 space-y-3">
                    <p class="text-lg font-semibold">Você não foi aprovado desta vez.</p>
                    <p class="text-sm">Pontuação mínima: {{ $training->passing_score ?? 70 }}% — Sua pontuação: {{ $score }}%</p>
                    <a
                        href="{{ route('employee.quiz.show', array_filter(['training' => $training, 'module' => $moduleId, 'lesson' => $lessonId])) }}"
                        class="inline-block bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-6 rounded-lg transition"
                    >
                        Tentar novamente
                    </a>
                </div>
            @endif

            <div class="pt-2">
                <a
                    href="{{ route('employee.trainings.show', $training) }}"
                    class="text-sm text-gray-500 hover:text-gray-700 underline"
                >
                    Voltar ao treinamento
                </a>
            </div>

        </div>

    </div>

</x-layout.app>
