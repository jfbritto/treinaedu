<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Certificado de Conclusão</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        @page { margin: 0; size: A4 landscape; }

        body {
            font-family: Georgia, 'Times New Roman', serif;
            background: #ffffff;
            width: 297mm;
            height: 210mm;
        }

        .certificate {
            width: 297mm;
            height: 210mm;
            background: linear-gradient(135deg, #f8faff 0%, #eef2ff 100%);
            border: 10px solid #1a3a6e;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 18mm 28mm;
        }

        .inner-border {
            position: absolute;
            top: 14px;
            left: 14px;
            right: 14px;
            bottom: 14px;
            border: 2px solid #1a3a6e;
            opacity: 0.35;
        }

        .logo {
            max-height: 60px;
            max-width: 180px;
            margin-bottom: 14px;
        }

        .company-name {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            font-weight: bold;
            color: #1a3a6e;
            letter-spacing: 1px;
            margin-bottom: 14px;
        }

        .title {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 26px;
            font-weight: bold;
            color: #1a3a6e;
            letter-spacing: 5px;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .certifies {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #888888;
            margin-bottom: 8px;
        }

        .recipient {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 32px;
            font-weight: bold;
            color: #1a3a6e;
            margin-bottom: 10px;
        }

        .completed {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #666666;
            margin-bottom: 6px;
        }

        .training-title {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 18px;
            font-weight: bold;
            color: #222222;
            margin-bottom: 10px;
        }

        .meta {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #777777;
            margin-bottom: 14px;
        }

        .meta strong {
            color: #333333;
        }

        .date-line {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #777777;
            margin-bottom: 0;
        }

        .date-line strong {
            color: #333333;
        }

        .divider {
            width: 80px;
            height: 2px;
            background-color: #1a3a6e;
            margin: 14px auto 14px;
            opacity: 0.3;
        }

        .footer {
            position: absolute;
            bottom: 22px;
            left: 30px;
            right: 30px;
            border-top: 1px solid #d0d8f0;
            padding-top: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer .code {
            font-family: 'Courier New', Courier, monospace;
            font-size: 9px;
            color: #aaaaaa;
        }

        .footer .verify {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9px;
            color: #aaaaaa;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="inner-border"></div>

        @if(!empty($companyLogo))
            <img src="{{ storage_path('app/' . $companyLogo) }}" alt="{{ $companyName }}" class="logo">
        @else
            <div class="company-name">{{ $companyName }}</div>
        @endif

        <div class="title">Certificado de Conclusão</div>

        <div class="certifies">Certificamos que</div>

        <div class="recipient">{{ $userName }}</div>

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
                com carga horária de <strong>{{ $durLabel }}</strong>,
            @endif
            na empresa <strong>{{ $companyName }}</strong>
        </div>

        <div class="divider"></div>

        <div class="date-line">
            Data de conclusão: <strong>{{ $completionDate }}</strong>
        </div>

        <div class="footer">
            <span class="code">Código: {{ $certificateCode }}</span>
            <span class="verify">{{ url('/certificate/verify') }}</span>
        </div>
    </div>
</body>
</html>
