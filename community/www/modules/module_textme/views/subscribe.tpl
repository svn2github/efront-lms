{capture name = "t_textme_tab"}

{assign var='t_textme_title' value=$smarty.const._SUBSCRIBE}
{assign var='t_textme_image' value='assets/images/32/phone.png'}

{if $smarty.get.cmd == 'unsubscribe' && !isset($smarty.post.submit)}

    {assign var='t_textme_title' value=$smarty.const._TEXTME_UNSUBSCRIBE}
    {assign var='t_textme_image' value='assets/images/32/error_delete.png'}

    <div class="headerTools">
        <span>
            <img src="{$T_TEXTME_BASELINK}assets/images/16/go_back.png" title="{$smarty.const._TEXTME_BACK}" alt="{$smarty.const._TEXTME_BACK}">
            <a href="{$T_TEXTME_BASEURL}&cat=subscribe" title="{$smarty.const._TEXTME_BACK}">{$smarty.const._TEXTME_BACK}</a>
        </span>
    </div>

    <p>
        {$smarty.const._TEXTME_AREYOUSUREYOUWANTTOUNSUBSCRIBE}<br/>
    </p>
    <form action="{$T_TEXTME_BASEURL}&cat=subscribe&cmd=unsubscribe" method="post">
            <table>
                <tr>
                    <td class = "labelCell">&nbsp;</td>
                    <td class = "elementCell"><input name="submit" type="submit" value="{$smarty.const._SUBMIT}" class="flatButton"/></td>
                </tr>
            </table>
    </form>

{elseif $smarty.get.cmd == 'mobile' && !isset($smarty.post.submit)}

    {assign var='t_textme_title' value=$smarty.const._TEXTME_CHANGEMOBILE}
    {assign var='t_textme_image' value='assets/images/32/edit.png'}

    <div class="headerTools">
        <span>
            <img src="{$T_TEXTME_BASELINK}assets/images/16/go_back.png" title="{$smarty.const._TEXTME_BACK}" alt="{$smarty.const._TEXTME_BACK}">
            <a href="{$T_TEXTME_BASEURL}&cat=subscribe" title="{$smarty.const._TEXTME_BACK}">{$smarty.const._TEXTME_BACK}</a>
        </span>
    </div>

    <p>
        {$smarty.const._TEXTME_AREYOUSUREYOUWANTTOCHANGEMOBILE}<br/>
    </p>
    <form action="{$T_TEXTME_BASEURL}&cat=subscribe&cmd=mobile" method="post">
            <table>
                <tr>
                    <td class = "labelCell">&nbsp;</td>
                    <td class = "elementCell"><input name="submit" type="submit" value="{$smarty.const._SUBMIT}" class="flatButton"/></td>
                </tr>
            </table>
    </form>

{elseif $smarty.get.cmd == 'vcode' && !isset($smarty.post.submit)}

    {assign var='t_textme_title' value='Resend verification code'}
    {assign var='t_textme_image' value='assets/images/32/refresh.png'}

    <div class="headerTools">
        <span>
            <img src="{$T_TEXTME_BASELINK}assets/images/16/go_back.png" title="{$smarty.const._TEXTME_BACK}" alt="{$smarty.const._TEXTME_BACK}">
            <a href="{$T_TEXTME_BASEURL}&cat=subscribe" title="{$smarty.const._TEXTME_BACK}">{$smarty.const._TEXTME_BACK}</a>
        </span>
    </div>

    <p>
        {$smarty.const._TEXTME_RESENDCODE}<br/>
    </p>
    <form action="{$T_TEXTME_BASEURL}&cat=subscribe&cmd=vcode" method="post">
            <table>
                <tr>
                    <td class = "labelCell">&nbsp;</td>
                    <td class = "elementCell"><input name="submit" type="submit" value="Submit" class="flatButton"/></td>
                </tr>
            </table>
    </form>

{elseif $T_TEXTME_SUBSCRIBER == NULL}

    <p>
        {$smarty.const._TEXTME_WHATTEXTMEIS}
    </p>

    {foreach name='t_textme_lessons' from=$T_TEXTME_LESSONS item=T_TEXTME_LESSON}
        {if $smarty.foreach.t_textme_lessons.first}
        <p>
            {$smarty.const._TEXTME_THEFOLLOWINGLESSONSPROVIDEALERTS}<br/>
        </p>
        <ul>
        {/if}
            <li>{$T_TEXTME_LESSON.name}</li>
        {if $smarty.foreach.t_textme_lessons.last}
        </ul>
        {/if}
    {/foreach}

    <p>{$smarty.const._TEXTME_YOUHAVENOTYETSUBSCRIBED}</p>

    {$T_TEXTME_FORM.javascript}
    <form {$T_TEXTME_FORM.attributes}>
        {$T_TEXTME_FORM.hidden}
        <table>
            <tr>
                <td class = "labelCell">{$T_TEXTME_FORM.mobile.label}:&nbsp;</td>
                <td class = "elementCell">{$T_TEXTME_FORM.mobile.html}</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td class = "infoCell">{$smarty.const._TEXTME_MOBILEDESCRIPTION}</td>
            </tr>
            <tr>
                <td></td>
                <td class = "infoCell">{$T_TEXTME_FORM.requirednote}</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td class = "elementCell">{$T_TEXTME_FORM.submit.html}</td>
            </tr>
        </table>
    </form>

{elseif $T_TEXTME_SUBSCRIBER.is_verified == 0}

    <div class="headerTools">
        <span>
            <img src="{$T_TEXTME_BASELINK}assets/images/16/edit.png"
                 title="{$smarty.const._TEXTME_CHANGEMOBILE}"
                 alt="{$smarty.const._TEXTME_CHANGEMOBILE}">
            <a href="{$T_TEXTME_BASEURL}&cat=subscribe&cmd=mobile"
               title="{$smarty.const._TEXTME_CHANGEMOBILE}">
                {$smarty.const._TEXTME_CHANGEMOBILE}</a>
        </span>
        <span>
            <img src="{$T_TEXTME_BASELINK}assets/images/16/refresh.png"
                 title="{$smarty.const._TEXTME_REQUESTVERIFICATIONCODE}"
                 alt="{$smarty.const._TEXTME_REQUESTVERIFICATIONCODE}">
            <a href="{$T_TEXTME_BASEURL}&cat=subscribe&cmd=vcode"
               title="{$smarty.const._TEXTME_REQUESTVERIFICATIONCODE}">
                {$smarty.const._TEXTME_REQUESTVERIFICATIONCODE}</a>
        </span>
        <span>
            <img src="{$T_TEXTME_BASELINK}assets/images/16/error_delete.png"
                 title="{$smarty.const._TEXTME_UNSUBSCRIBE}"
                 alt="{$smarty.const._TEXTME_UNSUBSCRIBE}">
            <a href="{$T_TEXTME_BASEURL}&cat=subscribe&cmd=unsubscribe"
               title="{$smarty.const._TEXTME_UNSUBSCRIBE}">
                {$smarty.const._TEXTME_UNSUBSCRIBE}</a>
        </span>
    </div>

    <p>
        {$smarty.const._TEXTME_YOURMOBILEIS|sprintf:$T_TEXTME_SUBSCRIBER.mobile}
        <br/><br/>
        {$smarty.const._TEXTME_VERIFICATIONCODEDESCRIPTION}
        <br/>
    </p>

   {$T_TEXTME_FORM.javascript}
    <form {$T_TEXTME_FORM.attributes}>
        {$T_TEXTME_FORM.hidden}
        <table>
            <tr>
                <td class = "labelCell">{$T_TEXTME_FORM.vcode.label}:&nbsp;</td>
                <td class = "elementCell">{$T_TEXTME_FORM.vcode.html}</td>
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

{else}

    <div class="headerTools">
        <span>
            <img src="{$T_TEXTME_BASELINK}assets/images/16/edit.png"
                 title="{$smarty.const._TEXTME_CHANGEMOBILE}"
                 alt="{$smarty.const._TEXTME_CHANGEMOBILE}">
            <a href="{$T_TEXTME_BASEURL}&cat=subscribe&cmd=mobile"
               title="{$smarty.const._TEXTME_CHANGEMOBILE}">
                {$smarty.const._TEXTME_CHANGEMOBILE}</a>
        </span>
        <span>
            <img src="{$T_TEXTME_BASELINK}assets/images/16/error_delete.png"
                 title="{$smarty.const._TEXTME_UNSUBSCRIBE}"
                 alt="{$smarty.const._TEXTME_UNSUBSCRIBE}">
            <a href="{$T_TEXTME_BASEURL}&cat=subscribe&cmd=unsubscribe"
               title="{$smarty.const._TEXTME_UNSUBSCRIBE}">
                {$smarty.const._TEXTME_UNSUBSCRIBE}</a>
        </span>
    </div>

    <p>
        {$smarty.const._TEXTME_YOURMOBILEIS|sprintf:$T_TEXTME_SUBSCRIBER.mobile}<br/>
        {$smarty.const._TEXTME_YOUCANSUBSCRIBETOTHEFOLLOWINGLESSONS}<br/>
    </p>

<!--ajax:lessons-table-->
    <table
        id="lessons-table"
        style="width:100%"
        class="sortedTable"
        sortBy="0"
        size="{$T_TEXTME_ITEMS_COUNT}"
        useAjax="1"
        rowsPerPage="20"
        url="{$T_TEXTME_BASEURL}&cat=subscribe&">

        <tr class="topTitle">
            <td class="topTitle" name="name">{$smarty.const._LESSON}</td>
            <td class="topTitle centerAlign noSort">{$smarty.const._STATUS}</td>
        </tr>

        {foreach from=$T_TEXTME_ITEMS item=T_TEXTME_ITEM}

        <tr class="{cycle values='oddRowColor,evenRowColor'}  {if $T_TEXTME_ITEM.is_activated == false}deactivatedTableElement{/if}">
            <td>{$T_TEXTME_ITEM.name}</td>
            {if $T_TEXTME_ITEM.is_activated == true}
            <td class="centerAlign">
                <a href="{$T_TEXTME_BASEURL}&cat=subscribe&subcat=lessons&cmd=deactivate&item={$T_TEXTME_ITEM.id}" title="{$smarty.const._ACTIVE}">
                    <img src="{$T_TEXTME_BASELINK}assets/images/16/trafficlight_green.png" title="{$smarty.const._ACTIVE}" alt="{$smarty.const._ACTIVE}">
                </a>
            </td>
            {else}
            <td class="centerAlign">
                <a href="{$T_TEXTME_BASEURL}&cat=subscribe&subcat=lessons&cmd=activate&item={$T_TEXTME_ITEM.id}" title="{$smarty.const._INACTIVE}">
                    <img src="{$T_TEXTME_BASELINK}assets/images/16/trafficlight_red.png" title="{$smarty.const._INCACTIVE}" alt="{$smarty.const._INACTIVE}">
                </a>
            </td>
            {/if}
        </tr>

        {foreachelse}

        <tr>
            <td colspan="2">{$smarty.const._TEXTME_NOLESSONS}</td>
        </tr>

        {/foreach}

    </table>
<!--/ajax:lessons-table-->

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
