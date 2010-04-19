{foreach name = "custom_block_links_list" item = "item" key = "key" from = $T_CUSTOM_BLOCKS}
 {if $T_POSITIONS.enabled.$key == 1}
  <a href = "{$smarty.server.PHP_SELF}?ctg={$key}">{$item.title}</a><br/>
 {/if}
{/foreach}
