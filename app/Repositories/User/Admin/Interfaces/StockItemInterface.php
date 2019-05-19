<?php

namespace App\Repositories\User\Admin\Interfaces;

interface StockItemInterface
{
    public function getActiveList($stockItemType = null);

    public function getCountByConditions(array $conditions);

    public function getStockPairsById($id);
}