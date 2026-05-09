<?php

namespace Tests\Feature\Api;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_list_only_their_orders(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Sanctum::actingAs($user);

        Order::create([
            'user_id' => $user->id,
            'total' => 49.99,
            'status' => 'pending',
        ]);

        Order::create([
            'user_id' => $otherUser->id,
            'total' => 99.99,
            'status' => 'completed',
        ]);

        $response = $this->getJson('/api/orders');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('code', 200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.user_id', $user->id);
    }

    public function test_authenticated_user_can_create_an_order_for_themselves(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/orders', [
            'total' => 120.50,
            'status' => 'pending',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('code', 201)
            ->assertJsonPath('data.user_id', $user->id)
            ->assertJsonPath('data.status', 'pending');

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'total' => 120.50,
            'status' => 'pending',
        ]);
    }

    public function test_authenticated_user_can_only_delete_their_own_order(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Sanctum::actingAs($user);

        $ownOrder = Order::create([
            'user_id' => $user->id,
            'total' => 10.00,
            'status' => 'pending',
        ]);

        $otherOrder = Order::create([
            'user_id' => $otherUser->id,
            'total' => 20.00,
            'status' => 'pending',
        ]);

        $this->deleteJson('/api/orders/' . $otherOrder->id)
            ->assertForbidden();

        $this->deleteJson('/api/orders/' . $ownOrder->id)
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertSoftDeleted('orders', [
            'id' => $ownOrder->id,
        ]);
    }
}
