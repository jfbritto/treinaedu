<x-layout.app :title="$training->title">

    {{-- Botões de ação --}}
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('trainings.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-800 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <span class="text-sm font-medium">Voltar</span>
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('trainings.edit', $training) }}"
               class="inline-flex items-center gap-2 bg-primary hover:bg-secondary text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar
            </a>
            <form method="POST" action="{{ route('trainings.destroy', $training) }}" data-confirm="Remover este treinamento?" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Deletar
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Coluna principal --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Detalhes do treinamento --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $training->title }}</h1>
                    @if($training->description)
                        <p class="text-gray-600 mt-2">{{ $training->description }}</p>
                    @endif
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    {{-- Duração --}}
                    @php
                        $totalMin = $training->calculatedDuration();
                        if ($totalMin >= 60) {
                            $hours = floor($totalMin/60);
                            $mins = $totalMin % 60;
                            $durText = $mins > 0 ? "{$hours}h {$mins}min" : "{$hours}h";
                        } else {
                            $durText = "{$totalMin} min";
                        }
                    @endphp
                    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Duração</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $durText }}</p>
                    </div>

                    {{-- Status --}}
                    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 rounded-xl {{ $training->active ? 'bg-green-50' : 'bg-gray-100' }} flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 {{ $training->active ? 'text-green-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($training->active)
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    @endif
                                </svg>
                            </div>
                        </div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Status</p>
                        <div class="flex items-center gap-1.5 mt-1">
                            <span class="w-2 h-2 rounded-full {{ $training->active ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                            <p class="text-2xl font-bold {{ $training->active ? 'text-gray-800' : 'text-gray-400' }}">{{ $training->active ? 'Ativo' : 'Inativo' }}</p>
                        </div>
                    </div>

                    {{-- Quiz --}}
                    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 rounded-xl {{ $training->has_quiz ? 'bg-purple-50' : 'bg-gray-100' }} flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 {{ $training->has_quiz ? 'text-purple-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Avaliação</p>
                        <p class="text-2xl font-bold {{ $training->has_quiz ? 'text-gray-800' : 'text-gray-400' }} mt-1">{{ $training->has_quiz ? 'Com quiz' : 'Sem quiz' }}</p>
                    </div>

                    {{-- Conclusão --}}
                    @php $completion = $training->completionRate(); @endphp
                    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Conclusão</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $completion }}%</p>
                        <div class="mt-2 w-full bg-gray-100 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full bg-primary transition-all" style="width: {{ $completion }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Conteúdo do treinamento --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-800">Estrutura do Conteúdo</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Módulos e aulas que compõem o treinamento</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    {{-- Stats compactas --}}
                    @php
                        $stats = [
                            ['label' => 'Módulo', 'labelPlural' => 'Módulos', 'count' => $training->modules->count(), 'color' => 'blue', 'icon' => 'M20 7l-8 4-8-4m16 0l-8-4-8 4m16 0v10l-8 4m0-14L4 7m8 4v10'],
                            ['label' => 'Aula', 'labelPlural' => 'Aulas', 'count' => $totalLessons, 'color' => 'primary', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                        ];
                        if (isset($contentTypes['video']) && $contentTypes['video'] > 0) {
                            $stats[] = ['label' => 'Vídeo', 'labelPlural' => 'Vídeos', 'count' => $contentTypes['video'], 'color' => 'red', 'icon' => 'M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z'];
                        }
                        if (isset($contentTypes['document']) && $contentTypes['document'] > 0) {
                            $stats[] = ['label' => 'Documento', 'labelPlural' => 'Documentos', 'count' => $contentTypes['document'], 'color' => 'amber', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'];
                        }
                        if (isset($contentTypes['text']) && $contentTypes['text'] > 0) {
                            $stats[] = ['label' => 'Texto', 'labelPlural' => 'Textos', 'count' => $contentTypes['text'], 'color' => 'green', 'icon' => 'M4 6h16M4 12h16M4 18h7'];
                        }
                        $colorMap = [
                            'blue' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600'],
                            'primary' => ['bg' => 'bg-primary/10', 'text' => 'text-primary'],
                            'red' => ['bg' => 'bg-red-50', 'text' => 'text-red-500'],
                            'amber' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600'],
                            'green' => ['bg' => 'bg-green-50', 'text' => 'text-green-600'],
                        ];
                    @endphp
                    <div class="grid grid-cols-2 sm:grid-cols-{{ min(count($stats), 4) }} gap-3 mb-6">
                        @foreach($stats as $stat)
                            @php $c = $colorMap[$stat['color']]; @endphp
                            <div class="bg-white rounded-xl border border-gray-100 p-4 hover:shadow-sm transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl {{ $c['bg'] }} flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 {{ $c['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-gray-800 leading-none">{{ $stat['count'] }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $stat['count'] !== 1 ? $stat['labelPlural'] : $stat['label'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Estrutura de módulos --}}
                    @if($training->modules->isNotEmpty())
                        <div class="space-y-3">
                            @foreach($training->modules as $index => $module)
                                @php
                                    $moduleLessons = $module->lessons->count();
                                    $moduleDuration = $module->lessons->sum('duration_minutes');
                                    $lessonsWithQuiz = $module->lessons->filter(fn($l) => $l->quiz)->count();
                                @endphp
                                <div class="border border-gray-200 rounded-xl overflow-hidden hover:border-primary/30 transition">
                                    {{-- Cabeçalho do módulo --}}
                                    <div class="bg-gradient-to-r from-gray-50 to-white px-5 py-4 flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                            <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 text-sm font-bold text-white shadow-sm"
                                                 style="background: linear-gradient(135deg, var(--primary), var(--secondary))">
                                                {{ $index + 1 }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-sm font-semibold text-gray-800 truncate">{{ $module->title }}</h4>
                                                <div class="flex items-center gap-3 mt-1 text-xs text-gray-500 flex-wrap">
                                                    <span class="inline-flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                        </svg>
                                                        {{ $moduleLessons }} {{ $moduleLessons !== 1 ? 'aulas' : 'aula' }}
                                                    </span>
                                                    @if($moduleDuration > 0)
                                                        <span class="inline-flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                            {{ $moduleDuration }} min
                                                        </span>
                                                    @endif
                                                    @if($module->is_sequential)
                                                        <span class="inline-flex items-center gap-1 text-blue-600">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                                            </svg>
                                                            Sequencial
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @if($module->quiz)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-100 flex-shrink-0"
                                                  title="Este módulo tem um quiz de avaliação">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                </svg>
                                                Quiz do módulo
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Aulas do módulo --}}
                                    @if($moduleLessons > 0)
                                        <div class="divide-y divide-gray-50 bg-white">
                                            @foreach($module->lessons as $li => $lesson)
                                                @php
                                                    $typeConfig = [
                                                        'video' => ['bg' => 'bg-red-50', 'text' => 'text-red-500', 'icon' => 'M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z'],
                                                        'document' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                                                        'text' => ['bg' => 'bg-green-50', 'text' => 'text-green-600', 'icon' => 'M4 6h16M4 12h16M4 18h7'],
                                                    ];
                                                    $t = $typeConfig[$lesson->type] ?? $typeConfig['text'];
                                                @endphp
                                                <div class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50 transition">
                                                    <span class="text-xs font-medium text-gray-400 w-5 text-center flex-shrink-0">{{ $li + 1 }}</span>
                                                    <div class="w-7 h-7 rounded-lg {{ $t['bg'] }} flex items-center justify-center flex-shrink-0">
                                                        <svg class="w-3.5 h-3.5 {{ $t['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $t['icon'] }}"/>
                                                        </svg>
                                                    </div>
                                                    <span class="flex-1 text-sm text-gray-700 truncate">{{ $lesson->title }}</span>
                                                    @if($lesson->quiz)
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-purple-50 text-purple-700 border border-purple-100 flex-shrink-0"
                                                              title="Esta aula tem quiz de verificação">
                                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                            </svg>
                                                            Quiz
                                                        </span>
                                                    @endif
                                                    @if($lesson->duration_minutes)
                                                        <span class="text-xs text-gray-400 flex-shrink-0">{{ $lesson->duration_minutes }} min</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach

                            {{-- Quiz final do treinamento --}}
                            @if($training->trainingQuiz)
                                <div class="border border-purple-200 rounded-xl overflow-hidden bg-gradient-to-br from-purple-50 to-white">
                                    <div class="px-5 py-4 flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 text-white shadow-sm"
                                             style="background: linear-gradient(135deg, #9333ea, #7e22ce)">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-sm font-semibold text-gray-800">Avaliação Final</h4>
                                            <div class="flex items-center gap-3 mt-1 text-xs text-gray-500">
                                                <span class="inline-flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                    </svg>
                                                    Quiz de conclusão do treinamento
                                                </span>
                                                @if($training->passing_score)
                                                    <span class="inline-flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                        Nota mínima: {{ $training->passing_score }}%
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700 border border-purple-200 flex-shrink-0">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Obrigatório
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Grupos atribuídos --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-800">Grupos Atribuídos</h3>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $training->assignments->count() }} {{ $training->assignments->count() === 1 ? 'grupo atribuído' : 'grupos atribuídos' }}</p>
                        </div>
                    </div>
                </div>

                @if($training->assignments->isEmpty())
                    <div class="p-8 text-center">
                        <svg class="w-10 h-10 text-gray-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <p class="text-sm text-gray-400">Nenhum grupo atribuído ainda.</p>
                    </div>
                @else
                    <div class="p-6 space-y-3">
                        @foreach($training->assignments as $assignment)
                            @php
                                $overdue  = $assignment->due_date && $assignment->due_date->isPast();
                                $soonDays = $assignment->due_date ? (int) now()->diffInDays($assignment->due_date, false) : null;
                                $dueSoon  = $soonDays !== null && $soonDays >= 0 && $soonDays <= 7;
                            @endphp
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-primary/30 transition">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-start gap-3 flex-1">
                                        {{-- Ícone grupo --}}
                                        <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                                            </svg>
                                        </div>
                                        {{-- Info do grupo --}}
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-sm font-semibold text-gray-800">{{ $assignment->group->name }}</h4>
                                            <div class="flex items-center gap-2 flex-wrap mt-2">
                                                @if($assignment->mandatory)
                                                    <span class="inline-flex items-center gap-1 text-xs font-medium bg-red-100 text-red-700 rounded-full px-2 py-0.5">
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 100-2 1 1 0 000 2zm0 4a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
                                                        Obrigatório
                                                    </span>
                                                @else
                                                    <span class="text-xs text-gray-500 bg-gray-100 rounded-full px-2 py-0.5">Opcional</span>
                                                @endif
                                                @if($assignment->due_date)
                                                    <span class="text-xs {{ $overdue ? 'text-red-600 font-medium bg-red-50' : ($dueSoon ? 'text-yellow-700 bg-yellow-50' : 'text-gray-600 bg-gray-50') }} rounded-full px-2 py-0.5">
                                                        {{ $assignment->due_date->format('d/m/Y') }}
                                                        @if($overdue)
                                                            <svg class="w-3 h-3 inline-block ml-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                                        @elseif($dueSoon)
                                                            ({{ $soonDays }}d)
                                                        @endif
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Remover --}}
                                    <form method="POST"
                                          action="{{ route('trainings.assignments.destroy', [$training, $assignment]) }}"
                                          data-confirm="Remover atribuição do grupo {{ $assignment->group->name }}?"
                                          class="flex-shrink-0">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs font-medium text-red-500 hover:text-red-700 hover:bg-red-50 rounded px-2 py-1 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Adicionar grupos --}}
            @if($availableGroups->isNotEmpty())
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Atribuir a mais grupos</h3>
                    <form method="POST" action="{{ route('trainings.assignments.store', $training) }}" class="space-y-4">
                        @csrf

                        {{-- Grupos disponíveis --}}
                        <div class="space-y-1">
                            <label class="block text-xs font-medium text-gray-600">Selecione os grupos</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 border border-gray-200 rounded-xl p-3 bg-gray-50">
                                @foreach($availableGroups as $group)
                                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer hover:text-gray-900 transition">
                                        <input type="checkbox" name="group_ids[]" value="{{ $group->id }}"
                                               class="rounded border-gray-300 text-primary">
                                        {{ $group->name }}
                                    </label>
                                @endforeach
                            </div>
                            @error('group_ids')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Prazo --}}
                            <div class="space-y-1">
                                <label class="block text-xs font-medium text-gray-600">Data limite (opcional)</label>
                                <input type="date" name="due_date" value="{{ old('due_date') }}"
                                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                                @error('due_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            {{-- Obrigatório --}}
                            <div class="flex items-end pb-0.5">
                                <label class="flex items-center gap-2.5 cursor-pointer p-3 rounded-xl border border-gray-200 hover:border-red-200 hover:bg-red-50 transition group w-full">
                                    <input type="checkbox" name="mandatory" value="1"
                                           {{ old('mandatory') ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-red-500">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700 group-hover:text-red-700 transition">Obrigatório</p>
                                        <p class="text-xs text-gray-400">Exige conclusão do colaborador</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <button type="submit"
                                    class="inline-flex items-center gap-2 bg-primary hover:bg-secondary text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Atribuir grupos
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="bg-gray-50 border border-dashed border-gray-200 rounded-xl p-4 text-center text-xs text-gray-400">
                    Todos os grupos já estão atribuídos a este treinamento.
                </div>
            @endif

            {{-- Usuários vinculados --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-6 py-5 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-800">Usuários Vinculados</h3>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $assignedUsers->count() }} colaborador{{ $assignedUsers->count() !== 1 ? 'es' : '' }} atribuído{{ $assignedUsers->count() !== 1 ? 's' : '' }}</p>
                        </div>
                    </div>
                </div>

                @if($assignedUsers->isEmpty())
                    <div class="p-8 text-center">
                        <svg class="w-10 h-10 text-gray-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <p class="text-sm text-gray-400">Nenhum usuário vinculado a este treinamento.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="text-left px-6 py-4 font-semibold text-gray-700 uppercase tracking-wider text-xs">Usuário</th>
                                    <th class="text-center px-6 py-4 font-semibold text-gray-700 uppercase tracking-wider text-xs">Progresso</th>
                                    <th class="text-left px-6 py-4 font-semibold text-gray-700 uppercase tracking-wider text-xs hidden md:table-cell">Último Acesso</th>
                                    <th class="text-center px-6 py-4 font-semibold text-gray-700 uppercase tracking-wider text-xs">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-150">
                                @foreach($assignedUsers as $user)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                                    <span class="text-xs font-semibold text-primary">{{ substr($user['name'], 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-800">{{ $user['name'] }}</p>
                                                    <p class="text-xs text-gray-500">{{ $user['email'] }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-center gap-2">
                                                <div class="w-20 bg-gray-200 rounded-full h-2.5">
                                                    <div class="bg-primary h-2.5 rounded-full transition-all" style="width: {{ $user['progress_percent'] }}%"></div>
                                                </div>
                                                <span class="text-xs font-bold text-gray-800 min-w-[35px] text-right">{{ $user['progress_percent'] }}%</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 hidden md:table-cell">
                                            @if($user['last_accessed'])
                                                <p class="text-sm text-gray-700">{{ $user['last_accessed']->format('d/m/Y H:i') }}</p>
                                            @else
                                                <span class="text-xs text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($user['completed'])
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Concluído
                                                </span>
                                            @elseif($user['progress_percent'] > 0)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                    </svg>
                                                    Em Progresso
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    Não Iniciado
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>

        {{-- Sidebar info --}}
        <div class="space-y-4">
            {{-- Resumo Rápido --}}
            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Resumo</p>

                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-2xl font-bold text-gray-800 leading-none">{{ $training->assignments->count() }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $training->assignments->count() === 1 ? 'Grupo atribuído' : 'Grupos atribuídos' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-2xl font-bold text-gray-800 leading-none">{{ $assignedUsers->count() }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $assignedUsers->count() === 1 ? 'Colaborador' : 'Colaboradores' }}</p>
                        </div>
                    </div>

                    @php
                        $completionRate = $training->completionRate();
                        $completionColor = $completionRate >= 50 ? 'green' : ($completionRate >= 25 ? 'amber' : 'gray');
                        $completionColorMap = [
                            'green' => ['bg' => 'bg-green-50', 'icon' => 'text-green-600', 'bar' => 'bg-green-500'],
                            'amber' => ['bg' => 'bg-amber-50', 'icon' => 'text-amber-600', 'bar' => 'bg-amber-500'],
                            'gray' => ['bg' => 'bg-gray-50', 'icon' => 'text-gray-400', 'bar' => 'bg-gray-300'],
                        ];
                        $cc = $completionColorMap[$completionColor];
                    @endphp
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-xl {{ $cc['bg'] }} flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 {{ $cc['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-2xl font-bold text-gray-800 leading-none">{{ $completionRate }}%</p>
                                <p class="text-xs text-gray-400 mt-1">Taxa de conclusão</p>
                            </div>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5">
                            <div class="{{ $cc['bar'] }} h-1.5 rounded-full transition-all" style="width: {{ $completionRate }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Metadata --}}
            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Informações</p>

                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-400">Criado em</p>
                            <p class="text-sm font-medium text-gray-800 mt-0.5">{{ $training->created_at->format('d/m/Y') }} às {{ $training->created_at->format('H:i') }}</p>
                            <p class="text-[11px] text-gray-400">{{ $training->created_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    @if($training->updated_at->gt($training->created_at))
                        <div class="flex items-start gap-3 pt-3 border-t border-gray-100">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-gray-400">Última atualização</p>
                                <p class="text-sm font-medium text-gray-800 mt-0.5">{{ $training->updated_at->format('d/m/Y') }} às {{ $training->updated_at->format('H:i') }}</p>
                                <p class="text-[11px] text-gray-400">{{ $training->updated_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Como funciona --}}
            <div class="bg-gradient-to-br from-primary/5 to-primary/0 border border-primary/15 rounded-xl p-5">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-800">Dicas rápidas</p>
                </div>
                <ul class="space-y-2.5 text-xs text-gray-600">
                    <li class="flex items-start gap-2">
                        <svg class="w-3.5 h-3.5 text-primary flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Ao atribuir a um <strong>grupo</strong>, todos os membros veem o treinamento automaticamente.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-3.5 h-3.5 text-primary flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Marcar como <strong>obrigatório</strong> sinaliza que a conclusão é exigida.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-3.5 h-3.5 text-primary flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>A <strong>data limite</strong> aparece com urgência quando o prazo se aproxima.</span>
                    </li>
                </ul>
            </div>

        </div>

    </div>

</x-layout.app>
