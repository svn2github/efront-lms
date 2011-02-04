{capture name='t_textme_tab'}

{if $smarty.get.subcat == 'view'}

    {assign var='t_textme_title' value=$smarty.const._TEXTME_VIEWMESSAGE}
    {assign var='t_textme_image' value='assets/images/32/mail.png'}


    <div class="headerTools">
        <span>
            <img src="{$T_TEXTME_BASELINK}assets/images/16/go_back.png" title="{$smarty.const._TEXTME_BACKTOINBOX}" alt="{$smarty.const._TEXTME_BACKTOINBOX}">
            <a href="{$T_TEXTME_BASEURL}&cat=inbox" title="{$smarty.const._TEXTME_BACKTOINBOX}">{$smarty.const._TEXTME_BACKTOINBOX}</a>
        </span>
    </div>

    <table class="textme-view-message">
        <tr>
            <td><strong>{$smarty.const._DATE}:</strong></td>
            <td>
                <span style = "display:none">{$T_TEXTME_MESSAGE.send_at}</span>
                #filter:timestamp_time-{$T_TEXTME_MESSAGE.send_at}#
            </td>
        </tr>
        <tr>
            <td><strong>{$smarty.const._FROM}:</strong></td>
            <td>#filter:login-{$T_TEXTME_MESSAGE.users_LOGIN}#</td>
        </tr>
        <tr>
            <td><strong>{$smarty.const._TEXTME_MESSAGE}:</strong></td>
            <td>
                <div class="textme-quotes">
                    <p>{$T_TEXTME_MESSAGE.text}</p>
                </div>
            </td>
        </tr>
    </table>

    <br/>
    <br/>

{if $T_TEXTME_CURRENTROLE == 'professor'}
<!--ajax:recipients-table-->
<table
    id="recipients-table"
    style="width:100%"
    class="sortedTable"
    sortBy="0"
    size="{$T_TEXTME_ITEMS_COUNT}"
    useAjax="1"
    rowsPerPage="20"
    url="{$T_TEXTME_BASEURL}&cat=inbox&subcat=view&item={$smarty.get.item}&">

    <tr class="topTitle">
        <td class="topTitle" name="users_LOGIN">{$smarty.const._TEXTME_RECIPIENT}</td>
        <td class="topTitle centerAlign" name="status">{$smarty.const._TEXTME_SMSSTATUS}</td>
        <td class="topTitle centerAlign" name="is_read">{$smarty.const._TEXTME_LOCALSTATUS}</td>
    </tr>

    {foreach from=$T_TEXTME_ITEMS item=T_TEXTME_ITEM}

    <tr class="{cycle values='oddRowColor,evenRowColor'}">
        <td>
            #filter:login-{$T_TEXTME_ITEM.users_LOGIN}#
        </td>
        <td class="centerAlign">
            {if $T_TEXTME_ITEM.status == 'pending'}
                <img src="{$T_TEXTME_BASELINK}assets/images/16/time.png" alt="{$smarty.const._TEXTME_MESSAGEPENDING}" title="{$smarty.const._TEXTME_MESSAGEPENDING}"/>
            {elseif $T_TEXTME_ITEM.status == 'failed'}
                <img src="{$T_TEXTME_BASELINK}assets/images/16/error_delete.png" alt="{$smarty.const._TEXTME_MESSAGEFAILED}" title="{$smarty.const._TEXTME_MESSAGEFAILED}"/>
            {elseif $T_TEXTME_ITEM.status == 'delivered'}
                <img src="{$T_TEXTME_BASELINK}assets/images/16/success.png" alt="{$smarty.const._TEXTME_MESSAGEDELIVERED}" title="{$smarty.const._TEXTME_MESSAGEDELIVERED}"/>
            {else}
                {$smarty.const._TEXTME_NOTROUTED}
            {/if}
        </td>
        <td class="centerAlign">

            {if $T_TEXTME_ITEM.is_read == 0}
                <img src="{$T_TEXTME_BASELINK}assets/images/16/email.png" alt="{$smarty.const._TEXTME_MESSAGEUNREAD}" title="{$smarty.const._TEXTME_MESSAGEUNREAD}"/>
            {elseif $T_TEXTME_ITEM.is_read == 1}
                <img src="{$T_TEXTME_BASELINK}assets/images/16/email_open.png" alt=" {$smarty.const._TEXTME_MESSAGEREAD}" title=" {$smarty.const._TEXTME_MESSAGEREAD}"/>
            {/if}
        </td>
    </tr>

    {foreachelse}

    <tr>
        <td colspan="3">{$smarty.const._TEXTME_NORECIPIENTS}</td>
    </tr>

    {/foreach}

</table>
<!--/ajax:recipients-table-->
{/if}

{*******************************************************************************
    Compose a new sms notification
*******************************************************************************}
{elseif $smarty.get.subcat == 'compose' && $T_TEXTME_CURRENTROLE == 'professor'}

    {assign var='t_textme_title' value=$smarty.const._TEXTME_COMPOSEMESSAGE}
    {assign var='t_textme_image' value='assets/images/32/mail.png'}

    <div class="headerTools">
        <span>
            <img src="{$T_TEXTME_BASELINK}assets/images/16/go_back.png" title="{$smarty.const._TEXTME_BACKTOINBOX}" alt="{$smarty.const._TEXTME_BACKTOINBOX}">
            <a href="{$T_TEXTME_BASEURL}&cat=inbox" title="{$smarty.const._TEXTME_BACKTOINBOX}">{$smarty.const._TEXTME_BACKTOINBOX}</a>
        </span>
    </div>

    <p>
        {if $T_TEXTME_SETTINGS.credits == null}
            {$smarty.const._TEXTME_ACCOUNTBALANCE|sprintf:$smarty.const._TEXTME_UNLIMITEDCREDITS}
        {else}
            {$smarty.const._TEXTME_ACCOUNTBALANCE|sprintf:$T_TEXTME_SETTINGS.credits}
        {/if}
    </p>

    {$T_TEXTME_FORM.javascript}
    <form {$T_TEXTME_FORM.attributes}>
        {$T_TEXTME_FORM.hidden}
        <table>
            <tr>
                <td class = "labelCell">{$T_TEXTME_FORM.recipients.label}:</td>
                <td class = "elementCell">
                    {$T_TEXTME_FORM.recipients.html}
                </td>
            </tr>
            <tr id="module_textme_usersrow" {if $smarty.post.recipients != 'select'}style="display:none;"{/if}>
                <td class = "labelCell">{$T_TEXTME_FORM.users.label}:</td>
                <td class = "elementCell">
                    {$T_TEXTME_FORM.users.html}
                </td>
            </tr>
            <tr id="module_textme_groupsrow" {if $smarty.post.recipients != 'select'}style="display:none;"{/if}>
                <td class = "labelCell">{$T_TEXTME_FORM.groups.label}:</td>
                <td class = "elementCell">
                    {$T_TEXTME_FORM.groups.html}
                </td>
            </tr>
            <tr>
                <td class = "labelCell">{$T_TEXTME_FORM.message.label}:</td>
                <td class = "elementCell">
                    {$T_TEXTME_FORM.message.html}
                </td>
            </tr>
            <tr {if isset($T_TEXTME_SCHEDULEDSMS) == false}style="display:none;"{/if}>
                <td class = "labelCell">{$T_TEXTME_FORM.schedule.label}:</td>
                <td class = "elementCell">
                    {$T_TEXTME_FORM.schedule.html}
                </td>
            </tr>
            <tr id="module_textme_daterow" {if $smarty.post.schedule != 'later'}style="display:none;{/if}">
                <td class = "labelCell">{$T_TEXTME_FORM.date.label}:</td>
                <td class = "elementCell">{$T_TEXTME_FORM.date.html}</td>
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

{*******************************************************************************
    Inbox
*******************************************************************************}
{else}

    {assign var='t_textme_title' value=$smarty.const._TEXTME_INBOX}
    {assign var='t_textme_image' value='assets/images/32/mail.png'}

    {if $T_TEXTME_CURRENTROLE == 'professor'}
    <div class="headerTools">
        <span>
            <img src="{$T_TEXTME_BASELINK}assets/images/16/add.png" title="{$smarty.const._TEXTME_COMPOSEMESSAGE}" alt="{$smarty.const._TEXTME_COMPOSEMESSAGE}">
            <a href="{$T_TEXTME_BASEURL}&cat=inbox&subcat=compose" title="{$smarty.const._TEXTME_COMPOSEMESSAGE}">{$smarty.const._TEXTME_COMPOSEMESSAGE}</a>
        </span>
    </div>
    {/if}

<!--ajax:inbox-table-->
<table
    id="inbox-table"
    style="width:100%"
    class="sortedTable"
    sortBy="2"
    size="{$T_TEXTME_ITEMS_COUNT}"
    useAjax="1"
    rowsPerPage="20"
    url="{$T_TEXTME_BASEURL}&cat=inbox&">

    <tr class="topTitle">
        <td class="topTitle" name="text">{$smarty.const._TEXTME_MESSAGE}</td>
        <td class="topTitle" name="users_LOGIN">{$smarty.const._FROM}</td>
        <td class="topTitle" name="send_at">{$smarty.const._DATE}</td>
        <td class="topTitle" name="credits">{$smarty.const._TEXTME_CREDITS}</td>
        <td class="topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
    </tr>
    {foreach from=$T_TEXTME_ITEMS item=T_TEXTME_ITEM}


    <tr class="{cycle values='oddRowColor,evenRowColor'} {if $T_TEXTME_ITEM.is_read == 0} unreadMessage {/if}">
        <td>
            <a href="{$T_TEXTME_BASEURL}&cat=inbox&subcat=view&item={$T_TEXTME_ITEM.id}">
                {$T_TEXTME_ITEM.text|truncate:30}
            </a>
        </td>
        <td>
            #filter:login-{$T_TEXTME_ITEM.users_LOGIN}#
        </td>
        <td>
            <span style = "display:none">{$T_TEXTME_ITEM.send_at}</span>
            #filter:timestamp_time-{$T_TEXTME_ITEM.send_at}#
        </td>
        <td>
            {$T_TEXTME_ITEM.credits}
        </td>
        <td class="centerAlign">
            <a href="{$T_TEXTME_BASEURL}&cat=inbox&subcat=group&cmd=delete&item={$T_TEXTME_ITEM.id}" title="{$smarty.const._TEXTME_DELETEMESSAGE}">
                <img src="{$T_TEXTME_BASELINK}assets/images/16/error_delete.png" alt="{$smarty.const._TEXTME_DELETEMESSAGE}" title="{$smarty.const._TEXTME_DELETEMESSAGE}"/>
            </a>
            {if $T_TEXTME_CURRENTROLE == 'professor'}
            <a href="{$T_TEXTME_BASEURL}&cat=inbox&subcat=group&cmd=delete-all&item={$T_TEXTME_ITEM.id}" onclick="return confirm('Are you sure you want to undo send?');" title="{$smarty.const._TEXTME_UNDOSEND}">
                <img src="{$T_TEXTME_BASELINK}assets/images/16/undo.png" alt="{$smarty.const._TEXTME_UNDOSEND}" title="{$smarty.const._TEXTME_UNDOSEND}"/>
            </a>
            {/if}
        </td>
    </tr>
    {foreachelse}
    <tr>
        <td colspan="5">{$smarty.const._TEXTME_NOMESSAGES}</td>
    </tr>
    {/foreach}
</table>
<!--/ajax:inbox-table-->
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
