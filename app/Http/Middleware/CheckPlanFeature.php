<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPlanFeature
{
    private const FEATURE_LABELS = [
        'ai_quiz' => 'Quiz com IA',
        'learning_paths' => 'Trilhas de Aprendizagem',
        'engagement' => 'Engajamento e Desafios',
        'export_reports' => 'Exportação de Relatórios',
    ];

    private const FEATURE_MIN_PLAN = [
        'ai_quiz' => 'Business',
        'learning_paths' => 'Business',
        'engagement' => 'Professional',
        'export_reports' => 'Business',
    ];

    public function handle(Request $request, Closure $next, string $feature)
    {
        $user = $request->user();

        // Super admin bypasses all checks
        if ($user?->isSuperAdmin()) {
            return $next($request);
        }

        $company = $user?->company;

        if ($company && $company->planHasFeature($feature)) {
            return $next($request);
        }

        $label = self::FEATURE_LABELS[$feature] ?? $feature;
        $minPlan = self::FEATURE_MIN_PLAN[$feature] ?? 'superior';

        // JSON response for API routes
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'error' => "O recurso \"{$label}\" está disponível a partir do plano {$minPlan}.",
                'upgrade_url' => route('subscription.plans'),
            ], 403);
        }

        return redirect()->route('subscription.plans')
            ->with('error', "O recurso \"{$label}\" está disponível a partir do plano {$minPlan}. Faça upgrade para desbloquear.");
    }
}
