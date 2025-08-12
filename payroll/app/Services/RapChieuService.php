<?php
namespace App\Services;

use App\Repositories\RapChieuReposity;

class RapChieuService
{
    protected $repo;
    public function __construct(RapChieuReposity $repo)
    {
        $this->repo=$repo;
    }
    public function getTinh()
    {
        return $this->repo->getTinh();
    }
    public function find($id)
    {
        return $this->repo->find($id);
    }
    public function create(array $data)
    {
        return $this->repo->create($data);
    }
    public function update($id,array $data)
    {
        $RapChieu=$this->repo->find($id);
        $RapChieu->update($data);
        return $RapChieu;
    }
    public function delete($id)
    {
        $RapChieu=$this->repo->find($id);
        return $this->repo->delete($RapChieu);
    }
}
