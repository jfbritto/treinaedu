<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            width: 1200px;
            height: 630px;
            font-family: 'Inter', -apple-system, sans-serif;
            background: linear-gradient(135deg, #4f46e5, #6366f1, #818cf8);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .circle-1 {
            position: absolute;
            top: -100px;
            right: -80px;
            width: 400px;
            height: 400px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
        }
        .circle-2 {
            position: absolute;
            bottom: -120px;
            left: -60px;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.06);
            border-radius: 50%;
        }
        .content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: white;
            padding: 60px;
        }
        .logo {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 40px;
        }
        .logo-icon {
            width: 48px;
            height: 48px;
            background: rgba(255,255,255,0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logo-text {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        h1 {
            font-size: 56px;
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 20px;
            letter-spacing: -1px;
        }
        p {
            font-size: 22px;
            opacity: 0.85;
            max-width: 700px;
            margin: 0 auto 40px;
            line-height: 1.5;
        }
        .tags {
            display: flex;
            gap: 16px;
            justify-content: center;
        }
        .tag {
            background: rgba(255,255,255,0.15);
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            backdrop-filter: blur(4px);
        }
    </style>
</head>
<body>
    <div class="circle-1"></div>
    <div class="circle-2"></div>
    <div class="content">
        <div class="logo">
            <div class="logo-icon">
                <svg width="28" height="28" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <span class="logo-text">TreinaEdu</span>
        </div>
        <h1>Capacite sua equipe.<br>Certifique com confianca.</h1>
        <p>Plataforma de treinamentos corporativos com videos, quizzes e certificados digitais.</p>
        <div class="tags">
            <span class="tag">Videos + Modulos</span>
            <span class="tag">Quizzes</span>
            <span class="tag">Certificados</span>
            <span class="tag">Relatorios</span>
        </div>
    </div>
</body>
</html>
