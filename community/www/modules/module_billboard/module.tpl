{*Smarty template*}
{capture name = 't_billboard_list_code'}
    {$T_BILLBOARD_FORM.javascript}
    <form {$T_BILLBOARD_FORM.attributes}>
        {$T_BILLBOARD_FORM.hidden}
        <table>
            <tr><td style = "vertical-align:middle"><a title="{$smarty.const._UPLOADFILESANDIMAGES}" href = "{$smarty.server.PHP_SELF}?ctg=file_manager&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._UPLOADFILESANDIMAGES}', 3)" target = "POPUP_FRAME"  style = "vertical-align:middle"><img src = "images/16x16/export.png" title = "{$smarty.const._UPLOADFILESANDIMAGES}" alt = "{$smarty.const._UPLOADFILESANDIMAGES}" style = "vertical-align:middle" border = "0"/>&nbsp;<span  style = "vertical-align:middle">{$smarty.const._UPLOADFILESANDIMAGES}</span></a></td></tr>
        </table>

        <table class = "formElements" style = "width:100%;margin-left:0px;">
            <tr id = "editorRow" >
                <td class = "elementCell" >
                    {$T_BILLBOARD_FORM.data.html}
                </td></tr>
            {if $T_BILLBOARD_FORM.data.error}<tr><td></td><td class = "formError">{$T_BILLBOARD_FORM.data.error}</td></tr>{/if}

            <tr><td>&nbsp;</td></tr>
            <tr><td colspan = "100%" class = "submitCell">
                {$T_BILLBOARD_FORM.submit_billboard.html}</td></tr>
        </table>
    </form>
{/capture}
{eF_template_printBlock title=$smarty.const._BILLBOARD data=$smarty.capture.t_billboard_list_code absoluteImagePath=1 image=$T_BILLBOARD_MODULE_BASELINK|cat:'images/note_pinned32.png'}



