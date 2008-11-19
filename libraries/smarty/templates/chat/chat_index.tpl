{* Smarty template for chat_index.php *}
{include file = "includes/header.tpl"}

{if $smarty.get.close}
    <script type = "text/javascript">
    <!--
        window.close();
    //-->
    </script>
{/if}

{if (isset($T_RELOAD_SIDEFRAME))}
	<script type = "text/javascript">
    top.sideframe.location = top.sideframe.location;
    </script>
{/if}

{if (isset($T_CHATROOMS_ID))}
    <script type = "text/javascript">
    <!--
        setInterval("makeAjaxRequest('ask_chat.php?chatrooms_ID={$T_CHATROOMS_ID}','special_get_request','chat')",2500);
        document.onload=setTimeout("setFocus()",2000);
    -->
    </script>
{/if}

<script type = "text/javascript">
<!--
    if (top.sideframe) top.sideframe.changeTDcolor('chat_a');      
//-->

</script>


{*-------------------------------End of Part 1: initialization etc-----------------------------------------------*}






{*-------------------------------Part 2: Modules List ---------------------------------------------*}
{if (isset($T_SHOW_ROOMS_LIST))}
{**moduleRoomsList: Show the list of rooms*}
    {capture name = "moduleRoomsList"}
                            <tr><td class = "moduleCell">

                            {capture name = 't_public_rooms_code'}
                                {if $smarty.session.s_type != 'student' && (!$T_CURRENT_USER->coreAccess.chat || $T_CURRENT_USER->coreAccess.chat == 'change')}
                                                <table border = "0" width="100%">
                                                    <tr><td>
                                                            <a href = "chat/chat_room_options.php?new_public_room=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._ADDROOM}', new Array('300px', '100px'))" class = "innerTable"><img src = "images/16x16/add2.png" alt = "{$smarty.const._ADDROOM}" title = "{$smarty.const._ADDROOM}" border = "0" style = "vertical-align:middle"/></a>&nbsp;
                                                            <a href = "chat/chat_room_options.php?new_public_room=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._ADDROOM}', new Array('300px', '100px'))" class = "innerTable" style = "vertical-align:middle">{$smarty.const._ADDROOM}</a></td></tr>
                                                    <tr><td>&nbsp;</td></tr>
                                                </table>
                                {/if}       
                                				<table border = "0" width="100%">
                                {section name = 'public_rooms_list' loop = $T_PUBLIC_ROOMS}
                                    {if $T_PUBLIC_ROOMS[public_rooms_list].active}
                                        {capture name = 't_assign'}<img src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._DEACTIVATE}" title = "{$smarty.const._DEACTIVATE}" border = "0">{/capture}
                                                    <tr><td width="40%"><span class = "counter">{$smarty.section.public_rooms_list.iteration}.</span>&nbsp;{$T_PUBLIC_ROOMS[public_rooms_list].name|eF_truncate:25:"...":true}</td>
                                                    
                                                    <!--<a href = "chat/chat_index.php?chatrooms_ID={$T_PUBLIC_ROOMS[public_rooms_list].id}" title="{$T_PUBLIC_ROOMS[public_rooms_list].name}">{$T_PUBLIC_ROOMS[public_rooms_list].name|eF_truncate:25:"...":true}</a>-->
                                    {else}
                                        {capture name = 't_assign'}<img src = "images/16x16/trafficlight_red.png" alt = "{$smarty.const._ACTIVATE}" title = "{$smarty.const._ACTIVATE}" border = "0"/>{/capture}
                                                    <tr><td width="40%"><span class = "counter">{$smarty.section.public_rooms_list.iteration}.</span>&nbsp;<span title="{$T_PUBLIC_ROOMS[public_rooms_list].name}">{$T_PUBLIC_ROOMS[public_rooms_list].name|eF_truncate:25:"...":true}</span></td>
                                    {/if}
                                                        <td>[(#filter:timestamp-{$T_PUBLIC_ROOMS[public_rooms_list].create_timestamp}#), {$T_PUBLIC_ROOMS[public_rooms_list].num_of_users_in_room} {$smarty.const._PARTICIPANTS}]</td>
                                    {if (!$T_CURRENT_USER->coreAccess.chat || $T_CURRENT_USER->coreAccess.chat == 'change')}
                                    					
                                                        <td>
                                                        	<table><tr><td width="33%">
                                                        	{if $T_PUBLIC_ROOMS[public_rooms_list].lessons_ID == 0}<a class = "deleteLink" href = "chat/chat_index.php?id={$T_PUBLIC_ROOMS[public_rooms_list].id}&delete=1"><img src = "images/16x16/delete.png" alt = "{$smarty.const._DELETE}" title = "{$smarty.const._DELETE}" border = "0"/></a>
                                                        	{else}<img src = "images/16x16/delete_gray.png" alt = "{$smarty.const._LESSONROOMSCANNOTBEDELETED}" title = "{$smarty.const._LESSONROOMSCANNOTBEDELETED}" border = "0"/>{/if}
                                                        	</td><td width="33%">
                                                            <a class = "activateLink" href = "chat/chat_index.php?id={$T_PUBLIC_ROOMS[public_rooms_list].id}&activate=1">{$smarty.capture.t_assign}</a>
                                                            </td><td width="*">
                                        {if $T_PUBLIC_ROOMS[public_rooms_list].active}
                                                            <a class = "inviteLink" href = "forum/new_message.php?chat_invite={$T_PUBLIC_ROOMS[public_rooms_list].id}" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._INVITEUSERS}', new Array('600px', '400px'))"><img src = "images/16x16/mail_forward.png" alt = "{$smarty.const._INVITEUSERS}" title = "{$smarty.const._INVITEUSERS}" border = "0"/></a> </td>
                                                            
                                        {/if}
                                        					</td></tr></table>
                                        				</td>
                                    {/if}
                                                        <td>{if (isset($T_PUBLIC_ROOMS[public_rooms_list].exit))}<a class = "exitLink" href = "chat/chat_index.php?logout=1&chatrooms_ID={$T_PUBLIC_ROOMS[public_rooms_list].id}"><img src = "images/16x16/arrow_left_blue.png" alt = "{$smarty.const._EXIT}" title = "{$smarty.const._EXIT}" border = "0"/></a>{/if}</td>
                                                    </tr>

                                {sectionelse}
                                                    <tr><td class = "emptyCategory">{$smarty.const._NOROOMSFOUND}</td></tr>
                                {/section}
                                                </table>

                            {/capture}

                            {capture name = 't_options_code'}
                                        <table border = "0" width="100%">
                                            <tr><td>
                                                <span class = "counter">1.</span> <a href = "chat/chat_room_options.php?past_messages=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._MANAGEPASTCOVERSATIONS}', new Array('800px', '400px'))">{$smarty.const._MANAGEPASTCOVERSATIONS}</a> 
                                            	</td>
                                            </tr>
                                            <!--
                                            <tr><td>	
                                                <span class = "counter">2.</span> <a href = "chat/chat_room_options.php?change_sidebar_width=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._CHANGESIDEBARWIDTH}', new Array('300px', '120px'))">{$smarty.const._CHANGESIDEBARWIDTH}</a>	
                                                 
                                            	</td>                                            
                                            </tr>
                                            -->
                                            <tr><td>
                                            	{if $T_CHAT_ENABLED}
                								<span class = "counter">2.</span> <a href = "chat/chat_index.php?activate_system_chat=0">{$smarty.const._DEACTIVATEEFRONTCHAT}</a>                            	
                                            	{else}
                                            	<span class = "counter">2.</span> <a href = "chat/chat_index.php?activate_system_chat=1">{$smarty.const._ACTIVATEEFRONTCHAT}</a>	
                                                {/if}	
                                                <!--span class = "counter">3.</span> <a href = "chat/chat_room_options.php?disable_chatting=1">{$smarty.const._DISABLEEFRONTCHAT}</a--> 
                                            	</td>                                            
                                            </tr>
                                            
                                        </table>
                            {/capture}

                                <table width = "100%" align = "left" valign = "top" border = "0">
                                	{if $T_CHAT_ENABLED}
                                    <tr><td>
                                        {eF_template_printInnerTable title=$smarty.const._ROOMS data=$smarty.capture.t_public_rooms_code image='/32x32/users1.png'}
                                    </td></tr>
                                    {/if}
                                    <tr><td>
                                        {eF_template_printInnerTable title=$smarty.const._OPTIONS data=$smarty.capture.t_options_code image='/32x32/gears.png'}
                                    </td></tr>
                                </table>  

                            </td></tr>
    {/capture}
{/if}

{if isset($T_SHOW_ROOM)}
{**moduleShowRoom: Show the room*}
    {capture name = "moduleShowRoom"}
                            <tr><td class = "moduleCell" >
							{assign var = 't_show_side_menu' value = true}
							{assign var = "room_title" value = $T_ROOM_TITLE|eF_truncate:30:"...":true}
							{assign var = "title" value ='&nbsp;&raquo;&nbsp;'|cat:$room_title}
                            {capture name = 't_input_code'}
                                    <table border = "0">
                                {if $smarty.get.standalone}
                                        <tr><td align = "center">{$smarty.const._ROOM}: <span class = "boldFont">{$T_ROOM_TITLE}</span></td></tr>
                                {/if}
                                        <tr>
                                            <form name = "chat_form" action = "javascript:sendMessage(document.chat_form.chat_message.value,'{$T_CHATROOMS_ID}');" method = "post">
                                            <td nowrap>{$smarty.const._MESSAGE}: <input type = "text" name = "chat_message" size = "40" valign = "middle" onpaste = "javascript: document.chat_form.submit.disabled = false;" onKeyup = "javascript:enableButton();" onMouseup = "javascript:enableButton();"/>
                                                <input type = "submit" name = "submit" value = "{$smarty.const._SEND}"  class = "flatButton"/>
                                                <input type = "hidden" name = "hidden_chat_room_id" value = "{$T_CHATROOMS_ID}"/>
                                            </td>
                                            </form>    
                                            <td>
                                                <a href = "chat/smilies.php" onclick = "eF_js_showDivPopup('{$smarty.const._SMILIES}', new Array('250px', '150px'))" target = "POPUP_FRAME"><img src = "images/smilies/icon_smile.gif" border = "0"/></a>
                                            </td>
                                        </tr>
                                    </table>
                            {/capture}

                            <script type="text/javascript">
                            {literal}
                            <!--
                                /*The user navigated away from the page, so log him out*/
                                function chat_logout() 
                                {
                            {/literal}
                                    logout_window = window.open("chat/chat_room_options.php?logout=1&chatrooms_ID={$T_CHATROOMS_ID}", "chat_logout", "scrollbars=no, resizable=no, toolbar=no, menubar=no, status=no, location=no, height=5, width=5");
                            {literal}
                                    logout_window.blur();
                                }

                                /*Resize the windows so that the new one appears below the parent window*/
                                function resizeChild() 
                                {
                                    parent_width  = parent.document.body.clientWidth; 
                                    parent_height = parent.document.body.clientHeight; 
                            {/literal}
                                    new_window    = window.open('chat/chat_index.php?chatrooms_ID={$T_CHATROOMS_ID}&standalone=1', 'standalone', 'resizable=yes, scrollbars=yes, width='+parent_width+', height=150'); 
                            {literal}
                                    parent.resizeTo(parent_width, parent_height - 50); 
                                    parent.moveTo(1, 1); 
                                    new_window.moveTo(1, parent_height - 50);
                                }
                            //-->
                            {/literal}
                            </script>
				<script language="JavaScript">
				 {literal}
				<!--
				function resize_iframe()
				{

					var height=window.innerWidth;//Firefox
					if (document.body.clientHeight)
					{
						height=document.body.clientHeight-100;//IE
					}
					//resize the iframe according to the size of the
					//window (all these should be on the same line)
					document.getElementById("glu").style.height=parseInt(height-
					document.getElementById("glu").offsetTop-8)+"px";
				}

				// this will resize the iframe every
				// time you change the size of the window.
				window.onresize=resize_iframe; 

				//Instead of using this you can use: 
				//	<BODY onresize="resize_iframe()">


				//-->
				 {/literal}
				</script>

                                    <table border = "0" width = "100%" style="height:100%;" cellpadding = "2">
                                     <tr><td>
                                            {$smarty.capture.t_input_code}
                                        </td></tr>
                                        <tr>
						<td valign = "top" style="height:100%;">
							<iframe name = "test" frameborder = "no" style="border: 1px solid #DDDDDD; " scrolling="auto" id="glu" width="100%" onload="resize_iframe()" src = "chat/blank.php" />{$smarty.const._SORRYNEEDIFRAME}</iframe>
						</td>
					</tr>
   
                                    </table>   

                            </td></tr>
    {/capture}
{/if}

{if isset($T_SHOW_ROOM)}
    {capture name = "sideRoomOptions"}  
                            {capture name = 't_functions_code'}
                                    <table>                                
                                        <tr><td><span class = "counter">{counter}.</span> <a href = "chat/chat_index.php?logout=1">{$smarty.const._EXITCHAT}</a></td></tr>
                                        {*<tr><td><span class = "counter">{counter}.</span> <a href = "javascript:void(0)" onclick = "resizeChild()">{$smarty.const._ADJUSTTOPAGE}</a></td></tr>*}
                            {if  !$T_CURRENT_USER->coreAccess.chat || $T_CURRENT_USER->coreAccess.chat == 'change'}
                                {if $smarty.session.s_type != 'student'}
                                        <tr><td><span class = "counter">{counter}.</span> <a href = "chat/chat_room_options.php?new_public_room=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._CREATEPUBLICROOM}', new Array('300px', '100px'))" >{$smarty.const._CREATEPUBLICROOM}</a></td></tr>
                                {/if}
                                        <tr><td><span class = "counter">{counter}.</span> <a href = "chat/chat_room_options.php?new_private_room=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._CREATEPRIVATEROOM}', new Array('300px', '100px'))" >{$smarty.const._CREATEPRIVATEROOM}</a></td></tr>
                            {/if}
                                        <tr><td><span class = "counter">{counter}.</span> <a href = "chat/chat_index.php">{$smarty.const._GOTOCHATROOMSLIST}</a></td></tr>
                                    </table>    
                            {/capture}

                                            {assign var = 't_rooms_span' value = '<span id = "rooms_list"></span>'}
                                            {eF_template_printSide title=$smarty.const._OPENROOMS data=$t_rooms_span id = 'open_rooms'}
                                            {eF_template_printSide title=$smarty.const._OPTIONS data=$smarty.capture.t_functions_code id = 'functions_list'}
                                            {assign var = 't_users_span' value = '<span id = "users_list"></span>'}
                                            {eF_template_printSide title=$smarty.const._OTHERUSERSINROOM data=$t_users_span id = 'other_users'}
                                            <span id = "notice"></span>
    {/capture}
{/if}


{*----------------------------End of Part 2: Modules List------------------------------------------------*}



{*-----------------------------Part 3: Display table-------------------------------------------------*}
{if $T_USER == 'professor'}
    {assign var = "home_title" value = '<a class = "titleLink" href ="professor.php?ctg=control_panel">'|cat:$smarty.const._HOME|cat:'</a>'}
{elseif $T_USER == 'administrator'}
    {assign var = "home_title" value = '<a class = "titleLink" href ="administrator.php?ctg=control_panel">'|cat:$smarty.const._HOME|cat:'</a>'}
{else}
    {assign var = "home_title" value = '<a class = "titleLink" href ="student.php?ctg=control_panel">'|cat:$smarty.const._HOME|cat:'</a>'}
{/if}

<table class = "mainTable">
    <tr>
        <td style = "vertical-align: top;">
            <table class = "centerTable">
                <tr class = "topTitle">
                    <td colspan = "2" class = "topTitle">                               {*Header*}
                        {$home_title}&nbsp;&raquo;&nbsp;<a class = "titleLink"  href ="javascript:void(0)" onclick = "location.reload()">{$smarty.const._CHAT}</a>
                    </td>         
               </tr>
{if $T_MESSAGE}
                <tr class = "messageRow">
                    <td colspan = "2">{eF_template_printMessage message=$T_MESSAGE type=$T_MESSAGE_TYPE}</td>        {*Display Message, if any*}
                </tr>
{/if}
{if $T_SEARCH_MESSAGE}
                <tr class = "messageRow">
                    <td colspan = "2">{eF_template_printMessage message=$T_SEARCH_MESSAGE}</td>        {*Display Search Message, if any*}
                </tr>                                        
{/if}                                    

{if ($T_OPERATION == 'control_panel')}        {*Pages with 2-column layout*}
{*LEFT MAIN COLUMN*}
                <tr>
                    <td class = "leftColumn" id = "leftColumn">
                        <table class = "leftColumnData">
                        </table>
                    </td>
{*RIGHT MAIN COLUMN*}
                    <td class = "rightColumn" id = "rightColumn">
                        <table class = "rightColumnData">
                        </table>
                    </td>
                </tr>
{else}                                                                          {*Pages with single-column layout*}
{*SINGLE MAIN COLUMN*}
                <tr>
                    <td class = "singleColumn" id = "singleColumn">
                        <table class = "singleColumnData" height = "100%">
                                {$smarty.capture.moduleShowRoom}
                                {$smarty.capture.moduleRoomsList}
                        </table>
                    </td>
                </tr>
{/if}
            </table>

        </td>
{*RIGHT SIDE MENU*}
{if isset($T_SHOW_ROOM) && !$smarty.get.standalone}
        <td class = "sideMenu" id = "sideMenu">
            {$smarty.capture.sideRoomOptions}
        </td>
{/if}
    </tr>
{if $T_SHOWFOOTER}
    {include file = "includes/footer.tpl"}
{/if}
</table>

{*-----------------------------End of Part 3: Display table-------------------------------------------------*}

{*-----------------------------Part 4: Finalization data etc-------------------------------------------------*}
{include file = "includes/closing.tpl"}

</body>
</html>
