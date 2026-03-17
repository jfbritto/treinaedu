<x-layout.app title="Usuários">

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Usuários</h2>
        <a href="{{ route('users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
            + Novo Usuário
        </a>
    </div>

    <x-ui.table :headers="['Nome', 'E-mail', 'Perfil', 'Grupos', 'Status', 'Ações']">
        @forelse($users as $user)
            <tr>
                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $user->name }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $user->email }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">
                    <span class="px-2 py-1 text-xs rounded-full {{ $user->role === 'instructor' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ $user->role === 'instructor' ? 'Instrutor' : 'Colaborador' }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">
                    {{ $user->groups->pluck('name')->join(', ') ?: '-' }}
                </td>
                <td class="px-6 py-4 text-sm">
                    <span class="{{ $user->active ? 'text-green-600' : 'text-red-500' }}">
                        {{ $user->active ? 'Ativo' : 'Inativo' }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm flex gap-3">
                    <a href="{{ route('users.edit', $user) }}" class="text-blue-600 hover:underline">Editar</a>
                    <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Remover este usuário?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:underline">Remover</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-400">Nenhum usuário cadastrado.</td>
            </tr>
        @endforelse
        <x-slot:pagination>{{ $users->links() }}</x-slot:pagination>
    </x-ui.table>

</x-layout.app>
