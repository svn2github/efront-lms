{strip}
<div id = "cart" class = "cart">	
{foreach name = 'cartlist' key = 'key' item = 'cartlist' from = $T_CART.lesson}
	{if !$cartlist.recurring}
    <div class = "cartElement">
    	<div class = "cartTitle">{$cartlist.name}</div>
        <div class = "cartDelete">
            <span>{if $cartlist.price}{$cartlist.price_string}{else}{$smarty.const._FREEOFCHARGE}{/if}</span>
            <img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._REMOVEFROMCART}" title = "{$smarty.const._REMOVEFROMCART}" onclick = "removeFromCart(this, '{$cartlist.id}', 'lesson');" id = "{$cartlist.id}">
        </div>
    &nbsp;</div>
    {/if}
{/foreach}        
{foreach name = 'cartlist' key = 'key' item = 'cartlist' from = $T_CART.course}
	{if !$cartlist.recurring}
    <div class = "cartElement">
    	<div class = "cartTitle">{$cartlist.name}</div>
        <div class = "cartDelete">
            <span>{if $cartlist.price}{$cartlist.price_string}{else}{$smarty.const._FREEOFCHARGE}{/if}</span>
            <img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._REMOVEFROMCART}" title = "{$smarty.const._REMOVEFROMCART}" onclick = "removeFromCart(this, '{$cartlist.id}', 'course');" id = "{$cartlist.id}">
        </div>
    &nbsp;</div>
    {/if}
{/foreach}        

{foreach name = 'cartlist' key = 'key' item = 'cartlist' from = $T_CART.lesson}
	{if $cartlist.recurring}
    <div class = "cartElement">
    	<div class = "cartTitle">{$cartlist.name}</div>
        <div class = "cartDelete">
            <span>{if $cartlist.price}{$cartlist.price_string}{else}{$smarty.const._FREEOFCHARGE}{/if}</span>
            <img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._REMOVEFROMCART}" title = "{$smarty.const._REMOVEFROMCART}" onclick = "removeFromCart(this, '{$cartlist.id}', 'lesson');" id = "{$cartlist.id}">
		{if $T_PAYPAL_SUBSCRIPTION_FORMS.lesson[$cartlist.id]}
			{$T_PAYPAL_SUBSCRIPTION_FORMS.lesson[$cartlist.id].javascript}
			<form {$T_PAYPAL_SUBSCRIPTION_FORMS.lesson[$cartlist.id].attributes}>
			{$T_PAYPAL_SUBSCRIPTION_FORMS.lesson[$cartlist.id].hidden}
			{if $T_CONFIGURATION.paypaldebug}
				<table class = "formElements">					
				{foreach name = "paypal_form_loop" item = "item" key = "key" from = $T_PAYPAL_SUBSCRIPTION_FORMS.lesson[$cartlist.id]}
					{if $item|is_array}<tr><td class = "labelCell">{$item.name}:</td><td class = "elementCell">{$item.html}</td></tr>{/if}
				{/foreach}
				</table>
			{else}
				{$T_PAYPAL_SUBSCRIPTION_FORMS.lesson[$cartlist.id].submit_checkout_subscription.html}
			{/if}
			</form>
		{/if}
        </div>
    &nbsp;</div>
    {/if}
{/foreach}        
{foreach name = 'cartlist' key = 'key' item = 'cartlist' from = $T_CART.course}
	{if $cartlist.recurring}
    <div class = "cartElement">
    	<div class = "cartTitle">{$cartlist.name}</div>
        <div class = "cartDelete">
            <span>{if $cartlist.price}{$cartlist.price_string}{else}{$smarty.const._FREEOFCHARGE}{/if}</span>
            <img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._REMOVEFROMCART}" title = "{$smarty.const._REMOVEFROMCART}" onclick = "removeFromCart(this, '{$cartlist.id}', 'course');" id = "{$cartlist.id}">
		{if $T_PAYPAL_SUBSCRIPTION_FORMS.course[$cartlist.id]}
			{$T_PAYPAL_SUBSCRIPTION_FORMS.course[$cartlist.id].javascript}
			<form {$T_PAYPAL_SUBSCRIPTION_FORMS.course[$cartlist.id].attributes}>
			{$T_PAYPAL_SUBSCRIPTION_FORMS.course[$cartlist.id].hidden}
			{if $T_CONFIGURATION.paypaldebug}
				<table class = "formElements">					
				{foreach name = "paypal_form_loop" item = "item" key = "key" from = $T_PAYPAL_SUBSCRIPTION_FORMS.course[$cartlist.id]}
					{if $item|is_array}<tr><td class = "labelCell">{$item.name}:</td><td class = "elementCell">{$item.html}</td></tr>{/if}
				{/foreach}
				</table>
			{else}
				{$T_PAYPAL_SUBSCRIPTION_FORMS.course[$cartlist.id].submit_checkout_subscription.html}
			{/if}
			</form>
		{/if}
        </div>
    &nbsp;</div>
    {/if}
{/foreach}        
 
{if $T_CART.credit}
    <div class = "cartElement">
    	<div class = "cartTitle">{$smarty.const._CREDIT}</div>
        <div class = "cartDelete">
            <span>{$T_CART.credit}</span>
            <img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._REMOVEFROMCART}" title = "{$smarty.const._REMOVEFROMCART}" onclick = "removeFromCart(this, '{$cartlist.id}', 'credit');" id = "{$cartlist.id}">
        </div>
    &nbsp;</div>
{/if}
{if $T_CART.lesson || $T_CART.course || $T_CART.credit}
    <div class = "cartElement">
    	<div class = "cartDelete">
	        <span>{if $T_CART.total_price}{$smarty.const._TOTAL}: <span id = "total_price_string">{$T_CART.total_price_string}</span> - {/if}{$smarty.const._REMOVEALL}</span>
	        <img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._REMOVEALLFROMCART}" title = "{$smarty.const._REMOVEALLFROMCART}" onclick = "removeAllFromCart(this);">
        </div>
    &nbsp;</div>
    {if $T_CONFIGURATION.voucher && $smarty.get.checkout}
   	<div class = "cartElement">
   		<div class = "cartDelete">
    	{if $T_VOUCHER_PRICE}
    		{$smarty.const._TOTALPRICEAFTERDISCOUNT}: {$T_VOUCHER_PRICE}
    	{else}
	    	<span style = "display:none">
	    		<input name = "voucher_bogus" id = "voucher_bogus" type = "text">
	    		<img class = "ajaxHandle" src = "images/16x16/success.png" alt = "{$smarty.const._OK}" title = "{$smarty.const._OK}" onclick = "updateVoucher(this)"/>
    		</span>
    		<a href = "javascript:void(0)" onclick = "Element.extend(this).previous().show();this.hide()">{$smarty.const._CLICKTOENTERDISCOUNTVOUCHER}</a>
    	{/if}
   		</div>
   	&nbsp;</div>
    {/if}
    {if $T_BALANCE}
    <div class = "cartElement">
    	<div class = "cartDelete">{$smarty.const._BALANCE}: {$T_BALANCE}</div>
    &nbsp;</div>
	{/if}
    
    <div id = "submit_cart">
    {if $T_CHECKOUT_FORM}
			{$T_CHECKOUT_FORM.javascript}
			<form {$T_CHECKOUT_FORM.attributes}>
			    {$T_CHECKOUT_FORM.hidden}
	    		{$T_CHECKOUT_FORM.voucher.html}
			    {$T_CHECKOUT_FORM.submit_order.html}
			    {$T_CHECKOUT_FORM.submit_checkout_balance.html}			    
			</form>    	
			
		{if $T_PAYPAL_FORM}
			{$T_PAYPAL_FORM.javascript}
			<form {$T_PAYPAL_FORM.attributes}>
			{$T_PAYPAL_FORM.hidden}
			{if $T_CONFIGURATION.paypaldebug}
				<table class = "formElements">					
				{foreach name = "paypal_form_loop" item = "item" key = "key" from = $T_PAYPAL_FORM}
					{if $item|is_array}<tr><td class = "labelCell">{$item.name}:</td><td class = "elementCell">{$item.html}</td></tr>{/if}
				{/foreach}
				</table>
			{else}
				{$T_PAYPAL_FORM.submit_checkout_paypal.html}
			{/if}
			</form>
		{/if}
    {else}
    	<input class = "flatButton" type = "submit" value = "{$smarty.const._CONTINUE}&nbsp;&raquo;" onclick = "buyRedirect()">
    {/if}
    </div>
{else}
	<div class = "emptyCategory">{$smarty.const._NODATAFOUND}</div>
{/if}
</div>

<script type = "text/javascript">
function buyRedirect() {ldelim}
	{if $smarty.session.s_login}
		{if $smarty.server.PHP_SELF|basename|replace:'.php':'' == 'index'}
			location = 'index.php?ctg=checkout&checkout=1&register_lessons=1';
		{else}
			location = '{$smarty.server.PHP_SELF}?ctg=lessons&catalog=1&checkout=1';		
		{/if}
	{else}
		location = 'index.php?ctg=login&register_lessons=1&message='+encodeURI('{$smarty.const._PLEASELOGINTOCOMPLETEREGISTRATION}')+'&message_type=success';
	{/if}
{rdelim}
</script>
{/strip}