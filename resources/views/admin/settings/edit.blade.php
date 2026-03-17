<x-layout.app title="Configurações da Empresa">
    <div class="max-w-2xl">
        <div class="flex items-center gap-3 mb-6">
            <h2 class="text-xl font-semibold text-gray-800">Configurações da Empresa</h2>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm p-6">
            <form method="POST"
                  action="{{ route('company.settings.update') }}"
                  enctype="multipart/form-data"
                  class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Logo --}}
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Logo da Empresa</label>

                    @if ($company->logo_path)
                        <div class="mb-2">
                            <img src="{{ asset($company->logo_path) }}"
                                 alt="Logo atual"
                                 class="h-16 object-contain rounded border border-gray-200 p-1">
                            <p class="text-xs text-gray-400 mt-1">Logo atual</p>
                        </div>
                    @endif

                    <input type="file"
                           name="logo"
                           accept="image/jpg,image/jpeg,image/png,image/svg+xml"
                           class="block w-full text-sm text-gray-600
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-lg file:border-0
                                  file:text-sm file:font-medium
                                  file:bg-gray-100 file:text-gray-700
                                  hover:file:bg-gray-200">

                    @error('logo')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-400">Formatos aceitos: JPG, PNG, SVG. Máx. 2 MB.</p>
                </div>

                {{-- Primary Color --}}
                <div class="space-y-1">
                    <label for="primary_color" class="block text-sm font-medium text-gray-700">
                        Cor Primária
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="color"
                               id="primary_color"
                               name="primary_color"
                               value="{{ old('primary_color', $company->primary_color ?? '#2563eb') }}"
                               class="h-10 w-16 cursor-pointer rounded border border-gray-300 p-1">
                        <span class="text-sm text-gray-500" id="primary_color_label">
                            {{ old('primary_color', $company->primary_color ?? '#2563eb') }}
                        </span>
                    </div>
                    @error('primary_color')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Secondary Color --}}
                <div class="space-y-1">
                    <label for="secondary_color" class="block text-sm font-medium text-gray-700">
                        Cor Secundária
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="color"
                               id="secondary_color"
                               name="secondary_color"
                               value="{{ old('secondary_color', $company->secondary_color ?? '#1e40af') }}"
                               class="h-10 w-16 cursor-pointer rounded border border-gray-300 p-1">
                        <span class="text-sm text-gray-500" id="secondary_color_label">
                            {{ old('secondary_color', $company->secondary_color ?? '#1e40af') }}
                        </span>
                    </div>
                    @error('secondary_color')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <x-forms.button type="submit">Salvar Configurações</x-forms.button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function syncColorLabel(inputId, labelId) {
            const input = document.getElementById(inputId);
            const label = document.getElementById(labelId);
            input.addEventListener('input', () => { label.textContent = input.value; });
        }
        syncColorLabel('primary_color', 'primary_color_label');
        syncColorLabel('secondary_color', 'secondary_color_label');
    </script>
</x-layout.app>
