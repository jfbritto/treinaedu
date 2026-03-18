<x-layout.app title="Atribuições de Treinamentos">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Atribuições de Treinamentos</h2>
        <a href="{{ route('training-assignments.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">+ Nova Atribuição</a>
    </div>

    <x-ui.table :headers="['Treinamento', 'Grupo', 'Prazo', 'Ações']">
        @forelse($assignments as $assignment)
            <tr>
                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $assignment->training->title }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $assignment->group->name }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $assignment->due_date ? $assignment->due_date->format('d/m/Y') : '-' }}</td>
                <td class="px-6 py-4 text-sm">
                    <form method="POST" action="{{ route('training-assignments.destroy', $assignment) }}" data-confirm="Remover esta atribuição?">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:underline">Remover</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="px-6 py-4 text-center text-gray-400">Nenhuma atribuição cadastrada.</td></tr>
        @endforelse
        <x-slot:pagination>{{ $assignments->links() }}</x-slot:pagination>
    </x-ui.table>
</x-layout.app>
