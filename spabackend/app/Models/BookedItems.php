<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookedItems extends Model
{
    use HasFactory;

    protected $table = 'booked_items';

    protected $fillable = [
        'booked_summary_id',
        'books_id',
    ];

    public function bookedSummary()
    {
        return $this->belongsTo(BookedSummary::class);
    }
}
