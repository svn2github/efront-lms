{if $smarty.get.add_skill || $smarty.get.edit_skill}

 {capture name = 't_skill_code'}
  {$T_SKILLS_FORM.javascript}
  <form {$T_SKILLS_FORM.attributes}>
  {$T_SKILLS_FORM.hidden}
  <table class = "formElements">
   <tr><td class = "labelCell">{$T_SKILLS_FORM.skill_description.label}:&nbsp;</td>
    <td class = "elementCell">{$T_SKILLS_FORM.skill_description.html}</td>
   </tr>
   <tr><td class = "labelCell">{$T_SKILLS_FORM.category.label}:&nbsp;</td>
    <td class = "elementCell">
     {$T_SKILLS_FORM.category.html}
     {if $_change_}
      <a href="{$smarty.session.s_type}.php?ctg=module_hcd&op=skill_cat&add_skill_cat=1&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._ADDSKILLCATEGORY}', 2)" target = "POPUP_FRAME"><img src='images/16x16/add.png' title= '{$smarty.const._ADDSKILLCATEGORY}' alt = '{$smarty.const._ADDSKILLCATEGORY}' border='0' /></a>
        <a id = "edit_skill_cat" href="{$smarty.session.s_type}.php?ctg=module_hcd&op=skill_cat&edit_skill_cat={$T_DEFAULT_CATEGORY}&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._EDITSKILLCATEGORY}', 2)" target = "POPUP_FRAME" {if $T_DEFAULT_CATEGORY == ""}style="visibility:hidden"{/if}><img src='images/16x16/edit.png' title= '{$smarty.const._EDITSKILLCATEGORY}' alt = '{$smarty.const._EDITSKILLCATEGORY}' border='0' /></a>
        <a id = "del_skill_cat" href="{$smarty.session.s_type}.php?ctg=module_hcd&op=skill_cat&del_skill_cat={$T_DEFAULT_CATEGORY}" onclick = "return confirm('{$smarty.const._AREYOUSUREYOUWANTTODISMISSTHISSKILLCATEGORY}')" {if $T_DEFAULT_CATEGORY == ""}style="visibility:hidden"{/if}><img src='images/16x16/error_delete.png' title= '{$smarty.const._DELETESKILLCATEGORY}' alt = '{$smarty.const._DELETESKILLCATEGORY}' border='0' /></a>
     {/if}
    </td>
   </tr>
   <tr><td></td>
    <td class = "submitCell">
    {if $_change_}
     {$T_SKILLS_FORM.submit_skill_details.html}
    {/if}
    </td>

   </tr>
   </table>
  </form>
 {/capture}

 {if $smarty.get.edit_skill}
  {capture name = 't_employees_code'}
{if !$T_SORTED_TABLE || $T_SORTED_TABLE == 'usersSkillsTable'}
<!--ajax:usersSkillsTable-->
  <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "0" id = "usersSkillsTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.session.s_type}.php?ctg=module_hcd&op=skills&edit_skill={$smarty.get.edit_skill}&">
   <tr class = "topTitle">
    <td class = "topTitle" name="login">{$smarty.const._USER}</td>
    <td class = "topTitle" name="specification">{$smarty.const._SPECIFICATION}</td>
    <td class = "topTitle centerAlign" name="score">{$smarty.const._SCORE}</td>
    <td class = "topTitle noSort centerAlign" noSort >{$smarty.const._OPERATIONS}</td>
   </tr>
   {assign var = "employees_found" value = 0}
   {foreach name = 'users_list' key = 'key' item = 'user' from = $T_DATA_SOURCE}
   {if $user.skill_ID == $smarty.get.edit_skill}
    {assign var = "employees_found" value = '1'}
    <tr class = "{cycle values = "oddRowColor, evenRowColor"} {if !$user.active}deactivatedTableElement{/if}">
     <td>
     {if ($user.pending == 1)}
      <a href = "{$smarty.session.s_type}.php?ctg=personal&user={$user.login}&op=profile" class = "editLink" style="color:red;">#filter:login-{$user.login}#</a>
     {elseif ($user.active == 1)}
      <a href = "{$smarty.session.s_type}.php?ctg=personal&user={$user.login}&op=profile" class = "editLink">#filter:login-{$user.login}#</a>
     {else}
      #filter:login-{$user.login}#
     {/if}
      </td>
     <td>{$user.specification}</td>
     <td class = "centerAlign">{if $user.score}{$user.score}%{/if}</td>
     <td class = "centerAlign">
      <a href="{$smarty.session.s_type}.php?ctg=statistics&option=user&sel_user={$user.login}"><img border = "0" src = "images/16x16/reports.png" title = "{$smarty.const._STATISTICS}" alt = "{$smarty.const._STATISTICS}" /></a>
     {if $_change_}
      {if $user.active == 1}
       <a href = "{$smarty.session.s_type}.php?ctg=personal&user={$user.login}&op=profile&tab=skills" class = "editLink"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
      {else}
       <img class="handle" src = "images/16x16/edit.png" class = "inactiveImage" title = "{$smarty.const._UNPRIVILEGEDATTEMPT}" alt = "{$smarty.const._UNPRIVILEGEDATTEMPT}" />
      {/if}
     {/if}
      {if $_change_}
       <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "removeSkillFromUser(this, '{$user.login}', '{$smarty.get.edit_skill}')"/>
      {/if}
     </td>
    </tr>
   {/if}
   {foreachelse}
    {assign var = "employees_found" value = '1'}
    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "4">{$smarty.const._NODATAFOUND}</td></tr>
   {/foreach}
   {if !$employees_found}
    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "4">{$smarty.const._NODATAFOUND}</td></tr>
   {/if}
  </table>
<!--/ajax:usersSkillsTable-->
{/if}
  {/capture}

  {capture name = 't_employees_to_skill'}
  <form method="post" action="{$smarty.server.PHP_SELF}?ctg=module_hcd&op=skills&edit_skill={$smarty.get.edit_skill}"&tab="assign_employees">
{if !$T_SORTED_TABLE || $T_SORTED_TABLE == 'skillEmployeesTable'}
<!--ajax:skillEmployeesTable-->
  <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "0" id = "skillEmployeesTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.session.s_type}.php?ctg=module_hcd&op=skills&edit_skill={$smarty.get.edit_skill}&show_all=1&">
   <tr class = "topTitle">
    <td class = "topTitle" name="users_login">{$smarty.const._USER}</td>
    <td class = "topTitle" name="specification">{$smarty.const._SPECIFICATION}</td>
    <td class = "topTitle centerAlign" name="score">{$smarty.const._SCORE}</td>
    <td class = "topTitle centerAlign" name="skill_ID">{$smarty.const._CHECK}</td>
   </tr>
   {assign var = "employees_found" value = '0'}
   {foreach name = 'users_list' key = 'key' item = 'user' from = $T_DATA_SOURCE}
    {if $user.active}
     {assign var = "employees_found" value = '1'}
     <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
      <td>
      {if ($user.pending == 1)}
       <a href = "{$smarty.session.s_type}.php?ctg=personal&user={$user.login}&op=profile" class = "editLink" style="color:red;">#filter:login-{$user.login}#</a>
      {else}
       #filter:login-{$user.login}#
      {/if}
      </td>
      <td>
       <input class="inputText" width = "*" type="text" id="spec_skill_{$user.login}" value="{$user.specification}" onchange="ajaxSkillUserPost('{$user.login}', this);" style="{if $user.skill_ID != $smarty.get.edit_skill}display:none{/if}">
      </td>
      <td class = "centerAlign">
       <input class="inputText" type="text" id="spec_skill_score_{$user.login}" value="{if $user.score}{$user.score}{else}100{/if}" onchange="ajaxSkillUserPost('{$user.login}', this);" style="width:30px;{if $user.skill_ID != $smarty.get.edit_skill}display:none{/if}">
      </td>
      <td class = "centerAlign">
       <input type = "checkbox" class = "inputCheckBox" name = "{$user.login}" id="skill_to_{$user.login}" {if $_change_} onclick="$('spec_skill_{$user.login}').toggle();$('spec_skill_score_{$user.login}').toggle(); ajaxSkillUserPost('{$user.login}', this);"{else}disabled{/if} {if $user.skill_ID == $smarty.get.edit_skill}checked{/if}>
      </td>
     </tr>
    {/if}
   {foreachelse}
    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "4">{$smarty.const._NODATAFOUND}</td></tr>
   {/foreach}
   {if !$employees_found}
    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "4">{$smarty.const._NODATAFOUND}</td></tr>
   {/if}
   </table>
<!--/ajax:skillEmployeesTable-->
{/if}
  </form>
  {/capture}
 {/if}

  {if $smarty.get.add_skill}
   {eF_template_printBlock title = $smarty.const._NEWSKILL data = $smarty.capture.t_skill_code image = '32x32/tools.png'}
   {if $T_MESSAGE_TYPE == 'success'}
      <script>parent.location = parent.location</script>
   {/if}
  {else}
   {capture name = "t_edit_skill_code"}
   <div class="tabber">
    <div class="tabbertab">
     <h3>{$smarty.const._EDITSKILL}</h3>
     {eF_template_printBlock title = $smarty.const._EDITSKILL|cat:"&nbsp;<span class='innerTableName'>&quot;`$T_SKILL_NAME`&quot;</span>" data = $smarty.capture.t_skill_code image = '32x32/tools.png'}
     {eF_template_printBlock title = $smarty.const._EMPLOYEES|cat:$smarty.const._HAVINGSKILL|cat:"<span class='innerTableName'>&quot;`$T_SKILL_NAME`&quot;</span>" data = $smarty.capture.t_employees_code image = '32x32/user.png'}
    </div>
    <script> var myform = "employees_to_skill";</script>
    {eF_template_printBlock tabber = "assign_employees" title = $smarty.const._ASSIGNEMPLOYEESTHESKILL|cat:"<span class='innerTableName'>&quot;`$T_SKILL_NAME`&quot;</span>" data = $smarty.capture.t_employees_to_skill image = '32x32/tools.png'}
    <div>
    {/capture}
    {eF_template_printBlock title = $smarty.const._EDITSKILL data = $smarty.capture.t_edit_skill_code image = '32x32/tools.png'}
  {/if}

{else}

 {capture name = 't_skills_code'}
  {if $smarty.session.s_type == "administrator"}
  <div class = "headerTools">
   {if $_change_}
    <span>
     <img src = "images/16x16/add.png" title = "{$smarty.const._NEWSKILL}" alt = "{$smarty.const._NEWSKILL}" >
     <a href = "{$smarty.server.PHP_SELF}?ctg=module_hcd&op=skills&add_skill=1&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._NEWSKILL}', 2)">{$smarty.const._NEWSKILL}</a>
    </span>
   {/if}
  </div>
  {/if}

{if !$T_SORTED_TABLE || $T_SORTED_TABLE == 'skillsTable'}
<!--ajax:skillsTable-->
  <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "0" id = "skillsTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.session.s_type}.php?ctg=module_hcd&op=skills&">
  <tr class = "topTitle">
   <td width = "35%" class = "topTitle" name = "description">{$smarty.const._SKILLDESCRIPTION}</td>
   <td width = "30%" class = "topTitle" name = "category_description">{$smarty.const._SKILLCATEGORY}</td>
   <td class = "topTitle centerAlign" name = "Employees">{$smarty.const._EMPLOYEESWITHTHATSKILL}</td>
   <td class = "topTitle noSort centerAlign">{$smarty.const._OPERATIONS}</td>
  </tr>

  {foreach name = 'skill_list' key = 'key' item = 'skill' from = $T_DATA_SOURCE}
  <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
   <td>
    <a href = "{$smarty.session.s_type}.php?ctg=module_hcd&op=skills&edit_skill={$skill.skill_ID}" class = "editLink">{$skill.description}</a>
   </td>
   <td align = "left"> {$skill.category_description}</td>
   <td class = "centerAlign"> {$skill.Employees}</td>
   <td class = "centerAlign">
   {if $_change_}
    <a href = "{$smarty.session.s_type}.php?ctg=module_hcd&op=skills&edit_skill={$skill.skill_ID}" class = "editLink"><img class="handle" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
    <img class="ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._AREYOUSUREYOUWANTTOREMOVETHATSKILL}')) deleteSkill(this, '{$skill.skill_ID}')"/>
   {/if}
   </td>
  </tr>
  {foreachelse}
  <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "4">{$smarty.const._NODATAFOUND}</td></tr>
  {/foreach}
  </table>
<!--/ajax:skillsTable-->
{/if}
 {/capture}

 {eF_template_printBlock title = $smarty.const._SKILLS data = $smarty.capture.t_skills_code image = '32x32/tools.png'}
{/if}
