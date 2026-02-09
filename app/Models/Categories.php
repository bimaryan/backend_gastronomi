<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    protected $fillable = ['nama'];

    public function items()
    {
        return $this->hasMany(Item::class, 'kategori_id');
    }

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'kategori_id');
    }
}
