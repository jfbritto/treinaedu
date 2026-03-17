<x-layout.app title="Super Admin - {{ $company->name }}">

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">{{ $company->name }}</h2>
        <a href="{{ route('super.companies.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
            &larr; Voltar às Empresas
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        {{-- Company Details --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Dados da Empresa</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Nome</dt>
                    <dd class="font-medium text-gray-800">{{ $company->name }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Slug</dt>
                    <dd class="font-medium text-gray-800">{{ $company->slug }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Cor Primária</dt>
                    <dd class="flex items-center gap-2">
                        @if($company->primary_color)
                            <span class="w-4 h-4 rounded-full border" style="background-color: {{ $company->primary_color }}"></span>
                            <span class="font-medium text-gray-800">{{ $company->primary_color }}</span>
                        @else
                            <span class="text-gray-400">Não definida</span>
                        @endif
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Cor Secundária</dt>
                    <dd class="flex items-center gap-2">
                        @if($company->secondary_color)
                            <span class="w-4 h-4 rounded-full border" style="background-color: {{ $company->secondary_color }}"></span>
                            <span class="font-medium text-gray-800">{{ $company->secondary_color }}</span>
                        @else
                            <span class="text-gray-400">Não definida</span>
                        @endif
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Cadastrada em</dt>
                    <dd class="font-medium text-gray-800">{{ $company->created_at->format('d/m/Y H:i') }}</dd>
                </div>
            </dl>
        </div>

        {{-- Subscription Info --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Assinatura</h3>
            @if($company->subscription)
                @php $sub = $company->subscription; @endphp
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Plano</dt>
                        <dd class="font-medium text-gray-800">{{ $sub->plan?->name ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Status</dt>
                        <dd>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $sub->status === 'active' ? 'bg-green-100 text-green-800' :
                                   ($sub->status === 'trial' ? 'bg-yellow-100 text-yellow-800' :
                                   ($sub->status === 'past_due' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-600')) }}">
                                {{ match($sub->status) {
                                    'active' => 'Ativa',
                                    'trial' => 'Trial',
                                    'past_due' => 'Em Atraso',
                                    'cancelled' => 'Cancelada',
                                    default => ucfirst($sub->status),
                                } }}
                            </span>
                        </dd>
                    </div>
                    @if($sub->trial_ends_at)
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Trial até</dt>
                            <dd class="font-medium text-gray-800">{{ $sub->trial_ends_at->format('d/m/Y') }}</dd>
                        </div>
                    @endif
                    @if($sub->starts_at)
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Início</dt>
                            <dd class="font-medium text-gray-800">{{ $sub->starts_at->format('d/m/Y') }}</dd>
                        </div>
                    @endif
                    @if($sub->ends_at)
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Vencimento</dt>
                            <dd class="font-medium text-gray-800">{{ $sub->ends_at->format('d/m/Y') }}</dd>
                        </div>
                    @endif
                </dl>
            @else
                <p class="text-sm text-gray-500">Nenhuma assinatura encontrada.</p>
            @endif
        </div>
    </div>

    {{-- Users Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-800">Usuários ({{ $company->users->count() }})</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Nome</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">E-mail</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Função</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Ativo</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($company->users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ ucfirst($user->role) }}</td>
                        <td class="px-4 py-3">
                            @if($user->active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Sim</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Não</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">Nenhum usuário encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</x-layout.app>
