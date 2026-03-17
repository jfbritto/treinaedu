<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $slug = Str::slug($request->company_name);
        $originalSlug = $slug;
        $counter = 1;
        while (Company::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        $company = Company::create([
            'name' => $request->company_name,
            'slug' => $slug,
        ]);

        $basicPlan = Plan::where('name', 'Basic')->first();

        Subscription::create([
            'company_id' => $company->id,
            'plan_id' => $basicPlan->id,
            'status' => 'trial',
            'trial_ends_at' => now()->addDays(7),
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => $company->id,
            'role' => 'admin',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard'));
    }
}
