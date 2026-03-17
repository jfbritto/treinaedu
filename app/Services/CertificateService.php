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

        if ($training->has_quiz) {
            $passed = $user->quizAttempts()
                ->whereHas('quiz', fn ($q) => $q->where('training_id', $training->id))
                ->where('passed', true)
                ->exists();

            if (!$passed) {
                return false;
            }
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

        $pdf = Pdf::loadView('certificates.template', [
            'userName' => $user->name,
            'trainingTitle' => $training->title,
            'durationMinutes' => $training->duration_minutes,
            'completionDate' => now()->format('d/m/Y'),
            'companyName' => $company->name,
            'companyLogo' => $company->logo_path,
            'certificateCode' => $code,
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

    private function generateUniqueCode(): string
    {
        do {
            $code = 'TH-' . date('Y') . '-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4));
        } while (Certificate::withoutGlobalScopes()->where('certificate_code', $code)->exists());

        return $code;
    }
}
