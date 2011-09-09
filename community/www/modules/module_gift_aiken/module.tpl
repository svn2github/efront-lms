{*Smarty template*}

{capture name = 't_insert_faq_code'}
            {$T_GIFTAIKENQUESTIONS_FORM.javascript}
            <form {$T_GIFTAIKENQUESTIONS_FORM.attributes}>
                {$T_GIFTAIKENQUESTIONS_FORM.hidden}
                <table class = "formElements" style = "width:100%">
                    <tr><td class = "labelCell">{$T_GIFTAIKENQUESTIONS_FORM.questions_format.gift.label}:&nbsp;</td>
                        <td class = "elementCell">{$T_GIFTAIKENQUESTIONS_FORM.questions_format.gift.html}</td></tr>
                    <tr><td class = "labelCell">{$T_GIFTAIKENQUESTIONS_FORM.questions_format.aiken.label}:&nbsp;</td>
                        <td class = "elementCell">{$T_GIFTAIKENQUESTIONS_FORM.questions_format.aiken.html}</td></tr>
                    <tr><td colspan = "2">&nbsp;</td></tr>


                    <tr><td class = "labelCell">{$smarty.const._GIFTAIKEN_IMPORTINTOUNIT}:&nbsp;</td>
                        <td class = "elementCell">
                            <select name = "select_unit">
                                <option value = "-1" {if $smarty.get.from_unit == -1}selected{/if}>{$smarty.const._ALLUNITS}</option>
                                <option value = "-2">-----------</option>
                            {foreach name = 'unit_options' key = 'id' item = 'unit' from = $T_UNITS}
                                <option value = "{$id}" {if $id == $smarty.post.select_unit}selected{/if}>{$unit}</option>
                            {/foreach}
                            </select>
                        </td></tr>
                    <tr><td colspan = "2">&nbsp;</td></tr>
                    <!--
                    <tr><td></td><td>
                    <table><tr><td>
                    <a id="hiddenPopupLink" href="{$T_GIFTAIKENQUESTIONS_MODULE_BASEURL}&preview=1" onclick = "eF_js_showDivPopup('{$smarty.const._ADDSKILLCATEGORY}', 2)" target = "POPUP_FRAME" ><img style="vertical-align:top" border="0" src= "images/16x16/search.png" /></a>
                    </td><td>
                    <a id="hiddenPopupLink" href="{$T_GIFTAIKENQUESTIONS_MODULE_BASEURL}&preview=1" onclick = "eF_js_showDivPopup('{$smarty.const._ADDSKILLCATEGORY}', 2)" target = "POPUP_FRAME" >{$smarty.const._PREVIEW}</a>
                    </td></tr></table>

                    </td></tr>
                    -->
                    {if $T_PREVIEW_DIV}
                    <tr><td class = "labelCell" style="vertical-align:top">{if isset($smarty.post.submit)}{$smarty.const._GIFTAIKEN_SUBMITTEDQUESTIONS}{else}{$smarty.const._PREVIEW}{/if}:&nbsp;</td>
                        <td class = "elementCell">{$T_PREVIEW_DIV}</td></tr>
                    {/if}
                    <tr><td class = "labelCell">{$T_GIFTAIKENQUESTIONS_FORM.imported_data.label}:&nbsp;</td>
                        <td class = "elementCell">{$T_GIFTAIKENQUESTIONS_FORM.imported_data.html}</td></tr>
                    <tr><td></td><td >&nbsp;</td></tr>
                    <!--<tr><td></td><td class = "submitCell">{$T_GIFTAIKENQUESTIONS_FORM.submit.html}</td></tr>-->
                    <tr><td></td><td class = "submitCell"><table><tr><td>{$T_GIFTAIKENQUESTIONS_FORM.preview.html}</td><td>{$T_GIFTAIKENQUESTIONS_FORM.submit.html}</td></tr></table></tr>
                </table>
            </form>

{/capture}

{eF_template_printBlock title=$smarty.const._GIFTAIKENIMPORTQUESTIONS data=$smarty.capture.t_insert_faq_code absoluteImagePath=1 image=$T_GIFTAIKENQUESTIONS_MODULE_BASELINK|cat:'images/transform32.png' help = 'Gift/Gift//Aiken'}

{if isset($smarty.post.questions_format)}
 {literal}
 <script>
 var modulegiftquestiontype = '{/literal}{$smarty.post.questions_format}{literal}';
 {/literal}
 </script>
{else}
 <script>
 var modulegiftquestiontype = 'gift';
 </script>
{/if}
