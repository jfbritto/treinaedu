<x-layout.app title="Usuários">

    @php
        $total     = $users->total();
        $active    = $users->getCollection()->where('active', true)->count();
        $instructors = $users->getCollection()->where('role', 'instructor')->count();
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
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $total }}</p>
                <p class="text-xs text-gray-400">Total de usuários</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $active }}</p>
                <p class="text-xs text-gray-400">Ativos (nesta página)</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $instructors }}</p>
                <p class="text-xs text-gray-400">Instrutores (nesta página)</p>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden overflow-x-auto">
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
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0
                                        {{ $user->role === 'instructor' ? 'bg-purple-100' : 'bg-blue-100' }}">
                                        <span class="text-xs font-bold {{ $user->role === 'instructor' ? 'text-purple-700' : 'text-blue-700' }}">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $user->role === 'instructor' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $user->role === 'instructor' ? 'Instrutor' : 'Colaborador' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($user->groups as $group)
                                        <span class="inline-block px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">{{ $group->name }}</span>
                                    @empty
                                        <span class="text-xs text-gray-300">—</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium {{ $user->active ? 'text-green-700' : 'text-red-500' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $user->active ? 'bg-green-500' : 'bg-red-400' }}"></span>
                                    {{ $user->active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('users.edit', $user) }}"
                                       class="text-xs font-medium text-primary hover:text-secondary transition">Editar</a>
                                    <form method="POST" action="{{ route('users.destroy', $user) }}" data-confirm="Remover este usuário?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-medium text-red-500 hover:text-red-700 transition">Remover</button>
                                    </form>
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

</x-layout.app>
