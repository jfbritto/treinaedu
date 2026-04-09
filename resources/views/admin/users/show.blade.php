<x-layout.app title="Colaborador: {{ $user->name }}">
    @php
        $initials = collect(explode(' ', $user->name))
            ->filter()->map(fn ($w) => strtoupper($w[0]))->take(2)->implode('');
        $roleLabel = match($user->role) {
            'admin'      => 'Administrador',
            'instructor' => 'Instrutor',
            'employee'   => 'Colaborador',
            default      => ucfirst($user->role),
        };
        $roleColor = match($user->role) {
            'admin'      => 'bg-purple-100 text-purple-700',
            'instructor' => 'bg-purple-100 text-purple-700',
            'employee'   => 'bg-primary/15 text-primary',
            default      => 'bg-gray-100 text-gray-700',
        };
    @endphp

    {{-- Botões de ação --}}
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-800 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <span class="text-sm font-medium">Voltar</span>
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('users.edit', $user) }}"
               class="inline-flex items-center gap-2 bg-primary hover:bg-secondary text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar
            </a>
            <form method="POST" action="{{ route('users.destroy', $user) }}" data-confirm="Remover este colaborador?" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Deletar
                </button>
            </form>
        </div>
    </div>

    {{-- Cabeçalho: banner + avatar --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
        {{-- Banner --}}
        <div class="h-28 relative" style="background: linear-gradient(to right, var(--secondary), var(--primary))">
            <div class="absolute -bottom-10 left-6">
                <div class="w-20 h-20 rounded-full border-4 border-white flex items-center justify-center shadow" style="background-color: var(--primary)">
                    <span class="text-white text-2xl font-bold">{{ $initials }}</span>
                </div>
            </div>
        </div>

        {{-- Info do usuário --}}
        <div class="px-6 pb-5 pt-14">
            <div class="mb-4">
                <h2 class="text-lg font-bold text-gray-800 leading-tight">{{ $user->name }}</h2>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $roleColor }}">{{ $roleLabel }}</span>
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium {{ $user->active ? 'text-green-700' : 'text-red-500' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $user->active ? 'bg-green-500' : 'bg-red-400' }}"></span>
                        {{ $user->active ? 'Ativo' : 'Inativo' }}
                    </span>
                </div>
                <p class="text-xs text-gray-400 mt-2">{{ $user->email }}</p>
            </div>

            {{-- Stats (3 colunas) --}}
            <div class="grid grid-cols-3 gap-4 pt-4 border-t border-gray-100">
                <div class="text-center">
                    <p class="text-2xl font-bold text-primary">{{ $totalAssigned }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Atribuído{{ $totalAssigned !== 1 ? 's' : '' }}</p>
                </div>
                <div class="text-center border-x border-gray-100">
                    <p class="text-2xl font-bold text-green-500">{{ $totalCompleted }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Concluído{{ $totalCompleted !== 1 ? 's' : '' }}</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold" style="color: var(--primary)">{{ $completionRate }}%</p>
                    <p class="text-xs text-gray-400 mt-0.5">Taxa</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Grid principal --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Coluna esquerda: Informações (2/3) --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">Informações gerais</h3>
                        <p class="text-xs text-gray-400">Dados do colaborador</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Nome completo</label>
                        <div class="px-3 py-2 bg-gray-50 rounded-lg text-sm text-gray-700">{{ $user->name }}</div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">E-mail</label>
                        <div class="px-3 py-2 bg-gray-50 rounded-lg text-sm text-gray-700">{{ $user->email }}</div>
                    </div>

                    @if($user->groups->count() > 0)
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Grupos</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach($user->groups as $group)
                                    <span class="inline-block px-3 py-1 rounded-lg text-xs bg-primary/10 text-primary font-medium">{{ $group->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Coluna direita: Stats adicionais (1/3) --}}
        <div>
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">Progresso</h3>
                        <p class="text-xs text-gray-400">Desempenho geral</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-xs font-medium text-gray-700">Progresso Médio</p>
                            <span class="text-sm font-bold text-primary">{{ $avgProgress }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-primary h-2 rounded-full transition-all" style="width: {{ $avgProgress }}%"></div>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-gray-100">
                        <p class="text-xs text-gray-600">
                            <span class="font-semibold text-gray-800">{{ $totalCompleted }}</span> de <span class="font-semibold text-gray-800">{{ $totalAssigned }}</span> treinamentos concluídos
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Trainings List --}}
    <div class="mt-8 bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h2 class="text-sm font-semibold text-gray-800">Treinamentos Atribuídos</h2>
            </div>
        </div>

        @if($assignedTrainings->isEmpty())
            <div class="px-6 py-12 text-center text-gray-400">
                <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <p class="text-sm">Nenhum treinamento atribuído a este colaborador.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left px-6 py-3 font-semibold text-gray-700">Treinamento</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-700">Status</th>
                            <th class="text-center px-6 py-3 font-semibold text-gray-700">Progresso</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-700 hidden md:table-cell">Último Acesso</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($assignedTrainings as $training)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">{{ $training['title'] }}</p>
                                        @if($training['mandatory'])
                                            <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-xs bg-red-100 text-red-700 font-medium">Obrigatório</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($training['status'] === 'completed')
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-sm font-medium text-green-700">Concluído</span>
                                        </div>
                                        @if($training['completed_at'])
                                            <p class="text-xs text-gray-500 mt-1">{{ $training['completed_at']->format('d/m/Y') }}</p>
                                        @endif
                                    @elseif($training['status'] === 'pending_completion')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            Aulas Concluídas
                                        </span>
                                    @elseif($training['status'] === 'in_progress')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                            Em Progresso
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                            Não Iniciado
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <div class="w-20 bg-gray-200 rounded-full h-2">
                                            <div class="bg-primary h-2 rounded-full transition-all" style="width: {{ $training['progress_percent'] }}%"></div>
                                        </div>
                                        <span class="text-xs font-semibold text-gray-700 min-w-[35px]">{{ $training['progress_percent'] }}%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 hidden md:table-cell">
                                    @if($training['last_accessed'])
                                        <p class="text-sm text-gray-600">{{ $training['last_accessed']->format('d/m/Y H:i') }}</p>
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</x-layout.app>
