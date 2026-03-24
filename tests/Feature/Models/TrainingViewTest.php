<?php

namespace Tests\Feature\Models;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\TrainingView;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrainingViewTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $plan = Plan::factory()->create();
        $this->company = Company::factory()->create();
        Subscription::factory()->create([
            'company_id' => $this->company->id,
            'plan_id' => $plan->id,
        ]);
        $this->admin = User::factory()->admin()->create([
            'company_id' => $this->company->id,
        ]);
    }

    private function createTraining(array $overrides = []): Training
    {
        return Training::factory()->create(array_merge([
            'company_id' => $this->company->id,
            'created_by' => $this->admin->id,
        ], $overrides));
    }

    private function createEmployee(): User
    {
        return User::factory()->create([
            'company_id' => $this->company->id,
            'role' => 'employee',
        ]);
    }

    // ---- scopeWithFilters: training_id ----

    public function test_scope_with_filters_filters_by_training_id(): void
    {
        $this->actingAs($this->admin);

        $training1 = $this->createTraining();
        $training2 = $this->createTraining();
        $employee = $this->createEmployee();

        TrainingView::factory()->create([
            'company_id' => $this->company->id,
            'training_id' => $training1->id,
            'user_id' => $employee->id,
        ]);
        TrainingView::factory()->create([
            'company_id' => $this->company->id,
            'training_id' => $training2->id,
            'user_id' => $employee->id,
        ]);

        $filtered = TrainingView::withFilters(['training_id' => $training1->id])->get();

        $this->assertCount(1, $filtered);
        $this->assertEquals($training1->id, $filtered->first()->training_id);
    }

    // ---- scopeWithFilters: status completed ----

    public function test_scope_with_filters_filters_by_status_completed(): void
    {
        $this->actingAs($this->admin);

        $training = $this->createTraining();
        $employee1 = $this->createEmployee();
        $employee2 = $this->createEmployee();

        TrainingView::factory()->create([
            'company_id' => $this->company->id,
            'training_id' => $training->id,
            'user_id' => $employee1->id,
            'completed_at' => now(),
        ]);
        TrainingView::factory()->create([
            'company_id' => $this->company->id,
            'training_id' => $training->id,
            'user_id' => $employee2->id,
            'completed_at' => null,
        ]);

        $completed = TrainingView::withFilters(['status' => 'completed'])->get();

        $this->assertCount(1, $completed);
        $this->assertNotNull($completed->first()->completed_at);
    }

    // ---- scopeWithFilters: status pending ----

    public function test_scope_with_filters_filters_by_status_pending(): void
    {
        $this->actingAs($this->admin);

        $training = $this->createTraining();
        $employee1 = $this->createEmployee();
        $employee2 = $this->createEmployee();

        TrainingView::factory()->create([
            'company_id' => $this->company->id,
            'training_id' => $training->id,
            'user_id' => $employee1->id,
            'completed_at' => now(),
        ]);
        TrainingView::factory()->create([
            'company_id' => $this->company->id,
            'training_id' => $training->id,
            'user_id' => $employee2->id,
            'completed_at' => null,
        ]);

        $pending = TrainingView::withFilters(['status' => 'pending'])->get();

        $this->assertCount(1, $pending);
        $this->assertNull($pending->first()->completed_at);
    }

    // ---- scopeWithFilters: date range (started_from / started_until) ----

    public function test_scope_with_filters_filters_by_started_date_range(): void
    {
        $this->actingAs($this->admin);

        $training = $this->createTraining();

        TrainingView::factory()->create([
            'company_id' => $this->company->id,
            'training_id' => $training->id,
            'user_id' => $this->createEmployee()->id,
            'started_at' => '2025-01-15 10:00:00',
        ]);
        TrainingView::factory()->create([
            'company_id' => $this->company->id,
            'training_id' => $training->id,
            'user_id' => $this->createEmployee()->id,
            'started_at' => '2025-03-20 10:00:00',
        ]);
        TrainingView::factory()->create([
            'company_id' => $this->company->id,
            'training_id' => $training->id,
            'user_id' => $this->createEmployee()->id,
            'started_at' => '2025-06-01 10:00:00',
        ]);

        $filtered = TrainingView::withFilters([
            'started_from' => '2025-02-01',
            'started_until' => '2025-04-30',
        ])->get();

        $this->assertCount(1, $filtered);
    }

    // ---- scopeWithFilters: completed date range ----

    public function test_scope_with_filters_filters_by_completed_date_range(): void
    {
        $this->actingAs($this->admin);

        $training = $this->createTraining();

        TrainingView::factory()->create([
            'company_id' => $this->company->id,
            'training_id' => $training->id,
            'user_id' => $this->createEmployee()->id,
            'completed_at' => '2025-01-10 10:00:00',
        ]);
        TrainingView::factory()->create([
            'company_id' => $this->company->id,
            'training_id' => $training->id,
            'user_id' => $this->createEmployee()->id,
            'completed_at' => '2025-05-15 10:00:00',
        ]);
        TrainingView::factory()->create([
            'company_id' => $this->company->id,
            'training_id' => $training->id,
            'user_id' => $this->createEmployee()->id,
            'completed_at' => null,
        ]);

        $filtered = TrainingView::withFilters([
            'completed_from' => '2025-04-01',
            'completed_until' => '2025-06-01',
        ])->get();

        $this->assertCount(1, $filtered);
        $this->assertEquals('2025-05-15', $filtered->first()->completed_at->toDateString());
    }

    // ---- scopeWithFilters: combined filters ----

    public function test_scope_with_filters_combines_multiple_filters(): void
    {
        $this->actingAs($this->admin);

        $training1 = $this->createTraining();
        $training2 = $this->createTraining();

        // Should match: correct training + completed
        TrainingView::factory()->create([
            'company_id' => $this->company->id,
            'training_id' => $training1->id,
            'user_id' => $this->createEmployee()->id,
            'completed_at' => now(),
        ]);
        // Should not match: wrong training
        TrainingView::factory()->create([
            'company_id' => $this->company->id,
            'training_id' => $training2->id,
            'user_id' => $this->createEmployee()->id,
            'completed_at' => now(),
        ]);
        // Should not match: correct training but pending
        TrainingView::factory()->create([
            'company_id' => $this->company->id,
            'training_id' => $training1->id,
            'user_id' => $this->createEmployee()->id,
            'completed_at' => null,
        ]);

        $filtered = TrainingView::withFilters([
            'training_id' => $training1->id,
            'status' => 'completed',
        ])->get();

        $this->assertCount(1, $filtered);
    }

    // ---- getGlobalStats ----

    public function test_get_global_stats_returns_correct_aggregation(): void
    {
        $this->actingAs($this->admin);

        $training = $this->createTraining();

        // 2 completed, 1 pending
        TrainingView::factory()->create([
            'company_id' => $this->company->id,
            'training_id' => $training->id,
            'user_id' => $this->createEmployee()->id,
            'progress_percent' => 100,
            'completed_at' => now(),
        ]);
        TrainingView::factory()->create([
            'company_id' => $this->company->id,
            'training_id' => $training->id,
            'user_id' => $this->createEmployee()->id,
            'progress_percent' => 100,
            'completed_at' => now(),
        ]);
        TrainingView::factory()->create([
            'company_id' => $this->company->id,
            'training_id' => $training->id,
            'user_id' => $this->createEmployee()->id,
            'progress_percent' => 40,
            'completed_at' => null,
        ]);

        $stats = TrainingView::getGlobalStats();

        $this->assertEquals(3, $stats['total']);
        $this->assertEquals(2, $stats['completed']);
        $this->assertEquals(1, $stats['pending']);
        $this->assertEquals(66.67, $stats['completed_percent']);
        $this->assertEquals(80.0, $stats['avg_progress']);
    }

    public function test_get_global_stats_returns_zeroes_when_empty(): void
    {
        $this->actingAs($this->admin);

        $stats = TrainingView::getGlobalStats();

        $this->assertEquals(0, $stats['total']);
        $this->assertEquals(0, $stats['completed']);
        $this->assertEquals(0, $stats['pending']);
        $this->assertEquals(0, $stats['completed_percent']);
        $this->assertEquals(0, $stats['avg_progress']);
    }
}
