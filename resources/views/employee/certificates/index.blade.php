<x-layout.app title="Meus Certificados">

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
                <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M5.166 2.621v.858c-1.035.148-2.059.33-3.071.543a.75.75 0 00-.584.859 6.753 6.753 0 006.138 5.6 6.73 6.73 0 002.743 1.346A6.707 6.707 0 019.279 15H8.54c-1.036 0-1.875.84-1.875 1.875V19.5h-.002A2.627 2.627 0 009.29 22.124c.262.02.526.03.79.037V22.5h3.84v-.339a18.353 18.353 0 00.79-.037 2.627 2.627 0 002.627-2.624h-.002v-2.625c0-1.036-.84-1.875-1.875-1.875h-.74a6.707 6.707 0 00-1.112-3.173 6.73 6.73 0 002.743-1.347 6.753 6.753 0 006.139-5.6.75.75 0 00-.585-.858 47.077 47.077 0 00-3.07-.543V2.62a.75.75 0 00-.658-.744 49.22 49.22 0 00-6.093-.377c-2.063 0-4.096.128-6.093.377a.75.75 0 00-.657.744z" clip-rule="evenodd"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $certificates->total() }}</p>
                <p class="text-sm text-gray-400">Certificado{{ $certificates->total() !== 1 ? 's' : '' }} emitido{{ $certificates->total() !== 1 ? 's' : '' }}</p>
            </div>
            <p class="ml-auto text-xs text-gray-400 hidden sm:block">
                Compartilhe nas redes profissionais ou baixe o PDF para guardar.
            </p>
        </div>

        {{-- Grid de certificados --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 mb-6">
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
                            <a href="{{ route('certificate.show', $certificate->certificate_code) }}"
                                target="_blank" rel="noopener"
                                class="flex-1 flex items-center justify-center gap-1.5 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 transition rounded-lg px-3 py-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                </svg>
                                Ver e Compartilhar
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{ $certificates->links() }}
    @endif

</x-layout.app>
