<x-layout.app title="Importar Usuários">

    <div class="mb-6">
        <a href="{{ route('users.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar para usuários
        </a>
    </div>

    <div class="max-w-3xl" x-data="importManager()">

        {{-- Header --}}
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Importar Usuários</h3>
                    <p class="text-xs text-gray-400">Cadastre vários colaboradores de uma vez</p>
                </div>
            </div>

            {{-- Tabs --}}
            <div class="flex gap-1 bg-gray-100 p-1 rounded-lg mb-5">
                <button type="button" @click="mode = 'paste'"
                    :class="mode === 'paste' ? 'bg-white shadow-sm text-gray-800' : 'text-gray-500 hover:text-gray-700'"
                    class="flex-1 text-sm font-medium py-2 rounded-md transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Colar do Excel
                </button>
                <button type="button" @click="mode = 'file'"
                    :class="mode === 'file' ? 'bg-white shadow-sm text-gray-800' : 'text-gray-500 hover:text-gray-700'"
                    class="flex-1 text-sm font-medium py-2 rounded-md transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Upload CSV
                </button>
            </div>

            {{-- Tab: Paste --}}
            <div x-show="mode === 'paste'" x-cloak>
                <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg mb-4">
                    <p class="text-xs text-blue-700"><strong>Dica:</strong> Copie as colunas <strong>Nome</strong> e <strong>Email</strong> do Excel ou Google Sheets e cole na tabela abaixo. Você também pode digitar diretamente.</p>
                </div>

                {{-- Mini spreadsheet --}}
                <div class="border border-gray-200 rounded-lg overflow-hidden mb-4">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="w-8 px-2 py-2 text-xs text-gray-400 font-medium text-center">#</th>
                                <th class="px-2 py-2 text-xs text-gray-600 font-semibold text-left">Nome</th>
                                <th class="px-2 py-2 text-xs text-gray-600 font-semibold text-left">Email</th>
                                <th class="w-10 px-2 py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(row, i) in rows" :key="i">
                                <tr class="border-t border-gray-100">
                                    <td class="px-2 py-1 text-center text-xs text-gray-400" x-text="i + 1"></td>
                                    <td class="px-1 py-1">
                                        <input type="text" x-model="row.name" placeholder="Nome completo"
                                            @paste="handlePaste($event, i)"
                                            class="w-full px-2 py-1.5 text-sm border-0 focus:ring-1 focus:ring-primary rounded bg-transparent hover:bg-gray-50 focus:bg-white">
                                    </td>
                                    <td class="px-1 py-1">
                                        <input type="text" x-model="row.email" placeholder="email@empresa.com"
                                            class="w-full px-2 py-1.5 text-sm border-0 focus:ring-1 focus:ring-primary rounded bg-transparent hover:bg-gray-50 focus:bg-white">
                                    </td>
                                    <td class="px-1 py-1 text-center">
                                        <button type="button" @click="removeRow(i)" x-show="rows.length > 1"
                                            class="text-gray-300 hover:text-red-500 transition p-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-between mb-4">
                    <button type="button" @click="addRows(5)"
                        class="inline-flex items-center gap-1.5 text-xs font-medium text-primary hover:text-secondary transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Adicionar 5 linhas
                    </button>
                    <span class="text-xs text-gray-400" x-text="validCount + ' email(s) válido(s)'"></span>
                </div>

                <form method="POST" action="{{ route('users.import.preview') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="paste_data" :value="JSON.stringify(rows.filter(r => r.name.trim() && r.email.trim()))">
                    <button type="submit" :disabled="validCount === 0"
                        :class="validCount === 0 ? 'opacity-50 cursor-not-allowed' : ''"
                        class="w-full inline-flex items-center justify-center gap-2 bg-primary hover:bg-secondary text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span x-text="'Revisar ' + validCount + ' usuário(s)'"></span>
                    </button>
                </form>
            </div>

            {{-- Tab: File Upload --}}
            <div x-show="mode === 'file'" x-cloak>
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs text-gray-500">Formato: CSV com colunas <strong>nome</strong>, <strong>email</strong>, <strong>cargo</strong></p>
                    <a href="{{ route('users.import.template') }}"
                       class="inline-flex items-center gap-1.5 text-xs font-medium text-primary hover:text-secondary transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Baixar modelo
                    </a>
                </div>

                <form method="POST" action="{{ route('users.import.preview') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary transition cursor-pointer"
                             onclick="document.getElementById('file-input').click()">
                            <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-sm text-gray-500 font-medium" id="file-label">Clique para selecionar o arquivo</p>
                            <p class="text-xs text-gray-400 mt-1">CSV até 2MB</p>
                        </div>
                        <input type="file" name="file" id="file-input" accept=".csv,.txt,.xlsx,.xls" class="hidden"
                               onchange="document.getElementById('file-label').textContent = this.files[0]?.name || 'Clique para selecionar'; this.form.querySelector('button[type=submit]').disabled = !this.files.length">
                        @error('file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                        <button type="submit" disabled
                            class="w-full inline-flex items-center justify-center gap-2 bg-primary hover:bg-secondary text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Visualizar dados
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
    function importManager() {
        return {
            mode: 'paste',
            rows: Array.from({length: 5}, () => ({name: '', email: ''})),

            get validCount() {
                return this.rows.filter(r => r.name.trim() && r.email.trim() && r.email.includes('@')).length;
            },

            addRows(n) {
                for (let i = 0; i < n; i++) this.rows.push({name: '', email: ''});
            },

            removeRow(i) {
                if (this.rows.length > 1) this.rows.splice(i, 1);
            },

            handlePaste(e, startIndex) {
                const text = (e.clipboardData || window.clipboardData).getData('text');
                if (!text) return;

                const lines = text.split(/\r?\n/).filter(l => l.trim());
                if (lines.length <= 1 && !text.includes('\t')) return; // Not a multi-line paste

                e.preventDefault();

                const parsed = lines.map(line => {
                    // Split by tab (Excel/Sheets) or semicolon or comma
                    const parts = line.includes('\t') ? line.split('\t') : (line.includes(';') ? line.split(';') : line.split(','));
                    const clean = parts.map(p => p.trim().replace(/^["']|["']$/g, ''));

                    // Detect which column is email
                    let name = '', email = '';
                    for (const part of clean) {
                        if (part.includes('@')) email = part;
                        else if (!name && part.length > 1) name = part;
                    }
                    return {name, email};
                }).filter(r => r.name || r.email);

                if (parsed.length === 0) return;

                // Replace rows starting from paste position
                for (let i = 0; i < parsed.length; i++) {
                    const idx = startIndex + i;
                    if (idx < this.rows.length) {
                        this.rows[idx] = parsed[i];
                    } else {
                        this.rows.push(parsed[i]);
                    }
                }

                // Add empty rows at end if needed
                if (this.rows.length < startIndex + parsed.length + 3) {
                    this.addRows(3);
                }
            }
        };
    }
    </script>
    @endpush

</x-layout.app>
