<?php


namespace App\Repositories;

use App\Models\TinTuc;

class NewRepository
{
    protected $model;
    public function __construct(TinTuc $model)
    {
        $this->model=$model;
    }
    public function getAll()
    {
        return $this->model->all();
    }
    public function findnew($newId)
    {
        return $this->model->findOrFail($newId);
    }
    public function find($id)
    {
        return $this->model->find($id);
    }
    public function create(array $data)
    {
        return $this->model->create($data);
    }
    public function update(TinTuc $tintuc,$data)
    {
        return $tintuc->update($data);
    }
    public function delete(TinTuc $tinTuc)
    {
        return $tinTuc->delete();
    }
}
