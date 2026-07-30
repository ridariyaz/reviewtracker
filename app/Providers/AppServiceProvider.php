<?php

namespace App\Providers;

use App\Models\Feedback;
use App\Services\CompanyContext;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            $count = 0;
            if (Auth::guard('web')->check()) {
                $user = Auth::guard('web')->user();
                $company = app(CompanyContext::class)->currentFor($user);
                if ($company) {
                    $count = Feedback::where('company_id', $company->id)
                        ->where('status', 'new')
                        ->count();
                }
            }
            $view->with('unresolvedFeedbackCount', $count);
        });
    }
}
