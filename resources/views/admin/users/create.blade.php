<x-layout.app title="Novo Usuário">

    <div class="mb-6">
        <a href="{{ route('users.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar para usuários
        </a>
    </div>

    <form method="POST" action="{{ route('users.store') }}"
          x-data="{
              name: '{{ old('name', '') }}',
              email: '{{ old('email', '') }}',
              role: '{{ old('role', '') }}',
              password: '',
              password_confirmation: '',
              get initials() {
                  if (!this.name) return '?';
                  return this.name.split(' ').filter(w => w).slice(0, 2).map(w => w[0].toUpperCase()).join('');
              },
              get passwordStrength() {
                  let score = 0;
                  if (this.password.length >= 8) score++;
                  if (/[A-Z]/.test(this.password)) score++;
                  if (/[0-9]/.test(this.password)) score++;
                  if (/[^A-Za-z0-9]/.test(this.password)) score++;
                  return score;
              },
              get strengthLabel() {
                  if (!this.password) return '';
                  return ['Muito fraca','Fraca','Razoável','Forte','Muito forte'][this.passwordStrength];
              },
              get strengthColor() {
                  return ['#ef4444','#f97316','#eab308','#22c55e','#16a34a'][this.passwordStrength];
              },
              get passwordsMatch() {
                  return this.password && this.password === this.password_confirmation;
              },
              get roleLabel() {
                  return { instructor: 'Instrutor', employee: 'Colaborador' }[this.role] || 'Sem perfil';
              }
          }">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-6xl">

            {{-- Coluna principal --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Dados do usuário --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-800">Dados Pessoais</h3>
                            <p class="text-xs text-gray-400">Identificação do colaborador</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nome completo <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <input type="text" id="name" name="name"
                                    x-model="name" required
                                    placeholder="Ex: Maria Silva"
                                    class="w-full pl-10 pr-3 rounded-lg border @error('name') border-red-400 @else border-gray-300 @enderror px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                            @error('name') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="email" class="block text-sm font-medium text-gray-700">E-mail corporativo <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <input type="email" id="email" name="email"
                                    x-model="email" required
                                    placeholder="maria@empresa.com"
                                    class="w-full pl-10 pr-3 rounded-lg border @error('email') border-red-400 @else border-gray-300 @enderror px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                            @error('email') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                            <p class="text-xs text-gray-400">O usuário receberá um e-mail de boas-vindas neste endereço.</p>
                        </div>
                    </div>
                </div>

                {{-- Perfil de acesso --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-800">Perfil de Acesso <span class="text-red-500">*</span></h3>
                            <p class="text-xs text-gray-400">Define o que o usuário pode fazer no sistema</p>
                        </div>
                    </div>

                    <input type="hidden" name="role" x-model="role">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        {{-- Colaborador --}}
                        <button type="button" @click="role = 'employee'"
                            :class="role === 'employee' ? 'border-primary bg-primary/5' : 'border-gray-200 hover:border-gray-300'"
                            class="text-left p-4 rounded-xl border-2 transition relative">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                                    :class="role === 'employee' ? 'bg-primary text-white' : 'bg-gray-100 text-gray-400'">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800">Colaborador</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Faz treinamentos atribuídos e gera certificados</p>
                                </div>
                                <svg x-show="role === 'employee'" class="w-5 h-5 text-primary flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </button>

                        {{-- Instrutor --}}
                        <button type="button" @click="role = 'instructor'"
                            :class="role === 'instructor' ? 'border-primary bg-primary/5' : 'border-gray-200 hover:border-gray-300'"
                            class="text-left p-4 rounded-xl border-2 transition relative">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                                    :class="role === 'instructor' ? 'bg-primary text-white' : 'bg-gray-100 text-gray-400'">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800">Instrutor</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Cria e gerencia treinamentos da empresa</p>
                                </div>
                                <svg x-show="role === 'instructor'" class="w-5 h-5 text-primary flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </button>
                    </div>
                    @error('role') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                </div>

                {{-- Senha --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-800">Senha de Acesso</h3>
                            <p class="text-xs text-gray-400">O usuário pode alterar a senha após o primeiro acesso</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label for="password" class="block text-sm font-medium text-gray-700">Senha <span class="text-red-500">*</span></label>
                            <input type="password" id="password" name="password"
                                x-model="password" required minlength="8"
                                placeholder="Mínimo 8 caracteres"
                                class="w-full rounded-lg border @error('password') border-red-400 @else border-gray-300 @enderror px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            @error('password') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror

                            {{-- Strength indicator --}}
                            <div x-show="password" x-cloak class="mt-2">
                                <div class="flex gap-1">
                                    <template x-for="i in 4" :key="i">
                                        <div class="flex-1 h-1 rounded-full bg-gray-100">
                                            <div class="h-full rounded-full transition-all"
                                                :style="'width:' + (passwordStrength >= i ? '100%' : '0%') + '; background-color: ' + strengthColor"></div>
                                        </div>
                                    </template>
                                </div>
                                <p class="text-xs mt-1.5" :style="'color:' + strengthColor" x-text="'Força: ' + strengthLabel"></p>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Senha <span class="text-red-500">*</span></label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                x-model="password_confirmation" required
                                placeholder="Repita a senha"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            <p x-show="password_confirmation && !passwordsMatch" x-cloak class="text-xs text-red-500 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                As senhas não coincidem
                            </p>
                            <p x-show="password_confirmation && passwordsMatch" x-cloak class="text-xs text-green-600 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Senhas coincidem
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Grupos --}}
                <div class="bg-white rounded-xl shadow-sm p-6" x-data="{ search: '' }">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-800">Grupos <span class="text-xs text-gray-400 font-normal">(opcional)</span></h3>
                            <p class="text-xs text-gray-400">Vincule a grupos para receber treinamentos automaticamente</p>
                        </div>
                    </div>

                    @if($groups->isEmpty())
                        <div class="text-center py-8 px-4 border-2 border-dashed border-gray-200 rounded-lg">
                            <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <p class="text-sm text-gray-500 font-medium">Nenhum grupo cadastrado ainda</p>
                            <p class="text-xs text-gray-400 mt-1">Você pode criar grupos para organizar seus colaboradores.</p>
                            <a href="{{ route('groups.create') }}" class="inline-block mt-3 text-xs text-primary hover:underline font-medium">+ Criar primeiro grupo</a>
                        </div>
                    @else
                        <div class="relative mb-4">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text" x-model="search" placeholder="Buscar grupo..."
                                class="w-full pl-10 pr-4 py-2 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>

                        <div class="space-y-1 max-h-64 overflow-y-auto pr-1">
                            @foreach($groups as $group)
                                <label class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-gray-50 cursor-pointer transition"
                                       x-show="!search || '{{ strtolower($group->name) }}'.includes(search.toLowerCase())"
                                       x-cloak>
                                    <input type="checkbox" name="groups[]" value="{{ $group->id }}"
                                        {{ in_array($group->id, old('groups', [])) ? 'checked' : '' }}
                                        class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer">
                                    <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-700">{{ $group->name }}</p>
                                        @if($group->description)
                                            <p class="text-xs text-gray-400 truncate">{{ $group->description }}</p>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

            {{-- Coluna lateral: Preview --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm p-6 sticky top-6">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Pré-visualização</p>

                    <div class="flex flex-col items-center text-center pb-5 border-b border-gray-100">
                        <div class="w-20 h-20 rounded-full flex items-center justify-center text-2xl font-bold text-white mb-3"
                             style="background: linear-gradient(135deg, var(--primary), var(--secondary))">
                            <span x-text="initials"></span>
                        </div>
                        <p class="text-base font-semibold text-gray-800" x-text="name || 'Nome do usuário'"></p>
                        <p class="text-sm text-gray-400" x-text="email || 'email@empresa.com'"></p>
                    </div>

                    <div class="pt-4 space-y-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Perfil</span>
                            <span class="font-medium text-gray-800" x-text="roleLabel"></span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Status</span>
                            <span class="inline-flex items-center gap-1 text-green-600 font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                Ativo
                            </span>
                        </div>
                    </div>

                    <div class="mt-5 pt-5 border-t border-gray-100 space-y-3">
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 bg-primary hover:bg-secondary text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Criar Usuário
                        </button>
                        <a href="{{ route('users.index') }}" class="block text-center text-sm text-gray-500 hover:text-gray-700 transition py-1">
                            Cancelar
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </form>

</x-layout.app>
