
<script>
var noSearchCriteriaDefined = '{$smarty.const._NOSEARCHCRITERIADEFINED}';
var sessionType = '{$smarty.session.s_type}';
var searchCourseUsersFormCourses = '{$T_SEARCH_COURSE_USERS_FORM.courses.html|replace:"\n":""}';
var searchCourseUsersFormCondition = '{$T_SEARCH_COURSE_USERS_FORM.condition.html|replace:"\n":""}';
var searchCourseUsersFormDateFrom = '<table><tr><td>{$T_SEARCH_COURSE_USERS_FORM.from_date_cond.html|replace:"\n":""}</td><td>{$T_SEARCH_COURSE_USERS_FORM.from_date_day.html|replace:"\n":""}</td><td>{$T_SEARCH_COURSE_USERS_FORM.from_date_month.html|replace:"\n":""}</td><td>{$T_SEARCH_COURSE_USERS_FORM.from_date_year.html|replace:"\n":""}</td></tr></table>'
var searchCourseUsersFormDateTo = '<table><tr><td>{$T_SEARCH_COURSE_USERS_FORM.to_date_cond.html|replace:"\n":""}</td><td>{$T_SEARCH_COURSE_USERS_FORM.to_date_day.html|replace:"\n":""}</td><td>{$T_SEARCH_COURSE_USERS_FORM.to_date_month.html|replace:"\n":""}</td><td>{$T_SEARCH_COURSE_USERS_FORM.to_date_year.html|replace:"\n":""}</td></tr></table>';
var deleteConst = '{$smarty.const._DELETE}';
var detailsConst = '{$smarty.const._DETAILS}';
</script>

{capture name = 't_search_course_code'}

    {* Check permissions for allowing user to assign a new job *}
    <table>
        <tr>
            <td><a href="javascript:void(0);" onclick="add_new_criterium_row({$T_PLACEMENTS_SIZE})"><img src="images/16x16/add.png" title="{$smarty.const._NEWSEARCHCRITERIUM}" alt="{$smarty.const._NEWSEARCHCRITERIUM}"/ border="0"></a></td><td><a href="javascript:void(0);" onclick="add_new_criterium_row({$T_PLACEMENTS_SIZE})">{$smarty.const._NEWSEARCHCRITERIUM}</a></td>
        </tr>
    </table>

        {$T_SEARCH_COURSE_USERS_FORM.hidden}
        <table border = "0" width = "100%" class = "sortedTable" id="criteriaTable" noFooter="true">
            <tr class = "topTitle">
                <td class = "topTitle noSort" >{$smarty.const._COURSE}</td>
                <td class = "topTitle noSort">{$smarty.const._STATUS}</td>
                <td class = "topTitle noSort">{$smarty.const._REGISTRATIONDATE}</td>
                <td class = "topTitle noSort">{$smarty.const._COMPLETIONDATE}</td>
                <td class = "topTitle noSort" align="center">{$smarty.const._OPERATIONS}</td>
            </tr>

             <tr id="no_criteria_found">
                <td colspan=5 class = "emptyCategory">{$smarty.const._NOSEARCHCRITERIADEFINED}</td>
             </tr>
        </table>
{/capture}


{capture name = 't_found_employees_code'}

<!--ajax:usersTable-->
        <table style = "width:100%" class = "sortedTable" size = "{$T_EMPLOYEES_SIZE}" sortBy = "0" id = "usersTable" useAjax = "1" rowsPerPage = "20" url = "{$smarty.session.s_type}.php?ctg=search_courses&">
        <tr class = "topTitle">
            <td class = "topTitle" name = "login">{$smarty.const._USER}</td>
            <td class = "topTitle" name = "languages_NAME">{$smarty.const._LANGUAGE}</td>
{* <td class = "topTitle" name = "timestamp">{$smarty.const._DETAILS}</td>*}
            <td class = "topTitle noSort" align="center">{$smarty.const._USERFORM}</td>
            <td class = "topTitle noSort" align="center">{$smarty.const._SENDMESSAGE}</td>
            <td class = "topTitle noSort" align="center">{$smarty.const._STATISTICS}</td>
            <td class = "topTitle noSort" align="center">{$smarty.const._OPERATIONS}</td>
        </tr>

        {if isset($T_EMPLOYEES_SIZE) && $T_EMPLOYEES_SIZE > 0}
            {foreach name = 'users_list' key = 'key' item = 'user' from = $T_EMPLOYEES}
            <tr class = "{cycle values = "oddRowColor, evenRowColor"} {if !$user.active}deactivatedTableElement{/if}">
            <td>
                <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$user.login}" class = "editLink">#filter:login-{$user.login}#</a>
            </td>
            <td>{$user.languages_NAME}</td>
    {*1111111111111
            <td>
                <a href = "javascript:void(0)" class = "info nonEmptyLesson">
                    {$smarty.const._COURSES}
                    <span class="tooltipSpan">
                        {if isset($lesson.info.general_description)}<strong>{$smarty.const._DESCRIPTION|cat:'</strong>:&nbsp;'|cat:$lesson.info.general_description}<br/>{/if}
                        {if isset($lesson.info.assessment)} <strong>{$smarty.const._ASSESSMENT|cat:'</strong>:&nbsp;'|cat:$lesson.info.assessment}<br/> {/if}
                        {if isset($lesson.info.objectives)} <strong>{$smarty.const._OBJECTIVES|cat:'</strong>:&nbsp;'|cat:$lesson.info.objectives}<br/> {/if}
                        {if isset($lesson.info.lesson_topics)} <strong>{$smarty.const._LESSONTOPICS|cat:'</strong>:&nbsp;'|cat:$lesson.info.lesson_topics}<br/> {/if}
                        {if isset($lesson.info.resources)} <strong>{$smarty.const._RESOURCES|cat:'</strong>:&nbsp;'|cat:$lesson.info.resources}<br/> {/if}
                        {if isset($lesson.info.other_info)} <strong>{$smarty.const._OTHERINFO|cat:'</strong>:&nbsp;'|cat:$lesson.info.other_info}<br/> {/if}
                    </span>
                </a>
            </td>
    *}
            <td align="center">
                {if $user.user_type != 'administrator'}
                    <a href="{$smarty.session.s_type}.php?ctg=users&edit_user={$user.login}&print_preview=1" onclick = "eF_js_showDivPopup('{if $smarty.const.G_VERSIONTYPE == 'enterprise'}{$smarty.const._EMPLOYEEFORMPRINTPREVIEW}{else}{$smarty.const._USERFORMPRINTPREVIEW}{/if}', 2)" target = "POPUP_FRAME"><img src='images/16x16/printer.png' title= '{$smarty.const._PRINTPREVIEW}' alt = '{$smarty.const._PRINTPREVIEW}' border='0' /></a>
                {else}
                    <img src='images/16x16/printer.png' title= '{$smarty.const._PRINTPREVIEW}' alt = '{$smarty.const._PRINTPREVIEW}' border='0' />
                {/if}
            </td>

            <td align="center"><a style="" href="{$smarty.server.PHP_SELF}?ctg=messages&add=1&recipient={$user.login}&popup=1" onclick='eF_js_showDivPopup("{$smarty.const._SENDMESSAGE}", 2)' target="POPUP_FRAME"><img src="images/16x16/mail.png" border="0"></a></td>
            <td align="center"><a href="{$smarty.session.s_type}.php?ctg=statistics&option=user&sel_user={$user.login}"><img border = "0" src = "images/16x16/reports.png" title = "{$smarty.const._STATISTICS}" alt = "{$smarty.const._STATISTICS}" /></a></td>
            <td align = "center">
                <table>
                <tr><td width="45%">
                    <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$user.login}" class = "editLink"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>

                </td><td></td><td width="45%">
                    <a href = "{$smarty.session.s_type}.php?ctg=users&op=users_data&delete_user={$user.login}" onclick = "return confirm('{$smarty.const._AREYOUSUREYOUWANTTODELETEUSER}')" class = "deleteLink"><img border = "0" src = "images/16x16/error_delete.png" title = "{$smarty.const._FIRE}" alt = "{$smarty.const._FIRE}" /></a>
                </td></tr>
                </table>
            </td>

    {* <td align="center"><a href="{$smarty.session.s_type}.php?ctg=statistics&option=user&sel_user={$user.login}"><img border = "0" src = "images/16x16/reports.png" title = "{$smarty.const._STATISTICS}" alt = "{$smarty.const._STATISTICS}" /></a></td>*}
            </tr>
            {/foreach}

             <tr style="display:none"><td><input type="hidden" id="sendAllRecipients" value="{$T_SENDALLMAIL_URL}" /></td></tr>
             {if $smarty.const.MSIE_BROWSER == 1}
                 <img style="display:none" src="images/16x16/question_type_free_text.png" onLoad="javascript:new Effect.Appear('sendToAllId');" />
             {else}
                 <script>
                 new Effect.Appear('sendToAllId');
                 </script>
             {/if}
        {else}
             <tr><td colspan="10" class = "emptyCategory">{$smarty.const._NOUSERSFULFILLTHESPECIFIEDCRITERIA}</td></tr>

             <tr style="display:none"><td><input type="hidden" id="sendAllRecipients" value="{$T_SENDALLMAIL_URL}" /></td></tr>
             {if $smarty.const.MSIE_BROWSER == 1}
                <img style="display:none" src="images/16x16/question_type_free_text.png" onLoad="javascript:document.getElementById('sendToAllId').style.display='none';" />
             {else}
                 <script>
                 document.getElementById('sendToAllId').style.display = 'none';
                 </script>
             {/if}
        {/if}

    </table>
<!--/ajax:usersTable-->

{/capture}
