<?php


namespace App\Repositories;

use App\Models\Province;

class ProvinceRepository
{
    protected $model;
    public function __construct(Province $model)
    {
        $this->model=$model;
    }
    public function getAll()
    {
        return $this->model->all();
    }
    public function findOrFail($id){
        return $this->model->findOrFail($id);
    }
    public function create(array $data)
    {
        return $this->model->create($data);
    }
    public function update(Province $province,array $data)
    {
        return $province->update($data);
    }
    public function delete(Province $province)
    {
        return $province->delete();
    }
}
