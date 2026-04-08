<x-layout.app title="Grupos">

    @php
        $totalMembers = $groups->getCollection()->sum('users_count');
        $avgMembers = $groups->total() > 0 ? round($totalMembers / $groups->total()) : 0;
    @endphp

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <p class="text-sm text-gray-500">Organize colaboradores em grupos para atribuir treinamentos</p>
        <a href="{{ route('groups.create') }}"
           class="inline-flex items-center gap-2 bg-primary hover:bg-secondary text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Novo Grupo
        </a>
    </div>

    @if($groups->isEmpty())
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <p class="text-gray-500 text-sm font-medium mb-1">Nenhum grupo cadastrado</p>
            <p class="text-xs text-gray-400 mb-4">Agrupe colaboradores para atribuir treinamentos em massa.</p>
            <a href="{{ route('groups.create') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:text-secondary transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Criar primeiro grupo
            </a>
        </div>
    @else
        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Grupos</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $groups->total() }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $groups->total() === 1 ? 'grupo cadastrado' : 'grupos cadastrados' }}</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Membros</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalMembers }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $totalMembers === 1 ? 'vínculo total' : 'vínculos totais' }}</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Média</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $avgMembers }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $avgMembers === 1 ? 'membro por grupo' : 'membros por grupo' }}</p>
            </div>
        </div>

        {{-- Grid de cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 mb-6">
            @foreach($groups as $group)
                <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow flex flex-col border border-gray-100">
                    <div class="h-1.5" style="background: linear-gradient(to right, var(--secondary), var(--primary))"></div>
                    <div class="p-5 flex-1 flex flex-col gap-4">
                        <div class="flex items-start gap-3">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                                 style="background: linear-gradient(135deg, var(--primary), var(--secondary))">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-800 leading-snug truncate">{{ $group->name }}</h3>
                                @if($group->description)
                                    <p class="text-xs text-gray-400 mt-0.5 line-clamp-2">{{ $group->description }}</p>
                                @else
                                    <p class="text-xs text-gray-300 mt-0.5 italic">Sem descrição</p>
                                @endif
                            </div>
                        </div>

                        {{-- Membros --}}
                        <div class="flex items-center gap-2">
                            @if($group->users_count > 0)
                                <div class="flex -space-x-2">
                                    @foreach($group->users->take(5) as $member)
                                        <div class="w-7 h-7 rounded-full border-2 border-white flex items-center justify-center text-[10px] font-bold text-white"
                                             style="background: linear-gradient(135deg, var(--primary), var(--secondary))"
                                             title="{{ $member->name }}">
                                            {{ strtoupper(substr($member->name, 0, 1)) }}
                                        </div>
                                    @endforeach
                                    @if($group->users_count > 5)
                                        <div class="w-7 h-7 rounded-full bg-gray-100 border-2 border-white flex items-center justify-center">
                                            <span class="text-[10px] font-bold text-gray-500">+{{ $group->users_count - 5 }}</span>
                                        </div>
                                    @endif
                                </div>
                                <span class="text-xs text-gray-500 font-medium">
                                    {{ $group->users_count }} {{ $group->users_count !== 1 ? 'membros' : 'membro' }}
                                </span>
                            @else
                                <div class="flex items-center gap-1.5 text-xs text-gray-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Grupo vazio
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 mt-auto pt-3 border-t border-gray-100">
                            <a href="{{ route('groups.edit', $group) }}"
                               class="flex-1 flex items-center justify-center gap-1.5 text-xs font-medium text-primary bg-primary/10 hover:bg-primary/20 transition rounded-lg px-3 py-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Editar
                            </a>
                            <form method="POST" action="{{ route('groups.destroy', $group) }}" data-confirm="Remover este grupo?">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="flex items-center justify-center gap-1.5 text-xs font-medium text-red-500 bg-red-50 hover:bg-red-100 transition rounded-lg px-3 py-2"
                                    title="Remover grupo">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($groups->hasPages())
            {{ $groups->links() }}
        @endif
    @endif

</x-layout.app>
