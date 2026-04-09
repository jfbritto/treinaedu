<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Training;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificateService
{
    public function canGenerate(User $user, Training $training): bool
    {
        $view = $user->trainingViews()
            ->where('training_id', $training->id)
            ->whereNotNull('completed_at')
            ->first();

        if (!$view) {
            return false;
        }

        // Check ALL quizzes (module-level + training-level)
        $quizzes = $training->quizzes()->get();
        foreach ($quizzes as $quiz) {
            $passed = $user->quizAttempts()
                ->whereHas('quiz', fn ($q) => $q->where('id', $quiz->id))
                ->where('passed', true)
                ->exists();
            if (!$passed) return false;
        }

        return true;
    }

    public function generate(User $user, Training $training): Certificate
    {
        $existing = Certificate::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->where('training_id', $training->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        $code = $this->generateUniqueCode();
        $company = $user->company;
        $modules = $training->modules()->with('lessons', 'quiz')->orderBy('sort_order')->get();

        $primaryColor = $this->safeColor($company->primary_color ?? null, '#4f46e5');
        $secondaryColor = $this->safeColor($company->secondary_color ?? null, '#3730a3');

        $verifyUrl = route('certificate.verify') . '?code=' . $code;
        $qrCodeDataUri = $this->fetchQrCodeDataUri($verifyUrl, $primaryColor);

        $pdf = Pdf::loadView('certificates.template', [
            'userName' => $user->name,
            'trainingTitle' => $training->title,
            'durationMinutes' => $training->calculatedDuration(),
            'completionDate' => now()->locale('pt_BR')->translatedFormat('d \d\e F \d\e Y'),
            'companyName' => $company->name,
            'companyLogo' => $company->logo_path,
            'certificateCode' => $code,
            'modules' => $modules,
            'primaryColor' => $primaryColor,
            'secondaryColor' => $secondaryColor,
            'qrCodeDataUri' => $qrCodeDataUri,
            'verifyUrl' => $verifyUrl,
        ])->setPaper('a4', 'landscape');

        $directory = "certificates/{$company->id}";
        $filename = "{$code}.pdf";
        $path = "{$directory}/{$filename}";

        Storage::disk('app')->put($path, $pdf->output());

        try {
            return Certificate::create([
                'company_id' => $company->id,
                'user_id' => $user->id,
                'training_id' => $training->id,
                'certificate_code' => $code,
                'pdf_path' => $path,
                'generated_at' => now(),
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle duplicate key (race condition) — return existing
            return Certificate::withoutGlobalScopes()
                ->where('user_id', $user->id)
                ->where('training_id', $training->id)
                ->firstOrFail();
        }
    }

    /**
     * Regenerate the PDF file for an existing certificate (keeps the same code).
     * Useful when the template changes or the file was lost.
     */
    public function regeneratePdf(Certificate $certificate): void
    {
        $user = $certificate->user;
        $training = $certificate->training;
        $company = $certificate->company;
        $modules = $training->modules()->with('lessons', 'quiz')->orderBy('sort_order')->get();

        $primaryColor = $this->safeColor($company->primary_color ?? null, '#4f46e5');
        $secondaryColor = $this->safeColor($company->secondary_color ?? null, '#3730a3');

        $verifyUrl = route('certificate.verify') . '?code=' . $certificate->certificate_code;
        $qrCodeDataUri = $this->fetchQrCodeDataUri($verifyUrl, $primaryColor);

        $pdf = Pdf::loadView('certificates.template', [
            'userName' => $user->name,
            'trainingTitle' => $training->title,
            'durationMinutes' => $training->calculatedDuration(),
            'completionDate' => $certificate->generated_at->locale('pt_BR')->translatedFormat('d \d\e F \d\e Y'),
            'companyName' => $company->name,
            'companyLogo' => $company->logo_path,
            'certificateCode' => $certificate->certificate_code,
            'modules' => $modules,
            'primaryColor' => $primaryColor,
            'secondaryColor' => $secondaryColor,
            'qrCodeDataUri' => $qrCodeDataUri,
            'verifyUrl' => $verifyUrl,
        ])->setPaper('a4', 'landscape');

        Storage::disk('app')->put($certificate->pdf_path, $pdf->output());
    }

    private function generateUniqueCode(): string
    {
        do {
            $code = 'TH-' . date('Y') . '-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4));
        } while (Certificate::withoutGlobalScopes()->where('certificate_code', $code)->exists());

        return $code;
    }

    private function safeColor(?string $color, string $fallback): string
    {
        return (is_string($color) && preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) ? $color : $fallback;
    }

    /**
     * Fetch a QR code PNG from the public qrserver API and return it as a
     * base64 data URI so it can be embedded directly in the PDF without
     * DomPDF needing remote image access. Returns null on failure.
     */
    private function fetchQrCodeDataUri(string $verifyUrl, string $primaryColor): ?string
    {
        try {
            $hex = ltrim($primaryColor, '#');
            $apiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&margin=0'
                . '&color=' . $hex
                . '&data=' . urlencode($verifyUrl);

            $ctx = stream_context_create([
                'http' => ['timeout' => 5, 'ignore_errors' => true],
                'https' => ['timeout' => 5, 'ignore_errors' => true],
            ]);

            $png = @file_get_contents($apiUrl, false, $ctx);
            if ($png === false || strlen($png) < 100) {
                return null;
            }

            return 'data:image/png;base64,' . base64_encode($png);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
