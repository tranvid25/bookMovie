<?php


namespace App\Repositories;

use App\Models\FeedBack;

class FeedBackRepository
{
    protected $model;
    public function __construct(FeedBack $model)
    {
       $this->model=$model;
    }
    public function getAll(){
        return $this->model->all();
    }
    public function findByFeedBack($FeedBackId)
    {
        return $this->model->find($FeedBackId);
    }
    public function find($id)
    {
        return $this->model->find($id);
    }
    public function create(array $data)
    {
        return $this->model->create($data);
    }
    public function update(FeedBack $feedBack,array $data)
    {
        return $feedBack->update($data);
    }
    public function delete(FeedBack $feedBack)
    {
        return $feedBack->delete();
    }

}
