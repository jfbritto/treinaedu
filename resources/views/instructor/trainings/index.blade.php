<x-layout.app title="Meus Treinamentos">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Meus Treinamentos</h2>
        <a href="{{ route('instructor.trainings.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">+ Criar Treinamento</a>
    </div>

    @if(session('success'))
        <x-ui.alert type="success" class="mb-4">{{ session('success') }}</x-ui.alert>
    @endif

    <x-ui.table :headers="['Título', 'Duração', 'Ações']">
        @forelse($trainings as $training)
            <tr>
                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $training->title }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $training->duration_minutes }} min</td>
                <td class="px-6 py-4 text-sm flex gap-3">
                    <a href="{{ route('instructor.trainings.edit', $training) }}" class="text-blue-600 hover:underline">Editar</a>
                    <form method="POST" action="{{ route('instructor.trainings.destroy', $training) }}" data-confirm="Remover este treinamento?">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:underline">Excluir</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="3" class="px-6 py-4 text-center text-gray-400">Nenhum treinamento cadastrado.</td></tr>
        @endforelse
        <x-slot:pagination>{{ $trainings->links() }}</x-slot:pagination>
    </x-ui.table>
</x-layout.app>
