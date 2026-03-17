<x-layout.app :title="'Quiz: ' . $training->title">

    <div class="max-w-3xl mx-auto space-y-6">

        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $training->title }}</h2>
            <p class="mt-1 text-gray-600">Responda as perguntas abaixo para concluir o quiz.</p>
        </div>

        <form method="POST" action="{{ route('employee.quiz.submit', $training) }}" class="space-y-6">
            @csrf

            @foreach ($quiz->questions as $index => $question)
                <div class="bg-white rounded-lg shadow p-6 space-y-4">
                    <p class="font-semibold text-gray-900">{{ $index + 1 }}. {{ $question->question }}</p>

                    <div class="space-y-2">
                        @foreach ($question->options as $option)
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input
                                    type="radio"
                                    name="answers[{{ $question->id }}]"
                                    value="{{ $option->id }}"
                                    class="text-primary"
                                    required
                                >
                                <span class="text-gray-700">{{ $option->option_text }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div>
                <button
                    type="submit"
                    class="w-full bg-primary hover:bg-primary-dark text-white font-semibold py-3 px-6 rounded-lg transition"
                >
                    Enviar Respostas
                </button>
            </div>

        </form>

    </div>

</x-layout.app>
