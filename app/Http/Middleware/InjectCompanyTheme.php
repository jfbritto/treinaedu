<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InjectCompanyTheme
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->company) {
            $company = $user->company;
            view()->share('currentCompany', $company);
            view()->share('primaryColor', $company->primary_color ?? '#3B82F6');
            view()->share('secondaryColor', $company->secondary_color ?? '#1E40AF');
            view()->share('companyLogo', $company->logo_path);
        }

        return $next($request);
    }
}
