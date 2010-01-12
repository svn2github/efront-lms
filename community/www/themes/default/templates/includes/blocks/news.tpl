{strip}

{foreach name = 'news_list' item = "item" key = "key" from = $T_NEWS}
    	<div class = "newsTitle"><div>#filter:timestamp-{$item.timestamp}#</div>{$item.title}</div>
    	<div class = "newsContent">{$item.data}</div>
{foreachelse}
    	{if $T_CONFIGURATION.display_empty_blocks}<span class = "small emptyCategory">{$smarty.const._NOSYSTEMANNOUNCEMENTS}</span>{/if}
{/foreach}

{/strip}