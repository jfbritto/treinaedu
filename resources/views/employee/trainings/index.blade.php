<x-layout.app title="Meus Treinamentos">

    @php
        $total = $pending->count() + $completed->count();
        $pct   = $total > 0 ? round(($completed->count() / $total) * 100) : 0;
    @endphp

    {{-- Barra de progresso geral --}}
    <div class="bg-white rounded-xl shadow-sm p-5 mb-6 flex flex-col sm:flex-row sm:items-center gap-5">
        <div class="flex-1">
            <div class="flex items-baseline justify-between mb-2">
                <span class="text-sm text-gray-500">Progresso geral</span>
                <span class="text-sm font-bold text-primary">{{ $pct }}% concluído</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2.5">
                <div class="bg-primary h-2.5 rounded-full transition-all" style="width: {{ $pct }}%"></div>
            </div>
            <p class="text-xs text-gray-400 mt-2">
                {{ $completed->count() }} de {{ $total }} treinamento{{ $total !== 1 ? 's' : '' }} concluído{{ $completed->count() !== 1 ? 's' : '' }}
            </p>
        </div>
        <div class="flex gap-4 sm:flex-col sm:text-right">
            <div>
                <p class="text-xl font-bold text-primary">{{ $pending->count() }}</p>
                <p class="text-xs text-gray-400">Pendente{{ $pending->count() !== 1 ? 's' : '' }}</p>
            </div>
            <div>
                <p class="text-xl font-bold text-green-500">{{ $completed->count() }}</p>
                <p class="text-xs text-gray-400">Concluído{{ $completed->count() !== 1 ? 's' : '' }}</p>
            </div>
        </div>
    </div>

    {{-- Pendentes --}}
    @if($pending->isNotEmpty())
        <div class="mb-8">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3 px-1">
                Pendentes &mdash; {{ $pending->count() }}
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($pending as $training)
                    @php
                        $view      = $training->views->first();
                        $progress  = $view?->progress_percent ?? 0;
                        $started   = $progress > 0;
                        $dueDate   = $training->effective_due_date;
                        $overdue   = $dueDate && $dueDate->isPast();
                        $soonDays  = $dueDate ? (int) now()->diffInDays($dueDate, false) : null;
                        $dueSoon   = $soonDays !== null && $soonDays >= 0 && $soonDays <= 7;
                        $borderCls = $overdue
                            ? 'border-red-500'
                            : ($training->is_mandatory ? 'border-red-300' : '');
                    @endphp
                    <a href="{{ route('employee.trainings.show', $training) }}"
                        class="group bg-white rounded-xl shadow-sm hover:shadow-md transition-all border-l-4 {{ $borderCls }} flex flex-col overflow-hidden"
                        @if(!$overdue && !$training->is_mandatory) style="border-left-color: var(--secondary)" @endif>

                        {{-- Cabeçalho do card --}}
                        <div class="p-5 flex-1 flex flex-col">
                            <div class="flex items-start gap-3 mb-2">
                                {{-- Ícone play --}}
                                <div class="w-10 h-10 rounded-lg flex-shrink-0 flex items-center justify-center mt-0.5
                                    {{ $started ? 'bg-primary/10' : 'bg-gray-50' }}">
                                    <svg class="w-6 h-6 {{ $started ? 'text-primary' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M4.5 5.653c0-1.426 1.529-2.33 2.779-1.643l11.54 6.348c1.295.712 1.295 2.573 0 3.285L7.28 19.991c-1.25.687-2.779-.217-2.779-1.643V5.653z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2">
                                        <h4 class="font-semibold text-gray-800 leading-snug group-hover:text-primary transition-colors">
                                            {{ $training->title }}
                                        </h4>
                                        @if($started)
                                            <span class="flex-shrink-0 text-xs rounded-full px-2 py-0.5" style="background-color: color-mix(in srgb, var(--secondary) 15%, transparent); color: var(--secondary); border: 1px solid color-mix(in srgb, var(--secondary) 30%, transparent)">Em andamento</span>
                                        @else
                                            <span class="flex-shrink-0 text-xs bg-gray-50 text-gray-400 border border-gray-200 rounded-full px-2 py-0.5">Não iniciado</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Badges: obrigatório + prazo --}}
                            <div class="flex items-center gap-2 flex-wrap mb-2">
                                @if($training->is_mandatory)
                                    <span class="inline-flex items-center gap-1 text-xs font-medium bg-red-100 text-red-700 rounded-full px-2 py-0.5">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Obrigatório
                                    </span>
                                @endif
                                @if($dueDate)
                                    <span class="inline-flex items-center gap-1 text-xs font-medium
                                        {{ $overdue ? 'text-red-600' : ($dueSoon ? 'text-yellow-600' : 'text-gray-500') }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ $dueDate->format('d/m/Y') }}
                                        @if($overdue) <span class="font-semibold">(vencido)</span>
                                        @elseif($dueSoon) <span>({{ $soonDays }}d)</span>
                                        @endif
                                    </span>
                                @endif
                            </div>

                            @if($training->description)
                                <p class="text-xs text-gray-400 leading-relaxed line-clamp-2 mb-3">{{ $training->description }}</p>
                            @endif

                            {{-- Tags --}}
                            <div class="flex items-center gap-2 flex-wrap mt-auto">
                                <span class="inline-flex items-center gap-1 text-xs text-gray-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $training->duration_minutes }} min
                                </span>
                                @if($training->has_quiz)
                                    <span class="inline-flex items-center gap-1 text-xs text-primary">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        Com quiz
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Barra de progresso --}}
                        <div class="px-5 pb-4">
                            <div class="flex justify-between text-xs text-gray-400 mb-1">
                                <span>Progresso</span>
                                <span class="font-medium" {!! $started ? 'style="color: var(--secondary)"' : '' !!}>{{ $progress }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full transition-all" style="width: {{ $progress }}%; background-color: var(--secondary)"></div>
                            </div>
                        </div>

                        {{-- CTA --}}
                        <div class="px-5 pb-4">
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-primary group-hover:underline">
                                {{ $started ? 'Continuar treinamento' : 'Iniciar treinamento' }}
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Concluídos --}}
    @if($completed->isNotEmpty())
        <div>
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3 px-1">
                Concluídos &mdash; {{ $completed->count() }}
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($completed as $training)
                    @php $view = $training->views->first(); @endphp
                    <a href="{{ route('employee.trainings.show', $training) }}"
                        class="group bg-white rounded-xl shadow-sm hover:shadow-md transition-all border-l-4 flex flex-col overflow-hidden"
                        style="border-left-color: var(--primary)">

                        <div class="p-5 flex-1 flex flex-col">
                            <div class="flex items-start justify-between gap-2 mb-3">
                                <h4 class="font-semibold text-gray-800 leading-snug group-hover:text-primary transition-colors">
                                    {{ $training->title }}
                                </h4>
                                <span class="flex-shrink-0 text-xs rounded-full px-2 py-0.5" style="background-color: color-mix(in srgb, var(--primary) 10%, transparent); color: var(--primary); border: 1px solid color-mix(in srgb, var(--primary) 25%, transparent)">Concluído</span>
                            </div>
                            @if($training->description)
                                <p class="text-xs text-gray-400 leading-relaxed line-clamp-2 mb-3">{{ $training->description }}</p>
                            @endif

                            <div class="flex items-center gap-2 flex-wrap mt-auto">
                                <span class="inline-flex items-center gap-1 text-xs text-gray-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $training->duration_minutes }} min
                                </span>
                                @if($training->has_quiz)
                                    <span class="inline-flex items-center gap-1 text-xs text-primary">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        Quiz aprovado
                                    </span>
                                @endif
                                @if($view?->completed_at)
                                    <span class="inline-flex items-center gap-1 text-xs text-gray-400">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ $view->completed_at->format('d/m/Y') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="px-5 pb-4">
                            <div class="w-full bg-gray-100 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full w-full" style="background-color: var(--primary)"></div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Estado vazio total --}}
    @if($pending->isEmpty() && $completed->isEmpty())
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            <p class="text-gray-400 text-sm font-medium">Nenhum treinamento atribuído ainda.</p>
            <p class="text-gray-300 text-xs mt-1">Seu gestor ainda não atribuiu treinamentos para você.</p>
        </div>
    @endif

</x-layout.app>
