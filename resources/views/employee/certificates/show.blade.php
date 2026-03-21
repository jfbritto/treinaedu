<x-layout.app title="Certificado de Conclusão">

    <div class="max-w-5xl mx-auto space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Certificado de Conclusão</h1>
                <p class="mt-1 text-gray-600">{{ $certificate->training->title }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('employee.certificates.download', $certificate) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-white transition"
                   style="background-color: var(--primary)"
                   download>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Baixar PDF
                </a>
                <a href="{{ route('employee.certificates.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Voltar
                </a>
            </div>
        </div>

        {{-- Certificado visual --}}
        @include('certificates.show')

    </div>

    <script>
        function copyLink() {
            const url = '{{ route("certificate.verify") }}?code={{ $certificate->certificate_code }}';
            navigator.clipboard.writeText(url).then(() => {
                const label = document.getElementById('copy-label');
                label.textContent = 'Link copiado!';
                setTimeout(() => label.textContent = 'Copiar link', 2000);
            });
        }
    </script>

</x-layout.app>
