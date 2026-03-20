<x-layout.app :title="$training->title">

    {{-- Botões de ação --}}
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('trainings.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-800 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <span class="text-sm font-medium">Voltar</span>
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('trainings.edit', $training) }}"
               class="inline-flex items-center gap-2 bg-primary hover:bg-secondary text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar
            </a>
            <form method="POST" action="{{ route('trainings.destroy', $training) }}" data-confirm="Remover este treinamento?" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Deletar
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Coluna principal --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Detalhes do treinamento --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $training->title }}</h1>
                    @if($training->description)
                        <p class="text-gray-600 mt-2">{{ $training->description }}</p>
                    @endif
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="relative overflow-hidden rounded-lg bg-gradient-to-br from-blue-50 to-blue-100/50 p-4 border border-blue-200/50">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-blue-600 font-medium uppercase tracking-wide">Duração</p>
                                <p class="text-2xl font-bold text-blue-900 mt-1">{{ $training->duration_minutes }}</p>
                                <p class="text-xs text-blue-700">minutos</p>
                            </div>
                            <svg class="w-8 h-8 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00-.293.707l-2.414 2.414a1 1 0 101.414 1.414L9 11.414V6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>

                    <div class="relative overflow-hidden rounded-lg {{ $training->active ? 'bg-gradient-to-br from-green-50 to-green-100/50 border border-green-200/50' : 'bg-gradient-to-br from-red-50 to-red-100/50 border border-red-200/50' }} p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs {{ $training->active ? 'text-green-600' : 'text-red-600' }} font-medium uppercase tracking-wide">Status</p>
                                <p class="text-2xl font-bold {{ $training->active ? 'text-green-900' : 'text-red-900' }} mt-1">{{ $training->active ? 'Ativo' : 'Inativo' }}</p>
                            </div>
                            <svg class="w-8 h-8 {{ $training->active ? 'text-green-200' : 'text-red-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>

                    <div class="relative overflow-hidden rounded-lg {{ $training->has_quiz ? 'bg-gradient-to-br from-purple-50 to-purple-100/50 border border-purple-200/50' : 'bg-gradient-to-br from-gray-50 to-gray-100/50 border border-gray-200/50' }} p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs {{ $training->has_quiz ? 'text-purple-600' : 'text-gray-600' }} font-medium uppercase tracking-wide">Quiz</p>
                                <p class="text-2xl font-bold {{ $training->has_quiz ? 'text-purple-900' : 'text-gray-800' }} mt-1">{{ $training->has_quiz ? 'Sim' : 'Não' }}</p>
                            </div>
                            <svg class="w-8 h-8 {{ $training->has_quiz ? 'text-purple-200' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h12a2 2 0 012 2v2a1 1 0 11-2 0V5H4v12h6a1 1 0 110 2H4a2 2 0 01-2-2V5zm8 5a1 1 0 100-2 1 1 0 000 2zm-3 5a1 1 0 11-2 0 1 1 0 012 0zM14 13a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>

                    <div class="relative overflow-hidden rounded-lg bg-gradient-to-br from-amber-50 to-amber-100/50 p-4 border border-amber-200/50">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-amber-600 font-medium uppercase tracking-wide">Conclusão</p>
                                <p class="text-2xl font-bold text-amber-900 mt-1">{{ $training->completionRate() }}%</p>
                            </div>
                            <div class="flex items-center justify-center w-8 h-8 rounded-full bg-amber-200/50">
                                <span class="text-xs font-bold text-amber-700">{{ $training->completionRate() }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Conteúdo do treinamento --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-primary"></div>
                        <h3 class="text-sm font-semibold text-gray-700">Estrutura do Conteúdo</h3>
                    </div>
                </div>

                <div class="p-6">
                    {{-- Stats compactas --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-6">
                        <div class="relative overflow-hidden rounded-lg bg-gradient-to-br from-primary/10 to-primary/5 p-4 border border-primary/20">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-2xl font-bold text-primary">{{ $training->modules->count() }}</p>
                                    <p class="text-xs text-gray-600 mt-1 font-medium">Módulo{{ $training->modules->count() !== 1 ? 's' : '' }}</p>
                                </div>
                                <svg class="w-8 h-8 text-primary/20" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 4a2 2 0 012-2h12a2 2 0 012 2v4a1 1 0 11-2 0V4H4v10h4a1 1 0 110 2H4a2 2 0 01-2-2V4z"/>
                                </svg>
                            </div>
                        </div>

                        <div class="relative overflow-hidden rounded-lg bg-gradient-to-br from-blue-500/10 to-blue-500/5 p-4 border border-blue-500/20">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-2xl font-bold text-blue-600">{{ $totalLessons }}</p>
                                    <p class="text-xs text-gray-600 mt-1 font-medium">Aula{{ $totalLessons !== 1 ? 's' : '' }}</p>
                                </div>
                                <svg class="w-8 h-8 text-blue-500/20" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000 2h10a1 1 0 000-2 2 2 0 00-2 2v10a2 2 0 002 2 1 1 0 100-2h-10a1 1 0 100 2 2 2 0 002-2V5z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>

                        @if(isset($contentTypes['video']) && $contentTypes['video'] > 0)
                            <div class="relative overflow-hidden rounded-lg bg-gradient-to-br from-red-500/10 to-red-500/5 p-4 border border-red-500/20">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-2xl font-bold text-red-600">{{ $contentTypes['video'] }}</p>
                                        <p class="text-xs text-gray-600 mt-1 font-medium">Vídeo{{ $contentTypes['video'] !== 1 ? 's' : '' }}</p>
                                    </div>
                                    <svg class="w-8 h-8 text-red-500/20" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 6a2 2 0 012-2h12a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm13.5-1a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
                                    </svg>
                                </div>
                            </div>
                        @endif

                        @if(isset($contentTypes['document']) && $contentTypes['document'] > 0)
                            <div class="relative overflow-hidden rounded-lg bg-gradient-to-br from-amber-500/10 to-amber-500/5 p-4 border border-amber-500/20">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-2xl font-bold text-amber-600">{{ $contentTypes['document'] }}</p>
                                        <p class="text-xs text-gray-600 mt-1 font-medium">Doc{{ $contentTypes['document'] !== 1 ? 's' : '' }}</p>
                                    </div>
                                    <svg class="w-8 h-8 text-amber-500/20" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0113 2.586L15.414 5A2 2 0 0116 6.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H7a1 1 0 01-1-1v-6z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                        @endif

                        @if(isset($contentTypes['text']) && $contentTypes['text'] > 0)
                            <div class="relative overflow-hidden rounded-lg bg-gradient-to-br from-green-500/10 to-green-500/5 p-4 border border-green-500/20">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-2xl font-bold text-green-600">{{ $contentTypes['text'] }}</p>
                                        <p class="text-xs text-gray-600 mt-1 font-medium">Texto{{ $contentTypes['text'] !== 1 ? 's' : '' }}</p>
                                    </div>
                                    <svg class="w-8 h-8 text-green-500/20" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.25 5.5a.75.75 0 00-.75.75v8.5c0 .414.336.75.75.75h8.5a.75.75 0 00.75-.75v-8.5a.75.75 0 00-.75-.75h-8.5zm12-1.5a2.25 2.25 0 00-2.25 2.25v8.5a2.25 2.25 0 002.25 2.25h3.75a.75.75 0 00.75-.75v-2.5a.75.75 0 00-.75-.75h-3v-2h2.25a.75.75 0 00.75-.75v-2.5a.75.75 0 00-.75-.75h-2.25v-2h3v-1.5a2.25 2.25 0 00-2.25-2.25h-3.75z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Estrutura de módulos --}}
                    @if($training->modules->isNotEmpty())
                        <div class="space-y-3">
                            @foreach($training->modules as $index => $module)
                                @php
                                    $moduleLessons = $module->lessons->count();
                                    $moduleQuizzes = $module->quiz ? 1 : 0;
                                @endphp
                                <div class="border border-gray-200 rounded-lg overflow-hidden hover:border-primary/30 transition">
                                    {{-- Cabeçalho do módulo --}}
                                    <div class="bg-gradient-to-r from-gray-50 to-white px-4 py-3 flex items-center justify-between">
                                        <div class="flex items-center gap-3 flex-1">
                                            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0 text-sm font-semibold text-primary">
                                                {{ $index + 1 }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-sm font-semibold text-gray-800">{{ $module->title }}</h4>
                                                @if($module->description)
                                                    <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ $module->description }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 ml-3">
                                            @if($module->is_sequential)
                                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-700 font-medium flex-shrink-0">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M5 10a1 1 0 011-1h8a1 1 0 011 1v4a1 1 0 11-2 0v-3H7v3a1 1 0 11-2 0v-4zm0-5a1 1 0 011-1h8a1 1 0 011 1v1a1 1 0 11-2 0V5H7v1a1 1 0 11-2 0V5z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Sequencial
                                                </span>
                                            @endif
                                            <span class="text-xs font-medium text-gray-500 px-2 py-1 bg-gray-100 rounded-full flex-shrink-0">
                                                {{ $moduleLessons }} aula{{ $moduleLessons !== 1 ? 's' : '' }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Aulas do módulo --}}
                                    <div class="border-t border-gray-100 px-4 py-2 space-y-1 bg-gray-50/50">
                                        @foreach($module->lessons as $lesson)
                                            <div class="flex items-center gap-2.5 py-2 text-sm text-gray-700">
                                                @if($lesson->type === 'video')
                                                    <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M2 6a2 2 0 012-2h12a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm13.5-1a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
                                                    </svg>
                                                @elseif($lesson->type === 'document')
                                                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0113 2.586L15.414 5A2 2 0 0116 6.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H7a1 1 0 01-1-1v-6z" clip-rule="evenodd"/>
                                                    </svg>
                                                @elseif($lesson->type === 'text')
                                                    <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4.25 5.5a.75.75 0 00-.75.75v8.5c0 .414.336.75.75.75h8.5a.75.75 0 00.75-.75v-8.5a.75.75 0 00-.75-.75h-8.5zm12-1.5a2.25 2.25 0 00-2.25 2.25v8.5a2.25 2.25 0 002.25 2.25h3.75a.75.75 0 00.75-.75v-2.5a.75.75 0 00-.75-.75h-3v-2h2.25a.75.75 0 00.75-.75v-2.5a.75.75 0 00-.75-.75h-2.25v-2h3v-1.5a2.25 2.25 0 00-2.25-2.25h-3.75z" clip-rule="evenodd"/>
                                                    </svg>
                                                @endif
                                                <span class="flex-1">{{ $lesson->title }}</span>
                                                @if($lesson->duration_minutes)
                                                    <span class="text-xs text-gray-500 flex-shrink-0">{{ $lesson->duration_minutes }}m</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Grupos atribuídos --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-primary"></div>
                        <h3 class="text-sm font-semibold text-gray-700">Grupos Atribuídos</h3>
                        <span class="text-xs text-gray-400 bg-gray-100 rounded-full px-2 py-0.5">{{ $training->assignments->count() }}</span>
                    </div>
                </div>

                @if($training->assignments->isEmpty())
                    <div class="p-8 text-center">
                        <svg class="w-10 h-10 text-gray-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <p class="text-sm text-gray-400">Nenhum grupo atribuído ainda.</p>
                    </div>
                @else
                    <div class="p-6 space-y-3">
                        @foreach($training->assignments as $assignment)
                            @php
                                $overdue  = $assignment->due_date && $assignment->due_date->isPast();
                                $soonDays = $assignment->due_date ? (int) now()->diffInDays($assignment->due_date, false) : null;
                                $dueSoon  = $soonDays !== null && $soonDays >= 0 && $soonDays <= 7;
                            @endphp
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-primary/30 transition">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-start gap-3 flex-1">
                                        {{-- Ícone grupo --}}
                                        <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                                            </svg>
                                        </div>
                                        {{-- Info do grupo --}}
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-sm font-semibold text-gray-800">{{ $assignment->group->name }}</h4>
                                            <div class="flex items-center gap-2 flex-wrap mt-2">
                                                @if($assignment->mandatory)
                                                    <span class="inline-flex items-center gap-1 text-xs font-medium bg-red-100 text-red-700 rounded-full px-2 py-0.5">
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 100-2 1 1 0 000 2zm0 4a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
                                                        Obrigatório
                                                    </span>
                                                @else
                                                    <span class="text-xs text-gray-500 bg-gray-100 rounded-full px-2 py-0.5">Opcional</span>
                                                @endif
                                                @if($assignment->due_date)
                                                    <span class="text-xs {{ $overdue ? 'text-red-600 font-medium bg-red-50' : ($dueSoon ? 'text-yellow-700 bg-yellow-50' : 'text-gray-600 bg-gray-50') }} rounded-full px-2 py-0.5">
                                                        {{ $assignment->due_date->format('d/m/Y') }}
                                                        @if($overdue)
                                                            <svg class="w-3 h-3 inline-block ml-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                                        @elseif($dueSoon)
                                                            ({{ $soonDays }}d)
                                                        @endif
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Remover --}}
                                    <form method="POST"
                                          action="{{ route('trainings.assignments.destroy', [$training, $assignment]) }}"
                                          data-confirm="Remover atribuição do grupo {{ $assignment->group->name }}?"
                                          class="flex-shrink-0">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs font-medium text-red-500 hover:text-red-700 hover:bg-red-50 rounded px-2 py-1 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Adicionar grupos --}}
            @if($availableGroups->isNotEmpty())
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Atribuir a mais grupos</h3>
                    <form method="POST" action="{{ route('trainings.assignments.store', $training) }}" class="space-y-4">
                        @csrf

                        {{-- Grupos disponíveis --}}
                        <div class="space-y-1">
                            <label class="block text-xs font-medium text-gray-600">Selecione os grupos</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 border border-gray-200 rounded-xl p-3 bg-gray-50">
                                @foreach($availableGroups as $group)
                                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer hover:text-gray-900 transition">
                                        <input type="checkbox" name="group_ids[]" value="{{ $group->id }}"
                                               class="rounded border-gray-300 text-primary">
                                        {{ $group->name }}
                                    </label>
                                @endforeach
                            </div>
                            @error('group_ids')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Prazo --}}
                            <div class="space-y-1">
                                <label class="block text-xs font-medium text-gray-600">Data limite (opcional)</label>
                                <input type="date" name="due_date" value="{{ old('due_date') }}"
                                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                                @error('due_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            {{-- Obrigatório --}}
                            <div class="flex items-end pb-0.5">
                                <label class="flex items-center gap-2.5 cursor-pointer p-3 rounded-xl border border-gray-200 hover:border-red-200 hover:bg-red-50 transition group w-full">
                                    <input type="checkbox" name="mandatory" value="1"
                                           {{ old('mandatory') ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-red-500">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700 group-hover:text-red-700 transition">Obrigatório</p>
                                        <p class="text-xs text-gray-400">Exige conclusão do colaborador</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <button type="submit"
                                    class="inline-flex items-center gap-2 bg-primary hover:bg-secondary text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Atribuir grupos
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="bg-gray-50 border border-dashed border-gray-200 rounded-xl p-4 text-center text-xs text-gray-400">
                    Todos os grupos já estão atribuídos a este treinamento.
                </div>
            @endif

            {{-- Usuários vinculados --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-6 py-5 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM9 6a3 3 0 11-6 0 3 3 0 016 0zM18 6a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-800">Usuários Vinculados</h3>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $assignedUsers->count() }} colaborador{{ $assignedUsers->count() !== 1 ? 'es' : '' }} atribuído{{ $assignedUsers->count() !== 1 ? 's' : '' }}</p>
                        </div>
                    </div>
                </div>

                @if($assignedUsers->isEmpty())
                    <div class="p-8 text-center">
                        <svg class="w-10 h-10 text-gray-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <p class="text-sm text-gray-400">Nenhum usuário vinculado a este treinamento.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="text-left px-6 py-4 font-semibold text-gray-700 uppercase tracking-wider text-xs">Usuário</th>
                                    <th class="text-center px-6 py-4 font-semibold text-gray-700 uppercase tracking-wider text-xs">Progresso</th>
                                    <th class="text-left px-6 py-4 font-semibold text-gray-700 uppercase tracking-wider text-xs hidden md:table-cell">Último Acesso</th>
                                    <th class="text-center px-6 py-4 font-semibold text-gray-700 uppercase tracking-wider text-xs">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-150">
                                @foreach($assignedUsers as $user)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                                    <span class="text-xs font-semibold text-primary">{{ substr($user['name'], 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-800">{{ $user['name'] }}</p>
                                                    <p class="text-xs text-gray-500">{{ $user['email'] }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-center gap-2">
                                                <div class="w-20 bg-gray-200 rounded-full h-2.5">
                                                    <div class="bg-primary h-2.5 rounded-full transition-all" style="width: {{ $user['progress_percent'] }}%"></div>
                                                </div>
                                                <span class="text-xs font-bold text-gray-800 min-w-[35px] text-right">{{ $user['progress_percent'] }}%</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 hidden md:table-cell">
                                            @if($user['last_accessed'])
                                                <p class="text-sm text-gray-700">{{ $user['last_accessed']->format('d/m/Y H:i') }}</p>
                                            @else
                                                <span class="text-xs text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($user['completed'])
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Concluído
                                                </span>
                                            @elseif($user['progress_percent'] > 0)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                    </svg>
                                                    Em Progresso
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    Não Iniciado
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>

        {{-- Sidebar info --}}
        <div class="space-y-4">
            {{-- Resumo Rápido --}}
            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-6 h-6 rounded-md bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-primary" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-800">Resumo</p>
                </div>
                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Grupos</span>
                        <span class="font-semibold text-gray-800">{{ $training->assignments->count() }}</span>
                    </div>
                    <div class="border-t border-gray-100 pt-3 flex items-center justify-between">
                        <span class="text-gray-600">Colaboradores</span>
                        <span class="font-semibold text-gray-800">{{ $assignedUsers->count() }}</span>
                    </div>
                    <div class="border-t border-gray-100 pt-3 flex items-center justify-between">
                        <span class="text-gray-600">Taxa Conclusão</span>
                        <span class="font-semibold {{ $training->completionRate() >= 50 ? 'text-green-600' : ($training->completionRate() >= 25 ? 'text-yellow-600' : 'text-gray-800') }}">
                            {{ $training->completionRate() }}%
                        </span>
                    </div>
                </div>
            </div>

            {{-- Metadata --}}
            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-6 h-6 rounded-md bg-gray-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-800">Informações</p>
                </div>
                <div class="space-y-3 text-xs">
                    <div>
                        <p class="text-gray-500">Criado em</p>
                        <p class="text-gray-800 font-medium mt-0.5">{{ $training->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="border-t border-gray-100 pt-3">
                        <p class="text-gray-500">Atualizado em</p>
                        <p class="text-gray-800 font-medium mt-0.5">{{ $training->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            {{-- Como funciona --}}
            <div class="bg-primary/5 border border-primary/15 rounded-xl p-4">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-6 h-6 rounded-md bg-primary flex items-center justify-center flex-shrink-0">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-primary">Como funciona</p>
                </div>
                <div class="space-y-2.5 text-xs text-gray-600">
                    <p>Atribuindo a um grupo, todos os membros passam a ver este treinamento automaticamente.</p>
                    <p>Marcar como <strong>obrigatório</strong> sinaliza que a conclusão é exigida.</p>
                    <p>A <strong>data limite</strong> aparece com urgência quando o prazo se aproxima.</p>
                </div>
            </div>

        </div>

    </div>

</x-layout.app>
