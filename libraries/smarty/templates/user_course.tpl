{include file = "includes/header.tpl"}
<table align = "center">
    <tr>
        <th class = "topTitle" style = "width:20%;white-space:nowrap"> {$smarty.const._LESSON} </th>
        <th class = "topTitle centerAlign" style = "width:20%;white-space:nowrap"> {$smarty.const._CONTENT} </th>
        <th class = "topTitle centerAlign" style = "width:20%;white-space:nowrap"> {$smarty.const._TESTS} </th>
        <th class = "topTitle centerAlign" style = "width:20%;white-space:nowrap"> {$smarty.const._PROJECTS} </th>
        <th class = "topTitle centerAlign" style = "width:20%;white-space:nowrap"> {$smarty.const._COMPLETED} </th>
    </tr>
    {foreach name = 'lesson_list' key = 'lesson_id' item = 'lesson_name' from = $T_LESSON_NAMES}
    <tr class = "{cycle name = "user_lessons_list" values = "oddRowColor, evenRowColor"}">
        <td> 
           {$lesson_name}
        </td>
        <td class = "progressCell" style = "vertical-align:top">
            <span class = "progressNumber">{$T_LESSON_CONTENT[$lesson_id]}%</span>
            <span class = "progressBar" style = "width:{$T_LESSON_CONTENT[$lesson_id]}px;">&nbsp;</span>
        </td>     
        <td class = "progressCell" style = "vertical-align:top">
            <span class = "progressNumber">{$T_LESSON_TESTS[$lesson_id]}%</span>
            <span class = "progressBar" style = "width:{$T_LESSON_TESTS[$lesson_id]}px;">&nbsp;</span>
        </td>  
        <td class = "progressCell" style = "vertical-align:top">
            <span class = "progressNumber">{$T_LESSON_PROJECTS[$lesson_id]}%</span>
            <span class = "progressBar" style = "width:{$T_LESSON_PROJECTS[$lesson_id]}px;">&nbsp;</span>
        </td>  
        <td style = "text-align:center">
            {$T_LESSON_COMPLETED[$lesson_id]}
        </td>
    </tr>
    {/foreach}    
</table>