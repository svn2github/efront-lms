{if $T_TRAININGREPORT_CREATEMESSAGE}
    <script type="text/javascript">
    parent.location = '{$T_MODULE_BASEURL}'+'&cat=edit&id={$T_TRAININGREPORT_NEWID}&message={$T_TRAININGREPORT_CREATEMESSAGE|urlencode}&message_type=success';
    </script>
{elseif $T_TRAININGREPORT_CLONEMESSAGE}
    <script type="text/javascript">
    parent.location = '{$T_MODULE_BASEURL}'+'&cat=view&id={$T_TRAININGREPORT_NEWID}&message={$T_TRAININGREPORT_CLONEMESSAGE|urlencode}&message_type=success';
    </script>
{/if}

{if $smarty.get.cmd == 'create' }

    {capture name='t_training_reports'}
        {eF_template_printForm form=$T_TRAININGREPORT_FORM}
    {/capture}

    {eF_template_printBlock
        title=$smarty.const._CREATE
        data=$smarty.capture.t_training_reports
        image=$T_RSS_MODULE_BASELINK|cat:'assets/images/logo32.png'
        absoluteImagePath = 1}

{elseif $smarty.get.cmd == 'clone'}

    {capture name='t_training_reports'}
        {eF_template_printForm form=$T_TRAININGREPORT_FORM}
    {/capture}

    {eF_template_printBlock
        title=$smarty.const._CLONE
        data=$smarty.capture.t_training_reports
        image=$T_RSS_MODULE_BASELINK|cat:'assets/images/logo32.png'
        absoluteImagePath = 1}
{else}

{capture name="t_training_reports"}

    {$T_TRAININGREPORT_FORM.javascript}
    <form {$T_TRAININGREPORT_FORM.attributes}>
        {$T_TRAININGREPORT_FORM.hidden}
        <table>
            <tr>
                <td class = "labelCell">
                    <div class="headerTools">
                        <span>
                            <img src="{$T_MODULE_BASELINK|cat:'assets/images/transparent.png'}" class="sprite16 sprite16-add" alt="{$smarty.const._CREATE}" title="{$smarty.const._CREATE}" />
                            <a href="{$T_MODULE_BASEURL}&amp;cat=view&amp;cmd=create&amp;popup=1" target="POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._CREATE}', 0)">{$smarty.const._CREATE}</a>
                        </span>
                        <span>
                        {$T_TRAININGREPORT_FORM.report.html}&nbsp;
                        </span>
                    </div>
                </td>
                <td class = "elementCell">
                        {if isset($T_TRAININGREPORT_REPORT)}
                        <span>
                            <img src="{$T_MODULE_BASELINK|cat:'assets/images/transparent.png'}" class="sprite16 sprite16-edit" alt="{$smarty.const._EDIT}" title="{$smarty.const._EDIT}" />
                            <a href="{$T_MODULE_BASEURL}&amp;cat=edit&amp;id={$T_TRAININGREPORT_REPORT.id}">{$smarty.const._EDIT}</a>
                        </span>
                        <span>
                            <img src="{$T_MODULE_BASELINK|cat:'assets/images/transparent.png'}" class="sprite16 sprite16-error_delete" alt="{$smarty.const._DELETE}" title="{$smarty.const._DELETE}" />
                            <a href="{$T_MODULE_BASEURL}&amp;cat=view&cmd=delete&amp;id={$T_TRAININGREPORT_REPORT.id}">{$smarty.const._DELETE}</a>
                        </span>
                        <span>
                            <img src="{$T_MODULE_BASELINK|cat:'assets/images/transparent.png'}" class="sprite16 sprite16-copy" alt="{$smarty.const._COPY}" title="{$smarty.const._COPY}" />
                            <a href="{$T_MODULE_BASEURL}&amp;cat=view&cmd=clone&amp;id={$T_TRAININGREPORT_REPORT.id}&amp;popup=1" target="POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._COPY}', 0)">{$smarty.const._COPY}</a>
                        </span>
                        {/if}
                </td>
            </tr>
        </table>
    </form>

    {if isset($T_TRAININGREPORT_REPORT)}
    <form action="" method="">
        <fieldset class="fieldsetSeparator">
            <legend>{$smarty.const._TRAININGREPORTS_FIELDS}</legend>
            <table>
                {foreach from=$T_TRAININGREPORT_SELECTEDFIELDS item=T_TRAININGREPORT_SELECTEDFIELD}
                <tr>
                    <td></td>
                    <td class="elementCell">{$T_TRAININGREPORT_FIELDS.$T_TRAININGREPORT_SELECTEDFIELD}</td>
                </tr>
                {/foreach}
            </table>
        </fieldset>

        <fieldset class="fieldsetSeparator">
            <legend>{$smarty.const._COURSES}</legend>
            <table>
                {foreach from=$T_TRAININGREPORT_SELECTEDCOURSES item=T_TRAININGREPORT_SELECTEDCOURSE}
                <tr>
                    <td></td>
                    <td class="elementCell">{$T_TRAININGREPORT_COURSES.$T_TRAININGREPORT_SELECTEDCOURSE}</td>
                </tr>
                {/foreach}
            </table>
        </fieldset>

        <fieldset class="fieldsetSeparator">
            <legend>{$smarty.const._TRAININGREPORTS_DATES}</legend>
            <table>
                <tr>
                    <td></td>
                    <td class="elementCell">
                        {$smarty.const._FROM}
                        <strong>
                            <span style = "display:none">{$T_TRAININGREPORT_REPORT.from_date}</span>
                            #filter:timestamp-{$T_TRAININGREPORT_REPORT.from_date}#
                        </strong>
                        {$smarty.const._TO}
                        <strong>
                            <span style = "display:none">{$T_TRAININGREPORT_REPORT.to_date}</span>
                            #filter:timestamp-{$T_TRAININGREPORT_REPORT.to_date}#
                        </strong>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td class="elementCell">
                        {$smarty.const._TRAININGREPORTS_SEPARATEDBY}
                        {$T_TRAININGREPORT_SEPARATEDBY}
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td class="elementCell"></td>
                </tr>
            </table>
        </fieldset>
    </form>

    <form id="kalimera" action="{$T_MODULE_BASEURL}&amp;cat=excel&amp;id={$T_TRAININGREPORT_REPORT.id}" method="post">
        <table>
            <tr>
                <td class="labelCell">&nbsp;</td>
                <td class="elementCell">
                    <input type="submit" value="{$smarty.const._DOWNLOADFILE}" class="flatButton" />
                </td>
            </tr>
        </table>
    </form>
    {/if}
{/capture}

{eF_template_printInnerTable
    title=$smarty.const._TRAININGREPORTS
    data=$smarty.capture.t_training_reports
    absoluteImagePath=1
    image=$T_REPORTS_BASELINK|cat:'assets/images/logo32.png'}

{/if}
