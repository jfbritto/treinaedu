<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Certificado de Conclusão</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        @page { margin: 0; size: A4 portrait; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #ffffff;
            width: 210mm;
            height: 297mm;
        }

        .certificate {
            width: 210mm;
            height: 297mm;
            background: linear-gradient(135deg, #ffffff 0%, #f0f7ff 50%, #ffffff 100%);
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            text-align: center;
            padding: 40px 35px;
            page-break-after: always;
        }

        .header {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }

        .logo {
            max-height: 50px;
            max-width: 150px;
            margin-bottom: 20px;
            object-fit: contain;
        }

        .company-name {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 16px;
            font-weight: 600;
            color: #1e3a8a;
            letter-spacing: 0.5px;
            margin-bottom: 25px;
        }

        .decoration-top {
            width: 100%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.2), transparent);
            margin-bottom: 20px;
        }

        .title-wrapper {
            margin-bottom: 30px;
        }

        .subtitle {
            font-size: 10px;
            color: #6b7280;
            letter-spacing: 1px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .title {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 48px;
            font-weight: 700;
            color: #1e3a8a;
            letter-spacing: 2px;
            margin-bottom: 4px;
        }

        .title-subtext {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 22px;
            font-weight: 300;
            color: #3b82f6;
            letter-spacing: 1px;
        }

        .content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            margin: 20px 0;
            width: 100%;
        }

        .certifies {
            font-size: 11px;
            color: #6b7280;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        .recipient {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 40px;
            font-weight: 700;
            color: #1e3a8a;
            margin-bottom: 20px;
            letter-spacing: 0.5px;
        }

        .highlight-box {
            border-top: 2px solid #3b82f6;
            border-bottom: 2px solid #3b82f6;
            background: rgba(59, 130, 246, 0.03);
            padding: 20px 25px;
            margin: 20px 0;
        }

        .completed {
            font-size: 11px;
            color: #4b5563;
            margin-bottom: 8px;
            letter-spacing: 0.3px;
        }

        .training-title {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 28px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 12px;
        }

        .meta {
            font-size: 10px;
            color: #6b7280;
            line-height: 1.6;
        }

        .meta strong {
            color: #1f2937;
            font-weight: 600;
        }

        .footer-content {
            display: flex;
            justify-content: space-around;
            align-items: flex-end;
            width: 100%;
            padding-top: 20px;
            border-top: 1px solid rgba(59, 130, 246, 0.15);
            margin-top: 20px;
        }

        .footer-item {
            text-align: center;
            flex: 1;
        }

        .footer-label {
            font-size: 8px;
            color: #9ca3af;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .footer-value {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 13px;
            color: #1e3a8a;
            font-weight: 600;
        }

        .footer-code {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            color: #4b5563;
        }

        .decoration-bottom {
            width: 100%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.2), transparent);
            position: absolute;
            bottom: 0;
            left: 0;
        }

        .modules {
            font-size: 9px;
            color: #6b7280;
            margin-top: 15px;
            padding: 15px;
            background: rgba(59, 130, 246, 0.05);
            border-radius: 4px;
        }

        .modules-title {
            font-size: 9px;
            color: #9ca3af;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .module-item {
            font-size: 8px;
            color: #4b5563;
            margin: 3px 0;
            line-height: 1.4;
        }

        #qrcode {
            width: 80px !important;
            height: 80px !important;
        }

        #qrcode img {
            width: 100% !important;
            height: 100% !important;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="header">
            @if(!empty($companyLogo))
                <img src="{{ storage_path('app/' . $companyLogo) }}" alt="{{ $companyName }}" class="logo">
            @else
                <div class="company-name">{{ $companyName }}</div>
            @endif

            <div class="decoration-top"></div>
        </div>

        <div class="title-wrapper">
            <div class="subtitle">Apresenta</div>
            <div class="title">CERTIFICADO</div>
            <div class="title-subtext">de Conclusão</div>
        </div>

        <div class="content">
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

                <div class="meta">
                    @if($durLabel)
                        com carga horária de <strong>{{ $durLabel }}</strong>
                    @endif
                    @if($durLabel && $companyName)
                        •
                    @endif
                    @if($companyName)
                        na empresa <strong>{{ $companyName }}</strong>
                    @endif
                </div>
            </div>

            @if(!empty($modules) && $modules->count() > 0)
                <div class="modules">
                    <div class="modules-title">Conteúdo Programático</div>
                    @foreach($modules as $module)
                        <div class="module-item">
                            {{ $module->title }} ({{ $module->lessons->count() }} aula{{ $module->lessons->count() !== 1 ? 's' : '' }}{{ $module->quiz ? ' + avaliação' : '' }})
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="footer-content">
            <div class="footer-item">
                <div class="footer-label">Emitido em</div>
                <div class="footer-value">{{ $completionDate }}</div>
            </div>

            <div class="footer-item">
                <div class="footer-label">Código</div>
                <div class="footer-code">{{ $certificateCode }}</div>
            </div>

            <div class="footer-item">
                <div class="footer-label">Verificar</div>
                <div id="qrcode"></div>
            </div>
        </div>

        <div class="decoration-bottom"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const qrcodeContainer = document.getElementById('qrcode');
            const verifyUrl = '{{ url("/certificate/verify") }}?code={{ $certificateCode }}';
            new QRCode(qrcodeContainer, {
                text: verifyUrl,
                width: 80,
                height: 80,
                colorDark: '#1e3a8a',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });
        });
    </script>
</body>
</html>
