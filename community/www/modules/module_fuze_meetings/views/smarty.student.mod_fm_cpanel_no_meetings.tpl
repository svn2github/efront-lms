{capture name="mod_fm_student_no_meetings"}
<div style="height:100px;width:100%;position:relative;">
 <div id="_mod_fm_registration_cpanel_div">
  <table cellpadding="0" cellspacing="0" style="width:100%;">
   <tr>
    <td style="height:100px;">
     <div style="width:100%; height:100px;" id="mod_fm_registration_msg_div">
      <table cellpadding="0" cellspacing="0" style="width:100%;">
       <tr>
        <td>
         <p>{$smarty.const._FUZE_STUDENT_CPANEL_NO_MEETINGS}</p>
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
{eF_template_printBlock title=$smarty.const._FUZE_MEETINGS data=$smarty.capture.mod_fm_student_no_meetings image= $T_RSS_MODULE_BASELINK|cat:'images/rss32.png' absoluteImagePath = 1}
