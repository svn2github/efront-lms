{* template functions for inner table *}
{capture name = 't_inner_table_code}
    <table>
        {if isset($T_QUOTE_INNERTABLE)}
        <tr>
            <td><i>&nbsp;&nbsp;{$T_QUOTE_INNERTABLE}</i></td>
        </tr>
        {else}
            <tr><td class = "emptyCategory">{$smarty.const._QUOTE_NOQUOTEFOUND}</td></tr>
        {/if}
    </table>
{/capture}
{eF_template_printBlock title = $smarty.const._QUOTE_QUOTEPAGE data = $smarty.capture.t_inner_table_code absoluteImagePath=1 image = $T_QUOTE_BASELINK|cat:'images/quote32.png' options = $T_QUOTE_INNERTABLE_OPTIONS}
