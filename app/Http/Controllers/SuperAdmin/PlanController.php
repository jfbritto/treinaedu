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
        ]);

        Plan::create($request->only(['name', 'price', 'max_users', 'max_trainings']));

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

        return redirect()->route('super.plans.index')
            ->with('success', 'Plano atualizado!');
    }
}
