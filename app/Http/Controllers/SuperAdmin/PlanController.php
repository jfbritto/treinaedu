<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::withoutGlobalScopes()->latest()->get();

        return view('super-admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('super-admin.plans.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'max_users' => 'nullable|integer|min:1',
            'max_trainings' => 'nullable|integer|min:1',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        // Custom plans get all features by default
        $data = $request->only(['name', 'price', 'max_users', 'max_trainings', 'company_id']);
        if ($request->company_id) {
            $data['features'] = ['certificates', 'basic_reports', 'ai_quiz', 'learning_paths', 'export_reports', 'engagement'];
        }

        $plan = Plan::create($data);

        if ($request->company_id) {
            return redirect()->route('super.companies.show', $request->company_id)
                ->with('success', "Plano personalizado \"{$plan->name}\" criado!");
        }

        return redirect()->route('super.plans.index')
            ->with('success', 'Plano criado!');
    }

    public function edit(Plan $plan)
    {
        return view('super-admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'max_users' => 'nullable|integer|min:1',
            'max_trainings' => 'nullable|integer|min:1',
            'active' => 'boolean',
        ]);

        $plan->update($request->only(['name', 'price', 'max_users', 'max_trainings', 'active']));

        if ($plan->company_id) {
            return redirect()->route('super.companies.show', $plan->company_id)
                ->with('success', 'Plano personalizado atualizado!');
        }

        return redirect()->route('super.plans.index')
            ->with('success', 'Plano atualizado!');
    }

    public function destroy(Plan $plan)
    {
        $companyId = $plan->company_id;

        if ($plan->subscriptions()->exists()) {
            return back()->with('error', 'Não é possível remover um plano com assinaturas ativas.');
        }

        $plan->delete();

        if ($companyId) {
            return redirect()->route('super.companies.show', $companyId)
                ->with('success', 'Plano personalizado removido.');
        }

        return redirect()->route('super.plans.index')->with('success', 'Plano removido.');
    }
}
