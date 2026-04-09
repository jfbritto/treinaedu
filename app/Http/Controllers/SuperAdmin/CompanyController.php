<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Subscription;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::withoutGlobalScopes()
            ->with('subscription.plan')
            ->latest()
            ->paginate(20);

        $stats = [
            'total' => Company::withoutGlobalScopes()->count(),
            'active' => Subscription::withoutGlobalScopes()->where('status', 'active')->count(),
            'trial' => Subscription::withoutGlobalScopes()->where('status', 'trial')->count(),
        ];

        return view('super-admin.companies.index', compact('companies', 'stats'));
    }

    public function show(Company $company)
    {
        $company->load(['subscription.plan', 'users']);

        $companyStats = [
            'users' => $company->users->count(),
            'trainings' => \App\Models\Training::withoutGlobalScopes()->where('company_id', $company->id)->count(),
            'certificates' => \App\Models\Certificate::withoutGlobalScopes()->where('company_id', $company->id)->count(),
        ];

        return view('super-admin.companies.show', compact('company', 'companyStats'));
    }
}
