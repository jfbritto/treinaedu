<?php

namespace Database\Seeders;

use App\Models\Certificate;
use App\Models\Company;
use App\Models\Group;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\TrainingAssignment;
use App\Models\TrainingView;
use App\Models\User;
use App\Services\CertificateService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Planos
        $this->call(PlanSeeder::class);

        $plan = Plan::where('name', 'Pro')->first();

        // Empresa demo
        $company = Company::firstOrCreate(
            ['slug' => 'acme-corp'],
            ['name' => 'Acme Corp']
        );

        // Subscription
        Subscription::firstOrCreate(
            ['company_id' => $company->id],
            ['plan_id' => $plan->id, 'status' => 'active']
        );

        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@acme.com'],
            [
                'name'       => 'Carlos Admin',
                'password'   => 'password',
                'company_id' => $company->id,
                'role'       => 'admin',
                'active'     => true,
            ]
        );

        // Instrutor
        $instructor = User::firstOrCreate(
            ['email' => 'instructor@acme.com'],
            [
                'name'       => 'Ana Instrutora',
                'password'   => 'password',
                'company_id' => $company->id,
                'role'       => 'instructor',
                'active'     => true,
            ]
        );

        // 10 colaboradores
        $employees = [];
        $employeeData = [
            ['name' => 'João Silva',      'email' => 'joao@acme.com'],
            ['name' => 'Maria Santos',    'email' => 'maria@acme.com'],
            ['name' => 'Pedro Oliveira',  'email' => 'pedro@acme.com'],
            ['name' => 'Lucia Fernandes', 'email' => 'lucia@acme.com'],
            ['name' => 'Bruno Costa',     'email' => 'bruno@acme.com'],
            ['name' => 'Carla Mendes',    'email' => 'carla@acme.com'],
            ['name' => 'Rafael Alves',    'email' => 'rafael@acme.com'],
            ['name' => 'Fernanda Lima',   'email' => 'fernanda@acme.com'],
            ['name' => 'Gustavo Rocha',   'email' => 'gustavo@acme.com'],
            ['name' => 'Tatiane Souza',   'email' => 'tatiane@acme.com'],
        ];

        foreach ($employeeData as $i => $data) {
            // Últimos 2 colaboradores ficam como "pendentes" (aceitaram o convite recentemente)
            $isPending = $i >= 8;
            $hasLoggedIn = !$isPending && $i < 7;

            $employees[] = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'          => $data['name'],
                    'password'      => 'password',
                    'company_id'    => $company->id,
                    'role'          => 'employee',
                    'active'        => true,
                    'created_at'    => now()->subDays(rand(1, 60)),
                    'invited_at'    => $isPending ? now()->subDays(rand(1, 5)) : null,
                    'last_login_at' => $hasLoggedIn ? now()->subDays(rand(0, 30)) : null,
                ]
            );
        }

        // 2 grupos
        $grupoGeral = Group::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Geral'],
            ['description' => 'Todos os colaboradores']
        );
        $grupoVendas = Group::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Vendas'],
            ['description' => 'Equipe de vendas']
        );

        // Associar colaboradores aos grupos
        $grupoGeral->users()->syncWithoutDetaching(collect($employees)->pluck('id')->all());
        $grupoVendas->users()->syncWithoutDetaching(collect($employees)->take(5)->pluck('id')->all());

        // 5 treinamentos
        $trainingsData = [
            ['title' => 'Boas-vindas à Acme Corp',       'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => 15],
            ['title' => 'Segurança no Trabalho',          'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => 30],
            ['title' => 'Atendimento ao Cliente',         'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => 45],
            ['title' => 'Uso de Ferramentas Internas',    'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => 20],
            ['title' => 'Compliance e Ética Corporativa', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration' => 60],
        ];

        $trainings = [];
        foreach ($trainingsData as $data) {
            $trainings[] = Training::firstOrCreate(
                ['company_id' => $company->id, 'title' => $data['title']],
                [
                    'created_by'       => $instructor->id,
                    'description'      => 'Treinamento de demonstração.',
                    'video_url'        => $data['url'],
                    'video_provider'   => 'youtube',
                    'duration_minutes' => $data['duration'],
                    'passing_score'    => 70,
                    'has_quiz'         => false,
                    'active'           => true,
                ]
            );
        }

        // Atribuir treinamentos a grupos
        foreach ($trainings as $training) {
            TrainingAssignment::firstOrCreate([
                'company_id'  => $company->id,
                'training_id' => $training->id,
                'group_id'    => $grupoGeral->id,
            ], ['due_date' => now()->addDays(30)]);
        }

        TrainingAssignment::firstOrCreate([
            'company_id'  => $company->id,
            'training_id' => $trainings[2]->id, // Atendimento ao Cliente
            'group_id'    => $grupoVendas->id,
        ], ['due_date' => now()->addDays(15)]);

        // Gerar views e conclusões
        // Treinamento 0 (Boas-vindas): todos concluíram
        foreach ($employees as $emp) {
            TrainingView::firstOrCreate(
                ['company_id' => $company->id, 'training_id' => $trainings[0]->id, 'user_id' => $emp->id],
                ['completed_at' => now()->subDays(rand(5, 40)), 'progress_percent' => 100]
            );
        }

        // Treinamento 1 (Segurança): 7 de 10 concluíram
        foreach (array_slice($employees, 0, 7) as $emp) {
            TrainingView::firstOrCreate(
                ['company_id' => $company->id, 'training_id' => $trainings[1]->id, 'user_id' => $emp->id],
                ['completed_at' => now()->subDays(rand(2, 30)), 'progress_percent' => 100]
            );
        }
        foreach (array_slice($employees, 7, 3) as $emp) {
            TrainingView::firstOrCreate(
                ['company_id' => $company->id, 'training_id' => $trainings[1]->id, 'user_id' => $emp->id],
                ['completed_at' => null, 'progress_percent' => rand(20, 70)]
            );
        }

        // Treinamento 2 (Atendimento): 4 de 10 concluíram
        foreach (array_slice($employees, 0, 4) as $emp) {
            TrainingView::firstOrCreate(
                ['company_id' => $company->id, 'training_id' => $trainings[2]->id, 'user_id' => $emp->id],
                ['completed_at' => now()->subDays(rand(1, 20)), 'progress_percent' => 100]
            );
        }
        foreach (array_slice($employees, 4, 4) as $emp) {
            TrainingView::firstOrCreate(
                ['company_id' => $company->id, 'training_id' => $trainings[2]->id, 'user_id' => $emp->id],
                ['completed_at' => null, 'progress_percent' => rand(10, 60)]
            );
        }

        // Treinamento 3 (Ferramentas): 2 em andamento, nenhum concluído
        foreach (array_slice($employees, 0, 2) as $emp) {
            TrainingView::firstOrCreate(
                ['company_id' => $company->id, 'training_id' => $trainings[3]->id, 'user_id' => $emp->id],
                ['completed_at' => null, 'progress_percent' => rand(10, 50)]
            );
        }

        // Treinamento 4 (Compliance): nenhuma view ainda

        // Certificados para quem concluiu Boas-vindas (gera PDFs reais)
        $certService = app(CertificateService::class);
        foreach (array_slice($employees, 0, 6) as $emp) {
            Certificate::where('company_id', $company->id)
                ->where('user_id', $emp->id)
                ->where('training_id', $trainings[0]->id)
                ->delete();
            $certService->generate($emp, $trainings[0]);
        }

        $this->command->info('Demo data created!');
        $this->command->info('Admin login: admin@acme.com / password');
    }
}
