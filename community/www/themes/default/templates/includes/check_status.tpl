{* smarty template for check_status.php *}
{capture name = 't_check_status_code'}
    <table width = "100%">
    <tr><td>
        <table style = "width:100%">
        <tr><td colspan = "100%">&nbsp;</td></tr>
        <tr><td colspan = "100%" class = " blockHeader">Recommended Software</td></tr>
        <tr class = "topTitle defaultRowHeight">
            <td style = "width:30%">Name</td>
            <td style = "width:30%">Installed Version</td>
            <td style = "width:30%">Recommended</td>
            <td style = "width:10%" class = "centerAlign">Status</td>
            <td style = "width:1%"></td></tr>
    {foreach name = 'settings_list' key = key item = item from = $T_SOFTWARE}
        <tr class = "{cycle values = "oddRowColor,evenRowColor"} defaultRowHeight">
            <td>{$item.name}</td>
            <td>{$item.installed}</td>
            <td>{$item.recommended}</td>
            <td class = "centerAlign">
            {if $item.status}
                <img src = "images/16x16/success.png" alt = "OK" title = "OK" />
            {else}
                {if $item.name == 'PHP'}<img src = "images/16x16/forbidden.png" alt = "Missing" title = "Missing" />{else}<img src = "images/16x16/warning.png" alt = "Missing" title = "Missing" />{/if}
            {/if}&nbsp;
            </td>
            <td><img src = "images/16x16/help.png" alt = "help" title = "help" onclick = "eF_js_showHideDiv(this, '{$key}', event)"><div id = '{$key}' onclick = "eF_js_showHideDiv(this, '{$key}', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">{$item.help}</div></td></tr>
    {/foreach}
        </table>

{if $T_MANDATORY}
        <table style = "width:100%">
        <tr><td colspan = "100%">&nbsp;</td></tr>
        <tr><td colspan = "100%" class = " blockHeader">Mandatory PHP extesions</td></tr>
        <tr class = "topTitle defaultRowHeight">
            <td>Name</td>

            <td style = "width:10%;" class = "centerAlign">Status</td>
            <td style = "width:1%"></td></tr>
    {foreach name = 'mandatory_list' key = key item = item from = $T_MANDATORY}
        <tr class = "{cycle values = "oddRowColor,evenRowColor"} defaultRowHeight">
            <td>{$item.name}</td>

            <td class = "centerAlign">{if $item.enabled}<img src = "images/16x16/success.png" alt = "OK" title = "OK" />{else}<img src = "images/16x16/forbidden.png" alt = "Missing" title = "Missing" />{/if}</td>
            <td><img src = "images/16x16/help.png" alt = "help" title = "help" onclick = "eF_js_showHideDiv(this, '{$key}', event)"><div id = '{$key}' onclick = "eF_js_showHideDiv(this, '{$key}', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">{$item.help}</div></td></tr>
    {/foreach}
        </table>
{/if}

{if $T_OPTIONAL}
        <table style = "width:100%">
        <tr><td colspan = "100%">&nbsp;</td></tr>
        <tr><td colspan = "100%" class = " blockHeader">Optional PHP extesions</td></tr>
        <tr class = "topTitle defaultRowHeight">
            <td>Name</td>

            <td style = "width:10%;" class = "centerAlign">Status</td>
            <td style = "width:1%"></td></tr>
    {foreach name = 'optional_list' key = key item = item from = $T_OPTIONAL}
        <tr class = "{cycle values = "oddRowColor,evenRowColor"} defaultRowHeight">
            <td>{$item.name}</td>

            <td class = "centerAlign">{if $item.enabled}<img src = "images/16x16/success.png" alt = "OK" title = "OK" />{else}<img src = "images/16x16/warning.png" alt = "Missing" title = "Missing" />{/if}</td>
            <td><img src = "images/16x16/help.png" alt = "help" title = "help" onclick = "eF_js_showHideDiv(this, '{$key}', event)"><div id = '{$key}' onclick = "eF_js_showHideDiv(this, '{$key}', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">{$item.help}</div></td></tr>
    {/foreach}
        </table>
{/if}
{if $T_SETTINGS_MANDATORY}
        <table style = "width:100%">
        <tr><td colspan = "100%">&nbsp;</td></tr>
        <tr><td colspan = "100%" class = " blockHeader">Mandatory PHP Settings</td></tr>
        <tr class = "topTitle defaultRowHeight">
            <td style = "width:30%">Name</td>
            <td style = "width:30%">Value</td>
            <td style = "width:30%">Recommended</td>

            <td style = "width:10%;" class = "centerAlign">Status</td>
            <td style = "width:1%"></td></tr>
    {foreach name = 'settings_list' key = key item = item from = $T_SETTINGS_MANDATORY}
        <tr class = "{cycle values = "oddRowColor,evenRowColor"} defaultRowHeight">
            <td>{$item.name}</td>
            <td>{$item.value}</td>
            <td>{$item.recommended}</td>

            <td class = "centerAlign">{if $item.status}<img src = "images/16x16/success.png" alt = "OK" title = "OK" />{else}<img src = "images/16x16/forbidden.png" alt = "Missing" title = "Missing" />{/if}</td>
            <td><img src = "images/16x16/help.png" alt = "help" title = "help" onclick = "eF_js_showHideDiv(this, '{$key}', event)"><div id = '{$key}' onclick = "eF_js_showHideDiv(this, '{$key}', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">{$item.help}</div></td></tr>
    {/foreach}
        </table>
{/if}
{if $T_SETTINGS}
        <table style = "width:100%">
        <tr><td colspan = "100%">&nbsp;</td></tr>
        <tr><td colspan = "100%" class = " blockHeader">Recommended PHP Settings</td></tr>
        <tr class = "topTitle defaultRowHeight">
            <td style = "width:30%">Name</td>
            <td style = "width:30%">Value</td>
            <td style = "width:30%">Recommended</td>

            <td style = "width:10%;" class = "centerAlign">Status</td>
            <td style = "width:1%"></td></tr>
    {foreach name = 'settings_list' key = key item = item from = $T_SETTINGS}
        <tr class = "{cycle values = "oddRowColor,evenRowColor"} defaultRowHeight">
            <td>{$item.name}</td>
            <td>{$item.value}</td>
            <td>{$item.recommended}</td>

            <td class = "centerAlign">{if $item.status}<img src = "images/16x16/success.png" alt = "OK" title = "OK" />{else}<img src = "images/16x16/{if $item.name == 'memory_limit'}forbidden{else}warning{/if}.png" alt = "Missing" title = "Missing" />{/if}</td>
            <td><img src = "images/16x16/help.png" alt = "help" title = "help" onclick = "eF_js_showHideDiv(this, '{$key}', event)"><div id = '{$key}' onclick = "eF_js_showHideDiv(this, '{$key}', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">{$item.help}</div></td></tr>
    {/foreach}
        </table>
{/if}
{if $T_PERMISSIONS}
        <table style = "width:100%">
        <tr><td colspan = "100%">&nbsp;</td></tr>
        <tr><td colspan = "100%" class = " blockHeader">Filesystem Permissions</td></tr>
        <tr class = "topTitle defaultRowHeight">
            <td style = "width:30%">Directory</td>
            <td style = "width:60%">Writable</td>

            <td style = "width:10%;" class = "centerAlign">Status</td>
            <td style = "width:1%"></td></tr>
    {foreach name = 'settings_list' key = key item = item from = $T_PERMISSIONS}
        <tr class = "{cycle values = "oddRowColor,evenRowColor"} defaultRowHeight">
            <td>{$key}</td>
            <td>{if $item.writable}YES{else}NO{/if}</td>

            <td class = "centerAlign">{if $item.writable}<img src = "images/16x16/success.png" alt = "OK" title = "OK" />{else}<img src = "images/16x16/forbidden.png" alt = "Missing" title = "Missing" />{/if}</td>
            <td><img src = "images/16x16/help.png" alt = "help" title = "help" onclick = "eF_js_showHideDiv(this, '{$key}', event)"><div id = '{$key}' onclick = "eF_js_showHideDiv(this, '{$key}', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">{$item.help}</div></td></tr>
    {/foreach}
        </table>
{/if}
{if $T_PEAR}
        <table style = "width:100%">
        <tr><td colspan = "100%">&nbsp;</td></tr>
        <tr><td colspan = "100%" class = " blockHeader">PEAR compatibility</td></tr>
        <tr class = "topTitle defaultRowHeight">
            <td>Package</td>

            <td style = "width:10%;" class = "centerAlign">Status</td>
            <td style = "width:1%"></td></tr>
    {foreach name = 'settings_list' key = key item = item from = $T_PEAR}
        <tr class = "{cycle values = "oddRowColor,evenRowColor"} defaultRowHeight">
            <td>{$key}</td>

            <td class = "centerAlign">{if $item.exists}<img src = "images/16x16/success.png" alt = "OK" title = "OK" />{else}<img src = "images/16x16/forbidden.png" alt = "Missing" title = "Missing" />{/if}</td>
            <td><img src = "images/16x16/help.png" alt = "help" title = "help" onclick = "eF_js_showHideDiv(this, '{$key}', event)"><div id = '{$key}' onclick = "eF_js_showHideDiv(this, '{$key}', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">{$item.help}</div></td></tr>
    {/foreach}
        </table>
{/if}
 <br>
 <div class = "blockHeader">Language compatibility</div>
 <div class = "headerTools">
  {if $T_CORRECT_LOCALE}
  <span>
   <img src = "images/16x16/success.png">
   <a href = "javascript:void(0)" onclick = "$('correct_locale').toggle()">{$T_CORRECT_LOCALE|@sizeof} available languages</a>
  </span>
  {/if}
  {if $T_INCORRECT_LOCALE}
  <span>
   <img src = "images/16x16/forbidden.png">
   <a href = "javascript:void(0)" onclick = "$('incorrect_locale').toggle()">{$T_INCORRECT_LOCALE|@sizeof} unavailable languages</a>
  </span>
  {/if}
 </div>

{if $T_CORRECT_LOCALE}
    <table style = "width:100%;display:none" id = "correct_locale">
        <tr class = "topTitle defaultRowHeight">
            <td>Language</td>
            <td>Installed Locale</td>

            <td style = "width:10%;" class = "centerAlign">Status</td>
            <td style = "width:1%"></td></tr>
    {foreach name = 'settings_list' key = key item = item from = $T_CORRECT_LOCALE}
        <tr class = "{cycle name = "local_colors" values = "oddRowColor,evenRowColor"} defaultRowHeight">
            <td>{$item.language}</td>
            <td>{$item.locale}</td>

            <td class = "centerAlign">{if $item.locale}<img src = "images/16x16/success.png" alt = "OK" title = "OK" />{else}<img src = "images/16x16/warning.png" alt = "Missing" title = "Missing" />{/if}</td>
            <td><img src = "images/16x16/help.png" alt = "help" title = "help" onclick = "eF_js_showHideDiv(this, '{$key}', event)"><div id = '{$key}' onclick = "eF_js_showHideDiv(this, '{$key}', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">{$item.help}</div></td></tr>
    {/foreach}
        <tr><td>&nbsp;</td></tr>
    </table>
{/if}
{if $T_INCORRECT_LOCALE}
    <table style = "width:100%;display:none" id = "incorrect_locale">
        <tr class = "topTitle defaultRowHeight">
            <td>Language</td>
            <td>Installed Locale</td>

            <td style = "width:10%;" class = "centerAlign">Status</td>
            <td style = "width:1%"></td></tr>
    {foreach name = 'settings_list' key = key item = item from = $T_INCORRECT_LOCALE}
        <tr class = "{cycle name = "local_colors" values = "oddRowColor,evenRowColor"} defaultRowHeight">
            <td>{$item.language}</td>
            <td>{$item.locale}</td>

            <td class = "centerAlign">{if $item.locale}<img src = "images/16x16/success.png" alt = "OK" title = "OK" />{else}<img src = "images/16x16/warning.png" alt = "Missing" title = "Missing" />{/if}</td>
            <td><img src = "images/16x16/help.png" alt = "help" title = "help" onclick = "eF_js_showHideDiv(this, '{$key}', event)"><div id = '{$key}' onclick = "eF_js_showHideDiv(this, '{$key}', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:300px;position:absolute;display:none">{$item.help}</div></td></tr>
    {/foreach}
        <tr><td>&nbsp;</td></tr>
    </table>
{/if}
    </td><td>
    </td></tr>
        <tr><td>&nbsp;</td></tr>
</table>
{/capture}
{eF_template_printBlock title=$smarty.const._ENVIRONMENTALCHECK data=$smarty.capture.t_check_status_code image='32x32/generic.png'}
