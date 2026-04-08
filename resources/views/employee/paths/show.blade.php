<x-layout.app title="{{ $path->title }}">

    {{-- Header --}}
    <div class="rounded-xl p-6 mb-6 text-white" style="background: linear-gradient(135deg, var(--secondary), var(--primary))">
        <div class="flex items-center gap-2 mb-4">
            <a href="{{ route('employee.paths.index') }}" class="text-white/60 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <span class="text-xs text-white/60 uppercase tracking-wider">Trilha de Aprendizagem</span>
        </div>
        <h2 class="text-2xl font-bold mb-1">{{ $path->title }}</h2>
        @if($path->description)
            <p class="text-white/70 text-sm mb-4">{{ $path->description }}</p>
        @endif
        <div class="mt-4">
            <div class="flex justify-between text-sm text-white/60 mb-1.5">
                <span>{{ $completedCount }} de {{ $totalCount }} treinamento{{ $totalCount !== 1 ? 's' : '' }}</span>
                <span class="font-semibold text-white">{{ $progressPercent }}%</span>
            </div>
            <div class="w-full rounded-full h-2.5" style="background-color: rgba(255,255,255,0.25)">
                <div class="bg-white h-2.5 rounded-full transition-all" style="width: {{ $progressPercent }}%"></div>
            </div>
        </div>
    </div>

    {{-- Treinamentos da trilha --}}
    <div class="space-y-3">
        @foreach($path->trainings as $index => $training)
            @php
                $statusColor = match($training->user_status) {
                    'completed' => 'text-green-600 bg-green-100',
                    'in_progress' => 'text-blue-600 bg-blue-100',
                    default => 'text-gray-400 bg-gray-100',
                };
                $statusLabel = match($training->user_status) {
                    'completed' => 'Concluído',
                    'in_progress' => 'Em andamento',
                    default => 'Não iniciado',
                };
            @endphp

            <a href="{{ route('employee.trainings.show', $training) }}"
               class="bg-white rounded-xl shadow-sm hover:shadow-md transition flex items-center gap-4 p-5 group">
                {{-- Número --}}
                <span class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0
                    {{ $training->user_status === 'completed' ? 'bg-green-500 text-white' : 'bg-primary/10 text-primary' }}">
                    @if($training->user_status === 'completed')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    @else
                        {{ $index + 1 }}
                    @endif
                </span>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-medium text-gray-800 group-hover:text-primary transition truncate">
                            {{ $training->title }}
                        </p>
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $statusColor }} flex-shrink-0">
                            {{ $statusLabel }}
                        </span>
                    </div>
                    <div class="flex items-center gap-3 mt-1 text-xs text-gray-400">
                        <span>{{ $training->calculatedDuration() }} min</span>
                        @if($training->has_quiz)
                            <span class="text-primary">Com quiz</span>
                        @endif
                    </div>
                    @if($training->user_status === 'in_progress')
                        <div class="flex items-center gap-2 mt-2">
                            <div class="flex-1 bg-gray-100 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full bg-primary" style="width: {{ $training->user_progress }}%"></div>
                            </div>
                            <span class="text-xs text-gray-400">{{ $training->user_progress }}%</span>
                        </div>
                    @endif
                </div>

                {{-- Seta --}}
                <svg class="w-5 h-5 text-gray-300 group-hover:text-primary transition flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        @endforeach
    </div>

</x-layout.app>
