<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShowTime\ShowTimeRequest;
use App\Models\Seat;
use App\Models\Showtime;
use App\Services\ShowTimeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ShowtimeController extends Controller
{
    protected $service;
    public function __construct(ShowTimeService $service)
    {
        $this->service=$service;
    }
    public function index(){
        try {
            $showtime=$this->service->getAll();
            if(!$showtime){
                return response()->json([
                'status'=>404,
                'message'=>'ShowTime not found'
                ]);
            }
            else{
                return response()->json([
                    'status'=>200,
                    'content'=>$showtime
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>500,
                'message'=>'Get Failed',
                'error'=>$th->getMessage()
            ]);
        }
    }
    public function showbyMovie($id){
        try {
            $showtime=$this->service->getByMovieId($id);
        if(!$showtime)
        {
            return response()->json([
                'status'=>404,
                'message'=>'ShowTime not found'
            ]);
        }
        else
        {
            return response()->json([
                'status'=>200,
                'message'=>'Get ShowTime successfully!',
                'content'=>$showtime
            ]);
        }
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>500,
                'error'=>$th->getMessage()
            ]);
        }

    }
    public function show($id){
        try {
            $showtime=$this->service->getByRap($id);
            if(!$showtime)
            {
                return response()->json([
                    'status'=>404,
                    'message'=>'Showtime not found'
                ]);
            }
            else
            {
                return response()->json([
                    'status'=>200,
                    'content'=>$showtime
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>500,
                'error'=>$th->getMessage()
            ]);
        }
    }
    public function store(ShowTimeRequest $request)
    {
        try {
            $showtime=$this->service->createWithSeats($request->validated());
            return response()->json([
                'status'=>200,
                'message'=>'Created successfully!',
                'data'=>$showtime
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>500,
                'message'=>'createFailed',
                'error'=>$th->getMessage()
            ]);
        }

    }
public function update(ShowTimeRequest $request, string $id)
{
    try {
        $showtime = $this->service->update($id,$request->validated());
        return response()->json([
        'status' => 200,
        'message' => 'Showtime successfully updated',
        'data'=>$showtime
        ], 200);
    } catch (\Throwable $th) {
        return response()->json([
            'status'=>500,
            'message'=>'Update Failed',
            'error'=>$th->getMessage()
        ]);
    }
}
   public function destroy($id){
    try {
        $showtime=$this->service->delete($id);
        return response()->json([
            'status'=>200,
            'message'=>'Deleted Successfully!'
        ]);
    } catch (\Throwable $th) {
        return response()->json([
            'status'=>500,
            'message'=>'Delete Failed',
            'error'=>$th->getMessage()
        ]);
    }
   }
}
