{*Smarty template*}

{if $smarty.get.add_quote || $smarty.get.edit_quote}
    {capture name = 't_insert_quote_code'}
                {$T_QUOTE_FORM.javascript}
                <form {$T_QUOTE_FORM.attributes}>
                    {$T_QUOTE_FORM.hidden}
                    <table class = "formElements" style = "width:100%">
                        <tr><td class = "labelCell">{$smarty.const._QUOTE_QUOTE}:&nbsp;</td>
                            <td class = "elementCell">{$T_QUOTE_FORM.quote.html}</td></tr>
                        <tr><td></td><td >&nbsp;</td></tr>
                        <tr><td></td><td class = "submitCell">{$T_QUOTE_FORM.submit_quote.html}</td></tr>
                    </table>
                </form>

    {/capture}
    {eF_template_printBlock title=$smarty.const._QUOTE_ADDQUOTE data=$smarty.capture.t_insert_quote_code image='32x32/quote.png'}
{else}
    {capture name = 't_quote_list_code'}
        {if $smarty.session.s_type != 'student'}
                    <table>
                        <tr><td>
                            <a href = "{$T_QUOTE_BASEURL}&add_quote=1"><img src = "images/16x16/add.png" alt = "{$smarty.const._QUOTE_ADDQUOTE}" title = "{$smarty.const._QUOTE_ADDQUOTE}" border = "0" /></a>
                        </td><td>
                            <a href = "{$T_QUOTE_BASEURL}&add_quote=1" title = "{$smarty.const._QUOTE_ADDQUOTE}">{$smarty.const._QUOTE_ADDQUOTE}</a>
                        </td></tr>
                    </table>
                    <div class = "blockHeader" style = "margin-top:10px">{$smarty.const._QUOTE_QUOTELIST}</div>
                    <table style = "margin-left:30px;padding:3px 3px 3px 3px;">
                    {section name = 'quotes_list' loop = $T_QUOTES.id}
                        <tr>
                            <td>{$smarty.section.quotes_list.iteration}.&nbsp;<b><i>{$T_QUOTES.quote[quotes_list]}</i><b></td>
                        <td><a href = "{$T_QUOTE_BASEURL}&edit_quote={$T_QUOTES.id[quotes_list]}"><img src = "images/16x16/edit.png" alt = "{$smarty.const._QUOTE_EDITQUOTE}" title = "{$smarty.const._QUOTE_EDITQUOTE}" border = "0"/></a>
                                <a href = "{$T_QUOTE_BASEURL}&delete_quote={$T_QUOTES.id[quotes_list]}" onclick = "return confirm ('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')"><img src = "images/16x16/error_delete.png" alt = "{$smarty.const._QUOTE_DELETEQUOTE}" title = "{$smarty.const._QUOTE_DELETEQUOTE}" border = "0"/></a>
                        </td>
                        </tr>
                    {/section}
                    </table>
        {else}
            <table>
                <tr>
                    <td><i>{$T_QUOTE}</i></td>
                </tr>
            </table>
        {/if}
                    
    {/capture}
    {eF_template_printBlock title=$smarty.const._QUOTE_QUOTEDAY data=$smarty.capture.t_quote_list_code image= '32x32/quote.png'}
{/if}


