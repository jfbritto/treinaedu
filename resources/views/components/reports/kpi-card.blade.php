{{-- resources/views/components/reports/kpi-card.blade.php --}}
<div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-4">
    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
        {{ $slot }}
    </div>
    <div>
        <p class="text-2xl font-bold text-gray-800" x-text="$stats?.{{ $key }}"></p>
        <p class="text-xs text-gray-400">{{ $label }}</p>
    </div>
</div>
