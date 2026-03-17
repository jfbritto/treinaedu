<x-layout.app title="Nova Atribuição">
    <div class="max-w-2xl">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('training-assignments.index') }}" class="text-gray-400 hover:text-gray-600">← Voltar</a>
            <h2 class="text-xl font-semibold text-gray-800">Atribuir Treinamento a Grupos</h2>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form method="POST" action="{{ route('training-assignments.store') }}" class="space-y-5">
                @csrf

                <x-forms.select name="training_id" label="Treinamento" required
                    :options="$trainings->pluck('title', 'id')->toArray()" />

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Grupos *</label>
                    <div class="grid grid-cols-2 gap-2 border rounded-lg p-3">
                        @foreach($groups as $group)
                            <label class="flex items-center gap-2 text-sm text-gray-600">
                                <input type="checkbox" name="group_ids[]" value="{{ $group->id }}"
                                    {{ in_array($group->id, old('group_ids', [])) ? 'checked' : '' }}
                                    class="rounded border-gray-300">
                                {{ $group->name }}
                            </label>
                        @endforeach
                    </div>
                    @error('group_ids')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror
                </div>

                <x-forms.input name="due_date" label="Data Limite (opcional)" type="date" />

                <div class="flex gap-3 pt-2">
                    <x-forms.button type="submit">Atribuir</x-forms.button>
                    <a href="{{ route('training-assignments.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-layout.app>
