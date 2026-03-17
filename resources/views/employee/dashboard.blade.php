<x-layout.app title="Meus Treinamentos">

    {{-- Pending --}}
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Treinamentos Pendentes</h2>
        @if($pending->isEmpty())
            <p class="text-gray-400 text-sm">Nenhum treinamento pendente.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($pending as $training)
                    <a href="{{ route('employee.trainings.show', $training) }}" class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition border-l-4 border-yellow-400">
                        <h3 class="font-medium text-gray-800">{{ $training->title }}</h3>
                        @php $view = $training->views->first(); @endphp
                        @if($view && $view->progress_percent > 0)
                            <div class="mt-2">
                                <div class="text-xs text-gray-500 mb-1">{{ $view->progress_percent }}% concluído</div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-yellow-400 h-1.5 rounded-full" style="width: {{ $view->progress_percent }}%"></div>
                                </div>
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Completed --}}
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Treinamentos Concluídos</h2>
        @if($completed->isEmpty())
            <p class="text-gray-400 text-sm">Nenhum treinamento concluído ainda.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($completed as $training)
                    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-green-400">
                        <h3 class="font-medium text-gray-800">{{ $training->title }}</h3>
                        <p class="text-xs text-green-600 mt-1">Concluído</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Certificates --}}
    <div>
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Meus Certificados</h2>
        @if($certificates->isEmpty())
            <p class="text-gray-400 text-sm">Nenhum certificado emitido ainda.</p>
        @else
            <div class="space-y-2">
                @foreach($certificates as $cert)
                    <div class="bg-white rounded-xl shadow-sm p-4 flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-800">{{ $cert->training->title }}</p>
                            <p class="text-xs text-gray-400">Emitido em {{ $cert->generated_at->format('d/m/Y') }}</p>
                        </div>
                        <a href="{{ route('employee.certificates.download', $cert) }}" class="text-blue-600 hover:underline text-sm">Baixar</a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</x-layout.app>
