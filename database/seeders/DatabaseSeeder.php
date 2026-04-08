<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Essenciais (sempre executados, inclusive em produção)
        $this->call([
            PlanSeeder::class,
            SuperAdminSeeder::class,
        ]);

        // Dados de demonstração apenas em ambientes não-produção
        if (!app()->environment('production')) {
            $this->call(DemoSeeder::class);
        }
    }
}
