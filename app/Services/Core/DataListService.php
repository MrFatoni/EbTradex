<?php

namespace App\Services\Core;

class DataListService
{
    public function dataList($query, $searchFields, $orderFields=null, $searchOnly=false, $pagination = true, $itemName='data', $is_frontend = false)
    {
        $route = url()->current();
        if (in_array('api', \Request::route()->middleware())) {
            return ['query'=>$query->toArray(), 'searchFields'=>$searchFields, 'orderFields'=>$orderFields, 'path'=>$route];
        }

        $view = $is_frontend ? config('commonconfig.front_end_view') : config('commonconfig.back_end_view');
        $itemName = !empty($itemName) ? $itemName : __('Table Data');
        // prepare query
        $data['query'] = $query;
        // prepare filters
        if($pagination){
            $paginationKey = $query->getPageName();
            $itemPerPage = $query->perPage();
            $data['pagination'] = view($view['pagination'], ['itemPerPage' => $itemPerPage, 'query' => $query, 'itemName' => $itemName, 'paginationKey' => $paginationKey, 'route' => $route])->render();
        }else{
            $paginationKey = 'p';
        }

        $data['filters'] = view($view['filter'], ['paginationKey' => $paginationKey, 'orderFields' => $orderFields, 'route' => $route, 'searchFields' => $searchFields, 'searchOnly'=>$searchOnly])->render();
        // prepare pagination


        return $data;
    }
}