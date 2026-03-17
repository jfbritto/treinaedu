<x-layout.app title="Editar Grupo">
    <div class="max-w-2xl">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('groups.index') }}" class="text-gray-400 hover:text-gray-600">← Voltar</a>
            <h2 class="text-xl font-semibold text-gray-800">Editar Grupo</h2>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form method="POST" action="{{ route('groups.update', $group) }}" class="space-y-5">
                @csrf @method('PUT')
                <x-forms.input name="name" label="Nome do Grupo" :value="$group->name" required />
                <div class="space-y-1">
                    <label class="block text-sm font-medium text-gray-700">Descrição</label>
                    <textarea name="description" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $group->description) }}</textarea>
                </div>
                @php $groupUserIds = $group->users->pluck('id')->toArray(); @endphp
                @if($users->isNotEmpty())
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Membros</label>
                    <div class="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto border rounded-lg p-3">
                        @foreach($users as $user)
                            <label class="flex items-center gap-2 text-sm text-gray-600">
                                <input type="checkbox" name="users[]" value="{{ $user->id }}"
                                    {{ in_array($user->id, old('users', $groupUserIds)) ? 'checked' : '' }}
                                    class="rounded border-gray-300">
                                {{ $user->name }}
                            </label>
                        @endforeach
                    </div>
                </div>
                @endif
                <div class="flex gap-3 pt-2">
                    <x-forms.button type="submit">Salvar</x-forms.button>
                    <a href="{{ route('groups.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-layout.app>
