"use strict";
(function($){
    // options
    $.fn.cValidate = function(options){
        options = $.extend({
            /*minStringLimit:1,
            maxStringLimit:255,
            minIntegerLimit:1,
            maxIntegerLimit:99999999999999999999,
            minNumericLimit:0.01,
            maxNumericLimit:99999999999.99,
            minFileSizeLimit:1,*/
            onSuccess:'',
            loadingClass:'is-loading',
            maxFileSizeLimit:2048,
            customRules: {},
            showErrorsInFlash:false,
            preventSubmit: false
        }, options);
        var htmlTags=[
            '<!--', '<!doctype', '<a', '<abbr', '<acronym', '<address', '<applet', '<area', '<article', '<aside', '<audio', '<b', '<base', '<basefont', '<bdi', '<bdo', '<big', '<blockquote', '<body', '<br', '<button', '<canvas', '<caption', '<center', '<cite', '<code', '<col', '<colgroup', '<datalist', '<dd', '<del', '<details', '<dfn', '<dir', '<div', '<dl', '<dt', '<em', '<embed', '<fieldset', '<figcaption', '<font', '<footer', '<form', '<frame', '<frameset', '<h1', '<h2', '<h3', '<h4', '<h5', '<h6', '<head', '<header', '<hr', '<html', '<i', '<iframe', '<img', '<input', '<ins', '<kbd', '<label', '<legend', '<li', '<link', '<main', '<map', '<mark', '<menu', '<menuitem', '<meta', '<meter', '<nav', '<noframes', '<noscript', '<object', '<ol', '<optgroup', '<option', '<output', '<p', '<param', '<picture', '<pre', '<progress', '<q', '<rp', '<rt', '<ruby', '<s', '<samp', '<script', '<section', '<select', '<small', '<source', '<span', '<strike', '<strong', '<style', '<sub', '<summary', '<sup', '<svg', '<table', '<tbody', '<td', '<template', '<textarea', '<tfoot', '<th', '<thead', '<time', '<title', '<tr', '<track', '<tt', '<u', '<ul', '<var', '<video', '<wbr'
        ];
        var limit = {
            string:{/*min:options.minStringLimit, max:options.maxStringLimit, */unit:' characters'},
            integer:{/*min:options.minIntegerLimit, max:options.maxIntegerLimit, */unit:''},
            numeric:{/*min:options.minNumericLimit, max:options.maxNumericLimit, */unit:''},
            files:{/*min:options.minFileSizeLimit, max:options.maxFileSizeLimit, */unit:' kb'}
        };
        var cRules = {
            url: function(thisField, value, key, parameter, name, type, isBetween, form){
                if(value == ''){return false;}
                var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
                    '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name and extension
                    '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
                    '(\\:\\d+)?'+ // port
                    '(\\/[-a-z\\d%@_.~+&:]*)*'+ // path
                    '(\\?[;&a-z\\d%@_.,~+&:=-]*)?'+ // query string
                    '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
                pattern = pattern.test(value);
                return pattern == false ? name + ' must be a valid url.' : false;
            },
            required: function(thisField, value, key, parameter, name, type, isBetween, form){
                return value == '' ? name + ' is required' : false;
            },
            email: function(thisField, value, key, parameter, name, type, isBetween){
                if(value == ''){return false;}
                var regex = /^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
                return regex.test(String(value)) ? false : name + ' must be email';
            },
            integer: function(thisField, value, key, parameter, name, type, isBetween, form){
                if(value == ''){return false;}
                return Math.ceil(value)===false || Math.ceil(value) != Math.floor(value) ? name + ' is not integer' : false;
            },
            numeric: function(thisField, value, key, parameter, name, type, isBetween, form){
                if(value == ''){return false;}
                return !$.isNumeric(value) ? name + ' is not a number' : false;
            },
            min: function(thisField, value, key, parameter, name, type, isBetween, form){
                if(value == ''){return false;}
                if(type=='string'){value = value.length;}
                else if(type=='files'){value = +value.minFileSize;}
                if(parameter !== true){
                    return false
                }
                var min = +parameter;
                // var min = parameter !== true ? +parameter : limit[type].min;
                return value < min && isBetween===false ? name + ' must be minimum ' + min + limit[type].unit : false;
            },
            max: function(thisField, value, key, parameter, name, type, isBetween, form){
                if(value == ''){return false;}
                if(type=='string'){value = value.length;}
                else if(type=='files'){value = +value.maxFileSize;}
                if(parameter !== true){
                    return false
                }
                var min = +parameter;
                // var max = parameter !== true ? +parameter : limit[type].max;
                return value > max && isBetween===false ? name + ' must be maximum ' + max + limit[type].unit : false;
            },
            between: function(thisField, value, key, parameter, name, type, isBetween, form){
                if(value == ''){return false;}
                if(type=='string'){value = value.length;}
                var max =0;
                var min =0;
                if(parameter!==true){var parameters=parameter.split(','); min= +parameters[0]; max= +parameters[1];}
                else{
                    return false;
                    // max=limit[type].max; min=limit[type].min;
                }
                if(type=='files'){
                    if(min==max){
                        return max != value.maxFileSize || min != value.minFileSize ? name + ' must be exactly ' + max + limit[type].unit : false;
                    }
                    return max < value.maxFileSize || min > value.minFileSize ? name + ' must be between ' + min + ' and ' + max + limit[type].unit : false;
                }
                else{
                    if(min==max){
                        return value != max ? name + ' must be exactly ' + max + limit[type].unit : false;
                    }
                    return value > max || value < min ? name + ' must be between ' + min + ' and ' + max + limit[type].unit : false;
                }
            },
            in: function(thisField, value, key, parameter, name, type, isBetween, form){
                if(value == ''){return false;}
                var parameters = parameter.split(',');
                return parameters.indexOf(value) < 0 ? name + ' must be one of the following ' + parameter : false;
            },
            notIn: function(thisField, value, key, parameter, name, type, isBetween, form){
                if(value == ''){return false;}
                var parameters = parameter.split(',');
                return parameters.indexOf(+value)>=0 ? name + ' must not be one of the following ' + parameter : false;
            },
            files: function(thisField, value, key, parameter, name, type, isBetween, form){
                if(value == ''){return false;}
                return value.supported ===false ? name + ' only supports ' + parameter.toLowerCase() : false;
            },
            alpha: function(thisField, value, key, parameter, name, type, isBetween, form){
                if(value == ''){return false;}
                var valid = true;
                var regex = /[1|2|3|4|5|6|7|8|9|0|¶|¤|£|¢|¥|¿|¬|½|¼|«|»|¦|°|•|€|†|‡|™|©|®|¾|¸|¯|!|@|#|$|%|^|&|\*|_|-|=|\+|\(|\)|\{|\}|\[|\]|\'|\"|\\|\|\/|\?|.|>|,|<|`|~|§±\d|\s]/;
                if(regex.exec(value)==null){
                    valid = false;
                }
                return valid!==false ? name + ' must only contain alphabet' : false;
            },
            alphaNum: function(thisField, value, key, parameter, name, type, isBetween, form){
                if(value == ''){return false;}
                var valid = true;
                var regex = /[¶|¤|£|¢|¥|¿|¬|½|¼|«|»|¦|°|•|€|†|‡|™|©|®|¾|¸|¯|!|@|#|$|%|^|&|\*|_|-|=|\+|\(|\)|\{|\}|\[|\]|\'|\"|\\|\|\/|\?|.|>|,|<|`|~|§±\d|\s]/;
                if(regex.exec(value)==null){
                    valid = false;
                }
                return valid!==false ? name + ' must only contain alphabet and numbers' : false;
            },
            alphaSpace: function(thisField, value, key, parameter, name, type, isBetween, form){
                if(value == ''){return false;}
                var valid = true;
                var regex = /[1|2|3|4|5|6|7|8|9|0|¶|¤|£|¢|¥|¿|¬|½|¼|«|»|¦|°|•|€|†|‡|™|©|®|¾|¸|¯|!|@|#|$|%|^|&|\*|_|-|=|\+|\(|\)|\{|\}|\[|\]|\'|\"|\\|\|\/|\?|.|>|,|<|`|~|§±\d]/;
                if(regex.exec(value)==null){
                    valid = false;
                }
                return valid!==false ? name + ' must only contain alphabet and space' : false;
            },
            alphaDash: function(thisField, value, key, parameter, name, type, isBetween, form){
                if(value == ''){return false;}
                var valid = true;
                var regex = /[¶|¤|£|¢|¥|¿|¬|½|¼|«|»|¦|°|•|€|†|‡|™|©|®|¾|¸|¯|!|@|#|$|%|^|&|\*|=|\+|\(|\)|\{|\}|\[|\]|\'|\"|\\|\|\/|\?|.|>|,|<|`|~|§±\d|\s]/;
                if(regex.exec(value)==null){
                    valid = false;
                }
                return valid!==false ? name + ' must only contain alphabet, numbers, _ and -.' : false;
            },
            generalText: function(thisField, value, key, parameter, name, type, isBetween, form){
                if(value == ''){return false;}
                var valid = true;
                var regex = /[1|2|3|4|5|6|7|8|9|0|¶|¤|£|¢|¥|¿|¬|½|¼|«|»|¦|°|•|€|†|‡|™|©|®|¾|¸|¯|!|@|#|$|%|^|&|\*|=|\+|\(|\)|\{|\}|\[|\]|\'|\"|\\|\|\/|\?|>|<|`|~|§±\d|]/;
                if(regex.exec(value)==null){
                    valid = false;
                }
                return valid!==false ? name + ' must only contain alphabet, numbers, _ and -.' : false;
            },
            digitsOnly: function(thisField, value, key, parameter, name, type, isBetween, form){
                if(value == ''){return false;}
                var regex = /^\d+$/;
                return regex.exec(value)==null ? name + ' can only contains digits.' : false;
            },
            strongPassword: function(thisField, value, key, parameter, name, type, isBetween, form){
                if(value == ''){return false;}
                var regex = /^.*(?=.{3,})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\d\X]).*$/;
                return regex.exec(value)==null ? name + ' must contain at least 1 Uppercase, 1 lowercase, 1 number and 1 special characters.' : false;
            },
            follow: function(thisField, value, key, parameter, name, type, isBetween, form){
                var followedValue = form.find('[name="'+parameter+'"]').eq(0).val();
                var followedFieldName = form.find('[name="'+parameter+'"]').eq(0).data('cval-name');
                if(!followedFieldName){
                    followedFieldName = parameter;
                }
                return value!= followedValue ? name + ' must be same as ' + followedFieldName : false;
            },
            followedBy: function(thisField, value, key, parameter, name, type, isBetween, form){
                var parameter = parameter.split(',');
                name = name =='This field' ? thisField.data('cval-name') : name;
                var otherErrors = {};
                for(var i=0; i<parameter.length; i++){
                    var followedValue = form.find('[name="'+parameter[i]+'"]').eq(0).val();
                    var followedFieldName = form.find('[name="'+parameter[i]+'"]').eq(0).data('cval-name');
                    if(!followedFieldName){
                        followedFieldName = 'This field';
                    }
                    if(value!= followedValue){
                        otherErrors[parameter[i]]='error';
                        form.find('.cval-error[data-cval-error="'+parameter[i]+'"]').text(followedFieldName + ' must be same as ' + name);
                    }
                    else{
                        otherErrors[parameter[i]]='noerror';
                        form.find('.cval-error[data-cval-error="'+parameter[i]+'"]').text('');
                    }
                }
                return otherErrors;
            },
            date: function(thisField, value, key, parameter, name, type, isBetween, form){
                if(value == ''){return false;}
                return checkdate(value)==false ? name + ' must be a valid date' : false;
            },
            oldDate: function(thisField, value, key, parameter, name, type, isBetween, form){
                if(value == ''){return false;}
                var output = checkdate(value);
                if(output){
                    output = checkdate(value,parameter)==false ? false : true;
                }
                return output ? name + ' must be a date of ' + parameter.replace(/\s+/g, ' ') + ' old from now.' : false;
            },
            futureDate: function(thisField, value, key, parameter, name, type, isBetween, form){
                if(value == ''){return false;}
                var output = checkdate(value);
                if(output){
                    output = checkdate(value,parameter,true)==false ? false : true;
                }
                return output==false ? name + ' must be a date ahead of ' + parameter.replace(/\s+/g, ' ') + ' from now.' : false;
            },
            escapeInput: function(thisField, value, key, parameter, name, type, isBetween, form){
                if(value == ''){return false;}
                return htmlTags.some(word => value.toLowerCase().includes(word)) ? name + ' must not contain any html tag.' : false;
            },
            escapeText: function(thisField, value, key, parameter, name, type, isBetween, form){
                if(value == ''){return false;}
                var avoidable=['<p','<br','<b','<i','<u','<strong','<ul','<ol','<li'];
                var diff = htmlTags.concat(avoidable).filter(function (e, i, array) {
                    // Check if the element is appearing only once
                    return array.indexOf(e) === array.lastIndexOf(e);
                });
                return diff.some(word => value.toLowerCase().includes(word)) ? name + ' contains unsupported tag(s)' : false;
            },
            escapeFullText: function(thisField, value, key, parameter, name, type, isBetween, form){
                if(value == ''){return false;}
                var avoidable=['<h1','<h2','<h3','<h4','<h5','<h6','<hr','<article','<section','<video','<audio','<table','<tr','<td','<thead','<tfoot','<footer','<header','<p','<br','<b','<i','<u','<strong','<ul','<ol','<dl','<dt','<li','<div','<sub','<sup','<span','<img','<a'];
                var diff = htmlTags.concat(avoidable).filter(function (e, i, array) {
                    // Check if the element is appearing only once
                    return array.indexOf(e) === array.lastIndexOf(e);
                });
                return diff.some(word => value.toLowerCase().includes(word)) ? name + ' contains unsupported tag(s).' : false;
            }
        };

        var cValidate = this;
        if(Object.keys(cRules).length>0){
            $.extend( cRules, options.customRules );
        }


        if(options.preventSubmit){
            cValidate.find('input').on('keydown keyup keypress', function (event) {
                if (event.which == 13 || event.keyCode == 13) {
                    event.preventDefault();
                }
            });
        }


        cValidate.each(function(){
            var cValidateSingle =$(this);
            var cReloadOnSuccess = cValidateSingle.data('reload-on-success');
            if(cReloadOnSuccess){cReloadOnSuccess = cReloadOnSuccess.toLowerCase()};
            var ajaxSubmit = cValidateSingle.data('ajax-submission');
            if(ajaxSubmit){ajaxSubmit = ajaxSubmit.toLowerCase();}
            // var ajaxAction = cValidateSingle.attr('action');
            var ajaxMethod = cValidateSingle.attr('method');
            if(ajaxMethod && ajaxMethod.toUpperCase() == 'POST'){ajaxMethod = 'POST';}else{ajaxMethod = 'GET';}
            var ajaxResetOnSuccess = cValidateSingle.data('reset-on-success');
            if(ajaxResetOnSuccess){ajaxResetOnSuccess = ajaxResetOnSuccess.toLowerCase();}
            var cValidateInputs = cValidateSingle.find('[data-cval-rules]');
            var formErrors={};
            cValidateInputs.each(function(){
                var $this = $(this);
                var inputType = $this.attr('type');
                var cValRules = $this.data('cval-rules').split('|');
                var cValname = $this.attr('name');
                var cValtag = $this.prop('tagName').toLowerCase();
                if($this.attr('type')=='radio'){
                    $this = cValidateSingle.find('[name="'+cValname+'"]');
                }
                var errorSpan = cValidateSingle.find('.cval-error[data-cval-error="'+cValname+'"]');
                var data = {
                    rules : {},
                    name : !$this.data('cval-name') ? 'This field' : $this.data('cval-name')
                };
                for(var i in cValRules){
                    var x = cValRules[i].split(':');
                    if(cRules[x[0]]){
                        data.rules[x[0]] = !x[1] ? true : x[1];
                    }
                }
                if(!data.rules.between && data.rules.min && !data.rules.max && !data.rules.follow){
                    data.rules.max = true;
                }
                else if(!data.rules.between && !data.rules.min && data.rules.max && !data.rules.follow){
                    data.rules.min = true;
                }
                else if(!data.rules.between && !data.rules.min && !data.rules.max && !data.rules.follow){
                    data.rules.between = true;
                }
                eventInside($this, data, errorSpan, cValname, inputType, true)
                $this.on('submit', function (event) {
                    if (Object.keys(formErrors).length > 0) {
                        event.preventDefault();
                    }
                });

                $this.on('keyup change blur input focus', function () {
                    if($this.attr('type') == 'radio'){
                        eventInside(cValidateSingle.find('[name="'+cValname+'"]:checked').eq(0), data, errorSpan, cValname, inputType);
                    }
                    else{
                        eventInside($this, data, errorSpan, cValname, inputType);
                    }
                });
            });
            //Ajaxifying here
            cValidateSingle.find('.reset-button').on('click',function(e){
                e.preventDefault();
                cValidateSingle[0].reset()
                cValidateInputs.each(function(){
                    $(this).focus();
                    $(this).blur();
                })
            });


            cValidateSingle.on('click',".form-submission-button", function (event) {
                if(ajaxSubmit=='y'){
                    event.preventDefault();
                    var submit = $(".form-submission-button",cValidateSingle);
                    submit.addClass(options.loadingClass);
                    var formData = new FormData(this.closest('form'));

                    if($(this).attr('name') && $(this).val()){
                        formData.append($(this).attr('name'), $(this).val());
                    }
                    $.ajax({
                        type: ajaxMethod,
                        url: cValidateSingle.attr('action'),
                        data: formData,
                        dataType: 'JSON',
                        processData: false,
                        contentType: false,
                        async: false,
                        success: function (returnData) {
                            if(returnData.error){
                                flashMessage('error', returnData.error);
                            }
                            else if(returnData.success){
                                flashMessage('success', returnData.success);
                                if(cReloadOnSuccess == 'y')
                                {
                                    location.reload();
                                }

                                // check if view exists or not
                                if(returnData.views){
                                    for(var view in returnData.views)
                                    {
                                        $(document).find('.' + view).html(returnData.views[view]);
                                    }
                                }

                                // check if view exists or not
                                if(returnData.data){
                                    for(var single_data in returnData.data)
                                    {
                                        $(document).find('.' + single_data).text(returnData.data[single_data]);
                                        $(document).find('.' + single_data).val(returnData.data[single_data]);
                                    }
                                }

                                // check function
                                if(options.onSuccess != '' && $.isArray(options.onSuccess))
                                {
                                    if(options.onSuccess[1] == true)
                                    {
                                        window[options.onSuccess[0]](returnData);
                                    }
                                    else{
                                        window[options.onSuccess[0]]();
                                    }
                                }

                            }

                            if(ajaxResetOnSuccess=='y') {
                                cValidateSingle[0].reset();
                            }
                            submit.removeClass(options.loadingClass);
                        },
                        error: function (ajaxStatus) {
                            if(ajaxStatus.status==422){
                                var ajaxErrors = JSON.parse(ajaxStatus.responseText).errors;

                                if(options.showErrorsInFlash){
                                    var errorMessages='';
                                    var joiner = '';
                                    for(var key in ajaxErrors){
                                        errorMessages = errorMessages + joiner + ajaxErrors[key][0]
                                        joiner = '<br>';
                                    }
                                    flashMessage('error', errorMessages);
                                }
                                else{
                                    for(var key in ajaxErrors){
                                        var phpError = cValidateSingle.find('.cval-error[data-cval-error="'+key+'"]');
                                        if(phpError){
                                            phpError.html(ajaxErrors[key][0]);
                                        }
                                    }
                                    flashMessage('error', 'Invalid data in field(s)');
                                }
                            }
                            else{
                                flashMessage('error', 'Connection or Code Errors');
                            }
                            submit.removeClass(options.loadingClass);
                        }
                    });
                }
            });
            function flashMessage(warnType, message){
                $('.flash-message').find('.centralize-content').addClass('flash-'+warnType).find('p').html(message);
                $('.flash-message').addClass('flash-message-active flash-message-window');
            }

            function eventInside($this, data, errorSpan, cValname, inputType, initialize){
                var isBetween = data.rules.between ? true : false;
                var type='string';
                var value = '';
                if(data.rules.integer){type='integer';}
                else if(data.rules.numeric){type='numeric';}
                else if(data.rules.files){
                    var supported = data.rules.files!==true ? data.rules.files.toLowerCase().split(',') : true;
                    type='files';
                    value = {
                        minFileSize:0,
                        maxFileSize:0,
                        supported:true
                    };
                    if($this[0].files && $this[0].files.length > 0){
                        for (var i=0 ; i< $this[0].files.length; i++){
                            if(i==0){
                                value = {
                                    minFileSize:$this[0].files[i].size/1000,
                                    maxFileSize:$this[0].files[i].size/1000,
                                    supported:true
                                };
                            }
                            // var extension = '';
                            value[i] = $this[0].files[i];
                            if($this[0].files[i].size/1000 < value.minFileSize){value.minFileSize=+$this[0].files[i].size/1000;}
                            if($this[0].files[i].size/1000 > value.maxFileSize){value.maxFileSize=+$this[0].files[i].size/1000;}
                            if(supported===true){
                                value.supported=true;
                                break;
                            }
                            else if(value.supported==true && supported !==true){
                                var fileExtension = $this[0].files[i].name.split('.');
                                if(fileExtension.length>1){
                                    fileExtension = fileExtension[fileExtension.length-1].toLowerCase();
                                    if(supported.indexOf(fileExtension)<0){
                                        value.supported=false;
                                    }
                                }
                                else{
                                    value.supported=false;
                                }
                            }
                        }
                    }
                }

                if(type=='files'){
                    if(!$this[0].files || $this[0].files.length <= 0){
                        value = '';
                    }
                }
                else if(type !='files'){
                    if(inputType=='checkbox' || inputType=='radio'){
                        if(!$this.is(':checked')){
                            value = '';
                        }
                        else{
                            value = $this.val();
                        }
                    }
                    else{
                        value = $this.val();
                    }
                }
                for (var key in data.rules) {
                    var error = cRules[key]($this, value, key, data.rules[key], data.name, type, isBetween, cValidateSingle);
                    if(error === false){
                        if(!initialize){
                            errorSpan.text('');
                        }
                        delete formErrors[cValname];
                    }
                    else{
                        if(!initialize && key!='followedBy'){
                            errorSpan.text(error);
                        }
                        if(key!='followedBy'){
                            if(key=='required'|| key=='follow' || $this.val()){
                                formErrors[cValname] = 'error';
                                break;
                            }
                        }
                        else{
                            for(var errorKey in error){
                                if(error[errorKey]=='error'){
                                    formErrors[errorKey] = 'error';
                                }
                                else{
                                    delete formErrors[errorKey];
                                }
                            }
                        }
                    }
                }
                if(Object.keys(formErrors).length>0){
                    cValidateSingle.find('.form-submission-button').attr('disabled','disabled');
                }
                else{
                    cValidateSingle.find('.form-submission-button').removeAttr('disabled');
                }
            }
        });

        function checkdate(data, datediff, future){
            data= data.split('-');
            for(var i=0; i<data.length; i++){
                var value = parseInt(data[i], 10);
                !isNaN(value) || data[i] >= 1 ? data[i]=value : data.splice(i, 1);
            }
            var month31 = [1,3,5,7,8,10,12];
            var month30 = [4,6,9,11];
            if(data.length<3 || (month31.indexOf(data[1]) > -1 && data[2] > 31)  || (month30.indexOf(data[1]) > -1 && data[2] > 30) || (data[0]%4==0 && data[1]==2 && data[2] > 29) || (data[0]%4 > 0 && data[1]==2 && data[2] > 28)){
                return false;
            }
            if(datediff){
                var currentTime = new Date();
                currentTime = [currentTime.getFullYear() , currentTime.getMonth()+1, currentTime.getDate()];
                var timeString = ['years', 'months', 'days', 'year', 'month', 'day']
                datediff = datediff.replace(/\s+/g, ' ').split(' ');
                datediff[0] = parseInt(datediff[0], 10);
                if(datediff.length<2 || isNaN(datediff[0]) || timeString.indexOf(datediff[1]) <= -1){
                    return false;
                }
                data= new Date(data[0]+'-'+data[1]+'-'+data[2]);
                data = data.getTime();
                if(future){
                    if(datediff[1]=='years' || datediff[1]=='year'){
                        currentTime = new Date((currentTime[0]+datediff[0])+'-'+currentTime[1]+'-'+currentTime[2]);
                        currentTime = currentTime.getTime();
                    }
                    else if(datediff[1]=='months' || datediff[1]=='month'){
                        currentTime = new Date((currentTime[0]+Math.floor(datediff[0]/12))+'-'+(currentTime[1]+(datediff[0]%12))+'-'+currentTime[2]);
                        currentTime = currentTime.getTime();
                    }
                    else if(datediff[1]=='days' || datediff[1]=='day'){
                        currentTime = new Date(currentTime[0]+'-'+currentTime[1]+'-'+(currentTime[2]+datediff[0]));
                        currentTime = currentTime.getTime()//+(datediff[0]*86400);
                    }

                    if(data>=currentTime){
                        return true;
                    }
                    return false;
                }
                else{
                    if(datediff[1]=='years' || datediff[1]=='year'){
                        currentTime = new Date((currentTime[0]-datediff[0])+'-'+currentTime[1]+'-'+currentTime[2]);
                        currentTime = currentTime.getTime();
                    }
                    else if(datediff[1]=='months' || datediff[1]=='month'){
                        currentTime = new Date((currentTime[0]-Math.floor(datediff[0]/12))+'-'+(currentTime[1]-(datediff[0]%12))+'-'+currentTime[2]);
                        currentTime = currentTime.getTime();
                    }
                    else if(datediff[1]=='days' || datediff[1]=='day'){
                        currentTime = new Date(currentTime[0]+'-'+currentTime[1]+'-'+(currentTime[2]-datediff[0]));
                        currentTime = currentTime.getTime();
                    }
                    if(data<=currentTime){
                        return true;
                    }
                    return false;
                }
            }
            return true;
        }
    };
})(jQuery);
