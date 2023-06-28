<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Helpers\Otp as Otp;
use App\Mail\OneTimePass;

class UserOtpController extends Controller
{
    //
    private $user; 

    public function init(Request $request): JsonResponse
    {
        $this->user = $request->user();

        if ($this->user->user_type == 'Admin') {
            return response()->json([
                'status' => true,
                'otp_expired' => false,
                'message' => null,
                'redirect' => "/admin/dashboard",
            ]);
        }

        if ($this->user->email_verified_at != null) {
            $redirect = ($this->user->user_type == 'Partner')? '/partner/dashboard':'/';
            return response()->json([
                'status' => true,
                'otp_expired' => false,
                'message' => 'The email account is already confirmed.',
                'redirect' => $redirect
            ]);
        }

        $expired = $this->isExpired();
        $message = $expired ?'OTP has expired! Please request a new one.': null;
        if (!$expired && $this->user->otp_sent==0) {
            $obj = ['otp' =>  $this->user->otp];
            Mail::to($this->user->email)->send(new OneTimePass($obj));
            
            Auth::user()->update([
                'otp_sent' => $this->user->otp_sent+1,
            ]);

            $message = null;
        }
            
        return response()->json([
            'status' => true,
            'otp_expired' => $expired,
            'message' => $message
        ]);
    }

    private function isExpired(){
        $current_time = time();
        $otp_at = $this->user->otp_at;
        return ($current_time-$otp_at) > (int)env('OTP_EXPIRATION');
    }

    public function send(Request $request): JsonResponse
    {
        $this->user = $request->user();
        $expired = $this->isExpired();
        $message = $expired ?'OTP has expired! Please request a new one.': null;
        if ($expired) {
            $range = array(100000,999999);
            $otp = Otp::generate($range);
            $obj = ['otp' =>  $otp];
            Mail::to($this->user->email)->send(new OneTimePass($obj));
            
            Auth::user()->update([
                'otp_sent' => $this->user->otp_sent+1,
                'otp' => $otp,
                'otp_at' => time()
            ]);
            $message = null;
        }
            
        return response()->json([
            'status' => true,
            'otp_expired' => false,
            'message' => $message
        ]);
    }

    public function verify(Request $request): JsonResponse
    {
        $this->user = $request->user();
        $redirect = null;
        $expired = $this->isExpired();
        $message = $expired ?'OTP has expired! Please request a new one.': null;

        if ($this->user->email_verified_at != null) {
            $redirect = ($this->user->user_type == 'Partner')? '/partner/dashboard':'/';
            return response()->json([
                'status' => true,
                'otp_expired' => false,
                'message' => 'The email account is already confirmed.',
                'redirect' => $redirect
            ]);
        }

        if ($expired) {
            return response()->json([
                'status' => true,
                'otp_expired' => $expired,
                'message' => $message,
                'redirect' => $redirect
            ]);
        }
        
        if ($request->otp != $this->user->otp) {
            return response()->json([
                'status' => true,
                'otp_expired' => $expired,
                'message' => 'Ooops! Invalid OTP. Try again.',
                'redirect' => $redirect
            ]);
        }

        $redirect = ($this->user->user_type == 'Partner')? '/partner/dashboard':'/';
        Auth::user()->update([
            'email_verified_at' => now(),
        ]);

        Auth::user()->email_verified_at = now();
        return response()->json([
            'status' => true,
            'otp_expired' => $expired,
            'message' => null,
            'redirect' => $redirect
        ]);
    }
}
