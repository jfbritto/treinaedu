<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private string $apiKey;
    private string $model;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key', '');
        $this->model = config('services.gemini.model', 'gemini-2.0-flash');
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Generate quiz questions from lesson content.
     *
     * @return array|null Array of questions in Alpine.js format, or null on failure.
     */
    public function generateQuiz(string $lessonTitle, string $content, int $numberOfQuestions = 5): ?array
    {
        if (!$this->isConfigured()) {
            return null;
        }

        $systemPrompt = <<<'PROMPT'
Você é um especialista em educação corporativa e avaliação de conhecimento.
Sua tarefa é criar perguntas de quiz sobre o conteúdo de aulas de treinamento empresarial.

Regras:
- Todas as perguntas e opções DEVEM ser em português brasileiro (pt-BR)
- Crie perguntas que avaliem compreensão real, não apenas memorização
- Cada pergunta deve ter entre 3 e 4 opções de resposta
- Apenas UMA opção deve ser a correta
- As opções incorretas devem ser plausíveis mas claramente erradas para quem estudou o conteúdo
- Varie o nível de dificuldade: inclua perguntas fáceis, médias e difíceis
- Responda EXCLUSIVAMENTE com um JSON array válido

Formato de saída (JSON array):
[
  {
    "text": "Texto da pergunta",
    "options": [
      { "text": "Opção A" },
      { "text": "Opção B" },
      { "text": "Opção C" },
      { "text": "Opção D" }
    ],
    "correct": 0
  }
]

O campo "correct" é o índice (começando em 0) da opção correta no array "options".
PROMPT;

        $userPrompt = "Gere {$numberOfQuestions} perguntas de quiz para a seguinte aula de treinamento:\n\n"
            . "Título da aula: {$lessonTitle}\n\n"
            . "Conteúdo da aula:\n{$content}";

        try {
            $response = Http::timeout(30)->post(
                "{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}",
                [
                    'systemInstruction' => [
                        'parts' => [['text' => $systemPrompt]],
                    ],
                    'contents' => [
                        ['parts' => [['text' => $userPrompt]]],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 4096,
                        'responseMimeType' => 'application/json',
                    ],
                ]
            );

            if (!$response->successful()) {
                Log::warning('Gemini API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $text = $response->json('candidates.0.content.parts.0.text');

            if (empty($text)) {
                Log::warning('Gemini API: empty response text');
                return null;
            }

            // Clean up markdown fences if present (defensive fallback)
            $text = preg_replace('/^```(?:json)?\s*/i', '', trim($text));
            $text = preg_replace('/\s*```$/', '', $text);

            $questions = json_decode($text, true);

            if (!is_array($questions)) {
                Log::warning('Gemini API: invalid JSON response', ['text' => $text]);
                return null;
            }

            return $this->validateQuestions($questions);
        } catch (\Throwable $e) {
            Log::warning('Gemini API exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Generate text content (descriptions, summaries, etc.)
     */
    public function generateText(string $systemPrompt, string $userPrompt): ?string
    {
        if (!$this->isConfigured()) {
            return null;
        }

        try {
            $response = Http::timeout(30)->post(
                "{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}",
                [
                    'systemInstruction' => [
                        'parts' => [['text' => $systemPrompt]],
                    ],
                    'contents' => [
                        ['parts' => [['text' => $userPrompt]]],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 2048,
                    ],
                ]
            );

            if (!$response->successful()) {
                return null;
            }

            return $response->json('candidates.0.content.parts.0.text');
        } catch (\Throwable $e) {
            Log::warning('Gemini text generation failed', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Validate and sanitize the questions array to match the expected Alpine.js format.
     */
    private function validateQuestions(array $questions): ?array
    {
        $valid = [];

        foreach ($questions as $q) {
            if (!is_array($q)) continue;
            if (empty($q['text']) || !is_string($q['text'])) continue;
            if (empty($q['options']) || !is_array($q['options']) || count($q['options']) < 2) continue;
            if (!isset($q['correct']) || !is_int($q['correct'])) continue;
            if ($q['correct'] < 0 || $q['correct'] >= count($q['options'])) continue;

            $options = [];
            $allValid = true;
            foreach ($q['options'] as $opt) {
                if (!is_array($opt) || empty($opt['text']) || !is_string($opt['text'])) {
                    $allValid = false;
                    break;
                }
                $options[] = ['text' => $opt['text']];
            }

            if (!$allValid) continue;

            $valid[] = [
                'text' => $q['text'],
                'options' => $options,
                'correct' => $q['correct'],
            ];
        }

        return !empty($valid) ? $valid : null;
    }
}
