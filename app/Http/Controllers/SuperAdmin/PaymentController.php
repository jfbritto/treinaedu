<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::withoutGlobalScopes()
            ->with(['company', 'subscription.plan'])
            ->latest()
            ->paginate(20);

        $stats = [
            'total_revenue' => Payment::withoutGlobalScopes()->where('status', 'confirmed')->sum('amount'),
            'this_month' => Payment::withoutGlobalScopes()->where('status', 'confirmed')->whereMonth('paid_at', now()->month)->whereYear('paid_at', now()->year)->sum('amount'),
            'pending' => Payment::withoutGlobalScopes()->where('status', 'pending')->count(),
            'confirmed' => Payment::withoutGlobalScopes()->where('status', 'confirmed')->count(),
        ];

        return view('super-admin.payments.index', compact('payments', 'stats'));
    }
}
