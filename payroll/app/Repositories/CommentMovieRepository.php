<?php
namespace App\Repositories;

use App\Models\Comment;

class CommentMovieRepository
{
    protected $model;
    public function __construct(Comment $model)
    {
       $this->model=$model;
    }
    public function allWithMovie()
    {
        return $this->model->with('tinPhim')->get();
    }
    public function findByMovie($movieId)
    {
        return $this->model->where('maPhim',$movieId)->get();
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
