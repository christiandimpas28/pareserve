<?php

namespace App\Http\Controllers\Api;

use Curl;
use App\Models\Books;
use App\Models\BookedItems;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use Illuminate\Http\Request;
use App\Models\BookedSummary;
use App\Traits\HttpResponses;
use App\Mail\BookingConfirmation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class PaymentController extends Controller
{
    use HttpResponses;
    //
    public function pay(Request $request, Books $books){
        // return base64_encode(env('PAYM_AUTH_PAY_SK'));

        $user = $request->user();
        if (!$user)
            return $this->error('', 'Log in is required before you can book.', 401 );

        if ($user && strtoupper($user->user_type) !== 'CUSTOMER')
            return $this->error('', 'Please register as customer before you can book.', 403);

        $user = $request->user();
        $booking_ref_no = strtoupper(bin2hex(random_bytes(5)));
        $booking_ref_no .= $user->id;

        // $su = "http://localhost:3000/booking/payment/success/". ($user!== null? $user->id: 0) ."/".$booking_ref_no;
        // $cu = "http://localhost:3000/booking/payment/cancelled/". ($user !== null? $user->id:0);
        // return $this->error($cu, 'Please register as customer before you can book.', 403);

        $mode = "CREATE";
        if ($books && $books->booked_id != null) {
            if ($books->booking_status !== BookingStatus::CANCELLED->value) {
                return $this->error('', 'Invalid request.', 403);
            }
            $mode= "UPDATE";
        }

        $request['checkout_session'] = '-';
        $request['booking_reference'] = $booking_ref_no;
        $request['total'] = floatval($books->total);
        $request['payment_mode'] = 'gcash';
        $request['user_id'] = $user->id;
        $request['payment_status'] = PaymentStatus::PENDING->value;
        $request['booking_status'] = BookingStatus::CHECKOUT->value;

        // $su = "http://localhost:3000/booking/payment/success/". $this->parent !== null? $this->parent->id: 0 ."/".$booking_ref_no;
        // $cu = "http://localhost:3000/booking/payment/cancelled/". $this->parent !== null? $this->parent->id:0;
        // return $this->success('OIOI', 'Success', 200);
        
        $parent = null;
        $child = null;
        DB::transaction(function () use ($request, $user, $booking_ref_no, $books, $mode) {
            if ($mode==='CREATE') {   
                $this->parent = BookedSummary::create([
                    'checkout_session' => '-',
                    'booking_reference' => $booking_ref_no,
                    'total' => floatval($books->total),
                    'payment_mode' => 'gcash',
                    'user_id' => $user->id,
                    'payment_status' => PaymentStatus::PENDING->value,
                ]);
                $this->child = BookedItems::create(['booked_summary_id'=> $this->parent->id, 'books_id' => $books->id]);
                
            } else {
                $this->parent = BookedSummary::where('id', $books->booked_id)->first();
                $this->parent->update([
                    'checkout_session' => '-',
                    'booking_reference' => $booking_ref_no,
                    'total' => floatval($books->total),
                    'payment_mode' => 'gcash',
                    'payment_status' => PaymentStatus::PENDING->value,
                ]);
            }
            
            $request['booked_id'] = $this->parent->id;
            $books->update($request->only(['booked_id', 'booking_status']));
            
        });

        $amount_int = sprintf("%.2f", $books->total);
        $amount_int = str_replace('.','',$amount_int);

        $callback_url_params = ($this->parent !== null? $this->parent->id:0) .'/'.$booking_ref_no;

        $data = [
            'data' => [
                'attributes' => [
                    "line_items" =>[
                        [
                            'currency'      => 'PHP',
                            'amount'        => (int)$amount_int,
                            'quantity'      => 1,
                            'description'   => $books->product_address,
                            'name'          => $books->product_name,
                        ],
                    ],
                    'payment_method_types' => [
                        'gcash'
                    ],
                    'send_email_receipt' => false,
                    'show_description' => true,
                    'show_line_items' => false,
                    'reference_number' => $booking_ref_no,
                    'success_url' => 'http://localhost:3000/booking/payment/success/'. $callback_url_params ,
                    'cancel_url' => 'http://localhost:3000/booking/payment/cancelled/'. $callback_url_params ,
                    'description' => 'PaReserve Booking - '. $books->product_name,
                    "billing" => [
                        "address" => [
                            "city" => null,
                            "country" => null,
                            "line1" => null,
                            "line2" => null,
                            "postal_code" => null,
                            "state" => null
                        ],
                        "email" => $user->email,
                        "name" => $user->name,
                        "phone" => null
                    ],
                    "merchant" => "PaReserve Test Account",
                ],
            ]
        ];


        /*
        // dd(json_encode($data));
        // $response = Curl::to('https://api.paymongo.com/v1/checkout_sessions')
        //             ->withHeader('Content-Type: application/json')
        //             ->withHeader('accept: application/json')
        //             ->withheader('authorization Basic'. base64_encode(env('PAYM_AUTH_PAY_SK')))
        //             ->withData($data)
        //             ->asJson()
        //             ->post();
        // dd($response);
        */

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.paymongo.com/v1/checkout_sessions",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "accept: application/json",
                "Access-Control-Allow-Origin: *",
                "authorization: Basic ".  base64_encode(env('PAYM_AUTH_PAY_SK'))
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $request['booking_status'] = BookingStatus::PAYMENT_SUBMITTED->value;
        $books->update($request->only([ 'booking_status']));

        if ($err) {
            // echo "cURL Error #:" . $err;
            return $this->error('', $err, 403);
        } else {
            $response = json_decode($response);
            Session::put('payment_session', $response->data->id);
            Session::put('reserve_url', $request->reserve_url);

            if ($parent !== null) {
                $request['payment_status'] = PaymentStatus::AWAITING->value;
                $request['checkout_session'] = $response->data->id;
                $this->parent->update($request->only([ 'payment_status', 'checkout_session']));
            }

            $checkout = [
                'url' => $response->data->attributes->checkout_url,
            ];

            return $this->success($checkout, 'Success', 200);
            // return redirect()->away($response->data->attributes->checkout_url);
        }
        
    }

    public function paymentSuccess(Request $request, BookedSummary $bookedSummary, $booking_ref_no){

        $user = $request->user();
        if (!$user)
            return $this->error('', 'Log in is required before you can book.', 401 );

        if ($user && strtoupper($user->user_type) !== 'CUSTOMER')
            return $this->error('', 'Please register as customer before you can book.', 403);

        if ($bookedSummary !== null && Session::has('payment_session')) {
            $payment_session_value = Session::get('payment_session');
            $bookedSummary->update([
                'payment_status' => PaymentStatus::COMPLETED->value,
                'checkout_session' => $payment_session_value,
            ]);

            $books = Books::where('booked_id', $bookedSummary->id)->first();
            $books->update([
                'booking_status' => BookingStatus::BOOKED->value
            ]);
            Session::forget('payment_session');

            // Send Email
            
            $obj = [
                'booking_ref_no' => $bookedSummary->booking_reference,
                'product_name' =>  $books->product_name,
                'from' =>  $books->from,
                'to' =>  $books->to,
                'days' =>  $books->days,
                'no_of_guest' =>  $books->number_of_guest,
                'total' =>  $bookedSummary->total,
            ];
            Mail::to($user->email)->send(new BookingConfirmation($obj));

            return $this->success($bookedSummary, 'Success', 200);
        }

        return $this->error('', 'Unable to process your request. Bad request', 400 );
    }

    public function paymentFail(Request $request, BookedSummary $bookedSummary, $booking_ref_no){
        if (Session::has('payment_session'))
        {
            $value = Session::get('payment_session');
            // dd($value);
        }
    }

    public function paymentCancelled(Request $request, BookedSummary $bookedSummary, $booking_ref_no){

        $user = $request->user();
        if (!$user)
            return $this->error('', 'Log in is required before you can book.', 401 );

        if ($user && strtoupper($user->user_type) !== 'CUSTOMER')
            return $this->error('', 'Please register as customer before you can book.', 403);

        if ($bookedSummary !== null && Session::has('payment_session')) {
            $bookedSummary->update([
                'payment_status' => PaymentStatus::CANCELLED->value
            ]);

            $books = Books::where('booked_id', $bookedSummary->id)->first();
            $books->update([
                'booking_status' => BookingStatus::CANCELLED->value
            ]);
            Session::forget('payment_session');
            $reserve_url='http://localhost:3000/listings';
            if (Session::has('reserve_url')){
                $reserve_url = Session::get('reserve_url');
                Session::forget('reserve_url');
            }
            
            return $this->success($reserve_url, 'Success', 200);
        }

        return $this->error('', 'Unable to process your request. Bad request', 400 );
    }

    public function testEmailConfirmation() {
        $obj = [
            'booking_ref_no' => 'UT976J548P',
            'product_name' =>  'My Villa',
            'from' =>  '2023-07-01',
            'to' =>  '2023-07-05',
            'days' =>  '4',
            'no_of_guest' =>  '2',
            'total' =>  '8,098.50',
        ];

        Mail::to('kaka@test.com')->send(new BookingConfirmation($obj));
    }
}
