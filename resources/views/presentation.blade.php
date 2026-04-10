<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TreinaEdu — Apresentação Comercial</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%234f46e5'><path d='M11.25 4.533A9.707 9.707 0 006 3a9.735 9.735 0 00-3.25.555.75.75 0 00-.5.707v14.25a.75.75 0 001 .707A8.237 8.237 0 016 18.75c1.995 0 3.823.707 5.25 1.886V4.533zM12.75 20.636A8.214 8.214 0 0118 18.75c.966 0 1.89.166 2.75.47a.75.75 0 001-.708V4.262a.75.75 0 00-.5-.707A9.735 9.735 0 0018 3a9.707 9.707 0 00-5.25 1.533v16.103z'/></svg>">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; scroll-snap-type: y mandatory; }
        body { font-family: 'Inter', sans-serif; background: #0f172a; color: white; overflow-x: hidden; }

        .slide {
            min-height: 100vh;
            scroll-snap-align: start;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .fade-in { opacity: 0; transform: translateY(40px); transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
        .fade-in.visible { opacity: 1; transform: translateY(0); }
        .fade-in-delay-1 { transition-delay: 0.15s; }
        .fade-in-delay-2 { transition-delay: 0.3s; }
        .fade-in-delay-3 { transition-delay: 0.45s; }
        .fade-in-delay-4 { transition-delay: 0.6s; }
        .fade-in-delay-5 { transition-delay: 0.75s; }

        .gradient-text {
            background: linear-gradient(135deg, #818cf8, #6366f1, #4f46e5);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .gradient-text-warm {
            background: linear-gradient(135deg, #f59e0b, #ef4444);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .glow { box-shadow: 0 0 60px rgba(99, 102, 241, 0.15); }
        .glow-strong { box-shadow: 0 0 80px rgba(99, 102, 241, 0.25); }

        .counter { font-variant-numeric: tabular-nums; }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .float { animation: float 3s ease-in-out infinite; }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }
        .pulse-dot { animation: pulse-dot 2s ease-in-out infinite; }

        .slide-indicator { transition: all 0.3s; }
        .slide-indicator.active { background: #6366f1; width: 32px; }

        .feature-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.06);
            backdrop-filter: blur(10px);
            transition: all 0.3s;
        }
        .feature-card:hover {
            background: rgba(255,255,255,0.06);
            border-color: rgba(99, 102, 241, 0.3);
            transform: translateY(-4px);
        }

        .price-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
            transition: all 0.4s;
        }
        .price-card:hover, .price-card.popular {
            background: rgba(99, 102, 241, 0.08);
            border-color: rgba(99, 102, 241, 0.4);
        }
        .price-card.popular {
            box-shadow: 0 0 40px rgba(99, 102, 241, 0.15);
        }
    </style>
</head>
<body>

    {{-- Navigation dots --}}
    <nav class="fixed right-6 top-1/2 -translate-y-1/2 z-50 flex flex-col gap-2" id="nav-dots">
        <button onclick="goSlide(0)" class="slide-indicator w-2 h-2 rounded-full bg-white/30 active" data-slide="0"></button>
        <button onclick="goSlide(1)" class="slide-indicator w-2 h-2 rounded-full bg-white/30" data-slide="1"></button>
        <button onclick="goSlide(2)" class="slide-indicator w-2 h-2 rounded-full bg-white/30" data-slide="2"></button>
        <button onclick="goSlide(3)" class="slide-indicator w-2 h-2 rounded-full bg-white/30" data-slide="3"></button>
        <button onclick="goSlide(4)" class="slide-indicator w-2 h-2 rounded-full bg-white/30" data-slide="4"></button>
        <button onclick="goSlide(5)" class="slide-indicator w-2 h-2 rounded-full bg-white/30" data-slide="5"></button>
        <button onclick="goSlide(6)" class="slide-indicator w-2 h-2 rounded-full bg-white/30" data-slide="6"></button>
        <button onclick="goSlide(7)" class="slide-indicator w-2 h-2 rounded-full bg-white/30" data-slide="7"></button>
    </nav>

    {{-- ====== SLIDE 1: Hero ====== --}}
    <section class="slide" style="background: radial-gradient(ellipse at 30% 50%, rgba(99,102,241,0.15) 0%, transparent 60%);">
        <div class="max-w-5xl mx-auto px-8 text-center">
            <div class="fade-in">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-indigo-500/10 border border-indigo-500/20 mb-8">
                    <span class="w-2 h-2 rounded-full bg-green-400 pulse-dot"></span>
                    <span class="text-sm text-indigo-300">Plataforma ativa e em produção</span>
                </div>
            </div>
            <h1 class="fade-in fade-in-delay-1 text-6xl md:text-8xl font-black tracking-tight leading-none mb-6">
                Treina<span class="gradient-text">Edu</span>
            </h1>
            <p class="fade-in fade-in-delay-2 text-xl md:text-2xl text-gray-400 max-w-2xl mx-auto mb-12 leading-relaxed">
                A plataforma completa de treinamentos corporativos com <strong class="text-white">vídeos</strong>, <strong class="text-white">quizzes com IA</strong> e <strong class="text-white">certificados digitais</strong>.
            </p>
            <div class="fade-in fade-in-delay-3 flex flex-wrap justify-center gap-6 text-sm text-gray-500">
                <div class="flex items-center gap-2"><svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> 7 dias grátis</div>
                <div class="flex items-center gap-2"><svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Sem cartão para testar</div>
                <div class="flex items-center gap-2"><svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Setup em 5 minutos</div>
            </div>
            <div class="fade-in fade-in-delay-4 mt-16 text-gray-600 text-xs animate-bounce">
                <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
            </div>
        </div>
    </section>

    {{-- ====== SLIDE 2: O Problema ====== --}}
    <section class="slide" style="background: radial-gradient(ellipse at 70% 30%, rgba(239,68,68,0.08) 0%, transparent 60%);">
        <div class="max-w-5xl mx-auto px-8">
            <p class="fade-in text-sm text-red-400 uppercase tracking-widest mb-4 font-semibold">O problema</p>
            <h2 class="fade-in fade-in-delay-1 text-4xl md:text-6xl font-bold mb-12 leading-tight">
                Sua empresa ainda treina<br>
                <span class="gradient-text-warm">no improviso?</span>
            </h2>
            <div class="grid md:grid-cols-3 gap-6">
                <div class="fade-in fade-in-delay-2 feature-card rounded-2xl p-6">
                    <div class="text-4xl mb-4">📋</div>
                    <h3 class="text-lg font-bold mb-2">Sem controle</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">Você não sabe quem fez o treinamento, quem está atrasado, ou quem nunca nem começou.</p>
                </div>
                <div class="fade-in fade-in-delay-3 feature-card rounded-2xl p-6">
                    <div class="text-4xl mb-4">📁</div>
                    <h3 class="text-lg font-bold mb-2">Conteúdo espalhado</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">Vídeos no Drive, PDFs no WhatsApp, planilhas no Excel. Tudo desconectado e impossível de medir.</p>
                </div>
                <div class="fade-in fade-in-delay-4 feature-card rounded-2xl p-6">
                    <div class="text-4xl mb-4">📜</div>
                    <h3 class="text-lg font-bold mb-2">Certificados manuais</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">Emitir certificado é um trabalho manual que ninguém tem tempo de fazer — e quando faz, não tem validade.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ====== SLIDE 3: A Solução ====== --}}
    <section class="slide" style="background: radial-gradient(ellipse at 30% 60%, rgba(99,102,241,0.1) 0%, transparent 60%);">
        <div class="max-w-5xl mx-auto px-8">
            <p class="fade-in text-sm text-indigo-400 uppercase tracking-widest mb-4 font-semibold">A solução</p>
            <h2 class="fade-in fade-in-delay-1 text-4xl md:text-6xl font-bold mb-6 leading-tight">
                Tudo em um só lugar.<br>
                <span class="gradient-text">Simples e poderoso.</span>
            </h2>
            <p class="fade-in fade-in-delay-2 text-xl text-gray-400 mb-12 max-w-2xl">O admin cria o treinamento, atribui aos colaboradores, e acompanha tudo em tempo real. O certificado é gerado automaticamente.</p>

            <div class="fade-in fade-in-delay-3 flex flex-wrap justify-center gap-3">
                @php $steps = ['Crie o treinamento', 'Cole os links dos vídeos', 'IA gera quizzes e títulos', 'Atribua aos colaboradores', 'Acompanhe em tempo real', 'Certificado automático']; @endphp
                @foreach($steps as $i => $step)
                    <div class="flex items-center gap-3 bg-white/5 border border-white/10 rounded-full px-5 py-3">
                        <span class="w-7 h-7 rounded-full bg-indigo-500 flex items-center justify-center text-xs font-bold">{{ $i + 1 }}</span>
                        <span class="text-sm font-medium">{{ $step }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ====== SLIDE 4: Features Grid ====== --}}
    <section class="slide" style="background: radial-gradient(ellipse at 60% 40%, rgba(99,102,241,0.08) 0%, transparent 60%);">
        <div class="max-w-6xl mx-auto px-8">
            <p class="fade-in text-sm text-indigo-400 uppercase tracking-widest mb-4 font-semibold text-center">Funcionalidades</p>
            <h2 class="fade-in fade-in-delay-1 text-4xl md:text-5xl font-bold mb-12 text-center">O que o TreinaEdu faz</h2>
            <div class="grid md:grid-cols-3 gap-4">
                @php
                    $features = [
                        ['🎬', 'Treinamentos em Vídeo', 'YouTube e Vimeo com progresso automático por aula'],
                        ['🤖', 'Quizzes com IA', 'A IA gera perguntas automaticamente a partir do conteúdo'],
                        ['📜', 'Certificados Digitais', 'PDF personalizado com QR code, assinatura e cores da empresa'],
                        ['🛤️', 'Trilhas de Aprendizagem', 'Jornadas combinando múltiplos treinamentos em sequência'],
                        ['📊', 'Relatórios em Tempo Real', 'Acompanhe conclusão, notas e engajamento de cada colaborador'],
                        ['🏢', 'Marca da Empresa', 'Logo, cores e identidade visual em toda a plataforma'],
                        ['👥', 'Importação em Massa', 'Cadastre 250 colaboradores de uma vez via planilha'],
                        ['✉️', 'Convite Automático', 'Email de convite com link para definir senha e começar'],
                        ['🔒', 'Seguro e Confiável', 'Dados isolados por empresa, SSL, LGPD, pagamento seguro'],
                    ];
                @endphp
                @foreach($features as $i => $f)
                    <div class="fade-in fade-in-delay-{{ min($i % 3 + 1, 5) }} feature-card rounded-2xl p-5">
                        <span class="text-2xl">{{ $f[0] }}</span>
                        <h3 class="text-sm font-bold mt-3 mb-1">{{ $f[1] }}</h3>
                        <p class="text-xs text-gray-500 leading-relaxed">{{ $f[2] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ====== SLIDE 5: IA ====== --}}
    <section class="slide" style="background: radial-gradient(ellipse at 40% 50%, rgba(168,85,247,0.1) 0%, transparent 60%);">
        <div class="max-w-5xl mx-auto px-8 text-center">
            <p class="fade-in text-sm text-purple-400 uppercase tracking-widest mb-4 font-semibold">Inteligência Artificial</p>
            <h2 class="fade-in fade-in-delay-1 text-4xl md:text-6xl font-bold mb-6">
                A IA trabalha<br><span class="gradient-text">por você</span>
            </h2>
            <p class="fade-in fade-in-delay-2 text-xl text-gray-400 max-w-2xl mx-auto mb-12">Cole o link do vídeo e a IA preenche o título da aula, do módulo, do treinamento, a descrição e gera o quiz automaticamente.</p>

            <div class="fade-in fade-in-delay-3 grid md:grid-cols-4 gap-4 text-left max-w-4xl mx-auto">
                @php
                    $aiFeatures = [
                        ['Cole o link', 'Título da aula é preenchido automaticamente a partir do vídeo'],
                        ['Título do módulo', 'IA sugere um nome curto que agrupa as aulas'],
                        ['Título do treinamento', 'Nome geral gerado a partir dos módulos e aulas'],
                        ['Quiz automático', 'Perguntas geradas com base no conteúdo do treinamento'],
                    ];
                @endphp
                @foreach($aiFeatures as $i => $af)
                    <div class="feature-card rounded-2xl p-5">
                        <div class="w-8 h-8 rounded-lg bg-purple-500/20 flex items-center justify-center mb-3">
                            <span class="text-purple-400 font-bold text-sm">{{ $i + 1 }}</span>
                        </div>
                        <h3 class="text-sm font-bold mb-1">{{ $af[0] }}</h3>
                        <p class="text-xs text-gray-500 leading-relaxed">{{ $af[1] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ====== SLIDE 6: Certificados ====== --}}
    <section class="slide" style="background: radial-gradient(ellipse at 50% 60%, rgba(16,185,129,0.08) 0%, transparent 60%);">
        <div class="max-w-5xl mx-auto px-8">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <p class="fade-in text-sm text-emerald-400 uppercase tracking-widest mb-4 font-semibold">Certificados</p>
                    <h2 class="fade-in fade-in-delay-1 text-4xl md:text-5xl font-bold mb-6 leading-tight">
                        Certificado profissional com <span class="gradient-text">assinatura digital</span>
                    </h2>
                    <div class="fade-in fade-in-delay-2 space-y-4">
                        @php
                            $certFeatures = [
                                'PDF com layout moderno personalizável',
                                'Logo, cores e identidade da empresa',
                                'Assinatura do responsável (desenhada ou upload)',
                                'QR Code que valida o certificado publicamente',
                                'Registro profissional (COREN, CRM, CREA...)',
                                'Compartilhamento direto no LinkedIn',
                            ];
                        @endphp
                        @foreach($certFeatures as $cf)
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span class="text-gray-300 text-sm">{{ $cf }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="fade-in fade-in-delay-3 float">
                    <div class="bg-white rounded-2xl p-8 glow-strong" style="aspect-ratio: 297/210;">
                        <div class="flex h-full">
                            <div class="w-3 rounded-full bg-indigo-500 mr-4 flex-shrink-0"></div>
                            <div class="flex-1 flex flex-col justify-between">
                                <div>
                                    <p class="text-indigo-500 text-3xl font-bold tracking-wider">CERTIFICADO</p>
                                    <p class="text-indigo-400 text-sm italic">de Conclusão</p>
                                    <div class="w-10 h-0.5 bg-indigo-500 my-3"></div>
                                    <p class="text-gray-400 text-xs uppercase tracking-wider">Certificamos que</p>
                                    <p class="text-gray-800 text-xl font-bold mt-1">Nome do Colaborador</p>
                                </div>
                                <div class="flex items-end justify-between">
                                    <div class="text-xs text-gray-400">
                                        <p>10/04/2026</p>
                                        <p class="font-mono text-gray-500">TH-2026-XXXX</p>
                                    </div>
                                    <div class="text-center">
                                        <div class="w-16 h-px bg-gray-300 mb-1"></div>
                                        <p class="text-xs font-bold text-gray-700">Responsável</p>
                                        <p class="text-xs text-gray-400">COREN-XX 000</p>
                                    </div>
                                    <div class="w-10 h-10 bg-gray-100 rounded"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ====== SLIDE 7: Pricing ====== --}}
    <section class="slide" style="background: radial-gradient(ellipse at 50% 50%, rgba(99,102,241,0.08) 0%, transparent 60%);">
        <div class="max-w-6xl mx-auto px-8">
            <p class="fade-in text-sm text-indigo-400 uppercase tracking-widest mb-4 font-semibold text-center">Investimento</p>
            <h2 class="fade-in fade-in-delay-1 text-4xl md:text-5xl font-bold mb-4 text-center">Planos que cabem no seu bolso</h2>
            <p class="fade-in fade-in-delay-2 text-gray-400 text-center mb-12">A partir de <strong class="text-white">R$ 9,95 por colaborador/mês</strong>. Comece grátis por 7 dias.</p>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="fade-in fade-in-delay-2 price-card rounded-2xl p-6">
                    <h3 class="text-lg font-bold mb-1">Starter</h3>
                    <p class="text-3xl font-black mb-1">R$ 199<span class="text-base font-normal text-gray-500">/mês</span></p>
                    <p class="text-xs text-gray-500 mb-6">Até 20 usuários · 30 treinamentos</p>
                    <div class="space-y-2 text-sm text-gray-400">
                        <p>✓ Certificados em PDF</p>
                        <p>✓ Relatórios básicos</p>
                        <p>✓ Marca personalizada</p>
                    </div>
                </div>
                <div class="fade-in fade-in-delay-3 price-card popular rounded-2xl p-6 relative">
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-1 bg-indigo-500 rounded-full text-xs font-bold">Mais popular</div>
                    <h3 class="text-lg font-bold mb-1">Business</h3>
                    <p class="text-3xl font-black mb-1">R$ 499<span class="text-base font-normal text-gray-500">/mês</span></p>
                    <p class="text-xs text-gray-500 mb-6">Até 50 usuários · 100 treinamentos</p>
                    <div class="space-y-2 text-sm text-gray-300">
                        <p>✓ Tudo do Starter</p>
                        <p>✓ <strong>Quiz com IA</strong></p>
                        <p>✓ Trilhas de aprendizagem</p>
                        <p>✓ Exportação PDF/Excel</p>
                    </div>
                </div>
                <div class="fade-in fade-in-delay-4 price-card rounded-2xl p-6">
                    <h3 class="text-lg font-bold mb-1">Professional</h3>
                    <p class="text-3xl font-black mb-1">R$ 999<span class="text-base font-normal text-gray-500">/mês</span></p>
                    <p class="text-xs text-gray-500 mb-6">Até 200 usuários · Ilimitado</p>
                    <div class="space-y-2 text-sm text-gray-400">
                        <p>✓ Tudo do Business</p>
                        <p>✓ Engajamento e desafios</p>
                        <p>✓ Suporte via WhatsApp</p>
                        <p>✓ Onboarding dedicado</p>
                    </div>
                </div>
            </div>
            <p class="fade-in fade-in-delay-5 text-center text-sm text-gray-500 mt-8">Acima de 200 usuários? <strong class="text-white">Plano Enterprise sob consulta.</strong></p>
        </div>
    </section>

    {{-- ====== SLIDE 8: CTA Final ====== --}}
    <section class="slide" style="background: radial-gradient(ellipse at 50% 50%, rgba(99,102,241,0.2) 0%, transparent 50%);">
        <div class="max-w-4xl mx-auto px-8 text-center">
            <h2 class="fade-in text-5xl md:text-7xl font-black mb-6 leading-tight">
                Pronto para<br><span class="gradient-text">transformar</span> seus treinamentos?
            </h2>
            <p class="fade-in fade-in-delay-1 text-xl text-gray-400 mb-12 max-w-2xl mx-auto">Comece agora com 7 dias grátis. Sem cartão de crédito. Sem compromisso. Cancele quando quiser.</p>
            <div class="fade-in fade-in-delay-2 flex flex-wrap justify-center gap-4">
                <a href="{{ route('register') }}"
                   class="inline-flex items-center gap-3 bg-indigo-500 hover:bg-indigo-600 text-white px-8 py-4 rounded-2xl text-lg font-bold transition shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40">
                    Começar grátis agora
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
                <a href="https://wa.me/5528999743099?text=Olá! Gostaria de saber mais sobre o TreinaEdu." target="_blank"
                   class="inline-flex items-center gap-3 bg-white/5 hover:bg-white/10 border border-white/10 text-white px-8 py-4 rounded-2xl text-lg font-medium transition">
                    <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Falar com vendas
                </a>
            </div>
            <p class="fade-in fade-in-delay-3 text-sm text-gray-600 mt-12">treinaedu.com.br</p>
        </div>
    </section>

    <script>
        // Intersection Observer for fade-in animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));

        // Navigation dots
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.slide-indicator');

        const slideObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const idx = Array.from(slides).indexOf(entry.target);
                    dots.forEach((d, i) => {
                        d.classList.toggle('active', i === idx);
                    });
                }
            });
        }, { threshold: 0.5 });

        slides.forEach(s => slideObserver.observe(s));

        function goSlide(n) {
            slides[n]?.scrollIntoView({ behavior: 'smooth' });
        }

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            const current = Array.from(slides).findIndex(s => {
                const rect = s.getBoundingClientRect();
                return rect.top >= -100 && rect.top < window.innerHeight / 2;
            });
            if (e.key === 'ArrowDown' || e.key === 'ArrowRight' || e.key === ' ') {
                e.preventDefault();
                goSlide(Math.min(current + 1, slides.length - 1));
            }
            if (e.key === 'ArrowUp' || e.key === 'ArrowLeft') {
                e.preventDefault();
                goSlide(Math.max(current - 1, 0));
            }
        });
    </script>

</body>
</html>
