<x-layout.app title="Treinamentos">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <p class="text-sm text-gray-500">Gerencie os treinamentos disponíveis para a equipe</p>
        <a href="{{ route('trainings.create') }}"
           class="inline-flex items-center gap-2 bg-primary hover:bg-secondary text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Novo Treinamento
        </a>
    </div>

    @if($trainings->isEmpty())
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            <p class="text-gray-400 text-sm font-medium">Nenhum treinamento cadastrado.</p>
            <a href="{{ route('trainings.create') }}" class="inline-block mt-3 text-sm text-primary hover:underline">Criar primeiro treinamento →</a>
        </div>
    @else
        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalActive }}</p>
                    <p class="text-xs text-gray-400">Ativos</p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V7.875L14.25 1.5H5.625zM7.5 15a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5A.75.75 0 017.5 15zm.75 2.25a.75.75 0 000 1.5H12a.75.75 0 000-1.5H8.25z" clip-rule="evenodd"/><path d="M12.971 1.816A5.23 5.23 0 0114.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 013.434 1.279 9.768 9.768 0 00-6.963-6.963z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalWithQuiz }}</p>
                    <p class="text-xs text-gray-400">Com quiz</p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24"><path d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75zM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 01-1.875-1.875V8.625zM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 013 19.875v-6.75z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $avgRate }}%</p>
                    <p class="text-xs text-gray-400">Conclusão média</p>
                </div>
            </div>
        </div>

        {{-- Table with server-side filter --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            {{-- Search --}}
            <form method="GET" action="{{ route('trainings.index') }}" x-data="{ timer: null }"
                  class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="relative flex-1 max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar treinamento..."
                           @input="clearTimeout(timer); timer = setTimeout(() => $el.closest('form').submit(), 400)"
                           class="w-full pl-10 pr-4 py-2 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                @if(request('search'))
                    <a href="{{ route('trainings.index') }}" class="text-xs text-gray-500 hover:text-gray-700 transition">Limpar</a>
                @endif
            </form>

            <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Treinamento</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider hidden sm:table-cell">Duração</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider hidden md:table-cell">Quiz</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider hidden md:table-cell">Grupos</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider hidden lg:table-cell">Conclusão</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($trainings as $training)
                        @php $rate = $training->completionRate(); @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd" d="M4.5 5.653c0-1.426 1.529-2.33 2.779-1.643l11.54 6.348c1.295.712 1.295 2.573 0 3.285L7.28 19.991c-1.25.687-2.779-.217-2.779-1.643V5.653z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <a href="{{ route('trainings.show', $training) }}"
                                           class="text-sm font-semibold text-gray-800 hover:text-primary transition">
                                            {{ $training->title }}
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 hidden sm:table-cell">
                                <span class="inline-flex items-center gap-1 text-xs text-gray-500">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $training->duration_minutes }} min
                                </span>
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell">
                                @if($training->assignments_count > 0)
                                    <a href="{{ route('trainings.show', $training) }}"
                                       class="inline-flex items-center gap-1 text-xs text-primary hover:underline">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                        {{ $training->assignments_count }} grupo{{ $training->assignments_count !== 1 ? 's' : '' }}
                                    </a>
                                @else
                                    <a href="{{ route('trainings.show', $training) }}"
                                       class="text-xs text-gray-400 hover:text-primary transition">Atribuir →</a>
                                @endif
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell">
                                @if($training->has_quiz)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Com Quiz
                                    </span>
                                @else
                                    <span class="text-xs text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium {{ $training->active ? 'text-green-700' : 'text-red-500' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $training->active ? 'bg-green-500' : 'bg-red-400' }}"></span>
                                    {{ $training->active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 hidden lg:table-cell">
                                <div class="flex items-center gap-2 w-32">
                                    <div class="flex-1 bg-gray-100 rounded-full h-1.5">
                                        <div class="h-1.5 rounded-full {{ $rate >= 70 ? 'bg-green-500' : ($rate >= 30 ? 'bg-yellow-400' : 'bg-primary') }}"
                                             style="width: {{ $rate }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium text-gray-600 flex-shrink-0 w-8 text-right">{{ $rate }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('trainings.edit', $training) }}"
                                       class="text-xs font-medium text-primary hover:text-secondary transition">Editar</a>
                                    <form method="POST" action="{{ route('trainings.destroy', $training) }}" data-confirm="Remover este treinamento?">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs font-medium text-red-500 hover:text-red-700 transition">Remover</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($trainings->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $trainings->links() }}
                </div>
            @endif
            </div>
        </div>
    @endif

</x-layout.app>
