{{-- resources/views/components/reports/tab-panel.blade.php --}}
<div x-show="activeTab === '{{ $name }}'"
     x-transition
     class="min-h-96">
    {{ $slot }}
</div>
