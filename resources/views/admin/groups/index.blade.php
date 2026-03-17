<x-layout.app title="Grupos">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Grupos</h2>
        <a href="{{ route('groups.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">+ Novo Grupo</a>
    </div>

    <x-ui.table :headers="['Nome', 'Descrição', 'Membros', 'Ações']">
        @forelse($groups as $group)
            <tr>
                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $group->name }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $group->description ?: '-' }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $group->users_count }}</td>
                <td class="px-6 py-4 text-sm flex gap-3">
                    <a href="{{ route('groups.edit', $group) }}" class="text-blue-600 hover:underline">Editar</a>
                    <form method="POST" action="{{ route('groups.destroy', $group) }}" onsubmit="return confirm('Remover este grupo?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:underline">Remover</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="px-6 py-4 text-center text-gray-400">Nenhum grupo cadastrado.</td></tr>
        @endforelse
        <x-slot:pagination>{{ $groups->links() }}</x-slot:pagination>
    </x-ui.table>
</x-layout.app>
