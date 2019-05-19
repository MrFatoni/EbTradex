(function ($) {
    'use strict';

    function footerFixer(){
        /*if($(window).height() >= $('.content-wrapper').height()){
            $('.content-wrapper').css({
                paddingBottom: $('.main-footer').height()
            })
            $('.main-footer').css({
                position:'absolute !important',
                bottom:'0 !important',
                width:'100% !important',
            })
        }
        else{
            $('.content-wrapper').css({
                paddingBottom: $('.main-footer').height()
            })
            $('.main-footer').css({
                position:'fixed !important',
                bottom:'0 !important',
                width:'100% !important',
            })
        }*/
    }
    $(window).resize(function () {
        footerFixer()
    })
    footerFixer()

    function elementAction(id, formSubmit) {
        var elem = document.getElementById(id);
        if (elem) {
            if (formSubmit == 'y') {
                document.getElementById(id).submit();
            } else {
                return elem.parentNode.removeChild(elem);
            }
        }
    }

    function closeMethod() {
        elementAction($('.flash-message').find('.flash-confirm').attr('data-form-auto-id'))
        $('.flash-message').removeClass('flash-message-active').remove('flash-message-window');
        $('.flash-message').find('.flash-confirm').attr('href', 'javascript:;').removeAttr('data-form-id').removeAttr('data-form-auto-id');
        $('.flash-message')
            .find('.centralize-content')
            .removeClass('flash-success')
            .removeClass('flash-error')
            .removeClass('flash-warning')
            .removeClass('flash-confirmation')
            .find('p')
            .text('');
    }

    $(document).on('click', '.flash-close', function (e) {
        e.preventDefault();
        closeMethod();
    });

    $(document).on('click', '.flash-message-window', function (e) {
        e.preventDefault();
        closeMethod();
    });

    $(document).on('click', '.flash-confirm', function (e) {
        var $this = $(this);
        var dataInfo = $this.attr('data-form-id');
        if (dataInfo) {
            e.preventDefault();
            var autoForm = $this.attr('data-form-auto-id');
            if (autoForm) {
                elementAction(autoForm, 'y');
            }
        } else {
            $('#' + dataInfo).submit();
        }
        closeMethod();
    });

    $(document).on('click', '.confirmation', function (e) {
        e.preventDefault();
        var $this = $(this);
        var dataAlert = $this.attr('data-alert');
        dataInfo = $this.attr('data-form-id');
        if (!dataInfo) {
            var dataInfo = $this.attr('href');
            $('.flash-message').find('.flash-confirm').attr('href', dataInfo);
        } else {
            var autoForm = $this.attr('data-form-method');
            if (autoForm) {
                var link = $this.attr('href')
                var dataToken = $('meta[name="csrf-token"]').attr('content');
                autoForm = autoForm.toUpperCase();
                if (autoForm == 'POST' || autoForm == 'PUT' || autoForm == 'DELETE') {
                    var newForm = '<form id="#auto-form-generation-' + dataInfo + '" method="POST" action= "' + link + '" style="height: 0; width: 0; overflow: hidden;">'; //
                    newForm = newForm + '<input type = "hidden" name ="_token" value = "' + dataToken + '">';
                    newForm = newForm + '<input type = "hidden" name ="_method" value = "' + autoForm + '">';
                    $('body').prepend(newForm);
                }
                $('.flash-confirm').attr('data-form-auto-id', '#auto-form-generation-' + dataInfo);
            }
            $('.flash-message').find('.flash-confirm').attr('data-form-id', dataInfo);
        }
        $('.flash-message').find('.centralize-content').addClass('flash-confirmation').find('p').text(dataAlert);
        $('.flash-message').addClass('flash-message-active');
    });

    $('.sidebar-menu').find('.active').closest('.treeview-menu').show().closest('.treeview').addClass('menu-open');

    $('.sidebar-menu').children('li').each(function () {
        var $this = $(this);
        var hrefData = $this.children('a').attr('href');
        if (!hrefData || hrefData == '' || hrefData == '#' || hrefData.toLowerCase() == 'javascript:;') {
            if ($this.find('li').length <= 0) {
                $this.remove();
            }
        }

        var dropdown = $this.children('ul');
        if(dropdown.length>0){
            $this.addClass('treeview');
            $this.children('a').append('<i class="fa fa-angle-left pull-right"></i>');
            if(dropdown.find('li.active').length > 0){
                $this.addClass('treeview menu-open');
            }
            else{
                $this.addClass('treeview');
            }
        }

    });
})(jQuery);

function flashBox(warnType, message){
    $('.flash-message').find('.centralize-content').addClass('flash-'+warnType).find('p').text(message);
    $('.flash-message').addClass('flash-message-active flash-message-window');
}

//iCheck for checkbox and radio inputs
$('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
    checkboxClass: 'icheckbox_minimal-blue',
    radioClass: 'iradio_minimal-blue'
});

//Flat red color scheme for iCheck
$('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
    checkboxClass: 'icheckbox_flat-green',
    radioClass: 'iradio_flat-green'
});

$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})