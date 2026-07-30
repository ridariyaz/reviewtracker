<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SettingsAndMultiLangTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword123'),
        ]);
        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'Test Cafe',
            'google_review_url' => 'https://g.page/r/test/review',
        ]);

        $response = $this->actingAs($user)->post('/settings/password', [
            'current_password' => 'oldpassword123',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHas('success_password');
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    public function test_admin_can_save_language_and_custom_review_links(): void
    {
        $user = User::factory()->create();
        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'Test Cafe',
            'google_review_url' => 'https://g.page/r/test/review',
        ]);

        $response = $this->actingAs($user)->post('/settings/preferences', [
            'language' => 'ml',
            'custom_link_name' => ['TripAdvisor', 'Yelp'],
            'custom_link_url' => ['https://www.tripadvisor.com/test', 'https://www.yelp.com/test'],
        ]);

        $response->assertSessionHas('success_pref');
        $this->assertEquals('ml', $company->fresh()->language);
        $this->assertCount(2, $company->fresh()->custom_links);
    }

    public function test_internal_feedback_view_does_not_contain_private_word(): void
    {
        $user = User::factory()->create();
        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'Test Cafe',
            'google_review_url' => 'https://g.page/r/test/review',
        ]);
        $employee = Employee::create([
            'company_id' => $company->id,
            'name' => 'Alice',
        ]);

        $response = $this->get("/ok/{$employee->id}");

        $response->assertOk();
        $response->assertSee('Tell us how we can improve');
        $response->assertDontSee('Private Feedback');
        $response->assertDontSee('<div class="pill">Private</div>', false);
    }
}
