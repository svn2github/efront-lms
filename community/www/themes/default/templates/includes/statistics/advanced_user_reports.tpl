{capture name = "t_users_table_code"}
 {if $T_REPORT_NAMES}
 <div class = "headerTools">
  <span>
   <form onsubmit = "location = location.toString().replace(/&report=\d*/, '').replace(/&tab=\w*/, '')+'&report='+$('reports_list').options[$('reports_list').options.selectedIndex].value;return false">
   <span>{$smarty.const._SELECTREPORT}:&nbsp;</span>
   <select id = "reports_list" onchange = "Element.extend(this).next().focus()">
    <option value = "0" {if $smarty.get.report==$key}selected{/if}>{$smarty.const._AVAILABLEREPORTS}</option>
   {foreach item = "item" key = "key" from = $T_REPORT_NAMES}
    <option value = "{$key}" {if $smarty.get.report==$key}selected{/if}>{$item}</option>
   {/foreach}
   </select>
   {*<input type = "image" src = "images/16x16/arrow_right.png" alt = "{$smarty.const._SHOW}" title = "{$smarty.const._SHOW}" nclick = "Element.extend(this);location = location.toString().replace(/&report=\d*/, '').replace(/&tab=\w*/, '')+'&report='+this.previous().options[this.previous().options.selectedIndex].value" style = "border:0px;background-color:inherit"/>*}
   <input type = "submit" value = "{$smarty.const._SHOW}" class = "flatButton"/>
   </form>
  </span>
 </div>
 {else}
 <div>{$smarty.const._NOREPORTSFOUNDINTHESYSTEM}. {if $smarty.session.s_type=='administrator'}<a href = "{$smarty.server.PHP_SELF}?ctg=statistics&option=advanced_user_reports&tab=builder&add=1&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._ADDREPORT}', 0)">{$smarty.const._YOUMAYCREATEONE}</a>{/if}</div>
 {/if}
 {if $T_REPORT}

<!--ajax:usersTable-->
 <table id = "usersTable" style = "width:100%" sortBy="{$T_DEFAULT_SORT}" size = "{$T_TABLE_SIZE}" class = "sortedTable" useAjax = "1" url = "{$smarty.server.PHP_SELF}?ctg=statistics&option=advanced_user_reports&report={$smarty.get.report}&">
  <tr class = "topTitle">
   {foreach name = 't_columns_list' item = "item" key = "key" from = $T_REPORT.rules.columns}
    {if $item.status}
     {if $item.column == 'formatted_login'}{assign var = "sort" value = "login"}
     {elseif $item.column == 'course_status'}{assign var = "sort" value = "count_courses"}
     {elseif $item.column == 'lesson_status'}{assign var = "sort" value = "count_lessons"}
     {else}{assign var = "sort" value = $item.column}
     {/if}
    <td style = "{if $item.width}width:{$item.width}%;{/if}{if $item.align}text-align:{$item.align};{/if}" name = "{$sort}">{if $item.grid_name}{$item.grid_name}{else}{$T_REPORT_COLUMNS[$item.column]}{/if}</td>
    {/if}
   {/foreach}
    <td class = "centerAlign noSort">{$smarty.const._SELECT}</td>
  </tr>
  {foreach name = 'conditions_list' item = "user" key = "key" from = $T_DATA_SOURCE}
  <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if $user.active != $smarty.const._ACTIVE}deactivatedTableElement{/if}">
   {foreach name = 't_columns_list' item = "item" key = "foo" from = $T_REPORT.rules.columns}
    {if $item.status}
    <td style = "{if $item.width}width:{$item.width}%;{/if}{if $item.align}text-align:{$item.align};{/if}">
     {assign var = "entry" value = $user[$item.column]}
     {if $item.column == $T_EDIT_LINK}
      <a href = "{$smarty.server.PHP_SELF}?ctg=users&edit_user={$user.login}" class = "editLink {if !$T_CONFIGURATION.disable_tooltip}info{/if}" url = "ask_information.php?users_LOGIN={$user.login}&type=user">
     {/if}
     {if $item.column == 'branch'}
      {if !$T_EDIT_LINK}
       <a href = "{$smarty.server.PHP_SELF}?ctg=module_hcd&op=branches&edit_branch={$user.branch_ID}" class = "editLink {if !$T_CONFIGURATION.disable_tooltip}info{/if}" url = "ask_information.php?users_LOGIN={$user.login}&type=user">{$entry} {if $user.sum_branch > 1}({$user.sum_branch-1} {$smarty.const._MORE}){/if}
      {else}
       <a href = "{$smarty.server.PHP_SELF}?ctg=module_hcd&op=branches&edit_branch={$user.branch_ID}" class = "editLink">{$entry} {if $user.sum_branch > 1}({$user.sum_branch-1} {$smarty.const._MORE}){/if}</a>
      {/if}
     {elseif $item.column == 'job_description'}
      <a href = "{$smarty.server.PHP_SELF}?ctg=module_hcd&op=job_descriptions&edit_job_description={$user.job_description_ID}" class = "editLink">{$entry}</a>
     {elseif $item.column == 'course_status'}
      {if $user.count_courses}
       <a href = "{$smarty.server.PHP_SELF}?ctg=users&edit_user={$user.login}&op=status&tab=courses" class = "editLink {if !$T_CONFIGURATION.disable_tooltip}info{/if}" url = "ask_information.php?users_LOGIN={$user.login}&type=course_status">
        {$user.course_status}
        {if !$T_CONFIGURATION.disable_tooltip}
         <span class = "tooltipSpan"></span>
        {/if}
       </a>
      {/if}
     {elseif $item.column == 'lesson_status'}
      {if $user.count_lessons}
       <a href = "{$smarty.server.PHP_SELF}?ctg=users&edit_user={$user.login}&op=status&tab=lessons" class = "editLink {if !$T_CONFIGURATION.disable_tooltip}info{/if}" url = "ask_information.php?users_LOGIN={$user.login}&type=course_status">
        {$user.lesson_status}
        {if !$T_CONFIGURATION.disable_tooltip}
         <span class = "tooltipSpan"></span>
        {/if}
       </a>
      {/if}
     {elseif $item.column == 'certifications'}
      {if $user.certifications}<a href = "{$smarty.server.PHP_SELF}?ctg=users&edit_user={$user.login}&op=status&tab=certifications" class = "editLink">{$user.certifications}</a>{/if}
     {elseif $item.column == 'certificate_status'}
      <a href = "{$smarty.server.PHP_SELF}?ctg=users&edit_user={$user.login}&op=status&tab=certifications" class = "editLink">{$entry}</a>
     {else}
      {$entry}
     {/if}
     {if $item.column == $T_EDIT_LINK || !$T_EDIT_LINK && $item.column=='branch'}
      {if !$T_CONFIGURATION.disable_tooltip}
       <span class = "tooltipSpan"></span>
      {/if}
      </a>
     {/if}
    </td>
    {/if}
   {/foreach}
    <td class = "centerAlign">
     <input type = "checkbox" id = "check_{$user.login}" onclick = "toggleUserDynamicGroup(this)"/>
     {*<img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._REMOVEFROMSET}" title = "{$smarty.const._REMOVEFROMSET}" onclick = "removeFromSet(this, '{$user.login}')"/>*}
    </td>
  </tr>
  {foreachelse}
  <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "{if $T_REPORT.rules.columns}{$T_REPORT.rules.columns|@sizeof}{else}3{/if}">{$smarty.const._NODATAFOUND}</td></tr>
  {/foreach}
 </table>
<!--/ajax:usersTable-->

 <div class = ""><span>{$smarty.const._CURREPAGEOPERATIONS}:</span>
  <img class = "ajaxHandle" src = "images/16x16/refresh.png" alt = "{$smarty.const._REFRESHTABLE}" title = "{$smarty.const._REFRESHTABLE}" onclick = "eF_js_rebuildTable('usersTable', 0, 'null', 'desc');"/>
  <img class = "ajaxHandle" src = "images/file_types/xls.png" alt = "{$smarty.const._EXPORTTOXLS}" title = "{$smarty.const._EXPORTTOXLS}" onclick = "exportXls(this);"/>
  <img class = "ajaxHandle" src = "images/file_types/txt.png" alt = "{$smarty.const._EXPORTTOCSV}" title = "{$smarty.const._EXPORTTOCSV}" onclick = "exportCsv(this);"/>
  {if $smarty.session.s_type == 'administrator'}
   <img class = "ajaxHandle" src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._ACTIVATE}" title = "{$smarty.const._ACTIVATE}" onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) applyOperation(this, 'activate');"/>
   <img class = "ajaxHandle" src = "images/16x16/trafficlight_red.png" alt = "{$smarty.const._DEACTIVATE}" title = "{$smarty.const._DEACTIVATE}" onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) applyOperation(this, 'deactivate');"/>
   <img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._ARCHIVE}" title = "{$smarty.const._ARCHIVE}" onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) applyOperation(this, 'archive');"/>
  {/if}
  <img class = "ajaxHandle" src = "images/16x16/undo.png" alt = "{$smarty.const._RESETLEARNINGPROGRESS}" title = "{$smarty.const._RESETLEARNINGPROGRESS}"onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) applyOperation(this, 'reset');"/>
  <img class = "ajaxHandle" src = "images/16x16/users.png" alt = "{$smarty.const._GROUPOPERATIONS}" title = "{$smarty.const._GROUPOPERATIONS}" onclick = "eF_js_showDivPopup('{$smarty.const._GROUPOPERATIONS}', 0, 'add_group_table')"/>
  <img class = "ajaxHandle" src = "images/16x16/courses.png" alt = "{$smarty.const._COURSEOPERATIONS}" title = "{$smarty.const._COURSEOPERATIONS}" onclick = "eF_js_showDivPopup('{$smarty.const._COURSEOPERATIONS}', 0, 'add_course_table')"/>
  <img class = "ajaxHandle" src = "images/16x16/lessons.png" alt = "{$smarty.const._LESSONOPERATIONS}" title = "{$smarty.const._LESSONOPERATIONS}" onclick = "eF_js_showDivPopup('{$smarty.const._LESSONOPERATIONS}', 0, 'add_lesson_table')"/>
  {*<img class = "ajaxHandle" src = "images/16x16/certificate.png" alt = "{$smarty.const._CERTIFICATEOPERATIONS}" title = "{$smarty.const._CERTIFICATEOPERATIONS}"onclick = "eF_js_showDivPopup('{$smarty.const._CERTIFICATEOPERATIONS}', 0, 'add_certificate_table')"/>*}
  {*<img class = "ajaxHandle" src = "images/16x16/mail.png" alt = "{$smarty.const._SENDEMAIL}" title = "{$smarty.const._SENDEMAIL}" onclick = "eF_js_showDivPopup('{$smarty.const._SENDEMAIL}', 2);$('popup_frame').src='{$smarty.server.PHP_SELF}?ctg=messages&add=1&popup=1';"/>*}
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


{capture name = "t_report_builder_code"}
 <div class = "headerTools">
   <form onsubmit = "location = location.toString().replace(/&report=\d*/, '').replace(/&tab=\w*/, '')+'&tab=builder&report='+$('reports_list_edit').options[$('reports_list_edit').options.selectedIndex].value;return false">
  <span>
   <img src = "images/16x16/add.png" alt = "{$smarty.const._ADDREPORT}" title = "{$smarty.const._ADDREPORT}" />
   <a href = "{$smarty.server.PHP_SELF}?ctg=statistics&option=advanced_user_reports&add=1&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._REPORT}', 0)">{$smarty.const._ADDREPORT}</a>
  </span>
  {if $T_REPORT_NAMES}
  <span>
   <img src = "images/16x16/edit.png" alt = "{$smarty.const._EDITREPORT}" title = "{$smarty.const._EDITREPORT}" />
   {$smarty.const._EDITREPORT}:&nbsp;
   <select id = "reports_list_edit" onchange = "Element.extend(this).next().focus(); if (this.options[this.options.selectedIndex].value != '0') $('delete_report').show(); else $('delete_report').hide();">
    <option value = "0" {if $smarty.get.report==$key}selected{/if}>{$smarty.const._AVAILABLEREPORTS}</option>
   {foreach item = "item" key = "key" from = $T_REPORT_NAMES}
    <option value = "{$key}" {if $smarty.get.report==$key}selected{/if}>{$item}</option>
   {/foreach}
   </select>
   {*<input type = "image" src = "images/16x16/arrow_right.png" alt = "{$smarty.const._SHOW}" title = "{$smarty.const._SHOW}" nclick = "Element.extend(this);location = location.toString().replace(/&report=\d*/, '').replace(/&tab=\w*/, '')+'&report='+this.previous().options[this.previous().options.selectedIndex].value" style = "border:0px;background-color:inherit"/>*}
   <input type = "submit" value = "{$smarty.const._SHOW}" class = "flatButton"/>
   <img id = "delete_report" {if !$smarty.get.report}style = "display:none"{/if} class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._DELETE}" title = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) deleteReport(this, $('reports_list_edit').options[$('reports_list_edit').options.selectedIndex].value)"/>
  </span>
   </form>
  <hr/>
  {/if}
 </div>

 {if $T_REPORT}
   <div class = "mediumHeader">
    <span>{$smarty.const._VIEWINGREPORT}: {$T_REPORT.name}</span>
    <a href = "{$smarty.server.PHP_SELF}?ctg=statistics&option=advanced_user_reports&edit={$smarty.get.report}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._REPORT}', 0)">
     <img class = "handle" src = "images/16x16/edit.png" alt = "{$smarty.const._EDIT}" title = "{$smarty.const._EDIT}" onclick = ""/>
    </a>
   </div>
   <div class = "headerTools">
    <span>
     <img src = "images/16x16/add.png" alt = "{$smarty.const._ADDCONDITION}" title = "{$smarty.const._ADDCONDITION}" />
     <a href = "{$smarty.server.PHP_SELF}?ctg=statistics&option=advanced_user_reports&add_condition=1&report={$smarty.get.report}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._ADDCONDITION}', 3)">{$smarty.const._ADDCONDITION}</a>
     {*<a href = "javascript:void(0)" onclick = "addCondition()">{$smarty.const._ADDCONDITION}</a>*}
    </span>
    <span>
     <img src = "images/16x16/order.png" alt = "{$smarty.const._CHANGECONDITIONORDER}" title = "{$smarty.const._CHANGECONDITIONORDER}" />
     <a href = "{$smarty.server.PHP_SELF}?ctg=statistics&option=advanced_user_reports&order_condition=1&report={$smarty.get.report}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._CHANGECONDITIONORDER}', 2)">{$smarty.const._CHANGECONDITIONORDER}</a>
    </span>
   </div>

<!--ajax:conditionsTable-->
 <table id = "conditionsTable" style = "width:100%" size = "{$T_TABLE_SIZE}" class = "sortedTable" useAjax = "1" url = "{$smarty.server.PHP_SELF}?ctg=statistics&option=advanced_user_reports&report={$smarty.get.report}&">
    <tr class = "topTitle">
     {*<td class = "noSort" name = "index" class = "centerAlign">{$smarty.const._INDEX}</td>*}
     <td class = "noSort" name = "condition">{$smarty.const._CONDITIONTYPE}</td>
     <td class = "noSort" name = "option">{$smarty.const._CONDITIONSPECIFICATION}</td>
     <td class = "centerAlign noSort" name = "relation">{$smarty.const._RELATIONWITHTHEFOLLOWINGCONDITION}</td>
     <td class = "centerAlign noSort" name = "status" class = "centerAlign">{$smarty.const._STATUS}</td>
     <td class = "centerAlign noSort">{$smarty.const._TOOLS}</td>
    </tr>
    {foreach name = 'conditions_list' item = "item" key = "key" from = $T_DATA_SOURCE}
    <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"}">
     {*<td class = "centerAlign">{$key+1}</td>*}
     <td>{$T_CONDITIONS[$item.condition].name}</td>
     <td>
      {$T_CONDITIONS[$item.condition].negation[$item.negation]}
      {$T_CONDITIONS[$item.condition].additional_options[$item.additional]}
      {if $item.condition == 'lesson'}
       {$T_LESSONS[$item.option]}
      {elseif $item.condition == 'sex'}
       {if $entry == 0}{$smarty.const._MALE}{else}{$smarty.const._FEMALE}{/if}
      {elseif $item.condition == 'category'}
       {$T_CATEGORIES[$item.option]}
      {elseif $item.condition == 'course'}
       {$T_COURSES[$item.option]}
      {elseif $item.condition == 'group'}
       {$T_GROUPS[$item.option]}
      {elseif $item.condition == 'active'}
       {if $item.option == 1}{$smarty.const._ACTIVE}{else}{$smarty.const._INACTIVE}{/if}
      {elseif $item.condition == 'branch' || $item.condition == 'branch_tree'}
       {$T_BRANCHES[$item.option]}
      {elseif $item.condition == 'learning_status'}
       {$T_CONDITIONS[$item.condition].values[$item.option]}
      {elseif $item.condition == 'skill'}
       {$T_SKILLS[$item.option]}
      {elseif $item.condition == 'job_description'}
       {$T_JOBS[$item.option]}
      {elseif $item.condition == 'user_type'}
       {$T_ROLE_NAMES[$item.option]}
      {else}
       {$item.option}
      {/if}
      {if $item.from}#filter:timestamp-{$item.from}# {$smarty.const._AND} #filter:timestamp-{$item.to}#{/if}
     </td>
     <td class = "centerAlign">{$item.relation}</td>
     <td class = "centerAlign"><span style = "display:none">{$item.status}</span><img class = "ajaxHandle" src = "images/16x16/{if $item.status}trafficlight_green{else}trafficlight_red{/if}.png" alt = "{$smarty.const._STATUS}" title = "{$smarty.const._STATUS}" onclick = "setConditionStatus(this, '{$key}')"/></td>
     <td class = "centerAlign">
      <a href = "{$smarty.server.PHP_SELF}?ctg=statistics&option=advanced_user_reports&edit_condition={$key}&report={$smarty.get.report}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._ADDCONDITION}', 3)">
       <img class = "ajaxHandle" src = "images/16x16/edit.png" alt = "{$smarty.const._EDIT}" title = "{$smarty.const._EDIT}" onclick = "eF_js_showDivPopup();"/></a>
      <img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._DELETE}" title = "{$smarty.const._DELETE}" onclick = "deleteCondition(this, '{$key}')"/>
     </td>
    </tr>
    {foreachelse}
    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "6">{$smarty.const._NODATAFOUND}</td></tr>
    {/foreach}
   </table>
<!--/ajax:conditionsTable-->

   <br/>
   <div class = "headerTools">
{*
    <span>
     <img src = "images/16x16/add.png" alt = "{$smarty.const._ADDCOLUMN}" title = "{$smarty.const._ADDCOLUMN}" />
     <a href = "{$smarty.server.PHP_SELF}?ctg=statistics&option=advanced_user_reports&add_column=1&report={$smarty.get.report}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._ADDCOLUMN}', 2)">{$smarty.const._ADDCOLUMN}</a>
    </span>
*}
    <span>
     <img src = "images/16x16/order.png" alt = "{$smarty.const._CHANGECOLUMNORDER}" title = "{$smarty.const._CHANGECOLUMNORDER}" />
     <a href = "{$smarty.server.PHP_SELF}?ctg=statistics&option=advanced_user_reports&order_column=1&report={$smarty.get.report}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._CHANGECOLUMNORDER}', 2)">{$smarty.const._CHANGECOLUMNORDER}</a>
    </span>
   </div>
   <script>translations['left'] = '{$smarty.const._LEFT}';translations['center'] = '{$smarty.const._CENTER}';translations['right'] = '{$smarty.const._RIGHT}';</script>
   <table class = "sortedTable" style = "width:100%" id = "columns_table">
    <tr class = "topTitle">
     <td class = "noSort">{$smarty.const._COLUMNTYPE}</td>
     <td class = "noSort">{$smarty.const._GRIDNAME}</td>
     <td class = "centerAlign noSort">{$smarty.const._WIDTH}</td>
     <td class = "noSort">{$smarty.const._ALIGNED}</td>
     <td class = "centerAlign noSort">{$smarty.const._DEFAULTSORT}</td>
     <td class = "centerAlign noSort">{$smarty.const._STATUS}</td>
     <td class = "centerAlign noSort">{$smarty.const._TOOLS}</td>
    </tr>
    {foreach name = 'columns_list' item = "item" key = "key" from = $T_REPORT.rules.columns}
    <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"}">
     <td>{$T_REPORT_COLUMNS[$item.column]}</td>
     <td>{$item.grid_name}</td>
     <td class = "centerAlign">{if $item.width}{$item.width}%{/if}</td>
     <td>
     {if $item.align == 'left'}
      <span>{$smarty.const._LEFT}</span>
     {elseif $item.align == 'center'}
      <span>{$smarty.const._CENTER}</span>
     {elseif $item.align == 'right'}
      <span>{$smarty.const._RIGHT}</span>
     {/if}
      &nbsp;<img src = "images/16x16/refresh.png" alt = "{$smarty.const._CHANGE}" title = "{$smarty.const._CHANGE}" class = "handle" onclick = "setAlign(this, '{$key}')"/>
     </td>
     <td class = "centerAlign"><span style = "display:none">{$item.default_sort}</span><img class = "ajaxHandle" src = "images/16x16/{if $item.default_sort}pin_green{else}pin_red{/if}.png" alt = "{$smarty.const._DEFAULTSORT}" title = "{$smarty.const._DEFAULTSORT}" onclick = "setDefaultSort(this, '{$key}')"/></td>
     <td class = "centerAlign"><span style = "display:none">{$item.status}</span><img class = "ajaxHandle" src = "images/16x16/{if $item.status}trafficlight_green{else}trafficlight_red{/if}.png" alt = "{$smarty.const._STATUS}" title = "{$smarty.const._STATUS}" onclick = "setColumnStatus(this, '{$key}')"/></td>
     <td class = "centerAlign">
      <a href = "{$smarty.server.PHP_SELF}?ctg=statistics&option=advanced_user_reports&edit_column={$key}&report={$smarty.get.report}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._EDITCOLUMN}', 3)">
       <img class = "ajaxHandle" src = "images/16x16/edit.png" alt = "{$smarty.const._EDIT}" title = "{$smarty.const._EDIT}" onclick = "eF_js_showDivPopup();"/></a>
     </td>
    </tr>
    {foreachelse}
    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "7">{$smarty.const._NODATAFOUND}</td></tr>
    {/foreach}
   </table>
 {/if}
{/capture}


{capture name = 't_tabber_code'}
<div class = "tabber">
 {eF_template_printBlock tabber = "users" title = $smarty.const._REPORTS data = $smarty.capture.t_users_table_code image = '32x32/reports.png'}
 {eF_template_printBlock tabber = "builder" title = $smarty.const._BUILDER data = $smarty.capture.t_report_builder_code image = '32x32/generic.png'}
</div>
{/capture}


 {if $smarty.get.add || $smarty.get.edit}
  {capture name = 't_new_report_code'}
   {eF_template_printForm form=$T_ADD_REPORTING_FORM}
  {/capture}
  {eF_template_printBlock title = $smarty.const._REPORT data = $smarty.capture.t_new_report_code image = '32x32/reports.png'}

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
        <option value = "{$value}" {if isset($T_EDITED_CONDITION) && $T_EDITED_CONDITION.option == $value}selected{/if}>{$option}</option>
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
      {if $item.score}
       {$smarty.const._WITHSCORE}
       <select name = "score_relation_{$key}">
        <option value = "atleast" {if isset($T_EDITED_CONDITION) && $T_EDITED_CONDITION.score_relation == "atleast"}selected{/if}>{$smarty.const._ATLEAST}</option>
        <option value = "atmost" {if isset($T_EDITED_CONDITION) && $T_EDITED_CONDITION.score_relation == "atmost"}selected{/if}>{$smarty.const._ATMOST}</option>
        <option value = "equal" {if isset($T_EDITED_CONDITION) && $T_EDITED_CONDITION.score_relation == "equal"}selected{/if}>{$smarty.const._EQUALTO}</option>
       </select>
       <select name = "score_{$key}">
        {foreach name = 'score_options_list' item = "option" key = "value" from = $item.score}
        <option value = "{$value}" {if isset($T_EDITED_CONDITION) && $T_EDITED_CONDITION.score == $value}selected{/if}>{$option}</option>
        {/foreach}
       </select>
      {/if}
      </td></tr>
   {/foreach}
           <tr><td class = "labelCell">{$T_ADD_CONDITION_FORM.relation.label}:&nbsp;</td>
               <td class = "elementCell">{$T_ADD_CONDITION_FORM.relation.html}</td></tr>
           <tr><td class = "labelCell"></td>
               <td class = "infoCell">{$smarty.const._ANDTAKESPRECEDENCE}</td></tr>
           <tr><td></td>
               <td class = "submitCell">{$T_ADD_CONDITION_FORM.submit.html} {$T_ADD_CONDITION_FORM.submit_another.html}</td></tr>
       </table>
   </form>

  {/capture}
  {eF_template_printBlock title = $smarty.const._ADDCONDITION data = $smarty.capture.t_add_condition_code image = '32x32/add.png'}

  {if $smarty.get.message_type == 'success' && !$smarty.get.post_another}
     {*<script>parent.location = '{$smarty.server.PHP_SELF}?ctg=statistics&option=advanced_user_reports&report={$smarty.get.report}&tab=builder';</script>*}
          <script>finishedAddingConditions = 1;</script>

  {/if}
 {elseif (isset($smarty.get.edit_column) || isset($smarty.get.add_column)) && $smarty.get.report}
  {capture name = 't_add_column_code'}
   {eF_template_printForm form=$T_ADD_COLUMN_FORM}
  {/capture}
  {eF_template_printBlock title = $smarty.const._EDITCOLUMN data = $smarty.capture.t_add_column_code image = '32x32/add.png'}

  {if $smarty.get.message_type == 'success' && !$smarty.get.post_another}
     <script>parent.location = '{$smarty.server.PHP_SELF}?ctg=statistics&option=advanced_user_reports&report={$smarty.get.report}&tab=builder';</script>
  {/if}

 {elseif $smarty.get.order_condition && $smarty.get.report}

  {capture name = 'condition_tree'}
   <ul id = "dhtmlgoodies_condition_tree" class = "dhtmlgoodies_tree">
   {foreach name = 'conditions_list' key = 'id' item = 'condition' from = $T_ORDER_CONDITIONS}
    {if $condition.status}
    <li id = "dragtree_{$id+1}" noChildren = "true">
     <a class = "drag_tree_conditions" href = "javascript:void(0)"> {$T_CONDITIONS[$condition.condition].name}</a>
    </li>
    {/if}
   {/foreach}
   </ul>
  {/capture}

  {capture name = 'conditions_treeTotal'}
   <table style = "width:100%">
    <tr><td class = "mediumHeader popUpInfoDiv" style = "width:90%">{$smarty.const._DRAGITEMSTOCHANGEORDER}</td></tr>
    <tr><td>&nbsp;</td></tr>
    <tr><td>&nbsp;</td></tr>
    <tr><td>{$smarty.capture.condition_tree}</td></tr>
    <tr><td>&nbsp;</td></tr>
    <tr><td><input class = "flatButton" type="button" onclick="saveConditionTree(this)" value="{$smarty.const._SAVECHANGES}"></td></tr>
   </table>
  {/capture}
  {eF_template_printBlock title = $smarty.const._CHANGEORDER data = $smarty.capture.conditions_treeTotal image = '32x32/order.png'}
 {elseif $smarty.get.order_column && $smarty.get.report}

  {capture name = 'column_tree'}
   <ul id = "dhtmlgoodies_column_tree" class = "dhtmlgoodies_tree">
   {foreach name = 'columns_list' key = 'id' item = 'column' from = $T_ORDER_COLUMNS}
    {if $column.status}
    <li id = "dragtree_{$id+1}" noChildren = "true">
     <a class = "drag_tree_columns" href = "javascript:void(0)"> {$T_REPORT_COLUMNS[$column.column]}</a>
    </li>
    {/if}
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
  {if $smarty.session.s_type == 'administrator'}
   {eF_template_printBlock title = $smarty.const._ADVANCEDUSERREPORTS data = $smarty.capture.t_tabber_code image = '32x32/users.png'}
  {else}
   {eF_template_printBlock title = $smarty.const._REPORTS data = $smarty.capture.t_users_table_code image = '32x32/reports.png'}
  {/if}
 {/if}
