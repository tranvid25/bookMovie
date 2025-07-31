<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RapChieu\RapChieuRequest;
use App\Models\RapChieu;
use App\Services\RapChieuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use function PHPSTORM_META\map;

class RapChieuController extends Controller
{
    protected $service;
    public function __construct(RapChieuService $service)
    {
        $this->service=$service;

    }
    public function index(){
        try {
            $rapChieu=$this->service->getTinh();
            if(!$rapChieu){
                return response()->json([
                    'status'=>404,
                    'message'=>'Rapchieu not found'
                ]);
            }else{
                return response()->json([
                    'status'=>201,
                    'message'=>'successfully!',
                    'content'=>$rapChieu
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>500,
                'message'=>'Get Failed!',
                'error'=>$th->getMessage()
            ]);
        }
    }
    public function store(RapChieuRequest $request){
        try {
            $rapChieu=$this->service->create($request->validated());
            return response()->json([
                'status'=>201,
                'message'=>'create successfully',
                'data'=>$rapChieu
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status'=>500,
                'message'=>'create failed',
                'error'=>$th->getMessage()
            ]);
        }
    }
    public function show($id){
        try {
            $rapChieu=$this->service->find($id);
            if(!$rapChieu){
                return response()->json([
                    'status'=>404,
                    'message'=>'Rapchieu not found'
                ]);
            }else{
                return response()->json([
                    'status'=>201,
                    'message'=>'successfully!',
                    'content'=>$rapChieu
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>500,
                'message'=>'Get Failed!',
                'error'=>$th->getMessage()
            ]);
        }
    }
    public function update(RapChieuRequest $request,string $id){
       try {
        $rapChieu=$this->service->update($id,$request->validated());
        return response()->json([
            'status'=>201,
            'message'=>'Update Successfully!',
            'data'=>$rapChieu
        ]);
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
            $rapChieu=$this->service->delete($id);
            return response()->json([
            'status'=>201,
            'message'=>'Delete Successfully!',
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
