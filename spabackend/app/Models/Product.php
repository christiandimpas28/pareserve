<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'max_guest',
        'photos',
        'enabled',
        'rate',
        'discount',
        'listing_category_id',
    ];

    public function listingCategory()
    {
        return $this->belongsTo(ListingCategory::class);
    }

    public function listing()
    {
        return $this->belongsTo(ListingCategory::class);
    }

    public function productAttributes()
    {
        return $this->hasMany(ProductAttributes::class);
    }
}
