<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Merchant;
use App\Models\Integration;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Models\ListingCategory;
use Illuminate\Support\Facades\DB;
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

    public function indexV2(Request $request){
        $user = $request->user();
        
        if (strtoupper($user->user_type) !== 'PARTNER') {
            return $this->error('', 'Forbidden. You dont have permission to access.', 403);
        }

        $record = Merchant::where('user_id', $user->id)->first();
        if ($record){
            
            $integration = Integration::where('merchant_id', $record->id)
                ->where('enabled', 1)
                ->first();

            // Check if has Integration keys
            if (is_null($integration)) 
                return $this->error(null, 'No record found.', 404);

            // $listings = ListingCategory::with('products')
            //     ->where('merchant_id', $record->id)->first();
            $listings = ListingCategory::where('merchant_id', $record->id)->first();


            //JSON_FORCE_OBJECT
            // $json_string = json_encode($listings, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
            // $json_string = utf8_encode()
            $json_string = json_encode($listings);
            // $json_string = trim(preg_replace('/[\t|\n{2,}]/', '', $json_string));
            // $json_string = str_replace(array("\r\n", "\r", "\n", "\t"), '', $json_string);
            $json_string= substr($json_string,500);

            $encrypted_data = hCryptoEncryptByPub($integration->public_key, $json_string);
           
            // $decrypted_data = hCryptoDecryptByPri($integration->private_key, base64_decode($encrypted_data));

            $payload = [
                'payload' => $encrypted_data
            ];

            // $keys = hGenerateIntegrationKeys();

            return response($payload, 200);

        }

        return $this->error($record, 'No record found.', 404);
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
            $integration = Integration::where('merchant_id', $record->id)
                ->where('enabled', 1)
                ->first();

            // Check if has Integration keys
            if (is_null($integration)) 
                return $this->error(null, 'No record found.', 404);

            $json_string = json_encode($record, JSON_UNESCAPED_UNICODE);             

            // $payload = [
            //     'payload' => Crypt::encryptString($json_string)
            // ];

            $encrypted_data = hCryptoEncryptByPub($integration->public_key, $json_string);
            $decrypted_data = hCryptoDecryptByPri($integration->private_key, base64_decode($encrypted_data));

            $payload = [
                'payload' => $encrypted_data
            ];

            return response($payload, 200);
        }
            
        return $this->error($record, 'No record found.', 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreMerchantRequest $request)
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

        $isNew = false;
        if ($request->id == 'undefined'){
            $request['terms_agreed_at'] = now();
            $isNew = true;
        } 
    
        if ($request->terms_agreed_at === "null")
            $request['terms_agreed_at'] = now();

        $files = [];
        // $request->listing_photos = null;
        if ($request->hasFile('files')){
            foreach($request->file('files') as $key => $file)
            {
                $fileName = $request->id.'-'.time().rand(1,99).'.'.$file->extension();  
                $file->move(public_path('uploads/merchants'), $fileName);
                $files[]['name'] = $fileName;
            }

            if (sizeof($files)>0) {
                $str_files = implode(',', array_map(function ($entry) {
                    return ($entry[key($entry)]);
                }, $files));

                $deli = ',';
                $existing_photos = $request['documents'];
                if ($existing_photos==null) {
                    $existing_photos = '';
                    $deli = '';
                }
                $str_files .= $deli.$existing_photos;

                $request['documents'] = $str_files;
                $request->merge(['documents' => $str_files]);
            }            
        }

        $table_cols = ['name', 'bus_contact_name', 'bus_contact_no', 'bus_email', 'bus_address', 'documents', 'terms_agreed_at'];
        // if (!$isNew) {
        //     $table_cols = ['name', 'bus_contact_name', 'bus_contact_no', 'bus_email', 'bus_address', 'documents'];
        // }
        $user->Merchant()->updateOrCreate(
            ['user_id' => $user->id],
            $request->only($table_cols)
        );

        $user->load('merchant');
        return $user;
    }

    public function agree(Request $request, Merchant $merchant){
        $user = $request->user();
        if (strtoupper($user->user_type) !== 'PARTNER') {
            return $this->error('', 'Forbidden. You dont have permission to access.', 403);
        }

        $now = now();
        if ($merchant) {
            $merchant->update(['terms_agreed_at'=>$now]);
        } else {
            return $this->error($record, 'No record found.', 404);
        }

        return $this->success($now, 'Success', 200);
    }

    public function cases(Request $request){
        $user = $request->user();
        if (strtoupper($user->user_type) !== 'PARTNER') {
            return $this->error('', 'Forbidden. You dont have permission to access.', 403);
        }

        if (!isset($request['qids'])){
            return $this->error($record, 'No record found.', 404);
        }

        $qids = base64_decode($request['qids']);
        $arr_ids = explode(",",$qids);
        try {
            
            $collection = DB::table('product_report')
                ->whereIn('product_report.product_id', $arr_ids)
                ->leftJoin('users', 'product_report.user_id','=','users.id')
                ->leftJoin('books', 'product_report.books_id','=','books.id')
                ->leftJoin('products', 'product_report.product_id','=','products.id')
                ->leftJoin('listing_categories', 'listing_categories.id','=','products.listing_category_id')
                ->orderBy('product_report.created_at', 'desc')
                ->get([
                    'product_report.id AS product_report_id',
                    'product_report.*',
                    'users.id', 'users.name', 'users.email', 'users.user_type',
                    'books.product_name', 'books.product_photo', 'books.product_category', 
                    'books.product_address', 'books.number_of_guest', 'books.from', 'books.to', 'books.days',
                    'books.total', 'books.booking_status',
                    'products.listing_category_id',
                    'listing_categories.id AS listing_categories_primary',  
                    'listing_categories.enabled AS listing_categories_enabled', 
                    'listing_categories.name AS listing_categories_name'
                ]);

            return $this->success($collection, 'Success', 200);

        } catch (\Throwable $th) {
            return $this->error('', $th->getMessage(), 400);
        }
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
