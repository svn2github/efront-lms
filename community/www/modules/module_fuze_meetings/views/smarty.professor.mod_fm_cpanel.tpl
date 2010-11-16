{* smarty template FUZE module pre-register screen *}
{capture name="_mod_fm_prof_cpanel"}
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
    <td>
     <div id="mod_fm_upper_area" style="width:100%;">
      <table cellpadding="0" cellspacing="0" style="width:100%;">
       <tr>
        <td style="width:1px; padding-right:10px;">
         <input type="button" class="flatButton" id="_mod_fm_prof_cpanel_host_btn" value="{$smarty.const._FUZE_PROF_MEETING_HOST}" onclick="javascript:_mod_fm_prof_host();"/>
        </td>
        <td>
         <input type="button" class="flatButton" id="_mod_fm_prof_cpanel_host_btn" value="{$smarty.const._FUZE_PROF_MEETING_SCHEDULE}" onclick="javascript:_mod_fm_prof_schedule();"/>
        </td>
       </tr>
       <tr>
        <td colspan="2">
         <div id="_mod_fm_prof_cpanel_scedule_desc" style="padding-top:10px;color:#999;">{$_FUZE_PROF_CPANEL_SCHEDULE_TIME_DESC}</div>
        </td>
       </tr>
       <tr>
        <td colspan="2" style="padding:2px; border:solid 1px #888; background:#F0F0F0;">
         <table cellpadding="2" cellspacing="0" style="width:100%;">
          <tr style="font-weight:bold; background:#CCCCCC;">
           <td style="width:270px;">
            {$smarty.const._FUZE_PROF_MEETING_CPANEL_TABLE_TITLE}
           </td>
           <td style="width:110px; text-align:center;">
            {$smarty.const._FUZE_PROF_MEETING_CPANEL_TABLE_WHEN}
           </td>
           <td style="text-align:center;">
            {$smarty.const._FUZE_PROF_MEETING_CPANEL_TABLE_LINK}
           </td>
          </tr>


          {foreach name='meeting_loop' key='meeting_id' item="meeting" from=$_FUZE_PROF_CPANEL_MEETINGS}
           <tr id="row_{$meeting.id}" class="{cycle values = "oddRowColor, evenRowColor"}">
            <td>{$meeting.subject}</td>
            <td class="centerAlign">{$meeting.starttime}</a></td>
            <td class="centerAlign">
            {if $meeting.link}
             <a href="{literal}javascript:void(0);{/literal}" onclick="{literal}javascript:_mod_fm_prof_launch('{/literal}{$meeting.meetingid}{literal}');{/literal}">{$smarty.const._FUZE_PROF_CPANEL_GO_TO_MEETING}</a>
            {else}
             -
            {/if}
            </td>
           </tr>
          {foreachelse}
           <tr class="defaultRowHeight oddRowColor">
            <td class="emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td>
           </tr>
          {/foreach}
         </table>
        </td>
       </tr>
       <tr>
        <td colspan="2">
         <a href="{$MOD_FM_BASEURL}">{$smarty.const._FUZE_PROF_MEETING_EDIT}</a>
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
{eF_template_printBlock title=$smarty.const._FUZE_MEETINGS data=$smarty.capture._mod_fm_prof_cpanel image= $T_RSS_MODULE_BASELINK|cat:'images/rss32.png' absoluteImagePath = 1}

<script>
{literal}

 function _mod_fm_prof_host() {
  window.location = '{/literal}{$MOD_FM_BASEURL}{literal}&action=meeting_host_prep';
 }

 function _mod_fm_prof_schedule() {
  window.location = '{/literal}{$MOD_FM_BASEURL}{literal}&action=meeting_schedule_prep';
 }

 function _mod_fm_prof_mask_on() {
  $('_mod_fm_admin_mask').show();
 }

 function _mod_fm_prof_mask_off() {
  $('_mod_fm_admin_mask').hide();
 }

 function _mod_fm_prof_launch(meetingid) {
  if (meetingid) {
   _mod_fm_prof_mask_on();
   var url = '{/literal}{$MOD_FM_BASEURL}{literal}&action=meeting_start&meetingid='+meetingid;
   new Ajax.Request(url, {
    method: 'get',
    aynchronous: true,
    onFailure: function() {},
    onSuccess: function(response) {
     var response = response.responseText.evalJSON();
     if (response.success) {
      _mod_fm_prof_mask_off(); // Mask off
      var meeting_url = response.url;
      var Popup = {open: function(options){
           this.options = {url: '#',width: 600,height: 600}
           Object.extend(this.options, options || {});
              window.open(this.options.url, '', 'width='+this.options.width+',height='+this.options.height);
              }
         }
      Popup.open({url:meeting_url});
      return false;
     }
     else {
      alert(response.error_msg);
      _mod_fm_prof_mask_off(); // Mask off
     }
    }
   });
  }
 }

{/literal}
</script>
