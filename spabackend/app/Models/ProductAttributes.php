<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttributes extends Model
{
    use HasFactory;

    protected $table = 'product_attributes';

    protected $fillable = [
        'name',
        'value',
        'highlight',
        'important',
        'product_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
