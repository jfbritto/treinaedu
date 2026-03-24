<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Training;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CertificateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'user_id' => User::factory(),
            'training_id' => Training::factory(),
            'certificate_code' => Str::upper(Str::random(16)),
            'pdf_path' => 'certificates/test-' . Str::random(8) . '.pdf',
            'generated_at' => now(),
        ];
    }
}
