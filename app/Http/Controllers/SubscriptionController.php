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

    public function updateCard(Request $request, AsaasService $asaas)
    {
        $request->validate([
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

        $subscription = auth()->user()->company->subscription;

        if (!$subscription || !$subscription->asaas_subscription_id) {
            return back()->with('error', 'Nenhuma assinatura ativa encontrada.');
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

        if ($asaas->updateCreditCard($subscription, $cardData)) {
            return back()->with('success', 'Cartão atualizado com sucesso! As próximas cobranças serão feitas no novo cartão.');
        }

        return back()->with('error', 'Erro ao atualizar o cartão. Verifique os dados e tente novamente.');
    }

    public function cancel(AsaasService $asaas)
    {
        $subscription = auth()->user()->company->subscription;

        if (!$subscription) {
            return back()->with('error', 'Nenhuma assinatura encontrada.');
        }

        if ($asaas->cancelSubscription($subscription)) {
            return redirect()->route('subscription.plans')
                ->with('success', 'Assinatura cancelada. Você ainda tem acesso até o fim do período atual.');
        }

        return back()->with('error', 'Erro ao cancelar. Entre em contato com o suporte.');
    }

    public function show()
    {
        $company = auth()->user()->company;
        $subscription = $company->subscription()->with('plan')->first();
        $payments = $subscription?->payments()->latest()->paginate(10) ?? collect();
        $plan = $subscription?->plan;

        // Usage stats
        $usersCount = $company->users()->whereIn('role', ['instructor', 'employee'])->count();
        $trainingsCount = \App\Models\Training::withoutGlobalScopes()->where('company_id', $company->id)->count();
        $certificatesCount = \App\Models\Certificate::withoutGlobalScopes()->where('company_id', $company->id)->count();

        $isOnTrial = $company->isOnTrial();

        $usage = [
            'users' => $usersCount,
            'users_limit' => $isOnTrial ? null : $plan?->max_users,
            'users_pct' => (!$isOnTrial && $plan?->max_users) ? min(100, round(($usersCount / $plan->max_users) * 100)) : 0,
            'trainings' => $trainingsCount,
            'trainings_limit' => $isOnTrial ? null : $plan?->max_trainings,
            'trainings_pct' => (!$isOnTrial && $plan?->max_trainings) ? min(100, round(($trainingsCount / $plan->max_trainings) * 100)) : 0,
            'certificates' => $certificatesCount,
        ];

        return view('subscription.show', compact('subscription', 'payments', 'plan', 'usage'));
    }
}
