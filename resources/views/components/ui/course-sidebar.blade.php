@props(['training', 'currentLesson', 'lessonViews', 'unlockStates', 'trainingProgress'])

@php
    $moduleIds = $training->modules->pluck('id')->toArray();
@endphp

<div class="bg-white rounded-xl shadow-sm overflow-hidden" x-data="{ trainingPct: {{ $trainingProgress }}, openModules: @js($moduleIds) }"
     @training-progress-updated.window="trainingPct = $event.detail.trainingProgress">

    {{-- Header with progress --}}
    <div class="px-4 py-3" style="border-bottom: 1px solid color-mix(in srgb, var(--primary) 15%, transparent)">
        <h3 class="text-sm font-bold text-gray-800 mb-1.5">{{ $training->title }}</h3>
        <div class="flex items-center gap-2">
            <div class="flex-1 bg-gray-100 rounded-full h-1.5">
                <div class="bg-primary h-1.5 rounded-full transition-all" :style="'width: ' + trainingPct + '%'"></div>
            </div>
            <span class="text-xs font-semibold text-primary" x-text="trainingPct + '%'"></span>
        </div>
    </div>

    {{-- Modules --}}
    <div>
        @foreach($training->modules as $mi => $module)
            @php
                $moduleUnlocked = $unlockStates['modules'][$module->id] ?? false;
                $moduleLessonIds = $module->lessons->pluck('id');
                $completedLessons = $moduleLessonIds->filter(fn($id) => isset($lessonViews[$id]) && $lessonViews[$id]->completed_at)->count();
                $totalLessons = $moduleLessonIds->count();
                $moduleComplete = $completedLessons === $totalLessons && $totalLessons > 0;
            @endphp

            {{-- Module header --}}
            <button
                @click="openModules.includes({{ $module->id }}) ? openModules = openModules.filter(id => id !== {{ $module->id }}) : openModules.push({{ $module->id }})"
                class="w-full flex items-center gap-2 px-4 py-2.5 text-left transition border-b border-gray-50 {{ $moduleUnlocked ? 'hover:bg-gray-50' : 'opacity-50' }}"
                {{ !$moduleUnlocked ? 'disabled' : '' }}>

                <svg class="w-3.5 h-3.5 flex-shrink-0 transition-transform duration-200 {{ $moduleUnlocked ? 'text-gray-500' : 'text-gray-300' }}"
                     :class="openModules.includes({{ $module->id }}) ? 'rotate-90' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>

                <span class="flex-1 text-xs font-bold uppercase tracking-wide {{ $moduleUnlocked ? 'text-gray-600' : 'text-gray-400' }} truncate">
                    {{ $module->title }}
                </span>

                <span class="text-xs font-medium flex-shrink-0 {{ $moduleComplete ? 'text-green-500' : 'text-gray-400' }}">
                    {{ $completedLessons }}/{{ $totalLessons }}
                </span>
            </button>

            {{-- Lessons --}}
            <div x-show="openModules.includes({{ $module->id }})"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                @foreach($module->lessons as $lesson)
                    @php
                        $lessonUnlocked = $unlockStates['lessons'][$lesson->id] ?? false;
                        $lessonView = $lessonViews[$lesson->id] ?? null;
                        $lessonComplete = $lessonView && $lessonView->completed_at;
                        $isCurrent = $currentLesson && $currentLesson->id === $lesson->id;
                    @endphp
                    @if($lessonUnlocked)
                        <a href="{{ route('employee.trainings.show', ['training' => $training, 'lesson' => $lesson->id]) }}"
                            class="flex items-center gap-2.5 pl-9 pr-4 py-2 text-sm transition {{ $isCurrent ? 'border-l-3 bg-primary/5' : 'hover:bg-gray-50' }}"
                            @if($isCurrent) style="border-left: 3px solid var(--primary)" @endif>

                            {{-- Status dot --}}
                            @if($lessonComplete)
                                <div class="w-2 h-2 rounded-full bg-green-500 flex-shrink-0"></div>
                            @elseif($isCurrent)
                                <div class="w-2 h-2 rounded-full flex-shrink-0" style="background-color: var(--primary)"></div>
                            @else
                                <div class="w-2 h-2 rounded-full bg-gray-300 flex-shrink-0"></div>
                            @endif

                            <span class="flex-1 truncate {{ $isCurrent ? 'font-medium text-primary' : ($lessonComplete ? 'text-gray-500' : 'text-gray-700') }}">{{ $lesson->title }}</span>

                            @if($lesson->duration_minutes > 0)
                                <span class="text-xs text-gray-400 flex-shrink-0">{{ $lesson->duration_minutes }}min</span>
                            @endif
                        </a>
                    @else
                        <div class="flex items-center gap-2.5 pl-9 pr-4 py-2 text-sm text-gray-300">
                            <div class="w-2 h-2 rounded-full bg-gray-200 flex-shrink-0"></div>
                            <span class="flex-1 truncate">{{ $lesson->title }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
        @endforeach
    </div>
</div>
