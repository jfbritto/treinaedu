<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificado de {{ $certificate->user->name }} — {{ $certificate->training->title }}</title>

    {{-- Open Graph (LinkedIn, WhatsApp, etc.) --}}
    <meta property="og:title" content="{{ $certificate->user->name }} concluiu {{ $certificate->training->title }}">
    <meta property="og:description" content="Certificado de conclusão emitido por {{ $certificate->company->name }} via TreinaEdu. Emitido em {{ $certificate->generated_at->format('d/m/Y') }}.">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">

    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@400;500;600&display=swap');
        .cert-font { font-family: 'Playfair Display', Georgia, serif; }
        .body-font { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 body-font">

    {{-- Header --}}
    <header class="bg-white border-b border-gray-200 px-6 py-4">
        <div class="max-w-4xl mx-auto flex items-center justify-between">
            <span class="text-lg font-bold text-blue-600">TreinaEdu</span>
            <div class="flex items-center gap-1.5 text-sm text-green-700 bg-green-50 border border-green-200 rounded-full px-3 py-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Certificado válido
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-8 space-y-6">

        {{-- Certificado visual --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="relative p-10 border-8 border-blue-900 m-4 rounded-xl text-center"
                 style="background: linear-gradient(135deg, #f8faff 0%, #eef2ff 100%);">
                {{-- Inner border --}}
                <div class="absolute inset-3 border-2 border-blue-900 rounded-lg pointer-events-none opacity-40"></div>

                {{-- Logo da empresa --}}
                @if($certificate->company->logo_path)
                    <img src="{{ Storage::url($certificate->company->logo_path) }}"
                         alt="{{ $certificate->company->name }}"
                         class="h-14 object-contain mx-auto mb-6">
                @else
                    <div class="text-blue-900 font-bold text-lg mb-6">{{ $certificate->company->name }}</div>
                @endif

                {{-- Título --}}
                <div class="cert-font text-3xl font-bold text-blue-900 tracking-widest uppercase mb-8">
                    Certificado de Conclusão
                </div>

                {{-- Corpo --}}
                <p class="text-gray-500 text-sm mb-2">Certificamos que</p>
                <p class="cert-font text-4xl font-bold text-blue-900 mb-4">{{ $certificate->user->name }}</p>
                <p class="text-gray-600 text-sm mb-2">concluiu com sucesso o treinamento</p>
                <p class="cert-font text-2xl font-bold text-gray-800 mb-4">{{ $certificate->training->title }}</p>
                <p class="text-gray-500 text-sm">
                    @php
                        $mins = $certificate->training->calculatedDuration();
                        $durLabel = $mins >= 60
                            ? floor($mins/60).'h'.($mins%60 > 0 ? ' '.($mins%60).'min' : '')
                            : $mins.' min';
                    @endphp
                    com carga horária de <strong>{{ $durLabel }}</strong>,
                    na empresa <strong>{{ $certificate->company->name }}</strong>.
                </p>

                {{-- Data --}}
                <div class="mt-8 text-gray-500 text-sm">
                    Data de conclusão: <strong>{{ $certificate->generated_at->locale('pt_BR')->translatedFormat('d \d\e F \d\e Y') }}</strong>
                </div>

                {{-- Rodapé com código --}}
                <div class="mt-8 pt-4 border-t border-blue-200 flex items-center justify-between text-xs text-gray-400">
                    <span class="font-mono">Código: {{ $certificate->certificate_code }}</span>
                    <span>{{ url('/certificate/verify') }}</span>
                </div>
            </div>
        </div>

        {{-- Compartilhar --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Compartilhar conquista</h2>

            @php
                $shareUrl = url()->current();
                $shareText = urlencode($certificate->user->name . ' concluiu o treinamento "' . $certificate->training->title . '" pela ' . $certificate->company->name . '. Veja o certificado:');
                $linkedinUrl = 'https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode($shareUrl);
                $twitterUrl = 'https://twitter.com/intent/tweet?text=' . $shareText . '&url=' . urlencode($shareUrl);
                $whatsappUrl = 'https://api.whatsapp.com/send?text=' . $shareText . '%20' . urlencode($shareUrl);
            @endphp

            <div class="flex flex-wrap gap-3">
                {{-- LinkedIn --}}
                <a href="{{ $linkedinUrl }}" target="_blank" rel="noopener"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium text-white transition"
                    style="background-color: #0A66C2;">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                    </svg>
                    LinkedIn
                </a>

                {{-- Twitter / X --}}
                <a href="{{ $twitterUrl }}" target="_blank" rel="noopener"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium text-white bg-black transition hover:bg-gray-800">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.747l7.73-8.835L1.254 2.25H8.08l4.259 5.63L18.244 2.25zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                    X (Twitter)
                </a>

                {{-- WhatsApp --}}
                <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium text-white transition"
                    style="background-color: #25D366;">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    WhatsApp
                </a>

                {{-- Copiar link --}}
                <button onclick="copyLink()" id="copy-btn"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                    <span id="copy-label">Copiar link</span>
                </button>
            </div>

            <p class="text-xs text-gray-400 mt-4">
                Este link é público e pode ser compartilhado com recrutadores, colegas e redes profissionais.
            </p>
        </div>

        {{-- Info do certificado --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Detalhes do certificado</h2>
            <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                <div>
                    <dt class="text-gray-400 text-xs uppercase tracking-wide mb-0.5">Concluinte</dt>
                    <dd class="font-medium text-gray-800">{{ $certificate->user->name }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400 text-xs uppercase tracking-wide mb-0.5">Empresa</dt>
                    <dd class="font-medium text-gray-800">{{ $certificate->company->name }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400 text-xs uppercase tracking-wide mb-0.5">Treinamento</dt>
                    <dd class="font-medium text-gray-800">{{ $certificate->training->title }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400 text-xs uppercase tracking-wide mb-0.5">Carga horária</dt>
                    <dd class="font-medium text-gray-800">{{ $durLabel }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400 text-xs uppercase tracking-wide mb-0.5">Data de emissão</dt>
                    <dd class="font-medium text-gray-800">{{ $certificate->generated_at->format('d/m/Y') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400 text-xs uppercase tracking-wide mb-0.5">Código</dt>
                    <dd class="font-mono text-xs text-gray-600 pt-0.5">{{ $certificate->certificate_code }}</dd>
                </div>
            </dl>

            @php
                $modules = $certificate->training->modules()->with('lessons')->orderBy('sort_order')->get();
            @endphp
            @if($modules->count() > 0)
                <div class="mt-5 pt-4 border-t border-gray-100">
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Conteúdo Programático</h3>
                    <ul class="space-y-1 text-sm text-gray-700">
                        @foreach($modules as $module)
                            <li>
                                <span class="font-medium">{{ $module->title }}</span>
                                <span class="text-gray-400 text-xs">({{ $module->lessons->count() }} aula{{ $module->lessons->count() !== 1 ? 's' : '' }}{{ $module->quiz ? ' + avaliação' : '' }})</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

    </main>

    <script>
        function copyLink() {
            navigator.clipboard.writeText(window.location.href).then(() => {
                const label = document.getElementById('copy-label');
                label.textContent = 'Link copiado!';
                setTimeout(() => label.textContent = 'Copiar link', 2000);
            });
        }
    </script>
</body>
</html>
