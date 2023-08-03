<?php

namespace App\Http\Controllers\Api;

use App\Models\Books;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Models\CancellationRequest;
use App\Http\Controllers\Controller;

class CancellationRequestController extends Controller
{
    use HttpResponses;
    //
    // 'books_id',
    // 'user_id',
    // 'remarks',
    // 'request_status',
    // 'refunded',
    // 'refunded_amount',
    // 'refunded_date',
    public function store(Request $request, Books $books){
        $user = $request->user();
        if (strtoupper($user->user_type) !== 'CUSTOMER') {
            return $this->error('', 'Forbidden. You dont have permission to access.', 403);
        }

        $request->validate([
            'books_id' => ['required'],
            'remarks' => ['required', 'min:10', 'max:1000'],
        ]);

        $request['user_id'] = $user->id;
        $request['request_status'] = "Cancel Requested";
        $cancel = CancellationRequest::create($request->all());

        if (!$cancel) {
            return $this->error('', 'Could not process your request.', 401);
        }
        return $this->success($cancel, 'Success', 200);
    }
}
