<x-layout.app title="Editar Usuário">

    @php
        $initials = collect(explode(' ', $user->name))
            ->filter()->map(fn ($w) => strtoupper($w[0]))->take(2)->implode('');
        $roleLabel = match($user->role) {
            'instructor' => 'Instrutor',
            'employee'   => 'Colaborador',
            default      => ucfirst($user->role),
        };
        $roleColor = $user->role === 'instructor' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700';
        $avatarBg  = $user->role === 'instructor' ? 'bg-purple-600' : 'bg-primary';
    @endphp

    <div class="mb-5">
        <a href="{{ route('users.index') }}"
           class="inline-flex items-center gap-1 text-sm text-gray-400 hover:text-gray-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Usuários
        </a>
    </div>

    {{-- Cabeçalho do usuário --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="h-24 bg-gradient-to-r from-gray-700 to-gray-600 relative"></div>
        <div class="px-6 pb-5 pt-0 relative">
            <div class="absolute -top-10 left-6">
                <div class="w-20 h-20 rounded-full {{ $avatarBg }} border-4 border-white flex items-center justify-center shadow">
                    <span class="text-white text-2xl font-bold">{{ $initials }}</span>
                </div>
            </div>
            <div class="pt-14">
                <h2 class="text-lg font-bold text-gray-800 leading-tight">{{ $user->name }}</h2>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $roleColor }}">{{ $roleLabel }}</span>
                    <span class="text-xs text-gray-400">{{ $user->email }}</span>
                    @if(!$user->active)
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-red-100 text-red-600">Inativo</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Coluna esquerda: informações + perfil + grupos --}}
        <div class="space-y-6">

            {{-- Informações básicas --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">Informações básicas</h3>
                        <p class="text-xs text-gray-400">Nome, e-mail e perfil de acesso</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-4" id="info-form">
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
                    <div class="w-8 h-8 rounded-lg bg-yellow-50 flex items-center justify-center flex-shrink-0">
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
