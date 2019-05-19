<?php

namespace App\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

abstract class BaseRepository
{
    use RepositoryTrait;

    public function getAll($relations = null, $orders = null)
    {
        $query = $this->model->with($this->extractToArray($relations));

        if (!empty($orders) && is_array($orders)) {
            $query = $query->orderBy($orders);
        }

        return $query->get();
    }

    public function getByConditions(array $conditions, $relations = null, $orders = null)
    {
        $query = $this->model->where($conditions)->with($this->extractToArray($relations));

        if (!empty($orders) && is_array($orders)) {
            $query = $query->orderBy($orders);
        }

        return $query->get();
    }

    public function findOrFailById(int $id, $relations = null)
    {
        return $this->model->with($this->extractToArray($relations))->findOrFail($id);
    }

    public function findOrFailByConditions(array $conditions, $relations = null)
    {
        $instant = $this->model->where($conditions)->with($this->extractToArray($relations))->first();
        abort_if(empty($instant), 404);
        return $instant;
    }

    public function create(array $attributes)
    {
        return $this->model->create(array_only($attributes, $this->model->getFillable()));
    }

    public function update(array $attributes, int $id, string $attribute = "id")
    {
        $instance = $this->getFirstByConditions([$attribute => $id]);

        if (empty($instance)) {
            return false;
        }

        return $instance->update(array_only($attributes, $this->model->getFillable())) ? $instance : false;
    }

    public function getFirstByConditions(array $conditions, $relations = null)
    {
        return $this->model->where($conditions)->with($this->extractToArray($relations))->first();
    }

    public function updateByConditions(array $attributes, array $conditions)
    {
        $instance = $this->getFirstByConditions($conditions);

        if (empty($instance)) {
            return false;
        }

        return $instance->update(array_only($attributes, $this->model->getFillable())) ? $instance : false;
    }

    public function deleteById(int $id)
    {
        $instance = $this->getFirstById($id);

        if (empty($instance)) {
            return false;
        }

        try {
            $instance->delete();
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    public function getFirstById(int $id, $relations = null)
    {
        return $this->model->where('id', $id)->with($this->extractToArray($relations))->first();
    }

    public function deleteByConditions(array $conditions)
    {
        $instance = $this->getFirstByConditions($conditions);

        if (empty($instance)) {
            return false;
        }

        try {
            $instance->delete();
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    public function toggleStatusById(int $id, string $attribute = 'is_active')
    {
        $instance = $this->getFirstById($id);

        if (empty($instance)) {
            return false;
        }

        $instance->{$attribute} = $instance->{$attribute} ? ACTIVE_STATUS_INACTIVE : ACTIVE_STATUS_ACTIVE;

        if ($instance->update()) {
            return $instance;
        }

        return $instance->update() ? $instance : false;
    }

    public function toggleStatusByConditions(array $conditions, string $attribute = 'is_active')
    {
        $instance = $this->getFirstByConditions($conditions);

        if (empty($instance)) {
            return false;
        }

        $instance->{$attribute} = $instance->{$attribute} ? ACTIVE_STATUS_INACTIVE : ACTIVE_STATUS_ACTIVE;

        return $instance->update() ? $instance : false;
    }

    public function insert(array $attributes)
    {
        return $this->model->insert($attributes);
    }

    public function bulkUpdate(array $values)
    {
        if (!count($values)) {
            return false;
        }

        $table = $this->model->getTable();

        $sql = "UPDATE {$table} SET ";
        $updatableFieldSeparator = '';
        $rowCount = count($values);
        $updatableFields = $this->_getUpdatableFields($values);
        if (!$updatableFields) {
            return false;
        }
        foreach ($updatableFields as $updatableField) {
            //for each updatable field
            $sql .= $updatableFieldSeparator;
            $sql .= "{$updatableField} = (CASE";
            for ($i = 0; $i < $rowCount; $i++) {
                if (!isset($values[$i]['fields'][$updatableField])) {
                    continue;
                }
                $sql .= " WHEN ";
                $conditionalSyntax = '';

                // for each condition
                foreach ($values[$i]['conditions'] as $conditionalKey => $conditionalValue) {
                    if (is_array($conditionalValue)) {
                        $sql .= $conditionalSyntax . "{$conditionalKey} {$conditionalValue[0]} {$conditionalValue[1]}";
                    } else {
                        $sql .= $conditionalSyntax . "{$conditionalKey}='{$conditionalValue}'";
                    }
                    $conditionalSyntax = ' AND ';
                }

                $updatableFieldValue = $values[$i]['fields'][$updatableField];

                if (is_array($updatableFieldValue)) {
                    if ($updatableFieldValue[0] == 'increment') {
                        $sql .= " THEN {$updatableField} + {$updatableFieldValue[1]}";
                    } elseif ($updatableFieldValue[0] == 'decrement') {
                        $sql .= " THEN {$updatableField} - {$updatableFieldValue[1]}";
                    }else{
                        $sql .= " THEN {$updatableFieldValue[1]}";
                    }
                } else {
                    $sql .= " THEN '{$updatableFieldValue}'";
                }
            }
            $sql .= " ELSE {$updatableField} END) ";
            $updatableFieldSeparator = ', ';
        }

        $conditionalClause = "WHERE ";
        $conditionalFieldSeparator = '(';
        foreach ($values as $value) {
            $innerSeparator = '';
            $conditionalClause .= $conditionalFieldSeparator;
            foreach ($value['conditions'] as $conditionalKey => $conditionalValue) {
                if (is_array($conditionalValue)) {
                    $conditionalClause .= $innerSeparator . "{$conditionalKey} {$conditionalValue[0]} {$conditionalValue[1]}";
                } else {
                    $conditionalClause .= $innerSeparator . "{$conditionalKey}='{$conditionalValue}'";
                }
                $innerSeparator = ' AND ';
            }
            $conditionalClause .= ')';
            $conditionalFieldSeparator = ' OR (';
        }
        $sql .= $conditionalClause;
        return DB::update($sql);
    }

    public function paginate(array $columns = ['*'], int $perPage = ITEM_PER_PAGE, string $paginationKey = 'p')
    {
        return $this->model->paginate($perPage, $columns, $paginationKey);
    }

    public function simplePaginate(array $columns = ['*'], int $perPage = ITEM_PER_PAGE, string $paginationKey = 'p',$where=null,$order='desc')
    {
        if(!empty($where)){
            $this->model = $this->model->where($where);
        }
        if($order != 'asc'){
            $order = 'desc';
        }
        return $this->model->orderBy('id',$order)->simplePaginate($perPage, $columns, $paginationKey);
    }

    public function filters($searchFields, $orderFields = null, $whereArray = null, $selectData = null, $joinArray = null, $groupBy=null,$paginationKey = 'p', $dateField = 'created_at')
    {
        $tableName = $this->model->getTable();
        if (!is_null($joinArray) && is_array($joinArray)) {
            $dateFieldChecker = explode('.', $dateField);
            if (count($dateFieldChecker) == 1) {
                $dateField = $tableName . '.' . $dateField;
            }
        }
        $itemPerPage = empty($itemPerPage) ? admin_settings('item_per_page') : $itemPerPage;
        $itemPerPage = filter_var($itemPerPage, FILTER_VALIDATE_INT) != false ? $itemPerPage : ITEM_PER_PAGE;
        $order = \Request::get($paginationKey . '_ord');
        $col = \Request::get($paginationKey . '_sort');
        $search = \Request::get($paginationKey . '_srch');
        $frm = \Request::get($paginationKey . '_frm');
        $to = \Request::get($paginationKey . '_to');
        $comp = \Request::get($paginationKey . '_comp');
        $ssf = \Request::get($paginationKey . '_ssf');

        if ($order == 'a') {
            $order = 'asc';
        } else {
            $order = 'desc';
        }

        $comparison = ['e' => '=', 'lk' => 'like', 'l' => '<', 'le' => '<=', 'm' => '>', 'me' => '>=', 'ne' => '!='];
        $comparison = array_key_exists($comp, $comparison) ? $comparison[$comp] : $comparison = 'LIKE';
        if ($orderFields) {
            $allcol = $orderFields;
        }

        $whereFields = array_column($searchFields, 0);
        $whereFields = array_key_exists($ssf, $whereFields) ? $whereFields[$ssf] : array_values($whereFields);
        $getelements = [$paginationKey . '_srch' => $search, $paginationKey . '_ord' => $order, $paginationKey . '_sort' => $col, $paginationKey . '_frm' => $frm, $paginationKey . '_to' => $to, $paginationKey . '_ssf' => $ssf, $paginationKey . '_comp' => $comp];
        if (isset($allcol)) {
            $column = $this->_column_query($allcol, $paginationKey . '_sort', $tableName . '.id');
        }

        $srcharr = $comparison == 'like' ? explode(' ', $search) : $search;

        foreach ($getelements as $key => $val) {
            if ($val == '') {
                unset($getelements[$key]);
            } elseif ($key == $paginationKey . '_frm' || $key == $paginationKey . '_to') {
                if (validate_date($val) == false) {
                    unset($getelements[$key]);
                }
            }
        }


        if ($joinArray != null && is_array($joinArray[0])) {
            foreach ($joinArray as $arr) {
                if(isset($arr[4])){
                    $this->model = $this->model->leftJoin($arr[0], function($join) use($arr) {
                        $join->on($arr[1], $arr[2], $arr[3])
                            ->where($arr[4]);
                    });
                }
                else {
                    $this->model = $this->model->leftJoin($arr[0], $arr[1], $arr[2], $arr[3]);
                }
            }
        } elseif ($joinArray != null) {
            if(isset($joinArray[4])){
                $this->model = $this->model->leftJoin($joinArray[0], function($join) use($joinArray) {
                    $join->on($joinArray[1], $joinArray[2], $joinArray[3])
                        ->where($joinArray[4]);
                });
            }
            else{
                $this->model = $this->model->leftJoin($joinArray[0], $joinArray[1], $joinArray[2], $joinArray[3]);
            }
        }

        if (isset($getelements[$paginationKey . '_frm'])) {
            $this->model = $this->model->where($dateField, '>=', $frm);
        }
        if (isset($getelements[$paginationKey . '_to'])) {
            $this->model = $this->model->where($dateField, '<', Carbon::parse($to)->addDay());
        }

        if (!is_null($whereArray)) {
            $this->model = $this->_where_builder($whereArray);
        }

        if (!empty($search)) {
            $this->model = $this->model->where(function ($query) use ($srcharr, $whereFields, $comparison) {
                $firstQuery = 1;
                if (is_array($srcharr) && $comparison == 'like') {
                    foreach ($srcharr as $wh) {
                        if (is_array($whereFields)) {
                            foreach ($whereFields as $sf) {
                                if ($firstQuery == 1) {
                                    $query->where($sf, 'like', '%' . $wh . '%');
                                } else {
                                    $query->orWhere($sf, 'like', '%' . $wh . '%');
                                }
                                $firstQuery = 0;
                            }
                        } else {
                            if ($firstQuery == 1) {
                                $query->where($whereFields, 'like', '%' . $wh . '%');
                            } else {
                                $query->orWhere($whereFields, 'like', '%' . $wh . '%');
                            }
                            $firstQuery = 0;
                        }
                    }
                } else {
                    if (is_array($whereFields)) {
                        foreach ($whereFields as $sf) {
                            if ($firstQuery == 1) {
                                $query->where($sf, $comparison, $srcharr);
                            } else {
                                $query->orWhere($sf, $comparison, $srcharr);
                            }
                            $firstQuery = 0;
                        }
                    } else {
                        $query->where($whereFields, $comparison, $srcharr);
                    }
                }
            });
        }
        if ($selectData != null) {
            $this->model = $this->model->select($selectData);
        }
        if(!empty($groupBy)){
            $this->model = $this->model->groupBy($groupBy);
        }

        if (!empty($column)) {
            $this->model = $this->model->orderBy($column, $order);
        }

        $data = $this->model->get();

        return $data;
    }

    public function paginateWithFilters($searchFields, $orderFields = null, $whereArray = null, $selectData = null, $joinArray = null, $groupBy=null, $itemPerPage=null, $paginationKey = 'p', $dateField = 'created_at')
    {
        $tableName = $this->model->getTable();
        if (!is_null($joinArray) && is_array($joinArray)) {
            $dateFieldChecker = explode('.', $dateField);
            if (count($dateFieldChecker) == 1) {
                $dateField = $tableName . '.' . $dateField;
            }
        }
        $itemPerPage = empty($itemPerPage) ? admin_settings('item_per_page') : $itemPerPage;
        $itemPerPage = filter_var($itemPerPage, FILTER_VALIDATE_INT) != false ? $itemPerPage : ITEM_PER_PAGE;
        $order = \Request::get($paginationKey . '_ord');
        $col = \Request::get($paginationKey . '_sort');
        $search = \Request::get($paginationKey . '_srch');
        $frm = \Request::get($paginationKey . '_frm');
        $to = \Request::get($paginationKey . '_to');
        $comp = \Request::get($paginationKey . '_comp');
        $ssf = \Request::get($paginationKey . '_ssf');

        if ($order == 'a') {
            $order = 'asc';
        } else {
            $order = 'desc';
        }

        $comparison = ['e' => '=', 'lk' => 'like', 'l' => '<', 'le' => '<=', 'm' => '>', 'me' => '>=', 'ne' => '!='];
        $comparison = array_key_exists($comp, $comparison) ? $comparison[$comp] : $comparison = 'LIKE';
        if ($orderFields) {
            $allcol = $orderFields;
        }

        $whereFields = array_column($searchFields, 0);
        $whereFields = array_key_exists($ssf, $whereFields) ? $whereFields[$ssf] : array_values($whereFields);
        $getelements = [$paginationKey . '_srch' => $search, $paginationKey . '_ord' => $order, $paginationKey . '_sort' => $col, $paginationKey . '_frm' => $frm, $paginationKey . '_to' => $to, $paginationKey . '_ssf' => $ssf, $paginationKey . '_comp' => $comp];
        if (isset($allcol)) {
            $column = $this->_column_query($allcol, $paginationKey . '_sort', $tableName . '.id');
        }

        $srcharr = $comparison == 'like' ? explode(' ', $search) : $search;

        foreach ($getelements as $key => $val) {
            if ($val == '') {
                unset($getelements[$key]);
            } elseif ($key == $paginationKey . '_frm' || $key == $paginationKey . '_to') {
                if (validate_date($val) == false) {
                    unset($getelements[$key]);
                }
            }
        }


        if ($joinArray != null && is_array($joinArray[0])) {
            foreach ($joinArray as $arr) {
                if(isset($arr[4])){
                    $this->model = $this->model->leftJoin($arr[0], function($join) use($arr) {
                        $join->on($arr[1], $arr[2], $arr[3])
                            ->where($arr[4]);
                    });
                }
                else {
                    $this->model = $this->model->leftJoin($arr[0], $arr[1], $arr[2], $arr[3]);
                }
            }
        } elseif ($joinArray != null) {
            if(isset($joinArray[4])){
                $this->model = $this->model->leftJoin($joinArray[0], function($join) use($joinArray) {
                    $join->on($joinArray[1], $joinArray[2], $joinArray[3])
                        ->where($joinArray[4]);
                });
            }
            else{
                $this->model = $this->model->leftJoin($joinArray[0], $joinArray[1], $joinArray[2], $joinArray[3]);
            }
        }

        if (isset($getelements[$paginationKey . '_frm'])) {
            $this->model = $this->model->where($dateField, '>=', $frm);
        }
        if (isset($getelements[$paginationKey . '_to'])) {
            $this->model = $this->model->where($dateField, '<', Carbon::parse($to)->addDay());
        }

        if (!is_null($whereArray)) {
            $this->model = $this->_where_builder($whereArray);
        }

        if (!empty($search)) {
            $this->model = $this->model->where(function ($query) use ($srcharr, $whereFields, $comparison) {
                $firstQuery = 1;
                if (is_array($srcharr) && $comparison == 'like') {
                    foreach ($srcharr as $wh) {
                        if (is_array($whereFields)) {
                            foreach ($whereFields as $sf) {
                                if ($firstQuery == 1) {
                                    $query->where($sf, 'like', '%' . $wh . '%');
                                } else {
                                    $query->orWhere($sf, 'like', '%' . $wh . '%');
                                }
                                $firstQuery = 0;
                            }
                        } else {
                            if ($firstQuery == 1) {
                                $query->where($whereFields, 'like', '%' . $wh . '%');
                            } else {
                                $query->orWhere($whereFields, 'like', '%' . $wh . '%');
                            }
                            $firstQuery = 0;
                        }
                    }
                } else {
                    if (is_array($whereFields)) {
                        foreach ($whereFields as $sf) {
                            if ($firstQuery == 1) {
                                $query->where($sf, $comparison, $srcharr);
                            } else {
                                $query->orWhere($sf, $comparison, $srcharr);
                            }
                            $firstQuery = 0;
                        }
                    } else {
                        $query->where($whereFields, $comparison, $srcharr);
                    }
                }
            });
        }
        if ($selectData != null) {
            $this->model = $this->model->select($selectData);
        }
        if(!empty($groupBy)){
            $this->model = $this->model->groupBy($groupBy);
        }

        if (!empty($column)) {
            $this->model = $this->model->orderBy($column, $order);
        } else {
            $this->model = $this->model->orderBy($tableName . '.id', $order);
        }
        $data = $this->model->paginate($itemPerPage, ['*'], $paginationKey)->appends($getelements);

        return $data;
    }
}