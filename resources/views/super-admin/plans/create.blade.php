<x-layout.app title="Super Admin - Novo Plano">

    <div class="mb-6">
        <a href="{{ route('super.plans.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar aos planos
        </a>
    </div>

    <p class="text-sm text-gray-500 mb-6">Preencha os dados para criar um novo plano</p>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden max-w-2xl">
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-gray-800">Novo Plano</h3>
                    <p class="text-xs text-gray-400">Defina nome, preço e limites</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('super.plans.store') }}">
                @csrf

                <div class="space-y-5">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Nome</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                               class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary @error('name') border-red-400 ring-1 ring-red-400 @enderror"
                               placeholder="Ex: Plano Profissional">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-1.5">Preço (R$)</label>
                        <input type="number" id="price" name="price" value="{{ old('price') }}" min="0" step="0.01" required
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
                        <input type="number" id="max_users" name="max_users" value="{{ old('max_users') }}" min="1"
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
                        <input type="number" id="max_trainings" name="max_trainings" value="{{ old('max_trainings') }}" min="1"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary @error('max_trainings') border-red-400 ring-1 ring-red-400 @enderror"
                               placeholder="Ilimitado">
                        @error('max_trainings')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 flex items-center gap-3">
                    <button type="submit"
                            class="bg-primary hover:bg-secondary text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition shadow-sm">
                        Criar Plano
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
