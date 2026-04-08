<x-layout.app title="Usuários">

    @php
        $total = $totalUsers;
        $active = $totalActive;
        $instructors = $totalInstructors;
    @endphp

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <p class="text-sm text-gray-500">Gerencie colaboradores e instrutores da empresa</p>
        <a href="{{ route('users.create') }}"
           class="inline-flex items-center gap-2 bg-primary hover:bg-secondary text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            Novo Usuário
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24"><path d="M8.25 6.75a3.75 3.75 0 117.5 0 3.75 3.75 0 01-7.5 0zM15.75 9.75a3 3 0 116 0 3 3 0 01-6 0zM2.25 9.75a3 3 0 116 0 3 3 0 01-6 0zM6.31 15.117A6.745 6.745 0 0112 12a6.745 6.745 0 016.709 7.498.75.75 0 01-.372.568A12.696 12.696 0 0112 21.75c-2.305 0-4.47-.612-6.337-1.684a.75.75 0 01-.372-.568 6.787 6.787 0 011.019-4.38z"/><path d="M5.082 14.254a8.287 8.287 0 00-1.308 5.135 9.687 9.687 0 01-1.764-.44l-.115-.04a.563.563 0 01-.373-.487l-.01-.121a3.75 3.75 0 013.57-4.047zM20.226 19.389a8.287 8.287 0 00-1.308-5.135 3.75 3.75 0 013.57 4.047l-.01.121a.563.563 0 01-.373.486l-.115.04c-.567.2-1.156.349-1.764.441z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $total }}</p>
                <p class="text-xs text-gray-400">Total de usuários</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $active }}</p>
                <p class="text-xs text-gray-400">Ativos</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm14.024-.983a1.125 1.125 0 010 1.966l-5.603 3.113A1.125 1.125 0 019 15.113V8.887c0-.857.921-1.4 1.671-.983l5.603 3.113z" clip-rule="evenodd"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $instructors }}</p>
                <p class="text-xs text-gray-400">Instrutores</p>
            </div>
        </div>
    </div>

    {{-- Table with server-side filters --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        {{-- Filters --}}
        <form method="GET" action="{{ route('users.index') }}" id="filter-form"
              x-data="{ timer: null }" class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[200px]">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nome ou e-mail..."
                       @input="clearTimeout(timer); timer = setTimeout(() => $el.closest('form').submit(), 400)"
                       class="w-full pl-10 pr-4 py-2 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <select name="role" onchange="this.form.submit()"
                    class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="">Todos os perfis</option>
                <option value="employee" {{ request('role') === 'employee' ? 'selected' : '' }}>Colaborador</option>
                <option value="instructor" {{ request('role') === 'instructor' ? 'selected' : '' }}>Instrutor</option>
            </select>
            @if(request()->hasAny(['search', 'role']))
                <a href="{{ route('users.index') }}" class="text-xs text-gray-500 hover:text-gray-700 transition">Limpar</a>
            @endif
        </form>

        {{-- Table --}}
        <div class="overflow-x-auto">
        @if($users->isEmpty())
            <div class="p-12 text-center">
                <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <p class="text-gray-400 text-sm font-medium">Nenhum usuário cadastrado.</p>
                <a href="{{ route('users.create') }}" class="inline-block mt-3 text-sm text-primary hover:underline">Criar primeiro usuário →</a>
            </div>
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Usuário</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Perfil</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider hidden md:table-cell">Grupos</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider hidden lg:table-cell">Último acesso</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($users as $user)
                        @php
                            $isPending = $user->isPendingInvite();
                            $statusLabel = !$user->active ? 'Inativo' : ($isPending ? 'Pendente' : 'Ativo');
                            $statusColors = !$user->active
                                ? ['bg' => 'bg-red-400', 'text' => 'text-red-500']
                                : ($isPending
                                    ? ['bg' => 'bg-yellow-400', 'text' => 'text-yellow-700']
                                    : ['bg' => 'bg-green-500', 'text' => 'text-green-700']);
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 cursor-pointer" onclick="window.location.href='{{ route('users.show', $user) }}'">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0
                                        {{ $user->role === 'instructor' ? 'bg-purple-100' : 'bg-primary/10' }}">
                                        <span class="text-xs font-bold {{ $user->role === 'instructor' ? 'text-purple-700' : 'text-primary' }}">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 cursor-pointer" onclick="window.location.href='{{ route('users.show', $user) }}'">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $user->role === 'instructor' ? 'bg-purple-100 text-purple-700' : 'bg-primary/15 text-primary' }}">
                                    {{ $user->role === 'instructor' ? 'Instrutor' : 'Colaborador' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell cursor-pointer" onclick="window.location.href='{{ route('users.show', $user) }}'">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($user->groups as $group)
                                        <span class="inline-block px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">{{ $group->name }}</span>
                                    @empty
                                        <span class="text-xs text-gray-300">—</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 cursor-pointer" onclick="window.location.href='{{ route('users.show', $user) }}'">
                                <div class="flex flex-col gap-0.5">
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium {{ $statusColors['text'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $statusColors['bg'] }}"></span>
                                        {{ $statusLabel }}
                                    </span>
                                    @if($isPending && $user->invited_at)
                                        <span class="text-[10px] text-gray-400">Convidado há {{ $user->invited_at->diffForHumans(null, true) }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 hidden lg:table-cell cursor-pointer" onclick="window.location.href='{{ route('users.show', $user) }}'">
                                @if($user->last_login_at)
                                    <div class="flex flex-col gap-0.5">
                                        <span class="text-xs text-gray-700">{{ $user->last_login_at->diffForHumans() }}</span>
                                        <span class="text-[10px] text-gray-400">{{ $user->last_login_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-300">Nunca acessou</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    @if($isPending)
                                        <form method="POST" action="{{ route('users.resend-invite', $user) }}" onclick="event.stopPropagation();">
                                            @csrf
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 text-xs font-medium text-primary hover:bg-primary/10 px-2.5 py-1.5 rounded-lg transition"
                                                title="Reenviar convite por email">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                </svg>
                                                Reenviar
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('users.show', $user) }}" class="text-gray-400 hover:text-gray-600 transition" title="Visualizar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $users->links() }}
                </div>
            @endif
        @endif
        </div>
    </div>

</x-layout.app>
