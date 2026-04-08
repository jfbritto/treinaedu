<x-layout.app title="Novo Grupo">

    <div class="mb-6">
        <a href="{{ route('groups.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar para grupos
        </a>
    </div>

    <form method="POST" action="{{ route('groups.store') }}"
          x-data="{
              name: '{{ old('name', '') }}',
              description: '{{ old('description', '') }}',
              search: '',
              selected: @js(old('users', [])),
              get selectedCount() { return this.selected.length; },
              isSelected(id) { return this.selected.includes(id); },
              toggle(id) {
                  if (this.isSelected(id)) {
                      this.selected = this.selected.filter(i => i != id);
                  } else {
                      this.selected.push(id);
                  }
              }
          }">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-6xl">

            {{-- Coluna principal --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Dados do grupo --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-800">Dados do Grupo</h3>
                            <p class="text-xs text-gray-400">Identificação e propósito do grupo</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nome do grupo <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                </div>
                                <input type="text" id="name" name="name"
                                    x-model="name" required
                                    placeholder="Ex: Equipe Comercial"
                                    class="w-full pl-10 pr-3 rounded-lg border @error('name') border-red-400 @else border-gray-300 @enderror px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                            @error('name') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="description" class="block text-sm font-medium text-gray-700">Descrição <span class="text-xs text-gray-400 font-normal">(opcional)</span></label>
                            <textarea id="description" name="description" rows="3"
                                x-model="description"
                                placeholder="Descreva o propósito deste grupo, qual área da empresa, etc."
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                            @error('description') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                            <p class="text-xs text-gray-400">A descrição ajuda a entender o objetivo do grupo na hora de atribuir treinamentos.</p>
                        </div>
                    </div>
                </div>

                {{-- Membros --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-gray-800">Membros do Grupo <span class="text-xs text-gray-400 font-normal">(opcional)</span></h3>
                            <p class="text-xs text-gray-400">Selecione os colaboradores que farão parte deste grupo</p>
                        </div>
                        <span x-show="selectedCount > 0" x-cloak
                            class="text-xs font-semibold text-primary bg-primary/10 px-2.5 py-1 rounded-full"
                            x-text="selectedCount + ' selecionado' + (selectedCount > 1 ? 's' : '')"></span>
                    </div>

                    @if($users->isEmpty())
                        <div class="text-center py-8 px-4 border-2 border-dashed border-gray-200 rounded-lg">
                            <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <p class="text-sm text-gray-500 font-medium">Nenhum colaborador cadastrado</p>
                            <p class="text-xs text-gray-400 mt-1">Cadastre colaboradores para vinculá-los a este grupo.</p>
                            <a href="{{ route('users.create') }}" class="inline-block mt-3 text-xs text-primary hover:underline font-medium">+ Cadastrar primeiro colaborador</a>
                        </div>
                    @else
                        <div class="relative mb-4">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text" x-model="search" placeholder="Buscar colaborador por nome ou e-mail..."
                                class="w-full pl-10 pr-4 py-2.5 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>

                        <div class="space-y-1 max-h-80 overflow-y-auto pr-1 -mr-1">
                            @foreach($users as $user)
                                <label class="flex items-center gap-3 p-3 rounded-lg cursor-pointer transition border border-transparent"
                                       :class="isSelected({{ $user->id }}) ? 'bg-primary/5 border-primary/20' : 'hover:bg-gray-50'"
                                       x-show="!search || '{{ strtolower($user->name . ' ' . $user->email) }}'.includes(search.toLowerCase())"
                                       x-cloak>
                                    <input type="checkbox" name="users[]" value="{{ $user->id }}"
                                        @click="toggle({{ $user->id }})"
                                        {{ in_array($user->id, old('users', [])) ? 'checked' : '' }}
                                        class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                                         style="background: linear-gradient(135deg, var(--primary), var(--secondary))">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-700 truncate">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-400 truncate">{{ $user->email }}</p>
                                    </div>
                                    <span class="text-xs font-medium px-2 py-0.5 rounded-full flex-shrink-0
                                        {{ $user->role === 'instructor' ? 'bg-purple-50 text-purple-600 border border-purple-100' : 'bg-gray-100 text-gray-500 border border-gray-200' }}">
                                        {{ $user->role === 'instructor' ? 'Instrutor' : 'Colaborador' }}
                                    </span>
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
                        <div class="w-20 h-20 rounded-2xl flex items-center justify-center mb-3"
                             style="background: linear-gradient(135deg, var(--primary), var(--secondary))">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <p class="text-base font-semibold text-gray-800 break-words" x-text="name || 'Nome do grupo'"></p>
                        <p class="text-xs text-gray-400 mt-1 line-clamp-3 break-words" x-text="description || 'Sem descrição'"></p>
                    </div>

                    <div class="pt-4 space-y-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Membros</span>
                            <span class="font-semibold text-gray-800" x-text="selectedCount"></span>
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
                            Criar Grupo
                        </button>
                        <a href="{{ route('groups.index') }}" class="block text-center text-sm text-gray-500 hover:text-gray-700 transition py-1">
                            Cancelar
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </form>

</x-layout.app>
