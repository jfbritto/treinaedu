<x-layout.app title="Escolha seu Plano">

    @if(auth()->user()->company->isOnTrial())
        <p class="text-sm text-gray-500 mb-6">Seu trial gratuito está ativo. Escolha um plano para continuar após o período de teste.</p>
    @else
        <p class="text-sm text-gray-500 mb-6">Selecione o plano ideal para sua empresa</p>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div x-data="{
        selectedPlan: null,
        showCardForm: false,
        cardNumber: '',
        cpf: '',
        phone: '',
        cep: '',
        maskCard(v) { return v.replace(/\D/g,'').replace(/(\d{4})(?=\d)/g,'$1 ').substring(0,19); },
        maskCpf(v) { v=v.replace(/\D/g,''); if(v.length<=11) return v.replace(/(\d{3})(\d)/,'$1.$2').replace(/(\d{3})(\d)/,'$1.$2').replace(/(\d{3})(\d{1,2})$/,'$1-$2'); return v.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{1,2})/,'$1.$2.$3/$4-$5'); },
        maskPhone(v) { v=v.replace(/\D/g,''); return v.length<=10 ? v.replace(/(\d{2})(\d)/,'($1) $2').replace(/(\d{4})(\d)/,'$1-$2') : v.replace(/(\d{2})(\d)/,'($1) $2').replace(/(\d{5})(\d)/,'$1-$2'); },
        maskCep(v) { return v.replace(/\D/g,'').replace(/(\d{5})(\d)/,'$1-$2').substring(0,9); },
        cardBrand() { const n=this.cardNumber.replace(/\D/g,''); if(/^4/.test(n)) return 'Visa'; if(/^5[1-5]/.test(n)||/^2[2-7]/.test(n)) return 'Mastercard'; if(/^3[47]/.test(n)) return 'Amex'; if(/^6/.test(n)) return 'Elo'; return ''; }
    }" class="space-y-6">

        {{-- Plans grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($plans as $plan)
                @php
                    $onTrial = auth()->user()->company->isOnTrial();
                    // During trial, no plan is "current" — all are selectable
                    $isCurrent = !$onTrial && $currentSubscription && $currentSubscription->plan_id === $plan->id;
                    $isUpgrade = !$onTrial && $currentSubscription && $plan->price > ($currentSubscription->plan->price ?? 0);
                    $isDowngrade = !$onTrial && $currentSubscription && !$isCurrent && !$isUpgrade;
                    $isSelectable = $onTrial || $isUpgrade;
                @endphp
                <div @if($isSelectable) @click="selectedPlan = {{ $plan->id }}; showCardForm = true" @endif
                     :class="selectedPlan === {{ $plan->id }} ? 'border-primary ring-2 ring-primary/20' : '{{ $isCurrent ? 'border-green-300' : ($isDowngrade ? 'border-gray-100 opacity-60' : 'border-gray-100 hover:border-gray-300') }}'"
                     class="bg-white rounded-xl shadow-sm overflow-hidden border-2 {{ $isSelectable ? 'cursor-pointer' : '' }} transition relative flex flex-col">

                    @if($isCurrent)
                        <div class="text-center text-xs font-semibold py-1.5 uppercase tracking-wide text-white" style="background-color: var(--primary)">
                            Plano Atual
                        </div>
                    @elseif($plan->isCustom())
                        <div class="text-center text-xs font-semibold py-1.5 uppercase tracking-wide text-white bg-gradient-to-r from-amber-500 to-orange-500">
                            Plano Negociado
                        </div>
                    @endif

                    <div class="p-6 flex-1 flex flex-col">
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-bold text-gray-800">{{ $plan->name }}</h3>
                            @if($plan->isCustom())
                                <span class="text-xs font-medium text-amber-700 bg-amber-50 border border-amber-200 px-1.5 py-0.5 rounded">Exclusivo</span>
                            @endif
                        </div>
                        <div class="mt-2 flex items-baseline gap-1">
                            <span class="text-3xl font-extrabold text-gray-900">R$ {{ number_format($plan->price, 2, ',', '.') }}</span>
                            <span class="text-sm text-gray-400">/mês</span>
                        </div>

                        @php
                            $featureLabels = [
                                'certificates' => 'Certificados em PDF',
                                'basic_reports' => 'Relatórios básicos',
                                'ai_quiz' => 'Quiz com IA',
                                'learning_paths' => 'Trilhas de aprendizagem',
                                'export_reports' => 'Exportação PDF e Excel',
                                'engagement' => 'Engajamento e desafios',
                            ];
                        @endphp

                        <ul class="mt-5 space-y-2.5 text-sm text-gray-600">
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <strong>{{ $plan->max_users ? 'Até ' . $plan->max_users . ' usuários' : 'Usuários ilimitados' }}</strong>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                {{ $plan->max_trainings ? 'Até ' . $plan->max_trainings . ' treinamentos' : 'Treinamentos ilimitados' }}
                            </li>
                            @if($plan->features)
                                @foreach($plan->features as $feature)
                                    @if(isset($featureLabels[$feature]))
                                        <li class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            {{ $featureLabels[$feature] }}
                                        </li>
                                    @endif
                                @endforeach
                            @endif
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                Cobrança no cartão de crédito
                            </li>
                        </ul>

                        <div class="mt-auto pt-5">
                            @if($isCurrent)
                                <span class="block text-center py-2.5 rounded-lg text-sm font-semibold bg-green-50 text-green-700 border border-green-200">
                                    Plano atual
                                </span>
                            @elseif($isSelectable)
                                <span :class="selectedPlan === {{ $plan->id }} ? 'text-white shadow-md' : 'text-white hover:opacity-90'"
                                      class="block text-center py-2.5 rounded-lg text-sm font-semibold transition cursor-pointer"
                                      :style="selectedPlan === {{ $plan->id }} ? 'background: linear-gradient(135deg, var(--primary), var(--secondary))' : 'background: linear-gradient(135deg, var(--primary), var(--secondary)); opacity: 0.85'">
                                    <span x-text="selectedPlan === {{ $plan->id }} ? 'Selecionado ✓' : '{{ $onTrial ? 'Assinar este plano' : 'Fazer upgrade' }}'"></span>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Enterprise --}}
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-xl shadow-sm overflow-hidden text-white flex flex-col">
                <div class="p-6 flex-1 flex flex-col">
                    <h3 class="text-lg font-bold">Enterprise</h3>
                    <div class="mt-2">
                        <span class="text-2xl font-extrabold">Sob consulta</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Acima de 200 usuários</p>

                    <ul class="mt-5 space-y-2.5 text-sm text-gray-300">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <strong class="text-white">Usuários ilimitados</strong>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Tudo do Professional
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Preço negociado
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            SLA dedicado
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Gerente de conta
                        </li>
                    </ul>

                    <div class="mt-auto pt-5">
                        <a href="https://wa.me/5528999743099?text=Ol%C3%A1%2C%20tenho%20interesse%20no%20plano%20Enterprise%20do%20TreinaEdu" target="_blank" rel="noopener"
                           class="flex items-center justify-center gap-2 py-2.5 rounded-lg text-sm font-semibold text-white bg-white/10 hover:bg-white/20 border border-white/20 transition">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            Conversar pelo WhatsApp
                        </a>
                    </div>
                </div>
            </div>
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

            <form method="POST" action="{{ route('subscription.subscribe') }}" class="p-6"
                  x-data="{ submitting: false }" @submit="if(submitting) { $event.preventDefault(); return; } submitting = true;">
                @csrf
                <input type="hidden" name="plan_id" :value="selectedPlan">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Card number --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Número do cartão</label>
                        <div class="relative">
                            <input type="text" name="card_number" required
                                x-model="cardNumber" @input="cardNumber = maskCard($event.target.value)"
                                placeholder="0000 0000 0000 0000" maxlength="19"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm tracking-widest focus:outline-none focus:ring-2 focus:ring-primary pr-20"
                                autocomplete="cc-number" inputmode="numeric">
                            <span x-show="cardBrand()" x-text="cardBrand()" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-primary bg-primary/10 px-2 py-0.5 rounded"></span>
                        </div>
                        @error('card_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Holder name --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Nome no cartão</label>
                        <input type="text" name="holder_name" value="{{ old('holder_name') }}" required placeholder="Nome como está no cartão"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                            autocomplete="cc-name">
                        @error('holder_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Expiry --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Validade</label>
                        <div class="flex gap-2">
                            <input type="text" name="expiry_month" value="{{ old('expiry_month') }}" required placeholder="MM" maxlength="2"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm text-center focus:outline-none focus:ring-2 focus:ring-primary"
                                autocomplete="cc-exp-month" inputmode="numeric">
                            <span class="flex items-center text-gray-300">/</span>
                            <input type="text" name="expiry_year" value="{{ old('expiry_year') }}" required placeholder="AAAA" maxlength="4"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm text-center focus:outline-none focus:ring-2 focus:ring-primary"
                                autocomplete="cc-exp-year" inputmode="numeric">
                        </div>
                    </div>

                    {{-- CVV --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">CVV</label>
                        <div class="relative">
                            <input type="password" name="ccv" value="{{ old('ccv') }}" required placeholder="•••" maxlength="4"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                                autocomplete="cc-csc" inputmode="numeric">
                            <svg class="w-4 h-4 text-gray-300 absolute right-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                    </div>

                    {{-- Separator --}}
                    <div class="md:col-span-2 border-t border-gray-100 pt-5">
                        <p class="text-xs font-medium text-gray-500 mb-3">Dados do titular</p>
                    </div>

                    {{-- CPF --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">CPF/CNPJ</label>
                        <input type="text" name="cpf_cnpj" required
                            x-model="cpf" @input="cpf = maskCpf($event.target.value)"
                            placeholder="000.000.000-00" maxlength="18"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                            inputmode="numeric">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Telefone</label>
                        <input type="text" name="phone" required
                            x-model="phone" @input="phone = maskPhone($event.target.value)"
                            placeholder="(11) 99999-9999" maxlength="15"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                            inputmode="numeric">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">CEP</label>
                        <input type="text" name="postal_code" required
                            x-model="cep" @input="cep = maskCep($event.target.value)"
                            placeholder="00000-000" maxlength="9"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                            inputmode="numeric">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Nº endereço</label>
                        <input type="text" name="address_number" value="{{ old('address_number') }}" required placeholder="123"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-4">
                    <button type="submit" :disabled="submitting"
                        :class="submitting ? 'opacity-75 cursor-not-allowed' : ''"
                        class="inline-flex items-center gap-2 bg-primary hover:bg-secondary text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition shadow-sm">
                        <svg x-show="!submitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <svg x-show="submitting" x-cloak class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span x-text="submitting ? 'Processando pagamento...' : 'Assinar com Cartão de Crédito'"></span>
                    </button>
                    <button type="button" x-show="!submitting" @click="showCardForm = false; selectedPlan = null"
                        class="text-sm text-gray-500 hover:text-gray-700 transition">Cancelar</button>
                </div>
            </form>
        </div>

    </div>

</x-layout.app>
