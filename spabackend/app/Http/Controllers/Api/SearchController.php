<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\Merchant;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Models\ListingCategory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\MerchantsResource;
use Illuminate\Database\Eloquent\Builder;

class SearchController extends Controller
{
    use HttpResponses;
    //
    public function index()
    {
        return MerchantsResource::collection(Merchant::all());
    }

    public function search(Request $request){
        // dd($request->all());
        
        // $product->load('listingCategory', 'productAttributes');
        // return $this->success($request['query'], 'Success', 200);
        if (!isset($request['query'])){
            return MerchantsResource::collection(Merchant::all());
        }

        $q = $request['query'];

        $products = Product::select(
            "products.*", 
        )
        ->leftJoin("listing_categories", "listing_categories.id", "=", "products.listing_category_id")
        ->where("products.enabled",1)
        ->where("listing_categories.enabled",1)
        ->where(
            // DB::raw("CONCAT(products.name,' ',listing_categories.category,' ',listing_categories.address,' ',listing_categories.city)"), 
            DB::raw("CONCAT(products.name,' ',products.description,' ',listing_categories.name,' ',listing_categories.category,' ',listing_categories.address,' ',listing_categories.city)"), 
            'like', 
            "%{$q}%")
        ->get();
        
        // dd(DB::getQueryLog());
        // dd($products);
        $products->load('listingCategory', 'productAttributes', 'productReviews');
        return $this->success($products, 'Success', 200);
    }

    public function viewProduct(Request $request, Product $product, $slug) {
        // Entity::where('id', $id)->with('relation1.subrelation1', 'relation1.subrelation2', 'relation2.subrelation1', 'relation2.subrelation2')->get();
        // Product::where('id', $product->id)->with('ListingCategory.Merchant')->get();
        $selected_product =Product::where('id', $product->id)->first();
        $selected_product->load('listingCategory', 'productAttributes', 'productReviews.user');
        return $this->success($selected_product, 'Success', 200);
    }
}
