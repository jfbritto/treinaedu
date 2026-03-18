<x-layout.app title="Meus Certificados">

    @if($certificates->isEmpty())
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
            </svg>
            <p class="text-gray-400 text-sm font-medium">Você ainda não possui certificados.</p>
            <p class="text-gray-300 text-xs mt-1">Conclua um treinamento para gerar seu primeiro certificado.</p>
            <a href="{{ route('employee.trainings.index') }}" class="inline-block mt-4 text-sm text-blue-600 hover:underline">Ver meus treinamentos →</a>
        </div>
    @else
        {{-- Resumo --}}
        <div class="bg-white rounded-xl shadow-sm p-5 mb-6 flex items-center gap-5">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
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
                    <div class="h-2 bg-gradient-to-r from-blue-500 to-blue-400"></div>

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
                                class="flex-1 flex items-center justify-center gap-1.5 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 transition rounded-lg px-3 py-2">
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
