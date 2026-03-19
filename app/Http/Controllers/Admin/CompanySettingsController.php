<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

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
            'name'            => 'required|string|max:255',
            'logo'            => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'primary_color'   => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $company = auth()->user()->company;

        $company->name = $request->name;

        if ($request->boolean('remove_logo')) {
            if ($company->logo_path) {
                Storage::disk('public')->delete($company->logo_path);
            }
            $company->logo_path = null;
        } elseif ($request->hasFile('logo')) {
            if ($company->logo_path) {
                Storage::disk('public')->delete($company->logo_path);
            }
            $company->logo_path = $request->file('logo')->store("logos/{$company->id}", 'public');
        }

        $company->primary_color   = $request->primary_color;
        $company->secondary_color = $request->secondary_color;
        $company->save();

        Cache::forget("company_theme_{$company->id}");

        return back()->with('success', 'Configurações atualizadas.');
    }
}
