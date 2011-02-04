{capture name = "t_textme_cpanel_widget"}

{if $T_TEXTME_GATEWAYENABLED == true}
<p>
    <img src="{$T_TEXTME_BASELINK}assets/images/16/success.png" title="Success" alt="Success"/>
    <span style="vertical-align: top;">{$smarty.const._TEXTME_SMSNOTIFICATIONSENABLEDANDROUTED|sprintf:$T_TEXTME_GATEWAY.type:$T_TEXTME_GATEWAY.name}</span>
</p>
{else}
<p>
    <img src="{$T_TEXTME_BASELINK}assets/images/16/warning.png" title="Warning" alt="Warning"/> <strong style="vertical-align: top;">Warning</strong><br/>
    {assign var='T_TEXTME_URL' value=$T_TEXTME_BASEURL|cat:'&cat=gateways'}
    <span style="vertical-align: top;">{$smarty.const._TEXTME_SMSNOTIFICATIONNOTSENABLEDANDROUTED|sprintf:$T_TEXTME_URL}</span>
</p>
{/if}

<p>
    <img src="{$T_TEXTME_BASELINK}assets/images/16/lessons.png" title="Lessons" alt="Lessons"/>
    <span style="vertical-align: top;">{$smarty.const._TEXTME_LESSONSTHATHAVEENABLEDTEXTME|sprintf:$T_TEXTME_LESSONSENABLEDCOUNT:$T_TEXTME_LESSONSCOUNT}</span>
</p>
<p>
    <img src="{$T_TEXTME_BASELINK}assets/images/16/users.png" title="Users" alt="Users"/>
    <span style="vertical-align: top;">{$smarty.const._TEXTME_USERSTHATHAVEENABLEDTEXTME|sprintf:$T_TEXTME_SUBSCRIBERSCOUNT}</span>
</p>

{/capture}

{eF_template_printBlock
    title=$smarty.const._TEXTME
    data=$smarty.capture.t_textme_cpanel_widget
    image= $T_TEXTME_BASELINK|cat:'assets/images/32/logo.png'
    absoluteImagePath = 1
    options = $T_TEXTME_OPTIONS}
