@props(['name', 'label', 'type' => 'text', 'value' => '', 'required' => false])

<div class="space-y-1">
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">
        {{ $label }}{{ $required ? ' *' : '' }}
    </label>
    <input
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary text-sm px-3 py-2 border']) }}
    >
    @error($name)
        <p class="text-red-500 text-xs">{{ $message }}</p>
    @enderror
</div>
