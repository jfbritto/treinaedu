<x-layout.app :title="$training->title">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('trainings.index') }}"
           class="inline-flex items-center gap-1 text-sm text-gray-400 hover:text-gray-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Coluna principal --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Detalhes do treinamento --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">{{ $training->title }}</h2>
                        @if($training->description)
                            <p class="text-sm text-gray-500 mt-1">{{ $training->description }}</p>
                        @endif
                    </div>
                    <a href="{{ route('trainings.edit', $training) }}"
                       class="flex-shrink-0 text-xs font-medium text-blue-600 hover:text-blue-800 transition">Editar →</a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-lg font-bold text-gray-800">{{ $training->duration_minutes }}</p>
                        <p class="text-xs text-gray-400">minutos</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-lg font-bold {{ $training->active ? 'text-green-600' : 'text-red-500' }}">
                            {{ $training->active ? 'Ativo' : 'Inativo' }}
                        </p>
                        <p class="text-xs text-gray-400">status</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-lg font-bold text-gray-800">{{ $training->has_quiz ? 'Sim' : 'Não' }}</p>
                        <p class="text-xs text-gray-400">quiz</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-lg font-bold text-gray-800">{{ $training->completionRate() }}%</p>
                        <p class="text-xs text-gray-400">conclusão</p>
                    </div>
                </div>
            </div>

            {{-- Grupos atribuídos --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                        <h3 class="text-sm font-semibold text-gray-700">Grupos Atribuídos</h3>
                        <span class="text-xs text-gray-400 bg-gray-100 rounded-full px-2 py-0.5">{{ $training->assignments->count() }}</span>
                    </div>
                </div>

                @if($training->assignments->isEmpty())
                    <div class="p-8 text-center">
                        <svg class="w-10 h-10 text-gray-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                        </svg>
                        <p class="text-sm text-gray-400">Nenhum grupo atribuído ainda.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-50">
                        @foreach($training->assignments as $assignment)
                            @php
                                $overdue  = $assignment->due_date && $assignment->due_date->isPast();
                                $soonDays = $assignment->due_date ? (int) now()->diffInDays($assignment->due_date, false) : null;
                                $dueSoon  = $soonDays !== null && $soonDays >= 0 && $soonDays <= 7;
                            @endphp
                            <div class="flex items-center gap-4 px-6 py-3.5">
                                {{-- Ícone grupo --}}
                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                                    </svg>
                                </div>
                                {{-- Nome do grupo --}}
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800">{{ $assignment->group->name }}</p>
                                    <div class="flex items-center gap-2 flex-wrap mt-0.5">
                                        @if($assignment->mandatory)
                                            <span class="inline-flex items-center gap-1 text-xs font-medium bg-red-100 text-red-700 rounded-full px-2 py-0.5">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                Obrigatório
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400">Opcional</span>
                                        @endif
                                        @if($assignment->due_date)
                                            <span class="text-xs {{ $overdue ? 'text-red-500 font-medium' : ($dueSoon ? 'text-yellow-600' : 'text-gray-400') }}">
                                                Prazo: {{ $assignment->due_date->format('d/m/Y') }}
                                                @if($overdue) (vencido)
                                                @elseif($dueSoon) ({{ $soonDays }}d)
                                                @endif
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                {{-- Remover --}}
                                <form method="POST"
                                      action="{{ route('trainings.assignments.destroy', [$training, $assignment]) }}"
                                      data-confirm="Remover atribuição do grupo {{ $assignment->group->name }}?">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs font-medium text-red-400 hover:text-red-600 transition">Remover</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Adicionar grupos --}}
            @if($availableGroups->isNotEmpty())
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Atribuir a mais grupos</h3>
                    <form method="POST" action="{{ route('trainings.assignments.store', $training) }}" class="space-y-4">
                        @csrf

                        {{-- Grupos disponíveis --}}
                        <div class="space-y-1">
                            <label class="block text-xs font-medium text-gray-600">Selecione os grupos</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 border border-gray-200 rounded-xl p-3 bg-gray-50">
                                @foreach($availableGroups as $group)
                                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer hover:text-gray-900 transition">
                                        <input type="checkbox" name="group_ids[]" value="{{ $group->id }}"
                                               class="rounded border-gray-300 text-blue-600">
                                        {{ $group->name }}
                                    </label>
                                @endforeach
                            </div>
                            @error('group_ids')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Prazo --}}
                            <div class="space-y-1">
                                <label class="block text-xs font-medium text-gray-600">Data limite (opcional)</label>
                                <input type="date" name="due_date" value="{{ old('due_date') }}"
                                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('due_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            {{-- Obrigatório --}}
                            <div class="flex items-end pb-0.5">
                                <label class="flex items-center gap-2.5 cursor-pointer p-3 rounded-xl border border-gray-200 hover:border-red-200 hover:bg-red-50 transition group w-full">
                                    <input type="checkbox" name="mandatory" value="1"
                                           {{ old('mandatory') ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-red-500">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700 group-hover:text-red-700 transition">Obrigatório</p>
                                        <p class="text-xs text-gray-400">Exige conclusão do colaborador</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <button type="submit"
                                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Atribuir grupos
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="bg-gray-50 border border-dashed border-gray-200 rounded-xl p-4 text-center text-xs text-gray-400">
                    Todos os grupos já estão atribuídos a este treinamento.
                </div>
            @endif

        </div>

        {{-- Sidebar info --}}
        <div class="space-y-4">
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-6 h-6 rounded-md bg-blue-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-blue-900">Como funciona</p>
                </div>
                <div class="space-y-2.5 text-xs text-blue-700">
                    <p>Atribuindo a um grupo, todos os membros desse grupo passam a ver este treinamento automaticamente.</p>
                    <p>Marcar como <strong>obrigatório</strong> destaca o treinamento para o colaborador e sinaliza que a conclusão é exigida.</p>
                    <p>A <strong>data limite</strong> aparece com indicador de urgência quando o prazo se aproxima.</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Ações</p>
                <div class="space-y-2">
                    <a href="{{ route('trainings.edit', $training) }}"
                       class="flex items-center gap-2 text-sm text-blue-600 hover:text-blue-800 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Editar conteúdo
                    </a>
                    <form method="POST" action="{{ route('trainings.destroy', $training) }}" data-confirm="Remover o treinamento {{ $training->title }}?">
                        @csrf @method('DELETE')
                        <button type="submit" class="flex items-center gap-2 text-sm text-red-500 hover:text-red-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Remover treinamento
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>

</x-layout.app>
