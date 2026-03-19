<x-layout.app title="Editar Grupo">

    <div class="mb-6">
        <a href="{{ route('groups.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-600 transition mb-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar para grupos
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Formulário (2/3) --}}
        <div class="lg:col-span-2 space-y-6">
            <form method="POST" action="{{ route('groups.update', $group) }}" enctype="multipart/form-data" id="group-form">
                @csrf @method('PUT')

                {{-- Dados do grupo --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-800">Dados do Grupo</h3>
                            <p class="text-xs text-gray-400">Informações básicas do grupo</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nome do grupo <span class="text-red-500">*</span></label>
                            <input type="text" id="name" name="name"
                                value="{{ old('name', $group->name) }}" required
                                placeholder="Ex: Equipe Comercial"
                                class="w-full rounded-lg border @error('name') border-red-400 @else border-gray-300 @enderror px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            @error('name') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="description" class="block text-sm font-medium text-gray-700">Descrição</label>
                            <textarea id="description" name="description" rows="3"
                                placeholder="Descreva o propósito deste grupo..."
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">{{ old('description', $group->description) }}</textarea>
                            @error('description') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Membros --}}
                @if($users->isNotEmpty())
                <div class="bg-white rounded-xl shadow-sm p-6" x-data="{
                    search: '',
                    get filteredUsers() {
                        if (!this.search) return true;
                        return true;
                    }
                }">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-800">Membros do Grupo</h3>
                            <p class="text-xs text-gray-400">Selecione os colaboradores que fazem parte deste grupo</p>
                        </div>
                    </div>

                    {{-- Busca --}}
                    <div class="relative mb-4">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" x-model="search" placeholder="Buscar colaborador..."
                            class="w-full pl-10 pr-4 py-2 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>

                    @php $groupUserIds = $group->users->pluck('id')->toArray(); @endphp

                    <div class="space-y-1 max-h-64 overflow-y-auto pr-1">
                        @foreach($users as $user)
                            <label class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-gray-50 cursor-pointer transition"
                                   x-show="!search || '{{ strtolower($user->name) }}'.includes(search.toLowerCase())"
                                   x-cloak>
                                <input type="checkbox" name="users[]" value="{{ $user->id }}"
                                    {{ in_array($user->id, old('users', $groupUserIds)) ? 'checked' : '' }}
                                    class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer">
                                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-primary">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-700">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $user->email }}</p>
                                </div>
                                <span class="text-xs px-2 py-0.5 rounded-full {{ $user->role === 'instructor' ? 'bg-purple-50 text-purple-600' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $user->role === 'instructor' ? 'Instrutor' : 'Colaborador' }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="flex justify-end mt-6">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('groups.index') }}" class="px-4 py-2.5 text-sm text-gray-600 hover:text-gray-800 transition">Cancelar</a>
                        <button type="submit"
                            class="inline-flex items-center gap-2 bg-primary hover:bg-secondary text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Salvar Alterações
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Resumo (1/3) --}}
        <div>
            <div class="bg-white rounded-xl shadow-sm p-5 sticky top-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Resumo do Grupo</p>

                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-800">{{ $group->users->count() }}</p>
                            <p class="text-xs text-gray-400">Membros atuais</p>
                        </div>
                    </div>

                    @if($group->users->isNotEmpty())
                        <div class="border-t border-gray-100 pt-3">
                            <p class="text-xs font-medium text-gray-500 mb-2">Membros</p>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($group->users->take(10) as $member)
                                    <span class="inline-flex items-center gap-1 text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                                        <span class="w-4 h-4 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0">
                                            <span class="text-[9px] font-bold text-primary">{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                                        </span>
                                        {{ Str::before($member->name, ' ') }}
                                    </span>
                                @endforeach
                                @if($group->users->count() > 10)
                                    <span class="text-xs text-gray-400 px-2 py-1">+{{ $group->users->count() - 10 }} mais</span>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="border-t border-gray-100 pt-3">
                        <p class="text-xs text-gray-400">Criado em {{ $group->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

</x-layout.app>
