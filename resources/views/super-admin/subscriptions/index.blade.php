<x-layout.app title="Super Admin - Assinaturas">

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Assinaturas</h2>
        <a href="{{ route('super.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">
            &larr; Voltar ao Dashboard
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Empresa</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Plano</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Período</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($subscriptions as $subscription)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">
                            {{ $subscription->company?->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $subscription->plan?->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $subscription->status === 'active' ? 'bg-green-100 text-green-800' :
                                   ($subscription->status === 'trial' ? 'bg-yellow-100 text-yellow-800' :
                                   ($subscription->status === 'past_due' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-600')) }}">
                                {{ match($subscription->status) {
                                    'active' => 'Ativa',
                                    'trial' => 'Trial',
                                    'past_due' => 'Em Atraso',
                                    'cancelled' => 'Cancelada',
                                    default => ucfirst($subscription->status),
                                } }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            @if($subscription->starts_at || $subscription->ends_at)
                                {{ $subscription->starts_at?->format('d/m/Y') ?? '—' }}
                                &rarr;
                                {{ $subscription->ends_at?->format('d/m/Y') ?? '—' }}
                            @elseif($subscription->trial_ends_at)
                                Trial até {{ $subscription->trial_ends_at->format('d/m/Y') }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($subscription->company)
                                <a href="{{ route('super.companies.show', $subscription->company) }}"
                                   class="text-blue-600 hover:text-blue-800 font-medium">Ver empresa</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">Nenhuma assinatura encontrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($subscriptions->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $subscriptions->links() }}
            </div>
        @endif
    </div>

</x-layout.app>
