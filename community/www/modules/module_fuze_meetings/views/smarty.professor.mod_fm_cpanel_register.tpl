{* smarty template FUZE module pre-register screen *}
{capture name="register"}
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
 <div id="_mod_fm_registration_cpanel_div">
  <table cellpadding="0" cellspacing="0" style="width:100%;">
   <tr>
    <td style="height:160px;">
     <div style="width:100%; height:250px;" id="mod_fm_registration_msg_div">
      <table cellpadding="0" cellspacing="0" style="width:100%;">
       <tr>
        <td style="height:75px;">
         <p style="padding-bottom:5px;">- {$smarty.const._FUZE_PROF_ACCOUNT_BULLET_1}</p>
         <p style="padding-bottom:5px;">- {$smarty.const._FUZE_PROF_ACCOUNT_BULLET_2}</p>
         <p style="padding-bottom:5px;">- {$smarty.const._FUZE_PROF_ACCOUNT_BULLET_3}</p>
         <input type="hidden" id="_mof_fm_prof_user_id" value="{$_FUZE_PROF_VAR_USER_ID}"/>
        </td>
       </tr>
       <tr>
        <td align="center">
         <input type="button" class="flatButton" id="_mod_fm_prof_register_btn" value="{$smarty.const._FUZE_PROF_ACCOUNT_CREATE}" onclick="javascript:_mod_fm_prof_user_create();"/>
        </td>
       </tr>
      </table>
     </div>
    </td>
   </tr>
  </table>
 </div>
</div>
{/capture}
{eF_template_printBlock title=$smarty.const._FUZE_MEETINGS data=$smarty.capture.register image= $T_RSS_MODULE_BASELINK|cat:'images/rss32.png' absoluteImagePath = 1}

<script>
{literal}

 var error_unknown = '<table cellpadding="0" cellspacing="0" style="width:100%;height:100%;"><tr valign="middle"><td align="center"><span style="font-size:14px; color:#AA0000;">{/literal}{$smarty.const._FUZE_ADMIN_ERROR_REGISTRATION_UNKNOWN}{literal}</span></td></tr></table>';
 var account_data_loader = '<img src="{/literal}{$MOD_FM_BASELINK}{literal}images/loader-blue.gif"/>';

 var _mod_fm_prof_html = '';
 _mod_fm_prof_html += '<table cellpadding="0" cellspacing="0" style="width:100%;">';
 _mod_fm_prof_html += '	<tr>';
 _mod_fm_prof_html += '		<td>';
 _mod_fm_prof_html += '			<div id="mod_fm_upper_area" style="width:100%;">';
 _mod_fm_prof_html += '				<table cellpadding="0" cellspacing="0" style="width:100%;">';
 _mod_fm_prof_html += '					<tr>';
 _mod_fm_prof_html += '						<td style="width:1px; padding-right:10px;">';
 _mod_fm_prof_html += '							<input type="button" class="flatButton" id="_mod_fm_prof_cpanel_host_btn" value="{/literal}{$smarty.const._FUZE_PROF_MEETING_HOST}{literal}" onclick="javascript:_mod_fm_prof_host();"/>';
 _mod_fm_prof_html += '						</td>';
 _mod_fm_prof_html += '						<td>';
 _mod_fm_prof_html += '							<input type="button" class="flatButton" id="_mod_fm_prof_cpanel_host_btn" value="{/literal}{$smarty.const._FUZE_PROF_MEETING_SCHEDULE}{literal}" onclick="javascript:_mod_fm_prof_schedule();"/>';
 _mod_fm_prof_html += '						</td>';
 _mod_fm_prof_html += '					</tr>';

 _mod_fm_prof_html += '					<tr>';
 _mod_fm_prof_html += '						<td colspan="2">';
 _mod_fm_prof_html += '							<div id="_mod_fm_prof_cpanel_scedule_desc" style="padding-top:10px;color:#999;">{/literal}{$smarty.const._FUZE_PROF_MEETING_AMOUNT_MEETING_NONE}{literal}</div>';
 _mod_fm_prof_html += '						</td>';
 _mod_fm_prof_html += '					</tr>';

 _mod_fm_prof_html += '					<tr>';
 _mod_fm_prof_html += '						<td colspan="2" style="padding:2px; background:#F0F0F0;">';

 _mod_fm_prof_html += '							<table cellpadding="2" cellspacing="0" style="width:100%;">';
 _mod_fm_prof_html += '								<tr style="font-weight:bold; background:#CCCCCC;">';
 _mod_fm_prof_html += '									<td style="width:250px; text-align:center;">';
 _mod_fm_prof_html += '										{/literal}{$smarty.const._FUZE_PROF_MEETING_CPANEL_TABLE_TITLE}{literal}';
 _mod_fm_prof_html += '									</td>';
 _mod_fm_prof_html += '									<td style="width:100px; text-align:center;">';
 _mod_fm_prof_html += '										{/literal}{$smarty.const._FUZE_PROF_MEETING_CPANEL_TABLE_WHEN}{literal}';
 _mod_fm_prof_html += '									</td>';
 _mod_fm_prof_html += '									<td style="text-align:center;">';
 _mod_fm_prof_html += '										{/literal}{$smarty.const._FUZE_PROF_MEETING_CPANEL_TABLE_LINK}{literal}';
 _mod_fm_prof_html += '									</td>';
 _mod_fm_prof_html += '								</tr>';
 _mod_fm_prof_html += '								<tr class="defaultRowHeight oddRowColor">';
 _mod_fm_prof_html += '									<td class="emptyCategory" colspan = "100%">{/literal}{$smarty.const._NODATAFOUND}{literal}</td>';
 _mod_fm_prof_html += '								</tr>';
 _mod_fm_prof_html += '							</table>';

 _mod_fm_prof_html += '						</td>';
 _mod_fm_prof_html += '					</tr>';

 _mod_fm_prof_html += '					<tr>';
 _mod_fm_prof_html += '						<td>';
 _mod_fm_prof_html += '							<a href="{/literal}{$MOD_FM_BASEURL}{literal}">{/literal}{$smarty.const._FUZE_PROF_MEETING_EDIT}{literal}</a>';
 _mod_fm_prof_html += '						</td>';
 _mod_fm_prof_html += '					</tr>';
 _mod_fm_prof_html += '				</table>';
 _mod_fm_prof_html += '			</div>';

 _mod_fm_prof_html += '		</td>';
 _mod_fm_prof_html += '	</tr>';
 _mod_fm_prof_html += '</table>';

 function _mod_fm_admin_mask_show() {
  $('_mod_fm_admin_mask').show();
 }

 function _mod_fm_admin_mask_hide() {
  $('_mod_fm_admin_mask').hide();
 }

 function _mod_fm_admin_disable_elements() {
  // The interface buttons
  $('_mod_fm_prof_register_btn').disabled = true;
 }

 function _mod_fm_prof_user_create() {
  _mod_fm_admin_disable_elements();
  var local_id = $('_mof_fm_prof_user_id').getValue();
  var el = $('_mod_fm_registration_cpanel_div');
  // Mask on
  _mod_fm_admin_mask_show();
  if (local_id != '0') {
   var url = '{/literal}{$MOD_FM_BASEURL}{literal}&action=user_create&local_id='+local_id;
   new Ajax.Request(url, {
    method: 'get',
    asynchronous: true,
    onFailure: function() {
     el.innerHTML = error_unknown;
     _mod_fm_admin_mask_hide(); // Mask off
    },
    onSuccess: function(response) {
     var response = response.responseText.evalJSON();
     if (response.success) {
      el.innerHTML = _mod_fm_prof_html;
      _mod_fm_admin_mask_hide(); // Mask off
     }
     else {
      alert(response.error_msg);
      $('_mod_fm_prof_register_btn').disabled = false;
      _mod_fm_admin_mask_hide(); // Mask off
     }
    }
   });
  }
 }

 function _mod_fm_prof_host() {
  window.location = '{/literal}{$MOD_FM_BASEURL}{literal}&action=meeting_host_prep';
 }

 function _mod_fm_prof_schedule() {
  window.location = '{/literal}{$MOD_FM_BASEURL}{literal}&action=meeting_schedule_prep';
 }

{/literal}
</script>
