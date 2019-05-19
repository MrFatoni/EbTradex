@foreach($stockMarkets['stockItems'] as $baseItem => $stockItemGroup)
    <table class="market-table table datatable dt-responsive display nowrap">
        <thead>
        <tr>
            <th><i class="fa fa-star"></i></th>
            <th>{{ __('STOCK') }}</th>
            <th>{{ __('PRICE') }}</th>
            <th>{{ __('VOLUME') }}</th>
            <th>{{ __('CHANGE') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($stockItemGroup as $stockItem)
            <tr>
                <td><i class="fa fa-star"></i></td>
                <td>{{ $stockItem['stockItem'] }}</td>
                <td>{{ $stockItem['lastPrice'] }}</td>
                <td>{{ $stockItem['baseVolume'] }}</td>
                <td>{{ $stockItem['change24hrInPercent'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @endforeach
    </div>