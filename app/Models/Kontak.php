<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kontak extends Model
{
    use HasFactory;

    protected $table = 'kontaks';

    protected $fillable = [
        'hero_title',
        'hero_subtitle',
        'hero_description',
    ];
}
