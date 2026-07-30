<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerLanguageAndToggleTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_view_translates_to_malayalam_when_configured(): void
    {
        $user = User::factory()->create();
        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'Acme Cafe',
            'google_review_url' => 'https://g.page/r/test/review',
            'language' => 'ml',
        ]);
        $employee = Employee::create([
            'company_id' => $company->id,
            'name' => 'Alice',
        ]);

        $response = $this->get("/review/{$employee->id}");

        $response->assertOk();
        $response->assertSee('നിങ്ങളുടെ അനുഭവം എങ്ങനെയായിരുന്നു?');
        $response->assertSee('വളരെ നന്നായിരുന്നു!');
    }

    public function test_customer_view_translates_to_arabic_with_rtl_support(): void
    {
        $user = User::factory()->create();
        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'Acme Cafe',
            'google_review_url' => 'https://g.page/r/test/review',
            'language' => 'ar',
        ]);
        $employee = Employee::create([
            'company_id' => $company->id,
            'name' => 'Alice',
        ]);

        $response = $this->get("/review/{$employee->id}");

        $response->assertOk();
        $response->assertSee('كيف كانت تجربتك؟');
        $response->assertSee('dir="rtl"', false);
    }

    public function test_good_rating_redirects_directly_by_default_even_with_custom_links(): void
    {
        $user = User::factory()->create();
        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'Acme Cafe',
            'google_review_url' => 'https://g.page/r/test/review',
            'enable_multi_review_prompt' => false, // Default is OFF
            'custom_links' => [
                ['name' => 'TripAdvisor', 'url' => 'https://www.tripadvisor.com/test'],
            ],
        ]);
        $employee = Employee::create([
            'company_id' => $company->id,
            'name' => 'Alice',
        ]);

        $response = $this->get("/good/{$employee->id}");

        // Forwards DIRECTLY to Google Reviews URL with zero extra clicks
        $response->assertRedirect('https://g.page/r/test/review');
    }

    public function test_good_rating_shows_multi_destination_card_only_when_toggle_is_on(): void
    {
        $user = User::factory()->create();
        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'Acme Cafe',
            'google_review_url' => 'https://g.page/r/test/review',
            'enable_multi_review_prompt' => true, // Explicitly ON
            'custom_links' => [
                ['name' => 'TripAdvisor', 'url' => 'https://www.tripadvisor.com/test'],
            ],
        ]);
        $employee = Employee::create([
            'company_id' => $company->id,
            'name' => 'Alice',
        ]);

        $response = $this->get("/good/{$employee->id}");

        $response->assertOk();
        $response->assertSee('TripAdvisor');
    }
}
