<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Requests\User\Admin\StockItemRequest;
use App\Repositories\User\Admin\Interfaces\StockItemInterface;
use App\Http\Controllers\Controller;
use App\Repositories\User\Admin\Interfaces\StockPairInterface;
use App\Repositories\User\Trader\Interfaces\WalletInterface;
use App\Services\Core\DataListService;
use App\Services\Core\FileUploadService;
use Illuminate\Support\Facades\DB;

class StockItemController extends Controller
{
    public $stockItem;

    /**
     * StockItemController constructor.
     * @param StockItemInterface $stockItem
     */
    public function __construct(StockItemInterface $stockItem)
    {
        $this->stockItem = $stockItem;
    }

    /**
     * @developer: M.G. Rabbi
     * @date: 2018-10-16 6:46 PM
     * @description:
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $searchFields = [
            ['item', __('Stock Item')],
            ['item_name', __('Stock Item Name')],
            ['item_type', __('Stock Item Type')],
            ['is_active', __('Active Status')],
        ];

        $orderFields = [
            ['item', __('Stock Item')],
            ['item_name', __('Stock Item Name')],
            ['item_type', __('Stock Item Type')],
            ['stock_items.created_at', __('Created Date')],
        ];

        $query = $this->stockItem->paginateWithFilters($searchFields, $orderFields);
        $data['list'] = app(DataListService::class)->dataList($query, $searchFields, $orderFields);
        $data['title'] = __('Stock Items');

        return view('backend.stockItems.index', $data);
    }

    /**
     * @developer: M.G. Rabbi
     * @date: 2018-10-16 6:46 PM
     * @description:
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $data['title'] = __('Create Stock Item');

        return view('backend.stockItems.create', $data);
    }

    /**
     * @developer: M.G. Rabbi
     * @date: 2018-10-15 5:20 PM
     * @description:
     * @param StockItemRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StockItemRequest $request)
    {
        $attributes = $this->_filterFields($request);

        if ($itemEmoji = $this->_uploadItemEmoji($request)) {
            $attributes['item_emoji'] = $itemEmoji;
        }

        if ($created = $this->stockItem->create($attributes)) {
            return redirect()->route('admin.stock-items.show', $created->id)->with(SERVICE_RESPONSE_SUCCESS, __('The stock item has been created successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to create stock item.'));
    }

    /**
     * @developer: M.G. Rabbi
     * @date: 2018-10-16 12:29 PM
     * @description:
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $data['title'] = __('Stock Item');
        $data['stockItem'] = $this->stockItem->findOrFailById($id);

        return view('backend.stockItems.show', $data);
    }

    /**
     * @developer: M.G. Rabbi
     * @date: 2018-10-16 6:46 PM
     * @description:
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $data['title'] = __('Edit Stock Item');
        $data['stockItem'] = $this->stockItem->findOrFailById($id);

        return view('backend.stockItems.edit', $data);
    }

    /**
     * @developer: M.G. Rabbi
     * @date: 2018-10-16 6:46 PM
     * @description:
     * @param StockItemRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(StockItemRequest $request, $id)
    {
        $attributes = $this->_filterFields($request);

        if ($itemEmoji = $this->_uploadItemEmoji($request)) {
            $attributes['item_emoji'] = $itemEmoji;
        }

        $stockItem = $this->stockItem->getFirstById($id);

        if ( !empty($stockItem) && $this->stockItem->update($attributes, $id) )
        {
            if( $request->item_type == CURRENCY_CRYPTO && $request->api_service != $stockItem->api_service )
            {
                app(WalletInterface::class)->updateAllByConditions(['address' => null], ['stock_item_id' => $id, ['address', '!=', null]]);
            }

            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The stock item has been updated successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to update.'));
    }

    /**
     * @developer: M.G. Rabbi
     * @date: 2018-10-17 12:24 AM
     * @description:
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            if ($this->stockItem->deleteById($id)) {
                return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The stock item has been deleted successfully.'));
            }

            return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to delete.'));
        } catch (\Exception $exception) {
            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to delete as the stock item is being used.'));
        }
    }

    /**
     * @developer: M.G. Rabbi
     * @date: 2018-10-16 6:45 PM
     * @description:
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleActiveStatus($id)
    {
        $stockItem = $this->stockItem->getFirstById($id, ['stockPairs','baseStockPairs']);

        if(empty($stockItem))
        {
            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Stock item could not found.'));
        }

        if($stockItem->stockPairs->where('is_default', ACTIVE_STATUS_ACTIVE)->first() || $stockItem->baseStockPairs->where('is_default', ACTIVE_STATUS_ACTIVE)->first()) {
            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __("The stock item's status can not be deactivated as it's being used in the default stock pair."));
        }

        if($updatedStockItem = $this->stockItem->toggleStatusById($id)) {
            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS,  __('The stock item has been :status successfully.', ['status' => $updatedStockItem->is_active == ACTIVE_STATUS_ACTIVE ? 'activated' : 'deactivated']));
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR,  __('Failed to change stock item status.'));
    }

    /**
     * @developer: M.G. Rabbi
     * @date: 2018-10-16 6:45 PM
     * @description:
     * @param $request
     * @return mixed
     */
    public function _filterFields($request)
    {
        $fields = ['item', 'item_name', 'item_type', 'is_active', 'is_ico'];

        if($request->is_ico == ACTIVE_STATUS_INACTIVE) {
            $fields = array_merge($fields, ['exchange_status']);

            if (in_array($request->item_type, config('commonconfig.currency_transferable')))
            {
                $conditionalFields = ['deposit_status', 'deposit_fee', 'withdrawal_status', 'withdrawal_fee', 'minimum_withdrawal_amount', 'daily_withdrawal_limit'];

                if ($request->deposit_status == ACTIVE_STATUS_ACTIVE || $request->withdrawal_status == ACTIVE_STATUS_ACTIVE) {
                    array_push($conditionalFields, 'api_service');
                }

                $fields = array_merge($fields, $conditionalFields);
            }
        }

        $attributes = $request->only($fields);

        if($request->is_ico == ACTIVE_STATUS_ACTIVE) {
            $attributes['exchange_status'] = ACTIVE_STATUS_INACTIVE;
            $attributes['deposit_status'] = ACTIVE_STATUS_INACTIVE;
            $attributes['deposit_fee'] = 0;
            $attributes['withdrawal_status'] = ACTIVE_STATUS_INACTIVE;
            $attributes['withdrawal_fee'] = 0;
            $attributes['minimum_withdrawal_amount'] = 0;
            $attributes['api_service'] = null;
        }

        return $attributes;
    }

    /**
     * @developer: M.G. Rabbi
     * @date: 2018-10-20 3:23 PM
     * @description:
     * @param StockItemRequest $request
     * @return bool
     */
    public function _uploadItemEmoji(StockItemRequest $request)
    {
        if ($request->hasFile('item_emoji')) {
            return app(FileUploadService::class)->upload($request->item_emoji, config('commonconfig.path_stock_item_emoji'), 'item_emoji', 'stock', $request->item, 'public', 100, 100);
        }

        return false;
    }
}