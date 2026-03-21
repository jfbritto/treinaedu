<x-layout.app title="Meus Certificados">

    @php
        $totalCerts = $certificates->total();
        $monthlyData = $certificates->groupBy(fn($c) => $c->generated_at->format('m/Y'))->map->count();
    @endphp

    {{-- Abas --}}
    <div class="bg-white rounded-xl shadow-sm mb-6 border-b border-gray-100" x-data="{ tab: 'cards' }">
        <div class="flex border-b border-gray-100">
            <button @click="tab = 'cards'"
                :class="tab === 'cards' ? 'border-b-2 text-primary' : 'text-gray-600'"
                class="px-6 py-4 text-sm font-medium hover:text-primary transition">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/>
                    </svg>
                    Meus Certificados
                </span>
            </button>
            <button @click="tab = 'timeline'"
                :class="tab === 'timeline' ? 'border-b-2 text-primary' : 'text-gray-600'"
                class="px-6 py-4 text-sm font-medium hover:text-primary transition">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Histórico
                </span>
            </button>
            <button @click="tab = 'stats'"
                :class="tab === 'stats' ? 'border-b-2 text-primary' : 'text-gray-600'"
                class="px-6 py-4 text-sm font-medium hover:text-primary transition">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Estatísticas
                </span>
            </button>
        </div>
    </div>

    @if($certificates->isEmpty())
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
            </svg>
            <p class="text-gray-400 text-sm font-medium">Você ainda não possui certificados.</p>
            <p class="text-gray-300 text-xs mt-1">Conclua um treinamento para gerar seu primeiro certificado.</p>
            <a href="{{ route('employee.trainings.index') }}" class="inline-block mt-4 text-sm text-primary hover:underline">Ver meus treinamentos →</a>
        </div>
    @else
        {{-- Resumo --}}
        <div class="bg-white rounded-xl shadow-sm p-5 mb-6 flex items-center gap-5">
            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005z" clip-rule="evenodd"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $totalCerts }}</p>
                <p class="text-sm text-gray-400">Certificado{{ $totalCerts !== 1 ? 's' : '' }} emitido{{ $totalCerts !== 1 ? 's' : '' }}</p>
            </div>
            <p class="ml-auto text-xs text-gray-400 hidden sm:block">
                Compartilhe nas redes profissionais ou baixe o PDF para guardar.
            </p>
        </div>

        {{-- Aba: Meus Certificados (Grid) --}}
        <div x-show="tab === 'cards'" x-transition class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 mb-6">
            @foreach($certificates as $certificate)
                <div class="bg-white rounded-xl shadow-sm overflow-hidden flex flex-col hover:shadow-md transition-shadow">
                    {{-- Topo decorativo --}}
                    <div class="h-2" style="background: linear-gradient(to right, var(--secondary), var(--primary))"></div>

                    <div class="p-5 flex-1 flex flex-col gap-4">
                        {{-- Título do treinamento --}}
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Treinamento</p>
                            <h3 class="font-semibold text-gray-800 leading-snug">{{ $certificate->training->title }}</h3>
                        </div>

                        {{-- Detalhes --}}
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div>
                                <p class="text-gray-400 mb-0.5">Data de emissão</p>
                                <p class="font-medium text-gray-700">{{ $certificate->generated_at->format('d/m/Y') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-400 mb-0.5">Código</p>
                                <p class="font-mono text-gray-600 truncate">{{ $certificate->certificate_code }}</p>
                            </div>
                        </div>

                        {{-- Ações --}}
                        <div class="flex items-center gap-2 mt-auto pt-2 border-t border-gray-100">
                            <a href="{{ route('employee.certificates.download', $certificate) }}"
                                class="flex-1 flex items-center justify-center gap-1.5 text-xs font-medium text-white bg-primary hover:bg-secondary transition rounded-lg px-3 py-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Baixar PDF
                            </a>
                            <a href="{{ route('employee.certificates.show', $certificate) }}"
                                class="flex-1 flex items-center justify-center gap-1.5 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 transition rounded-lg px-3 py-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Ver e Compartilhar
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Aba: Histórico (Timeline) --}}
        <div x-show="tab === 'timeline'" x-transition class="space-y-3">
            @foreach($certificates as $cert)
                <div class="bg-white rounded-xl shadow-sm p-5 flex items-start gap-4 hover:shadow-md transition">
                    <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h4 class="font-semibold text-gray-800">{{ $cert->training->title }}</h4>
                                <p class="text-sm text-gray-400 mt-1">{{ $cert->company->name }}</p>
                            </div>
                            <span class="text-xs font-medium text-gray-500 flex-shrink-0">
                                {{ $cert->generated_at->format('d/m/Y') }}
                            </span>
                        </div>
                        <div class="mt-3 flex items-center gap-2 text-xs text-gray-500">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            Código: <span class="font-mono text-gray-600">{{ $cert->certificate_code }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <a href="{{ route('employee.certificates.download', $cert) }}" class="text-xs text-primary hover:underline">Baixar</a>
                        <span class="text-gray-200">·</span>
                        <a href="{{ route('employee.certificates.show', $cert) }}" class="text-xs text-primary hover:underline">Ver</a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Aba: Estatísticas (Gráfico) --}}
        <div x-show="tab === 'stats'" x-transition>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-6">Evolução dos Certificados</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <div class="bg-primary/5 rounded-lg p-4">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Total</p>
                        <p class="text-2xl font-bold text-primary">{{ $totalCerts }}</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Este Mês</p>
                        <p class="text-2xl font-bold text-green-600">
                            {{ $certificates->filter(fn($c) => $c->generated_at->isCurrentMonth())->count() }}
                        </p>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Últimos 30 dias</p>
                        <p class="text-2xl font-bold text-blue-600">
                            {{ $certificates->filter(fn($c) => $c->generated_at->diffInDays(now()) <= 30)->count() }}
                        </p>
                    </div>
                </div>

                <div class="space-y-4">
                    <h4 class="font-medium text-gray-700">Certificados por Treinamento</h4>
                    @php
                        $byTraining = $certificates->groupBy('training.id')->map(fn($group) => [
                            'training' => $group->first()->training->title,
                            'count' => $group->count()
                        ])->sortByDesc('count')->take(5);
                    @endphp
                    @foreach($byTraining as $item)
                        <div class="flex items-center gap-4">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-700 truncate">{{ $item['training'] }}</p>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <div class="w-40 bg-gray-100 rounded-full h-2">
                                    <div class="h-2 rounded-full"
                                        style="width: {{ ($item['count'] / $byTraining->max('count')) * 100 }}%; background-color: var(--primary)"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-600 w-8 text-right">{{ $item['count'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{ $certificates->links() }}
    @endif

</x-layout.app>
