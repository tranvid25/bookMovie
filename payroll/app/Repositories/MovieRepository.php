<?php


namespace App\Repositories;

use App\Models\Movie;

class MovieRepository
{
    protected $model;
    public function __construct(Movie $model)
    {
      $this->model=$model;
    }
    public function getAll()
    {
        return $this->model->all();
    }
    public function findrapandlich($id)
    {
        return $this->model->with(['lichchieu.rapChieu'])->find($id);
    }
    public function find($id){
        return $this->model->find($id);
    }
    public function findCity($id)
    {
        return $this->model->with(['lichchieu.rapChieu.tinhthanh'])->find($id);
    }
    public function search($keyword)
    {
        return $this->model->where('tenPhim','like',"%$keyword%")->orWhere('moTa','like',"%$keyword%")->get();
    }
    public function create(array $data)
    {
        return $this->model->create($data);
    }
    public function update(Movie $movie,array $data)
    {
        return $movie->update($data);
    }
    public function delete(Movie $movie)
    {
        return $movie->delete();
    }

}
