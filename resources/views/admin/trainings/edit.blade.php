<x-layout.app title="Editar Treinamento">
    <div class="max-w-3xl">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('trainings.index') }}" class="text-gray-400 hover:text-gray-600">← Voltar</a>
            <h2 class="text-xl font-semibold text-gray-800">Editar Treinamento</h2>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form method="POST" action="{{ route('trainings.update', $training) }}" class="space-y-5">
                @csrf @method('PUT')
                <x-forms.input name="title" label="Título" :value="$training->title" required />
                <div class="space-y-1">
                    <label class="block text-sm font-medium text-gray-700">Descrição</label>
                    <textarea name="description" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">{{ old('description', $training->description) }}</textarea>
                </div>
                <x-forms.input name="duration_minutes" label="Duração (minutos)" type="number" :value="$training->duration_minutes" required />
                <div class="space-y-1">
                    <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                        <input type="checkbox" name="active" value="1" {{ $training->active ? 'checked' : '' }} class="rounded border-gray-300">
                        Treinamento ativo
                    </label>
                </div>
                {{-- Vídeo atual --}}
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                    <p class="text-xs font-medium text-gray-500 mb-1">Vídeo atual</p>
                    <a href="{{ $training->video_url }}" target="_blank" rel="noopener"
                        class="text-sm text-blue-600 hover:underline break-all">
                        {{ $training->video_url }}
                    </a>
                    <p class="text-xs text-gray-400 mt-2">Para trocar o vídeo ou editar o quiz, remova e recrie o treinamento.</p>
                </div>
                <div class="flex gap-3 pt-2">
                    <x-forms.button type="submit">Salvar</x-forms.button>
                    <a href="{{ route('trainings.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-layout.app>
