{* smarty template for logout user *}

{capture name = 't_logout_user_code'}
	{$T_LOGOUT_USER_FORM.javascript}
	<form {$T_LOGOUT_USER_FORM.attributes}>
	{$T_LOGOUT_USER_FORM.hidden}
		<table class = "formElements">
		    <tr><td class = "labelCell">{$smarty.const._CHOOSEUSERTODISCONNECT}:&nbsp;</td><td>{$T_LOGOUT_USER_FORM.user_type.html}</td></tr>
		    <tr><td></td><td class = "submitCell">{$T_LOGOUT_USER_FORM.submit_logout_user.html}</td></tr>    
		</table>
	</form>
{/capture}


{eF_template_printBlock title = $smarty.const._LOGOUTUSER data = $smarty.capture.t_logout_user_code image = '32x32/logout.png'}

{if $T_MESSAGE}
    {if $T_MESSAGE_TYPE == 'success'}
        <script>
            re = /\?/;
            !re.test(parent.location) ? parent.location = parent.location+'?reset_popup=1' : parent.location = parent.location+'&reset_popup=1';            
        </script>
    {/if}
{/if}
