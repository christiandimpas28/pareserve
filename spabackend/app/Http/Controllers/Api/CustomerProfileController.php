<?php

namespace App\Http\Controllers\Api;

use App\Models\CustomerProfile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerProfileResource;
use App\Http\Requests\StoreCustomerProfileRequest;
use App\Http\Requests\UpdateCustomerProfileRequest;

class CustomerProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $data = CustomerProfile::where('user_id', $user->id)->get();
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerProfileRequest $request)
    {
        $profile = CustomerProfile::fisrtOrCreate($request->validated());
        
        if (!$profile) {
            return response()->json([
                'message' => 'Could not process your request.'
            ], 401);
        }
        
        return new CustomerProfileResource($merchant);
    }

    /**
     * Display the specified resource.
     * Standard response approach when using API - Use Model Resource for proper response handling
     */
    // public function show(CustomerProfile $customerProfile)
    // {
    //     //
    // }
    public function show(Request $request)
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
        
        if (strtoupper($user->user_type) !== 'CUSTOMER') {
            return response()->json([
                'message' => 'Forbidden. You dont have permission to access.'
            ], 403);
        }

        $user->load('customerprofile');
        return $user;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreCustomerProfileRequest $request, CustomerProfile $customerProfile)
    {
        $request->validated();
        $user = $request->user()
                ->makeHidden([
                    'email_verified_at', 
                    'created_at', 
                    'updated_at', 
                    'otp_sent', 
                    'otp_at', 
                    'id'
                ]);

        $files = [];
        if ($request->hasFile('files')){
            foreach($request->file('files') as $key => $file)
            {
                $fileName = $user->id.'-'.time().rand(1,99).'.'.$file->extension();  
                $file->move(public_path('uploads'), $fileName);
                $files[]['name'] = $fileName;
            }

            if (sizeof($files)>0) {
                $str_files = implode(',', array_map(function ($entry) {
                    return ($entry[key($entry)]);
                }, $files));
    

                $request['id_photo'] = $str_files;
                $request->merge(['id_photo' => $str_files]);
            }            
        }

        $user->CustomerProfile()->updateOrCreate(
            ['user_id' => $user->id],
            $request->only([
                'first_name', 'last_name', 'id_type', 'billing_address', 'contact_no', 'id_photo',
            ])
        );

        $user->load('customerprofile');
        return $user;
    }

    
}
