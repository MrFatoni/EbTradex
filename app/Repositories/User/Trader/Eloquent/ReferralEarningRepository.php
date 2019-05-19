<?php

namespace App\Repositories\User\Trader\Eloquent;

use App\Models\User\ReferralEarning;
use App\Repositories\BaseRepository;
use App\Repositories\User\Trader\Interfaces\ReferralEarningInterface;

class ReferralEarningRepository extends BaseRepository implements ReferralEarningInterface
{
    /**
     * @var ReferralEarningRepository
     */
    protected $model;

    public function __construct(ReferralEarning $referralEarning)
    {
        $this->model = $referralEarning;
    }
}