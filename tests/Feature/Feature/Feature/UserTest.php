<?php

namespace Tests\Feature\Feature\Feature;

use App\Models\Incidence;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->user = User::factory()->create(['is_admin' => false]);
    }

    public function test_admin_can_list_users(): void
    {
        
        Passport::actingAs($this->admin);
        
        $response = $this->getJson('/api/v1/users');
        
        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_admin_can_view_user(): void
    {
        Passport::actingAs($this->admin);

        $response = $this->getJson("/api/v1/users/{$this->user->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $this->user->id);
    }

    public function test_admin_can_view_user_incidences(): void
    {
        Passport::actingAs($this->admin);

        Incidence::factory()->create(['user_id' => $this->user->id]);
        Incidence::factory()->create(['assigned_to' => $this->user->id]);

        $response = $this->getJson("/api/v1/users/{$this->user->id}/incidences");

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data'));
    }

    public function test_admin_can_delete_user(): void
    {
        Passport::actingAs($this->admin);
        
        $response = $this->deleteJson("/api/v1/users/{$this->user->id}");
        
        $response->assertStatus(200)
            ->assertJsonPath('message', 'User deleted successfully.');
        $this->assertDatabaseMissing('users', ['id' => $this->user->id]);
    }

    public function test_non_admin_cannot_access_users(): void
    {
        Passport::actingAs($this->user);
    
        $response = $this->getJson('/api/v1/users');
    
        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_users(): void
    {
        $response = $this->getJson('/api/v1/users');

        $response->assertStatus(401);
    }

    public function test_admin_cannot_delete_self(): void
    {
        Passport::actingAs($this->admin);

        $response = $this->deleteJson("/api/v1/users/{$this->admin->id}");

        $response->assertStatus(403);
    }
}
