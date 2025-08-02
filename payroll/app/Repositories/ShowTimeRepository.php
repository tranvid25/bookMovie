<?php


namespace App\Repositories;

use App\Models\Showtime;

class ShowTimeRepository
{
    protected $model;
    public function __construct(Showtime $model)
    {
        $this->model=$model;
    }
    public function getAll()
    {
        return $this->model->with(['rapchieu.tinhthanh','phim'])->get();
    }
    public function findByMovieIdWithRelations($movieId)
    {
        return $this->model->where('maPhim', $movieId)
            ->with(['rapchieu', 'rapchieu.tinhthanh', 'phim'])
            ->get();
    }
    public function findrapchieuwithphim($id)
    {
        return $this->model->where('maLichChieu',$id)->with(['rapchieu','phim'])->first();
    }
    public function find($id)
    {
        return $this->model->find($id);
    }
    public function create(array $data)
    {
        return $this->model->create($data);
    }
    public function update(Showtime $showtime,array $data)
    {
        return $showtime->update($data);
    }
    public function delete(Showtime $showtime)
    {
        return $showtime->delete();
    }
}
