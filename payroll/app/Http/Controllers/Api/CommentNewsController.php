<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentNew\StoreCommentRequest;
use App\Http\Requests\CommentNew\UpdateCommentRequest;
use App\Models\Comment;
use App\Services\CommentNewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class CommentNewsController extends Controller
{
    protected $commentService;

    public function __construct(CommentNewService $commentService)
    {
        $this->commentService = $commentService;
    }

    public function index()
    {
        $comments = $this->commentService->getAllNew();
        return response()->json([
            'status' => 200,
            'content' => $comments
        ]);
    }

    public function show($id)
    {
        $comments = $this->commentService->getByNew($id);
        return response()->json([
            'status' => 200,
            'content' => $comments
        ]);
    }

    public function store(StoreCommentRequest $request)
    {
        $comment = $this->commentService->create($request->validated());
        return response()->json([
            'status' => 200,
            'message' => 'Comment created successfully',
            'data' => $comment
        ]);
    }

    public function update(UpdateCommentRequest $request, $id)
    {
        $updated = $this->commentService->update($id, $request->validated());
        if (!$updated) {
            return response()->json(['status' => 404, 'message' => 'Comment not found']);
        }

        return response()->json(['status' => 200, 'message' => 'Comment updated successfully']);
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




