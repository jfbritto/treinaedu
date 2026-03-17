<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;

class CertificateController extends Controller
{
    public function index()
    {
        $certificates = Certificate::with(['user', 'training'])
            ->where('company_id', auth()->user()->company_id)
            ->latest()
            ->paginate(15);

        return view('admin.certificates.index', compact('certificates'));
    }
}
