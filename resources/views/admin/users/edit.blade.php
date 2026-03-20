<x-layout.app title="Editar Usuário: {{ $user->name }}">
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
            <a href="{{ route('users.show', $user) }}"
               class="inline-flex items-center gap-2 bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Visualizar
            </a>
            <form method="POST" action="{{ route('users.destroy', $user) }}" data-confirm="Remover este usuário?" style="display: inline;">
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
            @if($user->isEmployee())
                <div class="grid grid-cols-3 gap-4 pt-4 border-t border-gray-100">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-primary">{{ $user->trainingViews->count() }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Treinamentos</p>
                    </div>
                    <div class="text-center border-x border-gray-100">
                        <p class="text-2xl font-bold text-green-500">{{ $user->trainingViews->where('completed_at', '!=', null)->count() }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Concluídos</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold" style="color: var(--primary)">{{ $user->trainingViews->count() > 0 ? round(($user->trainingViews->where('completed_at', '!=', null)->count() / $user->trainingViews->count()) * 100) : 0 }}%</p>
                        <p class="text-xs text-gray-400 mt-0.5">Taxa</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Coluna esquerda: informações + perfil + grupos --}}
        <div class="space-y-6">

            {{-- Informações básicas --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">Informações gerais</h3>
                        <p class="text-xs text-gray-400">Edite os dados do usuário</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="space-y-1">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nome completo <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name"
                            value="{{ old('name', $user->name) }}" required autofocus
                            class="w-full rounded-lg border @error('name') border-red-400 @else border-gray-300 @enderror px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="email" class="block text-sm font-medium text-gray-700">E-mail <span class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email"
                            value="{{ old('email', $user->email) }}" required
                            class="w-full rounded-lg border @error('email') border-red-400 @else border-gray-300 @enderror px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="role" class="block text-sm font-medium text-gray-700">Perfil <span class="text-red-500">*</span></label>
                        <select id="role" name="role" required
                            class="w-full rounded-lg border @error('role') border-red-400 @else border-gray-300 @enderror px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="">Selecione...</option>
                            <option value="instructor" {{ old('role', $user->role) === 'instructor' ? 'selected' : '' }}>Instrutor</option>
                            <option value="employee"   {{ old('role', $user->role) === 'employee'   ? 'selected' : '' }}>Colaborador</option>
                        </select>
                        @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" name="active" value="1"
                                {{ old('active', $user->active) ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary">
                            <span class="text-sm font-medium text-gray-700">Usuário ativo</span>
                        </label>
                        <p class="text-xs text-gray-400 mt-1 ml-6">Usuários inativos não conseguem entrar na plataforma.</p>
                    </div>

                    {{-- Grupos --}}
                    @if($groups->isNotEmpty())
                        <div class="pt-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Grupos</label>
                            @php $userGroupIds = $user->groups->pluck('id')->toArray(); @endphp
                            <div class="grid grid-cols-2 gap-2">
                                @foreach($groups as $group)
                                    <label class="flex items-center gap-2 p-2.5 rounded-lg border border-gray-100 hover:border-primary/30 hover:bg-primary/5 cursor-pointer transition">
                                        <input type="checkbox" name="groups[]" value="{{ $group->id }}"
                                            {{ in_array($group->id, old('groups', $userGroupIds)) ? 'checked' : '' }}
                                            class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary">
                                        <span class="text-sm text-gray-700">{{ $group->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="flex gap-3 pt-2">
                        <button type="submit"
                            class="inline-flex items-center gap-2 bg-primary hover:bg-secondary text-white px-5 py-2 rounded-lg text-sm font-semibold transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Salvar alterações
                        </button>
                        <a href="{{ route('users.index') }}" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 transition">Cancelar</a>
                    </div>
                </form>
            </div>

        </div>

        {{-- Coluna direita: senha --}}
        <div class="space-y-6">

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">Redefinir senha</h3>
                        <p class="text-xs text-gray-400">Deixe em branco para manter a senha atual</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    {{-- Hidden fields to preserve existing values --}}
                    <input type="hidden" name="name" value="{{ $user->name }}">
                    <input type="hidden" name="email" value="{{ $user->email }}">
                    <input type="hidden" name="role" value="{{ $user->role }}">
                    <input type="hidden" name="active" value="{{ $user->active ? '1' : '0' }}">

                    <div class="space-y-1">
                        <label for="password" class="block text-sm font-medium text-gray-700">Nova senha</label>
                        <input type="password" id="password" name="password"
                            autocomplete="new-password"
                            class="w-full rounded-lg border @error('password') border-red-400 @else border-gray-300 @enderror px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        <p class="text-xs text-gray-400">Mínimo 8 caracteres.</p>
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar nova senha</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            autocomplete="new-password"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>

                    <div class="pt-1">
                        <button type="submit"
                            class="inline-flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white px-5 py-2 rounded-lg text-sm font-semibold transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                            Alterar senha
                        </button>
                    </div>
                </form>
            </div>

            {{-- Aviso --}}
            <div class="bg-amber-50 border border-amber-100 rounded-xl p-4 flex gap-3">
                <svg class="w-5 h-5 text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-sm font-medium text-amber-800 mb-0.5">Atenção</p>
                    <p class="text-xs text-amber-700">Ao alterar o perfil de um usuário, os treinamentos atribuídos ao grupo podem mudar de acordo com as configurações da empresa.</p>
                </div>
            </div>

        </div>

    </div>

</x-layout.app>
