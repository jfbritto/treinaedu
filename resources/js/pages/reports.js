// resources/js/pages/reports.js
import Chart from 'chart.js/auto';

window.filterForm = function() {
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
            const tab = document.querySelector('[x-data="reportsTabs()"]')?.__x?.getUnobservedData?.activeTab || 'general';

            try {
                const params = new URLSearchParams(this.filters);
                params.append('tab', tab);

                const response = await fetch(`/admin/reports/filter?${params}`);
                const json = await response.json();

                // Update stats globally
                window.dispatchEvent(new CustomEvent('filter-updated', {
                    detail: { stats: json.stats }
                }));

                // Notify tabs to update content
                window.dispatchEvent(new CustomEvent('data-updated', {
                    detail: { data: json.data, tab: json.tab }
                }));

                // Update URL
                window.history.replaceState({}, '', `?${params}`);
            } catch (error) {
                console.error('Filter error:', error);
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

window.reportsTabs = function() {
    return {
        activeTab: 'general',

        setTab(tab) {
            this.activeTab = tab;
            document.querySelector('[x-data="filterForm()"]')?.__x?.getUnobservedData?.applyFilters?.();
        }
    };
};

window.reportsContent = function() {
    return {
        activeTab: 'general',
        isLoading: false,
        generalTableHtml: '',
        groupTableHtml: '',
        groupChart: null,
        instructorTableHtml: '',
        instructorChart: null,
        periodTableHtml: '',
        periodChart: null,

        init() {
            window.addEventListener('data-updated', (e) => {
                this.handleDataUpdate(e.detail.data, e.detail.tab);
            });
        },

        handleDataUpdate(data, tab) {
            this.isLoading = false;

            switch(tab) {
                case 'general':
                    this.renderGeneralTable(data);
                    break;
                case 'group':
                    this.renderGroupChart(data);
                    this.renderGroupTable(data);
                    break;
                case 'instructor':
                    this.renderInstructorChart(data);
                    this.renderInstructorTable(data);
                    break;
                case 'period':
                    this.renderPeriodChart(data);
                    this.renderPeriodTable(data);
                    break;
            }
        },

        renderGeneralTable(data) {
            // This would render the general table - implementation depends on data structure
            // For now, using placeholder
            this.generalTableHtml = `<div class="p-4">Table content</div>`;
        },

        renderGroupChart(data) {
            const ctx = document.getElementById('groupChart');
            if (!ctx) return;

            if (this.groupChart) this.groupChart.destroy();

            this.groupChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(row => row.name),
                    datasets: [{
                        label: 'Progresso Médio (%)',
                        data: data.map(row => row.avg_progress),
                        backgroundColor: data.map(row =>
                            row.avg_progress >= 75 ? '#10b981' :
                            row.avg_progress >= 50 ? '#3b82f6' : '#fbbf24'
                        ),
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                }
            });
        },

        renderGroupTable(data) {
            this.groupTableHtml = `
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400">Grupo</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400">Total</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400">Concluídos</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400">Pendentes</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400">% Médio</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        ${data.map(row => `
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">${row.name}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">${row.total}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">${row.completed}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">${row.pending}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-800">${row.avg_progress}%</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
        },

        renderInstructorChart(data) {
            const ctx = document.getElementById('instructorChart');
            if (!ctx) return;

            if (this.instructorChart) this.instructorChart.destroy();

            this.instructorChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(row => row.name),
                    datasets: [{
                        label: 'Progresso Médio (%)',
                        data: data.map(row => row.avg_progress),
                        backgroundColor: '#3b82f6',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                }
            });
        },

        renderInstructorTable(data) {
            this.instructorTableHtml = `
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400">Instrutor</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400">Total Alunos</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400">Concluídos</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400">% Médio</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        ${data.map(row => `
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">${row.name}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">${row.total}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">${row.completed}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-800">${row.avg_progress}%</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
        },

        renderPeriodChart(data) {
            const ctx = document.getElementById('periodChart');
            if (!ctx) return;

            if (this.periodChart) this.periodChart.destroy();

            this.periodChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(row => new Date(row.period).toLocaleDateString('pt-BR')),
                    datasets: [{
                        label: 'Conclusões',
                        data: data.map(row => row.completed),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                }
            });
        },

        renderPeriodTable(data) {
            this.periodTableHtml = `
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400">Período</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400">Conclusões</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400">% Crescimento</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        ${data.map((row, idx) => `
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">${new Date(row.period).toLocaleDateString('pt-BR')}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">${row.completed}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-800">
                                    ${row.growth_percent > 0 ? '↑' : row.growth_percent < 0 ? '↓' : '—'} ${row.growth_percent}%
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
        }
    };
};
