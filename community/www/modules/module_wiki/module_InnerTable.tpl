{capture name = 't_inner_table_wiki_pages'}
<table width="100%">
{foreach name = 'pages_loop' key = key item = item from = $T_MODULE_WIKI_WIKIPAGES}
{if $smarty.foreach.pages_loop.iteration <=5 }
<tr>
 <td align="left">
 <!-- <a href={$smarty.const.G_LESSONSLINK}{$smarty.session.s_lessons_ID}/wiki/index.php?n={$key}>{$key}</a> -->

  <a href={$T_MODULE_WIKI_BASEURL}&n={$key}>{$key}</a>
 </td>
 <td align="right">{$item}</td>
</tr>
{/if}
{foreachelse}
  <tr><td class = "emptyCategory">{$smarty.const._WIKI_NOPAGESFOUND}</td></tr>
{/foreach}
</table>
{/capture}


{eF_template_printBlock title = '<a href='|cat:$T_MODULE_WIKI_BASEURL|cat:'>'|cat:$smarty.const._WIKI_WIKI|cat:'</a>' data = $smarty.capture.t_inner_table_wiki_pages image = $T_MODULE_WIKI_BASELINK|cat:'images/eFrontWiki32.png' absoluteImagePath=1 options = $T_WIKI_INNERTABLE_OPTIONS}
