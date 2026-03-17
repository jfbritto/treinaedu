<x-layout.app>
    <div class="max-w-6xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Certificados Emitidos</h1>

        @if($certificates->isEmpty())
            <div class="bg-white shadow rounded-lg p-8 text-center text-gray-500">
                Nenhum certificado emitido ainda.
            </div>
        @else
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Funcionário</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Treinamento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($certificates as $certificate)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $certificate->user->name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ $certificate->training->title }}
                                </td>
                                <td class="px-6 py-4 text-sm font-mono text-gray-600">
                                    {{ $certificate->certificate_code }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $certificate->generated_at->format('d/m/Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $certificates->links() }}
            </div>
        @endif
    </div>
</x-layout.app>
