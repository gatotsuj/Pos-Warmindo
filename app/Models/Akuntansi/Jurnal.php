<?php

namespace App\Models\Akuntansi;

use App\Models\Concerns\BelongsToTenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'akuntansi_jurnal';

    protected $fillable = [
        'tenant_id',
        'nomor_jurnal',
        'tanggal',
        'sumber_transaksi',
        'referensi_type',
        'referensi_id',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function details()
    {
        return $this->hasMany(JurnalDetail::class, 'jurnal_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function referensi()
    {
        return $this->morphTo(__FUNCTION__, 'referensi_type', 'referensi_id');
    }

    public function getTotalDebitAttribute()
    {
        return $this->details->where('jenis', 'debit')->sum('jumlah');
    }

    public function getTotalKreditAttribute()
    {
        return $this->details->where('jenis', 'kredit')->sum('jumlah');
    }
}
