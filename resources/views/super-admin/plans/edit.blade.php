<x-layout.app title="Super Admin - Editar Plano">

    <div class="mb-6">
        <a href="{{ route('super.plans.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar aos planos
        </a>
    </div>

    <p class="text-sm text-gray-500 mb-6">Atualize as informações do plano {{ $plan->name }}</p>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden max-w-2xl">
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-gray-800">Editar Plano</h3>
                    <p class="text-xs text-gray-400">{{ $plan->name }}</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('super.plans.update', $plan) }}">
                @csrf
                @method('PUT')

                <div class="space-y-5">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Nome</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $plan->name) }}" required
                               class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary @error('name') border-red-400 ring-1 ring-red-400 @enderror"
                               placeholder="Ex: Plano Profissional">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-1.5">Preço (R$)</label>
                        <input type="number" id="price" name="price" value="{{ old('price', $plan->price) }}" min="0" step="0.01" required
                               class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary @error('price') border-red-400 ring-1 ring-red-400 @enderror"
                               placeholder="0,00">
                        @error('price')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="max_users" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Max Usuários
                            <span class="text-gray-400 font-normal">(deixe vazio para ilimitado)</span>
                        </label>
                        <input type="number" id="max_users" name="max_users" value="{{ old('max_users', $plan->max_users) }}" min="1"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary @error('max_users') border-red-400 ring-1 ring-red-400 @enderror"
                               placeholder="Ilimitado">
                        @error('max_users')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="max_trainings" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Max Treinamentos
                            <span class="text-gray-400 font-normal">(deixe vazio para ilimitado)</span>
                        </label>
                        <input type="number" id="max_trainings" name="max_trainings" value="{{ old('max_trainings', $plan->max_trainings) }}" min="1"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary @error('max_trainings') border-red-400 ring-1 ring-red-400 @enderror"
                               placeholder="Ilimitado">
                        @error('max_trainings')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <input type="hidden" name="active" value="0">
                        <input type="checkbox" id="active" name="active" value="1"
                               {{ old('active', $plan->active) ? 'checked' : '' }}
                               class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                        <label for="active" class="text-sm font-medium text-gray-700">Plano ativo</label>
                    </div>
                </div>

                <div class="mt-8 flex items-center gap-3">
                    <button type="submit"
                            class="bg-primary hover:bg-secondary text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition shadow-sm">
                        Salvar Alterações
                    </button>
                    <a href="{{ route('super.plans.index') }}"
                       class="px-4 py-2.5 rounded-lg text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100 transition">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

</x-layout.app>
