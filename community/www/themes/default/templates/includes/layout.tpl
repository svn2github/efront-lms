{if $T_ADD_BLOCK_FORM && $_change_}
    {$T_ADD_BLOCK_FORM.javascript}
    <form {$T_ADD_BLOCK_FORM.attributes}>
        {$T_ADD_BLOCK_FORM.hidden}
        <table style = "width:100%">
         <tr><td class = "labelCell">{$T_ADD_BLOCK_FORM.title.label}:&nbsp;</td>
          <td class = "elementCell">{$T_ADD_BLOCK_FORM.title.html}</td></tr>
   <tr><td></td><td id = "toggleeditor_cell1">
    <div class = "headerTools">
     <span>
      <img class = "handle" id = "arrow_down" src = "images/16x16/navigate_down.png" alt = "{$smarty.const._OPENCLOSEFILEMANAGER}" title = "{$smarty.const._OPENCLOSEFILEMANAGER}"/>&nbsp;
      <a href = "javascript:void(0)" onclick = "toggleFileManager(this);">{$smarty.const._TOGGLEFILEMANAGER}</a>
     </span>
     <span>
      <img src = "images/16x16/order.png" title = "{$smarty.const._TOGGLEHTMLEDITORMODE}" alt = "{$smarty.const._TOGGLEHTMLEDITORMODE}" />&nbsp;
      <a href = "javascript:toggleEditor('editor_data', 'mceEditor');" id = "toggleeditor_link">{$smarty.const._TOGGLEHTMLEDITORMODE}</a>
     </span>
    </div>
    </td></tr>
            <tr><td></td><td id = "filemanager_cell"></td></tr>
         <tr><td class = "labelCell">{$T_ADD_BLOCK_FORM.content.label}:&nbsp;</td>
          <td class = "elementCell">{$T_ADD_BLOCK_FORM.content.html}</td></tr>
   {if $smarty.get.edit_footer || $smarty.get.edit_header}
   <tr><td></td>
    <td class = "elementCell"><img src = "images/16x16/rules.png" alt = "{$smarty.const._RESET}" title = "{$smarty.const._RESET}" onclick = "if (confirm('{$smarty.const._DELETECUSTOMHEADERAREYOUSURE}')) resetHeader(this);" style = "cursor:pointer"></td></tr>
   <tr><td class = "labelCell">{$smarty.const._SYSTEMENTITIES}:&nbsp;</td>
    <td class = "elementCell">
     {foreach name = "entities_loop" item = "item" key = "key" from = $T_SYSTEM_ENTITIES}
      {$item}{if !$smarty.foreach.entities_loop.last},{/if}
     {/foreach}
    </td></tr>
   {/if}
         <tr><td></td>
          <td class = "submitCell"><input type = "button" value = "{$smarty.const._CANCEL}" onclick = "location = '{$smarty.server.PHP_SELF}?ctg=themes'" class = "flatButton"/> {$T_ADD_BLOCK_FORM.submit_block.html}</td></tr>
        </table>
    </form>
 <div id = "fmInitial"><div id = "filemanager_div" style = "display:none;">{$T_FILE_MANAGER}</div></div>
{else}

    {capture name = "t_layout_code"}
     {capture name = "layoutPreview"}
     <table class = "layout preview">
      <tr><td class = "header" colspan = "3"></td></tr>
      <tr><td class = "layoutLeft">
           <div class = "layoutBlock">&nbsp;</div>
           <div class = "layoutBlock">&nbsp;</div>
           <div class = "layoutBlock">&nbsp;</div>
          </td><td class = "layoutCenter">
                 <div class = "layoutBlock">&nbsp;</div>
                 <div class = "layoutBlock">&nbsp;</div>
          </td><td class = "layoutRight">
           <div class = "layoutBlock">&nbsp;</div>
           <div class = "layoutBlock">&nbsp;</div>
           <div class = "layoutBlock">&nbsp;</div>
          </td></tr>
      <tr><td class = "footer" colspan = "3"></td></tr>
     </table>
     {/capture}

     {capture name = "layoutEdit"}
     <table class = "layout edit" id = "editLayout">
      <tr><td class = "header {if $T_LAYOUT_SETTINGS->options.show_header == 0}collapse{/if}" colspan = "3">&nbsp;
{* <img class = "ajaxHandle" src = "images/16x16/{if $T_LAYOUT_SETTINGS->options.show_header == 0}navigate_down{else}navigate_up{/if}.png" alt = "{$smarty.const._HIDEHEADER}" title = "{$smarty.const._HIDEHEADER}" style = "float:right" onclick = "hideHeader(this, 'header')"/>
        <a style = "float:left" href = "{$smarty.server.PHP_SELF}?ctg=themes&theme={$smarty.get.theme}&edit_header=1"><img src = "images/16x16/edit.png" alt = "{$smarty.const._EDITHEADER}" title = "{$smarty.const._EDITHEADER}" /></a>
*}
       </td></tr>
      <tr><td class = "layoutLeft"><ul class = "sortable" id = "leftList"></ul></td>
       <td class = "layoutCenter"><ul class = "sortable" id = "centerList"></ul></td>
       <td class = "layoutRight"><ul class = "sortable" id = "rightList"></ul></td></tr>
      <tr><td class = "footer {if $T_LAYOUT_SETTINGS->options.show_footer != 1}collapse{/if}" colspan = "3">&nbsp;
{* <img class = "ajaxHandle" src = "images/16x16/{if $T_LAYOUT_SETTINGS->options.show_footer != 1}navigate_down{else}navigate_up{/if}.png" alt = "{$smarty.const._HIDEFOOTER}" title = "{$smarty.const._HIDEFOOTER}" style = "float:right" onclick = "hideHeader(this, 'footer')"/>
        <a style = "float:left" href = "{$smarty.server.PHP_SELF}?ctg=themes&theme={$smarty.get.theme}&edit_footer=1"><img src = "images/16x16/edit.png" alt = "{$smarty.const._EDITFOOTER}" title = "{$smarty.const._EDITFOOTER}" /></a>
*}
       </td></tr>
     </table>
     {/capture}
{if $_change_}
    <div class = "headerTools">
     <span>
      <img src = "images/16x16/layout.png" alt = "{$smarty.const._CHANGELAYOUTFORTHEME}" title = "{$smarty.const._CHANGELAYOUTFORTHEME}" />
      <label for = "layout_for_theme">{$smarty.const._CHANGELAYOUTFORTHEME}:&nbsp;</label>
      <select name = "layout_for_theme" id = "layout_for_theme" onchange = "location = '{$smarty.server.PHP_SELF}?ctg=themes&theme_layout='+this.options[this.options.selectedIndex].value">
      {foreach name = 'themes_list' item = "theme" key = "id" from = $T_THEMES}
       <option value = "{$id}" {if $smarty.get.theme_layout==$id}selected{elseif !$smarty.get.theme_layout && $T_LAYOUT_THEME->themes.id==$id}selected{/if}>{$theme.name}</option>
      {/foreach}
      </select>
     </span>
     <span class = "headerTools">
      <img src = "images/16x16/import.png" alt = "{$smarty.const._IMPORT}" title = "{$smarty.const._IMPORTSETTINGS}" />
      <a href = "javascript:void(0);" onclick = "eF_js_showDivPopup('{$smarty.const._IMPORTSETTINGS}', 0, 'import_settings')">{$smarty.const._IMPORTSETTINGS}</a>
     </span>
     <span class = "headerTools">
      <img src = "images/16x16/export.png" alt = "{$smarty.const._EXPORT}" title = "{$smarty.const._EXPORTSETTINGS}" />
      <a href = "javascript:void(0);" onclick = "exportLayout(this)">{$smarty.const._EXPORTSETTINGS}</a>
     </span>
    </div>
    <div id = "import_settings" style = "display:none">

        {$T_IMPORT_SETTINGS_FORM.javascript}
        <form {$T_IMPORT_SETTINGS_FORM.attributes}>
            {$T_IMPORT_SETTINGS_FORM.hidden}
            <table class = "formElements">
                <tr><td class = "labelCell">{$T_IMPORT_SETTINGS_FORM.file_upload.label}:&nbsp;</td>
                    <td>{$T_IMPORT_SETTINGS_FORM.file_upload.html}</td></tr>
                <tr><td></td><td class = "infoCell">{$smarty.const._EACHFILESIZEMUSTBESMALLERTHAN} <b>{$T_MAX_FILESIZE}</b> {$smarty.const._KB}</td></tr>
                <tr><td></td><td class = "submitCell">{$T_IMPORT_SETTINGS_FORM.submit_import.html}</td></tr>
            </table>
        </form>
    </div>
{/if}
    <table id = "layoutMenu">
     <tr>
      <td id = "left">
             <div class = "layoutLabel mediumHeader">{$smarty.const._SELECTLAYOUT}</div>
             <div id = "layout_simple" class = "layout hideBoth" onclick = "setSelected(this);">{$smarty.capture.layoutPreview}</div>
             <div class = "layoutLabel">{$smarty.const._SIMPLE}</div>
             <div id = "layout_left" class = "layout hideRight" onclick = "setSelected(this);">{$smarty.capture.layoutPreview}</div>
             <div class = "layoutLabel">{$smarty.const._TWOCOLUMNSLEFT}</div>
             <div id = "layout_three" class = "layout" onclick = "setSelected(this);">{$smarty.capture.layoutPreview}</div>
             <div class = "layoutLabel">{$smarty.const._THREECOLUMNS} ({$smarty.const._DEFAULT})</div>
             <div id = "layout_right" class = "layout hideLeft" onclick = "setSelected(this);">{$smarty.capture.layoutPreview}</div>
             <div class = "layoutLabel">{$smarty.const._TWOCOLUMNSRIGHT}</div>
            </td>
            <td id = "center">
             <div class = "layoutLabel mediumHeader">{$smarty.const._CURRENTLAYOUT} <a href = "{$smarty.const.G_SERVERNAME}" target = "_NEW" ><img src = "images/16x16/search.png" alt = "{$smarty.const._PREVIEWSAVEDLAYOUT}" title = "{$smarty.const._PREVIEWSAVEDLAYOUT}"></a></div>
             <div class = "layout">{$smarty.capture.layoutEdit}</div>
            </td>
            <td id = "right">
             <div class = "layoutLabel mediumHeader">{$smarty.const._AVAILABLEBLOCKS}</div>
             <div class = "layoutBlock"><img src = "images/32x32/add.png" alt = "{$smarty.const._ADDBLOCK}" title = "{$smarty.const._ADDBLOCK}" onclick = "location = location + '&tab=layout&add_block=1'" class = "handle"></div>
       <ul class = "sortable" id = "toolsList"></ul>
            </td>
        </tr>
{if $_change_}
        <tr><td colspan = "3" id = "buttons">
          <div>
           <span>
            <img class = "ajaxHandle" src = "images/32x32/generic.png" alt = "{$smarty.const._RESTOREDEFAULTLAYOUT}" title = "{$smarty.const._RESTOREDEFAULTLAYOUT}" onclick = "if (confirm('{$smarty.const._THISWILLDELETECUSTOMBLOCKS}')) updateLayoutPositions(this, true)" />
            <br/>{$smarty.const._RESTOREDEFAULTLAYOUT}
           </span>
           <span>
            <img class = "ajaxHandle" src = "images/32x32/go_back.png" alt = "{$smarty.const._UNDOALLCHANGES}" title = "{$smarty.const._UNDOALLCHANGES}" onclick = "location=location+'&tab=layout'" />
            <br/>{$smarty.const._UNDOALLCHANGES}
           </span>
           <span>
            <img class = "ajaxHandle" src = "images/32x32/success.png" alt = "{$smarty.const._SAVELAYOUT}" title = "{$smarty.const._SAVELAYOUT}" onclick = "updateLayoutPositions(this)">
            <br/>{$smarty.const._SAVELAYOUT}
           </span>
          </div>
        </td></tr>
{/if}
    </table>
    {/capture}

    {eF_template_printBlock title = "`$smarty.const._LAYOUTFORTHEME`<span class = 'innerTableName'>&nbsp;&quot;`$T_LAYOUT_THEME->themes.name`&quot;</span>" data = $smarty.capture.t_layout_code image = '32x32/layout.png'}

    <script type="text/javascript">
     var edittag = '{$smarty.const._EDIT}';var deletetag = '{$smarty.const._DELETE}';var irreversible = '{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}';var toggletag='{$smarty.const._TOGGLEACCESSASSEPARATEPAGE}';
     var positions = '{$T_POSITIONS}';
     var blocks = '{$T_BLOCKS}';
     var currentLayout = '';
    </script>
{/if}
