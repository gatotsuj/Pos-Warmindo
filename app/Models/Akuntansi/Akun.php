<?php

namespace App\Models\Akuntansi;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Akun extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'akuntansi_akun';

    protected $fillable = [
        'tenant_id',
        'kode_akun',
        'nama_akun',
        'kategori',
        'posisi_normal',
        'is_system',
        'parent_id',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(Akun::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Akun::class, 'parent_id');
    }

    public function jurnalDetails()
    {
        return $this->hasMany(JurnalDetail::class, 'akun_id');
    }

    // Helper Kategori Label
    public function getKategoriLabelAttribute()
    {
        return match ($this->kategori) {
            'aset_lancar' => 'Aset Lancar',
            'aset_tetap' => 'Aset Tetap',
            'kewajiban' => 'Kewajiban / Utang',
            'ekuitas' => 'Ekuitas / Modal',
            'pendapatan' => 'Pendapatan Usaha',
            'hpp' => 'Beban Pokok Penjualan (HPP)',
            'beban_operasional' => 'Beban Operasional',
            default => ucfirst(str_replace('_', ' ', $this->kategori)),
        };
    }
}
