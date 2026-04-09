<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TreinaEdu — Plataforma de Treinamentos Corporativos</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%234f46e5'><path d='M11.25 4.533A9.707 9.707 0 006 3a9.735 9.735 0 00-3.25.555.75.75 0 00-.5.707v14.25a.75.75 0 001 .707A8.237 8.237 0 016 18.75c1.995 0 3.823.707 5.25 1.886V4.533zM12.75 20.636A8.214 8.214 0 0118 18.75c.966 0 1.89.166 2.75.47a.75.75 0 001-.708V4.262a.75.75 0 00-.5-.707A9.735 9.735 0 0018 3a9.707 9.707 0 00-5.25 1.533v16.103z'/></svg>">
    <meta name="description" content="Capacite sua equipe com treinamentos em vídeo, quizzes inteligentes com IA e certificados digitais. Plataforma completa para empresas de todos os tamanhos.">

    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="TreinaEdu — Capacite sua equipe. Certifique com confiança.">
    <meta property="og:description" content="Plataforma de treinamentos corporativos com vídeos, módulos, quizzes com IA e certificados digitais verificáveis.">
    <meta property="og:image" content="{{ url('/og-image.png') }}">
    <meta property="og:locale" content="pt_BR">
    <meta property="og:site_name" content="TreinaEdu">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
                    colors: {
                        brand: { 50: '#eef2ff', 100: '#e0e7ff', 200: '#c7d2fe', 300: '#a5b4fc', 400: '#818cf8', 500: '#6366f1', 600: '#4f46e5', 700: '#4338ca', 800: '#3730a3', 900: '#312e81' }
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        .float-animation { animation: float 6s ease-in-out infinite; }
        .float-delay { animation: float 6s ease-in-out 2s infinite; }
        html { scroll-behavior: smooth; }
    </style>
</head>
<body class="font-sans antialiased bg-white text-gray-900">

    {{-- Navigation --}}
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-lg border-b border-gray-100/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-brand-500 to-brand-700 rounded-lg flex items-center justify-center shadow-lg shadow-brand-500/25">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold bg-gradient-to-r from-brand-600 to-brand-800 bg-clip-text text-transparent">TreinaEdu</span>
                </div>
                <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-gray-600">
                    <a href="#funcionalidades" class="hover:text-brand-600 transition">Funcionalidades</a>
                    <a href="#precos" class="hover:text-brand-600 transition">Preços</a>
                    <a href="#como-funciona" class="hover:text-brand-600 transition">Como funciona</a>
                </nav>
                <nav class="flex items-center gap-2">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-4 py-2 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 rounded-lg transition shadow-sm">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-brand-600 transition">Entrar</a>
                        <a href="{{ route('register') }}" class="px-5 py-2 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 rounded-lg transition shadow-sm shadow-brand-500/25">Criar conta</a>
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    {{-- Hero --}}
    <section class="relative overflow-hidden py-24 sm:py-32 lg:py-40">
        <div class="absolute inset-0 bg-gradient-to-br from-brand-50 via-white to-indigo-50/50"></div>
        <div class="absolute top-20 right-10 w-72 h-72 bg-brand-200 rounded-full opacity-20 blur-3xl float-animation"></div>
        <div class="absolute bottom-10 left-10 w-96 h-96 bg-indigo-200 rounded-full opacity-20 blur-3xl float-delay"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold text-brand-700 bg-brand-100 rounded-full mb-6">
                        <span class="w-2 h-2 bg-brand-500 rounded-full animate-pulse"></span>
                        Plataforma de Treinamentos Corporativos
                    </div>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-[1.1] tracking-tight mb-6">
                        Capacite sua equipe.
                        <span class="bg-gradient-to-r from-brand-600 to-indigo-600 bg-clip-text text-transparent">Certifique com confiança.</span>
                    </h1>
                    <p class="text-lg text-gray-600 leading-relaxed mb-8 max-w-lg">
                        Treinamentos em vídeo com módulos, quizzes gerados por IA, trilhas de aprendizagem e certificados digitais verificáveis. Tudo em uma única plataforma.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 text-base font-bold text-white bg-gradient-to-r from-brand-600 to-brand-700 hover:from-brand-700 hover:to-brand-800 rounded-xl transition-all shadow-lg shadow-brand-500/25 hover:shadow-brand-500/40 hover:-translate-y-0.5">
                            Teste grátis por 7 dias
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                        <a href="#precos" class="inline-flex items-center justify-center gap-2 px-6 py-4 text-sm font-semibold text-gray-700 bg-white border border-gray-200 hover:border-brand-300 hover:text-brand-600 rounded-xl transition-all">
                            Ver planos e preços
                        </a>
                    </div>
                    <p class="mt-4 text-xs text-gray-400">Sem cartão de crédito no trial. Cancele quando quiser.</p>
                </div>

                <div class="hidden lg:block relative">
                    <div class="relative bg-white rounded-2xl shadow-2xl shadow-brand-500/10 border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-brand-600 to-brand-700 px-6 py-3 flex items-center gap-2">
                            <div class="flex gap-1.5"><div class="w-3 h-3 rounded-full bg-white/30"></div><div class="w-3 h-3 rounded-full bg-white/30"></div><div class="w-3 h-3 rounded-full bg-white/30"></div></div>
                            <span class="text-white/80 text-xs ml-2 font-medium">treinaedu.com.br</span>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-brand-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-brand-600" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm14.024-.983a1.125 1.125 0 010 1.966l-5.603 3.113A1.125 1.125 0 019 15.113V8.887c0-.857.921-1.4 1.671-.983l5.603 3.113z" clip-rule="evenodd"/></svg>
                                </div>
                                <div class="flex-1"><div class="h-3 bg-gray-200 rounded w-3/4 mb-1.5"></div><div class="h-2 bg-gray-100 rounded w-1/2"></div></div>
                                <span class="text-xs font-bold text-brand-600">85%</span>
                            </div>
                            <div class="bg-gray-100 rounded-full h-2"><div class="bg-brand-500 h-2 rounded-full" style="width:85%"></div></div>
                            <div class="grid grid-cols-3 gap-3">
                                <div class="bg-brand-50 rounded-xl p-3 text-center"><p class="text-lg font-bold text-brand-700">12</p><p class="text-[10px] text-brand-500">Módulos</p></div>
                                <div class="bg-green-50 rounded-xl p-3 text-center"><p class="text-lg font-bold text-green-700">48</p><p class="text-[10px] text-green-500">Aulas</p></div>
                                <div class="bg-amber-50 rounded-xl p-3 text-center"><p class="text-lg font-bold text-amber-700">6</p><p class="text-[10px] text-amber-500">Quizzes</p></div>
                            </div>
                            <div class="flex items-center gap-2 p-3 bg-green-50 rounded-xl border border-green-100">
                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd"/></svg>
                                <span class="text-sm font-medium text-green-700">Certificado gerado!</span>
                            </div>
                        </div>
                    </div>
                    <div class="absolute -bottom-6 -left-6 w-24 h-24 bg-brand-100 rounded-2xl rotate-12 opacity-60"></div>
                    <div class="absolute -top-4 -right-4 w-16 h-16 bg-indigo-100 rounded-xl -rotate-12 opacity-60"></div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section id="funcionalidades" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <p class="text-sm font-semibold text-brand-600 uppercase tracking-wider mb-2">Funcionalidades</p>
                <h2 class="text-3xl sm:text-4xl font-extrabold mb-4">Tudo que sua empresa precisa para capacitar</h2>
                <p class="text-lg text-gray-500 max-w-2xl mx-auto">Uma plataforma completa com ferramentas profissionais para cada etapa do aprendizado corporativo.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @php
                    $features = [
                        ['icon' => 'M4.5 4.5a3 3 0 00-3 3v9a3 3 0 003 3h8.25a3 3 0 003-3v-9a3 3 0 00-3-3H4.5zM19.94 18.75l-2.69-2.69V7.94l2.69-2.69c.944-.945 2.56-.276 2.56 1.06v11.38c0 1.336-1.616 2.005-2.56 1.06z', 'title' => 'Treinamentos em Vídeo', 'desc' => 'Organize conteúdos em módulos e aulas com vídeos do YouTube e Vimeo. Progresso rastreado automaticamente a cada segundo.', 'color' => 'brand'],
                        ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'title' => 'Quizzes com IA', 'desc' => 'Gere avaliações automaticamente com inteligência artificial. Quizzes por aula, módulo ou ao final do treinamento.', 'color' => 'purple'],
                        ['icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z', 'title' => 'Certificados Digitais', 'desc' => 'PDFs gerados automaticamente com QR code de verificação, cores da empresa e link público compartilhável.', 'color' => 'green'],
                        ['icon' => 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7', 'title' => 'Trilhas de Aprendizagem', 'desc' => 'Monte jornadas de aprendizado sequenciais combinando vários treinamentos em uma trilha estruturada com progresso.', 'color' => 'blue'],
                        ['icon' => 'M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75zM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 01-1.875-1.875V8.625zM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 013 19.875v-6.75z', 'title' => 'Relatórios e Engajamento', 'desc' => 'Dashboards com taxa de conclusão, progresso por colaborador, colaboradores em risco e exportação em PDF e Excel.', 'color' => 'amber'],
                        ['icon' => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01', 'title' => 'Sua Marca, Sua Cara', 'desc' => 'Personalize cores, logo e aparência da plataforma inteira com a identidade visual da sua empresa. White-label completo.', 'color' => 'rose'],
                        ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'title' => 'Grupos e Atribuições', 'desc' => 'Organize colaboradores em grupos, atribua treinamentos em massa, defina prazos e controle obrigatoriedade.', 'color' => 'cyan'],
                        ['icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'title' => 'Convite por E-mail', 'desc' => 'Convide colaboradores por e-mail com link de ativação. Reenvio automático, controle de pendentes e tracking de acesso.', 'color' => 'teal'],
                        ['icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'title' => 'Seguro e Confiável', 'desc' => 'Multi-tenant isolado por empresa, cobrança automática no cartão de crédito via Asaas. LGPD compliant.', 'color' => 'indigo'],
                    ];
                    $colorMap = [
                        'brand' => ['bg' => 'bg-brand-100', 'hover' => 'group-hover:bg-brand-600', 'text' => 'text-brand-600', 'htext' => 'group-hover:text-white'],
                        'purple' => ['bg' => 'bg-purple-100', 'hover' => 'group-hover:bg-purple-600', 'text' => 'text-purple-600', 'htext' => 'group-hover:text-white'],
                        'green' => ['bg' => 'bg-green-100', 'hover' => 'group-hover:bg-green-600', 'text' => 'text-green-600', 'htext' => 'group-hover:text-white'],
                        'blue' => ['bg' => 'bg-blue-100', 'hover' => 'group-hover:bg-blue-600', 'text' => 'text-blue-600', 'htext' => 'group-hover:text-white'],
                        'amber' => ['bg' => 'bg-amber-100', 'hover' => 'group-hover:bg-amber-600', 'text' => 'text-amber-600', 'htext' => 'group-hover:text-white'],
                        'rose' => ['bg' => 'bg-rose-100', 'hover' => 'group-hover:bg-rose-600', 'text' => 'text-rose-600', 'htext' => 'group-hover:text-white'],
                        'cyan' => ['bg' => 'bg-cyan-100', 'hover' => 'group-hover:bg-cyan-600', 'text' => 'text-cyan-600', 'htext' => 'group-hover:text-white'],
                        'teal' => ['bg' => 'bg-teal-100', 'hover' => 'group-hover:bg-teal-600', 'text' => 'text-teal-600', 'htext' => 'group-hover:text-white'],
                        'indigo' => ['bg' => 'bg-indigo-100', 'hover' => 'group-hover:bg-indigo-600', 'text' => 'text-indigo-600', 'htext' => 'group-hover:text-white'],
                    ];
                @endphp

                @foreach($features as $f)
                    @php $c = $colorMap[$f['color']]; @endphp
                    <div class="group p-8 rounded-2xl border border-gray-100 hover:border-gray-200 hover:shadow-lg transition-all duration-300">
                        <div class="w-12 h-12 {{ $c['bg'] }} {{ $c['hover'] }} rounded-xl flex items-center justify-center mb-5 transition-colors duration-300">
                            <svg class="w-6 h-6 {{ $c['text'] }} {{ $c['htext'] }} transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $f['icon'] }}"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $f['title'] }}</h3>
                        <p class="text-gray-500 leading-relaxed text-sm">{{ $f['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Pricing --}}
    <section id="precos" class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <p class="text-sm font-semibold text-brand-600 uppercase tracking-wider mb-2">Planos e Preços</p>
                <h2 class="text-3xl sm:text-4xl font-extrabold mb-4">Escolha o plano ideal para sua empresa</h2>
                <p class="text-lg text-gray-500 max-w-2xl mx-auto">Comece com 7 dias grátis em qualquer plano. Cobrança mensal no cartão de crédito.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Starter --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="p-8">
                        <h3 class="text-lg font-bold text-gray-900">Starter</h3>
                        <p class="text-sm text-gray-500 mt-1">Para equipes pequenas</p>
                        <div class="mt-5 flex items-baseline gap-1">
                            <span class="text-4xl font-extrabold text-gray-900">R$199</span>
                            <span class="text-sm text-gray-400">/mês</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">~R$10 por usuário</p>
                        <a href="{{ route('register') }}" class="mt-6 block text-center py-3 px-4 text-sm font-semibold text-brand-600 bg-brand-50 hover:bg-brand-100 border border-brand-200 rounded-xl transition">Começar grátis</a>
                    </div>
                    <div class="px-8 pb-8 space-y-3 text-sm text-gray-600">
                        <div class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Até <strong>20 usuários</strong></div>
                        <div class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Até 30 treinamentos</div>
                        <div class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Marca personalizada</div>
                        <div class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Certificados em PDF</div>
                        <div class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Suporte por e-mail</div>
                    </div>
                </div>

                {{-- Business (destaque) --}}
                <div class="bg-white rounded-2xl shadow-xl border-2 border-brand-500 overflow-hidden relative">
                    <div class="bg-brand-600 text-white text-center text-xs font-bold py-2 uppercase tracking-wider">Mais popular</div>
                    <div class="p-8">
                        <h3 class="text-lg font-bold text-gray-900">Business</h3>
                        <p class="text-sm text-gray-500 mt-1">Para empresas em crescimento</p>
                        <div class="mt-5 flex items-baseline gap-1">
                            <span class="text-4xl font-extrabold text-gray-900">R$499</span>
                            <span class="text-sm text-gray-400">/mês</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">~R$10 por usuário</p>
                        <a href="{{ route('register') }}" class="mt-6 block text-center py-3 px-4 text-sm font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-xl transition shadow-sm">Começar grátis</a>
                    </div>
                    <div class="px-8 pb-8 space-y-3 text-sm text-gray-600">
                        <div class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Até <strong>50 usuários</strong></div>
                        <div class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Até 100 treinamentos</div>
                        <div class="flex items-center gap-2"><svg class="w-4 h-4 text-brand-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg><strong>Quiz com IA</strong></div>
                        <div class="flex items-center gap-2"><svg class="w-4 h-4 text-brand-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg><strong>Trilhas de aprendizagem</strong></div>
                        <div class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Relatórios avançados</div>
                        <div class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Suporte prioritário</div>
                    </div>
                </div>

                {{-- Professional --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="p-8">
                        <h3 class="text-lg font-bold text-gray-900">Professional</h3>
                        <p class="text-sm text-gray-500 mt-1">Para operações maiores</p>
                        <div class="mt-5 flex items-baseline gap-1">
                            <span class="text-4xl font-extrabold text-gray-900">R$999</span>
                            <span class="text-sm text-gray-400">/mês</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">~R$5 por usuário</p>
                        <a href="{{ route('register') }}" class="mt-6 block text-center py-3 px-4 text-sm font-semibold text-brand-600 bg-brand-50 hover:bg-brand-100 border border-brand-200 rounded-xl transition">Começar grátis</a>
                    </div>
                    <div class="px-8 pb-8 space-y-3 text-sm text-gray-600">
                        <div class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Até <strong>200 usuários</strong></div>
                        <div class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg><strong>Treinamentos ilimitados</strong></div>
                        <div class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Engajamento e desafios</div>
                        <div class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Onboarding dedicado</div>
                        <div class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Suporte via WhatsApp</div>
                    </div>
                </div>

                {{-- Enterprise --}}
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl shadow-sm overflow-hidden text-white hover:shadow-lg transition-shadow">
                    <div class="p-8">
                        <h3 class="text-lg font-bold">Enterprise</h3>
                        <p class="text-sm text-gray-400 mt-1">Para grandes empresas</p>
                        <div class="mt-5">
                            <span class="text-2xl font-extrabold">Sob consulta</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Acima de 200 usuários</p>
                        <a href="mailto:contato@treinaedu.com.br" class="mt-6 block text-center py-3 px-4 text-sm font-semibold text-white bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl transition">Falar com vendas</a>
                    </div>
                    <div class="px-8 pb-8 space-y-3 text-sm text-gray-300">
                        <div class="flex items-center gap-2"><svg class="w-4 h-4 text-brand-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg><strong>Usuários ilimitados</strong></div>
                        <div class="flex items-center gap-2"><svg class="w-4 h-4 text-brand-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Tudo do Professional</div>
                        <div class="flex items-center gap-2"><svg class="w-4 h-4 text-brand-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Preço por usuário negociado</div>
                        <div class="flex items-center gap-2"><svg class="w-4 h-4 text-brand-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>SLA dedicado</div>
                        <div class="flex items-center gap-2"><svg class="w-4 h-4 text-brand-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Gerente de conta exclusivo</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section id="como-funciona" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <p class="text-sm font-semibold text-brand-600 uppercase tracking-wider mb-2">Como funciona</p>
                <h2 class="text-3xl sm:text-4xl font-extrabold mb-4">Simples de começar, poderoso de escalar</h2>
            </div>

            <div class="grid md:grid-cols-4 gap-8">
                @php
                    $steps = [
                        ['num' => '1', 'title' => 'Crie sua conta', 'desc' => 'Cadastro rápido e gratuito. 7 dias de teste sem compromisso.'],
                        ['num' => '2', 'title' => 'Monte treinamentos', 'desc' => 'Adicione módulos, aulas em vídeo, documentos e gere quizzes com IA.'],
                        ['num' => '3', 'title' => 'Atribua à equipe', 'desc' => 'Organize colaboradores em grupos, defina prazos e obrigatoriedade.'],
                        ['num' => '4', 'title' => 'Acompanhe resultados', 'desc' => 'Monitore progresso em tempo real e emita certificados digitais.'],
                    ];
                @endphp

                @foreach($steps as $step)
                    <div class="text-center">
                        <div class="w-14 h-14 bg-brand-600 text-white text-xl font-bold rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-brand-500/25">
                            {{ $step['num'] }}
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">{{ $step['title'] }}</h3>
                        <p class="text-sm text-gray-500">{{ $step['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-brand-600 to-brand-800 rounded-3xl p-12 sm:p-16 text-center relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>

                <div class="relative">
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-4">Pronto para transformar seus treinamentos?</h2>
                    <p class="text-lg text-indigo-200 mb-8 max-w-xl mx-auto">Comece com 7 dias grátis. Sem cartão de crédito no período de teste.</p>

                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-8 py-4 text-base font-bold text-brand-700 bg-white hover:bg-indigo-50 rounded-xl transition-all shadow-lg hover:-translate-y-0.5">
                            Criar conta gratuita
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-gray-950 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 pb-12 border-b border-gray-800">
                <div class="col-span-2 md:col-span-1">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-7 h-7 bg-gradient-to-br from-brand-500 to-brand-700 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <span class="text-white font-bold">TreinaEdu</span>
                    </div>
                    <p class="text-sm text-gray-400 leading-relaxed">Plataforma completa de treinamentos corporativos para empresas que investem em suas equipes.</p>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-white mb-4">Produto</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#funcionalidades" class="hover:text-white transition">Funcionalidades</a></li>
                        <li><a href="#precos" class="hover:text-white transition">Preços</a></li>
                        <li><a href="#como-funciona" class="hover:text-white transition">Como funciona</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-white mb-4">Empresa</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="{{ route('termos') }}" class="hover:text-white transition">Termos de Uso</a></li>
                        <li><a href="{{ route('privacidade') }}" class="hover:text-white transition">Privacidade</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-white mb-4">Acesso</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="{{ route('login') }}" class="hover:text-white transition">Entrar</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white transition">Criar conta</a></li>
                    </ul>
                </div>
            </div>

            <div class="pt-8 text-center space-y-2">
                <p class="text-xs text-gray-500">TreinaEdu é um produto da <strong class="text-gray-400">HELPFLUX SOLUÇÕES EM TECNOLOGIA LTDA</strong></p>
                <p class="text-xs text-gray-600">CNPJ: 58.063.432/0001-21 — Santa Maria de Jetibá – ES</p>
                <p class="text-xs text-gray-500">&copy; {{ date('Y') }} TreinaEdu. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

</body>
</html>
