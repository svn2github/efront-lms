        {capture name = 'lesson_statistics'}
            <table class = "statisticsTools statisticsSelectList">
                <tr><td class = "labelCell">{$smarty.const._CHOOSELESSON}:</td>
                    <td class = "elementCell" colspan = "4">
                        <input type = "text" id = "autocomplete" class = "autoCompleteTextBox"/>
                        <img id = "busy" src = "images/16x16/clock.png" style="display:none;" alt = "{$smarty.const._LOADING}" title = "{$smarty.const._LOADING}"/>
                        <div id = "autocomplete_lessons" class = "autocomplete"></div>&nbsp;&nbsp;&nbsp;
                    </td>
                </tr>
                <tr><td></td>
                 <td class = "infoCell" colspan = "4">{$smarty.const._STARTTYPINGFORRELEVENTMATCHES}</td></tr>
        {if !isset($T_CURRENT_LESSON)}
         </table>
        {else}

    <tr>
     {include file = "includes/statistics/stats_filters.tpl"}

                    <td id = "right">
                        {$smarty.const._EXPORTSTATS}
                        <a href = "{$T_BASIC_TYPE}.php?ctg=statistics&option=lesson&sel_lesson={$smarty.get.sel_lesson}&group_filter={$smarty.get.group_filter}&excel=lesson&branch_filter={$smarty.get.branch_filter}">
                            <img src = "images/file_types/xls.png" title = "{$smarty.const._XLSFORMAT}" alt = "{$smarty.const._XLSFORMAT}"/>
                        </a>
                        <a href = "{$T_BASIC_TYPE}.php?ctg=statistics&option=lesson&sel_lesson={$smarty.get.sel_lesson}&group_filter={$smarty.get.group_filter}&pdf=lesson&branch_filter={$smarty.get.branch_filter}">
                            <img src = "images/file_types/pdf.png" title = "{$smarty.const._PDFFORMAT}" alt = "{$smarty.const._PDFFORMAT}"/>
                        </a>
                    </td>
                 </tr>
         </table>

            <br />
            <table class = "statisticsGeneralInfo">
                <tr class = "{cycle name = 'common_lesson_info' values = 'oddRowColor, evenRowColor'}">
                    <td class = "labelCell">{$smarty.const._NAME}:</td>
                    <td class = "elementCell">{$T_CURRENT_LESSON->lesson.name}</td>
                </tr>
                <tr class = "{cycle name = 'common_lesson_info' values = 'oddRowColor, evenRowColor'}">
                    <td class = "labelCell">{$smarty.const._CATEGORY}:</td>
                    <td class = "elementCell">{$T_CURRENT_LESSON->lesson.category_path}</td>
                </tr>
                <tr class = "{cycle name = 'common_lesson_info' values = 'oddRowColor, evenRowColor'}">
                    <td class = "labelCell">{$smarty.const._USERS}:</td>
                    <td class = "elementCell">{if $T_CURRENT_LESSON->lesson.num_users}{$T_CURRENT_LESSON->lesson.num_users} ({foreach name = "user_types_list" item ="item" key = "key" from = $T_CURRENT_LESSON->lesson.users_per_role}{$T_ROLES_ARRAY[$key]}: {$item}{if !$smarty.foreach.user_types_list.last}, {/if}{/foreach}){else}0{/if}</td>
                </tr>
            </table>

            <div class = "tabber">
                <div class = "statisticsDiv tabbertab {if (isset($smarty.get.tab) &&  $smarty.get.tab == 'users')} tabbertabdefault{/if}" title = "{$smarty.const._USERS}">
 {if !$T_SORTED_TABLE || $T_SORTED_TABLE == 'lessonUsersTable'}
<style>
{literal}
table#lessonUsersTable {width:100%;}
table#lessonUsersTable td.login{width:30%;}
table#lessonUsersTable td.user_type{width:10%;}
table#lessonUsersTable td.time_in_lesson{width:15%;}
table#lessonUsersTable td.overall_progress{width:100px;}
table#lessonUsersTable td.test_status{width:100px;}
table#lessonUsersTable td.project_status{width:100px;}
table#lessonUsersTable td.completed{width:5%;text-align:center;}
table#lessonUsersTable td.score{width:5%;text-align:center;}
{/literal}
</style>
<!--ajax:lessonUsersTable-->
  <table id = "lessonUsersTable" sortBy=0 size = "{$T_TABLE_SIZE}" class = "sortedTable" useAjax = "1" url = "{$smarty.server.PHP_SELF}?ctg=statistics&option=lesson&sel_lesson={$smarty.get.sel_lesson}{$T_STATS_FILTERS_URL}&">
   <tr class = "topTitle">
    <td class = "topTitle login" name = "login">{$smarty.const._USER}</td>
    <td class = "topTitle user_type" name = "role">{$smarty.const._USERTYPE}</td>
    <td class = "topTitle time_in_lesson noSort" name = "time_in_lesson">{$smarty.const._TIMEINLESSON}</td>
    <td class = "topTitle overall_progress noSort" name = "overall_progress">{$smarty.const._OVERALLPROGRESS}</td>
   {if !$T_CONFIGURATION.disable_tests}
    <td class = "topTitle test_status noSort" name = "test_status">{$smarty.const._TESTSSCORE}</td>
   {/if}
   {if !$T_CONFIGURATION.disable_projects}
    <td class = "topTitle project_status noSort" name = "project_status">{$smarty.const._PROJECTSSCORE}</td>
   {/if}
    <td class = "topTitle completed" name = "completed">{$smarty.const._COMPLETED}</td>
    <td class = "topTitle score" name = "score">{$smarty.const._SCORE}</td>
   </tr>
   {foreach name = 'users_to_lessons_list' key = 'key' item = 'user' from = $T_DATA_SOURCE}
   <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$user.active}deactivatedTableElement{/if}">
    <td class = "name">#filter:login-{$user.login}#{* ({$T_ROLES[$user.user_type]})*}</td>
    <td class = "user_type">{$T_ROLES_ARRAY[$user.role]}</td>
    <td class = "time_in_lesson"><span style = "display:none">{$user.time_in_lesson.total_seconds}&nbsp;</span>{$user.time_in_lesson.time_string}</td>
    <td class = "progressCell overall_progress">
     {if $user.basic_user_type != 'professor'}
     <span style = "display:none">{$user.overall_progress.completed+1000}</span>
     <span class = "progressNumber">#filter:score-{$user.overall_progress.percentage}#%</span>
     <span class = "progressBar" style = "width:{$user.overall_progress.percentage}px;">&nbsp;</span>&nbsp;&nbsp;
     {else}<div class = "centerAlign">-</div>{/if}
    </td>
    {if !$T_CONFIGURATION.disable_tests}
     <td class = "progressCell test_status">
     {if $user.test_status && $user.basic_user_type != 'professor'}
      <span style = "display:none">{$user.test_status.mean_score+1000}</span>
      <span class = "progressNumber">#filter:score-{$user.test_status.mean_score}#% ({$user.test_status.completed}/{$user.test_status.total})</span>
      <span class = "progressBar" style = "width:{$user.test_status.mean_score}px;">&nbsp;</span>&nbsp;&nbsp;
     {else}<div class = "centerAlign">-</div>{/if}
     </td>
    {/if}
    {if !$T_CONFIGURATION.disable_projects}
     <td class = "progressCell project_status">
     {if $user.project_status && $user.basic_user_type != 'professor'}
      <span style = "display:none">{$user.project_status.mean_score+1000}</span>
      <span class = "progressNumber">#filter:score-{$user.project_status.mean_score}#% ({$user.project_status.completed}/{$user.project_status.total})</span>
      <span class = "progressBar" style = "width:{$user.project_status.mean_score}px;">&nbsp;</span>&nbsp;&nbsp;
     {else}<div class = "centerAlign">-</div>{/if}
     </td>
    {/if}
    <td class = "completed">
    {if $user.basic_user_type != 'professor'}
     {if $user.completed}<img src = "images/16x16/success.png" alt = "{$smarty.const._YES}" title = "{$smarty.const._YES}"/>{else}<img src = "images/16x16/forbidden.png" alt = "{$smarty.const._NO}" title = "{$smarty.const._NO}"/>{/if}</td>
    {else}<div class = "centerAlign">-</div>{/if}
    <td class = "score">{if $user.basic_user_type != 'professor'}#filter:score-{$user.score}#%{else}<div class = "centerAlign">-</div>{/if}</td>
   </tr>
   {foreachelse}
   <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "{$T_DATASOURCE_COLUMNS|@sizeof}">{$smarty.const._NODATAFOUND}</td></tr>
   {/foreach}
  </table>
<!--/ajax:lessonUsersTable-->
 {/if}



                </div>

                {if !empty($T_TESTS_INFO) && $T_CONFIGURATION.disable_tests != 1}
                <div class = "statisticsDiv tabbertab {if (isset($smarty.get.tab) &&  $smarty.get.tab == 'tests')} tabbertabdefault{/if}" title = "{$smarty.const._TESTS}">
                {foreach key = "test_id" item = "test_info" from = $T_TESTS_INFO}
                    {if !$test_info.general.scorm}
     <table class = "statisticsTools">
                        <tr><td>
                                <a href = "javascript:void(0)" onclick = "toggleVisibility($('tinfo{$test_id}'), Element.extend(this).down())">
         <img src = "images/16x16/navigate_down.png" title = "{$smarty.const._SHOWHIDE}" alt = "{$smarty.const._SHOWHIDE}"/>
         {$smarty.const._TEST}: {$test_info.general.name}</a>
                            </td>
                            <td id = "right">
                                <a href = "display_chart.php?id=2&lesson_id={$smarty.get.sel_lesson}&test_id={$test_info.general.id}" onclick = "eF_js_showDivPopup('{$smarty.const._QUESTIONSKIND}', 2)" target = "POPUP_FRAME">
                                 {$smarty.const._QUESTIONSKIND}: <img src = "images/16x16/reports.png" alt = "{$smarty.const._QUESTIONSKIND}" title = "{$smarty.const._QUESTIONSKIND}"/></a>
                                {$smarty.const._QUESTIONSKIND}: <img class = "ajaxHandle" src = "images/16x16/reports.png" alt = "{$smarty.const._QUESTIONSKIND}" title = "{$smarty.const._QUESTIONSKIND}" onclick = "eF_js_showDivPopup('{$smarty.const._ACCESSSTATISTICS}', 2, 'graph_table');showGraph($('proto_chart'), 'graph_test_questions', '{$test_info.general.id}');"/>
                            </td>
                    </table>
                    <table class = "statisticsSubInfo" id = "tinfo{$test_id}" style = "display:none">
                        <tr>
                            <td class = "topTitle leftAlign">{$smarty.const._TESTINFO}</td>
                            <td>&nbsp;</td>
                            <td class = "topTitle leftAlign">{$smarty.const._QUESTIONINFO}</td>
                        </tr>
                        <tr><td>
                                <table>
                                    <tr class = "{cycle name = 'test_info' values = 'oddRowColor, evenRowColor'}"><td>{$smarty.const._TESTDURATION}:</td><td>{if $test_info.general.duration_str.hours}{$test_info.general.duration_str.hours}{$smarty.const._HOURSSHORTHAND} {/if}{if $test_info.general.duration_str.minutes}{$test_info.general.duration_str.minutes}{$smarty.const._MINUTESSHORTHAND} {/if}{if $test_info.general.duration_str.seconds}{$test_info.general.duration_str.seconds}{$smarty.const._SECONDSSHORTHAND}{/if}</td></tr>
                                    <tr class = "{cycle name = 'test_info' values = 'oddRowColor, evenRowColor'}"><td>{$smarty.const._REDOABLE}:</td><td>{$test_info.general.redoable_str}</td></tr>
                                    <tr class = "{cycle name = 'test_info' values = 'oddRowColor, evenRowColor'}"><td>{$smarty.const._ONEBYONE}:</td><td>{$test_info.general.onebyone_str}</td></tr>
                                    <tr class = "{cycle name = 'test_info' values = 'oddRowColor, evenRowColor'}"><td>{$smarty.const._CREATED}:</td><td>#filter:timestamp-{$test_info.general.timestamp}#</td></tr>
                                </table>
                            </td>
                            <td>&nbsp;</td>
                            <td>
                                <table>
                                    <tr class = "{cycle name = 'question_info' values = 'oddRowColor, evenRowColor'}"><td>{$smarty.const._TOTALQUESTIONS}:</td><td>{$test_info.questions.total}</td></tr>
                                    <tr class = "{cycle name = 'question_info' values = 'oddRowColor, evenRowColor'}"><td>{$smarty.const._DEVELOPMENT}:</td><td>{$test_info.questions.raw_text}</td></tr>
                                    <tr class = "{cycle name = 'question_info' values = 'oddRowColor, evenRowColor'}"><td>{$smarty.const._MULTIPLEONE}:</td><td>{$test_info.questions.multiple_one}</td></tr>
                                    <tr class = "{cycle name = 'question_info' values = 'oddRowColor, evenRowColor'}"><td>{$smarty.const._MULTIPLEMANY}:</td><td>{$test_info.questions.multiple_many}</td></tr>
                                    <tr class = "{cycle name = 'question_info' values = 'oddRowColor, evenRowColor'}"><td>{$smarty.const._TRUEFALSE}:</td><td>{$test_info.questions.true_false}</td></tr>
                                    <tr class = "{cycle name = 'question_info' values = 'oddRowColor, evenRowColor'}"><td>{$smarty.const._MATCH}:</td><td>{$test_info.questions.match}</td></tr>
                                    <tr class = "{cycle name = 'question_info' values = 'oddRowColor, evenRowColor'}"><td>{$smarty.const._EMPTYSPACES}:</td><td>{$test_info.questions.empty_spaces}</td></tr>
                                    <tr class = "{cycle name = 'question_info' values = 'oddRowColor, evenRowColor'}"><td>{$smarty.const._DRAGNDROP}:</td><td>{$test_info.questions.drag_drop}</td></tr>
                                    <tr class = "{cycle name = 'question_info' values = 'oddRowColor, evenRowColor'}"><td>{$smarty.const._LOWDIFFICULTY}:</td><td>{$test_info.questions.low}</td></tr>
                                    <tr class = "{cycle name = 'question_info' values = 'oddRowColor, evenRowColor'}"><td>{$smarty.const._MEDIUMDIFFICULTY}:</td><td>{$test_info.questions.medium}</td></tr>
                                    <tr class = "{cycle name = 'question_info' values = 'oddRowColor, evenRowColor'}"><td>{$smarty.const._HIGHDIFFICULTY}:</td><td>{$test_info.questions.high}</td></tr>
                                </table>
                            </td></tr>
                    {else}
     <table class = "statisticsTools">
                        <tr><td>{$smarty.const._TEST}: {$test_info.general.name} (SCORM)</td></tr>
     </table>
                    {/if}
                    <table class = "sortedTable" sortBy = "0">
                        <tr>
                            <td style = "width:30%;" class = "topTitle">{$smarty.const._USER}</td>
                            <td style = "width:100px;" class = "topTitle centerAlign">{$smarty.const._SCORE}</td>
                            <td style = "" class = "topTitle centerAlign">{$smarty.const._MASTERYSCORE}</td>
                            <td style = "width:10%;" class = "topTitle centerAlign">{$smarty.const._STATUS}</td>
                            <td style = "width:25%;" class = "topTitle">{$smarty.const._DATE}</td>
                            <td style = "width:10%;" class = "topTitle centerAlign">{$smarty.const._OPERATIONS}</td>
                        </tr>
                        {foreach name = 'done_tests_list' key = "id" item = "done_test" from = $T_TESTS_INFO[$test_id].done}
                        <tr class = "{cycle name = $test_id values = "oddRowColor, evenRowColor"}">
                         <td><a href = "{$smarty.server.PHP_SELF}?ctg=statistics&option=user&sel_user={$done_test.users_LOGIN}">#filter:login-{$done_test.users_LOGIN}#</a></td>
                         <td class = "progressCell">
                                <span style = "display:none">{$done_test.score}</span>
                                <span class = "progressNumber">#filter:score-{$done_test.score}#%</span>
                                <span class = "progressBar" style = "width:{$done_test.score}px;">&nbsp;</span>&nbsp;
                         </td>
                         <td class = "centerAlign">#filter:score-{$done_test.mastery_score}#%</td>
                         <td class = "centerAlign">{if $done_test.status == 'failed'}<img src = "images/16x16/close.png" alt = "{$smarty.const._FAILED}" title = "{$smarty.const._FAILED}" style = "vertical-align:middle">{else}<img src = "images/16x16/success.png" alt = "{$smarty.const._PASSED}" title = "{$smarty.const._PASSED}" style = "vertical-align:middle">{/if}</td>
                         <td>#filter:timestamp_time-{$done_test.timestamp}#</td>
                         <td class = "centerAlign">
                          {if !$test_info.general.scorm}
                                <a href = "view_test.php?done_test_id={$done_test.id}&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._VIEWTEST}', 3)" target = "POPUP_FRAME">
                                 <img src = "images/16x16/search.png" alt = "{$smarty.const._VIEWTEST}" title = "{$smarty.const._VIEWTEST}" /></a>
                             {/if}
                         </td>
      </tr>
                        {foreachelse}
                        <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
                        {/foreach}
                    </table>
                    <br/>
                    {/foreach}
                </div>
                {/if}

                {if !empty($T_QUESTIONS_INFORMATION) && $T_CONFIGURATION.disable_tests != 1}
                <div class = "statisticsDiv tabbertab {if (isset($smarty.get.tab) &&  $smarty.get.tab == 'questions')} tabbertabdefault{/if}" title = "{$smarty.const._QUESTIONS}">
                    <table class = "sortedTable">
                        <tr>
                            <td class = "topTitle leftAlign nowrap">{$smarty.const._QUESTIONTEXT}</td>
                            <td class = "topTitle centerAlign">{$smarty.const._QUESTIONTYPE}</td>
                            <td class = "topTitle centerAlign nowrap">{$smarty.const._DIFFICULTY}</td>
                            <td class = "topTitle centerAlign nowrap">{$smarty.const._TIMESDONE}</td>
                            <td class = "topTitle centerAlign nowrap">{$smarty.const._AVERAGESCORE}</td>
                        </tr>
                        {foreach name = 'questions_list' item = "question" key = "id" from = $T_QUESTIONS_INFORMATION}
                        <tr class = "{cycle values = "oddRowColor,evenRowColor"}">
                            <td>{$question.text}</td>
                            <td class = "centerAlign">

                                {if $question.type == 'match'} <img src = "images/16x16/question_type_match.png" title = "{$smarty.const._MATCH}" alt = "{$smarty.const._MATCH}" />
                                {elseif $question.type == 'raw_text'} <img src = "images/16x16/question_type_free_text.png" title = "{$smarty.const._RAWTEXT}" alt = "{$smarty.const._RAWTEXT}" />
                                {elseif $question.type == 'multiple_one'} <img src = "images/16x16/question_type_one_correct.png" title = "{$smarty.const._MULTIPLEONE}" alt = "{$smarty.const._MULTIPLEONE}" />
                                {elseif $question.type == 'multiple_many'} <img src = "images/16x16/question_type_multiple_correct.png" title = "{$smarty.const._MULTIPLEMANY}" alt = "{$smarty.const._MULTIPLEMANY}" />
                                {elseif $question.type == 'true_false'} <img src = "images/16x16/question_type_true_false.png" title = "{$smarty.const._TRUEFALSE}" alt = "{$smarty.const._TRUEFALSE}" />
                                {elseif $question.type == 'empty_spaces'} <img src = "images/16x16/question_type_empty_spaces.png" title = "{$smarty.const._EMPTYSPACES}" alt = "{$smarty.const._EMPTYSPACES}" />
                                {elseif $question.type == 'drag_drop'} <img src = "images/16x16/question_type_drag_and_drop.png" title = "{$smarty.const._DRAGNDROP}" alt = "{$smarty.const._DRAGNDROP}" />
                                {/if}
                            </td>
                            <td class = "centerAlign">
                                {if $question.difficulty == 'low'} <img src = "images/16x16/flag_green.png" title = "{$smarty.const._LOW}" alt = "{$smarty.const._LOW}" />
                                {elseif $question.difficulty == 'medium'} <img src = "images/16x16/flag_blue.png" title = "{$smarty.const._MEDIUM}" alt = "{$smarty.const._MEDIUM}" />
                                {elseif $question.difficulty == 'high'} <img src = "images/16x16/flag_red.png" title = "{$smarty.const._HIGH}" alt = "{$smarty.const._HIGH}" />
                                {/if}
                            </td>
                            <td class = "centerAlign">{if $question.times_done}{$question.times_done}{else}0{/if}</td>
                            <td class = "centerAlign">#filter:score-{$question.avg_score}#%</td>
                        </tr>
                        {/foreach}
                    </table>
                </div>
                {/if}

                {if !empty($T_PROJECTS_INFORMATION) && $T_CONFIGURATION.disable_projects != 1}
                <div class = "statisticsDiv tabbertab {if (isset($smarty.get.tab) &&  $smarty.get.tab == 'projects')} tabbertabdefault{/if}" title = "{$smarty.const._PROJECTS}">
                    {foreach key = "project_id" item = "project_info" from = $T_PROJECTS_INFORMATION}
     <table class = "statisticsTools">
      <tr><td>
                                <a href = "javascript:void(0)" onclick = "toggleVisibility($('projects_info{$project_id}'), Element.extend(this).down())">
         <img src = "images/16x16/navigate_down.png" title = "{$smarty.const._SHOWHIDE}" alt = "{$smarty.const._SHOWHIDE}"/>{$smarty.const._PROJECT}: {$project_info.general.title}</a>
                            </td>
                        </tr>
     </table>

                    <table class = "statisticsSubInfo" id = "projects_info{$project_id}" style = "display:none;">
                        <tr><td>
                                <table>
                                    <tr><td class = "topTitle" colspan = "3">{$smarty.const._PROJECTINFO}</td></tr>
                                    <tr class = "{cycle name = 'project_info' values = 'oddRowColor, evenRowColor'}"><td>{$smarty.const._DESCRIPTION}:</td><td>{$project_info.general.data}</td></tr>
                                    <tr class = "{cycle name = 'project_info' values = 'oddRowColor, evenRowColor'}"><td>{$smarty.const._DEADLINE}:</td><td>#filter:timestamp_time-{$project_info.general.deadline}#</td></tr>
                                </table>
                            </td></tr>
                    </table>
                    <table class = "sortedTable" sortBy = "0">
                        <tr>
                         <td style = "" class = "topTitle">{$smarty.const._USER}</td>
                         <td style = "min-width:100px;" class = "topTitle centerAlign">{$smarty.const._GRADE}</td>
                         <td class = "topTitle">{$smarty.const._DATE}</td>
                        </tr>
                     {foreach name = 'done_projects_list' key = "key" item = "info" from = $project_info.done}
                        <tr class = "{cycle name = 'done_tests' values = 'oddRowColor, evenRowColor'}">
                            <td><a href = "{$T_BASIC_TYPE}.php?ctg=statistics&option=user&sel_user={$info.users_LOGIN}">#filter:login-{$info.users_LOGIN}#</a></td>
                            <td style = "width:100px;" class = "progressCell">
                                <span style = "display:none">{$info.grade}</span>
                                <span class = "progressNumber">#filter:score-{$info.grade}#%</span>
                                <span class = "progressBar" style = "width:{$info.grade}px;">&nbsp;</span>&nbsp;&nbsp;
                            </td>
                            <td>#filter:timestamp_time-{$info.upload_timestamp}#</td>
                        </tr>
                     {foreachelse}
                        <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "emptyCategory">{$smarty.const._NOONEHASBEENASSIGNEDTHISPROJECT}</td></tr>
                     {/foreach}
                 </table>
                 <br/>
                 {/foreach}
                </div>
                {/if}

                <div class = "statisticsDiv tabbertab {if (isset($smarty.get.tab) &&  $smarty.get.tab == 'overall')} tabbertabdefault{/if}" title = "{$smarty.const._MOREINFO}">
                    <table class = "statisticsGeneralInfo">
                        <tr class = "defaultRowHeight">
                            <td class = "topTitle" colspan = "3">{$smarty.const._GENERALLESSONINFO}</td>
                        </tr>
                        <tr class = "defaultRowHeight {cycle name = 'general_lesson_info' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._PRICE}:</td>
                            <td class = "elementCell">{$T_CURRENT_LESSON->lesson.price_string}</td>
                        </tr>
                        <tr class = "defaultRowHeight {cycle name = 'general_lesson_info' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._ACTIVENEUTRAL}:</td>
                            <td class = "elementCell">{$T_LESSON_INFO.active_string}</td>
                        </tr>
                        <tr class = "defaultRowHeight {cycle name = 'general_lesson_info' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._LANGUAGE}:</td>
                            <td class = "elementCell">{$T_LESSON_INFO.language}</td>
                        </tr>
                        <tr>
                            <td class = "topTitle leftAlign" colspan = "3">{$smarty.const._LESSONPARTICIPATIONINFO}</td>
                        </tr>
      {if $T_CONFIGURATION.disable_comments != 1}
       <tr class = "defaultRowHeight {cycle name = 'participation_lesson_info' values = 'oddRowColor, evenRowColor'}">
        <td class = "labelCell">{$smarty.const._COMMENTS}:</td>
        <td class = "elementCell">{$T_LESSON_INFO.comments}</td>
       </tr>
      {/if}
      {if $T_CONFIGURATION.disable_forum != 1}
       <tr class = "defaultRowHeight {cycle name = 'participation_lesson_info' values = 'oddRowColor, evenRowColor'}">
        <td class = "labelCell">{$smarty.const._FORUMPOSTS}:</td>
        <td class = "elementCell">{$T_LESSON_INFO.messages}</td>
       </tr>
      {/if}
                        <tr class = "defaultRowHeight {cycle name = 'participation_lesson_info' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._CHATMESSAGES}:</td>
                            <td class = "elementCell">{$T_LESSON_INFO.chatmessages}</td>
                        </tr>
                        <tr>
                            <td class = "topTitle leftAlign" colspan = "3">{$smarty.const._LESSONCONTENTINFO}</td>
                        </tr>
                        <tr class = "defaultRowHeight {cycle name = 'content_lesson_info' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._THEORY}:</td>
                            <td class = "elementCell">{$T_LESSON_INFO.theory}</td>
                        </tr>
                        <tr class = "defaultRowHeight {cycle name = 'content_lesson_info' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._EXAMPLES}:</td>
                            <td class = "elementCell">{$T_LESSON_INFO.examples}</td>
                        </tr>
      {if $T_CONFIGURATION.disable_projects != 1}
                        <tr class = "defaultRowHeight {cycle name = 'content_lesson_info' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._PROJECTS}:</td>
                            <td class = "elementCell">{$T_LESSON_INFO.projects}</td>
                        </tr>
      {/if}
      {if $T_CONFIGURATION.disable_tests != 1}
      <tr class = "defaultRowHeight {cycle name = 'content_lesson_info' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._TESTS}:</td>
                            <td class = "elementCell">{$T_LESSON_INFO.tests}</td>
                        </tr>
      {/if}
                        <tr class = "defaultRowHeight {cycle name = 'content_lesson_info' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._TOTAL}:</td>

       {assign var="x" value = $T_LESSON_INFO.theory}
       {assign var="y" value = $T_LESSON_INFO.examples}
       {if $T_CONFIGURATION.disable_projects != 1}
        {assign var="z" value = $T_LESSON_INFO.projects}
       {else}
        {assign var="z" value = 0}
       {/if}
       {if $T_CONFIGURATION.disable_tests != 1}
        {assign var="k" value = $T_LESSON_INFO.tests}
       {else}
        {assign var="k" value = 0}
       {/if}
       <td>{math equation="x + y +z + k" x = $x y = $y z = $z k = $k}</td>

      </tr>
                    </table>
                </div>

                {if ($T_BASIC_TYPE == 'administrator' || $T_ISPROFESSOR == true) }
                <div class = "statisticsDiv tabbertab {if (isset($smarty.get.tab) &&  $smarty.get.tab == 'traffic')} tabbertabdefault{/if}" title = "{$smarty.const._TRAFFIC}">
                    <form name = "period">
                    <table class = "statisticsSelectDate">
                        <tr><td class = "labelCell">{$smarty.const._FROM}:&nbsp;</td>
                            <td class = "elementCell">{eF_template_html_select_date prefix="from_" time=$T_FROM_TIMESTAMP start_year="-2" end_year="+2" field_order = $T_DATE_FORMATGENERAL} {$smarty.const._TIME}: {html_select_time prefix="from_" time = $T_FROM_TIMESTAMP display_seconds = false}</td></tr>
                        <tr><td class = "labelCell">{$smarty.const._TO}:&nbsp;</td>
                            <td class = "elementCell">{eF_template_html_select_date prefix="to_" time=$T_TO_TIMESTAMP start_year="-2" end_year="+2" field_order = $T_DATE_FORMATGENERAL} {$smarty.const._TIME}: {html_select_time prefix="to_" time = $T_TO_TIMESTAMP display_seconds = false}</td></tr>
                        <tr><td class = "labelCell">{$smarty.const._ANALYTICLOG}:</td>
                         <td class = "elementCell"><input class = "inputCheckbox" type = "checkbox" id = "showLog" {if ( isset($T_LESSON_LOG))} checked="true" {/if}></td></tr>
                        <tr><td colspan = "2">&nbsp;</td></tr>
                        <tr><td></td>
                            <td class = "elementCell"><input type = "button" class = "flatButton" value = "{$smarty.const._SHOW}" onclick = "document.location='{$smarty.session.s_type}.php?ctg=statistics&option=lesson&sel_lesson={$smarty.get.sel_lesson}&tab=traffic&from_year='+document.period.from_Year.value+'&from_month='+document.period.from_Month.value+'&from_day='+document.period.from_Day.value+'&from_hour='+document.period.from_Hour.value+'&from_min='+document.period.from_Minute.value+'&to_year='+document.period.to_Year.value+'&to_month='+document.period.to_Month.value+'&to_day='+document.period.to_Day.value+'&to_hour='+document.period.to_Hour.value+'&to_min='+document.period.to_Minute.value+'&showlog='+document.period.showLog.checked"></td>
                        </tr>
                 </table>
                 </form>

                    {if $T_LESSON_TRAFFIC.total_access > 0}
                    <table class = "statisticsTools">
                        <tr><td id = "right">

                                <a href = "display_chart.php?id=8&lesson_id={$smarty.get.sel_lesson}&from={$T_FROM_TIMESTAMP}&to={$T_TO_TIMESTAMP}" onclick = "eF_js_showDivPopup('{$smarty.const._ACCESSSTATISTICS}', 2)", target = "POPUP_FRAME">
                                 {$smarty.const._ACCESSSTATISTICS}: <img src = "images/16x16/reports.png" alt = "{$smarty.const._ACCESSSTATISTICS}" title = "{$smarty.const._ACCESSSTATISTICS}" /></a>

        {$smarty.const._ACCESSSTATISTICS}: <img class = "handle" src = "images/16x16/reports.png" alt = "{$smarty.const._ACCESSSTATISTICS}" title = "{$smarty.const._ACCESSSTATISTICS}" onclick = "eF_js_showDivPopup('{$smarty.const._ACCESSSTATISTICS}', 2, 'graph_table');showGraph($('proto_chart'), 'graph_access');"/>
        <div id = "graph_table" style = "display:none"><div id = "proto_chart" class = "proto_graph"></div></div>
       </td>
                        </tr>
                    </table>
                    <table class = "statisticsGeneralInfo">
                        <tr><td class = "topTitle" colspan = "2">{$smarty.const._LESSONTRAFFIC}</td></tr>
                        <tr class = "oddRowColor">
                            <td class = "labelCell">{$smarty.const._TOTALACCESS}:</td>
                            <td class = "elementCell">{$T_LESSON_TRAFFIC.total_access}</td>
                        </tr>
                        <tr class = "evenRowColor">
                            <td class = "labelCell">{$smarty.const._TOTALACCESSTIME}: </td>
                            <td class = "elementCell">
                                {if $T_LESSON_TRAFFIC.total_seconds}
                                 {if $T_LESSON_TRAFFIC.total_time.hours}{$T_LESSON_TRAFFIC.total_time.hours}{$smarty.const._HOURSSHORTHAND} {/if}
                                 {if $T_LESSON_TRAFFIC.total_time.minutes}{$T_LESSON_TRAFFIC.total_time.minutes}{$smarty.const._MINUTESSHORTHAND} {/if}
                                 {if $T_LESSON_TRAFFIC.total_time.seconds}{$T_LESSON_TRAFFIC.total_time.seconds}{$smarty.const._SECONDSSHORTHAND}{/if}
                                {else}
                                 {$smarty.const._NOACCESSDATA}
                                {/if}
                            </td>
                        </tr>
                    </table>
                    {/if}
     <br/>
                    <table class = "statisticsTools">
                        <tr><td>{$smarty.const._ACCESSNUMBER}</td>
{*
                    {if $T_LESSON_TRAFFIC.total_seconds > 0 }
                            <td id = "right">
                                <a href = "display_chart.php?id=5&lesson_id={$smarty.get.sel_lesson}&from={$T_FROM_TIMESTAMP}&to={$T_TO_TIMESTAMP}" onclick = "eF_js_showDivPopup('{$smarty.const._MOSTACTIVEUSERS}', 2)", target = "POPUP_FRAME" style = "vertical-align:middle">
                                 {$smarty.const._MOSTACTIVEUSERS}: <img src = "images/16x16/reports.png" alt = "{$smarty.const._MOSTACTIVEUSERS}" title = "{$smarty.const._MOSTACTIVEUSERS}"/></a>
                            </td>
                    {/if}
*}
                     </tr>
                    </table>
                    <table class = "sortedTable">
                        <tr>
                            <td class = "topTitle">{$smarty.const._LOGIN}</td>
                            <td class = "topTitle centerAlign">{$smarty.const._ACCESSNUMBER}</td>
                            <td class = "topTitle centerAlign">{$smarty.const._TOTALACCESSTIME}</td>
                            <td class = "topTitle noSort centerAlign">{$smarty.const._OPTIONS}</td>
                        </tr>
                        {foreach name = 'user_traffic_list' key = "login" item = "info" from = $T_LESSON_TRAFFIC.users}
                         {if $info.accesses}
                            <tr class = "{cycle name = 'usertraffic' values = 'oddRowColor, evenRowColor'} {if !$info.active}deactivatedTableElement{/if}">
                                <td><a href = "{$T_BASIC_TYPE}.php?ctg=statistics&option=user&sel_user={$login}">#filter:login-{$login}#</a></td>
                                <td class = "centerAlign">{$info.accesses}</td>
                                <td class = "centerAlign">{strip}<span style = "display:none">{$info.total_seconds}&nbsp;</span>
                                    {if $info.total_seconds}
                                  {if $info.hours}{$info.hours}{$smarty.const._HOURSSHORTHAND} {/if}
                                  {if $info.minutes}{$info.minutes}{$smarty.const._MINUTESSHORTHAND} {/if}
                                  {if $info.seconds}{$info.seconds}{$smarty.const._SECONDSSHORTHAND}{/if}
                                    {else}
                                     {$smarty.const._NOACCESSDATA}
                                    {/if}
                                {/strip}</td>
                                <td class = "centerAlign">
                                    <a href = "display_chart.php?id=10&from={$T_FROM_TIMESTAMP}&to={$T_TO_TIMESTAMP}&login={$login}&lesson_id={$smarty.get.sel_lesson}" onclick = "eF_js_showDivPopup('{$smarty.const._ACCESSSTATISTICS}', 2)" target = "POPUP_FRAME">
                                     <img src = "images/16x16/reports.png" alt = "{$smarty.const._ACCESSSTATISTICS}" title = "{$smarty.const._ACCESSSTATISTICS}"/></a>
                                    <img class = "handle" src = "images/16x16/reports.png" alt = "{$smarty.const._ACCESSSTATISTICS}" title = "{$smarty.const._ACCESSSTATISTICS}" onclick = "eF_js_showDivPopup('{$smarty.const._ACCESSSTATISTICS}', 2, 'graph_table');showGraph($('proto_chart'), 'graph_user_access', '{$login}');"/>
                                </td>
                            </tr>
                            {/if}
                        {foreachelse}
                         <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
                        {/foreach}
                    </table>
                    {if isset($T_LESSON_LOG)}
                    <br/>
                    <table class = "statisticsTools">
                        <tr><td>{$smarty.const._ANALYTICLOG}</td></tr>
                    </table>
                    <table>
                  <tr>
                            <td class = "topTitle">{$smarty.const._LOGIN}</td>
                            <td class = "topTitle">{$smarty.const._UNIT}</td>
                            <td class = "topTitle">{$smarty.const._ACTION}</td>
                            <td class = "topTitle">{$smarty.const._TIME}</td>
                            <td class = "topTitle">{$smarty.const._IPADDRESS}</td>
                        </tr>
                     {foreach name = 'lesson_log_loop' key = "key" item = "info" from = $T_LESSON_LOG}
                        <tr class = "{cycle name = 'lesson_log_list' values = 'oddRowColor, evenRowColor'}">
                            <td>#filter:login-{$info.users_LOGIN}#</td>
                            <td>{$info.content_name}</td>
                            <td>{$T_ACTIONS[$info.action]}</td>
                            <td>#filter:timestamp_time-{$info.timestamp}#</td>
                            <td>{$info.session_ip|eF_decodeIp}</td>
                        </tr>
                        {foreachelse}
                        <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
                     {/foreach}
                    </table>
     {/if}
                </div>
                {/if}
            </div>
        {/if}
    {/capture}
    {if $T_CURRENT_LESSON}
     {eF_template_printBlock title = "`$smarty.const._STATISTICSFORLESSON` <span class='innerTableName'>&quot;`$T_CURRENT_LESSON->lesson.name`&quot;</span>" data = $smarty.capture.lesson_statistics image = '32x32/reports.png' help = 'Reports'}
    {else}
     {eF_template_printBlock title = "`$smarty.const._STATISTICSFORLESSON`" data = $smarty.capture.lesson_statistics image = '32x32/reports.png' help = 'Reports'}
    {/if}
