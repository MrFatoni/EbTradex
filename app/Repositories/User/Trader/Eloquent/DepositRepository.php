<?php

namespace App\Repositories\User\Trader\Eloquent;

use App\Repositories\User\Trader\Interfaces\DepositInterface;
use App\Models\User\Deposit;
use App\Repositories\BaseRepository;

class DepositRepository extends BaseRepository implements DepositInterface
{
    /**
     * @var Deposit
     */
    protected $model;

    public function __construct(Deposit $deposit)
    {
        $this->model = $deposit;
    }

    public function updateOrCreate(array $attributes, array $conditions)
    {
        return $this->model->updateOrCreate($conditions, $attributes);
    }

    public function firstOrCreate(array $attributes)
    {
        return $this->model->firstOrCreate($attributes);
    }

    public function firstOrFail(array $conditions, $relations = null)
    {
        return $this->model->where($conditions)->with($this->extractToArray($relations))->firstOrFail();
    }
}