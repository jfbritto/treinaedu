<x-layout.app title="Novo Treinamento">
    <div class="max-w-3xl">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('instructor.trainings.index') }}" class="text-gray-400 hover:text-gray-600">← Voltar</a>
            <h2 class="text-xl font-semibold text-gray-800">Novo Treinamento</h2>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <form method="POST" action="{{ route('instructor.trainings.store') }}" class="space-y-6">
                @csrf

                <x-forms.input name="title" label="Título" required />
                <div class="space-y-1">
                    <label class="block text-sm font-medium text-gray-700">Descrição</label>
                    <textarea name="description" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                </div>
                <x-forms.input name="video_url" label="URL do Vídeo (YouTube ou Vimeo)" type="url" required />
                <x-forms.input name="duration_minutes" label="Duração (minutos)" type="number" required />

                <div class="flex gap-3 pt-2">
                    <x-forms.button type="submit">Salvar</x-forms.button>
                    <a href="{{ route('instructor.trainings.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-layout.app>
