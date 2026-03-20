{{-- resources/views/components/reports/filter-sticky.blade.php --}}
<div class="sticky top-0 z-40 bg-white border-b border-gray-100 shadow-sm"
     x-data="filterForm()"
     @submit.prevent="applyFilters()">
    <div class="px-6 py-4">
        <form @change="debounceFilter()" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                    Treinamento
                </label>
                <select name="training_id" x-model="filters.training_id"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:ring-2 focus:ring-primary">
                    <option value="">Todos</option>
                    @foreach($trainings as $training)
                        <option value="{{ $training->id }}">{{ $training->title }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                    Grupo
                </label>
                <select name="group_id" x-model="filters.group_id"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:ring-2 focus:ring-primary">
                    <option value="">Todos</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                    Status
                </label>
                <select name="status" x-model="filters.status"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:ring-2 focus:ring-primary">
                    <option value="">Todos</option>
                    <option value="completed">Concluído</option>
                    <option value="pending">Pendente</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                    Data início
                </label>
                <input type="date" name="date_from" x-model="filters.date_from"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:ring-2 focus:ring-primary">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                    Data fim
                </label>
                <input type="date" name="date_to" x-model="filters.date_to"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:ring-2 focus:ring-primary">
            </div>
        </form>

        <div class="mt-4 flex gap-2">
            <button type="button" @click="clearFilters()"
                    class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2 rounded-lg text-sm font-semibold transition">
                Limpar filtros
            </button>
            <div x-show="hasActiveFilters()" class="text-sm text-gray-600 flex items-center">
                <span class="inline-block bg-primary text-white px-2 py-1 rounded-full text-xs mr-2"
                      x-text="countActiveFilters() + ' filtro(s)'"></span>
            </div>
            <div x-show="isLoading" class="text-sm text-gray-500 flex items-center">
                <svg class="w-4 h-4 animate-spin mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Carregando...
            </div>
        </div>
    </div>
</div>
