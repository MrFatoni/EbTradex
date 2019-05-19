<?php

namespace App\Repositories\Exchange\Interfaces;

interface StockGraphDataInterface
{
    public function updateOrCreate($conditions, $attributes);
}