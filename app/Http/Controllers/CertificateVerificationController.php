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
}
