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
        $company = $request->user()?->company;
        if ($company && !$company->planHasFeature('ai_quiz')) {
            return response()->json([
                'error' => 'Quiz com IA está disponível a partir do plano Business.',
                'upgrade_url' => route('subscription.plans'),
            ], 403);
        }

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

    public function suggestTitle(Request $request, GeminiService $gemini): JsonResponse
    {
        $company = $request->user()?->company;
        if ($company && !$company->planHasFeature('ai_quiz')) {
            return response()->json(['error' => 'IA está disponível a partir do plano Business.'], 403);
        }

        $validated = $request->validate([
            'level' => 'required|in:lesson,module,training',
            'input' => 'required|string|max:2000',
            'context' => 'nullable|string|max:2000',
        ]);

        if (!$gemini->isConfigured()) {
            return response()->json(['error' => 'Serviço de IA não configurado.'], 503);
        }

        $systemPrompt = "Você é um especialista em nomear conteúdos de treinamentos corporativos em português brasileiro. "
            . "Você SEMPRE responde com APENAS o título sugerido, sem aspas, sem pontuação final, sem explicações. "
            . "Nunca comece com verbos como 'Aprimore', 'Descubra', 'Aprenda'. Use substantivos. "
            . "Exemplos de bons títulos: 'Velocidade Média', 'Física Básica', 'Segurança do Trabalho', 'Gestão de Projetos', 'Primeiros Socorros'.";

        $rules = match ($validated['level']) {
            'lesson' => "Limpe este título de vídeo para usar como nome de AULA (nível mais específico). "
                . "Máximo 5 palavras. Remova: nome de canais, numeração (Aula 01), termos como COMPLETO/DEFINITIVO, emojis. "
                . "Mantenha apenas o assunto central do vídeo.",
            'module' => "Crie um título de MÓDULO (categoria intermediária que agrupa aulas). "
                . "Exatamente 2 ou 3 palavras. Deve ser o tema/área que engloba as aulas listadas.",
            'training' => "Crie um título de TREINAMENTO (nível mais alto, tema geral). "
                . "Máximo 4 palavras. Deve ser amplo e representar toda a capacitação.",
        };

        $userPrompt = $rules . "\n\nConteúdo: " . $validated['input'];
        if (!empty($validated['context'])) {
            $userPrompt .= "\nContexto: " . $validated['context'];
        }

        $text = $gemini->generateText($systemPrompt, $userPrompt);

        if ($text === null) {
            return response()->json(['error' => 'Não foi possível sugerir o título.'], 422);
        }

        $title = preg_replace('/[""\'".!:;()\n\r]/', '', trim($text));
        return response()->json(['title' => $title]);
    }

    public function generateDescription(Request $request, GeminiService $gemini): JsonResponse
    {
        $company = $request->user()?->company;
        if ($company && !$company->planHasFeature('ai_quiz')) {
            return response()->json(['error' => 'IA está disponível a partir do plano Business.'], 403);
        }

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
