<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Certificado de Conclusão</title>
    <style>
        @page { margin: 0; size: A4 landscape; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            font-family: 'Helvetica', Arial, sans-serif;
            color: #1f2937;
            background: #ffffff;
        }

        .page {
            width: 297mm;
            height: 210mm;
            position: relative;
            overflow: hidden;
        }

        /* Left accent bar */
        .accent-bar {
            position: fixed;
            top: 0;
            left: 0;
            width: 18mm;
            height: 210mm;
            background: {{ $primaryColor }};
        }
        .accent-bar-inner {
            position: fixed;
            top: 8mm;
            left: 8mm;
            width: 2mm;
            height: 194mm;
            background: {{ $secondaryColor }};
            opacity: 0.3;
        }

        @if(($borderStyle ?? 'classic') !== 'none')
        /* Top and bottom lines */
        .line-top {
            position: fixed;
            top: 8mm;
            left: 24mm;
            right: 8mm;
            height: 1px;
            background: {{ $primaryColor }};
            opacity: 0.3;
        }
        .line-bottom {
            position: fixed;
            bottom: 8mm;
            left: 24mm;
            right: 8mm;
            height: 1px;
            background: {{ $primaryColor }};
            opacity: 0.3;
        }
        @endif

        @if(($borderStyle ?? 'classic') === 'classic')
        .corner-tr {
            position: fixed;
            top: 8mm;
            right: 8mm;
            width: 12mm;
            height: 12mm;
            border-top: 2px solid {{ $primaryColor }};
            border-right: 2px solid {{ $primaryColor }};
        }
        .corner-br {
            position: fixed;
            bottom: 8mm;
            right: 8mm;
            width: 12mm;
            height: 12mm;
            border-bottom: 2px solid {{ $primaryColor }};
            border-right: 2px solid {{ $primaryColor }};
        }
        @endif

        /* Main content area */
        .content {
            position: fixed;
            top: 0;
            left: 24mm;
            right: 0;
            bottom: 0;
        }

        .content-body {
            padding: 18mm 20mm 0 16mm;
        }

        .logo {
            max-height: 44px;
            max-width: 150px;
            margin-bottom: 20px;
        }

        .company-name {
            font-size: 14px;
            font-weight: bold;
            color: {{ $primaryColor }};
            letter-spacing: 1px;
            margin-bottom: 20px;
        }

        .title {
            font-size: {{ $sizeTitle ?? 54 }}px;
            font-weight: bold;
            color: {{ $primaryColor }};
            letter-spacing: 4px;
            line-height: 1;
            margin-bottom: 4px;
            opacity: 0.85;
        }

        .subtitle {
            font-size: {{ max(14, ($sizeTitle ?? 54) * 0.38) }}px;
            font-weight: 300;
            color: {{ $secondaryColor }};
            letter-spacing: 1.5px;
            margin-bottom: 32px;
        }

        .divider {
            width: 50px;
            height: 3px;
            background: {{ $primaryColor }};
            margin-bottom: 14px;
        }

        .certifies {
            font-size: 11px;
            color: #9ca3af;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .recipient {
            font-size: {{ $sizeName ?? 34 }}px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 6px;
        }
        .recipient-line {
            width: 220px;
            height: 2px;
            background: {{ $primaryColor }};
            margin-bottom: 30px;
        }

        .training-box {
            padding: 18px 26px;
            border-left: 4px solid {{ $primaryColor }};
            background-color: #f8fafc;
            text-align: left;
            max-width: 220mm;
        }

        .completed-label {
            font-size: 10px;
            color: #9ca3af;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .training-title {
            font-size: {{ $sizeTraining ?? 20 }}px;
            font-weight: bold;
            color: #1f2937;
            line-height: 1.3;
            margin-bottom: 4px;
        }

        .meta {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
        }
        .meta strong {
            color: #374151;
        }

        /* Footer */
        .footer {
            position: fixed;
            left: 30mm;
            right: 14mm;
            bottom: 18mm;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        .footer-cell {
            vertical-align: bottom;
            padding: 0 8px;
        }
        .footer-cell-left { width: 25%; text-align: left; }
        .footer-cell-center { width: 50%; text-align: center; }
        .footer-cell-right { width: 25%; text-align: right; }

        .footer-label {
            font-size: 10px;
            color: #9ca3af;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .footer-value {
            font-size: 15px;
            color: #374151;
            font-weight: bold;
        }
        .footer-code {
            font-family: 'Courier New', monospace;
            font-size: 13px;
            color: #6b7280;
        }
        .qrcode {
            width: 80px;
            height: 80px;
        }

        .signer-signature {
            max-height: 48px;
            max-width: 160px;
        }
        .signer-line {
            width: 160px;
            height: 1px;
            background: #d1d5db;
            margin: 4px auto;
        }
        .signer-name {
            font-size: 14px;
            font-weight: bold;
            color: #374151;
        }
        .signer-role {
            font-size: 11px;
            color: #6b7280;
        }
        .signer-registry {
            font-size: 10px;
            color: #9ca3af;
        }

        .verified-by {
            position: fixed;
            right: 14mm;
            bottom: 4mm;
            text-align: right;
            font-size: 7px;
            color: #d1d5db;
        }
        .verified-by strong { color: {{ $primaryColor }}; }
    </style>
</head>
<body>
    <div class="page">

        {{-- Left accent bar --}}
        <div class="accent-bar"></div>
        <div class="accent-bar-inner"></div>

        {{-- Optional frame elements --}}
        @if(($borderStyle ?? 'classic') !== 'none')
            <div class="line-top"></div>
            <div class="line-bottom"></div>
        @endif
        @if(($borderStyle ?? 'classic') === 'classic')
            <div class="corner-tr"></div>
            <div class="corner-br"></div>
        @endif

        {{-- Main content --}}
        <div class="content">
            <div class="content-body">
                {{-- Company identity --}}
                @if(!empty($companyLogo))
                    <img src="{{ storage_path('app/public/' . $companyLogo) }}" alt="{{ $companyName }}" class="logo">
                @else
                    <div class="company-name">{{ $companyName }}</div>
                @endif

                {{-- Title --}}
                <div class="title">{{ $titleText ?? 'CERTIFICADO' }}</div>
                <div class="subtitle">{{ $subtitleText ?? 'de Conclusão' }}</div>

                <div class="divider"></div>

                {{-- Body --}}
                <div class="certifies">Certificamos que</div>
                <div class="recipient">{{ $userName }}</div>
                <div class="recipient-line"></div>

                <div class="training-box">
                    <div class="completed-label">concluiu com sucesso o treinamento</div>
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
                                Carga horária: <strong>{{ $durLabel }}</strong>
                            @endif
                            @if($durLabel && $companyName) · @endif
                            @if($companyName)
                                Emitido por <strong>{{ $companyName }}</strong>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <table class="footer-table">
                <tr>
                    <td class="footer-cell footer-cell-left">
                        <div class="footer-label">Emitido em</div>
                        <div class="footer-value">{{ $completionDate }}</div>
                        <div style="margin-top: 4px;">
                            <div class="footer-label">Código</div>
                            <div class="footer-code">{{ $certificateCode }}</div>
                        </div>
                    </td>

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

                    <td class="footer-cell footer-cell-right">
                        @if(!empty($qrCodeDataUri))
                            <img src="{{ $qrCodeDataUri }}" class="qrcode" alt="QR Code">
                        @else
                            <div class="footer-label">Verificar</div>
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
