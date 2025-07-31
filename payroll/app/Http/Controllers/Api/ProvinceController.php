<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Province\ProvinceRequest;
use App\Models\Province;
use App\Services\ProvinceService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    protected $service;
    public function __construct(ProvinceService $service)
    {
        $this->service=$service;

    }
    public function index(){
      try {
        $province=$this->service->getAll();
        return response()->json([
            'status'=>200,
            'content'=>$province
        ]);
      } catch (ModelNotFoundException $e) {
        return response()->json([
            'status'=>404,
            'message'=>'Province not exists'
        ]);

      }
    }
    public function show($id)
    {
        try {
            $province=$this->service->findProvince($id);
            return response()->json([
                'status'=>200,
                'content'=>$province
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'=>500,
                'message'=>'New not found',
                'error'=>$e->getMessage()
            ],500);
        }
    }
    public function store(ProvinceRequest $request){
        try {
            $province=$this->service->create($request->validated());
            return response()->json([
                'status'=>201,
                'message'=>'created successfully!',
                'data'=>$province
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>500,
                'message'=>'created failed',
                'error'=>$th->getMessage()
            ]);
        }
    }
    public function update($id,ProvinceRequest $request)
    {
        try {
            $province=$this->service->update($id,$request->validated());
            return response()->json([
                'status'=>201,
                'message'=>'Update successfully!',
                'data'=>$province
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>500,
                'message'=>'Update Failed',
                'error'=>$th->getMessage()
            ],500);
        }
    }
    public function destroy($id)
{
    try {
        $province = $this->service->delete($id);
        return response()->json([
            'status' => 200,
            'message' => 'Delete Success' // ❗ Fix message cho đúng logic
        ]);
    } catch (\Throwable $th) {
        return response()->json([
            'status' => 500,
            'message' => 'Delete Failed',
            'error' => $th->getMessage()
        ]);
    }
}

}
