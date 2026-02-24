<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    protected $table = 'otp_verifications';

    protected $primaryKey = 'id_otp';

    protected $fillable = [
        'email',
        'telepon',
        'email_otp_hash',
        'telepon_otp_hash',
        'email_expires_at',
        'telepon_expires_at',
        'email_verified_at',
        'telepon_verified_at',
        'email_last_sent_at',
        'telepon_last_sent_at',
        'email_attempts',
        'telepon_attempts',
        'used_at',
    ];

    protected $casts = [
        'email_expires_at' => 'datetime',
        'telepon_expires_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'telepon_verified_at' => 'datetime',
        'email_last_sent_at' => 'datetime',
        'telepon_last_sent_at' => 'datetime',
        'used_at' => 'datetime',
    ];
}
