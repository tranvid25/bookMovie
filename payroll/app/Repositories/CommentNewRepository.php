<?php


namespace App\Repositories;

use App\Models\Comment;

class CommentNewRepository
{
    protected $model;
    public function __construct(Comment $model)
    {
        $this->model=$model;

    }
    public function getAllNew()
    {
       return $this->model->with('BaiViet')->get();
    }
    public function getByNew($newId)
    {
        return $this->model->where('maBaiViet',$newId)->get();
    }
    public function find($id)
    {
        return $this->model->find($id);
    }
    public function create(array $data)
    {
        return $this->model->create($data);
    }
    public function update(Comment $comment,array $data)
    {
        return $comment->update($data);
    }
    public function delete(Comment $comment)
    {
        return $comment->delete();
    }
}
