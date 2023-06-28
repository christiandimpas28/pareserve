<?php

use App\Mail\OneTimePass;
use App\Helpers\Otp as Otp;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BooksController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::get('testsend', function (){
    $range = array(100000,999999);
    $otp = Otp::generate($range);
    $obj = [
        'otp' => $otp
    ];
    Mail::to('my@email.com')->send(new OneTimePass($obj));
    
});

require __DIR__.'/auth.php';


