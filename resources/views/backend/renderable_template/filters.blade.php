<div class="row">
    <div class="col-lg-12">
        <div class="box box-primary box-borderless">
            <div class="box-body">
                <form action="{{$route}}" class="dc-form" method="get">
                    <div class="cm-filter clearfix">
                        @if(is_array($orderFields) && !$searchOnly)
                            <div class="cm-order-filter">
                                <select class="form-control" name="{{$paginationKey}}_sort">
                                    <option value="">{{ __('Sort by') }}</option>
                                    @foreach($orderFields as $ofKey => $ofVal)
                                        <option value="{{$ofKey}}"{{return_get($paginationKey.'_sort',$ofKey)}}>{{$ofVal[1]}}</option>
                                    @endforeach
                                </select>
                                <select class="form-control" name="{{$paginationKey}}_ord">
                                    <option value="d"{{return_get($paginationKey.'_ord','d')}}>{{ __('Desc') }}</option>
                                    <option value="a"{{return_get($paginationKey.'_ord','a')}}>{{ __('Asc') }}</option>
                                </select>
                                <button type="submit" class="btn"><i class="fa fa-arrow-right"></i></button>
                            </div>
                        @endif
                        @if(!$searchOnly)
                        <div class="cm-date-filter">
                            <input type="text" class="form-control datepicker" name="{{$paginationKey}}_frm"
                                   placeholder="{{ __('From date') }}"
                                   value="{{return_get($paginationKey.'_frm')}}">
                            <span> to </span>
                            <input type="text" class="form-control datepicker" name="{{$paginationKey}}_to"
                                   placeholder="{{ __('To date') }}"
                                   value="{{return_get($paginationKey.'_to')}}">
                            <button type="submit" class="btn dc-submit"><i class="fa fa-arrow-right"></i></button>
                        </div>
                        @endif
                        <div class="cm-search-filter">
                            @if(isset($searchFields) && !$searchOnly)
                            <select class="form-control" name="{{$paginationKey}}_ssf">
                                <option value="">{{ __('All Fields') }}</option>
                                @foreach($searchFields as $ssfKey => $ssfVal)
                                    <option value="{{$ssfKey}}"{{return_get($paginationKey.'_ssf',$ssfKey)}}>{{$ssfVal[1]}}</option>
                                @endforeach
                            </select>
                            <select class="form-control select-compact" name="{{$paginationKey}}_comp">
                                <option value="lk"{{return_get($paginationKey.'_comp','lk')}}>{{__('Similar to')}}</option>
                                <option value="e"{{return_get($paginationKey.'_comp','e')}}>{{__('Exact to')}}</option>
                                <option value="l"{{return_get($paginationKey.'_comp','l')}}>{{__('Smaller than')}}</option>
                                <option value="le"{{return_get($paginationKey.'_comp','le')}}>{{__('Less or equal to')}}</option>
                                <option value="m"{{return_get($paginationKey.'_comp','m')}}>{{__('Bigger Than')}}</option>
                                <option value="me"{{return_get($paginationKey.'_comp','me')}}>{{__('Bigger or Equal to')}}</option>
                                <option value="ne"{{return_get($paginationKey.'_comp','ne')}}>{{__('Not Equal')}}</option>
                            </select>
                            @endif
                            <input type="text" class="form-control" name="{{$paginationKey}}_srch" placeholder="{{ __('search') }}"
                                   value="{{return_get($paginationKey.'_srch')}}">
                            <button type="submit" class="btn"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>