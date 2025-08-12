<?php


namespace App\Repositories;

use App\Models\RapChieu;

class RapChieuReposity
{
    protected $model;
    public function __construct(RapChieu $model)
    {
        $this->model=$model;

    }
    public function getTinh()
    {
        return $this->model->with('tinhThanh')->get();
    }
    public function find($id)
    {
        return $this->model->findOrFail($id);
    }
    public function create(array $data)
    {
        return $this->model->create($data);
    }
    public function update(RapChieu $rapChieu,array $data)
    {
        return $rapChieu->update($data);
    }
    public function delete(RapChieu $rapChieu)
    {
        return $rapChieu->delete();
    }
}
