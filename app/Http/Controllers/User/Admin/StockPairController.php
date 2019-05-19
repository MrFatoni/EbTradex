<?php

namespace App\Http\Controllers\User\Admin;

use App\Exceptions\JobException;
use App\Http\Requests\User\Admin\StockPairRequest;
use App\Repositories\User\Admin\Interfaces\StockItemInterface;
use App\Repositories\User\Admin\Interfaces\StockPairInterface;
use App\Services\Core\DataListService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class StockPairController extends Controller
{
    public $stockPair;

    /**
     * StockPairController constructor.
     * @param StockPairInterface $stockPair
     */
    public function __construct(StockPairInterface $stockPair)
    {
        $this->stockPair = $stockPair;
    }

    /**
     * @developer: M.G. Rabbi
     * @date: 2018-10-18 3:57 PM
     * @description:
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $searchFields = [
            ['stock_items.item', __('Exchangeable Item')],
            ['stock_items.item_name', __('Exchangeable Item Name')],
            ['base_items.item', __('Base Item')],
            ['base_items.item_name', __('Base Item Name')],
            ['stock_pairs.is_active', __('Active Status')],
        ];
        $orderFields = [
            ['stock_items.item', __('Exchangeable Item')],
            ['base_items.item', __('Base Item')],
            ['stock_items.created_at', __('Created Date')],
        ];
        $joinArray = [
            // connected table, connected field, operator, this table field
            ['stock_items as base_items', 'base_items.id', '=', 'stock_pairs.base_item_id'],
            ['stock_items', 'stock_items.id', '=', 'stock_pairs.stock_item_id'],
        ];
        $select = [
            'stock_pairs.id as id',
            'base_item_id',
            'stock_item_id',
            'base_items.item as base_stock_item',
            'base_items.item_name as base_stock_name',
            'stock_items.item as stock_item',
            'stock_items.item_name as stock_name',
            'last_price',
            'stock_pairs.is_active',
            'is_default',
            'stock_pairs.created_at'
        ];
        $query = $this->stockPair->paginateWithFilters($searchFields, $orderFields, null, $select, $joinArray);
        $data['list'] = app(DataListService::class)->dataList($query, $searchFields, $orderFields);
        $data['title'] = __('Stock Pairs');

        return view('backend.stockPairs.index', $data);
    }

    /**
     * @developer: M.G. Rabbi
     * @date: 2018-10-18 5:45 PM
     * @description:
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $data['stockItems'] = app(StockItemInterface::class)->getActiveList()->pluck('item', 'id')->toArray();
        $data['title'] = __('Create Stock Pair');

        return view('backend.stockPairs.create', $data);
    }

    /**
     * @developer: M.G. Rabbi
     * @date: 2018-10-18 5:45 PM
     * @description:
     * @param StockPairRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StockPairRequest $request)
    {
        try {
            DB::beginTransaction();
            $attributes = $request->only('stock_item_id', 'base_item_id', 'is_active', 'last_price', 'is_default');

            if($request->is_default == ACTIVE_STATUS_ACTIVE) {
                $removePreviousDefaultStockPair = $this->stockPair->updateRows(['is_default' => ACTIVE_STATUS_INACTIVE], ['is_default' => ACTIVE_STATUS_ACTIVE]);

                if(!$removePreviousDefaultStockPair) {
                    DB::rollBack();

                    return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to create stock pair.'));
                }
            }

            $created = $this->stockPair->create($attributes);

            DB::commit();

            return redirect()->route('admin.stock-pairs.show', $created->id)->with(SERVICE_RESPONSE_SUCCESS, __('The stock pair has been created successfully.'));
        }
        catch (\Exception $exception) {
            DB::rollBack();
            if($exception->getCode() == 23000) {
                return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('The stock pair already exists.'));
            }

            return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to create stock pair.'));
        }
    }

    /**
     * @developer: M.G. Rabbi
     * @date: 2018-10-29 12:45 PM
     * @description:
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $data['title'] = __('Stock Pair');
        $data['stockPair'] = $this->stockPair->getFirstStockPairDetailByConditions(['stock_pairs.id' => $id]);

        return view('backend.stockPairs.show', $data);
    }

    /**
     * @developer: M.G. Rabbi
     * @date: 2018-10-20 11:42 PM
     * @description:
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $data['stockItems'] = app(StockItemInterface::class)->getActiveList()->pluck('item', 'id')->toArray();
        $data['title'] = __('Edit Stock Pair');
        $data['stockPair'] = $this->stockPair->findOrFailById($id);

        return view('backend.stockPairs.edit', $data);
    }

    /**
     * @developer: M.G. Rabbi
     * @date: 2018-10-20 11:36 PM
     * @description:
     * @param StockPairRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(StockPairRequest $request, $id)
    {
        $attributes = $request->only('stock_item_id', 'base_item_id', 'last_price');

        if ($this->stockPair->update($attributes, $id)) {
            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The stock pair has been updated successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to update.'));
    }

    /**
     * @developer: M.G. Rabbi
     * @date: 2018-10-20 11:20 PM
     * @description:
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            if ($this->stockPair->deleteByConditions(['id' => $id, 'is_default' => ACTIVE_STATUS_INACTIVE])) {
                return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The stock pair has been deleted successfully.'));
            }

            return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to delete.'));
        } catch (\Exception $exception) {
            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to delete as the stock pair is being used.'));
        }
    }

    /**
     * @developer: M.G. Rabbi
     * @date: 2018-10-20 11:20 PM
     * @description:
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleActiveStatus($id)
    {
        $response = [SERVICE_RESPONSE_ERROR => __('Failed to change status.')];

        if ($updatedInstance = $this->stockPair->toggleStatusByConditions(['id' => $id, 'is_default' => ACTIVE_STATUS_INACTIVE])) {
            $message = $updatedInstance->is_active == ACTIVE_STATUS_ACTIVE ? __('The stock pair has been activated successfully.') : __('The stock pair has been deactivated successfully.');
            $response = [SERVICE_RESPONSE_SUCCESS => $message];
        }

        return redirect()->back()->with($response);
    }

    /**
     * @developer: M.G. Rabbi
     * @date: 2018-10-29 1:20 PM
     * @description:
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function makeStatusDefault($id)
    {
        DB::beginTransaction();

        try{

            $this->stockPair->updateRows(['is_default' => ACTIVE_STATUS_INACTIVE], ['is_default' => ACTIVE_STATUS_ACTIVE]);

            $updateDefaultStockPair = $this->stockPair->updateByConditions(['is_default' => ACTIVE_STATUS_ACTIVE], ['is_active' => ACTIVE_STATUS_ACTIVE, 'is_default' => ACTIVE_STATUS_INACTIVE, 'id' => $id]);

            if (!$updateDefaultStockPair) {
                throw new JobException(__('Failed to make default.'));
            }

        }catch (\Exception $exception){
            DB::rollBack();
            logs()->error("Make default stock pair: ".$exception->getMessage());
            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to make default.'));
        }

        DB::commit();

        return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The stock pair has been made default successfully.'));
    }
}