@props(['training', 'currentLesson', 'lessonViews', 'unlockStates', 'trainingProgress'])

<div class="bg-white rounded-xl shadow-sm overflow-hidden" x-data="{ trainingPct: {{ $trainingProgress }} }"
     @training-progress-updated.window="trainingPct = $event.detail.trainingProgress">
    {{-- Header with progress --}}
    <div class="p-4" style="border-bottom: 1px solid color-mix(in srgb, var(--primary) 15%, transparent)">
        <h3 class="text-sm font-bold text-gray-800 mb-2">{{ $training->title }}</h3>
        <div class="flex items-center gap-2">
            <div class="flex-1 bg-gray-100 rounded-full h-2">
                <div class="bg-primary h-2 rounded-full transition-all" :style="'width: ' + trainingPct + '%'"></div>
            </div>
            <span class="text-xs font-semibold text-primary" x-text="trainingPct + '%'"></span>
        </div>
    </div>

    {{-- Modules accordion --}}
    <div class="divide-y divide-gray-100" x-data="{ openModule: {{ $currentLesson ? $currentLesson->module_id : 'null' }} }">
        @foreach($training->modules as $module)
            @php
                $moduleUnlocked = $unlockStates['modules'][$module->id] ?? false;
                $moduleLessonIds = $module->lessons->pluck('id');
                $completedLessons = $moduleLessonIds->filter(fn($id) => isset($lessonViews[$id]) && $lessonViews[$id]->completed_at)->count();
                $totalLessons = $moduleLessonIds->count();
                $moduleComplete = $completedLessons === $totalLessons;
            @endphp
            <div>
                {{-- Module header --}}
                <button
                    @click="openModule = openModule === {{ $module->id }} ? null : {{ $module->id }}"
                    class="w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-gray-50 transition"
                    {{ !$moduleUnlocked ? 'disabled' : '' }}>

                    @if(!$moduleUnlocked)
                        <svg class="w-5 h-5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    @elseif($moduleComplete)
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    @else
                        <svg class="w-5 h-5 flex-shrink-0 transition-transform" :class="openModule === {{ $module->id }} ? 'rotate-90' : ''" fill="none" stroke="currentColor" style="color: var(--primary)" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    @endif

                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold {{ $moduleUnlocked ? 'text-gray-800' : 'text-gray-400' }} truncate">{{ $module->title }}</p>
                        <p class="text-xs {{ $moduleUnlocked ? 'text-gray-400' : 'text-gray-300' }}">{{ $completedLessons }}/{{ $totalLessons }} aulas</p>
                    </div>
                </button>

                {{-- Lessons list --}}
                <div x-show="openModule === {{ $module->id }}" x-cloak class="bg-gray-50 border-t border-gray-100">
                    @foreach($module->lessons as $lesson)
                        @php
                            $lessonUnlocked = $unlockStates['lessons'][$lesson->id] ?? false;
                            $lessonView = $lessonViews[$lesson->id] ?? null;
                            $lessonComplete = $lessonView && $lessonView->completed_at;
                            $isCurrent = $currentLesson && $currentLesson->id === $lesson->id;
                        @endphp
                        @if($lessonUnlocked)
                            <a href="{{ route('employee.trainings.show', ['training' => $training, 'lesson' => $lesson->id]) }}"
                                class="flex items-center gap-3 px-4 py-2.5 pl-8 text-sm transition {{ $isCurrent ? 'bg-primary/10 border-l-2' : 'hover:bg-gray-100' }}"
                                @if($isCurrent) style="border-left-color: var(--primary)" @endif>
                                @if($lessonComplete)
                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd"/>
                                    </svg>
                                @elseif($lesson->type === 'video')
                                    <svg class="w-5 h-5 flex-shrink-0 {{ $isCurrent ? 'text-primary' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm14.024-.983a1.125 1.125 0 010 1.966l-5.603 3.113A1.125 1.125 0 019 15.113V8.887c0-.857.921-1.4 1.671-.983l5.603 3.113z" clip-rule="evenodd"/>
                                    </svg>
                                @elseif($lesson->type === 'document')
                                    <svg class="w-5 h-5 flex-shrink-0 {{ $isCurrent ? 'text-primary' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V7.875L14.25 1.5H5.625zM7.5 15a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5A.75.75 0 017.5 15zm.75 2.25a.75.75 0 000 1.5H12a.75.75 0 000-1.5H8.25z" clip-rule="evenodd"/>
                                        <path d="M12.971 1.816A5.23 5.23 0 0114.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 013.434 1.279 9.768 9.768 0 00-6.963-6.963z"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 flex-shrink-0 {{ $isCurrent ? 'text-primary' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M4.125 3C3.089 3 2.25 3.84 2.25 4.875V18a3 3 0 003 3h15a3 3 0 01-3-3V4.875C17.25 3.839 16.41 3 15.375 3H4.125zM12 9.75a.75.75 0 000 1.5h1.5a.75.75 0 000-1.5H12zm-.75-2.25a.75.75 0 01.75-.75h1.5a.75.75 0 010 1.5H12a.75.75 0 01-.75-.75zM6 12.75a.75.75 0 000 1.5h7.5a.75.75 0 000-1.5H6zm-.75 3.75a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5H6a.75.75 0 01-.75-.75zM6 6.75a.75.75 0 00-.75.75v3c0 .414.336.75.75.75h3a.75.75 0 00.75-.75v-3A.75.75 0 009 6.75H6z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                                <span class="{{ $isCurrent ? 'font-semibold text-primary' : ($lessonComplete ? 'text-gray-500' : 'text-gray-700') }} truncate">{{ $lesson->title }}</span>
                                @if($lesson->quiz)
                                    @php
                                        $lessonQuizPassed = auth()->user()->quizAttempts()
                                            ->where('quiz_id', $lesson->quiz->id)
                                            ->where('passed', true)->exists();
                                    @endphp
                                    <span class="flex-shrink-0 ml-auto flex items-center gap-1">
                                        @if($lessonQuizPassed)
                                            <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" title="Quiz aprovado">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                            </svg>
                                        @else
                                            <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" title="Quiz pendente">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                        @endif
                                    </span>
                                @elseif($lesson->duration_minutes > 0)
                                    <span class="text-xs text-gray-400 flex-shrink-0 ml-auto">{{ $lesson->duration_minutes }}min</span>
                                @endif
                            </a>
                        @else
                            <div class="flex items-center gap-3 px-4 py-2.5 pl-8 text-sm text-gray-300 cursor-not-allowed">
                                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 00-5.25 5.25v3a3 3 0 00-3 3v6.75a3 3 0 003 3h10.5a3 3 0 003-3v-6.75a3 3 0 00-3-3v-3c0-2.9-2.35-5.25-5.25-5.25zm3.75 8.25v-3a3.75 3.75 0 10-7.5 0v3h7.5z" clip-rule="evenodd"/>
                                </svg>
                                <span class="truncate">{{ $lesson->title }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
