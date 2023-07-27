<?php

namespace App\Http\Controllers\Api;

use App\Models\Books;
use App\Models\Product;
use App\Models\ProductReviews;
use App\Traits\HttpResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductReviewsResource;
use Illuminate\Http\Request;

class ProductReviewsController extends Controller
{
    //
    use HttpResponses;

    public function reviews(product $product){
        return Product()->productReviews->all(); 
    }

    public function store(Request $request, Books $books , Product $product){
        $user = $request->user();
        if (strtoupper($user->user_type) !== 'CUSTOMER') {
            return $this->error('', 'Forbidden. You dont have permission to access.', 403);
        }

        $request->validate([
            'product_id' => ['required'],
            'book_id' => ['required'],
            'rating' => ['required'],
            'review' => ['required', 'min:100', 'max:5000'],
        ]);

        $files = [];

        if ($request->hasFile('files')){
            foreach($request->file('files') as $key => $file)
            {
                $fileName = 'B'.$books->id.'-P'.$books->product_id.'-'.time().rand(1,99).'.'.$file->extension();  
                $file->move(public_path('uploads/reviews'), $fileName);
                $files[]['name'] = $fileName;
            }

            if (sizeof($files)>0) {
                $str_files = implode(',', array_map(function ($entry) {
                    return ($entry[key($entry)]);
                }, $files));
    
                $request['photos'] = $str_files;
                $request->merge(['photos' => $str_files]);
            }       
        }

        $request['user_id'] = $user->id;
        $review = ProductReviews::create($request->all());

        if (!$review) {
            return $this->error('', 'Could not process your request.', 401);
        }

        return new ProductReviewsResource($review);

        // return $this->success($request['photos'], 'Success', 200);

    }
}
