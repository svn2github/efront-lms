	<div id = "cart" class = "cart">	
    {if $smarty.session.cart.lesson || $smarty.session.cart.subscription}
        {if $smarty.session.cart.subscription && $smarty.get.fct != 'cartPreview'}
            {foreach name = 'cartlist' key = 'key' item = 'cartlist' from = $smarty.session.cart.subscription}
                <div class = "cartElement">
                	<div class = "cartTitle">{$cartlist.name}</div>
                    <div class = "cartDelete">
                        <span>{$cartlist.price}</span>
                        <img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._REMOVEFROMCART}" title = "{$smarty.const._REMOVEFROMCART}" onclick = "removeLessonFromCart(this, '{$cartlist.id}');" id = "{$cartlist.id}">
                    </div>
                &nbsp;</div>
            {/foreach}        
    	{/if}

        {foreach name = 'cartlist' key = 'key' item = 'cartlist' from = $smarty.session.cart.lesson}
            <div class = "cartElement">
            	<div class = "cartTitle">{$cartlist.name}</div>
                <div class = "cartDelete">
                    <span>{if $cartlist.price == 0}{$smarty.const._FREEOFCHARGE}{else} 
						{if $T_CONFIGURATION.currency_order != 1}
							{$cartlist.price} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}
						{else}
							{$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]} {$cartlist.price} 
						{/if}
					{/if}</span>
                    <img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._REMOVEFROMCART}" title = "{$smarty.const._REMOVEFROMCART}" onclick = "removeLessonFromCart(this, '{$cartlist.id}');">
                </div>
            &nbsp;</div>
            {*assign var = "totalPrice" value = $totalPrice+$cartlist.price*}
        {/foreach}
        {if $smarty.get.fct != 'cartPreview'}
            <div id = "cart_total">              	
                {*<span>{$smarty.const._PAYPALFINALPRICE}: {if $totalPrice == 0}{$smarty.const._FREEOFCHARGE}{else}{$totalPrice} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}{/if}</span>*}
                <span>{$smarty.const._REMOVEALL}</span>
                <img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._REMOVEALLFROMCART}" title = "{$smarty.const._REMOVEALLFROMCART}" onclick = "removeAllFromCart(this);">							
            </div>
            <div id = "submit_cart"><input class = "flatButton" type = "submit" value = "{$smarty.const._CONTINUE}&nbsp;&raquo;" onclick = "buyRedirect()"></div>
        {else}
                <form {$T_ORDER_LESSONS_FORM.attributes}>
                    {$T_ORDER_LESSONS_FORM.hidden}            
                {if !$T_ORDER_LESSONS_FORMDATA}
                    <div style = "text-align:center;">{$T_ORDER_LESSONS_FORM.order.html}</div>            
                {/if}
                </form>
                {if $T_ORDER_LESSONS_FORMDATA}
                <form {$T_ORDER_LESSONS_FORMDATA.attributes}>
                    {$T_ORDER_LESSONS_FORMDATA.hidden}
                    <div style = "text-align:center;">{$T_ORDER_LESSONS_FORMDATA.order.html}{$T_ORDER_LESSONS_FORM.order.html}</div>            
                </form>
                {/if}
        {/if}
	{/if}
    </div>
	
