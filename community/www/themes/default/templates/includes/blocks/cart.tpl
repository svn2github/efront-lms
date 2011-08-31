<!--ajax:cart-->
{strip}
<div id = "cart" class = "cart">
{foreach name = 'cartlist' key = 'key' item = 'cartlist' from = $T_CART.lesson}
 {if !$cartlist.recurring}
    <div class = "cartElement">
     <div class = "cartTitle">{$cartlist.name}</div>
        <div class = "cartDelete">
            <span>{if $cartlist.price}{$cartlist.price_string}{elseif !$T_CONFIGURATION.disable_payments}{$smarty.const._FREEOFCHARGE}{/if}</span>
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
            <span>{if $cartlist.price}{$cartlist.price_string}{elseif !$T_CONFIGURATION.disable_payments}{$smarty.const._FREEOFCHARGE}{/if}</span>
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
            <span>{if $cartlist.price}{$cartlist.price_string}{elseif !$T_CONFIGURATION.disable_payments}{$smarty.const._FREEOFCHARGE}{/if}</span>
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
            <span>{if $cartlist.price}{$cartlist.price_string}{elseif !$T_CONFIGURATION.disable_payments}{$smarty.const._FREEOFCHARGE}{/if}</span>
            <img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._REMOVEFROMCART}" title = "{$smarty.const._REMOVEFROMCART}" onclick = "removeFromCart(this, '{$cartlist.id}', 'course');" id = "{$cartlist.id}">
        </div>
    &nbsp;</div>
    {/if}
{/foreach}

{if $T_CART.credit}
    <div class = "cartElement">
     <div class = "cartTitle">{$smarty.const._CREDIT}</div>
        <div class = "cartDelete">
            <span>{$T_CART.credit|formatPrice}</span>
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
    {if $smarty.get.checkout && !$T_CART.credit}
    <div class = "cartElement">
     <div class = "cartDelete">
     {if $T_CART.total_price != "" && $T_CART.total_price != 0}
      <div id = "coupon_table" style = "display:none">
      {capture name = "t_coupon_form_code"}
      <table>
       <tr><td class = "labelCell">{$smarty.const._COUPON}:&nbsp;</td>
        <td class = "elementCell"><input name = "coupon_bogus" id = "coupon_bogus" type = "text"></td></tr>
       <tr><td></td>
        <td class = "submitCell"><input class = "flatButton" type = "button" value = "submit" onclick = "updateCoupon(this)"></td></tr>
      </table>
      {/capture}
      {eF_template_printBlock title = $smarty.const._COUPON data = $smarty.capture.t_coupon_form_code image = '32x32/shopping_basket_add.png'}
      </div>



     {/if}
     </div>
    &nbsp;</div>
    {/if}
    {if $T_BALANCE && !$T_CART.credit}
    <div class = "cartElement">
     <div class = "cartDelete">{$smarty.const._BALANCE}: {$T_BALANCE}</div>
    &nbsp;</div>
 {/if}

    <div id = "submit_cart">
    {if $T_CHECKOUT_FORM}
   {$T_CHECKOUT_FORM.javascript}
   <form {$T_CHECKOUT_FORM.attributes}>
       {$T_CHECKOUT_FORM.hidden}
       {$T_CHECKOUT_FORM.coupon.html}
       {$T_CHECKOUT_FORM.submit_order.html}
       {$T_CHECKOUT_FORM.submit_checkout_balance.html}
   </form>
{*
  {if $T_PAYPAL_SUBSCRIPTION_FORM}
   {$T_PAYPAL_SUBSCRIPTION_FORM.javascript}
   <form {$T_PAYPAL_SUBSCRIPTION_FORM.attributes}>
   {$T_PAYPAL_SUBSCRIPTION_FORM.hidden}
   {if $T_CONFIGURATION.paypaldebug}
    <table class = "formElements">
    {foreach name = "paypal_form_loop" item = "item" key = "key" from = $T_PAYPAL_SUBSCRIPTION_FORM}
     {if $item|is_array}<tr><td class = "labelCell">{$item.name}:</td><td class = "elementCell">{$item.html}</td></tr>{/if}
    {/foreach}
    </table>
   {else}
    {$T_PAYPAL_SUBSCRIPTION_FORM.submit_checkout_subscription.html}
   {/if}
   </form>
  {/if}
*}
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
     <input class = "flatButton" type = "submit" value = "{$smarty.const._CONTINUE}&nbsp;&raquo;" onclick = "location=redirectLocation">
    {/if}
    </div>
{else}
 <div class = "emptyCategory">{$smarty.const._NODATAFOUND}</div>
{/if}
</div>

<script type = "text/javascript">
translations['_COUPON'] = '{$smarty.const._COUPON}';
translations['_CLICKTOENTERDISCOUNTCOUPON'] = '{$smarty.const._CLICKTOENTERDISCOUNTCOUPON}';
{if $smarty.session.s_login}
 {if $smarty.server.PHP_SELF|basename|replace:'.php':'' == 'index'}
  redirectLocation ='index.php?ctg=checkout&checkout=1&register_lessons=1';
 {else}
  redirectLocation ='{$smarty.server.PHP_SELF}?ctg=lessons&catalog=1&checkout=1';
 {/if}
{else}
 redirectLocation ='index.php?ctg=login&register_lessons=1';
{/if}
</script>
{/strip}
<!--/ajax:cart-->
