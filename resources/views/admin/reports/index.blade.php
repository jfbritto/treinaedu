<x-layout.app title="Relatórios">

    {{-- Reports JavaScript Functions (injected into head) --}}
    @push('head')
    <script>
        console.log('📋 [HEAD] Reports JavaScript loaded');

        window.filterForm = function() {
            console.log('✓ filterForm() initialized');
            const filterFormData = {
                filters: {
                    training_id: new URLSearchParams(window.location.search).get('training_id') || '',
                    group_id: new URLSearchParams(window.location.search).get('group_id') || '',
                    status: new URLSearchParams(window.location.search).get('status') || '',
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
                    // Get activeTab from the registered reportsContent instance
                    const activeTab = window.__reportsContentData?.activeTab || 'general';
                    console.log('📋 applyFilters - activeTab:', activeTab);

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
                    };
                    this.applyFilters();
                }
            };

            // Register globally so reportsContent can access it
            window.__filterFormData = filterFormData;
            return filterFormData;
        };

        window.reportsContent = function() {
            console.log('✓ reportsContent() initialized');
            const reportsContentData = {
                activeTab: 'general',
                isLoading: false,
                groupTableHtml: '',
                groupChart: null,
                instructorTableHtml: '',
                instructorChart: null,
                periodTableHtml: '',
                periodChart: null,

                setTab(tab) {
                    console.log('📑 setTab called:', tab);
                    this.activeTab = tab;

                    // For general tab, reload page to apply filters server-side with pagination
                    if (tab === 'general') {
                        const params = new URLSearchParams(window.__filterFormData.filters);
                        window.location.search = params.toString();
                    } else {
                        this.applyFilters();
                    }
                },

                applyFilters() {
                    console.log('▶ reportsContent.applyFilters() called');

                    // Use window reference to get filterForm instance
                    if (window.__filterFormData) {
                        console.log('✓ Calling filterForm.applyFilters() via window...');
                        window.__filterFormData.applyFilters();
                    } else {
                        console.warn('⚠ filterForm not found in window reference');
                    }
                },

                init() {
                    console.log('📂 reportsContent.init() called - loading data');
                    window.addEventListener('data-updated', (e) => {
                        console.log('📥 data-updated event received:', e.detail);
                        this.handleDataUpdate(e.detail.data, e.detail.tab);
                    });

                    // Load initial data after a small delay to ensure DOM is ready
                    setTimeout(() => {
                        console.log('⏳ Calling applyFilters...');
                        this.applyFilters();
                    }, 100);
                },

                handleDataUpdate(responseData, tab) {
                    console.log(`📥 Handling ${tab} tab data:`, responseData);

                    if (tab === 'group') {
                        // Group analysis returns array directly
                        this.renderGroupAnalysis(responseData);
                    } else if (tab === 'instructor') {
                        // Instructor analysis returns array directly
                        this.renderInstructorAnalysis(responseData);
                    } else if (tab === 'period') {
                        // Period analysis returns array directly
                        this.renderPeriodAnalysis(responseData);
                    }
                },

                renderGroupAnalysis(data) {
                    console.log('📊 renderGroupAnalysis called with:', data);
                    if (!data || !Array.isArray(data) || data.length === 0) {
                        this.groupTableHtml = '<p class="p-4 text-gray-500">Nenhum dado disponível</p>';
                        return;
                    }

                    // Render chart
                    setTimeout(() => {
                        const canvas = document.getElementById('groupChart');
                        if (canvas) {
                            const labels = data.map(row => row.group_name);
                            const completedData = data.map(row => row.completed || 0);
                            const pendingData = data.map(row => row.pending || 0);

                            if (window.groupChartInstance) {
                                window.groupChartInstance.destroy();
                            }

                            window.groupChartInstance = new Chart(canvas, {
                                type: 'bar',
                                data: {
                                    labels: labels,
                                    datasets: [
                                        {
                                            label: 'Concluídos',
                                            data: completedData,
                                            backgroundColor: '#10b981',
                                        },
                                        {
                                            label: 'Pendentes',
                                            data: pendingData,
                                            backgroundColor: '#f59e0b',
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            position: 'bottom',
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true
                                        }
                                    }
                                }
                            });
                        }
                    }, 100);

                    let html = `
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Grupo</th>
                                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Total</th>
                                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Concluídos</th>
                                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Taxa de Conclusão</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    data.forEach(row => {
                        const completion = Math.round((row.completed || 0) / (row.total || 1) * 100);
                        html += `
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-gray-900 font-medium">${row.group_name || 'N/A'}</td>
                                <td class="px-4 py-3 text-gray-600">${row.total || 0}</td>
                                <td class="px-4 py-3 text-gray-600">${row.completed || 0}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-20 bg-gray-200 rounded-full h-2">
                                            <div class="bg-green-600 h-2 rounded-full" style="width:${completion}%"></div>
                                        </div>
                                        <span class="text-xs font-medium text-gray-600">${completion}%</span>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });

                    html += `</tbody></table></div>`;
                    this.groupTableHtml = html;
                },

                renderInstructorAnalysis(data) {
                    console.log('📊 renderInstructorAnalysis called with:', data);
                    if (!data || !Array.isArray(data) || data.length === 0) {
                        this.instructorTableHtml = '<p class="p-4 text-gray-500">Nenhum dado disponível</p>';
                        return;
                    }

                    // Render chart
                    setTimeout(() => {
                        const canvas = document.getElementById('instructorChart');
                        if (canvas) {
                            const labels = data.map(row => row.instructor_name);
                            const completedData = data.map(row => row.completed || 0);
                            const pendingData = data.map(row => row.pending || 0);

                            if (window.instructorChartInstance) {
                                window.instructorChartInstance.destroy();
                            }

                            window.instructorChartInstance = new Chart(canvas, {
                                type: 'doughnut',
                                data: {
                                    labels: labels,
                                    datasets: [{
                                        data: data.map(row => row.total || 0),
                                        backgroundColor: [
                                            '#3b82f6',
                                            '#10b981',
                                            '#f59e0b',
                                            '#ef4444',
                                            '#8b5cf6'
                                        ]
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            position: 'bottom',
                                        }
                                    }
                                }
                            });
                        }
                    }, 100);

                    let html = `
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Instrutor</th>
                                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Total</th>
                                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Concluídos</th>
                                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Progresso Médio</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    data.forEach(row => {
                        html += `
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-gray-900 font-medium">${row.instructor_name || 'N/A'}</td>
                                <td class="px-4 py-3 text-gray-600">${row.total || 0}</td>
                                <td class="px-4 py-3 text-gray-600">${row.completed || 0}</td>
                                <td class="px-4 py-3 text-gray-600">${row.avg_progress || 0}%</td>
                            </tr>
                        `;
                    });

                    html += `</tbody></table></div>`;
                    this.instructorTableHtml = html;
                },

                renderPeriodAnalysis(data) {
                    console.log('📊 renderPeriodAnalysis called with:', data);
                    if (!data || !Array.isArray(data) || data.length === 0) {
                        this.periodTableHtml = '<p class="p-4 text-gray-500">Nenhum dado disponível</p>';
                        return;
                    }

                    // Helper to format date to Brazilian format
                    const formatDateBR = (dateStr) => {
                        if (!dateStr) return 'N/A';
                        const [year, month, day] = dateStr.split('-');
                        return `${day}/${month}/${year}`;
                    };

                    // Render chart
                    setTimeout(() => {
                        const canvas = document.getElementById('periodChart');
                        if (canvas) {
                            const labels = data.map(row => formatDateBR(row.period));
                            const completedData = data.map(row => row.completed || 0);

                            if (window.periodChartInstance) {
                                window.periodChartInstance.destroy();
                            }

                            window.periodChartInstance = new Chart(canvas, {
                                type: 'line',
                                data: {
                                    labels: labels,
                                    datasets: [{
                                        label: 'Concluídos',
                                        data: completedData,
                                        borderColor: '#8b5cf6',
                                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                                        tension: 0.4,
                                        fill: true
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            position: 'bottom',
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true
                                        }
                                    }
                                }
                            });
                        }
                    }, 100);

                    let html = `
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Período</th>
                                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Total</th>
                                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Concluídos</th>
                                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Taxa de Conclusão</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    data.forEach(row => {
                        const completion = Math.round((row.completed || 0) / (row.total || 1) * 100);
                        html += `
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-gray-900 font-medium">${formatDateBR(row.period)}</td>
                                <td class="px-4 py-3 text-gray-600">${row.total || 0}</td>
                                <td class="px-4 py-3 text-gray-600">${row.completed || 0}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-20 bg-gray-200 rounded-full h-2">
                                            <div class="bg-purple-600 h-2 rounded-full" style="width:${completion}%"></div>
                                        </div>
                                        <span class="text-xs font-medium text-gray-600">${completion}%</span>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });

                    html += `</tbody></table></div>`;
                    this.periodTableHtml = html;
                }
            };

            // Register globally so it can be accessed from filterForm
            window.__reportsContentData = reportsContentData;
            return reportsContentData;
        };
    </script>
    @endpush

    {{-- Header --}}
    <div class="mb-6">
        <p class="text-sm text-gray-500">Acompanhe o progresso e conclusões da equipe</p>
    </div>

    {{-- Sticky Filters --}}
    <x-reports.filter-sticky :trainings="$trainings" :groups="$groups" />

    {{-- KPI Section with Toggle --}}
    <div x-data="{
            stats: { total: '-', completed: '-', pending: '-', avg_progress: '-' },
            showStats: true
         }"
         @filter-updated.window="stats = $event.detail.stats">

        {{-- KPI Toggle --}}
        <div class="flex items-center gap-2 mb-4">
            <button @click="showStats = !showStats"
                    class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800 transition">
                <svg :class="showStats ? 'rotate-90' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span x-text="showStats ? 'Ocultar indicadores' : 'Mostrar indicadores'"></span>
            </button>
        </div>

        {{-- KPI Cards --}}
        <div x-show="showStats" x-transition class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6"
             id="statsContainer">

        <x-reports.kpi-card key="total" label="Registros totais" color="blue" sublabel="visualizações de treinamentos">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </x-reports.kpi-card>

        <x-reports.kpi-card key="completed" label="Concluídos" color="green" sublabel="treinamentos finalizados">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </x-reports.kpi-card>

        <x-reports.kpi-card key="pending" label="Pendentes" color="amber" sublabel="em andamento ou não iniciados">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </x-reports.kpi-card>

        <x-reports.kpi-card key="avg_progress" label="Progresso médio" color="primary" sublabel="média geral de conclusão">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
        </x-reports.kpi-card>
        </div>
    </div>

    {{-- Tabs and Content --}}
    <div x-data="reportsContent()" id="reportsContent" class="mb-6">
        {{-- Tabs Navigation --}}
        <div class="mb-4 mt-4">
            <div class="flex gap-2 border-b border-gray-200">
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
                @if($views->isEmpty())
                    <div class="p-12 text-center">
                        <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <p class="text-gray-400 text-sm font-medium">Nenhum registro encontrado.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="text-left px-4 py-3 font-semibold text-gray-700">Funcionário</th>
                                    <th class="text-left px-4 py-3 font-semibold text-gray-700">Treinamento</th>
                                    <th class="text-left px-4 py-3 font-semibold text-gray-700">Progresso</th>
                                    <th class="text-left px-4 py-3 font-semibold text-gray-700">Data Início</th>
                                    <th class="text-left px-4 py-3 font-semibold text-gray-700">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($views as $view)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 text-gray-900">{{ $view->user?->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 text-gray-900">{{ $view->training?->title ?? 'N/A' }}</td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <div class="w-20 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-primary h-2 rounded-full" style="width:{{ $view->progress_percent ?? 0 }}%"></div>
                                                </div>
                                                <span class="text-xs font-medium text-gray-600">{{ $view->progress_percent ?? 0 }}%</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $view->started_at ? $view->started_at->format('d/m/Y') : '-' }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $view->completed_at ? 'bg-green-50 text-green-700' : 'bg-yellow-50 text-yellow-700' }}">
                                                {{ $view->completed_at ? 'Concluído' : 'Pendente' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($views->hasPages())
                        <div class="px-4 py-4 border-t border-gray-100">
                            {{ $views->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </x-reports.tab-panel>

        {{-- Group Tab --}}
        <x-reports.tab-panel name="group">
            <x-reports.chart-container chart-id="groupChart" title="Progresso por Grupo" description="Visualize a taxa de conclusão e treinamentos pendentes em cada grupo. Use para identificar quais grupos precisam de mais apoio ou recursos." height="350px" />
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div id="groupContent" x-html="groupTableHtml"></div>
            </div>
        </x-reports.tab-panel>

        {{-- Instructor Tab --}}
        <x-reports.tab-panel name="instructor">
            <x-reports.chart-container chart-id="instructorChart" title="Performance dos Instrutores" description="Compare o número de treinamentos e taxas de conclusão por instrutor. Identifique os melhores desempenhos e áreas que precisam de melhoria." height="350px" />
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div id="instructorContent" x-html="instructorTableHtml"></div>
            </div>
        </x-reports.tab-panel>

        {{-- Period Tab --}}
        <x-reports.tab-panel name="period">
            <x-reports.chart-container chart-id="periodChart" title="Progressão ao Longo do Tempo" description="Acompanhe as tendências de conclusão de treinamentos ao longo do tempo. Visualize padrões sazonais e melhorias na taxa de conclusão." height="350px" />
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div id="periodContent" x-html="periodTableHtml"></div>
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
