<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityAndUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_signup_rejects_passwords_shorter_than_8_chars(): void
    {
        $response = $this->post('/signup', [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => '1234',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertDatabaseMissing('users', ['username' => 'testuser']);
    }

    public function test_public_review_landing_displays_emoji_rating_buttons(): void
    {
        $user = User::factory()->create();
        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'Acme Cafe',
            'google_review_url' => 'https://g.page/r/test/review',
        ]);
        $employee = Employee::create([
            'company_id' => $company->id,
            'name' => 'Alice',
        ]);

        $response = $this->get("/review/{$employee->id}");

        $response->assertOk();
        $response->assertSee('How was your experience?');
        $response->assertSee('Great!');
    }

    public function test_employee_dashboard_displays_gamification_achievements(): void
    {
        $user = User::factory()->create();
        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'Acme Cafe',
            'google_review_url' => 'https://g.page/r/test/review',
        ]);
        $employee = Employee::create([
            'company_id' => $company->id,
            'name' => 'Alice',
            'scans' => 10,
            'good_count' => 5,
        ]);

        $response = $this->actingAs($employee, 'employee')->get('/employee/dashboard');

        $response->assertOk();
        $response->assertSee('Your performance');
        $response->assertSee('Rank #1');
        $response->assertSee('5-Star Champion');
    }
}
