<?php
namespace App\Services;

use App\Repositories\MovieRepository;
use Illuminate\Support\Str;

class MovieService
{
    protected $repo;

    public function __construct(MovieRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getAll()
    {
        return $this->repo->getAll();
    }

    public function findRapAndLich($id)
    {
        return $this->repo->findrapandlich($id);
    }

    public function findCity($id)
    {
        $movie = $this->repo->findCity($id);
        if (!$movie) return false;

        $grouped = [];
        $now = now();

        foreach ($movie->lichchieu as $lich) {
            // Kiểm tra lịch chiếu có phải trong tương lai không
            $ngayGioChieu = $lich->ngayChieu . ' ' . $lich->gioChieu;
            if (strtotime($ngayGioChieu) <= strtotime($now)) {
                continue; // Bỏ qua lịch chiếu đã qua
            }

            $tenTinh = $lich->rapChieu->tinhthanh->tenTinh ?? 'Không rõ thành phố';
            $tenRap = $lich->rapChieu->tenRap;

            $grouped[$tenTinh][$tenRap][] = [
                'ngayChieu' => $lich->ngayChieu,
                'gioChieu' => $lich->gioChieu,
                'maLichChieu' => $lich->maLichChieu,
            ];
        }

        return [
            'phim' => [
                'maPhim' => $movie->maPhim,
                'tenPhim' => $movie->tenPhim,
                'moTa' => $movie->moTa,
            ],
            'raps' => $grouped,
        ];
    }

    public function create(array $data)
    {
        if (isset($data['hinhAnh']) && $data['hinhAnh']->isValid()) {
            $file = $data['hinhAnh'];
            $imageName = Str::random(12) . '.' . $file->getClientOriginalExtension();
            $imageDirectory = 'images/movie/';
            $file->move(public_path($imageDirectory), $imageName);
            $data['hinhAnh'] = url($imageDirectory . $imageName);
            $data['fileName'] = $imageName;
        } else {
            $data['hinhAnh'] = null;
            $data['fileName'] = null;
        }

        return $this->repo->create($data);
    }

    public function update($id, array $data)
    {
        $movie = $this->repo->find($id);
        if (!$movie) return false;

        if (isset($data['hinhAnh']) && $data['hinhAnh']->isValid()) {
            $file = $data['hinhAnh'];
            $imageDirectory = 'images/movie/';

            if ($movie->hinhAnh) {
                $oldImage = public_path(parse_url($movie->hinhAnh, PHP_URL_PATH));
                if (file_exists($oldImage)) {
                    unlink($oldImage);
                }
            }

            $imageName = Str::random(12) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path($imageDirectory), $imageName);
            $data['hinhAnh'] = url($imageDirectory . $imageName);
            $data['fileName'] = $imageName;
        }

        return $this->repo->update($movie, $data);
    }

    public function delete($id)
    {
        $movie = $this->repo->find($id);
        if (!$movie) return false;

        if ($movie->hinhAnh) {
            $imagePath = public_path(parse_url($movie->hinhAnh, PHP_URL_PATH));
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        return $this->repo->delete($movie);
    }

    public function search($keyword)
    {
        return $this->repo->search($keyword);
    }
}
