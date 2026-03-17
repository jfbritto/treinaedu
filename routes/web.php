<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\TrainingController as AdminTrainingController;
use App\Http\Controllers\Admin\TrainingAssignmentController;
use App\Http\Controllers\Admin\CertificateController as AdminCertificateController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\CompanySettingsController;
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
    return view('welcome');
})->name('home');

Route::get('/certificate/verify', [CertificateVerificationController::class, 'show'])
    ->name('certificate.verify');
Route::post('/certificate/verify', [CertificateVerificationController::class, 'verify'])
    ->name('certificate.verify.post');

// Asaas Webhook (excluded from CSRF)
Route::post('/asaas/webhook', [AsaasWebhookController::class, 'handle'])
    ->name('asaas.webhook')
    ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

// Auth (Breeze) — override register routes
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
});

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
            Route::resource('users', UserController::class)->except('show');
            Route::resource('groups', GroupController::class);
            Route::resource('trainings', AdminTrainingController::class);
            Route::resource('training-assignments', TrainingAssignmentController::class)
                ->only(['index', 'create', 'store', 'destroy']);
            Route::get('certificates', [AdminCertificateController::class, 'index'])
                ->name('admin.certificates.index');
            Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
            Route::get('reports/export/pdf', [ReportController::class, 'exportPdf'])
                ->name('reports.export.pdf');
            Route::get('reports/export/excel', [ReportController::class, 'exportExcel'])
                ->name('reports.export.excel');
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
        Route::middleware('role:employee')->group(function () {
            Route::get('trainings/{training}', [EmployeeTrainingController::class, 'show'])
                ->name('employee.trainings.show');
            Route::post('trainings/{training}/complete', [EmployeeTrainingController::class, 'complete'])
                ->name('employee.trainings.complete');
            Route::get('trainings/{training}/quiz', [QuizController::class, 'show'])
                ->name('employee.quiz.show');
            Route::post('trainings/{training}/quiz', [QuizController::class, 'submit'])
                ->name('employee.quiz.submit');
            Route::get('certificates', [EmployeeCertificateController::class, 'index'])
                ->name('employee.certificates.index');
            Route::get('certificates/{certificate}/download', [EmployeeCertificateController::class, 'download'])
                ->name('employee.certificates.download');
            Route::post('certificates/{training}/generate', [EmployeeCertificateController::class, 'generate'])
                ->name('employee.certificates.generate');
        });
    });
});

// API routes (AJAX, auth via session)
Route::middleware('auth')->prefix('api')->group(function () {
    Route::post('training-progress', [TrainingProgressController::class, 'update'])
        ->name('api.training-progress')
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
