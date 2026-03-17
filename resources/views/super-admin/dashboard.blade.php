<x-layout.app title="Super Admin - Dashboard">

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Painel Super Admin</h2>
    </div>

    {{-- Metric Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500 mb-1">Total de Empresas</p>
            <p class="text-3xl font-bold text-gray-800">{{ $metrics['total_companies'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500 mb-1">Assinaturas Ativas</p>
            <p class="text-3xl font-bold text-green-600">{{ $metrics['active_subscriptions'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500 mb-1">Receita do Mês</p>
            <p class="text-3xl font-bold text-blue-600">R$ {{ number_format($metrics['monthly_revenue'], 2, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500 mb-1">Empresas em Trial</p>
            <p class="text-3xl font-bold text-yellow-600">{{ $metrics['trial_companies'] }}</p>
        </div>
    </div>

    {{-- Quick Links --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('super.companies.index') }}"
           class="bg-white rounded-lg shadow p-5 hover:shadow-md transition-shadow flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <p class="font-medium text-gray-800">Empresas</p>
                <p class="text-xs text-gray-500">Gerenciar empresas</p>
            </div>
        </a>

        <a href="{{ route('super.subscriptions.index') }}"
           class="bg-white rounded-lg shadow p-5 hover:shadow-md transition-shadow flex items-center gap-3">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="font-medium text-gray-800">Assinaturas</p>
                <p class="text-xs text-gray-500">Ver assinaturas</p>
            </div>
        </a>

        <a href="{{ route('super.payments.index') }}"
           class="bg-white rounded-lg shadow p-5 hover:shadow-md transition-shadow flex items-center gap-3">
            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <p class="font-medium text-gray-800">Pagamentos</p>
                <p class="text-xs text-gray-500">Ver pagamentos</p>
            </div>
        </a>

        <a href="{{ route('super.plans.index') }}"
           class="bg-white rounded-lg shadow p-5 hover:shadow-md transition-shadow flex items-center gap-3">
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <p class="font-medium text-gray-800">Planos</p>
                <p class="text-xs text-gray-500">Gerenciar planos</p>
            </div>
        </a>
    </div>

</x-layout.app>
