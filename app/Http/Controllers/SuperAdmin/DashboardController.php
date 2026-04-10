<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Payment;
use App\Models\Subscription;

class DashboardController extends Controller
{
    public function index()
    {
        $metrics = [
            'total_companies' => Company::withoutGlobalScopes()->count(),
            'active_subscriptions' => Subscription::withoutGlobalScopes()
                ->where('status', 'active')->count(),
            'monthly_revenue' => Payment::withoutGlobalScopes()
                ->where('status', 'confirmed')
                ->whereMonth('paid_at', now()->month)
                ->sum('amount'),
            'trial_companies' => Subscription::withoutGlobalScopes()
                ->where('status', 'trial')->count(),
        ];

        return view('super-admin.dashboard', compact('metrics'));
    }
}
