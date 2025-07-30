<?php


namespace App\Repositories;

use App\Models\Banner;

class BannerRepository{
    protected $model;
    public function __construct(Banner $model)
    {
        $this->model=$model;
    }
    public function all()
    {
        return $this->model->all();
    }
    public function find($id)
    {
        return $this->model->find($id);
    }
    public function create(array $data)
    {
        return $this->model->create($data);
    }
    public function update(Banner $banner,array $data)
    {
        return $banner->update($data);
    }
    public function delete(Banner $banner)
    {
        return $banner->delete();
    }
}
