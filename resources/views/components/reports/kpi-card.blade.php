{{-- resources/views/components/reports/kpi-card.blade.php --}}
<div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
    <div class="flex items-start justify-between gap-3">
        <div class="flex-1">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">{{ $label }}</p>
            <p class="text-3xl font-bold text-gray-900" x-text="stats?.{{ $key }}">-</p>
        </div>
        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0 text-blue-600">
            {{ $slot }}
        </div>
    </div>
</div>
