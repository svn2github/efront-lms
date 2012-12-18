{if $smarty.session.s_type == 'administrator'}
 {capture name = 't_import_code'}
  <div class = "headerTools">
   <span>
    <img src = "images/16x16/import.png" alt = "{$smarty.const._UPLOAD}" title = "{$smarty.const._UPLOAD}"/>
    <a href = "javascript:void(0)" onclick = "eF_js_showDivPopup('{$smarty.const._UPLOADFILE}', 1, 'module_leaflet_upload_div')">{$smarty.const._UPLOADFILE}</a>
   </span>
  </div>
  <div id = "module_leaflet_upload_div" style = "display:none">
   {eF_template_printForm form = $T_UPLOAD_FORM}
  </div>
  {$T_FILE_MANAGER}
 {/capture}

 {capture name = "t_leaflet_code"}
  <div class = "tabber">
   {eF_template_printBlock tabber = "upload" title = $smarty.const._MODULE_LEAFLET_MANAGEFILES data = $smarty.capture.t_import_code image = '32x32/import.png'}
   {eF_template_printBlock tabber = "list" title=$smarty.const._MODULE_LEAFLET_FILESLIST columns=3 links=$T_FILES image='32x32/folders.png'}
  </div>
 {/capture}
 {eF_template_printBlock title = $smarty.const._MODULE_LEAFLET_MODULELEAFLET data = $smarty.capture.t_leaflet_code image = '32x32/information.png'}
{else}
 {eF_template_printBlock title=$smarty.const._MODULE_LEAFLET_MODULELEAFLET columns=3 links=$T_FILES image='32x32/information.png'}
{/if}
