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
        toasts: @js($toasts).map((t, i) => ({ ...t, id: i, visible: true })),
        init() {
            this.toasts.forEach((t, i) => {
                setTimeout(() => this.dismiss(i), 4500)
            })
        },
        dismiss(i) {
            this.toasts[i].visible = false
        }
    }"
    class="fixed top-4 right-4 z-[9999] flex flex-col gap-2.5 w-80 max-w-[calc(100vw-2rem)] pointer-events-none"
    style="position: fixed;"
>
    <template x-for="(t, i) in toasts" :key="t.id">
        <div
            x-show="t.visible"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-8 scale-95"
            x-transition:enter-end="opacity-100 translate-x-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0 scale-100"
            x-transition:leave-end="opacity-0 translate-x-8 scale-95"
            :class="{
                'border-green-200':  t.type === 'success',
                'border-red-200':    t.type === 'error',
                'border-yellow-200': t.type === 'warning',
                'border-blue-200':   t.type === 'info',
            }"
            class="bg-white border rounded-xl shadow-xl overflow-hidden pointer-events-auto"
        >
            {{-- Colored top bar --}}
            <div
                :class="{
                    'bg-green-500':  t.type === 'success',
                    'bg-red-500':    t.type === 'error',
                    'bg-yellow-500': t.type === 'warning',
                    'bg-primary':    t.type === 'info',
                }"
                class="h-1 w-full"
            ></div>

            <div class="flex items-start gap-3 px-4 py-3">
                {{-- Icon circle --}}
                <div
                    :class="{
                        'bg-green-100 text-green-600':  t.type === 'success',
                        'bg-red-100 text-red-600':      t.type === 'error',
                        'bg-yellow-100 text-yellow-600': t.type === 'warning',
                        'bg-blue-100 text-primary':      t.type === 'info',
                    }"
                    class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5"
                >
                    <svg x-show="t.type === 'success'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    <svg x-show="t.type === 'error'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <svg x-show="t.type === 'warning'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <svg x-show="t.type === 'info'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>

                {{-- Message --}}
                <div class="flex-1 min-w-0">
                    <p
                        :class="{
                            'text-green-800':  t.type === 'success',
                            'text-red-800':    t.type === 'error',
                            'text-yellow-800': t.type === 'warning',
                            'text-gray-800':   t.type === 'info',
                        }"
                        class="text-sm font-medium leading-snug"
                        x-text="t.msg"
                    ></p>
                </div>

                {{-- Dismiss button --}}
                <button
                    @click="dismiss(i)"
                    class="flex-shrink-0 w-6 h-6 rounded-md flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </template>
</div>
@endif
