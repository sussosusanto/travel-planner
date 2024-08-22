<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function login(array $data)
    {

        $user = $this->model->where('email', $data['email'])->first();

        if (!$user || !\Hash::check($data['password'], $user->password)) {
            return null;
        }

        return $user;
    }

    public function register(array $data)
    {
        return $this->model->create($data);
    }

}
