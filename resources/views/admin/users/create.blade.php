<x-layout.app title="Novo Usuário">

    <div class="mb-6">
        <a href="{{ route('users.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-600 transition mb-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar para usuários
        </a>
    </div>

    <div class="max-w-2xl space-y-6">
        <form method="POST" action="{{ route('users.store') }}">
            @csrf

            {{-- Dados do usuário --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">Dados do Usuário</h3>
                        <p class="text-xs text-gray-400">Informações básicas do novo colaborador</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="space-y-1.5">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nome <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name"
                            value="{{ old('name') }}" required
                            placeholder="Nome completo"
                            class="w-full rounded-lg border @error('name') border-red-400 @else border-gray-300 @enderror px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        @error('name') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="email" class="block text-sm font-medium text-gray-700">E-mail <span class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email"
                            value="{{ old('email') }}" required
                            placeholder="email@empresa.com"
                            class="w-full rounded-lg border @error('email') border-red-400 @else border-gray-300 @enderror px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        @error('email') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label for="password" class="block text-sm font-medium text-gray-700">Senha <span class="text-red-500">*</span></label>
                            <input type="password" id="password" name="password" required
                                placeholder="Mínimo 8 caracteres"
                                class="w-full rounded-lg border @error('password') border-red-400 @else border-gray-300 @enderror px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            @error('password') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Senha <span class="text-red-500">*</span></label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                placeholder="Repita a senha"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label for="role" class="block text-sm font-medium text-gray-700">Perfil <span class="text-red-500">*</span></label>
                        <select id="role" name="role" required
                            class="w-full rounded-lg border @error('role') border-red-400 @else border-gray-300 @enderror px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="">Selecione...</option>
                            <option value="instructor" {{ old('role') === 'instructor' ? 'selected' : '' }}>Instrutor</option>
                            <option value="employee" {{ old('role') === 'employee' ? 'selected' : '' }}>Colaborador</option>
                        </select>
                        @error('role') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Grupos --}}
            @if($groups->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm p-6 mt-6" x-data="{ search: '' }">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">Grupos</h3>
                        <p class="text-xs text-gray-400">Vincule o usuário a um ou mais grupos</p>
                    </div>
                </div>

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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
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
            </div>
            @endif

            <div class="flex justify-end mt-6">
                <div class="flex items-center gap-3">
                    <a href="{{ route('users.index') }}" class="px-4 py-2.5 text-sm text-gray-600 hover:text-gray-800 transition">Cancelar</a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 bg-primary hover:bg-secondary text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Criar Usuário
                    </button>
                </div>
            </div>
        </form>
    </div>

</x-layout.app>
