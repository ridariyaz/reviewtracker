<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UiWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_creation_with_modal_credentials(): void
    {
        $user = User::factory()->create();
        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'Test Company',
            'google_review_url' => 'https://g.page/r/test/review',
        ]);

        $response = $this->actingAs($user)->withSession(['company_id' => $company->id])->post('/add_employee', [
            'name' => 'John Wick',
            'employee_username' => 'johnwick',
            'employee_password' => 'secret123',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('employees', [
            'company_id' => $company->id,
            'name' => 'John Wick',
            'employee_username' => 'johnwick',
        ]);
    }

    public function test_different_companies_can_have_employees_with_same_username(): void
    {
        $user = User::factory()->create();
        $company1 = Company::create([
            'user_id' => $user->id,
            'name' => 'sq',
            'google_review_url' => 'https://g.page/r/sq/review',
        ]);
        $company2 = Company::create([
            'user_id' => $user->id,
            'name' => 'isouq',
            'google_review_url' => 'https://g.page/r/isouq/review',
        ]);

        // Add employee 'riyaz' under company 'sq'
        $this->actingAs($user)->withSession(['company_id' => $company1->id])->post('/add_employee', [
            'name' => 'Riyaz SQ',
            'employee_username' => 'riyaz',
            'employee_password' => 'pass123',
        ])->assertSessionHasNoErrors();

        // Add employee 'riyaz' under company 'isouq' (different company)
        $this->actingAs($user)->withSession(['company_id' => $company2->id])->post('/add_employee', [
            'name' => 'Riyaz ISOUQ',
            'employee_username' => 'riyaz',
            'employee_password' => 'pass456',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('employees', ['company_id' => $company1->id, 'employee_username' => 'riyaz']);
        $this->assertDatabaseHas('employees', ['company_id' => $company2->id, 'employee_username' => 'riyaz']);
    }

    public function test_unresolved_feedback_badge_appears_on_dashboard(): void
    {
        $user = User::factory()->create();
        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'Test Company',
            'google_review_url' => 'https://g.page/r/test/review',
        ]);

        Feedback::create([
            'company_id' => $company->id,
            'rating' => 'bad',
            'comment' => 'Needs improvement',
            'status' => 'new',
        ]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertOk();
        $response->assertSee('nav-badge');
    }
}
