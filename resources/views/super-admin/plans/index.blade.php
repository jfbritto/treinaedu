<x-layout.app title="Super Admin - Planos">

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Planos</h2>
        <a href="{{ route('super.plans.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
            Novo Plano
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Nome</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Preço</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Max Usuários</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Max Treinamentos</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Ativo</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($plans as $plan)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $plan->name }}</td>
                        <td class="px-4 py-3 text-gray-800">R$ {{ number_format($plan->price, 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $plan->max_users ?? 'Ilimitado' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $plan->max_trainings ?? 'Ilimitado' }}</td>
                        <td class="px-4 py-3">
                            @if($plan->active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Sim</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Não</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('super.plans.edit', $plan) }}"
                               class="text-blue-600 hover:text-blue-800 font-medium">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">Nenhum plano encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</x-layout.app>
