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
                    <form method="POST" action="{{ route('employee.certificates.generate', $training) }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 text-white font-semibold py-2.5 rounded-xl text-sm transition shadow-sm bg-green-600 hover:bg-green-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Gerar Certificado
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Content area (3/4) --}}
        <div class="lg:col-span-3 order-1 lg:order-2" x-data="{ showCelebration: false }" @training-last-lesson-ended.window="showCelebration = true">
            @if($currentLesson)
                @php
                    $allLessons = $training->modules->flatMap->lessons;
                    $currentIndex = $allLessons->search(fn($l) => $l->id === $currentLesson->id);
                    $nextLesson = $currentIndex !== false ? $allLessons->get($currentIndex + 1) : null;
                    $prevLesson = $currentIndex !== false && $currentIndex > 0 ? $allLessons->get($currentIndex - 1) : null;
                    $nextUnlocked = $nextLesson && ($unlockStates['lessons'][$nextLesson->id] ?? false);
                    $prevUnlocked = $prevLesson && ($unlockStates['lessons'][$prevLesson->id] ?? false);
                    $nextLessonUrl = $nextLesson
                        ? route('employee.trainings.show', ['training' => $training, 'lesson' => $nextLesson->id, 'autoplay' => 1])
                        : null;
                    $prevLessonUrl = $prevLesson && $prevUnlocked
                        ? route('employee.trainings.show', ['training' => $training, 'lesson' => $prevLesson->id])
                        : null;
                    $currentNum = $currentIndex !== false ? $currentIndex + 1 : 1;
                    $totalLessons = $allLessons->count();
                    $isLastLesson = !$nextLesson;
                @endphp

                {{-- Lesson player (hidden when celebration shows) --}}
                <div x-show="!showCelebration">
                    <x-ui.lesson-player
                        :lesson="$currentLesson"
                        :lesson-view="$lessonViews[$currentLesson->id] ?? null"
                        :training="$training"
                        :next-lesson-url="$nextLessonUrl"
                        :prev-lesson-url="$prevLessonUrl"
                        :current-num="$currentNum"
                        :total-lessons="$totalLessons"
                    />
                </div>

                {{-- Celebration screen --}}
                <div x-show="showCelebration" x-cloak
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="rounded-xl overflow-hidden text-center py-16 px-8"
                     style="background: linear-gradient(135deg, var(--primary), var(--secondary))">

                    {{-- Confetti animation --}}
                    <div class="relative" x-init="$watch('showCelebration', val => { if (val) startConfetti() })">
                        <canvas id="confetti-canvas" class="absolute inset-0 w-full h-full pointer-events-none" style="z-index: 1"></canvas>

                        <div class="relative" style="z-index: 2">
                            {{-- Star icon --}}
                            <div class="text-6xl mb-6">🎉</div>

                            <h2 class="text-3xl font-bold text-white mb-3">Parabéns!</h2>
                            <p class="text-white/80 text-lg mb-2">Você concluiu todas as aulas de</p>
                            <p class="text-white font-bold text-xl mb-8">{{ $training->title }}</p>

                            <div class="flex flex-col items-center gap-3">
                                @if($canComplete)
                                    <form method="POST" action="{{ route('employee.trainings.complete', $training) }}">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-2 bg-white font-bold px-8 py-3 rounded-xl text-sm transition shadow-lg hover:shadow-xl hover:scale-105" style="color: var(--primary)">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                            </svg>
                                            Concluir Treinamento
                                        </button>
                                    </form>
                                @endif

                                <button @click="showCelebration = false" class="text-white/60 hover:text-white text-sm transition">
                                    Voltar para a aula
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <p class="text-gray-400">Nenhuma aula disponível neste treinamento.</p>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
    function startConfetti() {
        const canvas = document.getElementById('confetti-canvas');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        canvas.width = canvas.parentElement.offsetWidth;
        canvas.height = canvas.parentElement.offsetHeight;

        const particles = [];
        const colors = ['#FFD700', '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7', '#DDA0DD', '#98FB98'];

        for (let i = 0; i < 80; i++) {
            particles.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height - canvas.height,
                w: Math.random() * 8 + 4,
                h: Math.random() * 4 + 2,
                color: colors[Math.floor(Math.random() * colors.length)],
                speed: Math.random() * 3 + 2,
                angle: Math.random() * 360,
                spin: (Math.random() - 0.5) * 8,
                drift: (Math.random() - 0.5) * 2,
            });
        }

        let frame = 0;
        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            particles.forEach(p => {
                p.y += p.speed;
                p.x += p.drift;
                p.angle += p.spin;
                ctx.save();
                ctx.translate(p.x, p.y);
                ctx.rotate(p.angle * Math.PI / 180);
                ctx.fillStyle = p.color;
                ctx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h);
                ctx.restore();
            });
            frame++;
            if (frame < 180) requestAnimationFrame(animate);
        }
        animate();
    }
    </script>
    @endpush

</x-layout.app>
