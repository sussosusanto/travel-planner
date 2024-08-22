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

    public function paginate($perPage)
    {
        return $this->model->where('user_id', auth()->id())->paginate($perPage);
    }

    public function find($id)
    {
        return $this->model->where('user_id', auth()->id())->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $travel = $this->model->where('user_id', auth()->id())->find($id);

        if (!$travel) {
            return false;
        }

        return $travel->update($data);
    }

    public function delete($id)
    {
        $travel = $this->model->where('user_id', auth()->id())->find($id);

        if (!$travel) {
            return false;
        }

        return $travel->delete();
    }
}
