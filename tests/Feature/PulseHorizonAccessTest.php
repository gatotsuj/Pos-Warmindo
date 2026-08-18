<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PulseHorizonAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_cannot_access_pulse_dashboard(): void
    {
        $cashier = User::factory()->create([
            'role' => User::ROLE_CASHIER,
            'tenant_id' => 1,
        ]);

        $response = $this->actingAs($cashier)->get('/pulse');

        $response->assertStatus(403);
    }

    public function test_cashier_cannot_access_horizon_dashboard(): void
    {
        $cashier = User::factory()->create([
            'role' => User::ROLE_CASHIER,
            'tenant_id' => 1,
        ]);

        $response = $this->actingAs($cashier)->get('/horizon');

        $response->assertStatus(403);
    }

    public function test_admin_can_access_pulse_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'tenant_id' => 1,
        ]);

        $response = $this->actingAs($admin)->get('/pulse');

        $response->assertStatus(200);
    }

    public function test_admin_can_access_horizon_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'tenant_id' => 1,
        ]);

        $response = $this->actingAs($admin)->get('/horizon');

        $response->assertStatus(200);
    }
}
