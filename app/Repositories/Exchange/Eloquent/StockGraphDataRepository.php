<?php

namespace App\Repositories\Exchange\Eloquent;

use App\Models\Backend\StockGraphData;
use App\Repositories\Exchange\Interfaces\StockGraphDataInterface;
use App\Repositories\BaseRepository;

class StockGraphDataRepository extends BaseRepository implements StockGraphDataInterface
{
    /**
     * @var StockGraphData
     */
    protected $model;

    public function __construct(StockGraphData $stockGraphData)
    {
        $this->model = $stockGraphData;
    }

    public function updateOrCreate($conditions, $attributes)
    {
        return $this->model->updateOrCreate($conditions, $attributes);
    }
}