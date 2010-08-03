{capture name = "t_users_table_code"}
 {if $T_REPORT_NAMES}
 <div class = "headerTools">
  <span>
   <span>{$smarty.const._SELECTREPORT}:&nbsp;</span>
   <select onchange = "location = location.toString().replace(/&report=\d*/, '')+'&report='+this.options[this.options.selectedIndex].value">
    <option value = "0" {if $smarty.get.report==$key}selected{/if}>{$smarty.const._AVAILABLEREPORTS}</option>
   {foreach item = "item" key = "key" from = $T_REPORT_NAMES}
    <option value = "{$key}" {if $smarty.get.report==$key}selected{/if}>{$item}</option>
   {/foreach}
   </select>
  </span>
 </div>
 {else}
 <div>{$smarty.const._NOREPORTSINTHESYSTEM}<a href = "{$smarty.server.PHP_SELF}?ctg=statistics&option=advanced_user_reports&tab=builder&add=1">{$smarty.const._CREATEONE}</a></div>
 {/if}
 {if $T_REPORT}
<!--ajax:usersTable-->
 <table id = "usersTable" style = "width:100%" sortBy=0 size = "{$T_TABLE_SIZE}" class = "sortedTable" useAjax = "1" url = "{$smarty.server.PHP_SELF}?ctg=statistics&option=advanced_user_reports&report={$smarty.get.report}&">
  <tr class = "topTitle">
   <td>{$smarty.const._USER}</td>
   <td>{$smarty.const._USER}</td>
   <td>{$smarty.const._PROGRESS}</td>
   <td>{$smarty.const._CERTIFICATIONS}</td>
  </tr>
  {foreach name = 'conditions_list' item = "item" key = "key" from = $T_USERS}
  <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"}">
   <td></td>
   <td></td>
   <td></td>
   <td></td>
  </tr>
  {foreachelse}
  <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "{$T_COLUMNS}">{$smarty.const._NODATAFOUND}</td></tr>
  {/foreach}
 </table>
<!--/ajax:usersTable-->
 {/if}
{/capture}

{capture name = "t_report_tools_code"}
 <div class = "headerTools">
  <span>
   <img src = "images/16x16/mail.png" alt = "{$smarty.const._SENDEMAIL}" title = "{$smarty.const._SENDEMAIL}" />
   <a href = "javascript:void(0)">{$smarty.const._SENDEMAIL}</a>
  </span>
 </div>
 <div class = "headerTools">
  <span>
   <img src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._ACTIVATE}" title = "{$smarty.const._ACTIVATE}" />
   <a href = "javascript:void(0)">{$smarty.const._ACTIVATE}</a>
  </span>
  <span>
   <img src = "images/16x16/trafficlight_red.png" alt = "{$smarty.const._DEACTIVATE}" title = "{$smarty.const._DEACTIVATE}" />
   <a href = "javascript:void(0)">{$smarty.const._DEACTIVATE}</a>
  </span>
  <span>
   <img src = "images/16x16/error_delete.png" alt = "{$smarty.const._ARCHIVE}" title = "{$smarty.const._ARCHIVE}" />
   <a href = "javascript:void(0)">{$smarty.const._ARCHIVE}</a>
  </span>
 </div>
 <div class = "headerTools">
  <span>
   <img src = "images/16x16/users.png" alt = "{$smarty.const._ADDTOGROUP}" title = "{$smarty.const._ADDTOGROUP}" />
   <a href = "javascript:void(0)">{$smarty.const._ADDTOGROUP}</a>
  </span>
  <span>
   <img src = "images/16x16/courses.png" alt = "{$smarty.const._ASSIGNCOURSE}" title = "{$smarty.const._ASSIGNCOURSE}" />
   <a href = "javascript:void(0)">{$smarty.const._ASSIGNCOURSE}</a>
  </span>
  <span>
   <img src = "images/16x16/lessons.png" alt = "{$smarty.const._ASSIGNLESSON}" title = "{$smarty.const._ASSIGNLESSON}" />
   <a href = "javascript:void(0)">{$smarty.const._ASSIGNLESSON}</a>
  </span>
  <span>
   <img src = "images/16x16/certificate.png" alt = "{$smarty.const._ISSUECERTIFICATE}" title = "{$smarty.const._ISSUECERTIFICATE}" />
   <a href = "javascript:void(0)">{$smarty.const._ISSUECERTIFICATE}</a>
  </span>
 </div>
 <div class = "headerTools">
  <span>
   <img src = "images/16x16/users.png" alt = "{$smarty.const._REMOVEFROMGROUP}" title = "{$smarty.const._REMOVEFROMGROUP}" />
   <a href = "javascript:void(0)">{$smarty.const._REMOVEFROMGROUP}</a>
  </span>
  <span>
   <img src = "images/16x16/courses.png" alt = "{$smarty.const._REMOVECOURSE}" title = "{$smarty.const._REMOVECOURSE}" />
   <a href = "javascript:void(0)">{$smarty.const._REMOVECOURSE}</a>
  </span>
  <span>
   <img src = "images/16x16/lessons.png" alt = "{$smarty.const._REMOVELESSON}" title = "{$smarty.const._REMOVELESSON}" />
   <a href = "javascript:void(0)">{$smarty.const._REMOVELESSON}</a>
  </span>
  <span>
   <img src = "images/16x16/certificate.png" alt = "{$smarty.const._REVOKECERTIFICATE}" title = "{$smarty.const._REVOKECERTIFICATE}" />
   <a href = "javascript:void(0)">{$smarty.const._REVOKECERTIFICATE}</a>
  </span>
 </div>
 <div class = "headerTools">
  <span>
   <img src = "images/16x16/refresh.png" alt = "{$smarty.const._RESETLEARNINGPROGRESS}" title = "{$smarty.const._RESETLEARNINGPROGRESS}" />
   <a href = "javascript:void(0)">{$smarty.const._RESETLEARNINGPROGRESS}</a>
  </span>
 </div>

{/capture}

{capture name = "t_report_builder_code"}
 <div class = "headerTools">
  <span>
   <img src = "images/16x16/add.png" alt = "{$smarty.const._ADDREPORT}" title = "{$smarty.const._ADDREPORT}" />
   <a href = "{$smarty.server.PHP_SELF}?ctg=statistics&option=advanced_user_reports&add=1&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._NEWREPORT}', 0)">{$smarty.const._ADDREPORT}</a>
  </span>
  {if $T_REPORT_NAMES}
  <span>
   <img src = "images/16x16/edit.png" alt = "{$smarty.const._EDITREPORT}" title = "{$smarty.const._EDITREPORT}" />
   {$smarty.const._EDITREPORT}:&nbsp;
   <select onchange = "location = location.toString().replace(/&report=\d*/, '')+'&report='+this.options[this.options.selectedIndex].value+'&tab=builder'">
    <option value = "0" {if $smarty.get.report==$key}selected{/if}>{$smarty.const._AVAILABLEREPORTS}</option>
   {foreach item = "item" key = "key" from = $T_REPORT_NAMES}
    <option value = "{$key}" {if $smarty.get.report==$key}selected{/if}>{$item}</option>
   {/foreach}
   </select>
  </span>
  <hr/>
  {/if}
 </div>

 {if $smarty.get.report}

   <div class = "headerTools">
    <span>
     <img src = "images/16x16/add.png" alt = "{$smarty.const._ADDCONDITION}" title = "{$smarty.const._ADDCONDITION}" />
     <a href = "{$smarty.server.PHP_SELF}?ctg=statistics&option=advanced_user_reports&add_condition=1&report={$smarty.get.report}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._ADDCONDITION}', 1)">{$smarty.const._ADDCONDITION}</a>
     {*<a href = "javascript:void(0)" onclick = "addCondition()">{$smarty.const._ADDCONDITION}</a>*}
    </span>
   </div>
   <table class = "sortedTable" style = "width:100%" id = "conditions_table">
    <tr class = "topTitle">
     <td>{$smarty.const._CONDITIONTYPE}</td>
     <td>{$smarty.const._CONDITIONSPECIFICATION}</td>
     <td>{$smarty.const._RELATIONWITHTHEFOLLOWINGCONDITION}</td>
     <td>{$smarty.const._TOOLS}</td>
    </tr>
    {foreach name = 'conditions_list' item = "item" key = "key" from = $T_CONDITIONS}
    <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"}">
     <td></td>
     <td></td>
     <td></td>
     <td></td>
    </tr>
    {foreachelse}
    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "4">{$smarty.const._NODATAFOUND}</td></tr>
    {/foreach}
   </table>

   <div class = "headerTools">
    <span>
     <img src = "images/16x16/add.png" alt = "{$smarty.const._ADDCOLUMN}" title = "{$smarty.const._ADDCOLUMN}" />
     <a href = "{$smarty.server.PHP_SELF}?ctg=statistics&option=advanced_user_reports&add_column=1&report={$smarty.get.report}popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._ADDCOLUMN}', 1)">{$smarty.const._ADDCOLUMN}</a>
    </span>
   </div>
   <table class = "sortedTable" style = "width:100%">
    <tr class = "topTitle">
     <td>{$smarty.const._COLUMNTYPE}</td>
     <td>{$smarty.const._GRIDNAME}</td>
     <td>{$smarty.const._WIDTH}</td>
     <td>{$smarty.const._ALIGNED}</td>
     <td>{$smarty.const._TOOLS}</td>
    </tr>
    {foreach name = 'conditions_list' item = "item" key = "key" from = $T_CONDITIONS}
    <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"}">
     <td></td>
     <td></td>
     <td></td>
     <td></td>
     <td>
     </td>
    </tr>
    {foreachelse}
    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "5">{$smarty.const._NODATAFOUND}</td></tr>
    {/foreach}
   </table>
 {/if}
{/capture}


{capture name = 't_tabber_code'}
<div class = "tabber">
 {eF_template_printBlock tabber = "users" title = $smarty.const._REPORTS data = $smarty.capture.t_users_table_code image = '32x32/reports.png'}
 {eF_template_printBlock tabber = "tools" title = $smarty.const._TOOLS data = $smarty.capture.t_report_tools_code image = '32x32/tools.png'}
 {eF_template_printBlock tabber = "builder" title = $smarty.const._BUILDER data = $smarty.capture.t_report_builder_code image = '32x32/generic.png'}
</div>
{/capture}


 {if $smarty.get.add}
  {capture name = 't_new_report_code'}
   {eF_template_printForm form=$T_ADD_REPORTING_FORM}
  {/capture}
  {eF_template_printBlock title = $smarty.const._NEWREPORT data = $smarty.capture.t_new_report_code image = '32x32/add.png'}

  {if $T_MESSAGE_TYPE == 'success'}
     <script>parent.location = parent.location;</script>
  {/if}
 {elseif $smarty.get.add_condition && $smarty.get.report}
  {capture name = 't_add_condition_code'}
   {$T_ADD_CONDITION_FORM.javascript}
   <form {$T_ADD_CONDITION_FORM.attributes}>
       {$T_ADD_CONDITION_FORM.hidden}
       <table class = "formElements">
           <tr><td class = "labelCell">{$T_ADD_CONDITION_FORM.title.label}:&nbsp;</td>
               <td class = "elementCell">{$T_ADD_CONDITION_FORM.title.html}</td></tr>
           <tr><td></td>
               <td class = "submitCell">{$T_ADD_CONDITION_FORM.submit.html}</td></tr>
       </table>
   </form>

  {/capture}
  {eF_template_printBlock title = $smarty.const._ADDCONDITION data = $smarty.capture.t_add_condition_code image = '32x32/add.png'}

  {if $T_MESSAGE_TYPE == 'success'}
     <script>parent.location = parent.location.toString()+'&tab=builder';</script>
  {/if}
 {elseif $smarty.get.add_column && $smarty.get.report}
  {capture name = 't_add_column_code'}
   {eF_template_printForm form=$T_ADD_COLUMN_FORM}
  {/capture}
  {eF_template_printBlock title = $smarty.const._ADDCOLUMN data = $smarty.capture.t_add_column_code image = '32x32/add.png'}

  {if $T_MESSAGE_TYPE == 'success'}
     <script>parent.location = parent.location.toString()+'&tab=builder';</script>
  {/if}

 {else}
  {eF_template_printBlock title = $smarty.const._ADVANCEDUSERREPORTS data = $smarty.capture.t_tabber_code image = '32x32/users.png'}
 {/if}
