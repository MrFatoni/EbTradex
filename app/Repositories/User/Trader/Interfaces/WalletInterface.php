<?php

namespace App\Repositories\User\Trader\Interfaces;

interface WalletInterface
{
    public function findStockItem(int $id);

    public function insert(array $parameters);
}