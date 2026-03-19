@props(['training', 'currentLesson', 'lessonViews', 'unlockStates', 'trainingProgress'])

@php
    $moduleIds = $training->modules->pluck('id')->toArray();
    $openByDefault = json_encode($moduleIds);
@endphp

<div class="bg-white rounded-xl shadow-sm overflow-hidden" x-data="{ trainingPct: {{ $trainingProgress }}, openModules: {{ $openByDefault }} }"
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

    {{-- Modules --}}
    <div>
        @foreach($training->modules as $module)
            @php
                $moduleUnlocked = $unlockStates['modules'][$module->id] ?? false;
                $moduleLessonIds = $module->lessons->pluck('id');
                $completedLessons = $moduleLessonIds->filter(fn($id) => isset($lessonViews[$id]) && $lessonViews[$id]->completed_at)->count();
                $totalLessons = $moduleLessonIds->count();
                $moduleComplete = $completedLessons === $totalLessons && $totalLessons > 0;
            @endphp
            <div class="border-b border-gray-100 last:border-b-0">
                {{-- Module header --}}
                <button
                    @click="openModules.includes({{ $module->id }}) ? openModules = openModules.filter(id => id !== {{ $module->id }}) : openModules.push({{ $module->id }})"
                    class="w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-gray-50 transition"
                    {{ !$moduleUnlocked ? 'disabled' : '' }}>

                    @if(!$moduleUnlocked)
                        <svg class="w-5 h-5 text-gray-300 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 00-5.25 5.25v3a3 3 0 00-3 3v6.75a3 3 0 003 3h10.5a3 3 0 003-3v-6.75a3 3 0 00-3-3v-3c0-2.9-2.35-5.25-5.25-5.25zm3.75 8.25v-3a3.75 3.75 0 10-7.5 0v3h7.5z" clip-rule="evenodd"/>
                        </svg>
                    @elseif($moduleComplete)
                        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd"/>
                        </svg>
                    @else
                        <svg class="w-5 h-5 flex-shrink-0 transition-transform duration-200" :class="openModules.includes({{ $module->id }}) ? 'rotate-90' : ''" fill="none" stroke="currentColor" style="color: var(--primary)" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    @endif

                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold {{ $moduleUnlocked ? 'text-gray-800' : 'text-gray-400' }} truncate">{{ $module->title }}</p>
                    </div>
                    <span class="text-xs {{ $moduleComplete ? 'text-green-500' : ($moduleUnlocked ? 'text-gray-400' : 'text-gray-300') }} flex-shrink-0">{{ $completedLessons }}/{{ $totalLessons }}</span>
                </button>

                {{-- Lessons list with slide animation --}}
                <div x-show="openModules.includes({{ $module->id }})"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="bg-gray-50/50">
                    @foreach($module->lessons as $lesson)
                        @php
                            $lessonUnlocked = $unlockStates['lessons'][$lesson->id] ?? false;
                            $lessonView = $lessonViews[$lesson->id] ?? null;
                            $lessonComplete = $lessonView && $lessonView->completed_at;
                            $isCurrent = $currentLesson && $currentLesson->id === $lesson->id;
                        @endphp
                        @if($lessonUnlocked)
                            <a href="{{ route('employee.trainings.show', ['training' => $training, 'lesson' => $lesson->id]) }}"
                                class="flex items-center gap-3 px-4 py-2 pl-7 text-sm transition {{ $isCurrent ? 'bg-primary/10 border-l-3' : 'hover:bg-gray-100' }}"
                                @if($isCurrent) style="border-left: 3px solid var(--primary)" @endif>
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
                                <span class="flex-1 {{ $isCurrent ? 'font-semibold text-primary' : ($lessonComplete ? 'text-gray-500' : 'text-gray-700') }} truncate text-sm">{{ $lesson->title }}</span>
                                @if($lesson->duration_minutes > 0)
                                    <span class="text-xs text-gray-400 flex-shrink-0">{{ $lesson->duration_minutes }}min</span>
                                @endif
                            </a>
                        @else
                            <div class="flex items-center gap-3 px-4 py-2 pl-7 text-sm text-gray-300 cursor-not-allowed">
                                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 00-5.25 5.25v3a3 3 0 00-3 3v6.75a3 3 0 003 3h10.5a3 3 0 003-3v-6.75a3 3 0 00-3-3v-3c0-2.9-2.35-5.25-5.25-5.25zm3.75 8.25v-3a3.75 3.75 0 10-7.5 0v3h7.5z" clip-rule="evenodd"/>
                                </svg>
                                <span class="flex-1 truncate text-sm">{{ $lesson->title }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
