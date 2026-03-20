{{-- resources/views/components/reports/filter-sticky.blade.php --}}
<div class="sticky top-0 z-40 bg-white border-b border-gray-200 shadow-sm"
     x-data="filterForm()"
     @submit.prevent="applyFilters()">
    <div class="px-6 py-4">
        <form @change="debounceFilter()" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">
                    Treinamento
                </label>
                <select name="training_id" x-model="filters.training_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <option value="">Todos</option>
                    @foreach($trainings as $training)
                        <option value="{{ $training->id }}">{{ $training->title }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">
                    Grupo
                </label>
                <select name="group_id" x-model="filters.group_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <option value="">Todos</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">
                    Status
                </label>
                <select name="status" x-model="filters.status"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <option value="">Todos</option>
                    <option value="completed">Concluído</option>
                    <option value="pending">Pendente</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">
                    Data início
                </label>
                <input type="date" name="date_from" x-model="filters.date_from"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">
                    Data fim
                </label>
                <input type="date" name="date_to" x-model="filters.date_to"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>
        </form>

        <div class="mt-4 flex items-center gap-3">
            <button type="button" @click="clearFilters()"
                    class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Limpar
            </button>

            <template x-if="hasActiveFilters()">
                <span class="inline-block bg-blue-100 text-blue-700 px-3 py-1.5 rounded-full text-xs font-semibold"
                      x-text="countActiveFilters() + ' filtro(s) ativo(s)'"></span>
            </template>

            <template x-if="isLoading">
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span>Carregando...</span>
                </div>
            </template>
        </div>
    </div>
</div>
