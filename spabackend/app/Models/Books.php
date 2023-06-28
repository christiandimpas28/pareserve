<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Books extends Model
{
    use HasFactory;

    protected $table = 'books';

    protected $fillable = [
        'product_id',
        'product_name',
        'product_photo',
        'product_category',
        'product_address',
        'qty',
        'number_of_guest',
        'from',
        'to',
        'days',
        'service_fee_rate',
        'service_fee',
        'rate',
        'discount',
        'total',
        'booking_status',
        'user_id',
        'booked_id',       
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}