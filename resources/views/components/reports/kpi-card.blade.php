@props([
    'key',
    'label',
    'color' => 'primary',
    'sublabel' => null,
])

@php
    $colorMap = [
        'blue' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600'],
        'green' => ['bg' => 'bg-green-50', 'text' => 'text-green-600'],
        'amber' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600'],
        'purple' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-600'],
        'primary' => ['bg' => 'bg-primary/10', 'text' => 'text-primary'],
    ];
    $c = $colorMap[$color] ?? $colorMap['primary'];
@endphp

<div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
    <div class="flex items-center justify-between mb-3">
        <div class="w-10 h-10 rounded-xl {{ $c['bg'] }} flex items-center justify-center flex-shrink-0 {{ $c['text'] }}">
            {{ $slot }}
        </div>
    </div>
    <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">{{ $label }}</p>
    <p class="text-2xl font-bold text-gray-800 mt-1" x-text="stats?.{{ $key }}">-</p>
    @if($sublabel)
        <p class="text-xs text-gray-400 mt-0.5">{{ $sublabel }}</p>
    @endif
</div>
