function updateVoucher(el) {
	var url    = location.toString();
	parameters = {voucher:$('voucher_bogus').value, ajax:'voucher', method: 'get'};
	ajaxRequest(el, url, parameters, onUpdateVoucher);					
}
function onUpdateVoucher(el, response) {
	$('voucher_code').value = $('voucher_bogus').value;
	$('total_price_string').update(response.evalJSON().price); 
}

function addToCart(el, id, type) {
	var url    = location.toString();
	parameters = {fct:'addToCart', id:id, ajax:'cart', type:type, method: 'get'};
	ajaxRequest(el, url, parameters, onCartOperation, false, false);				
//	if (subscription) {
//		parameters = {fct:'addSubscriptionToCart', id:id, ajax:'cart', type:type, method: 'get'};
//	} else {
//	}
}		
function onAddToCart(el, response) {
	$('cart').innerHTML = response;
/*
	if (!response) {
		$('cart').up().up().up().hide();
	} else {
		$('cart').up().up().up().show();
	} 			
*/
}
function removeFromCart(el, id, type) {
	var url    = location.toString();
	parameters = {fct:'removeFromCart', ajax:'cart', id:id, type:type, method: 'get'};
	ajaxRequest(el, url, parameters, onCartOperation);				
}		
function onremoveFromCart(el, response) {
	$('cart').innerHTML = response;
/*	
	if (cart_preview) {
		if ($('cart_'+el.id)) {
			new Effect.Fade($('cart_'+el.id).up().up().up());
		} else {
			new Effect.Fade(el.up().up());
			location.reload();
		}
	} else {
		$('cart').innerHTML = response;
	}
*/
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
	$('cart').innerHTML = response;
}

function paypalSubmit() {
	$('checkout_form').request();
	return false;
}

