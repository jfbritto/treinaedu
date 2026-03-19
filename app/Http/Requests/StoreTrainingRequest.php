<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTrainingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (!$this->boolean('has_quiz')) {
            $this->request->remove('questions');
        }
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes_override' => 'nullable|integer|min:1',
            'is_sequential' => 'boolean',

            // Modules
            'modules' => 'required|array|min:1',
            'modules.*.title' => 'required|string|max:255',
            'modules.*.description' => 'nullable|string',
            'modules.*.is_sequential' => 'boolean',

            // Lessons within modules
            'modules.*.lessons' => 'required|array|min:1',
            'modules.*.lessons.*.title' => 'required|string|max:255',
            'modules.*.lessons.*.type' => 'required|in:video,document,text',
            'modules.*.lessons.*.video_url' => 'required_if:modules.*.lessons.*.type,video|nullable|url',
            'modules.*.lessons.*.duration_minutes' => 'nullable|integer|min:0',
            'modules.*.lessons.*.content' => 'required_if:modules.*.lessons.*.type,text|nullable|string',

            // Quiz (training-level) — only validated when has_quiz is checked
            'has_quiz' => 'boolean',
            'passing_score' => 'nullable|integer|min:1|max:100',
            'questions' => 'exclude_unless:has_quiz,1|array|min:1',
            'questions.*.question' => 'required_with:questions|string',
            'questions.*.options' => 'required_with:questions|array|min:2',
            'questions.*.options.*.text' => 'required_with:questions|string|max:500',
            'questions.*.correct' => 'required_with:questions|integer|min:0',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $questions = $this->input('questions', []);
            foreach ($questions as $qi => $question) {
                $optionCount = count($question['options'] ?? []);
                $correct = (int) ($question['correct'] ?? -1);
                if ($optionCount > 0 && ($correct < 0 || $correct >= $optionCount)) {
                    $validator->errors()->add(
                        "questions.{$qi}.correct",
                        "A opção correta da questão " . ($qi + 1) . " é inválida."
                    );
                }
            }
        });
    }
}
