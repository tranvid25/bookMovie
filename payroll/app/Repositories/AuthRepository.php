<?php


namespace App\Repositories;

use App\Models\User;

class AuthRepository{
    protected $model;
    public function __construct(User $model)
    {
       $this->model=$model;
    }
    public function create(array $data)
    {
        return $this->model->create($data);
    }
    public function findByEmail(string $email)
    {
        return $this->model->where('email',$email)->first();
    }
    public function updatePassword(User $user,string $hashedPassword)
    {
        $user->password=$hashedPassword;
        $user->save();
        return $user;
    }

}
