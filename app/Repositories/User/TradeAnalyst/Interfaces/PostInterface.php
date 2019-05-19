<?php

namespace App\Repositories\User\TradeAnalyst\Interfaces;

interface PostInterface
{
    public function getLatestByCondition(array  $conditions, $limit = null);
}