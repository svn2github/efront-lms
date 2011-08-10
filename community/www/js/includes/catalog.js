function updateCoupon(el) {
	var url    = location.toString();
	parameters = {coupon:$('coupon_bogus').value, ajax:'coupon', method: 'get'};
	ajaxRequest(el, url, parameters, onUpdateCoupon);					
}
function onUpdateCoupon(el, response) {
	try {
		$('coupon_code').value = $('coupon_bogus').value;
		$('total_price_string').update(response.evalJSON().price_string);
		if ($('paypal_form')) { 
			if ($('paypal_form')['amount']) {
				$('paypal_form')['amount'].value = response.evalJSON().price;
			} else if ($('paypal_form')['a3']) {
				$('paypal_form')['a3'].value = response.evalJSON().price;
			}
			if ($('paypal_form')['item_number']) {
				$('paypal_form')['item_number'].value += response.evalJSON().id+',';
				//$('paypal_form')['item_number'].value = $('paypal_form')['item_number'].value.slice(0, -1); //Remove trailing ','
			}
		}
		if ($('coupon_bogus').value) {
			$('enter_coupon_link').update(translations['_COUPON'] + ': ' + $('coupon_bogus').value);
		} else {
			$('enter_coupon_link').update(translations['_CLICKTOENTERDISCOUNTCOUPON']);
		}
		eF_js_showDivPopup('', '', 'coupon_table');
	} catch (e) {alert(e);}
}

function addToCart(el, id, type) {
	var url    = location.toString();
	parameters = {fct:'addToCart', id:id, ajax:'cart', type:type, method: 'get'};
	ajaxRequest(el, url, parameters, onCartOperation, false, false);				
}		
function removeFromCart(el, id, type) {
	var url    = location.toString();
	parameters = {fct:'removeFromCart', ajax:'cart', id:id, type:type, method: 'get'};
	ajaxRequest(el, url, parameters, onCartOperation);				
}		

function removeAllFromCart(el) {
	var url    = location.toString();
	parameters = {fct:'removeAllFromCart', ajax:'cart', method: 'get'};
	ajaxRequest(el, url, parameters, onCartOperation);				
}
function onRemoveAllFromCart(el, response) {
	$('cart').innerHTML = response;
}
function onCartOperation(el, response) {
	var re2         = new RegExp("<!--ajax:cart-->((.*[\n])*)<!--\/ajax:cart-->");	//Does not work with smarty {strip} tags!
    var tableText   = re2.exec(response);

	if (!tableText) {
        var re      = new RegExp("<!--ajax:cart-->((.*[\r\n\u2028\u2029])*)<!--\/ajax:cart-->");	//Does not work with smarty {strip} tags!
        tableText   = re.exec(response);
	}

    $('cart').innerHTML = tableText[1];
	//$('cart').innerHTML = response;
}

function paypalSubmit() {
	$('checkout_form').request();
	return false;
}



//Direction tree functions
function showAll() {
	$$('tr').each(function (tr) 	  {tr.id.match(/subtree/) ? tr.show() : null;});
	$$('table').each(function (table) {table.id.match(/direction_/) ? table.show() : null;});
	$$('img').each(function (img) {
		if (img.id.match('subtree_img') && !img.hasClassName('visible')) {
			setImageSrc(img, 16, 'navigate_up');
			img.addClassName('visible');
		}
	});
	$('catalog_hide_all').show();
	$('catalog_show_all').hide();
	setCookie('collapse_catalog', 0);
}
function hideAll() {
	$$('tr').each(function (tr) 	  {tr.id.match(/subtree/) ? tr.hide() : null;});
	$$('img').each(function (img) {
		if (img.id.match('subtree_img') && img.hasClassName('visible')) {
			img.removeClassName('visible');
			setImageSrc(img, 16, 'navigate_down');
		}
	});
	$('catalog_hide_all').hide();
	$('catalog_show_all').show();
	setCookie('collapse_catalog', 1);
}

function showHideDirections(el, ids, id, mode) {	 
	
	Element.extend(el);		//IE intialization
	if (mode == 'show') {
		el.up().up().nextSiblings().each(function(s) {s.show();});
		if (ids) {
			ids.split(',').each(function (s) { showHideDirections($('subtree_img'+id), $('subtree_children_'+s) ? $('subtree_children_'+s).innerHTML : '', s, 'show');});
			ids.split(',').each(function (s) { obj = $('direction_'+s); obj ? obj.show() : '';});
		}
		setImageSrc(el, 16, 'navigate_up');
		$('subtree_img'+id) ? $('subtree_img'+id).addClassName('visible') : '';
	} else {
		el.up().up().nextSiblings().each(function(s) {s.hide();});
		if (ids) {
			ids.split(',').each(function (s) { showHideDirections($('subtree_img'+id), $('subtree_children_'+s) ? $('subtree_children_'+s).innerHTML : '', s, 'hide') });
			ids.split(',').each(function (s) { obj = $('direction_'+s); obj ? obj.hide() : '';});
		}
		setImageSrc(el, 16, 'navigate_down.png');
		$('subtree_img'+id) ? $('subtree_img'+id).removeClassName('visible') : '';
	}
}
function showHideCourses(el, course) {
	Element.extend(el);
	if (el.hasClassName('visible')) {
		if (course) {
			course.hide();
		}
		setImageSrc(el, 16, 'navigate_down.png');
		el.removeClassName('visible');
	} else {
		if (course) {
			course.show();
		}
		setImageSrc(el, 16, 'navigate_up');
		el.addClassName('visible');
	}
}

function updateInformation2(el, id, type, from_course) {
	Element.extend(el);
	
	var url = 'ask_information.php';
	parameters = {method: 'get'};
	type == 'lesson' ? Object.extend(parameters, {lessons_ID:id}) : Object.extend(parameters, {courses_ID:id});
	
	ajaxRequest(el, url, parameters, onUpdateInformation2);					

}
function onUpdateInformation2(el, response) {
	alert(response);
}

function filterTree(el, url) {
	Element.extend(el);
	url.match(/\?/) ? url = url+'&' : url = url + '?';
	new Ajax.Request(url+'filter='+el.value+'&ajax=1', {
		method:'get',
		asynchronous:true,
		onSuccess: function (transport) {
			$('directions_tree').innerHTML = transport.responseText;
			showAll();
		}
	});
}

