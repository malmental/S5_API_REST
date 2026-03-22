<?php

namespace Tests\Feature\Feature;

use App\Models\Incidence;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class MetricTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        Incidence::factory()->create(['status' => 'open', 'priority' => 'high', 'user_id' => $this->user->id]);
        Incidence::factory()->create(['status' => 'open', 'priority' => 'low', 'user_id' => $this->user->id]);
        Incidence::factory()->create(['status' => 'in_progress', 'priority' => 'medium', 'user_id' => $this->user->id]);
        Incidence::factory()->create(['status' => 'resolved', 'priority' => 'critical', 'user_id' => $this->user->id]);
        Incidence::factory()->create(['status' => 'closed', 'priority' => 'low', 'user_id' => $this->user->id]);
    }

    public function test_authenticated_user_can_view_metrics(): void
    {
        Passport::actingAs($this->user);

        $response = $this->getJson('/api/v1/metrics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'by_status' => [
                        'open',
                        'in_progress',
                        'resolved',
                        'closed',
                    ],
                    'by_priority' => [
                        'low',
                        'medium',
                        'high',
                        'critical',
                    ],
                ],
            ]);
    }

    public function test_guest_cannot_view_metrics(): void
    {
        $response = $this->getJson('/api/v1/metrics');

        $response->assertStatus(401);
    }
    
    public function test_metrics_contains_by_status(): void
    {
        Passport::actingAs($this->user);

        $response = $this->getJson('/api/v1/metrics');

        $response->assertStatus(200);

        $data = $response->json('data.by_status');

        $this->assertArrayHasKey('open', $data);
        $this->assertArrayHasKey('in_progress', $data);
        $this->assertArrayHasKey('resolved', $data);
        $this->assertArrayHasKey('closed', $data);
    }

    public function test_metrics_contains_by_priority(): void
    {
        Passport::actingAs($this->user);

        $response = $this->getJson('/api/v1/metrics');

        $response->assertStatus(200);

        $data = $response->json('data.by_priority');

        $this->assertArrayHasKey('low', $data);
        $this->assertArrayHasKey('medium', $data);
        $this->assertArrayHasKey('high', $data);
        $this->assertArrayHasKey('critical', $data);
    }

    public function test_metrics_shows_correct_counts(): void
    {
        Passport::actingAs($this->user);

        $response = $this->getJson('/api/v1/metrics');

        $response->assertStatus(200);

        $byStatus = $response->json('data.by_status');

        $this->assertCount(2, $byStatus['open']);
        $this->assertCount(1, $byStatus['in_progress']);
        $this->assertCount(1, $byStatus['resolved']);
        $this->assertCount(1, $byStatus['closed']);
    }
}
