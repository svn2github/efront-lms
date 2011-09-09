{*Smarty template*}
{literal}
<script>
// Function which checks if the extension of the file given for the avatar is valid
function testFileExtension()
{
    var file_types = new Array('.jpg', '.jpeg', '.gif', '.png', '.bmp', '.tif', '.tiff', '.ico', '.JPG','.JPEG', '.GIF', '.PNG', '.BMP', '.TIF', '.TIFF', '.ICO');
    var file_name = document.getElementById('avatar').value;
    if (!file_name) {
        return true;
    }

    var dots = file_name.split(".")
    var file_type = "." + dots[dots.length-1];

    if (file_types.join("").indexOf(file_type) == -1) {
{/literal}
        alert ('{$smarty.const._ONLYIMAGEFILESAREVALID}');
{literal}
        return false;
    } else {
        return true;
    }
}
</script>

{/literal}
{if $smarty.get.add_thumbnail || $smarty.get.edit_thumbnail}
 {capture name = 't_insert_thumbnail_code'}
    {$T_THUMBNAIL_FORM.javascript}
    <form {$T_THUMBNAIL_FORM.attributes}>
     {$T_THUMBNAIL_FORM.hidden}
     <table class = "formElements">
      <tr><td class = "labelCell">{$T_THUMBNAIL_FORM.title.label}:&nbsp;</td>
       <td class = "elementCell">{$T_THUMBNAIL_FORM.title.html}</td>
       <td class = "formError">{$T_THUMBNAIL_FORM.title.error}</td></tr>
      {if isset($smarty.get.add_thumbnail)}
      <tr><td class = "labelCell">{$T_THUMBNAIL_FORM.file_upload.label}:&nbsp;</td>
       <td class = "elementCell">{$T_THUMBNAIL_FORM.file_upload.html}</td>
       <td class = "formError">{$T_THUMBNAIL_FORM.file_upload.error}</td></tr>
      {else}
      <tr><td colspan="3"><img src="{$smarty.const.G_LESSONSLINK}{$image.lessons_ID}/module_thumbnail/{$image.filename}{$T_THUMBNAIL_MODULE_IMAGE.lessons_ID}/{$T_THUMBNAIL_MODULE_IMAGE.filename}" /></td></tr>
      {/if}
      <tr><td></td><td >&nbsp;</td></tr>

      <tr><td></td><td class = "submitCell">{$T_THUMBNAIL_FORM.submit_thumbnail.html}</td></tr>
     </table>
    </form>

 {/capture}

 {eF_template_printBlock title=$smarty.const._THUMBNAIL_THUMBNAILVIDEODATA data=$smarty.capture.t_insert_thumbnail_code image='32x32/photo.png' help = 'Thumbnails'}

{else}
 {capture name = 't_thumbnail_list_code'}
  {if $T_THUMBNAIL_CURRENTLESSONTYPE == "professor"}
  <table>
   <tr><td>
    <a href = "{$T_THUMBNAIL_MODULE_BASEURL}&add_thumbnail=1"><img src = "images/16x16/add.png" alt = "{$smarty.const._THUMBNAIL_ADDTHUMBNAIL}" title = "{$smarty.const._THUMBNAIL_ADDTHUMBNAIL}" border = "0" /></a>
   </td><td>
    <a href = "{$T_THUMBNAIL_MODULE_BASEURL}&add_thumbnail=1" title = "{$smarty.const._THUMBNAIL_ADDTHUMBNAIL}">{$smarty.const._THUMBNAIL_ADDTHUMBNAIL}</a>
   </td></tr>
  </table>
  {/if}

  <table border = "0" width = "100%" class="sortedTable" sortBy = "0">
   <tr class = "topTitle">
    <td class = "topTitle">{$smarty.const._THUMBNAIL_NAME}</td>
    <td class = "topTitle" align="center" >{$smarty.const._THUMBNAIL_PREVIEW}</td>
    {if $T_THUMBNAIL_CURRENTLESSONTYPE == "professor"}
    <td class = "topTitle" align="center">{$smarty.const._OPERATIONS}</td>
    {/if}
   </tr>

   {foreach name =thumbnail item=image from = $T_THUMBNAIL}
   <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
    <td>{$image.title}</td>
    <td align="center" ><img align="center" width="50px" height="30px" src="{$smarty.const.G_LESSONSLINK}{$image.lessons_ID}/module_thumbnail/{$image.filename}" /></td>
    {if $T_THUMBNAIL_CURRENTLESSONTYPE == "professor"}
    <td align = "center">
     <table>
      <tr>
      <td width="45%">
       <a href = "{$T_THUMBNAIL_MODULE_BASEURL}&edit_thumbnail={$image.id}" class = "editLink"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
      </td>
      <td width="45%">
       <a href = "{$T_THUMBNAIL_MODULE_BASEURL}&delete_thumbnail={$image.id}" onclick = "return confirm('{$smarty.const._THUMBNAILAREYOUSUREYOUWANTTODELETEEVENT}')" class = "deleteLink"><img border = "0" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a>
      </td>
      </tr>
      </table>
    </td>
    {/if}
   </tr>
   {foreachelse}
   <tr><td colspan="5" class = "emptyCategory">{$smarty.const._THUMBNAILNOMEETINGSCHEDULED}</td></tr>
   {/foreach}
  </table>
 {/capture}


 {eF_template_printBlock title=$smarty.const._THUMBNAIL_THUMBNAILLIST data=$smarty.capture.t_thumbnail_list_code image='32x32/photo.png' help = 'Thumbnails'}
{/if}
