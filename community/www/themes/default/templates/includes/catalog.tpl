<div id = "cart_{$cartlist.id}" class = "cart">
    <div class = "cartElement">
     <div class = "cartTitle">{$cartlist.name}</div>
        <div class = "cartDelete">
            <span>{$cartlist.price}</span>
            <a href = "javascript:void(0)" onclick = "ajaxPostRemove('{$cartlist.id}', this);" id = "{$cartlist.id}">
                <img src = "images/16x16/error_delete.png" alt = "{$smarty.const._REMOVEFROMCART}" title = "{$smarty.const._REMOVEFROMCART}"></a>
        </div>
    &nbsp;</div>
    <div id = "cart_total"></div>

    <form {$T_SUBSCRIPTION_FORMS[$key].attributes}>
        {$T_SUBSCRIPTION_FORMS[$key].hidden}
        <div style = "text-align:center;">{$T_SUBSCRIPTION_FORMS[$key].order.html}</div>
    </form>
    <form {$T_PAYPAL_SUBSCRIPTION_FORMS[$key].attributes}>
        {$T_PAYPAL_SUBSCRIPTION_FORMS[$key].hidden}
     <div style = "text-align:center;">{$T_PAYPAL_SUBSCRIPTION_FORMS[$key].order.html}</div>
    </form>
</div>
asd
