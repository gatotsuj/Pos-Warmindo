<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use \App\Models\Concerns\BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'product_id',
        'user_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'reference',
        'notes',
    ];

    protected $casts = [
        'quantity'     => 'integer',
        'stock_before' => 'integer',
        'stock_after'  => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Helpers ────────────────────────────────────────────────

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'in'   => 'Stok Masuk',
            'out'  => 'Penjualan',
            'void' => 'Void Transaksi',
            default => $this->type,
        };
    }

    public function getTypeBadgeClassAttribute(): string
    {
        return match ($this->type) {
            'in'   => 'bg-green-100 text-green-700',
            'out'  => 'bg-blue-100 text-blue-700',
            'void' => 'bg-amber-100 text-amber-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }
}
