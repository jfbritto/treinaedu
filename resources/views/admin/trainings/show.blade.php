<x-layout.app title="Detalhes do Treinamento">
    <div class="max-w-3xl">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('trainings.index') }}" class="text-gray-400 hover:text-gray-600">← Voltar</a>
            <h2 class="text-xl font-semibold text-gray-800">{{ $training->title }}</h2>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="text-gray-500">Duração:</span> <span class="font-medium">{{ $training->duration_minutes }} min</span></div>
                    <div><span class="text-gray-500">Provider:</span> <span class="font-medium capitalize">{{ $training->video_provider }}</span></div>
                    <div><span class="text-gray-500">Quiz:</span> <span class="font-medium">{{ $training->has_quiz ? 'Sim' : 'Não' }}</span></div>
                    <div><span class="text-gray-500">Taxa de conclusão:</span> <span class="font-medium">{{ $training->completionRate() }}%</span></div>
                    <div><span class="text-gray-500">Status:</span>
                        <span class="{{ $training->active ? 'text-green-600' : 'text-red-500' }} font-medium">{{ $training->active ? 'Ativo' : 'Inativo' }}</span>
                    </div>
                </div>
                @if($training->description)
                    <p class="text-gray-600 mt-4 text-sm">{{ $training->description }}</p>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-medium text-gray-800 mb-3">Grupos Atribuídos ({{ $training->assignments->count() }})</h3>
                @if($training->assignments->isEmpty())
                    <p class="text-gray-400 text-sm">Nenhum grupo atribuído. <a href="{{ route('training-assignments.create') }}" class="text-blue-600 hover:underline">Atribuir agora</a></p>
                @else
                    <ul class="space-y-1">
                        @foreach($training->assignments as $assignment)
                            <li class="text-sm text-gray-600">
                                {{ $assignment->group->name }}
                                @if($assignment->due_date)
                                    <span class="text-gray-400">— prazo: {{ $assignment->due_date->format('d/m/Y') }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</x-layout.app>
