<?php
namespace App\Services;

use App\Repositories\ProvinceRepository;
use Illuminate\Support\Str;

use function PHPUnit\Framework\fileExists;

class ProvinceService
{
    protected $repo;
    public function __construct(ProvinceRepository $repo)
    {
        $this->repo=$repo;
    }
    public function getAll()
    {
        return $this->repo->getAll();
    }
    public function findProvince($id)
    {
        return $this->repo->findOrFail($id);
    }
    public function create(array $data)
    {
        if(isset($data['hinhAnh']) && $data['hinhAnh']->isvalid())
        {
            $file=$data['hinhAnh'];
            $imageName=Str::random(12) . '.' . $file->getClientOriginalExtension();
            $imageDirectory='images/province/';
            $file->move(public_path($imageDirectory),$imageName);
            $data['hinhAnh']=url($imageDirectory.$imageName);
        }
        return $this->repo->create($data);
    }
    public function update($id, array $data)
    {
    $province = $this->repo->findOrFail($id);

    // Nếu có ảnh mới upload
    if (isset($data['hinhAnh']) && $data['hinhAnh']->isValid()) {
        $file = $data['hinhAnh'];
        $imageDirectory = 'images/province/';

        // Xóa ảnh cũ nếu có
        if ($province->hinhAnh) {
            $oldImage = public_path(parse_url($province->hinhAnh, PHP_URL_PATH));
            if (file_exists($oldImage)) {
                unlink($oldImage);
            }
        }

        // Lưu ảnh mới
        $imageName = Str::random(12) . '.' . $file->getClientOriginalExtension();
        $file->move(public_path($imageDirectory), $imageName);
        $data['hinhAnh'] = url($imageDirectory . $imageName);
    }

    // Cập nhật dữ liệu
    $province->update($data);
    return $province;
    }

    public function delete($id)
    {
    $province = $this->repo->findOrFail($id);
    return $this->repo->delete($province);
    }
}
