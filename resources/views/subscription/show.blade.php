<x-layout.app title="Minha Assinatura">

    <p class="text-sm text-gray-500 mb-6">Gerencie seu plano, acompanhe o uso e veja o histórico de cobranças</p>

    @if($subscription)
        @php
            $statusColors = [
                'active' => 'bg-green-300', 'trial' => 'bg-blue-300',
                'past_due' => 'bg-amber-300', 'cancelled' => 'bg-red-300', 'expired' => 'bg-gray-300',
            ];
            $statusLabels = [
                'active' => 'Ativa', 'trial' => 'Período de Teste',
                'past_due' => 'Em Atraso', 'cancelled' => 'Cancelada', 'expired' => 'Expirada',
            ];
            $featureLabels = [
                'certificates' => 'Certificados em PDF', 'basic_reports' => 'Relatórios básicos',
                'ai_quiz' => 'Quiz com IA', 'learning_paths' => 'Trilhas de aprendizagem',
                'export_reports' => 'Exportação PDF e Excel', 'engagement' => 'Engajamento e desafios',
            ];
        @endphp

        {{-- Hero do plano --}}
        <div class="rounded-xl p-6 mb-6 text-white relative overflow-hidden"
             style="background: linear-gradient(135deg, var(--secondary), var(--primary))">
            <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-16 -left-16 w-56 h-56 rounded-full bg-white/5"></div>

            <div class="relative flex items-start justify-between gap-4 flex-wrap">
                <div class="flex items-start gap-4 flex-1 min-w-0">
                    <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center flex-shrink-0">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        @if(auth()->user()->company->isOnTrial())
                            <div class="flex items-center gap-2 mb-1">
                                <p class="text-xs text-white/70 uppercase tracking-wider">Período de teste</p>
                                <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full bg-white/20 backdrop-blur">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-300"></span>
                                    Acesso completo
                                </span>
                            </div>
                            <h1 class="text-2xl font-bold mb-1">Trial Gratuito</h1>
                            <p class="text-sm text-white/80">
                                Todos os recursos liberados · Expira em <strong>{{ $subscription->trial_ends_at->format('d/m/Y') }}</strong>
                                <span class="text-white/60">({{ $subscription->trial_ends_at->diffForHumans() }})</span>
                            </p>
                        @else
                            <div class="flex items-center gap-2 mb-1">
                                <p class="text-xs text-white/70 uppercase tracking-wider">Seu plano</p>
                                <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full bg-white/20 backdrop-blur">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $statusColors[$subscription->status] ?? 'bg-gray-300' }}"></span>
                                    {{ $statusLabels[$subscription->status] ?? ucfirst($subscription->status) }}
                                </span>
                            </div>
                            <h1 class="text-2xl font-bold mb-1">{{ $plan?->name ?? 'Sem plano' }}</h1>
                            <p class="text-sm text-white/80">
                                <span class="text-xl font-bold">R$ {{ number_format($plan?->price ?? 0, 2, ',', '.') }}</span>/mês
                                @if($subscription->current_period_end)
                                    · Próxima cobrança em {{ $subscription->current_period_end->format('d/m/Y') }}
                                @endif
                            </p>
                        @endif
                    </div>
                </div>
                <a href="{{ route('subscription.plans') }}"
                   class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 backdrop-blur text-white px-4 py-2 rounded-lg text-sm font-semibold transition flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    {{ auth()->user()->company->isOnTrial() ? 'Escolher plano' : 'Alterar plano' }}
                </a>
            </div>
        </div>

        {{-- Usage stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-semibold {{ $usage['users_pct'] >= 90 ? 'text-red-500' : ($usage['users_pct'] >= 70 ? 'text-amber-500' : 'text-gray-400') }}">
                        {{ $usage['users_pct'] }}%
                    </span>
                </div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Usuários</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $usage['users'] }} <span class="text-sm font-normal text-gray-400">/ {{ $usage['users_limit'] ?? '∞' }}</span></p>
                @if($usage['users_limit'])
                    <div class="w-full bg-gray-100 rounded-full h-1.5 mt-2">
                        <div class="h-1.5 rounded-full transition-all {{ $usage['users_pct'] >= 90 ? 'bg-red-500' : ($usage['users_pct'] >= 70 ? 'bg-amber-500' : 'bg-primary') }}" style="width: {{ $usage['users_pct'] }}%"></div>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    @if($usage['trainings_limit'])
                        <span class="text-xs font-semibold {{ $usage['trainings_pct'] >= 90 ? 'text-red-500' : ($usage['trainings_pct'] >= 70 ? 'text-amber-500' : 'text-gray-400') }}">
                            {{ $usage['trainings_pct'] }}%
                        </span>
                    @endif
                </div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Treinamentos</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $usage['trainings'] }} <span class="text-sm font-normal text-gray-400">/ {{ $usage['trainings_limit'] ?? '∞' }}</span></p>
                @if($usage['trainings_limit'])
                    <div class="w-full bg-gray-100 rounded-full h-1.5 mt-2">
                        <div class="h-1.5 rounded-full transition-all {{ $usage['trainings_pct'] >= 90 ? 'bg-red-500' : ($usage['trainings_pct'] >= 70 ? 'bg-amber-500' : 'bg-primary') }}" style="width: {{ $usage['trainings_pct'] }}%"></div>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Certificados</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $usage['certificates'] }}</p>
                <p class="text-xs text-gray-400 mt-0.5">emitidos no total</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            {{-- Features do plano --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-gray-800">{{ auth()->user()->company->isOnTrial() ? 'Recursos disponíveis' : 'Recursos do plano' }}</h3>
                            <p class="text-xs text-gray-400">{{ auth()->user()->company->isOnTrial() ? 'Tudo liberado durante o trial' : 'O que está incluso' }}</p>
                        </div>
                    </div>
                </div>
                <div class="p-5 space-y-3">
                    @foreach($featureLabels as $key => $label)
                        @php $has = auth()->user()->company->isOnTrial() || ($plan?->hasFeature($key) ?? false); @endphp
                        <div class="flex items-center gap-2.5 text-sm {{ $has ? 'text-gray-700' : 'text-gray-300' }}">
                            @if($has)
                                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            @else
                                <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            @endif
                            {{ $label }}
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Detalhes da assinatura / CTA trial --}}
            @if(auth()->user()->company->isOnTrial())
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm overflow-hidden flex flex-col items-center justify-center p-8 text-center">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-5" style="background: linear-gradient(135deg, var(--primary), var(--secondary))">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">Seu trial expira em {{ $subscription->trial_ends_at->diffForHumans() }}</h3>
                <p class="text-sm text-gray-500 mb-6 max-w-sm">Escolha um plano antes do fim do período de teste para não perder acesso à plataforma.</p>
                <a href="{{ route('subscription.plans') }}"
                   class="inline-flex items-center gap-2 text-white font-semibold px-6 py-2.5 rounded-lg text-sm transition shadow-sm hover:shadow-md"
                   style="background: linear-gradient(135deg, var(--primary), var(--secondary))">
                    Escolher plano
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            @else
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-gray-800">Detalhes da cobrança</h3>
                            <p class="text-xs text-gray-400">Informações do pagamento recorrente</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50">
                            <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <dt class="text-xs text-gray-400 mb-0.5">Meio de pagamento</dt>
                                <dd class="text-sm font-semibold text-gray-800">Cartão de crédito</dd>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50">
                            <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <dt class="text-xs text-gray-400 mb-0.5">Valor mensal</dt>
                                <dd class="text-sm font-semibold text-gray-800">R$ {{ number_format($plan?->price ?? 0, 2, ',', '.') }}</dd>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50">
                            <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <dt class="text-xs text-gray-400 mb-0.5">Próxima cobrança</dt>
                                <dd class="text-sm font-semibold text-gray-800">
                                    {{ $subscription->current_period_end?->format('d/m/Y') ?? ($subscription->trial_ends_at?->format('d/m/Y') ?? '—') }}
                                </dd>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50">
                            <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <dt class="text-xs text-gray-400 mb-0.5">Status</dt>
                                <dd class="text-sm font-semibold {{ match($subscription->status) { 'active' => 'text-green-600', 'trial' => 'text-blue-600', 'past_due' => 'text-amber-600', default => 'text-gray-600' } }}">
                                    {{ $statusLabels[$subscription->status] ?? ucfirst($subscription->status) }}
                                </dd>
                            </div>
                        </div>
                    </dl>

                    @if($subscription->status !== 'cancelled')
                        <div class="mt-4 pt-4 border-t border-gray-100"
                             x-data="{
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
                             }">
                            <button @click="showCardForm = !showCardForm" type="button"
                                class="inline-flex items-center gap-1.5 text-xs font-medium text-primary hover:text-secondary transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                <span x-text="showCardForm ? 'Cancelar troca' : 'Trocar cartão de crédito'"></span>
                            </button>

                            <form x-show="showCardForm" x-cloak x-transition method="POST" action="{{ route('subscription.update-card') }}" class="mt-4">
                                @csrf
                                @method('PUT')

                                {{-- Card info --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Número do cartão</label>
                                        <div class="relative">
                                            <input type="text" name="card_number" required
                                                x-model="cardNumber" @input="cardNumber = maskCard($event.target.value)"
                                                placeholder="0000 0000 0000 0000" maxlength="19"
                                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm tracking-widest focus:outline-none focus:ring-2 focus:ring-primary pr-20"
                                                autocomplete="cc-number" inputmode="numeric">
                                            <span x-show="cardBrand()" x-text="cardBrand()" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-primary bg-primary/10 px-2 py-0.5 rounded"></span>
                                        </div>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Nome no cartão</label>
                                        <input type="text" name="holder_name" required placeholder="Nome como está no cartão"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                                            autocomplete="cc-name">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Validade</label>
                                        <div class="flex gap-2">
                                            <input type="text" name="expiry_month" required placeholder="MM" maxlength="2"
                                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm text-center focus:outline-none focus:ring-2 focus:ring-primary"
                                                autocomplete="cc-exp-month" inputmode="numeric">
                                            <span class="flex items-center text-gray-300">/</span>
                                            <input type="text" name="expiry_year" required placeholder="AAAA" maxlength="4"
                                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm text-center focus:outline-none focus:ring-2 focus:ring-primary"
                                                autocomplete="cc-exp-year" inputmode="numeric">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">CVV</label>
                                        <div class="relative">
                                            <input type="password" name="ccv" required placeholder="•••" maxlength="4"
                                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                                                autocomplete="cc-csc" inputmode="numeric">
                                            <svg class="w-4 h-4 text-gray-300 absolute right-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        </div>
                                    </div>
                                </div>

                                {{-- Holder info --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4 pt-3 border-t border-gray-100">
                                    <p class="sm:col-span-2 text-xs font-medium text-gray-500">Dados do titular</p>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">CPF/CNPJ</label>
                                        <input type="text" name="cpf_cnpj" required
                                            x-model="cpf" @input="cpf = maskCpf($event.target.value)"
                                            placeholder="000.000.000-00" maxlength="18"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary" inputmode="numeric">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Telefone</label>
                                        <input type="text" name="phone" required
                                            x-model="phone" @input="phone = maskPhone($event.target.value)"
                                            placeholder="(11) 99999-9999" maxlength="15"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary" inputmode="numeric">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">CEP</label>
                                        <input type="text" name="postal_code" required
                                            x-model="cep" @input="cep = maskCep($event.target.value)"
                                            placeholder="00000-000" maxlength="9"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary" inputmode="numeric">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Nº endereço</label>
                                        <input type="text" name="address_number" required placeholder="123" maxlength="10"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    <button type="submit" class="inline-flex items-center gap-2 bg-primary hover:bg-secondary text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Salvar novo cartão
                                    </button>
                                    <button type="button" @click="showCardForm = false" class="text-sm text-gray-500 hover:text-gray-700 transition">Cancelar</button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

    @else
        <div class="bg-white rounded-xl shadow-sm p-12 text-center mb-6">
            <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
            <p class="text-gray-500 text-sm font-medium mb-1">Nenhuma assinatura ativa</p>
            <p class="text-xs text-gray-400 mb-4">Escolha um plano para desbloquear todos os recursos.</p>
            <a href="{{ route('subscription.plans') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:text-secondary transition">
                Escolher plano
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    @endif

    {{-- Payment History --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-gray-800">Histórico de Cobranças</h3>
                    <p class="text-xs text-gray-400">Pagamentos processados na sua assinatura</p>
                </div>
            </div>
        </div>

        @if($payments instanceof \Illuminate\Pagination\LengthAwarePaginator ? $payments->count() > 0 : $payments->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Data</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Valor</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Vencimento</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($payments as $payment)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-gray-700">{{ $payment->paid_at?->format('d/m/Y') ?? '—' }}</td>
                                <td class="px-6 py-4 font-semibold text-gray-800">R$ {{ number_format($payment->amount, 2, ',', '.') }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $pColors = ['confirmed' => 'bg-green-50 text-green-700 border-green-200', 'overdue' => 'bg-red-50 text-red-700 border-red-200', 'pending' => 'bg-amber-50 text-amber-700 border-amber-200'];
                                        $pLabels = ['confirmed' => 'Confirmado', 'overdue' => 'Vencido', 'pending' => 'Pendente'];
                                    @endphp
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium border {{ $pColors[$payment->status] ?? 'bg-gray-50 text-gray-600 border-gray-200' }}">
                                        {{ $pLabels[$payment->status] ?? ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500">{{ $payment->due_date?->format('d/m/Y') ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($payments instanceof \Illuminate\Pagination\LengthAwarePaginator && $payments->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">{{ $payments->links() }}</div>
            @endif
        @else
            <div class="px-6 py-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <p class="text-gray-500 text-sm font-medium mb-1">Nenhuma cobrança registrada</p>
                <p class="text-xs text-gray-400">As cobranças aparecerão aqui após o primeiro pagamento.</p>
            </div>
        @endif
    </div>

    {{-- Cancel --}}
    @if($subscription && $subscription->status !== 'cancelled')
        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Deseja cancelar sua assinatura?</p>
                    <p class="text-xs text-gray-400">Você mantém acesso até o fim do período atual. Dados preservados por 30 dias.</p>
                </div>
                <form id="cancel-subscription-form" method="POST" action="{{ route('subscription.cancel') }}">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="Swal.fire({ icon: 'warning', title: 'Cancelar assinatura?', text: 'Você perderá acesso aos recursos do plano atual ao fim do período. Tem certeza?', showCancelButton: true, confirmButtonText: 'Sim, cancelar', cancelButtonText: 'Manter assinatura', confirmButtonColor: '#DC2626', reverseButtons: true }).then((r) => { if (r.isConfirmed) document.getElementById('cancel-subscription-form').submit(); })"
                        class="text-xs font-medium text-red-500 hover:text-red-700 transition">
                        Cancelar assinatura
                    </button>
                </form>
            </div>
        </div>
    @endif

</x-layout.app>
