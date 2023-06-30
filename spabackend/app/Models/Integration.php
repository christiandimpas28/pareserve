<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Integration extends Model
{
    use HasFactory;

    protected $table = 'integrations';

    protected $fillable = [
        'merchant_id',
        'private_key',
        'public_key',
        'password',
        'enabled',
        'remarks',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}
