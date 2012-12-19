 <div id = "logo">
  <a href = "{if $smarty.session.s_login}{$smarty.server.PHP_SELF|basename}{else}index.php{/if}">
   <img class = "handle" src = "{$T_LOGO}" title = "{$T_CONFIGURATION.site_name}" alt = "{$T_CONFIGURATION.site_name}" />
  </a>
 </div>
 {if $smarty.session.s_login}
 <div id = "logout_link" >
  {if $T_THEME_SETTINGS->options.sidebar_interface}
   {if $T_ONLINE_USERS_LIST && !$T_CONFIGURATION.disable_online_users}
    <span class = "headerText" >
    {strip}
     <a href = "javascript:void(0)" class = "info">{$smarty.const._ONLINEUSERS}:&nbsp;(
      <span id = "header_connected_users">{$T_ONLINE_USERS_LIST|@sizeof}</span>
      <span class = "tooltipSpan">
       {foreach name = 'online_users_list' item = "item" key = "key" from = $T_ONLINE_USERS_LIST }
        #filter:login-{$item.login}#{if !$smarty.foreach.online_users_list.last},&nbsp;{/if}
       {/foreach}
      </span>
     )</a>
    {/strip}
    </span>
      {/if}

    <a href = "userpage.php?ctg=personal&user={$T_CURRENT_USER->user.login}" class="headerText" id = "personal_options_link">
     #filter:login-{$smarty.session.s_login}#
    </a>
    <div style = "display:none" id = "my_personal_options">
     <ul style = "list-style:none;padding:0px;" class = "headerMenu">
     {if $T_CURRENT_USER->coreAccess.dashboard != 'hidden'}
      <li onclick = "location='userpage.php?ctg=personal&user={$T_CURRENT_USER->user.login}&op=dashboard'">{$smarty.const._DASHBOARD}</li>
     {/if}
      <li onclick = "location='userpage.php?ctg=personal&user={$T_CURRENT_USER->user.login}&op=profile'">{$smarty.const._ACCOUNT}</li>
     {if $smarty.session.s_type != 'administrator'}
      <li onclick = "location='userpage.php?ctg=personal&user={$T_CURRENT_USER->user.login}&op=user_courses'">{$smarty.const._LEARNING}</li>
     {/if}






     </ul>
    </div>
   {if $T_CURRENT_USER->coreAccess.personal_messages != 'hidden' && $T_CONFIGURATION.disable_messages != 1}
    <span class = "headerText">
     <img class = "ajaxHandle" src = "images/16x16/mail.png" alt = "{$smarty.const._MESSAGES}" title = "{$smarty.const._MESSAGES}" onclick = "location='userpage.php?ctg=messages'"/>
     <span id = "header_total_messages"></span>
    </span>
   {/if}
   {if $T_MAPPED_ACCOUNTS && $smarty.get.ctg !='agreement'}
    {if !$T_CONFIGURATION.mapped_accounts ||
     $T_CONFIGURATION.mapped_accounts == 1 && $smarty.session.s_type!='student' ||
     $T_CONFIGURATION.mapped_accounts == 2 && $smarty.session.s_type=='administrator'}
    <span class = "headerText">
    <select class = "inputSelectMed" onchange = "if (this.value) changeAccount(this.value)" >
     <option value="">[{$smarty.const._SWITCHACCOUNT}]</option>
     {foreach name = 'additional_accounts' item = "item" key = "key" from = $T_MAPPED_ACCOUNTS}
     <option value="{$item.login}">#filter:login-{$item.login}#</option>
                 {/foreach}
             </select></span>
             {/if}
            {/if}
      <a class = "headerText" href = "index.php?logout=true">{$smarty.const._LOGOUT}</a>

  {/if}
  {if $T_THEME_SETTINGS->options.sidebar_interface != 0 && $T_HEADER_CLASS == 'header'}{$smarty.capture.t_path_additional_code}{/if}
 </div>
 {/if}

 {*{if $T_CONFIGURATION.motto_on_header}
  <div id = "info">
   <div id = "site_name" class= "headerText">{$T_CONFIGURATION.site_name}</div>
   <div id = "site_motto" class= "headerText">{$T_CONFIGURATION.site_motto}</div>
  </div>
 {/if}*}

 <div id = "path">
  <div id = "path_title">{$title|eF_formatTitlePath}</div>
  <div id = "tab_handles_div">
   {if $T_THEME_SETTINGS->options.sidebar_interface == 0 || $T_HEADER_CLASS == 'headerHidden'}{$smarty.capture.t_path_additional_code}{/if}
{*
  {if $smarty.session.s_lessons_ID && $smarty.session.s_lesson_user_type == 'professor'}
   <img src = "images/16x16/arrow_right.png" onclick = "window.location = window.location.toString()+'&set_student_mode=1'" class = "ajaxHandle" alt = "{$smarty.const._VIEWLESSONASSTUDENT}" title = "{$smarty.const._VIEWLESSONASSTUDENT}"/>
  {/if}
*}
  </div>
  <div id = "path_language">
  {if $smarty.server.PHP_SELF|basename != 'index.php' && $T_THEME_SETTINGS->options.sidebar_interface != 0 && $smarty.session.s_login}
            <form action = "{$smarty.server.PHP_SELF}?ctg={if $smarty.session.s_type == 'administrator'}control_panel{else}lessons{/if}&op=search" method = "post">
    <input type = "text" name = "search_text" value = "{$smarty.const._SEARCH}" onclick="if(this.value=='{$smarty.const._SEARCH}')this.value='';" onblur="if(this.value=='')this.value='{$smarty.const._SEARCH}';" class = "searchBox" />
    <input type = "hidden" name = "current_location" id = "current_location" />
   </form>
  {else}
   {$smarty.capture.header_language_code}
  {/if}
  </div>
 </div>
{*
 {if $smarty.session.student_mode}
 <div class = "studentMode"><div>{$smarty.const._YOUAREINSTUDENTMODE}</div><a href = "javascript:void(0)" onclick = "window.location=window.location.toString()+'&set_student_mode=0'">{$smarty.const._BACKTOPROFESSORMODE}</a></div>
 {/if}
*}
