<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FeedBack\StoreFeedBackRequest;
use App\Http\Requests\FeedBack\UpdateFeedBackRequest;
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
    public function store(StoreFeedBackRequest $request){
        $feebacks = $this->Service->create($request->validated());
        return response()->json([
            'status' => 200,
            'message' => 'Comment created successfully',
            'data' => $feebacks
        ]);
    }
    public function update(UpdateFeedBackRequest $request, $id)
    {
        $updated = $this->Service->update($id, $request->validated());
        if (!$updated) {
            return response()->json(['status' => 404, 'message' => 'FeedBack not found']);
        }

        return response()->json(['status' => 200, 'message' => 'FeedBack updated successfully']);
    }

    public function destroy($id)
    {
        $deleted = $this->Service->delete($id);
        if (!$deleted) {
            return response()->json(['status' => 404, 'message' => 'FeedBack not found']);
        }

        return response()->json(['status' => 200, 'message' => 'FeedBack deleted successfully']);
    }

}
