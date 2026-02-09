<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas'; // Explicit table name

    protected $guarded = ['id'];

    protected $casts = [
        'gambaran_event' => 'array', // Auto convert JSON string to array
        'is_link_eksternal' => 'boolean',
    ];

    public function kategori()
    {
        return $this->belongsTo(Categories::class, 'kategori_id');
    }

    public function ticketCategories()
    {
        return $this->hasMany(TiketKategori::class, 'kelas_id');
    }

    public function participants()
    {
        return $this->hasMany(KelasPeserta::class, 'kelas_id');
    }
}
