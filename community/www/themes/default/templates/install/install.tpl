{include file = "includes/header.tpl"}

{assign var = "path_title" value = "<a href = '`$smarty.server.PHP_SELF`'>Start</a>"}
{if $smarty.get.upgrade}{assign var = "upgrade" value = "&upgrade=1"}{/if}
{capture name = "center_code"}
    {if $T_MESSAGE}
      {eF_template_printMessageBlock content = $T_MESSAGE type = $T_MESSAGE_TYPE}
    {/if}

 {if $smarty.get.step == 1}
     {if $T_CONFIGURATION_EXISTS && !$smarty.get.upgrade && $smarty.get.step == 1}
      {eF_template_printMessageBlock content = "An existing configuration file was found, which probably means an existing installation is already in place. If you continue, it will be overwritten. Perhaps you prefer to <a href = '`$smarty.server.PHP_SELF`?step=`$smarty.get.step`&upgrade=1'>upgrade</a>?" type = "failure"}
      {/if}
  {capture name = 'step_1_code'}
  {assign var = "path_title" value = "`$path_title`&nbsp;&raquo;&nbsp;<a href = '`$smarty.server.PHP_SELF`?step=1`$upgrade`'>Step 1/2</a>"}
  <div class = "headerTools">
   <span>
    <img src = "images/16x16/php.png" alt = "phpinfo" title = "phpinfo"></img>
    <a href = "install/phpinfo.php" target = "new">Check current PHP settings</a>
   </span>
   <span>
    <img src = "images/16x16/flag_{if $smarty.session.error_level == 'warning'}yellow{elseif $smarty.session.error_level == 'all'}red{else}green{/if}.png" alt = "error reporting level" title = "error reporting level"></img>
    <a href = "javascript:void(0)" onclick = "setErrorReporting(this)" title = "error reporting level">Error reporting: {if $smarty.session.error_level == 'warning'}Warning{elseif $smarty.session.error_level == 'all'}All{else}Off{/if}</a>
   </span>
  </div>
  {include file = "includes/check_status.tpl"}

  <div style = "text-align:right"><input type = "submit" name = "next_step" value = "Continue &raquo;" onclick = "{if $T_MISSING_SETTINGS}if (confirm('Some mandatory elements were not found. Are you sure you want to continue?')){/if}window.location = '{$smarty.server.PHP_SELF}?step=2{if $smarty.get.upgrade}&upgrade=1{/if}'" class = "flatButton {if $T_MISSING_SETTINGS}inactiveElement{/if}"></div>
  {/capture}
  {eF_template_printBlock title = 'Efront Installation Wizard' content = $smarty.capture.step_1_code }
  <script>
  {literal}
  function setErrorReporting(el) {
   el.previous().src = 'themes/default/images/others/progress1.gif';
   parameters = {set_error_level: 1, method: 'get'};
   var url = window.location.toString();
   ajaxRequest(el, url, parameters, onSetErrorReporting);
  }
  function onSetErrorReporting(el, response) {
   switch (response) {
   case 'warning':
    el.previous().src = 'images/16x16/flag_yellow.png';
    el.update('Error reporting: Warnings');
    break;
   case 'all':
    el.previous().src = 'images/16x16/flag_red.png';
    el.update('Error reporting: All');
    break;
   default:
    el.previous().src = 'images/16x16/flag_green.png';
    el.update('Error reporting: Off');
    break;
   }
  }
  {/literal}
  </script>
 {elseif $smarty.get.step==2}
   {assign var = "path_title" value = "`$path_title`&nbsp;&raquo;&nbsp;<a href = '`$smarty.server.PHP_SELF`?step=1`$upgrade`'>Step 1/2</a>&nbsp;&raquo;&nbsp;<a href = '`$smarty.server.PHP_SELF`?step=2`$upgrade`'>Step 2/2</a>"}

  {capture name = 'step_2_code'}
            {$T_DATABASE_FORM.javascript}
            <form {$T_DATABASE_FORM.attributes}>
                {$T_DATABASE_FORM.hidden}
{*
                <div class = "formRow" tyle = "display:none">
                 <div class = "formLabel">
                  <div class = "header">Database type:&nbsp;</div>
                 </div>
                 <div class = "formElement">
                  <div class = "field">{$T_DATABASE_FORM.db_type.html}</div>
                  {if $T_DATABASE_FORM.db_type.error}<div class = "error">{$T_DATABASE_FORM.db_type.error}</div>{/if}
                 </div>
                </div>
*}
                <div class = "formRow">
                 <div class = "formLabel">
                  <div class = "header">Database host:&nbsp;</div>
                 </div>
                 <div class = "formElement">
                  <div class = "field">{$T_DATABASE_FORM.db_host.html}</div>
                  {if $T_DATABASE_FORM.db_host.error}<div class = "error">{$T_DATABASE_FORM.db_host.error}</div>{/if}
                 </div>
                </div>
                <div class = "formRow">
                 <div class = "formLabel">
                  <div class = "header">Database user:&nbsp;</div>
                 </div>
                 <div class = "formElement">
                  <div class = "field">{$T_DATABASE_FORM.db_user.html}</div>
                  {if $T_DATABASE_FORM.db_user.error}<div class = "error">{$T_DATABASE_FORM.db_user.error}</div>{/if}
                 </div>
                </div>
                <div class = "formRow">
                 <div class = "formLabel">
                  <div class = "header">Database password:&nbsp;</div>
                 </div>
                 <div class = "formElement">
                  <div class = "field">{$T_DATABASE_FORM.db_password.html}</div>
                  {if $T_DATABASE_FORM.db_password.error}<div class = "error">{$T_DATABASE_FORM.db_password.error}</div>{/if}
                 </div>
                </div>
                <div class = "formRow">
                 <div class = "formLabel">
                  <div class = "header">Database name:&nbsp;</div>
 {if $smarty.get.upgrade}
                  <div class = "explanation">Upgrading to a different database is safer (leaving old data intact) but may be very slow</div>
 {/if}
                 </div>
                 <div class = "formElement">
                  <div class = "field">{$T_DATABASE_FORM.db_name.html}</div>
                  {if $T_DATABASE_FORM.db_name.error}<div class = "error">{$T_DATABASE_FORM.db_name.error}</div>{/if}
                 </div>
                </div>
                <div class = "formRow" style = "display:none">
                 <div class = "formLabel">
                  <div class = "header">Database prefix:&nbsp;</div>
                 </div>
                 <div class = "formElement">
                  <div class = "field">{$T_DATABASE_FORM.db_prefix.html}</div>
                  {if $T_DATABASE_FORM.db_prefix.error}<div class = "error">{$T_DATABASE_FORM.db_prefix.error}</div>{/if}
                 </div>
                </div>
 {if !$smarty.get.upgrade}
                <div class = "formRow">
                 <div class = "formLabel">
                  <div class = "header">Administrator username:&nbsp;</div>
                  <div class = "explanation">{$smarty.const._ONLYALLOWEDCHARACTERSLOGIN}</div>
                 </div>
                 <div class = "formElement">
                  <div class = "field">{$T_DATABASE_FORM.admin_name.html}</div>
                  {if $T_DATABASE_FORM.admin_name.error}<div class = "error">{$T_DATABASE_FORM.admin_name.error}</div>{/if}
                 </div>
                </div>
                <div class = "formRow">
                 <div class = "formLabel">
                  <div class = "header">Administrator password:&nbsp;</div>
                 </div>
                 <div class = "formElement">
                  <div class = "field">{$T_DATABASE_FORM.admin_password.html}</div>
                  {if $T_DATABASE_FORM.admin_password.error}<div class = "error">{$T_DATABASE_FORM.admin_password.error}</div>{/if}
                 </div>
                </div>
                <div class = "formRow">
                 <div class = "formLabel">
                  <div class = "header">Administrator email:&nbsp;</div>
                 </div>
                 <div class = "formElement">
                  <div class = "field">{$T_DATABASE_FORM.admin_email.html}</div>
                  {if $T_DATABASE_FORM.admin_email.error}<div class = "error">{$T_DATABASE_FORM.admin_email.error}</div>{/if}
                 </div>
                </div>
                <div class = "formRow">
                 <div class = "formLabel">
                  <div class = "header"><span style = "vertical-align:middle">Create default lessons and users:&nbsp;</span>{$T_DATABASE_FORM.default_data.html}</div>
                 </div>
                </div>
 {else}
                <div class = "formRow">
                 <div class = "formLabel">
                  <div class = "header">Upgrade from database:&nbsp;</div>
                 </div>
                 <div class = "formElement">
                  <div class = "field">{$T_DATABASE_FORM.old_db_name.html}</div>
                  {if $T_DATABASE_FORM.old_db_name.error}<div class = "error">{$T_DATABASE_FORM.old_db_name.error}</div>{/if}
                 </div>
                </div>
{*
                <div class = "formRow">
                 <div class = "formLabel">
                  <div class = "header"><span style = "vertical-align:middle">Upgrade search table:&nbsp;</span>{$T_DATABASE_FORM.upgrade_search.html}</div>
                  <div class = "explanation">If you leave this unchecked, the upgrade will be much faster, but the search table must be rebuilt from the administrator's "maintenance" option</div>
                 </div>
                </div>
*}
                <div class = "formRow">
                 <div class = "formLabel">
                  <div class = "header"><span style = "vertical-align:middle">Backup database:&nbsp;</span>{$T_DATABASE_FORM.backup.html}</div>
                 </div>
                </div>
 {/if}
                <div class = "formRow">
                 <div class = "formLabel">
                  <div class = "header">&nbsp;</div>
                 </div>
                 <div class = "formElement">
                  <div>{$T_DATABASE_FORM.submit_form.html} {if $T_FAILED_TABLES}{$T_DATABASE_FORM.delete_form.html}{/if}</div>
                 </div>
                </div>
                <div>&nbsp;</div>
                <div class = "formRequired">{$T_DATABASE_FORM.requirednote}</div>
            </form>
{*
                <table class = "formElements">
                    <tr><td class = "labelCell">Database user:&nbsp;</td>
                     <td class = "elementCell">{$T_DATABASE_FORM.db_user.html}</td></tr>
                    {if $T_DATABASE_FORM.db_user.error}<tr><td></td><td class = "formError">{$T_DATABASE_FORM.db_user.error}</td></tr>{/if}
                    <tr><td class = "labelCell">Database password:&nbsp;</td>
                     <td class = "elementCell">{$T_DATABASE_FORM.db_password.html}</td></tr>
                    {if $T_DATABASE_FORM.db_password.error}<tr><td></td><td class = "formError">{$T_DATABASE_FORM.db_password.error}</td></tr>{/if}
                    <tr><td class = "labelCell">Database name:&nbsp;</td>
                     <td class = "elementCell">{$T_DATABASE_FORM.db_name.html}</td></tr>
                    {if $T_DATABASE_FORM.db_name.error}<tr><td></td><td class = "formError">{$T_DATABASE_FORM.db_name.error}</td></tr>{/if}
                    <tr style = "display:none"><td class = "labelCell">Database tables prefix:&nbsp;</td>
                     <td class = "elementCell">{$T_DATABASE_FORM.db_prefix.html}</td></tr>
                    {if $T_DATABASE_FORM.db_prefix.error}<tr><td></td><td class = "formError">{$T_DATABASE_FORM.db_prefix.error}</td></tr>{/if}
                    <tr><td colspan = "2">&nbsp;</td></tr>
                    <tr><td class = "labelCell">Administrator username:&nbsp;</td>
                     <td class = "elementCell">{$T_DATABASE_FORM.admin_name.html}</td></tr>
                    {if $T_DATABASE_FORM.admin_name.error}<tr><td></td><td class = "formError">{$T_DATABASE_FORM.admin_name.error}</td></tr>{/if}
                    <tr><td class = "labelCell">Administrator password:&nbsp;</td>
                     <td class = "elementCell">{$T_DATABASE_FORM.admin_password.html}</td></tr>
                    {if $T_DATABASE_FORM.admin_password.error}<tr><td></td><td class = "formError">{$T_DATABASE_FORM.admin_password.error}</td></tr>{/if}
                    <tr><td class = "labelCell">Administrator email:&nbsp;</td>
                     <td class = "elementCell">{$T_DATABASE_FORM.admin_email.html}</td></tr>
                    {if $T_DATABASE_FORM.admin_email.error}<tr><td></td><td class = "formError">{$T_DATABASE_FORM.admin_email.error}</td></tr>{/if}
                    <tr><td colspan = "2">&nbsp;</td></tr>
                    <tr><td></td>
                     <td class = "elementCell">{$T_DATABASE_FORM.submit_form.html} {if $T_FAILED_TABLES}{$T_DATABASE_FORM.delete_form.html}{/if}</td></tr>
                    <tr><td colspan = "2">&nbsp;</td></tr>
                    <tr><td></td>
                     <td class = "formRequired">{$T_DATABASE_FORM.requirednote}</td></tr>
                </table>
*}
  {/capture}
  {eF_template_printBlock title = 'Efront Installation Wizard' content = $smarty.capture.step_2_code }
 {elseif $smarty.get.restore}
  {assign var = "path_title" value = "`$path_title`&nbsp;&raquo;&nbsp;<a href = '`$smarty.server.PHP_SELF`?restore=1'>Emergency restore</a>"}
  <table style = "width:100%">
   <tr class = "topTitle">
    <td>Name</td>
    <td class = "centerAlign">Operations</td></tr>
  {foreach name = 'backups_list' item = "file" key = "key" from = $T_BACKUP_FILES}
   <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"}">
    <td>{$file}</td>
    <td class = "centerAlign"><img class = "ajaxHandle" src = "images/16x16/undo.png" alt = "Restore" title = "Restore" onclick = "if (confirm('This operation is irreversible! Are you sure?')) location = location+'&file={$file}';"></td>
   </tr>
  {foreachelse}
   <tr class = "oddRowColor defaultRowHeight"><td class = "emptyCategory" colspan="2">No data found</td></tr>
  {/foreach}
  </table>
 {elseif $smarty.get.finish}
  {capture name = 'finish_code'}
  {assign var = "path_title" value = "`$path_title`&nbsp;&raquo;&nbsp;<a href = '`$smarty.server.PHP_SELF`?step=1`$upgrade`'>Step 1/2</a>&nbsp;&raquo;&nbsp;<a href = '`$smarty.server.PHP_SELF`?step=2`$upgrade`'>Step 2/2</a>&nbsp;&raquo;&nbsp;<a href = '`$smarty.server.PHP_SELF`?finish=1`$upgrade`'>Finish</a>"}
  <div style = "text-align:center;font-size:14px;">
   <p>Congratulations!</p>
   <p><img src = "images/others/success.png" alt = "Finished" title = "Finished Installation"></p>
   <p>eFront installed succesfully!</p>
   <p>Click <a href = "index.php?delete_install=1" style = "font-weight:bold;text-decoration:underline">here</a> to delete the installation directory automatically and navigate to the system <a href = "index.php">index page</a>.</p>
  </div>
  {/capture}
  {eF_template_printBlock title = 'Efront Installation Wizard' content = $smarty.capture.finish_code }
 {else}
  {capture name = 'start_code'}
  <div style = "text-align:center;font-size:14px;">
   <p>Welcome to eFront's installation wizard! Click on a button below to start.</p>
   <table style = "margin:auto;">
    <tr><td style = "padding:20px 10px 0px 10px"><img src = "images/others/start.png" alt = "Install" title = "Start installation wizard" style = "cursor:pointer;" onclick = "window.location='{$smarty.server.PHP_SELF}?step=1'"></td>
    {if $T_CONFIGURATION_EXISTS}
     <td style = "padding:20px 10px 0px 10px"><img src = "images/others/upgrade.png" alt = "Upgrade" title = "Start upgrade wizard" style = "cursor:pointer;" onclick = "window.location='{$smarty.server.PHP_SELF}?step=1&upgrade=1'"></td>
    {/if}
    </tr>
    <tr><td style = "padding:0px 10px 20px 10px">New installation</td>
    {if $T_CONFIGURATION_EXISTS}
     <td style = "padding:0px 10px 20px 10px;">Upgrade existing installation</td>
    {/if}
    </tr>
   </table>
   <p>Need help? Check the <a style = "color:blue" target = "new" href = "http://docs.efrontlearning.net/">documentation</a> or ask the <a style = "color:blue" target = "new" href = "http://forum.efrontlearning.net/">support forums</a>!</p>
  </div>
  {/capture}
  {eF_template_printBlock title = 'Efront Installation Wizard' content = $smarty.capture.start_code options = $T_INSTALLATION_OPTIONS}
 {/if}
{/capture}

{capture name = "header_code"}
  <div id = "logo">
   <a href = "index.php"><img src = "themes/modern/images/logo/logo.png" title = "eFront" alt = "eFront" border = "0"></a>
  </div>
  <div id = "path">
   <div id = "path_title">{$path_title}</div>
   <div id = "path_language"></div>
  </div>
{/capture}
{capture name = "footer_code"}
  <a href = "www.efrontlearning.net">eFront</a> version {$smarty.const.G_VERSION_NUM} build {$smarty.const.G_BUILD} &bull; {$T_VERSION_TYPE} Edition
{/capture}

<table class = "pageLayout simple">
 <tr><td class = "header" colspan = "3">{$smarty.capture.header_code}</td></tr>
 <tr><td class = "left"></td>
  <td class = "center" style = "vertical-align:top;padding-top:50px">{$smarty.capture.center_code}</td>
  <td class = "right"></td></tr>
 <tr><td class = "footer" colspan = "3">{$smarty.capture.footer_code}</td></tr>
</table>

<div id = "error_details" style = "display:none"><pre>{$T_EXCEPTION_TRACE}</pre></div>
<script>if (document.getElementById('dimmer') && document.getElementById('dimmer').style.display != 'none') eF_js_showDivPopup();</script>
<table id = "popup_table" class = "divPopup" style = "display:none;">
    <tr class = "defaultRowHeight">
        <td class = "topTitle" id = "popup_title"></td>
        <td class = "topTitle" style = "width:1%;"><img src = "images/16x16/close.png" alt = "Close" name = "" id = "popup_close" title = "Close" onclick = "eF_js_showDivPopup('', '', this.name)"/>
    </td></tr>
    <tr><td colspan = "2" id = "popup_data" style = "vertical-align:top;"></td></tr>
    <tr><td colspan = "2" id = "frame_data" style = "width:100%;height:100%">
        <iframe name = "POPUP_FRAME" id = "popup_frame" src = "about:blank" style = "border-width:0px;width:100%;height:100%;padding:0px 0px 0px 0px">Sorry, but your browser needs to support iframes to see this</iframe>
    </td></tr>
</table>

<div id="dimmer" class = "dimmerDiv" style="display:none;"></div>


<script type = "text/javascript" src = "js/scripts.php?load={$T_HEADER_LOAD_SCRIPTS}"> </script>
</body>
</html>
