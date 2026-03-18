<x-layout.app :title="$training->title">

@php
    $dueOverdue  = $effectiveDue && $effectiveDue->isPast();
    $dueSoonDays = $effectiveDue ? (int) now()->diffInDays($effectiveDue, false) : null;
    $dueSoon     = $dueSoonDays !== null && $dueSoonDays >= 0 && $dueSoonDays <= 7;

    // Step states
    $step1Done   = $view->progress_percent >= 90 || $isCompleted;
    $step2Done   = $isCompleted;
    $step3Done   = $training->has_quiz ? $quizPassed : null;
    $certDone    = (bool) $existingCertificate;

    // Steps count
    $totalSteps  = 2 + ($training->has_quiz ? 1 : 0) + 1; // watch + complete + [quiz] + cert
@endphp

{{-- Banner de urgência --}}
@if($isMandatory && !$isCompleted && ($dueOverdue || $dueSoon))
    <div class="mb-5 flex items-start gap-3 rounded-xl px-4 py-3.5
        {{ $dueOverdue ? 'bg-red-50 border border-red-300 text-red-800' : 'bg-yellow-50 border border-yellow-300 text-yellow-800' }}">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="font-semibold text-sm">
                @if($dueOverdue)
                    Prazo vencido — este treinamento obrigatório precisava ser concluído em {{ $effectiveDue->format('d/m/Y') }}.
                @else
                    Prazo se encerrando — você tem {{ $dueSoonDays }} dia{{ $dueSoonDays !== 1 ? 's' : '' }} para concluir este treinamento obrigatório.
                @endif
            </p>
            <p class="text-xs mt-0.5 opacity-80">Conclua o vídeo e siga as etapas abaixo para registrar sua conclusão.</p>
        </div>
    </div>
@endif

<div
    class="max-w-5xl mx-auto"
    x-data="{
        progress: {{ $view->progress_percent }},
        onVideoProgress(e) {
            if (e.detail.percent > this.progress) this.progress = e.detail.percent;
        },
        get canComplete() { return this.progress >= 90; },
        get progressLeft() { return Math.max(0, 90 - this.progress); }
    }"
    @video-progress.window="onVideoProgress($event)"
>

    {{-- Voltar + título --}}
    <div class="mb-5">
        <a href="{{ route('employee.trainings.index') }}"
           class="inline-flex items-center gap-1 text-sm text-gray-400 hover:text-gray-600 transition mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Meus Treinamentos
        </a>
        <div class="flex items-center gap-2 flex-wrap mb-1">
            @if($isMandatory)
                <span class="inline-flex items-center gap-1 text-xs font-semibold bg-red-100 text-red-700 rounded-full px-2.5 py-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Obrigatório
                </span>
            @endif
            @if($effectiveDue)
                <span class="inline-flex items-center gap-1 text-xs font-medium rounded-full px-2.5 py-1
                    {{ $dueOverdue ? 'bg-red-50 text-red-600 border border-red-200' : ($dueSoon ? 'bg-yellow-50 text-yellow-700 border border-yellow-200' : 'bg-gray-50 text-gray-500 border border-gray-200') }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Prazo: {{ $effectiveDue->format('d/m/Y') }}
                    @if($dueOverdue) — vencido
                    @elseif($dueSoon) — {{ $dueSoonDays }}d
                    @endif
                </span>
            @endif
        </div>
        <h1 class="text-xl font-bold text-gray-900">{{ $training->title }}</h1>
        @if($training->description)
            <p class="text-sm text-gray-500 mt-1 leading-relaxed">{{ $training->description }}</p>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Coluna principal: vídeo + progresso + ação --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Player --}}
            <div class="bg-black rounded-xl overflow-hidden shadow-sm">
                <x-ui.video-player
                    :videoUrl="$training->video_url"
                    :provider="$training->video_provider"
                    :trainingId="$training->id"
                    :initialProgress="$view->progress_percent"
                />
            </div>

            {{-- Barra de progresso com marcador de 90% --}}
            @if(!$isCompleted)
                <div class="bg-white rounded-xl shadow-sm p-5">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Progresso do vídeo</span>
                        <span class="text-sm font-bold text-gray-800" x-text="progress + '%'"></span>
                    </div>

                    {{-- Barra com marcador --}}
                    <div class="relative">
                        <div class="w-full bg-gray-100 rounded-full h-4 overflow-visible">
                            <div class="h-4 rounded-full transition-all duration-500"
                                 :class="progress >= 90 ? 'bg-green-500' : 'bg-blue-500'"
                                 :style="'width: ' + progress + '%'"></div>
                        </div>
                        {{-- Marcador de 90% --}}
                        <div class="absolute top-0 bottom-0 flex flex-col items-center" style="left: 90%">
                            <div class="w-0.5 h-4 bg-orange-400"></div>
                            <span class="text-xs text-orange-500 font-semibold mt-1 -translate-x-1/2 whitespace-nowrap">90%</span>
                        </div>
                    </div>

                    {{-- Feedback dinâmico --}}
                    <div class="mt-3">
                        <template x-if="progress >= 90">
                            <p class="text-sm text-green-600 font-medium flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                Você assistiu o suficiente — pode concluir o treinamento abaixo.
                            </p>
                        </template>
                        <template x-if="progress > 0 && progress < 90">
                            <p class="text-sm text-gray-500 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Continue assistindo — faltam <strong x-text="progressLeft + '%'"></strong> para liberar a conclusão.
                            </p>
                        </template>
                        <template x-if="progress === 0">
                            <p class="text-sm text-gray-400 flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M4.5 5.653c0-1.426 1.529-2.33 2.779-1.643l11.54 6.348c1.295.712 1.295 2.573 0 3.285L7.28 19.991c-1.25.687-2.779-.217-2.779-1.643V5.653z" clip-rule="evenodd"/></svg>
                                Inicie o vídeo para começar — o progresso é salvo automaticamente.
                            </p>
                        </template>
                    </div>
                </div>
            @endif

            {{-- Card de ação principal --}}
            <div class="bg-white rounded-xl shadow-sm p-5 space-y-3">

                @if(!$isCompleted)
                    {{-- Botão travado --}}
                    <template x-if="!canComplete">
                        <div class="w-full flex items-center gap-3 bg-gray-100 text-gray-400 px-5 py-3.5 rounded-xl cursor-not-allowed select-none">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-semibold">Marcar como Concluído</p>
                                <p class="text-xs mt-0.5">Assista <span x-text="progressLeft + '%'"></span> a mais para desbloquear</p>
                            </div>
                        </div>
                    </template>

                    {{-- Botão desbloqueado --}}
                    <template x-if="canComplete">
                        <form method="POST" action="{{ route('employee.trainings.complete', $training) }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-3.5 rounded-xl transition shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                Marcar como Concluído
                            </button>
                        </form>
                    </template>
                @endif

                @if($isCompleted)
                    {{-- Concluído --}}
                    <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl px-4 py-3">
                        <div class="w-9 h-9 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-green-800">Treinamento concluído!</p>
                            <p class="text-xs text-green-600">Concluído em {{ $view->completed_at?->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    @if($training->has_quiz && !$quizPassed)
                        {{-- Quiz pendente --}}
                        <a href="{{ route('employee.quiz.show', $training) }}"
                           class="flex items-center justify-center gap-2 bg-primary hover:bg-secondary text-white font-semibold px-5 py-3.5 rounded-xl transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            Fazer Quiz — {{ $training->passing_score ?? 70 }}% para aprovação
                        </a>
                    @endif

                    @if($quizPassed)
                        <div class="flex items-center gap-3 bg-blue-50 border border-blue-200 rounded-xl px-4 py-3">
                            <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-secondary">Quiz aprovado!</p>
                                <p class="text-xs text-primary">Você atingiu a pontuação mínima.</p>
                            </div>
                        </div>
                    @endif

                    @if($canGenerateCertificate && !$existingCertificate)
                        <form method="POST" action="{{ route('employee.certificates.generate', $training) }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center justify-center gap-2 bg-primary hover:bg-primary-dark text-white font-semibold px-5 py-3.5 rounded-xl transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                </svg>
                                Gerar Certificado
                            </button>
                        </form>
                    @endif

                    @if($existingCertificate)
                        <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-xl px-4 py-3">
                            <div class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-700">Certificado emitido</p>
                                <p class="text-xs text-gray-400">{{ $existingCertificate->generated_at->format('d/m/Y') }}</p>
                            </div>
                            <a href="{{ route('employee.certificates.download', $existingCertificate) }}"
                               class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary hover:text-secondary transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Baixar
                            </a>
                        </div>
                    @endif
                @endif

            </div>

        </div>

        {{-- Sidebar: etapas + info --}}
        <div class="space-y-4">

            {{-- Stepper de etapas --}}
            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Sua jornada</p>
                <ol class="space-y-0">

                    {{-- Etapa 1: Assistir --}}
                    @php $s1 = $step1Done ? 'done' : ($view->progress_percent > 0 ? 'active' : 'pending'); @endphp
                    <li class="flex gap-3 pb-4 relative">
                        <div class="flex flex-col items-center">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 z-10
                                {{ $s1 === 'done' ? 'bg-green-500' : ($s1 === 'active' ? 'bg-blue-500' : 'bg-gray-200') }}">
                                @if($s1 === 'done')
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                @elseif($s1 === 'active')
                                    <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M4.5 5.653c0-1.426 1.529-2.33 2.779-1.643l11.54 6.348c1.295.712 1.295 2.573 0 3.285L7.28 19.991c-1.25.687-2.779-.217-2.779-1.643V5.653z" clip-rule="evenodd"/></svg>
                                @else
                                    <span class="text-xs font-bold text-gray-400">1</span>
                                @endif
                            </div>
                            <div class="w-0.5 flex-1 mt-1 {{ $s1 === 'done' ? 'bg-green-200' : 'bg-gray-100' }}"></div>
                        </div>
                        <div class="pb-2 min-w-0">
                            <p class="text-sm font-semibold {{ $s1 === 'done' ? 'text-green-700' : ($s1 === 'active' ? 'text-blue-700' : 'text-gray-400') }}">
                                Assistir o vídeo
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5" x-text="'Progresso: ' + progress + '% de 90% necessários'"></p>
                        </div>
                    </li>

                    {{-- Etapa 2: Concluir --}}
                    @php $s2 = $step2Done ? 'done' : ($step1Done ? 'active' : 'pending'); @endphp
                    <li class="flex gap-3 pb-4 relative">
                        <div class="flex flex-col items-center">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 z-10
                                {{ $s2 === 'done' ? 'bg-green-500' : ($s2 === 'active' ? 'bg-blue-500' : 'bg-gray-200') }}">
                                @if($s2 === 'done')
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                @elseif($s2 === 'active')
                                    <span class="text-xs font-bold text-white">2</span>
                                @else
                                    <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                @endif
                            </div>
                            @if($training->has_quiz || true)
                                <div class="w-0.5 flex-1 mt-1 {{ $s2 === 'done' ? 'bg-green-200' : 'bg-gray-100' }}"></div>
                            @endif
                        </div>
                        <div class="pb-2">
                            <p class="text-sm font-semibold {{ $s2 === 'done' ? 'text-green-700' : ($s2 === 'active' ? 'text-blue-700' : 'text-gray-400') }}">
                                Confirmar conclusão
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                @if($s2 === 'done') Concluído em {{ $view->completed_at?->format('d/m/Y') }}
                                @elseif($s2 === 'active') Clique no botão abaixo
                                @else Desbloqueado após 90% do vídeo
                                @endif
                            </p>
                        </div>
                    </li>

                    @if($training->has_quiz)
                        {{-- Etapa 3: Quiz --}}
                        @php $s3 = $quizPassed ? 'done' : ($isCompleted ? 'active' : 'pending'); @endphp
                        <li class="flex gap-3 pb-4 relative">
                            <div class="flex flex-col items-center">
                                <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 z-10
                                    {{ $s3 === 'done' ? 'bg-green-500' : ($s3 === 'active' ? 'bg-blue-500' : 'bg-gray-200') }}">
                                    @if($s3 === 'done')
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    @elseif($s3 === 'active')
                                        <span class="text-xs font-bold text-white">3</span>
                                    @else
                                        <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    @endif
                                </div>
                                <div class="w-0.5 flex-1 mt-1 {{ $s3 === 'done' ? 'bg-green-200' : 'bg-gray-100' }}"></div>
                            </div>
                            <div class="pb-2">
                                <p class="text-sm font-semibold {{ $s3 === 'done' ? 'text-green-700' : ($s3 === 'active' ? 'text-blue-700' : 'text-gray-400') }}">
                                    Passar no quiz
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    @if($s3 === 'done') Aprovado
                                    @elseif($s3 === 'active') Mínimo {{ $training->passing_score ?? 70 }}%
                                    @else Disponível após concluir
                                    @endif
                                </p>
                            </div>
                        </li>
                    @endif

                    {{-- Última etapa: Certificado --}}
                    @php
                        $sN = $certDone ? 'done' : ($canGenerateCertificate ? 'active' : 'pending');
                        $stepN = 2 + ($training->has_quiz ? 1 : 0) + 1;
                    @endphp
                    <li class="flex gap-3">
                        <div class="flex flex-col items-center">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0
                                {{ $sN === 'done' ? 'bg-green-500' : ($sN === 'active' ? 'bg-blue-500' : 'bg-gray-200') }}">
                                @if($sN === 'done')
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                @elseif($sN === 'active')
                                    <span class="text-xs font-bold text-white">{{ $stepN }}</span>
                                @else
                                    <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                @endif
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-semibold {{ $sN === 'done' ? 'text-green-700' : ($sN === 'active' ? 'text-blue-700' : 'text-gray-400') }}">
                                Obter certificado
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                @if($sN === 'done') Emitido em {{ $existingCertificate->generated_at->format('d/m/Y') }}
                                @elseif($sN === 'active') Disponível para gerar
                                @else Disponível ao final
                                @endif
                            </p>
                        </div>
                    </li>

                </ol>
            </div>

            {{-- Info do treinamento --}}
            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Detalhes</p>
                <dl class="space-y-2.5 text-sm">
                    <div class="flex items-center justify-between">
                        <dt class="text-gray-500">Duração</dt>
                        <dd class="font-medium text-gray-800">{{ $training->duration_minutes }} min</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-gray-500">Provedor</dt>
                        <dd class="font-medium text-gray-800">{{ ucfirst($training->video_provider) }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-gray-500">Quiz</dt>
                        <dd class="font-medium text-gray-800">
                            @if($training->has_quiz) Sim — mín. {{ $training->passing_score ?? 70 }}%
                            @else Não
                            @endif
                        </dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-gray-500">Obrigatoriedade</dt>
                        <dd>
                            @if($isMandatory)
                                <span class="text-xs font-semibold bg-red-100 text-red-700 rounded-full px-2 py-0.5">Obrigatório</span>
                            @else
                                <span class="text-xs font-medium text-gray-400">Opcional</span>
                            @endif
                        </dd>
                    </div>
                    @if($effectiveDue)
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-500">Prazo</dt>
                            <dd class="font-medium {{ $dueOverdue ? 'text-red-600' : ($dueSoon ? 'text-yellow-600' : 'text-gray-800') }}">
                                {{ $effectiveDue->format('d/m/Y') }}
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>

        </div>

    </div>

</div>

</x-layout.app>
