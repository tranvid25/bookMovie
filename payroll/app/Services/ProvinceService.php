<?php
namespace App\Services;

use App\Repositories\ProvinceRepository;

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
        return $this->repo->create($data);
    }
    public function update($id,array $data)
    {
        $new=$this->repo->findOrFail($id);
        $new->update($data);
        return $new;
    }
    public function delete($id)
    {
    $new = $this->repo->findOrFail($id);
    return $this->repo->delete($new);
    }
}
