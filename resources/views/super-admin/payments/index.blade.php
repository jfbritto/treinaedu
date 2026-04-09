<x-layout.app title="Super Admin - Pagamentos">

    <div class="mb-6">
        <a href="{{ route('super.dashboard') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar ao dashboard
        </a>
    </div>

    <p class="text-sm text-gray-500 mb-6">Todos os pagamentos registrados na plataforma</p>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-gray-800">Pagamentos</h3>
                    <p class="text-xs text-gray-400">{{ $payments->total() }} {{ $payments->total() === 1 ? 'pagamento' : 'pagamentos' }}</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Empresa</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Valor</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Método</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Data</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-800">{{ $payment->company?->name ?? '—' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-800">R$ {{ number_format($payment->amount, 2, ',', '.') }}</p>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $payment->payment_method ? ucfirst(str_replace('_', ' ', $payment->payment_method)) : '—' }}
                            </td>
                            <td class="px-6 py-4">
                                @php $status = $payment->status; @endphp
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ match($status) {
                                        'confirmed' => 'bg-green-50 text-green-700 border border-green-200',
                                        'pending' => 'bg-amber-50 text-amber-700 border border-amber-200',
                                        'overdue' => 'bg-red-50 text-red-700 border border-red-200',
                                        default => 'bg-gray-50 text-gray-500 border border-gray-200',
                                    } }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ match($status) {
                                        'confirmed' => 'bg-green-500',
                                        'pending' => 'bg-amber-500',
                                        'overdue' => 'bg-red-500',
                                        default => 'bg-gray-400',
                                    } }}"></span>
                                    {{ match($status) {
                                        'confirmed' => 'Confirmado',
                                        'pending' => 'Pendente',
                                        'overdue' => 'Vencido',
                                        'refunded' => 'Estornado',
                                        default => ucfirst($status),
                                    } }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-500 text-xs">
                                {{ $payment->paid_at?->format('d/m/Y') ?? $payment->due_date?->format('d/m/Y') ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <p class="text-gray-500 text-sm font-medium">Nenhum pagamento encontrado</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

</x-layout.app>
