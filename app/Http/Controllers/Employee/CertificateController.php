<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Training;
use App\Services\CertificateService;

class CertificateController extends Controller
{
    public function index()
    {
        $certificates = auth()->user()->certificates()
            ->with('training')
            ->latest()
            ->paginate(15);

        return view('employee.certificates.index', compact('certificates'));
    }

    public function generate(Training $training, CertificateService $service)
    {
        $user = auth()->user();

        if (!$service->canGenerate($user, $training)) {
            return back()->with('error', 'Você não pode gerar este certificado ainda.');
        }

        $certificate = $service->generate($user, $training);

        return redirect()->route('employee.certificates.success', $certificate);
    }

    public function success(Certificate $certificate)
    {
        if ($certificate->user_id !== auth()->id()) {
            abort(403);
        }

        return view('employee.certificates.success', compact('certificate'));
    }

    public function show(Certificate $certificate)
    {
        if ($certificate->user_id !== auth()->id()) {
            abort(403);
        }

        return view('employee.certificates.show', compact('certificate'));
    }

    public function download(Certificate $certificate, CertificateService $service)
    {
        if ($certificate->user_id !== auth()->id()) {
            abort(403);
        }

        $path = storage_path("app/{$certificate->pdf_path}");

        // Regenerate when:
        //  - file is missing
        //  - user explicitly requested ?refresh=1
        //  - cached PDF is older than the template blade (means template was updated)
        $templatePath = resource_path('views/certificates/template.blade.php');
        $needsRegen = !file_exists($path)
            || request()->boolean('refresh')
            || (file_exists($templatePath) && filemtime($templatePath) > filemtime($path));

        if ($needsRegen) {
            $service->regeneratePdf($certificate);
        }

        if (!file_exists($path)) {
            abort(404, 'Arquivo não encontrado.');
        }

        return response()->download($path);
    }
}
