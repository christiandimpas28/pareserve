<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookedSummary extends Model
{
    use HasFactory;

    protected $table = 'booked_summaries';

    protected $fillable = [
        'checkout_session',
        'booking_reference',
        'total',
        'payment_mode',
        'user_id',
        'payment_status',
    ];

    public function bookedItems()
    {
        return $this->hasMany(BookedItems::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
