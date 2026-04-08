<x-layout.app title="Certificado Gerado">

    <div class="max-w-3xl mx-auto">

        {{-- Back link --}}
        <div class="mb-6">
            <a href="{{ route('employee.certificates.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Voltar aos certificados
            </a>
        </div>

        {{-- Hero --}}
        <div class="rounded-xl overflow-hidden shadow-sm mb-6 text-white relative"
             style="background: linear-gradient(135deg, var(--primary), var(--secondary))">
            {{-- Decorative circles --}}
            <div class="absolute -top-16 -right-16 w-56 h-56 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-20 -left-20 w-64 h-64 rounded-full bg-white/5"></div>

            <div class="relative p-8 text-center">
                {{-- Icon --}}
                <div class="w-20 h-20 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center mx-auto mb-5">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>

                <p class="text-xs text-white/80 uppercase tracking-wider mb-1">Conquista desbloqueada</p>
                <h1 class="text-3xl font-bold mb-2">Certificado emitido com sucesso</h1>
                <p class="text-sm text-white/80 max-w-md mx-auto">Parabéns por concluir este treinamento. Seu certificado já está disponível para visualização e download.</p>
            </div>
        </div>

        {{-- Certificate Details --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-gray-800">Detalhes do certificado</h3>
                        <p class="text-xs text-gray-400">Informações de validação e autenticidade</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-5 leading-snug">{{ $certificate->training->title }}</h2>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50">
                        <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <dt class="text-xs text-gray-400 mb-0.5">Concluinte</dt>
                            <dd class="text-sm font-semibold text-gray-800 truncate">{{ $certificate->user->name }}</dd>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50">
                        <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <dt class="text-xs text-gray-400 mb-0.5">Empresa</dt>
                            <dd class="text-sm font-semibold text-gray-800 truncate">{{ $certificate->company->name }}</dd>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50">
                        <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <dt class="text-xs text-gray-400 mb-0.5">Data de emissão</dt>
                            <dd class="text-sm font-semibold text-gray-800">{{ $certificate->generated_at->format('d/m/Y') }}</dd>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50">
                        <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <dt class="text-xs text-gray-400 mb-0.5">Código de validação</dt>
                            <dd class="text-sm font-mono font-semibold text-gray-800 truncate">{{ $certificate->certificate_code }}</dd>
                        </div>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Actions --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-gray-800">Próximas ações</h3>
                        <p class="text-xs text-gray-400">Visualize, baixe ou compartilhe seu certificado</p>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-3">
                <a href="{{ route('employee.certificates.show', $certificate) }}"
                   target="_blank"
                   class="w-full flex items-center justify-center gap-2 px-5 py-3 rounded-lg text-white font-semibold text-sm transition shadow-sm hover:shadow-md"
                   style="background: linear-gradient(135deg, var(--primary), var(--secondary))">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Visualizar Certificado
                </a>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <a href="{{ route('employee.certificates.download', $certificate) }}"
                       class="flex items-center justify-center gap-2 px-5 py-3 rounded-lg text-primary font-semibold text-sm bg-primary/10 hover:bg-primary/20 transition"
                       download>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Baixar PDF
                    </a>

                    <a href="{{ route('employee.trainings.index') }}"
                       class="flex items-center justify-center gap-2 px-5 py-3 rounded-lg text-gray-700 font-semibold text-sm border border-gray-200 hover:bg-gray-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        Meus Treinamentos
                    </a>
                </div>
            </div>
        </div>

        {{-- Tip --}}
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex items-start gap-3">
            <div class="w-9 h-9 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-sm font-semibold text-blue-900 mb-0.5">Compartilhe sua conquista</p>
                <p class="text-xs text-blue-800">Use a opção "Visualizar Certificado" para compartilhar no LinkedIn ou validar a autenticidade do seu certificado com o código único de verificação.</p>
            </div>
        </div>

    </div>

</x-layout.app>
