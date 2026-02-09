<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // <--- PENTING: Tambahkan ini

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // <--- Gunakan trait HasApiTokens

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'username',
        'password',
        'nama_lengkap',
        'email',
        'no_telepon',
        'alamat',
        'role',
        'foto_profil',
        'is_active',
        'last_login', // <--- Tambahkan ini agar bisa di-update saat login
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'last_login' => 'datetime',
        'password' => 'hashed',
    ];
}
