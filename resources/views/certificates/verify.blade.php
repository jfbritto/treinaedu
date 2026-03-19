<x-layout.guest>
    <div class="max-w-xl mx-auto mt-16 px-4">
        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Verificar Certificado</h1>

        <form method="POST" action="{{ route('certificate.verify.post') }}" class="bg-white shadow rounded-lg p-6 mb-6">
            @csrf
            <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Código do Certificado</label>
            <div class="flex gap-2">
                <input
                    type="text"
                    id="code"
                    name="code"
                    value="{{ old('code') }}"
                    placeholder="Ex: TH-2024-ABCD-EFGH"
                    class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                    maxlength="20"
                >
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                    Verificar
                </button>
            </div>
            @error('code')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </form>

        @if(isset($certificate) && $certificate)
            <div class="bg-green-50 border border-green-300 rounded-lg p-6">
                <div class="flex items-center gap-2 mb-5">
                    <svg class="w-6 h-6 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h2 class="text-lg font-semibold text-green-800">Certificado Válido</h2>
                </div>
                <dl class="space-y-3 text-sm text-gray-700">
                    <div class="flex justify-between gap-4">
                        <dt class="font-medium text-gray-500">Funcionário</dt>
                        <dd class="font-medium text-gray-800">{{ $certificate->user->name }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="font-medium text-gray-500">Treinamento</dt>
                        <dd class="text-gray-800">{{ $certificate->training->title }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="font-medium text-gray-500">Empresa</dt>
                        <dd class="text-gray-800">{{ $certificate->company->name }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="font-medium text-gray-500">Data de emissão</dt>
                        <dd class="text-gray-800">{{ $certificate->generated_at->format('d/m/Y') }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 pt-2 border-t border-green-200">
                        <dt class="font-medium text-gray-500">Código</dt>
                        <dd class="font-mono text-xs text-gray-600">{{ $certificate->certificate_code }}</dd>
                    </div>
                </dl>
                <div class="mt-4 pt-4 border-t border-green-200">
                    <a href="{{ route('certificate.show', $certificate->certificate_code) }}"
                        class="inline-flex items-center gap-2 text-sm text-blue-600 hover:underline font-medium">
                        Ver certificado completo →
                    </a>
                </div>
            </div>
        @elseif(isset($certificate) && $certificate === null)
            <div class="bg-red-50 border border-red-300 rounded-lg p-4 text-center text-red-700">
                Certificado não encontrado. Verifique o código e tente novamente.
            </div>
        @endif
    </div>
</x-layout.guest>
