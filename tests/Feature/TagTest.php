<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected User $admin;

    protected Tag $tag;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['is_admin' => false]);
        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->tag = Tag::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_anyone_can_view_tags(): void
    {
        $response = $this->getJson('/api/v1/tags');
        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_create_tag(): void
    {
        Passport::actingAs($this->user);

        $response = $this->postJson('/api/v1/tags', [
            'name' => 'newtag',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tags', ['name' => 'newtag']);
    }

    public function test_guest_cannot_create_tag(): void
    {
        $response = $this->postJson('/api/v1/tags', [
            'name' => 'newtag',
        ]);
        $response->assertStatus(401);
    }

    public function test_owner_can_update_own_tag(): void
    {
        Passport::actingAs($this->user);

        $response = $this->putJson("/api/v1/tags/{$this->tag->id}", [
            'name' => 'updatedtag',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('tags', [
            'id' => $this->tag->id,
            'name' => 'updatedtag',
        ]);
    }

    public function test_admin_can_update_any_tag(): void
    {
        Passport::actingAs($this->admin);

        $response = $this->putJson("/api/v1/tags/{$this->tag->id}", [
            'name' => 'adminupdated',
        ]);

        $response->assertStatus(200);
    }

    public function test_owner_or_admin_can_delete_tag(): void
    {
        Passport::actingAs($this->user);

        $response = $this->deleteJson("/api/v1/tags/{$this->tag->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('tags', ['id' => $this->tag->id]);
    }

    public function test_create_tag_validates_name_required(): void
    {
        Passport::actingAs($this->user);

        $response = $this->postJson('/api/v1/tags', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_create_tag_validates_name_max_length(): void
    {
        Passport::actingAs($this->user);

        $longName = str_repeat('a', 256);

        $response = $this->postJson('/api/v1/tags', [
            'name' => $longName,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_update_tag_validates_name_required(): void
    {
        Passport::actingAs($this->user);

        $response = $this->putJson("/api/v1/tags/{$this->tag->id}", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }
}
