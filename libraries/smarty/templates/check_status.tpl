{* smarty template for check_status.php *}

    <table width = "100%">
    <tr><td>
        <table style = "width:100%">                                        
        <tr><td colspan = "100%">&nbsp;</td></tr>
        <tr><td colspan = "100%" class = "horizontalSeparator blockHeader">Recommended Software</td></tr>
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
        <tr><td colspan = "100%" class = "horizontalSeparator blockHeader">Mandatory PHP extesions</td></tr>
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
        <tr><td colspan = "100%" class = "horizontalSeparator blockHeader">Optional PHP extesions</td></tr>
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
        <tr><td colspan = "100%" class = "horizontalSeparator blockHeader">Recommended PHP Settings</td></tr>
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
        <tr><td colspan = "100%" class = "horizontalSeparator blockHeader">Filesystem Permissions</td></tr>
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
        <tr><td colspan = "100%" class = "horizontalSeparator blockHeader">PEAR compatibility</td></tr>
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
        <tr><td colspan = "100%" class = "horizontalSeparator blockHeader">Language compatibility</td></tr>
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
    </td><td>
    </td></tr>
</table>
