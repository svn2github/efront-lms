{capture name = 't_employee_skills'}

<!--ajax:skillsTable-->
  <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "4" order = "desc" id = "skillsTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.REQUEST_URI}&">
   <tr class = "topTitle">
    <td class = "topTitle" name="description" width="20%">{$smarty.const._SKILL}</td>
    <td class = "topTitle" name="category" width="15%">{$smarty.const._CATEGORY}</td>
    <td class = "topTitle" name="specification" width="*">{$smarty.const._SPECIFICATION}</td>
    <td class = "topTitle centerAlign" name="score" width="10%">{$smarty.const._SCORE}</td>
    <td class = "topTitle centerAlign" name="users_login" width="10%" >{$smarty.const._SELECT}</td>
   </tr>
  {foreach name = 'skill_list' key = 'key' item = 'skill' from = $T_DATA_SOURCE}
   <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
    <td>
    {if $_change_skills_}
     <a class = "editLink" href="{$smarty.server.PHP_SELF}?ctg=module_hcd&op=skills&edit_skill={$skill.skill_ID}">{$skill.description}</a>
    {else}
     {$skill.description}
    {/if}
    </td>
    <td>{$skill.category}</td>
    <td>
    {if $_change_skills_}
     <input class = "inputText" type="text" id="spec_skill_{$skill.skill_ID}" onchange="ajaxUserSkillPost('{$skill.skill_ID}', this , '{$skill.categories_ID}');" value="{$skill.specification}" style="width:90%;{if !$skill.users_login || $skill.users_login != $T_EDITEDUSER->user.login}display:none{/if}" />
    {else}
     {$skill.specification}
    {/if}
    </td>
    <td class = "centerAlign">
    {if $_change_skills_}
     <input class = "inputText" type="text" id="spec_skill_score_{$skill.skill_ID}" onchange="ajaxUserSkillPost('{$skill.skill_ID}', this , '{$skill.categories_ID}');" value="{if $skill.score}{$skill.score}{else}100{/if}" style="width:30px;{if !$skill.users_login || $skill.users_login != $T_EDITEDUSER->user.login}display:none{/if}"/>{if $skill.score}%{/if}
    {else}
     {if $skill.score}{$skill.score}%{/if}
    {/if}
    </td>
    <td class = "centerAlign">
    {if $_change_skills_}
     <input class = "inputCheckBox" type = "checkbox" name = "{$skill.skill_ID}" id = "skill_{$skill.skill_ID}" onclick="$('spec_skill_{$skill.skill_ID}').toggle(); $('spec_skill_score_{$skill.skill_ID}').toggle(); ajaxUserSkillPost('{$skill.skill_ID}', this, '{$skill.categories_ID}');" {if $skill.users_login && $skill.users_login == $T_EDITEDUSER->user.login} checked {/if} >
    {else}
     {if $skill.users_login && $skill.users_login == $T_EDITEDUSER->user.login}<img src = "images/16x16/success.png" alt = "{$smarty.const._OK}" title = "{$smarty.const._OK}"/>{/if}
    {/if}
    </td>
   </tr>
  {foreachelse}
   <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "5">{$smarty.const._NODATAFOUND}</td></tr>
  {/foreach}
  </table>
<!--/ajax:skillsTable-->
{/capture}
{eF_template_printBlock title = $smarty.const._SKILLS data = $smarty.capture.t_employee_skills image = '32x32/skills.png'}
