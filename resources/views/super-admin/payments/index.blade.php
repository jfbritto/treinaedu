<x-layout.app title="Super Admin - Pagamentos">

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Pagamentos</h2>
        <a href="{{ route('super.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">
            &larr; Voltar ao Dashboard
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Empresa</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Valor</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Método</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Data</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">
                            {{ $payment->company?->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-gray-800 font-medium">
                            R$ {{ number_format($payment->amount, 2, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $payment->payment_method ? ucfirst(str_replace('_', ' ', $payment->payment_method)) : '—' }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $payment->status === 'confirmed' ? 'bg-green-100 text-green-800' :
                                   ($payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                   ($payment->status === 'overdue' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-600')) }}">
                                {{ match($payment->status) {
                                    'confirmed' => 'Confirmado',
                                    'pending' => 'Pendente',
                                    'overdue' => 'Vencido',
                                    'refunded' => 'Estornado',
                                    default => ucfirst($payment->status),
                                } }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $payment->paid_at?->format('d/m/Y') ?? $payment->due_date?->format('d/m/Y') ?? '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">Nenhum pagamento encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($payments->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

</x-layout.app>
