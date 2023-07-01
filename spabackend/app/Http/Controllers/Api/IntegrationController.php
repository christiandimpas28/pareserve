<?php

namespace App\Http\Controllers\Api;

use App\Models\Merchant;
use App\Models\Integration;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Http\Controllers\Controller;

class IntegrationController extends Controller
{
    //
    use HttpResponses;

    public function generateIntegrationKeys(Request $request, Merchant $merchant){
        // return $this->error($request['merchant_id'], 'Invalid request', 400);
        try {
            if (!$merchant)  
                return $this->error($merchant, '1 Invalid request', 400);

            if (!$request->has('merchant_id')) 
                return $this->error(null, '2 Invalid request', 400);

            if ($request->has('merchant_id') && $request['merchant_id'] !== ($merchant->id."")) 
                return $this->error(null, 'Invalid request', 400);


            $user = $request->user();
            if (!$user)
                return $this->error('', 'Unauthorized', 401);

            if (strtoupper($user->user_type) !== 'PARTNER') 
                return $this->error('', 'Forbidden. You dont have permission to access.', 403);
            
            $integrationKeys = hGenerateIntegrationKeys();

            if ($integrationKeys === null) {
                return $this->error(null, 'Unexpected response data.', 400);
            }

            $record = $this->getIntegrationKeys($merchant->id);
            if ($record) 
                return $this->error(null, 'Unable to process your request. Record already exists.', 409);

            $integration = Integration::create([
                'merchant_id' => $merchant->id,
                'private_key' => $integrationKeys[0],
                'public_key' => $integrationKeys[1],
                'password' => null,
                'enabled' => 1,
            ]);

            $integration->makeHidden([
                'password'
            ]);

            return $this->success($integration, 'Success', 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->error('', $th->getMessage(), 400);
        }
    }

    private function getIntegrationKeys($merchant_id) {
        return Integration::where('merchant_id', $merchant_id)
                        ->first();
    }

    public function show(Request $request) 
    {
        $user = $request->user();
        if (!$user)
            return $this->error('', 'Unauthorized', 401);

        if (strtoupper($user->user_type) !== 'PARTNER') {
            return $this->error('', 'Forbidden. You dont have permission to access.', 403);
        }

        $merchant = Merchant::where('user_id', $user->id)->first();

        if (!$merchant) 
            return $this->error(null, 'Invalid request', 400);

        $json_data = array('integration' => null, 'merchant_id' => $merchant->id);

        $integration = $this->getIntegrationKeys($merchant->id);
        if ($integration) {
            $integration->makeHidden([
                'password'
            ]);
            $json_data = array('integration' => $integration, 'merchant_id' => $merchant->id);
        }
        return $this->success($json_data, 'Success', 200);
    }
}
