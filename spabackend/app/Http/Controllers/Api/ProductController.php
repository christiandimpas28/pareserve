<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Models\ListingCategory;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Requests\StoreProductRequest;

class ProductController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $products = Product::with([
        //     'product' => function($query)
        // ])
        $products = Product::with('listingCategory')->get();
        return ProductResource::collection($products);
        // return ProductResource::collection(Product::all());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $user = $request->user();
        if (strtoupper($user->user_type) !== 'PARTNER') {
            return $this->error('', 'Forbidden. You dont have permission to access.', 403);
        }

        $request->validate([
            'name' => ['required', 'max:500'],
            'slug' => ['required'],
            'description' => ['required', 'max:3000'],
            'max_guest' => ['required'],
            'rate' => ['required'],
            'listing_category_id' =>['required']
        ]);

        $files = [];

        if ($request->hasFile('files')){
            foreach($request->file('files') as $key => $file)
            {
                $fileName = $request->listing_category_id.'-'.time().rand(1,99).'.'.$file->extension();  
                $file->move(public_path('uploads'), $fileName);
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
        
        $request['discount'] = 0;
        $request['enabled'] = 1;
        // return $this->success($request->all(), 'Success', 200);

        $product = Product::create($request->all());
        if (!$product) {
            return $this->error('', 'Could not process your request.', 401);
        }

        return new ProductResource($product);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Product $product)
    {
        $user = $request->user();
        
        if (strtoupper($user->user_type) !== 'PARTNER') {
            return $this->error('', 'Forbidden. You dont have permission to access.', 403);
        }

        // $product = Product::with(['listingCategory','productAttributes'])->where(['id'=>$product])->get();
        // $product = Product::where(['id'=>$product])->get();
        $product->load('listingCategory', 'productAttributes');
        return $this->success($product, 'Success', 200);
        // return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreProductRequest $request, Product $product)
    {
        $user = $request->user();
        if (strtoupper($user->user_type) !== 'PARTNER') {
            return $this->error('', 'Forbidden. You dont have permission to access.', 403);
        }

        $request->validate([
            'name' => ['required', 'max:500'],
            'slug' => ['required'],
            'description' => ['required', 'max:3000'],
            'max_guest' => ['required'],
            'rate' => ['required'],
            'listing_category_id' =>['required']
        ]);

        $files = [];

        if ($request->hasFile('files')){
            foreach($request->file('files') as $key => $file)
            {
                $fileName = $request->listing_category_id.'-'.time().rand(1,99).'.'.$file->extension();  
                $file->move(public_path('uploads'), $fileName);
                $files[]['name'] = $fileName;
            }

            if (sizeof($files)>0) {
                $str_files = implode(',', array_map(function ($entry) {
                    return ($entry[key($entry)]);
                }, $files));
                
                $existing_photos = $request['photos'];
                $str_files .= ','.$existing_photos;

                $request['photos'] = $str_files;
                $request->merge(['photos' => $str_files]);
            }            
        }

        // return $this->success($request->all(), 'Success', 200);

        $product->update($request->all());
        return new ProductResource($product);
    }

    public function deletePhoto(Request $request, Product $product) {
        $user = $request->user();
        if (strtoupper($user->user_type) !== 'PARTNER') {
            return $this->error('', 'Forbidden. You dont have permission to access.', 403);
        }

        if (!isset($request->product_id) || !isset($request->name)){
            return $this->error('', 'Unable to complete your request', 400);
        }

        // return $this->success($listingCategory, 'OKI', 200);
        // $listing = ListingCategory::where('id',$request->listing_id)->first(); /* Check id exist in table */

        if(is_null($product)){
            return $this->error('', 'Record not found.', 404);
        }

        $existing_photos = explode(',',$product->photos);

        
        if (count($existing_photos)==0){
            return $this->error('', 'Record not found.', 404);
        }

        $selected_file = trim($request->name);
        $new_photos = array_filter($existing_photos, function($value) use ($selected_file) { 
            return trim($value) !== $selected_file; 
        });

        $str_files = implode(",",$new_photos);
        $request['photos'] = $str_files;

        // return $this->success($request['photos'], 'OKI', 200);

        $product->update($request->only(['photos']));
        return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Product $product)
    {
        $user = $request->user();
        if (strtoupper($user->user_type) !== 'PARTNER') {
            return $this->error('', 'Forbidden. You dont have permission to access.', 403);
        }

        $product->delete();
        return response(null, 204);

    }
}
