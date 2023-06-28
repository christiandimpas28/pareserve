<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerProfile extends Model
{
    use HasFactory;

    protected $table = 'customer_profiles';

    protected $fillable = [
        'first_name',
        'last_name',
        'id_type',
        'billing_address',
        'contact_no',
        'id_photo',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
