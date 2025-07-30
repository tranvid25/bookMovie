<?php
namespace App\Services;
use App\Repositories\BannerRepository;
use Illuminate\Support\Str;
class BannerService{
    protected $bannerRepository;
    public function __construct(BannerRepository $bannerRepository)
    {
        $this->bannerRepository = $bannerRepository;
    }
    public function getAll()
    {
        return $this->bannerRepository->all();
    }
    public function getById($id)
    {
        return $this->bannerRepository->find($id);
    }
    public function create(array $data)
    {
        // Xử lý file upload nếu có
        if(isset($data['hinhAnh']) && $data['hinhAnh']->isValid()){
            $file=$data['hinhAnh'];
            $imageName=Str::random(12).'.'.$file->getClientOriginalExtension();
            $imageDirectory='images/banner/';
            $file->move(public_path($imageDirectory),$imageName);
            $data['hinhAnh']=url($imageDirectory.$imageName);
            $data['fileName']=$imageName;
        } else {
            // Nếu không có file, set giá trị mặc định
            $data['hinhAnh'] = null;
            $data['fileName'] = null;
        }

        return $this->bannerRepository->create($data);
    }
    public function update($id,array $data)
    {
        $banner=$this->bannerRepository->find($id);
        if(isset($data['hinhAnh']) && $data['hinhAnh']->isValid()){
            $file=$data['hinhAnh'];
            $imageDirectory='images/banner/';
            // xóa ảnh cũ
            if($banner->hinhAnh){
                $oldImage=public_path(parse_url($banner->hinhAnh,PHP_URL_PATH));
                if(file_exists($oldImage)){
                    unlink($oldImage);
                }
            }
            $imageName=Str::random(12).'.'.$file->getClientOriginalExtension();
            $file->move(public_path($imageDirectory),$imageName);
            $data['hinhAnh']=url($imageDirectory.$imageName);
            $data['fileName']=$imageName;
        }
        $banner->update($data);
        return $banner;
    }
    public function delete($id)
    {
        $banner=$this->bannerRepository->find($id);
        if($banner->hinhAnh){
            $imagePath=public_path(parse_url($banner->hinhAnh,PHP_URL_PATH));
            if(file_exists($imagePath)){
                unlink($imagePath);
            }
        }
        $banner->delete();
        return true;
    }
}
