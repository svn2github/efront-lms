{* Smarty template for module_search.php *}

{capture name = 't_command_search_results_code'}
   <table width="100%">
    {section name = 'results_list' loop = $T_SEARCH_COMMAND}
 {if (isset($T_SEARCH_COMMAND_KEY3))}
  <tr><td><a href = "{$T_SEARCH_COMMAND_LOCATION|cat:$T_SEARCH_COMMAND[results_list].$T_SEARCH_COMMAND_KEY1|cat:"&question_type="|cat:$T_SEARCH_COMMAND[results_list].$T_SEARCH_COMMAND_KEY3}{if $T_SEARCH_COMMAND_CHANGELESSON}&lessons_ID={$T_SEARCH_COMMAND[results_list].lessons_ID}{/if}">{$T_SEARCH_COMMAND[results_list].$T_SEARCH_COMMAND_KEY2}</a></td></tr>
 {else}
        <tr><td><a href = "{$T_SEARCH_COMMAND_LOCATION|cat:$T_SEARCH_COMMAND[results_list].$T_SEARCH_COMMAND_KEY1}{if $T_SEARCH_COMMAND_CHANGELESSON}&lessons_ID={$T_SEARCH_COMMAND[results_list].lessons_ID}{/if}">{$T_SEARCH_COMMAND[results_list].$T_SEARCH_COMMAND_KEY2}</a></td></tr>
    {/if}
 {/section}
    </table>
{/capture}


{capture name = 't_lessons_results_code'}
    {foreach name = outerc from = $T_SEARCH_RESULTS_CURRENT_LESSON key= clesson_id item = clesson_results}
        <table width = "100%" bgcolor="#f1f1f1">
  {if !empty($T_SEARCH_RESULTS_LESSONS)}
   <tr><td class="labelFormCellTitle">{$smarty.const._CURRENTLESSON}&nbsp;"{$T_CURRENT_LESSON_NAME}"</td></tr>
        {/if}
  {foreach name = cinner from = $clesson_results item = cresult}
            <tr><td>
        <div class="searchResults">
        <div class="resultsTitle">
            {if $cresult.table_name == 'news'}
    <a href = "{$smarty.session.s_type}.php?ctg=news&view={$cresult.id}&popup=1" target = "POPUP_FRAME" onClick = "eF_js_showDivPopup('{$smarty.const._ANNOUNCEMENT}', 1);">
                <img src="images/16x16/announcements.png" border="0" align="top"/>&nbsp;{$cresult.name}
                </a>
            {elseif $cresult.table_name == 'lessons'}
                <a href="{$cresult.user_type}.php?ctg=control_panel&lessons_ID={$cresult.lessons_ID}">
                <img src="images/16x16/home.png" border="0" align="top"/>&nbsp;{$cresult.name}
                </a>
            {elseif $cresult.table_name == 'questions'}
                 <a href="{$cresult.user_type}.php?ctg=tests&lessons_id={$cresult.lessons_ID}&edit_question={$cresult.id}&question_type={$cresult.type}"><img src="images/16x16/content.png" border="0" align="top"/>&nbsp;{$cresult.name}</a>
            {else}
                 <a href="{$cresult.user_type}.php?ctg=content&lessons_ID={$cresult.lessons_ID}&view_unit={$cresult.id}"><img src="images/16x16/content.png" border="0" align="top"/>&nbsp;{$cresult.name}</a>
            {/if}
            ({$cresult.score|string_format:"%.0f"}%)
            <div class="small">({$smarty.const._LESSON}:
            <a href="{$cresult.user_type}.php?ctg=control_panel&lessons_ID={$cresult.lessons_ID}">
            {$T_CURRENT_LESSON_NAME}</a>)</div>
        </div>
            {if $cresult.position == $smarty.const._TEXT}
            {$cresult.content}
            {/if}
        </div>
        </td></tr>
        {/foreach}
        </table>
    {/foreach}


{if !empty($T_SEARCH_RESULTS_LESSONS)}
 <table width = "100%" bgcolor="#f4f4f4">
 {foreach name = outer from = $T_SEARCH_RESULTS_LESSONS key= lesson_id item = lesson_results}
        <tr><td class="labelFormCellTitle">{$lesson_results.0.lesson_name}</td></tr>
        {foreach name = inner from = $lesson_results item = result}
            <tr><td>
        <div class="searchResults">
        <div class="resultsTitle">
            {if $result.table_name == 'news'}
            {if $smarty.session.s_type == 'administrator'}
                <a href="administrator.php?ctg=lessons&amp;edit_lesson={$result.lessons_ID}" class="editLink"><img src="images/16x16/announcements.png" border="0" align="top"/>&nbsp;{$result.name}</a>
            {else}
                <a href = "{$smarty.session.s_type}.php?ctg=news&view={$result.id}&popup=1" target = "POPUP_FRAME" onClick = "eF_js_showDivPopup('{$smarty.const._ANNOUNCEMENT}', 1);"><img src="images/16x16/announcements.png" border="0" align="top"/>&nbsp;{$result.name}</a>
   {/if}
            {elseif $result.table_name == 'lessons'}
            {if $smarty.session.s_type == 'administrator'}
                <a href="administrator.php?ctg=lessons&amp;edit_lesson={$result.lessons_ID}" class="editLink"><img src="images/16x16/home.png" border="0" align="top"/>&nbsp;{$result.name}</a>
            {else}
                <a href="{$result.user_type}.php?ctg=control_panel&lessons_ID={$result.lessons_ID}"><img src="images/16x16/home.png" border="0" align="top"/>&nbsp;{$result.name}</a>
            {/if}
            {else}
                {if $smarty.session.s_type == 'administrator'}
                    <a href="administrator.php?ctg=lessons&amp;edit_lesson={$result.lessons_ID}" class="editLink"><img src="images/16x16/home.png" border="0" align="top"/>&nbsp;{$result.name}</a>
                {else}
                    <a href="{$result.user_type}.php?ctg=content&lessons_ID={$result.lessons_ID}&view_unit={$result.id}"><img src="images/16x16/content.png" border="0" align="top"/>&nbsp;{$result.name}</a>
                {/if}
            {/if}
            ({$result.score|string_format:"%.0f"}%)
            {if $smarty.session.s_type == 'administrator'}
            <a href="administrator.php?ctg=lessons&amp;edit_lesson={$result.lessons_ID}" class="editLink"><img src="images/16x16/edit.png" title="{$smarty.const._EDIT}" alt="{$smarty.const._EDIT}" border="0"></a>
            {/if}
            <div class="small">({$smarty.const._LESSON}:
            {if $smarty.session.s_type == 'administrator'}
                <a href="administrator.php?ctg=lessons&amp;edit_lesson={$result.lessons_ID}" class="editLink">
            {else}
                <a href="{$result.user_type}.php?ctg=control_panel&lessons_ID={$result.lessons_ID}">
            {/if}
            {$T_LESSON_NAMES[$lesson_id].name}</a>)</div>
        </div>
            {if $result.position == $smarty.const._TEXT}
            {$result.content}
            {/if}
        </div>
        </td></tr>
        {/foreach}


    {/foreach}
 </table>
{/if}
{/capture}

{capture name = 't_forum_search_results_code'}
    <table width="100%">
    {foreach name = forum_messages from = $T_SEARCH_RESULTS_FORUM key=key item = forum_results}
 <tr><td>

 {if $forum_results.table_name == 'f_messages'}
  <div class="searchResults">
  <div class="resultsTitle">
  <a href = "{$smarty.server.PHP_SELF}?ctg=forum&topic={$forum_results.topic_id}&view_message={$forum_results.message_id}"><img src="images/16x16/mail.png" alt="{$smarty.const._MESSAGE}" title="{$smarty.const._MESSAGE}" border="0" align="top"/>&nbsp;{$forum_results.message_subject}</a>
  <div class="small">({$smarty.const._FORUM}:
        <a href = "{$smarty.server.PHP_SELF}?ctg=forum&forum={$forum_results.category_id}">{$forum_results.lesson_name}</a>)
  </div>
        </div>
            {if $forum_results.position == $smarty.const._TEXT}
            {$forum_results.body}
            {/if}
        </div>
 {else}
  <div class="searchResults">
  <div class="resultsTitle">
  <a href = "{$smarty.server.PHP_SELF}?ctg=forum&topic={$forum_results.category_id}"><img src="images/16x16/forums.png" alt="{$smarty.const._FORUM}" title="{$smarty.const._FORUM}" border="0" align="top"/>&nbsp;{$forum_results.lesson_name}</a>
  </div>
        </div>
 {/if}
 </td></tr>
  {foreachelse}
        <tr><td class = "emptyCategory">{$smarty.const._NORESULTSFOUNDINFORUM}</td></tr>
  {/foreach}
    </table>
{/capture}


{capture name = 't_personal_messages_search_results_code'}
    <table width="100%">
    {section name = 'results_list' loop = $T_SEARCH_RESULTS_PERSONAL_MESSAGES}
        <tr><td>
    <div class="searchResults">
    <div class="resultsTitle">
    <a href = "{$smarty.server.PHP_SELF}?ctg=messages&view={$T_SEARCH_RESULTS_PERSONAL_MESSAGES[results_list].message_id}">{$T_SEARCH_RESULTS_PERSONAL_MESSAGES[results_list].message_subject}</a>
    <div class="small">
    {$smarty.const._MESSAGEFOLDER}: <a href = "{$smarty.server.PHP_SELF}?ctg=messages&folder={$T_SEARCH_RESULTS_PERSONAL_MESSAGES[results_list].folder_id}">{$T_SEARCH_RESULTS_PERSONAL_MESSAGES[results_list].folder_name}</a>
    <br>
    ({$smarty.const._FROM2}
    {if $smarty.session.s_login <> $T_SEARCH_RESULTS_PERSONAL_MESSAGES[results_list].sender}
    <a title="{$T_SEARCH_RESULTS_PERSONAL_MESSAGES[results_list].sender}" target="POPUP_FRAME" onclick="eF_js_showDivPopup('{$T_SEARCH_RESULTS_PERSONAL_MESSAGES[results_list].sender}', 2)" href="{$smarty.server.PHP_SELF}?ctg=messages&add=1&recipient={$T_SEARCH_RESULTS_PERSONAL_MESSAGES[results_list].sender}&popup=1">{$T_SEARCH_RESULTS_PERSONAL_MESSAGES[results_list].sender}</a>
    {else}
    {$T_SEARCH_RESULTS_PERSONAL_MESSAGES[results_list].sender}
    {/if}
    |
    {$smarty.const._TO2}
    {if $smarty.session.s_login <> $T_SEARCH_RESULTS_PERSONAL_MESSAGES[results_list].recipient}
    <a title="{$T_SEARCH_RESULTS_PERSONAL_MESSAGES[results_list].recipient}" target="POPUP_FRAME" onclick="eF_js_showDivPopup('{$T_SEARCH_RESULTS_PERSONAL_MESSAGES[results_list].recipient}', 2)" href="{$smarty.server.PHP_SELF}?ctg=messages&add=1&recipient={$T_SEARCH_RESULTS_PERSONAL_MESSAGES[results_list].recipient}&popup=1">{$T_SEARCH_RESULTS_PERSONAL_MESSAGES[results_list].recipient}</a>
    {else}
    {$T_SEARCH_RESULTS_PERSONAL_MESSAGES[results_list].recipient}
    {/if}
    )
    </div>
    {$T_SEARCH_RESULTS_PERSONAL_MESSAGES[results_list].body}
    </div>
    </div>
    </td></tr>
    {sectionelse}
        <tr><td class = "emptyCategory">{$smarty.const._NORESULTSFOUNDINPERSONALMESSAGES}</td></tr>
    {/section}
    </table>
{/capture}

{capture name = 't_users_search_results_code'}
    <table width="100%">
            {foreach key=key item=item from=$T_SEARCH_RESULTS_USERS}
            <tr>
                <td>
                <div class="searchResults">
                <div class="resultsTitle">
                <a class="editLink" href="{$smarty.session.s_type}.php?ctg=users&edit_user={$item.login}">#filter:login-{$item.login}#</a>
                <a title="#filter:login-{$item.login}#" target="POPUP_FRAME" onclick="eF_js_showDivPopup('{$item.login}', 2)" href="{$smarty.server.PHP_SELF}?ctg=messages&add=1&recipient={$item.login}&popup=1"><img border="0" alt="{$smarty.const._SENDPERSONALMESSAGE}" title="{$smarty.const._SENDPERSONALMESSAGE}" src="images/16x16/mail.png"/></a>
                <a class="editLink" href="{$smarty.session.s_type}.php?ctg=users&edit_user={$item.login}"><img border="0" alt="{$smarty.const._EDIT}" title="{$smarty.const._EDIT}" src="images/16x16/edit.png"/></a>
                </div>
                </div>
                </td>
            </tr>
            {/foreach}
            </td></tr>
    </table>
{/capture}

{capture name = 't_files_results_code'}
    <table width="100%">
            {foreach key=key item=item from=$T_SEARCH_RESULTS_FILES}
            <tr>
    <td>
      <div class="searchResults">
      <div class="resultsTitle">
      <img style = "vertical-align:middle;" src="{$item.icon}" /><a href="view_file.php?action=download&file={$item.id}">&nbsp;{$item.name}</a>
      <div class="small">
      {$item.date} {if $item.login != ""}- #filter:login-{$item.login}# {/if}
      </div>
      </div>
      </div>

                </td>
            </tr>
            {/foreach}
            </td></tr>
    </table>
{/capture}



{capture name = 't_courses_search_results_code'}
    <table width="100%">
            {foreach key=key item=item from=$T_SEARCH_RESULTS_COURSES}
            <tr>
                <td>
                <div class="searchResults">
                <div class="resultsTitle">
                {$item.name} ({$item.score|string_format:"%.0f"}%)
                {if $smarty.session.s_type == 'professor'}
                    <a href="{$smarty.session.s_type}.php?ctg=lessons&course={$item.id}&op=course_info"><img border="0" style="vertical-align: middle;" alt="{$smarty.const._COURSEINFORMATION}" title="{$smarty.const._COURSEINFORMATION}" src="images/16x16/information.png"/></a>
                    <a href="{$smarty.session.s_type}.php?ctg=lessons&course={$item.id}&op=course_certificates"><img border="0" style="vertical-align: middle;" alt="Course certificates" title="Course certificates" src="images/16x16/certificate\.png"/></a>
                    <a href="{$smarty.session.s_type}.php?ctg=lessons&course={$item.id}&op=course_rules"><img border="0" style="vertical-align: middle;" alt="Course Rules" title="Course Rules" src="images/16x16/order.png"/></a>
                {/if}
                {if $smarty.session.s_type == 'administrator'}
                    <a href = "{$smarty.session.s_type}.php?ctg=courses&course={$item.id}&op=course_info"><img border="0" style="vertical-align: middle;" alt="{$smarty.const._COURSEINFORMATION}" title="{$smarty.const._COURSEINFORMATION}" src="images/16x16/information.png"/></a>
                    <a href = "{$smarty.session.s_type}.php?ctg=courses&course={$item.id}&op=course_certificates"><img border="0" style="vertical-align: middle;" alt="Course certificates" title="Course certificates" src="images/16x16/certificate.png"/></a>
                    <a href = "{$smarty.session.s_type}.php?ctg=courses&course={$item.id}&op=course_rules"><img border="0" style="vertical-align: middle;" alt="Course Rules" title="Course Rules" src="images/16x16/order.png"/></a>
                    <a href = "{$smarty.session.s_type}.php?ctg=courses&edit_course=1" class = "editLink"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
                                        <a href = "a{$smarty.session.s_type}.php?ctg=courses&delete_course=1" onclick = "return confirm('{$smarty.const._AREYOUSUREYOUWANTTODELETECOURSE}')" class = "deleteLink"><img border = "0" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a>
                {/if}
                </div>
                </div>
                </td>
            </tr>
            {/foreach}
            </td></tr>
    </table>
{/capture}

{capture name = 't_glossary_search_results_code'}
    <table width="100%">
    {foreach name = glossary_terms from = $T_SEARCH_RESULTS_GLOSSARY key=key item = glossary_results}
 <tr><td>

 {if $glossary_results.table_name == 'glossary'}
  <div class="searchResults">
  <div class="resultsTitle">
  {if $smarty.session.s_type == 'professor'}
   <a href = "{$smarty.server.PHP_SELF}?ctg=glossary&lessons_ID={$glossary_results.lessons_ID}"><img src="images/16x16/glossary.png" alt="{$smarty.const._GLOSSARY}" title="{$smarty.const._GLOSSARY}" border="0" align="top"/>&nbsp;{$glossary_results.name}</a>
  {else}
   <img src="images/16x16/glossary.png" alt="{$smarty.const._GLOSSARY}" title="{$smarty.const._GLOSSARY}" border="0" align="top"/>&nbsp;{$glossary_results.name}
  {/if}
  <div class="small">({$smarty.const._GLOSSARY}:
        <a href = "{$smarty.server.PHP_SELF}?ctg=control_panel&lessons_ID={$glossary_results.lessons_ID}">{$glossary_results.lesson_name}</a>)
  </div>
        </div>
    {$glossary_results.content}

        </div>
 {/if}
 </td></tr>
  {foreachelse}
        <tr><td class = "emptyCategory">{$smarty.const._NORESULTSFOUNDINGLOSSARY}</td></tr>
  {/foreach}
    </table>
{/capture}



<div class = "tabber">
    {if sizeof($T_SEARCH_COMMAND) >0}
        <div class = "tabbertab" title = "{$smarty.const._COMMANDS}">
            {eF_template_printBlock title=$smarty.const._SEARCHRESULTSCOMMANDS data=$smarty.capture.t_command_search_results_code image='32x32/search.png'}
        </div>
    {/if}
    {if sizeof($T_SEARCH_RESULTS_CURRENT_LESSON) >0 || sizeof($T_SEARCH_RESULTS_LESSONS) >0}
        <div class = "tabbertab" title = "{$smarty.const._LESSONS}">
            {eF_template_printBlock title=$smarty.const._SEARCHRESULTSINLESSONS data=$smarty.capture.t_lessons_results_code image='32x32/search.png'}
        </div>
    {/if}
    {if sizeof($T_SEARCH_RESULTS_FORUM) >0 }
        <div class = "tabbertab" title = "{$smarty.const._FORUM}">
            {eF_template_printBlock title=$smarty.const._SEARCHRESULTSINFORUM data=$smarty.capture.t_forum_search_results_code image='32x32/search.png'}
        </div>
    {/if}
    {if sizeof($T_SEARCH_RESULTS_PERSONAL_MESSAGES) >0 }
        <div class = "tabbertab" title = "{$smarty.const._PERSONALMESSAGES}">
            {eF_template_printBlock title=$smarty.const._SEARCHRESULTSINPERSONALMESSAGES data=$smarty.capture.t_personal_messages_search_results_code image='32x32/search.png'}
        </div>
    {/if}
    {if sizeof($T_SEARCH_RESULTS_COURSES) >0 }
        <div class = "tabbertab" title = "{$smarty.const._COURSES}">
            {eF_template_printBlock title=$smarty.const._SEARCHRESULTSINCOURSES data=$smarty.capture.t_courses_search_results_code image='32x32/search.png'}
        </div>
    {/if}
 {if sizeof($T_SEARCH_RESULTS_FILES) >0 }
        <div class = "tabbertab" title = "{$smarty.const._FILES}">
            {eF_template_printBlock title=$smarty.const._SEARCHRESULTSINFILES data=$smarty.capture.t_files_results_code image='32x32/file_explorer.png'}
        </div>
    {/if}
    {if sizeof($T_SEARCH_RESULTS_USERS) >0 && $smarty.session.s_type == 'administrator'}
        <div class = "tabbertab" title = "{$smarty.const._USERS}">
            {eF_template_printBlock title=$smarty.const._SEARCHRESULTSINUSERS data=$smarty.capture.t_users_search_results_code image='32x32/search.png'}
        </div>
    {/if}
     {if sizeof($T_SEARCH_RESULTS_GLOSSARY) >0 && $smarty.session.s_type != 'administrator'}
        <div class = "tabbertab" title = "{$smarty.const._GLOSSARY}">
            {eF_template_printBlock title=$smarty.const._SEARCHRESULTSINGLOSSARY data=$smarty.capture.t_glossary_search_results_code image='32x32/search.png'}
        </div>
    {/if}

</div>
