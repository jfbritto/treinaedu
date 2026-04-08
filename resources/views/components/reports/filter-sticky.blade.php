{{-- resources/views/components/reports/filter-sticky.blade.php --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6"
     x-data="filterForm()"
     @submit.prevent="applyFilters()">

    {{-- Header do card --}}
    <div class="px-6 py-4 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-gray-800">Filtros</h3>
                <p class="text-xs text-gray-400">Refine os relatórios por treinamento, grupo ou status</p>
            </div>
            <template x-if="hasActiveFilters()">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-primary/10 text-primary"
                      x-text="countActiveFilters() + ' filtro' + (countActiveFilters() > 1 ? 's' : '') + ' ativo' + (countActiveFilters() > 1 ? 's' : '')"></span>
            </template>
        </div>
    </div>

    <div class="px-6 py-5">
        <form @change="debounceFilter()" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="space-y-1.5">
                <label class="block text-xs font-medium text-gray-600">Treinamento</label>
                <select name="training_id" x-model="filters.training_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary transition">
                    <option value="">Todos os treinamentos</option>
                    @foreach($trainings as $training)
                        <option value="{{ $training->id }}">{{ $training->title }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-medium text-gray-600">Grupo</label>
                <select name="group_id" x-model="filters.group_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary transition">
                    <option value="">Todos os grupos</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-medium text-gray-600">Status</label>
                <select name="status" x-model="filters.status"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary transition">
                    <option value="">Todos</option>
                    <option value="completed">Concluído</option>
                    <option value="pending">Pendente</option>
                </select>
            </div>
        </form>

        <div class="mt-4 flex items-center gap-3">
            <button type="button" @click="clearFilters()"
                    class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-500 hover:text-gray-700 transition px-3 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-50"
                    x-show="hasActiveFilters()" x-cloak>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Limpar filtros
            </button>

            <template x-if="isLoading">
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <svg class="w-3.5 h-3.5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>Atualizando...</span>
                </div>
            </template>
        </div>
    </div>
</div>
