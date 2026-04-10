<x-layout.app title="Super Admin - Dashboard">

    <p class="text-sm text-gray-500 mb-6">Visão geral da plataforma TreinaEdu</p>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Empresas</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $metrics['total_companies'] }}</p>
            <p class="text-xs text-gray-400 mt-0.5">cadastradas na plataforma</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Assinaturas Ativas</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $metrics['active_subscriptions'] }}</p>
            <p class="text-xs text-gray-400 mt-0.5">planos pagantes</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Receita do Mês</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">R$ {{ number_format($metrics['monthly_revenue'], 2, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-0.5">pagamentos confirmados</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Em Trial</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $metrics['trial_companies'] }}</p>
            <p class="text-xs text-gray-400 mt-0.5">empresas em período de teste</p>
        </div>
    </div>

    {{-- Quick Links --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-gray-800">Acesso Rápido</h3>
                    <p class="text-xs text-gray-400">Gerencie os recursos da plataforma</p>
                </div>
            </div>
        </div>

        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('super.companies.index') }}"
               class="border border-gray-100 rounded-xl p-5 hover:shadow-md hover:border-blue-200 transition group">
                <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center mb-3 group-hover:bg-blue-100 transition">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-800 group-hover:text-blue-600 transition">Empresas</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $metrics['total_companies'] }} cadastradas</p>
            </a>

            <a href="{{ route('super.subscriptions.index') }}"
               class="border border-gray-100 rounded-xl p-5 hover:shadow-md hover:border-green-200 transition group">
                <div class="w-11 h-11 rounded-xl bg-green-50 flex items-center justify-center mb-3 group-hover:bg-green-100 transition">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-800 group-hover:text-green-600 transition">Assinaturas</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $metrics['active_subscriptions'] }} ativas</p>
            </a>

            <a href="{{ route('super.payments.index') }}"
               class="border border-gray-100 rounded-xl p-5 hover:shadow-md hover:border-amber-200 transition group">
                <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center mb-3 group-hover:bg-amber-100 transition">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-800 group-hover:text-amber-600 transition">Pagamentos</p>
                <p class="text-xs text-gray-400 mt-0.5">Receitas e cobranças</p>
            </a>

            <a href="{{ route('super.plans.index') }}"
               class="border border-gray-100 rounded-xl p-5 hover:shadow-md hover:border-purple-200 transition group">
                <div class="w-11 h-11 rounded-xl bg-purple-50 flex items-center justify-center mb-3 group-hover:bg-purple-100 transition">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-800 group-hover:text-purple-600 transition">Planos</p>
                <p class="text-xs text-gray-400 mt-0.5">Configurar ofertas</p>
            </a>
        </div>
    </div>

</x-layout.app>
