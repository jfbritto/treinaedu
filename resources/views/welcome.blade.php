<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TreinaEdu — Plataforma de Treinamentos Corporativos</title>
    <meta name="description" content="Plataforma de treinamentos corporativos com rastreamento de progresso, quizzes e certificados digitais.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50:  '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="font-sans antialiased bg-white text-gray-900">

    <!-- Navigation -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-gray-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-brand-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-gray-900">TreinaEdu</span>
                </div>

                <!-- Nav Actions -->
                <nav class="flex items-center gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:text-brand-600 transition-colors">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:text-brand-600 transition-colors">
                            Entrar
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                               class="inline-flex items-center px-5 py-2 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 rounded-lg transition-colors shadow-sm">
                                Começar Grátis
                            </a>
                        @endif
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-brand-50 via-white to-indigo-50 py-20 sm:py-28">
        <!-- Background decoration -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-96 h-96 bg-brand-100 rounded-full opacity-40 blur-3xl"></div>
            <div class="absolute -bottom-20 -left-20 w-72 h-72 bg-indigo-100 rounded-full opacity-40 blur-3xl"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 text-xs font-semibold text-brand-700 bg-brand-100 rounded-full mb-6">
                <span class="w-2 h-2 bg-brand-600 rounded-full"></span>
                Plataforma de Treinamentos Corporativos
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 leading-tight tracking-tight mb-6">
                Treine sua equipe.<br>
                <span class="text-brand-600">Certifique com segurança.</span>
            </h1>

            <p class="max-w-2xl mx-auto text-lg sm:text-xl text-gray-600 mb-10">
                Plataforma de treinamentos corporativos com rastreamento de progresso, quizzes e certificados digitais.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('register') }}"
                   class="inline-flex items-center px-8 py-4 text-base font-semibold text-white bg-brand-600 hover:bg-brand-700 rounded-xl transition-all shadow-lg hover:shadow-brand-500/30 hover:-translate-y-0.5">
                    Começar gratuitamente — 7 dias grátis
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
                <p class="text-sm text-gray-500">Sem cartão de crédito necessário</p>
            </div>

            <!-- Stats -->
            <div class="mt-16 grid grid-cols-3 gap-8 max-w-lg mx-auto">
                <div class="text-center">
                    <p class="text-3xl font-bold text-gray-900">100%</p>
                    <p class="text-sm text-gray-500 mt-1">Digital</p>
                </div>
                <div class="text-center border-x border-gray-200">
                    <p class="text-3xl font-bold text-gray-900">7 dias</p>
                    <p class="text-sm text-gray-500 mt-1">Grátis</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-gray-900">Multi</p>
                    <p class="text-sm text-gray-500 mt-1">Empresa</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                    Tudo que sua empresa precisa
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Ferramentas completas para gerenciar treinamentos, acompanhar o desempenho e emitir certificados.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="relative p-8 bg-white border border-gray-100 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 bg-brand-100 rounded-xl flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.882v6.236a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Vídeos + Progresso</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Acompanhe o progresso dos colaboradores em tempo real. Vídeos, materiais e trilhas de aprendizado organizados por módulo.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="relative p-8 bg-brand-600 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-3">Quizzes e Certificados</h3>
                    <p class="text-indigo-100 leading-relaxed">
                        Avalie o aprendizado com quizzes personalizados e emita certificados automaticamente ao concluir os treinamentos.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="relative p-8 bg-white border border-gray-100 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 bg-brand-100 rounded-xl flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Multi-empresa</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Gerencie múltiplas equipes e grupos de treinamento. Cada empresa tem seu próprio ambiente isolado e personalizável.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Plans Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                    Escolha seu plano
                </h2>
                <p class="text-lg text-gray-600">
                    Planos flexíveis para empresas de todos os tamanhos.
                </p>
            </div>

            @if(isset($plans) && $plans->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-{{ $plans->count() > 2 ? '3' : $plans->count() }} gap-8 max-w-5xl mx-auto">
                    @foreach($plans as $index => $plan)
                        <div class="relative flex flex-col p-8 rounded-2xl border-2 transition-all
                            {{ $index === 1 ? 'border-brand-600 bg-white shadow-xl shadow-brand-500/10' : 'border-gray-200 bg-white shadow-sm hover:shadow-md' }}">

                            @if($index === 1)
                                <div class="absolute -top-4 left-1/2 -translate-x-1/2">
                                    <span class="inline-flex items-center px-4 py-1 text-xs font-bold text-white bg-brand-600 rounded-full shadow">
                                        Mais Popular
                                    </span>
                                </div>
                            @endif

                            <div class="mb-6">
                                <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $plan->name }}</h3>
                                <div class="flex items-end gap-1">
                                    <span class="text-4xl font-extrabold text-gray-900">
                                        R$&nbsp;{{ number_format($plan->price, 2, ',', '.') }}
                                    </span>
                                    <span class="text-gray-500 mb-1">/mês</span>
                                </div>
                            </div>

                            <ul class="flex-1 space-y-3 mb-8">
                                @if($plan->max_users)
                                    <li class="flex items-center gap-2 text-sm text-gray-700">
                                        <svg class="w-4 h-4 text-brand-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Até {{ $plan->max_users }} usuários
                                    </li>
                                @endif
                                @if($plan->max_trainings)
                                    <li class="flex items-center gap-2 text-sm text-gray-700">
                                        <svg class="w-4 h-4 text-brand-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Até {{ $plan->max_trainings }} treinamentos
                                    </li>
                                @endif
                                @if($plan->features)
                                    @foreach($plan->features as $feature)
                                        <li class="flex items-center gap-2 text-sm text-gray-700">
                                            <svg class="w-4 h-4 text-brand-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ $feature }}
                                        </li>
                                    @endforeach
                                @endif
                            </ul>

                            <a href="{{ route('register') }}"
                               class="block text-center py-3 px-6 text-sm font-semibold rounded-xl transition-all
                               {{ $index === 1
                                   ? 'bg-brand-600 text-white hover:bg-brand-700 shadow-md hover:shadow-brand-500/30'
                                   : 'bg-gray-100 text-gray-900 hover:bg-gray-200' }}">
                                Começar agora
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Fallback placeholder cards when no plans exist -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                    <!-- Starter -->
                    <div class="flex flex-col p-8 bg-white border-2 border-gray-200 rounded-2xl shadow-sm hover:shadow-md transition-all">
                        <div class="mb-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Starter</h3>
                            <div class="flex items-end gap-1">
                                <span class="text-4xl font-extrabold text-gray-900">R$&nbsp;99</span>
                                <span class="text-gray-500 mb-1">/mês</span>
                            </div>
                        </div>
                        <ul class="flex-1 space-y-3 mb-8">
                            <li class="flex items-center gap-2 text-sm text-gray-700">
                                <svg class="w-4 h-4 text-brand-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Até 10 usuários
                            </li>
                            <li class="flex items-center gap-2 text-sm text-gray-700">
                                <svg class="w-4 h-4 text-brand-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                5 treinamentos
                            </li>
                            <li class="flex items-center gap-2 text-sm text-gray-700">
                                <svg class="w-4 h-4 text-brand-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Certificados digitais
                            </li>
                        </ul>
                        <a href="{{ route('register') }}" class="block text-center py-3 px-6 text-sm font-semibold bg-gray-100 text-gray-900 hover:bg-gray-200 rounded-xl transition-all">
                            Começar agora
                        </a>
                    </div>

                    <!-- Pro (highlighted) -->
                    <div class="relative flex flex-col p-8 bg-white border-2 border-brand-600 rounded-2xl shadow-xl shadow-brand-500/10">
                        <div class="absolute -top-4 left-1/2 -translate-x-1/2">
                            <span class="inline-flex items-center px-4 py-1 text-xs font-bold text-white bg-brand-600 rounded-full shadow">
                                Mais Popular
                            </span>
                        </div>
                        <div class="mb-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Pro</h3>
                            <div class="flex items-end gap-1">
                                <span class="text-4xl font-extrabold text-gray-900">R$&nbsp;299</span>
                                <span class="text-gray-500 mb-1">/mês</span>
                            </div>
                        </div>
                        <ul class="flex-1 space-y-3 mb-8">
                            <li class="flex items-center gap-2 text-sm text-gray-700">
                                <svg class="w-4 h-4 text-brand-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Até 50 usuários
                            </li>
                            <li class="flex items-center gap-2 text-sm text-gray-700">
                                <svg class="w-4 h-4 text-brand-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Treinamentos ilimitados
                            </li>
                            <li class="flex items-center gap-2 text-sm text-gray-700">
                                <svg class="w-4 h-4 text-brand-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Quizzes avançados
                            </li>
                            <li class="flex items-center gap-2 text-sm text-gray-700">
                                <svg class="w-4 h-4 text-brand-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Relatórios detalhados
                            </li>
                        </ul>
                        <a href="{{ route('register') }}" class="block text-center py-3 px-6 text-sm font-semibold bg-brand-600 text-white hover:bg-brand-700 rounded-xl transition-all shadow-md hover:shadow-brand-500/30">
                            Começar agora
                        </a>
                    </div>

                    <!-- Enterprise -->
                    <div class="flex flex-col p-8 bg-white border-2 border-gray-200 rounded-2xl shadow-sm hover:shadow-md transition-all">
                        <div class="mb-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Enterprise</h3>
                            <div class="flex items-end gap-1">
                                <span class="text-4xl font-extrabold text-gray-900">R$&nbsp;799</span>
                                <span class="text-gray-500 mb-1">/mês</span>
                            </div>
                        </div>
                        <ul class="flex-1 space-y-3 mb-8">
                            <li class="flex items-center gap-2 text-sm text-gray-700">
                                <svg class="w-4 h-4 text-brand-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Usuários ilimitados
                            </li>
                            <li class="flex items-center gap-2 text-sm text-gray-700">
                                <svg class="w-4 h-4 text-brand-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Multi-empresa
                            </li>
                            <li class="flex items-center gap-2 text-sm text-gray-700">
                                <svg class="w-4 h-4 text-brand-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Suporte prioritário
                            </li>
                            <li class="flex items-center gap-2 text-sm text-gray-700">
                                <svg class="w-4 h-4 text-brand-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                API e integrações
                            </li>
                        </ul>
                        <a href="{{ route('register') }}" class="block text-center py-3 px-6 text-sm font-semibold bg-gray-100 text-gray-900 hover:bg-gray-200 rounded-xl transition-all">
                            Falar com vendas
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-brand-600">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-4">
                Comece hoje mesmo
            </h2>
            <p class="text-lg text-indigo-100 mb-10">
                Junte-se a empresas que já transformaram seus treinamentos corporativos com o TreinaEdu.
            </p>
            <a href="{{ route('register') }}"
               class="inline-flex items-center px-10 py-4 text-base font-bold text-brand-600 bg-white hover:bg-indigo-50 rounded-xl transition-all shadow-lg hover:-translate-y-0.5">
                Criar conta gratuita
                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
            <p class="mt-4 text-sm text-indigo-200">7 dias grátis • Cancele quando quiser</p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 bg-brand-600 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <span class="text-white font-semibold">TreinaEdu</span>
                </div>
                <p class="text-sm text-gray-400">
                    &copy; 2025 TreinaEdu. Todos os direitos reservados.
                </p>
            </div>
        </div>
    </footer>

</body>
</html>
