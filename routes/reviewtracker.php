<?php

use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Employee\EmployeeAuthController;
use App\Http\Controllers\Employee\EmployeeDashboardController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

// -----------------------------------------------------------------
// PUBLIC ROUTES -- no login required. These are what customers hit
// after scanning an employee's QR code.
// -----------------------------------------------------------------
Route::get('/review/{employee}', [ReviewController::class, 'show'])->name('review.show');
Route::get('/good/{employee}', [ReviewController::class, 'good'])->name('review.good');
Route::get('/ok/{employee}', [ReviewController::class, 'ok'])->name('review.ok');
Route::get('/bad/{employee}', [ReviewController::class, 'bad'])->name('review.bad');
Route::post('/submit-internal-feedback', [ReviewController::class, 'submitInternalFeedback'])->name('review.submit');
Route::get('/thankyou', [ReviewController::class, 'thankyou'])->name('review.thankyou');

// -----------------------------------------------------------------
// EMPLOYEE ROUTES -- lightweight login separate from admin auth.
// -----------------------------------------------------------------
Route::get('/employee/login', [EmployeeAuthController::class, 'showLogin'])->name('employee.login');
Route::post('/employee/login', [EmployeeAuthController::class, 'login']);
Route::post('/employee/logout', [EmployeeAuthController::class, 'logout'])->name('employee.logout');

Route::middleware('employee.auth')->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');
    Route::get('/qr', [EmployeeDashboardController::class, 'qrFullscreen'])->name('qr');
});

// -----------------------------------------------------------------
// ADMIN ROUTES -- wrapped in riyaloerp's existing 'auth' middleware,
// so only logged-in admin users of riyaloerp can reach these.
// Adjust the middleware name below ('auth') if riyaloerp uses a
// different guard/middleware name for its own admin login.
// -----------------------------------------------------------------
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/feedback', [DashboardController::class, 'feedback'])->name('feedback');
    Route::post('/feedback/{feedback}/status', [DashboardController::class, 'updateStatus'])->name('feedback.status');
    Route::get('/export/employees.csv', [DashboardController::class, 'exportEmployeesCsv'])->name('export.employees');
    Route::get('/export/feedback.csv', [DashboardController::class, 'exportFeedbackCsv'])->name('export.feedback');

    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::post('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::post('/employees/{employee}/credentials', [EmployeeController::class, 'updateCredentials'])->name('employees.credentials');
    Route::post('/employees/{employee}/delete', [EmployeeController::class, 'destroy'])->name('employees.delete');

    Route::get('/companies', [CompanyController::class, 'index'])->name('companies');
    Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
    Route::post('/companies/{company}', [CompanyController::class, 'update'])->name('companies.update');
    Route::post('/companies/switch', [CompanyController::class, 'switch'])->name('companies.switch');
});
