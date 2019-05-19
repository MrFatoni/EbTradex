<?php

namespace App\Repositories\User\Trader\Eloquent;
use App\Repositories\User\Trader\Interfaces\WithdrawalInterface;
use App\Models\User\Withdrawal;
use App\Repositories\BaseRepository;

class WithdrawalRepository extends BaseRepository implements WithdrawalInterface
{
    /**
    * @var Withdrawal
    */
     protected $model;

     public function __construct(Withdrawal $withdrawal)
     {
        $this->model = $withdrawal;
     }

     public function getLast24hrWithrawalAmount(array $conditions)
     {
         return $this->model->where($conditions)->where('created_at', '>=', now()->subDay())->sum('amount');
     }
}