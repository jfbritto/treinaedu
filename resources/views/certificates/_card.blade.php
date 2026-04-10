@php
    $company = $certificate->company;
    $mins = $certificate->training->calculatedDuration();
    $durLabel = $mins >= 60
        ? floor($mins/60).'h'.($mins%60 > 0 ? ' '.($mins%60).'min' : '')
        : $mins.' min';

    $shareUrl = route('certificate.show', $certificate->certificate_code);
    $shareText = urlencode($certificate->user->name . ' concluiu o treinamento "' . $certificate->training->title . '" pela ' . $company->name . '. Veja o certificado:');
    $linkedinUrl = 'https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode($shareUrl);
    $twitterUrl = 'https://twitter.com/intent/tweet?text=' . $shareText . '&url=' . urlencode($shareUrl);
    $whatsappUrl = 'https://api.whatsapp.com/send?text=' . $shareText . '%20' . urlencode($shareUrl);

    $linkedinAddUrl = 'https://www.linkedin.com/profile/add?startTask=CERTIFICATION_NAME'
        . '&name=' . urlencode($certificate->training->title)
        . '&organizationName=' . urlencode($company->name)
        . '&issueYear=' . $certificate->generated_at->year
        . '&issueMonth=' . $certificate->generated_at->month
        . '&certUrl=' . urlencode($shareUrl)
        . '&certId=' . urlencode($certificate->certificate_code);

    $modules = $certificate->training->modules()->with('lessons', 'quiz')->orderBy('sort_order')->get();

    $titleText = $company->cert_title_text ?? 'CERTIFICADO';
    $subtitleText = $company->cert_subtitle_text ?? 'de Conclusão';
    $borderStyle = $company->cert_border_style ?? 'classic';
@endphp

{{-- Certificate visual - modern layout --}}
<div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
    <div class="relative flex" style="min-height: 500px;">

        {{-- Left accent bar --}}
        <div class="w-5 flex-shrink-0 relative" style="background: var(--primary);">
            <div class="absolute top-4 left-2 bottom-4 w-1 rounded-full" style="background: var(--secondary); opacity: 0.3;"></div>
        </div>

        {{-- Content --}}
        <div class="flex-1 relative">

            {{-- Frame lines --}}
            @if($borderStyle !== 'none')
                <div class="absolute top-4 left-4 right-4 h-px" style="background: var(--primary); opacity: 0.2;"></div>
                <div class="absolute bottom-4 left-4 right-4 h-px" style="background: var(--primary); opacity: 0.2;"></div>
            @endif
            @if($borderStyle === 'classic')
                <div class="absolute top-4 right-4 w-8 h-8 border-t-2 border-r-2" style="border-color: var(--primary); opacity: 0.4;"></div>
                <div class="absolute bottom-4 right-4 w-8 h-8 border-b-2 border-r-2" style="border-color: var(--primary); opacity: 0.4;"></div>
            @endif

            <div class="p-8 md:p-12">
                {{-- Logo --}}
                <div class="mb-5">
                    @if($company->logo_path)
                        <img src="{{ Storage::disk('public')->url($company->logo_path) }}"
                             alt="{{ $company->name }}"
                             class="h-12 object-contain">
                    @else
                        <div class="text-sm font-bold tracking-wide" style="color: var(--primary)">{{ $company->name }}</div>
                    @endif
                </div>

                {{-- Title --}}
                <h1 class="text-4xl md:text-5xl font-bold tracking-wider mb-1" style="color: var(--primary); opacity: 0.85; letter-spacing: 3px;">
                    {{ $titleText }}
                </h1>
                <p class="text-lg md:text-xl font-light mb-8" style="color: var(--secondary); letter-spacing: 1px;">
                    {{ $subtitleText }}
                </p>

                {{-- Divider --}}
                <div class="w-12 h-1 rounded mb-4" style="background: var(--primary);"></div>

                {{-- Certifies --}}
                <p class="text-xs text-gray-400 uppercase tracking-widest mb-3">Certificamos que</p>

                {{-- Name - the star --}}
                <p class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                    {{ $certificate->user->name }}
                </p>
                <div class="w-48 h-0.5 mb-8" style="background: var(--primary);"></div>

                {{-- Training box --}}
                <div class="pl-5 py-4 pr-6 border-l-4 rounded-r-lg max-w-2xl" style="border-color: var(--primary); background: color-mix(in srgb, var(--primary) 3%, #f8fafc);">
                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-2">concluiu com sucesso o treinamento</p>
                    <p class="text-xl md:text-2xl font-bold text-gray-800 mb-1">{{ $certificate->training->title }}</p>
                    <p class="text-sm text-gray-500">
                        Carga horária: <strong class="text-gray-700">{{ $durLabel }}</strong>
                        · Emitido por <strong class="text-gray-700">{{ $company->name }}</strong>
                    </p>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-8 md:px-12 pb-8 md:pb-10">
                <div class="pt-6 border-t border-gray-100">
                    <div class="flex flex-wrap items-end justify-between gap-6">
                        {{-- Left: date + code --}}
                        <div>
                            <p class="uppercase tracking-wider text-gray-400 text-xs mb-1">Emitido em</p>
                            <p class="text-sm font-semibold text-gray-800">
                                {{ $certificate->generated_at->locale('pt_BR')->translatedFormat('d \d\e F \d\e Y') }}
                            </p>
                            <p class="uppercase tracking-wider text-gray-400 text-xs mt-3 mb-1">Código</p>
                            <p class="font-mono text-xs font-semibold text-gray-600">{{ $certificate->certificate_code }}</p>
                        </div>

                        {{-- Center: signer --}}
                        @if($company->cert_signer_name)
                            <div class="text-center">
                                @if($company->cert_signer_signature_path)
                                    <img src="{{ Storage::disk('public')->url($company->cert_signer_signature_path) }}"
                                         alt="Assinatura" class="h-10 object-contain mx-auto mb-1">
                                @endif
                                <div class="w-36 h-px bg-gray-300 mx-auto mb-1"></div>
                                <p class="text-sm font-bold text-gray-800">{{ $company->cert_signer_name }}</p>
                                @if($company->cert_signer_role)
                                    <p class="text-xs text-gray-500">{{ $company->cert_signer_role }}</p>
                                @endif
                                @if($company->cert_signer_registry)
                                    <p class="text-xs text-gray-400">{{ $company->cert_signer_registry }}</p>
                                @endif
                            </div>
                        @endif

                        {{-- Right: QR code --}}
                        <div class="text-center">
                            <div id="qrcode" class="flex justify-center"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Share card --}}
<div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6 print:hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-gray-800">Compartilhar conquista</h3>
                <p class="text-xs text-gray-400">Link público — compartilhe com recrutadores e colegas</p>
            </div>
        </div>
    </div>

    <div class="p-6 space-y-4">
        {{-- Add to LinkedIn Profile --}}
        <a href="{{ $linkedinAddUrl }}" target="_blank" rel="noopener"
            class="flex items-center justify-center gap-3 w-full px-4 py-3 rounded-lg text-sm font-semibold text-white transition hover:opacity-90"
            style="background-color: #0A66C2;">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
            Adicionar ao perfil do LinkedIn
        </a>

        <div class="relative">
            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
            <div class="relative flex justify-center"><span class="bg-white px-3 text-xs text-gray-400">ou compartilhe como post</span></div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <a href="{{ $linkedinUrl }}" target="_blank" rel="noopener"
                class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium text-white transition hover:opacity-90"
                style="background-color: #0A66C2;">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                LinkedIn
            </a>
            <a href="{{ $twitterUrl }}" target="_blank" rel="noopener"
                class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium text-white bg-black hover:bg-gray-800 transition">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.747l7.73-8.835L1.254 2.25H8.08l4.259 5.63L18.244 2.25zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                X
            </a>
            <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener"
                class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium text-white transition hover:opacity-90"
                style="background-color: #25D366;">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                WhatsApp
            </a>
            <button onclick="copyCertLink()" id="copy-btn"
                class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                <span id="copy-label">Copiar link</span>
            </button>
        </div>
    </div>
</div>

{{-- Details card --}}
<div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6 print:hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-gray-800">Detalhes do certificado</h3>
                <p class="text-xs text-gray-400">Informações de validação e conteúdo programático</p>
            </div>
        </div>
    </div>

    <div class="p-6">
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50">
                <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <dt class="text-xs text-gray-400 mb-0.5">Concluinte</dt>
                    <dd class="text-sm font-semibold text-gray-800 truncate">{{ $certificate->user->name }}</dd>
                </div>
            </div>
            <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50">
                <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <dt class="text-xs text-gray-400 mb-0.5">Empresa</dt>
                    <dd class="text-sm font-semibold text-gray-800 truncate">{{ $company->name }}</dd>
                </div>
            </div>
            <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50">
                <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <dt class="text-xs text-gray-400 mb-0.5">Carga horária</dt>
                    <dd class="text-sm font-semibold text-gray-800">{{ $durLabel }}</dd>
                </div>
            </div>
            <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50">
                <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <dt class="text-xs text-gray-400 mb-0.5">Data de emissão</dt>
                    <dd class="text-sm font-semibold text-gray-800">{{ $certificate->generated_at->format('d/m/Y') }}</dd>
                </div>
            </div>
        </dl>

        @if($modules->count() > 0)
            <div class="mt-6 pt-5 border-t border-gray-100">
                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Conteúdo Programático</h4>
                <ul class="space-y-2">
                    @foreach($modules as $module)
                        <li class="flex items-start gap-3 p-3 rounded-lg border border-gray-100">
                            <div class="w-7 h-7 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                                <span class="text-xs font-bold text-primary">{{ $loop->iteration }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800">{{ $module->title }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $module->lessons->count() }} aula{{ $module->lessons->count() !== 1 ? 's' : '' }}{{ $module->quiz ? ' · com avaliação' : '' }}
                                </p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    function copyCertLink() {
        navigator.clipboard.writeText('{{ $shareUrl }}').then(() => {
            const label = document.getElementById('copy-label');
            label.textContent = 'Link copiado!';
            setTimeout(() => label.textContent = 'Copiar link', 2000);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const qrcodeContainer = document.getElementById('qrcode');
        if (!qrcodeContainer) return;
        const verifyUrl = '{{ route('certificate.show', $certificate->certificate_code) }}';
        const primary = getComputedStyle(document.documentElement).getPropertyValue('--primary').trim() || '#4f46e5';
        new QRCode(qrcodeContainer, {
            text: verifyUrl,
            width: 80,
            height: 80,
            colorDark: primary,
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
    });
</script>
@endpush

<style>
    @media print {
        body { background: white; }
        .print\:hidden { display: none !important; }
    }
</style>
