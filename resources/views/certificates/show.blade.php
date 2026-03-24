<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificado de {{ $certificate->user->name }} — {{ $certificate->training->title }}</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%233B82F6'><path d='M11.25 4.533A9.707 9.707 0 006 3a9.735 9.735 0 00-3.25.555.75.75 0 00-.5.707v14.25a.75.75 0 001 .707A8.237 8.237 0 016 18.75c1.995 0 3.823.707 5.25 1.886V4.533zM12.75 20.636A8.214 8.214 0 0118 18.75c.966 0 1.89.166 2.75.47a.75.75 0 001-.708V4.262a.75.75 0 00-.5-.707A9.735 9.735 0 0018 3a9.707 9.707 0 00-5.25 1.533v16.103z'/></svg>">

    {{-- Open Graph (LinkedIn, WhatsApp, etc.) --}}
    <meta property="og:title" content="{{ $certificate->user->name }} concluiu {{ $certificate->training->title }}">
    <meta property="og:description" content="Certificado de conclusão emitido por {{ $certificate->company->name }} via TreinaEdu. Emitido em {{ $certificate->generated_at->format('d/m/Y') }}.">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Inter:wght@300;400;500;600;700&display=swap');
        .cert-font { font-family: 'Playfair Display', Georgia, serif; }
        .body-font { font-family: 'Inter', sans-serif; }
        .cert-decoration {
            background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.1), transparent);
        }
        .cert-badge {
            position: absolute;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: 3px solid #3B82F6;
        }
        @media print {
            body { background: white; }
            .print-hidden { display: none; }
        }
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
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="relative p-16 text-center print:p-12"
                 style="background: linear-gradient(135deg, #ffffff 0%, #f0f7ff 50%, #ffffff 100%); min-height: 600px; display: flex; flex-direction: column; justify-content: space-between;">

                {{-- Decoração superior --}}
                <div class="absolute top-0 left-0 right-0 h-1 cert-decoration"></div>
                <div class="absolute top-8 left-0 right-0 flex justify-around opacity-5">
                    <div class="w-32 h-32 rounded-full border-2" style="border-color: #3B82F6;"></div>
                    <div class="w-24 h-24 rounded-full border-2" style="border-color: #3B82F6;"></div>
                    <div class="w-32 h-32 rounded-full border-2" style="border-color: #3B82F6;"></div>
                </div>

                <div class="relative z-10">
                    {{-- Logo e empresa --}}
                    <div class="mb-6">
                        @if($certificate->company->logo_path)
                            <img src="{{ Storage::disk('public')->url($certificate->company->logo_path) }}"
                                 alt="{{ $certificate->company->name }}"
                                 class="h-16 object-contain mx-auto">
                        @else
                            <div class="text-blue-900 font-bold text-xl">{{ $certificate->company->name }}</div>
                        @endif
                    </div>

                    {{-- Linha decorativa --}}
                    <div class="flex items-center justify-center gap-4 mb-8">
                        <div class="flex-1 h-px" style="background: linear-gradient(90deg, transparent, #3B82F6, transparent);"></div>
                        <div class="text-blue-900 font-light text-xs tracking-widest">APRESENTA</div>
                        <div class="flex-1 h-px" style="background: linear-gradient(90deg, transparent, #3B82F6, transparent);"></div>
                    </div>

                    {{-- Título principal --}}
                    <div class="cert-font text-5xl font-bold text-blue-900 tracking-wider mb-2" style="letter-spacing: 2px;">
                        CERTIFICADO
                    </div>
                    <div class="cert-font text-2xl font-light text-blue-700 mb-12" style="letter-spacing: 1px;">
                        de Conclusão
                    </div>

                    {{-- Corpo principal --}}
                    <p class="text-gray-600 text-sm tracking-wide mb-4">Certificamos que</p>

                    <p class="cert-font text-5xl font-bold text-blue-900 mb-6" style="letter-spacing: 1px;">
                        {{ $certificate->user->name }}
                    </p>

                    <div class="border-t-2 border-b-2 border-blue-200 py-6 mb-8 px-8" style="background: rgba(59, 130, 246, 0.02);">
                        <p class="text-gray-600 text-sm mb-3">concluiu com sucesso o treinamento</p>
                        <p class="cert-font text-3xl font-bold text-gray-800 mb-3">
                            {{ $certificate->training->title }}
                        </p>
                        @php
                            $mins = $certificate->training->calculatedDuration();
                            $durLabel = $mins >= 60
                                ? floor($mins/60).'h'.($mins%60 > 0 ? ' '.($mins%60).'min' : '')
                                : $mins.' min';
                        @endphp
                        <p class="text-gray-600 text-sm">
                            com carga horária de <strong class="text-gray-800">{{ $durLabel }}</strong>
                        </p>
                    </div>

                    {{-- Data e footer --}}
                    <div class="flex flex-col md:flex-row items-center justify-between gap-6 text-xs text-gray-600">
                        <div class="text-center md:text-left">
                            <p class="uppercase tracking-wider text-gray-400 text-xs mb-1">Emitido em</p>
                            <p class="cert-font text-lg font-semibold text-gray-800">
                                {{ $certificate->generated_at->locale('pt_BR')->translatedFormat('d \d\e F \d\e Y') }}
                            </p>
                        </div>

                        {{-- Código do certificado --}}
                        <div class="text-center">
                            <p class="uppercase tracking-wider text-gray-400 text-xs mb-2">Código</p>
                            <p class="font-mono text-sm text-blue-900 font-semibold">{{ $certificate->certificate_code }}</p>
                        </div>

                        {{-- QR Code --}}
                        <div class="text-center">
                            <p class="uppercase tracking-wider text-gray-400 text-xs mb-2">Verificar</p>
                            <div id="qrcode" class="flex justify-center"></div>
                        </div>
                    </div>
                </div>

                {{-- Linha decorativa inferior --}}
                <div class="absolute bottom-0 left-0 right-0 h-1 cert-decoration"></div>
            </div>
        </div>

        {{-- Compartilhar --}}
        <div class="bg-white rounded-xl shadow-md p-6 print:hidden">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                </svg>
                <h2 class="text-sm font-semibold text-gray-700">Compartilhar conquista</h2>
            </div>

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
        <div class="bg-white rounded-xl shadow-md p-6 print:hidden">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h2 class="text-sm font-semibold text-gray-700">Detalhes do certificado</h2>
            </div>
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

        // Generate QR Code
        document.addEventListener('DOMContentLoaded', function() {
            const qrcodeContainer = document.getElementById('qrcode');
            const verifyUrl = '{{ url("/certificate/verify") }}?code={{ $certificate->certificate_code }}';
            new QRCode(qrcodeContainer, {
                text: verifyUrl,
                width: 100,
                height: 100,
                colorDark: '#3B82F6',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });
        });
    </script>
</body>
</html>
