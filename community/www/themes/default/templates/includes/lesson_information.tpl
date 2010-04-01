{if !isset($T_CURRENT_USER->coreAccess.news) || $T_CURRENT_USER->coreAccess.news == 'change'}
 {assign var = "_change_" value = 1}
{/if}


        {*moduleLessonInformation: Show lesson information*}
        {capture name = "moduleLessonInformation"}
                                <tr><td class = "moduleCell">
{if $smarty.get.edit_info}
                                {capture name = 't_lesson_info_code'}
                                    <fieldset class = "fieldsetSeparator">
                                        <legend>{$smarty.const._LESSONINFORMATION}</legend>
                                        {$T_LESSON_INFO_HTML}
                                    </fieldset>
                                    <fieldset class = "fieldsetSeparator">
                                        <legend>{$smarty.const._LESSONMETADATA}</legend>
                                        {$T_LESSON_METADATA_HTML}
                                    </fieldset>
{*
                                    <fieldset class = "fieldsetSeparator">
                                        <legend>{$smarty.const._LESSONAVATAR}</legend>
                                    </fieldset>
*}
                                {/capture}
                                {eF_template_printBlock title = $smarty.const._INFORMATIONFORLESSON|cat:' &quot;'|cat:$T_CURRENT_LESSON->lesson.name|cat:'&quot;' data = $smarty.capture.t_lesson_info_code image = '32x32/information.png'}
{else}
 {capture name = 't_lesson_info_code'}
     {*if $T_LESSON_PASSED}<div style = "padding-bottom:10px" class = "centerAlign success mediumHeader">{$smarty.const._YOUHAVECOMPLETEDTHELESSON}</div>{/if*}
     {if !$_student_ && $_change_}
     <div class = "headerTools">
      <span>
       <img src = "images/16x16/edit.png" alt = "{$smarty.const._EDIT}" title = "{$smarty.const._EDIT}">
       <a href = "{$smarty.server.PHP_SELF}?{$T_BASE_URL}&edit_info=1" title = "{$smarty.const._EDITINFORMATION}">{$smarty.const._EDITINFORMATION}</a>
      </span>
      {if !$_admin_}
      <span>
       <img src = "images/16x16/edit.png" alt = "{$smarty.const._EDIT}" title = "{$smarty.const._EDIT}" >
       <a href = "{$smarty.server.PHP_SELF}?ctg=rules&tab=conditions" title = "{$smarty.const._EDITCONDITIONS}">{$smarty.const._EDITCONDITIONS}</a>
      </span>
      {/if}
     </div>
     {/if}
     <table>
         <tr><td><img style="vertical-align:middle;" src="images/16x16/user.png" alt="{$smarty.const._PROFESSORS}" title="{$smarty.const._PROFESSORS}"/><span style="vertical-align:middle;">&nbsp;{$smarty.const._PROFESSORS}:&nbsp;</span></td>
             <td>
         {foreach name = 'lesson_professors' key = 'login' item = 'user' from = $T_LESSON_INFO.professors}
     #filter:login-{$login}#{if !$smarty.foreach.lesson_professors.last},&nbsp;{/if}
         {/foreach}
             </td></tr>
         {if $T_LESSON_INFO.content} <tr><td style = "white-space:nowrap"><img style="vertical-align:middle;" src="images/16x16/theory.png" alt="{$smarty.const._CONTENT}" title="{$smarty.const._CONTENT}"/>&nbsp;{$smarty.const._CONTENT}:&nbsp; </td><td>{$T_LESSON_INFO.content} {$smarty.const._UNITS} </td></tr>{/if}
         {if $T_LESSON_INFO.tests} <tr><td style = "white-space:nowrap"><img style="vertical-align:middle;" src="images/16x16/tests.png" alt="{$smarty.const._TESTS}" title="{$smarty.const._TESTS}"/>&nbsp;{$smarty.const._TESTS}:&nbsp; </td><td>{$T_LESSON_INFO.tests} {$smarty.const._TESTS} </td></tr>{/if}
         {if $T_LESSON_INFO.projects}<tr><td style = "white-space:nowrap"><img style="vertical-align:middle;" src="images/16x16/projects.png" alt="{$smarty.const._PROJECTS}" title="{$smarty.const._PROJECTS}"/>&nbsp;{$smarty.const._PROJECTS}:&nbsp;</td><td>{$T_LESSON_INFO.projects} {$smarty.const._PROJECTS}</td></tr>{/if}
     </table>
     <table>
         {foreach name = 'lesson_info_list' key = key item = item from = $T_LESSON_INFO_CATEGORIES}
             {if $T_LESSON_INFO[$key]}
                 <tr><td>&nbsp;</td></tr>
                 <tr><td class = "mediumHeader" style = "text-align: left">
                   {$T_LESSON_INFO_CATEGORIES.$key}
                  </td></tr>
                 <tr><td>&nbsp;</td></tr>
                 <tr><td>{$T_LESSON_INFO[$key]}</td></tr>
                 <tr><td class = "horizontalSeparator"></td></tr>
             {/if}
         {foreachelse}
                 <tr><td class = "emptyCategory">{$smarty.const._NODESCRIPTIONSET}</td></tr>
         {/foreach}
         {if $T_CURRENT_LESSON->options.tracking}
                 <tr><td>&nbsp;</td></tr>
                 <tr><td class = "mediumHeader" style = "text-align: left">
                   {$smarty.const._LESSONCONDITIONS}
                  </td></tr>
                 <tr><td>&nbsp;</td></tr>
             {foreach name = 'conditions_loop' key = key item = condition from = $T_CONDITIONS}
                 <tr><td style = "color:{if !$_student_}{elseif $T_CONDITIONS_STATUS[$key]}green{else}red{/if}">
                 {if $smarty.foreach.conditions_loop.total > 1}{if $condition.relation == 'and'}&nbsp;{$smarty.const._AND}&nbsp;{else}&nbsp;{$smarty.const._OR}&nbsp;{/if}{/if}
                 {if $condition.type == 'all_units'}
                     {$smarty.const._YOUMUSTSEEALLUNITS}
                 {elseif $condition.type == 'percentage_units'}
                     {$smarty.const._YOUMUSTSEE} {$condition.options.0}% {$smarty.const._OFLESSONUNITS}
                 {elseif $condition.type == 'specific_unit'}
                     {$smarty.const._YOUMUSTSEEUNIT} &quot;{$T_TREE_NAMES[$condition.options.0]}&quot;
                 {elseif $condition.type == 'all_tests'}
                     {$smarty.const._YOUMUSTCOMPLETEALLTESTSWITHSCORE} {$condition.options.0}%
                 {elseif $condition.type == 'specific_test'}
                     {$smarty.const._YOUMUSTCOMPLETETEST} &quot;{$T_TREE_NAMES[$condition.options.0]}&quot; {$smarty.const._WITHSCORE} #filter:score-{$condition.test_passing_score}#%
                 {/if}
                 {if $_student_}
                     {if !$T_CONDITIONS_STATUS[$key]}<img src = "images/16x16/forbidden.png" title = "{$smarty.const._CONDITIONNOTMET}" alt = "{$smarty.const._CONDITIONNOTMET}" style = "vertical-align:middle;margin-left:25px">{else}<img src = "images/16x16/success.png" title = "{$smarty.const._CONDITIONMET}" alt = "{$smarty.const._CONDITIONMET}" style = "vertical-align:middle;margin-left:25px">{/if}
                 {/if}
                     </td></tr>
             {foreachelse}
                 <tr><td class = "emptyCategory">{$smarty.const._NOCONDITIONSSET}</td></tr>
             {/foreach}
                 <tr><td class = "horizontalSeparator"></td></tr>
         {/if}
     </table>
 {/capture}
 {eF_template_printBlock title = $smarty.const._INFORMATIONFORLESSON|cat:' &quot;'|cat:$T_CURRENT_LESSON->lesson.name|cat:'&quot;' data = $smarty.capture.t_lesson_info_code image = '32x32/information.png' help = 'Lesson_information'}
{/if}
                                </td></tr>
        {/capture}
