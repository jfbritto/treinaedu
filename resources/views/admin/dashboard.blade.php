<x-layout.app title="Dashboard Admin">

    {{-- Metrics --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-ui.card title="Colaboradores" :value="$metrics['total_employees']" icon="👥" />
        <x-ui.card title="Treinamentos" :value="$metrics['trainings_created']" icon="🎓" color="green" />
        <x-ui.card title="Concluídos" :value="$metrics['trainings_completed']" icon="✅" color="teal" />
        <x-ui.card title="Certificados Emitidos" :value="$metrics['certificates_issued']" icon="📜" color="purple" />
    </div>

    {{-- Quick links --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('users.create') }}" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition text-center">
            <div class="text-3xl mb-2">➕</div>
            <p class="font-medium text-gray-700">Novo Usuário</p>
        </a>
        <a href="{{ route('trainings.create') }}" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition text-center">
            <div class="text-3xl mb-2">🎬</div>
            <p class="font-medium text-gray-700">Novo Treinamento</p>
        </a>
        <a href="{{ route('reports.index') }}" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition text-center">
            <div class="text-3xl mb-2">📊</div>
            <p class="font-medium text-gray-700">Ver Relatórios</p>
        </a>
    </div>

</x-layout.app>
