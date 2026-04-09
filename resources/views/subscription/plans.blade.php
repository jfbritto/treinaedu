<x-layout.app title="Escolha seu Plano">

    <p class="text-sm text-gray-500 mb-6">Selecione o plano ideal para sua empresa</p>

    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div x-data="{ selectedPlan: null, showCardForm: false }" class="space-y-6">

        {{-- Plans grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($plans as $plan)
                @php $isCurrent = $currentSubscription && $currentSubscription->plan_id === $plan->id; @endphp
                <div @click="selectedPlan = {{ $plan->id }}; showCardForm = true"
                     :class="selectedPlan === {{ $plan->id }} ? 'border-primary ring-2 ring-primary/20' : 'border-gray-100 hover:border-gray-300'"
                     class="bg-white rounded-xl shadow-sm overflow-hidden border-2 cursor-pointer transition relative">

                    @if($isCurrent)
                        <div class="text-center text-xs font-semibold py-1.5 uppercase tracking-wide text-white" style="background-color: var(--primary)">
                            Plano Atual
                        </div>
                    @endif

                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-800">{{ $plan->name }}</h3>
                        <div class="mt-2 flex items-baseline gap-1">
                            <span class="text-3xl font-extrabold text-gray-900">R$ {{ number_format($plan->price, 2, ',', '.') }}</span>
                            <span class="text-sm text-gray-400">/mês</span>
                        </div>

                        <ul class="mt-5 space-y-2.5 text-sm text-gray-600">
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ $plan->max_users ? 'Até ' . $plan->max_users . ' usuários' : 'Usuários ilimitados' }}
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ $plan->max_trainings ? 'Até ' . $plan->max_trainings . ' treinamentos' : 'Treinamentos ilimitados' }}
                            </li>
                            @if($plan->features)
                                @foreach($plan->features as $feature)
                                    <li class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        {{ ucfirst($feature) }}
                                    </li>
                                @endforeach
                            @endif
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                Cobrança no cartão de crédito
                            </li>
                        </ul>

                        <div class="mt-5">
                            <span :class="selectedPlan === {{ $plan->id }} ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600'"
                                  class="block text-center py-2.5 rounded-lg text-sm font-semibold transition">
                                <span x-text="selectedPlan === {{ $plan->id }} ? 'Selecionado' : '{{ $isCurrent ? 'Renovar' : 'Selecionar' }}'"></span>
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Card form --}}
        <div x-show="showCardForm" x-cloak x-transition class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-gray-800">Dados do Cartão de Crédito</h3>
                        <p class="text-xs text-gray-400">A cobrança será mensal e automática</p>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span class="text-xs text-green-600 font-medium">Pagamento seguro</span>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('subscription.subscribe') }}" class="p-6">
                @csrf
                <input type="hidden" name="plan_id" :value="selectedPlan">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Card info --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nome no cartão <span class="text-red-500">*</span></label>
                        <input type="text" name="holder_name" value="{{ old('holder_name') }}" required placeholder="Nome como está no cartão"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        @error('holder_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Número do cartão <span class="text-red-500">*</span></label>
                        <input type="text" name="card_number" value="{{ old('card_number') }}" required placeholder="0000 0000 0000 0000" maxlength="19"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        @error('card_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Validade (mês) <span class="text-red-500">*</span></label>
                        <input type="text" name="expiry_month" value="{{ old('expiry_month') }}" required placeholder="MM" maxlength="2"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Validade (ano) <span class="text-red-500">*</span></label>
                        <input type="text" name="expiry_year" value="{{ old('expiry_year') }}" required placeholder="AAAA" maxlength="4"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">CVV <span class="text-red-500">*</span></label>
                        <input type="text" name="ccv" value="{{ old('ccv') }}" required placeholder="123" maxlength="4"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>

                    {{-- Separator --}}
                    <div class="md:col-span-2 border-t border-gray-100 pt-5">
                        <p class="text-sm font-medium text-gray-700 mb-3">Dados do titular</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">CPF/CNPJ <span class="text-red-500">*</span></label>
                        <input type="text" name="cpf_cnpj" value="{{ old('cpf_cnpj') }}" required placeholder="000.000.000-00"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Telefone <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="(11) 99999-9999"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">CEP <span class="text-red-500">*</span></label>
                        <input type="text" name="postal_code" value="{{ old('postal_code') }}" required placeholder="00000-000" maxlength="9"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Número do endereço <span class="text-red-500">*</span></label>
                        <input type="text" name="address_number" value="{{ old('address_number') }}" required placeholder="123"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-4">
                    <button type="submit"
                        class="inline-flex items-center gap-2 bg-primary hover:bg-secondary text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Assinar com Cartão de Crédito
                    </button>
                    <button type="button" @click="showCardForm = false; selectedPlan = null"
                        class="text-sm text-gray-500 hover:text-gray-700 transition">Cancelar</button>
                </div>
            </form>
        </div>

    </div>

</x-layout.app>
