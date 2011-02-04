{capture name='t_textme_tab'}

    {if $smarty.get.subcat == 'account' && $smarty.get.cmd == 'edit'}

{*******************************************************************************
    Edit lesson account page
*******************************************************************************}

        {assign var='t_textme_title' value=$smarty.const._TEXTME_EDITLESSONACCOUNT}
        {assign var='t_textme_image' value='assets/images/32/edit.png'}

        <div class="headerTools">
            <span>
                <img src="{$T_TEXTME_BASELINK}assets/images/16/go_back.png"
                     title="{$smarty.const._TEXTME_BACKTOLESSONSACCOUNTS}"
                     alt="{$smarty.const._TEXTME_BACKTOLESSONSACCOUNTS}">
                <a href="{$T_TEXTME_BASEURL}&cat=lessons"
                   title="{$smarty.const._TEXTME_BACKTOLESSONSACCOUNTS}">
                    {$smarty.const._TEXTME_BACKTOLESSONSACCOUNTS}</a>
            </span>
        </div>
        <br/>

        {$T_TEXTME_FORM.javascript}
        <form {$T_TEXTME_FORM.attributes}>
            {$T_TEXTME_FORM.hidden}
            <table>
                <tr>
                    <td class = "labelCell">{$smarty.const._NAME}:</td>
                    <td class = "elementCell">{$T_TEXTME_LESSONNAME}</td>
                </tr>
                <tr>
                    <td class = "labelCell">{$smarty.const._TEXTME_SENDERID}:</td>
                    <td class = "elementCell">{$T_TEXTME_LESSONALIAS}</td>
                </tr>
                <tr>
                    <td class = "labelCell">{$T_TEXTME_FORM.credits.label}:&nbsp;</td>
                    <td class = "elementCell">{$T_TEXTME_FORM.credits.html}</td>
                </tr>
                <tr>
                    <td class = "labelCell">&nbsp;</td>
                    <td class = "infoCell">{$smarty.const._TEXTME_LEAVEBLANKFORUNLIMITEDCREDITS}</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>{$T_TEXTME_FORM.submit.html}</td>
                </tr>
            </table>
        </form>

    {else}

{*******************************************************************************
    Lessons accounts list page
*******************************************************************************}

        {assign var='t_textme_title' value=$smarty.const._TEXTME_LESSONSACCOUNTSTAB}
        {assign var='t_textme_image' value='assets/images/32/lessons.png'}

<!--ajax:lessons-table-->
        <table
            id="lessons-table"
            style="width:100%"
            class="sortedTable"
            sortBy="0"
            size="{$T_TEXTME_ITEMS_COUNT}"
            useAjax="1"
            rowsPerPage="20"
            url="{$T_TEXTME_BASEURL}&cat=lessons&">

            <tr class="topTitle">
                <td class="topTitle" name="name">{$smarty.const._NAME}</td>
                <td class="topTitle" name="alias">{$smarty.const._TEXTME_SENDERID}</td>
                <td class="topTitle centerAlign" name="credits">{$smarty.const._TEXTME_CREDITSREMAINING}</td>
                <td class="topTitle centerAlign" name="credits_spent">{$smarty.const._TEXTME_CREDITSSPENT}</td>
                <td class="topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
            </tr>

            {foreach from=$T_TEXTME_ITEMS item=T_TEXTME_ITEM}

            <tr class="{cycle values='oddRowColor,evenRowColor'}  {if $T_TEXTME_ITEM.module_textme == false}deactivatedTableElement{/if}">
                <td>{$T_TEXTME_ITEM.name}</td>
                <td>{$T_TEXTME_ITEM.alias}</td>
                <td class="centerAlign">
                    {if $T_TEXTME_ITEM.credits == NULL}
                    {$smarty.const._TEXTME_UNLIMITEDCREDITS}
                    {else}
                    {$T_TEXTME_ITEM.credits}
                    {/if}
                </td>
                <td class="centerAlign">{$T_TEXTME_ITEM.credits_spent}</td>
                <td class="centerAlign">
                    {if $T_TEXTME_ITEM.module_textme == true}
                    <a href="{$T_TEXTME_BASEURL}&cat=lessons&subcat=account&cmd=edit&item={$T_TEXTME_ITEM.id}" title="{$smarty.const._TEXTME_EDITLESSONACCOUNT}">
                        <img src="{$T_TEXTME_BASELINK}assets/images/16/edit.png" title="{$smarty.const._TEXTME_EDITLESSONACCOUNT}" alt="{$smarty.const._TEXTME_EDITLESSONACCOUNT}">
                    </a>
                    {else}
                    {$smarty.const._TEXTME_MODULEDISABLED}
                    {/if}
                </td>
            </tr>

            {foreachelse}

            <tr>
                <td colspan="5">{$smarty.const._TEXTME_NOLESSONSACCOUNTS}</td>
            </tr>

            {/foreach}

        </table>
<!--/ajax:lessons-table-->

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
