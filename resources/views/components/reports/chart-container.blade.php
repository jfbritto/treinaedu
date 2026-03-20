{{-- resources/views/components/reports/chart-container.blade.php --}}
<div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-gray-100">
    <div class="mb-6">
        <div class="flex items-start justify-between mb-2">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">{{ $title }}</h3>
                @if($description ?? false)
                    <p class="text-sm text-gray-600 mt-2 leading-relaxed">{{ $description }}</p>
                @endif
            </div>
        </div>
    </div>
    <div class="relative" style="height: {{ $height ?? '300px' }}">
        <canvas id="{{ $chartId }}"></canvas>
    </div>
</div>
