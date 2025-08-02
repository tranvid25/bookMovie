<?php
namespace App\Services;

use App\Models\Showtime;
use App\Repositories\ShowTimeRepository;

class ShowTimeService
{
    protected $repo;
    public function  __construct(ShowTimeRepository $repo)
    {
        $this->repo=$repo;

    }
    public function getAll()
    {
        return $this->repo->getAll();
    }
    public function getByMovieId($movieId)
    {
        return $this->repo->findByMovieIdWithRelations($movieId);
    }
    public function getByRap($id)
    {
        return $this->repo->findrapchieuwithphim($id);
    }
    public function createWithSeats(array $data)
    {
        $showtime = $this->repo->create($data);

        if (!$showtime) {
            return [
                'status' => 500,
                'message' => 'Tạo lịch chiếu thất bại',
            ];
        }

        $this->generateSeats($showtime, $data['giaVeThuong'], $data['giaVeVip']);

        return [
            'status' => 200,
            'message' => 'Tạo lịch chiếu và ghế thành công',
            'content' => $showtime
        ];
    }

    protected function generateSeats($showtime, $giaVeThuong, $giaVeVip)
    {
        $hangs = ['A','B','C','D','E','F','G','H','I','K'];
        $seats = [];

        foreach ($hangs as $hang) {
            for ($so = 1; $so <= 16; $so++) {
                $seats[] = [
                    'tenGhe' => $hang . $so,
                    'loaiGhe' => in_array($hang, ['I', 'K']) ? 'vip' : 'thuong',
                    'giaVe' => in_array($hang, ['I', 'K']) ? $giaVeVip : $giaVeThuong,
                    'daDat' => false,
                    'maLichChieu' => $showtime->maLichChieu,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        \App\Models\Seat::insert($seats);
    }
    public function update($id,array $data)
    {
        $showtime=$this->repo->find($id);
        $showtime->update($data);
        return $showtime;
    }
    public function delete($id)
    {
        $showtime=$this->repo->find($id);
        return $this->repo->delete($showtime);
    }
}
