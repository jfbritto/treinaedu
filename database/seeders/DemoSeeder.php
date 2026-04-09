<?php

namespace Database\Seeders;

use App\Models\Certificate;
use App\Models\Company;
use App\Models\Group;
use App\Models\Path;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\TrainingAssignment;
use App\Models\TrainingLesson;
use App\Models\TrainingModule;
use App\Models\TrainingView;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(PlanSeeder::class);

        $plans = [
            'Starter' => Plan::where('name', 'Starter')->first(),
            'Business' => Plan::where('name', 'Business')->first(),
            'Professional' => Plan::where('name', 'Professional')->first(),
        ];

        foreach ($plans as $planName => $plan) {
            if (!$plan) {
                $this->command->warn("Plano {$planName} não encontrado, pulando...");
                continue;
            }

            $slug = strtolower($planName);
            $this->createCompanyWithData($plan, $planName, $slug);
        }

        $this->command->info('');
        $this->command->info('=== CREDENCIAIS DE ACESSO ===');
        $this->command->info('');
        $this->command->info('STARTER:      admin@starter.com / password');
        $this->command->info('BUSINESS:     admin@business.com / password');
        $this->command->info('PROFESSIONAL: admin@professional.com / password');
        $this->command->info('SUPER ADMIN:  admin@treinaedu.com.br / password');
        $this->command->info('');
        $this->command->info('Colaboradores: [nome]@[plano].com / password');
    }

    private function createCompanyWithData(Plan $plan, string $planName, string $slug): void
    {
        $companyNames = [
            'Starter' => 'TechStart Ltda',
            'Business' => 'Nexus Corporativo',
            'Professional' => 'GlobalPro Enterprises',
        ];

        // Company
        $company = Company::firstOrCreate(
            ['slug' => $slug],
            [
                'name' => $companyNames[$planName],
                'primary_color' => match($planName) {
                    'Starter' => '#3B82F6',
                    'Business' => '#8B5CF6',
                    'Professional' => '#059669',
                },
                'secondary_color' => match($planName) {
                    'Starter' => '#1E40AF',
                    'Business' => '#6D28D9',
                    'Professional' => '#047857',
                },
            ]
        );

        // Subscription
        Subscription::firstOrCreate(
            ['company_id' => $company->id],
            ['plan_id' => $plan->id, 'status' => 'active', 'current_period_start' => now(), 'current_period_end' => now()->addMonth()]
        );

        // Admin
        $admin = User::firstOrCreate(
            ['email' => "admin@{$slug}.com"],
            [
                'name' => "Admin {$planName}",
                'password' => 'password',
                'company_id' => $company->id,
                'role' => 'admin',
                'active' => true,
            ]
        );

        // Instructor
        $instructor = User::firstOrCreate(
            ['email' => "instrutor@{$slug}.com"],
            [
                'name' => "Instrutor {$planName}",
                'password' => 'password',
                'company_id' => $company->id,
                'role' => 'instructor',
                'active' => true,
            ]
        );

        // 10 employees
        $names = ['Ana Silva', 'Bruno Costa', 'Carla Mendes', 'Diego Souza', 'Elena Rocha', 'Felipe Lima', 'Gabi Santos', 'Hugo Ferreira', 'Iris Oliveira', 'Jorge Alves'];
        $employees = [];
        foreach ($names as $i => $name) {
            $firstName = strtolower(explode(' ', $name)[0]);
            $employees[] = User::firstOrCreate(
                ['email' => "{$firstName}@{$slug}.com"],
                [
                    'name' => $name,
                    'password' => 'password',
                    'company_id' => $company->id,
                    'role' => 'employee',
                    'active' => true,
                    'created_at' => now()->subDays(rand(5, 60)),
                    'last_login_at' => $i < 7 ? now()->subDays(rand(0, 15)) : null,
                ]
            );
        }

        // Groups
        $grupoGeral = Group::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Geral'],
            ['description' => 'Todos os colaboradores']
        );
        $grupoGeral->users()->syncWithoutDetaching(collect($employees)->pluck('id'));

        $grupoVendas = Group::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Vendas'],
            ['description' => 'Equipe comercial']
        );
        $grupoVendas->users()->syncWithoutDetaching(collect($employees)->take(5)->pluck('id'));

        // Trainings with real modules and lessons
        $trainings = $this->createTrainings($company, $instructor, $admin);

        // Assign to groups
        foreach ($trainings as $training) {
            TrainingAssignment::firstOrCreate([
                'company_id' => $company->id,
                'training_id' => $training->id,
                'group_id' => $grupoGeral->id,
            ], ['due_date' => now()->addDays(30)]);
        }

        // Create views/progress
        $this->createProgress($company, $trainings, $employees);

        // Paths (only if plan supports)
        if ($plan->hasFeature('learning_paths') && count($trainings) >= 3) {
            $path = Path::firstOrCreate(
                ['company_id' => $company->id, 'title' => 'Trilha de Onboarding'],
                ['description' => 'Jornada inicial para novos colaboradores', 'active' => true, 'sort_order' => 0]
            );
            $path->trainings()->syncWithoutDetaching([$trainings[0]->id, $trainings[1]->id, $trainings[2]->id]);
        }

        $this->command->info("[{$planName}] {$companyNames[$planName]} criada com " . count($trainings) . " treinamentos e " . count($employees) . " colaboradores.");
    }

    private function createTrainings(Company $company, User $instructor, User $admin): array
    {
        $trainingsData = [
            [
                'title' => 'Onboarding - Boas-vindas',
                'description' => 'Conheça a empresa, nossa cultura e valores.',
                'modules' => [
                    ['title' => 'Sobre a Empresa', 'lessons' => [
                        ['title' => 'Nossa História', 'type' => 'video', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration_minutes' => 10],
                        ['title' => 'Missão e Valores', 'type' => 'text', 'content' => 'Nossa missão é transformar a educação corporativa através da tecnologia, tornando o aprendizado acessível, engajante e mensurável para todas as empresas.'],
                    ]],
                    ['title' => 'Primeiros Passos', 'lessons' => [
                        ['title' => 'Como usar o sistema', 'type' => 'video', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration_minutes' => 8],
                    ]],
                ],
            ],
            [
                'title' => 'Segurança do Trabalho',
                'description' => 'Normas e procedimentos de segurança essenciais.',
                'modules' => [
                    ['title' => 'NR Básicas', 'lessons' => [
                        ['title' => 'Introdução às NRs', 'type' => 'video', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration_minutes' => 15],
                        ['title' => 'EPIs obrigatórios', 'type' => 'text', 'content' => 'Equipamentos de Proteção Individual são obrigatórios em todas as áreas de risco. Capacete, óculos, luvas, botas e protetores auriculares devem ser utilizados conforme a atividade.'],
                        ['title' => 'Procedimentos de emergência', 'type' => 'video', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration_minutes' => 12],
                    ]],
                ],
            ],
            [
                'title' => 'Atendimento ao Cliente',
                'description' => 'Técnicas para um atendimento de excelência.',
                'modules' => [
                    ['title' => 'Fundamentos', 'lessons' => [
                        ['title' => 'O que é excelência no atendimento', 'type' => 'video', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration_minutes' => 12],
                        ['title' => 'Comunicação eficaz', 'type' => 'text', 'content' => 'A comunicação eficaz no atendimento ao cliente envolve escuta ativa, empatia, clareza na linguagem e resolução proativa de problemas. Sempre confirme o entendimento antes de prosseguir.'],
                    ]],
                    ['title' => 'Prática', 'lessons' => [
                        ['title' => 'Lidando com reclamações', 'type' => 'video', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration_minutes' => 18],
                    ]],
                ],
            ],
            [
                'title' => 'Ferramentas Internas',
                'description' => 'Aprenda a usar as ferramentas do dia a dia.',
                'modules' => [
                    ['title' => 'Ferramentas Essenciais', 'lessons' => [
                        ['title' => 'CRM e Pipeline', 'type' => 'video', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration_minutes' => 20],
                        ['title' => 'Comunicação interna', 'type' => 'video', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration_minutes' => 10],
                    ]],
                ],
            ],
            [
                'title' => 'Compliance e Ética',
                'description' => 'Código de conduta e normas corporativas.',
                'modules' => [
                    ['title' => 'Código de Conduta', 'lessons' => [
                        ['title' => 'Princípios éticos', 'type' => 'text', 'content' => 'Nossa empresa se baseia em integridade, transparência, respeito à diversidade e compromisso com a qualidade. Todo colaborador deve conhecer e praticar esses princípios no dia a dia.'],
                        ['title' => 'LGPD e Proteção de Dados', 'type' => 'video', 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'duration_minutes' => 25],
                    ]],
                ],
            ],
        ];

        $trainings = [];
        foreach ($trainingsData as $tData) {
            $training = Training::firstOrCreate(
                ['company_id' => $company->id, 'title' => $tData['title']],
                [
                    'created_by' => $instructor->id,
                    'description' => $tData['description'],
                    'duration_minutes' => 0,
                    'passing_score' => 70,
                    'has_quiz' => false,
                    'active' => true,
                ]
            );

            foreach ($tData['modules'] as $mi => $mData) {
                $module = TrainingModule::firstOrCreate(
                    ['training_id' => $training->id, 'title' => $mData['title']],
                    ['sort_order' => $mi, 'is_sequential' => false]
                );

                foreach ($mData['lessons'] as $li => $lData) {
                    TrainingLesson::firstOrCreate(
                        ['module_id' => $module->id, 'title' => $lData['title']],
                        [
                            'type' => $lData['type'],
                            'video_url' => $lData['video_url'] ?? null,
                            'video_provider' => isset($lData['video_url']) ? 'youtube' : null,
                            'content' => $lData['content'] ?? null,
                            'duration_minutes' => $lData['duration_minutes'] ?? 0,
                            'sort_order' => $li,
                        ]
                    );
                }
            }

            $trainings[] = $training;
        }

        return $trainings;
    }

    private function createProgress(Company $company, array $trainings, array $employees): void
    {
        if (count($trainings) < 5 || count($employees) < 10) return;

        // Training 0 (Onboarding): all completed
        foreach ($employees as $emp) {
            TrainingView::firstOrCreate(
                ['company_id' => $company->id, 'training_id' => $trainings[0]->id, 'user_id' => $emp->id],
                ['completed_at' => now()->subDays(rand(5, 40)), 'progress_percent' => 100, 'started_at' => now()->subDays(rand(41, 60))]
            );
        }

        // Training 1 (Segurança): 7/10 completed
        foreach (array_slice($employees, 0, 7) as $emp) {
            TrainingView::firstOrCreate(
                ['company_id' => $company->id, 'training_id' => $trainings[1]->id, 'user_id' => $emp->id],
                ['completed_at' => now()->subDays(rand(2, 20)), 'progress_percent' => 100, 'started_at' => now()->subDays(rand(21, 40))]
            );
        }
        foreach (array_slice($employees, 7, 3) as $emp) {
            TrainingView::firstOrCreate(
                ['company_id' => $company->id, 'training_id' => $trainings[1]->id, 'user_id' => $emp->id],
                ['progress_percent' => rand(20, 70), 'started_at' => now()->subDays(rand(5, 15))]
            );
        }

        // Training 2 (Atendimento): 4/10 completed
        foreach (array_slice($employees, 0, 4) as $emp) {
            TrainingView::firstOrCreate(
                ['company_id' => $company->id, 'training_id' => $trainings[2]->id, 'user_id' => $emp->id],
                ['completed_at' => now()->subDays(rand(1, 10)), 'progress_percent' => 100, 'started_at' => now()->subDays(rand(11, 25))]
            );
        }
        foreach (array_slice($employees, 4, 3) as $emp) {
            TrainingView::firstOrCreate(
                ['company_id' => $company->id, 'training_id' => $trainings[2]->id, 'user_id' => $emp->id],
                ['progress_percent' => rand(15, 60), 'started_at' => now()->subDays(rand(3, 10))]
            );
        }

        // Training 3 (Ferramentas): 2 in progress
        foreach (array_slice($employees, 0, 2) as $emp) {
            TrainingView::firstOrCreate(
                ['company_id' => $company->id, 'training_id' => $trainings[3]->id, 'user_id' => $emp->id],
                ['progress_percent' => rand(10, 50), 'started_at' => now()->subDays(rand(1, 5))]
            );
        }

        // Training 4 (Compliance): no views yet
    }
}
