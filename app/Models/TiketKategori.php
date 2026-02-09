<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TiketKategori extends Model
{
    protected $table = 'tiket_kategoris';
    protected $guarded = ['id'];

    protected $casts = [
        'manfaat'    => 'array', 
        'is_populer' => 'boolean',
        'is_active'  => 'boolean',
        'harga'      => 'decimal:2',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
}
