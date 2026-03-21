<x-layout.app title="Certificado Gerado">

    <div class="max-w-2xl mx-auto">
        {{-- Success Card --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            {{-- Success Header --}}
            <div class="bg-gradient-to-r p-8 text-center" style="background: linear-gradient(135deg, var(--primary), var(--secondary));">
                <div class="text-5xl mb-4">🎉</div>
                <h1 class="text-3xl font-bold text-white mb-2">Certificado Gerado!</h1>
                <p class="text-white/90">Seu certificado de conclusão está pronto</p>
            </div>

            {{-- Certificate Info --}}
            <div class="p-8 space-y-6">
                <div class="bg-gray-50 rounded-xl p-6">
                    <h2 class="font-semibold text-gray-900 mb-4">{{ $certificate->training->title }}</h2>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Concluinte:</dt>
                            <dd class="font-medium text-gray-900">{{ $certificate->user->name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Empresa:</dt>
                            <dd class="font-medium text-gray-900">{{ $certificate->company->name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Data:</dt>
                            <dd class="font-medium text-gray-900">{{ $certificate->generated_at->format('d/m/Y') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Código:</dt>
                            <dd class="font-mono text-xs text-gray-900">{{ $certificate->certificate_code }}</dd>
                        </div>
                    </dl>
                </div>

                {{-- Action Buttons --}}
                <div class="space-y-3">
                    <a href="{{ route('employee.certificates.show', $certificate) }}"
                       target="_blank"
                       class="w-full flex items-center justify-center gap-2 px-6 py-3 rounded-lg text-white font-semibold transition"
                       style="background-color: var(--primary)">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Visualizar Certificado
                    </a>

                    <a href="{{ route('employee.certificates.download', $certificate) }}"
                       class="w-full flex items-center justify-center gap-2 px-6 py-3 rounded-lg text-gray-700 font-semibold bg-gray-100 hover:bg-gray-200 transition"
                       download>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Baixar PDF
                    </a>

                    <a href="{{ route('employee.trainings.index') }}"
                       class="w-full flex items-center justify-center gap-2 px-6 py-3 rounded-lg text-gray-600 font-semibold bg-white border border-gray-200 hover:bg-gray-50 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Voltar aos Treinamentos
                    </a>
                </div>

                {{-- Tip --}}
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-800">
                    <p class="font-medium mb-1">💡 Dica</p>
                    <p>Clique em "Visualizar Certificado" para compartilhar no LinkedIn ou validar a autenticidade do seu certificado.</p>
                </div>
            </div>
        </div>
    </div>

</x-layout.app>
