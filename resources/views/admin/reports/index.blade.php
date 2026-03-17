<x-layout.app title="Relatórios">

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Relatório de Treinamentos</h2>
        <div class="flex gap-2">
            <a href="{{ route('reports.export.pdf', request()->query()) }}"
               class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                Exportar PDF
            </a>
            <a href="{{ route('reports.export.excel', request()->query()) }}"
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                Exportar Excel
            </a>
        </div>
    </div>

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('reports.index') }}" class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Treinamento</label>
                <select name="training_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    @foreach($trainings as $training)
                        <option value="{{ $training->id }}" {{ request('training_id') == $training->id ? 'selected' : '' }}>
                            {{ $training->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Grupo</label>
                <select name="group_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>
                            {{ $group->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Concluído</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendente</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data início</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data fim</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div class="mt-4 flex gap-2">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                Filtrar
            </button>
            <a href="{{ route('reports.index') }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">
                Limpar
            </a>
        </div>
    </form>

    {{-- Results Table --}}
    <x-ui.table :headers="['Funcionário', 'Treinamento', 'Progresso', 'Status', 'Data de Conclusão']">
        @forelse($views as $view)
            <tr>
                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $view->user->name ?? 'N/A' }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $view->training->title ?? 'N/A' }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">
                    <div class="flex items-center gap-2">
                        <div class="w-24 bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $view->progress_percent }}%"></div>
                        </div>
                        <span>{{ $view->progress_percent }}%</span>
                    </div>
                </td>
                <td class="px-6 py-4 text-sm">
                    @if($view->completed_at)
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Concluído</span>
                    @else
                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">Pendente</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">
                    {{ $view->completed_at?->format('d/m/Y') ?? '-' }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-400">Nenhum registro encontrado.</td>
            </tr>
        @endforelse
        <x-slot:pagination>{{ $views->appends(request()->query())->links() }}</x-slot:pagination>
    </x-ui.table>

</x-layout.app>
