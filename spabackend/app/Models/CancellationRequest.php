<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CancellationRequest extends Model
{
    use HasFactory;

    protected $table = 'cancellation_request';

    protected $fillable = [
        'books_id',
        'user_id',
        'remarks',
        'request_status',
        'refunded',
        'refunded_amount',
        'refunded_date',
    ];

    public function booking()
    {
        return $this->belongsTo(Books::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}