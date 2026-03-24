<?php

namespace Tests\Feature\Models;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\TrainingLesson;
use App\Models\TrainingModule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizFlowTest extends TestCase
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

    private function createTraining(array $overrides = []): Training
    {
        return Training::factory()->create(array_merge([
            'company_id' => $this->company->id,
            'created_by' => $this->admin->id,
        ], $overrides));
    }

    // ---- Quiz belongs to training ----

    public function test_quiz_belongs_to_training(): void
    {
        $training = $this->createTraining(['has_quiz' => true]);

        $quiz = Quiz::factory()->create([
            'training_id' => $training->id,
            'company_id' => $this->company->id,
            'module_id' => null,
            'lesson_id' => null,
        ]);

        $this->assertEquals($training->id, $quiz->training->id);
        $this->assertInstanceOf(Training::class, $quiz->training);
    }

    public function test_training_quiz_relationship_returns_training_level_quiz(): void
    {
        $training = $this->createTraining(['has_quiz' => true]);
        $module = TrainingModule::factory()->create(['training_id' => $training->id]);

        // Training-level quiz (no module_id, no lesson_id)
        $trainingQuiz = Quiz::factory()->create([
            'training_id' => $training->id,
            'company_id' => $this->company->id,
            'module_id' => null,
            'lesson_id' => null,
        ]);

        // Module-level quiz (has module_id)
        Quiz::factory()->create([
            'training_id' => $training->id,
            'company_id' => $this->company->id,
            'module_id' => $module->id,
            'lesson_id' => null,
        ]);

        $this->actingAs($this->admin);

        // The quiz() / trainingQuiz() relation returns only the training-level quiz
        $this->assertNotNull($training->quiz);
        $this->assertEquals($trainingQuiz->id, $training->quiz->id);
    }

    // ---- Quiz can belong to module ----

    public function test_quiz_can_belong_to_module(): void
    {
        $training = $this->createTraining();
        $module = TrainingModule::factory()->create(['training_id' => $training->id]);

        $quiz = Quiz::factory()->create([
            'training_id' => $training->id,
            'company_id' => $this->company->id,
            'module_id' => $module->id,
            'lesson_id' => null,
        ]);

        $this->assertNotNull($quiz->module);
        $this->assertEquals($module->id, $quiz->module->id);
        $this->assertInstanceOf(TrainingModule::class, $quiz->module);
    }

    public function test_module_quiz_relationship(): void
    {
        $training = $this->createTraining();
        $module = TrainingModule::factory()->create(['training_id' => $training->id]);

        $quiz = Quiz::factory()->create([
            'training_id' => $training->id,
            'company_id' => $this->company->id,
            'module_id' => $module->id,
            'lesson_id' => null,
        ]);

        $this->actingAs($this->admin);

        $this->assertNotNull($module->quiz);
        $this->assertEquals($quiz->id, $module->quiz->id);
    }

    // ---- Quiz can belong to lesson ----

    public function test_quiz_can_belong_to_lesson(): void
    {
        $training = $this->createTraining();
        $module = TrainingModule::factory()->create(['training_id' => $training->id]);
        $lesson = TrainingLesson::factory()->create(['module_id' => $module->id]);

        $quiz = Quiz::factory()->create([
            'training_id' => $training->id,
            'company_id' => $this->company->id,
            'module_id' => $module->id,
            'lesson_id' => $lesson->id,
        ]);

        $this->assertNotNull($quiz->lesson);
        $this->assertEquals($lesson->id, $quiz->lesson->id);
        $this->assertInstanceOf(TrainingLesson::class, $quiz->lesson);
    }

    public function test_lesson_quiz_relationship(): void
    {
        $training = $this->createTraining();
        $module = TrainingModule::factory()->create(['training_id' => $training->id]);
        $lesson = TrainingLesson::factory()->create(['module_id' => $module->id]);

        $quiz = Quiz::factory()->create([
            'training_id' => $training->id,
            'company_id' => $this->company->id,
            'module_id' => $module->id,
            'lesson_id' => $lesson->id,
        ]);

        $this->assertNotNull($lesson->quiz);
        $this->assertEquals($quiz->id, $lesson->quiz->id);
    }

    // ---- Quiz has questions with options ----

    public function test_quiz_has_questions_with_options(): void
    {
        $training = $this->createTraining();

        $quiz = Quiz::factory()->create([
            'training_id' => $training->id,
            'company_id' => $this->company->id,
        ]);

        $question1 = QuizQuestion::factory()->create([
            'quiz_id' => $quiz->id,
            'question' => 'What is Laravel?',
            'order' => 1,
        ]);
        $question2 = QuizQuestion::factory()->create([
            'quiz_id' => $quiz->id,
            'question' => 'What is PHP?',
            'order' => 2,
        ]);

        // Question 1: 4 options, 1 correct
        QuizOption::factory()->create([
            'quiz_question_id' => $question1->id,
            'option_text' => 'A PHP framework',
            'is_correct' => true,
            'order' => 1,
        ]);
        QuizOption::factory()->count(3)->create([
            'quiz_question_id' => $question1->id,
            'is_correct' => false,
        ]);

        // Question 2: 3 options, 1 correct
        QuizOption::factory()->create([
            'quiz_question_id' => $question2->id,
            'option_text' => 'A programming language',
            'is_correct' => true,
            'order' => 1,
        ]);
        QuizOption::factory()->count(2)->create([
            'quiz_question_id' => $question2->id,
            'is_correct' => false,
        ]);

        $this->actingAs($this->admin);

        $quiz->load('questions.options');

        $this->assertCount(2, $quiz->questions);
        $this->assertCount(4, $quiz->questions->first()->options);
        $this->assertCount(3, $quiz->questions->last()->options);

        // Verify correct option is identified
        $correctOption = $quiz->questions->first()->options->where('is_correct', true)->first();
        $this->assertEquals('A PHP framework', $correctOption->option_text);
    }

    public function test_questions_are_ordered_by_order_field(): void
    {
        $training = $this->createTraining();

        $quiz = Quiz::factory()->create([
            'training_id' => $training->id,
            'company_id' => $this->company->id,
        ]);

        QuizQuestion::factory()->create([
            'quiz_id' => $quiz->id,
            'question' => 'Third question',
            'order' => 3,
        ]);
        QuizQuestion::factory()->create([
            'quiz_id' => $quiz->id,
            'question' => 'First question',
            'order' => 1,
        ]);
        QuizQuestion::factory()->create([
            'quiz_id' => $quiz->id,
            'question' => 'Second question',
            'order' => 2,
        ]);

        $this->actingAs($this->admin);

        $questions = $quiz->questions;

        $this->assertEquals('First question', $questions[0]->question);
        $this->assertEquals('Second question', $questions[1]->question);
        $this->assertEquals('Third question', $questions[2]->question);
    }

    // ---- Quiz attempt records score and pass/fail ----

    public function test_quiz_attempt_records_score_and_pass(): void
    {
        $training = $this->createTraining(['passing_score' => 70]);
        $employee = User::factory()->create([
            'company_id' => $this->company->id,
            'role' => 'employee',
            'active' => true,
        ]);

        $quiz = Quiz::factory()->create([
            'training_id' => $training->id,
            'company_id' => $this->company->id,
        ]);

        $attempt = QuizAttempt::factory()->create([
            'quiz_id' => $quiz->id,
            'user_id' => $employee->id,
            'company_id' => $this->company->id,
            'score' => 85,
            'passed' => true,
            'completed_at' => now(),
        ]);

        $this->assertEquals(85, $attempt->score);
        $this->assertTrue($attempt->passed);
        $this->assertNotNull($attempt->completed_at);
        $this->assertEquals($quiz->id, $attempt->quiz_id);
        $this->assertEquals($employee->id, $attempt->user_id);
    }

    public function test_quiz_attempt_records_score_and_fail(): void
    {
        $training = $this->createTraining(['passing_score' => 70]);
        $employee = User::factory()->create([
            'company_id' => $this->company->id,
            'role' => 'employee',
            'active' => true,
        ]);

        $quiz = Quiz::factory()->create([
            'training_id' => $training->id,
            'company_id' => $this->company->id,
        ]);

        $attempt = QuizAttempt::factory()->create([
            'quiz_id' => $quiz->id,
            'user_id' => $employee->id,
            'company_id' => $this->company->id,
            'score' => 50,
            'passed' => false,
            'completed_at' => now(),
        ]);

        $this->assertEquals(50, $attempt->score);
        $this->assertFalse($attempt->passed);
    }

    public function test_quiz_has_many_attempts(): void
    {
        $training = $this->createTraining();
        $employee = User::factory()->create([
            'company_id' => $this->company->id,
            'role' => 'employee',
            'active' => true,
        ]);

        $quiz = Quiz::factory()->create([
            'training_id' => $training->id,
            'company_id' => $this->company->id,
        ]);

        // First attempt: fail
        QuizAttempt::factory()->create([
            'quiz_id' => $quiz->id,
            'user_id' => $employee->id,
            'company_id' => $this->company->id,
            'score' => 40,
            'passed' => false,
        ]);

        // Second attempt: pass
        QuizAttempt::factory()->create([
            'quiz_id' => $quiz->id,
            'user_id' => $employee->id,
            'company_id' => $this->company->id,
            'score' => 90,
            'passed' => true,
        ]);

        $this->actingAs($this->admin);

        $this->assertCount(2, $quiz->attempts);
        $this->assertFalse($quiz->attempts->first()->passed);
        $this->assertTrue($quiz->attempts->last()->passed);
    }

    public function test_user_has_quiz_attempts_relationship(): void
    {
        $training = $this->createTraining();
        $employee = User::factory()->create([
            'company_id' => $this->company->id,
            'role' => 'employee',
            'active' => true,
        ]);

        $quiz = Quiz::factory()->create([
            'training_id' => $training->id,
            'company_id' => $this->company->id,
        ]);

        QuizAttempt::factory()->count(3)->create([
            'quiz_id' => $quiz->id,
            'user_id' => $employee->id,
            'company_id' => $this->company->id,
        ]);

        $this->actingAs($employee);

        $this->assertCount(3, $employee->quizAttempts);
    }

    // ---- Training quizzes() returns all levels ----

    public function test_training_quizzes_returns_all_quiz_levels(): void
    {
        $training = $this->createTraining();
        $module = TrainingModule::factory()->create(['training_id' => $training->id]);
        $lesson = TrainingLesson::factory()->create(['module_id' => $module->id]);

        // Training-level quiz
        Quiz::factory()->create([
            'training_id' => $training->id,
            'company_id' => $this->company->id,
            'module_id' => null,
            'lesson_id' => null,
        ]);

        // Module-level quiz
        Quiz::factory()->create([
            'training_id' => $training->id,
            'company_id' => $this->company->id,
            'module_id' => $module->id,
            'lesson_id' => null,
        ]);

        // Lesson-level quiz
        Quiz::factory()->create([
            'training_id' => $training->id,
            'company_id' => $this->company->id,
            'module_id' => $module->id,
            'lesson_id' => $lesson->id,
        ]);

        $this->actingAs($this->admin);

        $this->assertCount(3, $training->quizzes);
    }
}
