<?php

namespace App\Repositories\User\Trader\Eloquent;
use App\Models\User\Wallet;
use App\Repositories\User\Admin\Interfaces\StockItemInterface;
use App\Repositories\User\Trader\Interfaces\WalletInterface;
use App\Repositories\BaseRepository;

class WalletRepository extends BaseRepository implements WalletInterface
{
    protected $model;

    public function __construct(Wallet $model)
    {
        $this->model = $model;
    }

    public function findStockItem(int $id) {
        return $this->model->where('stock_item_id', $id)->first();
    }

    public function insert(array $parameters) {
        return $this->model->insert($parameters);
    }

    public function createUnavailableWallet($userID)
    {
        $date = now();
        $activeStockItems = app(StockItemInterface::class)->getActiveList();
        $wallet = $this->getByConditions(['user_id'=>$userID]);
        $unavailalbeWallets = $activeStockItems->whereNotIn('id', $wallet->pluck('stock_item_id')->toArray());
        $walletParameters = [];

        foreach($unavailalbeWallets->pluck('id') as $stockItemID) {
            $walletParameters[] = [
                'user_id' => $userID,
                'stock_item_id' => $stockItemID,
                'created_at' => $date,
                'updated_at' => $date,
            ];
        }

        return $this->insert($walletParameters);
    }

    public function firstOrFail(array $conditions)
    {
        return $this->model->where($conditions)->firstOrFail();
    }

    public function count(array $conditions)
    {
        return $this->model->where($conditions)->count();
    }

    public function updateAllByConditions(array $attributes, array $conditions)
    {
        return $this->model->where($conditions)->update($attributes);
    }
}