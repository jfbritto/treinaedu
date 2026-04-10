<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificado de {{ $certificate->user->name }} — {{ $certificate->training->title }}</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%233B82F6'><path d='M11.25 4.533A9.707 9.707 0 006 3a9.735 9.735 0 00-3.25.555.75.75 0 00-.5.707v14.25a.75.75 0 001 .707A8.237 8.237 0 016 18.75c1.995 0 3.823.707 5.25 1.886V4.533zM12.75 20.636A8.214 8.214 0 0118 18.75c.966 0 1.89.166 2.75.47a.75.75 0 001-.708V4.262a.75.75 0 00-.5-.707A9.735 9.735 0 0018 3a9.707 9.707 0 00-5.25 1.533v16.103z'/></svg>">

    {{-- Open Graph --}}
    <meta property="og:title" content="{{ $certificate->user->name }} concluiu {{ $certificate->training->title }}">
    <meta property="og:description" content="Certificado de conclusão emitido por {{ $certificate->company->name }} via TreinaEdu. Emitido em {{ $certificate->generated_at->format('d/m/Y') }}.">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ route('certificate.og-image', $certificate->certificate_code) }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $certificate->user->name }} concluiu {{ $certificate->training->title }}">
    <meta name="twitter:description" content="Certificado emitido por {{ $certificate->company->name }} via TreinaEdu.">
    <meta name="twitter:image" content="{{ route('certificate.og-image', $certificate->certificate_code) }}">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary:   'var(--primary)',
                        secondary: 'var(--secondary)',
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    @php
        $safePrimary = preg_match('/^#[0-9A-Fa-f]{3,6}$/', $certificate->company->primary_color ?? '') ? $certificate->company->primary_color : '#4f46e5';
        $safeSecondary = preg_match('/^#[0-9A-Fa-f]{3,6}$/', $certificate->company->secondary_color ?? '') ? $certificate->company->secondary_color : '#3730a3';
    @endphp
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        :root {
            --primary: {{ $safePrimary }};
            --secondary: {{ $safeSecondary }};
        }
        body { font-family: 'Inter', sans-serif; }
        .bg-primary   { background-color: var(--primary); }
        .text-primary { color: var(--primary); }
        .bg-primary\/10 { background-color: color-mix(in srgb, var(--primary) 10%, transparent); }
    </style>
</head>
<body class="bg-gray-50">

    {{-- Header --}}
    <header class="bg-white border-b border-gray-100 px-6 py-4 print:hidden">
        <div class="max-w-5xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-2">
                @if($certificate->company->logo_path)
                    <img src="{{ Storage::disk('public')->url($certificate->company->logo_path) }}" alt="{{ $certificate->company->name }}" class="h-8 object-contain">
                @endif
                <span class="text-sm font-semibold text-gray-700">{{ $certificate->company->name }}</span>
            </div>
            <div class="flex items-center gap-1.5 text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded-full px-3 py-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Certificado válido
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-8">
        @include('certificates._card')
    </main>

    <footer class="text-center text-xs text-gray-400 py-6 print:hidden">
        Verificado por <span class="font-semibold" style="color: var(--primary)">TreinaEdu</span>
    </footer>

    {{-- Scripts pushed from the partial --}}
    @stack('scripts')
</body>
</html>
