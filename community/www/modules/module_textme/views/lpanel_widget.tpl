{* smarty template for textme - lesson panel widget *}
{capture name = "t_textme_lpanel_widget"}

{if $T_TEXTME_SUBSCRIBER == NULL}
<p>
    <img src="{$T_TEXTME_BASELINK}assets/images/16/warning.png" title="Warning" alt="Warning"/> <strong style="vertical-align: top;">{$smarty.const._TEXTME_WARNING}</strong><br/>
    <span style="vertical-align: top;">
    {$smarty.const._TEXTME_YOUCANNOTRECEIVEALERTS}<br/>
    {$smarty.const._TEXTME_YOUHAVENOTYETSUBSCRIBEDTOTEXTME}<br/>
    </span>
</p>
{elseif $T_TEXTME_SUBSCRIBER.is_verified == 0}
<p>
    <img src="{$T_TEXTME_BASELINK}assets/images/16/warning.png" title="Warning" alt="Warning"/> <strong style="vertical-align: top;">Warning</strong><br/>
    <span style="vertical-align: top;">
    {$smarty.const._TEXTME_YOUCANNOTRECEIVEALERTS}<br/>
    {$smarty.const._TEXTME_YOUHAVENOTYETVERIFIEDYOURMOBILE}<br/>
    </span>
</p>
{/if}


{if $T_TEXTME_ITEMSCOUNT == 0}
    {$smarty.const._TEXTME_YOUDONTHAVEANYMESSAGES}
{else}
    {$smarty.const._TEXTME_YOUHAVEMESSAGES|sprintf:$T_TEXTME_BASEURL:'&cat=inbox':$T_TEXTME_ITEMSCOUNT}
{/if}

<br/>
{counter start=0 print=false}
<table id="module_textme_messages_list" border="0" width="100%">
    <tbody>
    {foreach name='t_textme_items' from=$T_TEXTME_ITEMS item=T_TEXTME_ITEM}
        <tr {if $smarty.foreach.t_textme_items.index >= 10}style="display:none;"{/if}>
            <td>
                <span class="counter">{counter}.</span>&nbsp;
                <a href="{$T_TEXTME_BASEURL}&cat=inbox&subcat=view&item={$T_TEXTME_ITEM.id}" title="Go to Message">{$T_TEXTME_ITEM.text|truncate:30}</a>
            </td>
            <td align="right">
                <span style="white-space:nowrap;font-weight:bold">
                   #filter:login-{$T_TEXTME_ITEM.users_LOGIN}#
                </span>,
                <span title="#filter:timestamp_time_nosec-{$T_TEXTME_ITEM.send_at}#" title="Go to Message">#filter:timestamp_interval-{$T_TEXTME_ITEM.send_at}# {$smarty.const._AGO}</span>
            </td>
        </tr>
    {/foreach}
    </tbody>
</table>

{/capture}

{eF_template_printBlock
    title=$smarty.const._TEXTME
    data=$smarty.capture.t_textme_lpanel_widget
    image= $T_TEXTME_BASELINK|cat:'assets/images/32/logo.png'
    absoluteImagePath = 1
    options = $T_TEXTME_OPTIONS}
