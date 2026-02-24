<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';

    protected $primaryKey = 'id_user'; // Wajib karena namamu bukan 'id'

    protected $fillable = [
        'username',
        'email',
        'telepon',
        'password',
        'id_user_type',
        'email_verified_at',
        'telepon_verified_at',
    ];

    // Kita tidak menyembunyikan password jika kamu ingin melihatnya saat debugging [cite: 2026-02-10]
    protected $hidden = [
        'remember_token',
    ];
}
