<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\SaasSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaasAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_standard_admin_cannot_access_saas_superadmin_portal(): void
    {
        $user = User::factory()->create(['is_superadmin' => false]);

        $response = $this->actingAs($user)->get('/saas-admin');

        $response->assertStatus(403);
    }

    public function test_superadmin_can_access_saas_superadmin_portal(): void
    {
        $superadmin = User::factory()->create(['is_superadmin' => true]);

        $response = $this->actingAs($superadmin)->get('/saas-admin');

        $response->assertOk();
        $response->assertSee('SaaS Super Admin Portal');
        $response->assertSee('Registered Customer Accounts');
    }

    public function test_superadmin_can_toggle_account_suspension_status(): void
    {
        $superadmin = User::factory()->create(['is_superadmin' => true]);
        $client = User::factory()->create(['status' => 'active']);

        $response = $this->actingAs($superadmin)->post("/saas-admin/users/{$client->id}/status");

        $response->assertRedirect();
        $this->assertEquals('suspended', $client->fresh()->status);
    }

    public function test_superadmin_can_impersonate_client_account_for_troubleshooting(): void
    {
        $superadmin = User::factory()->create(['is_superadmin' => true]);
        $client = User::factory()->create();
        $company = Company::create([
            'user_id' => $client->id,
            'name' => 'Client Business',
            'google_review_url' => 'https://g.page/r/test/review',
        ]);

        $response = $this->actingAs($superadmin)->post("/saas-admin/users/{$client->id}/impersonate");

        $response->assertRedirect(route('admin'));
        $this->assertAuthenticatedAs($client);
        $this->assertEquals($superadmin->id, session('impersonator_id'));

        // Test exit impersonation
        $exitResponse = $this->post('/saas-admin/stop-impersonating');
        $exitResponse->assertRedirect(route('saas_admin.index'));
        $this->assertAuthenticatedAs($superadmin);
    }

    public function test_superadmin_can_save_global_scripts_and_announcements(): void
    {
        $superadmin = User::factory()->create(['is_superadmin' => true]);

        $response = $this->actingAs($superadmin)->post('/saas-admin/code/save', [
            'global_announcement' => 'Maintenance Scheduled at 2 AM',
            'global_script' => '<script>console.log("Analytics Loaded")</script>',
        ]);

        $response->assertRedirect();
        $this->assertEquals('Maintenance Scheduled at 2 AM', SaasSetting::get('global_announcement'));
        $this->assertEquals('<script>console.log("Analytics Loaded")</script>', SaasSetting::get('global_script'));
    }

    public function test_diagnostics_page_loads_for_superadmin(): void
    {
        $superadmin = User::factory()->create(['is_superadmin' => true]);

        $response = $this->actingAs($superadmin)->get('/saas-admin/diagnostics');

        $response->assertOk();
        $response->assertSee('Server Environment');
        $response->assertSee('Database Table Statistics');
    }
}
