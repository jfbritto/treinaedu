<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{Company, Plan, Subscription, Training, Group, User, TrainingView};
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReportsControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);

        $this->company = Company::factory()->create();
        $plan = Plan::factory()->create();
        Subscription::factory()->create([
            'company_id' => $this->company->id,
            'plan_id' => $plan->id,
            'status' => 'active',
        ]);
        $this->admin = User::factory()->admin()->create([
            'company_id' => $this->company->id,
        ]);
    }

    public function test_reports_page_loads()
    {
        $this->actingAs($this->admin)
            ->get(route('reports.index'))
            ->assertStatus(200);
    }

    public function test_filter_endpoint_returns_json()
    {
        $training = Training::factory()->create(['company_id' => $this->company->id]);
        TrainingView::factory(5)->create([
            'company_id' => $this->company->id,
            'training_id' => $training->id,
        ]);

        $this->actingAs($this->admin)
            ->getJson(route('reports.filter', ['training_id' => $training->id]))
            ->assertStatus(200)
            ->assertJsonStructure(['stats', 'data', 'tab']);
    }

    public function test_global_stats_reflect_all_data()
    {
        TrainingView::factory(10)->create(['company_id' => $this->company->id]);
        TrainingView::factory(5)->create(['company_id' => $this->company->id, 'completed_at' => now()]);

        $this->actingAs($this->admin)
            ->getJson(route('reports.filter'))
            ->assertJsonPath('stats.total', 15)
            ->assertJsonPath('stats.completed', 5)
            ->assertJsonPath('stats.pending', 10);
    }
}
