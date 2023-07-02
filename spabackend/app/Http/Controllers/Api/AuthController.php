<?php

/* Auth Login Intended for API only. Not for Single Page Application */
namespace App\Http\Controllers\Api;

use Browser;
use App\Helpers\Otp;
use App\Models\User;
use App\Models\LoginOtp;
use App\Models\UserLogs;

use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Mail\LoginOneTimePass;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Auth\LoginRequest;

class AuthController extends Controller
{
    use HttpResponses;

    public function login(LoginRequest $request){
        $request->authenticate(); // Validate user credentials
        $user = User::where('email', $request->email)
            // ->where('password', $request->password)
            // ->whereNotNull('email_verified_at')
            ->first();
        
        if ($user && $user->email_verified_at != null ) {
            //Return with other attributes:
            // return $this->success([
            //     'user' => $user->makeHidden(['created_at', 'updated_at', 'otp_sent', 'otp_at']),
            //     'token' => $user->createToken('PaReserve.Api.Tk.'.$user->name)->plainTextToken
            // ]);

            // Or Directly return the Token:
            return $user->createToken('PaReserve.Api.Tk.'.$user->email)->plainTextToken;
        }
        
        return response()->json([
            'message' => 'Invalid or unverified user account.'
        ], 401);
    }

    public function loginLog(Request $request){
        $user = $request->user();

        $log_data = [
            'isBot' => Browser::isBot(),
            'isOtpRequired' => false,
            'otp_id' => null
        ];

        // When You call the detect function You will get a result object, from the current user's agent.
        // $result = Browser::detect();
        // return $this->success($result['browserFamily'], 'OK', 200);
        $clientIp = $request->ip();

        if (Browser::isBot()) {
            $log_data['isOtpRequired'] = true;
        } else {
            $ulr = UserLogs::where('user_id', $user->id)->first();

            if (empty($ulr)) {
                $log_data['isOtpRequired'] = false;
            } else {
                $ulr = UserLogs::where('user_id', $user->id)
                    ->where('device_type', Browser::deviceType())
                    ->where('platform_name', Browser::platformName())
                    ->where('browser_family', Browser::browserFamily())
                    ->where('ip', $request->ip())
                    ->orderBy('created_at', 'desc')
                    ->take(10)
                    ->get();

                if ($ulr !== null && count($ulr)==0) {
                    $log_data['isOtpRequired'] = true;

                    $range = array(100000,999999);
                    $otp = Otp::generate($range);

                    //Create Login Otp Record
                    $loginOtp = LoginOtp::create([
                                    'user_id' => $user->id,
                                    'otp' => $otp,
                                    'is_verified' => false
                                ]);
                    
                    if ($loginOtp) $log_data['otp_id'] = $loginOtp->id;

                    // Send Email
                    $obj = [
                        'otp' => $otp,
                    ];
                    Mail::to($user->email)->send(new LoginOneTimePass($obj));
                }

                // $r = [
                //     'existing_record' =>$ulr,
                //     'response' => $log_data,
                //     'device_type' => Browser::deviceType(),
                //     'platform_name' => Browser::platformName(),
                //     'browser_family' => Browser::browserFamily(),
                //     'ip' => $clientIp,
                // ];
                // return $this->success($r, 'ulrc data', 200);
            }
        }

        UserLogs::create([
            'user_id' => $user->id,
            'is_bot' => Browser::isBot(),
            'device_type' => Browser::deviceType(),
            'platform_name' => Browser::platformName(),
            'browser_family' => Browser::browserFamily()==='Unknown'? "": Browser::browserFamily(),
            'ip' => $clientIp
        ]);

        // $request->ip()
        // \Request::getClientIp(true)
        
        return $this->success($log_data, 'OK', 200);
    }

    public function loginVerify(Request $request) {
        $request->validate([
            'id' => ['required'],
            'otp' => ['required'],
        ]);

        $user = $request->user();
        $redirect = ($user->user_type == 'Partner')? '/partner/dashboard':'/';
        
        $loginOtp = LoginOtp::where('id', $request->id)
                            ->where('user_id', $user->id)
                            ->where('otp', $request->otp)
                            ->first();

        if (empty($loginOtp)) {
            return response()->json([
                'message' => 'Ooops! Invalid OTP',
                'errors' => [
                    'otp' => array('Ooops! Invalid OTP') 
                ]
            ], 422);
            
        } else {

            $loginOtp->is_verified = true;
            $loginOtp->save();
            return response()->json([
                'status' => true,
                'message' => 'Success',
                'redirect' => $redirect,
            ]);
        }
    }
}
