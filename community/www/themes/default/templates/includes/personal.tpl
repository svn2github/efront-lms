{capture name = "t_personal_code"}
 {if $T_OP == 'dashboard'}
  {if $T_SOCIAL_INTERFACE}
   {capture name = "t_status_change_interface"}
    <table class = "horizontalBlock">
     <tr><td>
    {if $smarty.session.s_type == "administrator"}
       <span class = "rightOption smallHeader">
        <img class = "ajaxHandle" src = "images/32x32/home.png" title = "{$smarty.const._HOME}" alt = "{$smarty.const._HOME}">
        <a class = "titleLink" href = "{$smarty.server.PHP_SELF}?ctg=control_panel" title = "{$smarty.const._HOME}">{$smarty.const._HOME}</a>
       </span>
    {/if}
       <span class = "leftOption">#filter:login-{$T_CURRENT_USER->user.login}#&nbsp;</span>







      </td>
     </tr>
    </table>
   {/capture}
   {$smarty.capture.t_status_change_interface}
  {/if}

  {include file = "social.tpl"}
 {elseif $T_OP == 'profile' || $T_OP == 'user_groups' || $T_OP == 'mapped_accounts' || $T_OP == 'payments'}
  <div class = "tabber">
  {if in_array('profile', $T_ACCOUNT_OPERATIONS)}
   <div class = "tabbertab {if $T_OP=='profile'}tabbertabdefault{/if}" title = "{$smarty.const._PERSONALDATA}">{include file = "includes/personal/profile.tpl"}</div>
  {/if}
  {if in_array('user_groups', $T_ACCOUNT_OPERATIONS)}
   <div class = "tabbertab {if $T_OP=='user_groups'}tabbertabdefault{/if}" title = "{$smarty.const._GROUPS}">{include file = "includes/personal/user_groups.tpl"}</div>
  {/if}
  {if in_array('mapped_accounts', $T_ACCOUNT_OPERATIONS)}
   <div class = "tabbertab {if $T_OP=='mapped_accounts'}tabbertabdefault{/if}" title = "{$smarty.const._MAPPEDACCOUNTS}">{include file = "includes/personal/mapped_accounts.tpl"}</div>
  {/if}
  {if in_array('payments', $T_ACCOUNT_OPERATIONS)}
   <div class = "tabbertab {if $T_OP=='payments'}tabbertabdefault{/if}" title = "{$smarty.const._PAYMENTS}">{include file = "includes/personal/payments.tpl"}</div>
  {/if}
  </div>
 {elseif $T_OP == 'user_courses' || $T_OP == 'user_lessons' || $T_OP == 'certificates' || $T_OP == 'user_form'}
  <div class = "tabber">
  {if in_array('user_courses', $T_LEARNING_OPERATIONS)}
   <div class = "tabbertab {if $T_OP=='user_courses'}tabbertabdefault{/if}" title = "{$smarty.const._COURSES}">{include file = "includes/personal/user_courses.tpl"}</div>
  {/if}
  {if in_array('user_lessons', $T_LEARNING_OPERATIONS)}
   <div class = "tabbertab {if $T_OP=='user_lessons'}tabbertabdefault{/if}" title = "{$smarty.const._LESSONS}">{include file = "includes/personal/user_lessons.tpl"}</div>
  {/if}
  {if in_array('certificates', $T_LEARNING_OPERATIONS)}
   <div class = "tabbertab {if $T_OP=='certificates'}tabbertabdefault{/if}" title = "{$smarty.const._CERTIFICATES}">{include file = "includes/personal/certificates.tpl"}</div>
  {/if}
  {if in_array('user_form', $T_LEARNING_OPERATIONS)}
   <div class = "tabbertab {if $T_OP=='user_form'}tabbertabdefault{/if}" title = "{$smarty.const._USERFORM}">{include file = "includes/personal/user_form.tpl"}</div>
  {/if}
  </div>
 {elseif $T_OP == 'placements' || $T_OP == 'history' || $T_OP == 'skills' || $T_OP == 'evaluations' || $T_OP == 'org_form' || $T_OP == 'quals'}
  <div class = "tabber">
   <div class = "tabbertab {if $T_OP=='org_form'}tabbertabdefault{/if}" title = "{$smarty.const._ORGANIZATIONALDATA}">{include file = "includes/personal/org_form.tpl"}</div>
   <div class = "tabbertab {if $T_OP=='placements'}tabbertabdefault{/if}" title = "{$smarty.const._PLACEMENTS}">{include file = "includes/personal/placements.tpl"}</div>
   <div class = "tabbertab {if $T_OP=='skills'}tabbertabdefault{/if}" title = "{$smarty.const._SKILLS}">{include file = "includes/personal/skills.tpl"}</div>
   <div class = "tabbertab {if $T_OP=='evaluations'}tabbertabdefault{/if}" title = "{$smarty.const._EVALUATIONS}">{include file = "includes/personal/evaluations.tpl"}</div>
   <div class = "tabbertab {if $T_OP=='history'}tabbertabdefault{/if}" title = "{$smarty.const._HISTORY}">{include file = "includes/personal/history.tpl"}</div>
  </div>
 {elseif $T_OP == 'files'}
  {include file = "includes/personal/files.tpl"}
 {/if}
{/capture}
{if $smarty.get.show_avatars_list}
 <table width = "100%" >
  <tr>
  {foreach name = "avatars_list" item = "item" key = "key" from = $T_SYSTEM_AVATARS}
   {if $smarty.foreach.avatars_list.first}{assign var="item" value = "unknown_small.png"}{/if}
   <td class = "centerAlign ">
    <img src = "{$smarty.const.G_SYSTEMAVATARSURL}{$item}" class = "ajaxHandle" alt = "{$item}" title = "{$item}" onclick = "parent.$('select_avatar').selectedIndex = '{$smarty.foreach.avatars_list.index}';parent.$('popup_close').onclick();window.close();"/>
    <br/>{$item}
   </td>
   {if $smarty.foreach.avatars_list.iteration % 4 == 0}</tr><tr>{/if}
  {/foreach}
  </tr>
 </table>
{elseif $smarty.get.add_placement || $smarty.get.edit_placement}
 {include file = "includes/personal/placements.tpl"}
{elseif $smarty.get.add_evaluation || $smarty.get.edit_evaluation}
 {include file = "includes/personal/evaluations.tpl"}
{elseif $T_OP == 'user_form' && $smarty.get.printable}
 {include file = "includes/personal/user_form.tpl"}
{else}
 {eF_template_printBlock title = $smarty.const._PERSONALDATA data = $smarty.capture.t_personal_code image = '32x32/user.png' main_options = $T_TABLE_OPTIONS}
{/if}
