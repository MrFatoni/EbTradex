$(document).ready(function(){
	//Load default actions first
    var navSerial = 99999;
	firstload();
	myAutoOrder();

	var allMenuItem = $('.mymenu').find('li');
	if(!allMenuItem){
	    navSerial=1
    }
    else{
        navSerial =allMenuItem.length+1;
    }
	//Add Page Action
	$('#add-route').on('click', function(){
		$('#all-routes').find('.route-check-box:checked').each(function(){
			var $this = $(this);
			var itemName = $(this).siblings('span').text();
			var itemValue = $(this).val();
			var appendedData = newItem(navSerial, itemName, 'Route', itemValue);
			$('.mymenu').append(appendedData);
			$this.prop('checked', false);
        	navSerial++;
		})
		myAutoOrder();
	})

	//Add Custom Link Action
	$('#add-link').on('click', function(){
        var itemValue = $('#link-data').val();
		if(!itemValue || itemValue==''){
            itemValue = 'javascript:;';
		}
        var itemName = $('#link-name').val();
		if(!itemName || itemName==''){
            itemName = 'Unnamed';
		}
        var appendedData = newItem(navSerial, itemName, 'Link', itemValue);
        $('.mymenu').append(appendedData);
		$('#link-data').val('');
		$('#link-name').val('');
        navSerial++;
		myAutoOrder();
	})

	//Delete Action
	$(document).on('click', '.deletebutton', function(e) {
		e.preventDefault();
		var innerOl = $(this).closest('.individual-menu-item').children('ol').children('li');
        if(innerOl.length!=0){
            $(this).closest('.individual-menu-item').after(innerOl);
        }
		$(this).closest('li').remove();
		myAutoOrder();
	}) ;

    $(document).on('keypress', '.prevent-default', function(e) {
        if (e.keyCode === 10 || e.keyCode === 13) {
            e.preventDefault();
        }
        // e.preventDefault();
    })
    $(document).on('keyup keypress', '.name', function(e) {
        if (e.keyCode === 10 || e.keyCode === 13) {
            e.preventDefault();
        }
        var nameValue = $(this).val();
        $(this).closest('.individual-menu-item').children('.innermenu').children('.innermenuhead').find('.title').text(nameValue);
    })

	//Toggle Action
	$(document).on('click', '.innermenuhead .arrow-icon', function() {
			$(this).closest('.innermenuhead').siblings('.innermenubody').slideToggle(300);
			if($(this).find('i').hasClass('fa-caret-right')){
				$(this).find('i').removeClass('fa-caret-right').addClass('fa-caret-down');
			}
			else{
				$(this).find('i').removeClass('fa-caret-down').addClass('fa-caret-right');
			}
	}) ;

	//Megamenu action
	$(document).on('click', '.megamenu', function() {
        var hiddenMegamenuField = 0;
        if($(this).prop('checked')==true){
            hiddenMegamenuField = 1;
        }
		$(this).closest('.innermenubody').find('.hidden-megamenu-field').val(hiddenMegamenuField);
	})

	//NewTab action
	$(document).on('click', '.newwindow', function() {
		var hiddenNewtabField = 0;
		if($(this).prop('checked')==true){
            hiddenNewtabField = 1;
		}
		$(this).closest('.innermenubody').find('.hidden-newtab-field').val(hiddenNewtabField);
	})

	//Sortable Nesting
    $('.mymenu').nestedSortable({
        handle: '.title',
        items: 'li',
        toleranceElement: '> div',
        revert: function(){
        	// myAutoOrder('y')
        	// return true;
        },
        change: function(){
            // myAutoOrder('y')
        },
        relocate: function(){
        	myAutoOrder()
        	// return true;
        }
    });

    //Auto arrange Hidden field
    function myAutoOrder(){
    	var mymenu = $('.mymenu').find('li');
		var serialInside = 1;
		var mymenulength = mymenu.length;
		for(var i=0; i<mymenulength; i++){
			$(mymenu[i]).children('.innermenu').children('.innermenubody').find('.hidden-order-field').val(serialInside);
			if(!$(mymenu[i]).parent().hasClass('mymenu')){
				$(mymenu[i]).children('.innermenu').children('.innermenubody').find('.hidden-parent-field').val($(mymenu[i]).parent().parent().children('.innermenu').children('.innermenubody').find('.hidden-order-field').val());
				$(mymenu[i]).children('.innermenu').children('.innermenubody').find('.megamenu').prop('checked', false);
				$(mymenu[i]).children('.innermenu').children('.innermenubody').find('.hidden-megamenu-field').val('0');
			}else{
				$(mymenu[i]).children('.innermenu').children('.innermenubody').find('.hidden-parent-field').val('0');
			}
			serialInside++;
		}
		return true
    }

    //Action when window gets loaded first
    function firstload(){
		$('.route-check-box').prop('checked', false);
		$('#link-data').val('');
		$('#link-name').val('');
    }

	function newItem(serial, itemName, type, itemValue){
    	var output = '<li class="individual-menu-item"><div class="innermenu"><div class="innermenuhead"><div class="title">';
		output = !itemName ? output + 'Unnamed' : output + itemName;
		output = output + '</div><div class="type"><span class="arrow-icon">';
        output = output + type;
        output = output + '<i class="fa fa-caret-right"></i></span></div></div><div class="innermenubody"><p><label>Navigation Label<br></label>';
        output = output + '<input type="text" class="name" value="'+itemName+'" name="menu_item['+serial+'][name]"></p>';
		if(type=='Link'){
            output = output + '<p><label>Link<br></label><input type="text" class="custom-link-field prevent-default" value="'+ itemValue +'" name="menu_item['+serial+'][custom_link]"></p>';
        } else{
            output = output + '<p style="padding-top:10px"><label>Route: '+ itemValue +'</label></p>';
        }
        output = output + '<div class="row"><div class="col-xs-6"><p><label>Extra Class<br></label><input type="text" name="menu_item['+serial+'][class]" value="" class="prevent-default"></p></div><div class="col-xs-6"><p><label>Menu Icon<br></label><input type="text" name="menu_item['+serial+'][icon]" value="" class="prevent-default"></p></div></div><p><label>Beginning Text<br></label><input type="text" name="menu_item['+serial+'][beginning_text]" value="" class="prevent-default"></p><p><label>Ending Text<br></label><input type="text" name="menu_item['+serial+'][ending_text]" value="" class="prevent-default"></p><p><label></label><input type="checkbox" class="newwindow"><em>Open link in a new window/tab</em></p><p class="mymgmenu"><label></label><input type="checkbox" class="megamenu"><em>Use As Mega Menu</em></p><hr class="myhrborder"><button class="deletebutton">Remove</button>';
		if(type=='Route'){
            output = output + '<input type="hidden" value="" name="menu_item['+serial+'][custom_link]" class="custom-link-field">';
            output = output + '<input type="hidden" value="'+itemValue+'" name="menu_item['+serial+'][route]" class="hidden-route-field">';
		}
		else{
            output = output + '<input type="hidden" value="" name="menu_item['+serial+'][route]" class="hidden-route-field">';
		}
        output = output + '<input type="hidden" name="menu_item['+serial+'][parent_id]" value="0" class="hidden-parent-field"><input type="hidden" name="menu_item['+serial+'][new_tab]" value="0" class="hidden-newtab-field"><input type="hidden" name="menu_item['+serial+'][mega_menu]" value="0" class="hidden-megamenu-field"><input type="hidden" name="menu_item['+serial+'][order]" value="0" class="hidden-order-field"></div></div></li>';
		return output;
	}

	$('.menu-submit').on('click',function(){
	    var a = myAutoOrder();
        if(a==true){
	        $('#form-submit-button').click();
        }
    })
});