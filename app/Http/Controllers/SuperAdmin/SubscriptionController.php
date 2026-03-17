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

        return view('super-admin.subscriptions.index', compact('subscriptions'));
    }
}
