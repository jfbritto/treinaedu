@props(['training', 'currentLesson', 'lessonViews', 'unlockStates', 'trainingProgress'])

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    {{-- Header with progress --}}
    <div class="p-4" style="border-bottom: 1px solid color-mix(in srgb, var(--primary) 15%, transparent)">
        <h3 class="text-sm font-bold text-gray-800 mb-2">{{ $training->title }}</h3>
        <div class="flex items-center gap-2">
            <div class="flex-1 bg-gray-100 rounded-full h-2">
                <div class="bg-primary h-2 rounded-full transition-all" style="width: {{ $trainingProgress }}%"></div>
            </div>
            <span class="text-xs font-semibold text-primary">{{ $trainingProgress }}%</span>
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
                        <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    @elseif($moduleComplete)
                        <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    @else
                        <svg class="w-4 h-4 flex-shrink-0 transition-transform" :class="openModule === {{ $module->id }} ? 'rotate-90' : ''" fill="none" stroke="currentColor" style="color: var(--primary)" viewBox="0 0 24 24">
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
                            $typeIcon = match($lesson->type) {
                                'video' => 'M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z',
                                'document' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                                'text' => 'M4 6h16M4 12h16M4 18h7',
                                default => '',
                            };
                        @endphp
                        @if($lessonUnlocked)
                            <a href="{{ route('employee.trainings.show', ['training' => $training, 'lesson' => $lesson->id]) }}"
                                class="flex items-center gap-3 px-4 py-2.5 pl-8 text-sm transition {{ $isCurrent ? 'bg-primary/10 border-l-2' : 'hover:bg-gray-100' }}"
                                @if($isCurrent) style="border-left-color: var(--primary)" @endif>
                                @if($lessonComplete)
                                    <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 flex-shrink-0 {{ $isCurrent ? 'text-primary' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $typeIcon }}"/>
                                    </svg>
                                @endif
                                <span class="{{ $isCurrent ? 'font-semibold text-primary' : ($lessonComplete ? 'text-gray-500' : 'text-gray-700') }} truncate">{{ $lesson->title }}</span>
                                @if($lesson->duration_minutes > 0)
                                    <span class="text-xs text-gray-400 flex-shrink-0 ml-auto">{{ $lesson->duration_minutes }}min</span>
                                @endif
                            </a>
                        @else
                            <div class="flex items-center gap-3 px-4 py-2.5 pl-8 text-sm text-gray-300 cursor-not-allowed">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
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
