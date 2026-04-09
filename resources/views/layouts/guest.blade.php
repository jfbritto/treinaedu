<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        <link rel="stylesheet" href="{{ \Illuminate\Support\Facades\Vite::asset('resources/css/app.css') }}">
        <script src="{{ \Illuminate\Support\Facades\Vite::asset('resources/js/app.js') }}" type="module"></script>
        <style>
            body { font-family: 'Inter', system-ui, sans-serif; }
        </style>
    </head>
    <body class="text-gray-900 antialiased">
        <div class="min-h-screen flex">
            {{-- Left panel - Branding --}}
            <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-indigo-600 to-indigo-800 text-white flex-col justify-between p-12 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>

                <div class="relative">
                    <a href="/" class="flex items-center gap-2.5">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <span class="text-2xl font-bold">TreinaEdu</span>
                    </a>
                </div>

                <div class="relative">
                    <h1 class="text-4xl font-extrabold leading-tight mb-4">
                        Capacite sua equipe.<br>
                        <span class="text-indigo-200">Certifique com confiança.</span>
                    </h1>
                    <p class="text-indigo-200 text-lg leading-relaxed max-w-md">
                        Treinamentos em vídeo, quizzes com IA, trilhas de aprendizagem e certificados digitais verificáveis.
                    </p>
                </div>

                <div class="relative text-sm text-indigo-300">
                    &copy; {{ date('Y') }} TreinaEdu · Plataforma de Treinamentos Corporativos
                </div>
            </div>

            {{-- Right panel - Form --}}
            <div class="flex-1 flex flex-col justify-center items-center px-6 py-12 bg-gray-50">
                <div class="lg:hidden mb-8">
                    <a href="/" class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-gray-900">TreinaEdu</span>
                    </a>
                </div>

                <div class="w-full sm:max-w-md">
                    <div class="bg-white shadow-sm rounded-2xl border border-gray-100 px-8 py-8">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
