<?php

namespace App\Http\Controllers\Api;

use App\Models\Books;
use App\Models\Product;
use App\Models\Merchant;
use App\Models\Integration;
use App\Enums\BookingStatus;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Models\ListingCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\StoreBooksRequest;
use App\Http\Requests\UpdateBooksRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BooksController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function availability(Request $request, $product_id){
        // return $this->success($product_id, 'Success', 200);
        $bookings = $this->checkAvailability($request, $product_id, array('PAYMENT_SUBMITTED', 'BOOKED'));
        // if (is_null($bookings))
        return $this->success($bookings, 'Success', 200);
    }

    private function checkAvailability(Request $request, $product_id, $statuses){
        return Books::select(
            "product_id", 
            "qty", 
            "from", 
            "to", 
            "booking_status")
        ->where('product_id', $product_id)
        ->where('from', $request->from)
        ->where('to', $request->to)
        ->whereIn('booking_status', $statuses)
        ->get();
    }

    private function checkExistingBooking(Request $request, $product_id, $statuses) {
        return Books::where('product_id', $product_id)
            ->where('user_id', $request->user()->id)
            ->whereIn('booking_status', $statuses)
            ->first();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBooksRequest $request, Product $product)
    {
        //1. Validate request inputs
        $request->validate([
            'product_id' => ['required'],
            'product_name' => ['required'],
            'product_photo' => ['required'],
            'product_category' => ['required'],
            'product_address' => ['required'],
            'qty' => ['required'],
            'number_of_guest' => ['required', 'min:1'],
            'from' => ['required'],
            'to' => ['required'],
            'days' =>['required'],
            'service_fee_rate' =>['required'],
            'rate' =>['required'],
            'total' =>['required'],
        ]);

        if (!$product)
            return $this->error('', 'Unable to process your request. Invalid request details.', 409 );

        $bookings = $this->checkAvailability($request, $product->id,  array('Payment Submitted', 'Booked'));
        if ($bookings && count($bookings)>0)
            return $this->error('', 'Unable to process your request. Selected dates are aleady booked.', 409 );
            
        //2. Check user
        $user = $request->user();
        if (!$user)
            return $this->error('', 'Log in is required before you can book.', 401 );

        if ($user && strtoupper($user->user_type) !== 'CUSTOMER')
            return $this->error('', 'Please register as customer before you can book.', 403);

        if ($user && $user->email_verified_at == null)
            return $this->error('', 'Unable to process your request. Unverified user account.', 401 );

        //3. Check if has existing booking
        $booking = $this->checkExistingBooking($request, $product->id, array('Pending'));
        
        //4. Recompute Charges
        // $rate_per_night = (float) $product->rate;
        // $days = (int) $request->days; 
        // $service_fee_rate = (float) $request->service_fee_rate;

        // $sub_total = (float) ($rate_per_night * $days);
        // $service_fee = (float) ($sub_total * $service_fee_rate);
        // $total = $sub_total+$service_fee;

        // $request['rate'] = $rate_per_night;
        // $request['qty'] = $days;
        // $request['days'] = $days;
        // $request['service_fee_rate'] = $service_fee_rate;
        // $request['service_fee'] = $service_fee;
        // $request['total'] = $total;

        $request['user_id'] = $user->id;

        if (!$booking) {
            // Create new Booking
            $booking = Books::create($request->all());
            return $this->success($booking, 'Success', 200);
        } else{
            // Udpdate Existing Booking
            $booking->update($request->all());
            return $this->success($booking, 'Success', 200);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Books $books, $slug)
    {
        $user = $request->user();
        
        if (strtoupper($user->user_type) !== 'CUSTOMER') {
            return $this->error('', 'Forbidden. You dont have permission to access.', 403);
        }
        
        $books->load('user');
        return $this->success($books, 'Success', 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function customerTransactions(Request $request)
    {
        $user = $request->user();
        
        if (strtoupper($user->user_type) !== 'CUSTOMER') {
            return $this->error('', 'Forbidden. You dont have permission to access.', 403);
        }

        $transactions = DB::table('books')
            ->leftJoin('booked_summaries', 'books.booked_id', '=', 'booked_summaries.id')
            ->leftJoin('product_review', 'books.id', '=', 'product_review.book_id')
            ->leftJoin('product_report', 'books.id', '=', 'product_report.books_id')
            ->where('books.user_id', '=', $user->id)
            ->orderBy('books.created_at', 'desc')
            ->get([
                'books.id AS books_id', 
                'books.*',
                'booked_summaries.*',
                'product_review.id AS product_review_id',
                'product_review.book_id AS product_review_book_id',
                'product_review.product_id AS product_review_product_id',
                'product_review.photos AS product_review_photos',
                'product_review.rating AS product_review_rating',
                'product_review.review AS product_review_review',
                'product_report.id AS product_report_id',
                'product_report.related_to AS product_report_related_to',
                'product_report.remarks AS product_report_remarks',
                'product_report.photos AS product_report_photos',
                'product_report.settled AS product_report_settled',
                'product_report.case_id AS product_report_case_id',
            ]);

        // $transactions = Books::select('books.*, booked_summaries.*')
        //     ->leftJoin('booked_summaries', 'books.booked_id', '=', 'booked_summaries.id')
        //     ->where('user_id', '=', $user->id)
        //     ->get();

        return $this->success($transactions, 'Success', 200);
    }

    public function merchantTransactions(Request $request){

        $user = $request->user();
        
        if (strtoupper($user->user_type) !== 'PARTNER') {
            return $this->error('', 'Forbidden. You dont have permission to access.', 403);
        }

        $e_q = $request['ids']; //Product Ids
        $q = base64_decode($e_q);
        $ids = explode(',',$q);

        $transactions = DB::table('books')
            ->leftJoin('booked_summaries', 'books.booked_id', '=', 'booked_summaries.id')
            ->leftJoin('users', 'books.user_id', '=', 'users.id')
            ->whereIn('books.product_id', $ids)
            ->orderBy('books.created_at', 'desc')
            ->get([
                'books.id AS books_id', 
                'books.*',
                'booked_summaries.*',
                'users.name',
                'users.email',
            ]);

        return $this->success($transactions, 'Success', 200);
    }

    public function merchantTransactionsV2(Request $request){

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

            $listings = ListingCategory::with('products')
                ->where('merchant_id', $record->id)->get();

            // $filtered_listings = array_filter((array)$listings, function ($item) { 
            //     return count($item->products)>0; 
            // });
            $ids = array();

            foreach ($listings as $item) {
                //statement(s)
                if (count($item['products'])>0) {
                    foreach($item['products'] as $prod) {
                        array_push($ids, $prod['id']);
                    }
                }
            }

            if (count($ids)>0) {
                $transactions = DB::table('books')
                    ->leftJoin('booked_summaries', 'books.booked_id', '=', 'booked_summaries.id')
                    ->leftJoin('users', 'books.user_id', '=', 'users.id')
                    ->whereIn('books.product_id', $ids)
                    ->orderBy('books.created_at', 'desc')
                    ->get([
                        'books.id AS books_id', 
                        'books.*',
                        'booked_summaries.*',
                        'users.name',
                        'users.email',
                    ]);
                $json_string = json_encode($transactions);
                $encrypted_data = hCryptoEncryptByPub($integration->public_key, $json_string);

                $payload = [
                    'payload' => $encrypted_data
                ];

                return response($payload, 200);
            } 
            
            return $this->error(null, 'No record found.', 404);
        }

        

        return $this->success($filtered_listings, 'Success', 200);
    }

    public function checkout(Request $request, Books $books)
    {
        $user = $request->user();
        
        if (strtoupper($user->user_type) !== 'PARTNER') {
            return $this->error('', 'Forbidden. You dont have permission to access.', 403);
        }
        
        $books->update(['booking_status'  => BookingStatus::COMPLETED->value]);
        return $this->success($books, 'Success', 200);
    }

}
