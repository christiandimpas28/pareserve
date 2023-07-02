<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLogs extends Model
{
    use HasFactory;

    protected $table = 'user_logs';

    protected $fillable = [
        'user_id',
        'is_bot',
        'device_type',
        'platform_name',
        'browser_family',
        'ip'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // {"isBot":false,"deviceType":"Desktop","platformName":"Windows 10","deviceFamily":"","deviceModel":"","browserFamily":"Chrome"}
}
