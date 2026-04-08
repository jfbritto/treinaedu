<x-layout.app title="Certificado de Conclusão">

    <div class="max-w-5xl mx-auto">

        {{-- Back link --}}
        <div class="mb-6">
            <a href="{{ route('employee.certificates.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Voltar aos certificados
            </a>
        </div>

        {{-- Hero header --}}
        <div class="rounded-xl p-6 mb-6 text-white relative overflow-hidden"
             style="background: linear-gradient(135deg, var(--primary), var(--secondary))">
            <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-16 -left-16 w-56 h-56 rounded-full bg-white/5"></div>

            <div class="relative flex items-start justify-between gap-4 flex-wrap">
                <div class="flex items-start gap-4 flex-1 min-w-0">
                    <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center flex-shrink-0">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <p class="text-xs text-white/70 uppercase tracking-wider">Certificado de Conclusão</p>
                            <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full bg-white/20 backdrop-blur">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                Válido
                            </span>
                        </div>
                        <h1 class="text-2xl font-bold mb-1 break-words">{{ $certificate->training->title }}</h1>
                        <p class="text-sm text-white/80">Emitido em {{ $certificate->generated_at->format('d/m/Y') }} para {{ $certificate->user->name }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="{{ route('employee.certificates.download', $certificate) }}"
                       class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 backdrop-blur text-white px-4 py-2 rounded-lg text-sm font-semibold transition"
                       download>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Baixar PDF
                    </a>
                </div>
            </div>
        </div>

        {{-- Certificate content (visual + share + details) --}}
        @include('certificates._card')

    </div>

</x-layout.app>
