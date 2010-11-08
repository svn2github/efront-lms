{* template functions for inner table *}
{literal}
<script>
function requestVideo(id) {

    // Check if the job description exists
    var url = '{/literal}{$T_YOUTUBE_MODULE_BASEURL}{literal}&postAjaxRequest=1&id='+id;
    new Ajax.Request(url, {
            method:'get',
            asynchronous:true,
            onSuccess: function (transport) {
             var table = document.getElementById('youtube_player');
             //table.innerHTML = transport.responseText;
             spanElement = document.createElement('span');
             spanElement.innerHTML += transport.responseText;
    table.parentNode.replaceChild(spanElement, table);
            }
        });

}
</script>
{/literal}

{capture name = 't_youtube_list_code'}
<table id="youtube_player">
<tr>
 {if isset($T_VIDEOLINK)}
 <td align="center" colspan="2">
  <object>
   <param name="movie" value="http://www.youtube.com/v/{$T_VIDEOLINK}"></param>
   <param name="allowFullScreen" value="true"></param>
   <param name="allowscriptaccess" value="always"></param>
   <embed src="http://www.youtube.com/v/{$T_VIDEOLINK}" type="application/x-shockwave-flash" allowfullscreen="true" width="400" height="323"></embed>
  </object>
 </td>
</tr>
<tr>
 <td width="50%" align="left">{if isset($T_PREVIOUS)}<a href = "javascript:void(0);" alt="{$T_PREV_TAG}" title="{$T_PREV_TAG}" onClick="requestVideo('{$T_PREVIOUS}')">{$smarty.const._YOUTUBE_PREVIOUS}</a>{/if}</td>
 <td width="50%" align="right">{if isset($T_NEXT)}<a href = "javascript:void(0);" alt="{$T_NEXT_TAG}" title="{$T_NEXT_TAG}" onClick="requestVideo('{$T_NEXT}')">{$smarty.const._YOUTUBE_NEXT}</a>{/if}</td>

 {else}
 <td class="emptyCategory" colspan="2">{$smarty.const._YOUTUBENOMEETINGSCHEDULED}</td>
 {/if}

</tr>
</table>
{/capture}

{eF_template_printBlock title=$smarty.const._YOUTUBE data=$smarty.capture.t_youtube_list_code absoluteImagePath=1 image=$T_YOUTUBE_MODULE_BASELINK|cat:'images/youtube32.png' options=$T_YOUTUBE_INNERTABLE_OPTIONS}
