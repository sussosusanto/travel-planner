<?php

namespace App\Repositories\Eloquent;

use App\Models\Travel;
use App\Repositories\Contracts\TravelRepositoryInterface;

class TravelRepository implements TravelRepositoryInterface
{
    protected $model;

    public function __construct(Travel $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        //
    }

    public function find($id)
    {
        //
    }

    public function create(array $data)
    {
        //
    }

    public function update($id, array $data)
    {
        //
    }

    public function delete($id)
    {
        //
    }
}
