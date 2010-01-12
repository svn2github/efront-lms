{strip}
{foreach name = "online_users" item = "item" key = "key" from = $T_ONLINE_USERS_LIST}
	#filter:login-{$item.login}#{if !$smarty.foreach.online_users.last},&nbsp;{/if}
{foreachelse}
    {if $T_CONFIGURATION.display_empty_blocks}<span class = "small emptyCategory">{$smarty.const._NOONELOGGEDIN}</span>{/if}
{/foreach}
{/strip}