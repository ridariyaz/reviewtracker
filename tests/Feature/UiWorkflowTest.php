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
