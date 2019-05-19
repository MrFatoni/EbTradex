<li class="individual-menu-item">
    <div class="innermenu">
        <div class="innermenuhead">
            <div class="title">
                {{$row['name'] !='' ? $row['name'] : __('Unnamed')}}
            </div>
            <div class="type"><span class="arrow-icon">
                {{$row['route'] !='' ? __('Route') : __('Link')}}
                <i class="fa fa-caret-right"></i></span>
            </div>
        </div>
        <div class="innermenubody">
            <p><label>{{ __('Navigation Label') }}<br></label><input type="text" class="name" value="{{$row['name']}}" name="menu_item[{{$row['order']}}][name]"></p>
            @if($row['route']=='')
            <p><label>{{ __('Link') }}<br></label><input type="text" class="custom-link-field prevent-default" value="{{$row['custom_link']}}" name="menu_item[{{$row['order']}}][custom_link]"></p>
            @else
                <p style="padding-top:10px"><label>{{ __('Route') }}: {{$row['route']}}</label></p>
            @endif
            <div class="row">
                <div class="col-xs-6">
                    <p><label>{{ __('Extra Class') }}<br></label><input type="text" name="menu_item[{{$row['order']}}][class]" value="{{$row['class']}}" class="prevent-default"></p>
                    </div>
                <div class="col-xs-6">
                    <p><label>{{ __('Menu Icon') }}<br></label><input type="text" name="menu_item[{{$row['order']}}][icon]" value="{{$row['icon']}}" class="prevent-default"></p>
                </div>
            </div>
            <p><label>{{ __('Beginning Text') }}<br></label><input type="text" name="menu_item[{{$row['order']}}][beginning_text]" value="{{$row['beginning_text']}}" class="prevent-default"></p>
            <p><label>{{ __('Ending Text') }}<br></label><input type="text" name="menu_item[{{$row['order']}}][ending_text]" value="{{$row['ending_text']}}" class="prevent-default"></p>
            <p><label for=""></label><input type="checkbox" class="newwindow"{{$row['new_tab']==1 ? ' checked' : ''}}><em>{{ __('Open link in a new window/tab') }}</em></p>
            <p class="mymgmenu"><label for=""></label><input type="checkbox" class="megamenu"{{$row['mega_menu'] == 1 ? ' checked' : ''}}><em>{{ __('Use As Mega Menu') }}</em></p>
            <hr class="myhrborder">
            <button class="deletebutton">{{ __('Remove') }}</button>
            @if($row['route']!='')
                <input type="hidden" value="{{$row['custom_link']}}" name="menu_item[{{$row['order']}}][custom_link]" class="custom-link-field">
            @endif
            {{--<input type="hidden" name="menu_item[{{$row['order']}}][id]" value="{{$row['parent_id']}}" class="hidden-id-field">--}}
            <input type="hidden" name="menu_item[{{$row['order']}}][parent_id]" value="{{$row['parent_id']}}" class="hidden-parent-field">
            <input type="hidden" name="menu_item[{{$row['order']}}][route]" value="{{$row['route']}}" class="hidden-route-field">
            <input type="hidden" name="menu_item[{{$row['order']}}][new_tab]" value="{{$row['new_tab']}}" class="hidden-newtab-field">
            <input type="hidden" name="menu_item[{{$row['order']}}][mega_menu]" value="{{$row['mega_menu']}}" class="hidden-megamenu-field">
            <input type="hidden" name="menu_item[{{$row['order']}}][order]" value="{{$row['order']}}" class="hidden-order-field">
        </div>
    </div>