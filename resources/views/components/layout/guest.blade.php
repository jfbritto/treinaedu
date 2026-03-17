<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-blue-600">TreinaEdu</h1>
            <p class="text-gray-500 text-sm mt-1">Plataforma de Treinamentos Corporativos</p>
        </div>
        <div class="bg-white rounded-xl shadow-md p-8">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
