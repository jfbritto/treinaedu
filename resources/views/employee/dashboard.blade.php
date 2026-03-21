<x-layout.app title="Dashboard">

    @php
        $total = $pending->count() + $completed->count();
        $pct   = $total > 0 ? round(($completed->count() / $total) * 100) : 0;
        $user  = auth()->user();
    @endphp

    {{-- Boas-vindas + progresso geral --}}
    <div class="rounded-xl p-6 mb-6 text-white" style="background: linear-gradient(to right, var(--secondary), var(--primary))">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="flex-1">
                <h2 class="text-xl font-bold mb-0.5">Olá, {{ explode(' ', $user->name)[0] }}! 👋</h2>
                <p class="text-white/70 text-sm">
                    @if($pct === 100)
                        Parabéns! Você concluiu todos os treinamentos.
                    @elseif($pct > 50)
                        Você já passou da metade. Continue assim!
                    @elseif($completed->count() > 0)
                        Bom começo! Continue avançando nos treinamentos.
                    @else
                        Comece pelo primeiro treinamento abaixo.
                    @endif
                </p>
                <div class="mt-3">
                    <div class="flex justify-between text-xs text-white/60 mb-1">
                        <span>Progresso geral</span>
                        <span class="font-semibold text-white">{{ $pct }}%</span>
                    </div>
                    <div class="w-full rounded-full h-2" style="background-color: rgba(255,255,255,0.25)">
                        <div class="bg-white h-2 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                    </div>
                    <p class="text-xs text-white/60 mt-1.5">{{ $completed->count() }} de {{ $total }} treinamento{{ $total !== 1 ? 's' : '' }} concluído{{ $completed->count() !== 1 ? 's' : '' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zM12.75 6a.75.75 0 00-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 000-1.5h-3.75V6z" clip-rule="evenodd"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $pending->count() }}</p>
                <p class="text-xs text-gray-400">Pendente{{ $pending->count() !== 1 ? 's' : '' }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $completed->count() }}</p>
                <p class="text-xs text-gray-400">Concluído{{ $completed->count() !== 1 ? 's' : '' }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005z" clip-rule="evenodd"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $certificates->count() }}</p>
                <p class="text-xs text-gray-400">Certificado{{ $certificates->count() !== 1 ? 's' : '' }}</p>
            </div>
        </div>
    </div>

    {{-- Conteúdo em grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Treinamentos pendentes (2/3) --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-yellow-400"></div>
                    <h3 class="text-sm font-semibold text-gray-700">Treinamentos Pendentes</h3>
                </div>
                <a href="{{ route('employee.trainings.index') }}" class="text-xs text-primary hover:underline">Ver todos →</a>
            </div>

            @if($pending->isEmpty())
                <div class="p-8 text-center">
                    <svg class="w-10 h-10 text-gray-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm text-gray-400">Nenhum treinamento pendente. Parabéns!</p>
                </div>
            @else
                <div class="divide-y divide-gray-50">
                    @foreach($pending as $training)
                        @php
                            $view      = $training->views->first();
                            $progress  = $view?->progress_percent ?? 0;
                            $started   = $progress > 0;
                            $dueDate   = $training->effective_due_date;
                            $overdue   = $dueDate && $dueDate->isPast();
                            $soonDays  = $dueDate ? (int) now()->diffInDays($dueDate, false) : null;
                            $dueSoon   = $soonDays !== null && $soonDays >= 0 && $soonDays <= 7;
                        @endphp
                        <a href="{{ route('employee.trainings.show', $training) }}"
                            class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 transition group
                                {{ $overdue ? 'border-l-2 border-red-400' : ($training->is_mandatory ? 'border-l-2 border-red-200' : '') }}">
                            {{-- Ícone --}}
                            <div class="w-10 h-10 rounded-lg flex-shrink-0 flex items-center justify-center
                                {{ $started ? 'bg-primary/10' : 'bg-gray-50' }}">
                                <svg class="w-6 h-6 {{ $started ? 'text-primary' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M4.5 5.653c0-1.426 1.529-2.33 2.779-1.643l11.54 6.348c1.295.712 1.295 2.573 0 3.285L7.28 19.991c-1.25.687-2.779-.217-2.779-1.643V5.653z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="text-sm font-medium text-gray-800 group-hover:text-primary transition truncate">
                                        {{ $training->title }}
                                    </p>
                                    @if($training->is_mandatory)
                                        <span class="flex-shrink-0 text-xs font-medium bg-red-100 text-red-700 rounded-full px-1.5 py-0.5">Obrigatório</span>
                                    @endif
                                </div>
                                @if($started)
                                    <div class="flex items-center gap-2 mt-1.5">
                                        <div class="flex-1 bg-gray-100 rounded-full h-1.5">
                                            <div class="h-1.5 rounded-full" style="width: {{ $progress }}%; background-color: var(--secondary)"></div>
                                        </div>
                                        <span class="text-xs text-gray-400 flex-shrink-0">{{ $progress }}%</span>
                                    </div>
                                    @if($training->total_lessons > 0)
                                        <p class="text-xs text-gray-400 mt-1">
                                            📊 {{ $training->completed_lessons }}/{{ $training->total_lessons }} aula{{ $training->total_lessons !== 1 ? 's' : '' }} concluída{{ $training->completed_lessons !== 1 ? 's' : '' }}
                                        </p>
                                    @endif
                                @else
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        Não iniciado
                                        @if($training->total_lessons > 0)
                                            · {{ $training->completed_lessons }}/{{ $training->total_lessons }} aula{{ $training->total_lessons !== 1 ? 's' : '' }}
                                        @else
                                            · {{ $training->duration_minutes }} min
                                        @endif
                                    </p>
                                @endif
                                @if($dueDate)
                                    <p class="text-xs mt-0.5 {{ $overdue ? 'text-red-500 font-medium' : ($dueSoon ? 'text-yellow-600' : 'text-gray-400') }}">
                                        Prazo: {{ $dueDate->format('d/m/Y') }}
                                        @if($overdue) (vencido)
                                        @elseif($dueSoon) ({{ $soonDays }}d)
                                        @endif
                                    </p>
                                @endif
                            </div>
                            {{-- CTA --}}
                            <span class="text-xs font-medium text-primary flex-shrink-0 opacity-0 group-hover:opacity-100 transition">
                                {{ $started ? 'Continuar' : 'Iniciar' }} →
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Coluna direita --}}
        <div class="space-y-6">

            {{-- Certificados recentes --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-primary"></div>
                        <h3 class="text-sm font-semibold text-gray-700">Certificados</h3>
                    </div>
                    <a href="{{ route('employee.certificates.index') }}" class="text-xs text-primary hover:underline">Ver todos →</a>
                </div>

                @if($certificates->isEmpty())
                    <div class="p-6 text-center">
                        <p class="text-xs text-gray-400">Nenhum certificado emitido ainda.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-50">
                        @foreach($certificates->take(3) as $cert)
                            <div class="px-5 py-3.5 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-medium text-gray-700 truncate">{{ $cert->training->title }}</p>
                                    <p class="text-xs text-gray-400">{{ $cert->generated_at->format('d/m/Y') }}</p>
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <a href="{{ route('employee.certificates.download', $cert) }}"
                                        class="text-xs text-primary hover:underline">Baixar</a>
                                    <span class="text-gray-200">·</span>
                                    <a href="{{ route('certificate.show', $cert->certificate_code) }}"
                                        target="_blank" rel="noopener"
                                        class="text-xs text-gray-400 hover:text-gray-600">Ver</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Treinamentos concluídos recentes --}}
            @if($completed->isNotEmpty())
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-100">
                        <div class="w-2 h-2 rounded-full bg-primary"></div>
                        <h3 class="text-sm font-semibold text-gray-700">Recém Concluídos</h3>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @foreach($completed->take(3) as $training)
                            <div class="px-5 py-3.5 flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-primary flex-shrink-0 ml-1"></div>
                                <p class="text-xs text-gray-700 flex-1 truncate">{{ $training->title }}</p>
                                <svg class="w-3.5 h-3.5 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>

</x-layout.app>
