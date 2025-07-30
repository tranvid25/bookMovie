<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeedBack;
use App\Services\FeedBackService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;

class FeedBackController extends Controller
{
    protected $Service;
    public function __construct(FeedBackService $Service)
    {
       $this->Service=$Service;
    }
    public function index(){
        $feebacks=$this->Service->getAll();
        return response()->json([
            'status'=>200,
            'content'=>$feebacks
        ]);
    }
    public function show($id){
        $feebacks=$this->Service->findByFeedBack($id);
        return response()->json([
            'status'=>200,
            'content'=>$feebacks
        ]);
    }
    public function create(){
        
    }
}
