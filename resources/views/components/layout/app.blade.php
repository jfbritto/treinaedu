<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @php
        $safePrimary = preg_match('/^#[0-9A-Fa-f]{3,6}$/', $primaryColor ?? '') ? $primaryColor : '#3B82F6';
        $safeSecondary = preg_match('/^#[0-9A-Fa-f]{3,6}$/', $secondaryColor ?? '') ? $secondaryColor : '#1E40AF';
    @endphp
    <style>
        :root {
            --primary: {{ $safePrimary }};
            --secondary: {{ $safeSecondary }};
        }
        .bg-primary { background-color: var(--primary); }
        .text-primary { color: var(--primary); }
        .border-primary { border-color: var(--primary); }
        .hover\:bg-primary-dark:hover { background-color: var(--secondary); }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen overflow-hidden">

        {{-- Sidebar --}}
        <aside class="w-64 bg-gray-900 text-white flex flex-col">
            <div class="p-4 border-b border-gray-700">
                @if($companyLogo ?? null)
                    <img src="{{ Storage::url($companyLogo) }}" alt="Logo" class="h-10 object-contain">
                @else
                    <span class="text-xl font-bold text-primary">TreinaEdu</span>
                @endif
                @if($currentCompany ?? null)
                    <p class="text-xs text-gray-400 mt-1 truncate">{{ $currentCompany->name }}</p>
                @endif
            </div>

            <nav class="flex-1 p-4 space-y-1">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-md text-sm hover:bg-gray-700 {{ request()->routeIs('dashboard') ? 'bg-gray-700' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                    Dashboard
                </a>

                @if(auth()->user()->isAdmin())
                    <a href="{{ route('users.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-md text-sm hover:bg-gray-700 {{ request()->routeIs('users.*') ? 'bg-gray-700' : '' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                        Usuários
                    </a>
                    <a href="{{ route('groups.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-md text-sm hover:bg-gray-700 {{ request()->routeIs('groups.*') ? 'bg-gray-700' : '' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7"/></svg>
                        Grupos
                    </a>
                    <a href="{{ route('trainings.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-md text-sm hover:bg-gray-700 {{ request()->routeIs('trainings.*') ? 'bg-gray-700' : '' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Treinamentos
                    </a>
                    <a href="{{ route('reports.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-md text-sm hover:bg-gray-700 {{ request()->routeIs('reports.*') ? 'bg-gray-700' : '' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Relatórios
                    </a>
                    <a href="{{ route('company.settings') }}" class="flex items-center gap-2 px-3 py-2 rounded-md text-sm hover:bg-gray-700 {{ request()->routeIs('company.settings') ? 'bg-gray-700' : '' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Configurações
                    </a>
                @endif

                @if(auth()->user()->isInstructor())
                    <a href="{{ route('instructor.trainings.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-md text-sm hover:bg-gray-700 {{ request()->routeIs('instructor.*') ? 'bg-gray-700' : '' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26"/></svg>
                        Meus Treinamentos
                    </a>
                @endif

                @if(auth()->user()->isEmployee())
                    <a href="{{ route('employee.certificates.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-md text-sm hover:bg-gray-700 {{ request()->routeIs('employee.certificates.*') ? 'bg-gray-700' : '' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        Meus Certificados
                    </a>
                @endif
            </nav>
        </aside>

        {{-- Main --}}
        <div class="flex-1 flex flex-col overflow-hidden">

            {{-- Topbar --}}
            <header class="bg-white shadow-sm px-6 py-3 flex items-center justify-between">
                <h1 class="text-lg font-semibold text-gray-800">{{ $title ?? config('app.name') }}</h1>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-gray-500 hover:text-red-600">Sair</button>
                    </form>
                </div>
            </header>

            {{-- Subscription warning --}}
            @if(isset($currentCompany) && $currentCompany->subscription?->status === 'past_due')
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 px-6 py-3 text-sm">
                    Sua assinatura está em atraso. <a href="{{ route('subscription.plans') }}" class="underline font-medium">Regularize agora</a> para evitar a suspensão.
                </div>
            @endif

            {{-- Content --}}
            <main class="flex-1 overflow-y-auto p-6">
                <x-ui.alert />
                {{ $slot }}
            </main>

        </div>
    </div>
    @stack('scripts')
    <script>
        document.querySelectorAll('form[data-confirm]').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const message = this.dataset.confirm;
                Swal.fire({
                    title: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, remover',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#EF4444',
                    cancelButtonColor: '#6B7280',
                    reverseButtons: true,
                }).then(result => {
                    if (result.isConfirmed) this.submit();
                });
            });
        });
    </script>
</body>
</html>
