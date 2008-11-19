{* Template file for install.php *}
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <base href = "{$T_SERVERNAME}" />
    <meta http-equiv = "Content-Language" content = "english" />
    <meta http-equiv = "keywords"         content = "education" />
    <meta http-equiv = "description"      content = "Collaborative Elearning Platform" />
    <meta http-equiv = "Content-Type"     content = "text/html; charset = utf-8"/>
    <meta http-equiv = "Cache-Control"    content = "no-cache" />
    <meta http-equiv = "Pragma"           content = "no-cache" />
    <meta http-equiv = "Expires"          content = "0" />
    <link rel = "shortcut icon" href = "images/favicon.ico" >
    <link rel = "icon" href = "images/favicon.ico" type = "image/ico" >
    <link rel = "stylesheet" type = "text/css" href = "css/css_global.css" />   

    <title>eFront Installation Wizard</title>
    <script type = "text/javascript" src = "js/scriptaculous/prototype.js" ></script>
    <script type = "text/javascript" src = "js/EfrontScripts.js" ></script>
    <script>
    <!--
    {literal}
    function eF_js_activateElements(name) {
        var collection = document.getElementsByTagName('input');
        for (var i = 0; i < collection.length; i++) {
            if (collection[i].name.match(name) && !collection[i].name.match('activate_'+name)) {
                if (collection[i].readOnly) {
                    collection[i].readOnly = false;
                    collection[i].style.color = 'black';
                    collection[i].parentNode.previousSibling.style.color = 'black';
                } else {
                    collection[i].readOnly = true;
                    collection[i].style.color = '#808080';
                    collection[i].parentNode.previousSibling.style.color = '#808080';
                }
            }
        }
        
        var collection = document.getElementsByTagName('select');
        for (var i = 0; i < collection.length; i++) {
            if (collection[i].name.match(name) && !collection[i].name.match('activate_'+name)) {
                if (collection[i].disabled) {
                    collection[i].disabled = false;
                    collection[i].style.color = 'black';
                    collection[i].parentNode.previousSibling.style.color = 'black';
                } else {
                    collection[i].disabled = true;
                    collection[i].style.color = '';
                    collection[i].parentNode.previousSibling.style.color = '#808080';
                }
            }
        }
        
    }

    {/literal}
    //-->
    </script>
</head>
<body id = "body_">

{if $smarty.get.step == 1}
    {assign var = "title" value = '<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'">eFront installation</a>&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?step=1">Step 1</a>'}
    {capture name = 'step1'}
                                <tr><td class = "moduleCell">
                                    <table width = "100%">
                                        <tr><td>
                                            <table style = "width:100%">                                        
                                                <tr><td colspan = "100%">&nbsp;</td></tr>
                                                <tr><td colspan = "100%" class = "horizontalSeparator" style = "color:#a0bdef;font-size:16px">Recommended Software</td></tr>
                                                <tr><td colspan = "100%"></td></tr>
                                                <tr class = "topTitle defaultRowHeight">
                                                    <td>Name</td>
                                                    <td>Installed Version</td>
                                                    <td>Recommended</td>
                                                    <td style = "text-align:center;width:10%">Status</td>
                                                    <td></td></tr>
                                    {foreach name = 'settings_list' key = key item = item from = $T_SOFTWARE}
                                                <tr class = "{cycle values = "oddRowColor,evenRowColor"} defaultRowHeight">
                                                    <td>{$item.name}</td>
                                                    <td>{$item.installed}</td>
                                                    <td>{$item.recommended}</td>
                                                    <td style = "text-align:center">
                                                    {if $item.status}
                                                        <img src = "images/16x16/check.png" alt = "OK" title = "OK" />
                                                    {else}
                                                        {if $item.name == 'PHP'}<img src = "images/16x16/forbidden.png" alt = "Missing" title = "Missing" />{else}<img src = "images/16x16/warning.png" alt = "Missing" title = "Missing" />{/if}
                                                    {/if}&nbsp;
                                                    </td>
                                                    <td style = "width:1%"><img src = "images/16x16/help2.png" alt = "help" title = "help" onclick = "eF_js_showHideDiv(this, '{$key}', event)"><div id = '{$key}' onclick = "eF_js_showHideDiv(this, '{$key}', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">{$item.help}</div></td></tr>
                                    {/foreach}
                                            </table>

                                            <table style = "width:100%">                                        
                                                <tr><td colspan = "100%">&nbsp;</td></tr>
                                                <tr><td colspan = "100%" class = "horizontalSeparator" style = "color:#a0bdef;font-size:16px">Mandatory PHP extesions</td></tr>
                                                <tr><td colspan = "100%"></td></tr>
                                                <tr class = "topTitle defaultRowHeight">
                                                    <td>Name</td>
                                                    <td style = "text-align:center;width:10%">Status</td>
                                                    <td></td></tr>
                                    {foreach name = 'mandatory_list' key = key item = item from = $T_MANDATORY}
                                                <tr class = "{cycle values = "oddRowColor,evenRowColor"} defaultRowHeight">
                                                    <td>{$item.name}</td>
                                                    <td style = "text-align:center">{if $item.enabled}<img src = "images/16x16/check.png" alt = "OK" title = "OK" />{else}<img src = "images/16x16/forbidden.png" alt = "Missing" title = "Missing" />{/if}</td>
                                                    <td style = "width:1%"><img src = "images/16x16/help2.png" alt = "help" title = "help" onclick = "eF_js_showHideDiv(this, '{$key}', event)"><div id = '{$key}' onclick = "eF_js_showHideDiv(this, '{$key}', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">{$item.help}</div></td></tr>
                                    {/foreach}
                                            </table>

                                            <table style = "width:100%">                                        
                                                <tr><td colspan = "100%">&nbsp;</td></tr>
                                                <tr><td colspan = "100%" class = "horizontalSeparator" style = "color:#a0bdef;font-size:16px">Optional PHP extesions</td></tr>
                                                <tr><td colspan = "100%"></td></tr>
                                                <tr class = "topTitle defaultRowHeight">
                                                    <td>Name</td>
                                                    <td style = "text-align:center;width:10%">Status</td>
                                                    <td></td></tr>
                                    {foreach name = 'optional_list' key = key item = item from = $T_OPTIONAL}
                                                <tr class = "{cycle values = "oddRowColor,evenRowColor"} defaultRowHeight">
                                                    <td>{$item.name}</td>
                                                    <td style = "text-align:center">{if $item.enabled}<img src = "images/16x16/check.png" alt = "OK" title = "OK" />{else}<img src = "images/16x16/warning.png" alt = "Missing" title = "Missing" />{/if}</td>
                                                    <td style = "width:1%"><img src = "images/16x16/help2.png" alt = "help" title = "help" onclick = "eF_js_showHideDiv(this, '{$key}', event)"><div id = '{$key}' onclick = "eF_js_showHideDiv(this, '{$key}', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">{$item.help}</div></td></tr>
                                    {/foreach}
                                            </table>

                                            <table style = "width:100%">                                        
                                                <tr><td colspan = "100%">&nbsp;</td></tr>
                                                <tr><td colspan = "100%" class = "horizontalSeparator" style = "color:#a0bdef;font-size:16px">Recommended PHP Settings</td></tr>
                                                <tr><td colspan = "100%"></td></tr>
                                                <tr class = "topTitle defaultRowHeight">
                                                    <td>Name</td>
                                                    <td>Value</td>
                                                    <td>Recommended</td>
                                                    <td style = "text-align:center;width:10%">Status</td>
                                                    <td></td></tr>
                                    {foreach name = 'settings_list' key = key item = item from = $T_SETTINGS}
                                                <tr class = "{cycle values = "oddRowColor,evenRowColor"} defaultRowHeight">
                                                    <td>{$item.name}</td>
                                                    <td>{$item.value}</td>
                                                    <td>{$item.recommended}</td>
                                                    <td style = "text-align:center">{if $item.status}<img src = "images/16x16/check.png" alt = "OK" title = "OK" />{else}<img src = "images/16x16/{if $item.name == 'memory_limit'}forbidden{else}warning{/if}.png" alt = "Missing" title = "Missing" />{/if}</td>
                                                    <td style = "width:1%"><img src = "images/16x16/help2.png" alt = "help" title = "help" onclick = "eF_js_showHideDiv(this, '{$key}', event)"><div id = '{$key}' onclick = "eF_js_showHideDiv(this, '{$key}', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">{$item.help}</div></td></tr>
                                    {/foreach}
                                            </table>

                                            <table style = "width:100%">                                        
                                                <tr><td colspan = "100%">&nbsp;</td></tr>
                                                <tr><td colspan = "100%" class = "horizontalSeparator" style = "color:#a0bdef;font-size:16px">Filesystem Permissions</td></tr>
                                                <tr><td colspan = "100%"></td></tr>
                                                <tr class = "topTitle defaultRowHeight">
                                                    <td>Directory</td>
                                                    <td>Writable</td>
                                                    <td style = "text-align:center;width:10%">Status</td>
                                                    <td></td></tr>
                                    {foreach name = 'settings_list' key = key item = item from = $T_PERMISSIONS}
                                                <tr class = "{cycle values = "oddRowColor,evenRowColor"} defaultRowHeight">
                                                    <td>{$key}</td>
                                                    <td>{if $item.writable}YES{else}NO{/if}</td>
                                                    <td style = "text-align:center">{if $item.writable}<img src = "images/16x16/check.png" alt = "OK" title = "OK" />{else}<img src = "images/16x16/forbidden.png" alt = "Missing" title = "Missing" />{/if}</td>
                                                    <td style = "width:1%"><img src = "images/16x16/help2.png" alt = "help" title = "help" onclick = "eF_js_showHideDiv(this, '{$key}', event)"><div id = '{$key}' onclick = "eF_js_showHideDiv(this, '{$key}', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">{$item.help}</div></td></tr>
                                    {/foreach}
                                            </table>

                                            <table style = "width:100%">                                        
                                                <tr><td colspan = "100%">&nbsp;</td></tr>
                                                <tr><td colspan = "100%" class = "horizontalSeparator" style = "color:#a0bdef;font-size:16px">PEAR compatibility</td></tr>
                                                <tr><td colspan = "100%"></td></tr>
                                                <tr class = "topTitle defaultRowHeight">
                                                    <td>Package</td>
                                                    <td style = "text-align:center;width:10%">Status</td>
                                                    <td></td></tr>
                                    {foreach name = 'settings_list' key = key item = item from = $T_PEAR}
                                                <tr class = "{cycle values = "oddRowColor,evenRowColor"} defaultRowHeight">
                                                    <td>{$key}</td>
                                                    <td style = "text-align:center">{if $item.exists}<img src = "images/16x16/check.png" alt = "OK" title = "OK" />{else}<img src = "images/16x16/forbidden.png" alt = "Missing" title = "Missing" />{/if}</td>
                                                    <td style = "width:1%"><img src = "images/16x16/help2.png" alt = "help" title = "help" onclick = "eF_js_showHideDiv(this, '{$key}', event)"><div id = '{$key}' onclick = "eF_js_showHideDiv(this, '{$key}', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">{$item.help}</div></td></tr>
                                    {/foreach}
                                            </table>

                                            <table style = "width:100%">                                        
                                                <tr><td colspan = "100%">&nbsp;</td></tr>
                                                <tr><td colspan = "100%" class = "horizontalSeparator" style = "color:#a0bdef;font-size:16px">Language compatibility</td></tr>
                                                <tr><td colspan = "100%"></td></tr>
                                                <tr class = "topTitle defaultRowHeight">
                                                    <td>Language</td>
                                                    <td>Installed Locale</td>
                                                    <td style = "text-align:center;width:10%">Status</td>
                                                    <td></td></tr>
                                    {foreach name = 'settings_list' key = key item = item from = $T_LOCALE}
                                                <tr class = "{cycle values = "oddRowColor,evenRowColor"} defaultRowHeight">
                                                    <td>{$item.language}</td>
                                                    <td>{$item.locale}</td>
                                                    <td style = "text-align:center">{if $item.locale}<img src = "images/16x16/check.png" alt = "OK" title = "OK" />{else}<img src = "images/16x16/warning.png" alt = "Missing" title = "Missing" />{/if}</td>
                                                    <td style = "width:1%"><img src = "images/16x16/help2.png" alt = "help" title = "help" onclick = "eF_js_showHideDiv(this, '{$key}', event)"><div id = '{$key}' onclick = "eF_js_showHideDiv(this, '{$key}', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">{$item.help}</div></td></tr>
                                    {/foreach}
                                                <tr><td>&nbsp;</td></tr>
                                            </table>

                                            <table align = "right">
                                    {if $T_CONFIGURATION_EXISTS}
                                                {assign var = "notice_message" value = "Notice: An existing configuration.php was detected.<br/><br/>"}
                                                {assign var = "T_MESSAGE" value = "Notice: An existing configuration.php was detected. If you continue, it will be overwritten.<br/>"}
                                                
                                    {/if}
                                    {if $T_NON_EMPTY_FOLDERS}
                                                 {assign var = "T_MESSAGE" value = $T_MESSAGE|cat:'Notice: Installation has detected that lessons folder (www/content/lessons/) and users folder (upload/) are not empty. This can cause problems during system operation.<br/>'}
                                                 {assign var = "notice_message" value = $notice_message|cat:"Notice: Lessons folder and users folder are not empty."}
                                    {/if}
                                                <tr><td style = "text-align:center;padding:1em 2em 1em 2em">
                                    {if $T_INSTALL}
                                                    <a href = "{$smarty.server.PHP_SELF}?step=2{if $smarty.get.upgrade}&upgrade=1{/if}{if $smarty.get.migrate}&migrate=1{/if}" title = "Next"><img src = "images/32x32/navigate_right2.png" border = "0" alt = "Next" title = "Next"/><br/>Next</a>
                                    {else}
                                                    {assign var = "error_message" value = "Error: You should not continue since there are mandatory elements not present."}
                                                    <a href = "{$smarty.server.PHP_SELF}?step=2{if $smarty.get.upgrade}&upgrade=1{/if}{if $smarty.get.migrate}&migrate=1{/if}" title = "Should not continue" onclick = "return confirm('You should not continue since there are mandatory elements not present. Are you sure you want to continue without them? The installation will probably not complete.')"><img src = "images/32x32/navigate_right2_gray.png" border = "0" alt = "Should not ontinue" title = "Should not continue"/><br/>Should not continue!</a>
                                                    
                                    {/if}
                                                </td></tr>
                                            </table>
                                        
                                        </td><td>
                                        
                                        
                                        </td></tr>
                                    </table>
                                        
                                </td></tr>
    {/capture}
{elseif $smarty.get.step == 2}

    {capture name = 'step2'}
                                <tr><td class = "moduleCell">
                                        {$T_DATABASE_FORM.javascript}
                                        <form {$T_DATABASE_FORM.attributes}>
                                            {$T_DATABASE_FORM.hidden}
                                            <table class = "formElements" width = "99%">
                                                <tr><td colspan = "100%">&nbsp;</td></tr>
                                                <tr><td colspan = "100%" class = "horizontalSeparator" style = "color:#a0bdef;font-size:16px">Database settings</td></tr>
                                                <tr><td colspan = "100%"></td></tr>
                                                <tr><td class = "labelCell">Database type:&nbsp;</td><td>{$T_DATABASE_FORM.db_type.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'db_type_help', event)"/><div id = 'db_type_help' onclick = "eF_js_showHideDiv(this, 'db_type_help', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">The database type can only be MySQL</div></td></tr>
                                                {if $T_DATABASE_FORM.db_type.error}<tr><td></td><td class = "formError">{$T_DATABASE_FORM.db_type.error}</td></tr>{/if}
                                                <tr><td class = "labelCell">Database host:&nbsp;</td><td>{$T_DATABASE_FORM.db_host.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'db_host_help', event)"/><div id = 'db_host_help' onclick = "eF_js_showHideDiv(this, 'db_host_help', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">Enter the host address, where the database is installed, usually "localhost" or "." (without the quotes)</div></td></tr>
                                                {if $T_DATABASE_FORM.db_host.error}<tr><td></td><td class = "formError">{$T_DATABASE_FORM.db_host.error}</td></tr>{/if}
                                                <tr><td class = "labelCell">Database user:&nbsp;</td><td>{$T_DATABASE_FORM.db_user.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'db_user_help', event)"/><div id = 'db_user_help' onclick = "eF_js_showHideDiv(this, 'db_user_help', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">Enter the user that has access to the database</div></td></tr>
                                                {if $T_DATABASE_FORM.db_user.error}<tr><td></td><td class = "formError">{$T_DATABASE_FORM.db_user.error}</td></tr>{/if}
                                                <tr><td class = "labelCell">Database password:&nbsp;</td><td>{$T_DATABASE_FORM.db_password.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'db_password_help', event)"/><div id = 'db_password_help' onclick = "eF_js_showHideDiv(this, 'db_password_help', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">Enter the password for the database user. It can be left blank, if no password is set</div></td></tr>
                                                {if $T_DATABASE_FORM.db_password.error}<tr><td></td><td class = "formError">{$T_DATABASE_FORM.db_password.error}</td></tr>{/if}
                                                <tr><td class = "labelCell">Database name:&nbsp;</td><td>{$T_DATABASE_FORM.db_name.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'db_name_help', event)"/><div id = 'db_name_help' onclick = "eF_js_showHideDiv(this, 'db_name_help', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">Enter the database name. If the database does not exist, then click the "Create database" link to create it. The database user must have the corresponding privileges</div></td></tr>
                                                {if $T_DATABASE_FORM.db_name.error}<tr><td></td><td class = "formError">{$T_DATABASE_FORM.db_name.error}</td></tr>{/if}
                            {if $smarty.get.upgrade}                            
                                                <tr><td class = "labelCell">New Database name:&nbsp;</td><td>{$T_DATABASE_FORM.new_db_name.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'new_db_name_help', event)"/><div id = 'new_db_name_help' onclick = "eF_js_showHideDiv(this, 'new_db_name_help', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">Enter the new database name, if you want the data to be copied from the old database to a new one (thus keeping the old data intact). If the new database does not exist, then click the "Create new database" link to create it. The database user must have the corresponding privileges</div></td></tr>
                                                <tr><td></td><td class = "infoCell">Leave blank, if you want to replace existing database</td></tr>
                                                {if $T_DATABASE_FORM.new_db_name.error}<tr><td></td><td class = "formError">{$T_DATABASE_FORM.new_db_name.error}</td></tr>{/if}
                            {elseif $smarty.get.migrate}
                                                <tr><td class = "labelCell">Source Directory&nbsp;</td><td>{$T_DATABASE_FORM.dir.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'dir_help', event)"/><div id = 'dir_help' onclick = "eF_js_showHideDiv(this, 'dir_help', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">Enter the directory path, where the source system is located, using slashes (/) and not backslashes (\). For example c:/efront or /var/www/efront </div></td></tr>
                                                {if $T_DATABASE_FORM.dir.error}<tr><td></td><td class = "formError">{$T_DATABASE_FORM.dir.error}</td></tr>{/if}

                                                <tr><td colspan = "100%">&nbsp;</td></tr>
                                                <tr><td colspan = "100%" class = "horizontalSeparator" style = "color:#a0bdef;font-size:16px">Target Database and Directory settings</td></tr>
                                                <tr><td colspan = "100%"></td></tr>
                                                <tr><td class = "labelCell">Target Database type&nbsp;</td><td>{$T_DATABASE_FORM.new_db_type.html}</td><td><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'new_db_type_help', event)"/><div id = 'new_db_type_help' onclick = "eF_js_showHideDiv(this, 'new_db_type_help', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">The database type can only be MySQL</div></td></tr>
                                                {if $T_DATABASE_FORM.new_db_type.error}<tr><td></td><td class = "formError">{$T_DATABASE_FORM.new_db_type.error}</td></tr>{/if}
                                                <tr><td class = "labelCell">Target Database host&nbsp;</td><td>{$T_DATABASE_FORM.new_db_host.html}</td><td><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'new_db_host_help', event)"/><div id = 'new_db_host_help' onclick = "eF_js_showHideDiv(this, 'new_db_host_help', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">Enter the host address, where the database is installed, usually "localhost" or "." (without the quotes)</div></td></tr>
                                                {if $T_DATABASE_FORM.new_db_host.error}<tr><td></td><td class = "formError">{$T_DATABASE_FORM.new_db_host.error}</td></tr>{/if}
                                                <tr><td class = "labelCell">Target Database user&nbsp;</td><td>{$T_DATABASE_FORM.new_db_user.html}</td><td><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'new_db_user_help', event)"/><div id = 'new_db_user_help' onclick = "eF_js_showHideDiv(this, 'new_db_user_help', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">Enter the user that has access to the database</div></td></tr>
                                                {if $T_DATABASE_FORM.new_db_user.error}<tr><td></td><td class = "formError">{$T_DATABASE_FORM.new_db_user.error}</td></tr>{/if}
                                                <tr><td class = "labelCell">Target Database password&nbsp;</td><td>{$T_DATABASE_FORM.new_db_password.html}</td><td><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'new_db_password_help', event)"/><div id = 'new_db_password_help' onclick = "eF_js_showHideDiv(this, 'new_db_password_help', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">Enter the password for the database user. It can be left blank, if no password is set</div></td></tr>
                                                {if $T_DATABASE_FORM.new_db_password.error}<tr><td></td><td class = "formError">{$T_DATABASE_FORM.new_db_password.error}</td></tr>{/if}
                                                <tr><td class = "labelCell">Target Database name&nbsp;</td><td>{$T_DATABASE_FORM.new_db_name.html}</td><td><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'new_db_name_help', event)"/><div id = 'new_db_name_help' onclick = "eF_js_showHideDiv(this, 'new_db_name_help', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">Enter the database name. </div></td></tr>
                                                {if $T_DATABASE_FORM.new_db_name.error}<tr><td></td><td class = "formError">{$T_DATABASE_FORM.new_db_name.error}</td></tr>{/if}
                                                <tr><td class = "labelCell">Target Directory&nbsp;</td><td>{$T_DATABASE_FORM.new_dir.html}</td><td><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'new_dir_help', event)"/><div id = 'new_dir_help' onclick = "eF_js_showHideDiv(this, 'new_dir_help', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">Enter the directory path, where the target system is located, using slashes (/) and not backslashes (\). For example c:/efront or /var/www/efront </div></td></tr>
                                                {if $T_DATABASE_FORM.new_dir.error}<tr><td></td><td class = "formError">{$T_DATABASE_FORM.new_dir.error}</td></tr>{/if}
                            {/if}
                                                <tr><td colspan = "100%">&nbsp;</td></tr>
                                                <tr><td></td><td class = "formRequired">{$T_DATABASE_FORM.requirednote}</td><td></td></tr>

                                                <tr><td colspan = "100%">&nbsp;</td></tr>
                                                <tr><td colspan = "100%">
                                                    <table align = "right"><tr>
                                                {if $T_ERROR}
                                                    {if !$UPGRADE_SINGLE_DB}
                                                        <td style = "white-space:nowrap; text-align:center;padding-right:2em"><a href = "#" title = "Delete database and try again" onclick = "document.step2_form.rollback.click();return false;"><img src = "images/32x32/data_delete.png" border = "0" title = "Delete database and try again" alt = "Delete database and try again"><br/>Delete database and try again</a></td>
                                                    {/if}
                                                    <td style = "white-space:nowrap; text-align:center"><a href = "#" title = "Continue anyway" onclick = "document.step2_form.step2_submit.click();return false;"><img src = "images/32x32/navigate_right2.png" border = "0" title = "Continue anyway" alt = "Continue anyway"><br/>Continue anyway</a></td>
                                                {else}
                                                    <td style = "white-space:nowrap; text-align:center"><a href = "#" title = "Next" onclick = "document.step2_form.create_tables.click();return false;"><img src = "images/32x32/navigate_right2.png" border = "0" title = "Next" alt = "Next"><br/>Next</a></td>
                                                {/if}
                                                    </tr></table>
                                                </td></tr>
                                                <div style = "display:none">
                                                    {$T_DATABASE_FORM.create_tables.html}
                                                    {$T_DATABASE_FORM.step2_submit.html}
                                                    {$T_DATABASE_FORM.rollback.html}
                                                </div>
                                            </table>
                                        </form>
                                </td></tr>
    {/capture}
{elseif $smarty.get.step == 3}

    {capture name = 'step3'}
                                <tr><td class = "moduleCell">


                                        {$T_DATABASE_FORM.javascript}                                    
                                        <form {$T_DATABASE_FORM.attributes}>
                                            {$T_DATABASE_FORM.hidden}
                                            <table class = "formElements" style = "margin-left:0px;width:100%">

                                                <tr><td colspan = "100%">&nbsp;</td></tr>
                                                <tr><td colspan = "100%" class = "horizontalSeparator" style = "color:#a0bdef;font-size:16px">Core Server Settings</td></tr>
                                                <tr><td colspan = "100%"></td></tr>
                                                <tr><td class = "labelCell">Default Language:&nbsp;</td><td>{$T_DATABASE_FORM.conf.default_language.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'db_type_help', event)"/><div id = 'db_type_help' onclick = "eF_js_showHideDiv(this, 'db_type_help', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">The system default language will be the one used for index page, as well as the default set during user registration</div></td></tr>
                                                <tr><td class = "labelCell">Server name:&nbsp;</td><td>http://{$smarty.server.HTTP_HOST}{$T_DATABASE_FORM.server_name.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'server_name_help', event)"/><div id = 'server_name_help' onclick = "eF_js_showHideDiv(this, 'server_name_help', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">The server name matches the current one, and is were users will access content from</div></td></tr>
                                                <tr><td class = "labelCell">Server port:&nbsp;</td><td>{$T_DATABASE_FORM.server_port.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'server_port', event)"/><div id = 'server_port' onclick = "eF_js_showHideDiv(this, 'server_port', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">The server port</div></td></tr>
                                                <tr><td class = "labelCell">Root path:&nbsp;</td><td>{$T_DATABASE_FORM.root_path.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'root_path', event)"/><div id = 'root_path' onclick = "eF_js_showHideDiv(this, 'root_path', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">The root path is the complete path where the software is located</div></td></tr>

                                                <tr><td colspan = "100%">&nbsp;</td></tr>
                                                <tr><td colspan = "100%" class = "horizontalSeparator" style = "color:#a0bdef;font-size:16px">Additional System Settings</td></tr>
                                                <tr><td colspan = "100%"></td></tr>
                                                <tr><td class = "labelCell">Automatic registration:&nbsp;</td><td>{$T_DATABASE_FORM.conf.activation.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'conf.activation', event)"/><div id = 'conf.activation' onclick = "eF_js_showHideDiv(this, 'conf.activation', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">Check if you want users to be able to use the system right after registering, without administrator confirmation</div></td></tr>
                                                <tr><td class = "labelCell">Single language:&nbsp;</td><td>{$T_DATABASE_FORM.conf.onelanguage.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'conf.onelanguage', event)"/><div id = 'conf.onelanguage' onclick = "eF_js_showHideDiv(this, 'conf.onelanguage', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">Check if you don&#039;t want multilingual suport. In this case, the system default language will be used always</div></td></tr>
                                                <tr><td class = "labelCell">Allow user signup:&nbsp;</td><td>{$T_DATABASE_FORM.conf.signup.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'conf.signup', event)"/><div id = 'conf.signup' onclick = "eF_js_showHideDiv(this, 'conf.signup', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">Check if you want to enable users registration. Leave unchecked if all registration will be performed by the administator</div></td></tr>
                                                <tr><td class = "labelCell">Show footer:&nbsp;</td><td>{$T_DATABASE_FORM.conf.show_footer.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'conf.show_footer', event)"/><div id = 'conf.show_footer' onclick = "eF_js_showHideDiv(this, 'conf.show_footer', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">Check if you want a footer to be visible in all pages</div></td></tr>
                                                <!--<tr><td class = "labelCell">Encrypt passwords:&nbsp;</td><td>{$T_DATABASE_FORM.encrypt_pwd.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'encrypt_pwd', event)"/><div id = 'encrypt_pwd' onclick = "eF_js_showHideDiv(this, 'encrypt_pwd', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">The database type can only be MySQL</div></td></tr>
                                                <tr id = "md5_row" style = "display:none"><td class = "labelCell">MD5 key:&nbsp;</td><td>{$T_DATABASE_FORM.conf.md5_key.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'conf.md5_key', event)"/><div id = 'conf.md5_key' onclick = "eF_js_showHideDiv(this, 'conf.md5_key', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">The database type can only be MySQL</div></td></tr>-->
                                                <tr><td class = "labelCell">IP white list:&nbsp;</td><td>{$T_DATABASE_FORM.conf.ip_white_list.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'conf.ip_white_list', event)"/><div id = 'conf.ip_white_list' onclick = "eF_js_showHideDiv(this, 'conf.ip_white_list', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">Enter a range of IPs, comma-separated, that are allowed to access the system</div></td></tr>
                                                <tr><td class = "labelCell">IP black list:&nbsp;</td><td>{$T_DATABASE_FORM.conf.ip_black_list.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'conf.ip_black_list', event)"/><div id = 'conf.ip_black_list' onclick = "eF_js_showHideDiv(this, 'conf.ip_black_list', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">Enter a range of IPs, comma-separated, that are not allowed to access the system</div></td></tr>
                                                <tr><td class = "labelCell">File extensions white list:&nbsp;</td><td>{$T_DATABASE_FORM.conf.file_white_list.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'conf.file_white_list', event)"/><div id = 'conf.file_white_list' onclick = "eF_js_showHideDiv(this, 'conf.file_white_list', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">Enter a comma-separated list of file extensions that are allowed to be uploaded to the system</div></td></tr>
                                                <tr><td class = "labelCell">File extensions black list:&nbsp;</td><td>{$T_DATABASE_FORM.conf.file_black_list.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'conf.file_black_list', event)"/><div id = 'conf.file_black_list' onclick = "eF_js_showHideDiv(this, 'conf.file_black_list', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">Enter a comma-separated list of file extensions that are not allowed to be uploaded to the system</div></td></tr>
                                                <tr><td class = "labelCell">Maximum file size (KB):&nbsp;</td><td>{$T_DATABASE_FORM.conf.max_file_size.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'conf.max_file_size', event)"/><div id = 'conf.max_file_size' onclick = "eF_js_showHideDiv(this, 'conf.max_file_size', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">Enter the maximum file size, in Kilobytes, that is allowed to be uploaded</div></td></tr>

                                                <tr><td colspan = "100%">&nbsp;</td></tr>
                                                <tr><td colspan = "100%" class = "horizontalSeparator" style = "color:#a0bdef;font-size:16px">Mail Server Settings</td></tr>
                                                <tr><td colspan = "100%"></td></tr>
                                                <tr><td class = "labelCell">SMTP Server name:&nbsp;</td><td>{$T_DATABASE_FORM.conf.smtp_host.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'conf.smtp_host', event)"/><div id = 'conf.smtp_host' onclick = "eF_js_showHideDiv(this, 'conf.smtp_host', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">The SMTP server name</div></td></tr>
                                                <tr><td class = "labelCell">User name:&nbsp;</td><td>{$T_DATABASE_FORM.conf.smtp_user.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'conf.smtp_user', event)"/><div id = 'conf.smtp_user' onclick = "eF_js_showHideDiv(this, 'conf.smtp_user', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">The user name that has accessto the SMTP server</div></td></tr>
                                                <tr><td class = "labelCell">Password:&nbsp;</td><td>{$T_DATABASE_FORM.conf.smtp_pass.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'conf.smtp_pass', event)"/><div id = 'conf.smtp_pass' onclick = "eF_js_showHideDiv(this, 'conf.smtp_pass', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">The user password that has access to the SMTP server</div></td></tr>
                                                <tr><td class = "labelCell">Port:&nbsp;</td><td>{$T_DATABASE_FORM.conf.smtp_port.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'conf.smtp_port', event)"/><div id = 'conf.smtp_port' onclick = "eF_js_showHideDiv(this, 'conf.smtp_port', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">The SMTP server port</div></td></tr>
                                                <tr><td class = "labelCell">SMTP Authentication:&nbsp;</td><td>{$T_DATABASE_FORM.conf.smtp_auth.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'conf.smtp_auth', event)"/><div id = 'conf.smtp_auth' onclick = "eF_js_showHideDiv(this, 'conf.smtp_auth', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">The SMTP server port</div></td></tr>

                                                <tr><td colspan = "100%">&nbsp;</td></tr>
                                                <tr><td colspan = "100%" class = "horizontalSeparator" style = "color:#a0bdef;font-size:16px">LDAP Server Settings</td></tr>
                                                <tr><td colspan = "100%"></td></tr>
                                                <tr><td class = "labelCell">Activate LDAP:&nbsp;</td><td>{$T_DATABASE_FORM.conf.activate_ldap.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'conf.activate_ldap', event)"/><div id = 'conf.activate_ldap' onclick = "eF_js_showHideDiv(this, 'conf.activate_ldap', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">Check if you want to enable LDAP server support</div></td></tr>
                                                <tr><td class = "labelCell" style = "color:#808080">Allow only LDAP accounts:&nbsp;</td><td>{$T_DATABASE_FORM.conf.only_ldap.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'conf.only_ldap', event)"/><div id = 'conf.only_ldap' onclick = "eF_js_showHideDiv(this, 'conf.only_ldap', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">Check if you want to only allowed user registrations that have an active account to the LDAP server</div></td></tr>
                                                <tr><td class = "labelCell" style = "color:#808080">LDAP server:&nbsp;</td><td>{$T_DATABASE_FORM.conf.ldap_server.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'conf.ldap_server', event)"/><div id = 'conf.ldap_server' onclick = "eF_js_showHideDiv(this, 'conf.ldap_server', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">Enter the LDAP server name</div></td></tr>
                                                <tr><td class = "labelCell" style = "color:#808080">LDAP port:&nbsp;</td><td>{$T_DATABASE_FORM.conf.ldap_port.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'conf.ldap_port', event)"/><div id = 'conf.ldap_port' onclick = "eF_js_showHideDiv(this, 'conf.ldap_port', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">Enter the LDAP server port (usually 389)</div></td></tr>
                                                <tr><td class = "labelCell" style = "color:#808080">Base dn:&nbsp;</td><td>{$T_DATABASE_FORM.conf.ldap_base_dn.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'conf.ldap_base_dn', event)"/><div id = 'conf.ldap_base_dn' onclick = "eF_js_showHideDiv(this, 'conf.ldap_base_dn', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">Enter the LDAP server base dn</div></td></tr>
                                                <tr><td class = "labelCell" style = "color:#808080">Bind dn:&nbsp;</td><td>{$T_DATABASE_FORM.conf.ldap_bind_dn.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'conf.ldap_bind_dn', event)"/><div id = 'conf.ldap_bind_dn' onclick = "eF_js_showHideDiv(this, 'conf.ldap_bind_dn', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">Enter the LDAP server bind dn</div></td></tr>
                                                <tr><td class = "labelCell" style = "color:#808080">LDAP Password:&nbsp;</td><td>{$T_DATABASE_FORM.conf.ldap_password.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'conf.ldap_password', event)"/><div id = 'conf.ldap_password' onclick = "eF_js_showHideDiv(this, 'conf.ldap_password', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">Enter the LDAP server password</div></td></tr>
                                                <tr><td class = "labelCell" style = "color:#808080">Protocol:&nbsp;</td><td>{$T_DATABASE_FORM.conf.ldap_protocol.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'conf.ldap_protocol', event)"/><div id = 'conf.ldap_protocol' onclick = "eF_js_showHideDiv(this, 'conf.ldap_protocol', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">Enter the LDAP server protocol (2 or preferrably 3)</div></td></tr>

                                                <tr><td colspan = "100%">&nbsp;</td></tr>
                                                <tr><td colspan = "100%" class = "horizontalSeparator" style = "color:#a0bdef;font-size:16px">LDAP Attribute mapping</td></tr>
                                                <tr><td colspan = "100%"></td></tr>
                                                <tr><td class = "labelCell" style = "color:#808080">preferredlanguage:&nbsp;</td><td>{$T_DATABASE_FORM.conf.ldap_preferredlanguage.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'conf.ldap_preferredlanguage', event)"/><div id = 'conf.ldap_preferredlanguage' onclick = "eF_js_showHideDiv(this, 'conf.ldap_preferredlanguage', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">Enter the name of the equivalent "preferredlanguage" attribute at your server</div></td></tr>
                                                <tr><td class = "labelCell" style = "color:#808080">telephonenumber:&nbsp;</td><td>{$T_DATABASE_FORM.conf.ldap_telephonenumber.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'conf.ldap_telephonenumber', event)"/><div id = 'conf.ldap_telephonenumber' onclick = "eF_js_showHideDiv(this, 'conf.ldap_telephonenumber', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">Enter the name of the equivalent "telephonenumber" attribute at your server</div></td></tr>
                                                <tr><td class = "labelCell" style = "color:#808080">mail:&nbsp;</td><td>{$T_DATABASE_FORM.conf.ldap_mail.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'conf.ldap_mail', event)"/><div id = 'conf.ldap_mail' onclick = "eF_js_showHideDiv(this, 'conf.ldap_mail', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">Enter the name of the equivalent "mail" attribute at your server</div></td></tr>
                                                <tr><td class = "labelCell" style = "color:#808080">postaladdress:&nbsp;</td><td>{$T_DATABASE_FORM.conf.ldap_postaladdress.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'conf.ldap_postaladdress', event)"/><div id = 'conf.ldap_postaladdress' onclick = "eF_js_showHideDiv(this, 'conf.ldap_postaladdress', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">Enter the name of the equivalent "postaladdress" attribute at your server</div></td></tr>
                                                <tr><td class = "labelCell" style = "color:#808080">l (locality):&nbsp;</td><td>{$T_DATABASE_FORM.conf.ldap_l.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'conf.ldap_l', event)"/><div id = 'conf.ldap_l' onclick = "eF_js_showHideDiv(this, 'conf.ldap_l', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">Enter the name of the equivalent "l" (locality) attribute at your server</div></td></tr>
                                                <tr><td class = "labelCell" style = "color:#808080">cn (common name):&nbsp;</td><td>{$T_DATABASE_FORM.conf.ldap_cn.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'conf.ldap_cn', event)"/><div id = 'conf.ldap_cn' onclick = "eF_js_showHideDiv(this, 'conf.ldap_cn', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">Enter the name of the equivalent "cn" (Common name) attribute at your server</div></td></tr>
                                                <tr><td class = "labelCell" style = "color:#808080">uid:&nbsp;</td><td>{$T_DATABASE_FORM.conf.ldap_uid.html}</td><td style = "text-align:right"><img src = "images/16x16/help2.png" alt = "Help" title = "Help" onclick = "eF_js_showHideDiv(this, 'conf.ldap_uid', event)"/><div id = 'conf.ldap_uid' onclick = "eF_js_showHideDiv(this, 'conf.ldap_uid', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">Enter the name of the equivalent "uid" attribute at your server</div></td></tr>

                                                <tr><td colspan = "100%">&nbsp;</td></tr>
                                                <tr><td></td><td class = "formRequired">{$T_DATABASE_FORM.requirednote}</td></tr>
                                                <tr><td colspan = "100%">&nbsp;</td></tr>
                                            
                                                <tr><td></td>
                                                    <td></td>
                                                    <td style = "width:1%">
                                                        <table>
                                            {if $T_ROLLBACK_PROBLEM}
                                                {assign var = "error_message" value = "Could not empty table: "|cat:$T_ROLLBACK_PROBLEM}
                                            {elseif $T_ROLLBACK_PROBLEM === false}
                                                {assign var = "success_message" value = "Table emptied"}
                                            {/if}
                                            {if !isset($T_FAILED_INSERTIONS)}
                                                            <tr><td style = "text-align:center;padding-right:2em">
                                                                    <a href = "#" title = "Continue" onclick = "document.step3_form.step3_submit.click();return false;"><img src = "images/32x32/navigate_right2.png" border = "0" title = "Continue" alt = "Continue"><br/>Continue</a>
                                                                </td></tr>
                                            {else}
                                                {assign var = "error_message" value = "Some values could not be inserted because:<br/><br/>"|cat:$T_FAILED_INSERTIONS}
                                                            <tr><td style = "text-align:center;padding-right:2em">
                                                                    <a href = "#" title = "Empty table and retry" onclick = "document.step3_form.rollback.click();return false;"><img src = "images/32x32/undo.png" border = "0" title = "Empty table and retry" alt = "Empty table and retry"><br/>Empty table and retry</a>
                                                                </td><td style = "text-align:center;padding-right:2em">
                                                                    <a href = "#" title = "Continue anyway" onclick = "document.step3_form.continue_anyway.click();return false;"><img src = "images/32x32/navigate_right2.png" border = "0" title = "Continue anyway" alt = "Continue anyway"><br/>Continue anyway</a>
                                                                </td></tr>
                                            {/if}
                                                        </table>
                                                    </td></tr>
                                                <div style = "display:none">                                                
                                                    {$T_DATABASE_FORM.rollback.html}
                                                    {$T_DATABASE_FORM.continue_anyway.html}
                                                    {$T_DATABASE_FORM.step3_submit.html}
                                                </div>
                                            
                                            </table>
                                        </form>
                                </td></tr>
    {/capture}
{elseif $smarty.get.step == 4}

    {capture name = 'step4'}
                                <tr><td class = "moduleCell">

                                        {$T_DATABASE_FORM.javascript}                                    
                                        <form {$T_DATABASE_FORM.attributes}>
                                            {$T_DATABASE_FORM.hidden}
                                            <table class = "formElements" style = "margin-left:0px">
                                                <tr><td colspan = "100%">&nbsp;</td></tr>
                                                <tr><td colspan = "100%" class = "horizontalSeparator" style = "color:#a0bdef;font-size:16px">Administrator Account</td></tr>
                                                <tr><td colspan = "100%"></td></tr>
                                                <tr><td class = "labelCell">Login:&nbsp;</td><td>{$T_DATABASE_FORM.admin_user.html}</td></tr>
                                                <tr><td class = "labelCell">Password:&nbsp;</td><td>{$T_DATABASE_FORM.admin_password.html}</td></tr>
                                                <tr><td class = "labelCell">Repeat password:&nbsp;</td><td>{$T_DATABASE_FORM.admin_repeat_pwd.html}</td></tr>
                                                <tr><td class = "labelCell">Name:&nbsp;</td><td>{$T_DATABASE_FORM.admin_name.html}</td></tr>
                                                <tr><td class = "labelCell">Surname:&nbsp;</td><td>{$T_DATABASE_FORM.admin_surname.html}</td></tr>
                                                <tr><td class = "labelCell">Email:&nbsp;</td><td>{$T_DATABASE_FORM.admin_email.html}</td></tr>
                                                <tr><td class = "labelCell">Language:&nbsp;</td><td>{$T_DATABASE_FORM.admin_language.html}</td></tr>
                                                <tr><td class = "labelCell">Create Default lesson:&nbsp;</td><td>{$T_DATABASE_FORM.create_lesson.html}</td></tr>
                                                <tr><td class = "labelCell">Create Professor account:&nbsp;</td><td>{$T_DATABASE_FORM.activate_prof.html}</td></tr>
                                                <tr><td class = "labelCell">Create Student account:&nbsp;</td><td>{$T_DATABASE_FORM.activate_stud.html}</td></tr>

                                                <tr><td colspan = "100%">&nbsp;</td></tr>
                                                <tr><td colspan = "100%" class = "horizontalSeparator" style = "color:#a0bdef;font-size:16px">Professor Account</td></tr>
                                                <tr><td colspan = "100%"></td></tr>
                                                <tr><td class = "labelCell">Login:&nbsp;</td><td>{$T_DATABASE_FORM.prof_user.html}</td></tr>
                                                <tr><td class = "labelCell">Password:&nbsp;</td><td>{$T_DATABASE_FORM.prof_password.html}</td></tr>
                                                <tr><td class = "labelCell">Repeat password:&nbsp;</td><td>{$T_DATABASE_FORM.prof_repeat_pwd.html}</td></tr>
                                                <tr><td class = "labelCell">Name:&nbsp;</td><td>{$T_DATABASE_FORM.prof_name.html}</td></tr>
                                                <tr><td class = "labelCell">Surname:&nbsp;</td><td>{$T_DATABASE_FORM.prof_surname.html}</td></tr>
                                                <tr><td class = "labelCell">Email:&nbsp;</td><td>{$T_DATABASE_FORM.prof_email.html}</td></tr>
                                                <tr><td class = "labelCell">Language:&nbsp;</td><td>{$T_DATABASE_FORM.prof_language.html}</td></tr>

                                                <tr><td colspan = "100%">&nbsp;</td></tr>
                                                <tr><td colspan = "100%" class = "horizontalSeparator" style = "color:#a0bdef;font-size:16px">Student Account</td></tr>
                                                <tr><td colspan = "100%"></td></tr>
                                                <tr><td class = "labelCell">Login:&nbsp;</td><td>{$T_DATABASE_FORM.stud_user.html}</td></tr>
                                                <tr><td class = "labelCell">Password:&nbsp;</td><td>{$T_DATABASE_FORM.stud_password.html}</td></tr>
                                                <tr><td class = "labelCell">Repeat password:&nbsp;</td><td>{$T_DATABASE_FORM.stud_repeat_pwd.html}</td></tr>
                                                <tr><td class = "labelCell">Name:&nbsp;</td><td>{$T_DATABASE_FORM.stud_name.html}</td></tr>
                                                <tr><td class = "labelCell">Surname:&nbsp;</td><td>{$T_DATABASE_FORM.stud_surname.html}</td></tr>
                                                <tr><td class = "labelCell">Email:&nbsp;</td><td>{$T_DATABASE_FORM.stud_email.html}</td></tr>
                                                <tr><td class = "labelCell">Language:&nbsp;</td><td>{$T_DATABASE_FORM.stud_language.html}</td></tr>

                                                <tr><td colspan = "100%">&nbsp;</td></tr>
                                                <tr><td></td><td class = "formRequired">{$T_DATABASE_FORM.requirednote}</td></tr>
                                                <tr><td colspan = "100%">&nbsp;</td></tr>
                                                <tr><td></td><td style = "text-align:right">
                                                        <table align = "right">
                                                {if $T_CREATE_ERRORS}
                                                    {assign var = "error_message" value = $T_CREATE_ERRORS}
                                                            <tr><td style = "text-align:center;padding-right:2em">
                                                                    <a href = "#" title = "Delete existing users and retry" onclick = "document.step4_form.try_again.click();return false;"><img src = "images/32x32/navigate_right2.png" border = "0" title = "Delete existing users and retry" alt = "Delete existing users and retry"><br/>Delete existing users and retry</a>
                                                                </td><td style = "text-align:center;padding-right:2em">
                                                                    <a href = "#" title = "Continue anyway" onclick = "document.step4_form.continue_anyway.click();return false;"><img src = "images/32x32/navigate_right2.png" border = "0" title = "Continue anyway" alt = "Continue anyway"><br/>Continue anyway</a>
                                                                </td></tr>
                                                {else}
                                                            <tr><td style = "text-align:center;padding-right:2em">
                                                                    <a href = "#" title = "Continue" onclick = "document.step4_form.step4_submit.click();return false;"><img src = "images/32x32/navigate_right2.png" border = "0" title = "Continue" alt = "Continue"><br/>Continue</a>
                                                                </td></tr>
                                                {/if}
                                                        </table>
                                                    </td></tr>
                                                <div style = "display:none">
                                                {$T_DATABASE_FORM.step4_submit.html}
                                                {$T_DATABASE_FORM.try_again.html}
                                                {$T_DATABASE_FORM.continue_anyway.html}
                                                </div>

                                            </table>
                                        </form>
                                        <script>
                                            <!--
                                            //Depending on the checkbox states, (in)activate corresponding inputs
                                            if (!document.getElementById("activate_prof").checked) 
                                                eF_js_activateElements('prof'); 
                                            if (!document.getElementById("activate_stud").checked) 
                                                eF_js_activateElements('stud');
                                            //-->
                                        </script>
                                </td></tr>
    {/capture}

{/if}

{if $smarty.get.finish}
    {capture name = 'finish'}
                                <tr><td class = "moduleCell">
                                        {if $T_ERROR}
                                            {assign var = "error_message" value = $T_ERROR}
                                        {/if}
                                        <table width = "100%">
                                            <tr><td class = "mediumHeader" style = "vertical-align:middle;height:5em;">Congratulations!</td></tr>
                                            <tr><td style = "vertical-align:middle;height:20em;font-size:12px;text-align:center">
                                                    <img src = "images/48x48/checks.png" alt = "eFront installed succesfully" title = "eFront installed succesfully"/>
                                                </td></tr>
                                            <tr><td style = "font-size:12px;text-align:center">
                                                eFront installed succesfully!<br/><br/>
                                                You must delete the installation folder <strong>www/install/</strong> in order to use the system.<br/>
                                                You are strongly recommended to set the <strong>libraries/</strong> folder permissions to "read only"<br/>
                                                Click <a href = "index.php?delete_install=1" style = "font-weight:bold;text-decoration:underline">here</a> to delete the installation directory automatically and navigate to the system index page.
                                            </td></tr>
                                        </table>
                                </td></tr>
    {/capture}
{/if}


            <table style = "width:100%;height:100%;padding:0px;margin:0px">
                <tr><td style = "vertical-align:top;height:1px" colspan = "2">
                        {if $T_MESSAGE}
                            {eF_template_printMessage message=$T_MESSAGE type=$T_MESSAGE_TYPE}
                        {/if}
                        {if $smarty.get.message}
                            {eF_template_printMessage message=$smarty.get.message type=$smarty.get.message_type}
                        {/if}
                    </td>
                </tr>
{if !$smarty.get.step && !$smarty.get.finish}
                <tr><td style = "height:80%;">
                        <table class = "singleColumnData" height = "100%">
                            <tr style = "height:1%"><td colspan = "100%" >&nbsp;</td></tr>
                            <tr style = "height:1%"><td colspan = "100%" class = "mediumHeader">Welcome to eFront's installation wizard</td></tr>
    {if $T_FAILED_UPGRADES || $T_FAILED_UPGRADES_OTHERS}
                            <tr style = "height:1%"><td style = "padding-top:50px;vertical-align:middle" colspan = "100%" class = "smallHeader">
                                                        Automatic backups from passed update processes:<br/>
        {section name = 'failed_upgrades_list' loop = $T_FAILED_UPGRADES}
                                                        <a style = "vertical-align:middle;" href = "{$smarty.server.PHP_SELF}?restore={$T_FAILED_UPGRADES[failed_upgrades_list].file}" onclick = "return confirm('Are you sure? This action will alter permanently the contents of database &quot;{$T_FAILED_UPGRADES[failed_upgrades_list].name}&quot!\nThis action may take a considerable amount of time to complete, depending on the size of your data. Make sure your PHP setting max_execution_time is set to a large value (1200 seconds for example)')">#filter:timestamp_time-{$T_FAILED_UPGRADES[failed_upgrades_list].date}#: Database &quot;{$T_FAILED_UPGRADES[failed_upgrades_list].name}&quot;</a> <a href = "{$smarty.server.PHP_SELF}?delete_backup={$T_FAILED_UPGRADES[failed_upgrades_list].file}" ><img src = "images/16x16/delete.png"  style = "vertical-align:middle;" border = "0"/></a><br/>
        {/section}
        
		{section name = 'failed_upgrades_list' loop = $T_FAILED_UPGRADES_OTHERS}
                                                        <a style = "vertical-align:middle;" href = "{$smarty.server.PHP_SELF}?restore={$T_FAILED_UPGRADES_OTHERS[failed_upgrades_list].file}" onclick = "return confirm('Are you sure? This action will alter permanently the contents of database &quot;{$T_FAILED_UPGRADES_OTHERS[failed_upgrades_list].name}&quot!\nThis action may take a considerable amount of time to complete, depending on the size of your data. Make sure your PHP setting max_execution_time is set to a large value (1200 seconds for example)')">#filter:timestamp_time-{$T_FAILED_UPGRADES_OTHERS[failed_upgrades_list].date}#: Database &quot;{$T_FAILED_UPGRADES_OTHERS[failed_upgrades_list].name}&quot;</a> <a href = "{$smarty.server.PHP_SELF}?delete_backup={$T_FAILED_UPGRADES_OTHERS[failed_upgrades_list].file}" ><img src = "images/16x16/delete.png"  style = "vertical-align:middle;" border = "0"/></a><br/>
        {/section}
                                                        Click on any of the above links to restore it
                            </td></tr>
    {/if}
                            <tr><td colspan = "100%" style = "vertical-align:middle;">
                                <table align = "center" >
                                    <tr>
                                        <td style = "text-align:center;width:100px;">
                                            <a href = "{$smarty.server.PHP_SELF}?step=1" title = "New Installation"><img src = "images/48x48/box.png" border = "0" title = "New Installation" alt = "New Installation"><br/>New Installation</a></td>
                                        <td style = "width:100px;">&nbsp;</td>

                                        <td style = "text-align:center;width:100px;">
                                            <a href = "{$smarty.server.PHP_SELF}?step=1&upgrade=1" title = "Upgrade from previous versions"><img src = "images/48x48/box_next.png" border = "0" title = "Upgrade from previous versions" alt = "Upgrade from previous versions"><br/>Upgrade from previous versions</a></td>
                                    </tr>
                                    <tr><td colspan = "100%">&nbsp;</td></tr>
                                    <tr>
                                        <td style = "width:100px;">&nbsp;</td>
                                        <td style = "text-align:center;width:100px;">
                                            <img src = "images/48x48/help2.png" border = "0" title = "Installation instructions" alt = "Installation instructions" onclick = "eF_js_showHideDiv(this, 'install_instructions', event)">
                                            <div id = 'install_instructions' onclick = "eF_js_showHideDiv(this, 'install_instructions', event)" class = "popUpInfoDiv" style = "padding:5px 5px 5px 5px;width:400px;position:absolute;display:none;text-align:left">
                                                <ul style = "padding-left:10px;">
                                                There are 2 installation options: New installation and Upgrade from previous versions.<br/><br/>
                                                <li>New installation: Select this option if you are installing efront for the first time on this machine, or you want to perform a clean installation</li>
                                                <li>Upgrade from previous versions: Select this option if you have an existing eFront installation and you want to upgrade it to the current version. You must have already copied the current version files to the same place where the old eFront files are located. During the upgrade process, you will be asked whether you wish to retain your current database data.</li>
                                                </ul>
                                            </div>
                                            <br/>Installation instructions
                                        </td>
                                        <td style = "width:100px;">&nbsp;</td></tr>
                                    <tr><td colspan = "100%">&nbsp;</td></tr>
                                </table>
                            </td></tr>
                        </table>        
                    </td></tr>
{else}                
                <tr>
                    <td class = "leftColumn" id = "leftColumn" style = "width:10%;background-color:#f8f8f8;border-right:1px dotted gray;height:80%;vertical-align:top">
                        <table class = "leftColumnData">
                            <tr><td style = "text-align:center"><img src = "images/logo.png"</td></tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr><td class = "smallHeader"><a href = "{$smarty.server.PHP_SELF}">Start</a></td></tr>
                            <tr><td class = "smallHeader" {if $smarty.get.step == 1} style = "font-weight:bold;background-color:#f1f1f1"{/if}>System Settings</td></tr>
                            <tr><td class = "smallHeader" {if $smarty.get.step == 2} style = "font-weight:bold;background-color:#f1f1f1"{/if}>Database Settings</td></tr>
{if !$smarty.get.upgrade}   
                            <tr><td class = "smallHeader" {if $smarty.get.step == 3} style = "font-weight:bold;background-color:#f1f1f1"{/if}>Configuration Options</td></tr>
    {if !$smarty.get.migrate}   
                            <tr><td class = "smallHeader" {if $smarty.get.step == 4} style = "font-weight:bold;background-color:#f1f1f1"{/if}>User accounts</td></tr>
    {/if}
{/if}
                            <tr><td class = "smallHeader" {if $smarty.get.finish}    style = "font-weight:bold;background-color:#f1f1f1"{/if}>Finish</td></tr>
                            <tr><td>&nbsp;</td></tr>
                        {if $error_message || $notice_message || $success_message}
                            <tr><td>
                                <table style = "background-color:#f1f1f1;width:100%;border:1px dotted #D0D0D0">
                                    <tr><td style = "font-size:12px;padding:1em 0em 1em 1em">Installation messages</td></tr>
                                    <tr><td id = "success_messages"  style = "padding-left:1em;color:green;font-size:12px"> {$success_message}</td></tr>
                                    <tr><td id = "notice_messages"   style = "padding-left:1em;color:orange;font-size:12px">{$notice_message}</td></tr>
                                    <tr><td id = "error_messages"    style = "padding-left:1em;color:red;font-size:12px">   {$error_message}</td></tr>
                                    <tr><td>&nbsp;</td></tr>
                                </table>
                            </td></tr>
                        {/if}
                        </table>
                    </td>
                    <td class = "rightColumn" id = "rightColumn" style = "vertical-align:top">
                        <table class = "rightColumnData">
                            {$smarty.capture.step0}
                            {$smarty.capture.step1}
                            {$smarty.capture.step2}
                            {$smarty.capture.step3}
                            {$smarty.capture.step4}
                            {$smarty.capture.finish}
                        </table>
                    </td>
                </tr>
{/if}
                <tr><td style = "vertical-align:bottom;">
{if !$smarty.get.step && !$smarty.get.finish}
            <table class = "indexFoot" style = "width:100%;">
                <tr><td style = "white-space:nowrap;text-align:center">
                        <a href = ""><img src = "images/logo.png"  border = "0" title="{$smarty.const._EFRONT}" alt="{$smarty.const._EFRONT}"/ style = "vertical-align:middle"></a>
                        <a href = "http://www.efrontlearning.net">{$smarty.const._EFRONT}</a> - <b> version {$T_VERSION}</b>
                    </td></tr>
            </table>
{/if}
 
   </td></tr>
</table>


<script>if (document.getElementById('dimmer') && document.getElementById('dimmer').style.display != 'none') eF_js_showDivPopup();</script>
</body>
</html>