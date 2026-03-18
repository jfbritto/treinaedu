<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-screen flex">

    {{-- Painel esquerdo — branding --}}
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-blue-700 via-blue-600 to-indigo-700 flex-col justify-between p-12 relative overflow-hidden">

        {{-- Círculos decorativos --}}
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-white/5 rounded-full"></div>
        <div class="absolute -bottom-32 -right-16 w-[500px] h-[500px] bg-white/5 rounded-full"></div>
        <div class="absolute top-1/2 left-1/4 w-48 h-48 bg-white/5 rounded-full"></div>

        {{-- Logo --}}
        <div class="relative z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <span class="text-white text-xl font-bold tracking-tight">TreinaEdu</span>
            </div>
        </div>

        {{-- Conteúdo central --}}
        <div class="relative z-10 space-y-8">
            <div>
                <h2 class="text-4xl font-bold text-white leading-tight">
                    Capacite sua equipe.<br>
                    <span class="text-blue-200">Certifique com confiança.</span>
                </h2>
                <p class="mt-4 text-blue-100 text-lg leading-relaxed">
                    Plataforma completa de treinamentos corporativos com vídeos, quizzes e certificados digitais.
                </p>
            </div>

            {{-- Destaques --}}
            <div class="space-y-4">
                @foreach([
                    ['icon' => 'M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'text' => 'Vídeos com rastreamento de progresso'],
                    ['icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'text' => 'Quizzes automáticos e notas de aprovação'],
                    ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'text' => 'Certificados digitais verificáveis'],
                ] as $item)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}" />
                            </svg>
                        </div>
                        <span class="text-blue-50 text-sm">{{ $item['text'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Rodapé --}}
        <div class="relative z-10">
            <p class="text-blue-200 text-sm">© {{ date('Y') }} TreinaEdu. Todos os direitos reservados.</p>
        </div>
    </div>

    {{-- Painel direito — formulário --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-gray-50">
        <div class="w-full max-w-sm">

            {{-- Logo mobile (visível só em telas pequenas) --}}
            <div class="flex items-center gap-2 mb-8 lg:hidden">
                <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <span class="text-blue-600 text-xl font-bold">TreinaEdu</span>
            </div>

            {{ $slot }}
        </div>
    </div>

</body>
</html>
