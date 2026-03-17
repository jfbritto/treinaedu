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
}
