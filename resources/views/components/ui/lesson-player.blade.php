@props(['lesson', 'lessonView', 'training', 'nextLessonUrl' => null, 'prevLessonUrl' => null, 'currentNum' => 1, 'totalLessons' => 1])

@php
    $progress = $lessonView?->progress_percent ?? 0;
    $lessonHasQuiz = (bool) $lesson->quiz;
    $lessonQuizPassed = $lessonHasQuiz && auth()->user()->quizAttempts()
        ->where('quiz_id', $lesson->quiz->id)
        ->where('passed', true)
        ->exists();
    $quizUrl = $lessonHasQuiz
        ? route('employee.quiz.show', ['training' => $training, 'lesson' => $lesson->id])
        : null;
    // When lesson has a pending quiz, block advancing to next lesson
    $requiresQuiz = $lessonHasQuiz && !$lessonQuizPassed;
    $effectiveNextUrl = $requiresQuiz ? null : $nextLessonUrl;
@endphp

<div>
    {{-- Content based on type --}}
    @if($lesson->isVideo())
        <x-ui.video-player
            :video-url="$lesson->video_url"
            :provider="$lesson->video_provider"
            :training-id="$lesson->id"
            :initial-progress="$progress"
            :next-lesson-url="$effectiveNextUrl"
            :quiz-url="$requiresQuiz ? $quizUrl : null"
        />

        {{-- Control bar --}}
        <div class="bg-white rounded-b-xl shadow-sm px-4 py-3" x-data="{ pct: {{ $progress }}, completed: {{ $progress >= 90 ? 'true' : 'false' }} }"
             @video-progress.window="pct = $event.detail.percent; if (pct >= 90) completed = true">
            {{-- Progress bar --}}
            <div class="w-full bg-gray-100 rounded-full h-1.5 mb-3">
                <div class="h-1.5 rounded-full transition-all" :style="'width: ' + pct + '%; background-color: var(--secondary)'"></div>
            </div>

            {{-- Controls row --}}
            <div class="flex items-center justify-between">
                {{-- Prev button --}}
                <div class="w-32">
                    @if($prevLessonUrl)
                        <a href="{{ $prevLessonUrl }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Anterior
                        </a>
                    @endif
                </div>

                {{-- Center info --}}
                <div class="flex items-center gap-3 text-xs text-gray-500">
                    <span class="font-medium" style="color: var(--secondary)" x-text="pct + '%'"></span>
                    <span class="text-gray-300">|</span>
                    <span>Aula {{ $currentNum }} de {{ $totalLessons }}</span>
                    <span x-show="pct >= 100" x-cloak class="flex items-center gap-1 text-green-600 font-medium">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd"/>
                        </svg>
                        Concluída
                    </span>
                </div>

                {{-- Next button --}}
                <div class="w-32 text-right">
                    @if($nextLessonUrl)
                        @if($requiresQuiz)
                            {{-- Blocked by pending quiz --}}
                            <span
                                @click="Swal.fire({ icon: 'info', title: 'Quiz pendente', text: 'Você precisa realizar e passar no quiz desta aula para avançar.', confirmButtonText: 'Fazer quiz', confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--primary').trim() }).then((r) => { if (r.isConfirmed) window.location.href = '{{ $quizUrl }}'; })"
                                class="inline-flex items-center gap-1.5 text-sm font-semibold px-3 py-1.5 rounded-lg text-gray-400 bg-gray-100 cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                Próxima
                            </span>
                        @else
                            {{-- Enabled state --}}
                            <a x-show="completed" href="{{ $nextLessonUrl }}" class="inline-flex items-center gap-1.5 text-sm font-semibold transition px-3 py-1.5 rounded-lg text-white" style="background-color: var(--primary)">
                                Próxima
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                            {{-- Disabled state --}}
                            <span x-show="!completed" x-cloak
                                  @click="Swal.fire({ icon: 'info', title: 'Aula em andamento', text: 'Assista a aula atual antes de avançar para a próxima.', confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--primary').trim() })"
                                  class="inline-flex items-center gap-1.5 text-sm font-semibold px-3 py-1.5 rounded-lg text-gray-400 bg-gray-100 cursor-not-allowed">
                                Próxima
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </span>
                        @endif
                    @endif
                </div>
            </div>
        </div>

    @elseif($lesson->isDocument())
        {{-- Document header --}}
        <div class="flex items-center gap-2 mb-2">
            <span class="text-xs font-medium px-2 py-0.5 rounded-full" style="background-color: color-mix(in srgb, var(--primary) 10%, transparent); color: var(--primary)">Documento</span>
            <h2 class="text-base font-bold text-gray-800 truncate">{{ $lesson->title }}</h2>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden" x-data="{ marked: {{ $lessonView?->completed_at ? 'true' : 'false' }} }">
            @if($lesson->file_path)
                <iframe src="{{ Storage::disk('public')->url($lesson->file_path) }}" class="w-full" style="height: 70vh" frameborder="0"></iframe>
            @endif
            <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    @if($prevLessonUrl)
                        <a href="{{ $prevLessonUrl }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            Anterior
                        </a>
                    @endif
                    <a href="{{ Storage::disk('public')->url($lesson->file_path) }}" target="_blank" download
                        class="inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:underline"
                        @click="if (!marked) { fetch('/api/lesson-progress', { method: 'POST', headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content}, body: JSON.stringify({lesson_id: {{ $lesson->id }}, progress_percent: 100}) }); marked = true; }">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Baixar
                    </a>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-gray-400">Aula {{ $currentNum }} de {{ $totalLessons }}</span>
                    <span x-show="marked" class="text-xs text-green-600 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd"/></svg>
                        Concluída
                    </span>
                    @if($nextLessonUrl)
                        @if($requiresQuiz)
                            <span
                                @click="Swal.fire({ icon: 'info', title: 'Quiz pendente', text: 'Você precisa realizar e passar no quiz desta aula para avançar.', confirmButtonText: 'Fazer quiz', confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--primary').trim() }).then((r) => { if (r.isConfirmed) window.location.href = '{{ $quizUrl }}'; })"
                                class="inline-flex items-center gap-1.5 text-sm font-semibold px-3 py-1.5 rounded-lg text-gray-400 bg-gray-100 cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Próxima
                            </span>
                        @else
                            <a href="{{ $nextLessonUrl }}" class="inline-flex items-center gap-1.5 text-sm font-semibold px-3 py-1.5 rounded-lg text-white transition" style="background-color: var(--primary)">
                                Próxima <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        @if(!$lessonView?->completed_at)
            @push('scripts')
            <script>
                fetch('/api/lesson-progress', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: JSON.stringify({ lesson_id: {{ $lesson->id }}, progress_percent: 100 }) });
            </script>
            @endpush
        @endif

    @elseif($lesson->isText())
        {{-- Text header --}}
        <div class="flex items-center gap-2 mb-2">
            <span class="text-xs font-medium px-2 py-0.5 rounded-full" style="background-color: color-mix(in srgb, var(--primary) 10%, transparent); color: var(--primary)">Texto</span>
            <h2 class="text-base font-bold text-gray-800 truncate">{{ $lesson->title }}</h2>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden"
             x-data="{ completed: {{ $lessonView?->completed_at ? 'true' : 'false' }} }"
             x-init="if (!completed) { fetch('/api/lesson-progress', { method: 'POST', headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content}, body: JSON.stringify({lesson_id: {{ $lesson->id }}, progress_percent: 100}) }); completed = true; }">
            <div class="p-6 prose prose-sm max-w-none" style="max-height: 60vh; overflow-y: auto">
                {!! nl2br(e($lesson->content)) !!}
            </div>
            <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    @if($prevLessonUrl)
                        <a href="{{ $prevLessonUrl }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            Anterior
                        </a>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-gray-400">Aula {{ $currentNum }} de {{ $totalLessons }}</span>
                    <span x-show="completed" x-cloak class="text-xs text-green-600 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd"/></svg>
                        Concluída
                    </span>
                    <span x-show="!completed" class="text-xs text-gray-400">Lendo...</span>
                    @if($nextLessonUrl)
                        @if($requiresQuiz)
                            <span
                                @click="Swal.fire({ icon: 'info', title: 'Quiz pendente', text: 'Você precisa realizar e passar no quiz desta aula para avançar.', confirmButtonText: 'Fazer quiz', confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--primary').trim() }).then((r) => { if (r.isConfirmed) window.location.href = '{{ $quizUrl }}'; })"
                                class="inline-flex items-center gap-1.5 text-sm font-semibold px-3 py-1.5 rounded-lg text-gray-400 bg-gray-100 cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Próxima
                            </span>
                        @else
                            <a href="{{ $nextLessonUrl }}" class="inline-flex items-center gap-1.5 text-sm font-semibold px-3 py-1.5 rounded-lg text-white transition" style="background-color: var(--primary)">
                                Próxima <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Lesson quiz CTA --}}
    @if($lessonHasQuiz)
        @php
            $lessonProgress = $lessonView?->progress_percent ?? 0;
            $threshold = $lesson->completionThreshold();
            $lessonMeetsThreshold = $lessonProgress >= $threshold;
        @endphp

        @if($lessonQuizPassed)
            {{-- Quiz passed --}}
            <div class="mt-4 bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-green-800">Quiz aprovado</p>
                    <p class="text-xs text-green-700">{{ $nextLessonUrl ? 'Você pode avançar para a próxima aula.' : 'Confira o quadro ao lado para os próximos passos.' }}</p>
                </div>
            </div>
        @elseif($lessonMeetsThreshold)
            {{-- Quiz pending — lesson watched, blocking next lesson --}}
            <div class="mt-4 rounded-xl p-5 text-white relative overflow-hidden"
                 style="background: linear-gradient(135deg, var(--primary), var(--secondary))">
                <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full bg-white/10"></div>
                <div class="relative flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-white/70 uppercase tracking-wider mb-0.5">Quiz pendente</p>
                        <p class="text-base font-bold">Complete o quiz para avançar</p>
                        <p class="text-xs text-white/80 mt-0.5">Você precisa passar no quiz desta aula antes de ir para a próxima.</p>
                    </div>
                    <a href="{{ $quizUrl }}"
                        class="flex-shrink-0 inline-flex items-center gap-1.5 bg-white text-primary px-4 py-2 rounded-lg text-sm font-semibold transition hover:scale-105 shadow-sm">
                        Fazer quiz
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        @else
            {{-- Quiz exists but lesson not yet completed --}}
            <div class="mt-4 bg-gray-50 border border-gray-200 rounded-xl p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-700">Quiz disponível ao concluir a aula</p>
                    <p class="text-xs text-gray-500">Assista toda a aula para liberar o quiz e poder avançar.</p>
                </div>
            </div>
        @endif
    @endif
</div>
