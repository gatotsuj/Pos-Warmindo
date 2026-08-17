<?php

namespace App\Models\Akuntansi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalDetail extends Model
{
    use HasFactory;

    protected $table = 'akuntansi_jurnal_detail';

    protected $fillable = [
        'jurnal_id',
        'akun_id',
        'jenis',
        'jumlah',
        'catatan',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
    ];

    public function jurnal()
    {
        return $this->belongsTo(Jurnal::class, 'jurnal_id');
    }

    public function akun()
    {
        return $this->belongsTo(Akun::class, 'akun_id');
    }
}
