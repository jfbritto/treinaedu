@props(['id', 'title'])

<div x-data="{ open: false }" id="{{ $id }}">
    <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak>
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4">
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-gray-800">{{ $title }}</h3>
                <button @click="open = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <div class="p-6">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
