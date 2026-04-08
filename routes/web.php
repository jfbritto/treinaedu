<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\TrainingController as AdminTrainingController;
use App\Http\Controllers\Admin\CertificateController as AdminCertificateController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\EngagementController;
use App\Http\Controllers\Admin\CompanySettingsController;
use App\Http\Controllers\Admin\PathController;
use App\Http\Controllers\Employee\PathController as EmployeePathController;
use App\Http\Controllers\Instructor\TrainingController as InstructorTrainingController;
use App\Http\Controllers\Employee\TrainingController as EmployeeTrainingController;
use App\Http\Controllers\Employee\CertificateController as EmployeeCertificateController;
use App\Http\Controllers\Employee\QuizController;
use App\Http\Controllers\Api\TrainingProgressController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\CertificateVerificationController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperDashboardController;
use App\Http\Controllers\SuperAdmin\CompanyController as SuperCompanyController;
use App\Http\Controllers\SuperAdmin\SubscriptionController as SuperSubscriptionController;
use App\Http\Controllers\SuperAdmin\PaymentController as SuperPaymentController;
use App\Http\Controllers\SuperAdmin\PlanController as SuperPlanController;
use App\Http\Controllers\AsaasWebhookController;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', function () {
    $plans = \App\Models\Plan::where('active', true)->get();
    return view('welcome', compact('plans'));
})->name('home');

Route::get('/termos-de-uso', fn () => view('pages.termos-de-uso'))->name('termos');
Route::get('/politica-de-privacidade', fn () => view('pages.politica-de-privacidade'))->name('privacidade');
Route::get('/og-image-preview', fn () => view('pages.og-image'))->name('og-image-preview');

Route::get('/certificate/verify', [CertificateVerificationController::class, 'show'])
    ->name('certificate.verify');
Route::post('/certificate/verify', [CertificateVerificationController::class, 'verify'])
    ->name('certificate.verify.post');
Route::get('/certificate/{code}', [CertificateVerificationController::class, 'showByCode'])
    ->name('certificate.show');

// Asaas Webhook (excluded from CSRF)
Route::post('/asaas/webhook', [AsaasWebhookController::class, 'handle'])
    ->name('asaas.webhook')
    ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

require __DIR__.'/auth.php';

// Authenticated routes
Route::middleware(['auth', 'theme'])->group(function () {

    // Subscription plans (accessible even with expired subscription)
    Route::get('/subscription/plans', [SubscriptionController::class, 'plans'])
        ->name('subscription.plans');
    Route::post('/subscription/subscribe', [SubscriptionController::class, 'subscribe'])
        ->name('subscription.subscribe');

    // Routes that require active subscription
    Route::middleware('subscription')->group(function () {

        // Dashboard (single controller, dispatches by role)
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Admin routes
        Route::middleware('role:admin')->group(function () {
            Route::resource('users', UserController::class);
            Route::post('users/{user}/resend-invite', [UserController::class, 'resendInvite'])
                ->name('users.resend-invite');
            Route::resource('groups', GroupController::class);
            Route::resource('trainings', AdminTrainingController::class);
            Route::resource('paths', PathController::class);
            Route::post('paths/{path}/move-up', [PathController::class, 'moveUp'])->name('paths.move-up');
            Route::post('paths/{path}/move-down', [PathController::class, 'moveDown'])->name('paths.move-down');
            Route::post('trainings/{training}/assignments', [AdminTrainingController::class, 'storeAssignment'])
                ->name('trainings.assignments.store');
            Route::delete('trainings/{training}/assignments/{assignment}', [AdminTrainingController::class, 'destroyAssignment'])
                ->name('trainings.assignments.destroy');
            Route::get('certificates', [AdminCertificateController::class, 'index'])
                ->name('admin.certificates.index');
            Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
            Route::get('reports/filter', [ReportController::class, 'filter'])->name('reports.filter');
            Route::get('reports/export/pdf', [ReportController::class, 'exportPdf'])
                ->name('reports.export.pdf');
            Route::get('reports/export/excel', [ReportController::class, 'exportExcel'])
                ->name('reports.export.excel');
            Route::get('engagement', [EngagementController::class, 'index'])
                ->name('engagement.index');
            Route::get('subscription', [SubscriptionController::class, 'show'])
                ->name('subscription.show');
            Route::get('company/settings', [CompanySettingsController::class, 'edit'])
                ->name('company.settings');
            Route::put('company/settings', [CompanySettingsController::class, 'update'])
                ->name('company.settings.update');
        });

        // Instructor routes
        Route::middleware('role:instructor')->prefix('instructor')->name('instructor.')->group(function () {
            Route::resource('trainings', InstructorTrainingController::class);
        });

        // Employee routes
        Route::middleware('role:employee')->prefix('employee')->name('employee.')->group(function () {
            Route::get('trainings', [EmployeeTrainingController::class, 'index'])
                ->name('trainings.index');
            Route::get('trainings/{training}', [EmployeeTrainingController::class, 'show'])
                ->name('trainings.show');
            Route::post('trainings/{training}/complete', [EmployeeTrainingController::class, 'complete'])
                ->name('trainings.complete');
            Route::get('trainings/{training}/quiz', [QuizController::class, 'show'])
                ->name('quiz.show');
            Route::post('trainings/{training}/quiz', [QuizController::class, 'submit'])
                ->name('quiz.submit');
            Route::get('paths', [EmployeePathController::class, 'index'])
                ->name('paths.index');
            Route::get('paths/{path}', [EmployeePathController::class, 'show'])
                ->name('paths.show');
            Route::get('certificates', [EmployeeCertificateController::class, 'index'])
                ->name('certificates.index');
            Route::get('certificates/{certificate}/success', [EmployeeCertificateController::class, 'success'])
                ->name('certificates.success');
            Route::get('certificates/{certificate}', [EmployeeCertificateController::class, 'show'])
                ->name('certificates.show');
            Route::get('certificates/{certificate}/download', [EmployeeCertificateController::class, 'download'])
                ->name('certificates.download');
            Route::post('certificates/{training}/generate', [EmployeeCertificateController::class, 'generate'])
                ->name('certificates.generate');
        });
    });
});

// API routes (AJAX, auth via session)
Route::middleware('auth')->prefix('api')->group(function () {
    Route::post('training-progress', [TrainingProgressController::class, 'update'])
        ->name('api.training-progress')
        ->middleware('throttle:30,1');

    Route::post('lesson-progress', \App\Http\Controllers\Api\LessonProgressController::class)
        ->name('api.lesson-progress')
        ->middleware('throttle:30,1');
});

// Super Admin routes
Route::middleware(['auth', 'role:super_admin'])->prefix('super')->name('super.')->group(function () {
    Route::get('dashboard', [SuperDashboardController::class, 'index'])->name('dashboard');
    Route::resource('companies', SuperCompanyController::class);
    Route::get('subscriptions', [SuperSubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('payments', [SuperPaymentController::class, 'index'])->name('payments.index');
    Route::resource('plans', SuperPlanController::class);
});
