{capture name = "t_additional_accounts_code"}
 <div class = "headerTools">
  <span>
   <img src = "images/16x16/add.png" alt = "{$smarty.const._ADDACCOUNT}" title = "{$smarty.const._ADDACCOUNT}">
   <a href = "javascript:void(0)" onclick = "$('add_account').show();">{$smarty.const._ADDACCOUNT}</a>
  </span>
 </div>
 <div id = "add_account" style = "display:none">
  {$smarty.const._LOGIN}: <input type = "text" name = "account_login" id = "account_login">
  {$smarty.const._PASSWORD}: <input type = "password" name = "account_password" id = "account_password">
  <img class = "ajaxHandle" src = "images/16x16/success.png" alt = "{$smarty.const._ADD}" title = "{$smarty.const._ADD}" onclick = "addAccount(this)">
  <img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._CANCEL}" title = "{$smarty.const._CANCEL}" onclick = "$('add_account').hide();">
 </div>
 <br/>
 <fieldset class = "fieldsetSeparator">
  <legend>{$smarty.const._ADDITIONALACCOUNTS}</legend>
  <table id = "additional_accounts">
  {foreach name = 'additional_accounts_list' item = "item" key = "key" from = $T_ADDITIONAL_ACCOUNTS}
   <tr><td>#filter:login-{$item}#&nbsp;</td>
    <td><img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._DELETEACCOUNT}" title = "{$smarty.const._DELETEACCOUNT}" onclick = "deleteAccount(this, '{$item}')"></td>
  {foreachelse}
   <tr id = "empty_accounts"><td class = "emptyCategory">{$smarty.const._YOUHAVENTSETADDITIONALACCOUNTS}</td></tr>
  {/foreach}
  </table>
 </fieldset>

 {if $T_FACEBOOK_ENABLED}
 <fieldset class = "fieldsetSeparator" id = "facebook_accounts">
  <legend>{$smarty.const._FACEBOOKMAPPEDACCOUNT}</legend>
  <table id = "additional_accounts">
  {if $T_FB_ACCOUNT}
   <tr><td>{$T_FB_ACCOUNT.fb_name}&nbsp;</td>
    <td><img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._DELETEACCOUNT}" title = "{$smarty.const._DELETEACCOUNT}" onclick = "deleteFacebookAccount(this, '{$T_FB_ACCOUNT.users_LOGIN}')"></td>
  {else}
   <tr><td class = "emptyCategory">{$smarty.const._YOUHAVENTSETFACEBOOKACCOUNT}</td></tr>
  {/if}
  </table>
 </fieldset>
 {/if}
{/capture}
{eF_template_printBlock title = $smarty.const._MAPPEDACCOUNTS data = $smarty.capture.t_additional_accounts_code image = '32x32/user_mapping.png'}
