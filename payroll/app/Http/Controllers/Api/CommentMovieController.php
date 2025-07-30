<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentMovie\StoreCommentRequest;
use App\Http\Requests\CommentMovie\UpdateCommentRequest;
use App\Models\Comment;
use App\Services\CommentMovieService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentMovieController extends Controller
{
    protected $commentService;
    public function __construct(CommentMovieService $commentService)
    {
       $this->commentService=$commentService;
    }
    //lấy theo movie
    public function index()
    {
      $comments=$this->commentService->getAllWithMovie();
      if($comments->isEmpty()){
        return response()->json([
            'status'=>400,
            'message'=>'No comments found'
        ]);
      }
      return response()->json([
        'status'=>200,
        'content'=>$comments
      ]);
    }
    public function show($id)
    {
        $comments=$this->commentService->getByMovie($id);
        if($comments->isEmpty()){
            return response()->json([
                'status'=>400,
                'message'=>'No comments found'
            ]);
        }
        return response()->json([
            'status'=>200,
            'content'=>$comments
        ]);
    }
    public function store(StoreCommentRequest $request)
    {
        if(!Auth::check()){
            return response()->json([
                'status'=>401,
                'message'=>'Vui lòng đăng nhập'
            ]);
        }
        $user=Auth::user();
        $comments=$this->commentService->create($request->validated(),$user);
        return response()->json([
            'status'=>200,
            'message'=>'Comment created successfully',
            'data'=>$comments
        ]);
    }
    public function update(UpdateCommentRequest $request,$id)
    {
        $updated=$this->commentService->update($id,$request->validated());
        if(!$updated){
            return response()->json([
                'status'=>404,
                'message'=>'Comment not found'
            ]);
        }
        return response()->json([
            'status'=>200,
            'comment'=>$updated
        ]);
    }
     public function destroy($id)
    {
        $deleted = $this->commentService->delete($id);

        if (!$deleted) {
            return response()->json(['status' => 404, 'message' => 'Comment not found']);
        }

        return response()->json(['status' => 200, 'message' => 'Comment deleted successfully']);
    }

}
