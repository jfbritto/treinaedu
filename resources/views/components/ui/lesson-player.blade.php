@props(['lesson', 'lessonView', 'training', 'nextLessonUrl' => null])

@php
    $progress = $lessonView?->progress_percent ?? 0;
@endphp

<div>
    {{-- Lesson header (compact) --}}
    <div class="flex items-center gap-2 mb-2">
        <span class="text-xs font-medium px-2 py-0.5 rounded-full" style="background-color: color-mix(in srgb, var(--primary) 10%, transparent); color: var(--primary)">
            {{ match($lesson->type) { 'video' => 'Vídeo', 'document' => 'Documento', 'text' => 'Texto' } }}
        </span>
        <h2 class="text-base font-bold text-gray-800 truncate">{{ $lesson->title }}</h2>
        @if($lesson->duration_minutes > 0)
            <span class="text-xs text-gray-400 flex-shrink-0 ml-auto">{{ $lesson->duration_minutes }} min</span>
        @endif
    </div>

    {{-- Content based on type --}}
    @if($lesson->isVideo())
        <x-ui.video-player
            :video-url="$lesson->video_url"
            :provider="$lesson->video_provider"
            :training-id="$lesson->id"
            :initial-progress="$progress"
            :next-lesson-url="$nextLessonUrl"
        />

        {{-- Compact progress bar (inline, no card) --}}
        <div class="mt-2 px-1" x-data="{ pct: {{ $progress }} }" @video-progress.window="pct = $event.detail.percent">
            <div class="flex items-center gap-3">
                <div class="flex-1 bg-gray-200 rounded-full h-1.5">
                    <div class="h-1.5 rounded-full transition-all" :style="'width: ' + pct + '%; background-color: var(--secondary)'"></div>
                </div>
                <span class="text-xs font-semibold w-10 text-right" style="color: var(--secondary)" x-text="pct + '%'"></span>
                <span x-show="pct >= 100" x-cloak class="text-xs text-green-600 flex items-center gap-1 flex-shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Concluída
                </span>
            </div>
        </div>

    @elseif($lesson->isDocument())
        <div class="bg-white rounded-xl shadow-sm overflow-hidden" x-data="{ marked: {{ $lessonView?->completed_at ? 'true' : 'false' }} }">
            @if($lesson->file_path)
                <iframe src="{{ Storage::url($lesson->file_path) }}" class="w-full" style="height: 70vh" frameborder="0"></iframe>
            @endif
            <div class="p-3 border-t border-gray-100 flex items-center justify-between">
                <a href="{{ Storage::url($lesson->file_path) }}" target="_blank" download
                    class="inline-flex items-center gap-2 text-sm font-medium text-primary hover:underline"
                    @click="if (!marked) { fetch('/api/lesson-progress', { method: 'POST', headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content}, body: JSON.stringify({lesson_id: {{ $lesson->id }}, progress_percent: 100}) }); marked = true; }">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Baixar
                </a>
                <span x-show="marked" class="text-xs text-green-600 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Concluída
                </span>
            </div>
        </div>

        @if(!$lessonView?->completed_at)
            @push('scripts')
            <script>
                fetch('/api/lesson-progress', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ lesson_id: {{ $lesson->id }}, progress_percent: 100 })
                });
            </script>
            @endpush
        @endif

    @elseif($lesson->isText())
        <div class="bg-white rounded-xl shadow-sm p-6 prose prose-sm max-w-none" style="max-height: 70vh; overflow-y: auto"
             x-data="{ completed: {{ $lessonView?->completed_at ? 'true' : 'false' }} }"
             x-init="if (!completed) { setTimeout(() => { fetch('/api/lesson-progress', { method: 'POST', headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content}, body: JSON.stringify({lesson_id: {{ $lesson->id }}, progress_percent: 100}) }); completed = true; }, 30000); }">
            {!! nl2br(e($lesson->content)) !!}

            <div class="mt-4 pt-4 border-t border-gray-100">
                <span x-show="completed" class="text-xs text-green-600 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Aula concluída
                </span>
                <span x-show="!completed" class="text-xs text-gray-400">
                    Leia o conteúdo para completar esta aula...
                </span>
            </div>
        </div>
    @endif

    {{-- Lesson quiz button --}}
    @if($lesson->quiz)
        @php
            $lessonProgress = $lessonView?->progress_percent ?? 0;
            $threshold = $lesson->completionThreshold();
            $lessonMeetsThreshold = $lessonProgress >= $threshold;
            $lessonQuizPassed = auth()->user()->quizAttempts()
                ->where('quiz_id', $lesson->quiz->id)
                ->where('passed', true)->exists();
        @endphp
        <div class="mt-2">
            @if($lessonQuizPassed)
                <span class="inline-flex items-center gap-1.5 text-xs text-green-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Quiz aprovado
                </span>
            @elseif($lessonMeetsThreshold)
                <a href="{{ route('employee.quiz.show', ['training' => $training->id, 'lesson' => $lesson->id]) }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition"
                    style="background-color: var(--primary)">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Fazer quiz
                </a>
            @endif
        </div>
    @endif
</div>
