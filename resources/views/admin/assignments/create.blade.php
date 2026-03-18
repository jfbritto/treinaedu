<x-layout.app title="Nova Atribuição">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('training-assignments.index') }}"
           class="inline-flex items-center gap-1 text-sm text-gray-400 hover:text-gray-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar
        </a>
        <h2 class="text-lg font-bold text-gray-800">Atribuir Treinamento a Grupos</h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Formulário --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <form method="POST" action="{{ route('training-assignments.store') }}" class="space-y-5">
                    @csrf

                    @if($errors->any())
                        <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-700">
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Treinamento --}}
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Treinamento <span class="text-red-500">*</span></label>
                        <select name="training_id" required
                            class="w-full rounded-lg border @error('training_id') border-red-400 @else border-gray-300 @enderror px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="">Selecione um treinamento...</option>
                            @foreach($trainings as $training)
                                <option value="{{ $training->id }}" {{ old('training_id') == $training->id ? 'selected' : '' }}>
                                    {{ $training->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('training_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Grupos --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Grupos <span class="text-red-500">*</span></label>
                        <p class="text-xs text-gray-400">Todos os colaboradores desses grupos receberão o treinamento.</p>
                        @if($groups->isEmpty())
                            <p class="text-sm text-gray-400 border border-dashed border-gray-200 rounded-lg p-4 text-center">
                                Nenhum grupo cadastrado. <a href="{{ route('groups.create') }}" class="text-primary hover:underline">Criar grupo →</a>
                            </p>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 border border-gray-200 rounded-xl p-4 bg-gray-50">
                                @foreach($groups as $group)
                                    <label class="flex items-center gap-2.5 text-sm text-gray-700 cursor-pointer hover:text-gray-900 transition">
                                        <input type="checkbox" name="group_ids[]" value="{{ $group->id }}"
                                            {{ in_array($group->id, old('group_ids', [])) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-primary">
                                        <span class="flex-1">{{ $group->name }}</span>
                                        <span class="text-xs text-gray-400">{{ $group->users_count ?? 0 }} membros</span>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                        @error('group_ids')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Data limite + Obrigatório --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Data limite</label>
                            <input type="date" name="due_date" value="{{ old('due_date') }}"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            <p class="text-xs text-gray-400">Deixe em branco se não houver prazo.</p>
                            @error('due_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="flex flex-col justify-center">
                            <label class="flex items-start gap-3 cursor-pointer p-4 rounded-xl border border-gray-200 hover:border-red-200 hover:bg-red-50 transition group">
                                <input type="checkbox" name="mandatory" value="1"
                                    {{ old('mandatory') ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-red-500 mt-0.5">
                                <div>
                                    <p class="text-sm font-medium text-gray-700 group-hover:text-red-700 transition">Treinamento obrigatório</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Aparece destacado para o colaborador e exige conclusão.</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit"
                            class="inline-flex items-center gap-2 bg-primary hover:bg-secondary text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Atribuir treinamento
                        </button>
                        <a href="{{ route('training-assignments.index') }}"
                           class="px-4 py-2.5 text-sm font-medium text-gray-500 hover:text-gray-700 transition">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Dica lateral --}}
        <div>
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-6 h-6 rounded-md bg-primary flex items-center justify-center flex-shrink-0">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-blue-900">Como funciona</p>
                </div>
                <div class="space-y-3">
                    <div class="flex gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-primary text-white text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">1</span>
                        <p class="text-xs text-blue-700">O treinamento aparece automaticamente para todos os membros dos grupos selecionados.</p>
                    </div>
                    <div class="flex gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-primary text-white text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">2</span>
                        <p class="text-xs text-blue-700">Se marcar como <strong>obrigatório</strong>, o colaborador vê um destaque vermelho e sabe que é exigência.</p>
                    </div>
                    <div class="flex gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-primary text-white text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">3</span>
                        <p class="text-xs text-blue-700">A <strong>data limite</strong> é exibida ao colaborador com aviso de urgência quando o prazo se aproxima.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-layout.app>
