<x-layout.app title="Relatórios">

    {{-- Reports JavaScript Functions (injected into head) --}}
    @push('head')
    <script>
        console.log('📋 [HEAD] Reports JavaScript loaded');

        window.filterForm = function() {
            console.log('✓ filterForm() initialized');
            return {
                filters: {
                    training_id: new URLSearchParams(window.location.search).get('training_id') || '',
                    group_id: new URLSearchParams(window.location.search).get('group_id') || '',
                    status: new URLSearchParams(window.location.search).get('status') || '',
                    date_from: new URLSearchParams(window.location.search).get('date_from') || '',
                    date_to: new URLSearchParams(window.location.search).get('date_to') || '',
                },
                isLoading: false,
                debounceTimer: null,

                debounceFilter() {
                    clearTimeout(this.debounceTimer);
                    this.debounceTimer = setTimeout(() => {
                        this.applyFilters();
                    }, 400);
                },

                hasActiveFilters() {
                    return Object.values(this.filters).some(v => v !== '');
                },

                countActiveFilters() {
                    return Object.values(this.filters).filter(v => v !== '').length;
                },

                async applyFilters() {
                    this.isLoading = true;
                    const reportsContent = document.querySelector('[x-data*="reportsContent"]')?.__x;
                    const activeTab = reportsContent?.$data?.activeTab || 'general';

                    try {
                        const params = new URLSearchParams(this.filters);
                        params.append('tab', activeTab);

                        console.log('Fetching /reports/filter with params:', params.toString());
                        const response = await fetch(`/reports/filter?${params}`);
                        if (!response.ok) throw new Error(`API error: ${response.status}`);
                        const json = await response.json();

                        console.log('Received response:', json);

                        // Update stats globally
                        window.dispatchEvent(new CustomEvent('filter-updated', {
                            detail: { stats: json.stats }
                        }));

                        // Notify tabs to update content
                        window.dispatchEvent(new CustomEvent('data-updated', {
                            detail: { data: json.data, tab: json.tab }
                        }));

                        console.log('Events dispatched, data should be updating');

                        // Update URL
                        window.history.replaceState({}, '', `?${params}`);
                    } catch (error) {
                        console.error('Filter error:', error);
                        this.isLoading = false;
                    } finally {
                        this.isLoading = false;
                    }
                },

                clearFilters() {
                    this.filters = {
                        training_id: '',
                        group_id: '',
                        status: '',
                        date_from: '',
                        date_to: '',
                    };
                    this.applyFilters();
                }
            };
        };

        window.reportsContent = function() {
            console.log('✓ reportsContent() initialized');
            return {
                activeTab: 'general',
                isLoading: false,
                generalTableHtml: '<p class="p-4 text-gray-500">Carregando dados...</p>',
                groupTableHtml: '',
                groupChart: null,
                instructorTableHtml: '',
                instructorChart: null,
                periodTableHtml: '',
                periodChart: null,

                setTab(tab) {
                    this.activeTab = tab;
                    this.applyFilters();
                },

                applyFilters() {
                    const filterForm = document.querySelector('[x-data*="filterForm"]')?.__x;
                    if (filterForm && filterForm.$data) {
                        filterForm.$data.applyFilters();
                    }
                },

                init() {
                    console.log('📂 reportsContent.init() called - loading data');
                    window.addEventListener('data-updated', (e) => {
                        console.log('📥 data-updated event received:', e.detail);
                        this.handleDataUpdate(e.detail.data, e.detail.tab);
                    });

                    // Load initial data
                    this.$nextTick(() => {
                        console.log('⏳ Calling applyFilters...');
                        this.applyFilters();
                    });
                },

                handleDataUpdate(data, tab) {
                    if (tab === 'general') {
                        this.renderGeneralTable(data);
                    } else if (tab === 'group') {
                        this.renderGroupAnalysis(data);
                    } else if (tab === 'instructor') {
                        this.renderInstructorAnalysis(data);
                    } else if (tab === 'period') {
                        this.renderPeriodAnalysis(data);
                    }
                },

                renderGeneralTable(data) {
                    if (!data.data || !Array.isArray(data.data)) {
                        this.generalTableHtml = '<p class="p-4 text-gray-500">Nenhum dado disponível</p>';
                        return;
                    }

                    let html = '<table class="w-full text-sm"><thead><tr class="border-b"><th class="text-left p-3">Funcionário</th><th class="text-left p-3">Treinamento</th><th class="text-left p-3">Progresso</th><th class="text-left p-3">Status</th></tr></thead><tbody>';

                    data.data.forEach(row => {
                        const progress = row.progress || 0;
                        const status = row.completed_at ? 'Concluído' : 'Pendente';
                        const statusColor = row.completed_at ? 'text-green-600' : 'text-yellow-600';
                        html += `<tr class="border-b"><td class="p-3">${row.user_name || 'N/A'}</td><td class="p-3">${row.training_name || 'N/A'}</td><td class="p-3"><div class="bg-gray-200 rounded h-2"><div class="bg-green-600 h-2 rounded" style="width:${progress}%"></div></div></td><td class="p-3"><span class="${statusColor}">${status}</span></td></tr>`;
                    });

                    html += '</tbody></table>';
                    this.generalTableHtml = html;
                },

                renderGroupAnalysis(data) {
                    this.groupTableHtml = '<p class="p-4 text-gray-500">Dados de grupo disponíveis</p>';
                    if (data && Array.isArray(data)) {
                        let html = '<table class="w-full text-sm"><thead><tr class="border-b"><th class="text-left p-3">Grupo</th><th class="text-left p-3">Total</th><th class="text-left p-3">Concluídos</th><th class="text-left p-3">% Conclusão</th></tr></thead><tbody>';
                        data.forEach(row => {
                            html += `<tr class="border-b"><td class="p-3">${row.group_name || 'N/A'}</td><td class="p-3">${row.total || 0}</td><td class="p-3">${row.completed || 0}</td><td class="p-3">${Math.round((row.completed || 0) / (row.total || 1) * 100)}%</td></tr>`;
                        });
                        html += '</tbody></table>';
                        this.groupTableHtml = html;
                    }
                },

                renderInstructorAnalysis(data) {
                    this.instructorTableHtml = '<p class="p-4 text-gray-500">Dados de instrutor disponíveis</p>';
                },

                renderPeriodAnalysis(data) {
                    this.periodTableHtml = '<p class="p-4 text-gray-500">Dados de período disponíveis</p>';
                }
            };
        };
    </script>
    @endpush

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <p class="text-sm text-gray-500">Acompanhe o progresso e conclusões da equipe</p>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('reports.export.pdf', request()->query()) }}"
               class="inline-flex items-center gap-2 bg-primary hover:bg-secondary text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Exportar PDF
            </a>
            <a href="{{ route('reports.export.excel', request()->query()) }}"
               class="inline-flex items-center gap-2 border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                Exportar Excel
            </a>
        </div>
    </div>

    {{-- Sticky Filters --}}
    <x-reports.filter-sticky :trainings="$trainings" :groups="$groups" />

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6 relative z-30"
         id="statsContainer"
         x-data="{
            stats: { total: '-', completed: '-', pending: '-', avg_progress: '-' }
         }"
         @filter-updated.window="stats = $event.detail.stats">

        <x-reports.kpi-card key="total" label="Registros totais">
            <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24"><path d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75zM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 01-1.875-1.875V8.625zM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 013 19.875v-6.75z"/></svg>
        </x-reports.kpi-card>

        <x-reports.kpi-card key="completed" label="Concluídos (total)">
            <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd"/></svg>
        </x-reports.kpi-card>

        <x-reports.kpi-card key="pending" label="Pendentes (total)">
            <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zM12.75 6a.75.75 0 00-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 000-1.5h-3.75V6z" clip-rule="evenodd"/></svg>
        </x-reports.kpi-card>

        <x-reports.kpi-card key="avg_progress" label="Progresso médio">
            <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 24 24"><path d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75zM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 01-1.875-1.875V8.625zM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 013 19.875v-6.75z"/></svg>
        </x-reports.kpi-card>
    </div>

    {{-- Tabs and Content --}}
    <div x-data="reportsContent()" id="reportsContent" class="mb-6">
        {{-- Tabs Navigation --}}
        <div class="mb-6">
            <div class="flex gap-1 border-b border-gray-200">
                <button @click="setTab('general')"
                        :class="activeTab === 'general' ? 'border-b-2 border-primary text-primary' : 'text-gray-600 hover:text-gray-800'"
                        class="px-4 py-3 font-medium text-sm transition">
                    Geral
                </button>
                <button @click="setTab('group')"
                        :class="activeTab === 'group' ? 'border-b-2 border-primary text-primary' : 'text-gray-600 hover:text-gray-800'"
                        class="px-4 py-3 font-medium text-sm transition">
                    Por Grupo
                </button>
                <button @click="setTab('instructor')"
                        :class="activeTab === 'instructor' ? 'border-b-2 border-primary text-primary' : 'text-gray-600 hover:text-gray-800'"
                        class="px-4 py-3 font-medium text-sm transition">
                    Por Instrutor
                </button>
                <button @click="setTab('period')"
                        :class="activeTab === 'period' ? 'border-b-2 border-primary text-primary' : 'text-gray-600 hover:text-gray-800'"
                        class="px-4 py-3 font-medium text-sm transition">
                    Por Período
                </button>
            </div>
        </div>

        {{-- Tab Content --}}
        {{-- General Tab --}}
        <x-reports.tab-panel name="general">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div id="generalLoading" class="p-12 text-center">
                    <div class="inline-block animate-spin">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </div>
                </div>
                <div id="generalContent"></div>
            </div>
        </x-reports.tab-panel>

        {{-- Group Tab --}}
        <x-reports.tab-panel name="group">
            <x-reports.chart-container chart-id="groupChart" title="Progresso por Grupo" height="350px" />
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div id="groupContent"></div>
            </div>
        </x-reports.tab-panel>

        {{-- Instructor Tab --}}
        <x-reports.tab-panel name="instructor">
            <x-reports.chart-container chart-id="instructorChart" title="Performance dos Instrutores" height="350px" />
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div id="instructorContent"></div>
            </div>
        </x-reports.tab-panel>

        {{-- Period Tab --}}
        <x-reports.tab-panel name="period">
            <x-reports.chart-container chart-id="periodChart" title="Progressão ao Longo do Tempo" height="350px" />
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div id="periodContent"></div>
            </div>
        </x-reports.tab-panel>
    </div>

    <script>
        // Load initial data when page loads
        document.addEventListener('DOMContentLoaded', () => {
            // Find the reportsContent element and trigger initial load
            const element = document.querySelector('[x-data*="reportsContent"]');
            if (element && element.__x && element.__x.$data) {
                element.__x.$data.loadTab('general');
            }
        });
    </script>

</x-layout.app>
