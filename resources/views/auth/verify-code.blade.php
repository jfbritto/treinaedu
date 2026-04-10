<x-layout.guest>

    <div class="text-center mb-8">
        <div class="w-16 h-16 rounded-2xl mx-auto mb-5 flex items-center justify-center" style="background: linear-gradient(135deg, #4f46e5, #3730a3)">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Verifique seu e-mail</h1>
        <p class="mt-2 text-sm text-gray-500">Enviamos um código de 6 dígitos para</p>
        <p class="text-sm font-semibold text-gray-800">{{ $email }}</p>
    </div>

    @if (session('status'))
        <div class="mb-5 flex items-center gap-2 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('status') }}
        </div>
    @endif

    @error('code')
        <div class="mb-5 flex items-center gap-2 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            {{ $message }}
        </div>
    @enderror

    <form id="verify-form" method="POST" action="{{ route('verification.code.verify') }}">
        @csrf
        <input type="hidden" id="code-hidden" name="code" value="">
    </form>

    <div x-data="{
        digits: '',
        submitting: false,
        onInput(el) {
            this.digits = el.value.replace(/\D/g, '').substring(0, 6);
            el.value = this.digits;
            if (this.digits.length === 6) {
                this.doSubmit();
            }
        },
        doSubmit() {
            if (this.submitting) return;
            this.submitting = true;
            document.getElementById('code-hidden').value = this.digits;
            document.getElementById('verify-form').submit();
        }
    }">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 text-center mb-2">Código de verificação</label>
            <input
                type="text"
                x-ref="input"
                :value="digits"
                @input="onInput($event.target)"
                @paste.prevent="
                    let text = ($event.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').substring(0, 6);
                    digits = text;
                    $refs.input.value = text;
                    if (text.length === 6) doSubmit();
                "
                :disabled="submitting"
                autofocus
                autocomplete="one-time-code"
                inputmode="numeric"
                maxlength="6"
                placeholder="000000"
                class="w-full py-4 text-center text-2xl font-bold tracking-[0.5em] rounded-lg border border-gray-300 bg-white text-gray-900 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition disabled:opacity-50"
            >
        </div>

        {{-- Status --}}
        <div class="text-center mb-5 h-6">
            <p x-show="submitting" class="flex items-center justify-center gap-2 text-indigo-600 text-sm font-semibold">
                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Verificando seu código...
            </p>
            <p x-show="!submitting && digits.length === 0" class="text-sm text-gray-400">
                Cole ou digite o código do e-mail
            </p>
            <p x-show="!submitting && digits.length > 0 && digits.length < 6" class="text-sm text-gray-500">
                Faltam <span class="font-semibold" x-text="6 - digits.length"></span> dígito<span x-show="(6 - digits.length) > 1">s</span>
            </p>
        </div>

        {{-- Botão fallback --}}
        <div x-show="!submitting && digits.length === 6" class="mb-4">
            <button
                type="button"
                @click="doSubmit()"
                class="w-full py-3 px-4 rounded-lg text-sm font-semibold text-white transition-all shadow-sm hover:shadow-md"
                style="background: linear-gradient(135deg, #4f46e5, #3730a3)">
                Verificar e-mail
            </button>
        </div>
    </div>

    <div class="mt-4 text-center">
        <p class="text-xs text-gray-400 mb-2">Não recebeu o código?</p>
        <form method="POST" action="{{ route('verification.code.resend') }}" class="inline">
            @csrf
            <button type="submit" class="text-sm font-medium text-indigo-600 hover:text-indigo-700 transition">
                Reenviar código
            </button>
        </form>
        <p class="mt-2 text-xs text-gray-400">O código expira em 10 minutos.</p>
    </div>

</x-layout.guest>
