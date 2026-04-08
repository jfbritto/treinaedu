<x-layout.app title="Meus Certificados">

    @php
        $totalCerts = $certificates->total();
        $thisMonth = $certificates->filter(fn($c) => $c->generated_at->isCurrentMonth())->count();
        $last30Days = $certificates->filter(fn($c) => $c->generated_at->diffInDays(now()) <= 30)->count();
    @endphp

    <p class="text-sm text-gray-500 mb-6">Suas conquistas e certificados emitidos</p>

    @if($certificates->isEmpty())
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
            </div>
            <p class="text-gray-500 text-sm font-medium mb-1">Você ainda não possui certificados</p>
            <p class="text-xs text-gray-400 mb-4">Conclua um treinamento para gerar seu primeiro certificado.</p>
            <a href="{{ route('employee.trainings.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:text-secondary transition">
                Ver meus treinamentos
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    @else
        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Total</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalCerts }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $totalCerts === 1 ? 'certificado emitido' : 'certificados emitidos' }}</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Este Mês</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $thisMonth }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $thisMonth === 1 ? 'conquista no mês' : 'conquistas no mês' }}</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                </div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Últimos 30 dias</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $last30Days }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $last30Days === 1 ? 'certificado recente' : 'certificados recentes' }}</p>
            </div>
        </div>

        {{-- Lista de certificados --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-gray-800">Certificados emitidos</h3>
                        <p class="text-xs text-gray-400">Visualize, baixe ou compartilhe suas conquistas</p>
                    </div>
                </div>
            </div>

            <div class="p-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($certificates as $certificate)
                    <div class="border border-gray-100 rounded-xl overflow-hidden flex flex-col hover:shadow-md hover:border-primary/20 transition">
                        <div class="h-1.5" style="background: linear-gradient(to right, var(--secondary), var(--primary))"></div>

                        <div class="p-5 flex-1 flex flex-col gap-4">
                            <div class="flex items-start gap-3">
                                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                                     style="background: linear-gradient(135deg, var(--primary), var(--secondary))">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">Treinamento</p>
                                    <h3 class="font-semibold text-gray-800 leading-snug line-clamp-2">{{ $certificate->training->title }}</h3>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3 text-xs pb-3 border-b border-gray-100">
                                <div>
                                    <p class="text-gray-400 mb-0.5">Data de emissão</p>
                                    <p class="font-medium text-gray-700">{{ $certificate->generated_at->format('d/m/Y') }}</p>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-gray-400 mb-0.5">Código</p>
                                    <p class="font-mono text-gray-600 truncate">{{ $certificate->certificate_code }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 mt-auto">
                                <a href="{{ route('employee.certificates.download', $certificate) }}"
                                    class="flex-1 flex items-center justify-center gap-1.5 text-xs font-medium text-white bg-primary hover:bg-secondary transition rounded-lg px-3 py-2">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Baixar PDF
                                </a>
                                <a href="{{ route('employee.certificates.show', $certificate) }}"
                                    class="flex-1 flex items-center justify-center gap-1.5 text-xs font-medium text-primary bg-primary/10 hover:bg-primary/20 transition rounded-lg px-3 py-2">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Ver
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        @if($certificates->hasPages())
            <div class="mt-6">
                {{ $certificates->links() }}
            </div>
        @endif
    @endif

</x-layout.app>
