 {if $smarty.get.from_year}
  {assign var = "dates_url" value = "&from_year=`$smarty.get.from_year`&from_month=`$smarty.get.from_month`&from_day=`$smarty.get.from_day`&from_hour=`$smarty.get.from_hour`&from_min=`$smarty.get.from_min`"}
  {assign var = "dates_url" value = "`$dates_url`&to_year=`$smarty.get.to_year`&to_month=`$smarty.get.to_month`&to_day=`$smarty.get.to_day`&to_hour=`$smarty.get.to_hour`&to_min=`$smarty.get.to_min`"}
 {/if}
 <td class = "labelCell">{$smarty.const._FILTERS}:</td>
 <td class = "filter">
        <select style = "vertical-align:middle" name = "user_filter" onchange = "if (this.options[this.selectedIndex].value != '') document.location='{$smarty.server.PHP_SELF}?ctg=statistics&option={$smarty.get.option}&{if (isset($smarty.get.tab))}&tab={$smarty.get.tab}{else}&tab=users{/if}&sel_{$smarty.get.option}={$T_STATS_ENTITY_ID}{if (isset($smarty.get.branch_filter))}&branch_filter={$smarty.get.branch_filter}{/if}{if (isset($smarty.get.group_filter))}&group_filter={$smarty.get.group_filter}{/if}&user_filter='+this.options[this.selectedIndex].value+'{$dates_url}';">
                <option value = "1"{if !$smarty.get.user_filter || $smarty.get.user_filter == 1}selected{/if}>{$smarty.const._ACTIVEUSERS}</option>
                <option value = "2"{if $smarty.get.user_filter == 2}selected{/if}>{$smarty.const._INACTIVEUSERS}</option>
                <option value = "3"{if $smarty.get.user_filter == 3}selected{/if}>{$smarty.const._ALLUSERS}</option>
        </select>
    </td><td class = "filter">
        <select style = "vertical-align:middle" name = "group_filter" onchange = "if (this.options[this.selectedIndex].value != '') document.location='{$smarty.server.PHP_SELF}?ctg=statistics&option={$smarty.get.option}&{if (isset($smarty.get.tab))}&tab={$smarty.get.tab}{else}&tab=users{/if}&sel_{$smarty.get.option}={$T_STATS_ENTITY_ID}{if (isset($smarty.get.branch_filter))}&branch_filter={$smarty.get.branch_filter}{/if}{if (isset($smarty.get.user_filter))}&user_filter={$smarty.get.user_filter}{/if}&group_filter='+this.options[this.selectedIndex].value+'{$dates_url}';">
                <option value = "-1" class = "inactiveElement" {if !$smarty.get.group_filter}selected{/if}>{$smarty.const._SELECTGROUP}</option>
            {foreach name = "group_options" from = $T_GROUPS item = 'group' key='id'}
                <option value = "{$group.id}" {if $smarty.get.group_filter == $group.id}selected{/if}>{$group.name}</option>
            {/foreach}
        </select>
    </td>
