<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactItems extends Model
{
    use HasFactory;

    protected $table = 'contact_items';

    protected $fillable = [
        'icon',
        'title',
        'details',
        'action_url',
        'order_position',
        'is_active',
    ];

    protected $casts = [
        'details' => 'array',
        'is_active' => 'boolean',
        'order_position' => 'integer',
    ];
}
