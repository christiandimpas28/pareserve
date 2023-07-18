<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Traits\HttpResponses;

class ProductReviewsController extends Controller
{
    //
    use HttpResponses;

    public function reviews(product $product){
        return Product()->productReviews->all(); 
    }
}
