<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Movie\StoreMovieRequest;
use App\Models\Movie;
use App\Services\MovieService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
class MovieController extends Controller
{
 protected $service;
 public function __construct(MovieService $service)
 {
   $this->service=$service;
 }
 public function index()
 {
    $movie=$this->service->getAll();
    if(!$movie){
        return response()->json([
            'status'=>404,
            'message'=>'Movie not found'
        ]);
    }
    else{
        return response()->json([
            'status'=>200,
            'content'=>$movie
        ]);
    }
 }
 public function show($id){
    $movie=$this->service->findRapAndLich($id);
    if(!$movie){
        return response()->json([
            'status'=>404,
            'message'=>'Movie not found'
        ]);
    }
    else{
        return response()->json([
            'status'=>200,
            'content'=>$movie
        ]);
    }
 }
 public function showCity($id)
 {
    $movie=$this->service->findCity($id);
    if(!$movie){
        return response()->json([
            'status'=>404,
            'message'=>'Movie not found'
        ]);
    }
    else{
        return response()->json([
            'status'=>200,
            'content'=>$movie
        ]);
    }
 }
 public function store(StoreMovieRequest $request)
 {
    $data = $request->validated();
    $data['hinhAnh'] = $request->file('hinhAnh');
    $movie = $this->service->create($data);
    if(!$movie){
        return response()->json([
            'status'=>404,
            'message'=>'Movie not found'
        ]);
    }
    else{
        return response()->json([
            'status'=>200,
            'data'=>$movie
        ]);
    }
 }
 public function update(StoreMovieRequest $request,$id)
 {
    $data = $request->validated();
    $data['hinhAnh'] = $request->file('hinhAnh'); // 👈 thêm dòng này
    $movie = $this->service->update($id, $data);
    if(!$movie){
        return response()->json([
            'status'=>404,
            'message'=>'Movie not found'
        ]);
    }
    else{
        return response()->json([
            'status'=>200,
            'data'=>$movie
        ]);
    }
 }
 public function destroy($id)
 {
    $movie=$this->service->delete($id);
    if(!$movie){
        return response()->json([
            'status'=>404,
            'message'=>'Movie not found'
        ]);
    }
    else{
        return response()->json([
            'status'=>200,
            'message'=>'Delete successfully'
        ]);
    }

 }
 public function search(Request $request)
 {
    $movies = $this->service->search($request->query('q'));
    return response()->json([
        'status' => 200,
        'content' => $movies
    ]);
 }

}
