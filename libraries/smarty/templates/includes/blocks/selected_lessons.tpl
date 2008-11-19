    {if $smarty.session.cart.lesson}
    	<div id = "cart">
        {foreach name = 'cartlist' key = 'key' item = 'cartlist' from = $smarty.session.cart.lesson}
            <div class = "cartElement">
            	<div class = "cartTitle">{$cartlist.name}</div>
                <div class = "cartDelete">
                    <span>{if $cartlist.price == 0}{$smarty.const._FREEOFCHARGE}{else}{$cartlist.price} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}{/if}</span>
                    <a href = "javascript:void(0)" onclick = "ajaxPostRemove('{$cartlist.id}', this);">
                        <img src = "images/16x16/delete.png" alt = "{$smarty.const._REMOVEFROMCART}" title = "{$smarty.const._REMOVEFROMCART}"></a>
                </div>
            </div>
            {assign var = "totalPrice" value = $totalPrice+$cartlist.price}
        {/foreach}
            <div id = "cart_total">                        	
                <span>{$smarty.const._PAYPALFINALPRICE}: {if $totalPrice == 0}{$smarty.const._FREEOFCHARGE}{else}{$totalPrice} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}{/if}</span>
				<a href = "javascript:void(0)" onclick = "ajaxPostRemoveAll('', this);">
                	<img src = "images/16x16/delete.png" alt = "{$smarty.const._REMOVEALLFROMCART}" title = "{$smarty.const._REMOVEALLFROMCART}"></a>							
            </div>
        {if $smarty.get.fct != 'cartPreview'}
            <div id = "submit_cart"><input class = "flatButton" type = "submit" value = "{$smarty.const._CONTINUE}&nbsp;&raquo;" onclick = "location = '{if $smarty.session.s_login}directory.php?fct=cartPreview{else}index.php?ctg=login&register_lessons=1&message='+encodeURI('{$smarty.const._PLEASELOGINTOCOMPLETEREGISTRATION}')+'{/if}'"></div>
        {else}
                <form {$T_ORDER_LESSONS_FORM.attributes}>
                    {$T_ORDER_LESSONS_FORM.hidden}            
                    <div style = "text-align:center;">{$T_ORDER_LESSONS_FORM.order.html}</div>            
                </form>
                <form {$T_ORDER_LESSONS_FORMDATA.attributes}>
                    {$T_ORDER_LESSONS_FORMDATA.hidden}
                </form>
        {/if}
        </div>
	{else}
    	<div id = "cart">
			<span class = "small">-</span>
		</div>
	{/if}
	{assign var = "title" value = "`$title`<span>&nbsp;&raquo;&nbsp;</span><a href = '`$smarty.server.PHP_SELF`?fct=cartPreview'>`$smarty.const._SELECTEDLESSONS`</a>"}
	
	<script type = "text/javascript">
<!--	
	{literal}
        function ajaxPost(id, el, login) {
            Element.extend(el);
            var url      = 'directory.php?fct=addLessonToCart' + '&id='+id;
			src = el.down().src;
			el.down().src = 'images/others/progress_big.gif';
			
            new Ajax.Request(url, {
                method:'get',
                asynchronous:true,
                onFailure: function (transport) {
                	showMessage(transport.responseText, 'failure');
                    el.down().src = src;
                },
                onSuccess: function (transport) {      
                    $('cart').innerHTML = transport.responseText;
                    el.down().src = src;
                    if (!transport.responseText) {
                    	$('cart').up().up().up().hide();
                    } else {
                    	$('cart').up().up().up().show();
                    } 
                }
            });
		}		
        function ajaxPostRemove(id, el) {
        	Element.extend(el);
            var url      = 'directory.php?fct=removeLessonFromCart' + '&id='+id;
			src = el.down().src;
			el.down().src = 'images/others/progress1.gif';
            
            new Ajax.Request(url, {
                method:'get',
                asynchronous:true,
                onFailure: function (transport) {
                	showMessage(transport.responseText, 'failure');
                    el.down().src = src;
                },
                onSuccess: function (transport) {
					$('cart').innerHTML = transport.responseText;
                    el.down().src = src;
                    if (!transport.responseText) {
                    	$('cart').up().up().up().hide();
                    } else {
                    	$('cart').up().up().up().show();
                    } 
                }
            });
        }
        function ajaxPostRemoveAll(id, el) {
        	Element.extend(el);
            var url  =  'directory.php?fct=removeLessonAllFromCart';
			src = el.down().src;
			el.down().src = 'images/others/progress1.gif';
            
            new Ajax.Request(url, {
                method:'get',
                asynchronous:true,
                onFailure: function (transport) {
                	showMessage(transport.responseText, 'failure');
                    el.down().src = src;
                },
                onSuccess: function (transport) {
                    $('cart').innerHTML = transport.responseText;
                    el.down().src = src;
                    if (!transport.responseText) {
                    	$('cart').up().up().up().hide();
                    } else {
                    	$('cart').up().up().up().show();
                    } 
                }
            });
        }
    {/literal}
-->    
	</script>	
