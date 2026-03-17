<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificado de Conclusão</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #ffffff;
            color: #333333;
            width: 297mm;
            height: 210mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .certificate {
            width: 100%;
            height: 100%;
            padding: 20mm 25mm;
            border: 8px solid #1a5276;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
        }

        .inner-border {
            position: absolute;
            top: 12px;
            left: 12px;
            right: 12px;
            bottom: 12px;
            border: 2px solid #1a5276;
            pointer-events: none;
        }

        .logo {
            max-height: 70px;
            max-width: 200px;
            margin-bottom: 20px;
        }

        .title {
            font-size: 28px;
            font-weight: bold;
            color: #1a5276;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-bottom: 30px;
        }

        .body-text {
            font-size: 14px;
            line-height: 1.8;
            max-width: 520px;
            color: #444444;
            margin-bottom: 20px;
        }

        .body-text .highlight {
            font-size: 18px;
            font-weight: bold;
            color: #1a5276;
        }

        .date {
            font-size: 13px;
            color: #666666;
            margin-bottom: 30px;
        }

        .footer {
            position: absolute;
            bottom: 20px;
            left: 30px;
            right: 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 10px;
            color: #999999;
        }

        .certificate-code {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10px;
            color: #aaaaaa;
        }

        .verify-url {
            font-size: 10px;
            color: #aaaaaa;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="inner-border"></div>

        @if(!empty($companyLogo))
            <img src="{{ storage_path('app/' . $companyLogo) }}" alt="Logo" class="logo">
        @endif

        <div class="title">Certificado de Conclusão</div>

        <div class="body-text">
            Certificamos que<br>
            <span class="highlight">{{ $userName }}</span><br>
            concluiu com sucesso o treinamento<br>
            <span class="highlight">{{ $trainingTitle }}</span><br>
            com carga horária de {{ (int) round($durationMinutes / 60) }}h,
            na empresa <strong>{{ $companyName }}</strong>.
        </div>

        <div class="date">Data de conclusão: {{ $completionDate }}</div>

        <div class="footer">
            <span class="certificate-code">Código: {{ $certificateCode }}</span>
            <span class="verify-url">Verifique este certificado em: {{ url('/certificate/verify') }}</span>
        </div>
    </div>
</body>
</html>
