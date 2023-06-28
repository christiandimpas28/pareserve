<?php

namespace App\Http\Controllers\Api;

use App\Models\BookedSummary;
use App\Http\Requests\StoreBookedSummaryRequest;
use App\Http\Requests\UpdateBookedSummaryRequest;

class BookedSummaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookedSummaryRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(BookedSummary $bookedSummary)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BookedSummary $bookedSummary)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookedSummaryRequest $request, BookedSummary $bookedSummary)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BookedSummary $bookedSummary)
    {
        //
    }

    private function generateBookingReference($id) {
        $user_id = $id; //user id       
        $id_lenth=strlen($id);
        $stamp = mt_rand(2,100);
        $random_id_length = 6-$id_lenth;
        $paymentreferenceno = hexdec(uniqid(rand(),1)); 
        $paymentreferenceno = strip_tags(stripslashes($paymentreferenceno)); 
        $paymentreferenceno = str_replace(".","",$paymentreferenceno); 
        $paymentreferenceno = str_replace("E", "$stamp", $paymentreferenceno);
        $paymentreferenceno = str_replace("+", "9", $paymentreferenceno);
        $paymentreferenceno = strrev(str_replace("/","",$paymentreferenceno));
        $paymentreferenceno = substr($paymentreferenceno,0,$random_id_length);
        $paymentreference_no = $paymentreferenceno.$id; //payment reference no

        //Save in your database 
        return $paymentreference_no;
    }
}
