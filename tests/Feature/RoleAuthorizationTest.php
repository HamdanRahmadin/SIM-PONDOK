<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login()
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');

        $response = $this->get('/ustaz/dashboard');
        $response->assertRedirect('/login');

        $response = $this->get('/bendahara/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_access_only_admin_routes()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get('/ustaz/dashboard');
        $response->assertStatus(403);

        $response = $this->actingAs($admin)->get('/bendahara/dashboard');
        $response->assertStatus(403);
    }

    public function test_ustaz_can_access_only_ustaz_routes()
    {
        $ustaz = User::factory()->create(['role' => 'ustaz']);

        $response = $this->actingAs($ustaz)->get('/ustaz/dashboard');
        $response->assertStatus(200);

        $response = $this->actingAs($ustaz)->get('/admin/dashboard');
        $response->assertStatus(403);

        $response = $this->actingAs($ustaz)->get('/bendahara/dashboard');
        $response->assertStatus(403);
    }

    public function test_bendahara_can_access_only_bendahara_routes()
    {
        $bendahara = User::factory()->create(['role' => 'bendahara']);

        $response = $this->actingAs($bendahara)->get('/bendahara/dashboard');
        $response->assertStatus(200);

        $response = $this->actingAs($bendahara)->get('/admin/dashboard');
        $response->assertStatus(403);

        $response = $this->actingAs($bendahara)->get('/ustaz/dashboard');
        $response->assertStatus(403);
    }
}
