<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TreinaEdu — Plataforma de Treinamentos Corporativos</title>
    <meta name="description" content="Capacite sua equipe com treinamentos em vídeo, quizzes inteligentes e certificados digitais. Plataforma completa para empresas de todos os tamanhos.">
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
        .float-animation-delay { animation: float 6s ease-in-out 2s infinite; }
        @keyframes fade-in-up { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .fade-in-up { animation: fade-in-up 0.6s ease-out forwards; }
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
                <nav class="flex items-center gap-2">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-4 py-2 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 rounded-lg transition shadow-sm">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-brand-600 transition">Entrar</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-5 py-2 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 rounded-lg transition shadow-sm shadow-brand-500/25">Criar conta</a>
                        @endif
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    {{-- Hero --}}
    <section class="relative overflow-hidden py-24 sm:py-32 lg:py-40">
        <div class="absolute inset-0 bg-gradient-to-br from-brand-50 via-white to-indigo-50/50"></div>
        <div class="absolute top-20 right-10 w-72 h-72 bg-brand-200 rounded-full opacity-20 blur-3xl float-animation"></div>
        <div class="absolute bottom-10 left-10 w-96 h-96 bg-indigo-200 rounded-full opacity-20 blur-3xl float-animation-delay"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="fade-in-up">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold text-brand-700 bg-brand-100 rounded-full mb-6">
                        <span class="w-2 h-2 bg-brand-500 rounded-full animate-pulse"></span>
                        Plataforma de Treinamentos Corporativos
                    </div>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-[1.1] tracking-tight mb-6">
                        Capacite sua equipe.
                        <span class="bg-gradient-to-r from-brand-600 to-indigo-600 bg-clip-text text-transparent">Certifique com confianca.</span>
                    </h1>
                    <p class="text-lg text-gray-600 leading-relaxed mb-8 max-w-lg">
                        Treinamentos em video com modulos, quizzes inteligentes, progressao sequencial e certificados digitais verificaveis. Tudo em uma unica plataforma.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 text-base font-bold text-white bg-gradient-to-r from-brand-600 to-brand-700 hover:from-brand-700 hover:to-brand-800 rounded-xl transition-all shadow-lg shadow-brand-500/25 hover:shadow-brand-500/40 hover:-translate-y-0.5">
                            Comece gratuitamente
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                        <a href="#funcionalidades" class="inline-flex items-center justify-center gap-2 px-6 py-4 text-sm font-semibold text-gray-700 bg-white border border-gray-200 hover:border-brand-300 hover:text-brand-600 rounded-xl transition-all">
                            Conhecer funcionalidades
                        </a>
                    </div>
                    <p class="mt-4 text-xs text-gray-400">Sem cartao de credito. Cancele quando quiser.</p>
                </div>

                {{-- Hero visual --}}
                <div class="hidden lg:block relative">
                    <div class="relative bg-white rounded-2xl shadow-2xl shadow-brand-500/10 border border-gray-100 overflow-hidden">
                        {{-- Fake app screenshot --}}
                        <div class="bg-gradient-to-r from-brand-600 to-brand-700 px-6 py-3 flex items-center gap-2">
                            <div class="flex gap-1.5">
                                <div class="w-3 h-3 rounded-full bg-white/30"></div>
                                <div class="w-3 h-3 rounded-full bg-white/30"></div>
                                <div class="w-3 h-3 rounded-full bg-white/30"></div>
                            </div>
                            <span class="text-white/80 text-xs ml-2 font-medium">treinaedu.com.br</span>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-brand-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-brand-600" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm14.024-.983a1.125 1.125 0 010 1.966l-5.603 3.113A1.125 1.125 0 019 15.113V8.887c0-.857.921-1.4 1.671-.983l5.603 3.113z" clip-rule="evenodd"/></svg>
                                </div>
                                <div class="flex-1">
                                    <div class="h-3 bg-gray-200 rounded w-3/4 mb-1.5"></div>
                                    <div class="h-2 bg-gray-100 rounded w-1/2"></div>
                                </div>
                                <span class="text-xs font-bold text-brand-600">85%</span>
                            </div>
                            <div class="bg-gray-100 rounded-full h-2"><div class="bg-brand-500 h-2 rounded-full" style="width:85%"></div></div>
                            <div class="grid grid-cols-3 gap-3">
                                <div class="bg-brand-50 rounded-xl p-3 text-center"><p class="text-lg font-bold text-brand-700">12</p><p class="text-[10px] text-brand-500">Modulos</p></div>
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
                <p class="text-lg text-gray-500 max-w-2xl mx-auto">Uma plataforma completa de treinamentos com ferramentas profissionais para cada etapa do aprendizado.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @php
                    $features = [
                        ['icon' => 'M4.5 4.5a3 3 0 00-3 3v9a3 3 0 003 3h8.25a3 3 0 003-3v-9a3 3 0 00-3-3H4.5zM19.94 18.75l-2.69-2.69V7.94l2.69-2.69c.944-.945 2.56-.276 2.56 1.06v11.38c0 1.336-1.616 2.005-2.56 1.06z', 'title' => 'Treinamentos em Video', 'desc' => 'Organize conteudos em modulos e aulas com videos do YouTube e Vimeo. Progresso rastreado automaticamente.'],
                        ['icon' => 'M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5', 'title' => 'Modulos e Aulas', 'desc' => 'Estruture treinamentos em modulos com aulas de video, documentos PDF e conteudo texto. Progressao sequencial configuravel.'],
                        ['icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z', 'title' => 'Certificados Digitais', 'desc' => 'Certificados PDF gerados automaticamente ao concluir treinamentos. Verificaveis por link publico com codigo unico.'],
                        ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'title' => 'Quizzes Inteligentes', 'desc' => 'Avalie o aprendizado com quizzes por aula, modulo ou ao final do treinamento. Nota minima configuravel.'],
                        ['icon' => 'M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75zM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 01-1.875-1.875V8.625zM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 013 19.875v-6.75z', 'title' => 'Relatorios e Metricas', 'desc' => 'Dashboards com taxa de conclusao, progresso por colaborador e exportacao em PDF e Excel.'],
                        ['icon' => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01', 'title' => 'Identidade Visual', 'desc' => 'Personalize cores, logo e aparencia da plataforma com a identidade da sua empresa. White-label completo.'],
                    ];
                @endphp

                @foreach($features as $i => $f)
                    <div class="group p-8 rounded-2xl border border-gray-100 hover:border-brand-200 hover:shadow-lg hover:shadow-brand-500/5 transition-all duration-300">
                        <div class="w-12 h-12 bg-brand-100 group-hover:bg-brand-600 rounded-xl flex items-center justify-center mb-5 transition-colors duration-300">
                            <svg class="w-6 h-6 text-brand-600 group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

    {{-- How it works --}}
    <section class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <p class="text-sm font-semibold text-brand-600 uppercase tracking-wider mb-2">Como funciona</p>
                <h2 class="text-3xl sm:text-4xl font-extrabold mb-4">Simples de comecar, poderoso de escalar</h2>
            </div>

            <div class="grid md:grid-cols-4 gap-8">
                @php
                    $steps = [
                        ['num' => '1', 'title' => 'Crie sua conta', 'desc' => 'Cadastro rapido e gratuito. Sem burocracia.'],
                        ['num' => '2', 'title' => 'Monte treinamentos', 'desc' => 'Adicione modulos, aulas em video e quizzes.'],
                        ['num' => '3', 'title' => 'Atribua a equipe', 'desc' => 'Organize por grupos e defina prazos.'],
                        ['num' => '4', 'title' => 'Acompanhe resultados', 'desc' => 'Monitore progresso e emita certificados.'],
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

    {{-- Social proof --}}
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-brand-600 to-brand-800 rounded-3xl p-12 sm:p-16 text-center relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>

                <div class="relative">
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-4">Pronto para transformar seus treinamentos?</h2>
                    <p class="text-lg text-indigo-200 mb-8 max-w-xl mx-auto">Junte-se a empresas que ja usam o TreinaEdu para capacitar suas equipes de forma profissional.</p>

                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-8 py-4 text-base font-bold text-brand-700 bg-white hover:bg-indigo-50 rounded-xl transition-all shadow-lg hover:-translate-y-0.5">
                            Criar conta gratuita
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                        <a href="{{ route('login') }}" class="text-white/80 hover:text-white text-sm font-medium transition">Ja tenho conta</a>
                    </div>

                    <p class="mt-6 text-xs text-indigo-300">Teste gratis. Sem compromisso. Cancele quando quiser.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-gray-950 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 bg-gradient-to-br from-brand-500 to-brand-700 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <span class="text-white font-bold">TreinaEdu</span>
                </div>
                <p class="text-sm text-gray-500">&copy; {{ date('Y') }} TreinaEdu. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

</body>
</html>
