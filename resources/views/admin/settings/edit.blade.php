<x-layout.app title="Configurações da Empresa">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>

    <div class="mb-6">
        <h2 class="text-lg font-bold text-gray-800">Configurações da Empresa</h2>
        <p class="text-xs text-gray-400 mt-0.5">Personalize a identidade visual da plataforma</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Formulário (2/3) --}}
        <div class="lg:col-span-2 space-y-6">

            <form method="POST" action="{{ route('company.settings.update') }}" enctype="multipart/form-data" id="settings-form">
                @csrf
                @method('PUT')

                {{-- Nome --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-800">Dados da Empresa</h3>
                            <p class="text-xs text-gray-400">Nome exibido no sistema para todos os usuários</p>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nome da empresa <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name"
                            value="{{ old('name', $company->name) }}" required
                            class="w-full rounded-lg border @error('name') border-red-400 @else border-gray-300 @enderror px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Logo com Cropper --}}
                <div class="bg-white rounded-xl shadow-sm p-6" x-data="logoUploader()">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-800">Logo da Empresa</h3>
                            <p class="text-xs text-gray-400">Exibida no topo do menu lateral para todos os usuários</p>
                        </div>
                    </div>

                    @if($company->logo_path)
                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100 mb-4">
                            <img src="{{ Storage::url($company->logo_path) }}" alt="Logo atual"
                                 class="h-12 w-auto max-w-32 object-contain rounded-lg border border-gray-200 bg-white p-1.5">
                            <div>
                                <p class="text-sm font-medium text-gray-700">Logo atual</p>
                                <p class="text-xs text-gray-400">Envie uma nova imagem para substituir</p>
                            </div>
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
                            class="w-full flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition">
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
                            <p class="text-xs text-gray-400">Cores aplicadas no menu lateral e nos destaques da interface</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6" x-data="{
                        primary: '{{ old('primary_color', $company->primary_color ?? '#1e293b') }}',
                        secondary: '{{ old('secondary_color', $company->secondary_color ?? '#3B82F6') }}'
                    }">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-0.5">Cor do Menu Lateral</label>
                            <p class="text-xs text-gray-400 mb-3">Fundo do sidebar — prefira tons escuros</p>
                            <div class="flex items-center gap-3">
                                <input type="color" name="primary_color"
                                       x-model="primary"
                                       @input="window.dispatchEvent(new CustomEvent('preview-color', {detail: {primary: $event.target.value}}))"
                                       class="h-10 w-14 cursor-pointer rounded-lg border border-gray-200 p-1">
                                <div>
                                    <p class="text-sm font-mono font-medium text-gray-700" x-text="primary"></p>
                                    <p class="text-xs text-gray-400">Sidebar background</p>
                                </div>
                            </div>
                            @error('primary_color') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-0.5">Cor de Destaque</label>
                            <p class="text-xs text-gray-400 mb-3">Botões e ícones de ação</p>
                            <div class="flex items-center gap-3">
                                <input type="color" name="secondary_color"
                                       x-model="secondary"
                                       @input="window.dispatchEvent(new CustomEvent('preview-color', {detail: {secondary: $event.target.value}}))"
                                       class="h-10 w-14 cursor-pointer rounded-lg border border-gray-200 p-1">
                                <div>
                                    <p class="text-sm font-mono font-medium text-gray-700" x-text="secondary"></p>
                                    <p class="text-xs text-gray-400">Buttons &amp; accents</p>
                                </div>
                            </div>
                            @error('secondary_color') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Salvar Configurações
                    </button>
                </div>
            </form>
        </div>

        {{-- Preview ao vivo (1/3) --}}
        <div x-data="{
            primary: '{{ $company->primary_color ?? '#1e293b' }}',
            secondary: '{{ $company->secondary_color ?? '#3B82F6' }}'
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
                            <div class="flex items-center gap-1.5 px-1 pb-2 mb-1"
                                 style="border-bottom:1px solid rgba(255,255,255,0.12)">
                                <div class="w-5 h-5 rounded flex-shrink-0 flex items-center justify-center"
                                     :style="'background:linear-gradient(135deg,' + secondary + ',#6366f1)'">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13"/></svg>
                                </div>
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
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function logoUploader() {
        return {
            showCropper: false,
            croppedPreview: null,
            cropper: null,

            onFileSelected(e) {
                const file = e.target.files[0];
                if (!file) return;
                if (file.type === 'image/svg+xml') {
                    this.croppedPreview = URL.createObjectURL(file);
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
                    this.croppedPreview = URL.createObjectURL(blob);
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
