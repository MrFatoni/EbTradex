<?php

namespace App\Repositories\Exchange\Eloquent;

use App\Models\Backend\StockExchange;
use App\Repositories\BaseRepository;
use App\Repositories\Exchange\Interfaces\StockExchangeInterface;

class StockExchangeRepository extends BaseRepository implements StockExchangeInterface
{
    /**
     * @var StockExchange
     */
    protected $model;

    public function __construct(StockExchange $stockExchange)
    {
        $this->model = $stockExchange;
    }


    public function getLatest(array $conditions, int $limit)
    {
        return $this->model
            ->select(['stock_exchanges.price','stock_exchanges.amount','stock_exchanges.exchange_type', 'stock_exchanges.created_at as date'])
            ->leftJoin('stock_orders', 'stock_orders.id', '=','stock_exchanges.stock_order_id')
            ->where($conditions)
            ->orderBy('stock_exchanges.id', 'desc')
            ->take($limit)
            ->get();
    }

    public function count(array $conditions)
    {
        return $this->model->where($conditions)->count();
    }
}