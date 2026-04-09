<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GeminiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function generateQuiz(Request $request, GeminiService $gemini): JsonResponse
    {
        $validated = $request->validate([
            'lesson_title' => 'required|string|max:255',
            'content' => 'required|string|max:15000',
            'num_questions' => 'nullable|integer|min:1|max:15',
        ]);

        if (!$gemini->isConfigured()) {
            return response()->json(['error' => 'Serviço de IA não configurado. Adicione GEMINI_API_KEY no .env.'], 503);
        }

        $questions = $gemini->generateQuiz(
            $validated['lesson_title'],
            $validated['content'],
            $validated['num_questions'] ?? 5
        );

        if ($questions === null) {
            return response()->json(['error' => 'Não foi possível gerar o quiz. Tente novamente.'], 422);
        }

        return response()->json(['questions' => $questions]);
    }

    public function generateDescription(Request $request, GeminiService $gemini): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'context' => 'nullable|string|max:5000',
            'type' => 'required|in:training,path',
        ]);

        if (!$gemini->isConfigured()) {
            return response()->json(['error' => 'Serviço de IA não configurado.'], 503);
        }

        $typeLabel = $validated['type'] === 'training' ? 'treinamento corporativo' : 'trilha de aprendizagem';

        $systemPrompt = "Você é um redator de conteúdo de educação corporativa. "
            . "Escreva descrições curtas (2-3 frases), profissionais e em português brasileiro (pt-BR). "
            . "Seja objetivo e destaque o valor para o colaborador. Responda apenas com o texto da descrição, sem aspas ou formatação.";

        $userPrompt = "Escreva uma descrição para o seguinte {$typeLabel}:\n\n"
            . "Título: {$validated['title']}";

        if (!empty($validated['context'])) {
            $userPrompt .= "\n\nContexto adicional:\n{$validated['context']}";
        }

        $text = $gemini->generateText($systemPrompt, $userPrompt);

        if ($text === null) {
            return response()->json(['error' => 'Não foi possível gerar a descrição.'], 422);
        }

        return response()->json(['description' => trim($text)]);
    }
}
