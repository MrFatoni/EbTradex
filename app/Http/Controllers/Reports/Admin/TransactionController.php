<?php

namespace App\Http\Controllers\Reports\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\User\Admin\Interfaces\TransactionInterface;
use App\Services\Core\DataListService;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function user($userId, $journalType = null)
    {

        $data = $this->generateTransaction($userId, $journalType);
        $data['title'] = __('Transaction');
        $data['journalType'] = $journalType;
        $data['userId'] = $userId;

        return view('backend.transactions.all_users', $data);
    }

    private function generateTransaction($userId = null, $journalType = null)
    {
        $searchFields = [
            ['first_name', __('First Name')],
            ['last_name', __('Last Name')],
            ['email', __('Email')],
            ['item', __('Stock Item')],
        ];


        $orderFields = [
            ['amount', __('Amount')],
            ['transactions.created_at', __('Date')],
        ];


        $where = null;

        if (!is_null($userId)) {
            $where['transactions.user_id'] = $userId;
        }

        if (!is_null($journalType)) {
            $where['journal'] = config('commonconfig.journal_type.' . $journalType);
        }

        $select = ['transactions.*', 'first_name', 'last_name', 'email', 'item'];
        $joinArray = [
            ['stock_items', 'stock_items.id', '=', 'transactions.stock_item_id'],
            ['users', 'users.id', '=', 'transactions.user_id'],
            ['user_infos', 'users.id', '=', 'user_infos.user_id'],
        ];

        $query = app(TransactionInterface::class)->paginateWithFilters($searchFields, $orderFields, $where, $select, $joinArray);
        $select = ['stock_items.item', 'journal', DB::raw('sum(amount) as amount')];
        $data['summary'] = app(TransactionInterface::class)->filters($searchFields, $orderFields, $where, $select, $joinArray, ['stock_items.item', 'journal']);
        $data['list'] = app(DataListService::class)->dataList($query, $searchFields, $orderFields);
        return $data;
    }

    public function allUser($journalType = null)
    {

        $data= $this->generateTransaction(null, $journalType);
        $data['title'] = __('Transaction');
        $data['journalType'] = $journalType;
//        dd($data['summary']->groupBy(['item','transaction_type']));
        return view('backend.transactions.all_users', $data);
    }
}
