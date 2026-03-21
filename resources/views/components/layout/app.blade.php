<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary:   'var(--primary)',
                        secondary: 'var(--secondary)',
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('head')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        .bg-primary   { background-color: var(--primary); }
        .text-primary { color: var(--primary); }
        .border-primary { border-color: var(--primary); }
        .bg-secondary { background-color: var(--secondary); }
        .text-secondary { color: var(--secondary); }
        .hover\:bg-primary-dark:hover { background-color: var(--secondary); }
        /* Opacity variants via color-mix (CSS variables don't support Tailwind's /N syntax) */
        .bg-primary\/5  { background-color: color-mix(in srgb, var(--primary)  5%, transparent); }
        .bg-primary\/10 { background-color: color-mix(in srgb, var(--primary) 10%, transparent); }
        .bg-primary\/20 { background-color: color-mix(in srgb, var(--primary) 20%, transparent); }
        .bg-primary\/30 { background-color: color-mix(in srgb, var(--primary) 30%, transparent); }
        .bg-primary\/60 { background-color: color-mix(in srgb, var(--primary) 60%, transparent); }
        .text-primary\/60 { color: color-mix(in srgb, var(--primary) 60%, transparent); }
        .text-white\/60  { color: rgba(255,255,255,0.6); }
        .text-white\/70  { color: rgba(255,255,255,0.7); }
        .hover\:bg-primary\/5:hover  { background-color: color-mix(in srgb, var(--primary)  5%, transparent); }
        .hover\:bg-primary\/20:hover { background-color: color-mix(in srgb, var(--primary) 20%, transparent); }
        .group:hover .group-hover\:bg-primary\/20 { background-color: color-mix(in srgb, var(--primary) 20%, transparent); }
        .group:hover .group-hover\:text-primary { color: var(--primary); }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

        {{-- Backdrop (mobile only) --}}
        <div
            x-show="sidebarOpen"
            x-cloak
            x-transition:enter="transition-opacity ease-linear duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="sidebarOpen = false"
            class="fixed inset-0 bg-black/50 z-40 lg:hidden">
        </div>

        {{-- Sidebar --}}
        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            class="fixed inset-y-0 left-0 lg:static lg:inset-auto z-50 w-64 flex-shrink-0 text-white flex flex-col transition-transform duration-300 ease-in-out"
            style="background-color: var(--primary)">

            <div class="px-4 pt-5 pb-4" style="border-bottom: 1px solid rgba(255,255,255,0.12)">
                {{-- Close button (mobile only) --}}
                <button @click="sidebarOpen = false" class="lg:hidden absolute top-3 right-3 p-1.5 rounded-md text-white/60 hover:text-white hover:bg-white/10 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                @if($companyLogo ?? null)
                    {{-- Logo da empresa centralizada --}}
                    <div class="flex justify-center mb-3">
                        <div class="bg-white rounded-xl px-4 py-3 shadow-lg" style="max-width: 200px">
                            <img src="{{ Storage::disk('public')->url($companyLogo) }}" alt="Logo" class="h-12 w-auto object-contain mx-auto">
                        </div>
                    </div>
                    <div class="text-center">
                        @if($currentCompany ?? null)
                            <p class="text-sm font-semibold text-white leading-tight">{{ $currentCompany->name }}</p>
                        @endif
                        <p class="text-[10px] text-white/40 uppercase tracking-widest mt-1">Powered by TreinaEdu</p>
                    </div>
                @else
                    {{-- Sem logo: nome da plataforma --}}
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-white leading-tight">TreinaEdu</p>
                            @if($currentCompany ?? null)
                                <p class="text-xs text-white/60 truncate leading-tight mt-0.5">{{ $currentCompany->name }}</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                <a href="{{ route('dashboard') }}" @click="sidebarOpen = false" class="flex items-center gap-2 px-3 py-2.5 rounded-md text-sm hover:bg-white/10 transition {{ request()->routeIs('dashboard') ? 'bg-white/20' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>

                @if(auth()->user()->isAdmin())
                    <a href="{{ route('users.index') }}" @click="sidebarOpen = false" class="flex items-center gap-2 px-3 py-2.5 rounded-md text-sm hover:bg-white/10 transition {{ request()->routeIs('users.*') ? 'bg-white/20' : '' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Usuários
                    </a>
                    <a href="{{ route('groups.index') }}" @click="sidebarOpen = false" class="flex items-center gap-2 px-3 py-2.5 rounded-md text-sm hover:bg-white/10 transition {{ request()->routeIs('groups.*') ? 'bg-white/20' : '' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Grupos
                    </a>
                    <a href="{{ route('trainings.index') }}" @click="sidebarOpen = false" class="flex items-center gap-2 px-3 py-2.5 rounded-md text-sm hover:bg-white/10 transition {{ request()->routeIs('trainings.*') ? 'bg-white/20' : '' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Treinamentos
                    </a>
                    <a href="{{ route('reports.index') }}" @click="sidebarOpen = false" class="flex items-center gap-2 px-3 py-2.5 rounded-md text-sm hover:bg-white/10 transition {{ request()->routeIs('reports.*') ? 'bg-white/20' : '' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Relatórios
                    </a>
                    <a href="{{ route('engagement.index') }}" @click="sidebarOpen = false" class="flex items-center gap-2 px-3 py-2.5 rounded-md text-sm hover:bg-white/10 transition {{ request()->routeIs('engagement.*') ? 'bg-white/20' : '' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Desafios
                    </a>
                    <a href="{{ route('company.settings') }}" @click="sidebarOpen = false" class="flex items-center gap-2 px-3 py-2.5 rounded-md text-sm hover:bg-white/10 transition {{ request()->routeIs('company.settings') ? 'bg-white/20' : '' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Configurações
                    </a>
                @endif

                @if(auth()->user()->isInstructor())
                    <a href="{{ route('instructor.trainings.index') }}" @click="sidebarOpen = false" class="flex items-center gap-2 px-3 py-2.5 rounded-md text-sm hover:bg-white/10 transition {{ request()->routeIs('instructor.*') ? 'bg-white/20' : '' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Meus Treinamentos
                    </a>
                @endif

                @if(auth()->user()->isEmployee())
                    <a href="{{ route('employee.trainings.index') }}" @click="sidebarOpen = false" class="flex items-center gap-2 px-3 py-2.5 rounded-md text-sm hover:bg-white/10 transition {{ request()->routeIs('employee.trainings.*') ? 'bg-white/20' : '' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Meus Treinamentos
                    </a>
                    <a href="{{ route('employee.certificates.index') }}" @click="sidebarOpen = false" class="flex items-center gap-2 px-3 py-2.5 rounded-md text-sm hover:bg-white/10 transition {{ request()->routeIs('employee.certificates.*') ? 'bg-white/20' : '' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        Meus Certificados
                    </a>
                @endif
            </nav>

            {{-- User footer --}}
            @php
                $authUser   = auth()->user();
                $authInitials = collect(explode(' ', $authUser->name))->filter()->map(fn($w) => strtoupper($w[0]))->take(2)->implode('');
                $authRoleLabel = match($authUser->role) {
                    'admin'      => 'Administrador',
                    'instructor' => 'Instrutor',
                    'employee'   => 'Colaborador',
                    default      => ucfirst($authUser->role),
                };
            @endphp
            <div class="px-3 py-3 flex-shrink-0" style="border-top: 1px solid rgba(255,255,255,0.12)">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-2 py-2 rounded-lg hover:bg-white/10 transition group">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                        <span class="text-xs font-bold text-white">{{ $authInitials }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white leading-tight truncate">{{ $authUser->name }}</p>
                        <p class="text-[10px] text-white/50 leading-tight">{{ $authRoleLabel }}</p>
                    </div>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="mt-1">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2 px-2 py-2 rounded-lg text-sm text-white/50 hover:text-white hover:bg-white/10 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Sair
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main --}}
        <div class="flex-1 flex flex-col overflow-hidden min-w-0">

            {{-- Topbar --}}
            <header class="bg-white border-b border-gray-100 px-4 py-2.5 flex items-center gap-3 flex-shrink-0">
                {{-- Hamburger (mobile only) --}}
                <button @click="sidebarOpen = true" class="lg:hidden flex-shrink-0 p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h1 class="text-base font-semibold text-gray-800 truncate">{{ $title ?? config('app.name') }}</h1>
            </header>

            {{-- Subscription warning --}}
            @if(isset($currentCompany) && $currentCompany->subscription?->status === 'past_due')
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 px-4 py-3 text-sm flex-shrink-0">
                    Sua assinatura está em atraso. <a href="{{ route('subscription.plans') }}" class="underline font-medium">Regularize agora</a> para evitar a suspensão.
                </div>
            @endif

            {{-- Content --}}
            <main class="flex-1 overflow-y-auto p-4 sm:p-6">
                {{ $slot }}
            </main>

        </div>
    </div>
    <x-ui.alert />
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
