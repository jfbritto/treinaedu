<x-layout.app title="Importar Usuários">

    <div class="mb-6">
        <a href="{{ route('users.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar para usuários
        </a>
    </div>

    <div class="max-w-2xl space-y-6">

        {{-- Instructions --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Importar Usuários via Planilha</h3>
                    <p class="text-xs text-gray-400">Cadastre vários colaboradores de uma vez</p>
                </div>
            </div>

            <div class="space-y-4">
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-xs font-semibold text-blue-800 mb-2">Como funciona:</p>
                    <ol class="text-xs text-blue-700 space-y-1 list-decimal list-inside">
                        <li>Baixe o modelo de planilha ou prepare a sua</li>
                        <li>Preencha com <strong>nome</strong>, <strong>email</strong> e <strong>cargo</strong> (employee ou instructor)</li>
                        <li>Faça o upload do arquivo CSV</li>
                        <li>Revise os dados antes de confirmar</li>
                        <li>Os convites são enviados automaticamente por e-mail</li>
                    </ol>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-600 mb-2">Formato aceito da planilha:</p>
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="w-full text-xs">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-600">nome</th>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-600">email</th>
                                    <th class="px-4 py-2 text-left font-semibold text-gray-600">cargo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr>
                                    <td class="px-4 py-2 text-gray-500">Maria Silva</td>
                                    <td class="px-4 py-2 text-gray-500">maria@empresa.com</td>
                                    <td class="px-4 py-2 text-gray-500">employee</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2 text-gray-500">João Santos</td>
                                    <td class="px-4 py-2 text-gray-500">joao@empresa.com</td>
                                    <td class="px-4 py-2 text-gray-500">instructor</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">O campo <strong>cargo</strong> é opcional — padrão é "employee" (colaborador).</p>
                </div>

                <a href="{{ route('users.import.template') }}"
                   class="inline-flex items-center gap-2 text-xs font-medium text-primary hover:text-secondary transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Baixar modelo de planilha (.csv)
                </a>
            </div>
        </div>

        {{-- Upload form --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form method="POST" action="{{ route('users.import.preview') }}" enctype="multipart/form-data">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-2">Arquivo da planilha</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary transition cursor-pointer"
                             onclick="document.getElementById('file-input').click()">
                            <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-sm text-gray-500 font-medium" id="file-label">Clique para selecionar o arquivo</p>
                            <p class="text-xs text-gray-400 mt-1">CSV até 2MB</p>
                        </div>
                        <input type="file" name="file" id="file-input" accept=".csv,.txt,.xlsx,.xls" required class="hidden"
                               onchange="document.getElementById('file-label').textContent = this.files[0]?.name || 'Clique para selecionar o arquivo'">
                        @error('file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 bg-primary hover:bg-secondary text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Visualizar dados antes de importar
                    </button>
                </div>
            </form>
        </div>

    </div>

</x-layout.app>
