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
         style="background: linear-gradient(135deg, {{ $company->primary_color ?? '#3B82F6' }}, {{ $company->secondary_color ?? '#1E40AF' }})">
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
            <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full bg-white/20 backdrop-blur flex-shrink-0">
                <span class="w-1.5 h-1.5 rounded-full {{ match($status) { 'active' => 'bg-green-300', 'trial' => 'bg-amber-300', 'past_due' => 'bg-red-300', default => 'bg-gray-300' } }}"></span>
                {{ match($status) { 'active' => 'Ativa', 'trial' => 'Trial', 'past_due' => 'Em Atraso', 'cancelled' => 'Cancelada', default => 'Sem assinatura' } }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Company Details --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
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
