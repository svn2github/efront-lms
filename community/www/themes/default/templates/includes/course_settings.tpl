                            {if $T_OP == course_info}
                                {capture name = 't_course_info_code'}
                                    <fieldset class = "fieldsetSeparator">
                                        <legend>{$smarty.const._COURSEINFORMATION}</legend>
                                        {$T_COURSE_INFO_HTML}
                                    </fieldset>
                                    <fieldset class = "fieldsetSeparator">
                                        <legend>{$smarty.const._COURSEMETADATA}</legend>
                                        {$T_COURSE_METADATA_HTML}
                                    </fieldset>
                                {/capture}
                                {eF_template_printBlock title = "`$smarty.const._INFORMATIONFORCOURSE`<span class = 'innerTableName'>&nbsp;&quot;`$T_CURRENT_COURSE->course.name`&quot;</span>" data = $smarty.capture.t_course_info_code image = '32x32/information.png' main_options = $T_TABLE_OPTIONS options = $T_COURSE_OPTIONS}
                            {elseif $T_OP == 'course_certificates'}
                                {if $smarty.get.edit_user}
                                    {capture name = 't_course_user_progress'}
                                    <fieldset class = "fieldsetSeparator">
                                        <legend>{$smarty.const._LESSONSPROGRESS}</legend>
                                        <table>
                                            <tr>
                                        {foreach name = 'lessons_list' item = "lesson" key = "id" from = $T_USER_PROGRESS.lesson_status}
                                                <td style = "width:50%;">
                                                <table>
                                                    <tr><td>{$smarty.const._LESSON}:&nbsp;</td><td>{$lesson.lesson_name}</td></tr>
                                                    <tr><td>{$smarty.const._COMPLETED}:&nbsp;</td><td>{if $lesson.completed}{$smarty.const._YES}{else}{$smarty.const._NO}{/if}</td></tr>
                                                    {if $lesson.score}<tr><td>{$smarty.const._SCORE}:&nbsp;</td><td>{$lesson.score}&nbsp;%</td></tr>{/if}
                                                    <tr><td>{$smarty.const._CONTENTDONE}:&nbsp;</td>
                                                        <td class = "progressCell" style = "vertical-align:top">
                                                            <span class = "progressNumber">{if $lesson.overall_progress}{$lesson.overall_progress}{else}0{/if}%</span>
                                                            <span class = "progressBar" style = "width:{$lesson.percentage_done}px;">&nbsp;</span>
                                                        </td></tr>
                                                </table>
                                                </td>
                                            {if $smarty.foreach.lessons_list.iteration%2 == 0}</tr><tr>{/if}
                                        {/foreach}
                                            </tr>
                                        </table>
                                    </fieldset>
                                    <fieldset class = "fieldsetSeparator">
                                        <legend>{$smarty.const._COMPLETECOURSE}</legend>
                                        {$T_COMPLETE_LESSON_FORM.javascript}
                                        <form {$T_COMPLETE_COURSE_FORM.attributes}>
                                            {$T_COMPLETE_COURSE_FORM.hidden}
                                            <table class = "formElements">
                                                <tr><td class = "labelCell">{$T_COMPLETE_COURSE_FORM.completed.label}&nbsp;</td><td>{$T_COMPLETE_COURSE_FORM.completed.html}</td></tr>
                                                <tr><td class = "labelCell">{$T_COMPLETE_COURSE_FORM.score.label}&nbsp;</td><td>{$T_COMPLETE_COURSE_FORM.score.html}</td></tr>
                                                {if !$T_USER_PROGRESS.completed}<tr><td></td><td class = "infoCell">{$smarty.const._PROPOSEDSCOREISAVERAGELESSONSCORE}</td></tr>{/if}
                                                {if $T_COMPLETE_COURSE_FORM.score.error}<tr><td></td><td class = "formError">{$T_COMPLETE_COURSE_FORM.score.error}</td></tr>{/if}
                                                <tr><td class = "labelCell">{$T_COMPLETE_COURSE_FORM.comments.label}&nbsp;</td><td>{$T_COMPLETE_COURSE_FORM.comments.html}</td></tr>
                                                {if $T_COMPLETE_COURSE_FORM.comments.error}<tr><td></td><td class = "formError">{$T_COMPLETE_COURSE_FORM.comments.error}</td></tr>{/if}
                                                <tr><td colspan = "100%">&nbsp;</td></tr>
                                                <tr><td></td><td>{$T_COMPLETE_COURSE_FORM.submit_course_complete.html}</td></tr>
                                            </table>
                                        </form>
                                    </fieldset>
                                    {/capture}
                                    {eF_template_printBlock title = "`$T_USER_PROGRESS.name` `$T_USER_PROGRESS.surname`&#039s `$smarty.const._PROGRESS`" data = $smarty.capture.t_course_user_progress image = '32x32/users.png'}
        {if $T_MESSAGE_TYPE == 'success'}
           <script>
               re = /\?/;
               !re.test(parent.location) ? parent.location = parent.location+'?reset_popup=1' : parent.location = parent.location+'&reset_popup=1';
           </script>
        {/if}
                                {elseif $smarty.get.issue_certificate}

                                {else}
                                    {capture name = 't_course_certificates_code'}
                                     <script>var autocompleteyes = '{$smarty.const._AUTOCOMPLETE}: {$smarty.const._YES}';var autocompleteno = '{$smarty.const._AUTOCOMPLETE}: {$smarty.const._NO}';
                                       var autocertificateyes = '{$smarty.const._AUTOMATICCERTIFICATES}: {$smarty.const._YES}';var autocertificateno = '{$smarty.const._AUTOMATICCERTIFICATES}: {$smarty.const._NO}';</script>
                                     <div class = "headerTools">






           <span>
                                             <img src = "images/16x16/autocomplete.png" title = "{$smarty.const._AUTOCOMPLETE}" alt = "{$smarty.const._AUTOCOMPLETE}"/>
                                                <a href = "javascript:void(0)" onclick = "setAutoComplete(this)">{$smarty.const._AUTOCOMPLETE}: {if $T_CURRENT_COURSE->course.auto_complete}{$smarty.const._YES}{else}{$smarty.const._NO}{/if}</a>
                                      </span>






                                     </div>

<!--ajax:usersTable-->
                                                        <table style = "width:100%" class = "sortedTable" size = "{$T_USERS_SIZE}" sortBy = "0" id = "usersTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?{$T_BASE_URL}&op=course_certificates&">
                                                            <tr class = "topTitle">
                                                                <td class = "topTitle" name = "login">{$smarty.const._USER}</td>
                                                                <td class = "topTitle centerAlign" name = "conditions_passed">{$smarty.const._LESSONSCOMPLETED}</td>
                                                                <td class = "topTitle centerAlign" name = "completed" >{$smarty.const._COURSESTATUS}</td>
                                                                <td class = "topTitle centerAlign" name = "score">{$smarty.const._COURSESCORE}</td>




                                                                <td class = "topTitle centerAlign noSort">{$smarty.const._FUNCTIONS}



                </td>
                                                            </tr>
                                                {foreach name = 'users_progress_list' item = 'item' key = 'login' from = $T_USERS_PROGRESS}
                                                            <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$item.active}deactivatedTableElement{/if}">
                                                                <td>#filter:login-{$item.login}#</td>
                                                                <td style = "text-align:center">
                                                                    {$item.completed_lessons}/{$item.total_lessons}
                                                                </td>
                                                                <td style = "text-align:center">
                                                                    {if $item.completed}
                                                                        <img src = "images/16x16/success.png" title = "{$smarty.const._COMPLETED}" alt = "{$smarty.const._COMPLETED}" />
                                                                    {elseif $item.completed_lessons == $item.total_lessons}
                                                                        <img src = "images/16x16/lessons.png" title = "{$smarty.const._LESSONSCOMPLETED}" alt = "{$smarty.const._LESSONSCOMPLETED}" />
                                                                    {else}
                                                                        <img src = "images/16x16/forbidden.png" title = "{$smarty.const._NOTCOMPLETED}" alt = "{$smarty.const._NOTCOMPLETED}" />
                                                                    {/if}
                                                                </td>
                                                                <td style = "text-align:center">{if $item.score}{$item.score}{/if}</td>




                <td style = "text-align:center">{strip}
                                                                        <a href = "{$smarty.server.PHP_SELF}?{$T_BASE_URL}&op=course_certificates&edit_user={$item.login}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._PROGRESS}', 2)" title = "{$smarty.const._VIEWUSERLESSONPROGRESS}">
                                                                            <img src = "images/16x16/users.png" title = "{$smarty.const._VIEWUSERCOURSEPROGRESS}" alt = "{$smarty.const._VIEWUSERCOURSEPROGRESS}"/>
                                                                        </a>
                                                                {/strip}
                                                                </td>
                                                            </tr>
                                                {foreachelse}
                                                        <tr class = "{cycle values = "oddRowColor, evenRowColor"} defaultRowHeight"><td colspan = "100%" class = "emptyCategory">{$smarty.const._NOUSERDATAFOUND}</td></tr>
                                                {/foreach}
                                                    </table>
<!--/ajax:usersTable-->
                                    {/capture}
                                    {eF_template_printBlock title = "`$smarty.const._COMPLETION`<span class = 'innerTableName'>&nbsp;&quot;`$T_CURRENT_COURSE->course.name`&quot;</span>" data = $smarty.capture.t_course_certificates_code image = '32x32/autocomplete.png' main_options = $T_TABLE_OPTIONS options = $T_COURSE_OPTIONS options = $T_COURSE_OPTIONS}
                                {/if}
       {elseif $T_OP == 'format_certificate'}
       {elseif $T_OP == 'course_rules'}
                              <script>var dependson = '&nbsp;{$smarty.const._DEPENDSON}&nbsp;';var generallyavailable = '&nbsp;{$smarty.const._GENERALLYAVAILABLE}&nbsp;';</script>
                                    {capture name = 't_course_rules_code'}
                                                    {$T_COURSE_RULES_FORM.javascript}
                                                    <form {$T_COURSE_RULES_FORM.attributes}>
                                                    {$T_COURSE_RULES_FORM.hidden}
                                                    <table style = "max-width:100%">
                                            {foreach name = 'rules_list' item = 'item' key = 'key' from = $T_COURSE_LESSONS}
                                                        <tr class = "defaultRowHeight {if !$item.active}deactivatedTableElement{/if}">
                                                            <td id = "first_node_{$item.id}" style = "white-space:nowrap">{$item.name}</td>
                                                            <td id = "label_{$item.id}" style = "white-space:nowrap;">&nbsp;{$smarty.const._GENERALLYAVAILABLE}&nbsp;</td>
                                                            <td id = "insert_node_{$item.id}"></td>
                                                            <td id = "last_node_{$item.id}" style = "white-space:nowrap;text-align:right;vertical-align:bottom">
                                                                &nbsp;<img src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETECONDITION}" alt = "{$smarty.const._DELETECONDITION}" border = "0" id = "delete_icon_{$item.id}" onclick = "eF_js_removeCourseRule({$item.id})" style = "display:none"/>
                                                                {if $T_COURSE_LESSONS|@sizeof > 1}&nbsp;<img src = "images/16x16/add.png" title = "{$smarty.const._ADDCONDITION}" alt = "{$smarty.const._ADDCONDITION}" border = "0" id = "add_icon_{$item.id}" onclick = "eF_js_addCourseRule({$item.id})"/>{/if}
                                                            </td>
                                                        </tr>
                                            {/foreach}
                                                        <tr><td>&nbsp;</td></tr>
                                                        <tr><td></td><td class = "submitCell">{$T_COURSE_RULES_FORM.submit_rule.html}</td></tr>
                                                    </table>
                                                    </form>
                                                    {*Auxilliary select element, used below in building conditions*}
                                                    <select name = "condition" id = "conditions" style = "display:none;margin-left:5px;vertical-align:middle">
                                                        <option value = "and">{$smarty.const._AND}</option>
                                                        <option value = "or">{$smarty.const._OR}</option>
                                                    </select>
                                                    <script type = "text/javascript">
                                                     var lessonsIds = new Array();
                                                     var lessonsNames = new Array();
                                                     var calls = new Array();
                                     {foreach name = 'lessons_list' item = 'lesson' key = 'key' from = $T_COURSE_LESSONS} {*Create javascript arrays*}
                                                     lessonsIds.push('{$lesson.id}');
                                                     lessonsNames.push('{$lesson.name}');
                                        {/foreach}
                                        {foreach name = 'course_rules_list' item = "rule" key = "key" from = $T_COURSE_RULES}
                                            {foreach name = 'lesson_rules' item = "lesson_id" key = "index" from = $rule.lesson}
                                                {if !$rule.condition.$index || $rule.condition.$index == 'and'}{assign var = 'condition' value = 0}{else}{assign var = 'condition' value = 1}{/if}
                                                     calls.push(new Array({$key}, {$lesson_id}, {$condition}));
                                            {/foreach}
                                        {/foreach}
                                                    </script>
                                            {/capture}
                                            {eF_template_printBlock title = $smarty.const._COURSERULES data = $smarty.capture.t_course_rules_code image = '32x32/rules.png' main_options = $T_TABLE_OPTIONS options = $T_COURSE_OPTIONS}
                            {elseif $T_OP == 'course_order'}
                                {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=courses&course='|cat:$smarty.get.course|cat:'&op=course_order">'|cat:$smarty.const._ORDERFORCOURSE|cat:' &quot;'|cat:$T_CURRENT_COURSE->course.name|cat:'&quot;</a>'}
                                    {capture name = 't_course_rules_code'}
                                        <fieldset class = "fieldsetSeparator">
                                            <legend>{$smarty.const._DRAGITEMSTOCHANGELESSONSORDER}</legend>
                                            <ul id = "dhtmlgoodies_lessons_tree" class = "dhtmlgoodies_tree">
                                            {foreach name = 'lessons_list' key = 'key' item = 'lesson' from = $T_COURSE_LESSONS}
                                                <li id = "dragtree_{$lesson.id}" noChildren = "true">
                                                    <a class = "{if !$lesson.active}deactivatedLinkElement{/if}" href = "javascript:void(0)">&nbsp;{$lesson.name|eF_truncate:100}</a>
                                                </li>
                                            {/foreach}
                                            </ul>
                                        </fieldset>
                                        <br/>
                                        <input id = "save_button" class = "flatButton" type="button" onclick="saveQuestionTree(this)" value="{$smarty.const._SAVECHANGES}">
                                    {/capture}
                                    {eF_template_printBlock title = $smarty.const._COURSEORDER data = $smarty.capture.t_course_rules_code image = '32x32/order.png' main_options = $T_TABLE_OPTIONS options = $T_COURSE_OPTIONS}
                            {elseif $T_OP == 'course_scheduling'}
                                 <script>var noscheduleset = '{$smarty.const._NOSCHEDULESET}';</script>
                                    {capture name = 't_course_scheduling_code'}
                                        <table>
                                        {foreach name = 'lessons_list' key = "id" item = "lesson" from = $T_COURSE_LESSONS}
                                            <tr {if !$lesson.active}class = "deactivatedTableElement"{/if}><td>{$lesson.name}:&nbsp;</td>
                                                <td id = "schedule_dates_{$id}">{if $lesson.from_timestamp}{$smarty.const._FROM} #filter:timestamp_time_nosec-{$lesson.from_timestamp}# {$smarty.const._TO} #filter:timestamp_time_nosec-{$lesson.to_timestamp}#{else}<span class = "emptyCategory">{$smarty.const._NOSCHEDULESET}</span>{/if}&nbsp;</td>
                                                <td>
                                                    <span id = "add_schedule_link_{$id}">
                                                        <img src = "images/16x16/{if $lesson.from_timestamp}edit.png{else}add.png{/if}" alt = "{$smarty.const._ADDSCHEDULE}" title = "{$smarty.const._ADDSCHEDULE}" class = "handle" onclick = "showEdit({$id})"/>
                                                        <img src = "images/16x16/error_delete.png" alt = "{$smarty.const._DELETESCHEDULE}" title = "{$smarty.const._DELETESCHEDULE}" class = "handle" onclick = "deleteSchedule(this, {$id})" {if !$lesson.from_timestamp}style = "display:none"{/if}/>
                                                    </span>&nbsp;
                                                </td>
                                                <td id = "schedule_dates_form_{$id}" style = "display:none">
                                                    <table>
                                                        <tr><td>{$smarty.const._FROM}&nbsp;</td><td>{eF_template_html_select_date prefix="from_" time=$lesson.from_timestamp start_year="-2" end_year="+2" field_order = $T_DATE_FORMATGENERAL} {$smarty.const._TIME}: {html_select_time prefix="from_" time = $lesson.from_timestamp display_seconds = false}&nbsp;</td></tr>
                                                        <tr><td>{$smarty.const._TO}&nbsp;</td><td>{eF_template_html_select_date prefix="to_" time=$lesson.to_timestamp start_year="-2" end_year="+2" field_order = $T_DATE_FORMATGENERAL} {$smarty.const._TIME}: {html_select_time prefix="to_" time = $lesson.to_timestamp display_seconds = false}&nbsp;</td></tr>
                                                    </table>
                                                </td>
                                                <td>
                                                    <img src = "images/16x16/success.png" alt = "{$smarty.const._SAVE}" title = "{$smarty.const._SAVE}" class = "ajaxHandle" id = "set_schedules_link_{$id}" style = "display:none" onclick = "setSchedule(this, {$id})"/>&nbsp;
                                                    <img src = "images/16x16/error_delete.png" alt = "{$smarty.const._CANCEL}" title = "{$smarty.const._CANCEL}" class = "ajaxHandle" id = "remove_schedule_link_{$id}" style = "display:none" onclick = "hideEdit({$id})" />
                                                </td></tr>
                                        {/foreach}
                                        </table>
                                    {/capture}
                                    {eF_template_printBlock title = $smarty.const._COURSEORDER data = $smarty.capture.t_course_scheduling_code image = '32x32/calendar.png' main_options = $T_TABLE_OPTIONS options = $T_COURSE_OPTIONS}
                            {elseif $T_OP == 'export_course'}
                                {capture name = 't_export_course_code'}
                                    <fieldset class = "fieldsetSeparator">
                                    <legend>{$smarty.const._EXPORTCOURSE}</legend>
                                    {$T_EXPORT_COURSE_FORM.javascript}
                                    <form {$T_EXPORT_COURSE_FORM.attributes}>
                                        {$T_EXPORT_COURSE_FORM.hidden}
                                        <table class = "formElements" style = "margin-left:0px"> {if $T_NEW_EXPORTED_FILE}
                                            <tr><td colspan = "2">{$smarty.const._DOWNLOADEXPORTEDCOURSE}:&nbsp; <a href = "view_file.php?file={$T_NEW_EXPORTED_FILE.id}&action=download">{$T_NEW_EXPORTED_FILE.name}</a> ({$T_NEW_EXPORTED_FILE.size} {$smarty.const.KB}, #filter:timestamp-{$T_NEW_EXPORTED_FILE.timestamp}#)</td></tr>
                                    {elseif $T_EXPORTED_FILE}
                                            <tr><td colspan = "2">{$smarty.const._EXISTINGFILE}:&nbsp;<a href = "view_file.php?file={$T_EXPORTED_FILE.id}&action=download">{$T_EXPORTED_FILE.name}</a> ({$T_EXPORTED_FILE.size} {$smarty.const.KB}, #filter:timestamp-{$T_EXPORTED_FILE.timestamp}#)</td></tr>
                                    {/if}
                                            <tr><td class = "labelCell">{$smarty.const._CLICKTOEXPORTCOURSE}:&nbsp;</td>
                                                <td class = "elementCell">{$T_EXPORT_COURSE_FORM.submit_export_course.html}</td></tr>
                                        </table>
                                    </form>
                                    </fieldset>
                                {/capture}
                                {eF_template_printBlock title = "`$smarty.const._EXPORTCOURSE`<span class = 'innerTableName'>&nbsp;&quot;`$T_CURRENT_COURSE->course.name`&quot;</span>" data = $smarty.capture.t_export_course_code image = '32x32/export.png' main_options = $T_TABLE_OPTIONS options = $T_COURSE_OPTIONS}
                            {elseif $T_OP == 'import_course'}
                                    {capture name = 't_import_course_code'}
                                        <fieldset class = "fieldsetSeparator">
                                        <legend>{$smarty.const._IMPORTCOURSE}</legend>
                                        {$T_IMPORT_COURSE_FORM.javascript}
                                        <form {$T_IMPORT_COURSE_FORM.attributes}>
                                            {$T_IMPORT_COURSE_FORM.hidden}
                                            <table class = "formElements">
                                             <tr><td colspan = "2">{$smarty.const._COURSEIMPORTNOTICE}</td></tr>
                                                <tr><td class = "labelCell">{$smarty.const._COURSEDATAFILE}:&nbsp;</td>
                                                    <td>{$T_IMPORT_COURSE_FORM.file_upload.html}</td></tr>
                                                <tr><td></td><td class = "infoCell">{$smarty.const._EACHFILESIZEMUSTBESMALLERTHAN} <b>{$T_MAX_FILESIZE}</b> {$smarty.const._KB}</td></tr>
                                                <tr><td colspan = "100%">&nbsp;</td></tr>
                                                <tr><td></td><td>{$T_IMPORT_COURSE_FORM.submit_import_course.html}</td></tr>
                                            </table>
                                        </form>
                                        </fieldset>
                                    {/capture}
                                    {eF_template_printBlock title = $smarty.const._IMPORTCOURSE data = $smarty.capture.t_import_course_code image = '32x32/import.png' main_options = $T_TABLE_OPTIONS options = $T_COURSE_OPTIONS}
{/if}
