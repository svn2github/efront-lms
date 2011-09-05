{include file = "includes/header.tpl"}

<script language = "JavaScript" type = "text/javascript">

    // Translations used in the sidebar.js script
    var translations = new Array();
    translations['lessons'] = '{$smarty.const._LESSONS}';
    translations['servername'] = '{$smarty.const.G_SERVERNAME}';
    translations['onlineusers'] = '{$smarty.const._ONLINEUSERS}';
    translations['nousersinroom'] = '{$smarty.const._THEREARENOOTHERUSERSRIGHTNOWINTHISROOM}';
    translations['redirectedtomain']= '{$smarty.const._REDIRECTEDTOEFRONTMAIN}';
    translations['s_type'] = '{$smarty.session.s_type}';
    translations['s_login'] = '{$smarty.session.s_login}';
    translations['clicktochange'] = '{$smarty.const._CLICKTOCHANGESTATUS}';
    translations['userisonline'] = '{$smarty.const._USERISONLINE}';
    translations['and'] = '{$smarty.const._AND}';
    translations['hours'] = '{$smarty.const._HOURS}';
    translations['minutes'] = '{$smarty.const._MINUTES}';
    translations['userjustloggedin']= '{$smarty.const._USERJUSTLOGGEDIN}';
    translations['user'] = '{$smarty.const._USER}';
    translations['sendmessage'] = '{$smarty.const._SENDMESSAGE}';
    translations['web'] = '{$smarty.const._WEB}';
 translations['user_stats'] = '{$smarty.const._USERSTATISTICS}';
 translations['user_settings'] = '{$smarty.const._USERPROFILE}';
 translations['logout_user'] = '{$smarty.const._LOGOUTUSER}';
 translations['_ADMINISTRATOR'] = '{$smarty.const._ADMINISTRATOR}';
 translations['_PROFESSOR'] = '{$smarty.const._PROFESSOR}';
 translations['_STUDENT'] = '{$smarty.const._STUDENT}';



    // Global variables
    var menuCount = '{$T_MENUCOUNT}'; // How many menus are initially loaded?
    var browser = '{$T_BROWSER}';
    var active_id = '{$T_ACTIVE_ID}'; // What is the id of the menu item that should be set as activated (gray background)
    var activeMenu = '{$T_ACTIVE_MENU}'; // What is the active menu? (active_id exists within that menu)
    var setActiveMenu = 0; // Has the active menu been explicitly set by the mainFrame - behave differently in fixUpperMenu

    // Facebook related
    {if $T_OPEN_FACEBOOK_SESSION}
    var facebook_api_key = "{$T_FACEBOOK_API_KEY}";
    var facebook_should_update_status = "{$T_SHOULD_UPDATE_STATUS}";
    {else}
    var facebook_api_key = 0;
    var facebook_should_update_status = 0;
    {/if}
    var __shouldTriggerNextNotifications = false;

    // Get unread messages

 {*{if !$T_NO_MESSAGES}var startUpdater = true;{else}var startUpdater = false;{/if}*}
 {if $T_CONFIGURATION.updater_period}var updaterPeriod = '{$T_CONFIGURATION.updater_period}';{else}var updaterPeriod = 100000;{/if}

    var arrow_status = "down"; // Initialize toggle arrows

    {if $T_BROWSER == 'IE6' || $T_BROWSER == 'IE7'}
     var table_style_size = "90%";
    {else}
     var table_style_size = "100%";
    {/if}

</script>

<body class = "sidebar" >
    <span id = "nobookmarks" style = "display:none">{$smarty.const._YOUHAVENOBOOKMARKS}</span>

    {math assign='T_SB_WIDTH_MINUS_ONE' equation="x-1" x=$T_SIDEBARWIDTH}
    <div id="loading_sidebar" class="loading" style="opacity: 0.9; height: 100%; width: {$T_SB_WIDTH_MINUS_ONE}px; display: block;" ><div style="top: 50%; left:12%; position: absolute;" ><img src="images/others/progress1.gif" style="vertical-align: middle;"/><span style="vertical-align: middle;">{$smarty.const._LOADINGDATA}</span></div></div>

    {* Top menu with photo and name - Hiding on click *}
    <div class = "tabmenu" id = "tabmenu" align="center" style="visibility:hidden">
        {* Spacer from top *}
        <table><tr height="10px"><td></td></tr></table>

        {* Photo *}
        <div class = "topPhoto" id = "topPhoto" style="height:{$T_NEWHEIGHT}px">
            <a href = "{$smarty.session.s_type}.php?ctg=personal&user={$smarty.session.s_login}" target = "mainframe">
            {*<a href = "{$smarty.session.s_type}.php?ctg=social&op=dashboard" target = "mainframe">*}
            {if isset($T_AVATAR)}
                <img src = "{if isset($T_ABSOLUTE_AVATAR_PATH)}{$T_AVATAR}{else}view_file.php?file={$T_AVATAR}{/if}" border = "0" title="{$smarty.const._GOTODASHBOARD}" alt="{$smarty.const._GOTODASHBOARD}"
                {if isset($T_NEWWIDTH)} width = "{$T_NEWWIDTH}" height = "{$T_NEWHEIGHT}"{/if} />
            {else}
                <img src = "{$smarty.const.G_SYSTEMAVATARSURL}unknown_small.png" border = "0" title="{$smarty.const._EFRONTNAME}" alt="{$smarty.const._EFRONTNAME}" />
            {/if}
            </a>
        </div>

        <div id = "personIdentity">
            <table>
                <tr><td>
                        <a href = "{$smarty.session.s_type}.php?ctg=personal&user={$smarty.session.s_login}" target = "mainframe" class = "info nonEmptyLesson" id="nameSurname" onmouseover="if($('tooltipImg'))$('tooltipImg').style.visibility = 'visible';" onmouseout="if($('tooltipImg')) $('tooltipImg').style.visibility = 'hidden';">
                            #filter:login-{$smarty.session.s_login}#<br />
                            <span class = 'tooltipSpan' id='userInfo'>{$T_TYPE}</span>
                        </a>
                    </td></tr>
                <tr><td>
                    {if !$T_NO_PERSONAL_MESSAGES}
                    <div id="unread_img" style="display:inline">{if $T_UNREAD_MESSAGES != 0}<img src = "images/16x16/mail.{$globalImageExtension}" style="vertical-align:middle; border:0; 'float': left;" title="{$smarty.const._MESSAGES}" alt="{$smarty.const._MESSAGES}" />{/if}</div>
                    <div id="recent_unread_left" style="display:inline">{if $T_UNREAD_MESSAGES != 0}(<a href = "{$smarty.session.s_type}.php?ctg=messages" target="mainframe">{$T_UNREAD_MESSAGES}</a>){/if}</div>
                    {/if}
                    </td>
                 </tr>
            </table>
        </div>
        {* Search div *}
        <div>
            {if $smarty.session.s_type == 'administrator'}
                <form style="display: inline;" action = "{$smarty.const.G_SERVERNAME}{$smarty.session.s_type}.php?ctg=control_panel&op=search" method = "post" target="mainframe">
            {else}
                <form style="display: inline;" action = "{$smarty.const.G_SERVERNAME}{$smarty.session.s_type}.php?ctg=lessons&op=search" method = "post" target="mainframe">
            {/if}
            <div>
                    <div id="search_suggest"></div>
                    <input class = "searchBox" type="text" name="search_text"
                        value = "{if isset($smarty.post.search_text)}{$smarty.post.search_text}{else}{$smarty.const._SEARCH}...{/if}"
                        onclick="if(this.value=='{$smarty.const._SEARCH}...')this.value='';" onblur="if(this.value=='')this.value='{$smarty.const._SEARCH}...';"
                        style = "background-image:url('images/16x16/search.png');"/> <!-- width:134px;-->
                    <input type = "hidden" name = "current_location" id = "current_location" value = ""/>
            </div>
                </form>
        </div>
    </div>
    {* Basic menu called "menu" includes all other menus in successive order: menu1 (always), menu2,..., menuN, logout (always) *}
    <div class = "menu" id = "menu" style="visibility:hidden">
        {*********}
        {* MENUS *}
        {*********}
        {foreach name = 'outer_menu' key = 'menu_key' item = 'menu' from = $T_MENU}
        <div class = "verticalTab" id = "menu{$menu_key}">
            <div class = "tabHeader" onclick = "move($('menu{$menu_key}'));" id="tabmenu{$menu_key}" title = "{$menu.title}">{$menu.title|eF_truncate:30}</div>
            <div class = "menuList" id="listmenu{$menu_key}">
                {foreach name = 'options_list' key = 'option_id' item = 'option' from = $menu.options}
                    {if isset($option.html)}
                        <div class = "menuOption" {if $menu_key == 1 && $smarty.session.s_type != "administrator"}name="lessonSpecific"{/if}>{$option.html}</div>
                    {else}
                        {* Special treatment for the first menu of professors and students so that no reload is needed*}
                        <div class = "menuOption"
                         {if $menu_key == 1 && $smarty.session.s_type != "administrator"}
                          {if !strpos($option.id,"lessons_") && strpos($option.id,"lessons_") !==0 && $option.id != "skillgap_tests_a"}
                           name="lessonSpecific"
                          {else}
                           name="lessonGeneral"
                           {if $T_SPECIFIC_LESSON}style="display:none"{/if}
                          {/if}
                         {/if} id="{$option.id}" >
                         <table>
                          <tr>
                          {if $T_SHOW_SIDEBAR_IMAGES}
                          <td><a href = "{$option.link}" target="{$option.target}">
                              {if isset($option.moduleLink)}
                                  {if isset($option.eFrontExtensions)}
                                      <img src="{$option.image}.png" class = "handle">
                                  {else}
                                      <img src="{$option.image}" class = "handle">
                                  {/if}
                              {else}
                               <img src="images/16x16/{$option.image}.png" class = "handle">
                              {/if}
                              </a>
                          </td>
                          {/if}
                          <td class = "menuListOption" >
                           <a href = "{$option.link}" title="{$option.title}" target="{$option.target}">{$option.title|eF_truncate:25}</a>
                          </td>
                        </tr>
                    </table>
                </div>
                    {/if}
                {/foreach}
            </div>
        </div>
        {/foreach}
    {*********************************}
    {* NEXT MENU : ONLINE USERS MENU *}
    {*********************************}
    {if isset($T_ONLINE_USERS_LIST) && !$T_CONFIGURATION.disable_online_users}
        <div class = "verticalTab" id = "menu{$T_MENUCOUNT}" >
            <div class = "tabHeader" onclick = "move($('menu{$T_MENUCOUNT}'));" id="tabmenu{$T_MENUCOUNT}">{$smarty.const._ONLINEUSERS}&nbsp;&nbsp;({$T_ONLINE_USERS_COUNT})</div>
            <div class = "menuList" id="listmenu{$T_MENUCOUNT}">
            <script>menuCount = '{$T_MENUCOUNT}';</script>
                <table width = "100%">
                    <tr><td id = "users_online"></td></tr>
                </table>
            </div>
        </div>
    {if $menuCount}{math assign='menuCount' equation="x+1" x=$menuCount}{else}{math assign='menuCount' equation="x+1" x=0}{/if}
    {/if}
        {***********************}
        {* FINAL MENU : LOGOUT *}
        {***********************}
        <div class = "verticalTab" id = "logout">
            <div class = "tabHeader">
                <table>
                    <tr>
                        <td class="smallIconCell">
       <img src = "images/16x16/logout.png" onclick = "top.location='index.php?logout=true'" title = "{$smarty.const._LOGOUT}" alt = "{$smarty.const._LOGOUT}" class = "menuTool"/>
                        </td>
                        <td onclick = "top.location= '{$smarty.const.G_SERVERNAME}index.php?logout=true'">
                            <a href = "{$smarty.const.G_SERVERNAME}index.php?logout=true" target = "_top" style="white-space:nowrap;">{$smarty.const._LOGOUT}</a>
                        </td>
                        {if $T_PROMPT_FB_CONNECTION}
                        <td class="smallIconCell">
                        <img src ='images/16x16/facebook.png' onclick = "{literal}FB.Connect.requireSession(function() { top.location='professorpage.php?fb_authenticated=1'; }); return false;{/literal}" title = "{$smarty.const._FACEBOOKCONNECT}" alt = "{$smarty.const._FACEBOOKCONNECT}" class = "menuTool"/>
                        <script type="text/javascript">FB.init("{$T_FACEBOOK_API_KEY}", "facebook/xd_receiver.htm");</script>
                        </td>
                        {/if}
                        {foreach name = 'additional_accounts' item = "item" key = "key" from = $T_BAR_ADDITIONAL_ACCOUNTS}
                        <td class="smallIconCell">
                            {if $item.user_type == 'administrator'}
                                {assign var=image value="images/16x16/goto_admin.png"}
                            {elseif $item.user_type == 'professor'}
                                {assign var=image value="images/16x16/goto_student.png"}
                            {else}
                                {assign var=image value="images/16x16/goto_professor.png"}
                            {/if}
                            <img src ='{$image}' onclick = "changeAccount('{$item.login}')" title = "{$smarty.const._SWITCHTO} #filter:login-{$item.login}#" alt = "{$smarty.const._SWITCHTO} {$item.login}" class = "menuTool"/>
                        </td>
                        {/foreach}
                        <td width="1px"></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <input type ="hidden" id = "online_users_text" value="{$smarty.const._ONLINEUSERS}&nbsp;&nbsp;" class ="tabmenu{$T_MENUCOUNT}" />
    <div id="utility_images" style="visibility:visible">
        <img id = "toggleSidebarImage" src = "images/others/blank.gif" onClick = "toggleSidebar('{$smarty.session.s_login}');checkSidebarMode('{$smarty.session.s_login}');" style = "position: absolute; top:4px; right: -1px; cursor: pointer; " align = "right" alt = "{$smarty.const._SHOWHIDE}" title = "{$smarty.const._SHOWHIDE}"/>
    </div>
<script type = "text/javascript" src = "js/scripts.php?load={$T_HEADER_LOAD_SCRIPTS}"> </script>
    <!--<script type = "text/javascript" src = "jsslashfiles/menu.js"></script> There is no that file any more....Why?-->
    <div id="dimmer" class = "dimmerDiv" style="display:none;"></div>
    <script>
    if (parent.frames[0] && parent.frames[0].document.getElementById('dimmer')) parent.frames[0].document.getElementById('dimmer').style.display = 'none'
    </script>
    <input type="hidden" value="myhidden" id="hasLoaded" />
{literal}
    <script type = "text/javascript">
        initSidebar({/literal}'{$smarty.session.s_login}'{literal}); //initialization of sidebar according to cookie value
  setMenuPositions();
        $('userInfo').setStyle({left: -($('nameSurname').positionedOffset().left) + "px"});
        var maximumFramewidth = $('tabmenu').getWidth()-30;
        $('userInfo').setStyle({width: (maximumFramewidth < 0 ? 0 : maximumFramewidth) + "px"});
  if (top.mainframe && top.mainframe.category) {
         arr = top.mainframe.category.split("&");
         setActiveId(arr[0], arr[1], arr[2], arr[3], arr[4], arr[5], "{/literal}{$smarty.session.s_type}{literal}");
     }
    </script>
{/literal}
</body>
</html>
