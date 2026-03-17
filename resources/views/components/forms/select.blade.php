@props(['name', 'label', 'options' => [], 'selected' => null, 'required' => false])

<div class="space-y-1">
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">
        {{ $label }}{{ $required ? ' *' : '' }}
    </label>
    <select
        id="{{ $name }}"
        name="{{ $name }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-3 py-2 border']) }}
    >
        <option value="">Selecione...</option>
        @foreach($options as $optValue => $optLabel)
            <option value="{{ $optValue }}" {{ old($name, $selected) == $optValue ? 'selected' : '' }}>
                {{ $optLabel }}
            </option>
        @endforeach
    </select>
    @error($name)
        <p class="text-red-500 text-xs">{{ $message }}</p>
    @enderror
</div>
