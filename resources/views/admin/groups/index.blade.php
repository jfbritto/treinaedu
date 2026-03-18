<x-layout.app title="Grupos">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <p class="text-sm text-gray-500">Organize colaboradores em grupos para atribuir treinamentos</p>
        <a href="{{ route('groups.create') }}"
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Novo Grupo
        </a>
    </div>

    @if($groups->isEmpty())
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
            </svg>
            <p class="text-gray-400 text-sm font-medium">Nenhum grupo cadastrado.</p>
            <a href="{{ route('groups.create') }}" class="inline-block mt-3 text-sm text-blue-600 hover:underline">Criar primeiro grupo →</a>
        </div>
    @else
        {{-- Summary bar --}}
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6 flex items-center gap-5">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $groups->total() }}</p>
                <p class="text-sm text-gray-400">Grupo{{ $groups->total() !== 1 ? 's' : '' }} cadastrado{{ $groups->total() !== 1 ? 's' : '' }}</p>
            </div>
            <p class="ml-auto text-xs text-gray-400 hidden sm:block">Grupos permitem atribuir treinamentos a múltiplos colaboradores de uma vez.</p>
        </div>

        {{-- Grid de cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 mb-6">
            @foreach($groups as $group)
                <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow flex flex-col">
                    <div class="h-1.5 bg-gradient-to-r from-blue-500 to-blue-400"></div>
                    <div class="p-5 flex-1 flex flex-col gap-4">
                        <div class="flex items-start justify-between gap-2">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-800 leading-snug">{{ $group->name }}</h3>
                                @if($group->description)
                                    <p class="text-xs text-gray-400 mt-0.5 line-clamp-2">{{ $group->description }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            {{-- Member avatars --}}
                            <div class="flex -space-x-2">
                                @foreach($group->users->take(5) as $member)
                                    <div class="w-7 h-7 rounded-full bg-blue-200 border-2 border-white flex items-center justify-center"
                                         title="{{ $member->name }}">
                                        <span class="text-xs font-bold text-blue-800">{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                                    </div>
                                @endforeach
                                @if($group->users_count > 5)
                                    <div class="w-7 h-7 rounded-full bg-gray-100 border-2 border-white flex items-center justify-center">
                                        <span class="text-xs font-bold text-gray-500">+{{ $group->users_count - 5 }}</span>
                                    </div>
                                @endif
                            </div>
                            <span class="text-xs text-gray-500">
                                {{ $group->users_count }} membro{{ $group->users_count !== 1 ? 's' : '' }}
                            </span>
                        </div>

                        <div class="flex items-center gap-2 mt-auto pt-3 border-t border-gray-100">
                            <a href="{{ route('groups.edit', $group) }}"
                               class="flex-1 flex items-center justify-center gap-1.5 text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 transition rounded-lg px-3 py-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Editar
                            </a>
                            <form method="POST" action="{{ route('groups.destroy', $group) }}" data-confirm="Remover este grupo?">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="flex items-center justify-center gap-1.5 text-xs font-medium text-red-500 bg-red-50 hover:bg-red-100 transition rounded-lg px-3 py-2">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Remover
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
