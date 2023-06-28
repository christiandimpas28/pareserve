<?php

namespace App\Http\Controllers\Api;

use App\Models\Merchant;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Models\ListingCategory;
use App\Http\Controllers\Controller;
use App\Http\Resources\ListingCategoryResource;
use App\Http\Requests\StoreListingCategoryRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;


//Collection response with relationship sample also with parent-child create record
//https://www.youtube.com/watch?v=Gp0dlM_SL6s

class ListingCategoryController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index_x(Request $request)
    {
        $user = $request->user()
                ->makeHidden([
                    'email_verified_at', 
                    'created_at', 
                    'updated_at', 
                    'otp_sent', 
                    'otp_at', 
                    'id'
                ]);
        
        if (strtoupper($user->user_type) !== 'PARTNER') {
            return $this->error('', 'Forbidden. You dont have permission to access.', 403);
            // return response()->json([
            //     'message' => 'Forbidden. You dont have permission to access.'
            // ], 403);
        }

        $listings = ListingCategory::with('merchant')->get();
        return ListingCategoryResource::collection($listings);
    }

    public function index(Request $request) {
        $user = $request->user()
                ->makeHidden([
                    'email_verified_at', 
                    'created_at', 
                    'updated_at', 
                    'otp_sent', 
                    'otp_at', 
                    'id'
                ]);
        
        if (strtoupper($user->user_type) !== 'PARTNER') {
            return $this->error('', 'Forbidden. You dont have permission to access.', 403);
        }

        $merchant = Merchant::with('user')->first();
        return ListingCategoryResource::collection(
            ListingCategory::where('merchant_id', $merchant->id)->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreListingCategoryRequest $request)
    {
        $user = $request->user();
        if (strtoupper($user->user_type) !== 'PARTNER') {
            return $this->error('', 'Forbidden. You dont have permission to access.', 403);
        }

        $request->validate([
            'name' => ['required', 'max:255'],
            'category' => ['required', 'max:255'],
            'description' => ['required', 'max:1000'],
            'address' => ['required', 'max:1000'],
            'city' => ['required', 'max:500'],
            'listing_photos' => ['max:1000'],
            'merchant_id' =>['required']
        ]);

        $files = [];
        // $request->listing_photos = null;
        if ($request->hasFile('files')){
            foreach($request->file('files') as $key => $file)
            {
                $fileName = $request->merchant_id.'-'.time().rand(1,99).'.'.$file->extension();  
                $file->move(public_path('uploads'), $fileName);
                $files[]['name'] = $fileName;
            }

            if (sizeof($files)>0) {
                $str_files = implode(',', array_map(function ($entry) {
                    return ($entry[key($entry)]);
                }, $files));
    
                $request['listing_photos'] = $str_files;
                $request->merge(['listing_photos' => $str_files]);
            }            
        }
        $listingCategory = ListingCategory::create($request->all());
        return new ListingCategoryResource($listingCategory);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, ListingCategory $listingCategory)
    {
        $user = $request->user();
        
        if (strtoupper($user->user_type) !== 'PARTNER') {
            return $this->error('', 'Forbidden. You dont have permission to access.', 403);
        }
        return new ListingCategoryResource($listingCategory);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreListingCategoryRequest $request, ListingCategory $listingCategory)
    {
        $user = $request->user();
        
        if (strtoupper($user->user_type) !== 'PARTNER') {
            return $this->error('', 'Forbidden. You dont have permission to access.', 403);
        }

        $request->validated();
        $files = [];
        if ($request->hasFile('files')){
            foreach($request->file('files') as $key => $file)
            {
                $fileName = $request->merchant_id.'-'.time().rand(1,99).'.'.$file->extension();  
                $file->move(public_path('uploads'), $fileName);
                $files[]['name'] = $fileName;
            }

            if (sizeof($files)>0) {
                $str_files = implode(',', array_map(function ($entry) {
                    return ($entry[key($entry)]);
                }, $files));
    
                $existing_photos = $request['listing_photos'];
                $str_files .= ','.$existing_photos;
                $request['listing_photos'] = $str_files;
                $request->merge(['listing_photos' => $str_files]);
            }            
        }

        $listingCategory->update($request->all());
        return new ListingCategoryResource($listingCategory);
    }

    public function deletePhoto(Request $request, ListingCategory $listingCategory) {
        $user = $request->user();
        if (strtoupper($user->user_type) !== 'PARTNER') {
            return $this->error('', 'Forbidden. You dont have permission to access.', 403);
        }

        if (!isset($request->listing_id) || !isset($request->name)){
            return $this->error('', 'Unable to complete your request', 400);
        }

        // return $this->success($listingCategory, 'OKI', 200);
        // $listing = ListingCategory::where('id',$request->listing_id)->first(); /* Check id exist in table */

        if(is_null($listingCategory)){
            return $this->error('', 'Record not found.', 404);
        }

        $existing_photos = explode(',',$listingCategory->listing_photos);
        if (count($existing_photos)==0){
            return $this->error('', 'Record not found.', 404);
        }

        $selected_file = trim($request->name);
        $new_photos = array_filter($existing_photos, function($value) use ($selected_file) { 
            return trim($value) !== $selected_file; 
        });

        $str_files = implode(",",$new_photos);
        $request['listing_photos'] = $str_files;

        $listingCategory->update($request->only(['listing_photos']));
        return new ListingCategoryResource($listingCategory);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, ListingCategory $listingCategory)
    {
        $user = $request->user();
        if (strtoupper($user->user_type) !== 'PARTNER') {
            return $this->error('', 'Forbidden. You dont have permission to access.', 403);
        }

        $listingCategory->delete();
        return response(null, 204);
        
        // try{
        //     return $listingCategory;
        // } catch (Exception $e){
        //     $this->error('', 'No record found', 404);
        // }
        // return ($listingCategory)? $listingCategory: $this->error('', 'No record found', 402);
        // $record = ListingCategory::where('id', $listingCategory)->get();
        // return $record;
        // $listingCategory->delete();

    }
}
