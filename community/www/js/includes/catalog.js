function updateCoupon(el) {
 var url = location.toString();
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
 var url = location.toString();
 parameters = {fct:'addToCart', id:id, ajax:'cart', type:type, method: 'get'};
 ajaxRequest(el, url, parameters, onCartOperation, false, false);
}
function removeFromCart(el, id, type) {
 var url = location.toString();
 parameters = {fct:'removeFromCart', ajax:'cart', id:id, type:type, method: 'get'};
 ajaxRequest(el, url, parameters, onCartOperation);
}

function removeAllFromCart(el) {
 var url = location.toString();
 parameters = {fct:'removeAllFromCart', ajax:'cart', method: 'get'};
 ajaxRequest(el, url, parameters, onCartOperation);
}
function onRemoveAllFromCart(el, response) {
 $('cart').innerHTML = response;
}
function onCartOperation(el, response) {
 var re2 = new RegExp("<!--ajax:cart-->((.*[\n])*)<!--\/ajax:cart-->"); //Does not work with smarty {strip} tags!
    var tableText = re2.exec(response);

 if (!tableText) {
        var re = new RegExp("<!--ajax:cart-->((.*[\r\n\u2028\u2029])*)<!--\/ajax:cart-->"); //Does not work with smarty {strip} tags!
        tableText = re.exec(response);
 }

    $('cart').innerHTML = tableText[1];
 //$('cart').innerHTML = response;
}

function paypalSubmit() {
 $('checkout_form').request();
 return false;
}
