<?php

namespace Tests\Feature\Services;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\TrainingView;
use App\Models\User;
use App\Services\VideoProgressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VideoProgressServiceTest extends TestCase
{
    use RefreshDatabase;

    private function setup_data(): array
    {
        $plan = Plan::create(['name' => 'Basic', 'price' => 99.90, 'max_users' => 50, 'max_trainings' => 20]);
        $company = Company::create(['name' => 'Test', 'slug' => 'test']);
        Subscription::create(['company_id' => $company->id, 'plan_id' => $plan->id, 'status' => 'active']);
        $user = User::create(['name' => 'Emp', 'email' => 'e@test.com', 'password' => 'pw', 'company_id' => $company->id, 'role' => 'employee']);
        $admin = User::create(['name' => 'Admin', 'email' => 'a@test.com', 'password' => 'pw', 'company_id' => $company->id, 'role' => 'admin']);
        $training = Training::create([
            'company_id' => $company->id, 'created_by' => $admin->id,
            'title' => 'Test', 'video_url' => 'https://youtube.com/watch?v=123',
            'video_provider' => 'youtube', 'duration_minutes' => 30,
        ]);

        return [$company, $user, $training];
    }

    public function test_update_progress_creates_view(): void
    {
        [$company, $user, $training] = $this->setup_data();
        $service = new VideoProgressService();

        $view = $service->updateProgress($training->id, $user->id, $company->id, 50);

        $this->assertEquals(50, $view->progress_percent);
        $this->assertNotNull($view->started_at);
    }

    public function test_progress_never_decreases(): void
    {
        [$company, $user, $training] = $this->setup_data();
        $service = new VideoProgressService();

        $service->updateProgress($training->id, $user->id, $company->id, 70);
        $view = $service->updateProgress($training->id, $user->id, $company->id, 40);

        $this->assertEquals(70, $view->progress_percent);
    }

    public function test_mark_completed_requires_90_percent(): void
    {
        [$company, $user, $training] = $this->setup_data();
        $service = new VideoProgressService();

        $service->updateProgress($training->id, $user->id, $company->id, 50);
        $result = $service->markCompleted($training->id, $user->id);

        $this->assertNull($result);
    }

    public function test_mark_completed_succeeds_at_90_percent(): void
    {
        [$company, $user, $training] = $this->setup_data();
        $service = new VideoProgressService();

        $service->updateProgress($training->id, $user->id, $company->id, 92);
        $result = $service->markCompleted($training->id, $user->id);

        $this->assertNotNull($result);
        $this->assertNotNull($result->completed_at);
    }
}
