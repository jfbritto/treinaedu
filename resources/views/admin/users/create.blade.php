<x-layout.app title="Novo Usuário">

    <div class="max-w-2xl">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('users.index') }}" class="text-gray-400 hover:text-gray-600">← Voltar</a>
            <h2 class="text-xl font-semibold text-gray-800">Novo Usuário</h2>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <form method="POST" action="{{ route('users.store') }}" class="space-y-5">
                @csrf

                <x-forms.input name="name" label="Nome" required />
                <x-forms.input name="email" label="E-mail" type="email" required />
                <x-forms.input name="password" label="Senha" type="password" required />
                <x-forms.input name="password_confirmation" label="Confirmar Senha" type="password" required />

                <x-forms.select name="role" label="Perfil" required :options="['instructor' => 'Instrutor', 'employee' => 'Colaborador']" />

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Grupos</label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($groups as $group)
                            <label class="flex items-center gap-2 text-sm text-gray-600">
                                <input type="checkbox" name="groups[]" value="{{ $group->id }}"
                                    {{ in_array($group->id, old('groups', [])) ? 'checked' : '' }}
                                    class="rounded border-gray-300">
                                {{ $group->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <x-forms.button type="submit">Criar Usuário</x-forms.button>
                    <a href="{{ route('users.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

</x-layout.app>
