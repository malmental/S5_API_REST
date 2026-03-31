<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Incidence;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected User $admin;

    protected Incidence $incidence;

    protected Comment $comment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['is_admin' => false]);
        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->incidence = Incidence::factory()->create(['user_id' => $this->user->id]);
        $this->comment = Comment::factory()->create([
            'user_id' => $this->user->id,
            'incidence_id' => $this->incidence->id,
        ]);
    }

    public function test_anyone_can_view_comments(): void
    {
        $response = $this->getJson("/api/v1/incidences/{$this->incidence->id}/comments");
        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_create_comment(): void
    {
        Passport::actingAs($this->user);

        $response = $this->postJson("/api/v1/incidences/{$this->incidence->id}/comments", [
            'body' => 'Test comment',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('comments', [
            'body' => 'Test comment',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_guest_cannot_create_comment(): void
    {
        $response = $this->postJson("/api/v1/incidences/{$this->incidence->id}/comments", [
            'body' => 'Test comment',
        ]);

        $response->assertStatus(401);
    }

    public function test_owner_can_update_own_comment(): void
    {
        Passport::actingAs($this->user);

        $response = $this->putJson("/api/v1/comments/{$this->comment->id}", [
            'body' => 'Updated comment',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('comments', [
            'id' => $this->comment->id,
            'body' => 'Updated comment',
        ]);
    }

    public function test_user_cannot_update_others_comment(): void
    {
        $otherUser = User::factory()->create();
        Passport::actingAs($otherUser);

        $response = $this->putJson("/api/v1/comments/{$this->comment->id}", [
            'body' => 'Hacked comment',
        ]);

        $response->assertStatus(403);
    }

    public function test_owner_can_delete_own_comment(): void
    {
        Passport::actingAs($this->user);

        $response = $this->deleteJson("/api/v1/comments/{$this->comment->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('comments', ['id' => $this->comment->id]);
    }

    public function test_admin_can_delete_any_comment(): void
    {
        Passport::actingAs($this->admin);

        $response = $this->deleteJson("/api/v1/comments/{$this->comment->id}");

        $response->assertStatus(200);
    }

    public function test_deleting_parent_deletes_children(): void
    {
        $childComment = Comment::factory()->create([
            'user_id' => $this->user->id,
            'incidence_id' => $this->incidence->id,
            'parent_id' => $this->comment->id,
        ]);

        Passport::actingAs($this->user);

        $response = $this->deleteJson("/api/v1/comments/{$this->comment->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('comments', ['id' => $this->comment->id]);
        $this->assertDatabaseMissing('comments', ['id' => $childComment->id]);
    }

    public function test_create_comment_validates_body_required(): void
    {
        Passport::actingAs($this->user);
        
        $response = $this->postJson("/api/v1/incidences/{$this->incidence->id}/comments", []);
        
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['body']);
    }

    public function test_create_comment_validates_body_max_length(): void
    {
        Passport::actingAs($this->user);

        $longBody = str_repeat('a', 10001);
    
        $response = $this->postJson("/api/v1/incidences/{$this->incidence->id}/comments", [
            'body' => $longBody,
        ]);
    
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['body']);
    }

    public function test_update_comment_validates_body_required(): void
    {
        Passport::actingAs($this->user);
        
        $response = $this->putJson("/api/v1/comments/{$this->comment->id}", []);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['body']);
    }
}
