<x-layout.app title="Grupo">
    <div class="max-w-2xl">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('groups.index') }}" class="text-gray-400 hover:text-gray-600">← Voltar</a>
            <h2 class="text-xl font-semibold text-gray-800">{{ $group->name }}</h2>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-gray-600 mb-4">{{ $group->description ?: 'Sem descrição.' }}</p>
            <h3 class="font-medium text-gray-800 mb-3">Membros ({{ $group->users->count() }})</h3>
            <ul class="space-y-1">
                @foreach($group->users as $user)
                    <li class="text-sm text-gray-600">{{ $user->name }} — {{ $user->email }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</x-layout.app>
