{* smarty template for add_definition.php *}

{include file = "includes/header.tpl"}

{literal}
<script language = "JavaScript" type = "text/javascript" src = "js/effects.js"></script>
<script>

function ajaxPostTerm(el) {
    // Check whether both fields are completed
    if (!document.getElementById('mce_editor_0').contentWindow.frames.document.body.innerHTML) {
        alert('{/literal}{$smarty.const._DEFINITIONFIELDISMANDATORY}{literal}');
        return false;
    } else if (document.getElementById('termField').value == '') {
        alert('{/literal}{$smarty.const._TERMFIELDISMANDATORY}{literal}');
        return false;
    } else {
        var url =  'add_definition.php?add=1&postAjaxRequest=1&term=' + encodeURI(document.getElementById('termField').value) + '&definition=' + encodeURI(document.getElementById('mce_editor_0').contentWindow.frames.document.body.innerHTML);

        var img_id = "img_ok";
        var position = eF_js_findPos(el);
        var img      = document.createElement("img");

        img.style.position = 'absolute';
        img.style.top      = Element.positionedOffset(Element.extend(el)).top  + 'px';
        img.style.left     = Element.positionedOffset(Element.extend(el)).left + 6 + Element.getDimensions(Element.extend(el)).width + 'px';

        img.setAttribute("id", img_id);
        img.setAttribute('src', 'images/others/progress1.gif');

        el.parentNode.appendChild(img);

        new Ajax.Request(url, {
                method:'get',
                asynchronous:true,
                onSuccess: function (transport) {
                        img.style.display = 'none';
                        img.setAttribute('src', 'images/16x16/check.png');
                        new Effect.Appear(img_id);
                        window.setTimeout('Effect.Fade("'+img_id+'")', 2500);
                        document.getElementById('mce_editor_0').contentWindow.frames.document.body.innerHTML = "";
                        document.getElementById('termField').value = "";
                    }
            });

        parent.document.getElementById('reloadHidden').value = 1;
    }
}
</script>
{/literal}

{if $T_MESSAGE}
    {if $T_MESSAGE_TYPE == 'success'}
        <script>
            re = /\?/;
            !re.test(parent.location) ? parent.location = parent.location+'?message={$T_MESSAGE}&message_type={$T_MESSAGE_TYPE}' : parent.location = parent.location+'&message={$T_MESSAGE}&message_type={$T_MESSAGE_TYPE}';
        </script>
    {else}
        {eF_template_printMessage message = $T_MESSAGE type = $T_MESSAGE_TYPE}
    {/if}
{/if}


{$T_GLOSSARY_FORM.javascript}
<form {$T_GLOSSARY_FORM.attributes}>
    {$T_GLOSSARY_FORM.hidden}
    <table class = "formElements">
        <tr><td class = "labelCell">{$T_GLOSSARY_FORM.term.label}:&nbsp;</td>
            <td class = "elementCell">{$T_GLOSSARY_FORM.term.html}</td></tr>
        {if $T_GLOSSARY_FORM.term.error}<tr><td></td><td class = "formError">{$T_GLOSSARY_FORM.term.error}</td></tr>{/if}

        <tr><td class = "labelCell">{$T_GLOSSARY_FORM.definition.label}:&nbsp;</td>
            <td class = "elementCell">{$T_GLOSSARY_FORM.definition.html}</td></tr>
        {if $T_GLOSSARY_FORM.definition.error}<tr><td></td><td class = "formError">{$T_GLOSSARY_FORM.definition.error}</td></tr>{/if}
        <tr><td colspan = "100%">&nbsp;</td></tr>
        <tr><td></td><td><table><tr><td class = "submitCell">{$T_GLOSSARY_FORM.submit_term.html}</td>
            <td align="center">{$T_GLOSSARY_FORM.submit_term_add_another.html}</td>
            </tr></table></td></tr>
    </table>
</form>
