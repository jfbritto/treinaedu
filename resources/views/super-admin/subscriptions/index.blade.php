<x-layout.app title="Super Admin - Assinaturas">

    <div class="mb-6">
        <a href="{{ route('super.dashboard') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar ao dashboard
        </a>
    </div>

    <p class="text-sm text-gray-500 mb-6">Todas as assinaturas registradas na plataforma</p>

    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Total</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Ativas</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['active'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Trial</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['trial'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Em Atraso</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['past_due'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-gray-800">Assinaturas</h3>
                    <p class="text-xs text-gray-400">{{ $subscriptions->total() }} {{ $subscriptions->total() === 1 ? 'assinatura' : 'assinaturas' }}</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Empresa</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Plano</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Período</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Valor</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Ação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($subscriptions as $subscription)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-800">{{ $subscription->company?->name ?? '—' }}</p>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                @if($subscription->status === 'trial')
                                    <span class="text-gray-400 text-xs">Em avaliação</span>
                                @else
                                    {{ $subscription->plan?->name ?? '—' }}
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php $status = $subscription->status; @endphp
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ match($status) {
                                        'active' => 'bg-green-50 text-green-700 border border-green-200',
                                        'trial' => 'bg-amber-50 text-amber-700 border border-amber-200',
                                        'past_due' => 'bg-red-50 text-red-700 border border-red-200',
                                        default => 'bg-gray-50 text-gray-500 border border-gray-200',
                                    } }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ match($status) {
                                        'active' => 'bg-green-500',
                                        'trial' => 'bg-amber-500',
                                        'past_due' => 'bg-red-500',
                                        default => 'bg-gray-400',
                                    } }}"></span>
                                    {{ match($status) {
                                        'active' => 'Ativa',
                                        'trial' => 'Trial',
                                        'past_due' => 'Em Atraso',
                                        'cancelled' => 'Cancelada',
                                        default => ucfirst($status),
                                    } }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500">
                                @if($subscription->status === 'trial' && $subscription->trial_ends_at)
                                    Trial até {{ $subscription->trial_ends_at->format('d/m/Y') }}
                                @elseif($subscription->current_period_start)
                                    {{ $subscription->current_period_start->format('d/m/Y') }}
                                    @if($subscription->current_period_end)
                                        — {{ $subscription->current_period_end->format('d/m/Y') }}
                                    @endif
                                @else
                                    Desde {{ $subscription->created_at->format('d/m/Y') }}
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600 text-xs font-medium">
                                @if($subscription->status !== 'trial' && $subscription->plan?->price)
                                    R$ {{ number_format($subscription->plan->price, 2, ',', '.') }}/mês
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($subscription->company)
                                    <a href="{{ route('super.companies.show', $subscription->company) }}"
                                       class="text-xs font-medium text-primary hover:text-secondary transition">Ver empresa</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <p class="text-gray-500 text-sm font-medium">Nenhuma assinatura encontrada</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($subscriptions->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $subscriptions->links() }}
            </div>
        @endif
    </div>

</x-layout.app>
