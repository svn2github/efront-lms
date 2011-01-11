 {capture name = "t_payment_coupons_code"}
   <div class = "headerTools">
    <span>
     <img src = "images/16x16/add.png" title = "{$smarty.const._ADDCOUPON}" alt = "{$smarty.const._ADDCOUPON}">
     <a href = "{$smarty.server.PHP_SELF}?ctg=payments&coupons=1&add=1&popup=1" title = "{$smarty.const._ADDCOUPON}" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._ADDCOUPON}', 2)">{$smarty.const._ADDCOUPON}</a>
    </span>
   </div>
<!--ajax:couponsTable-->
   <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "0" id = "couponsTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=payments&">
    <tr class = "topTitle defaultRowHeight">
     <td class = "topTitle" name = "code">{$smarty.const._COUPONCODE}</td>
     <td class = "topTitle centerAlign" name = "max_uses">{$smarty.const._MAXIMUMUSES}</td>
     <td class = "topTitle centerAlign" name = "max_user_uses">{$smarty.const._MAXIMUMUSESBYSINGLEUSER}</td>
     <td class = "topTitle" name = "from_timestamp">{$smarty.const._VALIDFROM}</td>
     <td class = "topTitle centerAlign" name = "duration">{$smarty.const._DURATION}</td>
     <td class = "topTitle centerAlign" name = "discount">{$smarty.const._DISCOUNT}</td>
     <td class = "topTitle centerAlign" name = "active">{$smarty.const._ACTIVE}</td>
     <td class = "topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
    </tr>
    {foreach name = 'users_list' key = 'key' item = 'coupon' from = $T_DATA_SOURCE}
    <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
     <td><a class = "editLink" href = "{$smarty.server.PHP_SELF}?ctg=payments&coupons=1&edit={$coupon.id}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._EDIT}', 2)">{$coupon.code}</a></td>
     <td class = "centerAlign">{if $coupon.max_uses}{$coupon.max_uses}{else}{$smarty.const._UNLIMITED}{/if}</td>
     <td class = "centerAlign">{if $coupon.max_uses}{$coupon.max_user_uses}{else}{$smarty.const._UNLIMITED}{/if}</td>
     <td>#filter:timestamp_time-{$coupon.from_timestamp}#</td>
     <td class = "centerAlign">{$coupon.duration} {$smarty.const._DAYS}</td>
     <td class = "centerAlign">{$coupon.discount} %</td>
     <td class = "centerAlign">
      <img {if $coupon.active == 0}style = "display:none"{/if} class = "ajaxHandle" src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._DEACTIVATE}" title = "{$smarty.const._DEACTIVATE}" onclick = "deactivateEntity(this, '{$coupon.id}', {ldelim}coupons:1{rdelim});">
      <img {if $coupon.active == 1}style = "display:none"{/if} class = "ajaxHandle" src = "images/16x16/trafficlight_red.png" alt = "{$smarty.const._ACTIVATE}" title = "{$smarty.const._ACTIVATE}" onclick = "activateEntity(this, '{$coupon.id}', {ldelim}coupons:1{rdelim})">
     </td>
     <td class = "centerAlign">
      <a href = "{$smarty.server.PHP_SELF}?ctg=payments&coupons=1&reports={$coupon.id}&popup=1" target = "POPUP_FRAME"><img class = "handle" src = "images/16x16/reports.png" title = "{$smarty.const._reports}" alt = "{$smarty.const._REPORTS}" onclick = "eF_js_showDivPopup('{$smarty.const._REPORTS}', 2)"/></a>
      <a href = "{$smarty.server.PHP_SELF}?ctg=payments&coupons=1&edit={$coupon.id}&popup=1" target = "POPUP_FRAME"><img class = "handle" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" onclick = "eF_js_showDivPopup('{$smarty.const._EDIT}', 2)"/></a>
      <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) deleteEntity(this, '{$coupon.id}', {ldelim}coupons:1{rdelim})"/>
     </td>
    </tr>
    {foreachelse}
    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "6">{$smarty.const._NODATAFOUND}</td></tr>
    {/foreach}
   </table>
<!--/ajax:couponsTable-->
 {/capture}

{if $smarty.get.add || $smarty.get.edit}
 {if $smarty.get.coupons}
  {capture name = 't_add_code'}
  {$T_ENTITY_FORM.javascript}
  <form {$T_ENTITY_FORM.attributes}>
   {$T_ENTITY_FORM.hidden}
   <table class = "formElements">
    <tr><td class = "labelCell">{$T_ENTITY_FORM.code.label}:&nbsp;</td>
     <td class = "elementCell">{$T_ENTITY_FORM.code.html} <img class = "ajaxHandle" src = "images/16x16/wizard.png" alt = "{$smarty.const._CREATEUNIQUECOUPON}" title = "{$smarty.const._CREATEUNIQUECOUPON}" onclick = "createCouponCode(this)" /></td></tr>
    <tr><td class = "labelCell">{$T_ENTITY_FORM.max_uses.label}:&nbsp;</td>
     <td class = "elementCell">{$T_ENTITY_FORM.max_uses.html} {$smarty.const._BLANKFORUNLIMITED}</td></tr>
    <tr><td class = "labelCell">{$T_ENTITY_FORM.max_user_uses.label}:&nbsp;</td>
     <td class = "elementCell">{$T_ENTITY_FORM.max_user_uses.html} {$smarty.const._BLANKFORUNLIMITED}</td></tr>
    <tr><td class = "labelCell">{$T_ENTITY_FORM.from_timestamp.label}:&nbsp;</td>
     <td class = "elementCell">{eF_template_html_select_date prefix="from_timestamp_" time = $T_ENTITY_FORM.from_timestamp start_year="-1" end_year="+2" field_order = $T_DATE_FORMATGENERAL}</td></tr>
    <tr><td class = "labelCell">{$T_ENTITY_FORM.duration.label}:&nbsp;</td>
     <td class = "elementCell">{$T_ENTITY_FORM.duration.html} {$smarty.const._DAYS}</td></tr>
    <tr><td class = "labelCell">{$T_ENTITY_FORM.discount.label}:&nbsp;</td>
     <td class = "elementCell">{$T_ENTITY_FORM.discount.html} %</td></tr>
    <tr><td class = "labelCell">{$T_ENTITY_FORM.active.label}:&nbsp;</td>
     <td class = "elementCell">{$T_ENTITY_FORM.active.html}</td></tr>
    <tr><td class = "labelCell">{$T_ENTITY_FORM.description.label}:&nbsp;</td>
     <td class = "elementCell">{$T_ENTITY_FORM.description.html}</td></tr>
    <tr><td></td>
     <td class = "submitCell">{$T_ENTITY_FORM.submit_coupon.html}</td>
    </tr>
   </table>
  </form>
  {if $T_MESSAGE_TYPE == 'success'}
   <script>parent.location = parent.location+'&tab=coupons';</script>
  {/if}
  {/capture}
  {eF_template_printBlock title = $smarty.const._COUPONPROPERTIES data = $smarty.capture.t_add_code image = '32x32/shopping_basket_add.png'}

 {else}
  {capture name = 't_add_code'}
   {$T_ENTITY_FORM.javascript}
   <form {$T_ENTITY_FORM.attributes}>
    {$T_ENTITY_FORM.hidden}
    <table class = "formElements">
     <tr><td class = "labelCell">{$smarty.const._DATE}:&nbsp;</td>
      <td class = "elementCell">{eF_template_html_select_date prefix="payment_" start_year="-1" end_year="+2" field_order = $T_DATE_FORMATGENERAL} {$smarty.const._TIME}: {html_select_time prefix="payment_" display_seconds = false}</td></tr>
     <tr><td class = "labelCell">{$smarty.const._USER}:&nbsp;</td>
      <td class = "elementCell">
       <input type = "text" id = "autocomplete" class = "autoCompleteTextBox" name = "user"/>
       <img id = "busy" src = "images/16x16/clock.png" style = "display:none;" alt = "{$smarty.const._LOADING}" title = "{$smarty.const._LOADING}"/>
       <div id = "autocomplete_users" class = "autocomplete"></div>&nbsp;&nbsp;&nbsp;
      </td>
     </tr>
     <tr><td></td>
      <td class = "infoCell">{$smarty.const._STARTTYPINGFORRELEVENTMATCHES}</td>
     </tr>
     <tr><td class = "labelCell">{$T_ENTITY_FORM.amount.label} ({$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}):&nbsp;</td>
      <td class = "elementCell">{$T_ENTITY_FORM.amount.html}</td></tr>
     <tr><td></td>
      <td><span>
        <img style="vertical-align:middle" src = "images/16x16/order.png" title = "{$smarty.const._TOGGLEHTMLEDITORMODE}" alt = "{$smarty.const._TOGGLEHTMLEDITORMODE}" />&nbsp;
        <a href = "javascript:toggleEditor('comments','simpleEditor');" id = "toggleeditor_link">{$smarty.const._TOGGLEHTMLEDITORMODE}</a>
       </span></td></tr>
     <tr><td class = "labelCell">{$T_ENTITY_FORM.comments.label}:&nbsp;</td>
      <td class = "elementCell">{$T_ENTITY_FORM.comments.html}</td></tr>
     <tr><td></td>
      <td class = "submitCell">{$T_ENTITY_FORM.submit.html}</td></tr>
    </table>
   </form>
  {if $T_MESSAGE_TYPE == 'success'}
   <script>parent.location = parent.location;</script>
  {/if}
  {/capture}

  {eF_template_printBlock title = $smarty.const._PAYMENT data = $smarty.capture.t_add_code image = '32x32/shopping_basket_add.png'}
 {/if}

{elseif $smarty.get.reports}
 {capture name = "t_coupon_reports_code"}
  <table class = "statisticsGeneralInfo">
   <tr class = "{cycle name = 'common_lesson_info' values = 'oddRowColor, evenRowColor'}">
    <td class = "labelCell">{$smarty.const._COUPONCODE}:</td>
    <td class = "elementCell"><b>{$T_COUPON->coupons.code}</b></td>
   </tr>
   <tr class = "{cycle name = 'common_lesson_info' values = 'oddRowColor, evenRowColor'}">
    <td class = "labelCell">{$smarty.const._MAXIMUMUSES}:</td>
    <td class = "elementCell"><b>{if $T_COUPON->coupons.max_uses}{$T_COUPON->coupons.max_uses}{else}{$smarty.const._UNLIMITED}{/if}</b></td>
   </tr>
   <tr class = "{cycle name = 'common_lesson_info' values = 'oddRowColor, evenRowColor'}">
    <td class = "labelCell">{$smarty.const._USESSOFAR}:</td>
    <td class = "elementCell"><b>{$T_COUPON_STATS.total_uses}</b></td>
   </tr>
   <tr class = "{cycle name = 'common_lesson_info' values = 'oddRowColor, evenRowColor'}">
    <td class = "labelCell">{$smarty.const._USESREMAINING}:</td>
    <td class = "elementCell"><b>{if $T_COUPON->coupons.max_uses}{$T_COUPON_STATS.remaining_uses}{else}{$smarty.const._UNLIMITED}{/if}</b></td>
   </tr>
   <tr class = "{cycle name = 'common_lesson_info' values = 'oddRowColor, evenRowColor'}">
    <td class = "labelCell">{$smarty.const._VALIDFROM}:</td>
    <td class = "elementCell"><b>#filter:timestamp-{$T_COUPON->coupons.from_timestamp}#</b></td>
   </tr>
   {if $T_COUPON->coupons.duration}
   <tr class = "{cycle name = 'common_lesson_info' values = 'oddRowColor, evenRowColor'}">
    <td class = "labelCell">{$smarty.const._VALIDUNTIL}:</td>
    <td class = "elementCell"><b>#filter:timestamp-{$T_COUPON_STATS.valid_until}#</b>{if $T_COUPON_STATS.expired} ({$smarty.const._EXPIRED}){/if}</td>
   </tr>
   {/if}
  </table>

<!--ajax:usersCouponsTable-->
   <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "0" id = "usersCouponsTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=payments&coupons=1&reports={$smarty.get.reports}&">
    <tr class = "topTitle defaultRowHeight">
     <td class = "topTitle" name = "login">{$smarty.const._USER}</td>
     <td class = "topTitle" name = "timestamp">{$smarty.const._DATE}</td>
     <td class = "topTitle noSort">{$smarty.const._COURSES}</td>
     <td class = "topTitle noSort">{$smarty.const._LESSONS}</td>
    </tr>
    {foreach name = 'items_list' key = 'key' item = 'item' from = $T_DATA_SOURCE}
    <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
     <td>#filter:login-{$item.login}#</td>
     <td>#filter:timestamp_time-{$item.timestamp}#</td>
     <td>{$T_COUPON_COURSES[$item.id]|@implode:', '}</td>
     <td>{$T_COUPON_LESSONS[$item.id]|@implode:', '}</td>
    </tr>
    {foreachelse}
    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "5">{$smarty.const._NODATAFOUND}</td></tr>
    {/foreach}
   </table>
<!--/ajax:usersCouponsTable-->
 {/capture}
 {eF_template_printBlock title = $smarty.const._REPORTS data = $smarty.capture.t_coupon_reports_code image = '32x32/reports.png'}

{else}
 {capture name = "t_payments_history_code"}
   <div class = "headerTools">
    <span>
     <img src = "images/16x16/add.png" title = "{$smarty.const._ADDMANUALPAYMENT}" alt = "{$smarty.const._ADDMANUALPAYMENT}">
     <a href = "{$smarty.server.PHP_SELF}?ctg=payments&add=1&popup=1" title = "{$smarty.const._ADDMANUALPAYMENT}" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._ADDMANUALPAYMENT}', 2)">{$smarty.const._ADDMANUALPAYMENT}</a>
    </span>
    {if $T_TRANSACTIONS_LOG_FILE}
    <span>
     <img src = "images/16x16/search.png" title = "{$smarty.const._VIEWTRANSACTIONLOGFILE}" alt = "{$smarty.const._VIEWTRANSACTIONLOGFILE}">
     <a href = "view_file.php?file={$T_TRANSACTIONS_LOG_FILE}&download=1" title = "{$smarty.const._VIEWTRANSACTIONLOGFILE}" >{$smarty.const._VIEWTRANSACTIONLOGFILE}</a>
    </span>
    {/if}
   </div>
<!--ajax:paymentsTable-->
   <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "0" id = "paymentsTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=payments&">
    <tr class = "topTitle defaultRowHeight">
     <td class = "topTitle" name = "timestamp">{$smarty.const._DATE}</td>
     <td class = "topTitle" name = "users_LOGIN">{$smarty.const._USER}</td>
     <td class = "topTitle centerAlign" name = "amount">{$smarty.const._AMOUNT}</td>
     <td class = "topTitle" name = "method">{$smarty.const._METHOD}</td>
     <td class = "topTitle" name = "status">{$smarty.const._STATUS}</td>
     <td class = "topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
    </tr>
    {foreach name = 'users_list' key = 'key' item = 'payment' from = $T_DATA_SOURCE}
    <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
     <td>#filter:timestamp_time-{$payment.timestamp}#</td>
     <td>#filter:login-{$payment.users_LOGIN}#</td>
     <td class = "centerAlign">{$payment.amount|formatPrice}</td>
     <td>{$T_PAYMENT_METHODS[$payment.method]}</td>
     <td>{$payment.status}</td>
     <td class = "centerAlign">
      <div style = "display:none" id = "details_div_{$payment.id}">
       {eF_template_printBlock title = $smarty.const._DETAILS data = "<pre>`$payment.comments`</pre>" image='32x32/information.png'}
      </div>
      <img class = "handle" src = "images/16x16/information.png" title = "{$smarty.const._DETAILS}" alt = "{$smarty.const._DETAILS}" onclick = "eF_js_showDivPopup('{$smarty.const._DETAILS}', 1, 'details_div_{$payment.id}')"/>
      <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) deleteEntity(this, '{$payment.id}')"/>
     </td>
    </tr>
    {foreachelse}
    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "6">{$smarty.const._NODATAFOUND}</td></tr>
    {/foreach}
   </table>
<!--/ajax:paymentsTable-->
 {/capture}

 {capture name = 't_payment_accounts_code'}
<!--ajax:usersTable-->
   <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "0" id = "usersTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=payments&">
    <tr class = "topTitle defaultRowHeight">
     <td class = "topTitle">{$smarty.const._USER}</td>
     <td class = "topTitle centerAlign">{$smarty.const._BALANCE}</td>
    </tr>
    {foreach name = 'users_list' key = 'key' item = 'user' from = $T_DATA_SOURCE}
    <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
     <td>#filter:login-{$user.login}#</td>
     <td class = "centerAlign">
      <span style = "display:none">
       <input type = "text" value = "{$user.balance}">
       <img class = "handle" src = "images/16x16/success.png" title = "{$smarty.const._OK}" alt = "{$smarty.const._OK}" onclick = "editBalance(this, '{$user.login}')"/>
       <img class = "handle" src = "images/16x16/error_delete.png" title = "{$smarty.const._CANCEL}" alt = "{$smarty.const._CANCEL}" onclick = "Element.extend(this).up().hide();this.up().next().show();"/>
      </span>
      <span>
       <span>{$user.balance|formatPrice}</span>
       <img class = "handle" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" onclick = "Element.extend(this).up().hide();this.up().previous().show();"/>
      </span>
     </td>
    </tr>
    {foreachelse}
    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "5">{$smarty.const._NODATAFOUND}</td></tr>
    {/foreach}
   </table>
<!--/ajax:usersTable-->
 {/capture}

 {capture name = 't_payment_settings_code'}
  {$T_CONFIG_FORM_DEFAULT.javascript}
  <form {$T_CONFIG_FORM_DEFAULT.attributes}>
   {$T_CONFIG_FORM_DEFAULT.hidden}
   <table class = "formElements">
    <tr><td class = "labelCell">{$smarty.const._CURRENCY}:&nbsp;</td>
     <td class = "elementCell">{$T_CONFIG_FORM_DEFAULT.currency.html}</td></tr>
    <tr><td class = "labelCell">{$T_CONFIG_FORM_DEFAULT.currency_order.label}:&nbsp;</td>
     <td class = "elementCell">{$T_CONFIG_FORM_DEFAULT.currency_order.html}</td></tr>
    <tr><td class = "labelCell">{$T_CONFIG_FORM_DEFAULT.enable_balance.label}:&nbsp;</td>
     <td class = "elementCell">{$T_CONFIG_FORM_DEFAULT.enable_balance.html}</td></tr>
    <tr><td class = "labelCell">{$T_CONFIG_FORM_DEFAULT.enable_cart.label}:&nbsp;</td>
     <td class = "elementCell">{$T_CONFIG_FORM_DEFAULT.enable_cart.html}</td></tr>
    <tr><td class = "labelCell">{$T_CONFIG_FORM_DEFAULT.total_discount.label} (%):&nbsp;</td>
     <td class = "elementCell">{$T_CONFIG_FORM_DEFAULT.total_discount.html}</td></tr>
    <tr><td class = "labelCell">{$smarty.const._DISCOUNTSTARTSAT}:&nbsp;</td>
     <td class = "elementCell">{eF_template_html_select_date prefix="discount_" time = $T_CONFIGURATION.discount_start start_year="-1" end_year="+2" field_order = $T_DATE_FORMATGENERAL} {$smarty.const._ANDLASTS} {$T_CONFIG_FORM_DEFAULT.discount_period.html} {$smarty.const._DAYS}</td></tr>
    <tr><td class = "labelCell">{$T_CONFIG_FORM_DEFAULT.paypalbusiness.label}:&nbsp;</td>
     <td class = "elementCell">{$T_CONFIG_FORM_DEFAULT.paypalbusiness.html}</td></tr>
    <tr><td class = "labelCell">{$T_CONFIG_FORM_DEFAULT.paypalmode.label}:&nbsp;</td>
     <td class = "elementCell">{$T_CONFIG_FORM_DEFAULT.paypalmode.html}</td></tr>
    <tr><td class = "labelCell">{$T_CONFIG_FORM_DEFAULT.paypaldebug.label}:&nbsp;</td>
     <td class = "elementCell">{$T_CONFIG_FORM_DEFAULT.paypaldebug.html}</td></tr>
    <tr><td></td>
     <td class = "submitCell">{$T_CONFIG_FORM_DEFAULT.submit_config.html}</td>
    </tr>
   </table>
  </form>
 {/capture}

 {capture name = 't_payments_code'}
 <div class = "tabber">
  <div class = "tabbertab" title = "{$smarty.const._HISTORY}">
   {eF_template_printBlock title = $smarty.const._HISTORY data = $smarty.capture.t_payments_history_code image='32x32/generic.png'}
  </div>
  <div class = "tabbertab {if $smarty.get.tab == 'balance'}tabbertabdefault{/if}" title = "{$smarty.const._BALANCE}">
   {eF_template_printBlock title = $smarty.const._BALANCE data = $smarty.capture.t_payment_accounts_code image='32x32/users.png'}
  </div>
{*
  <div class = "tabbertab {if $smarty.get.tab == 'paypal'}tabbertabdefault{/if}" title = "{$smarty.const._PAYPAL}">
   {eF_template_printBlock title = $smarty.const._PAYPALTITLE data = $smarty.capture.t_paypal_data image='32x32/paypal.png'}
  </div>
*}
  <div class = "tabbertab {if $smarty.get.tab == 'coupons'}tabbertabdefault{/if}" title = "{$smarty.const._COUPONS}">
   {eF_template_printBlock title = $smarty.const._COUPONS data = $smarty.capture.t_payment_coupons_code image='32x32/shopping_basket.png'}
  </div>
  <div class = "tabbertab {if $smarty.get.tab == 'settings'}tabbertabdefault{/if}" title = "{$smarty.const._SETTINGS}">
   {eF_template_printBlock title = $smarty.const._SETTINGS data = $smarty.capture.t_payment_settings_code image='32x32/settings.png'}
  </div>
 </div>
 {/capture}
 {eF_template_printBlock title = $smarty.const._PAYMENTS data = $smarty.capture.t_payments_code image='32x32/shopping_basket.png' help = 'Payments'}
{/if}
