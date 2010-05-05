  {if !($T_THEME_SETTINGS->options.sidebar_interface == 2 && $T_THEME_SETTINGS->options.show_header == 2)}
  <td>
         {* Search div *}
         <div width="100%" align="center">
             {if $smarty.session.s_type == 'administrator'}
                 <form style="margin:0;padding:0;" action = "{$smarty.const.G_SERVERNAME}{$smarty.session.s_type}.php?ctg=control_panel&op=search" method = "post" target="mainframe">
             {else}
                 <form style="margin:0;padding:0;" action = "{$smarty.const.G_SERVERNAME}{$smarty.session.s_type}.php?ctg=lessons&op=search" method = "post" target="mainframe">
             {/if}
             <div style="background:transparent url('images/others/search_bg_dark.png') no-repeat scroll 0%; height:29px; width:175px; ">
                     <div id="search_suggest"></div>

                     <input type="text" name="search_text"
                         value = "{if isset($smarty.post.search_text)}{$smarty.post.search_text}{else}{$smarty.const._SEARCH}...{/if}"
                         onclick="if(this.value=='{$smarty.const._SEARCH}...')this.value='';" onblur="if(this.value=='')this.value='{$smarty.const._SEARCH}...';"

                         style="background-image:url('images/16x16/search.png'); background-repeat:no-repeat; border-width:0px; margin-top:4px; padding-left:18px; width:134px}" /> <!-- width:134px;-->
                     <input type = "hidden" name = "current_location" id = "current_location" value = ""/>
             </div>
                 </form>
         </div>


  </td>

  <td width="10%" align="center">
  {if $T_CURRENT_USER->coreAccess.dashboard != 'hidden'}
   <a href="{if $smarty.session.s_type == "administrator"}administrator.php?ctg=users&edit_user={$smarty.session.s_login}{else}{$smarty.session.s_type}.php?ctg=personal{/if}" class = "info nonEmptyLesson" id="nameSurname" onmouseover="$('tooltipImg').style.visibility = 'visible';" onmouseout="$('tooltipImg').style.visibility = 'hidden';" alt="goto my account" title="goto my account">&nbsp;&nbsp;#filter:login-{$smarty.session.s_login}#&nbsp;&nbsp;<img id="tooltipImg" class = "tooltip" border = '0' src='images/others/tooltip_arrow.gif'><span class = 'tooltipSpan' id='userInfo' style="font-size: 10px" width="100%" align="center">{$T_TYPE}</span></a>
  {else}
   &nbsp;&nbsp;#filter:login-{$smarty.session.s_login}#&nbsp;&nbsp;<img id="tooltipImg" class = "tooltip" border = '0' src='images/others/tooltip_arrow.gif'><span class = 'tooltipSpan' id='userInfo' style="font-size: 10px" width="100%" align="center">{$T_TYPE}</span>
  {/if}
   <script>
          document.getElementById('userInfo').style.left = "-25px";
          document.getElementById('userInfo').style.{if $T_BROWSER == 'IE6'}width{else}width{/if} = "140px";
   </script>
  </td>
  <td align="center">|</td>

  {if $T_THEME_SETTINGS->options.sidebar_interface == 1}
     <td>
      <div id = "mainButton" style="z-index:1000;" onMouseOver="$('horizontal_menu').style.display = 'block';" onMouseOut = "$('horizontal_menu').style.display = 'none';">
       <table border="0" cellspacing="0" cellpadding="0">
        <tr><td align="center"><a href="javascript:void(0);" style="text-decoration:none">&nbsp;&nbsp;{$smarty.const._MENU}&nbsp;&nbsp;</a></td></tr>
        <tr>
       <td>
       <div id = "horizontal_menu" style="position:absolute">
        <table cellspacing="0" cellspacing="0" style="margin-top:6px;z-index:300;border-style:outset;border-collapse:collapse;border-spacing:0 0 0 0;border-width:1px 1px 1px 1px;border-color:rgb(204, 204, 204);">
           {foreach name = 'outer_menu' key = 'menu_key' item = 'menu' from = $T_MENU}
            <tr id = "menu{$menu_key}" style="background-color:#EEEEEE;" onMouseOver="$('menuTitle{$menu_key}').className = 'horizontalSelectedTopTitle';$('listmenu{$menu_key}').style.display = 'block';" onMouseOut = "$('menuTitle{$menu_key}').className = 'horizontalMenuOption';$('listmenu{$menu_key}').style.display = 'none';">
            <td width="175px">
             <table>
              <tr>
               <td width="175px" class = "horizontalMenuOption" id="menuTitle{$menu_key}" height="32px" style="vertical-align:middle;"><a href="javascript:void(0)">{$menu.title|eF_truncate:30}</a></td>
               <td valign="top" height="32px" >
                <table id="listmenu{$menu_key}" style="background-color:#EEEEEE;z-index:300;position:absolute;border-style:solid;border-spacing:0 0 0 0;border-width:1px 1px 1px 1px;border-color:rgb(204, 204, 204);">
                 <tr><td>
                       {foreach name = 'options_list' key = 'option_id' item = 'option' from = $menu.options}
                           {if isset($option.html)}
                               <div class = "horizontalMenuOption" width="175px" height="32px" {if $menu_key == 1 && $smarty.session.s_type != "administrator"}name="lessonSpecific"{/if} onMouseOver="this.className = 'horizontalSelectedTopTitle';" onMouseOut = "this.className = 'horizontalMenuOption';">{$option.html}</div>
                           {else}
                               {* Special treatment for the first menu of professors and students so that no reload is needed*}
                               <div class = "horizontalMenuOption" width="175px" height="32px" onMouseOver="this.className = 'horizontalSelectedTopTitle';" onMouseOut = "this.className = 'horizontalMenuOption';" {if $menu_key == 1 && $smarty.session.s_type != "administrator"} {if !strpos($option.id,"lessons_") && strpos($option.id,"lessons_") !==0 && $option.id != "skillgap_tests_a"}name="lessonSpecific"{else}name="lessonGeneral" {if $T_SPECIFIC_LESSON}style="display:none"{/if}{/if}{/if} id="{$option.id}" ><table><tr style="vertical-align:middle;" width="100%">
                                {if $T_SHOW_SIDEBAR_IMAGES}
                                <td align="left" height="32px"><a href = "{$option.link}" target="{$option.target}">
                                    {if isset($option.moduleLink)}
                                        {if isset($option.eFrontExtensions)}
                                            <img style="vertical-align:middle;" src="images/{$option.image}.{$globalImageExtension}" border="0">
                                        {else}
                                            <img style="vertical-align:middle;" src="images/{$option.image}" border="0">
                                        {/if}
                                    {else}
                                    <img style="vertical-align:middle;" src="images/16x16/{$option.image}.{$globalImageExtension}" border="0">
                                    {/if}
                                    </a>
                                </td>
                                {/if}
                                <td height="32px" style="white-space:nowrap;" align="left" width="100%"><a href = "{$option.link}" target="{$option.target}">{$option.title}</a></td></tr></table>
                               </div>
                           {/if}
                       {/foreach}
                    </td></tr>
                 </table>
               </td>
              </tr>
              </table>

            </td></tr>


           {/foreach}
           </table>

      </div>
      </td></tr>
      </table>
     </div>


  </td>

  <td align="center">|</td>
  {/if}

  <td class = "topTitle" align="right">
              <table style="vertical-align:top">
                    <tr style="vertical-align:top;white-space:nowrap;">
      <td>
      {if isset($T_BAR_ADDITIONAL_ACCOUNTS)}
         {*<script type = "text/javascript" src = "js/sidebar.php"> </script>*}
      {/if}
      {foreach name = 'additional_accounts' item = "item" key = "key" from = $T_BAR_ADDITIONAL_ACCOUNTS}
                            {if $item.user_type == 'administrator'}
        {assign var=image value="images/16x16/goto_admin.png"}
       {elseif $item.user_type == 'professor'}
        {assign var=image value="images/16x16/goto_student.png"}
       {else}
        {assign var=image value="images/16x16/goto_professor.png"}
       {/if}

                        <td><a href="javascript:void(0)"><img src ='{$image}' border = "0" onclick = "changeAccount('{$item.login}')" align="right" title = "{$smarty.const._SWITCHTO} #filter:login-{$item.login}#" alt = "{$smarty.const._SWITCHTO} #filter:login-{$item.login}#"/></a></td>
                        {/foreach}

                        <td><a href="javascript:void(0)"><img src = "images/16x16/logout.{$globalImageExtension}" border = "0" onclick = "top.location='index.php?logout=true'" align="right" title = "{$smarty.const._LOGOUT}" alt = "{$smarty.const._LOGOUT}"/></a></td>

                        </td><td width="1px"></td>
                    </tr>
            </table>
  </td>
  {/if}
