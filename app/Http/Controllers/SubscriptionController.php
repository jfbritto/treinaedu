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
            'holder_name' => 'required|string|max:255',
            'card_number' => 'required|string|min:13|max:19',
            'expiry_month' => 'required|string|size:2',
            'expiry_year' => 'required|string|size:4',
            'ccv' => 'required|string|min:3|max:4',
            'cpf_cnpj' => 'required|string|min:11|max:18',
            'phone' => 'required|string|min:10|max:15',
            'postal_code' => 'required|string|min:8|max:9',
            'address_number' => 'required|string|max:10',
        ]);

        $company = auth()->user()->company;
        $plan = Plan::findOrFail($request->plan_id);

        // Create Asaas customer if needed
        if (!$company->asaas_customer_id) {
            $customerId = $asaas->createCustomer($company, auth()->user()->email);
            if (!$customerId) {
                return back()->withInput()->with('error', 'Erro ao criar cliente. Tente novamente.');
            }
        }

        $cardData = [
            'holder_name' => $request->holder_name,
            'number' => $request->card_number,
            'expiry_month' => $request->expiry_month,
            'expiry_year' => $request->expiry_year,
            'ccv' => $request->ccv,
            'holder_email' => auth()->user()->email,
            'cpf_cnpj' => $request->cpf_cnpj,
            'phone' => $request->phone,
            'postal_code' => $request->postal_code,
            'address_number' => $request->address_number,
        ];

        $result = $asaas->createSubscription($company, $plan, $cardData);

        if ($result) {
            return redirect()->route('dashboard')
                ->with('success', "Assinatura do plano {$plan->name} ativada com sucesso!");
        }

        return back()->withInput()->with('error', 'Erro ao processar pagamento. Verifique os dados do cartão e tente novamente.');
    }

    public function show()
    {
        $company = auth()->user()->company;
        $subscription = $company->subscription()->with('plan')->first();
        $payments = $subscription?->payments()->latest()->paginate(10) ?? collect();

        return view('subscription.show', compact('subscription', 'payments'));
    }
}
