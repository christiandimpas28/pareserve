<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Merchant;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Http\Resources\MerchantsResource;
use App\Http\Requests\StoreMerchantRequest;
use App\Http\Requests\UpdatemerchantRequest;

class MerchantController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $user = Auth::user();
        // // return Merchant::where('user_id', $user->id())->get();
        // return Merchant::where('user_id', $user->id)->get();
        // return response()->json([
        //     'success' => true,
        //     'user' => $user->id
        // ]);
        // // dd($user->name);
        // $merchants = Merchant::with(['listings'])->get();
        // return MerchantsResource::collection($merchants);

        return MerchantsResource::collection(Merchant::all());
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
    public function store(StoreMerchantRequest $request)
    {
        $merchant = Merchant::fisrtOrCreate($request->validated());
        
        if (!$merchant) {
            // return response()->json([
            //     'message' => 'Could not process your request.'
            // ], 401);
            return $this->error('', 'Could not process your request.', 401);
        }
        
        return new MerchantsResource($merchant);
    }

    /**
     * Display the specified resource.
     * Standard response approach when using API - Use Model Resource for proper response handling
     */
    // public function show(merchant $merchant)
    // {
    //     return new MerchantsResource($merchant);
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
        
        if (strtoupper($user->user_type) !== 'PARTNER') {
            return $this->error('', 'Forbidden. You dont have permission to access.', 403);
        }

        // $user->load('merchant');
        // return $user;
        $record = Merchant::where('user_id', $user->id)->first();
        if ($record)
            return new MerchantsResource($record);
        
        return $this->error($record, 'No record found.', 404);
    }

    public function showV2(Request $request)
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
        }


        $record = Merchant::where('user_id', $user->id)->first();
        if ($record){
            $json_string = json_encode($record); 
            $payload = [
                'payload' => Crypt::encryptString($json_string)
            ];

            return $this->success($payload, 'OK.', 200);
        }
            
        
        return $this->error($record, 'No record found.', 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreMerchantRequest $request, Merchant $merchant)
    {
        // $merchant->update($request->only([
        //     'name', 'bus_contact_name', 'bus_contact_no', 'bus_email', 'bus_address',
        // ]));

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

        $user->Merchant()->updateOrCreate(
            ['user_id' => $user->id],
            $request->only([
                'name', 'bus_contact_name', 'bus_contact_no', 'bus_email', 'bus_address',
            ])
        );

        $user->load('merchant');
        return $user;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(merchant $merchant)
    {
        $merchant->delete();
        return $this->success('', 'Mechant has been deleted from the database.', 200);
    }
}
