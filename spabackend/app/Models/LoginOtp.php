<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginOtp extends Model
{
    use HasFactory;

    protected $table = 'login_otp';

    protected $fillable = [
        'user_id',
        'otp',
        'is_verified'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
