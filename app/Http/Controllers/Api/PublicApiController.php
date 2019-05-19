<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\JobException;
use App\Http\Requests\Api\PublicApiRequest;
use App\Repositories\User\Admin\Interfaces\StockPairInterface;
use App\Http\Controllers\Controller;
use App\Services\Exchange\StockGraphDataService;

class PublicApiController extends Controller
{
    public function command(PublicApiRequest $request)
    {
        $command = $request->get('command');

        return $this->{$command}($request);
    }

    public function returnTicker($request)
    {
        $conditions = ['stock_pairs.is_active' => ACTIVE_STATUS_ACTIVE];
        if($request->has('coinPair'))
        {
            $coinPair = explode('_', $request->get('coinPair'));
            $conditions['stock_item.item'] = strtoupper($coinPair[0]);
            $conditions['base_item.item'] = strtoupper($coinPair[1]);
        }

        $response = app(StockPairInterface::class)->getAllStockPairForApiByConditions($conditions);

        if( empty($response) )
        {
            return response()->json('No coin pair found.');
        }

        return $response;
    }

    public function returnChartData($request)
    {
        $coinPair = explode('_', $request->get('coinPair'));
        $interval = $request->get('interval');

        $stockPair = app(StockPairInterface::class)->getByPair(strtoupper($coinPair[0]), strtoupper($coinPair[1]));

        if(empty($stockPair))
        {
            return response()->json('Invalid coin pair.');
        }

        $chartData = app(StockGraphDataService::class)->getGraphData($stockPair->id, $interval);

        $refactoredData = [];

        foreach($chartData as $data)
        {
            $refactoredData[] = [
                'date' => $data[0],
                'open' => $data[1],
                'close' => $data[4],
                'high' => $data[2],
                'low' => $data[3],
            ];
        }

        return response()->json($refactoredData);
    }
}
