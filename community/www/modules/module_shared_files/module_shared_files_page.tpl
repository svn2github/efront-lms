{capture name = "t_shared_files_code"}
{if $T_SHARED_FILES_ENABLED}
 {$smarty.const._MODULE_SHARED_FILES_SHAREDFILESENABLED}
{else}
<!--ajax:sharedFilesTable-->
 <table class = "sortedTable" sortBy = "0" style = "width:100%" size = "{$T_TABLE_SIZE}" id = "sharedFilesTable" useAjax = "1" rowsPerPage = "20" other = "{$T_CURRENT_DIRECTORY}" url = "{$T_MODULE_BASEURL}&" nomass = "1" currentDir = "{$T_DIR_PATH}">
  <tr>
   <td class = "topTitle centerAlign" name = "image">{$smarty.const._FILETYPE}</td>
   <td class = "topTitle" name = "name" >{$smarty.const._FILENAME}</td>
   <td class = "topTitle" name = "size" >{$smarty.const._SIZE}</td>
   <td class = "topTitle centerAlign" name = "share">{$smarty.const._SHARE}</td>
  </tr>
  {if $T_PARENT_DIR}
        <tr class = "defaultRowHeight eventRowColor"><td class = "centerAlign" colspan = "100%">{$smarty.const._CURRENTLYBROWSINGFOLDER}: {$T_CURRENT_DIR}</td></tr>
        <tr class = "defaultRowHeight oddRowColor">
         <td class = "centerAlign"><span style = "display:none"></span><img src = "images/16x16/folder_up.png" alt = "{$smarty.const._UPONELEVEL}" title = "{$smarty.const._UPONELEVEL}"/></td>
            <td><a class="editLink" href = "javascript:void(0)" onclick = "eF_js_rebuildTable($('sharedFilesTable'), 0, 'image', 'asc', '{$T_PARENT_DIR}');">.. ({$smarty.const._UPONELEVEL})</a></td>
            <td colspan = "5"></td></tr>
  {/if}
  {foreach name = 'files_list' key = 'key' item = 'file' from = $T_DATA_SOURCE}
  <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"}">
   <td class = "centerAlign"><img src = "{$file.image}" alt = "{$file.mime_type}" title = "{$file.mime_type}"/></td>
   <td>
    {if $file.type == 'directory'}
     <a class="editLink" href = "javascript:void(0)" onclick = "eF_js_rebuildTable($('sharedFilesTable'), 0, 'image', 'asc', '{$file.path}');">{$file.name}</a>
    {elseif $file.preview}
     <a href = "view_file.php?file={$file.path}" onclick = "eF_js_showDivPopup('{$smarty.const._PREVIEW}', 2);" target = "POPUP_FRAME">{$file.name}</a>
    {else}
     <a href = "view_file.php?file={$file.path}&action=download">{$file.name}</a>
    {/if}
   </td>
   <td>{if $file.size}{$file.size} {$smarty.const._KB}{/if}</td>
   <td class = "centerAlign">
    <img class = "ajaxHandle" src = "images/16x16/trafficlight_{if !$file.module_shared_files_status}red{else}green{/if}.png" alt = "{$smarty.const._MODULE_SHARED_FILES_SETSHARESTATUS}" title = "{$smarty.const._MODULE_SHARED_FILES_SETSHARESTATUS}" onclick = "moduleSharedFiles(this, '{$file.path}');"/>
   </td>
  </tr>
  {foreachelse}
  <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "4">{$smarty.const._NODATAFOUND}</td></tr>
  {/foreach}
 </table>
<!--/ajax:sharedFilesTable-->
{/if}
{/capture}
{eF_template_printBlock title = $smarty.const._MODULE_SHARED_FILES_SHAREDFILES data = $smarty.capture.t_shared_files_code}
