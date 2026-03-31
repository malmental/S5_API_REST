<?php

namespace Tests\Feature;

use App\Models\Incidence;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class IncidenceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected User $admin;

    protected Incidence $incidence;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['is_admin' => false]);
        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->incidence = Incidence::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_anyone_can_view_all_incidences(): void
    {
        $response = $this->getJson('/api/v1/incidences');
        $response->assertStatus(200);
    }

    public function test_can_view_single_incidence(): void
    {
        $response = $this->getJson("/api/v1/incidences/{$this->incidence->id}");
        $response->assertStatus(200)
            ->assertJsonPath('data.id', $this->incidence->id);
    }

    public function test_authenticated_user_can_create_incidence(): void
    {
        Passport::actingAs($this->user);

        $response = $this->postJson('/api/v1/incidences', [
            'title' => 'Test Incidence',
            'description' => 'Test description',
            'status' => 'open',
            'priority' => 'high',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('incidences', [
            'title' => 'Test Incidence',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_guest_cannot_create_incidence(): void
    {
        $response = $this->postJson('/api/v1/incidences', [
            'title' => 'Test Incidence',
        ]);

        $response->assertStatus(401);
    }

    public function test_owner_can_update_own_incidence(): void
    {
        Passport::actingAs($this->user);

        $response = $this->putJson("/api/v1/incidences/{$this->incidence->id}", [
            'title' => 'Updated Title',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('incidences', [
            'id' => $this->incidence->id,
            'title' => 'Updated Title',
        ]);
    }

    public function test_admin_can_update_any_incidence(): void
    {
        Passport::actingAs($this->admin);

        $response = $this->putJson("/api/v1/incidences/{$this->incidence->id}", [
            'title' => 'Admin Updated',
        ]);

        $response->assertStatus(200);
    }

    public function test_user_cannot_update_others_incidence(): void
    {
        $otherUser = User::factory()->create();
        Passport::actingAs($otherUser);

        $response = $this->putJson("/api/v1/incidences/{$this->incidence->id}", [
            'title' => 'Hacked Title',
        ]);

        $response->assertStatus(403);
    }

    public function test_owner_can_delete_own_incidence(): void
    {
        Passport::actingAs($this->user);

        $response = $this->deleteJson("/api/v1/incidences/{$this->incidence->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('incidences', ['id' => $this->incidence->id]);
    }

    public function test_admin_can_delete_any_incidence(): void
    {
        Passport::actingAs($this->admin);

        $response = $this->deleteJson("/api/v1/incidences/{$this->incidence->id}");

        $response->assertStatus(200);
    }

    public function test_user_cannot_delete_others_incidence(): void
    {
        $otherUser = User::factory()->create();
        Passport::actingAs($otherUser);

        $response = $this->deleteJson("/api/v1/incidences/{$this->incidence->id}");

        $response->assertStatus(403);
    }

    public function test_can_filter_by_status(): void
    {
        Incidence::factory()->create(['status' => 'closed', 'user_id' => $this->user->id]);

        $response = $this->getJson('/api/v1/incidences?status=open');

        $response->assertStatus(200);
    }

    public function test_can_filter_by_priority(): void
    {
        Incidence::factory()->create(['priority' => 'critical', 'user_id' => $this->user->id]);

        $response = $this->getJson('/api/v1/incidences?priority=high');

        $response->assertStatus(200);
    }

    public function test_can_search_by_keyword(): void
    {
        Incidence::factory()->create([
            'title' => 'Server Down',
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson('/api/v1/incidences?search=Server');

        $response->assertStatus(200);
    }

    public function test_incidence_resource_structure(): void
    {
        $incidence = Incidence::factory()->create();
        $resource = new \App\Http\Resources\IncidenceResource($incidence);
        $data = $resource->toArray(new \Illuminate\Http\Request);
    
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('priority', $data);
        $this->assertArrayHasKey('created_at', $data);
        $this->assertArrayHasKey('updated_at', $data);
    }

    public function test_create_incidence_validates_title_required(): void
    {
        Passport::actingAs($this->user);

        $response = $this->postJson('/api/v1/incidences', [
            'description' => 'Missing title',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('title');
    }

    public function test_create_incidence_validates_status_values(): void
    {
        Passport::actingAs($this->user);

        $response = $this->postJson('/api/v1/incidences', [
            'title' => 'Invalid Status',
            'status' => 'invalid_status',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('status');
    }

    public function test_create_incidence_validates_priority_values(): void
    {
        Passport::actingAs($this->user);

        $response = $this->postJson('/api/v1/incidences', [
            'title' => 'Invalid Priority',
            'priority' => 'invalid_priority',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('priority');
    }

    public function test_create_incidence_accepts_valid_assigned_user(): void
    {
        Passport::actingAs($this->user);
        
        $otherUser = User::factory()->create();
        
        $response = $this->postJson('/api/v1/incidences', [
            'title' => 'Test',
            'assigned_to' => $otherUser->id,
        ]);
    
        $response->assertStatus(201);
    
        $this->assertDatabaseHas('incidences', [
            'title' => 'Test',
            'assigned_to' => $otherUser->id,
    ]);
}

    public function test_update_incidence_validates_status_values(): void
    {
        Passport::actingAs($this->user);

        $response = $this->putJson("/api/v1/incidences/{$this->incidence->id}", [
            'status' => 'invalid_status',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('status');
    }
}
