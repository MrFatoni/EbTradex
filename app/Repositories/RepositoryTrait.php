<?php

namespace App\Repositories;

trait RepositoryTrait
{
    protected function _column_query($columns, $key = 'sort', $default = null)
    {
        $data = \Request::get($key);
        if (is_numeric($data) && array_key_exists($data, array_column($columns, 0))) {
            return $columns[$data][0];
        }
        return false;
    }

    protected function _where_builder($where)
    {
        $model = $this->model;
        $output = $this->_subWhere($model, $where);
        return $output;
    }

    protected function _subWhere($model, $where)
    {
        if (count($where) <= 0) {
            return $model;
        }
        if (
            !isset($where['where_type']) &&
            !isset($where['group_query']) &&
            !isset($where[0])
        ) {
            $model = $model->where($where);
        } elseif (
            !isset($where['where_type']) &&
            !isset($where['group_query']) &&
            isset($where[0])
        ) {
            if (!is_array($where[0])) {
                $model = $model->where([$where]);
            } else {
                foreach ($where as $where_single) {
                    $model = $this->_subWhere($model, $where_single);
                }
            }
        } elseif (
            isset($where['where_type']) &&
            in_array($where['where_type'], ['whereBetween', 'whereNotBetween', 'whereIn', 'whereNotIn'])
        ) {
            $model = $model->{$where['where_type']}($where[0][0], $where[0][1]);
        } elseif (
            !isset($where['group_query']) ||
            (isset($where['group_query']) && $where['group_query'] !== true)
        ) {
            $wheretype = 'where';
            if ($where['where_type'] == 'orWhere') {
                $wheretype = 'orWhere';
            }
            if (isset($where[0][0])) {
                if (is_array($where[0][0])) {
                    $model = $model->{$wheretype}($where[0]);
                } else {
                    $model = $model->{$wheretype}([$where[0]]);
                }
            } else {
                $model = $model->{$wheretype}($where[0]);
            }
        } elseif (isset($where['group_query']) && $where['group_query'] === true) {
            $wheretype = 'where';
            if ($where['where_type'] == 'orWhere') {
                $wheretype = 'orWhere';
            }
            $model = $model->{$wheretype}(function ($query) use ($where) {
                foreach ($where[0] as $sub_key => $sub_val) {
                    $query = $this->_subWhere($query, $sub_val);
                }
            });
        }
        return $model;
    }

    private function _getUpdatableFields($values)
    {
        $fields = [];
        $conditions = [];
        foreach ($values as $value) {
            if (count(array_intersect_key($value['fields'], $value['conditions'])) >= 2) {
                return false;
            }
            $fields = array_merge($fields, $value['fields']);
            $conditions = array_merge($conditions, $value['conditions']);
        }
        $fields = array_keys($fields);
        $conditions = array_keys($conditions);
        $commonFields = array_intersect($fields, $conditions);
        if (count($commonFields) >= 1) {
            $fields = array_merge(array_diff($fields, $commonFields), $commonFields);
        }
        return $fields;
    }

    public function extractToArray($relations)
    {

        if (is_string($relations)) {
            return explode(',', $relations);
        }

        return is_array($relations) ? $relations : [];

    }
}