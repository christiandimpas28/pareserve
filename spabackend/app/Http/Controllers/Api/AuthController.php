<?php

/* Auth Login Intended for API only. Not for Single Page Application */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Traits\HttpResponses;
use App\Models\User;

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
}
