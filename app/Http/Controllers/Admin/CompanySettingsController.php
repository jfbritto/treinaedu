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
            'cert_signer_name'     => 'nullable|string|max:255',
            'cert_signer_role'     => 'nullable|string|max:255',
            'cert_signer_registry' => 'nullable|string|max:255',
            'signature'            => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
            'signature_drawn'      => 'nullable|string',
            'cert_border_style'    => 'nullable|string|in:classic,simple,none',
            'cert_title_text'      => 'nullable|string|max:100',
            'cert_subtitle_text'   => 'nullable|string|max:100',
            'cert_size_title'      => 'nullable|integer|min:30|max:72',
            'cert_size_name'       => 'nullable|integer|min:20|max:50',
            'cert_size_training'   => 'nullable|integer|min:14|max:32',
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

        // Certificate customization
        $company->cert_border_style  = $request->cert_border_style ?? 'classic';
        $company->cert_title_text    = $request->cert_title_text ?: 'CERTIFICADO';
        $company->cert_subtitle_text = $request->cert_subtitle_text ?: 'de Conclusão';

        // Certificate sizes
        $company->cert_size_title    = $request->cert_size_title ?? 54;
        $company->cert_size_name     = $request->cert_size_name ?? 34;
        $company->cert_size_training = $request->cert_size_training ?? 20;

        // Certificate signer
        $company->cert_signer_name     = $request->cert_signer_name;
        $company->cert_signer_role     = $request->cert_signer_role;
        $company->cert_signer_registry = $request->cert_signer_registry;

        if ($request->boolean('remove_signature')) {
            if ($company->cert_signer_signature_path) {
                Storage::disk('public')->delete($company->cert_signer_signature_path);
            }
            $company->cert_signer_signature_path = null;
        } elseif ($request->hasFile('signature')) {
            if ($company->cert_signer_signature_path) {
                Storage::disk('public')->delete($company->cert_signer_signature_path);
            }
            $company->cert_signer_signature_path = $request->file('signature')->store("signatures/{$company->id}", 'public');
        } elseif ($request->filled('signature_drawn')) {
            // Save drawn signature from base64 data URL
            $dataUrl = $request->signature_drawn;
            if (preg_match('/^data:image\/png;base64,/', $dataUrl)) {
                $imageData = base64_decode(preg_replace('/^data:image\/png;base64,/', '', $dataUrl));
                if ($imageData && strlen($imageData) > 100) {
                    if ($company->cert_signer_signature_path) {
                        Storage::disk('public')->delete($company->cert_signer_signature_path);
                    }
                    $path = "signatures/{$company->id}/" . uniqid('sig_') . '.png';
                    Storage::disk('public')->put($path, $imageData);
                    $company->cert_signer_signature_path = $path;
                }
            }
        }

        $company->save();

        Cache::forget("company_theme_{$company->id}");

        return back()->with('success', 'Configurações atualizadas.');
    }
}
