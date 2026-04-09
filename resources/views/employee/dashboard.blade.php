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
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Pendente{{ $pending->count() !== 1 ? 's' : '' }}</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $pending->count() }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $pending->count() === 1 ? 'treinamento aguardando' : 'treinamentos aguardando' }}</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Concluído{{ $completed->count() !== 1 ? 's' : '' }}</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $completed->count() }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $completed->count() === 1 ? 'treinamento finalizado' : 'treinamentos finalizados' }}</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Certificado{{ $certificates->count() !== 1 ? 's' : '' }}</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $certificates->count() }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $certificates->count() === 1 ? 'emitido' : 'emitidos' }}</p>
        </div>
    </div>

    {{-- Trilhas --}}
    @if($paths->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-gray-800">Trilhas de Aprendizagem</h3>
                        <p class="text-xs text-gray-400">Jornadas estruturadas atribuídas a você</p>
                    </div>
                    <a href="{{ route('employee.paths.index') }}" class="text-xs font-medium text-primary hover:text-secondary transition">Ver todas →</a>
                </div>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($paths->take(3) as $path)
                    <a href="{{ route('employee.paths.show', $path) }}"
                       class="border border-gray-100 rounded-xl p-4 hover:shadow-md hover:border-primary/20 transition group">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                                 style="background: linear-gradient(135deg, var(--primary), var(--secondary))">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate group-hover:text-primary transition">{{ $path->title }}</p>
                                <p class="text-xs text-gray-400">{{ $path->completed_trainings }}/{{ $path->trainings_count }} treinamentos</p>
                            </div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-400 mb-1">
                            <span>Progresso</span>
                            <span class="font-semibold text-gray-600">{{ $path->progress_percent }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full bg-primary transition-all" style="width: {{ $path->progress_percent }}%"></div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Gráfico de Progresso --}}
    @if($certificates->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-gray-800">Evolução dos Certificados</h3>
                        <p class="text-xs text-gray-400">Histórico de certificados emitidos ao longo do tempo</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div style="height: 250px;">
                    <canvas id="progressChart"></canvas>
                </div>
            </div>
        </div>
    @endif

    {{-- Conteúdo em grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Treinamentos pendentes (2/3) --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-gray-800">Treinamentos Pendentes</h3>
                        <p class="text-xs text-gray-400">Continue de onde parou ou inicie um novo treinamento</p>
                    </div>
                    <a href="{{ route('employee.trainings.index') }}" class="text-xs font-medium text-primary hover:text-secondary transition">Ver todos →</a>
                </div>
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
                                            · {{ $training->calculatedDuration() }} min
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
                <div class="px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-semibold text-gray-800">Certificados</h3>
                            <p class="text-xs text-gray-400">Conquistas recentes</p>
                        </div>
                        <a href="{{ route('employee.certificates.index') }}" class="text-xs font-medium text-primary hover:text-secondary transition">Ver todos →</a>
                    </div>
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
                    <div class="px-6 py-4 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-sm font-semibold text-gray-800">Recém Concluídos</h3>
                                <p class="text-xs text-gray-400">Últimas finalizações</p>
                            </div>
                        </div>
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

    @if($certificates->isNotEmpty())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('progressChart').getContext('2d');

                // Resolve CSS variables to actual hex colors (Chart.js doesn't parse CSS vars)
                const rootStyles = getComputedStyle(document.documentElement);
                const primary = rootStyles.getPropertyValue('--primary').trim() || '#3B82F6';

                // Convert hex → rgba with alpha for the area fill
                const hexToRgba = (hex, alpha) => {
                    const h = hex.replace('#', '');
                    const full = h.length === 3 ? h.split('').map(c => c + c).join('') : h;
                    const r = parseInt(full.substring(0, 2), 16);
                    const g = parseInt(full.substring(2, 4), 16);
                    const b = parseInt(full.substring(4, 6), 16);
                    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
                };

                // Create a vertical gradient for the area fill
                const gradient = ctx.createLinearGradient(0, 0, 0, 250);
                gradient.addColorStop(0, hexToRgba(primary, 0.25));
                gradient.addColorStop(1, hexToRgba(primary, 0));

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($chartData['labels']),
                        datasets: [{
                            label: 'Certificados emitidos',
                            data: @json($chartData['data']),
                            borderColor: primary,
                            backgroundColor: gradient,
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: primary,
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: primary,
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 3,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false,
                            },
                            tooltip: {
                                backgroundColor: '#1f2937',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                padding: 10,
                                cornerRadius: 8,
                                displayColors: false,
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { color: '#9ca3af', font: { size: 11 } },
                                border: { display: false },
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    color: '#9ca3af',
                                    font: { size: 11 },
                                },
                                grid: { color: '#f3f4f6' },
                                border: { display: false },
                            }
                        }
                    }
                });
            });
        </script>
    @endif

</x-layout.app>
