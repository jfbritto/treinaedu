<x-layout.app title="Editar Usuário">

    <div class="max-w-2xl">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('users.index') }}" class="text-gray-400 hover:text-gray-600">← Voltar</a>
            <h2 class="text-xl font-semibold text-gray-800">Editar Usuário</h2>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <x-forms.input name="name" label="Nome" :value="$user->name" required />
                <x-forms.input name="email" label="E-mail" type="email" :value="$user->email" required />
                <x-forms.input name="password" label="Nova Senha (deixe em branco para manter)" type="password" />
                <x-forms.input name="password_confirmation" label="Confirmar Nova Senha" type="password" />

                <x-forms.select name="role" label="Perfil" required :options="['instructor' => 'Instrutor', 'employee' => 'Colaborador']" :selected="$user->role" />

                <div class="space-y-1">
                    <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                        <input type="checkbox" name="active" value="1" {{ $user->active ? 'checked' : '' }} class="rounded border-gray-300">
                        Usuário ativo
                    </label>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Grupos</label>
                    @php $userGroupIds = $user->groups->pluck('id')->toArray(); @endphp
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($groups as $group)
                            <label class="flex items-center gap-2 text-sm text-gray-600">
                                <input type="checkbox" name="groups[]" value="{{ $group->id }}"
                                    {{ in_array($group->id, old('groups', $userGroupIds)) ? 'checked' : '' }}
                                    class="rounded border-gray-300">
                                {{ $group->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <x-forms.button type="submit">Salvar</x-forms.button>
                    <a href="{{ route('users.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

</x-layout.app>
