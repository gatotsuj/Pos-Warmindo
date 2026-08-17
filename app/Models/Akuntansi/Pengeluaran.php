<?php

namespace App\Models\Akuntansi;

use App\Models\Concerns\BelongsToTenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'akuntansi_pengeluaran';

    protected $fillable = [
        'tenant_id',
        'nomor_pengeluaran',
        'tanggal',
        'akun_beban_id',
        'akun_kas_id',
        'jumlah',
        'keterangan',
        'bukti_foto',
        'user_id',
        'jurnal_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2',
    ];

    public function akunBeban()
    {
        return $this->belongsTo(Akun::class, 'akun_beban_id');
    }

    public function akunKas()
    {
        return $this->belongsTo(Akun::class, 'akun_kas_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jurnal()
    {
        return $this->belongsTo(Jurnal::class, 'jurnal_id');
    }

    public function getFormattedJumlahAttribute()
    {
        return 'Rp ' . number_format($this->jumlah, 0, ',', '.');
    }
}
