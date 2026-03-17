<x-layout.guest>
    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        {{-- Nome da Empresa --}}
        <div class="space-y-1">
            <label for="company_name" class="block text-sm font-medium text-gray-700">Nome da Empresa *</label>
            <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}" required autofocus
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('company_name')
                <p class="text-red-500 text-xs">{{ $message }}</p>
            @enderror
        </div>

        {{-- Nome --}}
        <div class="space-y-1">
            <label for="name" class="block text-sm font-medium text-gray-700">Seu Nome *</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('name')
                <p class="text-red-500 text-xs">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div class="space-y-1">
            <label for="email" class="block text-sm font-medium text-gray-700">E-mail *</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('email')
                <p class="text-red-500 text-xs">{{ $message }}</p>
            @enderror
        </div>

        {{-- Senha --}}
        <div class="space-y-1">
            <label for="password" class="block text-sm font-medium text-gray-700">Senha *</label>
            <input type="password" id="password" name="password" required
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('password')
                <p class="text-red-500 text-xs">{{ $message }}</p>
            @enderror
        </div>

        {{-- Confirmar Senha --}}
        <div class="space-y-1">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Senha *</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg text-sm">
            Criar Conta Gratuita
        </button>

        <p class="text-center text-sm text-gray-500">
            Já tem uma conta? <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Entrar</a>
        </p>
    </form>
</x-layout.guest>
