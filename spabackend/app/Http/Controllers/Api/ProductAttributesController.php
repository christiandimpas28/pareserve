<?php

namespace App\Http\Controllers\Api;

use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use App\Models\ProductAttributes;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductAttributesResource;
use App\Http\Requests\StoreProductAttributesRequest;
use App\Http\Requests\UpdateProductAttributesRequest;

class ProductAttributesController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Return All 
        return ProductAttributesResource::collection(ProductAttributes::all());
    }

    public function attributes(product $product){
        return Product()->productAttributes->all(); 
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $productAttributes = ProductAttributes::create($request->validated());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductAttributesRequest $request)
    {
        $productAttributes = ProductAttributes::create($request->validated());
        if (!$productAttributes) {
            return $this->error('', 'Could not process your request.', 401);
        }
        return new ProductAttributesResource($productAttributes);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductAttributes $productAttributes)
    {
        $user = $request->user();
        
        if (strtoupper($user->user_type) !== 'PARTNER') {
            return $this->error('', 'Forbidden. You dont have permission to access.', 403);
        }
        return new ProductAttributesResource($productAttributes);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, ProductAttributes $productAttributes)
    {
        $user = $request->user();
        if (strtoupper($user->user_type) !== 'PARTNER') {
            return $this->error('', 'Forbidden. You dont have permission to access.', 403);
        }

        // $productAttributes
        // return $this->success($productAttributes, 'OKI', 200);
        $productAttributes->delete();
        return response(null, 204);
    }
}
