<?php

namespace Tests\Feature\Models;

use App\Models\Company;
use App\Models\Path;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PathTest extends TestCase
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
            'active' => true,
        ]);
    }

    // ---- Relationship: trainings (belongsToMany) ----

    public function test_path_has_trainings_relationship(): void
    {
        $path = Path::factory()->create(['company_id' => $this->company->id]);

        $training1 = Training::factory()->create([
            'company_id' => $this->company->id,
            'created_by' => $this->admin->id,
        ]);
        $training2 = Training::factory()->create([
            'company_id' => $this->company->id,
            'created_by' => $this->admin->id,
        ]);

        $path->trainings()->attach($training1->id, ['sort_order' => 1]);
        $path->trainings()->attach($training2->id, ['sort_order' => 2]);

        $path->load('trainings');

        $this->assertCount(2, $path->trainings);
        $this->assertInstanceOf(Training::class, $path->trainings->first());
    }

    public function test_path_trainings_ordered_by_pivot_sort_order(): void
    {
        $path = Path::factory()->create(['company_id' => $this->company->id]);

        $trainingA = Training::factory()->create([
            'company_id' => $this->company->id,
            'created_by' => $this->admin->id,
            'title' => 'Second Training',
        ]);
        $trainingB = Training::factory()->create([
            'company_id' => $this->company->id,
            'created_by' => $this->admin->id,
            'title' => 'First Training',
        ]);

        $path->trainings()->attach($trainingA->id, ['sort_order' => 2]);
        $path->trainings()->attach($trainingB->id, ['sort_order' => 1]);

        $orderedTrainings = $path->trainings;

        $this->assertEquals('First Training', $orderedTrainings->first()->title);
        $this->assertEquals('Second Training', $orderedTrainings->last()->title);
    }

    public function test_path_trainings_pivot_has_sort_order(): void
    {
        $path = Path::factory()->create(['company_id' => $this->company->id]);

        $training = Training::factory()->create([
            'company_id' => $this->company->id,
            'created_by' => $this->admin->id,
        ]);

        $path->trainings()->attach($training->id, ['sort_order' => 7]);

        $attachedTraining = $path->trainings()->first();
        $this->assertEquals(7, $attachedTraining->pivot->sort_order);
    }

    // ---- Company scope ----

    public function test_path_respects_company_scope(): void
    {
        $otherCompany = Company::factory()->create();

        $path1 = Path::factory()->create(['company_id' => $this->company->id]);
        $path2 = Path::factory()->create(['company_id' => $otherCompany->id]);

        // When acting as user from company, scope should filter
        $this->actingAs($this->admin);

        $paths = Path::all();

        $this->assertTrue($paths->contains($path1));
        $this->assertFalse($paths->contains($path2));
    }

    // ---- SoftDeletes ----

    public function test_soft_delete_works(): void
    {
        $path = Path::factory()->create(['company_id' => $this->company->id]);
        $pathId = $path->id;

        $path->delete();

        $this->assertSoftDeleted('paths', ['id' => $pathId]);
        $this->assertNull(Path::withoutGlobalScope('company')->find($pathId));
        $this->assertNotNull(Path::withoutGlobalScope('company')->withTrashed()->find($pathId));
    }

    // ---- Active scope ----

    public function test_path_can_be_deactivated(): void
    {
        $path = Path::factory()->create([
            'company_id' => $this->company->id,
            'active' => true,
        ]);

        $this->assertTrue($path->active);

        $path->update(['active' => false]);

        $this->assertFalse($path->fresh()->active);
    }
}
