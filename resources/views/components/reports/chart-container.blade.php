{{-- resources/views/components/reports/chart-container.blade.php --}}
<div class="bg-white rounded-xl shadow-sm p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ $title }}</h3>
    <div class="relative" style="height: {{ $height ?? '300px' }}">
        <canvas id="{{ $chartId }}"></canvas>
    </div>
</div>
