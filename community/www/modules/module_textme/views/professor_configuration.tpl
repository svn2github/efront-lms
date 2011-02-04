{capture name="t_textme_tab"}

    {assign var='t_textme_title' value=$smarty.const._TEXTME_CONFIGURATION}
    {assign var='t_textme_image' value='assets/images/32/generic.png'}

    {$T_TEXTME_FORM.javascript}
    <form {$T_TEXTME_FORM.attributes}>
        {$T_TEXTME_FORM.hidden}
        <table>
            <tr>
                <td class = "labelCell">{$T_TEXTME_FORM.alias.label}:</td>
                <td class = "elementCell">{$T_TEXTME_FORM.alias.html}</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td class = "infoCell">
                    eg. CS100, MATH101, CHEM305
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td class = "infoCell">{$T_TEXTME_FORM.requirednote}</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td class = "elementCell">{$T_TEXTME_FORM.submit.html}</td>
            </tr>
        </table>
    </form>


{/capture}

{capture name = 't_textme_tabs'}
<div class="tabberlive">
    <ul class="tabbernav">
    {foreach from=$T_TEXTME_TABS key=T_TEXTME_CATEGORY item=T_TEXTME_LABEL}
        {if $smarty.get.cat == $T_TEXTME_CATEGORY || ( $smarty.get.cat=='' && $T_TEXTME_TAB_DEFAULT == $T_TEXTME_CATEGORY )}
        <li class="tabberactive"><a href="{$T_TEXTME_BASEURL}&amp;cat={$T_TEXTME_CATEGORY}">{$T_TEXTME_LABEL}</a></li>
        {else}
        <li><a href="{$T_TEXTME_BASEURL}&amp;cat={$T_TEXTME_CATEGORY}">{$T_TEXTME_LABEL}</a></li>
        {/if}
    {/foreach}
    </ul>
    <div class="tabbertab">
    {eF_template_printBlock
        title=$t_textme_title
        data=$smarty.capture.t_textme_tab
        absoluteImagePath=1
        image=$T_TEXTME_BASELINK|cat:$t_textme_image
        options = $T_TEXTME_OPTIONS}
    </div>
</div>
{/capture}

{eF_template_printInnerTable
    title=$smarty.const._TEXTME
    data=$smarty.capture.t_textme_tabs
    absoluteImagePath=1
    image=$T_TEXTME_BASELINK|cat:'assets/images/32/logo.png'}
