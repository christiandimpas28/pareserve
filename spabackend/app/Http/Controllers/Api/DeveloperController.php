<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Spatie\Crypto\Rsa\KeyPair;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;

class DeveloperController extends Controller
{
    //
    use HttpResponses;

    public function devkeys(Request $request){
        // $pathToPrivateKey = 'public/keys/pvt';
        // $pathToPublicKey = 'public/keys/pub';
        // (new KeyPair())->generate($pathToPrivateKey, $pathToPublicKey);
        $sample_data = [
            'name' => 'Karl',
            'address' => 'asdk sakdj saldj ls'
        ];
        
        $jsonString = json_encode($sample_data);
        $jsonString = Crypt::encryptString($jsonString);

        return $this->success($jsonString, 'Success', 200);
    }
}
