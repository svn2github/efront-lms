{include file = "includes/header.tpl"}

<script language = "JavaScript" type = "text/javascript">

    // Translations used in the sidebar.js script
    var translations = new Array();
    translations['lessons'] = '{$smarty.const._LESSONS}';
    translations['servername'] = '{$smarty.const.G_SERVERNAME}';
    translations['onlineusers'] = '{$smarty.const._ONLINEUSERS}';
    translations['nousersinroom'] = '{$smarty.const._THEREARENOOTHERUSERSRIGHTNOWINTHISROOM}';
    translations['redirectedtomain']= '{$smarty.const._REDIRECTEDTOEFRONTMAIN}';
    translations['chatroomdeleted'] = '{$smarty.const._CHATROOMDELETEDBYOWNER}';
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

    // Chat related
    var chatroomIntervalId = 0; // This id relates to the periodical functionality of updating the active chat room, if the chat tab is open
    var chatactivityIntervalId = 0; // This id relates to the periodical functionality of checking for any chat activity, if the chat tab is closed
    var chatOptionIsEnabled = '{$T_CHATENABLED}'; // Is the chat feature enabled in the system in general?
    var chatEnabled = 0; // This global variable is used to denote whether the chat menu is currently open or not
    var onlyViewChat = '{$T_ONLY_VIEW_CHAT}'; // User is only allowed to view chat window, not write on it
    var chat_listmenu = -1; // Global variable to denote the listmenu element of the chat tab - used to hide/show that menu

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

 {if !$T_NO_MESSAGES}var startUpdater = true;{else}var startUpdater = false;{/if}
 {if $T_CONFIGURATION.updater_period}var updaterPeriod = '{$T_CONFIGURATION.updater_period}';{else}var updaterPeriod = 100000;{/if}

    var arrow_status = "down"; // Initialize toggle arrows

    {if $T_BROWSER == 'IE6' || $T_BROWSER == 'IE7'}
     var table_style_size = "90%";
    {else}
     var table_style_size = "100%";
    {/if}

    var chatRoomDoesNotExistError = "{$smarty.const._CHATROOMDOESNOTEXIST_ERROR}";
    var chatRoomIsNotEnabled = "{$smarty.const._CHATROOMISNOTENABLED_ERROR}";
    var redicrectedToEfrontMain = "{$smarty.const._REDIRECTEDTOEFRONTMAIN}";
    var chatRoomHasBeenDeactivated = "{$smarty.const._CHATROOMHASBEENDEACTIVATED}";
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
            <a href = "{if $smarty.session.s_type == "administrator"}administrator.php?ctg=users&edit_user={$smarty.session.s_login}{else}{$smarty.session.s_type}.php?ctg=personal{/if}" target = "mainframe">
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
                        <a href = "javascript:void(0)" class = "info nonEmptyLesson" id="nameSurname" onmouseover="if($('tooltipImg'))$('tooltipImg').style.visibility = 'visible';" onmouseout="if($('tooltipImg')) $('tooltipImg').style.visibility = 'hidden';">
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
    {* NEXT MENU : CHAT TAB *}
    {*********************************}
    {if $T_CHATENABLED == 1}
        {if $T_BROWSER == 'IE6' || $T_BROWSER == 'IE7'}
            {math assign='T_SB_WIDTH_MINUS_5' equation="x-1" x=$T_SIDEBARWIDTH}
        {else}
            {math assign='T_SB_WIDTH_MINUS_5' equation="x-2" x=$T_SIDEBARWIDTH}
        {/if}
        {assign var='generalWidth' value=$T_SB_WIDTH_MINUS_5}
        <div class = "verticalTab" id = "menu{$T_MENUCOUNT}">
            <div class = "tabHeader" onclick = "move($('menu{$T_MENUCOUNT}'));" name="chatmenu" id="tabmenu{$T_MENUCOUNT}">
   <script>chat_listmenu = "listmenu{$T_MENUCOUNT}";</script><table cellpadding="0" cellspacing="0" width="100%"><tr><td align="left">{$smarty.const._CHAT}</td><td align="right"><img id="new_chat_messages" src="images/16x16/forum.png" style = "vertical-align:middle;display:none" border=0 title = "{$smarty.const._NEWCHATMESSAGES}"/></td><td width="1px"></td></tr></table></div>
            <div class = "menuList" id="listmenu{$T_MENUCOUNT}" align="center" style="display:none">
                <table cellpadding="0" cellspacing="0">
                    <tr><td align="left">
                        <table cellpadding="0" cellspacing="0" width="100%"><tr valign="middle"><td width="1"></td>
                            {if $T_BROWSER == 'IE6' || $T_BROWSER == 'IE7'}
                            <td width="1">&nbsp;</td> {* This is used here, because of scrollIntoView in print-script, which without a pixel in the message table, moved the entire sideframe *}
                            {/if}
                            <td>{$smarty.const._ROOM}:</td>
                            <td align="right">
                                <table>
                                    <tr>
                                        <td valign="middle">
                                            <a href = "javascript:void(0);" onclick= "increaseChatboxFontSize()">
                                                <img id = "increase_font" src = "images/16x16/navigate_up.png" alt = "{$smarty.const._CHANGEFONTSIZE}" title = "{$smarty.const._CHANGEFONTSIZE}" border="0" style = "vertical-align:middle"/></a>
                                        </td>
                                        <td valign="middle">
                                            <a href = "javascript:void(0);" onclick= "ajaxGetRoomUsers(this,event)">
                                                <img id = "room_users_image" src = "images/16x16/users.png" alt = "{$smarty.const._SHOWUSERSINROOM}" title = "{$smarty.const._SHOWUSERSINROOM}" border="0" style = "vertical-align:middle"/></a>
                                            {math assign='T_SB_WIDTH_MINUS_32' equation="x-32" x=$T_SIDEBARWIDTH}
                                            <div id = 'room_users' onclick = "eF_js_showHideDiv(this, 'room_users', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:{$T_SB_WIDTH_MINUS_32}px;position:absolute;left:0px;top:0px;display:none"></div><!-width:143px;-->
                                        </td>
                                        <td {if $T_ONLY_VIEW_CHAT == 1 || $T_INVITE_DISABLED == 1}style="display:none"{/if}>
                                         <a href = "javascript:void(0)" target = "POPUP_FRAME" onclick = "this.href = '{$smarty.session.s_type}.php?ctg=messages&add=1&popup=1&chat_invite='+$('current_chatroom_id').value;eF_js_showDivPopup('{$smarty.const._INVITEUSERS}', 3)">
                                         <img class = "handle" src = "images/16x16/mail.png" alt = "{$smarty.const._INVITEUSERS}" title = "{$smarty.const._INVITEUSERS}" /></a>
                                        </td>
                                        <td {if $T_ONLY_VIEW_CHAT == 1}style="display:none"{/if}>
                                            <a class = "inviteLink" href = "javascript:void(0)" target = "POPUP_FRAME" onclick = "exportChatRoomHistory(this);eF_js_showDivPopup('{$smarty.const._EXPORTCHATCONVERSATIONS}', 2)">
                                                <img src="images/16x16/export.png" alt = "{$smarty.const._EXPORTCHATCONVERSATIONS}" title = "{$smarty.const._EXPORTCHATCONVERSATIONS}" class = "handle"/></a>
                                        </td>
                                        <td {if $T_ONLY_VIEW_CHAT == 1}style="display:none"{/if}>
                                            <a href = "{$smarty.session.s_type}.php?ctg=chat&chat_room_options=1&new_public_room=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._NEWPUBLICROOM}', 0)" class = "innerTable">
                                             <img src = "images/16x16/add.png" alt = "{$smarty.const._NEWPUBLICROOM}" title = "{$smarty.const._NEWPUBLICROOM}" class = "handle"/></a>
                                        </td>
                                        <td><div id="delete_room" {if isset($T_CHATROOM_OWNED)}style="display:block"{else}style="display:none"{/if}><a href="javascript:void(0)" onClick="ajaxDeleteRoom()" ><img id="delete_room_image" src="images/16x16/error_delete.png" border="0" title="{$smart.const._DELETE}" alt="{$smart.const._DELETE}" style = "vertical-align:middle"/></a></div></td>
                                        </td>
                                        {* Hidden used to denote the current font size - initially 10px *}
                                        <td><input type = "hidden" id = "current_font_size" name = "current_font_size" value = "10"/></td>
                                        {* Hidden used to denote who is the last user spoken for this room *}
                                        <td><input type = "hidden" id = "last_spoken_login" name = "last_spoken_login" value = ""/></td>
                                        {* Hidden used to denote if this is the first time getting messages from a room *}
                                        <td><input type = "hidden" id = "first_time_messages" name = "first_time_messages" value = "1"/></td>
                                    </tr>
                                </table>
                            </td>
                            </tr></table></td></tr>
                    <tr><td align="center"><select width="{$generalWidth}" STYLE="width: {$generalWidth}px;font-size:10px" id = "chat_rooms" {if $T_BROWSER == 'IE6' || $T_BROWSER == 'IE7'}onfocus{else}onclick{/if}="ajaxBringRooms()" onchange="ajaxEnterRoom(this)">
                                                <option value="0" {if $T_CHATROOMS_ID == 0}selected{/if}>{$smarty.const._EFRONTMAIN}</option>
                                                {foreach name = 'chat_rooms' item = 'room' from = $T_CHATROOMS}
                                                    {if $room.users > 0}
                                                    <option value="{$room.id}" {if $T_CHATROOMS_ID == $room.id}selected{/if}>{$room.name|eF_truncate:25}</option> {*>&nbsp;({$room.users})*}
                                                    {/if}
                                                {/foreach}
                                                </select>
                        </td>
                    </tr>
                    <tr><td align="{if $T_BROWSER == 'IE6' || $T_BROWSER == 'IE7'}left{else}center{/if}" width="100%">
                        <iframe name = "test" frameborder = "no" scrolling="no" id="glu" width = "{$generalWidth}" onload="resize_iframe();" src = "chat_blank.php" />{$smarty.const._SORRYNEEDIFRAME}</iframe>
                        </td>
                    </tr>
                    <form style="display:inline;" name = "chat_form" action = "javascript:sendMessage(document.chat_form.chat_message.value,$('current_chatroom_id').value); " method = "post">
                    <tr {if $T_ONLY_VIEW_CHAT == 1}style="display:none"{/if}><td>
                        <table cellpadding="0" cellspacing="0" border="0"><tr>
                        <td width="20"><a href = "smilies.php" onclick = "eF_js_showDivPopup('{$smarty.const._SMILIES}', 1)" target = "POPUP_FRAME"><img src = "images/smilies/icon_smile.gif" style="vertical-align:middle" border = "0"/></a></td>
                        {math assign='T_SB_WIDTH_MINUS_20' equation="x-20" x=$T_SIDEBARWIDTH}
                        <td align="left" width="{$T_SB_WIDTH_MINUS_20}" nowrap>
                            <input type = "text" name = "chat_message" width="{$T_SB_WIDTH_MINUS_20}" style = "width:97%" valign = "middle" onpaste = "javascript: document.chat_form.submit.disabled = false;" onKeyup = "javascript:enableButton();" onMouseup = "javascript:enableButton();"/>
                        </td>
                        </tr></table>
                        </td>
                        <td><input type = "submit" name = "submit" value = "{$smarty.const._SEND}" class = "flatButton" style="display:none"/>
                            <input type = "hidden" id = "current_chatroom_id" name = "hidden_chat_room_id" value = "{$T_CHATROOMS_ID}"/></td>
                    </tr>
                    </form>
                </table>
            </div>
        </div>
        {math assign='T_MENUCOUNT' equation="x+1" x=$T_MENUCOUNT}
    {/if}
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
