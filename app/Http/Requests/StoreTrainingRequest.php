<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTrainingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => ['required', 'url', function ($attribute, $value, $fail) {
                if (!str_contains($value, 'youtube.com') && !str_contains($value, 'youtu.be') && !str_contains($value, 'vimeo.com')) {
                    $fail('A URL deve ser do YouTube ou Vimeo.');
                }
            }],
            'duration_minutes' => 'required|integer|min:1',
            'has_quiz' => 'boolean',
            'passing_score' => 'nullable|required_if:has_quiz,1|integer|min:1|max:100',
            'questions' => 'nullable|required_if:has_quiz,1|array|min:1',
            'questions.*.question' => 'required_with:questions|string',
            'questions.*.options' => 'required_with:questions|array|min:2',
            'questions.*.options.*.text' => 'required|string|max:500',
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
