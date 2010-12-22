{*moduleProgress: The Progress page*}
    {capture name = "moduleProgress"}
 <tr><td class = "moduleCell">
 {if $smarty.get.edit_user || $_student_}
     {capture name = 't_edit_progress_code'}
         {if $T_CONDITIONS}
         <fieldset class = "fieldsetSeparator">
             <legend>{$smarty.const._LESSONCONDITIONS}</legend>
             <table>
                 {foreach name = 'conditions_loop' key = key item = condition from = $T_CONDITIONS}
                     <tr><td style = "color:{if $T_CONDITIONS_STATUS[$key]}green{else}red{/if}">
                     {if $smarty.foreach.conditions_loop.total > 1}{if $condition.relation == 'and'}&nbsp;{$smarty.const._AND}&nbsp;{else}&nbsp;{$smarty.const._OR}&nbsp;{/if}{/if}
                     {if $condition.type == 'all_units'}
                         {$smarty.const._MUSTSEEALLUNITS}{if !$T_CONDITIONS_STATUS[$key]}<img src = "images/16x16/forbidden.png" title = "{$smarty.const._CONDITIONNOTMET}" alt = "{$smarty.const._CONDITIONNOTMET}" style = "vertical-align:middle;margin-left:25px">{else}<img src = "images/16x16/success.png" title = "{$smarty.const._CONDITIONMET}" alt = "{$smarty.const._CONDITIONMET}" style = "vertical-align:middle;margin-left:25px">{/if}
                     {elseif $condition.type == 'percentage_units'}
                         {$smarty.const._MUSTSEE} {$condition.options.0}% {$smarty.const._OFLESSONUNITS}{if !$T_CONDITIONS_STATUS[$key]}<img src = "images/16x16/forbidden.png" title = "{$smarty.const._CONDITIONNOTMET}" alt = "{$smarty.const._CONDITIONNOTMET}" style = "vertical-align:middle;margin-left:25px">{else}<img src = "images/16x16/success.png" title = "{$smarty.const._CONDITIONMET}" alt = "{$smarty.const._CONDITIONMET}" style = "vertical-align:middle;margin-left:25px">{/if}
                     {elseif $condition.type == 'specific_unit'}
                         {$smarty.const._MUSTSEEUNIT} &quot;{$T_TREE_NAMES[$condition.options.0]}&quot;{if !$T_CONDITIONS_STATUS[$key]}<img src = "images/16x16/forbidden.png" title = "{$smarty.const._CONDITIONNOTMET}" alt = "{$smarty.const._CONDITIONNOTMET}" style = "vertical-align:middle;margin-left:25px">{else}<img src = "images/16x16/success.png" title = "{$smarty.const._CONDITIONMET}" alt = "{$smarty.const._CONDITIONMET}" style = "vertical-align:middle;margin-left:25px">{/if}
                     {elseif $condition.type == 'all_tests'}
                         {$smarty.const._MUSTCOMPLETEALLTESTS}{if !$T_CONDITIONS_STATUS[$key]}<img src = "images/16x16/forbidden.png" title = "{$smarty.const._CONDITIONNOTMET}" alt = "{$smarty.const._CONDITIONNOTMET}" style = "vertical-align:middle;margin-left:25px">{else}<img src = "images/16x16/success.png" title = "{$smarty.const._CONDITIONMET}" alt = "{$smarty.const._CONDITIONMET}" style = "vertical-align:middle;margin-left:25px">{/if}
                     {elseif $condition.type == 'specific_test'}
                         {$smarty.const._MUSTCOMPLETETEST} &quot;{$T_TREE_NAMES[$condition.options.0]}&quot; {$smarty.const._WITHSCORE} {$condition.options.1}%{if !$T_CONDITIONS_STATUS[$key]}<img src = "images/16x16/forbidden.png" title = "{$smarty.const._CONDITIONNOTMET}" alt = "{$smarty.const._CONDITIONNOTMET}" style = "vertical-align:middle;margin-left:25px">{else}<img src = "images/16x16/success.png" title = "{$smarty.const._CONDITIONMET}" alt = "{$smarty.const._CONDITIONMET}" style = "vertical-align:middle;margin-left:25px">{/if}
                     {/if}
                         </td></tr>
                 {/foreach}
             </table>
         </fieldset>
         {/if}
         <fieldset class = "fieldsetSeparator">
             <legend>{$smarty.const._LESSONPROGRESS}</legend>
             <table>
              <tr><td colspan = "3">{$smarty.const._TIMEINLESSON}:
                      {if $T_USER_TIME.hours == 1}{$T_USER_TIME.hours} {$smarty.const._HOUR}{elseif $T_USER_TIME.hours > 1}{$T_USER_TIME.hours} {$smarty.const._HOURS}{/if}
                      {if $T_USER_TIME.minutes == 1}{$T_USER_TIME.minutes} {$smarty.const._MINUTE}{elseif $T_USER_TIME.minutes > 1}{$T_USER_TIME.minutes} {$smarty.const._MINUTES}{/if}
                      {if $T_USER_TIME.seconds == 1}{$T_USER_TIME.seconds} {$smarty.const._SECOND}{elseif $T_USER_TIME.seconds > 1}{$T_USER_TIME.seconds} {$smarty.const._SECONDS}{/if}
                  </td>
              </tr>
              <tr><td>{$smarty.const._OVERALLPROGRESS}:&nbsp;</td>
                  <td class = "progressCell">
                      <span class = "progressNumber">#filter:score-{$T_USER_LESSONS_INFO.overall_progress.percentage}#%</span>
                      <span class = "progressBar" style = "width:{$T_USER_LESSONS_INFO.overall_progress.percentage}px;">&nbsp;</span>
                  </td><td></td>
              </tr>
              <tr><td>{$smarty.const._AVERAGETESTSCOREOFLASTEXECUTIONS}:&nbsp;</td>
                  <td class = "progressCell">
                      <span class = "progressNumber">#filter:score-{$T_USER_LESSONS_INFO.test_status.mean_score}#%</span>
                      <span class = "progressBar" style = "width:{$T_USER_LESSONS_INFO.test_status.mean_score}px;">&nbsp;</span>
                  </td><td></td>
              </tr>
              {foreach name = 'done_tests_list' item = "test" key = "id" from = $T_USER_LESSONS_INFO.done_tests}
              <tr><td>{$smarty.const._TEST} <span class = "innerTableName">&quot;{$test.name}&quot;</span> ({$smarty.const._AVERAGESCOREON} {$test.times_done} {if $test.times_done == 1}{$smarty.const._EXECUTION|@mb_strtolower}{else}{$smarty.const._EXECUTIONS|@mb_strtolower}{/if}):&nbsp;</td>
                  <td class = "progressCell">
                      <span class = "progressNumber">#filter:score-{$test.score}#%</span>
                      <span class = "progressBar" style = "width:{$test.score}px;">&nbsp;</span>
                  </td><td></td>
              </tr>
              <tr><td>{$smarty.const._TEST} <span class = "innerTableName">&quot;{$test.name}&quot;</span> ({$smarty.const._SCOREONLASTEXECUTION}):&nbsp;</td>
                  <td class = "progressCell">
                      <span class = "progressNumber">#filter:score-{$test.last_score}#%</span>
                      <span class = "progressBar" style = "width:{$test.last_score}px;">&nbsp;</span>
                  </td><td>

                      <a href = "{$smarty.server.PHP_SELF}?ctg={if $_student_}content&view_unit={$test.content_ID}{else}tests{/if}&show_solved_test={$test.last_test_id}">
                          <img class = "handle" src = "images/16x16/search.png" title = "{$smarty.const._VIEWTEST}" alt = "{$smarty.const._VIEWTEST}">
                      </a>
                  </td>
              </tr>
              {foreachelse}
               {if $T_USER_LESSONS_INFO.scorm_done_tests|@sizeof == 0}
                <tr><td colspan = "3" class = "emptyCategory">{$smarty.const._TESTS}: {$smarty.const._NODATAFOUND}</td></tr>
    {/if}
              {/foreach}
              {foreach name = 'scorm_done_tests_list' item = "test" key = "id" from = $T_USER_LESSONS_INFO.scorm_done_tests}
              <tr><td>{$smarty.const._TEST} <span class = "innerTableName">&quot;{$test.name}&quot;</span></td>
                  <td class = "progressCell">
                      <span class = "progressNumber">#filter:score-{$test.score}#%</span>
                      <span class = "progressBar" style = "width:{$test.score}px;">&nbsp;</span>
                  </td><td></td>
              </tr>
              {/foreach}
              {foreach name = 'pending_tests_list' item = "name" key = "id" from = $T_PENDING_TESTS}
                  <tr><td>{$smarty.const._TEST} <span class = "innerTableName">&quot;{$name}&quot;</span>:</td>
                      <td class = "emptyCategory" colspan = "2">{$smarty.const._USERHASNOTDONETEST}</td>
                  </tr>
              {/foreach}



              {if $T_USER_PROJECTS}
              <tr><td>{$smarty.const._AVERAGEPROJECTSCORE}:&nbsp;</td>
                  <td class = "progressCell">
                      <span class = "progressNumber">#filter:score-{$T_USER_LESSONS_INFO.project_status.mean_score}#%</span>
                      <span class = "progressBar" style = "width:{$T_USER_LESSONS_INFO.project_status.mean_score}px;">&nbsp;</span>
                 </td><td></td>
              </tr>
              {/if}

              {foreach name = 'done_projects_list' item = "project" key = "id" from = $T_USER_PROJECTS}
                  <tr><td>{$smarty.const._PROJECT} <span class = "innerTableName">&quot;{$project.title}&quot;</span>:</td>
                  {if $project.grade}
                      <td class = "progressCell">
                          <span class = "progressNumber">#filter:score-{$project.grade}#%</span>
                          <span class = "progressBar" style = "width:{$project.grade}px;">&nbsp;</span>
                      </td><td>
                          {if $project.timestamp}(#filter:timestamp-{$project.timestamp}#) &nbsp;{/if}
                      </td>
                  {else}
                      <td class = "emptyCategory" colspan = "2">{$smarty.const._PROJECTPENDING}</td>
                  {/if}
                  </tr>
              {foreachelse}
                  <tr><td colspan = "3" class = "emptyCategory">{$smarty.const._PROJECTS}: {$smarty.const._NODATAFOUND}</td></tr>
              {/foreach}
          </table>
         </fieldset>
         <fieldset class = "fieldsetSeparator">
             <legend>{$smarty.const._COMPLETELESSON}</legend>
             {$T_COMPLETE_LESSON_FORM.javascript}
             <form {$T_COMPLETE_LESSON_FORM.attributes}>
                 {$T_COMPLETE_LESSON_FORM.hidden}
                 <table class = "formElements">
                     <tr><td class = "labelCell">{$T_COMPLETE_LESSON_FORM.completed.label}&nbsp;:</td>
                         <td class = "elementCell">{$T_COMPLETE_LESSON_FORM.completed.html}</td></tr>
                     <tr><td class = "labelCell">{$T_COMPLETE_LESSON_FORM.score.label}&nbsp;:</td>
                         <td class = "elementCell">{$T_COMPLETE_LESSON_FORM.score.html}</td></tr>
                     {if $T_COMPLETE_LESSON_FORM.score.error}<tr><td></td><td class = "formError">{$T_COMPLETE_LESSON_FORM.score.error}</td></tr>{/if}
                     {if $T_STUDENT_ROLE != true}
       <tr><td></td>
        <td><span>
        <img style="vertical-align:middle" src = "images/16x16/order.png" title = "{$smarty.const._TOGGLEHTMLEDITORMODE}" alt = "{$smarty.const._TOGGLEHTMLEDITORMODE}" />&nbsp;
        <a href = "javascript:toggleEditor('comments','simpleEditor');" id = "toggleeditor_link">{$smarty.const._TOGGLEHTMLEDITORMODE}</a>
        </span></td>
       </tr>
      {/if}
      <tr><td class = "labelCell">{$T_COMPLETE_LESSON_FORM.comments.label}&nbsp;:</td>
                         <td class = "elementCell">{$T_COMPLETE_LESSON_FORM.comments.html}</td></tr>
                     {if $T_COMPLETE_LESSON_FORM.comments.error}<tr><td></td><td class = "formError">{$T_COMPLETE_LESSON_FORM.comments.error}</td></tr>{/if}
                     <tr><td colspan = "100%">&nbsp;</td></tr>
                     <tr><td></td><td>{$T_COMPLETE_LESSON_FORM.submit_lesson_complete.html}</td></tr>
                 </table>
             </form>
         </fieldset>
      {foreach name = "module_fieldsets_list" item = "fieldset" key = "key" from=$T_MODULE_FIELDSETS}
          <fieldset class = "fieldsetSeparator">
           <legend>{$fieldset.title}</legend>
              {include file = $fieldset.file}
          </fieldset>
   {/foreach}
     {/capture}
     {eF_template_printBlock title = "`$smarty.const._PROGRESSFORUSER`: <span class = 'innerTableName'>&quot;#filter:login-`$T_USER_LESSONS_INFO.users_LOGIN`#&quot;</span>" data = $smarty.capture.t_edit_progress_code image = '32x32/users.png' help = 'Users_status'}
 {else}
         {capture name = 't_progress_code'}
 {if !$T_SORTED_TABLE || $T_SORTED_TABLE == 'usersTable'}
<!--ajax:usersTable-->
                 <table style = "width:100%" activeFilter = "1" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "0" id = "usersTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=progress&">
                     <tr class = "topTitle">
                         <td class = "topTitle" name = "login">{$smarty.const._USER}</td>
                         {*<td class = "topTitle centerAlign" name = "conditions_passed" >{$smarty.const._CONDITIONSCOMPLETED}</td>*}
                         <td class = "topTitle centerAlign" name = "completed" >{$smarty.const._LESSONSTATUS}</td>
                         <td class = "topTitle centerAlign" name = "score" >{$smarty.const._LESSONSCORE}</td>
                         <td class = "topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
                     </tr>
         {foreach name = 'users_progress_list' item = 'item' key = 'login' from = $T_DATA_SOURCE}
                     <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$item.active}deactivatedTableElement{/if}">
                         <td><a href = "{$smarty.server.PHP_SELF}?ctg=progress&edit_user={$item.login}" class = "editLink">#filter:login-{$item.login}#</a></td>
{*
                         <td class = "centerAlign">
                             {$item.conditions_passed}/{$item.total_conditions}
                         </td>
*}
                         <td class = "centerAlign">
                             {if $item.completed}
                                 <img src = "images/16x16/success.png" title = "{$smarty.const._COMPLETED}" alt = "{$smarty.const._COMPLETED}" />
                             {elseif $item.lesson_passed}
                                 <img src = "images/16x16/lessons.png" title = "{$smarty.const._CONDITIONSMET}" alt = "{$smarty.const._CONDITIONSMET}" />
                             {else}
                                 <img src = "images/16x16/forbidden.png" title = "{$smarty.const._NOTCOMPLETED}" alt = "{$smarty.const._NOTCOMPLETED}" />
                             {/if}
                         </td>
                         <td class = "centerAlign">{if $item.score}#filter:score-{$item.score}#%{/if}</td>
                         <td class = "centerAlign">
                             <a href = "{$smarty.server.PHP_SELF}?ctg=progress&edit_user={$item.login}" title = "{$smarty.const._VIEWUSERLESSONPROGRESS}">
                                 <img src = "images/16x16/search.png" title = "{$smarty.const._VIEWUSERLESSONPROGRESS}" alt = "{$smarty.const._VIEWUSERLESSONPROGRESS}" border = "0"/>
                             </a>
                         </td>
                     </tr>
         {foreachelse}
                 <tr class = "{cycle values = "oddRowColor, evenRowColor"} defaultRowHeight"><td colspan = "100%" class = "emptyCategory">{$smarty.const._NOUSERDATAFOUND}</td></tr>
         {/foreach}
             </table>
<!--/ajax:usersTable-->
{/if}
         {/capture}
         {eF_template_printBlock title = $smarty.const._USERSPROGRESS data = $smarty.capture.t_progress_code image = '32x32/users.png' help = 'Users_status'}
 {/if}
 </td></tr>
 {/capture}
