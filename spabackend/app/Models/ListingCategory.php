<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingCategory extends Model
{
    use HasFactory;
    
    protected $table = 'listing_categories';

    protected $fillable = [
        'name',
        'category',
        'description',
        'address',
        'city',
        'listing_photos',
        'merchant_id',
        'discount_title',
        'discount_rate',
        'discount_condition',
        'enabled',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
