 {if $smarty.get.from_year}
  {assign var = "dates_url" value = "&from_year=`$smarty.get.from_year`&from_month=`$smarty.get.from_month`&from_day=`$smarty.get.from_day`&from_hour=`$smarty.get.from_hour`&from_min=`$smarty.get.from_min`"}
  {assign var = "dates_url" value = "`$dates_url`&to_year=`$smarty.get.to_year`&to_month=`$smarty.get.to_month`&to_day=`$smarty.get.to_day`&to_hour=`$smarty.get.to_hour`&to_min=`$smarty.get.to_min`"}
 {/if}
 <td class = "filter">{$smarty.const._FILTERS}:
        <select style = "vertical-align:middle" id = "user_filter" name = "user_filter">
                <option value = "1"{if !$smarty.get.user_filter || $smarty.get.user_filter == 1}selected{/if}>{$smarty.const._ACTIVEUSERS}</option>
                <option value = "2"{if $smarty.get.user_filter == 2}selected{/if}>{$smarty.const._INACTIVEUSERS}</option>
                <option value = "3"{if $smarty.get.user_filter == 3}selected{/if}>{$smarty.const._ALLUSERS}</option>
        </select>
    </td><td class = "filter">
        <select style = "vertical-align:middle" id = "group_filter" name = "group_filter" >
                <option value = "-1" class = "inactiveElement" {if !$smarty.get.group_filter}selected{/if}>{$smarty.const._SELECTGROUP}</option>
            {foreach name = "group_options" from = $T_GROUPS item = 'group' key='id'}
                <option value = "{$group.id}" {if $smarty.get.group_filter == $group.id}selected{/if}>{$group.name}</option>
            {/foreach}
        </select>
    </td>
    <td>
   <input class = "flatButton" type = "button" value="{$smarty.const._SUBMIT}" onclick = "document.location='{$smarty.server.PHP_SELF}?ctg=statistics&option={$smarty.get.option}{if (isset($smarty.get.tab))}&tab={$smarty.get.tab}{/if}&sel_{$smarty.get.option}={$T_STATS_ENTITY_ID}&group_filter='+$('group_filter').options[$('group_filter').selectedIndex].value+'&user_filter='+$('user_filter').options[$('user_filter').selectedIndex].value+'{$dates_url}';">
  </td>
