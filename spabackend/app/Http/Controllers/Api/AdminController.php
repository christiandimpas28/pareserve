<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\Merchant;
use Illuminate\Http\Request;
use App\Models\ProductReport;
use App\Traits\HttpResponses;
use App\Models\ListingCategory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    use HttpResponses;

    private function validateLoggedUser(Request $request, $ut) {
        $user = $request->user();
        if (strtoupper($user->user_type) !== $ut) {
            return $this->error('', 'Forbidden. You dont have permission to access.', 403);
        }
    }

    public function guestUsers(Request $request){
        $this->validateLoggedUser($request, 'ADMIN');
        try {
            $colletion = DB::table('users')
                ->leftJoin('customer_profiles', 'users.id','=','customer_profiles.user_id')
                ->where('user_type', 'Customer')
                ->orderBy('users.created_at', 'desc')
                ->get([
                    'users.id', 'users.name', 'users.email', 'users.email_verified_at', 'users.user_type', 'users.created_at',
                    'customer_profiles.id AS profile_id', 'customer_profiles.first_name', 'customer_profiles.last_name', 'customer_profiles.id_type', 
                    'customer_profiles.billing_address', 'customer_profiles.contact_no', 'customer_profiles.id_photo'
                ]);

            return $this->success($colletion, 'Success', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->error('', $th->getMessage(), 400);
        }
    }

    public function partnerUsers(Request $request){
        $this->validateLoggedUser($request, 'ADMIN');
        try {
            $colletion = DB::table('users')
                ->leftJoin('merchants', 'users.id','=','merchants.user_id')
                ->where('user_type', 'Partner')
                ->orderBy('users.created_at', 'desc')
                ->get([
                    'users.id', 'users.name', 'users.email', 'users.email_verified_at', 'users.user_type', 'users.created_at',
                    'merchants.id AS merchant_id', 'merchants.name as business_name', 'merchants.bus_contact_name', 'merchants.bus_contact_no', 
                    'merchants.bus_email', 'merchants.bus_address', 'merchants.status', 'merchants.status_remarks', 'merchants.terms_agreed_at',
                    'merchants.created_at AS registered_at', 'merchants.documents'
                ]);

            return $this->success($colletion, 'Success', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->error('', $th->getMessage(), 400);
        }
    }

    public function merchants(Request $request){
        $this->validateLoggedUser($request, 'ADMIN');
        try {
            $merchants  = Merchant::all();
            $merchants->load('listings');
            $merchants = $merchants->load('listings', 'listings.products');
            return $this->success($merchants, 'Success', 200);
        } catch (\Throwable $th) {
            return $this->error('', $th->getMessage(), 400);
        }
    }

    public function totals(Request $request){
        $this->validateLoggedUser($request, 'ADMIN');
        try {
            $colletion = DB::table('books')
            ->leftJoin('users', 'books.user_id','=','users.id')
            ->leftJoin('booked_summaries', 'books.booked_id','=','booked_summaries.id')
            ->orderBy('books.created_at', 'desc')
            ->get([
                'books.*',
                'users.id', 'users.name', 'users.email', 'users.user_type',
                'booked_summaries.checkout_session',
                'booked_summaries.booking_reference',
                'booked_summaries.total',
                'booked_summaries.payment_mode',
                'booked_summaries.payment_status',
            ]);
            
            return $this->success($colletion, 'Success', 200);
        } catch (\Throwable $th) {
            return $this->error('', $th->getMessage(), 400);
        }
    }

    public function alarmingCases(Request $request){
        $this->validateLoggedUser($request, 'ADMIN');
        try {
            $getArray = DB::table('product_report')
                ->whereNull('listing_category_id')
                ->groupBy('product_id')
                ->having(DB::raw('count(product_id)'), '>', 0)
                ->pluck('product_id');

            $collection = DB::table('product_report')
                ->leftJoin('users', 'product_report.user_id','=','users.id')
                ->leftJoin('books', 'product_report.books_id','=','books.id')
                ->leftJoin('products', 'product_report.product_id','=','products.id')
                ->whereNull('product_report.listing_category_id')
                ->whereIn('product_report.product_id', $getArray)
                ->get([
                    'product_report.id AS product_report_id',
                    'product_report.*',
                    'users.id', 'users.name', 'users.email', 'users.user_type',
                    'books.product_name', 'books.product_photo', 'books.product_category', 
                    'books.product_address', 'books.number_of_guest', 'books.from', 'books.to', 'books.days',
                    'books.total', 'books.booking_status',
                    'products.listing_category_id'
                ]);

            return $this->success($collection, 'Success', 200);
        } catch (\Throwable $th) {
            return $this->error('', $th->getMessage(), 400);
        }
    }

    public function updateMerchantStatus(Request $request, Merchant $merchant){
        $this->validateLoggedUser($request, 'ADMIN');
        try {
            $enabled = $request->status==1? 1: 0;

            DB::transaction(function () use ($request, $merchant, $enabled) {
                $merchant->update($request->only(['status', 'status_remarks']));
                ListingCategory::where('merchant_id', $merchant->id)->update(['enabled' => $enabled]);
            });
            
            return $this->success($request->all(), 'Success', 200);
        } catch (\Throwable $th) {
            return $this->error('', $th->getMessage(), 400);
        }
    }

    public function toggleSuspension(Request $request, ProductReport $productReport, Product $product){
        $this->validateLoggedUser($request, 'ADMIN');
        try {
            //code...
            if ($productReport !== null && $product !== null) {
                DB::transaction(function () use ($request, $productReport, $product) {
                    $productReport->update(['listing_category_id'=>$product->listing_category_id]);
                    ListingCategory::where('id', $product->listing_category_id)->update(['enabled' => $request->state]);
                });
                
                return $this->success($product->listing_category_id, 'Success', 200);
            } else {
                return $this->error('', 'Not found', 404);
            }
            
        } catch (\Throwable $th) {
            return $this->error('', $th->getMessage(), 400);
        }
    }
}
