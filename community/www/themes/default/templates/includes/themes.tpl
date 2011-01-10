{if $smarty.get.add || $smarty.get.edit}
 {if !$_student_ && ($smarty.get.add || $smarty.get.edit)}
  {if $T_MESSAGE_TYPE == 'success'}
     <script>
         //re = /\?/;
         parent.location = parent.location+'&tab=set_theme';
     </script>
  {/if}
 {/if}
 {capture name = "t_add_theme_code"}
  {$T_ENTITY_FORM.javascript}
  <form {$T_ENTITY_FORM.attributes}>
      {$T_ENTITY_FORM.hidden}
      <table>
          <tr><td class = "labelCell">{$T_ENTITY_FORM.theme_file.label}:</td>
              <td class = "elementCell">{$T_ENTITY_FORM.theme_file.html}</td></tr>
          <tr><td></td>
              <td class = "infoCell">{$smarty.const._EACHFILESIZEMUSTBESMALLERTHAN} <b>{$T_MAX_FILESIZE}</b> {$smarty.const._KB}</td></tr>
          <tr><td class = "labelCell">{$T_ENTITY_FORM.remote_theme.label}:</td>
              <td class = "elementCell">{$T_ENTITY_FORM.remote_theme.html}</td></tr>
          <tr><td></td>
              <td class = "infoCell">{$smarty.const._REMOTETHEMEXMLFILE}</td></tr>
          <tr><td></td><td class = "submitCell">{$T_ENTITY_FORM.submit_theme.html}</td></tr>
   </table>
  </form>
 {/capture}
 {if $smarty.get.edit}{assign var = "block_title" value = $smarty.const._UPDATETHEME}{else}{assign var = "block_title" value = $smarty.const._INSTALLTHEME}{/if}
 {eF_template_printBlock title=$block_title data=$smarty.capture.t_add_theme_code image='32x32/themes.png'}
{else}
 {capture name = "t_layout_tab"}
  {include file = "includes/layout.tpl"}
 {/capture}

 {if !$T_REMOTE_THEME}
  {capture name = "t_theme_external_tab"}
   {if $smarty.get.file_manager}
       {$T_FILE_MANAGER}
   {elseif $smarty.get.add_page || $smarty.get.edit_page}
       {capture name='t_new_page_code'}
     <script>var tinyMCEmode = true;</script>
              {$T_CMS_FORM.javascript}
              <form {$T_CMS_FORM.attributes}>
              {$T_CMS_FORM.hidden}
              <table class = "formElements" style = "width:100%">
                  <tr><td class = "labelCell">{$T_CMS_FORM.name.label}:&nbsp;</td>
                      <td class = "elementCell">{$T_CMS_FORM.name.html}</td></tr>
                  {if $T_CMS_FORM.name.error}<tr><td></td><td class = "formError">{$T_CMS_FORM.name.error}</td></tr>{/if}
      <tr><td></td><td id = "toggleeditor_cell1">
       <div class = "headerTools">
        <span>
         <img class = "handle" id = "arrow_down" src = "images/16x16/navigate_down.png" alt = "{$smarty.const._OPENCLOSEFILEMANAGER}" title = "{$smarty.const._OPENCLOSEFILEMANAGER}"/>&nbsp;
         <a href = "javascript:void(0)" onclick = "toggleFileManager(this);">{$smarty.const._TOGGLEFILEMANAGER}</a>
        </span>
        <span>
         <img src = "images/16x16/order.png" title = "{$smarty.const._TOGGLEHTMLEDITORMODE}" alt = "{$smarty.const._TOGGLEHTMLEDITORMODE}" />&nbsp;
         <a href = "javascript:toggleEditor('editor_cms_data', 'mceEditor');" id = "toggleeditor_link">{$smarty.const._TOGGLEHTMLEDITORMODE}</a>
        </span>
       </div>
       </td></tr>
               <tr><td></td><td id = "filemanager_cell"></td></tr>
                  <tr><td class = "labelCell">{$T_CMS_FORM.page.label}:&nbsp;</td>
                      <td class = "elementCell">{$T_CMS_FORM.page.html}</td>
                  </tr>
                  <tr><td></td><td class = "infoCell">{$smarty.const._YOUMUSTPROVIDELOGINLINK}</td></tr>
                  {if $T_CMS_FORM.page.error}<tr><td></td><td class = "formError">{$T_CMS_FORM.page.error}</td></tr>{/if}
                  <tr><td></td><td class = "submitCell">{$T_CMS_FORM.submit_cms.html}</td></tr>
              </table>
              </form>
     <div id = "fmInitial"><div id = "filemanager_div" style = "display:none;">{$T_FILE_MANAGER}</div></div>
       {/capture}

       {if $smarty.get.edit_page != ""}
           {eF_template_printBlock title = "`$smarty.const._UPDATEPAGE` <span class = 'innerTableName'>&quot;`$smarty.get.edit_page` &quot;</span>" data = $smarty.capture.t_new_page_code image = '32x32/unit.png'}
       {else}
           {eF_template_printBlock title = $smarty.const._NEWPAGE data = $smarty.capture.t_new_page_code image = '32x32/unit.png'}
       {/if}
   {else}
       {capture name = 't_cms_code'}
           {if $_change_}
              <div class = "headerTools">
                  <span>
                      <img src = "images/16x16/add.png" alt = "{$smarty.const._NEWPAGE}" title = "{$smarty.const._NEWPAGE}">
                      <a href = "administrator.php?ctg=themes&tab=external&add_page=1">{$smarty.const._NEWPAGE}</a>
                  </span>
              </div>
           {/if}
              <table class = "sortedTable" width = "100%" id = 'cms_table'>
                  <tr class = "topTitle">
                      <td class = "topTitle">{$smarty.const._NAME}</td>
                      <td class = "topTitle centerAlign">{$smarty.const._DEFAULTPAGE}</td>
                      <td class = "topTitle centerAlign">{$smarty.const._OPERATIONS}</td>
                  </tr>
           {foreach name = 'pages_list' key = 'key' item = 'page' from = $T_CMS_PAGES}
                  <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                      <td>
               {if $_change_}
                      <a href = "administrator.php?ctg=themes&tab=external&edit_page={$page}" class = "editLink">{$page}</a>
               {else}
                   {$page}
               {/if}
                   </td>
                   <td class = "centerAlign">
                       {if ($page == $T_DEFAULT_PAGE)}
                           <img class = "ajaxHandle" src = "images/16x16/pin_green.png" alt = "{$smarty.const._USENONE}" title = "{$smarty.const._USENONE}" {if $_change_}onclick = "usePage(this, '{$page}')"{/if}>
                       {else}
                           <img class = "ajaxHandle" src = "images/16x16/pin_red.png" alt = "{$smarty.const._USETHIS}" title = "{$smarty.const._USETHIS}" {if $_change_}onclick = "usePage(this, '{$page}')"{/if}/>
                       {/if}
                   </td>
                   <td class = "centerAlign">
                       <a href = "{$smarty.const.G_SERVERNAME}{$smarty.const.G_CURRENTTHEMEURL}external/{$page}.php" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._PREVIEW}', 2)"><img src = "images/16x16/search.png" title = "{$smarty.const._PREVIEW}" alt = "{$smarty.const._PREVIEW}" /></a>
                   {if $_change_}
                       <a href = "administrator.php?ctg=themes&tab=external&edit_page={$page}"><img src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
                       <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._AREYOUSUREYOUWANTTODELETEPAGE}')) deleteEntity(this, '{$page}');"/>
                   {/if}
                </td>
               </tr>
           {foreachelse}
            <tr class = "defaultRowHeight oddRowColor"><td colspan = "3" class = "emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
           {/foreach}
           </table>
       {/capture}

       {eF_template_printBlock title = $smarty.const._UPDATEPAGES data = $smarty.capture.t_cms_code image = '32x32/unit.png'}
   {/if}
  {/capture}
 {/if}

 {capture name = "t_change_theme_tab"}
  {capture name = "t_themes_code"}
  <script>var activetheme = '{$smarty.const._ACTIVETHEME}';var usetheme = '{$smarty.const._USETHEME}';</script>
  {if $_change_}
  <div class = "headerTools">
   <span>
    <img src = "images/16x16/add.png" title = "{$smarty.const._INSTALLTHEME}" alt = "{$smarty.const._INSTALLTHEME}"/>
    <a href = "{$smarty.server.PHP_SELF}?ctg=themes&add=1&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._INSTALLTHEME}', 2)" title = "{$smarty.const._INSTALLTHEME}" target = "POPUP_FRAME">{$smarty.const._INSTALLTHEME}</a>
   </span>
  </div>
  {/if}
  <table style="width: 100%" class="sortedTable" id = "themesTable">
   <tr class="defaultRowHeight">
    <td class = "topTitle">{$smarty.const._NAME}</td>
    <td class = "topTitle">{$smarty.const._AUTHOR}</td>
    <td class = "topTitle">{$smarty.const._VERSION}</td>
    <td class = "topTitle centerAlign noSort">{$smarty.const._ACTIVETHEME}</td>
   {foreach name = 'browsers_list' item = "browser" key = "key" from = $T_BROWSERS}
    <td class = "topTitle centerAlign noSort"><img src = "images/file_types/{$key}.png" alt = "{$browser}" title = "{$browser}"></td>
   {/foreach}
    <td class = "topTitle centerAlign">{$smarty.const._OPERATIONS}</td>
   </tr>
   {foreach name = 'users_list' key = 'key' item = 'theme' from = $T_THEMES}
   <tr class="{cycle name = "themes" values = "oddRowColor, evenRowColor"}">
    <td>{$theme.name}</td>
    <td>{$theme.author}</td>
    <td>{$theme.version}</td>
    <td class = "centerAlign currentTheme">
   {if $_change_}
    {if $theme.id == $T_CURRENT_THEME->themes.id}
     <img class = "ajaxHandle" src = "images/16x16/pin_green.png" alt = "{$smarty.const._ACTIVETHEME}" title = "{$smarty.const._ACTIVETHEME}" onclick = "useTheme(this, '{$theme.id}')">
    {else}
     <img class = "ajaxHandle" src = "images/16x16/pin_red.png" alt = "{$smarty.const._USETHEME}" title = "{$smarty.const._USETHEME}" onclick = "useTheme(this, '{$theme.id}')">
    {/if}
   {else}
    {if $theme.id == $T_CURRENT_THEME->themes.id}
     <img class = "ajaxHandle" src = "images/16x16/success.png" alt = "{$smarty.const._ACTIVETHEME}" title = "{$smarty.const._ACTIVETHEME}">
    {/if}
   {/if}
    </td>
   {foreach name = 'browsers_list' item = "browser" key = "key" from = $T_BROWSERS}
    <td class = "centerAlign {$key}">
   {if $_change_}
    {if $theme.options.browsers[$key]}
     <img class = "ajaxHandle browser_{$key}" src = "images/16x16/pin_green.png" alt = "{$smarty.const._ACTIVETHEMEBROWSER}" title = "{$smarty.const._ACTIVETHEMEBROWSER}" onclick = "setBrowser(this, '{$theme.id}', '{$key}')"/>
    {else}
     <img class = "ajaxHandle browser_{$key}" src = "images/16x16/pin_red.png" alt = "{$smarty.const._USETHEMEBROWSER}" title = "{$smarty.const._USETHEMEBROWSER}" onclick = "setBrowser(this, '{$theme.id}', '{$key}')"/>
    {/if}
   {else}
    {if $theme.options.browsers[$key]}
     <img class = "ajaxHandle" src = "images/16x16/success.png" alt = "{$smarty.const._ACTIVETHEMEBROWSER}" title = "{$smarty.const._ACTIVETHEMEBROWSER}" />
    {/if}
   {/if}
    </td>
   {/foreach}
    <td class = "centerAlign">
     <div style = "display:none" id = "theme_settings_{$theme.name}">{foreach key = "name" item = "setting" from = $theme.settings}{$name}:&nbsp;{$setting}<br/>{/foreach}</div>
     <img class = "ajaxHandle" src = "images/16x16/search.png" title = "{$smarty.const._PREVIEW}" alt = "{$smarty.const._PREVIEW}" onclick = "window.open('index.php?preview_theme={$theme.id}', 'preview_theme')" />
   {if $_change_}
    {if !$theme.remote && !$theme.options.locked}
     <img class = "ajaxHandle" src = "images/16x16/export.png" alt = "{$smarty.const._EXPORTTHEME}" title = "{$smarty.const._EXPORTTHEME}" onclick = "exportTheme(this, '{$theme.id}')">
    {/if}
    {if $theme.name == $smarty.const.G_CURRENTTHEME}
     <img class = "ajaxHandle" src = "images/16x16/undo.png" title = "{$smarty.const._RESETTHEME}" alt = "{$smarty.const._RESETTHEME}" onclick = "resetTheme(this, '{$theme.id}')" />
    {/if}
    {if $theme.name != 'default' && $theme.name != $smarty.const.G_CURRENTTHEME}<img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "if (confirm ('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) deleteEntity(this, '{$theme.id}')" />{/if}
   {/if}
    </td>
   </tr>
   {/foreach}
  </table>
  {/capture}
  {eF_template_printBlock title=$smarty.const._THEMESELECTION data=$smarty.capture.t_themes_code image='32x32/themes.png'}
 {/capture}
 {capture name = "t_theme_divs_code"}
 <div class = "tabber">
        <div class="tabbertab {if (isset($smarty.get.tab) &&  $smarty.get.tab == 'layout')} tabbertabdefault{/if}" title = "{$smarty.const._LAYOUT}">{$smarty.capture.t_layout_tab}</div>
        <div class="tabbertab {if (isset($smarty.get.tab) &&  $smarty.get.tab == 'set_theme')} tabbertabdefault{/if}" title = "{$smarty.const._CHANGETHEME}">{$smarty.capture.t_change_theme_tab}</div>
 </div>
 {/capture}
 {eF_template_printBlock title = $smarty.const._THEMES data = $smarty.capture.t_theme_divs_code image = '32x32/themes.png' help='Themes' options = $T_APPEARANCE_LINK}
{/if}
