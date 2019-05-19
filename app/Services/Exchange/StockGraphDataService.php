<?php

namespace App\Services\Exchange;

use App\Repositories\Exchange\Interfaces\StockGraphDataInterface;
use Carbon\Carbon;

class StockGraphDataService
{
    public $stockGraphData;

    public function __construct(StockGraphDataInterface $stockGraphData)
    {
        $this->stockGraphData = $stockGraphData;
    }

    public function process($stockPairId, $latestStockPrice, $date)
    {
        $currentDate = Carbon::now();
        $startDate = $currentDate->copy()->startOfDay();
        $endDate = $currentDate->copy()->addDay()->startOfDay();
        $conditions = [
            'stock_pair_id' => $stockPairId,
            ['created_at', '>=', $startDate],
            ['created_at', '<', $endDate],
        ];
        $stockGraphData = $this->stockGraphData->getFirstByConditions($conditions);

        if (empty($stockGraphData)) {
            $stockGraphData = $this->generateGraphDataWithEmptyValue($startDate, $endDate);
        }

        $stockGraphDataForFiveMinutes = json_decode($stockGraphData->{'5min'}, true);
        $stockGraphDataForFifteenMinutes = json_decode($stockGraphData->{'15min'}, true);
        $stockGraphDataForThirtyMinutes = json_decode($stockGraphData->{'30min'}, true);
        $stockGraphDataForTwoHours = json_decode($stockGraphData->{'2hr'}, true);
        $stockGraphDataForFourHours = json_decode($stockGraphData->{'4hr'}, true);
        $stockGraphDataForOneDays = json_decode($stockGraphData->{'1day'}, true);


        $timeOffset = date_offset_get($date);

        $unixTime = strtotime($date) + $timeOffset;


        $intervals = [
            'stockGraphDataForFiveMinutes' => 300,
            'stockGraphDataForFifteenMinutes' => 900,
            'stockGraphDataForThirtyMinutes' => 1800,
            'stockGraphDataForTwoHours' => 7200,
            'stockGraphDataForFourHours' => 14400,
            'stockGraphDataForOneDays' => 86400
        ];

        foreach ($intervals as $variable => $interval) {

            $formation = (((int)($unixTime / $interval)) * $interval) - $timeOffset;
            $startingDate = strtotime(date('Y-m-d H:i:s', $formation));

            if (bccomp(${$variable}[$startingDate][1], '0') === 0) {
                ${$variable}[$startingDate][1] = $latestStockPrice; //open
            }

            ${$variable}[$startingDate][2] = $latestStockPrice; //close


            if (bccomp(${$variable}[$startingDate][3], '0') === 0 || bccomp(${$variable}[$startingDate][3], $latestStockPrice) === 1) {
                ${$variable}[$startingDate][3] = $latestStockPrice; //low
            }

            if (bccomp(${$variable}[$startingDate][4], '0') === 0 || bccomp($latestStockPrice, ${$variable}[$startingDate][4]) === 1) {
                ${$variable}[$startingDate][4] = $latestStockPrice;
            }

        }

        $attributes = [
            'stock_pair_id' => $stockPairId,
            '5min' => json_encode($stockGraphDataForFiveMinutes),
            '15min' => json_encode($stockGraphDataForFifteenMinutes),
            '30min' => json_encode($stockGraphDataForThirtyMinutes),
            '2hr' => json_encode($stockGraphDataForTwoHours),
            '4hr' => json_encode($stockGraphDataForFourHours),
            '1day' => json_encode($stockGraphDataForOneDays),
            'created_at' => $startDate,
            'updated_at' => $startDate,
        ];

        return $this->stockGraphData->updateOrCreate($conditions, $attributes);

    }

    private function generateGraphDataWithEmptyValue(Carbon $startDate, Carbon $endDate)
    {
        $stockGraphDataForFiveMinutes = [];
        $stockGraphDataForFifteenMinutes = [];
        $stockGraphDataForThirtyMinutes = [];
        $stockGraphDataForTwoHours = [];
        $stockGraphDataForFourHours = [];
        $stockGraphDataForOneDay = [];
        $createdAt = $startDate->copy();

        for ($startDate, $count = 0; $startDate < $endDate; $startDate->addMinute(5), $count++) {

            $data = [
                $startDate->toDateTimeString(), //Date
                0, //open,
                0, //close,
                0, //low
                0 //high
            ];
            $stockGraphDataForFiveMinutes[$startDate->timestamp] = $data;

            if ($count == 0 || $count % 3 == 0) {
                $stockGraphDataForFifteenMinutes[$startDate->timestamp] = $data;
            }

            if ($count == 0 || $count % 6 == 0) {
                $stockGraphDataForThirtyMinutes[$startDate->timestamp] = $data;
            }

            if ($count == 0 || $count % 24 == 0) {
                $stockGraphDataForTwoHours[$startDate->timestamp] = $data;
            }

            if ($count == 0 || $count % 48 == 0) {
                $stockGraphDataForFourHours[$startDate->timestamp] = $data;
            }

            if ($count == 0) {
                $stockGraphDataForOneDay[$startDate->timestamp] = $data;
            }
        }


        return (object)[
            '5min' => json_encode($stockGraphDataForFiveMinutes),
            '15min' => json_encode($stockGraphDataForFifteenMinutes),
            '30min' => json_encode($stockGraphDataForThirtyMinutes),
            '2hr' => json_encode($stockGraphDataForTwoHours),
            '4hr' => json_encode($stockGraphDataForFourHours),
            '1day' => json_encode($stockGraphDataForOneDay),
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];
    }

    public function getGraphData($stockPairId, $interval = 1440)
    {
        $intervals = chart_data_interval();

        $currentDate = Carbon::now();
        $currentDateStart = $currentDate->copy()->startOfDay();
        $currentDayEnd = $currentDate->copy()->addDay()->startOfDay();
        $conditions = [
            'stock_pair_id' => $stockPairId,
        ];

        $stockGraphs = $this->stockGraphData->getByConditions($conditions);

        $stockGraphData = [];
        $timeOffset = date_offset_get($currentDate);
        foreach ($stockGraphs as $stockGraph) {
            if ($stockGraph->created_at >= $currentDateStart && $stockGraph->created_at < $currentDayEnd) {
                $formation = (((int)(($currentDate->timestamp + $timeOffset) / ($interval * 60))) * ($interval * 60)) - $timeOffset;
                $startingDate = strtotime(date('Y-m-d H:i:s', $formation));

                $graphData = json_decode($stockGraph->{$intervals[$interval]}, true);

                $key = array_search($startingDate, array_keys($graphData), true) + 1;


                $graphData = array_slice($graphData, 0, $key);
            } else {
                $graphData = json_decode($stockGraph->{$intervals[$interval]}, true);
            }

            $stockGraphData = array_merge($stockGraphData, $graphData);
        }

        $lastGraphDate = $stockGraphs->isEmpty() ? $currentDateStart : $stockGraphs->last()->created_at->addDay()->startOfDay();

        for ($graphDate = $lastGraphDate; $graphDate <= $currentDateStart; $graphDate = $graphDateEnd) {

            $graphDateStart = $graphDate->copy()->startOfDay();
            $graphDateEnd = $graphDate->copy()->addDay()->startOfDay();

            $currentDayData = $stockGraphs->where('created_at', '>=', $graphDateStart)->where('created_at', '<', $graphDateEnd)->first();
            if (empty($currentDayData)) {
                $currentDayData = $this->generateGraphDataWithEmptyValue($graphDateStart, $graphDateEnd);
                $attributes = (array)$currentDayData;
                $attributes['stock_pair_id'] = $stockPairId;

                $currentDayData = $this->stockGraphData->create($attributes);

                $graphData = json_decode($currentDayData->{$intervals[$interval]}, true);

                if ($graphDate->copy()->startOfDay() == $currentDateStart) {
                    $formation = (((int)(($currentDate->timestamp + $timeOffset) / ($interval * 60))) * ($interval * 60) - $timeOffset);
                    $startingDate = strtotime(date('Y-m-d H:i:s', $formation));
                    $key = array_search($startingDate, array_keys($graphData), true) + 1;
                    $graphData = array_slice($graphData, 0, $key);
                }

                $stockGraphData = array_merge($stockGraphData, $graphData);
            }
        }

        return $stockGraphData;
    }
}