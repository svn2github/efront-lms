{literal}
<script type="text/javascript">
var thumbnailmodulebaselink = '{/literal}{$T_THUMBNAIL_MODULE_BASELINK}{literal}highslide/graphics/';
</script>
{/literal}

{* template functions for inner table *}
{capture name = 't_thumbnail_list_code'}
<div style="height:110px;overflow:auto">
{foreach name =thumbnail item=image from = $T_THUMBNAIL_INNERTABLE}

<a href="{$smarty.const.G_LESSONSLINK}{$image.lessons_ID}/module_thumbnail/{$image.filename}" class="highslide" onclick="return hs.expand(this)">
	<img src="{$smarty.const.G_LESSONSLINK}{$image.lessons_ID}/module_thumbnail/{$image.filename}" width="80px" height="100px" alt="Highslide JS"
		title="Click to enlarge" />

</a>
<!-- Caption -->
<div class="highslide-caption">
	{$image.title}
</div>
{foreachelse}
<div style="valign:middle">
<table width="400px" height="110px" valign="middle"><tr><td class = "emptyCategory">{$smarty.const._THUMBNAILNOMEETINGSCHEDULED}</td></tr></table>
</div>
{/foreach}
</div>
{/capture}


{eF_template_printBlock title=$smarty.const._THUMBNAIL_THUMBNAILLIST data=$smarty.capture.t_thumbnail_list_code image='32x32/photo.png' options=$T_MODULE_THUMBNAIL_INNERTABLE_OPTIONS}
