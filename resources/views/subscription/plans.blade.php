<x-layout.app title="Escolha seu Plano">

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Escolha seu Plano</h2>
        <p class="text-gray-500 mt-1">Selecione o plano ideal para sua empresa e a forma de pagamento.</p>
    </div>

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($plans as $plan)
            @php
                $isCurrent = $currentSubscription && $currentSubscription->plan_id === $plan->id;
            @endphp
            <div class="bg-white rounded-xl shadow-md overflow-hidden border-2 {{ $isCurrent ? 'border-primary' : 'border-transparent' }}">
                @if($isCurrent)
                    <div class="bg-primary text-white text-center text-xs font-semibold py-1 uppercase tracking-wide">
                        Plano Atual
                    </div>
                @endif

                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800">{{ $plan->name }}</h3>
                    <div class="mt-3">
                        <span class="text-3xl font-extrabold text-gray-900">R$ {{ number_format($plan->price, 2, ',', '.') }}</span>
                        <span class="text-gray-500 text-sm">/mês</span>
                    </div>

                    <ul class="mt-4 space-y-2 text-sm text-gray-600">
                        @if($plan->max_users)
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Até {{ $plan->max_users }} usuários
                            </li>
                        @else
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Usuários ilimitados
                            </li>
                        @endif

                        @if($plan->max_trainings)
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Até {{ $plan->max_trainings }} treinamentos
                            </li>
                        @else
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Treinamentos ilimitados
                            </li>
                        @endif

                        @if($plan->features)
                            @foreach($plan->features as $feature)
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    {{ $feature }}
                                </li>
                            @endforeach
                        @endif
                    </ul>

                    <form method="POST" action="{{ route('subscription.subscribe') }}" class="mt-6">
                        @csrf
                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">

                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-700 mb-2">Forma de pagamento</p>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="payment_method" value="pix" class="text-primary" required>
                                    <span class="text-sm text-gray-700">PIX</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="payment_method" value="boleto" class="text-primary">
                                    <span class="text-sm text-gray-700">Boleto Bancário</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="payment_method" value="credit_card" class="text-primary">
                                    <span class="text-sm text-gray-700">Cartão de Crédito</span>
                                </label>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full py-2 px-4 rounded-lg text-white font-semibold bg-primary hover:bg-primary-dark transition-colors {{ $isCurrent ? 'opacity-75' : '' }}">
                            {{ $isCurrent ? 'Renovar Assinatura' : 'Assinar' }}
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-12 text-gray-500">
                Nenhum plano disponível no momento.
            </div>
        @endforelse
    </div>

</x-layout.app>
