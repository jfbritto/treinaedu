<x-layout.app title="Configurações da Empresa">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>

    <p class="text-sm text-gray-500 mb-6">Personalize a identidade visual da plataforma</p>

    <form method="POST" action="{{ route('company.settings.update') }}" enctype="multipart/form-data" id="settings-form">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Formulário (2/3) --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Nome da Empresa --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-800">Nome da Empresa</h3>
                            <p class="text-xs text-gray-400">Como aparece no menu e nas telas do sistema</p>
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nome exibido no sistema <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name"
                            value="{{ old('name', $company->name) }}" required
                            placeholder="Ex: Minha Empresa Ltda"
                            class="w-full rounded-lg border @error('name') border-red-400 @else border-gray-300 @enderror px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Logo com Cropper --}}
                <div class="bg-white rounded-xl shadow-sm p-6" x-data="logoUploader()">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-800">Logo</h3>
                            <p class="text-xs text-gray-400">Imagem exibida no topo do menu lateral</p>
                        </div>
                    </div>

                    @if($company->logo_path)
                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100 mb-4">
                            <img src="{{ Storage::disk('public')->url($company->logo_path) }}" alt="Logo atual"
                                 class="h-12 w-auto max-w-32 object-contain rounded-lg border border-gray-200 bg-white p-1.5">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-700">Logo atual</p>
                                <p class="text-xs text-gray-400">Envie uma nova imagem para substituir</p>
                            </div>
                            <label class="flex items-center gap-1.5 text-xs text-red-500 hover:text-red-700 cursor-pointer transition">
                                <input type="checkbox" name="remove_logo" value="1" class="rounded border-gray-300 text-red-500 focus:ring-red-400">
                                Remover logo
                            </label>
                        </div>
                    @endif

                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-blue-300 transition cursor-pointer"
                         @click="$refs.fileInput.click()">
                        <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        <p class="text-sm text-gray-500 font-medium">Clique para escolher ou arraste um arquivo</p>
                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, SVG · Máx. 2 MB</p>
                    </div>

                    <input type="file" x-ref="fileInput" name="logo"
                           accept="image/jpg,image/jpeg,image/png,image/svg+xml"
                           class="hidden" @change="onFileSelected($event)">

                    @error('logo') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror

                    {{-- Cropper --}}
                    <div x-show="showCropper" x-cloak class="mt-5 space-y-3">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-gray-700">Ajustar recorte</p>
                            <div class="flex gap-2">
                                <button type="button" @click="rotateCrop(-90)"
                                    class="p-1.5 rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-500 transition" title="Girar esquerda">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                </button>
                                <button type="button" @click="rotateCrop(90)"
                                    class="p-1.5 rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-500 transition" title="Girar direita">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 4v5h-.582M4.064 11A8.001 8.001 0 0119.418 9M19.418 9H15m-11 11v-5h.581m0 0a8.003 8.003 0 0015.357-2M4.581 15H9"/></svg>
                                </button>
                                <button type="button" @click="cancelCrop()"
                                    class="p-1.5 rounded-lg border border-gray-200 hover:bg-gray-50 text-red-400 transition" title="Cancelar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>
                        <div class="rounded-xl overflow-hidden bg-gray-50 border border-gray-100" style="max-height:300px">
                            <img x-ref="cropImage" style="max-width:100%;display:block">
                        </div>
                        <button type="button" @click="applyCrop()"
                            class="w-full flex items-center justify-center gap-2 bg-primary hover:bg-secondary text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Usar imagem recortada
                        </button>
                    </div>

                    {{-- Preview após recorte --}}
                    <div x-show="croppedPreview" x-cloak class="mt-4 flex items-center gap-3 p-3 bg-green-50 border border-green-200 rounded-xl">
                        <img :src="croppedPreview" class="h-10 w-auto object-contain rounded-lg border border-green-200 bg-white p-1">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-green-700">Imagem recortada pronta</p>
                            <p class="text-xs text-green-600">Salve as configurações para aplicar</p>
                        </div>
                        <button type="button" @click="cancelCrop()" class="text-xs text-green-600 hover:text-green-800">Refazer</button>
                    </div>

                    {{-- Cores extraídas da logo --}}
                    <div x-show="extractedColors.length > 0" x-cloak class="mt-4 p-4 bg-primary/5 border border-primary/20 rounded-xl">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                            </svg>
                            <p class="text-sm font-semibold text-primary">Cores detectadas na logo</p>
                        </div>
                        <div class="flex flex-wrap gap-2 mb-3">
                            <template x-for="(color, index) in extractedColors" :key="index">
                                <button type="button"
                                    @click="selectedColorIndex = index"
                                    class="w-10 h-10 rounded-lg border-2 transition-transform hover:scale-110 cursor-pointer"
                                    :class="selectedColorIndex === index ? 'border-primary ring-2 ring-primary/30' : 'border-gray-200'"
                                    :style="'background-color:' + color">
                                </button>
                            </template>
                        </div>
                        <div x-show="selectedColorIndex !== null" class="flex gap-2">
                            <button type="button" @click="applyColorAs('primary')"
                                class="flex-1 text-xs font-medium px-3 py-2 rounded-lg bg-white border border-primary/20 text-primary hover:bg-primary/10 transition">
                                Usar como Cor do Menu
                            </button>
                            <button type="button" @click="applyColorAs('secondary')"
                                class="flex-1 text-xs font-medium px-3 py-2 rounded-lg bg-white border border-primary/20 text-primary hover:bg-primary/10 transition">
                                Usar como Cor de Destaque
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Cores --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-800">Cores da Marca</h3>
                            <p class="text-xs text-gray-400">Personalize as cores que representam sua empresa no sistema</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6" x-data="{
                        primary: '{{ old('primary_color', $company->primary_color ?? '#4f46e5') }}',
                        secondary: '{{ old('secondary_color', $company->secondary_color ?? '#3730a3') }}'
                    }">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Menu lateral</label>
                            <div class="flex items-center gap-3">
                                <input type="color" name="primary_color"
                                       x-model="primary"
                                       @input="window.dispatchEvent(new CustomEvent('preview-color', {detail: {primary: $event.target.value}}))"
                                       class="h-10 w-14 cursor-pointer rounded-lg border border-gray-200 p-1">
                                <div>
                                    <p class="text-sm font-mono font-medium text-gray-700" x-text="primary"></p>
                                    <p class="text-xs text-gray-400">Fundo do menu</p>
                                </div>
                            </div>
                            @error('primary_color') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Destaque</label>
                            <div class="flex items-center gap-3">
                                <input type="color" name="secondary_color"
                                       x-model="secondary"
                                       @input="window.dispatchEvent(new CustomEvent('preview-color', {detail: {secondary: $event.target.value}}))"
                                       class="h-10 w-14 cursor-pointer rounded-lg border border-gray-200 p-1">
                                <div>
                                    <p class="text-sm font-mono font-medium text-gray-700" x-text="secondary"></p>
                                    <p class="text-xs text-gray-400">Botões e ícones</p>
                                </div>
                            </div>
                            @error('secondary_color') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Restaurar cores padrão --}}
                        <div class="sm:col-span-2 pt-3 border-t border-gray-100">
                            <button type="button"
                                @click="
                                    primary = '#4f46e5';
                                    secondary = '#3730a3';
                                    document.querySelector('input[name=primary_color]').value = '#4f46e5';
                                    document.querySelector('input[name=secondary_color]').value = '#3730a3';
                                    window.dispatchEvent(new CustomEvent('preview-color', {detail: {primary: '#4f46e5', secondary: '#3730a3'}}));
                                "
                                class="inline-flex items-center gap-2 text-xs font-medium text-gray-500 hover:text-gray-700 transition px-3 py-2 rounded-lg border border-gray-200 hover:bg-gray-50">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Restaurar cores padrão do sistema
                            </button>
                            <p class="text-xs text-gray-400 mt-1">Menu: #4f46e5 (indigo) · Destaque: #3730a3 (indigo escuro)</p>
                        </div>
                    </div>
                </div>

                {{-- Responsável pelo Certificado --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-800">Responsável pelo Certificado</h3>
                            <p class="text-xs text-gray-400">Dados exibidos no certificado com assinatura</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="block text-xs font-medium text-gray-600">Nome completo</label>
                                <input type="text" name="cert_signer_name" value="{{ old('cert_signer_name', $company->cert_signer_name) }}"
                                       placeholder="Ex: Maria da Silva"
                                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-xs font-medium text-gray-600">Cargo / Função</label>
                                <input type="text" name="cert_signer_role" value="{{ old('cert_signer_role', $company->cert_signer_role) }}"
                                       placeholder="Ex: Enfermeira Chefe"
                                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-xs font-medium text-gray-600">Registro profissional <span class="text-gray-400 font-normal">(opcional)</span></label>
                            <input type="text" name="cert_signer_registry" value="{{ old('cert_signer_registry', $company->cert_signer_registry) }}"
                                   placeholder="Ex: COREN-ES 123456"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            <p class="text-xs text-gray-400">COREN, CRM, CREA, OAB, ou qualquer registro que valide a certificação.</p>
                        </div>

                        <div class="space-y-2" x-data="signaturePad()">
                            <label class="block text-xs font-medium text-gray-600">Assinatura</label>

                            @if($company->cert_signer_signature_path)
                                <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg border border-gray-200" x-show="!drawnDataUrl">
                                    <img src="{{ Storage::url($company->cert_signer_signature_path) }}" alt="Assinatura" class="h-12 object-contain">
                                    <div class="flex-1">
                                        <p class="text-xs text-gray-500">Assinatura atual</p>
                                    </div>
                                    <label class="flex items-center gap-1.5 text-xs text-red-500 cursor-pointer hover:text-red-700">
                                        <input type="checkbox" name="remove_signature" value="1" class="rounded border-gray-300 text-red-500 focus:ring-red-500">
                                        Remover
                                    </label>
                                </div>
                            @endif

                            {{-- Tabs --}}
                            <div class="flex gap-1 bg-gray-100 p-1 rounded-lg">
                                <button type="button" @click="mode = 'draw'" :class="mode === 'draw' ? 'bg-white shadow-sm text-gray-800' : 'text-gray-500 hover:text-gray-700'" class="flex-1 text-xs font-medium py-1.5 rounded-md transition flex items-center justify-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    Desenhar
                                </button>
                                <button type="button" @click="mode = 'upload'" :class="mode === 'upload' ? 'bg-white shadow-sm text-gray-800' : 'text-gray-500 hover:text-gray-700'" class="flex-1 text-xs font-medium py-1.5 rounded-md transition flex items-center justify-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Enviar imagem
                                </button>
                            </div>

                            {{-- Draw pad --}}
                            <div x-show="mode === 'draw'" x-cloak>
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-1 bg-white relative" :class="drawnDataUrl ? 'border-green-400' : ''">
                                    <canvas x-ref="sigCanvas" width="500" height="160" class="w-full rounded cursor-crosshair" style="touch-action: none;"></canvas>
                                    <div x-show="isEmpty && !drawnDataUrl" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                        <p class="text-xs text-gray-400">Assine aqui com o mouse ou dedo</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 mt-2">
                                    <button type="button" @click="clearPad()" class="inline-flex items-center gap-1 text-xs text-gray-500 hover:text-gray-700 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Limpar
                                    </button>
                                    <div class="flex-1"></div>
                                    <div x-show="drawnDataUrl" x-cloak class="inline-flex items-center gap-1 text-xs text-green-600 font-medium">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Assinatura capturada
                                    </div>
                                </div>
                                <input type="hidden" name="signature_drawn" :value="drawnDataUrl">
                            </div>

                            {{-- Upload --}}
                            <div x-show="mode === 'upload'" x-cloak>
                                <input type="file" name="signature" accept="image/png,image/jpeg,image/jpg"
                                       class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer">
                                <p class="text-xs text-gray-400 mt-1">PNG com fundo transparente funciona melhor.</p>
                            </div>
                        </div>

                        <div class="p-3 bg-amber-50 rounded-lg border border-amber-200">
                            <p class="text-xs text-amber-700">
                                <strong>Dica:</strong> Esses dados aparecem no certificado PDF. Desenhe sua assinatura ou envie uma imagem escaneada.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Personalizar Certificado --}}
                <div class="bg-white rounded-xl shadow-sm p-6" x-data="certCustomizer()" x-init="renderPreview()" @preview-color.window="cprimary=$event.detail.primary||cprimary;csecondary=$event.detail.secondary||csecondary;renderPreview()">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-800">Personalizar Certificado</h3>
                            <p class="text-xs text-gray-400">Aparência e textos do certificado PDF</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-1">
                            <label class="block text-xs font-medium text-gray-600">Estilo da Moldura</label>
                            <div class="grid grid-cols-3 gap-2">
                                <label class="cursor-pointer">
                                    <input type="radio" name="cert_border_style" value="classic" x-model="borderStyle" @change="renderPreview()" class="sr-only peer">
                                    <div class="border-2 rounded-lg p-3 text-center transition peer-checked:border-primary peer-checked:bg-primary/5 border-gray-200 hover:border-gray-300">
                                        <div class="w-full h-10 border-2 border-gray-400 rounded relative mb-1.5">
                                            <div class="absolute inset-1 border border-gray-300 rounded"></div>
                                        </div>
                                        <span class="text-xs font-medium text-gray-700">Clássico</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="cert_border_style" value="simple" x-model="borderStyle" @change="renderPreview()" class="sr-only peer">
                                    <div class="border-2 rounded-lg p-3 text-center transition peer-checked:border-primary peer-checked:bg-primary/5 border-gray-200 hover:border-gray-300">
                                        <div class="w-full h-10 border-2 border-gray-400 rounded mb-1.5"></div>
                                        <span class="text-xs font-medium text-gray-700">Simples</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="cert_border_style" value="none" x-model="borderStyle" @change="renderPreview()" class="sr-only peer">
                                    <div class="border-2 rounded-lg p-3 text-center transition peer-checked:border-primary peer-checked:bg-primary/5 border-gray-200 hover:border-gray-300">
                                        <div class="w-full h-10 border-2 border-dashed border-gray-200 rounded mb-1.5 flex items-center justify-center">
                                            <span class="text-gray-300 text-xs">—</span>
                                        </div>
                                        <span class="text-xs font-medium text-gray-700">Sem moldura</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="block text-xs font-medium text-gray-600">Título principal</label>
                                <input type="text" name="cert_title_text" x-model="titleText" @input="renderPreview()"
                                       placeholder="CERTIFICADO" maxlength="100"
                                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-xs font-medium text-gray-600">Subtítulo</label>
                                <input type="text" name="cert_subtitle_text" x-model="subtitleText" @input="renderPreview()"
                                       placeholder="de Conclusão" maxlength="100"
                                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                        </div>
                    </div>

                    {{-- Live Preview do Certificado --}}
                    <div class="mt-5 pt-5 border-t border-gray-100">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Preview do Certificado</p>
                        <div class="rounded-lg border border-gray-200 overflow-hidden bg-white">
                            <iframe x-ref="certPreview" style="width:100%;border:0;pointer-events:none;aspect-ratio:297/210;" scrolling="no"></iframe>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">O preview atualiza ao vivo conforme você altera as opções acima.</p>
                    </div>
                </div>

            </div>

            {{-- Preview ao vivo (1/3) --}}
        <div x-data="{
            primary: '{{ $company->primary_color ?? '#4f46e5' }}',
            secondary: '{{ $company->secondary_color ?? '#3730a3' }}'
        }"
        @preview-color.window="
            if ($event.detail.primary !== undefined) primary = $event.detail.primary;
            if ($event.detail.secondary !== undefined) secondary = $event.detail.secondary;
        ">
            <div class="bg-white rounded-xl shadow-sm p-5 sticky top-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Pré-visualização ao vivo</p>

                <div class="rounded-xl overflow-hidden border border-gray-200 shadow-sm">
                    <div class="flex" style="height:220px">
                        {{-- Mini sidebar --}}
                        <div class="w-28 flex flex-col py-2.5 px-2 gap-0.5 flex-shrink-0 text-white"
                             :style="'background-color:' + primary">
                            <div class="px-1 pb-2 mb-1" style="border-bottom:1px solid rgba(255,255,255,0.12)">
                                <span class="font-bold text-white truncate" style="font-size:9px">TreinaEdu</span>
                            </div>
                            <div class="flex items-center gap-1.5 px-1.5 py-1 rounded"
                                 style="background:rgba(255,255,255,0.2)">
                                <div class="w-2.5 h-2.5 rounded-sm bg-white/50 flex-shrink-0"></div>
                                <span class="text-white" style="font-size:8px">Dashboard</span>
                            </div>
                            @foreach(['Usuários','Grupos','Treinamentos','Relatórios'] as $item)
                            <div class="flex items-center gap-1.5 px-1.5 py-1 rounded opacity-60">
                                <div class="w-2.5 h-2.5 rounded-sm bg-white/40 flex-shrink-0"></div>
                                <span style="font-size:8px">{{ $item }}</span>
                            </div>
                            @endforeach
                        </div>
                        {{-- Content --}}
                        <div class="flex-1 bg-gray-50 p-2.5">
                            <div class="bg-white rounded-lg px-2 py-1.5 mb-2 flex items-center justify-between border border-gray-100">
                                <div class="h-2 w-12 bg-gray-200 rounded"></div>
                                <div class="flex items-center gap-1">
                                    <div class="w-4 h-4 rounded-full" :style="'background-color:' + secondary"></div>
                                    <div class="h-1.5 w-8 bg-gray-200 rounded"></div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-1.5 mb-2">
                                <div class="bg-white rounded-lg p-2 border border-gray-100">
                                    <div class="h-3 w-4 rounded mb-1" :style="'background-color:' + secondary + '30'"></div>
                                    <div class="h-1.5 w-7 bg-gray-200 rounded"></div>
                                </div>
                                <div class="bg-white rounded-lg p-2 border border-gray-100">
                                    <div class="h-3 w-4 rounded mb-1" :style="'background-color:' + secondary + '30'"></div>
                                    <div class="h-1.5 w-7 bg-gray-200 rounded"></div>
                                </div>
                            </div>
                            <div class="h-6 rounded-lg flex items-center justify-center"
                                 :style="'background-color:' + secondary">
                                <div class="h-1.5 w-10 bg-white/50 rounded"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 mt-3">
                    <div class="flex items-center gap-2 flex-1 min-w-0">
                        <div class="w-5 h-5 rounded-full border border-gray-200 flex-shrink-0" :style="'background-color:' + primary"></div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-gray-600">Menu</p>
                            <p class="text-xs font-mono text-gray-400 truncate" x-text="primary"></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-1 min-w-0">
                        <div class="w-5 h-5 rounded-full border border-gray-200 flex-shrink-0" :style="'background-color:' + secondary"></div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-gray-600">Destaque</p>
                            <p class="text-xs font-mono text-gray-400 truncate" x-text="secondary"></p>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-3">As cores são refletidas no menu ao salvar e recarregar a página.</p>

                <div class="mt-5 pt-5 border-t border-gray-100">
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 bg-primary hover:bg-secondary text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Salvar Configurações
                    </button>
                </div>
            </div>
        </div>
        </div>
    </form>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.2.0/dist/signature_pad.umd.min.js"></script>
    <script>
    function certCustomizer() {
        return {
            borderStyle: '{{ $company->cert_border_style ?? 'classic' }}',
            titleText: '{{ addslashes($company->cert_title_text ?? 'CERTIFICADO') }}',
            subtitleText: '{{ addslashes($company->cert_subtitle_text ?? 'de Conclusão') }}',
            cprimary: '{{ $company->primary_color ?? '#4f46e5' }}',
            csecondary: '{{ $company->secondary_color ?? '#3730a3' }}',
            renderPreview() {
                const iframe = this.$refs.certPreview;
                if (!iframe) return;
                const p = this.cprimary, s = this.csecondary, b = this.borderStyle;
                const t = (this.titleText || 'CERTIFICADO').replace(/</g,'&lt;');
                const st = (this.subtitleText || 'de Conclusão').replace(/</g,'&lt;');
                const logo = @js($company->logo_path ? Storage::url($company->logo_path) : '');
                const companyName = @js($company->name);
                const signerName = @js($company->cert_signer_name ?? '');
                const signerRole = @js($company->cert_signer_role ?? '');
                const borderHtml = b === 'none' ? '' : `
                    <div style="position:absolute;top:5%;left:4%;right:4%;height:2px;background:${p}"></div>
                    <div style="position:absolute;bottom:5%;left:4%;right:4%;height:2px;background:${p}"></div>
                ` + (b === 'classic' ? `
                    <div style="position:absolute;top:6.5%;left:4%;right:4%;height:1px;background:${p}"></div>
                    <div style="position:absolute;bottom:6.5%;left:4%;right:4%;height:1px;background:${p}"></div>
                    <div style="position:absolute;top:7%;left:5%;width:4%;height:6%;border-top:2px solid ${p};border-left:2px solid ${p}"></div>
                    <div style="position:absolute;top:7%;right:5%;width:4%;height:6%;border-top:2px solid ${p};border-right:2px solid ${p}"></div>
                    <div style="position:absolute;bottom:7%;left:5%;width:4%;height:6%;border-bottom:2px solid ${p};border-left:2px solid ${p}"></div>
                    <div style="position:absolute;bottom:7%;right:5%;width:4%;height:6%;border-bottom:2px solid ${p};border-right:2px solid ${p}"></div>
                ` : '');
                const logoHtml = logo
                    ? `<img src="${logo}" style="max-height:7%;max-width:20%;margin:0 auto 1%;">`
                    : `<div style="font-size:2.2vw;font-weight:bold;color:${s};margin-bottom:1%">${companyName}</div>`;
                const signerHtml = signerName ? `
                    <div style="width:15%;height:1px;background:#9ca3af;margin:0 auto 1%"></div>
                    <div style="font-size:1.4vw;font-weight:bold;color:#1f2937">${signerName}</div>
                    ${signerRole ? `<div style="font-size:1vw;color:#6b7280">${signerRole}</div>` : ''}
                ` : '';
                const html = `<!DOCTYPE html><html><head><style>
                    *{margin:0;padding:0;box-sizing:border-box}
                    body{font-family:Helvetica,Arial,sans-serif;color:#1f2937;overflow:hidden;width:100vw;height:100vh;position:relative;text-align:center}
                </style></head><body>
                    ${borderHtml}
                    <div style="padding:12% 12% 28% 12%">
                        ${logoHtml}
                        <div style="margin:1.5% 0;font-size:1vw;letter-spacing:3px;color:${s}">
                            <span style="display:inline-block;width:8%;height:1px;background:${p};vertical-align:middle"></span>
                            <span style="padding:0 1%;vertical-align:middle">APRESENTA</span>
                            <span style="display:inline-block;width:8%;height:1px;background:${p};vertical-align:middle"></span>
                        </div>
                        <div style="font-size:6vw;font-weight:bold;color:${s};letter-spacing:3px;line-height:1.1">${t}</div>
                        <div style="font-size:2.2vw;font-style:italic;color:${p};letter-spacing:1px;margin-bottom:2%">${st}</div>
                        <div style="font-size:1.2vw;color:#6b7280;letter-spacing:2px;text-transform:uppercase;margin-bottom:1.5%">Certificamos que</div>
                        <div style="font-size:4vw;font-weight:bold;color:${s};margin-bottom:2%">Nome do Colaborador</div>
                        <div style="max-width:70%;margin:0 auto;padding:1.5% 3%;border-top:2px solid ${p};border-bottom:2px solid ${p};background:#f5f9ff">
                            <div style="font-size:1vw;color:#6b7280;letter-spacing:1px;text-transform:uppercase;margin-bottom:0.5%">concluiu com sucesso o treinamento</div>
                            <div style="font-size:2.5vw;font-weight:bold;color:#1f2937">Treinamento Exemplo</div>
                            <div style="font-size:1.2vw;color:#6b7280;margin-top:0.5%">carga horária de <strong style="color:#1f2937">2h</strong> · emitido por <strong style="color:#1f2937">${companyName}</strong></div>
                        </div>
                    </div>
                    <div style="position:absolute;left:10%;right:10%;bottom:8%">
                        <table style="width:100%;border-collapse:collapse"><tr>
                            <td style="width:28%;text-align:center;vertical-align:bottom">
                                <div style="font-size:0.9vw;color:#9ca3af;letter-spacing:1px;text-transform:uppercase">Emitido em</div>
                                <div style="font-size:1.4vw;font-weight:bold;color:${s}">${new Date().toLocaleDateString('pt-BR')}</div>
                            </td>
                            <td style="width:44%;text-align:center;vertical-align:bottom">${signerHtml}</td>
                            <td style="width:28%;text-align:center;vertical-align:bottom">
                                <div style="font-size:0.9vw;color:#9ca3af;letter-spacing:1px;text-transform:uppercase">Verificar</div>
                                <div style="width:6%;padding-bottom:6%;background:#e5e7eb;margin:1% auto 0;border-radius:3px"></div>
                            </td>
                        </tr></table>
                    </div>
                    <div style="position:absolute;left:0;right:0;bottom:3%;text-align:center;font-size:0.9vw;color:#9ca3af">
                        Verificado por <strong style="color:${p}">TreinaEdu</strong>
                    </div>
                </body></html>`;
                const scrollY = window.scrollY;
                iframe.srcdoc = html;
                requestAnimationFrame(() => window.scrollTo(0, scrollY));
            }
        };
    }

    function signaturePad() {
        return {
            mode: 'draw',
            pad: null,
            isEmpty: true,
            drawnDataUrl: '',
            init() {
                this.$nextTick(() => {
                    const canvas = this.$refs.sigCanvas;
                    if (!canvas) return;
                    this.pad = new SignaturePad(canvas, {
                        backgroundColor: 'rgba(0,0,0,0)',
                        penColor: '#1f2937',
                        minWidth: 1.5,
                        maxWidth: 3,
                    });
                    this.pad.addEventListener('beginStroke', () => { this.isEmpty = false; });
                    this.pad.addEventListener('endStroke', () => {
                        this.drawnDataUrl = this.pad.toDataURL('image/png');
                    });
                    this.resizeCanvas(canvas);
                    window.addEventListener('resize', () => this.resizeCanvas(canvas));
                });
            },
            resizeCanvas(canvas) {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                const rect = canvas.getBoundingClientRect();
                canvas.width = rect.width * ratio;
                canvas.height = rect.height * ratio;
                canvas.getContext('2d').scale(ratio, ratio);
                if (this.pad) this.pad.clear();
                this.isEmpty = true;
                this.drawnDataUrl = '';
            },
            clearPad() {
                if (this.pad) this.pad.clear();
                this.isEmpty = true;
                this.drawnDataUrl = '';
            }
        };
    }

    function logoUploader() {
        return {
            showCropper: false,
            croppedPreview: null,
            cropper: null,
            extractedColors: [],
            selectedColorIndex: null,

            extractColors(imageSource) {
                const img = new Image();
                img.crossOrigin = 'anonymous';
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    const size = 80;
                    canvas.width = size;
                    canvas.height = size;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, size, size);
                    const data = ctx.getImageData(0, 0, size, size).data;

                    const colorMap = {};
                    for (let i = 0; i < data.length; i += 4) {
                        const r = data[i], g = data[i+1], b = data[i+2], a = data[i+3];
                        if (a < 128) continue;
                        // Ignorar cores muito claras (branco/quase branco) e muito escuras (preto/quase preto)
                        const brightness = (r * 299 + g * 587 + b * 114) / 1000;
                        if (brightness > 240 || brightness < 15) continue;
                        // Quantizar para reduzir variações
                        const qr = Math.round(r / 32) * 32;
                        const qg = Math.round(g / 32) * 32;
                        const qb = Math.round(b / 32) * 32;
                        const key = qr + ',' + qg + ',' + qb;
                        if (!colorMap[key]) colorMap[key] = { r: qr, g: qg, b: qb, count: 0 };
                        colorMap[key].count++;
                    }

                    const sorted = Object.values(colorMap).sort((a, b) => b.count - a.count);
                    const result = [];
                    for (const c of sorted) {
                        if (result.length >= 6) break;
                        // Evitar cores muito similares
                        const isDuplicate = result.some(existing => {
                            return Math.abs(existing.r - c.r) < 40 && Math.abs(existing.g - c.g) < 40 && Math.abs(existing.b - c.b) < 40;
                        });
                        if (!isDuplicate) {
                            const hex = '#' + [c.r, c.g, c.b].map(v => Math.min(255, v).toString(16).padStart(2, '0')).join('');
                            result.push({ r: c.r, g: c.g, b: c.b, hex });
                        }
                    }
                    this.extractedColors = result.map(c => c.hex);
                    this.selectedColorIndex = null;
                };
                img.src = imageSource;
            },

            applyColorAs(type) {
                if (this.selectedColorIndex === null) return;
                const color = this.extractedColors[this.selectedColorIndex];
                const input = document.querySelector('input[name="' + type + '_color"]');
                if (input) {
                    input.value = color;
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                }
                window.dispatchEvent(new CustomEvent('preview-color', { detail: { [type]: color } }));
            },

            onFileSelected(e) {
                const file = e.target.files[0];
                if (!file) return;
                if (file.type === 'image/svg+xml') {
                    const url = URL.createObjectURL(file);
                    this.croppedPreview = url;
                    this.extractColors(url);
                    return;
                }
                const reader = new FileReader();
                reader.onload = (ev) => {
                    this.$refs.cropImage.src = ev.target.result;
                    this.showCropper = true;
                    this.croppedPreview = null;
                    this.$nextTick(() => {
                        if (this.cropper) this.cropper.destroy();
                        this.cropper = new Cropper(this.$refs.cropImage, {
                            aspectRatio: NaN,
                            viewMode: 2,
                            dragMode: 'move',
                            autoCropArea: 0.9,
                            guides: true,
                            center: true,
                            highlight: false,
                        });
                    });
                };
                reader.readAsDataURL(file);
            },

            rotateCrop(deg) {
                if (this.cropper) this.cropper.rotate(deg);
            },

            applyCrop() {
                if (!this.cropper) return;
                this.cropper.getCroppedCanvas({ maxWidth: 600, maxHeight: 300 }).toBlob((blob) => {
                    const file = new File([blob], 'logo.png', { type: 'image/png' });
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    this.$refs.fileInput.files = dt.files;
                    const url = URL.createObjectURL(blob);
                    this.croppedPreview = url;
                    this.extractColors(url);
                    this.showCropper = false;
                    this.cropper.destroy();
                    this.cropper = null;
                }, 'image/png');
            },

            cancelCrop() {
                if (this.cropper) { this.cropper.destroy(); this.cropper = null; }
                this.showCropper = false;
                this.croppedPreview = null;
                this.$refs.fileInput.value = '';
            }
        }
    }
    </script>
    @endpush

</x-layout.app>
