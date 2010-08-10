{capture name = "t_users_table_code"}
 {if $T_REPORT_NAMES}
 <div class = "headerTools">
  <span>
   <span>{$smarty.const._SELECTREPORT}:&nbsp;</span>
   <select>
    <option value = "0" {if $smarty.get.report==$key}selected{/if}>{$smarty.const._AVAILABLEREPORTS}</option>
   {foreach item = "item" key = "key" from = $T_REPORT_NAMES}
    <option value = "{$key}" {if $smarty.get.report==$key}selected{/if}>{$item}</option>
   {/foreach}
   </select>
   <img src = "images/16x16/arrow_right.png" alt = "{$smarty.const._SHOW}" title = "{$smarty.const._SHOW}" onclick = "Element.extend(this);location = location.toString().replace(/&report=\d*/, '').replace(/&tab=\w*/, '')+'&report='+this.previous().options[this.previous().options.selectedIndex].value"/>
  </span>
 </div>
 {else}
 <div>{$smarty.const._NOREPORTSINTHESYSTEM}<a href = "{$smarty.server.PHP_SELF}?ctg=statistics&option=advanced_user_reports&tab=builder&add=1">{$smarty.const._CREATEONE}</a></div>
 {/if}
 {if $T_REPORT}
<!--ajax:usersTable-->
 <table id = "usersTable" style = "width:100%" sortBy="{$T_DEFAULT_SORT}" size = "{$T_TABLE_SIZE}" class = "sortedTable" useAjax = "1" url = "{$smarty.server.PHP_SELF}?ctg=statistics&option=advanced_user_reports&report={$smarty.get.report}&">
  <tr class = "topTitle">
   {foreach name = 't_columns_list' item = "item" key = "key" from = $T_REPORT.rules.columns}
   <td style = "{if $item.width}width:{$item.width}%;{/if}{if $item.align}text-align:{$item.align};{/if}" name = "{if $item.column == 'formatted_login'}login{else}{$item.column}{/if}">{if $item.grid_name}{$item.grid_name}{else}{$T_REPORT_COLUMNS[$item.column]}{/if}</td>
   {foreachelse}
   <td name = "login">{$smarty.const._USER}</td>
   <td name = "user_type">{$smarty.const._USERTYPE}</td>
   <td name = "email">{$smarty.const._EMAIL}</td>
   {/foreach}
  </tr>
  {foreach name = 'conditions_list' item = "user" key = "key" from = $T_DATA_SOURCE}
  <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"}">
   {foreach name = 't_columns_list' item = "item" key = "key" from = $T_REPORT.rules.columns}
   <td style = "{if $item.width}width:{$item.width}%;{/if}{if $item.align}text-align:{$item.align};{/if}">
    {assign var = "entry" value = $user[$item.column]}
    {if $item.column == $T_EDIT_LINK}
     <a href = "{$smarty.server.PHP_SELF}?ctg=users&edit_user={$user.login}" class = "editLink {if !$T_CONFIGURATION.disable_tooltip}info{/if}" onmouseover = "updateInformation(this, '{$user.login}', 'user');">
    {/if}
    {if $item.column == 'languages_NAME'}
     {$T_LANGUAGES[$entry]}
    {elseif $item.column == 'formatted_login'}
     {$user.formatted_login}
    {elseif $item.column == 'user_type'}
     {$T_ROLE_NAMES[$entry]}
    {elseif $item.column == 'active'}
     {if $entry}<img src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._ACTIVE}" title = "{$smarty.const._ACTIVE}"/>{else}<img src = "images/16x16/trafficlight_red.png" alt = "{$smarty.const._INACTIVE}" title = "{$smarty.const._INACTIVE}"/>{/if}
    {elseif $item.column == 'timestamp' || $item.column == 'last_login' || $item.column == 'hired_on' || $item.column == 'left_on'}
     #filter:timestamp_time-{$entry}#
    {elseif $item.column == 'branch'}
     {$T_BRANCHES[$entry]}
    {elseif $item.column == 'supervisor' || $item.column == 'driving_licence'}
     {if $entry}{$smarty.const._YES}{else}{$smarty.const._NO}{/if}
    {elseif $item.column == 'marital_status'}
     {if $entry}{$smarty.const._MARRIED}{else}{$smarty.const._SINGLE}{/if}
    {elseif $item.column == 'sex'}
     {if $entry}{$smarty.const._FEMALE}{else}{$smarty.const._MALE}{/if}
    {elseif $item.column == 'way_of_working'}
     {if $entry}{$smarty.const._PARTTIME}{else}{$smarty.const._FULLTIME}{/if}
    {else}
     {$entry}
    {/if}
    {if $item.column == $T_EDIT_LINK}
     {if !$T_CONFIGURATION.disable_tooltip}
      <img class = "tooltip" border = "0" src = "images/others/tooltip_arrow.gif" height = "15" width = "15"/>
      <span class = "tooltipSpan"></span>
     {/if}
     </a>
    {/if}
   </td>
   {foreachelse}
   <td>{$user.login}</td>
   <td>{$T_ROLE_NAMES[$user.user_type]}</td>
   <td>{$user.email}</td>
   {/foreach}
  </tr>
  {foreachelse}
  <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "{if $T_REPORT.rules.columns}{$T_REPORT.rules.columns|@sizeof}{else}3{/if}">{$smarty.const._NODATAFOUND}</td></tr>
  {/foreach}
 </table>
<!--/ajax:usersTable-->
 <div class = ""><span>{$smarty.const._CURREPAGEOPERATIONS}:</span>
  <img class = "ajaxHandle" src = "images/file_types/xls.png" alt = "{$smarty.const._EXPORTTOCSV}" title = "{$smarty.const._EXPORTTOCSV}" onclick = "exportCsv(this);"/>
  <img class = "ajaxHandle" src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._ACTIVATE}" title = "{$smarty.const._ACTIVATE}" onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) applyOperation(this, 'activate');"/>
  <img class = "ajaxHandle" src = "images/16x16/trafficlight_red.png" alt = "{$smarty.const._DEACTIVATE}" title = "{$smarty.const._DEACTIVATE}" onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) applyOperation(this, 'deactivate');"/>
  <img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._ARCHIVE}" title = "{$smarty.const._ARCHIVE}" onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) applyOperation(this, 'archive');"/>
  <img class = "ajaxHandle" src = "images/16x16/refresh.png" alt = "{$smarty.const._RESETLEARNINGPROGRESS}" title = "{$smarty.const._RESETLEARNINGPROGRESS}"onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) applyOperation(this, 'reset');"/>
  <img class = "ajaxHandle" src = "images/16x16/users.png" alt = "{$smarty.const._GROUPOPERATIONS}" title = "{$smarty.const._GROUPOPERATIONS}" onclick = "eF_js_showDivPopup('{$smarty.const._GROUPOPERATIONS}', 0, 'add_group_table')"/>
  <img class = "ajaxHandle" src = "images/16x16/courses.png" alt = "{$smarty.const._COURSEOPERATIONS}" title = "{$smarty.const._COURSEOPERATIONS}" onclick = "eF_js_showDivPopup('{$smarty.const._COURSEOPERATIONS}', 0, 'add_course_table')"/>
  <img class = "ajaxHandle" src = "images/16x16/lessons.png" alt = "{$smarty.const._LESSONOPERATIONS}" title = "{$smarty.const._LESSONOPERATIONS}" onclick = "eF_js_showDivPopup('{$smarty.const._LESSONOPERATIONS}', 0, 'add_lesson_table')"/>
  <img class = "ajaxHandle" src = "images/16x16/certificate.png" alt = "{$smarty.const._CERTIFICATEOPERATIONS}" title = "{$smarty.const._CERTIFICATEOPERATIONS}"onclick = "eF_js_showDivPopup('{$smarty.const._CERTIFICATEOPERATIONS}', 0, 'add_certificate_table')"/>
  <img class = "ajaxHandle" src = "images/16x16/mail.png" alt = "{$smarty.const._SENDEMAIL}" title = "{$smarty.const._SENDEMAIL}" onclick = "location='{$smarty.server.PHP_SELF}?ctg=messages&add=1&popup=1';eF_js_showDivPopup('{$smarty.const._SENDEMAIL}', 2)"/>
 </div>
 <div id = "add_group_table" style = "display:none">
  {capture name = "t_add_group_table_code"}
   <table>
    <tr><td class = "labelCell">{$T_GROUP_FORM.options.html}
      {$smarty.const._GROUP}:&nbsp;</td>
     <td class = "elementCell">{$T_GROUP_FORM.group.html}</td></tr>
    <tr><td class = "labelCell">{$T_GROUP_FORM.new_group.label}:&nbsp;</td>
     <td class = "elementCellCell">{$T_GROUP_FORM.new_group.html}</td></tr>
    <tr><td></td>
     <td class = "submitCell">{$T_GROUP_FORM.submit.html}</td></tr>
   </table>
  {/capture}
  {eF_template_printBlock title = $smarty.const._GROUP data = $smarty.capture.t_add_group_table_code image = '32x32/groups.png'}
 </div>
 <div id = "add_course_table" style = "display:none">
  {capture name = "t_add_course_table_code"}
   <table>
    <tr><td class = "labelCell">{$T_COURSE_FORM.options.html}
      {$smarty.const._COURSE}:&nbsp;</td>
     <td class = "elementCell">{$T_COURSE_FORM.course.html}</td></tr>
    <tr><td></td>
     <td class = "submitCell">{$T_COURSE_FORM.submit.html}</td></tr>
   </table>
  {/capture}
  {eF_template_printBlock title = $smarty.const._COURSE data = $smarty.capture.t_add_course_table_code image = '32x32/courses.png'}
 </div>
 <div id = "add_lesson_table" style = "display:none">
  {capture name = "t_add_lesson_table_code"}
   <table>
    <tr><td class = "labelCell">{$T_LESSON_FORM.options.html}
      {$smarty.const._LESSON}:&nbsp;</td>
     <td class = "elementCell">{$T_LESSON_FORM.lesson.html}</td></tr>
    <tr><td></td>
     <td class = "submitCell">{$T_LESSON_FORM.submit.html}</td></tr>
   </table>
  {/capture}
  {eF_template_printBlock title = $smarty.const._LESSON data = $smarty.capture.t_add_lesson_table_code image = '32x32/lessons.png'}
 </div>
 <div id = "add_certificate_table" style = "display:none">
  {capture name = "t_add_certificate_table_code"}
  {$T_CERTIFICATE_FORM.javascript}
  <form {$T_CERTIFICATE_FORM.attributes}>
   {$T_CERTIFICATE_FORM.hidden}
   <table>
    <tr><td class = "labelCell">{$T_CERTIFICATE_FORM.certificate_options.html}
      {$smarty.const._CERTIFICATE}:&nbsp;</td>
     <td class = "elementCell">{$T_CERTIFICATE_FORM.certificate.html}</td></tr>
    <tr><td></td>
     <td class = "submitCell">{$T_CERTIFICATE_FORM.submit.html}</td></tr>
   </table>
  </form>
  {/capture}
  {eF_template_printBlock title = $smarty.const._CERTIFICATE data = $smarty.capture.t_add_certificate_table_code image = '32x32/certificates.png'}
 </div>
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
   <a href = "javascript:void(0)" onclick = "applyOperation(this, 'activate');">{$smarty.const._ACTIVATE}</a>
  </span>
  <span>
   <img src = "images/16x16/trafficlight_red.png" alt = "{$smarty.const._DEACTIVATE}" title = "{$smarty.const._DEACTIVATE}" />
   <a href = "javascript:void(0)" onclick = "applyOperation(this, 'deactivate');">{$smarty.const._DEACTIVATE}</a>
  </span>
  <span>
   <img src = "images/16x16/error_delete.png" alt = "{$smarty.const._ARCHIVE}" title = "{$smarty.const._ARCHIVE}" />
   <a href = "javascript:void(0)" onclick = "applyOperation(this, 'archive');">{$smarty.const._ARCHIVE}</a>
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
   <a href = "javascript:void(0)" onclick = "applyOperation(this, 'reset');">{$smarty.const._RESETLEARNINGPROGRESS}</a>
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
   <select>
    <option value = "0" {if $smarty.get.report==$key}selected{/if}>{$smarty.const._AVAILABLEREPORTS}</option>
   {foreach item = "item" key = "key" from = $T_REPORT_NAMES}
    <option value = "{$key}" {if $smarty.get.report==$key}selected{/if}>{$item}</option>
   {/foreach}
   </select>
   <img src = "images/16x16/arrow_right.png" alt = "{$smarty.const._SHOW}" title = "{$smarty.const._SHOW}" onclick = "Element.extend(this);location = location.toString().replace(/&report=\d*/, '').replace(/&tab=\w*/, '')+'&report='+this.previous().options[this.previous().options.selectedIndex].value+'&tab=builder'"/>
  </span>
  <hr/>
  {/if}
 </div>

 {if $T_REPORT}
   <div class = "mediumHeader">
    <span>{$smarty.const._VIEWINGREPORT}: {$T_REPORT.name}</span>
    <a href = "{$smarty.server.PHP_SELF}?ctg=statistics&option=advanced_user_reports&edit={$smarty.get.report}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._NEWREPORT}', 0)">
     <img class = "handle" src = "images/16x16/edit.png" alt = "{$smarty.const._EDIT}" title = "{$smarty.const._EDIT}" onclick = ""/>
    </a>
    <img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._DELETE}" title = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) deleteReport(this, '{$smarty.get.report}')"/>
   </div>
   <div class = "headerTools">
    <span>
     <img src = "images/16x16/add.png" alt = "{$smarty.const._ADDCONDITION}" title = "{$smarty.const._ADDCONDITION}" />
     <a href = "{$smarty.server.PHP_SELF}?ctg=statistics&option=advanced_user_reports&add_condition=1&report={$smarty.get.report}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._ADDCONDITION}', 3)">{$smarty.const._ADDCONDITION}</a>
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
    {foreach name = 'conditions_list' item = "item" key = "key" from = $T_REPORT.rules.conditions}
    <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"}">
     <td>{$T_CONDITIONS[$item.condition].name}</td>
     <td>
      {$T_CONDITIONS[$item.condition].negation[$item.negation]}
      {$T_CONDITIONS[$item.condition].additional_options[$item.additional]}
      {if $item.condition == 'lesson'}
       {$T_LESSONS[$item.option]}
      {elseif $item.condition == 'course'}
       {$T_COURSES[$item.option]}
      {elseif $item.condition == 'group'}
       {$T_GROUPS[$item.option]}
      {elseif $item.condition == 'active'}
       {if $item.option == 1}{$smarty.const._ACTIVE}{else}{$smarty.const._INACTIVE}{/if}
      {elseif $item.condition == 'branch'}
       {$T_BRANCHES[$item.option]}
      {elseif $item.condition == 'learning_status'}
       {$T_CONDITIONS[$item.condition].values[$item.option]}
      {else}
       {$item.option}
      {/if}
      {if $item.from}#filter:timestamp-{$item.from}# {$smarty.const._AND} #filter:timestamp-{$item.to}#{/if}
     </td>
     <td>{$item.relation}</td>
     <td>
      <img class = "ajaxHandle" src = "images/16x16/{if $item.status}trafficlight_green{else}trafficlight_red{/if}.png" alt = "{$smarty.const._STATUS}" title = "{$smarty.const._STATUS}" onclick = "setStatus(this, '{$key}')"/>
      <a href = "{$smarty.server.PHP_SELF}?ctg=statistics&option=advanced_user_reports&edit_condition={$key}&report={$smarty.get.report}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._ADDCONDITION}', 3)">
       <img class = "ajaxHandle" src = "images/16x16/edit.png" alt = "{$smarty.const._EDIT}" title = "{$smarty.const._EDIT}" onclick = "eF_js_showDivPopup();"/></a>
      <img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._DELETE}" title = "{$smarty.const._DELETE}" onclick = "deleteCondition(this, '{$key}')"/>
     </td>
    </tr>
    {foreachelse}
    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "4">{$smarty.const._NODATAFOUND}</td></tr>
    {/foreach}
   </table>
   <br/>
   <div class = "headerTools">
    <span>
     <img src = "images/16x16/add.png" alt = "{$smarty.const._ADDCOLUMN}" title = "{$smarty.const._ADDCOLUMN}" />
     <a href = "{$smarty.server.PHP_SELF}?ctg=statistics&option=advanced_user_reports&add_column=1&report={$smarty.get.report}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._ADDCOLUMN}', 2)">{$smarty.const._ADDCOLUMN}</a>
    </span>
    <span>
     <img src = "images/16x16/order.png" alt = "{$smarty.const._CHANGECOLUMNORDER}" title = "{$smarty.const._CHANGECOLUMNORDER}" />
     <a href = "{$smarty.server.PHP_SELF}?ctg=statistics&option=advanced_user_reports&order_column=1&report={$smarty.get.report}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._CHANGECOLUMNORDER}', 2)">{$smarty.const._CHANGECOLUMNORDER}</a>
    </span>
   </div>
   <table class = "sortedTable" style = "width:100%" id = "columns_table">
    <tr class = "topTitle">
     <td>{$smarty.const._COLUMNTYPE}</td>
     <td>{$smarty.const._GRIDNAME}</td>
     <td>{$smarty.const._WIDTH}</td>
     <td>{$smarty.const._ALIGNED}</td>
     <td>{$smarty.const._TOOLS}</td>
    </tr>
    {foreach name = 'columns_list' item = "item" key = "key" from = $T_REPORT.rules.columns}
    <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"}">
     <td>{$T_REPORT_COLUMNS[$item.column]}</td>
     <td>{$item.grid_name}</td>
     <td>{if $item.width}{$item.width}%{/if}</td>
     <td>{$item.align}</td>
     <td>
      <img class = "ajaxHandle" src = "images/16x16/{if $item.default_sort}pin_green{else}pin_red{/if}.png" alt = "{$smarty.const._DEFAULTSORT}" title = "{$smarty.const._DEFAULTSORT}" onclick = "setDefaultSort(this, '{$key}')"/>
      <a href = "{$smarty.server.PHP_SELF}?ctg=statistics&option=advanced_user_reports&edit_column={$key}&report={$smarty.get.report}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._ADDCOLUMN}', 3)">
       <img class = "ajaxHandle" src = "images/16x16/edit.png" alt = "{$smarty.const._EDIT}" title = "{$smarty.const._EDIT}" onclick = "eF_js_showDivPopup();"/></a>
      <img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._DELETE}" title = "{$smarty.const._DELETE}" onclick = "deleteColumn(this, '{$key}')"/>
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
 {*{eF_template_printBlock tabber = "tools" title = $smarty.const._TOOLS data = $smarty.capture.t_report_tools_code image = '32x32/tools.png'}*}
 {eF_template_printBlock tabber = "builder" title = $smarty.const._BUILDER data = $smarty.capture.t_report_builder_code image = '32x32/generic.png'}
</div>
{/capture}


 {if $smarty.get.add || $smarty.get.edit}
  {capture name = 't_new_report_code'}
   {eF_template_printForm form=$T_ADD_REPORTING_FORM}
  {/capture}
  {eF_template_printBlock title = $smarty.const._NEWREPORT data = $smarty.capture.t_new_report_code image = '32x32/add.png'}

  {if $T_MESSAGE_TYPE == 'success'}
     <script>parent.location = '{$smarty.server.PHP_SELF}?ctg=statistics&option=advanced_user_reports&report={$T_ADDED_REPORT}&tab=builder';</script>
  {/if}
 {elseif (isset($smarty.get.edit_condition) || isset($smarty.get.add_condition)) && $smarty.get.report}
  {capture name = 't_add_condition_code'}
   {$T_ADD_CONDITION_FORM.javascript}
   <form {$T_ADD_CONDITION_FORM.attributes}>
       {$T_ADD_CONDITION_FORM.hidden}
       <table class = "formElements">
           <tr><td class = "labelCell">{$T_ADD_CONDITION_FORM.condition.label}:&nbsp;</td>
               <td class = "elementCell">{$T_ADD_CONDITION_FORM.condition.html}</td></tr>
   {foreach name = 'specifications_list' item = "item" key = "key" from = $T_CONDITIONS}
           <tr class = "specification" {if !$key || !isset($T_EDITED_CONDITION) || $T_EDITED_CONDITION.condition != $key}style = "display:none"{/if} {if $key}id = "specification_{$key}"{/if}><td class = "labelCell">{$item.name}:&nbsp;</td>
               <td class = "elementCell">
      {if $item.negation}
       <select name = "negation_{$key}">
        {foreach name = 'negation_list' item = "option" key = "value" from = $item.negation}
        <option value = "{$value}" {if isset($T_EDITED_CONDITION) && $T_EDITED_CONDITION.negation == $value}selected{/if}>{$option}</option>
        {/foreach}
       </select>
      {/if}
      {if $item.additional_options}
       <select name = "additional_{$key}" onchange = "additionalSpecificationChanged(this, '{$key}')">
        {foreach name = 'additional_options_list' item = "option" key = "value" from = $item.additional_options}
        <option value = "{$value}" {if isset($T_EDITED_CONDITION) && $T_EDITED_CONDITION.additional == $value}selected{/if}>{$option}</option>
        {/foreach}
       </select>
      {/if}
      {if $item.type == 'text'}
       <input type = "text" name = "option_{$key}" value = "{$T_EDITED_CONDITION.option}" class = "inputText"/>
      {elseif $item.type == 'select'}
       <select name = "option_{$key}">
       {foreach name = 'options_list' item = "option" key = "value" from = $item.values}
        <option value = "{$value}">{$option}</option>
       {/foreach}
       </select>
      {elseif $item.type == 'date'}
       <span id = "{$key}_first_date" {if $T_EDITED_CONDITION.additional != 'between'}style = "display:none"{/if}>
       {eF_template_html_select_date prefix="from_$key" time=$FROM_TIMESTAMP start_year="-5" end_year="+1"}
       </span>
       <span id = "{$key}_second_date" {if $T_EDITED_CONDITION.additional != 'between'}style = "display:none"{/if}>
       {$smarty.const._AND}
       {eF_template_html_select_date prefix="to_$key" time=$TO_TIMESTAMP start_year="-5" end_year="+1"}
       </span>
      {/if}
      </td></tr>
   {/foreach}
           <tr><td class = "labelCell">{$T_ADD_CONDITION_FORM.relation.label}:&nbsp;</td>
               <td class = "elementCell">{$T_ADD_CONDITION_FORM.relation.html}</td></tr>
           <tr><td class = "labelCell"></td>
               <td class = "infoCell">{$smarty.const._ANDTAKESPRECEDENCE}</td></tr>
           <tr><td></td>
               <td class = "submitCell">{$T_ADD_CONDITION_FORM.submit.html}</td></tr>
       </table>
   </form>

  {/capture}
  {eF_template_printBlock title = $smarty.const._ADDCONDITION data = $smarty.capture.t_add_condition_code image = '32x32/add.png'}

  {if $T_MESSAGE_TYPE == 'success'}
     <script>parent.location = parent.location.toString()+'&tab=builder';</script>
  {/if}
 {elseif (isset($smarty.get.edit_column) || isset($smarty.get.add_column)) && $smarty.get.report}
  {capture name = 't_add_column_code'}
   {eF_template_printForm form=$T_ADD_COLUMN_FORM}
  {/capture}
  {eF_template_printBlock title = $smarty.const._ADDCOLUMN data = $smarty.capture.t_add_column_code image = '32x32/add.png'}

  {if $smarty.get.message_type == 'success' && !$smarty.get.post_another}
     <script>parent.location = '{$smarty.server.PHP_SELF}?ctg=statistics&option=advanced_user_reports&report={$smarty.get.report}&tab=builder';</script>
  {/if}

 {elseif $smarty.get.order_column && $smarty.get.report}

  {capture name = 'column_tree'}
   <ul id = "dhtmlgoodies_column_tree" class = "dhtmlgoodies_tree">
   {foreach name = 'columns_list' key = 'id' item = 'column' from = $T_ORDER_COLUMNS}
    <li id = "dragtree_{$id+1}" noChildren = "true">
     <a class = "drag_tree_columns" href = "javascript:void(0)"> {$T_REPORT_COLUMNS[$column.column]}</a>
    </li>
   {/foreach}
   </ul>
  {/capture}

  {capture name = 'columns_treeTotal'}
   <table style = "width:100%">
    <tr><td class = "mediumHeader popUpInfoDiv" style = "width:90%">{$smarty.const._DRAGITEMSTOCHANGEORDER}</td></tr>
    <tr><td>&nbsp;</td></tr>
    <tr><td>&nbsp;</td></tr>
    <tr><td>{$smarty.capture.column_tree}</td></tr>
    <tr><td>&nbsp;</td></tr>
    <tr><td><input class = "flatButton" type="button" onclick="saveColumnTree(this)" value="{$smarty.const._SAVECHANGES}"></td></tr>
   </table>
  {/capture}
  {eF_template_printBlock title = $smarty.const._CHANGEORDER data = $smarty.capture.columns_treeTotal image = '32x32/order.png'}
 {else}
  {eF_template_printBlock title = $smarty.const._ADVANCEDUSERREPORTS data = $smarty.capture.t_tabber_code image = '32x32/users.png'}
 {/if}
