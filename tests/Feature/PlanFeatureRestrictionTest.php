<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PlanFeatureRestrictionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    /**
     * Helper: cria Plan, Company, Subscription(active) e User(admin).
     */
    private function createUserWithPlan(string $planName, array $features, ?int $maxTrainings = null): User
    {
        $plan = Plan::factory()->create([
            'name' => $planName,
            'features' => $features,
            'max_trainings' => $maxTrainings,
            'active' => true,
        ]);

        $company = Company::factory()->create();

        Subscription::factory()->create([
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'status' => 'active',
        ]);

        return User::factory()->admin()->create([
            'company_id' => $company->id,
            'active' => true,
        ]);
    }

    // ---------------------------------------------------------------
    // 1. Starter NÃO pode usar AI Quiz
    // ---------------------------------------------------------------
    public function test_starter_cannot_use_ai_quiz(): void
    {
        Http::fake();
        Notification::fake();

        $user = $this->createUserWithPlan('Starter', ['certificates', 'basic_reports']);

        $response = $this->actingAs($user)->postJson('/api/ai/generate-quiz', [
            'lesson_title' => 'Aula de Segurança',
            'content' => 'Conteúdo sobre segurança do trabalho.',
            'num_questions' => 3,
        ]);

        $response->assertStatus(403);
        $response->assertJson(['error' => true], false);
        $response->assertJsonStructure(['error']);
    }

    // ---------------------------------------------------------------
    // 2. Business PODE usar AI Quiz
    // ---------------------------------------------------------------
    public function test_business_can_use_ai_quiz(): void
    {
        Notification::fake();

        // Fake Gemini API response
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [[
                    'content' => [
                        'parts' => [[
                            'text' => json_encode([
                                [
                                    'question' => 'Pergunta 1?',
                                    'options' => [
                                        ['text' => 'Opção A'],
                                        ['text' => 'Opção B'],
                                    ],
                                    'correct' => 0,
                                ],
                            ]),
                        ]],
                    ],
                ]],
            ], 200),
            '*' => Http::response([], 200),
        ]);

        $user = $this->createUserWithPlan('Business', [
            'certificates', 'basic_reports', 'ai_quiz', 'learning_paths', 'export_reports',
        ]);

        // Set Gemini API key so isConfigured() returns true
        config(['services.gemini.api_key' => 'fake-key']);

        $response = $this->actingAs($user)->postJson('/api/ai/generate-quiz', [
            'lesson_title' => 'Aula de Segurança',
            'content' => 'Conteúdo sobre segurança do trabalho para gerar quiz.',
            'num_questions' => 1,
        ]);

        $this->assertNotEquals(403, $response->getStatusCode());
    }

    // ---------------------------------------------------------------
    // 3. Starter NÃO pode criar trilha (path)
    // ---------------------------------------------------------------
    public function test_starter_cannot_create_path(): void
    {
        Http::fake();
        Notification::fake();

        $user = $this->createUserWithPlan('Starter', ['certificates', 'basic_reports']);

        $response = $this->actingAs($user)->post('/paths', [
            'title' => 'Trilha de Onboarding',
            'trainings' => [],
        ]);

        // Deve redirecionar de volta com erro OU redirecionar para subscription.plans
        $hasError = $response->isRedirect();
        $this->assertTrue($hasError);

        if ($response->getSession()) {
            $session = $response->getSession();
            $hasSessionError = $session->has('error');
            $redirectsToPlans = str_contains($response->headers->get('Location', ''), 'subscription/plans');
            $this->assertTrue($hasSessionError || $redirectsToPlans);
        }
    }

    // ---------------------------------------------------------------
    // 4. Business PODE criar trilha (path)
    // ---------------------------------------------------------------
    public function test_business_can_create_path(): void
    {
        Http::fake();
        Notification::fake();

        $user = $this->createUserWithPlan('Business', [
            'certificates', 'basic_reports', 'ai_quiz', 'learning_paths', 'export_reports',
        ]);

        $response = $this->actingAs($user)->post('/paths', [
            'title' => 'Trilha de Onboarding',
            'description' => 'Trilha para novos colaboradores',
            'color' => '#10B981',
            'active' => true,
            'trainings' => [],
        ]);

        // Não deve redirecionar para subscription.plans
        $location = $response->headers->get('Location', '');
        $this->assertStringNotContainsString('subscription/plans', $location);
        $response->assertRedirect();
    }

    // ---------------------------------------------------------------
    // 5. Starter NÃO pode acessar engajamento
    // ---------------------------------------------------------------
    public function test_starter_cannot_access_engagement(): void
    {
        Http::fake();
        Notification::fake();

        $user = $this->createUserWithPlan('Starter', ['certificates', 'basic_reports']);

        $response = $this->actingAs($user)->get('/engagement');

        $response->assertRedirect(route('subscription.plans'));
    }

    // ---------------------------------------------------------------
    // 6. Professional PODE acessar engajamento
    // ---------------------------------------------------------------
    public function test_professional_can_access_engagement(): void
    {
        Http::fake();
        Notification::fake();

        $user = $this->createUserWithPlan('Professional', [
            'certificates', 'basic_reports', 'ai_quiz', 'learning_paths',
            'export_reports', 'engagement',
        ]);

        $response = $this->actingAs($user)->get('/engagement');

        $response->assertStatus(200);
    }

    // ---------------------------------------------------------------
    // 7. Limite de treinamentos é respeitado
    // ---------------------------------------------------------------
    public function test_training_limit_enforced(): void
    {
        Http::fake();
        Notification::fake();

        $user = $this->createUserWithPlan('Starter', ['certificates', 'basic_reports'], maxTrainings: 2);

        // Cria 2 treinamentos já existentes para a empresa (atinge o limite)
        Training::factory()->count(2)->create([
            'company_id' => $user->company_id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->post('/trainings', [
            'title' => 'Terceiro Treinamento',
            'description' => 'Não deveria ser criado',
            'is_sequential' => '0',
            'has_quiz' => '0',
            'modules' => [
                [
                    'title' => 'Modulo 1',
                    'lessons' => [
                        [
                            'title' => 'Aula 1',
                            'type' => 'text',
                            'content' => 'Conteúdo de teste',
                        ],
                    ],
                ],
            ],
        ]);

        $response->assertSessionHas('error');
        $this->assertStringContainsString('limite', strtolower($response->getSession()->get('error')));
    }

    // ---------------------------------------------------------------
    // 8. Treinamento dentro do limite é permitido
    // ---------------------------------------------------------------
    public function test_training_within_limit_allowed(): void
    {
        Http::fake();
        Notification::fake();

        $user = $this->createUserWithPlan('Business', [
            'certificates', 'basic_reports', 'ai_quiz', 'learning_paths', 'export_reports',
        ], maxTrainings: 5);

        // Cria 4 treinamentos (ainda cabe mais 1)
        Training::factory()->count(4)->create([
            'company_id' => $user->company_id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->post('/trainings', [
            'title' => 'Quinto Treinamento',
            'description' => 'Dentro do limite',
            'is_sequential' => '0',
            'has_quiz' => '0',
            'modules' => [
                [
                    'title' => 'Modulo 1',
                    'lessons' => [
                        [
                            'title' => 'Aula 1',
                            'type' => 'text',
                            'content' => 'Conteúdo de teste',
                        ],
                    ],
                ],
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionMissing('error');
    }

    // ---------------------------------------------------------------
    // 9. Starter NÃO pode exportar PDF
    // ---------------------------------------------------------------
    public function test_starter_cannot_export_pdf(): void
    {
        Http::fake();
        Notification::fake();

        $user = $this->createUserWithPlan('Starter', ['certificates', 'basic_reports']);

        $response = $this->actingAs($user)->get('/reports/export/pdf');

        $response->assertSessionHas('error');
    }

    // ---------------------------------------------------------------
    // 10. Business PODE exportar PDF
    // ---------------------------------------------------------------
    public function test_business_can_export_pdf(): void
    {
        Http::fake();
        Notification::fake();

        $user = $this->createUserWithPlan('Business', [
            'certificates', 'basic_reports', 'ai_quiz', 'learning_paths', 'export_reports',
        ]);

        $response = $this->actingAs($user)->get('/reports/export/pdf');

        // Não deve redirecionar para subscription.plans com erro de feature
        $location = $response->headers->get('Location', '');
        $this->assertStringNotContainsString('subscription/plans', $location);
    }
}
