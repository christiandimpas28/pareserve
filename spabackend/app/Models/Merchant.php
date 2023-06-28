<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    use HasFactory;

    protected $table = 'merchants';

    protected $fillable = [
        'name',
        'bus_contact_name',
        'bus_contact_no',
        'bus_email',
        'bus_address',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function listings()
    {
        return $this->hasMany(ListingCategory::class);
    }

    public function products()
    {
        return $this->hasManyThrough(Products::class, ListingCategory::class);
    }

    public function getListings(){
        return $this->belongsToMany(ListingCategory::class);
    }
}

