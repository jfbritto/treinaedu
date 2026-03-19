<x-layout.app title="Editar Treinamento">

    <style>
        .tip { position: relative; }
        .tip::after {
            content: attr(data-tip);
            position: absolute;
            bottom: calc(100% + 6px);
            left: 50%;
            transform: translateX(-50%);
            background: #1f2937;
            color: white;
            font-size: 11px;
            font-weight: 500;
            padding: 4px 10px;
            border-radius: 6px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.15s;
            z-index: 50;
        }
        .tip:hover::after { opacity: 1; }
        @keyframes flash-move {
            0% { box-shadow: 0 0 0 0 rgba(59,130,246,0); }
            30% { box-shadow: 0 0 0 4px rgba(59,130,246,0.3); }
            100% { box-shadow: 0 0 0 0 rgba(59,130,246,0); }
        }
        .flash-move { animation: flash-move 0.6s ease-out; }
    </style>

    <div x-data="{
        modules: @js($training->modules->map(fn($m) => [
            'id' => $m->id,
            'title' => $m->title,
            'description' => $m->description,
            'is_sequential' => $m->is_sequential,
            'showDescription' => !empty($m->description),
            'lessons' => $m->lessons->map(fn($l) => [
                'id' => $l->id,
                'title' => $l->title,
                'type' => $l->type,
                'video_url' => $l->video_url ?? '',
                'duration_minutes' => $l->duration_minutes ?? 0,
                'content' => $l->content ?? '',
                'file_path' => $l->file_path,
                'hasQuiz' => $l->quiz !== null,
                'questions' => $l->quiz ? $l->quiz->questions->map(fn($q) => [
                    'text' => $q->question,
                    'options' => $q->options->map(fn($o) => ['text' => $o->option_text])->values()->toArray(),
                    'correct' => $q->options->search(fn($o) => $o->is_correct) ?? 0,
                ])->values()->toArray() : [['text' => '', 'options' => [['text' => ''], ['text' => '']], 'correct' => 0]],
            ])->values()->toArray(),
        ])->values()->toArray()),
        addModule() {
            this.modules.push({
                id: null, title: '', description: '', is_sequential: true, showDescription: false,
                lessons: [{ id: null, title: '', type: 'video', video_url: '', duration_minutes: 0, content: '', hasQuiz: false, questions: [{ text: '', options: [{ text: '' }, { text: '' }], correct: 0 }] }]
            });
        },
        removeModule(i) {
            if (this.modules.length > 1) this.modules.splice(i, 1);
        },
        moveModule(i, dir) {
            const j = i + dir;
            if (j < 0 || j >= this.modules.length) return;
            [this.modules[i], this.modules[j]] = [this.modules[j], this.modules[i]];
            this.$nextTick(() => {
                const cards = this.$el.querySelectorAll('[data-module-card]');
                if (cards[j]) { cards[j].classList.remove('flash-move'); void cards[j].offsetWidth; cards[j].classList.add('flash-move'); }
            });
        },
        addLesson(mi) {
            this.modules[mi].lessons.push({ id: null, title: '', type: 'video', video_url: '', duration_minutes: 0, content: '', hasQuiz: false, questions: [{ text: '', options: [{ text: '' }, { text: '' }], correct: 0 }] });
        },
        removeLesson(mi, li) {
            if (this.modules[mi].lessons.length > 1) this.modules[mi].lessons.splice(li, 1);
        },
        moveLesson(mi, li, dir) {
            const lessons = this.modules[mi].lessons;
            const j = li + dir;
            if (j < 0 || j >= lessons.length) return;
            [lessons[li], lessons[j]] = [lessons[j], lessons[li]];
            this.$nextTick(() => {
                const cards = this.$el.querySelectorAll('[data-module-card]');
                if (cards[mi]) {
                    const lessonCards = cards[mi].querySelectorAll('[data-lesson-card]');
                    if (lessonCards[j]) { lessonCards[j].classList.remove('flash-move'); void lessonCards[j].offsetWidth; lessonCards[j].classList.add('flash-move'); }
                }
            });
        },
        getEmbedUrl(url) {
            if (!url) return '';
            let match = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/);
            if (match) return 'https://www.youtube.com/embed/' + match[1];
            match = url.match(/vimeo\.com\/(\d+)/);
            if (match) return 'https://player.vimeo.com/video/' + match[1];
            return '';
        },
        addLessonQuestion(mi, li) {
            this.modules[mi].lessons[li].questions.push({ text: '', options: [{ text: '' }, { text: '' }], correct: 0 });
        },
        removeLessonQuestion(mi, li, qi) {
            if (this.modules[mi].lessons[li].questions.length > 1) this.modules[mi].lessons[li].questions.splice(qi, 1);
        },
        addLessonOption(mi, li, qi) {
            this.modules[mi].lessons[li].questions[qi].options.push({ text: '' });
        },
        removeLessonOption(mi, li, qi, oi) {
            if (this.modules[mi].lessons[li].questions[qi].options.length > 2) this.modules[mi].lessons[li].questions[qi].options.splice(oi, 1);
        },
        hasQuiz: {{ $training->has_quiz ? 'true' : 'false' }},
        questions: @js($training->quiz ? $training->quiz->questions->map(fn($q) => [
            'text' => $q->question,
            'options' => $q->options->map(fn($o) => ['text' => $o->option_text])->values()->toArray(),
            'correct' => $q->options->search(fn($o) => $o->is_correct) ?? 0,
        ])->values()->toArray() : [['text' => '', 'options' => [['text' => ''], ['text' => '']], 'correct' => 0]]),
        addQuestion() {
            this.questions.push({ text: '', options: [{ text: '' }, { text: '' }], correct: 0 });
        },
        removeQuestion(qi) {
            if (this.questions.length > 1) this.questions.splice(qi, 1);
        },
        addOption(qi) {
            this.questions[qi].options.push({ text: '' });
        },
        removeOption(qi, oi) {
            if (this.questions[qi].options.length > 2) this.questions[qi].options.splice(oi, 1);
        }
    }">

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('trainings.index') }}"
               class="inline-flex items-center gap-1 text-sm text-gray-400 hover:text-gray-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Voltar
            </a>
            <h2 class="text-lg font-bold text-gray-800">Editar Treinamento</h2>
        </div>

        <form method="POST" action="{{ route('trainings.update', $training) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            {{-- Section 1: Training Basics --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">Dados do Treinamento</h3>
                        <p class="text-xs text-gray-400">Informações gerais sobre o treinamento</p>
                    </div>
                </div>

                <div class="space-y-5">
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Título <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $training->title) }}" required
                               class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                               placeholder="Ex: Onboarding de novos colaboradores">
                        @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Descrição</label>
                        <textarea name="description" rows="3"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                            placeholder="Descreva o objetivo deste treinamento...">{{ old('description', $training->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Duração (minutos)</label>
                            <input type="number" name="duration_minutes_override" value="{{ old('duration_minutes_override', $training->duration_minutes_override) }}" min="1"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                                   placeholder="Calculado automaticamente">
                            <p class="text-xs text-gray-400">Deixe vazio para calcular a partir das aulas</p>
                        </div>
                        <div class="flex flex-col justify-end pb-0.5 space-y-1">
                            <label class="flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                                <input type="checkbox" name="is_sequential" value="1"
                                       {{ old('is_sequential', $training->is_sequential) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-primary focus:ring-primary">
                                Módulos sequenciais
                            </label>
                            <p class="text-xs text-gray-400">Colaborador deve concluir os módulos na ordem</p>
                        </div>
                    </div>

                    <div class="flex flex-col space-y-1">
                        <label class="flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                            <input type="checkbox" name="active" value="1"
                                   {{ old('active', $training->active) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-primary focus:ring-primary">
                            Treinamento ativo
                        </label>
                        <p class="text-xs text-gray-400">Visível para os colaboradores</p>
                    </div>
                </div>
            </div>

            {{-- Section 2: Module Builder --}}
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">Módulos e Aulas</h3>
                        <p class="text-xs text-gray-400">Organize o conteúdo do treinamento em módulos com suas aulas</p>
                    </div>
                </div>

                <template x-for="(module, mi) in modules" :key="mi">
                    <div class="bg-white rounded-xl shadow-sm transition-shadow" data-module-card>
                        {{-- Hidden ID for existing modules --}}
                        <template x-if="module.id">
                            <input type="hidden" :name="'modules['+mi+'][id]'" :value="module.id">
                        </template>

                        {{-- Module Header --}}
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
                            <div class="flex items-center gap-3">
                                <span class="w-7 h-7 rounded-lg bg-primary text-white text-xs font-bold flex items-center justify-center flex-shrink-0"
                                      x-text="mi + 1"></span>
                                <input type="text"
                                       :name="'modules['+mi+'][title]'"
                                       x-model="module.title"
                                       placeholder="Título do módulo"
                                       required
                                       class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-primary">
                                <div class="flex items-center gap-1">
                                    <button type="button" @click="moveModule(mi, -1)" x-show="mi > 0"
                                            class="tip p-1.5 rounded-lg border border-gray-200 hover:bg-gray-100 text-gray-400 transition" data-tip="Subir módulo na ordem">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                    </button>
                                    <button type="button" @click="moveModule(mi, 1)" x-show="mi < modules.length - 1"
                                            class="tip p-1.5 rounded-lg border border-gray-200 hover:bg-gray-100 text-gray-400 transition" data-tip="Descer módulo na ordem">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                    <button type="button" @click="removeModule(mi)" x-show="modules.length > 1"
                                            class="tip p-1.5 rounded-lg border border-red-200 hover:bg-red-50 text-red-400 hover:text-red-600 transition" data-tip="Excluir este módulo">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Module options row --}}
                            <div class="flex items-center gap-4 mt-3">
                                <button type="button" @click="module.showDescription = !module.showDescription"
                                        class="text-xs text-primary hover:text-secondary transition font-medium">
                                    <span x-text="module.showDescription ? 'Ocultar descrição' : 'Adicionar descrição'"></span>
                                </button>
                                <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer">
                                    <input type="checkbox"
                                           :name="'modules['+mi+'][is_sequential]'"
                                           value="1"
                                           x-model="module.is_sequential"
                                           class="rounded border-gray-300 text-primary focus:ring-primary">
                                    Aulas sequenciais
                                </label>
                            </div>

                            {{-- Module description --}}
                            <div x-show="module.showDescription" x-cloak class="mt-3">
                                <textarea :name="'modules['+mi+'][description]'"
                                          x-model="module.description"
                                          rows="2"
                                          placeholder="Descrição do módulo (opcional)"
                                          class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                            </div>
                        </div>

                        {{-- Lessons --}}
                        <div class="p-6 space-y-3">
                            <template x-for="(lesson, li) in module.lessons" :key="li">
                                <div class="bg-gray-50 rounded-xl border border-gray-100 p-4 space-y-3 transition-shadow" data-lesson-card>
                                    {{-- Hidden ID for existing lessons --}}
                                    <template x-if="lesson.id">
                                        <input type="hidden" :name="'modules['+mi+'][lessons]['+li+'][id]'" :value="lesson.id">
                                    </template>

                                    <div class="flex items-center gap-3">
                                        <span class="text-xs font-semibold text-gray-400 w-6 text-center flex-shrink-0"
                                              x-text="(li + 1) + '.'"></span>

                                        {{-- Lesson title --}}
                                        <input type="text"
                                               :name="'modules['+mi+'][lessons]['+li+'][title]'"
                                               x-model="lesson.title"
                                               placeholder="Título da aula"
                                               required
                                               class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-white">

                                        {{-- Lesson type --}}
                                        <div class="flex items-center gap-1 flex-shrink-0">
                                            <input type="hidden" :name="'modules['+mi+'][lessons]['+li+'][type]'" x-model="lesson.type">
                                            <button type="button" @click="lesson.type = 'video'"
                                                :class="lesson.type === 'video' ? 'bg-primary/10 text-primary border-primary/30' : 'bg-white text-gray-400 border-gray-200 hover:text-gray-600'"
                                                class="tip p-1.5 rounded-lg border transition" data-tip="Vídeo">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            </button>
                                            <button type="button" @click="lesson.type = 'document'"
                                                :class="lesson.type === 'document' ? 'bg-primary/10 text-primary border-primary/30' : 'bg-white text-gray-400 border-gray-200 hover:text-gray-600'"
                                                class="tip p-1.5 rounded-lg border transition" data-tip="Documento">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </button>
                                            <button type="button" @click="lesson.type = 'text'"
                                                :class="lesson.type === 'text' ? 'bg-primary/10 text-primary border-primary/30' : 'bg-white text-gray-400 border-gray-200 hover:text-gray-600'"
                                                class="tip p-1.5 rounded-lg border transition" data-tip="Texto">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                                            </button>
                                        </div>

                                        {{-- Lesson actions --}}
                                        <div class="flex items-center gap-1">
                                            <button type="button" @click="moveLesson(mi, li, -1)" x-show="li > 0"
                                                    class="tip p-1 rounded border border-gray-200 hover:bg-gray-100 text-gray-400 transition" data-tip="Subir aula na ordem">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                            </button>
                                            <button type="button" @click="moveLesson(mi, li, 1)" x-show="li < module.lessons.length - 1"
                                                    class="tip p-1 rounded border border-gray-200 hover:bg-gray-100 text-gray-400 transition" data-tip="Descer aula na ordem">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                            <button type="button" @click="removeLesson(mi, li)" x-show="module.lessons.length > 1"
                                                    class="tip p-1 rounded border border-red-200 hover:bg-red-50 text-red-400 hover:text-red-600 transition" data-tip="Excluir esta aula">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Conditional fields: Video --}}
                                    <div x-show="lesson.type === 'video'" x-cloak class="flex gap-3 pl-9">
                                        <div class="flex-1 space-y-1">
                                            <label class="block text-xs font-medium text-gray-500">URL do vídeo</label>
                                            <input type="url"
                                                   :name="'modules['+mi+'][lessons]['+li+'][video_url]'"
                                                   x-model="lesson.video_url"
                                                   placeholder="https://www.youtube.com/watch?v=..."
                                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-white">
                                        </div>
                                        <div class="w-32 space-y-1">
                                            <label class="block text-xs font-medium text-gray-500">Duração (min)</label>
                                            <input type="number"
                                                   :name="'modules['+mi+'][lessons]['+li+'][duration_minutes]'"
                                                   x-model="lesson.duration_minutes"
                                                   min="0"
                                                   placeholder="0"
                                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-white">
                                        </div>
                                    </div>

                                    {{-- Conditional fields: Document --}}
                                    <div x-show="lesson.type === 'document'" x-cloak class="pl-9 space-y-1">
                                        <label class="block text-xs font-medium text-gray-500">Arquivo</label>
                                        <template x-if="lesson.file_path">
                                            <div class="flex items-center gap-2 mb-2 p-2 bg-white rounded-lg border border-gray-200">
                                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <span class="text-xs text-gray-600 truncate" x-text="lesson.file_path.split('/').pop()"></span>
                                                <span class="text-xs text-gray-400">(atual)</span>
                                            </div>
                                        </template>
                                        <input type="file"
                                               :name="'modules['+mi+'][lessons]['+li+'][file]'"
                                               accept=".pdf,.pptx,.docx,application/pdf,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-white file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                                        <p class="text-xs text-gray-400" x-text="lesson.file_path ? 'Envie um novo arquivo para substituir o atual' : 'PDF, PPTX ou DOCX'"></p>
                                    </div>

                                    {{-- Conditional fields: Text --}}
                                    <div x-show="lesson.type === 'text'" x-cloak class="pl-9 space-y-1">
                                        <label class="block text-xs font-medium text-gray-500">Conteúdo</label>
                                        <textarea :name="'modules['+mi+'][lessons]['+li+'][content]'"
                                                  x-model="lesson.content"
                                                  rows="4"
                                                  placeholder="Digite o conteúdo da aula..."
                                                  class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-white"></textarea>
                                    </div>

                                    {{-- Video preview --}}
                                    <div x-show="lesson.type === 'video' && getEmbedUrl(lesson.video_url)" x-cloak class="pl-9">
                                        <p class="text-xs font-medium text-gray-500 mb-1.5">Preview</p>
                                        <div class="rounded-lg overflow-hidden bg-black border border-gray-200" style="max-width: 480px">
                                            <div class="aspect-video">
                                                <iframe :src="getEmbedUrl(lesson.video_url)" class="w-full h-full" frameborder="0" allowfullscreen
                                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Lesson quiz toggle --}}
                                    <div class="pl-9 pt-2 border-t border-gray-100 mt-2">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox"
                                                   :name="'modules['+mi+'][lessons]['+li+'][has_quiz]'"
                                                   value="1"
                                                   x-model="lesson.hasQuiz"
                                                   class="rounded border-gray-300 text-primary focus:ring-primary">
                                            <span class="text-xs font-medium text-gray-600">Quiz desta aula</span>
                                        </label>

                                        <div x-show="lesson.hasQuiz" x-cloak class="mt-3 space-y-3">
                                            <template x-for="(q, qi) in lesson.questions" :key="qi">
                                                <div class="border border-gray-200 rounded-lg p-3 space-y-2 bg-white">
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-xs font-semibold text-gray-500" x-text="'Questão ' + (qi + 1)"></span>
                                                        <button type="button" @click="removeLessonQuestion(mi, li, qi)" x-show="lesson.questions.length > 1"
                                                            class="text-xs text-red-500 hover:text-red-700">Remover</button>
                                                    </div>
                                                    <input type="text" :name="'modules['+mi+'][lessons]['+li+'][questions]['+qi+'][question]'"
                                                           x-model="q.text" placeholder="Pergunta..."
                                                           class="w-full rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                                                    <div class="space-y-1.5">
                                                        <template x-for="(opt, oi) in q.options" :key="oi">
                                                            <div class="flex items-center gap-2">
                                                                <input type="radio" :name="'modules['+mi+'][lessons]['+li+'][questions]['+qi+'][correct]'" :value="oi"
                                                                       x-model="q.correct" class="text-primary border-gray-300">
                                                                <input type="text" :name="'modules['+mi+'][lessons]['+li+'][questions]['+qi+'][options]['+oi+'][text]'"
                                                                       x-model="opt.text" placeholder="Opção..."
                                                                       class="flex-1 rounded-lg border border-gray-300 px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                                                                <button type="button" @click="removeLessonOption(mi, li, qi, oi)" x-show="q.options.length > 2"
                                                                    class="text-gray-400 hover:text-red-500">
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                                </button>
                                                            </div>
                                                        </template>
                                                        <button type="button" @click="addLessonOption(mi, li, qi)"
                                                            class="text-xs text-primary hover:text-secondary font-medium">+ Opção</button>
                                                    </div>
                                                </div>
                                            </template>
                                            <button type="button" @click="addLessonQuestion(mi, li)"
                                                class="text-xs text-primary hover:text-secondary font-medium">+ Questão</button>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            {{-- Add lesson button --}}
                            <button type="button" @click="addLesson(mi)"
                                    class="inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:text-secondary transition mt-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Adicionar aula
                            </button>
                        </div>
                    </div>
                </template>

                {{-- Add module button --}}
                <button type="button" @click="addModule()"
                        class="w-full flex items-center justify-center gap-2 border-2 border-dashed border-gray-300 hover:border-primary rounded-xl py-4 text-sm font-medium text-gray-500 hover:text-primary transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Adicionar módulo
                </button>
            </div>

            {{-- Section 3: Quiz --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">Quiz de avaliação</h3>
                        <p class="text-xs text-gray-400">Opcional — aplicado ao final do treinamento</p>
                    </div>
                    <label class="ml-auto flex items-center gap-2 cursor-pointer">
                        <span class="text-xs text-gray-500">Ativar quiz</span>
                        <div class="relative">
                            <input type="checkbox" name="has_quiz" value="1" x-model="hasQuiz" class="sr-only peer">
                            <div class="w-10 h-5 bg-gray-200 peer-checked:bg-primary rounded-full transition"></div>
                            <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition peer-checked:translate-x-5"></div>
                        </div>
                    </label>
                </div>

                <div x-show="hasQuiz" x-cloak class="space-y-5">
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Nota mínima de aprovação (%)</label>
                        <input type="number" name="passing_score" value="{{ old('passing_score', $training->passing_score ?? 70) }}" min="1" max="100"
                               class="w-32 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>

                    <div class="space-y-4">
                        <template x-for="(q, qi) in questions" :key="qi">
                            <div class="border border-gray-200 rounded-xl p-4 space-y-3 bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide" x-text="'Questão ' + (qi + 1)"></span>
                                    <button type="button" @click="removeQuestion(qi)" x-show="questions.length > 1"
                                        class="text-xs text-red-500 hover:text-red-700 transition">Remover</button>
                                </div>
                                <input type="text" :name="'questions[' + qi + '][question]'"
                                       x-model="q.text" placeholder="Digite a pergunta..."
                                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary">
                                <div class="space-y-2">
                                    <p class="text-xs font-medium text-gray-500">Opções <span class="text-gray-400 font-normal">(marque a correta)</span></p>
                                    <template x-for="(opt, oi) in q.options" :key="oi">
                                        <div class="flex items-center gap-2">
                                            <input type="radio" :name="'questions[' + qi + '][correct]'" :value="oi"
                                                   x-model="q.correct" class="text-primary border-gray-300">
                                            <input type="text" :name="'questions[' + qi + '][options][' + oi + '][text]'"
                                                   x-model="opt.text" placeholder="Opção..."
                                                   class="flex-1 rounded-lg border border-gray-300 px-3 py-1.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary">
                                            <button type="button" @click="removeOption(qi, oi)" x-show="q.options.length > 2"
                                                class="text-gray-400 hover:text-red-500 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                    <button type="button" @click="addOption(qi)"
                                        class="text-xs text-primary hover:text-secondary transition font-medium">+ Adicionar opção</button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <button type="button" @click="addQuestion()"
                        class="inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:text-secondary transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Adicionar questão
                    </button>
                </div>
            </div>

            {{-- Section 4: Submit --}}
            <div class="flex gap-3">
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-primary hover:bg-secondary text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Salvar Alterações
                </button>
                <a href="{{ route('trainings.index') }}"
                   class="px-4 py-2.5 text-sm font-medium text-gray-500 hover:text-gray-700 transition">Cancelar</a>
            </div>
        </form>
    </div>
</x-layout.app>
