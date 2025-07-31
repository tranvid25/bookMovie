<?php
namespace App\Services;
use Illuminate\Support\Str;
use App\Repositories\NewRepository;

use function PHPUnit\Framework\fileExists;

class NewService
{
    protected $repo;
    public function __construct(NewRepository $repo)
    {
        $this->repo=$repo;
    }
    public function getAll()
    {
        return $this->repo->getAll();
    }
    public function findByNew($newId)
    {
        return $this->repo->findnew($newId);
    }
    public function create(array $data)
    {
        if(isset($data['hinhAnh']) && $data['hinhAnh']->isValid())
        {
            $file=$data['hinhAnh'];
            $imageName=Str::random(12) . '.' . $file->getClientOriginalExtension();
            $imageDirectory='images/tintuc';
            $file->move(public_path($imageDirectory),$imageName);
            $data['hinhAnh']=url($imageDirectory.$imageName);
            $data['fileName']=$imageName;
        }
        return $this->repo->create($data);
    }
    public function update($id,array $data)
    {
        $new=$this->repo->find($id);
        if(!$new)
        {
            return false;
        }
        if(isset($data['hinhAnh']) && $data['hinhAnh']->isValid())
        {
          $file=$data['hinhAnh'];
          $imageDirectory='images/tintuc';
          if($new->hinhAnh){
            $oldImage=public_path(parse_url($new->hinhAnh,PHP_URL_PATH));
            if(fileExists($oldImage))
            {
                unlink($oldImage);
            }
          }
          $imageName=Str::random(12) . '.' . $file->getClientOriginalExtension();
          $file->move(public_path($imageDirectory),$imageName);
          $data['hinhAnh']=url($imageDirectory.$imageName);
          $data['fileName']=$imageName;
        }
        return $this->repo->update($new,$data);
    }
    public function delete($id)
    {
        $new=$this->repo->find($id);
        if(!$new) return false;

        if($new->hinhAnh){
            $imagePath=public_path(parse_url($new->hinhAnh,PHP_URL_PATH));
            if(fileExists($imagePath)){
                unlink($imagePath);
            }
        }
        return $this->repo->delete($new);
    }
}

