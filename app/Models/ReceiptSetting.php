<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptSetting extends Model
{
    use HasFactory;
    use \App\Models\Concerns\BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'store_name',
        'store_address',
        'store_phone',
        'header_line_1',
        'header_line_2',
        'footer_line_1',
        'footer_line_2',
        'tax_percent',
        'tax_enabled',
        'discount_enabled',
    ];

    protected $casts = [
        'tax_percent'       => 'decimal:2',
        'tax_enabled'       => 'boolean',
        'discount_enabled'  => 'boolean',
    ];
}

