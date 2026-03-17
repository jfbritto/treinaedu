<x-layout.app title="Treinamentos">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Treinamentos</h2>
        <a href="{{ route('trainings.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">+ Novo Treinamento</a>
    </div>

    <x-ui.table :headers="['Título', 'Duração', 'Quiz', 'Ativo', 'Conclusão', 'Ações']">
        @forelse($trainings as $training)
            <tr>
                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                    <a href="{{ route('trainings.show', $training) }}" class="hover:text-blue-600">{{ $training->title }}</a>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $training->duration_minutes }} min</td>
                <td class="px-6 py-4 text-sm">
                    <span class="{{ $training->has_quiz ? 'text-green-600' : 'text-gray-400' }}">
                        {{ $training->has_quiz ? '✓ Sim' : 'Não' }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm">
                    <span class="{{ $training->active ? 'text-green-600' : 'text-red-500' }}">
                        {{ $training->active ? 'Ativo' : 'Inativo' }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $training->completionRate() }}%</td>
                <td class="px-6 py-4 text-sm flex gap-3">
                    <a href="{{ route('trainings.edit', $training) }}" class="text-blue-600 hover:underline">Editar</a>
                    <form method="POST" action="{{ route('trainings.destroy', $training) }}" onsubmit="return confirm('Remover este treinamento?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:underline">Remover</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-6 py-4 text-center text-gray-400">Nenhum treinamento cadastrado.</td></tr>
        @endforelse
        <x-slot:pagination>{{ $trainings->links() }}</x-slot:pagination>
    </x-ui.table>
</x-layout.app>
