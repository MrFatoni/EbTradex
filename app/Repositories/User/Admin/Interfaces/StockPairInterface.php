<?php

namespace App\Repositories\User\Admin\Interfaces;

interface StockPairInterface
{
    function getByPair($stockItem, $baseItem);
}