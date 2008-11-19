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
            {if $smarty.session.s_type == 'administrator'}
                <a href="administrator.php?ctg=lessons&amp;edit_lesson={$cresult.lessons_ID}" class="editLink">
            {else}
				<a href = "news.php?id={$cresult.id}" target = "POPUP_FRAME" onClick = "eF_js_showDivPopup('{$smarty.const._ANNOUNCEMENT}', 1);">
            {/if}
            <img src="images/16x16/news.png" border="0" align="top"/>&nbsp;{$cresult.name}
            </a>
            {elseif $cresult.table_name == 'lessons'}
            {if $smarty.session.s_type == 'administrator'}
                <a href="administrator.php?ctg=lessons&amp;edit_lesson={$cresult.lessons_ID}" class="editLink">
            {else}
                <a href="{$cresult.user_type}.php?ctg=control_panel&lessons_ID={$cresult.lessons_ID}">
            {/if}
            <img src="images/16x16/home.png" border="0" align="top"/>&nbsp;{$cresult.name}
            </a>
            {else}
            {if $cresult.ctg_type == 'tests'}
                {if $smarty.session.s_type == 'administrator'}
                    <a href="administrator.php?ctg=lessons&amp;edit_lesson={$cresult.lessons_ID}" class="editLink"><img src="images/16x16/book_open.png" border="0" align="top"/>&nbsp;{$cresult.name}</a>
                {else}
                    <a href="{$cresult.user_type}.php?ctg=tests&lessons_ID={$cresult.lessons_ID}&show_test={$cresult.id}&isContentId=1"><img src="images/16x16/book_open.png" border="0" align="top"/>&nbsp;{$cresult.name}</a>
                {/if}
            {else}
            <a href="{$cresult.user_type}.php?ctg=content&lessons_ID={$cresult.lessons_ID}&view_unit={$cresult.id}"><img src="images/16x16/book_open.png" border="0" align="top"/>&nbsp;{$cresult.name}</a>
            {/if}
            {/if}
            ({$cresult.score|string_format:"%.0f"}%)
            {if $smarty.session.s_type == 'administrator'}
            <a href="administrator.php?ctg=lessons&amp;edit_lesson={$cresult.lessons_ID}" class="editLink"><img src="images/16x16/edit.png" title="{$smarty.const._EDIT}" alt="{$smarty.const._EDIT}" border="0"></a>
            {/if}
            <div class="small">({$smarty.const._LESSON}:
            {if $smarty.session.s_type == 'administrator'}
                <a href="administrator.php?ctg=lessons&amp;edit_lesson={$cresult.lessons_ID}" class="editLink">
            {else}
                <a href="{$cresult.user_type}.php?ctg=control_panel&lessons_ID={$cresult.lessons_ID}">
            {/if}
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
                <a href="administrator.php?ctg=lessons&amp;edit_lesson={$result.lessons_ID}" class="editLink"><img src="images/16x16/news.png" border="0" align="top"/>&nbsp;{$result.name}</a>
            {else}
                <a href = "news.php?id={$result.id}" target = "POPUP_FRAME" onClick = "eF_js_showDivPopup('{$smarty.const._ANNOUNCEMENT}', 1);"><img src="images/16x16/news.png" border="0" align="top"/>&nbsp;{$result.name}</a>
			{/if}
            {elseif $result.table_name == 'lessons'}
            {if $smarty.session.s_type == 'administrator'}
                <a href="administrator.php?ctg=lessons&amp;edit_lesson={$result.lessons_ID}" class="editLink"><img src="images/16x16/home.png" border="0" align="top"/>&nbsp;{$result.name}</a>
            {else}
                <a href="{$result.user_type}.php?ctg=control_panel&lessons_ID={$result.lessons_ID}"><img src="images/16x16/home.png" border="0" align="top"/>&nbsp;{$result.name}</a>
            {/if}
            {else}
            {if $result.ctg_type == 'tests'}
                {if $smarty.session.s_type == 'administrator'}
                    <a href="administrator.php?ctg=lessons&amp;edit_lesson={$result.lessons_ID}" class="editLink"><img src="images/16x16/book_open.png" border="0" align="top"/>&nbsp;{$result.name}</a>
                {else}
                    <a href="{$result.user_type}.php?ctg=tests&lessons_ID={$result.lessons_ID}&show_test={$result.id}&isContentId=1"><img src="images/16x16/book_open.png" border="0" align="top"/>&nbsp;{$result.name}</a>
                {/if}
            {else}
                {if $smarty.session.s_type == 'administrator'}
                    <a href="administrator.php?ctg=lessons&amp;edit_lesson={$result.lessons_ID}" class="editLink"><img src="images/16x16/home.png" border="0" align="top"/>&nbsp;{$result.name}</a>
                {else}
                    <a href="{$result.user_type}.php?ctg=content&lessons_ID={$result.lessons_ID}&view_unit={$result.id}"><img src="images/16x16/book_open.png" border="0" align="top"/>&nbsp;{$result.name}</a>
                {/if}
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
    {section name = 'results_list' loop = $T_SEARCH_RESULTS_FORUM}
        {if $smarty.section.results_list.first}
        <tr>
			<td><b>{$smarty.const._MESSAGESUBJECT}</b></td>
            <td><b>{$smarty.const._TOPICSUBJECT}</b></td>
			<td><b>{$smarty.const._LESSON}&nbsp;({$smarty.const._FORUMTITLE})</b></td>    
            <td><b>{$smarty.const._POSITION}</b></td></tr>
        </tr>
        {/if}
        <tr>
			<td>
				{if $T_SEARCH_RESULTS_FORUM[results_list].message_subject != ""}
					<a href = "forum/forum_index.php?topic={$T_SEARCH_RESULTS_FORUM[results_list].topic_id}&view_message={$T_SEARCH_RESULTS_FORUM[results_list].message_id}">{$T_SEARCH_RESULTS_FORUM[results_list].message_subject}</a>
				{else} 
					- 
				{/if}
			</td>
		
		
		    <td>
				{if $T_SEARCH_RESULTS_FORUM[results_list].topic_subject != ""}
					<a href = "forum/forum_index.php?topic={$T_SEARCH_RESULTS_FORUM[results_list].topic_id}">{$T_SEARCH_RESULTS_FORUM[results_list].topic_subject}</a>
				{else} 
					- 
				{/if}
			</td>
 
			<td><a href = "forum/forum_index.php?forum={$T_SEARCH_RESULTS_FORUM[results_list].category_id}">{$T_SEARCH_RESULTS_FORUM[results_list].lesson_name}</a></td>
        
 
			<td>{$T_SEARCH_RESULTS_FORUM[results_list].position}</td></tr>
    {sectionelse}
        <tr><td class = "emptyCategory">{$smarty.const._NORESULTSFOUNDINFORUM}</td></tr>
    {/section}
    </table>
{/capture}

{capture name = 't_personal_messages_search_results_code'}
    <table width="100%">
    {section name = 'results_list' loop = $T_SEARCH_RESULTS_PERSONAL_MESSAGES}
        <tr><td>
    <div class="searchResults">
    <div class="resultsTitle">
    <a href = "forum/messages_index.php?p_message={$T_SEARCH_RESULTS_PERSONAL_MESSAGES[results_list].message_id}">{$T_SEARCH_RESULTS_PERSONAL_MESSAGES[results_list].message_subject}</a>
    <div class="small">
    {$smarty.const._MESSAGEFOLDER}: <a href = "forum/messages_index.php?folder={$T_SEARCH_RESULTS_PERSONAL_MESSAGES[results_list].folder_id}">{$T_SEARCH_RESULTS_PERSONAL_MESSAGES[results_list].folder_name}</a>
    <br>
    ({$smarty.const._FROM2}
    {if $smarty.session.s_login <> $T_SEARCH_RESULTS_PERSONAL_MESSAGES[results_list].sender}
    <a title="{$T_SEARCH_RESULTS_PERSONAL_MESSAGES[results_list].sender}" target="POPUP_FRAME" onclick="eF_js_showDivPopup('{$T_SEARCH_RESULTS_PERSONAL_MESSAGES[results_list].sender}', new Array('650px', '450px'))" href="forum/new_message.php?recipient={$T_SEARCH_RESULTS_PERSONAL_MESSAGES[results_list].sender}">{$T_SEARCH_RESULTS_PERSONAL_MESSAGES[results_list].sender}</a>
    {else}
    {$T_SEARCH_RESULTS_PERSONAL_MESSAGES[results_list].sender}
    {/if}
    |
    {$smarty.const._TO2}
    {if $smarty.session.s_login <> $T_SEARCH_RESULTS_PERSONAL_MESSAGES[results_list].recipient}
    <a title="{$T_SEARCH_RESULTS_PERSONAL_MESSAGES[results_list].recipient}" target="POPUP_FRAME" onclick="eF_js_showDivPopup('{$T_SEARCH_RESULTS_PERSONAL_MESSAGES[results_list].recipient}', new Array('650px', '450px'))" href="forum/new_message.php?recipient={$T_SEARCH_RESULTS_PERSONAL_MESSAGES[results_list].recipient}">{$T_SEARCH_RESULTS_PERSONAL_MESSAGES[results_list].recipient}</a>
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
                {$item.surname} {$item.name} (<a class="editLink" href="{$smarty.session.s_type}.php?ctg=users&edit_user={$item.login}">{$item.login}</a>)
                <a title="{$item.login}" target="POPUP_FRAME" onclick="eF_js_showDivPopup('{$item.login}', new Array('650px', '450px'))" href="forum/new_message.php?recipient={$item.login}"><img border="0" alt="{$smarty.const._SENDPERSONALMESSAGE}" title="{$smarty.const._SENDPERSONALMESSAGE}" src="images/16x16/mail2.png"/></a>
                <a class="editLink" href="{$smarty.session.s_type}.php?ctg=users&edit_user={$item.login}"><img border="0" alt="{$smarty.const._EDIT}" title="{$smarty.const._EDIT}" src="images/16x16/edit.png"/></a>
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
                    <a href="{$smarty.session.s_type}.php?ctg=lessons&course={$item.id}&op=course_info"><img border="0" style="vertical-align: middle;" alt="{$smarty.const._COURSEINFORMATION}" title="{$smarty.const._COURSEINFORMATION}" src="images/16x16/about.png"/></a>
                    <a href="{$smarty.session.s_type}.php?ctg=lessons&course={$item.id}&op=course_certificates"><img border="0" style="vertical-align: middle;" alt="Course certificates" title="Course certificates" src="images/16x16/certificate_add.png"/></a>
                    <a href="{$smarty.session.s_type}.php?ctg=lessons&course={$item.id}&op=course_rules"><img border="0" style="vertical-align: middle;" alt="Course Rules" title="Course Rules" src="images/16x16/replace2.png"/></a>
                {/if}
                {if $smarty.session.s_type == 'administrator'}
                    <a href = "{$smarty.session.s_type}.php?ctg=courses&course={$item.id}&op=course_info"><img border="0" style="vertical-align: middle;" alt="{$smarty.const._COURSEINFORMATION}" title="{$smarty.const._COURSEINFORMATION}" src="images/16x16/about.png"/></a>
                    <a href = "{$smarty.session.s_type}.php?ctg=courses&course={$item.id}&op=course_certificates"><img border="0" style="vertical-align: middle;" alt="Course certificates" title="Course certificates" src="images/16x16/certificate_add.png"/></a>
                    <a href = "{$smarty.session.s_type}.php?ctg=courses&course={$item.id}&op=course_rules"><img border="0" style="vertical-align: middle;" alt="Course Rules" title="Course Rules" src="images/16x16/replace2.png"/></a>
                    <a href = "{$smarty.session.s_type}.php?ctg=courses&edit_course=1" class = "editLink"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
                                        <a href = "a{$smarty.session.s_type}.php?ctg=courses&delete_course=1" onclick = "return confirm('{$smarty.const._AREYOUSUREYOUWANTTODELETECOURSE}')" class = "deleteLink"><img border = "0" src = "images/16x16/delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a>
                {/if}
                </div>
                </div>
                </td>
            </tr>
            {/foreach}
            </td></tr>
    </table>
{/capture}

<div class = "tabber">
    {if sizeof($T_SEARCH_COMMAND) >0} 
        <div class = "tabbertab" title = "{$smarty.const._COMMANDS}">
            {eF_template_printInnerTable title=$smarty.const._SEARCHRESULTSCOMMANDS data=$smarty.capture.t_command_search_results_code image='/32x32/find_text.png'}
        </div>
    {/if} 
    {if sizeof($T_SEARCH_RESULTS_CURRENT_LESSON) >0 || sizeof($T_SEARCH_RESULTS_LESSONS) >0}
        <div class = "tabbertab" title = "{$smarty.const._LESSONS}">
            {eF_template_printInnerTable title=$smarty.const._SEARCHRESULTSINLESSONS data=$smarty.capture.t_lessons_results_code image='/32x32/find_text.png'}
        </div>
    {/if}
    {if sizeof($T_SEARCH_RESULTS_FORUM) >0 }
        <div class = "tabbertab" title = "{$smarty.const._FORUM}">
            {eF_template_printInnerTable title=$smarty.const._SEARCHRESULTSINFORUM data=$smarty.capture.t_forum_search_results_code image='/32x32/find_text.png'}
        </div>
    {/if}
    {if sizeof($T_SEARCH_RESULTS_PERSONAL_MESSAGES) >0 }
        <div class = "tabbertab" title = "{$smarty.const._PERSONALMESSAGES}">
            {eF_template_printInnerTable title=$smarty.const._SEARCHRESULTSINPERSONALMESSAGES data=$smarty.capture.t_personal_messages_search_results_code image='/32x32/find_text.png'}
        </div>
    {/if}
    {if sizeof($T_SEARCH_RESULTS_COURSES) >0 }
        <div class = "tabbertab" title = "{$smarty.const._COURSES}">
            {eF_template_printInnerTable title=$smarty.const._SEARCHRESULTSINCOURSES data=$smarty.capture.t_courses_search_results_code image='/32x32/find_text.png'}
        </div>
    {/if}
    {if sizeof($T_SEARCH_RESULTS_USERS) >0 && $smarty.session.s_type == 'administrator'}
        <div class = "tabbertab" title = "{$smarty.const._USERS}">
            {eF_template_printInnerTable title=$smarty.const._SEARCHRESULTSINUSERS data=$smarty.capture.t_users_search_results_code image='/32x32/find_text.png'}
        </div>
    {/if}
</div>