<?php

/**
 * HTTP routes for ReviewTracker.
 *
 * Groups:
 * - Public auth (admin + employee login)
 * - Admin area (middleware: admin)
 *   - Setup + help (allowed before Google review URL is set)
 *   - Feature pages (middleware: company.configured)
 * - Employee portal (middleware: employee)
 * - Public customer review funnel (no auth)
 *
 * See docs/ARCHITECTURE.md for product flow and code map.
 */

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeePortalController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SetupController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SaasAdminController;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('admin')
        : redirect()->route('login');
});

// --- Exit Troubleshooting Mode (accessible by impersonated admin) ---
Route::post('/saas-admin/stop-impersonating', [SaasAdminController::class, 'stopImpersonating'])->name('saas_admin.stop_impersonating');

// --- SaaS Super Admin Portal ---
Route::middleware(['admin', 'superadmin'])->prefix('saas-admin')->group(function () {
    Route::get('/', [SaasAdminController::class, 'index'])->name('saas_admin.index');
    Route::get('/users', [SaasAdminController::class, 'users'])->name('saas_admin.users');
    Route::post('/users/{user}/status', [SaasAdminController::class, 'toggleStatus'])->name('saas_admin.users.status');
    Route::post('/users/{user}/superadmin', [SaasAdminController::class, 'toggleSuperAdmin'])->name('saas_admin.users.superadmin');
    Route::post('/users/{user}/impersonate', [SaasAdminController::class, 'impersonate'])->name('saas_admin.users.impersonate');
    Route::post('/users/{user}/delete', [SaasAdminController::class, 'deleteUser'])->name('saas_admin.users.delete');

    Route::get('/code', [SaasAdminController::class, 'codeInjector'])->name('saas_admin.code');
    Route::post('/code/save', [SaasAdminController::class, 'saveCode'])->name('saas_admin.code.save');
    Route::get('/diagnostics', [SaasAdminController::class, 'diagnostics'])->name('saas_admin.diagnostics');
});

// --- Admin authentication ---
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup');
Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- Employee authentication ---
Route::get('/employee/login', [EmployeePortalController::class, 'showLogin'])->name('employee.login');
Route::post('/employee/login', [EmployeePortalController::class, 'login']);
Route::post('/employee/logout', [EmployeePortalController::class, 'logout'])->name('employee.logout');

// --- Admin (company owners) ---
Route::middleware('admin')->group(function () {
    // Allowed before Google review URL is configured
    Route::get('/setup', [SetupController::class, 'show'])->name('setup.show');
    Route::post('/setup', [SetupController::class, 'store'])->name('setup.store');
    Route::get('/help', [HelpController::class, 'show'])->name('help');

    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/company', [\App\Http\Controllers\SettingsController::class, 'updateCompany'])->name('settings.company');
    Route::post('/settings/password', [\App\Http\Controllers\SettingsController::class, 'updatePassword'])->name('settings.password');

    // Blocked until company has a valid Google review URL
    Route::middleware('company.configured')->group(function () {
        Route::get('/admin', [AdminController::class, 'index'])->name('admin');

        Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
        Route::post('/companies/create', [CompanyController::class, 'store'])->name('companies.store');
        Route::post('/companies/{company}/update', [CompanyController::class, 'update'])->name('companies.update');
        Route::post('/companies/switch', [CompanyController::class, 'switch'])->name('companies.switch');

        Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
        Route::post('/add_employee', [EmployeeController::class, 'store'])->name('employees.store');
        Route::post('/edit_employee/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::post('/employee/{employee}/credentials', [EmployeeController::class, 'updateCredentials'])->name('employees.credentials');
        Route::post('/delete_employee/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

        Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
        Route::post('/feedback/{feedback}/status', [FeedbackController::class, 'updateStatus'])->name('feedback.status');
        Route::get('/export/employees.csv', [FeedbackController::class, 'exportEmployees'])->name('export.employees');
        Route::get('/export/feedback.csv', [FeedbackController::class, 'exportFeedback'])->name('export.feedback');

        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
    });
});

// --- Employee portal ---
Route::middleware('employee')->group(function () {
    Route::get('/employee/dashboard', [EmployeePortalController::class, 'dashboard'])->name('employee.dashboard');
    Route::get('/employee/qr', [EmployeePortalController::class, 'qr'])->name('employee.qr');
    Route::post('/employee/force_win', [EmployeePortalController::class, 'toggleForceWin'])->name('employee.force_win');
});

// --- Public customer review funnel (QR landing with rate-limiting protection) ---
Route::middleware('throttle:10,1')->group(function () {
    Route::get('/review/{employee}', [ReviewController::class, 'show'])->name('review.show');
    Route::get('/good/{employee}', [ReviewController::class, 'good'])->name('review.good');
    Route::get('/ok/{employee}', [ReviewController::class, 'ok'])->name('review.ok');
    Route::get('/bad/{employee}', [ReviewController::class, 'bad'])->name('review.bad');
    Route::post('/submit_internal_feedback', [ReviewController::class, 'submitInternal'])->name('review.submit');
    Route::get('/thankyou', [ReviewController::class, 'thankyou'])->name('thankyou');
});
