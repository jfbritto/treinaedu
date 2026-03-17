<x-layout.app title="Super Admin - Empresas">

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Empresas</h2>
        <a href="{{ route('super.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">
            &larr; Voltar ao Dashboard
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Nome</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Slug</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Plano</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Status Assinatura</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Data de Cadastro</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($companies as $company)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $company->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $company->slug }}</td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $company->subscription?->plan?->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            @php $status = $company->subscription?->status ?? 'none'; @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $status === 'active' ? 'bg-green-100 text-green-800' :
                                   ($status === 'trial' ? 'bg-yellow-100 text-yellow-800' :
                                   ($status === 'past_due' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-600')) }}">
                                {{ match($status) {
                                    'active' => 'Ativa',
                                    'trial' => 'Trial',
                                    'past_due' => 'Em Atraso',
                                    'cancelled' => 'Cancelada',
                                    default => 'Sem assinatura',
                                } }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $company->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('super.companies.show', $company) }}"
                               class="text-blue-600 hover:text-blue-800 font-medium">Ver</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">Nenhuma empresa encontrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($companies->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $companies->links() }}
            </div>
        @endif
    </div>

</x-layout.app>
