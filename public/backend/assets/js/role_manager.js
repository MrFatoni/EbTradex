    (function ($) {
    $('.route-group').each(function(){
        var $this                   = $(this);
        var mainGroupItems          = $(this).find('.route-item').length;
        var mainGroupItemsChecked   = $(this).find('.route-item:checked').length;
        if(mainGroupItems==mainGroupItemsChecked){
            $this.find('.module').parent().addClass('checked');
            $this.find('.sub-module').parent().addClass('checked');
        }
        else{
            $this.find('.module').parent().removeClass('checked');
            $this.find('.route-subgroup').each(function(){
                var subGroupItems           = $(this).find('.route-item').length;
                var subGroupItemsChecked    = $(this).find('.route-item:checked').length;
                if(subGroupItems==subGroupItemsChecked){
                    $(this).find('.sub-module').parent().addClass('checked');
                }
                else{
                    $(this).find('.sub-module').parent().removeClass('checked');
                }
            })
        }
    });


    $('input.route-item').on('ifChanged', function(){
        var $this                   = $(this);
        var mainGroupItems          = $(this).closest('.route-group').find('.route-item').length;
        var mainGroupItemsChecked   = $(this).closest('.route-group').find('.route-item:checked').length;
        var subGroupItems           = $(this).closest('.route-subgroup').find('.route-item').length;
        var subGroupItemsChecked    = $(this).closest('.route-subgroup').find('.route-item:checked').length;
        if(mainGroupItems==mainGroupItemsChecked){
            $(this).closest('.route-group').find('.module').parent().addClass('checked');
        }
        else{
            $(this).closest('.route-group').find('.module').parent().removeClass('checked');
        }
        if(subGroupItems==subGroupItemsChecked){
            $(this).closest('.route-subgroup').find('.sub-module').parent().addClass('checked');
        }
        else{
            $(this).closest('.route-subgroup').find('.sub-module').parent().removeClass('checked');
        }
    })
})(jQuery);