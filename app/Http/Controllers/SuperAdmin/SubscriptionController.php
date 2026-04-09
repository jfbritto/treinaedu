<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::withoutGlobalScopes()
            ->with(['company', 'plan'])
            ->latest()
            ->paginate(20);

        $stats = [
            'total' => Subscription::withoutGlobalScopes()->count(),
            'active' => Subscription::withoutGlobalScopes()->where('status', 'active')->count(),
            'trial' => Subscription::withoutGlobalScopes()->where('status', 'trial')->count(),
            'past_due' => Subscription::withoutGlobalScopes()->where('status', 'past_due')->count(),
        ];

        return view('super-admin.subscriptions.index', compact('subscriptions', 'stats'));
    }
}
