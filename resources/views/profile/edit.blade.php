<x-layout.app title="Meu Perfil">
    @php
        $user = auth()->user();
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
            'instructor' => 'bg-primary/15 text-primary',
            'employee'   => 'bg-green-100 text-green-700',
            default      => 'bg-gray-100 text-gray-700',
        };
    @endphp

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
                    @if($user->company)
                        <span class="text-xs text-gray-400">{{ $user->company->name }}</span>
                    @endif
                </div>
            </div>

            {{-- Stats (só para colaboradores) --}}
            @if($stats)
                <div class="grid grid-cols-3 gap-4 pt-4 border-t border-gray-100">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-primary">{{ $stats['pending'] }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Pendente{{ $stats['pending'] !== 1 ? 's' : '' }}</p>
                    </div>
                    <div class="text-center border-x border-gray-100">
                        <p class="text-2xl font-bold text-green-500">{{ $stats['completed'] }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Concluído{{ $stats['completed'] !== 1 ? 's' : '' }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold" style="color: var(--primary)">{{ $stats['certificates'] }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Certificado{{ $stats['certificates'] !== 1 ? 's' : '' }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Grid principal --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Coluna esquerda: informações pessoais --}}
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">Informações pessoais</h3>
                        <p class="text-xs text-gray-400">Nome e e-mail de acesso à plataforma</p>
                    </div>
                </div>

                @if(session('status') === 'profile-updated')
                    <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-2.5 rounded-lg mb-4">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Perfil atualizado com sucesso!
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-1">
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Nome completo <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name"
                            value="{{ old('name', $user->name) }}" required autofocus
                            class="w-full rounded-lg border @error('name') border-red-400 @else border-gray-300 @enderror px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            E-mail <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" name="email"
                            value="{{ old('email', $user->email) }}" required
                            class="w-full rounded-lg border @error('email') border-red-400 @else border-gray-300 @enderror px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        <p class="text-xs text-gray-400">Usado para entrar na plataforma.</p>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-1">
                        <x-forms.button type="submit">Salvar informações</x-forms.button>
                    </div>
                </form>
            </div>

            {{-- Dica de segurança --}}
            <div class="rounded-xl p-4 flex gap-3" style="background-color: color-mix(in srgb, var(--primary) 8%, transparent); border: 1px solid color-mix(in srgb, var(--primary) 15%, transparent)">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--primary)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-sm font-medium mb-0.5" style="color: var(--secondary)">Dica de segurança</p>
                    <p class="text-xs" style="color: var(--primary)">Nunca compartilhe sua senha com ninguém. Nenhum funcionário autorizado vai pedir sua senha.</p>
                </div>
            </div>
        </div>

        {{-- Coluna direita: alterar senha --}}
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">Alterar senha</h3>
                        <p class="text-xs text-gray-400">Troque periodicamente para manter sua conta segura</p>
                    </div>
                </div>

                @if(session('status') === 'password-updated')
                    <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-2.5 rounded-lg mb-4">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Senha alterada com sucesso!
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="space-y-1">
                        <label for="current_password" class="block text-sm font-medium text-gray-700">Senha atual</label>
                        <input type="password" id="current_password" name="current_password"
                            autocomplete="current-password"
                            class="w-full rounded-lg border @if($errors->updatePassword->has('current_password')) border-red-400 @else border-gray-300 @endif px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        <p class="text-xs text-gray-400">A senha que você usa hoje para entrar.</p>
                        @if($errors->updatePassword->has('current_password'))
                            <p class="text-red-500 text-xs mt-1">{{ $errors->updatePassword->first('current_password') }}</p>
                        @endif
                    </div>

                    <div class="space-y-1">
                        <label for="password" class="block text-sm font-medium text-gray-700">Nova senha</label>
                        <input type="password" id="password" name="password"
                            autocomplete="new-password"
                            class="w-full rounded-lg border @if($errors->updatePassword->has('password')) border-red-400 @else border-gray-300 @endif px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        <p class="text-xs text-gray-400">Mínimo 8 caracteres. Misture letras e números.</p>
                        @if($errors->updatePassword->has('password'))
                            <p class="text-red-500 text-xs mt-1">{{ $errors->updatePassword->first('password') }}</p>
                        @endif
                    </div>

                    <div class="space-y-1">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar nova senha</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            autocomplete="new-password"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        <p class="text-xs text-gray-400">Digite a nova senha mais uma vez para confirmar.</p>
                    </div>

                    <div class="pt-1">
                        <x-forms.button type="submit">Alterar senha</x-forms.button>
                    </div>
                </form>
            </div>

            {{-- Card de acesso rápido para colaboradores --}}
            @if($user->isEmployee())
                <div class="bg-white rounded-xl shadow-sm p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Acesso rápido</h3>
                    <div class="space-y-2">
                        <a href="{{ route('employee.trainings.index') }}"
                            class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 hover:border-primary/30 hover:bg-primary/5 transition">
                            <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Meus Treinamentos</p>
                                <p class="text-xs text-gray-400">Ver todos os treinamentos atribuídos</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-300 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                        <a href="{{ route('employee.certificates.index') }}"
                            class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 hover:border-green-200 hover:bg-green-50 transition">
                            <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Meus Certificados</p>
                                <p class="text-xs text-gray-400">Baixar e compartilhar certificados</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-300 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
            @endif
        </div>

    </div>
</x-layout.app>
