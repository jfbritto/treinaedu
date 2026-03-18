@php
    $toasts = [];
    if (session('success')) $toasts[] = ['type' => 'success', 'msg' => session('success')];
    if (session('error'))   $toasts[] = ['type' => 'error',   'msg' => session('error')];
    if (session('warning')) $toasts[] = ['type' => 'warning', 'msg' => session('warning')];
    if (session('info'))    $toasts[] = ['type' => 'info',    'msg' => session('info')];
@endphp

@if(count($toasts))
<div
    x-data="{
        toasts: @js($toasts),
        init() { setTimeout(() => this.toasts = [], 4000) }
    }"
    class="fixed top-4 right-4 z-[100] flex flex-col gap-2 w-80 max-w-[calc(100vw-2rem)]"
>
    <template x-for="(t, i) in toasts" :key="i">
        <div
            x-show="toasts.length > 0"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-4"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 translate-x-4"
            :class="{
                'bg-green-50 border-green-300 text-green-800': t.type === 'success',
                'bg-red-50 border-red-300 text-red-800':     t.type === 'error',
                'bg-yellow-50 border-yellow-300 text-yellow-800': t.type === 'warning',
                'bg-blue-50 border-blue-300 text-blue-800':  t.type === 'info',
            }"
            class="flex items-start gap-3 border rounded-xl px-4 py-3 shadow-lg text-sm"
        >
            {{-- Ícone --}}
            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <template x-if="t.type === 'success'">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </template>
                <template x-if="t.type === 'error'">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </template>
                <template x-if="t.type === 'warning'">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </template>
                <template x-if="t.type === 'info'">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </template>
            </svg>
            <span x-text="t.msg" class="flex-1"></span>
            <button @click="toasts.splice(i, 1)" class="flex-shrink-0 opacity-50 hover:opacity-100 transition ml-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </template>
</div>
@endif
