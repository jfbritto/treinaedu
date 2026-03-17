<x-layout.app title="Meus Treinamentos">

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Meus Treinamentos</h2>
        <a href="{{ route('instructor.trainings.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
            + Novo Treinamento
        </a>
    </div>

    <x-ui.table :headers="['Título', 'Visualizações', 'Concluídos', 'Taxa', 'Ações']">
        @forelse($trainings as $training)
            <tr>
                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $training->title }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $training->views_count }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $training->completed_count }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">
                    @if($training->views_count > 0)
                        {{ round(($training->completed_count / $training->views_count) * 100) }}%
                    @else
                        -
                    @endif
                </td>
                <td class="px-6 py-4 text-sm">
                    <a href="{{ route('instructor.trainings.edit', $training) }}" class="text-blue-600 hover:underline">Editar</a>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-6 py-4 text-center text-gray-400">Nenhum treinamento criado ainda.</td></tr>
        @endforelse
        <x-slot:pagination>{{ $trainings->links() }}</x-slot:pagination>
    </x-ui.table>

</x-layout.app>
