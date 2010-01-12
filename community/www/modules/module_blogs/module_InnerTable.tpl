{capture name = 't_inner_table_blogs_pages}
<table width="100%">
{foreach name = 'blogs_loop' key = key item = blog from = $T_BLOGS_BLOGS}
{if $smarty.foreach.blogs_loop.iteration <=5 }
<tr>
	<td align="left">
		<a href="{$T_MODULE_BLOGS_BASEURL}&view_blog={$blog.id}">{$blog.name}</a>
	</td>
	<td align="right">{$blog.users_LOGIN}, #filter:timestamp_interval-{$blog.timestamp}# {$smarty.const._AGO}</td>
</tr>
{/if}
{foreachelse}
  <tr><td class = "emptyCategory">{$smarty.const._BLOGS_NOBLOGSFOUND}</td></tr>
{/foreach}
</table>
{/capture}


{eF_template_printBlock title = '<a href='|cat:$T_MODULE_BLOGS_BASEURL|cat:'>'|cat:$smarty.const._BLOGS_BLOG|cat:'</a>' data = $smarty.capture.t_inner_table_blogs_pages   image = $T_MODULE_BLOGS_BASELINK|cat:'images/eFrontBlog32.png' absoluteImagePath=1 options = $T_BLOGS_INNERTABLE_OPTIONS}
