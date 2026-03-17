<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Services\AsaasService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function plans()
    {
        $plans = Plan::where('active', true)->get();
        $currentSubscription = auth()->user()->company->subscription;

        return view('subscription.plans', compact('plans', 'currentSubscription'));
    }

    public function subscribe(Request $request, AsaasService $asaas)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'payment_method' => 'required|in:boleto,pix,credit_card',
        ]);

        $company = auth()->user()->company;
        $plan = Plan::findOrFail($request->plan_id);

        if (!$company->asaas_customer_id) {
            $customerId = $asaas->createCustomer($company, auth()->user()->email);
            if (!$customerId) {
                return back()->with('error', 'Erro ao criar cliente. Tente novamente.');
            }
        }

        $result = $asaas->createSubscription($company, $plan, $request->payment_method);

        if ($result) {
            return redirect()->route('dashboard')
                ->with('success', "Assinatura do plano {$plan->name} ativada!");
        }

        return back()->with('error', 'Erro ao processar pagamento. Tente novamente.');
    }

    public function show()
    {
        $company = auth()->user()->company;
        $subscription = $company->subscription()->with('plan')->first();
        $payments = $subscription?->payments()->latest()->paginate(10) ?? collect();

        return view('subscription.show', compact('subscription', 'payments'));
    }
}
