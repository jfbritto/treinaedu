<x-layout.app :title="$training->title">

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">

        {{-- Sidebar (1/4) --}}
        <div class="lg:col-span-1 order-2 lg:order-1">
            <div class="sticky top-4 space-y-3">
                <x-ui.course-sidebar
                    :training="$training"
                    :current-lesson="$currentLesson"
                    :lesson-views="$lessonViews"
                    :unlock-states="$unlockStates"
                    :training-progress="$trainingProgress"
                />

                {{-- Completion / Certificate --}}
                @if($canComplete)
                    <form method="POST" action="{{ route('employee.trainings.complete', $training) }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 text-white font-semibold py-2.5 rounded-xl text-sm transition shadow-sm" style="background-color: var(--primary)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Concluir Treinamento
                        </button>
                    </form>
                @endif

                @if($canGenerateCertificate)
                    <div class="bg-green-50 border border-green-200 rounded-xl p-3 text-center">
                        <form method="POST" action="{{ route('employee.certificates.generate', $training) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-3 py-2 rounded-lg transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0"/>
                                </svg>
                                Gerar Certificado
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        {{-- Content area (3/4) --}}
        <div class="lg:col-span-3 order-1 lg:order-2">
            @if($currentLesson)
                @php
                    $allLessons = $training->modules->flatMap->lessons;
                    $currentIndex = $allLessons->search(fn($l) => $l->id === $currentLesson->id);
                    $nextLesson = $currentIndex !== false ? $allLessons->get($currentIndex + 1) : null;
                    $nextUnlocked = $nextLesson && ($unlockStates['lessons'][$nextLesson->id] ?? false);
                    $nextLessonUrl = $nextLesson
                        ? route('employee.trainings.show', ['training' => $training, 'lesson' => $nextLesson->id, 'autoplay' => 1])
                        : null;
                @endphp

                <x-ui.lesson-player
                    :lesson="$currentLesson"
                    :lesson-view="$lessonViews[$currentLesson->id] ?? null"
                    :training="$training"
                    :next-lesson-url="$nextLessonUrl"
                />

                @if($nextLesson && $nextUnlocked)
                    <div class="mt-2 flex justify-end">
                        <a href="{{ route('employee.trainings.show', ['training' => $training, 'lesson' => $nextLesson->id]) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-white transition shadow-sm" style="background-color: var(--primary)">
                            Próxima aula
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    </div>
                @endif
            @else
                <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <p class="text-gray-400">Nenhuma aula disponível neste treinamento.</p>
                </div>
            @endif
        </div>
    </div>

</x-layout.app>
