{* Smarty template for chat_room_options.php *}
{if $smarty.const.MSIE_BROWSER == 1}
{literal}
<script>
/****************************************************************************
* Auxilliary functions by Alistair Lattimore to simulate IE options disabled
* Website:  http://www.lattimore.id.au/
*****************************************************************************/
function restoreSelection(e) {

    Element.extend(e);
    var previous_value = e.selIndex;
    if (e.options[e.selectedIndex].disabled) {
        e.selectedIndex = previous_value;
        return false;
    } else {
       e.selIndex = e.selectedIndex;
       return true;
    }
}

function emulateDisabledOptions(e) {
    Element.extend(e);
    for (var i=0, option; option = e.options[i]; i++) {
        if (option.disabled) {
            option.style.color = "#BBA8AC";
        } else {
            option.style.color = 0;
        }
    }
}
</script>
{/literal}
{/if}

{if $T_MESSAGE_TYPE == 'success'}
    <script>
        re = /\?/;
        !re.test(parent.location) ? parent.location = parent.location+'?message={$T_MESSAGE}&message_type={$T_MESSAGE_TYPE}' : parent.location = parent.location+'&message={$T_MESSAGE}&message_type={$T_MESSAGE_TYPE}';
    </script>
{/if}

{include file = "includes/header.tpl"}          {*The inclusion is put here instead of the beginning in order to speed up reloading, in case of success*}

{if $T_MESSAGE}
        {eF_template_printMessage message = $T_MESSAGE type = $T_MESSAGE_TYPE}
{/if}

{if isset($T_RELOAD_ALL_AFTER_SIDEBAR)}
	<script>
	top.global_sideframe_width = {$T_RELOAD_ALL_AFTER_SIDEBAR}; 
	top.location.href = top.location.href;
	</script>
{/if}

{if $smarty.post.chat_room_submit}
    {if $T_MESSAGE_TYPE == 'success'}
        {eF_template_printCloseButton reload = true}
        <meta http-equiv = "refresh" content = "5;url=chat_room_options.php?close=true">
    {else}
        {eF_template_printBackButton}
    {/if}
{else}


    {if $smarty.get.new_public_room || $smarty.get.new_private_room}
        <form name = "new_room_form" method = "post" action = "">
        <table>
            <tr><td> {$smarty.const._ROOMNAME}:</td>
                <td>&nbsp;<input type = "text" name = "chat_room_name" /></td></tr>
            {*
            <tr><td> {$smarty.const._ACTIVENEUTRAL}:</td>
                <td>&nbsp;<input type = "checkbox" name = "chat_room_active" /></td></tr>
            *}    
            <tr><td colspan = "2" align = "center">&nbsp;<input class = "flatButton" type = "submit" name = "chat_room_submit" value = "{$smarty.const._CREATE}"/></td></tr>
        </table>
        <input type = "hidden" name = "chat_room_type" value = "{$T_ROOM_TYPE}" />
        </form>
    {/if}

    {if $smarty.get.past_messages}
        <form name = "chat_show_messages_form" method = "post" action = "">
        <table>
            <tr><td>{$smarty.const._SHOWCONVERSATIONSFORROOM}:</td>
                <td><select name = "select_chat_room">
        {section name = 'chatroom_list' loop = $T_CHATROOMS}
                        <option value = "{$T_CHATROOMS[chatroom_list].id}" {if $smarty.post.select_chat_room == $T_CHATROOMS[chatroom_list].id}selected{/if}>{$T_CHATROOMS[chatroom_list].name}</option>
        {/section}
                    </select>
            </td></tr>
            <tr><td>{$smarty.const._FROM}</td>
                <td>{eF_template_html_select_date prefix="from_date_" time = $T_DAY_BEFORE start_year="-2" field_order = 'YDM'}, {html_select_time prefix="from_time_"}</td>
            <tr><td>{$smarty.const._TOCAPITAL}</td>
                <td>{eF_template_html_select_date prefix="to_date_" start_year="-2" field_order = 'YDM'}, {html_select_time prefix="to_time_"}
            </td></tr>
            <tr><td>{$smarty.const._ANDTHEMESSAGESOFUSER}:</td>
                <td><select name = "select_user" id="select_user_id" {if $smarty.const.MSIE_BROWSER == 1}onChange="restoreSelection(this);"{/if}>
                        <option value = "0">{$smarty.const._ALLUSERS}</option>
                        {eF_template_printUsersList data = $T_USERS selected = $smarty.post.select_user}
                    </select>
            </td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td colspan = "2" align = "center">
            	<table>
            		<tr>
	            		<td><input class = "flatButton" type = "submit" name = "chat_submit_show_messages" value = "{$smarty.const._SHOW}" /></td>
	            		<td><input class = "flatButton" type = "submit" name = "chat_submit_delete_messages" value = "{$smarty.const._DELETE}" onclick = "return confirm('{$smarty.const._AREYOUSUREYOUTODELETETHISCONVERSATION}');" /></td>
	            		<td><input class = "flatButton" type = "submit" name = "chat_submit_export_messages" value = "{$smarty.const._EXPORT}" /></td>
            		</tr>
            	</table>
            	</td>	
        </table>
        {if $smarty.const.MSIE_BROWSER == 1}
        {literal}
        <script>
            select_item = document.getElementById('select_user_id');
            select_item.selIndex = 0;

            select_item.onfocus = function(){ this.selIndex = this.selectedIndex; };
            emulateDisabledOptions(select_item);
        </script>
        {/literal}
        {/if}

        </form>
    {/if}

    {if $smarty.get.change_sidebar_width}
    	<script>
    	{literal}
    	function checkNewSidebar() {
    		if(!document.getElementById('sidebar_width').value.match(/^\d*$/))  {
    			alert("{/literal}{$smarty.const._VALUESUBMITTEDISNOTNUMERICAL}{literal}");
    			return false;
    		} else {
    		
    			new_value = parseInt(document.getElementById('sidebar_width').value);
    			if (new_value < 175 || new_value > 450) {
    				alert("{/literal}{$smarty.const._SIDEBARVALUESMUSTBEBETWEEN} 175 {$smarty.const._AND} 450{literal}");
    				return false;
    			}
 
    		}
    		return true;
    	}
    	{/literal}
    	</script>
        <form name = "change_sidebar_form" method = "post" action = "" target="_parent">
        <table>
            <tr>
            	<td>{$smarty.const._SIDEBARWIDTH}:</td>
				<td><input size="32" width ="200px" type="text" name="sidebar_width" value="{$T_INITWIDTH}" id="sidebar_width" /></td>
			</tr>
			<tr>
				<td>&nbsp;</td>	
				<td><input type="submit" name="new_sidebar_width" input class = "flatButton"  value = "{$smarty.const._SUBMIT}" onClick="return checkNewSidebar();" /></td>
			</tr>
        </table>    
            
        </form>
    {/if}
    
    {if $smarty.post.chat_submit_show_messages}
        <table border = "0" align = "center">

        {foreach name = 'messages_list' item = 'message' from = $T_MESSAGES}
            {if $message.users_LOGIN == $smarty.session.s_login}
                {assign var = 'span_class' value = 'chatMyMessages'}
            {elseif $message.users_USER_TYPE == 'student'}
                {assign var = 'span_class' value = 'chatStudentMessages'}
            {elseif $message.users_USER_TYPE == 'professor'}
                {assign var = 'span_class' value = 'chatProfessorMessages'}
            {elseif $message.users_USER_TYPE == 'administrator'}
                {assign var = 'span_class' value = 'chatAdministratorMessages'}
            {else}
                {assign var = 'span_class' value = ''}
            {/if}
            <tr><td nowrap class = "{$span_class}">#filter:timestamp_time-{$message.timestamp}#, <span class = "boldFont">{$message.users_LOGIN}</span>: </td><td class = "{$span_class}">{$message.content}</td></tr>
        {foreachelse}
            <tr><td align = "center" class = "emptyCategory">{$smarty.const._NOMESSAGESFOUNDFORTHISINTERVALANDUSER}</td></tr>
        {/foreach}
        </table>
    {/if}

    {if $smarty.get.show_users}
        <table border = "0" align = "center">
            <td align = "center" class = "horizontalSeparator"><span class = "boldFont">{$T_CHATROOM_NAME}</span></td>
        {section name = "users_list" loop = $T_USERS_LIST}
            <tr><td align = "center">{$T_USERS_LIST[users_list]}</td></tr>
        {/section}
        </table>
    {/if}

{/if}