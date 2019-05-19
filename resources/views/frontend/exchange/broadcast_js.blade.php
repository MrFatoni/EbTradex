<script>

    function broadcast() {
        let channelPrefix = '{{ channel_prefix() }}';
        Echo.channel(channelPrefix + 'orders.' + defaultStockPairId).listen('Exchange.BroadcastOrder', (data) => {
            processOrderTable(data);
        }).listen('Exchange.BroadcastCancelOrder', (data) => {
            processOrderTable(data);
        });

        if (user) {
            Echo.private(channelPrefix + 'orders.' + defaultStockPairId + '.' + user.id).listen('Exchange.BroadcastPrivateOrder', (data) => {
                updateMyOpenOrderTable(data);
                updateOrderFormOnOrderPlace(data);
            }).listen('Exchange.BroadcastPrivateCancelOrder', (data) => {
                updateMyOpenOrderTable(data);
                updateOrderFormOnCancel(data);
            });
        }


        Echo.channel(channelPrefix + 'exchange.' + defaultStockPairId).listen('Exchange.BroadcastStockExchange', (data) => {

            $.each(data.exchangedOrders[exchangeTypeBuy], function (_, buy) {
                if (buy) {
                    updateOrdersBook(buyOrderTable, buy);
                    updateOrderBookTotal($('#total_buy_order_in_base'), buy.total);
                    if (buy.is_maker) {
                        buy.amount = bcmul(buy.amount, '-1');
                        buy.total = bcmul(buy.total, '-1');
                        updateTradeHistory(tradeHistoryTable, buy);
                    }
                }
            });

            $.each(data.exchangedOrders[exchangeTypeSell], function (_, sell) {
                if (sell) {
                    updateOrdersBook(sellOrderTable, sell);
                    updateOrderBookTotal($('#total_sell_order_in_item'), sell.amount);
                    if (sell.is_maker) {
                        sell.amount = bcmul(sell.amount, '-1');
                        sell.total = bcmul(sell.total, '-1');
                        updateTradeHistory(tradeHistoryTable, sell);
                    }
                }

            });

            updateChart(data.chartData);
            updateStockPairSummary(data.stockPairSummary)
        }).listen('Exchange.BroadcastSettlementOrders', (data) => {
            let table = sellOrderTable;
            let removeAmount = data.amount;

            if (data.exchange_type == exchangeTypeBuy) {
                table = buyOrderTable;
                removeAmount = data.total;
            }
            updateOrdersBook(table, data);
            updateOrderBookTotal($('#total_sell_order_in_item'), removeAmount);
        });

        Echo.channel(channelPrefix + 'exchange').listen('Exchange.BroadcastStockPairSummary', (data) => {
            updateStockMarketTable(data);
        });

        if (user) {
            Echo.private(channelPrefix + 'exchange.' + defaultStockPairId + '.' + user.id).listen('Exchange.BroadcastPrivateStockExchange', (data) => {
                $.each(data, function (_, order) {
                    if (order) {
                        updateMyOpenOrderTable(order);
                        updateMyTradeHisty(order);
                        updateOrderFormOnExchange(order);
                    }
                });
            }).listen('Exchange.BroadcastPrivateSettlementOrder', (data) => {
                $.each(data, function (_, order) {
                    if (order) {
                        updateMyOpenOrderTable(order);
                    }
                });
            });


        }
    }
</script>