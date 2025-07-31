<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\New\NewRequest;
use App\Models\TinTuc;
use App\Services\NewService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use function PHPUnit\Framework\fileExists;

class newsController extends Controller
{
   protected $servie;
   public function __construct(NewService $servie)
   {
     $this->servie=$servie;
   }
   public function index()
   {
    $new=$this->servie->getAll();
    return response()->json([
        'status'=>200,
        'content'=>$new
    ]);
   }
   public function show($id){
    $new=$this->servie->findByNew($id);
    return response()->json([
        'status'=>200,
        'content'=>$new
    ]);
   }
   public function store(NewRequest $request){
    $data = $request->validated();
    $data['hinhAnh'] = $request->file('hinhAnh');
    $new = $this->servie->create($data);
    if(!$new){
        return response()->json([
            'status'=>404,
            'message'=>'Movie not found'
        ]);
    }
    else{
        return response()->json([
            'status'=>200,
            'data'=>$new
        ]);
    }
   }
   public function update($id,NewRequest $request)
   {
    $data = $request->validated();
    $data['hinhAnh'] = $request->file('hinhAnh');
    $new = $this->servie->update($data,$id);
    if(!$new){
        return response()->json([
            'status'=>404,
            'message'=>'Movie not found'
        ]);
    }
    else{
        return response()->json([
            'status'=>200,
            'data'=>$new
        ]);
    }
   }
   public function destroy($id){
    $new=$this->servie->delete($id);
    if(!$new){
        return response()->json([
            'status'=>404,
            'message'=>'new not found'
        ]);
    }
    else{
        return response()->json([
            'status'=>200,
            'message'=>'Delete successfully'
        ]);
    }
   }
}

