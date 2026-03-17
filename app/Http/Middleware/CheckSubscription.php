<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || $user->isSuperAdmin()) {
            return $next($request);
        }

        $company = $user->company;

        if (!$company || !$company->hasActiveSubscription()) {
            return redirect()->route('subscription.plans')
                ->with('warning', 'Sua assinatura expirou. Escolha um plano para continuar.');
        }

        return $next($request);
    }
}
