<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashierShift extends Model
{
    use HasFactory;
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'starting_cash',
        'cash_sales',
        'non_cash_sales',
        'cash_expenses',
        'expected_cash',
        'actual_cash',
        'cash_difference',
        'opened_at',
        'closed_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'starting_cash'   => 'decimal:2',
        'cash_sales'      => 'decimal:2',
        'non_cash_sales'  => 'decimal:2',
        'cash_expenses'   => 'decimal:2',
        'expected_cash'   => 'decimal:2',
        'actual_cash'     => 'decimal:2',
        'cash_difference' => 'decimal:2',
        'opened_at'       => 'datetime',
        'closed_at'       => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
