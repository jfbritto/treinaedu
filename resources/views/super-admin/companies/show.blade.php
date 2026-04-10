<x-layout.app title="Super Admin - {{ $company->name }}">

    <div class="mb-6">
        <a href="{{ route('super.companies.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar às empresas
        </a>
    </div>

    {{-- Hero --}}
    <div class="rounded-xl p-6 mb-6 text-white relative overflow-hidden"
         style="background: linear-gradient(135deg, {{ $company->primary_color ?? '#4f46e5' }}, {{ $company->secondary_color ?? '#3730a3' }})">
        <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-white/10"></div>
        <div class="relative flex items-start gap-4">
            <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center flex-shrink-0">
                @if($company->logo_path)
                    <img src="{{ Storage::disk('public')->url($company->logo_path) }}" alt="{{ $company->name }}" class="h-8 object-contain">
                @else
                    <span class="text-lg font-bold text-white">{{ strtoupper(substr($company->name, 0, 2)) }}</span>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs text-white/70 uppercase tracking-wider mb-1">Empresa</p>
                <h1 class="text-2xl font-bold break-words">{{ $company->name }}</h1>
                <p class="text-sm text-white/80 mt-1">{{ $company->slug }} · Cadastrada em {{ $company->created_at->format('d/m/Y') }}</p>
            </div>
            @php $status = $company->subscription?->status ?? 'none'; @endphp
            <div class="flex items-center gap-2 flex-shrink-0">
                <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full bg-white/20 backdrop-blur">
                    <span class="w-1.5 h-1.5 rounded-full {{ match($status) { 'active' => 'bg-green-300', 'trial' => 'bg-amber-300', 'past_due' => 'bg-red-300', default => 'bg-gray-300' } }}"></span>
                    {{ match($status) { 'active' => 'Ativa', 'trial' => 'Trial', 'past_due' => 'Em Atraso', 'cancelled' => 'Cancelada', 'expired' => 'Expirada', default => 'Sem assinatura' } }}
                </span>
                @if($company->subscription)
                    <form method="POST" action="{{ route('super.companies.toggle-subscription', $company) }}"
                          data-confirm="{{ in_array($status, ['active', 'trial', 'past_due']) ? 'Suspender esta empresa? Todos os usuários perderão acesso.' : 'Reativar esta empresa por mais 30 dias?' }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1 text-xs font-medium px-2.5 py-1 rounded-full transition {{ in_array($status, ['active', 'trial', 'past_due']) ? 'bg-red-500/20 hover:bg-red-500/40 text-white' : 'bg-green-500/20 hover:bg-green-500/40 text-white' }}">
                            {{ in_array($status, ['active', 'trial', 'past_due']) ? 'Suspender' : 'Reativar' }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Company stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Usuários</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $companyStats['users'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Treinamentos</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $companyStats['trainings'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                </div>
            </div>
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Certificados</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $companyStats['certificates'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Company Details --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-gray-800">Dados da Empresa</h3>
                        <p class="text-xs text-gray-400">Informações de cadastro</p>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-3">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Nome</span>
                    <span class="font-medium text-gray-800">{{ $company->name }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Slug</span>
                    <span class="font-medium text-gray-800">{{ $company->slug }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Cor Primária</span>
                    <span class="flex items-center gap-2">
                        @if($company->primary_color)
                            <span class="w-4 h-4 rounded-full border" style="background-color: {{ $company->primary_color }}"></span>
                            <span class="font-mono text-xs text-gray-600">{{ $company->primary_color }}</span>
                        @else
                            <span class="text-gray-400 text-xs">Padrão</span>
                        @endif
                    </span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Cor Secundária</span>
                    <span class="flex items-center gap-2">
                        @if($company->secondary_color)
                            <span class="w-4 h-4 rounded-full border" style="background-color: {{ $company->secondary_color }}"></span>
                            <span class="font-mono text-xs text-gray-600">{{ $company->secondary_color }}</span>
                        @else
                            <span class="text-gray-400 text-xs">Padrão</span>
                        @endif
                    </span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Cadastrada em</span>
                    <span class="font-medium text-gray-800">{{ $company->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>

        {{-- Subscription --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-gray-800">Assinatura</h3>
                        <p class="text-xs text-gray-400">Plano e período</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                @if($company->subscription)
                    @php $sub = $company->subscription; @endphp
                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Plano</span>
                            <span class="font-semibold text-gray-800">{{ $sub->plan?->name ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Status</span>
                            <span class="inline-flex items-center gap-1 font-medium {{ match($sub->status) { 'active' => 'text-green-600', 'trial' => 'text-amber-600', 'past_due' => 'text-red-600', default => 'text-gray-400' } }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ match($sub->status) { 'active' => 'bg-green-500', 'trial' => 'bg-amber-500', 'past_due' => 'bg-red-500', default => 'bg-gray-300' } }}"></span>
                                {{ match($sub->status) { 'active' => 'Ativa', 'trial' => 'Trial', 'past_due' => 'Em Atraso', 'cancelled' => 'Cancelada', default => ucfirst($sub->status) } }}
                            </span>
                        </div>
                        @if($sub->trial_ends_at)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Trial até</span>
                                <span class="font-medium text-gray-800">{{ $sub->trial_ends_at->format('d/m/Y') }}</span>
                            </div>
                        @endif
                        @if($sub->starts_at)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Início</span>
                                <span class="font-medium text-gray-800">{{ $sub->starts_at->format('d/m/Y') }}</span>
                            </div>
                        @endif
                        @if($sub->ends_at)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Vencimento</span>
                                <span class="font-medium text-gray-800">{{ $sub->ends_at->format('d/m/Y') }}</span>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-4">
                        <p class="text-sm text-gray-400">Nenhuma assinatura encontrada</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Custom Plan --}}
    @php $customPlan = \App\Models\Plan::where('company_id', $company->id)->first(); @endphp
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6" x-data="{ showForm: false }">
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-gray-800">Plano Personalizado</h3>
                    <p class="text-xs text-gray-400">Plano exclusivo negociado para esta empresa</p>
                </div>
                @if(!$customPlan)
                    <button @click="showForm = !showForm" type="button" class="text-xs font-medium text-amber-600 hover:text-amber-700 transition">
                        <span x-text="showForm ? 'Cancelar' : '+ Criar plano'"></span>
                    </button>
                @endif
            </div>
        </div>

        @if($customPlan)
            <div class="p-6">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="p-3 rounded-lg bg-gray-50">
                        <p class="text-xs text-gray-400 mb-0.5">Nome</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $customPlan->name }}</p>
                    </div>
                    <div class="p-3 rounded-lg bg-gray-50">
                        <p class="text-xs text-gray-400 mb-0.5">Preço</p>
                        <p class="text-sm font-semibold text-gray-800">R$ {{ number_format($customPlan->price, 2, ',', '.') }}/mês</p>
                    </div>
                    <div class="p-3 rounded-lg bg-gray-50">
                        <p class="text-xs text-gray-400 mb-0.5">Max Usuários</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $customPlan->max_users ?? 'Ilimitado' }}</p>
                    </div>
                    <div class="p-3 rounded-lg bg-gray-50">
                        <p class="text-xs text-gray-400 mb-0.5">Max Treinamentos</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $customPlan->max_trainings ?? 'Ilimitado' }}</p>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-3">
                    <a href="{{ route('super.plans.edit', $customPlan) }}" class="text-xs font-medium text-primary hover:text-secondary transition">Editar plano</a>
                    <form method="POST" action="{{ route('super.plans.destroy', $customPlan) }}" data-confirm="Remover plano personalizado desta empresa?">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs font-medium text-red-500 hover:text-red-700 transition">Remover</button>
                    </form>
                </div>
            </div>
        @else
            <div x-show="!showForm" class="p-6 text-center">
                <p class="text-sm text-gray-400">Nenhum plano personalizado para esta empresa.</p>
            </div>

            <form x-show="showForm" x-cloak method="POST" action="{{ route('super.plans.store') }}" class="p-6">
                @csrf
                <input type="hidden" name="company_id" value="{{ $company->id }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Nome do plano</label>
                        <input type="text" name="name" required placeholder="Ex: Enterprise {{ $company->name }}" value="Enterprise {{ $company->name }}"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Preço mensal (R$)</label>
                        <input type="number" name="price" required min="0" step="0.01" placeholder="2500.00"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Max Usuários <span class="text-gray-300">(vazio = ilimitado)</span></label>
                        <input type="number" name="max_users" min="1" placeholder="500"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Max Treinamentos <span class="text-gray-300">(vazio = ilimitado)</span></label>
                        <input type="number" name="max_trainings" min="1" placeholder="Ilimitado"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                </div>
                <button type="submit" class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Criar plano personalizado
                </button>
            </form>
        @endif
    </div>

    {{-- Users Table --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-gray-800">Usuários</h3>
                    <p class="text-xs text-gray-400">{{ $company->users->count() }} {{ $company->users->count() === 1 ? 'usuário' : 'usuários' }} na empresa</p>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nome</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">E-mail</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Função</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($company->users as $user)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-semibold text-gray-800">{{ $user->name }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $user->role === 'admin' ? 'bg-blue-50 text-blue-700' : ($user->role === 'instructor' ? 'bg-purple-50 text-purple-700' : 'bg-gray-50 text-gray-600') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1 text-xs font-medium {{ $user->active ? 'text-green-600' : 'text-gray-400' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $user->active ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                                    {{ $user->active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-400 text-sm">Nenhum usuário encontrado</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-layout.app>
