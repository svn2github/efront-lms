 <div id = "logo">
  <a href = "{if $smarty.session.s_login}{$smarty.server.PHP_SELF}{else}index.php{/if}"><img src = "{$T_LOGO}" title = "{$T_CONFIGURATION.site_name}" alt = "{$T_CONFIGURATION.site_name}" border = "0"></a>
 </div>
 {if $smarty.session.s_login}
 <div id = "logout_link" style = "float:right;margin-top:5px" align="right">
  {* Merged header with mobile horizontal interface *}
  {if $T_THEME_SETTINGS->options.sidebar_interface != 0}
   {* First row *}

   {if isset($T_ONLINE_USERS_LIST)} <script> var startUpdater = true; </script>{else}<script> var startUpdater = false; </script>{/if}
   {if $T_CONFIGURATION.updater_period}<script> var updaterPeriod = '{$T_CONFIGURATION.updater_period}';</script>{else}<script>var updaterPeriod = 100000;</script>{/if}

   {if isset($T_ONLINE_USERS_LIST)}
    {*<span id = "online_users_display" class = "headerText" onMouseOver="$('users_online').show()" onMouseOut='setTimeout("$(\"users_online\").hide()", 2500);'>{$smarty.const._ONLINEUSERS}&nbsp;({$T_ONLINE_USERS_COUNT})</span><span class = "headerText">&nbsp;|</span>*}

    {if $T_ONLINE_USERS_COUNT}<span id = "online_users_display" class = "headerText" >{$smarty.const._ONLINEUSERS}&nbsp;({$T_ONLINE_USERS_COUNT})</span><span class = "headerText">&nbsp;|</span>{/if}


    {*<div class = "headerText" id = "users_online" style="display:none;position:absolute;"></div>
    <input type ="hidden" id = "online_users_text" value="{$smarty.const._ONLINEUSERS}&nbsp;" class ="online_users_display" />*}

      {/if}
   {if $T_CURRENT_USER->coreAccess.dashboard != 'hidden'}
    <span class = "headerText"><!--{$smarty.const._YOUARECURRENTLYLOGGEDINAS}:--></span><a href = "{$smarty.session.s_type}.php?{if $smarty.session.s_type == "administrator"}ctg=users&edit_user={$smarty.session.s_login}{else}ctg=personal{/if}" class="headerText">#filter:login-{$smarty.session.s_login}#</a><span class="headerText">&nbsp;</span>
   {else}
    <span class = "headerText"><!--{$smarty.const._YOUARECURRENTLYLOGGEDINAS}:-->#filter:login-{$smarty.session.s_login}#</span><span class="headerText">&nbsp;</span>
   {/if}
   {if isset($T_BAR_ADDITIONAL_ACCOUNTS)}
    {*<script type = "text/javascript" src = "js/sidebar.php"> </script>*}
    <select class = "inputSelectMed" onChange = "if (this.value != '') changeAccount(this.value)" >
     <option value="">[{$smarty.const._SWITCHACCOUNT}]</option>
     {foreach name = 'additional_accounts' item = "item" key = "key" from = $T_BAR_ADDITIONAL_ACCOUNTS}
       <option value="{$item.login}">#filter:login-{$item.login}#</option>
                 {/foreach}
             </select>
            {/if}

   {* Logout *}
     <a class = "headerText" href = "index.php?logout=true"> &nbsp;| {$smarty.const._LOGOUT}</a>

  {elseif $smarty.server.PHP_SELF|basename == 'index.php'}
   <span class = "headerText">{$smarty.const._YOUARECURRENTLYLOGGEDINAS}: </span><a href = "{$smarty.session.s_type}page.php?dashboard={$smarty.session.s_login}" class = "headerText">#filter:login-{$smarty.session.s_login}#</a>
   <a href = "index.php?logout=true" class = "headerText">({$smarty.const._LOGOUT})</a>
  {/if}
 </div>

 {/if}
 {if $T_CONFIGURATION.motto_on_header == 1}
  <div id = "info">
   <div id = "site_name" class= "headerText">{$T_CONFIGURATION.site_name}</div>
   <div id = "site_motto" class= "headerText">{$T_CONFIGURATION.site_motto}</div>
  </div>
 {/if}
 {if !$hide_path}
 <div id = "path">
  <div id = "path_title">{$title|eF_formatTitlePath}</div>
   <div id = "path_language">
  {if $smarty.server.PHP_SELF|basename == 'index.php' || $T_THEME_SETTINGS->options.sidebar_interface != 0}
   {*Search div*}
      {if $smarty.session.s_login}
          {if $smarty.session.s_type == 'administrator'}
              <form style="margin:0;padding:0;" action = "{$smarty.const.G_SERVERNAME}{$smarty.session.s_type}.php?ctg=control_panel&op=search" method = "post">
          {else}
              <form style="margin:0;padding:0;" action = "{$smarty.const.G_SERVERNAME}{$smarty.session.s_type}.php?ctg=lessons&op=search" method = "post">
          {/if}
     <input type="text" name="search_text"
      value = "{if isset($smarty.post.search_text)}{$smarty.post.search_text}{else}{$smarty.const._SEARCH}...{/if}"
      onclick="if(this.value=='{$smarty.const._SEARCH}...')this.value='';" onblur="if(this.value=='')this.value='{$smarty.const._SEARCH}...';"
      class = "searchBox" style = "background-image:url('images/16x16/search.png');"/>
     <input type = "hidden" name = "current_location" id = "current_location" value = ""/>
     </form>
   {else}
   {*language div*}
    {$smarty.capture.header_language_code}
   {/if}
  {/if}
   </div>
   <div id = "path_extra">{$smarty.capture.t_path_additional_code}</div>
 </div>
 {/if}
