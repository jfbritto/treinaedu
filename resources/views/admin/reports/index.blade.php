<x-layout.app title="Relatórios">

	<div class="space-y-6">

		{{-- Header with Title and Export Buttons --}}
		<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
			<div>
				<h1 class="text-3xl font-bold text-gray-800">Relatórios de Treinamentos</h1>
				<p class="text-sm text-gray-500 mt-1">Acompanhe o progresso e conclusões da equipe em tempo real</p>
			</div>
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

		{{-- KPI Cards --}}
		@php
			$totalViews     = $views->total();
			$completedCount = $views->getCollection()->whereNotNull('completed_at')->count();
			$pendingCount   = $views->getCollection()->whereNull('completed_at')->count();
			$avgProgress    = $views->getCollection()->avg('progress_percent') ?? 0;
		@endphp
		<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
			{{-- Total Views KPI --}}
			<x-reports.kpi-card
				:label="'Registros Totais'"
				:value="$totalViews"
				color="primary"
				icon="chart-bars">
			</x-reports.kpi-card>

			{{-- Completed Count KPI --}}
			<x-reports.kpi-card
				:label="'Concluídos'"
				:value="$completedCount"
				color="green"
				icon="check-circle">
			</x-reports.kpi-card>

			{{-- Pending Count KPI --}}
			<x-reports.kpi-card
				:label="'Pendentes'"
				:value="$pendingCount"
				color="yellow"
				icon="clock">
			</x-reports.kpi-card>

			{{-- Average Progress KPI --}}
			<x-reports.kpi-card
				:label="'Progresso Médio'"
				:value="round($avgProgress) . '%'"
				color="primary"
				icon="trending-up">
			</x-reports.kpi-card>
		</div>

		{{-- Filter Section (Sticky) --}}
		<x-reports.filter-sticky :trainings="$trainings" :groups="$groups">
		</x-reports.filter-sticky>

		{{-- Tabs Section --}}
		<div x-data="reportsTabs()" class="bg-white rounded-xl shadow-sm overflow-hidden">
			<div class="border-b border-gray-200">
				<div class="flex">
					<button @click="activeTab = 'table'"
						:class="activeTab === 'table' ? 'border-b-2 border-primary text-primary' : 'text-gray-600 hover:text-gray-800'"
						class="px-6 py-4 font-semibold text-sm transition">
						Tabela
					</button>
					<button @click="activeTab = 'charts'"
						:class="activeTab === 'charts' ? 'border-b-2 border-primary text-primary' : 'text-gray-600 hover:text-gray-800'"
						class="px-6 py-4 font-semibold text-sm transition">
						Gráficos
					</button>
					<button @click="activeTab = 'summary'"
						:class="activeTab === 'summary' ? 'border-b-2 border-primary text-primary' : 'text-gray-600 hover:text-gray-800'"
						class="px-6 py-4 font-semibold text-sm transition">
						Resumo
					</button>
				</div>
			</div>

			{{-- Tab Content: Table --}}
			<x-reports.tab-panel :active="'table'" x-show="activeTab === 'table'">
				<div class="overflow-x-auto">
					@if($views->isEmpty())
						<div class="p-12 text-center">
							<svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
							</svg>
							<p class="text-gray-400 text-sm">Nenhum registro encontrado com os filtros aplicados.</p>
						</div>
					@else
						<table class="w-full">
							<thead>
								<tr class="border-b border-gray-100 bg-gray-50">
									<th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Funcionário</th>
									<th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Treinamento</th>
									<th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Progresso</th>
									<th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
									<th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Data de Conclusão</th>
								</tr>
							</thead>
							<tbody class="divide-y divide-gray-50">
								@foreach($views as $view)
									<tr class="hover:bg-gray-50 transition">
										<td class="px-6 py-4">
											<div class="flex items-center gap-3">
												<div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
													<span class="text-xs font-bold text-primary">
														{{ strtoupper(substr($view->user->name ?? '?', 0, 2)) }}
													</span>
												</div>
												<div>
													<p class="text-sm font-semibold text-gray-800">{{ $view->user->name ?? 'N/A' }}</p>
													<p class="text-xs text-gray-400 hidden md:block lg:hidden">{{ $view->training->title ?? 'N/A' }}</p>
												</div>
											</div>
										</td>
										<td class="px-6 py-4 hidden md:table-cell">
											<p class="text-sm text-gray-700">{{ $view->training->title ?? 'N/A' }}</p>
										</td>
										<td class="px-6 py-4 hidden lg:table-cell">
											<div class="flex items-center gap-2 w-36">
												<div class="flex-1 bg-gray-100 rounded-full h-2">
													<div class="h-2 rounded-full {{ $view->progress_percent >= 100 ? 'bg-green-500' : ($view->progress_percent >= 50 ? 'bg-primary' : 'bg-yellow-400') }}"
														style="width: {{ $view->progress_percent }}%"></div>
												</div>
												<span class="text-xs font-medium text-gray-600 w-8 text-right flex-shrink-0">{{ $view->progress_percent }}%</span>
											</div>
										</td>
										<td class="px-6 py-4">
											@if($view->completed_at)
												<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
													<span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
													Concluído
												</span>
											@else
												<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
													<span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
													Pendente
												</span>
											@endif
										</td>
										<td class="px-6 py-4 hidden sm:table-cell">
											<span class="text-sm text-gray-500">{{ $view->completed_at?->format('d/m/Y') ?? '—' }}</span>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
						@if($views->hasPages())
							<div class="px-6 py-4 border-t border-gray-100">
								{{ $views->appends(request()->query())->links() }}
							</div>
						@endif
					@endif
				</div>
			</x-reports.tab-panel>

			{{-- Tab Content: Charts --}}
			<x-reports.tab-panel :active="'charts'" x-show="activeTab === 'charts'">
				<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
					<x-reports.chart-container title="Taxa de Conclusão">
						<canvas id="completionChart"></canvas>
					</x-reports.chart-container>
					<x-reports.chart-container title="Distribuição de Progresso">
						<canvas id="progressChart"></canvas>
					</x-reports.chart-container>
				</div>
			</x-reports.tab-panel>

			{{-- Tab Content: Summary --}}
			<x-reports.tab-panel :active="'summary'" x-show="activeTab === 'summary'">
				<div class="p-6 space-y-4">
					<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
						<div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4">
							<p class="text-sm text-green-600 font-semibold">Taxa de Conclusão</p>
							<p class="text-3xl font-bold text-green-700 mt-2">{{ $totalViews > 0 ? round(($completedCount / $totalViews) * 100) : 0 }}%</p>
						</div>
						<div class="bg-gradient-to-br from-primary/20 to-primary/10 rounded-lg p-4">
							<p class="text-sm text-primary font-semibold">Progresso Médio</p>
							<p class="text-3xl font-bold text-primary mt-2">{{ round($avgProgress) }}%</p>
						</div>
					</div>
				</div>
			</x-reports.tab-panel>
		</div>

	</div>

	@push('scripts')
		<script>
			function reportsTabs() {
				return {
					activeTab: 'table',
				};
			}

			function filterForm() {
				return {
					training_id: '{{ request("training_id") }}',
					group_id: '{{ request("group_id") }}',
					status: '{{ request("status") }}',
					date_from: '{{ request("date_from") }}',
					date_to: '{{ request("date_to") }}',
					submitForm() {
						document.querySelector('form[method="GET"]').submit();
					},
					clearFilters() {
						window.location.href = '{{ route("reports.index") }}';
					},
				};
			}

			function reportsContent() {
				return {
					init() {
						console.log('Reports page loaded');
					},
				};
			}
		</script>
		<script src="{{ asset('js/pages/reports.js') }}" defer></script>
	@endpush

</x-layout.app>
