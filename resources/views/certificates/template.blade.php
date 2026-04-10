<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Certificado de Conclusão</title>
    <style>
        @page { margin: 0; size: A4 landscape; }

        * { margin: 0; padding: 0; }

        html, body {
            font-family: 'Helvetica', Arial, sans-serif;
            color: #1f2937;
            background: #ffffff;
        }

        .certificate {
            width: 297mm;
            height: 210mm;
            text-align: center;
        }

        .certificate-inner {
            width: 100%;
            height: 210mm;
            border-collapse: collapse;
        }
        .certificate-inner td {
            vertical-align: middle;
            text-align: center;
            padding: 28mm 32mm 50mm 32mm;
        }

        /* Decorative borders */
        .border-top,
        .border-bottom {
            position: fixed;
            left: 12mm;
            right: 12mm;
            height: 2px;
            background-color: {{ $primaryColor }};
        }
        .border-top { top: 10mm; }
        .border-bottom { bottom: 10mm; }

        .border-accent-top,
        .border-accent-bottom {
            position: fixed;
            left: 12mm;
            right: 12mm;
            height: 1px;
            background-color: {{ $primaryColor }};
        }
        .border-accent-top { top: 13mm; }
        .border-accent-bottom { bottom: 13mm; }

        /* Corner ornaments */
        .corner-tl, .corner-tr, .corner-bl, .corner-br {
            position: fixed;
            width: 10mm;
            height: 10mm;
        }
        .corner-tl { top: 14mm; left: 14mm; border-top: 1.5px solid {{ $primaryColor }}; border-left: 1.5px solid {{ $primaryColor }}; }
        .corner-tr { top: 14mm; right: 14mm; border-top: 1.5px solid {{ $primaryColor }}; border-right: 1.5px solid {{ $primaryColor }}; }
        .corner-bl { bottom: 14mm; left: 14mm; border-bottom: 1.5px solid {{ $primaryColor }}; border-left: 1.5px solid {{ $primaryColor }}; }
        .corner-br { bottom: 14mm; right: 14mm; border-bottom: 1.5px solid {{ $primaryColor }}; border-right: 1.5px solid {{ $primaryColor }}; }

        .logo {
            max-height: 48px;
            max-width: 160px;
        }

        .company-name {
            font-size: 13px;
            font-weight: bold;
            color: {{ $secondaryColor }};
            letter-spacing: 0.5px;
        }

        .separator-wrap {
            margin-top: 10px;
            margin-bottom: 10px;
            text-align: center;
        }
        .separator-line {
            display: inline-block;
            width: 70px;
            height: 1px;
            background: {{ $primaryColor }};
            vertical-align: middle;
        }
        .separator-label {
            display: inline-block;
            font-size: 9px;
            color: {{ $secondaryColor }};
            letter-spacing: 4px;
            padding: 0 12px;
            vertical-align: middle;
        }

        .title {
            font-size: 52px;
            font-weight: bold;
            color: {{ $secondaryColor }};
            letter-spacing: 4px;
            line-height: 1.1;
            margin-bottom: 2px;
        }

        .subtitle {
            font-size: 20px;
            font-weight: normal;
            font-style: italic;
            color: {{ $primaryColor }};
            letter-spacing: 1.5px;
            margin-bottom: 16px;
        }

        .certifies {
            font-size: 11px;
            color: #6b7280;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .recipient {
            font-size: 36px;
            font-weight: bold;
            color: {{ $secondaryColor }};
            margin-bottom: 14px;
            letter-spacing: 0.5px;
        }

        .highlight-box {
            margin: 0 auto;
            max-width: 200mm;
            padding: 12px 20px;
            border-top: 2px solid {{ $primaryColor }};
            border-bottom: 2px solid {{ $primaryColor }};
            background-color: #f5f9ff;
        }

        .completed {
            font-size: 10px;
            color: #6b7280;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .training-title {
            font-size: 22px;
            font-weight: bold;
            color: #1f2937;
            line-height: 1.3;
            margin-bottom: 6px;
        }

        .meta {
            font-size: 11px;
            color: #6b7280;
        }
        .meta strong {
            color: #1f2937;
            font-weight: bold;
        }

        /* Footer — fixed to the bottom */
        .footer {
            position: fixed;
            left: 28mm;
            right: 28mm;
            bottom: 16mm;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        .footer-cell {
            text-align: center;
            vertical-align: bottom;
            padding: 0 6px;
        }
        .footer-cell-side {
            width: 28%;
        }
        .footer-cell-center {
            width: 44%;
        }
        .footer-label {
            font-size: 8px;
            color: #9ca3af;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .footer-value {
            font-size: 12px;
            color: {{ $secondaryColor }};
            font-weight: bold;
        }
        .footer-code {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            color: #4b5563;
            font-weight: bold;
        }
        .qrcode {
            width: 60px;
            height: 60px;
        }

        /* Signer in footer center */
        .signer-signature {
            max-height: 36px;
            max-width: 140px;
        }
        .signer-line {
            width: 140px;
            height: 1px;
            background: #9ca3af;
            margin: 4px auto 4px auto;
        }
        .signer-name {
            font-size: 11px;
            font-weight: bold;
            color: #1f2937;
        }
        .signer-role {
            font-size: 9px;
            color: #6b7280;
        }
        .signer-registry {
            font-size: 8px;
            color: #9ca3af;
            margin-top: 1px;
        }

        .verified-by {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 6mm;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
            letter-spacing: 0.5px;
        }
        .verified-by strong { color: {{ $primaryColor }}; }
    </style>
</head>
<body>
    <div class="certificate">

        {{-- Decorative frame --}}
        <div class="border-top"></div>
        <div class="border-accent-top"></div>
        <div class="border-bottom"></div>
        <div class="border-accent-bottom"></div>
        <div class="corner-tl"></div>
        <div class="corner-tr"></div>
        <div class="corner-bl"></div>
        <div class="corner-br"></div>

        <table class="certificate-inner">
            <tr>
                <td>

                    {{-- Company identity --}}
                    @if(!empty($companyLogo))
                        <img src="{{ storage_path('app/public/' . $companyLogo) }}" alt="{{ $companyName }}" class="logo">
                    @else
                        <div class="company-name">{{ $companyName }}</div>
                    @endif

                    {{-- Separator --}}
                    <div class="separator-wrap">
                        <span class="separator-line"></span>
                        <span class="separator-label">APRESENTA</span>
                        <span class="separator-line"></span>
                    </div>

                    {{-- Title --}}
                    <div class="title">CERTIFICADO</div>
                    <div class="subtitle">de Conclusão</div>

                    {{-- Body --}}
                    <div class="certifies">Certificamos que</div>
                    <div class="recipient">{{ $userName }}</div>

                    <div class="highlight-box">
                        <div class="completed">concluiu com sucesso o treinamento</div>
                        <div class="training-title">{{ $trainingTitle }}</div>

                        @php
                            $mins = (int) $durationMinutes;
                            $durLabel = $mins >= 60
                                ? floor($mins/60).'h'.($mins%60 > 0 ? ' '.($mins%60).'min' : '')
                                : ($mins > 0 ? $mins.' min' : null);
                        @endphp

                        @if($durLabel || $companyName)
                            <div class="meta">
                                @if($durLabel)
                                    carga horária de <strong>{{ $durLabel }}</strong>
                                @endif
                                @if($durLabel && $companyName)
                                    &nbsp;·&nbsp;
                                @endif
                                @if($companyName)
                                    emitido por <strong>{{ $companyName }}</strong>
                                @endif
                            </div>
                        @endif
                    </div>

                </td>
            </tr>
        </table>

        {{-- Footer with signer in center --}}
        <div class="footer">
            <table class="footer-table">
                <tr>
                    {{-- Left: emission date + code --}}
                    <td class="footer-cell footer-cell-side">
                        <div class="footer-label">Emitido em</div>
                        <div class="footer-value">{{ $completionDate }}</div>
                        <div style="margin-top: 6px;">
                            <div class="footer-label">Código</div>
                            <div class="footer-code">{{ $certificateCode }}</div>
                        </div>
                    </td>

                    {{-- Center: signer --}}
                    <td class="footer-cell footer-cell-center">
                        @if(!empty($signerName))
                            @if(!empty($signerSignaturePath))
                                <img src="{{ $signerSignaturePath }}" class="signer-signature" alt="Assinatura">
                            @endif
                            <div class="signer-line"></div>
                            <div class="signer-name">{{ $signerName }}</div>
                            @if(!empty($signerRole))
                                <div class="signer-role">{{ $signerRole }}</div>
                            @endif
                            @if(!empty($signerRegistry))
                                <div class="signer-registry">{{ $signerRegistry }}</div>
                            @endif
                        @endif
                    </td>

                    {{-- Right: QR code --}}
                    <td class="footer-cell footer-cell-side">
                        <div class="footer-label">Verificar</div>
                        @if(!empty($qrCodeDataUri))
                            <img src="{{ $qrCodeDataUri }}" class="qrcode" alt="QR Code">
                        @else
                            <div class="footer-code" style="font-size: 7px;">{{ $verifyUrl }}</div>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <div class="verified-by">
            Verificado por <strong>TreinaEdu</strong>
        </div>

    </div>
</body>
</html>
