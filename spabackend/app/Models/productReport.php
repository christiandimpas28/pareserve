<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReport extends Model
{
    use HasFactory;

    protected $table = 'product_report';

    protected $fillable = [
        'user_id',
        'books_id',
        'photos',
        'remarks',
        'settled'
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
