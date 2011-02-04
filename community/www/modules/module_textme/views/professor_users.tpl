{capture name="t_textme_tab"}

{if $smarty.get.subcat == 'group' && $smarty.get.cmd == 'add'}

    {assign var='t_textme_title' value=$smarty.const._TEXTME_ADDGROUP}
    {assign var='t_textme_image' value='assets/images/32/add.png'}

    <div class="headerTools">
        <span>
            <img src="{$T_TEXTME_BASELINK}assets/images/16/go_back.png"
                 title="{$smarty.const._TEXTME_BACKTOUSERSANDGROUPS}"
                 alt="{$smarty.const._TEXTME_BACKTOUSERSANDGROUPS}">
            <a href="{$T_TEXTME_BASEURL}&cat=users"
               title="{$smarty.const._TEXTME_BACKTOUSERSANDGROUPS}">
                {$smarty.const._TEXTME_BACKTOUSERSANDGROUPS}</a>
        </span>
    </div>
    <br/>

    {$T_TEXTME_FORM.javascript}
    <form {$T_TEXTME_FORM.attributes}>
        {$T_TEXTME_FORM.hidden}
        <table>
            <tr>
                <td>{$T_TEXTME_FORM.name.label}:&nbsp;</td>
                <td class = "elementCell">{$T_TEXTME_FORM.name.html}</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <div class="textme-groups">
                        <div>
                            <label for="module_textme_in_group">{$T_TEXTME_FORM.in_group.label}:</label>
                            <br/>
                            {$T_TEXTME_FORM.in_group.html}
                        </div>
                        <div>
                            <br/>
                            <button id="module_textme_group_remove" type="button" class="flatButton">&gt;&gt;</button>
                            <br/>
                            <button id="module_textme_group_add" type="button" class="flatButton">&lt;&lt;</button>
                        </div>
                        <div>
                            <label for="module_textme_out_group">{$T_TEXTME_FORM.out_group.label}:</label>
                            <br/>
                            {$T_TEXTME_FORM.out_group.html}
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td></td>
                <td class = "infoCell">{$T_TEXTME_FORM.requirednote}</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>{$T_TEXTME_FORM.submit.html}</td>
            </tr>
        </table>
    </form>

{elseif $smarty.get.subcat == 'group' && $smarty.get.cmd == 'edit'}

    {assign var='t_textme_title' value=$smarty.const._TEXTME_EDITGROUP}
    {assign var='t_textme_image' value='assets/images/32/edit.png'}

    <div class="headerTools">
        <span>
            <img src="{$T_TEXTME_BASELINK}assets/images/16/go_back.png"
                 title="{$smarty.const._TEXTME_BACKTOUSERSANDGROUPS}"
                 alt="{$smarty.const._TEXTME_BACKTOUSERSANDGROUPS}">
            <a href="{$T_TEXTME_BASEURL}&cat=users"
               title="{$smarty.const._TEXTME_BACKTOUSERSANDGROUPS}">
                {$smarty.const._TEXTME_BACKTOUSERSANDGROUPS}</a>
        </span>
    </div>
    <br/>

    {$T_TEXTME_FORM.javascript}
    <form {$T_TEXTME_FORM.attributes}>
        {$T_TEXTME_FORM.hidden}
        <table>
            <tr>
                <td>{$T_TEXTME_FORM.name.label}:&nbsp;</td>
                <td class = "elementCell">{$T_TEXTME_FORM.name.html}</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <div class="textme-groups">
                        <div>
                            <label for="textme_in_group">{$T_TEXTME_FORM.in_group.label}:</label>
                            <br/>
                            {$T_TEXTME_FORM.in_group.html}
                        </div>
                        <div>
                            <br/>
                            <button id="module_textme_group_remove" type="button" class="flatButton">&gt;&gt;</button>
                            <br/>
                            <button id="module_textme_group_add" type="button" class="flatButton">&lt;&lt;</button>
                        </div>
                        <div>
                            <label for="textme_out_group">{$T_TEXTME_FORM.out_group.label}:</label>
                            <br/>
                            {$T_TEXTME_FORM.out_group.html}
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td></td>
                <td class = "infoCell">{$T_TEXTME_FORM.requirednote}</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>{$T_TEXTME_FORM.submit.html}</td>
            </tr>
        </table>
    </form>

{else}

    {assign var='t_textme_title' value=$smarty.const._TEXTME_USERSANDGROUPS}
    {assign var='t_textme_image' value='assets/images/32/users.png'}

<!--ajax:users-table-->
    <table
        id="users-table"
        style="width:100%"
        class="sortedTable"
        sortBy="0"
        size="{$T_TEXTME_ITEMS_COUNT}"
        useAjax="1"
        rowsPerPage="20"
        url="{$T_TEXTME_BASEURL}&cat=users&">

        <tr class="topTitle">
            <td class="topTitle" name="login">{$smarty.const._TEXTME_USER}</td>
            <td class="topTitle centerAlign" name="is_subscribed">{$smarty.const._TEXTME_ALERTSSTATUS}</td>
            <td class="topTitle centerAlign" name="role">{$smarty.const._TEXTME_ROLE}</td>
        </tr>

        {foreach from=$T_TEXTME_ITEMS item=T_TEXTME_ITEM}

        <tr class="{cycle values='oddRowColor,evenRowColor'}">
            <td>#filter:login-{$T_TEXTME_ITEM.login}#</td>
            <td class="centerAlign">
                {if $T_TEXTME_ITEM.is_subscribed}
                    <img src="{$T_TEXTME_BASELINK}assets/images/16/success.png"
                         title="{$smarty.const._TEXTME_HASENABLEDALERTS}"
                         alt="{$smarty.const._TEXTME_HASENABLEDALERTS}">
                {else}
                    <img src="{$T_TEXTME_BASELINK}assets/images/16/forbidden.png"
                         title="{$smarty.const._TEXTME_HASNOTENABLEDALERTS}"
                         alt="{$smarty.const._TEXTME_HASNOTENABLEDALERTS}">
                {/if}
            </td>
            <td class="centerAlign">{$T_TEXTME_ITEM.role}</td>
        </tr>

        {foreachelse}

        <tr>
            <td colspan="3">{$smarty.const._TEXME_NOUSERS}</td>
        </tr>

        {/foreach}

    </table>
<!--/ajax:users-table-->

    <br/>

    <div class="headerTools">
        <span>
            <img src="{$T_TEXTME_BASELINK}assets/images/16/add.png"
                 title="{$smarty.const._TEXTME_ADDGROUP}"
                 alt="{$smarty.const._TEXTME_ADDGROUP}">
            <a href="{$T_TEXTME_BASEURL}&cat=users&subcat=group&cmd=add"
               title="{$smarty.const._TEXTME_ADDGROUP}">
                {$smarty.const._TEXTME_ADDGROUP}</a>
        </span>
    </div>

<!--ajax:groups-table-->
    <table
        id="groups-table"
        style="width:100%"
        class="sortedTable"
        sortBy="0"
        size="{$T_TEXTME_ITEMS_COUNT}"
        useAjax="1"
        rowsPerPage="20"
        url="{$T_TEXTME_BASEURL}&cat=users&">

        <tr class="topTitle">
            <td class="topTitle" name="name">{$smarty.const._TEXTME_GROUP}</td>
            <td class="topTitle centerAlign" name="count">{$smarty.const._TEXTME_NUMBEROFUSERS}</td>
            <td class="topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
        </tr>

        {foreach from=$T_TEXTME_ITEMS item=T_TEXTME_ITEM}
        <tr class="{cycle values='oddRowColor,evenRowColor'}">
            <td>{$T_TEXTME_ITEM.name}</td>
            <td class="centerAlign">{$T_TEXTME_ITEM.count}</td>
            <td class="centerAlign">
                <a href="{$T_TEXTME_BASEURL}&cat=users&subcat=group&cmd=edit&item={$T_TEXTME_ITEM.id}"
                   title="{$smarty.const._TEXTME_EDITGROUP}">
                    <img src="{$T_TEXTME_BASELINK}assets/images/16/edit.png"
                         alt="{$smarty.const._TEXTME_EDITGROUP}"
                         title="{$smarty.const._TEXTME_EDITGROUP}"/>
                </a>
                <a href="{$T_TEXTME_BASEURL}&cat=users&subcat=group&cmd=delete&item={$T_TEXTME_ITEM.id}"
                   title="{$smarty.const._TEXTME_DELETEGROUP}">
                    <img src="{$T_TEXTME_BASELINK}assets/images/16/error_delete.png"
                         alt="{$smarty.const._TEXTME_DELETEGROUP}"
                         title="{$smarty.const._TEXTME_DELETEGROUP}"/>
                </a>
            </td>
        </tr>

        {foreachelse}
        <tr>
            <td colspan="3">{$smarty.const._TEXTME_NOGROUPS}</td>
        </tr>
        {/foreach}

    </table>
<!--/ajax:groups-table-->

{/if}

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
