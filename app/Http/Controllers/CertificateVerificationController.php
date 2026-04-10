<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;

class CertificateVerificationController extends Controller
{
    public function show()
    {
        return view('certificates.verify');
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|string|max:20']);

        $certificate = Certificate::withoutGlobalScopes()
            ->with(['user', 'training', 'company'])
            ->where('certificate_code', $request->code)
            ->first();

        return view('certificates.verify', compact('certificate'));
    }

    public function showByCode(string $code)
    {
        $certificate = Certificate::withoutGlobalScopes()
            ->with(['user', 'training', 'company'])
            ->where('certificate_code', $code)
            ->firstOrFail();

        return view('certificates.show', compact('certificate'));
    }

    public function ogImage(string $code)
    {
        $certificate = Certificate::withoutGlobalScopes()
            ->with(['user', 'training', 'company'])
            ->where('certificate_code', $code)
            ->firstOrFail();

        $company = $certificate->company;
        $primary = preg_match('/^#[0-9A-Fa-f]{6}$/', $company->primary_color ?? '') ? $company->primary_color : '#4f46e5';
        $secondary = preg_match('/^#[0-9A-Fa-f]{6}$/', $company->secondary_color ?? '') ? $company->secondary_color : '#3730a3';
        $userName = htmlspecialchars($certificate->user->name, ENT_XML1);
        $trainingTitle = htmlspecialchars($certificate->training->title, ENT_XML1);
        $companyName = htmlspecialchars($company->name, ENT_XML1);
        $titleText = htmlspecialchars($company->cert_title_text ?? 'CERTIFICADO', ENT_XML1);
        $date = $certificate->generated_at->format('d/m/Y');

        $svg = <<<SVG
        <svg xmlns="http://www.w3.org/2000/svg" width="1200" height="630" viewBox="0 0 1200 630">
            <rect width="1200" height="630" fill="white"/>
            <rect x="0" y="0" width="40" height="630" fill="{$primary}"/>
            <rect x="16" y="16" width="8" height="598" fill="{$secondary}" opacity="0.3"/>
            <text x="80" y="100" font-family="Helvetica,Arial,sans-serif" font-size="18" font-weight="bold" fill="{$primary}" letter-spacing="1">{$companyName}</text>
            <text x="80" y="200" font-family="Helvetica,Arial,sans-serif" font-size="64" font-weight="bold" fill="{$primary}" letter-spacing="3" opacity="0.85">{$titleText}</text>
            <rect x="80" y="230" width="80" height="4" fill="{$primary}"/>
            <text x="80" y="280" font-family="Helvetica,Arial,sans-serif" font-size="16" fill="#9ca3af" letter-spacing="3">CERTIFICAMOS QUE</text>
            <text x="80" y="340" font-family="Helvetica,Arial,sans-serif" font-size="48" font-weight="bold" fill="#1f2937">{$userName}</text>
            <rect x="80" y="358" width="280" height="3" fill="{$primary}"/>
            <rect x="80" y="390" width="1040" height="80" fill="#f8fafc"/>
            <rect x="80" y="390" width="5" height="80" fill="{$primary}"/>
            <text x="104" y="420" font-family="Helvetica,Arial,sans-serif" font-size="14" fill="#9ca3af" letter-spacing="2">CONCLUIU COM SUCESSO O TREINAMENTO</text>
            <text x="104" y="452" font-family="Helvetica,Arial,sans-serif" font-size="28" font-weight="bold" fill="#1f2937">{$trainingTitle}</text>
            <text x="80" y="540" font-family="Helvetica,Arial,sans-serif" font-size="13" fill="#9ca3af">Emitido em {$date}</text>
            <text x="80" y="560" font-family="Helvetica,Arial,sans-serif" font-size="13" fill="#9ca3af">{$certificate->certificate_code}</text>
            <text x="1120" y="600" font-family="Helvetica,Arial,sans-serif" font-size="14" fill="#d1d5db" text-anchor="end">Verificado por TreinaEdu</text>
        </svg>
        SVG;

        return response($svg, 200)->header('Content-Type', 'image/svg+xml')->header('Cache-Control', 'public, max-age=86400');
    }
}
