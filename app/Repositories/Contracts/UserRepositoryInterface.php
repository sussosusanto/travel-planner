<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface
{
    public function login(
        array $data
    );
    public function register(
        array $data
    );
}
