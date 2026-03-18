<x-layout.app title="Configurações da Empresa">

    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-lg font-bold text-gray-800">Configurações da Empresa</h2>
        <p class="text-xs text-gray-400 mt-0.5">Personalize a identidade visual da plataforma</p>
    </div>

    @if (session('success'))
        <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Formulário (2/3) --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Logo --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">Logo da Empresa</h3>
                        <p class="text-xs text-gray-400">Aparece no topo do menu lateral</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('company.settings.update') }}" enctype="multipart/form-data" id="settings-form" class="space-y-5">
                    @csrf
                    @method('PUT')

                    @if ($company->logo_path)
                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100">
                            <img src="{{ asset($company->logo_path) }}" alt="Logo atual"
                                 class="h-14 w-auto object-contain rounded-lg border border-gray-200 bg-white p-1">
                            <div>
                                <p class="text-sm font-medium text-gray-700">Logo atual</p>
                                <p class="text-xs text-gray-400 mt-0.5">Envie uma nova imagem para substituir</p>
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ $company->logo_path ? 'Substituir logo' : 'Enviar logo' }}
                        </label>
                        <input type="file" name="logo" accept="image/jpg,image/jpeg,image/png,image/svg+xml"
                               class="block w-full text-sm text-gray-600
                                      file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                                      file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700
                                      hover:file:bg-blue-100 transition">
                        @error('logo')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-400 mt-1.5">Formatos aceitos: JPG, PNG, SVG. Máx. 2 MB.</p>
                    </div>
                </form>
            </div>

            {{-- Cores --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">Identidade Visual</h3>
                        <p class="text-xs text-gray-400">Cores do menu e elementos da interface</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6" x-data="{
                    primary: '{{ old('primary_color', $company->primary_color ?? '#2563eb') }}',
                    secondary: '{{ old('secondary_color', $company->secondary_color ?? '#1e40af') }}'
                }">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cor Primária</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="primary_color" form="settings-form"
                                   x-model="primary"
                                   class="h-10 w-14 cursor-pointer rounded-lg border border-gray-200 p-1">
                            <div>
                                <p class="text-sm font-mono text-gray-700" x-text="primary"></p>
                                <p class="text-xs text-gray-400">Cor do menu lateral</p>
                            </div>
                        </div>
                        @error('primary_color')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cor Secundária</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="secondary_color" form="settings-form"
                                   x-model="secondary"
                                   class="h-10 w-14 cursor-pointer rounded-lg border border-gray-200 p-1">
                            <div>
                                <p class="text-sm font-mono text-gray-700" x-text="secondary"></p>
                                <p class="text-xs text-gray-400">Cor de destaque e hover</p>
                            </div>
                        </div>
                        @error('secondary_color')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Salvar --}}
            <div class="flex justify-end">
                <button type="submit" form="settings-form"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Salvar Configurações
                </button>
            </div>
        </div>

        {{-- Preview (1/3) --}}
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Pré-visualização</h3>
                <div class="rounded-xl overflow-hidden border border-gray-200"
                     x-data="{
                         primary: '{{ old('primary_color', $company->primary_color ?? '#2563eb') }}',
                         secondary: '{{ old('secondary_color', $company->secondary_color ?? '#1e40af') }}'
                     }">
                    {{-- Mini sidebar preview --}}
                    <div class="flex h-40">
                        <div class="w-16 flex flex-col items-center py-3 gap-3" :style="'background-color: ' + secondary">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center" :style="'background-color: ' + primary">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                            </div>
                            <div class="w-6 h-1 rounded-full bg-white opacity-30"></div>
                            <div class="w-6 h-1 rounded-full bg-white opacity-30"></div>
                            <div class="w-6 h-1 rounded-full bg-white opacity-30"></div>
                        </div>
                        <div class="flex-1 bg-gray-50 p-3">
                            <div class="h-4 w-20 rounded bg-gray-200 mb-2"></div>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="h-8 rounded-lg bg-white border border-gray-100"></div>
                                <div class="h-8 rounded-lg bg-white border border-gray-100"></div>
                                <div class="h-8 rounded-lg bg-white border border-gray-100"></div>
                                <div class="h-8 rounded-lg" :style="'background-color: ' + primary + '20'"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Color swatches --}}
                    <div class="border-t border-gray-100 p-3 flex gap-2 bg-white">
                        <div class="flex items-center gap-2 flex-1">
                            <div class="w-6 h-6 rounded-full border border-gray-200" :style="'background-color: ' + primary"></div>
                            <span class="text-xs text-gray-500">Primária</span>
                        </div>
                        <div class="flex items-center gap-2 flex-1">
                            <div class="w-6 h-6 rounded-full border border-gray-200" :style="'background-color: ' + secondary"></div>
                            <span class="text-xs text-gray-500">Secundária</span>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-3">As cores são aplicadas ao recarregar a página após salvar.</p>
            </div>

            {{-- Dica --}}
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex gap-3">
                <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-sm font-medium text-blue-800 mb-0.5">Dica</p>
                    <p class="text-xs text-blue-600">Use cores com bom contraste para garantir legibilidade. O logo é exibido no topo do menu lateral para todos os usuários da empresa.</p>
                </div>
            </div>
        </div>
    </div>

</x-layout.app>
