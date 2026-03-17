<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::withoutGlobalScopes()
            ->with('subscription.plan')
            ->latest()
            ->paginate(20);

        return view('super-admin.companies.index', compact('companies'));
    }

    public function show(Company $company)
    {
        $company->load(['subscription.plan', 'users']);

        return view('super-admin.companies.show', compact('company'));
    }
}
