{*Smarty template*}

{if $smarty.get.add_link || $smarty.get.edit_link}
    {capture name = 't_insert_link_code'}
                {$T_LINKS_FORM.javascript}
                <form {$T_LINKS_FORM.attributes}>
                    {$T_LINKS_FORM.hidden}
                    <table class = "formElements" style = "width:100%">
                        <tr><td class = "labelCell">{$smarty.const._LINKS_DISPLAY}:&nbsp;</td>
                            <td class = "elementCell">{$T_LINKS_FORM.display.html}</td></tr>
                        <tr><td class = "labelCell">{$smarty.const._LINKS_LINK}:&nbsp;</td>
                            <td class = "elementCell">{$T_LINKS_FORM.link.html}</td></tr>
                        <tr><td class = "labelCell">{$smarty.const._LINKS_DESCRIPTION}:&nbsp;</td>
                            <td class = "elementCell">{$T_LINKS_FORM.description.html}</td></tr>
                        <tr><td></td><td >&nbsp;</td></tr>
                        <tr><td></td><td class = "submitCell">{$T_LINKS_FORM.submit_link.html}</td></tr>
                    </table>
                </form>

    {/capture}
    {eF_template_printBlock title=$smarty.const._LINKS_INSERTLINK data=$smarty.capture.t_insert_link_code absoluteImagePath = 1 image = $T_LINKS_BASELINK|cat:'images/link.png'}
{else}
    {capture name = 't_links_list_code'}
        {if $smarty.session.s_type != 'student'}
                    <table>
                        <tr><td>
                            <a href = "{$T_LINKS_BASEURL}&add_link=1"><img src = "images/16x16/add.png" alt = "{$smarty.const._LINKS_ADDLINK}" title = "{$smarty.const._LINKS_ADDLINK}" border = "0" /></a>
                        </td><td>
                            <a href = "{$T_LINKS_BASEURL}&add_link=1" title = "{$smarty.const._LINKS_ADDLINK}">{$smarty.const._LINKS_ADDLINK}</a>
                        </td></tr>
                    </table>
        {/if}
                    <div class = "blockHeader" style = "margin-top:10px">{$smarty.const._LINKS}</div>
                    <table style = "margin-left:30px;padding:3px 3px 3px 3px;">
                    {section name = 'links_list' loop = $T_LINKS.id}
                        <tr><td>{$smarty.section.links_list.iteration}. 
                                <a href = "{$T_LINKS.link[links_list]}">{$T_LINKS.display[links_list]}</a>
                            </td>
                        {if $smarty.session.s_type != 'student'}
                            <td><a href = "{$T_LINKS_BASEURL}&edit_link={$T_LINKS.id[links_list]}"><img src = "images/16x16/edit.png" alt = "{$smarty.const._LINKS_EDITLINK}" title = "{$smarty.const._LINKS_EDITLINK}" border = "0"/></a>
                                <a href = "{$T_LINKS_BASEURL}&delete_link={$T_LINKS.id[links_list]}" onclick = "return confirm ('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')"><img src = "images/16x16/error_delete.png" alt = "{$smarty.const._LINKS_DELETELINK}" title = "{$smarty.const._LINKS_DELETELINK}" border = "0"/></a>
                            </td>
                        {/if}
                        </tr>
                        <tr>
                            <td><i>&nbsp;&nbsp;{$T_LINKS.description[links_list]}</i></td>
                        </tr>
                    {/section}
                    </table>
    {/capture}
    {eF_template_printBlock title=$smarty.const._LINKS_LINKLIST data=$smarty.capture.t_links_list_code absoluteImagePath = 1 image = $T_LINKS_BASELINK|cat:'images/link.png'}
{/if}


