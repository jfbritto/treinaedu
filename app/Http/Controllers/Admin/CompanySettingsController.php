<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CompanySettingsController extends Controller
{
    public function edit()
    {
        $company = auth()->user()->company;
        return view('admin.settings.edit', compact('company'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'primary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $company = auth()->user()->company;

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store("public/logos/{$company->id}");
            $company->logo_path = str_replace('public/', 'storage/', $path);
        }

        $company->primary_color = $request->primary_color;
        $company->secondary_color = $request->secondary_color;
        $company->save();

        // Invalidate theme cache so InjectCompanyTheme picks up new colors
        Cache::forget("company_theme_{$company->id}");

        return back()->with('success', 'Configurações atualizadas.');
    }
}
