<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    //
    use HasFactory;
    use \App\Models\Concerns\BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'invoice_number',
        'user_id',
        'subtotal',
        'discount_percent',
        'discount_amount',
        'tax_percent',
        'tax_amount',
        'grand_total',
        'payment_method',
        'paid_amount',
        'change_amount',
        'notes',
        'status',
        'voided_at',
        'voided_by',
        'void_reason',
    ];

    protected $casts = [
        'subtotal'         => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount'  => 'decimal:2',
        'tax_percent'      => 'decimal:2',
        'tax_amount'       => 'decimal:2',
        'grand_total'      => 'decimal:2',
        'paid_amount'      => 'decimal:2',
        'change_amount'    => 'decimal:2',
        'voided_at'        => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function voidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'voided_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'reference', 'invoice_number');
    }

    // Status helpers
    public function isVoided(): bool
    {
        return $this->status === 'voided';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    // Generate unique invoice number: INV-YYYYMMDD-XXXX
    public static function generateInvoiceNumber(): string
    {
        $date = now()->format('Ymd');
        $prefix = "INV-{$date}-";

        $lastTransaction = self::query()
            ->where('invoice_number', 'like', "{$prefix}%")
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastTransaction) {
            $lastNumber = (int) substr($lastTransaction->invoice_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    // Accessors
    public function getFormattedGrandTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->grand_total, 0, ',', '.');
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->created_at->format('d M Y H:i');
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'cash' => 'Cash',
            'card' => 'Kartu Debit/Kredit',
            'qris' => 'QRIS',
            default => $this->payment_method,
        };
    }
}
