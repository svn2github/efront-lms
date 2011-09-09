{capture name="mod_fm_admin_cpanel_content"}
<div style="height:255px;width:100%;position:relative;">
<div id="_mod_fm_admin_mask" style="position:absolute; top:0px; left:0px; width:100%; display:none;">
 <div style="filter:alpha(opacity=35); -moz-opacity:0.35; -khtml-opacity: 0.35; opacity: 0.35; width:100%; height:255px; z-index:100; background:#AAAAAA; position:absolute; top:0px; left:0px; display:inherit;">&nbsp;</div>
 <div style="z-index:101;position:absolute;top:0px;left:0px; width:100%; height:255px; display:inherit;">
  <table cellpadding="0" cellspacing="0" style="width:100%; height:255px;">
   <tr valign="middle">
    <td align="center">
     <img src="{$MOD_FM_BASELINK}images/loader-blue.gif"/>
    </td>
   </tr>
  </table>
 </div>
</div>
<table cellpadding="0" cellspacing="0" style="width:100%;">
 <tr>
  <td style="height:150px;">
   <div id="mod_fm_upper_area" style="width:100%; height:150px;">
    <table cellpadding="0" cellspacing="0" style="width:100%;">
     <tr>
      <td>
       <div id="mod_fm_account_amount" style="width:100%;">{$MOD_FM_ADMIN_CPANEL_ACCOUNTS_AMOUNT} {$smarty.const._FUZE_ADMIN_USER_AMOUNT}</div>
      </td>
     </tr>
     <tr>
      <td>
       <select id="mod_fm_admin_account_select" name="mod_fm_account_select" onchange="javascript:_mod_fm_get_user_data();">
        <option value="0">{$smarty.const._FUZE_ADMIN_USER_SELECT_DEFAULT}</option>
        {foreach name='acounts_loop' key='account_id' item="account" from=$MOD_FM_ADMIN_CPANEL_ACCOUNTS}
         <option value="{$account.id}">{$account.name}</option>
        {/foreach}
       </select>
      </td>
     </tr>
     <tr valign="middle">
      <td align="center" style="padding-top:10px;">
       <div id="mod_fm_account_data" style="width:100%; vertical-align:middle;">&nbsp;</div>
      </td>
     </tr>
    </table>
   </div>

  </td>
 </tr>
 <tr>
  <td style="padding:1px;">
   <input type="button" class="flatButton inactiveElement" id="mod_fm_admin_user_create_btn" value="{$smarty.const._FUZE_ADMIN_USER_CREATE_BTN}" onclick="javascript:_mod_fm_user_create();" disabled/>
  </td>
 </tr>
 <tr>
  <td style="padding:1px;">
   <input type="button" class="flatButton inactiveElement" id="mod_fm_admin_user_remove_btn" value="{$smarty.const._FUZE_ADMIN_USER_SUSPEND_BTN}" onclick="javascript:_mod_fm_user_suspend();" disabled/>
  </td>
 </tr>
 <tr>
  <td style="padding:1px;">
   <input type="button" class="flatButton inactiveElement" id="mod_fm_admin_user_login_btn" value="{$smarty.const._FUZE_ADMIN_USER_LOGIN_BTN}" onclick="javascript:_mod_fm_user_login();" disabled/>
  </td>
 </tr>
</table>
</div>
{/capture}
{eF_template_printBlock title=$smarty.const._FUZE_MEETINGS data=$smarty.capture.mod_fm_admin_cpanel_content image= $T_RSS_MODULE_BASELINK|cat:'images/rss32.png' absoluteImagePath = 1 help = 'Fuze_Meeting'}

<script>
{literal}

 var progressHTML = '<table cellpadding="0" cellspacing="0" style="width:100%;height:100%;"><tr valign="middle"><td align="center"><img src="{/literal}{$MOD_FM_BASELINK}{literal}images/loader-blue.gif"/></td></tr></table>';
 var error_unknown = '<table cellpadding="0" cellspacing="0" style="width:100%;height:100%;"><tr valign="middle"><td align="center"><span style="font-size:14px; color:#AA0000;">{/literal}{$smarty.const._FUZE_ADMIN_ERROR_REGISTRATION_UNKNOWN}{literal}</span></td></tr></table>';
 var account_data_loader = '<img src="{/literal}{$MOD_FM_BASELINK}{literal}images/loader-blue.gif"/>';

 var account_data_user_not_found = '{/literal}{$smarty.const._FUZE_ADMIN_USER_NOT_FOUND}{literal}';
 var account_data_user_not_suspended = '{/literal}{$smarty.const._FUZE_ADMIN_USER_NOT_SUSPENDED}{literal}';
 var account_data_user_count_suffix = ' {/literal}{$smarty.const._FUZE_ADMIN_USER_AMOUNT}{literal}';
 var account_data_error = '{/literal}{$smarty.const._FUZE_ADMIN_ERROR_COULDNT_GET_USER_DATA}{literal}';

 var account_data_user_found = '';
 account_data_user_found += '<table cellpadding="0" cellspacing="0" style="width:100%; text-align:left;">';
 account_data_user_found += '	<tr>';
 account_data_user_found += '		<td style="padding-top:5px; width:160px;">{/literal}{$smarty.const._FUZE_ADMIN_USER_DATA_EMAIL}{literal}:&nbsp;&nbsp;</td>';
 account_data_user_found += '		<td style="padding-top:5px;"><code><div id="mod_fm_account_data_email"></div></code></td>';
 account_data_user_found += '	</tr>';
 account_data_user_found += '	<tr>';
 account_data_user_found += '		<td style="padding-top:5px;">{/literal}{$smarty.const._FUZE_ADMIN_USER_DATA_PASSWORD}{literal}:&nbsp;&nbsp;</td>';
 account_data_user_found += '		<td style="padding-top:5px;"><code><div id="mod_fm_account_data_password"></div></code></td>';
 account_data_user_found += '	</tr>';
 account_data_user_found += '	<tr>';
 account_data_user_found += '		<td style="padding-top:5px;">{/literal}{$smarty.const._FUZE_ADMIN_USER_DATA_DATE_ADDED}{literal}:&nbsp;&nbsp;</td>';
 account_data_user_found += '		<td style="padding-top:5px;"><code><div id="mod_fm_account_data_date_added"></div></code></td>';
 account_data_user_found += '	</tr>';
 account_data_user_found += '</table>';
 account_data_user_found += '<input type="hidden" id="mod_fm_account_data_login_url" value=""/>';

 function _mod_fm_admin_mask_show() {
  $('_mod_fm_admin_mask').show();
 }

 function _mod_fm_admin_mask_hide() {
  $('_mod_fm_admin_mask').hide();
 }

 function _mod_fm_admin_disable_elements() {
  // The interface buttons
  $('mod_fm_admin_user_create_btn').disabled = true;
  $('mod_fm_admin_user_create_btn').className = 'flatButton inactiveElement';
  $('mod_fm_admin_user_remove_btn').disabled = true;
  $('mod_fm_admin_user_remove_btn').className = 'flatButton inactiveElement';
  $('mod_fm_admin_user_login_btn').disabled = true;
  $('mod_fm_admin_user_login_btn').className = 'flatButton inactiveElement';

  // The interface select element
  $('mod_fm_admin_account_select').disable = true;
 }

 function _mod_fm_get_user_data() {
  var id = $('mod_fm_admin_account_select').getValue();
  var el = $('mod_fm_account_data');
  el.innerHTML = '';
  _mod_fm_admin_disable_elements();
  if (id != '0') {
   var url = '{/literal}{$MOD_FM_BASEURL}{literal}&action=user_show&local_id='+id;
   // Mask on
   _mod_fm_admin_mask_show();
   new Ajax.Request(url, {
    method: 'get',
    asynchronous: true,
    onFailure: function() {
     el.innerHTML = error_unknown;
     _mod_fm_admin_mask_hide();
    },
    onSuccess: function(response) {
     var response = response.responseText.evalJSON();
     if (response.success) {
      el.innerHTML = account_data_user_found;
      $('mod_fm_account_data_email').innerHTML = response.fuze_email;
      $('mod_fm_account_data_password').innerHTML = response.fuze_password;
      $('mod_fm_account_data_date_added').innerHTML = response.date_added;
      $('mod_fm_account_data_login_url').value = response.login_url;
      $('mod_fm_admin_user_remove_btn').disabled = false;
      $('mod_fm_admin_user_remove_btn').className = 'flatButton';
      $('mod_fm_admin_user_login_btn').disabled = false;
      $('mod_fm_admin_user_login_btn').className = 'flatButton';

      // Changing the user count
      if (response.active_user_count != undefined) {
       $('mod_fm_account_amount').innerHTML = response.active_user_count + account_data_user_count_suffix;
      }
     }
     else {
      el.innerHTML = account_data_user_not_found;
      $('mod_fm_admin_user_create_btn').disabled = false;
      $('mod_fm_admin_user_create_btn').className = 'flatButton';
      if (response.error_msg) {
       alert(response.error_msg);
      }
     }
     _mod_fm_admin_mask_hide();
    }
   });
  }
 }

 function _mod_fm_user_create() {
  var id = $('mod_fm_admin_account_select').getValue();
  var el = $('mod_fm_account_data');
  el.innerHTML = '';
  _mod_fm_admin_disable_elements();
  if (id != '0') {
   var url = '{/literal}{$MOD_FM_BASEURL}{literal}&action=user_create&local_id='+id;
   // Mask on
   _mod_fm_admin_mask_show();
   new Ajax.Request(url, {
    method: 'get',
    asynchronous: true,
    onFailure: function() {
     el.innerHTML = error_unknown;
    },
    onSuccess: function(response) {
     var response = response.responseText.evalJSON();
     if (response.success) {
      el.innerHTML = account_data_user_found;
      $('mod_fm_account_data_email').innerHTML = response.fuze_email;
      $('mod_fm_account_data_password').innerHTML = response.fuze_password;
      $('mod_fm_account_data_date_added').innerHTML = response.date_added;
      $('mod_fm_account_data_login_url').value = response.login_url;
      $('mod_fm_admin_user_remove_btn').disabled = false;
      $('mod_fm_admin_user_remove_btn').className = 'flatButton';
      $('mod_fm_admin_user_login_btn').disabled = false;
      $('mod_fm_admin_user_login_btn').className = 'flatButton';
      // Changing the user count
      if (response.active_user_count != undefined) {
       $('mod_fm_account_amount').innerHTML = response.active_user_count + account_data_user_count_suffix;
      }
     }
     else {
      if (response.error_msg) {
       alert(response.error_msg);
      }
      $('mod_fm_admin_user_create_btn').disabled = false;
      $('mod_fm_admin_user_create_btn').className = 'flatButton';
     }
     _mod_fm_admin_mask_hide();
    }
   });
  }
 }

 function _mod_fm_user_suspend() {
  var id = $('mod_fm_admin_account_select').getValue();
  var el = $('mod_fm_account_data');
  el.innerHTML = '';
  _mod_fm_admin_disable_elements();
  if (id != '0') {
   var url = '{/literal}{$MOD_FM_BASEURL}{literal}&action=user_suspend&local_id='+id;
   // Mask on
   _mod_fm_admin_mask_show();
   new Ajax.Request(url, {
    method: 'get',
    asynchronous: true,
    onFailure: function() {
     el.innerHTML = error_unknown;
    },
    onSuccess: function(response) {
     var response = response.responseText.evalJSON();
     if (response.success) {
      el.innerHTML = account_data_user_not_found;
      $('mod_fm_admin_user_create_btn').disabled = false;
      $('mod_fm_admin_user_create_btn').className = 'flatButton';
      // Changing the user count
      if (response.active_user_count != undefined) {
       $('mod_fm_account_amount').innerHTML = response.active_user_count + account_data_user_count_suffix;
      }
     }
     else {
      el.innerHTML = account_data_user_not_suspended;
      $('mod_fm_admin_user_remove_btn').disabled = false;
      $('mod_fm_admin_user_remove_btn').className = 'flatButton';
      $('mod_fm_admin_user_login_btn').disabled = false;
      $('mod_fm_admin_user_login_btn').className = 'flatButton';
     }
     _mod_fm_admin_mask_hide();
    }
   });
  }
 }

 function _mod_fm_user_login() {
  var id = $('mod_fm_admin_account_select').getValue();
  if (id != '0') {
   _mod_fm_admin_disable_elements();
   // Mask on
   _mod_fm_admin_mask_show();
   var url = '{/literal}{$MOD_FM_BASEURL}{literal}&action=user_login&local_id='+id;
   new Ajax.Request(url, {
    method: 'get',
    asynchronous: true,
    onFailure: function() {_mod_fm_admin_mask_hide();},
    onSuccess: function(response) {
     var response = response.responseText.evalJSON();
     if (response.success) {
      var _mod_fm_user_login_url = response.url;
      popupFullScreenScroll(_mod_fm_user_login_url);
      $('mod_fm_admin_user_remove_btn').disabled = false;
      $('mod_fm_admin_user_remove_btn').className = 'flatButton';
      $('mod_fm_admin_user_login_btn').disabled = false;
      $('mod_fm_admin_user_login_btn').className = 'flatButton';
     }
     else {
      $('mod_fm_admin_user_remove_btn').disabled = false;
      $('mod_fm_admin_user_remove_btn').className = 'flatButton';
      $('mod_fm_admin_user_login_btn').disabled = false;
      $('mod_fm_admin_user_login_btn').className = 'flatButton';
      if (response.error_msg) {
       alert(response.error_msg);
      }
     }
     _mod_fm_admin_mask_hide();
    }
   });
  }
 }

 function popupFullScreen(url) {
  var top = 0;
  var left = 0;
  w = screen.width;
  h = screen.height;
  var target = window.open(url, '', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
 }

 function popupFullScreenScroll(url) {
  var top = 0;
  var left = 0;
  w = screen.width;
  h = screen.height;
  var target = window.open(url, '', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
 }

 function popupCenter(url, title, w, h) {
  var left = (screen.width/2) - (w/2);
  var top = (screen.height/2) - (h/2);
  var target = window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
 }

 function _mod_fm_register() {
  var url = '{/literal}{$MOD_FM_BASEURL}{literal}&action=register';
  var el = $('mod_fm_registration_msg_div');
  el.innerHTML = progressHTML;
  new Ajax.Request(url, {
   method: 'get',
   asynchronous: true,
   onFailure: function() {
    el.innerHTML = error_unknown;
   },
   onSuccess: function(response) {
    var response = response.responseText.evalJSON();
    if (response.success && response.professor_ids.length && response.professor_names.length) {
     // All went well
     var html = '';
     html += '<table cellpadding="0" cellspacing="0" style="width:100%;">';
     html += '	<tr>';
     html += '		<td style="height:160px;">';

     html += '			<div id="mod_fm_upper_area" style="width:100%; height:150px;">';
     html += '				<table cellpadding="0" cellspacing="0" style="width:100%;">';
     html += '					<tr>';
     html += '						<td>';
     html += '							<div id="mod_fm_account_amount" style="width:100%;">0 {/literal}{$smarty.const._FUZE_ADMIN_USER_AMOUNT}{literal}</div>';
     html += '						</td>';
     html += '					</tr>';
     html += '					<tr>';
     html += '						<td>';
     html += '							<select id="mod_fm_account_select" name="mod_fm_account_select" onchange="javascript:_mod_fm_get_user_data();">';
     html += '								<option value="0">{/literal}{$smarty.const._FUZE_ADMIN_USER_SELECT_DEFAULT}{literal}</option>';
     for (var i=0; i<response.professor_ids.length; i++) {
      html += '								<option value="'+response.professor_ids[i]+'">'+response.professor_names[i]+'</option>';
     }
     html += '							</select>';
     html += '						</td>';
     html += '					</tr>';
     html += '					<tr>';
     html += '						<td>';
     html += '							<div id="mod_fm_account_data" style="width:100%;">&nbsp;</div>';
     html += '						</td>';
     html += '					</tr>';
     html += '				</table>';
     html += '			</div>';

     html += '		</td>';
     html += '	</tr>';
     html += '	<tr>';
     html += '		<td style="padding:5px;">';
     html += '			<input type="button" class="flatButton" id="mod_fm_admin_user_create_btn" value={/literal}"{$smarty.const._FUZE_ADMIN_USER_CREATE_BTN}"{literal} onclick="javascript:_mod_fm_user_create();" disabled/>';
     html += '		</td>';
     html += '	</tr>';
     html += '	<tr>';
     html += '		<td style="padding:5px;">';
     html += '			<input type="button" class="flatButton" id="mod_fm_admin_user_remove_btn" value={/literal}"{$smarty.const._FUZE_ADMIN_USER_SUSPEND_BTN}"{literal} onclick="javascript:_mod_fm_user_remove();" disabled/>';
     html += '		</td>';
     html += '	</tr>';
     html += '	<tr>';
     html += '		<td style="padding:5px;">';
     html += '			<input type="button" class="flatButton" id="mod_fm_admin_user_login_btn" value={/literal}"{$smarty.const._FUZE_ADMIN_USER_LOGIN_BTN}"{literal} onclick="javascript:_mod_fm_user_login();" disabled/>';
     html += '		</td>';
     html += '	</tr>';
     html += '</table>';
     el.innerHTML = html;
    }
    else {
     // Some error occured
     el.innerHTML = response.error_msg;
    }
   }
  });
 }
{/literal}
</script>
