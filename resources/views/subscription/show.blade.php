<x-layout.app title="Minha Assinatura">

    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Minha Assinatura</h2>
        <a href="{{ route('subscription.plans') }}"
           class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-dark transition-colors">
            Ver Planos
        </a>
    </div>

    {{-- Current Subscription Info --}}
    @if($subscription)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">
                        Plano {{ $subscription->plan?->name ?? 'N/A' }}
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        @if($subscription->current_period_start && $subscription->current_period_end)
                            Período: {{ $subscription->current_period_start->format('d/m/Y') }}
                            até {{ $subscription->current_period_end->format('d/m/Y') }}
                        @endif
                    </p>
                </div>

                <div>
                    @php
                        $statusColors = [
                            'active'    => 'bg-green-100 text-green-800',
                            'trial'     => 'bg-blue-100 text-blue-800',
                            'past_due'  => 'bg-yellow-100 text-yellow-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                        ];
                        $statusLabels = [
                            'active'    => 'Ativa',
                            'trial'     => 'Período de Teste',
                            'past_due'  => 'Em Atraso',
                            'cancelled' => 'Cancelada',
                        ];
                        $colorClass = $statusColors[$subscription->status] ?? 'bg-gray-100 text-gray-800';
                        $statusLabel = $statusLabels[$subscription->status] ?? ucfirst($subscription->status);
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $colorClass }}">
                        {{ $statusLabel }}
                    </span>
                </div>
            </div>

            @if($subscription->plan)
                <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Valor mensal</p>
                        <p class="font-semibold text-gray-800">R$ {{ number_format($subscription->plan->price, 2, ',', '.') }}</p>
                    </div>
                    @if($subscription->plan->max_users)
                        <div>
                            <p class="text-gray-500">Limite de usuários</p>
                            <p class="font-semibold text-gray-800">{{ $subscription->plan->max_users }}</p>
                        </div>
                    @endif
                    @if($subscription->plan->max_trainings)
                        <div>
                            <p class="text-gray-500">Limite de treinamentos</p>
                            <p class="font-semibold text-gray-800">{{ $subscription->plan->max_trainings }}</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center mb-6">
            <p class="text-gray-500 mb-4">Você ainda não possui uma assinatura ativa.</p>
            <a href="{{ route('subscription.plans') }}"
               class="bg-primary text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-primary-dark transition-colors">
                Escolher um Plano
            </a>
        </div>
    @endif

    {{-- Payment History --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">Histórico de Pagamentos</h3>
        </div>

        @if($payments->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3 text-left">Data</th>
                            <th class="px-6 py-3 text-left">Valor</th>
                            <th class="px-6 py-3 text-left">Método</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Vencimento</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($payments as $payment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-gray-700">
                                    {{ $payment->paid_at ? $payment->paid_at->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-800">
                                    R$ {{ number_format($payment->amount, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-gray-600 capitalize">
                                    {{ str_replace('_', ' ', $payment->payment_method) }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $paymentStatusColors = [
                                            'confirmed' => 'bg-green-100 text-green-700',
                                            'overdue'   => 'bg-red-100 text-red-700',
                                            'pending'   => 'bg-yellow-100 text-yellow-700',
                                        ];
                                        $paymentStatusLabels = [
                                            'confirmed' => 'Confirmado',
                                            'overdue'   => 'Em Atraso',
                                            'pending'   => 'Pendente',
                                        ];
                                        $pColor = $paymentStatusColors[$payment->status] ?? 'bg-gray-100 text-gray-700';
                                        $pLabel = $paymentStatusLabels[$payment->status] ?? ucfirst($payment->status);
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pColor }}">
                                        {{ $pLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ $payment->due_date ? $payment->due_date->format('d/m/Y') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($payments instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $payments->links() }}
                </div>
            @endif
        @else
            <div class="px-6 py-10 text-center text-gray-500">
                Nenhum pagamento registrado até o momento.
            </div>
        @endif
    </div>

</x-layout.app>
