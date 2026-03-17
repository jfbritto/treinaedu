@props(['title', 'value', 'icon' => null, 'color' => 'blue'])

<div class="bg-white rounded-xl shadow-sm p-6 flex items-center gap-4">
    @if($icon)
        <div class="p-3 rounded-full bg-{{ $color }}-100">
            <span class="text-{{ $color }}-600 text-xl">{{ $icon }}</span>
        </div>
    @endif
    <div>
        <p class="text-sm text-gray-500">{{ $title }}</p>
        <p class="text-2xl font-bold text-gray-800">{{ $value }}</p>
    </div>
</div>
