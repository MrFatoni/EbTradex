<?php

namespace App\Repositories\Exchange\Interfaces;

interface StockExchangeInterface
{
    public function getLatest(array $conditions, int $limit);
}