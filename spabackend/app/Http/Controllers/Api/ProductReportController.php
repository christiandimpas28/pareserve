<?php

namespace App\Http\Controllers\Api;

use App\Models\Books;
use App\Models\Product;
use App\Models\ProductReport;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductReportController extends Controller
{
    //
    use HttpResponses;

    public function store(Request $request, Books $books){
        $user = $request->user();
        if (strtoupper($user->user_type) !== 'CUSTOMER') {
            return $this->error('', 'Forbidden. You dont have permission to access.', 403);
        }

        $request->validate([
            'books_id' => ['required'],
            'product_id' => ['required'],
            'remarks' => ['required', 'min:10', 'max:5000'],
            'related_to' => ['required', 'max:225'],
        ]);

        $files = [];

        if ($request->hasFile('files')){
            foreach($request->file('files') as $key => $file)
            {
                $fileName = 'B'.$books->id.'-P'.$books->product_id.'-U'.$user->id.'-'.time().rand(1,99).'.'.$file->extension();  
                $file->move(public_path('uploads/complains'), $fileName);
                $files[]['name'] = $fileName;
            }

            if (sizeof($files)>0) {
                $str_files = implode(',', array_map(function ($entry) {
                    return ($entry[key($entry)]);
                }, $files));
    
                $request['photos'] = $str_files;
                $request->merge(['photos' => $str_files]);
            }       
        }

        $request['user_id'] = $user->id;
        $report = ProductReport::create($request->all());

        if (!$report) {
            return $this->error('', 'Could not process your request.', 401);
        }
        return $this->success($report, 'Success', 200);
    }
}
