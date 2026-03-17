<x-layout.app :title="$training->title">

    <div class="max-w-4xl mx-auto space-y-6">

        {{-- Training header --}}
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $training->title }}</h2>
            @if($training->description)
                <p class="mt-1 text-gray-600">{{ $training->description }}</p>
            @endif
        </div>

        {{-- Video player --}}
        <x-ui.video-player
            :videoUrl="$training->video_url"
            :provider="$training->video_provider"
            :trainingId="$training->id"
        />

        {{-- Progress bar --}}
        <div class="bg-white rounded-lg shadow p-4 space-y-2">
            <div class="flex items-center justify-between text-sm text-gray-600">
                <span>Progresso do vídeo</span>
                <span class="font-semibold">{{ $view->progress_percent }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div
                    class="bg-primary h-3 rounded-full transition-all duration-500"
                    style="width: {{ $view->progress_percent }}%"
                ></div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="space-y-4">

            {{-- Mark as completed --}}
            @if($canComplete)
                <form method="POST" action="{{ route('employee.trainings.complete', $training) }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition"
                    >
                        Marcar como Concluído
                    </button>
                </form>
            @endif

            {{-- Completed badge --}}
            @if($isCompleted)
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 text-center font-semibold">
                    Treinamento concluído!
                </div>
            @endif

            {{-- Quiz link --}}
            @if($isCompleted && $training->has_quiz && !$quizPassed)
                <a
                    href="{{ route('employee.quiz.show', $training) }}"
                    class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition"
                >
                    Fazer Quiz
                </a>
            @endif

            {{-- Quiz passed badge --}}
            @if($training->has_quiz && $quizPassed)
                <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-lg p-4 text-center font-semibold">
                    Quiz aprovado!
                </div>
            @endif

            {{-- Generate certificate --}}
            @if($canGenerateCertificate && !$existingCertificate)
                <form method="POST" action="{{ route('employee.certificates.generate', $training) }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full bg-primary hover:bg-primary-dark text-white font-semibold py-3 px-6 rounded-lg transition"
                    >
                        Gerar Certificado
                    </button>
                </form>
            @endif

            {{-- Download certificate --}}
            @if($existingCertificate)
                <a
                    href="{{ route('employee.certificates.download', $existingCertificate) }}"
                    class="block w-full text-center bg-gray-800 hover:bg-gray-900 text-white font-semibold py-3 px-6 rounded-lg transition"
                >
                    Download Certificado
                </a>
            @endif

        </div>

        {{-- Training info --}}
        <div class="bg-white rounded-lg shadow p-4 text-sm text-gray-600 space-y-1">
            <p><span class="font-medium">Duração:</span> {{ $training->duration_minutes }} minutos</p>
            <p><span class="font-medium">Provedor:</span> {{ ucfirst($training->video_provider) }}</p>
            @if($training->has_quiz)
                <p><span class="font-medium">Quiz:</span> Sim (mínimo {{ $training->passing_score ?? 70 }}% para aprovação)</p>
            @endif
        </div>

    </div>

</x-layout.app>
