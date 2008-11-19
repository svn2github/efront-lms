{include file = "includes/header.tpl"}
{if $T_MESSAGE}
        {eF_template_printMessage message = $T_MESSAGE type = $T_MESSAGE_TYPE}    
{/if}

<table>
	<tr><td>{$smarty.const._LOGIN}:&nbsp;</td><td>{$T_LOG_INFO.login}</td></tr>
	<tr><td>{$smarty.const._DATE}:&nbsp;</td><td>#filter:timestamp_time-{$T_LOG_INFO.timestamp}#</td></tr>
	<tr><td>{$smarty.const._IPADDRESS}:&nbsp;</td><td>{$T_LOG_INFO.session_ip}</td></tr>
	<tr><td>{$smarty.const._ACTION}:&nbsp;</td><td>{$T_LOG_INFO.action}</td></tr>
	{if $T_LOG_INFO.lesson}<tr><td>{$smarty.const._LESSON}:&nbsp;</td><td>{$T_LOG_INFO.lesson}</td></tr>{/if}
	{if $T_LOG_INFO.content}<tr><td>{$smarty.const._UNIT}:&nbsp;</td><td>{$T_LOG_INFO.content}</td></tr>{/if}
</table>

{include file = "includes/closing.tpl"}