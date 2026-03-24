<?php

namespace Tests\Feature\Models;

use App\Models\Certificate;
use App\Models\Company;
use App\Models\Group;
use App\Models\LessonView;
use App\Models\Path;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\TrainingAssignment;
use App\Models\TrainingLesson;
use App\Models\TrainingModule;
use App\Models\TrainingView;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrainingTest extends TestCase
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

    // ---- Relationship: modules ----

    public function test_training_has_modules_relationship(): void
    {
        $training = $this->createTraining();

        TrainingModule::factory()->count(3)->create([
            'training_id' => $training->id,
        ]);

        $this->assertCount(3, $training->modules);
        $this->assertInstanceOf(TrainingModule::class, $training->modules->first());
    }

    // ---- Relationship: lessons (hasManyThrough) ----

    public function test_training_has_lessons_through_modules(): void
    {
        $training = $this->createTraining();

        $module1 = TrainingModule::factory()->create(['training_id' => $training->id]);
        $module2 = TrainingModule::factory()->create(['training_id' => $training->id]);

        TrainingLesson::factory()->count(2)->create(['module_id' => $module1->id]);
        TrainingLesson::factory()->count(3)->create(['module_id' => $module2->id]);

        $this->assertCount(5, $training->lessons);
        $this->assertInstanceOf(TrainingLesson::class, $training->lessons->first());
    }

    // ---- totalLessons() ----

    public function test_total_lessons_returns_correct_count(): void
    {
        $training = $this->createTraining();
        $module = TrainingModule::factory()->create(['training_id' => $training->id]);

        $this->assertEquals(0, $training->totalLessons());

        TrainingLesson::factory()->count(4)->create(['module_id' => $module->id]);

        $this->assertEquals(4, $training->totalLessons());
    }

    public function test_total_lessons_counts_across_multiple_modules(): void
    {
        $training = $this->createTraining();

        $module1 = TrainingModule::factory()->create(['training_id' => $training->id]);
        $module2 = TrainingModule::factory()->create(['training_id' => $training->id]);

        TrainingLesson::factory()->count(2)->create(['module_id' => $module1->id]);
        TrainingLesson::factory()->count(3)->create(['module_id' => $module2->id]);

        $this->assertEquals(5, $training->totalLessons());
    }

    // ---- userCompletedLessons() ----

    public function test_user_completed_lessons_returns_correct_count(): void
    {
        $training = $this->createTraining();
        $module = TrainingModule::factory()->create(['training_id' => $training->id]);
        $employee = User::factory()->create([
            'company_id' => $this->company->id,
            'role' => 'employee',
        ]);

        $lesson1 = TrainingLesson::factory()->create(['module_id' => $module->id]);
        $lesson2 = TrainingLesson::factory()->create(['module_id' => $module->id]);
        $lesson3 = TrainingLesson::factory()->create(['module_id' => $module->id]);

        // Employee completed lesson 1 and 2 but not 3
        LessonView::factory()->create([
            'lesson_id' => $lesson1->id,
            'user_id' => $employee->id,
            'company_id' => $this->company->id,
            'completed_at' => now(),
        ]);
        LessonView::factory()->create([
            'lesson_id' => $lesson2->id,
            'user_id' => $employee->id,
            'company_id' => $this->company->id,
            'completed_at' => now(),
        ]);
        LessonView::factory()->create([
            'lesson_id' => $lesson3->id,
            'user_id' => $employee->id,
            'company_id' => $this->company->id,
            'completed_at' => null,
        ]);

        $this->actingAs($employee);
        $this->assertEquals(2, $training->userCompletedLessons($employee->id));
    }

    public function test_user_completed_lessons_returns_zero_with_no_views(): void
    {
        $training = $this->createTraining();
        $module = TrainingModule::factory()->create(['training_id' => $training->id]);
        $employee = User::factory()->create([
            'company_id' => $this->company->id,
            'role' => 'employee',
        ]);

        TrainingLesson::factory()->count(3)->create(['module_id' => $module->id]);

        $this->actingAs($employee);
        $this->assertEquals(0, $training->userCompletedLessons($employee->id));
    }

    public function test_user_completed_lessons_does_not_count_other_users(): void
    {
        $training = $this->createTraining();
        $module = TrainingModule::factory()->create(['training_id' => $training->id]);

        $employee1 = User::factory()->create(['company_id' => $this->company->id, 'role' => 'employee']);
        $employee2 = User::factory()->create(['company_id' => $this->company->id, 'role' => 'employee']);

        $lesson = TrainingLesson::factory()->create(['module_id' => $module->id]);

        // Only employee2 completed the lesson
        LessonView::factory()->create([
            'lesson_id' => $lesson->id,
            'user_id' => $employee2->id,
            'company_id' => $this->company->id,
            'completed_at' => now(),
        ]);

        $this->actingAs($employee1);
        $this->assertEquals(0, $training->userCompletedLessons($employee1->id));
        $this->assertEquals(1, $training->userCompletedLessons($employee2->id));
    }

    // ---- calculatedDuration() ----

    public function test_calculated_duration_sums_lesson_durations(): void
    {
        $training = $this->createTraining([
            'duration_minutes_override' => null,
        ]);

        $module = TrainingModule::factory()->create(['training_id' => $training->id]);

        TrainingLesson::factory()->create(['module_id' => $module->id, 'duration_minutes' => 10]);
        TrainingLesson::factory()->create(['module_id' => $module->id, 'duration_minutes' => 20]);
        TrainingLesson::factory()->create(['module_id' => $module->id, 'duration_minutes' => 15]);

        $this->assertEquals(45, $training->calculatedDuration());
    }

    public function test_calculated_duration_uses_override_when_set(): void
    {
        $training = $this->createTraining([
            'duration_minutes_override' => 120,
        ]);

        $module = TrainingModule::factory()->create(['training_id' => $training->id]);
        TrainingLesson::factory()->create(['module_id' => $module->id, 'duration_minutes' => 10]);
        TrainingLesson::factory()->create(['module_id' => $module->id, 'duration_minutes' => 20]);

        // Override should take priority, not the sum (30)
        $this->assertEquals(120, $training->calculatedDuration());
    }

    public function test_calculated_duration_returns_zero_with_no_lessons(): void
    {
        $training = $this->createTraining([
            'duration_minutes_override' => null,
        ]);

        $this->assertEquals(0, $training->calculatedDuration());
    }

    public function test_calculated_duration_sums_across_multiple_modules(): void
    {
        $training = $this->createTraining([
            'duration_minutes_override' => null,
        ]);

        $module1 = TrainingModule::factory()->create(['training_id' => $training->id]);
        $module2 = TrainingModule::factory()->create(['training_id' => $training->id]);

        TrainingLesson::factory()->create(['module_id' => $module1->id, 'duration_minutes' => 25]);
        TrainingLesson::factory()->create(['module_id' => $module2->id, 'duration_minutes' => 35]);

        $this->assertEquals(60, $training->calculatedDuration());
    }

    // ---- completionRate() ----

    public function test_completion_rate_returns_zero_with_no_assignments(): void
    {
        $training = $this->createTraining();

        $this->assertEquals(0, $training->completionRate());
    }

    public function test_completion_rate_calculates_correctly(): void
    {
        $training = $this->createTraining();

        $group = Group::factory()->create(['company_id' => $this->company->id]);

        // Assign training to the group
        TrainingAssignment::factory()->create([
            'company_id' => $this->company->id,
            'training_id' => $training->id,
            'group_id' => $group->id,
        ]);

        // Create 4 employees in the group
        $employees = User::factory()->count(4)->create([
            'company_id' => $this->company->id,
            'role' => 'employee',
        ]);

        foreach ($employees as $employee) {
            $group->users()->attach($employee->id);
        }

        // 1 out of 4 completed
        TrainingView::factory()->create([
            'company_id' => $this->company->id,
            'training_id' => $training->id,
            'user_id' => $employees[0]->id,
            'completed_at' => now(),
        ]);

        $this->actingAs($this->admin);
        $this->assertEquals(25.0, $training->completionRate());
    }

    public function test_completion_rate_with_all_completed(): void
    {
        $training = $this->createTraining();
        $group = Group::factory()->create(['company_id' => $this->company->id]);

        TrainingAssignment::factory()->create([
            'company_id' => $this->company->id,
            'training_id' => $training->id,
            'group_id' => $group->id,
        ]);

        $employees = User::factory()->count(2)->create([
            'company_id' => $this->company->id,
            'role' => 'employee',
        ]);

        foreach ($employees as $employee) {
            $group->users()->attach($employee->id);
            TrainingView::factory()->create([
                'company_id' => $this->company->id,
                'training_id' => $training->id,
                'user_id' => $employee->id,
                'completed_at' => now(),
            ]);
        }

        $this->actingAs($this->admin);
        $this->assertEquals(100.0, $training->completionRate());
    }

    // ---- Relationship: paths (belongsToMany) ----

    public function test_paths_relationship_works(): void
    {
        $training = $this->createTraining();

        $path1 = Path::factory()->create(['company_id' => $this->company->id]);
        $path2 = Path::factory()->create(['company_id' => $this->company->id]);

        $training->paths()->attach($path1->id, ['sort_order' => 1]);
        $training->paths()->attach($path2->id, ['sort_order' => 2]);

        $training->load('paths');

        $this->assertCount(2, $training->paths);
        $this->assertTrue($training->paths->contains($path1));
        $this->assertTrue($training->paths->contains($path2));
    }

    public function test_paths_relationship_includes_pivot_sort_order(): void
    {
        $training = $this->createTraining();
        $path = Path::factory()->create(['company_id' => $this->company->id]);

        $training->paths()->attach($path->id, ['sort_order' => 5]);

        $attachedPath = $training->paths()->first();
        $this->assertEquals(5, $attachedPath->pivot->sort_order);
    }

    // ---- SoftDeletes ----

    public function test_soft_delete_works(): void
    {
        $training = $this->createTraining();
        $trainingId = $training->id;

        $training->delete();

        $this->assertSoftDeleted('trainings', ['id' => $trainingId]);
        $this->assertNull(Training::find($trainingId));
        $this->assertNotNull(Training::withTrashed()->find($trainingId));
    }

    // ---- detectProvider() ----

    public function test_detect_provider_identifies_youtube(): void
    {
        $this->assertEquals('youtube', Training::detectProvider('https://youtube.com/watch?v=abc'));
        $this->assertEquals('youtube', Training::detectProvider('https://www.youtube.com/watch?v=abc'));
        $this->assertEquals('youtube', Training::detectProvider('https://youtu.be/abc'));
    }

    public function test_detect_provider_identifies_vimeo(): void
    {
        $this->assertEquals('vimeo', Training::detectProvider('https://vimeo.com/123456'));
    }

    public function test_detect_provider_throws_for_unsupported_url(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Training::detectProvider('https://dailymotion.com/video/abc');
    }
}
