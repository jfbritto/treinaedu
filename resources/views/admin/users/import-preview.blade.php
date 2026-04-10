<x-layout.app title="Revisar Importação">

    <div class="mb-6">
        <a href="{{ route('users.import') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar e reenviar
        </a>
    </div>

    @php
        $validCount = collect($preview)->where('valid', true)->count();
        $errorCount = collect($preview)->where('valid', false)->count();
    @endphp

    <div class="max-w-4xl space-y-6">

        {{-- Summary --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Total</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ count($preview) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5 border border-green-100">
                <p class="text-xs font-medium text-green-600 uppercase tracking-wide">Válidos</p>
                <p class="text-2xl font-bold text-green-700 mt-1">{{ $validCount }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5 border {{ $errorCount > 0 ? 'border-red-100' : 'border-gray-100' }}">
                <p class="text-xs font-medium {{ $errorCount > 0 ? 'text-red-600' : 'text-gray-400' }} uppercase tracking-wide">Com erro</p>
                <p class="text-2xl font-bold {{ $errorCount > 0 ? 'text-red-700' : 'text-gray-800' }} mt-1">{{ $errorCount }}</p>
            </div>
        </div>

        {{-- Data table --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">Dados encontrados</h3>
                        <p class="text-xs text-gray-400">Linhas com erro serão ignoradas na importação</p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100 sticky top-0">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 w-12">#</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500">Nome</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500">Email</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500">Cargo</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($preview as $row)
                            <tr class="{{ $row['valid'] ? '' : 'bg-red-50/50' }}">
                                <td class="px-4 py-2.5 text-xs text-gray-400">{{ $row['row'] }}</td>
                                <td class="px-4 py-2.5 text-gray-800 font-medium">{{ $row['name'] ?: '—' }}</td>
                                <td class="px-4 py-2.5 text-gray-600 text-xs">{{ $row['email'] ?: '—' }}</td>
                                <td class="px-4 py-2.5 text-xs">
                                    <span class="px-2 py-0.5 rounded-full {{ $row['role'] === 'instructor' ? 'bg-purple-50 text-purple-700' : 'bg-blue-50 text-blue-700' }}">
                                        {{ $row['role'] === 'instructor' ? 'Instrutor' : 'Colaborador' }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5">
                                    @if($row['valid'])
                                        <span class="inline-flex items-center gap-1 text-xs text-green-600">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            OK
                                        </span>
                                    @else
                                        <span class="text-xs text-red-600">{{ implode(', ', $row['errors']) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Import options --}}
        @if($validCount > 0)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <form method="POST" action="{{ route('users.import.process') }}">
                    @csrf

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="block text-xs font-medium text-gray-600">Forçar cargo para todos</label>
                                <select name="role_override" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                                    <option value="">Usar o cargo da planilha</option>
                                    <option value="employee">Todos como Colaborador</option>
                                    <option value="instructor">Todos como Instrutor</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="block text-xs font-medium text-gray-600">Adicionar ao grupo</label>
                                <select name="groups[]" multiple class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary" style="min-height: 38px;">
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-400">Segure Ctrl/Cmd para selecionar vários</p>
                            </div>
                        </div>

                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="send_invites" value="1" checked
                                   class="rounded border-gray-300 text-primary focus:ring-primary">
                            <span class="text-sm text-gray-700">Enviar convite por e-mail para cada usuário</span>
                        </label>

                        <div class="flex items-center gap-4 pt-2">
                            <button type="submit"
                                class="inline-flex items-center gap-2 bg-primary hover:bg-secondary text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Importar {{ $validCount }} usuário(s)
                            </button>
                            <a href="{{ route('users.import') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">Cancelar</a>
                        </div>
                    </div>
                </form>
            </div>
        @else
            <div class="bg-white rounded-xl shadow-sm p-6 text-center">
                <p class="text-sm text-red-600 font-medium">Nenhum registro válido encontrado.</p>
                <p class="text-xs text-gray-400 mt-1">Corrija os erros na planilha e tente novamente.</p>
                <a href="{{ route('users.import') }}" class="inline-flex items-center gap-2 mt-4 text-sm font-medium text-primary hover:text-secondary transition">Voltar e reenviar</a>
            </div>
        @endif

    </div>

</x-layout.app>
