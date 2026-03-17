<x-layout.app title="Super Admin - Editar Plano">

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Editar Plano: {{ $plan->name }}</h2>
        <a href="{{ route('super.plans.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
            &larr; Voltar aos Planos
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6 max-w-lg">
        <form method="POST" action="{{ route('super.plans.update', $plan) }}">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $plan->name) }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Preço (R$)</label>
                    <input type="number" id="price" name="price" value="{{ old('price', $plan->price) }}" min="0" step="0.01" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('price') border-red-500 @enderror">
                    @error('price')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="max_users" class="block text-sm font-medium text-gray-700 mb-1">
                        Max Usuários <span class="text-gray-400">(deixe vazio para ilimitado)</span>
                    </label>
                    <input type="number" id="max_users" name="max_users" value="{{ old('max_users', $plan->max_users) }}" min="1"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('max_users') border-red-500 @enderror">
                    @error('max_users')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="max_trainings" class="block text-sm font-medium text-gray-700 mb-1">
                        Max Treinamentos <span class="text-gray-400">(deixe vazio para ilimitado)</span>
                    </label>
                    <input type="number" id="max_trainings" name="max_trainings" value="{{ old('max_trainings', $plan->max_trainings) }}" min="1"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('max_trainings') border-red-500 @enderror">
                    @error('max_trainings')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3">
                    <input type="hidden" name="active" value="0">
                    <input type="checkbox" id="active" name="active" value="1"
                           {{ old('active', $plan->active) ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="active" class="text-sm font-medium text-gray-700">Plano ativo</label>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium">
                    Salvar Alterações
                </button>
                <a href="{{ route('super.plans.index') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg text-sm font-medium">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

</x-layout.app>
