<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiPlatformReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_good_rating_redirects_directly_when_only_google_configured(): void
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

        $response = $this->get("/good/{$employee->id}");

        $response->assertOk();
        $response->assertSee('Copy Review & Open Google', false);
    }

    public function test_good_rating_renders_multi_destination_card_when_multiple_links_configured(): void
    {
        $user = User::factory()->create();
        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'Acme Cafe',
            'google_review_url' => 'https://g.page/r/test/review',
            'enable_multi_review_prompt' => true,
            'custom_links' => [
                ['name' => 'TripAdvisor', 'url' => 'https://www.tripadvisor.com/UserReview-test'],
            ],
        ]);
        $employee = Employee::create([
            'company_id' => $company->id,
            'name' => 'Alice',
        ]);

        $response = $this->get("/good/{$employee->id}");

        $response->assertOk();
        $response->assertSee('Where would you like to review us?');
        $response->assertSee('Post on TripAdvisor');
    }

    public function test_internal_feedback_view_displays_clean_private_badge(): void
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

        $response = $this->get("/ok/{$employee->id}");

        $response->assertOk();
        $response->assertSee('Tell us how we can improve');
        $response->assertDontSee('Private');
    }
}
