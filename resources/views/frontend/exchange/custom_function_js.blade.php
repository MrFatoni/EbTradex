<script>
    function updateChartZoom() {
        let zoom = calculateZoom();

        eChart.setOption({
            dataZoom: [
                {
                    start: zoom
                },
                {
                    start: zoom
                }
            ]
        });
    }

    function updateStockMarketTable(data) {
        let row = stockMarketTable.row('#stock_market' + data.stock_pair_id);
        let rowData = row.data();

        if (rowData) {
            let status = 'updated';

            if (rowData.last_price > data.last_price) {
                status = 'deleted';
            } else if (rowData.last_price < data.last_price) {
                status = 'inserted'
            }

            rowData.last_price = data.last_price;
            rowData.exchanged_base_item_volume_24 = data.exchanged_base_item_volume_24;
            rowData.change_24 = number_format(data.change_24, 2);
            row.data(rowData).invalidate();
            rowHighlight(row.node(), status);
            stockMarketTable.draw();
        }
    }

    function processOrderTable(data) {
        let selector, table, total;

        if (data.exchange_type == exchangeTypeBuy) {
            table = buyOrderTable;
            selector = $('#total_buy_order_in_base');
            total = data.order.total;
        } else {
            table = sellOrderTable;
            selector = $('#total_sell_order_in_item');
            total = data.order.amount;
        }

        updateOrdersBook(table, data.order);
        updateOrderBookTotal(selector, total);
    }

    function updateMyOpenOrderTable(data) {
        let row = myOpenOrderTable.row("#" + data.order_number);
        let rowData = row.data();

        if (rowData) {
            rowData.amount = bcsub(rowData.amount, data.amount);
            if (rowData.amount > 0) {
                row.data(rowData).invalidate();
                rowHighlight(row.node(), 'updated');
            } else {
                myOpenOrderTable.rows(row).remove();
            }
        } else {
            let row = myOpenOrderTable.row.add(data).node();
            rowHighlight(row, 'inserted');
        }

        myOpenOrderTable.draw();
    }

    function calculateOrderForm(event, value, selector) {
        if (event.target.classList.contains('price')) {

            let amount = $(selector + ' .amount').val() || '0';
            let total = bcmul(amount, value);
            total = isNaN(total) ? 0 : total;
            $(selector + ' .total').val(total);

        } else if (event.target.classList.contains('amount')) {

            let price = $(selector + ' .price').val() || '0';
            let total = bcmul(price, value);
            total = isNaN(total) ? 0 : total;
            $(selector + ' .total').val(total);
        } else {
            let price = $(selector + ' .price').val() || '0';
            let amount = bcdiv(value, price);

            amount = isNaN(amount) ? 0 : amount;

            $(selector + ' .amount').val(amount);
        }
    }

    function initOrderForm() {

        $('.stock-pair').val(defaultStockPairId);

        $.ajax({
            type: 'GET',
            url: '{{ route('exchange.get-wallet-summary') }}',
            data: {stock_pair_id: defaultStockPairId},
            success: function (data) {
                $('.base_item_balance').text(number_format(data.base_item_balance));
                $('.stock_item_balance').text(number_format(data.stock_item_balance));

                $('#buy_order_form .amount').val('');
                $('#buy_order_form .total').val('');

                $('#sell_order_form .amount').val('');
                $('#sell_order_form .total').val('');
            }
        });
    }

    function fillUpSellOrderForm(data) {
        $('#sell_order_form .price').val(data.price);
        $('#sell_order_form .amount').val(data.total_stock_item);
        $('#sell_order_form .total').val(data.total_base_item);

        $('#buy_order_form .price').val(data.price);
        $('#buy_order_form .amount').val('');
        $('#buy_order_form .total').val('');
    }

    function fillUpBuyOrderForm(data) {
        $('#buy_order_form .price').val(data.price);
        $('#buy_order_form .amount').val(data.total_stock_item);
        $('#buy_order_form .total').val(data.total_base_item);

        $('#sell_order_form .price').val(data.price);
        $('#sell_order_form .amount').val('');
        $('#sell_order_form .total').val('');


    }


    function updateChart(data) {
        let lastChartData = chartData.slice(-1)[0];
        let intervalDate = data.interval[defaultInterval];

        if (lastChartData[0] == intervalDate) {
            lastChartData[2] = data.price;

            if (lastChartData[1] == 0) {
                lastChartData[1] = data.price;
            }

            if (lastChartData[3] > data.price || lastChartData[3] == 0) {
                lastChartData[3] = data.price;
            }

            if (lastChartData[4] < data.price) {
                lastChartData[4] = data.price;
            }
        } else {
            chartData.push([
                data.date,
                data.price,
                data.price,
                data.price,
                data.price,
            ])
        }

        makeChart(document.getElementById('echart'), chartData)

    }


    function updateTradeHistory(table, data) {
        let row = table.row.add(data).node();
        rowHighlight(row, 'inserted');
        table.draw();
    }

    function updateMyTradeHisty(data) {
        myTradeHistoryTable.row.add(data);
        myTradeHistoryTable.draw();
    }

    function updateOrdersBook(table, data) {
        let row = table.row("#" + hash(data.price));
        let rowData = row.data();

        if (rowData) {
            rowData.amount = bcadd(rowData.amount, data.amount);
            rowData.total = bcadd(rowData.total, data.total);
            if (rowData.amount > 0) {
                row.data(rowData).invalidate();
                rowHighlight(row.node(), 'updated');
            } else {
                table.rows(row).remove();
            }

            table.draw();

        } else {
            row = table.row.add(data).draw().node();
            rowHighlight(row, 'inserted');
        }
    }

    function updateOrderBookTotal(selector, total) {
        let grandTotal = bcadd(selector.text(), total);
        selector.text(grandTotal);
    }


    function updateStockPairSummary(data) {
        $('#last_price').text(number_format(data.last_price));
        let change = '';
        if (parseFloat(data.change_24) > 0) {
            change = '<i class="fa fa-caret-up text-green"></i> ';
        } else if (parseFloat(data.change_24) < 0) {
            change = '<i class="fa fa-caret-down text-red"></i> ';
        } else {
            change = '<i class="fa fa-sort text-gray"></i> ';
        }

        $('#_24_hour_percentage').html(change + ' ' + number_format(Math.abs(data.change_24), 2) + '%');
        $('#_24_hour_high').text(number_format(data.high_24));
        $('#_24_hour_low').text(number_format(data.low_24));

        $('#_24_hour_base_volume').text(number_format(data.exchanged_base_item_volume_24, 3));
        $('#_24_hour_item_volume').text(number_format(data.exchanged_stock_item_volume_24, 3));

        if (data.base_item_abbr) {
            $('.base_item').text(data.base_item_abbr);
        }

        if (data.stock_item_abbr) {
            $('.stock_item').text(data.stock_item_abbr);
        }

    }

    function number_format(number, decimalPoint = 8) {
        number = parseFloat(number);
        return number.toFixed(decimalPoint);
    }

    function initStockChart(stockPairId, interval) {
        $.ajax({
            type: 'GET',
            url: '{{ route('exchange.get-chart-data') }}',
            data: {stock_pair_id: stockPairId, interval: interval},
            success: function (data) {
                chartData = data;
                makeChart(document.getElementById('echart'), chartData);
                $(window).resize(function () {
                    eChart.resize();
                })
            }
        });
    }

    function hash(number) {
        number = number_format(number);
        let res = number.replace(/[0-9]|\./gi, function myFunction(x) {
            if (x == 0) {
                return 'z';
            } else if (x == 1) {
                return 'o';
            } else if (x == 2) {
                return 't';
            } else if (x == 3) {
                return 'r';
            } else if (x == 4) {
                return 'f';
            } else if (x == 5) {
                return 'i';
            } else if (x == 6) {
                return 's';
            } else if (x == 7) {
                return 'e';
            } else if (x == 8) {
                return 'g';
            } else if (x == 9) {
                return 'n';
            } else {
                return 'x';
            }


        });

        return res;
    }

    function initBuyStockOrderTable() {
        buyOrderTable = $('#buy_order_table').DataTable({
            destroy: true,
            order: [[0, 'desc']],
            paging: false,
            searching: false,
            info: false,
            scrollY: 385,
            ajax: {
                url: '{{ route('exchange.get-orders') }}',
                data: {
                    stock_pair_id: defaultStockPairId,
                    last_price: null,
                    exchange_type: exchangeTypeBuy,
                    exchange_category: exchangeCategory
                },
                dataSrc: function (data) {
                    let baseItemTotal = data.totalStockOrder.base_total || 0;
                    $('#total_buy_order_in_base').text(number_format(baseItemTotal));
                    return data.stockOrders;
                }
            },
            language: {
                sLoadingRecords: '<span style="width:100%;"><img src="{{ asset('common/images/loader.svg')}}"></span>'
            },
            drawCallback: function () {
                let api = this.api();

                let totalBaseItem = '0';
                let totalStockItem = '0';
                let heightBid = 0;
                let rowCount = 0;
                api.rows().every(function (rowId) {
                    let data = this.data();

                    if (rowId == 0) {
                        heightBid = data.price;

                    }

                    totalStockItem = bcadd(totalStockItem, data.amount);
                    totalBaseItem = bcmul(totalStockItem, data.price);

                    data.total_stock_item = number_format(totalStockItem);
                    data.total_base_item = number_format(totalBaseItem);

                    this.data(data);

                    rowCount++
                });

                $('.highest_bid').text(number_format(heightBid));
                $('#sell_order_form .price').val(number_format(heightBid));

                //Load more option
                if (rowCount > orderBookRowPerPage) {
                    if ($('#buy_order_load_more').length == 0 && buyOrderTableLoadMore) {
                        let tbody = '<tbody id="buy_order_load_more">' +
                            '<tr style="background-color: #a3ddd7">' +
                            '<td colspan="4"><a onClick="loadMoreData(' + exchangeTypeBuy + ')" style="color:#08534c;float:right" href="javascript:;">{{ __('Load :loadCount more',['loadCount' => 50]) }}</a></td>' +
                            '</tr>' +
                            '</tbody>';
                        $('#buy_order_table').append(tbody);
                    } else {
                        $('#buy_order_load_more').remove();
                    }
                } else {
                    if ($('#buy_order_load_more').length == 1) {
                        $('#buy_order_load_more').remove();
                    }
                }
            },
            createdRow: function (row, data) {
                $(row).attr('id', hash(data.price));
            },
            columns: [
                {
                    data: 'price',
                    orderable: false,

                },
                {
                    data: "amount",
                    orderable: false,
                },
                {
                    data: 'total',
                    orderable: false,
                },
                {
                    data: 'total_base_item',
                    orderable: false,
                    className: 'hide_in_mobile_small',
                    render: function (data) {
                        return number_format(data);
                    }
                }
            ]
        });

    }

    function initSellStockOrderTable() {
        sellOrderTable = $('#sell_order_table').DataTable({
            destroy: true,
            paging: false,
            order: [[0, 'asc']],
            searching: false,
            info: false,
            scrollY: 385,
            ajax: {
                url: '{{ route('exchange.get-orders') }}',
                data: {
                    stock_pair_id: defaultStockPairId,
                    last_price: null,
                    exchange_type: exchangeTypeSell,
                    exchange_category: exchangeCategory
                },
                dataSrc: function (data) {
                    let stockItemTotal = data.totalStockOrder.item_total || 0;
                    $('#total_sell_order_in_item').text(number_format(stockItemTotal));
                    return data.stockOrders;
                }
            },
            language: {
                sLoadingRecords: '<span style="width:100%;"><img src="{{ asset('common/images/loader.svg')}}"></span>'
            },
            drawCallback: function () {
                let api = this.api();

                let totalStockItem = '0';
                let totalBaseItem = '0';
                let lowestAsk = 0;
                let rowCount = 0;
                api.rows().every(function (rowId) {
                    let data = this.data();

                    if (rowId == 0) {
                        lowestAsk = data.price;
                    }

                    totalStockItem = bcadd(totalStockItem, data.amount);
                    totalBaseItem = bcmul(totalStockItem, data.price);

                    data.total_stock_item = number_format(totalStockItem);
                    data.total_base_item = number_format(totalBaseItem);
                    this.data(data);
                    rowCount++;
                    sellOrderLastPrice = data.price;
                });

                $('.lowest_ask').text(number_format(lowestAsk));
                $('#buy_order_form .price').val(number_format(lowestAsk));

                //Load more option
                if (rowCount > orderBookRowPerPage) {
                    if ($('#sell_order_load_more').length == 0 && sellOrderTableLoadMore) {
                        let tbody = '<tbody id="sell_order_load_more">' +
                            '<tr style="background-color: #a3ddd7">' +
                            '<td colspan="4"><a onClick="loadMoreData(' + exchangeTypeSell + ')" style="color:#08534c;float:right" href="javascript:;">{{ __('Load :loadCount more',['loadCount' => 50]) }}</a></td>' +
                            '</tr>' +
                            '</tbody>';
                        $('#sell_order_table').append(tbody);
                    } else {
                        $('#sell_order_load_more').remove();
                    }
                } else {
                    if ($('#sell_order_load_more').length == 1) {
                        $('#sell_order_load_more').remove();
                    }
                }


            },
            createdRow: function (row, data) {
                $(row).attr('id', hash(data.price));
            },
            columns: [
                {
                    data: 'price',
                    orderable: false,

                },
                {
                    data: "amount",
                    orderable: false,
                },
                {
                    data: "total",
                    orderable: false,
                },
                {
                    data: 'total_base_item',
                    orderable: false,
                    className: 'hide_in_mobile_small',
                    render: function (data) {
                        return number_format(data);
                    }
                }
            ]
        });

    }

    function initMyOpenOrderTable() {
        myOpenOrderTable = $('#my_open_order_table').DataTable({
            destroy: true,
            paging: false,
            searching: false,
            scrollY: 300,
            scrollCollapse: true,
            order: [[4, 'desc']],
            info: false,
            ajax: {
                url: '{{ route('exchange.get-my-open-orders') }}',
                data: {
                    stock_pair_id: defaultStockPairId
                },
                dataSrc: function (data) {
                    return data;
                }
            },
            language: {
                sLoadingRecords: '<span style="width:100%;"><img src="{{ asset('common/images/loader.svg')}}"></span>'
            },
            createdRow: function (row, data) {
                $(row).attr('id', data.order_number);
            },
            columns: [
                {
                    data: null,
                    render: function (data) {
                        let html = '<span class="text-red">' + exchangeType[data.exchange_type] + '</span>';
                        if (data.exchange_type == exchangeTypeBuy) {
                            html = '<span class="text-green">' + exchangeType[data.exchange_type] + '</span>'
                        }

                        return html;
                    }
                },
                {
                    data: "price",
                },
                {
                    data: "amount"
                },
                {
                    data: null,
                    className: 'hide_in_mobile_small',
                    render: function (data) {
                        return bcmul(data.amount, data.price);
                    }
                },
                {
                    data: "date",
                    className: 'hide_in_mobile'
                },
                {
                    data: null,
                    className: 'hide_in_mobile',
                    render: function (data) {
                        return data.stop_limit ? data.stop_limit : '-';
                    }
                },
                {
                    data: null,
                    render: function (data) {
                        let url = orderCancelURL.replace('##', data.order_number);
                        return '<a class="delete_order" href="' + url + '">{{ __('Cancel') }}</a>';

                    }
                }
            ]
        });

    }

    function initMyTradeHistoryTable() {
        myTradeHistoryTable = $('#my_trade_table').DataTable({
            destroy: true,
            paging: false,
            searching: false,
            order: [[0, 'desc']],
            info: false,
            scrollY: 300,
            scrollCollapse: true,
            ajax: {
                url: '{{ route('exchange.get-my-trade') }}',
                data: {
                    stock_pair_id: defaultStockPairId
                },
                dataSrc: function (data) {
                    return data;
                }
            },
            language: {
                sLoadingRecords: '<span style="width:100%;"><img src="{{ asset('common/images/loader.svg')}}"></span>'
            },
            columns: [
                {
                    data: "date",
                    orderable: false,
                    className: 'hide_in_mobile'
                },
                {
                    data: null,
                    orderable: false,
                    render: function (data) {
                        let html = '<span class="text-red">' + exchangeType[data.exchange_type] + '</span>';
                        if (data.exchange_type == exchangeTypeBuy) {
                            html = '<span class="text-green">' + exchangeType[data.exchange_type] + '</span>'
                        }

                        return html;
                    }
                },
                {
                    data: "price",
                    orderable: false,
                    className: 'dt-body-center'
                },
                {
                    data: "amount",
                    orderable: false,
                    className: 'dt-body-center'
                },
                {
                    data: null,
                    orderable: false,
                    className: 'dt-body-center hide_in_mobile_small',
                    render: function (data) {
                        return bcmul(data.amount, data.price);
                    }
                }
            ]
        });

    }

    function initTradeHistoryTable() {
        tradeHistoryTable = $('#trade_history_table').DataTable({
            destroy: true,
            paging: false,
            searching: false,
            order: [[0, 'desc']],
            info: false,
            scrollY: 300,
            scrollCollapse: true,
            ajax: {
                url: '{{ route('exchange.get-trade-histories') }}',
                data: {
                    stock_pair_id: defaultStockPairId
                },
                dataSrc: function (data) {
                    return data;
                }
            },
            language: {
                sLoadingRecords: '<span style="width:100%;"><img src="{{ asset('common/images/loader.svg')}}"></span>'
            },
            columns: [
                {
                    data: "date",
                    orderable: false,
                    className: 'hide_in_mobile'
                },
                {
                    data: null,
                    orderable: false,
                    render: function (data) {
                        let html = '<span class="text-red">' + exchangeType[data.exchange_type] + '</span>';
                        if (data.exchange_type == exchangeTypeBuy) {
                            html = '<span class="text-green">' + exchangeType[data.exchange_type] + '</span>'
                        }

                        return html;
                    }
                },
                {
                    data: "price",
                    orderable: false,
                    className: 'dt-body-center'
                },
                {
                    data: "amount",
                    orderable: false,
                    className: 'dt-body-center'
                },
                {
                    data: null,
                    orderable: false,
                    className: 'dt-body-center hide_in_mobile_small',
                    render: function (data) {
                        return bcmul(data.amount, data.price);
                    }
                }
            ]
        });

        initScroller();
    }

    function rowHighlight(row, className, animationTime = 1000) {
        $(row).addClass(className);
        setTimeout(autoTransition, animationTime);

        function autoTransition() {
            $(row).removeClass(className);
        }
    }

    function initScroller() {
        $(".dataTables_scrollBody").mCustomScrollbar("destroy");
        $(".dataTables_scrollBody").mCustomScrollbar({
            axis: "y",
            theme: "dark-thick",
        });
    }

    function loadMoreData(exchangeType) {

        let lastPrice = sellOrderLastPrice;
        let table = sellOrderTable;
        let loadMore = sellOrderTableLoadMore;
        if (exchangeType == exchangeTypeBuy) {
            lastPrice = buyOrderLastPrice;
            table = buyOrderTable;
            loadMore = buyOrderTableLoadMore;
        }

        $.ajax({
            type: 'GET',
            url: '{{ route('exchange.get-orders') }}',
            data: {
                stock_pair_id: defaultStockPairId,
                exchange_type: exchangeType,
                exchange_category: exchangeCategory,
                last_price: lastPrice
            },
            success: function (data) {
                let length = data.stockOrders.length;
                if (length) {

                    if (length < orderBookRowPerPage) {
                        loadMore = false;
                    }

                    table.rows.add(data.stockOrders);
                    table.draw();

                }
            }
        });
    }

    function updateOrderFormOnCancel(data) {
        if (data.exchange_type == exchangeTypeBuy) {
            let baseItemBalance = $('.base_item_balance').first().text();
            baseItemBalance = bcadd(baseItemBalance, data.total);
            $('.base_item_balance').text(baseItemBalance);
        } else {
            let stockItemBalance = $('.stock_item_balance').first().text();
            stockItemBalance = bcadd(stockItemBalance, data.amount);
            $('.stock_item_balance').text(stockItemBalance);
        }
    }

    function updateOrderFormOnOrderPlace(data) {
        if (data.exchange_type == exchangeTypeBuy) {
            let baseItemBalance = $('.base_item_balance').first().text();
            baseItemBalance = bcsub(baseItemBalance, data.total);
            $('.base_item_balance').text(baseItemBalance);
        } else {
            let stockItemBalance = $('.stock_item_balance').first().text();
            stockItemBalance = bcsub(stockItemBalance, data.amount);
            $('.stock_item_balance').text(stockItemBalance);
        }
    }

    function updateOrderFormOnExchange(data) {
        if (data.exchange_type == exchangeTypeBuy) {
            let stockItemBalance = $('.stock_item_balance').first().text();
            stockItemBalance = bcadd(stockItemBalance, bcsub(data.amount, data.fee));
            $('.stock_item_balance').text(stockItemBalance);
        } else {
            let baseItemBalance = $('.base_item_balance').first().text();
            baseItemBalance = bcadd(baseItemBalance, bcsub(data.total, data.fee));
            $('.base_item_balance').text(baseItemBalance);
        }
    }

    function changeUrl(data) {
        let url = "{{ route('exchange.index') }}/" + (data.stock_item_abbr + "-" + data.base_item_abbr).toLowerCase();
        window.history.pushState({}, null, url);
    }

</script>