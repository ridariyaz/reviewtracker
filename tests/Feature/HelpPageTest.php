<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HelpPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_help_page_loads_for_authenticated_admin(): void
    {
        $user = User::factory()->create();
        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'Test Business',
            'google_review_url' => 'https://g.page/r/test/review',
        ]);

        $response = $this->actingAs($user)->get('/help');

        $response->assertOk();
        $response->assertSee('How ReviewTracker Works');
        $response->assertSee('Configure Brand');
    }

    public function test_admin_dashboard_shows_quick_start_explainer(): void
    {
        $user = User::factory()->create();
        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'Test Business',
            'google_review_url' => 'https://g.page/r/test/review',
        ]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertOk();
        $response->assertSee('Quick Start Guide');
    }
}
