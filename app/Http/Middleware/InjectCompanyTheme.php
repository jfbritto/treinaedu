<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class InjectCompanyTheme
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->company_id) {
            // super_admin não tem company, company_id pode ser null
            $company = Cache::remember(
                "company_theme_{$user->company_id}",
                3600,
                fn () => $user->company
            );

            if ($company) {
                view()->share('currentCompany', $company);
                view()->share('primaryColor', $company->primary_color ?? '#3B82F6');
                view()->share('secondaryColor', $company->secondary_color ?? '#1E40AF');
                view()->share('companyLogo', $company->logo_path);
            }
        }

        return $next($request);
    }
}
