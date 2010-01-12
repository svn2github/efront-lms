{include file = "includes/header.tpl"}
{if $T_MESSAGE}
	<table style = "width:100%">
		<tr class = "messageRow">
		    <td colspan = "100%">{eF_template_printMessage message=$T_MESSAGE type=$T_MESSAGE_TYPE}</td>        {*Display Message, if any*}
		</tr>		
	</table>
	<div style = "font-size:16px;margin-top:30px;font-weight:bold;text-align:center"><a href = "javascript:void(0)" onclick = "window.close()">{$smarty.const._CLOSEWINDOW}</div>
{/if}
{include file = "includes/closing.tpl"}