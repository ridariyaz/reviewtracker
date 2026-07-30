<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Employee;
use App\Models\ScanLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsScanTest extends TestCase
{
    use RefreshDatabase;

    public function test_scanning_employee_qr_creates_scan_log(): void
    {
        $user = User::factory()->create();
        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'Test Company',
            'google_review_url' => 'https://g.page/r/test/review',
        ]);
        $employee = Employee::create([
            'company_id' => $company->id,
            'name' => 'Jane Doe',
        ]);

        $response = $this->get("/review/{$employee->id}");

        $response->assertOk();
        $this->assertDatabaseHas('scan_logs', [
            'company_id' => $company->id,
            'employee_id' => $employee->id,
        ]);
    }

    public function test_analytics_dashboard_renders_with_scan_logs(): void
    {
        $user = User::factory()->create();
        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'Test Company',
            'google_review_url' => 'https://g.page/r/test/review',
        ]);
        $employee = Employee::create([
            'company_id' => $company->id,
            'name' => 'Jane Doe',
        ]);

        ScanLog::create([
            'company_id' => $company->id,
            'employee_id' => $employee->id,
            'device_type' => 'mobile',
        ]);

        $response = $this->actingAs($user)->get('/analytics');

        $response->assertOk();
        $response->assertSee('Conversion Funnel');
        $response->assertSee('Jane Doe');
    }
}
