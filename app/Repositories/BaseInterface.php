<?php
/**
 * Created by PhpStorm.
 * User: rana
 * Date: 9/30/18
 * Time: 12:39 PM
 */

namespace App\Repositories;


interface BaseInterface
{
    public function model();

    public function bulkUpdate($values);

    public function search($searchFields, $orderFields = null, $whereArray = null, $selectData = null, $joinArray = null, $paginationKey = 'p', $dateField = 'created_at');
}