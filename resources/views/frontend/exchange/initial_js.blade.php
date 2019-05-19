<script>

    let defaultBaseId = parseInt({{ $stockPair->base_item_id }});
    let defaultStockId = parseInt({{ $stockPair->stock_item_id }});
    let defaultStockPairId = parseInt({{ $stockPair->id }});
    let defaultInterval = parseInt({{ $chartInterval }});
    let defaultZoom = parseInt({{ $chartZoom }});
    let stockMarketTable = '';
    let buyOrderTable = '';
    let sellOrderTable = '';
    let myOpenOrderTable = '';
    let myTradeHistoryTable = '';
    let tradeHistoryTable = '';
    let exchangeTypeBuy = '{{ EXCHANGE_BUY }}';
    let exchangeTypeSell = '{{ EXCHANGE_SELL }}';
    let exchangeCategory = '{{ CATEGORY_EXCHANGE }}';
    let exchangeType = JSON.parse('{!! json_encode(exchange_type()) !!}');
    let user = JSON.parse('{!! json_encode(Auth::user()) !!}');
    let orderCancelURL = '{{ route('trader.orders.destroy', '##') }}';
    let chartData;
    let sellOrderLastPrice = 0;
    let buyOrderLastPrice = 0;
    let orderBookRowPerPage = 50;
    let sellOrderTableLoadMore = true;
    let buyOrderTableLoadMore = true;

    bcscale(8);

    $(document).ready(function () {


        $('#buy_order_form').cValidate({
            showErrorsInFlash: true,
            preventSubmit: true
        });

        $('#sell_order_form').cValidate({
            showErrorsInFlash: true,
            preventSubmit: true
        });

        $('#stop_limit_form').cValidate({
            showErrorsInFlash: true,
            preventSubmit: true
        });


        stockMarketTable = $('#stock_market_table').DataTable({
            destroy: true,
            paging: false,
            order: [[0, 'asc']],
            dom: '<"filter">ft',
            select: {
                style: 'single',
                selector: 'tr:not(.selected)'
            },
            scrollY: 500,
            scrollCollapse: true,
            language: {search: "", searchPlaceholder: "{{ __('Search...') }}"},
            ajax: {
                url: '{{ route('exchange.get-stock-market') }}',
                dataSrc: function (json) {

                    if (json.baseItems) {
                        let html = '<select id="datatable-filter" class="form-control">';

                        $.each(json.baseItems, function (baseId, baseItem) {
                            let selected = defaultBaseId == baseId ? "selected" : "";
                            html += '<option ' + selected + ' value="' + baseId + '">' + baseItem + '</option>';
                        });
                        html += '</select>';

                        $("div.filter").html(html);
                    }
                    return json.stockItems;
                }
            },
            initComplete: function () {
                stockMarketTable.column(4).search(defaultBaseId).draw();
                let selectedRowData = stockMarketTable.row({selected: true}).data();
                updateStockPairSummary(selectedRowData);
                @auth
                initOrderForm();
                initMyOpenOrderTable();
                initMyTradeHistoryTable();
                @endauth
                initStockChart(defaultStockPairId, defaultInterval);
                initBuyStockOrderTable();
                initSellStockOrderTable();
                initTradeHistoryTable();
                broadcast();
            },
            rowCallback: function (row, data) {
                if (data.id == defaultStockPairId) {
                    stockMarketTable.row(row).select();
                    changeUrl(data);
                }

                $(row).attr('id', 'stock_market' + data.id);
            },
            columns: [
                {
                    data: "stock_item_abbr",
                },
                {
                    data: "last_price",
                },
                {
                    data: "exchanged_base_item_volume_24",
                    render: function (data) {
                        return number_format(data, 3)
                    }
                },
                {
                    data: "change_24",
                    render: function (data) {
                        let change = '';
                        if (parseFloat(data) > 0) {
                            change = '<i class="fa fa-caret-up text-green"></i> ';
                        } else if (parseFloat(data) < 0) {
                            change = '<i class="fa fa-caret-down text-red"></i> ';
                        } else {
                            change = '<i class="fa fa-sort text-gray"></i> ';
                        }
                        return change + number_format(Math.abs(data), 2);
                    }
                },
                {
                    data: "base_item_id",
                    visible: false
                }
            ]
        });

        $(document).on('change', '#datatable-filter', function () {
            stockMarketTable.column(4).search(this.value).draw();
        });

        stockMarketTable.on('user-select', function (e, dt, type, cell, originalEvent) {
            if ( type === 'row') {
                let row = dt.row(originalEvent.currentTarget);
                let data = row.data();

                defaultStockPairId = data.id;
                defaultStockId = data.stock_item_id;
                defaultBaseId = data.base_item_id;
                changeUrl(data);
                updateStockPairSummary(data);
                initOrderForm();
                initStockChart(defaultStockPairId, defaultInterval);
                initBuyStockOrderTable();
                initSellStockOrderTable();
                initMyOpenOrderTable();
                initMyTradeHistoryTable();
                initTradeHistoryTable();
                broadcast();
            }
        });

        $(document).on('click', '#buy_order_table tbody:first tr', function () {
            let data = buyOrderTable.row(this).data();
            fillUpSellOrderForm(data);
        });

        $(document).on('click', '#sell_order_table tbody:first tr', function () {
            let data = sellOrderTable.row(this).data();
            fillUpBuyOrderForm(data);
        });

        $(document).on('click', '.base_item_balance, .lowest_ask, .stock_item_balance, .highest_bid', function (event) {
            let selector = '#' + $(this).closest('.box').find('form').attr('id');
            let value = $(this).text();

            if ($(this).hasClass('base_item_balance')) {
                $(selector + ' .total').val(value);
                event.target.classList.add('total');
            } else if ($(this).hasClass('stock_item_balance')) {
                $(selector + ' .amount').val(value);
                event.target.classList.add('amount');
            } else {
                $(selector + ' .price').val(value);
                event.target.classList.add('price');
            }

            calculateOrderForm(event, value, selector);
        });

        $(document).on('keyup', '.price, .amount, .total', function (event) {
            let selector = '#' + $(this).closest('form').attr('id');
            calculateOrderForm(event, $(this).val(), selector);
        });

        $(document).on('click', '.delete_order', function (event) {
            event.preventDefault();
            let token = $('meta[name="csrf-token"]').attr('content');
            let $this = $(this);
            let url = $this.attr('href');
            $this.closest('td').html('<span class="text-red">{{ __('Cancelling') }}</span>');

            $.ajax({
                type: 'POST',
                url: url,
                data: {_token: token,_method : 'DELETE'},
                success: function () {

                }
            });
        });

        $(document).on('click', '#candlestick li a', function (event) {
            defaultInterval = $(this).data('interval') || 240;
            $('#candlestick li').removeClass('disabled');
            $(this).closest('li').addClass('disabled');
            initStockChart(defaultStockPairId, defaultInterval);
            updateChartZoom();
        });

        $(document).on('click', '#candlestick_zoom li a', function (event) {
            $('#candlestick_zoom li').removeClass('disabled');
            $(this).closest('li').addClass('disabled');
            defaultZoom = parseInt($(this).data('zoom'));
            updateChartZoom();
        });

        // Stock Market Toggling
        $('#fixed-stock-market-toggler').on('click', function () {
            var $this = $(this);
            if ($this.hasClass('opened')) {
                $this.removeClass('opened')
                $('#fixed-stock-market').removeClass('opened')
            } else {
                $this.addClass('opened')
                $('#fixed-stock-market').addClass('opened')
            }
        });

        $(document).on('click', function (e) {
            if ($(e.target).closest("#fixed-stock-market").length === 0) {
                $('#fixed-stock-market-toggler').removeClass('opened');
                $('#fixed-stock-market').removeClass('opened');
            }

        });
    });
</script>
