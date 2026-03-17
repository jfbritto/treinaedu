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

        return view('super-admin.payments.index', compact('payments'));
    }
}
