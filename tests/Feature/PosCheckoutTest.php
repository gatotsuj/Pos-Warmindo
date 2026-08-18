<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ReceiptSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected User $cashier;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Create user with cashier role
        $this->cashier = User::factory()->create([
            'role' => User::ROLE_CASHIER,
            'tenant_id' => 1,
        ]);

        // Create category and product
        $category = Category::create([
            'name' => 'Makanan',
            'tenant_id' => 1,
        ]);

        $this->product = Product::create([
            'name' => 'Indomie Goreng Spesiil',
            'sku' => 'IND-G-001',
            'price' => 12000,
            'cost_price' => 7000,
            'stock' => 50,
            'category_id' => $category->id,
            'is_active' => true,
            'tenant_id' => 1,
        ]);

        ReceiptSetting::create([
            'tenant_id' => 1,
            'store_name' => 'Warmindo Utama',
            'tax_percent' => 11,
            'tax_enabled' => true,
            'discount_enabled' => true,
            'is_cash_enabled' => true,
            'is_qris_enabled' => true,
            'is_card_enabled' => true,
        ]);
    }

    public function test_pos_page_can_be_accessed_by_cashier(): void
    {
        $response = $this->actingAs($this->cashier)->get('/pos');
        $response->assertStatus(200);
    }

    public function test_cashier_can_add_product_to_cart(): void
    {
        $response = $this->actingAs($this->cashier)->post('/pos/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
    }

    public function test_checkout_reduces_stock_and_creates_transaction(): void
    {
        // Add 1 item to cart
        $this->actingAs($this->cashier)->post('/pos/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        // Checkout
        $response = $this->actingAs($this->cashier)->post('/pos/checkout', [
            'payment_method' => 'cash',
            'paid_amount' => 50000,
            'discount_percent' => 0,
            'notes' => 'Test Checkout Automated',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        // Verify stock reduced from 50 to 49
        $this->assertDatabaseHas('products', [
            'id' => $this->product->id,
            'stock' => 49,
        ]);

        // Verify transaction recorded
        $this->assertDatabaseHas('transactions', [
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);
    }
}
