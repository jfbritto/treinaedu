<?php

namespace Tests\Feature\Employee;

use App\Models\Company;
use App\Models\Group;
use App\Models\Plan;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\TrainingAssignment;
use App\Models\TrainingView;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizControllerTest extends TestCase
{
    use RefreshDatabase;

    private function setupQuizScenario(): array
    {
        $plan = Plan::create(['name' => 'Basic', 'price' => 99.90, 'max_users' => 50, 'max_trainings' => 20]);
        $company = Company::create(['name' => 'Test', 'slug' => 'test']);
        Subscription::create(['company_id' => $company->id, 'plan_id' => $plan->id, 'status' => 'active']);

        $admin = User::create(['name' => 'Admin', 'email' => 'admin@t.com', 'password' => 'pw', 'company_id' => $company->id, 'role' => 'admin', 'active' => true]);
        $employee = User::create(['name' => 'Emp', 'email' => 'emp@t.com', 'password' => 'pw', 'company_id' => $company->id, 'role' => 'employee', 'active' => true]);

        $group = Group::create(['company_id' => $company->id, 'name' => 'Team']);
        $group->users()->attach($employee);

        $training = Training::create([
            'company_id' => $company->id, 'created_by' => $admin->id,
            'title' => 'Test Training', 'video_url' => 'https://youtube.com/watch?v=x',
            'video_provider' => 'youtube', 'duration_minutes' => 10,
            'has_quiz' => true, 'passing_score' => 70,
        ]);

        TrainingAssignment::create(['company_id' => $company->id, 'training_id' => $training->id, 'group_id' => $group->id]);
        TrainingView::create(['company_id' => $company->id, 'training_id' => $training->id, 'user_id' => $employee->id, 'progress_percent' => 100, 'started_at' => now(), 'completed_at' => now()]);

        $quiz = Quiz::create(['training_id' => $training->id, 'company_id' => $company->id]);
        $q1 = QuizQuestion::create(['quiz_id' => $quiz->id, 'question' => 'Q1?', 'order' => 0]);
        $correct = QuizOption::create(['quiz_question_id' => $q1->id, 'option_text' => 'Correct', 'is_correct' => true, 'order' => 0]);
        $wrong = QuizOption::create(['quiz_question_id' => $q1->id, 'option_text' => 'Wrong', 'is_correct' => false, 'order' => 1]);

        return [$employee, $training, $quiz, $q1, $correct, $wrong];
    }

    public function test_employee_passes_quiz_with_correct_answer(): void
    {
        [$employee, $training, $quiz, $q1, $correct, $wrong] = $this->setupQuizScenario();

        $response = $this->actingAs($employee)->post(route('employee.quiz.submit', $training), [
            'answers' => [$q1->id => $correct->id],
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('quiz_attempts', [
            'user_id' => $employee->id, 'quiz_id' => $quiz->id, 'passed' => true,
        ]);
    }

    public function test_employee_fails_quiz_with_wrong_answer(): void
    {
        [$employee, $training, $quiz, $q1, $correct, $wrong] = $this->setupQuizScenario();

        $response = $this->actingAs($employee)->post(route('employee.quiz.submit', $training), [
            'answers' => [$q1->id => $wrong->id],
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('quiz_attempts', [
            'user_id' => $employee->id, 'quiz_id' => $quiz->id, 'passed' => false,
        ]);
    }
}
