{capture name="_mod_fm_prof_view_all"}

<div style="height:100%;width:100%;position:relative;" id="_mod_fm_prof_container">
 <div id="_mod_fm_prof_mask" style="position:absolute; top:0px; left:0px; width:100%; height:100%; display:none;">
  <div style="filter:alpha(opacity=35); -moz-opacity:0.35; -khtml-opacity: 0.35; opacity: 0.35; width:100%; height:100%; z-index:100; background:#AAAAAA; position:absolute; top:0px; left:0px; display:inherit;">&nbsp;</div>
  <div style="z-index:101;position:absolute;top:0px;left:0px; width:100%; height:100%; display:inherit;">
   <table cellpadding="0" cellspacing="0" style="width:100%; height:100%;">
    <tr valign="middle">
     <td align="center">
      <img src="{$MOD_FM_BASELINK}images/loader-blue.gif"/>
     </td>
    </tr>
   </table>
  </div>
 </div>
 <div>
  <table cellpadding="5" cellspacing="2" style="width:100%;">
   <tr><td style="font-size:14px; font-weight:bold;">{$smarty.const._FUZE_PROF_VIEW_ALL_TITLE}</td></tr>
   <tr valign="top">
    <td style="padding:2px;">
     <div id="_mod_fm_prof_view_all">
     {if !$T_SORTED_TABLE || $T_SORTED_TABLE == 'meetingListTable'}


<!--ajax:meetingListTable-->


       <table style="width:100%;" class="sortedTable" size="{$T_TABLE_SIZE}" sortBy="2" order="asc" id="meetingListTable" useAjax="1" rowsPerPage="{$smarty.const.G_DEFAULT_TABLE_SIZE}" url="{$MOD_FM_BASEURL}&action=fetch_meetings&">
        <tr class="topTitle">
         <td name="subject" class="topTitle">{$smarty.const._FUZE_PROF_VIEW_ALL_TITLE_SUBJECT}</td>
         <td name="lesson_name" class="topTitle noSort" style="width:250px;">{$smarty.const._FUZE_PROF_VIEW_ALL_TITLE_LESSON_NAME}</td>
         <td name="starttime" class="topTitle" style="text-align:center;width:100px;">{$smarty.const._FUZE_PROF_VIEW_ALL_TITLE_WHEN}</td>
         <td name="link" class="topTitle noSort" style="text-align:center;width:100px;">{$smarty.const._FUZE_PROF_VIEW_ALL_TITLE_LINK}</td>
         <td name="tools" class="topTitle noSort" style="text-align:center;width:100px;">{$smarty.const._FUZE_PROF_VIEW_ALL_TITLE_TOOLS}</td>
        </tr>
        {foreach name="meeting_loop" key="meeting_id" item="meeting" from=$T_DATA_SOURCE}
         <tr class="{cycle values ="oddRowColor,evenRowColor"} defaultRowHeight">
          <td>{$meeting.subject}</td>
          <td>{$meeting.lesson_name}</td>
          <td align="center">{$meeting.starttime}</td>
          {if $meeting.link}
           <td align="center"><a href="{literal}javascript:void(0);{/literal}" onclick="{literal}_mod_fm_prof_launch('{/literal}{$meeting_id}{literal}'){/literal}">{$meeting.link}<a/></td>
           <td align="center">-</td>
          {else}
           <td align="center">-</td>
           <td align="center"><a href="{$MOD_FM_BASEURL}&action=meeting_edit_prep&meeting_id={$meeting_id}" onclick=""><img src="{$MOD_FM_BASELINK}images/milky_pencil.png"/></a>&nbsp;&nbsp;<a href="{literal}javascript:void(0);{/literal}" onclick="{literal}javascript:_mod_fm_prof_remove_meeting('{/literal}{$meeting_id}{literal}');{/literal}"><img src="{$MOD_FM_BASELINK}images/milky_delete.png"/></a></td>
          {/if}
         </tr>
        {foreachelse}
         <tr class="defaultRowHeight oddRowColor">
          <td class="emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td>
         </tr>
        {/foreach}
       </table>


<!--/ajax:meetingListTable-->



{/if}
     </div>
    </td>
   </tr>
  </table>
 </div>
</div>

{/capture}

{eF_template_printBlock title=$smarty.const._FUZE_MEETINGS data=$smarty.capture._mod_fm_prof_view_all image= $T_RSS_MODULE_BASELINK|cat:'images/rss32.png' absoluteImagePath = 1}

<script>
{literal}

 var _mod_fm_remove_meeting_message = '{/literal}{$smarty.const._FUZE_PROF_VIEW_ALL_CONFIRM_REMOVE}{literal}';
 var _mod_fm_remove_meeting_success = '{/literal}{$smarty.const._FUZE_PROF_VIEW_ALL_REMOVE_SUCCESS}{literal}';
 var _mod_fm_remove_meeting_failure = '{/literal}{$smarty.const._FUZE_PROF_VIEW_ALL_REMOVE_FAILURE}{literal}';

 function _mod_fm_trim(string) {
  return string.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
 }

 function _mod_fm_prof_mask_on() { $('_mod_fm_prof_mask').show(); }
 function _mod_fm_prof_mask_off() { $('_mod_fm_prof_mask').hide(); }

 function _mod_fm_prof_remove_meeting(meeting_id) {
  if (meeting_id) {
   if(confirm(_mod_fm_remove_meeting_message)) {
    _mod_fm_prof_mask_on();
    var url = '{/literal}{$MOD_FM_BASEURL}{literal}&action=meeting_cancel&meeting_id='+meeting_id;
    new Ajax.Request(url, {
     method: 'get',
     asynchronous: true,
     onFailure: function() {
      alert(_mod_fm_remove_meeting_failure);
      _mod_fm_prof_mask_off();
     },
     onSuccess: function(response) {
      var response = response.responseText.evalJSON();
      if (response.success) {
       alert(_mod_fm_remove_meeting_success);
       _mod_fm_prof_mask_off();
       eF_js_redrawPage('meetingListTable', true);
      }
      else {
       alert(_mod_fm_remove_meeting_failure);
       _mod_fm_prof_mask_off();
      }
     }
    });
   }
  }
 }

 function _mod_fm_prof_launch(meeting_id) {
  if (meeting_id) {
   _mod_fm_prof_mask_on();
  }
 }

{/literal}
</script>
