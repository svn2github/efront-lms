{capture name='t_textme_tab'}

    {if $smarty.get.subcat == 'account' && $smarty.get.cmd == 'add'}

{*******************************************************************************
    Add sms gateway page
*******************************************************************************}

    {assign var='t_textme_title' value=$smarty.const._TEXTME_ADDSMSGATEWAY}
    {assign var='t_textme_image' value='assets/images/32/add.png'}

    <div class="headerTools">
        <span>
            <img src="{$T_TEXTME_BASELINK}assets/images/16/go_back.png"
                 title="{$smarty.const._TEXTME_BACKTOSMSGATEWAYS}"
                 alt="{$smarty.const._TEXTME_BACKTOSMSGATEWAYS}">
            <a href="{$T_TEXTME_BASEURL}&cat=gateways"
               title="{$smarty.const._TEXTME_BACKTOSMSGATEWAYS}">
                {$smarty.const._TEXTME_BACKTOSMSGATEWAYS}</a>
        </span>
    </div>
    <br/>

    {$T_TEXTME_FORM.javascript}
    <form {$T_TEXTME_FORM.attributes}>
        {$T_TEXTME_FORM.hidden}
        <table>
            <tr>
                <td class = "labelCell">{$T_TEXTME_FORM.type.label}:</td>
                <td class = "elementCell">{$T_TEXTME_FORM.type.html}</td>
            </tr>
            <tr>
                <td class = "labelCell">{$T_TEXTME_FORM.name.label}:</td>
                <td class = "elementCell">{$T_TEXTME_FORM.name.html}</td>
            </tr>
            <tr>
                <td class = "labelCell">{$T_TEXTME_FORM.data.label}:</td>
                <td class = "elementCell">{$T_TEXTME_FORM.data.html}</td>
            </tr>
            <tr>
                <td class = "labelCell"></td>
                <td class = "infoCell">{$smarty.const._TEXTME_PARAMETERSEXPLANATION}</td>
            </tr>
            <tr>
                <td class = "labelCell">&nbsp;</td>
                <td class = "infoCell">{$T_TEXTME_FORM.requirednote}</td>
            </tr>
            <tr>
                <td class = "labelCell">&nbsp;</td>
                <td>{$T_TEXTME_FORM.submit.html}</td>
            </tr>
        </table>
    </form>

    {elseif $smarty.get.subcat == 'account' && $smarty.get.cmd == 'edit'}

{*******************************************************************************
    Edit sms gateway page
*******************************************************************************}

    {assign var='t_textme_title' value=$smarty.const._TEXTME_EDITSMSGATEWAY}
    {assign var='t_textme_image' value='assets/images/32/edit.png'}

    <div class="headerTools">
        <span>
            <img src="{$T_TEXTME_BASELINK}assets/images/16/go_back.png"
                 title="{$smarty.const._TEXTME_BACKTOSMSGATEWAYS}"
                 alt="{$smarty.const._TEXTME_BACKTOSMSGATEWAYS}">
            <a href="{$T_TEXTME_BASEURL}&cat=gateways"
               title="{$smarty.const._TEXTME_BACKTOSMSGATEWAYS}">
                {$smarty.const._TEXTME_BACKTOSMSGATEWAYS}</a>
        </span>
    </div>
    <br/>

    {$T_TEXTME_FORM.javascript}
    <form {$T_TEXTME_FORM.attributes}>
        {$T_TEXTME_FORM.hidden}
        <table>
            <tr>
                <td class = "labelCell">{$T_TEXTME_FORM.type.label}:&nbsp;</td>
                <td class = "elementCell">{$T_TEXTME_FORM.type.html}</td>
            </tr>
            <tr>
                <td class = "labelCell">{$T_TEXTME_FORM.name.label}:&nbsp;</td>
                <td class = "elementCell">{$T_TEXTME_FORM.name.html}</td>
            </tr>
            <tr>
                <td class = "labelCell">{$T_TEXTME_FORM.data.label}:&nbsp;</td>
                <td class = "elementCell">{$T_TEXTME_FORM.data.html}</td>
            </tr>
            <tr>
                <td class = "labelCell"></td>
                <td class = "infoCell">{$smarty.const._TEXTME_PARAMETERSEXPLANATION}</td>
            </tr>
            <tr>
                <td class = "labelCell">&nbsp;</td>
                <td class = "infoCell">{$T_TEXTME_FORM.requirednote}</td>
            </tr>
            <tr>
                <td class = "labelCell">&nbsp;</td>
                <td>{$T_TEXTME_FORM.submit.html}</td>
            </tr>
        </table>
    </form>

    {else}

{*******************************************************************************
    Sms gateways list page
*******************************************************************************}

    {assign var='t_textme_title' value=$smarty.const._TEXTME_SMSGATEWAYSTAB}
    {assign var='t_textme_image' value='assets/images/32/generic.png'}

    <div class="headerTools">
        <span>
            <img src="{$T_TEXTME_BASELINK}assets/images/16/add.png" title="{$smarty.const._TEXTME_ADDSMSGATEWAY}" alt="{$smarty.const._TEXTME_ADDSMSGATEWAY}">
            <a href="{$T_TEXTME_BASEURL}&cat=gateways&subcat=account&cmd=add" title="{$smarty.const._TEXTME_ADDSMSGATEWAY}">{$smarty.const._TEXTME_ADDSMSGATEWAY}</a>
        </span>
    </div>

<!--ajax:gateways-table-->
    <table
        id="gateways-table"
        style="width:100%"
        class="sortedTable"
        sortBy="0"
        size="{$T_TEXTME_ITEMS_COUNT}"
        useAjax="1"
        rowsPerPage="20"
        url="{$T_TEXTME_BASEURL}&cat=gateways&">

        <tr class="topTitle">
            <td class="topTitle" name="name">{$smarty.const._NAME}</td>
            <td class="topTitle" name="type">{$smarty.const._TEXTME_SMSGATEWAYURL}</td>
            <td class="topTitle" name="data">{$smarty.const._TEXTME_PARAMETERS}</td>
            <td class="topTitle centerAlign" name="is_active">{$smarty.const._ACTIVE}</td>
            <td class="topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
        </tr>

        {foreach from=$T_TEXTME_ITEMS item=T_TEXTME_ITEM}

        <tr class="{cycle values='oddRowColor,evenRowColor'}  {if $T_TEXTME_ITEM.is_active == false}deactivatedTableElement{/if}">
            <td>{$T_TEXTME_ITEM.name}</td>
            <td>{$T_TEXTME_ITEM.type}</td>
            <td>{$T_TEXTME_ITEM.data}</td>
            <td class="centerAlign">
                {if $T_TEXTME_ITEM.is_active == true}
                <a href="{$T_TEXTME_BASEURL}&cat=gateways&subcat=account&cmd=deactivate&item={$T_TEXTME_ITEM.id}" title="{$smarty.const._ACTIVE}">
                    <img src="{$T_TEXTME_BASELINK}assets/images/16/trafficlight_green.png" title="{$smarty.const._ACTIVE}" alt="{$smarty.const._ACTIVE}">
                </a>
                {else}
                <a href="{$T_TEXTME_BASEURL}&cat=gateways&subcat=account&cmd=activate&item={$T_TEXTME_ITEM.id}" title="{$smarty.const._INACTIVE}">
                    <img src="{$T_TEXTME_BASELINK}assets/images/16/trafficlight_red.png" title="{$smarty.const._INACTIVE}" alt="{$smarty.const._INACTIVE}">
                </a>
                {/if}
            </td>
            <td class="centerAlign">
                <a href="{$T_TEXTME_BASEURL}&cat=gateways&subcat=account&cmd=test&item={$T_TEXTME_ITEM.id}" title="{$smarty.const._TEXTME_TESTSMSGATEWAY}">
                    <img src="{$T_TEXTME_BASELINK}assets/images/16/email.png" title="{$smarty.const._TEXTME_TESTSMSGATEWAY}" alt="{$smarty.const._TEXTME_TESTSMSGATEWAY}">
                </a>
                <a href="{$T_TEXTME_BASEURL}&cat=gateways&subcat=account&cmd=edit&item={$T_TEXTME_ITEM.id}" title="{$smarty.const._TEXTME_EDITSMSGATEWAY}">
                    <img src="{$T_TEXTME_BASELINK}assets/images/16/edit.png" title="{$smarty.const._TEXTME_EDITSMSGATEWAY}" alt="{$smarty.const._TEXTME_EDITSMSGATEWAY}">
                </a>
                <a href="{$T_TEXTME_BASEURL}&cat=gateways&subcat=account&cmd=delete&item={$T_TEXTME_ITEM.id}" title="{$smarty.const._TEXTME_DELETESMSGATEWAY}">
                    <img src="{$T_TEXTME_BASELINK}assets/images/16/error_delete.png" title="{$smarty.const._TEXTME_DELETESMSGATEWAY}" alt="{$smarty.const._TEXTME_DELETESMSGATEWAY}">
                </a>
            </td>
        </tr>

        {foreachelse}

        <tr>
            <td colspan="5">{$smarty.const._TEXTME_NOSMSGATEWAYS}</td>
        </tr>

        {/foreach}

    </table>
<!--/ajax:gateways-table-->

    {/if}

{/capture}

{*******************************************************************************
    Tabs
*******************************************************************************}

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
