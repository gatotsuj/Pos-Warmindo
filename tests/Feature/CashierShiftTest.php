<?php

namespace Tests\Feature;

use App\Models\CashierShift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashierShiftTest extends TestCase
{
    use RefreshDatabase;

    protected User $cashier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cashier = User::factory()->create([
            'role' => User::ROLE_CASHIER,
            'tenant_id' => 1,
        ]);
    }

    public function test_cashier_can_open_shift_with_starting_cash(): void
    {
        $response = $this->actingAs($this->cashier)
            ->from('/pos')
            ->post('/shifts/open', [
                'starting_cash' => 150000,
            ]);

        $response->assertRedirect('/pos');

        $this->assertDatabaseHas('cashier_shifts', [
            'user_id' => $this->cashier->id,
            'starting_cash' => 150000,
            'status' => 'open',
        ]);
    }

    public function test_cashier_can_close_shift_and_reconcile_cash(): void
    {
        $shift = CashierShift::create([
            'tenant_id' => 1,
            'user_id' => $this->cashier->id,
            'starting_cash' => 100000,
            'cash_sales' => 50000,
            'opened_at' => now()->subHours(4),
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->cashier)
            ->from('/pos')
            ->post("/shifts/{$shift->id}/close", [
                'actual_cash' => 150000,
                'notes' => 'Tutup shift aman tanpa selisih',
            ]);

        $response->assertRedirect('/pos');

        $this->assertDatabaseHas('cashier_shifts', [
            'id' => $shift->id,
            'actual_cash' => 150000,
            'status' => 'closed',
        ]);
    }
}
