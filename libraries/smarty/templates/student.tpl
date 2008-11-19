{include file = "includes/header.tpl"}
{if $smarty.session.s_lessons_ID}
	{assign var = "title" value = '<a class = "titleLink" title = "'|cat:$smarty.const._HOMEOFLESSON|cat:'&nbsp;&quot;'|cat:$T_CURRENT_LESSON->lesson.name|cat:'&quot;" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel" onclick="checkSidebar(\''|cat:$smarty.session.s_login|cat:'\')">'|cat:$smarty.const._HOME|cat:'</a>'}
	{literal}
        <script language="javascript">
        <!--
             function checkSidebar(s_login)
             { 
			 	var value = readCookie(s_login+'_sidebar');
				var valueMode = readCookie(s_login+'_sidebarMode');
                if(value == 'hidden' && valueMode == 'automatic'){
					top.sideframe.toggleSidebar(s_login);
				}
			 }
        -->
        </script>  
	{/literal}   
{else}
	{assign var = "title" value = '<a class = "titleLink" title = "'|cat:$smarty.const._HOME|cat:'" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel">'|cat:$smarty.const._HOME|cat:'</a>'}
{/if}

{strip}
<form name = "scorm_form" method = "post" action = "LMSCommitPage.php" target = "commitFrame" style = "display:none">
    <input type = "hidden" name = "id"                id = "id"                />
    <input type = "hidden" name = "content_ID"        id = "content_ID"        />
    <input type = "hidden" name = "users_LOGIN"       id = "users_LOGIN"       />
    <input type = "hidden" name = "lesson_location"   id = "lesson_location"   />
    <input type = "hidden" name = "maxtimeallowed"    id = "maxtimeallowed"    />
    <input type = "hidden" name = "timelimitaction"   id = "timelimitaction"   />
    <input type = "hidden" name = "masteryscore"      id = "masteryscore"      />
    <input type = "hidden" name = "datafromlms"       id = "datafromlms"       />
    <input type = "hidden" name = "entry"             id = "entry"             />
    <input type = "hidden" name = "total_time"        id = "total_time"        />
    <input type = "hidden" name = "comments"          id = "comments"          />
    <input type = "hidden" name = "comments_from_lms" id = "comments_from_lms" />
    <input type = "hidden" name = "lesson_status"     id = "lesson_status"     />
    <input type = "hidden" name = "score"             id = "score"             />
    <input type = "hidden" name = "scorm_exit"        id = "scorm_exit"        />
    <input type = "hidden" name = "minscore"          id = "minscore"          />
    <input type = "hidden" name = "maxscore"          id = "maxscore"          />
    <input type = "hidden" name = "suspend_data"      id = "suspend_data"      />
    <input type = "hidden" name = "session_time"      id = "session_time"      />
    <input type = "hidden" name = "credit"            id = "credit"            />
</form>

{if $T_SCORM}
    <script language="JavaScript" type="text/javascript" src="js/LMSFunctions.php?view_unit={$smarty.get.view_unit}"></script>
{/if}

<script>
{if $T_CTG == 'content'}                            {*Only for student, because in the case of 'content' the ctg is not the same with the menu option (which is either 'theory' or 'exercise')*}
    var ctg = '{$smarty.get.type}';
{else}
    var ctg = '{$T_CTG}';
{/if}

{* Code for changing the sideframe - reloading it if necessary and selecting the right link *}
{if (isset($T_CHANGE_LESSON) || isset($T_REFRESH_SIDE))}
    {if isset($T_PERSONAL_CTG)}
        {if $smarty.session.s_lessons_ID != ''}
            top.sideframe.location = "new_sidebar.php?sbctg=personal&new_lesson_id={$smarty.session.s_lessons_ID}";
        {else}
            top.sideframe.location = "new_sidebar.php?sbctg=personal";
        {/if}
    {else}
        top.sideframe.location = "new_sidebar.php?ctg=lessons&new_lesson_id={$smarty.session.s_lessons_ID}";
    {/if}
{/if}

{if $T_MODULE_HCD_INTERFACE}
    var myform = "noform";
{/if}


{* The following code checks whether the sideframe is Loaded, by checking the existence of an element defined at the end of the page   *}
{* If so, then the changeTDcolor function will be called from here, otherwise the sideframe will reload and the changeTDcolor function *}
{* will be called internally *}
{literal}
if (top.sideframe && top.sideframe.document.getElementById('hasLoaded')) {
{/literal}
   {if !$T_POPUP_MODE && !$smarty.get.popup}
       if (top.sideframe)
       {if $T_CTG == 'personal' && isset($smarty.get.tab) && $smarty.get.tab == 'file_record'}
           top.sideframe.changeTDcolor('file_manager');
       {elseif $T_CTG == 'control_panel' && isset($smarty.session.s_lessons_ID)}
           top.sideframe.changeTDcolor('lesson_main');
       {elseif $T_CTG == 'content' && isset($smarty.get.type) && $smarty.get.type == 'theory'}
           top.sideframe.changeTDcolor('theory');
       {elseif $T_CTG == 'tests' || ($T_CTG == 'lessons' && $T_OP == 'tests')}
           top.sideframe.changeTDcolor('tests');
       {elseif $T_CTG == 'projects'}
           top.sideframe.changeTDcolor('exercises');
       {elseif $T_CTG == 'glossary'}
           top.sideframe.changeTDcolor('glossary');
       {elseif $T_CTG == 'users' && $smarty.session.employee_type == $smarty.const._SUPERVISOR}
           top.sideframe.changeTDcolor('employees');
       {elseif $T_MODULE_HCD_INTERFACE  && ($T_CTG == "module_hcd")}
            {if ($T_OP == "reports")}
                top.sideframe.changeTDcolor('search_employee');
            {elseif isset($T_OP) && $T_OP != ''}
                top.sideframe.changeTDcolor('{$T_OP}');
            {else}
                top.sideframe.changeTDcolor('hcd_control_panel');
            {/if}
       {elseif $T_CTG == 'module'}
            top.sideframe.changeTDcolor('{$T_MODULE_HIGHLIGHT}');
	   {else}
           top.sideframe.changeTDcolor(ctg);
       {/if}
    {/if}
{literal}
} else {
{/literal}
    {if !$T_POPUP_MODE && !$smarty.get.popup}
        if (top.sideframe)
        {if $T_CTG == 'personal' && isset($smarty.get.tab) && $smarty.get.tab == 'file_record'}
            top.sideframe.location = "new_sidebar.php?sbctg=file_manager";
        {elseif $T_CTG == 'control_panel' && isset($smarty.session.s_lessons_ID)}
            top.sideframe.location = "new_sidebar.php?sbctg=lesson_main";
        {elseif $T_CTG == 'content' && isset($smarty.get.type) && $smarty.get.type == 'theory'}
            top.sideframe.location = "new_sidebar.php?sbctg=theory";
        {elseif $T_CTG == 'tests'}
            top.sideframe.location = "new_sidebar.php?sbctg=tests";
        {elseif $T_CTG == 'exercises'}
            top.sideframe.location = "new_sidebar.php?sbctg=exercises";
        {elseif $T_CTG == 'glossary'}
            top.sideframe.location = "new_sidebar.php?sbctg=glossary";
        {elseif $T_CTG == 'users' && $smarty.session.employee_type == $smarty.const._SUPERVISOR}
            top.sideframe.location = "new_sidebar.php?sbctg=employees";
        {elseif $T_MODULE_HCD_INTERFACE  && ($T_CTG == "module_hcd")}
            {if ($T_OP == "reports")}
                top.sideframe.location = "new_sidebar.php?sbctg=search_employee";
            {elseif isset($T_OP) && $T_OP != ''}
                top.sideframe.location = "new_sidebar.php?sbctg=placements{$T_OP}";
            {else}
                top.sideframe.location = "new_sidebar.php?sbctg=hcd_control_panel";
            {/if}
       {elseif $T_CTG == 'module'}
            top.sideframe.location = "new_sidebar.php?sbctg={$T_MODULE_HIGHLIGHT}";
        {else}
            top.sideframe.location = "new_sidebar.php?sbctg="+ctg;
        {/if}
    {/if}
{literal}
}
{/literal}
</script>

{*-------------------------------End of Part 1: initialization etc-----------------------------------------------*}




{*-------------------------------Part 2: Modules List ---------------------------------------------*}
{if $T_CTG == 'control_panel'}
    {assign var = "category" value = 'mypage'}

    {if $T_OP == 'search'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="javascript:void(0)" onclick = "location.reload()">'|cat:$smarty.const._SEARCHRESULTS|cat:'</a>'}
        {*moduleSearchResults: The Search results page*}
            {capture name = "moduleSearchResults"}
                                    <tr><td class = "moduleCell">
                                            {include file = "includes/module_search.tpl"}
                                    </td></tr>
            {/capture}

    {elseif $T_OP == 'news'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel&op=news">'|cat:$smarty.const._ANNOUNCEMENTS|cat:'</a>'}
        {*moduleNewsPage: The news page*}
            {capture name = "moduleNewsPage"}
                                    <tr><td class = "moduleCell">
                                        {capture name = "t_news_code"}
                                        	{include file = "news_list.tpl"}
                                        {/capture}

                                        {eF_template_printInnerTable title = $smarty.const._ANNOUNCEMENTS data = $smarty.capture.t_news_code image = '/32x32/news.png'}
                                    </td></tr>
            {/capture}

    {elseif $T_OP == 'lesson_information'}
       <!-- {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel&op=lesson_information">'|cat:$smarty.const._LESSONINFORMATION|cat:'</a>'} -->
        {*moduleLessonInformation: Show lesson information*}
        {capture name = "moduleLessonInformation"}
                                <tr><td class = "moduleCell">
                                    <table>
                                                <tr><td><img style="vertical-align:middle;" src="images/16x16/user1.png" alt="{$smarty.const._PROFESSORS}" title="{$smarty.const._PROFESSORS}"/><span style="vertical-align:middle;">&nbsp;{$smarty.const._PROFESSORS}:&nbsp;</span></td>
                                                    <td>
                                        {foreach name = 'lesson_professors' key = 'login' item = 'user' from = $T_LESSON_INFO.professors}
                                                        #filter:user_loginNoIcon-{$user.name} {$user.surname}#{if !$smarty.foreach.lesson_professors.last},&nbsp;{/if}
                                        {/foreach}
                                                    </td></tr>
                                                {if $T_LESSON_INFO.content} <tr><td><img style="vertical-align:middle;" src="images/16x16/book_blue.png" alt="{$smarty.const._CONTENT}" title="{$smarty.const._CONTENT}"/>&nbsp;{$smarty.const._CONTENT}:&nbsp; </td><td>{$T_LESSON_INFO.content}  {$smarty.const._UNITS}   </td></tr>{/if}
                                                {if $T_LESSON_INFO.tests}   <tr><td><img style="vertical-align:middle;" src="images/16x16/tests.png" alt="{$smarty.const._TESTS}" title="{$smarty.const._TESTS}"/>&nbsp;{$smarty.const._TESTS}:&nbsp;   </td><td>{$T_LESSON_INFO.tests}    {$smarty.const._TESTS}   </td></tr>{/if}
                                                {if $T_LESSON_INFO.projects}<tr><td><img style="vertical-align:middle;" src="images/16x16/exercises.png" alt="{$smarty.const._PROJECTS}" title="{$smarty.const._PROJECTS}"/>&nbsp;{$smarty.const._PROJECTS}:&nbsp;</td><td>{$T_LESSON_INFO.projects} {$smarty.const._PROJECTS}</td></tr>{/if}
                                    </table>
                                    <table style = "width:100%">
                                        {foreach name = 'lesson_info_list' key = key item = item from = $T_LESSON_INFO_CATEGORIES}
                                            {if $T_LESSON_INFO[$key]}
                                                <tr><td>&nbsp;</td></tr>
                                                <tr><td class = "mediumHeader" style = "text-align: left"><img style="vertical-align:middle;" src="images/16x16/about.png" alt="{$T_LESSON_INFO_CATEGORIES.$key}" title="{$T_LESSON_INFO_CATEGORIES.$key}"/>&nbsp;{$T_LESSON_INFO_CATEGORIES.$key}&nbsp;</td></tr>
                                                <tr><td>&nbsp;</td></tr>
                                                <tr><td>{$T_LESSON_INFO[$key]}</td></tr>
                                                <tr><td class = "horizontalSeparator"></td></tr>
                                            {/if}
                                        {foreachelse}
                                                <tr><td class = "emptyCategory">{$smarty.const._NODESCRIPTIONSET}</td></tr>
                                        {/foreach}
                                        {if $T_CURRENT_LESSON->options.tracking}
                                                <tr><td>&nbsp;</td></tr>
                                                <tr><td class = "mediumHeader" style = "text-align: left"><img style="vertical-align:middle;" src="images/16x16/recycle.png" alt="{$smarty.const._LESSONCONDITIONS}" title="{$smarty.const._LESSONCONDITIONS}"/>&nbsp;{$smarty.const._LESSONCONDITIONS}</td></tr>
                                                <tr><td>&nbsp;</td></tr>
                                            {foreach name = 'conditions_loop' key = key item = condition from = $T_CONDITIONS}
                                                <tr><td style = "color:{if $T_CONDITIONS_STATUS[$key]}green{else}red{/if}">
                                                {if $smarty.foreach.conditions_loop.total > 1}{if $condition.relation == 'and'}&nbsp;{$smarty.const._AND}&nbsp;{else}&nbsp;{$smarty.const._OR}&nbsp;{/if}{/if}
                                                {if $condition.type == 'all_units'}
                                                    {$smarty.const._YOUMUSTSEEALLUNITS}{if !$T_CONDITIONS_STATUS[$key]}<img src = "images/16x16/forbidden.png" title = "{$smarty.const._CONDITIONNOTMET}" alt = "{$smarty.const._CONDITIONNOTMET}" style = "vertical-align:middle;margin-left:25px">{else}<img src = "images/16x16/check.png"  title = "{$smarty.const._CONDITIONMET}" alt = "{$smarty.const._CONDITIONMET}" style = "vertical-align:middle;margin-left:25px">{/if}
                                                {elseif $condition.type == 'percentage_units'}
                                                    {$smarty.const._YOUMUSTSEE} {$condition.options.0}% {$smarty.const._OFLESSONUNITS}{if !$T_CONDITIONS_STATUS[$key]}<img src = "images/16x16/forbidden.png" title = "{$smarty.const._CONDITIONNOTMET}" alt = "{$smarty.const._CONDITIONNOTMET}" style = "vertical-align:middle;margin-left:25px">{else}<img src = "images/16x16/check.png"  title = "{$smarty.const._CONDITIONMET}" alt = "{$smarty.const._CONDITIONMET}" style = "vertical-align:middle;margin-left:25px">{/if}
                                                {elseif $condition.type == 'specific_unit'}
                                                    {$smarty.const._YOUMUSTSEEUNIT} &quot;{$T_TREE_NAMES[$condition.options.0]}&quot;{if !$T_CONDITIONS_STATUS[$key]}<img src = "images/16x16/forbidden.png" title = "{$smarty.const._CONDITIONNOTMET}" alt = "{$smarty.const._CONDITIONNOTMET}" style = "vertical-align:middle;margin-left:25px">{else}<img src = "images/16x16/check.png"  title = "{$smarty.const._CONDITIONMET}" alt = "{$smarty.const._CONDITIONMET}" style = "vertical-align:middle;margin-left:25px">{/if}
                                                {elseif $condition.type == 'all_tests'}
                                                    {$smarty.const._YOUMUSTCOMPLETEALLTESTSWITHSCORE} {$condition.options.0}%{if !$T_CONDITIONS_STATUS[$key]}<img src = "images/16x16/forbidden.png" title = "{$smarty.const._CONDITIONNOTMET}" alt = "{$smarty.const._CONDITIONNOTMET}" style = "vertical-align:middle;margin-left:25px">{else}<img src = "images/16x16/check.png"  title = "{$smarty.const._CONDITIONMET}" alt = "{$smarty.const._CONDITIONMET}" style = "vertical-align:middle;margin-left:25px">{/if}
                                                {elseif $condition.type == 'specific_test'}
                                                    {$smarty.const._YOUMUSTCOMPLETETEST} &quot;{$T_TREE_NAMES[$condition.options.0]}&quot; {$smarty.const._WITHSCORE} {$condition.options.1}%{if !$T_CONDITIONS_STATUS[$key]}<img src = "images/16x16/forbidden.png" title = "{$smarty.const._CONDITIONNOTMET}" alt = "{$smarty.const._CONDITIONNOTMET}" style = "vertical-align:middle;margin-left:25px">{else}<img src = "images/16x16/check.png"  title = "{$smarty.const._CONDITIONMET}" alt = "{$smarty.const._CONDITIONMET}" style = "vertical-align:middle;margin-left:25px">{/if}
                                                {/if}
                                                    </td></tr>
                                            {foreachelse}
                                                <tr><td class = "emptyCategory">{$smarty.const._NOCONDITIONSSET}</td></tr>
                                            {/foreach}
                                                <tr><td class = "horizontalSeparator"></td></tr>
                                        {/if}
                                    </table>
                                </td></tr>
        {/capture}
    {elseif isset($T_OP_MODULE)}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel&op='|cat:$T_OP|cat:'">'|cat:$T_OP_MODULE|cat:'</a>'}
        {capture name = "importedModule"}
                                <tr><td class = "moduleCell">
                                    {include file = $smarty.const.G_MODULESPATH|cat:$T_OP|cat:'/module.tpl'}
                                </td></tr>
        {/capture}
    {elseif $T_OP == 'digital_library'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel&op=digital_library">'|cat:$smarty.const._SHAREDFILES|cat:'</a>'}
            {capture name = "moduleDigitalLibraryFull"}
                                <tr><td class = "moduleCell">
                                        {capture name = 't_digital_library'}
                                            {$T_FILE_MANAGER}
                                        {/capture}

                                        {eF_template_printInnerTable title = $smarty.const._SHAREDFILES data = $smarty.capture.t_digital_library image = "/32x32/folder_view.png"}
                                </td></tr>
            {/capture}
    {else}
        {if $T_CURRENT_USER->coreAccess.content != 'hidden' && $T_CURRENT_LESSON->options.content_tree}
            {*moduleContentTree: Print the Content Tree*}
            {capture name = "moduleContentTree"}
                                    <tr><td class = "moduleCell">
                                        {capture name = 't_content_tree'}
                                            {$T_CONTENT_TREE}
                                        {/capture}
                                        {eF_template_printInnerTable title = $smarty.const._CURRENTCONTENT data = $smarty.capture.t_content_tree image = "/32x32/book_open.png" alt = '<span class = "emptyCategory">'|cat:$smarty.const._NOCONTENTFOUND|cat:'</span>' options = $T_TREE_OPTIONS}
                                    </td></tr>
            {/capture}

        {/if}
        {if $T_ALL_PROJECTS}
        {*moduleProjectsList: Lists Assigned Projects*}
        {capture name = "moduleProjectsList"}
                                <tr><td class = "moduleCell">
                                        {capture name='t_projects_code'}
                                            {eF_template_printProjects data=$T_ALL_PROJECTS limit=5}
                                        {/capture}

                                        {eF_template_printInnerTable title=$smarty.const._PROJECTS data=$smarty.capture.t_projects_code image='/32x32/exercises.png' options=$T_PROJECTS_OPTIONS link=$T_PROJECTS_LINK}
                                </td></tr>
        {/capture}
        {/if}

        {*moduleForumList: Lists recent Forum messages*}
        {if ($T_CURRENT_LESSON->options.forum) && $T_FORUM_MESSAGES}
            {capture name = "moduleForumList"}
                                <tr><td class = "moduleCell">
                                        {capture name='t_forum_messages_code'}
                                            {eF_template_printForumMessages data=$T_FORUM_MESSAGES forum_lessons_ID = $T_FORUM_LESSONS_ID limit = 3}
                                        {/capture}

                                        {eF_template_printInnerTable title=$smarty.const._RECENTMESSAGESATFORUM data=$smarty.capture.t_forum_messages_code image='/32x32/messages.png' options=$T_FORUM_OPTIONS link=$T_FORUM_LINK}
                                </td></tr>
            {/capture}
        {/if}

        {if $T_NEWS && $T_CURRENT_USER->coreAccess.news != 'hidden'}
        {*moduleNewsList: Lists lesson messages*}
        {capture name = "moduleNewsList"}
                                <tr><td class = "moduleCell">
                                        {capture name='t_news_code'}
                                            {eF_template_printNews data=$T_NEWS}
                                        {/capture}

                                        {eF_template_printInnerTable title=$smarty.const._ANNOUNCEMENTS data=$smarty.capture.t_news_code image='/32x32/news.png' array=$T_NEWS options = $T_NEWS_OPTIONS link = $T_NEWS_LINK}
                                </td></tr>
        {/capture}
        {/if}

        {if $T_PERSONAL_MESSAGES}
        {*modulePersonalMessagesList: Lists Unread personal messages*}
        {capture name = "modulePersonalMessagesList"}
                                <tr><td class = "moduleCell">
                                        {capture name='t_personal_messages_code'}
                                            {eF_template_printPersonalMessages data=$T_PERSONAL_MESSAGES limit = 10}
                                        {/capture}

                                        {eF_template_printInnerTable title=$smarty.const._RECENTUNREADPERSONALMESSAGES data=$smarty.capture.t_personal_messages_code image='/32x32/mail2.png' options=$T_PERSONAL_MESSAGES_OPTIONS link=$T_PERSONAL_MESSAGES_LINK}
                                </td></tr>
        {/capture}
        {/if}

        {*moduleComments: Lists recent Comments*}
        {if ($T_CURRENT_LESSON->options.comments) && $T_COMMENTS}
            {capture name = "moduleComments"}
                        <tr><td class = "moduleCell">
                                {capture name='t_comments_code'}
                                    {eF_template_printComments data = $T_COMMENTS}
                                {/capture}

                                {eF_template_printInnerTable title=$smarty.const._RECENTCOMMENTS data=$smarty.capture.t_comments_code image='/32x32/note.png'}
                        </td></tr>
            {/capture}
        {/if}

        {*moduleCalendar: Print lesson calendar*}
        {if ($T_CURRENT_LESSON->options.calendar) && $T_CURRENT_USER->coreAccess.calendar != 'hidden'}
            {capture name = "moduleCalendar"}
                        <tr><td class = "moduleCell">
                            {capture name = "t_calendar_code"}
                                {eF_template_printCalendar events=$T_CALENDAR_EVENTS timestamp=$T_VIEW_CALENDAR}
                            {/capture}
                            {assign var="calendar_title"  value = `$smarty.const._CALENDAR`&nbsp;(#filter:timestamp-`$T_VIEW_CALENDAR`#)}
                                {eF_template_printInnerTable title=$calendar_title data=$smarty.capture.t_calendar_code image='/32x32/calendar.png' options=$T_CALENDAR_OPTIONS link=$T_CALENDAR_LINK}
                        </td></tr>
            {/capture}
        {/if}

        {*moduleDigitalLibrary: Print the digital library section and file list*}

        {if $T_FILE_MANAGER}
            {capture name = "moduleDigitalLibrary"}
                                <tr><td class = "moduleCell">
                                        {capture name = 't_digital_library'}
                                            {$T_FILE_MANAGER}
                                        {/capture}

                                        {eF_template_printInnerTable title = $smarty.const._SHAREDFILES data = $smarty.capture.t_digital_library image = "/32x32/folder_view.png" navigation = $smarty.capture.t_digital_library_nav array = $T_FILES_LIST options=$T_FILES_LIST_OPTIONS}
                                </td></tr>
            {/capture}
        {/if}

{*///MODULES CAPTURING*}
        {*Inner table modules *}
        {foreach name = 'module_inner_tables_list' key = key item = moduleItem from = $T_INNERTABLE_MODULES}
            {capture name = $key|replace:"_":""}                    {*We cut off the underscore, since scriptaculous does not seem to like them*}
                <tr><td class = "moduleCell">
                    {if $moduleItem.smarty_file}
                        {include file = $moduleItem.smarty_file}
                    {else}
                        {$moduleItem.html_code}
                    {/if}
                </td></tr>
            {/capture}
        {/foreach}
{*
        {if $T_INNERTABLE_MODULES}
            {foreach name = 'module_inner_tables_list' key = key item = item from = $T_MODULES}
                {if in_array($key, $T_INNERTABLE_MODULES)}
                    {capture name = $key|replace:"_":""}
                                        <tr><td class = "moduleCell">
                                            {include file = $smarty.const.G_MODULESPATH|cat:$key|cat:'/module_innerTable.tpl'}
                                        </td></tr>
                    {/capture}
                {/if}
            {/foreach}
        {/if}
*}
    {/if}
{elseif $T_CTG == 'content'}
    {assign var = "category" value = 'lessons'}

    {if !$smarty.get.view_unit}
        {if $smarty.get.type == 'theory'}
            {assign var = "title" value = $title|cat:' &raquo; <a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=content&type=theory">'|cat:$smarty.const._THEORY|cat:'</a>'}
            {assign var = "specific_title" value = $smarty.const._THEORY}
            {assign var = "image" value = "32x32/book_blue.png"}
        {elseif $smarty.get.type == 'examples'}
            {assign var = "title" value = $title|cat:' &raquo; <a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=content&type=examples">'|cat:$smarty.const._EXAMPLES|cat:'</a>'}
            {assign var = "specific_title" value = $smarty.const._EXAMPLES}
            {assign var = "image" value = "32x32/lightbulb_on.png"}
        {elseif $smarty.get.type == 'tests'}
            {assign var = "title" value = $title|cat:' &raquo; <a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=content&type=tests">'|cat:$smarty.const._TESTS|cat:'</a>'}
            {assign var = "specific_title" value = $smarty.const._TESTS}
            {assign var = "image" value = "32x32/tests.png"}
        {else}
            {assign var = "title" value = $title|cat:' &raquo; <a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=content">'|cat:$smarty.const._CONTENT|cat:'</a>'}
            {assign var = "image" value = "32x32/book_open.png"}
        {/if}
        {*moduleSpecificContent: Show content based on its type*}
        {capture name = "moduleSpecificContent"}
                            <tr><td class = "moduleCell">
                                {capture name = 't_theory_tree_code'}
                                    {$T_THEORY_TREE}
                                {/capture}
                                {eF_template_printInnerTable title=$specific_title data=$smarty.capture.t_theory_tree_code image=$image alt=$smarty.const._NOCONTENTAVAILABLE}
                            </td></tr>
        {/capture}
    {else}
        {literal}
        <script language="javascript">
        <!--
             function hideSidebar(s_login)
             {
                var is_ie;
                var detect = navigator.userAgent.toLowerCase();
                detect.indexOf("msie") > 0 ? is_ie = "true" : is_ie = "false";
                var value = readCookie(s_login+'_sidebar');
                if(value != 'hidden')
                {
                    createCookie(s_login+'_sidebar','hidden',30);
                    top.document.getElementById('framesetId').cols = ""+sidebar_width+", *";
                    top.sideframe.document.body.style.paddingLeft = "130px";
                    top.sideframe.document.getElementById('arrow_down').style.right = "300px";
                    top.sideframe.document.getElementById('arrow_up').style.right = "300px";
                    top.sideframe.document.getElementById('toggleSidebarImage').src = 'images/16x16/navigate_right.png';

                    if(is_ie == "true")
                    {
                        top.sideframe.document.getElementById('toggleSidebarImage').style.position="absolute";
                        top.sideframe.document.getElementById('toggleSidebarImage').style.left = "0px";
                        top.sideframe.document.getElementById('toggleSidebarImage').style.top = "4px";

                    }
                    top.sideframe.document.getElementById('logoutImage').style.position="absolute";
                    top.sideframe.document.getElementById('logoutImage').style.left = "1px";
                    top.sideframe.document.getElementById('logoutImage').style.top = "45px";
                    top.sideframe.document.getElementById('mainPageImage').style.position="absolute";
                    top.sideframe.document.getElementById('mainPageImage').style.left = "1px";
                    top.sideframe.document.getElementById('mainPageImage').style.top = "25px";

                    var menus = top.sideframe.document.getElementById('menu').childElements().length - 1;
                    var i = 2;
                        for (i = 2; i <= menus; i++) {
                        if (top.sideframe.document.getElementById('menu'+i)) {
                            top.sideframe.document.getElementById('menu'+i).style.visibility = "hidden";
                        }
                    }

                    //top.sideframe.document.getElementById('toggleSidebarImage').style.position = "absolute";position: absolute; left: 0px";
                    //changeImage(top.sideframe.document.getElementById('toggleSidebarImage'));
                    //changeImage(top.sideframe.document.getElementById('logoutImage'));
                    //changeImage(top.sideframe.document.getElementById('mainPageImage'));
                }
            }
            var value = readCookie({/literal}'{$smarty.session.s_login}'{literal}+'_sidebar');
			var valueMode = readCookie({/literal}'{$smarty.session.s_login}'{literal}+'_sidebarMode');
			
			// If chat tab is enabled do not hide the sidebar
            if(!top.sideframe.chatEnabled && (value != 'visible' || valueMode == 'automatic')) {
            	hideSidebar({/literal}'{$smarty.session.s_login}'{literal})
            }	
            //-->
        </script>
        {/literal}
        {section name = 'parents_list' loop = $T_PARENT_LIST step = "-1"}
            {assign var = "truncated_name" value = $T_PARENT_LIST[parents_list].name|eF_truncate:40}
            {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = \"titleLink\" href = \"`$smarty.server.PHP_SELF`?ctg=content&view_unit=`$T_PARENT_LIST[parents_list].id`\" title = \"`$T_PARENT_LIST[parents_list].name`\">`$truncated_name`</a>"}
        {/section}
        {assign var = 't_show_side_menu' value = true}
        {capture name = "sideContentTree"}
                            {eF_template_printSide title=$smarty.const._LESSONMATERIAL data = $T_CONTENT_TREE}
        {/capture}

        {if $T_CURRENT_LESSON->options.tracking && (!isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change')}
            {capture name = 'sideProgress'}
                {capture name = 'progressBar'}
                        <table>
                            <tr><td>{$smarty.const._PROGRESS}:&nbsp;
                                <span style = "position:absolute;text-align:center;width:100px;border:1px solid #d3d3d3;vertical-align:middle;z-index:2">{$T_USER_PROGRESS.overall_progress}%</span>
                                <span style = "background-color:#A0BDEF;width:{$T_USER_PROGRESS.overall_progress}px;border:1px dotted #d3d3d3;position:absolute">&nbsp;</span>
                                &nbsp;
                            </td></tr>
                            <tr><td>&nbsp;</td></tr>
                    {if $T_USER_PROGRESS.total_conditions > 0 && $T_CURRENT_LESSON->options.lesson_info}
                        {if $T_USER_PROGRESS.lesson_passed}
                            <tr><td><a style = "color:green" href = "{$smarty.server.PHP_SELF}?ctg=control_panel&op=lesson_information&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._LESSONINFORMATION}', 2)" target = "POPUP_FRAME">{$smarty.const._CONDITIONSCOMPLETED}: {$T_USER_PROGRESS.conditions_passed} {$smarty.const._OUTOF} {$T_USER_PROGRESS.total_conditions}</a></td></tr>
                        {else}
                            <tr><td><a style = "color:red" href = "{$smarty.server.PHP_SELF}?ctg=control_panel&op=lesson_information&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._LESSONINFORMATION}', 2)" target = "POPUP_FRAME">{$smarty.const._CONDITIONSCOMPLETED}: {$T_USER_PROGRESS.conditions_passed} {$smarty.const._OUTOF} {$T_USER_PROGRESS.total_conditions}</a></td></tr>
                        {/if}
                    {/if}
                        </table>
                {/capture}
                {eF_template_printSide title = $smarty.const._LESSONPROGRESS data = $smarty.capture.progressBar id = 'progress'}
            {/capture}
        {/if}

        {*moduleContentData: A specific content page*}
        {capture name = "moduleContentData"}
                            <tr><td class = "moduleCell">
                                        <table style = "width:100%;vertical-align:top;" id = "contentTable">
                                            <tr><td style = "text-align:right">{if !$T_UNIT.options.hide_navigation}{eF_template_printPreviousNext previous = $T_PREVIOUS_UNIT next = $T_NEXT_UNIT}{/if}</td></tr>
                            {if !$T_RULE_CHECK_FAILED}
                                            <tr><td colspan = "2" id = "contentCell" class = "contentCell">{$T_UNIT.data}</td></tr>
                                {if $T_QUESTION}
                                            <tr><td style = "background-color:#DDDDDD;">
													<form id = "question_form" method = "post" action = "{$smarty.server.PHP_SELF}?view_unit={$smarty.get.view_unit}">{$T_QUESTION}</form>
													<div style = "text-align:left;margin-top:10px">
														<img id = "correct_answer" src = "images/32x32/check.png" alt = "{$smarty.const._CORRECTANSWER}" title = "{$smarty.const._CORRECTANSWER}" style = "display:none;float:right">
														<img id = "wrong_answer" src = "images/32x32/delete.png" alt = "{$smarty.const._WRONGANSWER}" title = "{$smarty.const._WRONGANSWER}" style = "display:none;float:right">
														<input class = "flatButton" type = "button" value = "{$smarty.const._SUBMIT}" onclick = "answerQuestion(this)"></div>
													<script>
													{literal}
													function answerQuestion(el) {
														Element.extend(el);
														$('correct_answer').hide();
														$('wrong_answer').hide();
														el.up().insert(new Element('img', {src:'images/others/progress1.gif', id:'progress_image'}).setStyle({verticalAlign:'middle', marginLeft:'5px'}));
														$('question_form').request({
															onFailure: function(transport) {
																$('progress_image').remove();
																showMessage(transport.responseText, 'failure');
															},
															onSuccess:function(transport) {
																$('progress_image').remove();
																if (transport.responseText == 'correct') {
																	new Effect.Appear($('correct_answer'));
																} else {
																	new Effect.Appear($('wrong_answer'));
																}
															}
														});
													}
													{/literal}
													</script>
												</td></tr>
								{/if}
                                            <tr><td align = "right" style = "border-top: 1px solid #DDDDDD">
                                                    <table width = "100%">
                                                        <tr><td align = "center" width = "90%">
                                {if !$T_UNIT.options.hide_complete_unit && (!$T_QUESTION || $T_SEEN_UNIT) && (!isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change')}
                                                                <a id = "seenLink" href = "javascript:void(0)" onclick = "setSeenUnit();">
                                                                    {if $T_SEEN_UNIT}
                                                                        <img id = "seenImage" src = "images/32x32/text_ok.png" title = "{$smarty.const._NOTSAWUNIT}" alt = "{$smarty.const._NOTSAWUNIT}" border = "0"/><br/><span id = "seenImageText">{$smarty.const._NOTSAWUNIT}</span>
                                                                    {else}
                                                                        <img id = "seenImage" src = "images/32x32/text.png"    title = "{$smarty.const._SAWUNIT}" alt = "{$smarty.const._SAWUNIT}" border = "0"/><br/><span id = "seenImageText">{$smarty.const._SAWUNIT}</span>
                                                                    {/if}
                                                                </a>
                                {/if}
                                                            </td>
                                                            <td id = "navigationDown" style = "white-space:nowrap;display:none;">
                                                                <a href = "javascript:void(0)" onclick = "$('mainTable').scrollTo()"><img onload="$('contentTable').getDimensions().height > 600 ? $('navigationDown').show() : $('navigationDown').hide();" border = "0" src = "images/24x24/navigate_up.png" title = "{$smarty.const._BACKTOTOP}" alt = "{$smarty.const._BACKTOTOP}" /></a>
                                                            {if !$T_UNIT.options.hide_navigation}
                                                                {eF_template_printPreviousNext previous = $T_PREVIOUS_UNIT next = $T_NEXT_UNIT}
                                                            {/if}
                                                            </td>
                                                        </tr>
                                                    </table>
                                            </td></tr>
                                        </table>
										
                                        <script>
                                        {if $T_SEEN_UNIT}var hasSeen  = true;{else}var hasSeen  = false;{/if}
                                        {literal}
                                        function setSeenUnit(status) {
                                            if (typeof(status) == 'undefined') {        //If "status" parameter is not set, then toggle the seen status based on whether the user has seen the lesson
                                                url = 'student.php?ctg=content&view_unit={/literal}{$T_UNIT.id}{literal}&ajax=1&set_seen='+(hasSeen ? 0 : 1);
                                            } else {            //If "status" parameter is set, then toggle the seen status based on this parameter
                                                url = 'student.php?ctg=content&view_unit={/literal}{$T_UNIT.id}{literal}&ajax=1&set_seen='+(status ? 1 : 0);
                                            }
                                            if ($('seenLink')) {
                                                $('seenLink').blur();
                                                $('seenImage').setAttribute('src', 'images/others/progress_big.gif');
                                                $('seenImageText').update('{/literal}{$smarty.const._LOADING}{literal}');
                                            }
                                            new Ajax.Request(url, {
                                                    method:'get',
                                                    asynchronous:true,
                                                    onSuccess: function (transport) {
                                                        if ($('seenLink')) {
                                                            if (hasSeen) {
                                                                $('seenImage').setAttribute('src', 'images/32x32/text.png');
                                                                $('seenImageText').update('{/literal}{$smarty.const._SAWUNIT}{literal}');
                                                            } else {
                                                                $('seenImage').setAttribute('src', 'images/32x32/text_ok.png');
                                                                $('seenImageText').update('{/literal}{$smarty.const._NOTSAWUNIT}{literal}');
                                                            }
                                                            new Effect.Appear($('seenLink'));
                                                        }
                                                        hasSeen = !hasSeen;
                                                    }
                                                });
                                        }
                                        {/literal}
                                        </script>
                                        <br/><br/>

                                {if (isset($T_CURRENT_LESSON->options.comments) && $T_COMMENTS)}
                                    {capture name = 't_comments_code'}
                                        <table border = "0" width = "100%">
                                    {if !isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change'}
                                                <tr><td style = "width:1%">
                                                    <a href = "add_comment.php?content_ID={$T_UNIT.id}&op=insert", onclick = "eF_js_showDivPopup('{$smarty.const._ADDCOMMENT}', new Array('500px','300px'))" target = "POPUP_FRAME">
                                                        <img border = "0" src = "images/16x16/add2.png" title = "{$smarty.const._ADDCOMMENT}" alt = "{$smarty.const._ADDCOMMENT}"/>
                                                    </a>
                                                </td><td>
                                                    <a href = "add_comment.php?content_ID={$T_UNIT.id}&op=insert", onclick = "eF_js_showDivPopup('{$smarty.const._ADDCOMMENT}', new Array('500px','300px'))" target = "POPUP_FRAME">
                                                        {$smarty.const._ADDCOMMENT}
                                                    </a>
                                                </td></tr>
                                    {/if}
                                        </table>
                                        <table style = "width:100%">
                                            <tr class = "defaultRowHeight">
                                                <td class = "topTitle" style = "width:60%">{$smarty.const._COMMENTS}</td>
                                                <td class = "topTitle">{$smarty.const._USER}</td>
                                                <td class = "topTitle">{$smarty.const._DATE}</td>
                                                <td class = "topTitle centerAlign">{$smarty.const._OPERATIONS}</td>
                                            </tr>
                                    {section name = 'comments_list' loop = $T_COMMENTS}
                                            <tr class = "{cycle values = "oddRowColor, evenRowColor"} defaultRowHeight">
                                                <td>{$T_COMMENTS[comments_list].data|eF_truncate:300}</td>
                                                <td>#filter:user_loginNoIcon-{$T_COMMENTS[comments_list].users_LOGIN}#</td>
                                                <td>#filter:timestamp-{$T_COMMENTS[comments_list].timestamp}#</td>
                                                <td class = "centerAlign" style = "white-space:nowrap">
                                                    <a href = "javascript: void(0)" onclick = "$('comments_div').update('{$T_COMMENTS[comments_list].data}');eF_js_showDivPopup('{$smarty.const._VIEWCOMMENT}', new Array(1), 'comments_div')"><img src = "images/16x16/view.png" title = "{$smarty.const._VIEWCOMMENT}" alt = "{$smarty.const._VIEWCOMMENT}" border = "0"/></a>
                                        {if $T_COMMENTS[comments_list].users_LOGIN == $T_CURRENT_USER->user.login}
                                                    <a href = "add_comment.php?id={$T_COMMENTS[comments_list].id}&op=change", onclick = "eF_js_showDivPopup('{$smarty.const._CORRECTION}', new Array('500px','300px'))" target = "POPUP_FRAME"><img src = "images/16x16/edit.png" alt = "{$smarty.const._CORRECTION}" title = "{$smarty.const._CORRECTION}" border = "0"/></a>&nbsp;
                                                    <a href = "add_comment.php?id={$T_COMMENTS[comments_list].id}&op=delete" target = "POPUP_FRAME" onclick = "return confirm ('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')"><img src = "images/16x16/delete.png" alt = "{$smarty.const._DELETE}" title = "{$smarty.const._DELETE}" border = "0"/></a>
                                        {/if}
                                                </td></tr>
                                    {/section}
                                        </table>
                                        <div id = "comments_div" style = "display:none"></div>
                                {/capture}
                                {eF_template_printInnerTable title=$smarty.const._COMMENTS data=$smarty.capture.t_comments_code image='/32x32/note.png'}
                            {/if}

                        {else}
                                </table>
                        {/if}

                            </td></tr>
        {/capture}


        {capture name = 'sideUnitOperations'}
            {capture name = 'UnitOperationsBar'}
                                {if !$T_SCORM}
                                                        {counter name = "unit_operations"}. <a href = "show_print_friendly.php?content_ID={$T_UNIT.id}", onclick = "eF_js_showDivPopup('{$smarty.const._PRINTERFRIENDLY}', new Array('800px','500px'))" target = "POPUP_FRAME">{$smarty.const._PRINTERFRIENDLY}</a><br/>
                                {/if}
                                                        {counter name = "unit_operations"}. <a href = "show_print_friendly.php", onclick = "eF_js_showDivPopup('{$smarty.const._PRINTERFRIENDLYALLCONTENT}', new Array('800px','500px'))" target = "POPUP_FRAME">{$smarty.const._PRINTERFRIENDLYALLCONTENT}</a><br/>
                                {if $T_CURRENT_LESSON->options.comments && (!isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change')}
                                                        {counter name = "unit_operations"}. <a href = "add_comment.php?content_ID={$T_UNIT.id}&op=insert", onclick = "eF_js_showDivPopup('{$smarty.const._ADDCOMMENT}', new Array('500px','300px'))" target = "POPUP_FRAME">{$smarty.const._ADDCOMMENT}</a><br/>
														{if $T_CURRENT_LESSON->options.content_report}
															{counter name = "unit_operations"}. <a href = "content_report.php?{$smarty.server.QUERY_STRING}" onclick = "eF_js_showDivPopup('{$smarty.const._CONTENTREPORT}', new Array('500px','300px'))" target = "POPUP_FRAME">{$smarty.const._CONTENTREPORTTOPROFS}</a><br/>
														{/if}
								{/if}
                                {if $T_LESSON_FORUM}
                                                        {counter name = "unit_operations"}. <a href = "forum/forum_add.php?add_topic=1&forum_id={$T_LESSON_FORUM}&subject={$T_UNIT.name}", onclick = "eF_js_showDivPopup('{$smarty.const._ADDFORUMPOSTONTHISUNIT}', new Array('600px','300px'))" target = "POPUP_FRAME" title="{$smarty.const._ADDFORUMPOSTONTHISUNIT}">{$smarty.const._ADDFORUMPOSTONTHISUNIT|eF_truncate:25:"...":true}</a><br/>
                                {/if}

            {/capture}
            {eF_template_printSide title = $smarty.const._UNITOPERATIONS data = $smarty.capture.UnitOperationsBar id = 'unit_operations'}
        {/capture}
    {/if}


{elseif $T_CTG == 'projects'}
    {assign var = "title" value = $title|cat:' &raquo; <a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=projects">'|cat:$smarty.const._PROJECTS|cat:'</a>'}
    {assign var = "category" value = 'lessons'}

    {capture name = "moduleExercises"}
    <tr><td class = "moduleCell">

            {if $smarty.get.view_project}
                {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = \"titleLink\" href = \"`$smarty.server.PHP_SELF`?ctg=projects&view_project=`$smarty.get.view_project`\">`$smarty.const._VIEWPROJECT` &quot;`$T_PROJECT->project.title`&quot;</a>"}
                {capture name = "t_view_project_code"}
                                <table>
                                    <tr><td>{$smarty.const._TITLE}:</td>
                                        <td>&nbsp;{$T_PROJECT->project.title}</td></tr>
                                    <tr><td>{$smarty.const._DEADLINE}:</td>
                                        <td>&nbsp;#filter:timestamp_time_nosec-{$T_PROJECT->project.deadline}#</td></tr>
                                    <tr><td>{$smarty.const._REMAINING}:</td>
                                        <td>&nbsp;{$T_PROJECT->timeRemaining}</td></tr>
                                    <tr><td colspan = "100%">&nbsp;</td></tr>
                                    <tr><td colspan = "2" style = "font-style:italic">{$T_PROJECT->project.data}</td></tr>
                                </table><br/>


                                <table class = "formElements">
                        {if $T_PROJECT_FILE}
                                    <tr><td>{$smarty.const._YOUHAVEALREADYUPLOADEDAFILE}:&nbsp;<a target = "_blank" href="view_file.php?file={$T_PROJECT_FILE.id}&action=download">{$T_PROJECT_FILE.name}</a>
                            {if !$T_PROJECT->expired && $T_PROJECT_USER_INFO.grade == ''}
                                        &nbsp;&nbsp;<a href = "student.php?ctg=projects&view_project={$smarty.get.view_project}&delete_file=1"><img style = "vertical-align:middle" src = "images/16x16/delete.png" border = "0" alt="{$smarty.const._DELETE}" title="{$smarty.const._DELETE}" onclick = "return confirm ('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')"/></a>
                            {/if}
                                    </td></tr>

                        {/if}
                        {if ($T_PROJECT_USER_INFO.grade != '')}
                                <tr><td style = "color:red;">{$smarty.const._YOURPROJECTSCOREIS}:&nbsp;{$T_PROJECT_USER_INFO.grade}</td></tr>
                            {if ($T_PROJECT_USER_INFO.comments)}
                                <tr><td>{$smarty.const._PROFESSORCOMMENTS}: &nbsp;{$T_PROJECT_USER_INFO.comments}</td></tr>
                            {/if}
                        {/if}
                                </table>

                        {if $T_PROJECT_USER_INFO.grade == ''}
                            {if !$T_PROJECT->expired}
                                {$T_UPLOAD_PROJECT_FORM.javascript}
                                <form {$T_UPLOAD_PROJECT_FORM.attributes}>
                                    {$T_UPLOAD_PROJECT_FORM.hidden}
                                    <table class = "formElements" style = "margin-left:0px">
                                        <tr><td class = "labelCell">{$smarty.const._FILE}:&nbsp;</td>
                                            <td>{$T_UPLOAD_PROJECT_FORM.filename.html}</td></tr>
                                        <tr><td></td><td class = "infoCell">{$smarty.const._FILESIZEMUSTBESMALLERTHAN} <b>{$T_MAX_FILE_SIZE}</b> {$smarty.const._KB}</td></tr>
                                        {if $T_UPLOAD_PROJECT_FORM.filename.error}<tr><td></td><td class = "formError">{$T_UPLOAD_PROJECT_FORM.filename.error}</td></tr>{/if}
                                        <tr><td colspan = "100%">&nbsp;</td></tr>
                                        <tr><td></td><td>{$T_UPLOAD_PROJECT_FORM.submit_upload_project.html}</td></tr>
                                    </table>
                                </form>
                            {else}
                                <img style = "vertical-align:middle;margin-right:5px;" src = "images/16x16/warning.png"/>
                                {$smarty.const._DEADLINEPASSEDYOUCANNOLONGERUPLOADFILES}
                            {/if}
                        {/if}


                {/capture}
                {eF_template_printInnerTable title="`$smarty.const._VIEWPROJECT`: `$T_PROJECT->project.title`" data=$smarty.capture.t_view_project_code image='/32x32/exercises.png'}
            {else}
                {capture name = "t_print_projects_code"}
                    {if $smarty.session.s_type == 'professor'}
                                <table border = "0">
                                    <tr><td><a href = "{$smarty.server.PHP_SELF}?ctg=projects&add_project=1"><img src="images/16x16/add2.png" style = "vertical-align: middle;" title="{$smarty.const._ADDPROJECT}" alt="{$smarty.const._ADDPROJECT}" border="0"/></a></td>
                                        <td><a href = "{$smarty.server.PHP_SELF}?ctg=projects&add_project=1">{$smarty.const._ADDPROJECT}</a></td></tr>
                                </table>
                    {/if}
                                <div class = "tabber">
                                    <div class = "tabbertab" title = "{$smarty.const._ACTIVE_PROJECTS} ({$T_ACTIVE_COUNT})">
                                    <table class = "sortedTable" width = "100%">
                                        <tr><td class = "topTitle">{$smarty.const._TITLE}</td>
                                            <td class = "topTitle">{$smarty.const._DEADLINE}</td>
                                            <td class = "topTitle">{$smarty.const._TIMEREMAIN}</td>
                                            <td class = "topTitle">{$smarty.const._CREATOR}</td>
                                            <td class = "topTitle centerAlign">{$smarty.const._GRADE}</td>
                                            <td class = "topTitle centerAlign noSort">{$smarty.const._STATUS}</td>
                                        </tr>
                        {foreach name = 'projects_list' key = 'key' item = 'project' from = $T_CURRENT_PROJECTS}
                                        <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"}">
                                            <td><a href = "{$smarty.server.PHP_SELF}?ctg=projects&view_project={$project.id}">{$project.title}</a></td>
                                            <td><span style = "display:none">{$project.deadline}</span>#filter:timestamp_time_nosec-{$project.deadline}#</td>
                                            <td><span style = "display:none">{$project.deadline}</span>{$project.time_remaining}</td>
                                            <td>{$project.creator_LOGIN}</td>
                                            <td class = "centerAlign">{$project.grade}</td>
                                            <td class = "centerAlign">
                            {if $project.filename && $project.grade}
                                                <img src = "images/16x16/checks.png" title = "{$smarty.const._PROJECTFINISHED}" alt = "{$smarty.const._PROJECTFINISHED}" />
                            {elseif $project.filename}
                                                <img src = "images/16x16/check.png" title = "{$smarty.const._FILEUPLOADEDAWAITINGGRADE}" alt = "{$smarty.const._FILEUPLOADEDAWAITINGGRADE}" />
                            {else}
                                                <img src = "images/16x16/clock.png" title = "{$smarty.const._PENDING}" alt = "{$smarty.const._PENDING}" />
                            {/if}
                                            </td>
                                        </tr>
                        {foreachelse}
                                        <tr class = "defaultRowHeight oddRowColor"><td class = "centerAlign emptyCategory" colspan = "100%">{$smarty.const._NOPROJECTS}</td></tr>
                        {/foreach}
                                    </table>
                                    </div>
                                    <div class = "tabbertab" title = "{$smarty.const._INACTIVE_PROJECTS} ({$T_INACTIVE_COUNT})">
                                    <table class = "sortedTable" width = "100%">
                                        <tr><td class = "topTitle">{$smarty.const._TITLE}</td>
                                            <td class = "topTitle">{$smarty.const._DEADLINE}</td>
                                            <td class = "topTitle">{$smarty.const._CREATOR}</td>
                                            <td class = "topTitle centerAlign">{$smarty.const._GRADE}</td>
                                            <td class = "topTitle centerAlign noSort">{$smarty.const._STATUS}</td>
                                        </tr>
                        {foreach name = 'projects_list' key = 'key' item = 'project' from = $T_EXPIRED_PROJECTS}
                                        <tr class = "defaultRowHeight {cycle name = "inactive" values = "oddRowColor, evenRowColor"}">
                                            <td><a href = "{$smarty.server.PHP_SELF}?ctg=projects&view_project={$project.id}">{$project.title}</a></td>
                                            <td><span style = "display:none">{$project.deadline}</span>#filter:timestamp_time_nosec-{$project.deadline}#</td>
                                            <td><span style = "display:none">{$project.deadline}</span>{$project.creator_LOGIN}</td>
                                            <td class = "centerAlign">{$project.grade}</td>
                                            <td class = "centerAlign">
                            {if $project.filename && $project.grade}
                                                <img src = "images/16x16/checks.png" title = "{$smarty.const._PROJECTFINISHED}" alt = "{$smarty.const._PROJECTFINISHED}" />
                            {elseif $project.filename}
                                                <img src = "images/16x16/check.png" title = "{$smarty.const._FILEUPLOADEDAWAITINGGRADE}" alt = "{$smarty.const._FILEUPLOADEDAWAITINGGRADE}" />
                            {else}
                                                <img src = "images/16x16/warning.png" title = "{$smarty.const._DEADLINEPASSED}" alt = "{$smarty.const._DEADLINEPASSED}" />
                            {/if}
                                            </td>
                                        </tr>
                        {foreachelse}
                                        <tr class = "defaultRowHeight oddRowColor"><td class = "centerAlign emptyCategory" colspan = "100%">{$smarty.const._NOPROJECTS}</td></tr>
                        {/foreach}
                                    </table>
                                    </div>
                                </div>
                {/capture}

                {eF_template_printInnerTable title=$smarty.const._PROJECTS data=$smarty.capture.t_print_projects_code image='/32x32/exercises.png'}
            {/if}

    </td></tr>
    {/capture}

{elseif $T_CTG == 'tests'}
    {assign var = "category" value = 'lessons'}
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=content&type=tests">'|cat:$smarty.const._TESTS|cat:'</a>'}

        {*moduleShowTest: Show a specific test*}
        {capture name = "moduleShowTest"}
                {section name = 'parents_list' loop = $T_PARENT_LIST step = "-1"}
                    {assign var = "truncated_name" value = $T_PARENT_LIST[parents_list].name|eF_truncate:40}
                    {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = \"titleLink\" href = \"`$smarty.server.PHP_SELF`?ctg=content&view_unit=`$T_PARENT_LIST[parents_list].id`\" title = \"`$T_PARENT_LIST[parents_list].name`\">`$truncated_name`</a>"}
                {/section}
                                <tr><td class = "moduleCell">
                        {if $T_SHOW_CONFIRMATION}
                                    {assign var = 't_show_side_menu' value = true}
                                        {capture name = "sideContentTree"}
                                            {eF_template_printSide title = $smarty.const._LESSONMATERIAL data = $T_CONTENT_TREE id = 'current_content'}
                                        {/capture}
                                        <table class = "navigationHandles">
                                            <tr><td>{eF_template_printPreviousNext previous = $T_PREVIOUS_UNIT next = $T_NEXT_UNIT}</td></tr>
                                        </table>
                                        <table class = "testHeader">
                                            <tr><td id = "testName">{$T_TEST_DATA->test.name}</td></tr>
                                            <tr><td id = "testDescription">{$T_TEST_DATA->test.description}</td></tr>
                                            <tr><td>
                                                    <table class = "testInfo">
                                                        <tr><td rowspan = "6" id = "testInfoImage"><img src = "images/48x48/desktop.png" alt = "{$T_TEST_DATA->test.name}" title = "{$T_TEST_DATA->test.name}"/></td>
                                                            <td id = "testInfoLabels"></td>
                                                            <td></td></tr>
                                                        <tr><td>{$smarty.const._TESTDURATION}:&nbsp;</td>
                                                            <td>
                                                            {if $T_TEST_DATA->options.duration}
                                                                {if $T_TEST_DATA->convertedDuration.hours}{$T_TEST_DATA->convertedDuration.hours}     {$smarty.const._HOURS}&nbsp;{/if}
                                                                {if $T_TEST_DATA->convertedDuration.minutes}{$T_TEST_DATA->convertedDuration.minutes} {$smarty.const._MINUTES}&nbsp;{/if}
                                                                {if $T_TEST_DATA->convertedDuration.seconds}{$T_TEST_DATA->convertedDuration.seconds} {$smarty.const._SECONDS}{/if}
                                                            {else}
                                                                {$smarty.const._UNLIMITED}
                                                            {/if}
                                                            </td></tr>
                                                        <tr><td>{$smarty.const._NUMOFQUESTIONS}:&nbsp;</td>
                                                            <td>{$T_TEST_QUESTIONS_NUM}</td></tr>
                                                        <tr><td>{$smarty.const._QUESTIONSARESHOWN}:&nbsp;</td>
                                                            <td>{if $T_TEST_DATA->options.onebyone}{$smarty.const._ONEBYONEQUESTIONS}{else}{$smarty.const._ALLTOGETHER}{/if}</td></tr>
                                                    {if $T_TEST_STATUS.status == 'incomplete' && $T_TEST_DATA->time.pause}
                                                        <tr><td>{$smarty.const._YOUPAUSEDTHISTESTON}:&nbsp;</td>
                                                            <td>#filter:timestamp_time-{$T_TEST_DATA->time.pause}#</td></tr>
                                                    {else}
                                                        <tr><td>{$smarty.const._DONETIMESSOFAR}:&nbsp;</td>
                                                            <td>{if $T_TEST_STATUS.timesDone}{$T_TEST_STATUS.timesDone}{else}0{/if}&nbsp;{$smarty.const._TIMES}</td></tr>
                                                        <tr><td>{if $T_TEST_STATUS.timesLeft !== false }{$smarty.const._YOUCANDOTHETEST}:&nbsp;</td>
                                                            <td>{$T_TEST_STATUS.timesLeft}&nbsp;{$smarty.const._TIMESMORE}{/if}</td></tr>
                                                    {/if}
                                                    </table>
                                                </td>
                                            <tr><td id = "testProceed">
                                            {if $T_TEST_STATUS.status == 'incomplete' && $T_TEST_DATA->time.pause}
                                                <input class = "flatButton" type = "button" name = "submit_sure" value = "{$smarty.const._RESUMETEST}&nbsp;&raquo;" onclick = "javascript:location=location+'&resume=1'" />
                                            {else}
                                                <input class = "flatButton" type = "button" name = "submit_sure" value = "{$smarty.const._PROCEEDTOTEST}&nbsp;&raquo;" onclick = "javascript:location=location+'&confirm=1'" />
                                            {/if}
                                            </td></tr>
                                        </table>
                        {elseif $smarty.get.test_analysis}
                                    {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=content&view_unit=`$smarty.get.view_unit`&test_analysis=1'>`$smarty.const._TESTANALYSISFORTEST` &quot;`$T_TEST_DATA->test.name`&quot;</a>"}

                                    {capture name = "t_test_analysis_code"}
                                        <div class = "headerTools">
                                        	<span>
	                                            <img src = "images/16x16/arrow_left_blue.png" alt = "{$smarty.const._VIEWSOLVEDTEST}" title = "{$smarty.const._VIEWSOLVEDTEST}">
    	                                        <a href = "{$smarty.server.PHP_SELF}?ctg=tests&view_unit={$smarty.get.view_unit}&show_solved_test={$smarty.get.show_solved_test}">{$smarty.const._VIEWSOLVEDTEST}</a>
    	                                    </span>
											{if $T_TEST_STATUS.testIds|@sizeof > 1}
                                            <span>
                                                <img src = "images/16x16/redo.png" alt = "{$smarty.const._JUMPTOEXECUTION}" title = "{$smarty.const._JUMPTOEXECUTION}">
												{$smarty.const._JUMPTOEXECUTION}
												<select  style = "vertical-align:middle" onchange = "location.toString().match(/show_solved_test/) ? location = location.toString().replace(/show_solved_test=\d+/, 'show_solved_test='+this.options[this.selectedIndex].value) : location = location + '&show_solved_test='+this.options[this.selectedIndex].value">
													{foreach name = "test_analysis_list" item = "item" key = "key" from = $T_TEST_STATUS.testIds}
														<option value = "{$item}" {if $smarty.get.show_solved_test == $item}selected{/if}>#{$smarty.foreach.test_analysis_list.iteration} - #filter:timestamp_time-{$T_TEST_STATUS.timestamps[$key]}#</option>
													{/foreach}
												</select>
                                            </span>
                                            {/if}
                                        </div>
                                        <table style = "width:100%">
                                            <tr><td style = "vertical-align:top">{$T_CONTENT_ANALYSIS}</td></tr>
                                            <tr><td style = "vertical-align:top"><iframe width = "750px" id = "analysis_frame" height = "550px" frameborder = "no" src = "student.php?ctg=content&view_unit={$smarty.get.view_unit}&display_chart=1&selected_unit={$smarty.get.selected_unit}&test_analysis=1"></iframe></td></tr>
                                        </table>
                                    {/capture}

                                    {eF_template_printInnerTable title = "`$smarty.const._TESTANALYSIS` `$smarty.const._FORTEST` &quot;`$T_TEST_DATA->test.name`&quot; `$smarty.const._ANDUSER` &quot;`$T_TEST_DATA->completedTest.login`&quot;" data = $smarty.capture.t_test_analysis_code image='32x32/text_view.png'}
                        {else}
                                {if $T_TEST_STATUS.status == '' || $T_TEST_STATUS.status == 'incomplete'}
                                    {capture name = "test_footer"}
                                    <table class = "formElements" style = "width:100%">
                                        <tr><td colspan = "2">&nbsp;</td></tr>
                                        <tr><td colspan = "2" class = "submitCell" style = "text-align:center">{$T_TEST_FORM.submit_test.html}&nbsp;{$T_TEST_FORM.pause_test.html}</td></tr>
                                    </table>
                                    {/capture}
                                {else}
                                    {capture name = "test_header"}
                                        {assign var = 't_show_side_menu' value = true}
                                        {capture name = "sideContentTree"}
                                            {eF_template_printSide title=$smarty.const._LESSONMATERIAL data = $T_CONTENT_TREE}
                                        {/capture}
                                        <table class = "navigationHandles">
                                            <tr><td>{eF_template_printPreviousNext previous = $T_PREVIOUS_UNIT next = $T_NEXT_UNIT}</td></tr>
                                        </table>
                                    {/capture}
                                {/if}
                                {if !$T_NO_TEST}
                                    {$T_TEST_FORM.javascript}
                                    <form {$T_TEST_FORM.attributes}>
                                        {$T_TEST_FORM.hidden}
                                        {$T_TEST}
                                        {$smarty.capture.test_footer}
                                    </form>
                                {else}
                                        {assign var = 't_show_side_menu' value = true}
                                        {capture name = "sideContentTree"}
                                            {eF_template_printSide title=$smarty.const._LESSONMATERIAL data = $T_CONTENT_TREE}
                                        {/capture}
                                        <table style = "vertical-align:top;width:100%">
                                            <tr><td></td><td style = "text-align:right">
                                                    {eF_template_printPreviousNext previous = $T_PREVIOUS_UNIT next = $T_NEXT_UNIT}
                                            </td></tr>
                                        </table>
                            {/if}
                        {/if}
                                </td></tr>
        
						{literal}
        <script language="javascript">
        <!--
             function hideSidebar(s_login)
             {
                var is_ie;
                var detect = navigator.userAgent.toLowerCase();
                detect.indexOf("msie") > 0 ? is_ie = "true" : is_ie = "false";
                var value = readCookie(s_login+'_sidebar');
                if(value != 'hidden')
                {
                    createCookie(s_login+'_sidebar','hidden',30);
                    top.document.getElementById('framesetId').cols = ""+sidebar_width+", *";
                    top.sideframe.document.body.style.paddingLeft = "130px";
                    top.sideframe.document.getElementById('arrow_down').style.right = "300px";
                    top.sideframe.document.getElementById('arrow_up').style.right = "300px";
                    top.sideframe.document.getElementById('toggleSidebarImage').src = 'images/16x16/navigate_right.png';

                    if(is_ie == "true")
                    {
                        top.sideframe.document.getElementById('toggleSidebarImage').style.position="absolute";
                        top.sideframe.document.getElementById('toggleSidebarImage').style.left = "0px";
                        top.sideframe.document.getElementById('toggleSidebarImage').style.top = "4px";

                    }
                    top.sideframe.document.getElementById('logoutImage').style.position="absolute";
                    top.sideframe.document.getElementById('logoutImage').style.left = "1px";
                    top.sideframe.document.getElementById('logoutImage').style.top = "45px";
                    top.sideframe.document.getElementById('mainPageImage').style.position="absolute";
                    top.sideframe.document.getElementById('mainPageImage').style.left = "1px";
                    top.sideframe.document.getElementById('mainPageImage').style.top = "25px";

                    var menus = top.sideframe.document.getElementById('menu').childElements().length - 1;
                    var i = 2;
                        for (i = 2; i <= menus; i++) {
                        if (top.sideframe.document.getElementById('menu'+i)) {
                            top.sideframe.document.getElementById('menu'+i).style.visibility = "hidden";
                        }
                    }

                    //top.sideframe.document.getElementById('toggleSidebarImage').style.position = "absolute";position: absolute; left: 0px";
                    //changeImage(top.sideframe.document.getElementById('toggleSidebarImage'));
                    //changeImage(top.sideframe.document.getElementById('logoutImage'));
                    //changeImage(top.sideframe.document.getElementById('mainPageImage'));
                }
            }
            var value = readCookie({/literal}'{$smarty.session.s_login}'{literal}+'_sidebar');
			var valueMode = readCookie({/literal}'{$smarty.session.s_login}'{literal}+'_sidebarMode');
            
            // If chat tab is enabled do not hide the sidebar
            if(!top.sideframe.chatEnabled && (value != 'visible' || valueMode == 'automatic')) {
            	hideSidebar({/literal}'{$smarty.session.s_login}'{literal})
            }	
            	
            //-->
        </script>
        {/literal}
		{/capture}

{elseif $T_CTG == 'calendar'}
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=calendar">'|cat:$smarty.const._CALENDAR|cat:'</a>'}
    {*moduleCalendarPage: Display the calendar page*}
    {capture name = "moduleCalendarPage"}
                            <tr><td class = "moduleCell">
                                {include file = "calendar.tpl"}
                                {eF_template_printInnerTable title=$T_CALENDAR_TITLE data=$smarty.capture.t_calendar_code image='/32x32/calendar.png' main_options=$T_CALENDAR_OPTIONS}
                            </td></tr>
    {/capture}



{elseif $T_CTG == 'glossary'}
    {assign var = "category" value = 'lessons'}

    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=glossary">'|cat:$smarty.const._GLOSSARY|cat:'</a>'}
    {*moduleGlossary: Display the glossary*}
    {capture name = "moduleGlossary"}
                            <tr><td class = "moduleCell">

                                {capture name='t_glossary_code'}
                                    {eF_template_printGlossary data=$T_GLOSSARY user_type='student'}
                                {/capture}

                                {eF_template_printInnerTable title=$smarty.const._GLOSSARY data=$smarty.capture.t_glossary_code image='/32x32/book_open2.png'}

                            </td></tr>
    {/capture}
{elseif $T_CTG == 'survey'}
    {assign var = "category" value = 'lessons'}

    {*moduleSurvey: The survey page*}
    {capture name = "moduleSurvey"}
                <tr><td class="moduleCell">
        {if ( $T_DO_TEST == '-1')}
            {eF_template_printMessage message=$smarty.const._YOUCANTDOTHESURVEYCONTACTLESSONPROFESSOR type='failure'}
            <input class="flatButton" type="button" value="{$smarty.const._RETURN}" onclick="Javascript:self.location='student.php'">
        {/if}
        {if ($smarty.get.screen_survey == '1')}
            {assign var = "title" value = '<a class="titleLink" href ="#">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo&nbsp;<a class="titleLink" href="#">'|cat:$T_SURVEYNAME|cat:'</a>'}
            <table align="center" valign="baseline" widht="100%">
            {section name='survey_screen_1' loop=$T_SURVEY_INFO}
                <tr><td>&nbsp;</td></tr>
                <tr><td>&nbsp;</td></tr>
                <tr><td>&nbsp;</td></tr>
                <tr><td>&nbsp;</td></tr>
                <tr><td align="right"><img src='images/32x32/form_green.png' border='0px'></td><td align="left"><h2>{$smarty.const._SURVEY}</h2></td>
                <tr>
                    <td></td><td align="left"><h3>{$T_SURVEY_INFO[survey_screen_1].survey_name}</h3>
                                              <h4>{$T_SURVEY_INFO[survey_screen_1].survey_info}</h4></td>
                </tr>
                <tr><td class="horizontalSeparator" colspan="2">&nbsp;</td></tr>
                <tr><td>&nbsp;</td></tr>
                <tr>
                    <td colspan="2" align="center"><input class="flatButton" type="button" value="{$smarty.const._STARTSURVEY}" onclick="Javascript:self.location='student.php?ctg=survey&screen_survey=2&surveys_ID={$smarty.get.surveys_ID}'"></td>
                </tr>
            {/section}
            </table>
        {/if}
        {if ($smarty.get.screen_survey == '2')}
            {assign var = "title" value = '<a class="titleLink" href ="#">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo&nbsp;<a class="titleLink" href="#">'|cat:$T_SURVEYNAME|cat:'</a>'}
            <table class="innerTable" width="100%">
                <tr class="handle">
                    <td class="tableImage"><img src='images/32x32/form_green.png' border='0px'></td>
                    <td class="innerTableHeader"><b>{$T_SURVEYNAME}<br>{$T_SURVEY_INFOTEXT}</b><br></td></tr>
                </tr>
                <tr><td colspan="2" class="horizontalSeparator"></td></tr>
                <tr><td colspan="2">&nbsp;</td></tr>
            </table>
            {eF_template_printSurvey questions=$T_SURVEY_QUESTIONS user_type=$T_USER intro=$T_SURVEY_STARTTEXT}
        {/if}
        {if ($smarty.get.screen_survey == '3')}
            {assign var = "title" value = '<a class="titleLink" href ="#">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo&nbsp;<a class="titleLink" href="#">'|cat:$T_SURVEYNAME|cat:'</a>'}
            <table align="center" valign="baseline" widht="100%">
            {eF_template_printMessage message=$smarty.const._SURVEYSUBMISSIONSUCCESSFUL type='success'}
            {section name='survey_screen_3' loop=$T_SURVEY_INFO}
                <tr>
                    <td>{$T_SURVEY_INFO[survey_screen_3].end_text}</td>
                </tr>
                <tr>
                    <td><input class="flatButton" type="button" value="{$smarty.const._RETURN}" onclick="Javascript:self.location='student.php'"></td>
                </tr>
            {/section}
            </table>
        {/if}
                </td></tr>
   {/capture}
{elseif $T_CTG == 'statistics'}
    {assign var = "category" value = 'mypage'}
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics">'|cat:$smarty.const._STATISTICS|cat:'</a>'}
    {if $smarty.get.option == 'user'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=user">'|cat:$smarty.const._USERSTATISTICS|cat:'</a>'}
        {if $smarty.get.sel_user}
            {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=user&sel_user='|cat:$smarty.get.sel_user|cat:'">'|cat:$smarty.get.sel_user|cat:'</a>'}
        {/if}
    {elseif $smarty.get.option == 'lesson'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=lesson">'|cat:$smarty.const._LESSONSTATISTICS|cat:'</a>'}
    {elseif $smarty.get.option == 'test'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=test">'|cat:$smarty.const._TESTSTATISTICS|cat:'</a>'}
    {elseif $smarty.get.option == 'course'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=course">'|cat:$smarty.const._COURSESTATISTICS|cat:'</a>'}
    {/if}

    {capture name = "moduleStatistics"}
                        <tr><td class = "moduleCell">
                        {if $T_TEST_SOLVED}
                            {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=statistics&show_solved_test=`$T_SOLVED_TEST_DATA.id`&lesson=`$smarty.get.lesson`'>`$smarty.const._VIEWSOLVEDTEST`: &quot;`$T_CURRENT_TEST.name`&quot; `$smarty.const._BYUSER`: `$T_SOLVED_TEST_DATA.users_LOGIN`</a>"}
                            {$T_TEST_SOLVED}
                        {else}
                            {include file="module_statistics.tpl"}
                        {/if}
                        </td></tr>
    {/capture}

{elseif $T_CTG == 'lessons'}
    {if $T_OP == 'new_lessons'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=lessons">'|cat:$smarty.const._MYLESSONS|cat:'</a>&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=lessons&op=new_lessons">'|cat:$smarty.const._NEWLESSONS|cat:'</a>'}

        {capture name = 't_paypal_show_lessons'}
                <table class = "innerTable">
                    <tr class = "handle"><th class = "innerTableHeader">
                            <img class = "iconTableImage" alt = "{$smarty.const._PAYPALTABLELESSONS}" title = "{$smarty.const._PAYPALTABLELESSONS}" src = "images/32x32/books.png"/>
                            {$smarty.const._PAYPALTABLELESSONS}
                        </th></tr>
                </table>

                {$T_BUY_LESSONS_FORM.javascript}
                <form {$T_BUY_LESSONS_FORM.attributes}>
                {$T_BUY_LESSONS_FORM.hidden}
                <table style = "width:100%" size = "{$T_LESSONS_SIZE}" id = "lessonsTable">
                    <tr class = "topTitle">
                        <td class = "topTitle" name = "name">{$smarty.const._NAME} </td>
                        <td class = "topTitle" name = "directionsTreeString">{$smarty.const._DIRECTION}</td>
                        <td class = "topTitle" name = "languages_NAME">{$smarty.const._LANGUAGE}</td>
                        <td class = "topTitle" name = "price">{$smarty.const._PRICE}</td>
                        <td class = "topTitle" style = "text-align:center">{$smarty.const._LESSONSELECT}</td>
                    </tr>
            {foreach name = 'lessons_list2' key = 'key' item = 'lesson' from = $T_LESSONS_DATA}
                {if $lesson.price != 0}
                    {assign var = "found_lessons" value = 1}
                    <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                        <td>{$lesson.name}<input type = "hidden" name = "name_lessons[{$lesson.id}]" value = "{$lesson.name}" /></td>
                        <td>{$lesson.directionsTreeString}</td>
                        <td>{$lesson.languages_NAME}</td>
                        <td>{$lesson.price} {$T_CONFIGURATION.currency}<input type = "hidden" name = "price_lessons[{$lesson.id}]" value = "{$lesson.price}" /></td>
                        <td style = "text-align:center">
                            <input  class = "inputCheckbox" type="checkbox" name="id_lessons[{$lesson.id}]" {if $lesson.directions_ID == $smarty.get.edit_direction}checked{/if}>
                        </td>
                    </tr>
                    {/if}
            {foreachelse}
                    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory centerAlign" colspan = "100%">{$smarty.const._NOLESSONSFOUND}</td></tr>
            {/foreach}
            {if !$found_lessons}
                    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory centerAlign" colspan = "100%">{$smarty.const._NOLESSONSFOUND}</td></tr>
            {/if}
                </table>
                <div align="center" style="padding: 15px;">
                    {$T_BUY_LESSONS_FORM.add_to_cart.html}
                </div>
                </form>
        {/capture}

        {capture name = 't_paypal_preview_order'}
                <table class="innerTable">
                    <tr class="handle"><th class="innerTableHeader">
                        <img class="iconTableImage" alt="{$smarty.const._PAYPALORDERPREVIEW}" title="{$smarty.const._PAYPALORDERPREVIEW}" src="images/32x32/books.png"/>
                        {$smarty.const._PAYPALORDERPREVIEW}
                    </th></tr>
                </table>
                <table style = "width:100%" size = "{$T_LESSONS_SIZE}" id = "lessonsTable">
                    <tr class = "topTitle">
                        <td class = "topTitle" name = "name">{$smarty.const._NAME} </td>
                        <td class = "topTitle" name = "price">{$smarty.const._PRICE}</td>
                    </tr>
            {foreach name = 'name' key = 'key' item = 'item' from = $T_LESSONS_DATA.name_lessons}
                {if $T_LESSONS_DATA.id_lessons[$key] == 'on'}
                    <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                        <td>{$item}</td><td>{$T_LESSONS_DATA.price_lessons[$key]} {$T_CONFIGURATION.currency}</td>
                    </tr>
                {/if}
            {/foreach}
                    <tr style="height:25px; background:#D3D3D3 none repeat scroll 0%; border-bottom:1px solid #AAAAAA; border-top:1px solid #AAAAAA;">
                        <td align="right"><b>{$smarty.const._PAYPALFINALPRICE}:</b> </td><td><b>{$T_LESSONS_FINAL_PRICE} {$T_CONFIGURATION.currency}</b></td>
                    </tr>
                </table>
                <form {$T_ORDER_LESSONS_FORM.attributes}>
                    {$T_ORDER_LESSONS_FORM.hidden}
                    <div align="center" style="padding: 15px;">
                        {$T_ORDER_LESSONS_FORM.order.html}
                    </div>
                </form>
                <form {$T_ORDER_LESSONS_FORMDATA.attributes}>
                    {$T_ORDER_LESSONS_FORMDATA.hidden}
                </form>
        {/capture}

        {capture name = "moduleNewLessonsList"}
            {if $smarty.get.fct == 'preview_order'}
                {$smarty.capture.t_paypal_preview_order}
            {else}
                {$smarty.capture.t_paypal_show_lessons}
            {/if}
        {/capture}
    {elseif $T_OP == 'tests'}
{*ttttttttttttttttttttt*}

        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=lessons&op=tests">'|cat:$smarty.const._SKILLGAPTESTS|cat:'</a>'}
        {capture name = "moduleLessonsList"}
                              <tr><td class = "moduleCell">
         {if isset($smarty.get.solve_test)}
         {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=lessons&op=tests&solve_test='|cat:$smarty.get.solve_test|cat:'">'|cat:$T_TEST_DATA->test.name|cat:'</a>'}

            {if $T_SHOW_CONFIRMATION}
                            <table class = "testHeader">
                                <tr><td id = "testName">{$T_TEST_DATA->test.name}</td></tr>
                                <tr><td id = "testDescription">{$T_TEST_DATA->test.description}</td></tr>
                                <tr><td>
                                        <table class = "testInfo">
                                            <tr><td rowspan = "3" id = "testInfoImage"><img src = "images/48x48/desktop.png" alt = "{$T_TEST_DATA->test.name}" title = "{$T_TEST_DATA->test.name}"/></td>
                                                <td id = "testInfoLabels"></td>
                                                <td></td></tr>
                                            <tr><td>{$smarty.const._NUMOFQUESTIONS}:&nbsp;</td>
                                                <td>{$T_TEST_QUESTIONS_NUM}</td></tr>
                                            <tr><td>{$smarty.const._QUESTIONSARESHOWN}:&nbsp;</td>
                                                <td>{if $T_TEST_DATA->test.onebyone}{$smarty.const._ONEBYONEQUESTIONS}{else}{$smarty.const._ALLTOGETHER}{/if}</td></tr>
                                        </table>
                                    </td>
                                <tr><td id = "testProceed">
                                {if $T_TEST_STATUS.status == 'incomplete' && $T_TEST_DATA->time.pause}
                                    <input class = "flatButton" type = "button" name = "submit_sure" value = "{$smarty.const._RESUMETEST}&nbsp;&raquo;" onclick = "javascript:location=location+'&resume=1'" />
                                {else}
                                    <input class = "flatButton" type = "button" name = "submit_sure" value = "{$smarty.const._PROCEEDTOTEST}&nbsp;&raquo;" onclick = "javascript:location=location+'&confirm=1'" />
                                {/if}
                                </td></tr>
                            </table>
            {elseif $smarty.get.test_analysis}
                        {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=content&view_unit=`$smarty.get.view_unit`&test_analysis=1'>`$smarty.const._TESTANALYSISFORTEST` &quot;`$T_TEST_DATA->test.name`&quot;</a>"}

                        {capture name = "t_test_analysis_code"}
                            <div class = "headerTools">
                                <img src = "images/16x16/arrow_left_blue.png" alt = "{$smarty.const._VIEWSOLVEDTEST}" title = "{$smarty.const._VIEWSOLVEDTEST}">
                                <a href = "{$smarty.server.PHP_SELF}?ctg=tests&show_solved_test={$smarty.get.show_solved_test}">{$smarty.const._VIEWSOLVEDTEST}</a>
                            </div>
                            <table style = "width:100%">
                                <tr><td style = "vertical-align:top">{$T_CONTENT_ANALYSIS}</td></tr>
                                <tr><td style = "vertical-align:top"><iframe width = "100%" height = "300px" frameborder = "no" src = "student.php?ctg=content&view_unit={$smarty.get.view_unit}&display_chart=1&test_analysis=1"></iframe></td></tr>
                            </table>
                        {/capture}

                        {eF_template_printInnerTable title = "`$smarty.const._TESTANALYSIS` `$smarty.const._FORTEST` &quot;`$T_TEST_DATA->test.name`&quot; `$smarty.const._ANDUSER` &quot;`$T_TEST_DATA->completedTest.login`&quot;" data = $smarty.capture.t_test_analysis_code image='32x32/text_view.png'}
            {else}
                    {if $T_TEST_STATUS.status == '' || $T_TEST_STATUS.status == 'incomplete'}
                        {capture name = "test_footer"}
                        <table class = "formElements" style = "width:100%">
                            <tr><td colspan = "2">&nbsp;</td></tr>
                            <tr><td colspan = "2" class = "submitCell" style = "text-align:center">{$T_TEST_FORM.submit_test.html}&nbsp;{$T_TEST_FORM.pause_test.html}</td></tr>
                        </table>
                        {/capture}
                    {/if}
                    {if !$T_NO_TEST}
                        {$T_TEST_FORM.javascript}
                        <form {$T_TEST_FORM.attributes}>
                            {$T_TEST_FORM.hidden}
                            {$T_TEST}
                            {$smarty.capture.test_footer}
                        </form>
                {/if}
            {/if}
         {else}

            {if $T_TESTS}
            {eF_template_printIconTable title=$smarty.const._SKILLGAPTESTSTOBECOMPLETED columns=3 links=$T_TESTS image='/32x32/pda_write.png'}
            {else}
                <table width = "100%">
                    <tr><td class = "emptyCategory centerAlign">{$smarty.const._NOSKILLGAPTESTSASSIGNEDTOYOU}</td></tr>
                </table>
            {/if}
         {/if}
                                </td></tr>
        {/capture}
    {else}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=lessons">'|cat:$smarty.const._MYLESSONS|cat:'</a>'}
        {capture name = "moduleLessonsList"}
                              <tr><td class = "moduleCell">
            {if $T_DIRECTIONS_TREE}
                {if $T_CONFIGURATION.lessons_directory == '1' || $T_CONFIGURATION.lessons_directory == '2'}
                                    <div style = "float:right;">
                                        <table>
                                            <tr>
	                                            {if $T_SKILLGAP_TESTS}
	                                            <td>
	                                                <img src = "images/32x32/pda_write.png" title = "{$T_SKILLGAP_TESTS}" alt = "{$T_SKILLGAP_TESTS}" style = "vertical-align:middle;"></td><td>
	                                                <a href = "student.php?ctg=lessons&op=tests" style = "vertical-align:middle;" title="{$T_SKILLGAP_TESTS}">{$smarty.const._NEWSKILLGAPTESTS}</a>
	                                            </td>
	                                            {/if}                                            
                                            	<td>
                                                <img src = "images/32x32/cabinet.png" title = "{$smarty.const._LESSONSDIRECTORY}" alt = "{$smarty.const._LESSONSDIRECTORY}" style = "vertical-align:middle;"></td><td>
                                                <a href = "directory.php" style = "vertical-align:middle;">{$smarty.const._LESSONSDIRECTORY}</a>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                {/if}
									{$T_DIRECTIONS_TREE}									
                                    <script language = "JavaScript" type = "text/javascript" src = "js/wz_tooltip.php"></script>
            {else}
                                    <table style = "width:100%;margin-top:100px;text-align:center;">
                                        <tr><td class = "mediumHeader">{$smarty.const._YOUDONTHAVEANYLESSONS}</td></tr>
                {if $T_CONFIGURATION.lessons_directory == '1' || $T_CONFIGURATION.lessons_directory == '2'}
                                        <tr><td style = "padding-top:25px">
                                            <a href = "directory.php" style = "vertical-align:middle"><img src = "images/48x48/cabinet.png" title = "{$smarty.const._LESSONSDIRECTORY}" alt = "{$smarty.const._LESSONSDIRECTORY}" style = "vertical-align:middle;border-width:0px"></a><br/>
                                            <a href = "directory.php" style = "vertical-align:middle">{$smarty.const._LESSONSDIRECTORY}</a>
                                        </td></tr>
                {else}
                                        <tr><td style = "padding-top:25px">{$smarty.const._THEADMINISTRATORWILLASSIGNYOULESSONS}</td></tr>
                {/if}
                     
                                        {if $T_SKILLGAP_TESTS}
                                        <tr><td class = "mediumHeader" style = "padding-top:15px">{$smarty.const._COMPLETETHESKILLGAPTESTSBELOWSOTHATWECANASSIGNLESSONS}</td></tr>
                                        <tr><td><a href = "student.php?ctg=lessons&op=tests" style = "vertical-align:middle;" title="{$T_SKILLGAP_TESTS}"><img src = "images/48x48/pda_write.png" border =0 title = "{$T_SKILLGAP_TESTS}" alt = "{$T_SKILLGAP_TESTS}" style = "vertical-align:middle;"></a></td></tr>
                                        <tr><td><a href = "student.php?ctg=lessons&op=tests" style = "vertical-align:middle;" title="{$T_SKILLGAP_TESTS}">{$smarty.const._NEWSKILLGAPTESTS}</a></td></tr>
                                        {elseif $T_SKILLGAP_TESTS_SOLVED}
                                        <tr><td class = "mediumHeader" style = "padding-top:15px">{$smarty.const._YOUHAVECOMPLETEDALLSKILLGAPSTESTSASSIGNEDTOYOUWAITTOBEASSIGNEDLESSONS}</td></tr>
                                        {/if}

                                    </table>
            {/if}
                            </td></tr>

        {/capture}
    {/if}
{elseif $T_CTG == 'personal'}
    {assign var = "category" value = 'mypage'}

    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=personal">'|cat:$smarty.const._MYSETTINGS|cat:'</a>'}
    {capture name = "modulePersonal"}
                            <tr><td class = "moduleCell">
                                {include file = "includes/module_personal.tpl"}
                            </td></tr>
    {/capture}

{elseif $T_CTG_MODULE}
    {assign var = "category" value = 'lessons'}

    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg='|cat:$T_CTG|cat:'">'|cat:$T_CTG_MODULE|cat:'</a>'}
    {capture name = "importedModule"}
                            <tr><td class = "moduleCell">
                                {include file = $smarty.const.G_MODULESPATH|cat:$T_CTG|cat:'/module.tpl'}
                            </td></tr>
    {/capture}
{/if}


{**************}
{* MODULE HCD: *}
{**************}
{*yyyyyyyyyyyyyyyyyyyypopto*}
{if (isset($T_CTG) && $T_CTG == 'users')}
    {if !isset($smarty.get.print_preview) && !isset($smarty.get.print)}
    {assign var = "category" value = 'company'}
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=users">'|cat:$smarty.const._USERS|cat:'</a>'}
    {/if}

    {if $smarty.get.add_user || $smarty.get.edit_user}
    {*moduleNewUser: Create a new user*}
            {capture name = "moduleNewUser"}
                                <tr><td class = "moduleCell">
                                {if !isset($smarty.get.print_preview) && !isset($smarty.get.print)}
                                    {if $smarty.get.edit_user != ""}
                                        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=users&edit_user='|cat:$smarty.get.edit_user|cat:'">'|cat:$smarty.const._EDITUSER|cat:'</a>'}
                                    {else}
                                        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=users&add_user=1">'|cat:$smarty.const._NEWUSER|cat:'</a>'}
                                    {/if}
                                {/if}
                                        <table width = "100%">
                                            <tr><td class = "topAlign" width = "50%">
                                                    {if isset($T_PERSONAL)}
                                                        {include file = "includes/module_personal.tpl"}
                                                    {/if}
                                                </td>
                                            </tr>
                                        </table>
                                </td></tr>
        {/capture}
    {else}
{*moduleUsers: The users functions*}
    {capture name = "moduleUsers"}
        {if $T_MODULE_HCD_INTERFACE}
            {include file = "module_hcd.tpl"}
        {else}
                            <tr><td class = "moduleCell">
                                    {capture name = 't_users_code'}
                                                    <table border = "0" >
                                                        <tr><td>
                                                            <a href="administrator.php?ctg=users&add_user=1"><img src="images/16x16/add2.png" title="{$smarty.const._NEWUSER}" alt="{$smarty.const._NEWUSER}"/ border="0"></a></td><td><a href="administrator.php?ctg=users&add_user=1">{$smarty.const._NEWUSER}</a>
                                                        </td></tr>
                                                    </table>
                                                    <table border = "0" width = "100%" class = "sortedTable" sortBy = "0">
                                                    <tr class = "topTitle">
                                                        <td class = "topTitle">{$smarty.const._LOGIN}</td>
                                                        <td class = "topTitle">{$smarty.const._NAME}</td>
                                                        <td class = "topTitle">{$smarty.const._SURNAME}</td>
                                                        <td class = "topTitle">{$smarty.const._USERTYPE}</td>
                                                        <td class = "topTitle">{$smarty.const._LANGUAGE}</td>
                                                        <td class = "topTitle" align="center">{$smarty.const._LESSONSNUMBER}</td>
                                                        <td class = "topTitle" align="center">{$smarty.const._ACTIVE2}</td>
                                                        <td class = "topTitle noSort" align="center">{$smarty.const._STATISTICS}</td>
                                                        <td class = "topTitle" align="center">{$smarty.const._OPERATIONS}</td>
                                                    </tr>
                                            {foreach name = 'users_list' key = 'key' item = 'user' from = $T_USERS}
                                                        <tr class = "{cycle values = "oddRowColor, evenRowColor"}">

                                                            <td>
                                                            {if ($user.pending == 1)}
                                                                 <a href = "administrator.php?ctg=users&edit_user={$user.login}" class = "editLink" style="color:red;">{$user.login}</a>
                                                            {elseif ($user.active == 1)}
                                                                 <a href = "administrator.php?ctg=users&edit_user={$user.login}" class = "editLink">{$user.login}</a>
                                                            {else}
                                                                {$user.login}
                                                            {/if}
                                                            </td>

                                                            <td>{$user.name}</td>
                                                            <td>{$user.surname}</td>
                                                            <td>{$user.user_type}</td>
                                                            <td>{$user.languages_NAME}</td>
                                                            <td align="center">{$user.lessons_num}</td>


                                                            <td align = "center">
                                                           {if $user.user_type != $smarty.const._ADMINISTRATOR}
                                                                {if $user.active == 1}
                                                                        <a href="administrator.php?ctg=users&deactivate_user={$user.login}"><img src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._DEACTIVATE}" title = "{$smarty.const._DEACTIVATE}" border = "0"></a>
                                                                {else}
                                                                        <a href="administrator.php?ctg=users&activate_user={$user.login}"><img src = "images/16x16/trafficlight_red.png" alt = "{$smarty.const._ACTIVATE}" title = "{$smarty.const._ACTIVATE}" border = "0"></a>
                                                                {/if}
                                                            {else}
                                                                <img src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._ACTIVE}" title = "{$smarty.const._ACTIVE}" border = "0">
                                                            {/if}
                                                            </td>
                                                            <td align="center"><a href="administrator.php?ctg=statistics&option=user&sel_user={$user.login}"><img border = "0" src = "images/16x16/chart.png" title = "{$smarty.const._STATISTICS}" alt = "{$smarty.const._STATISTICS}" /></a></td>
                                                            <td align = "center">
                                                                <table><tr><td width="45%">
                                                                    {if $user.active == 1}
                                                                        <a href = "administrator.php?ctg=users&edit_user={$user.login}" class = "editLink"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
                                                                    {else}
                                                                        <img border = "0" src = "images/16x16/edit_gray.png" title = "{$smarty.const._UNPRIVILEGEDATTEMPT}" alt = "{$smarty.const._UNPRIVILEGEDATTEMPT}" />
                                                                     {/if}
                                                                </td><td></td><td  width="45%">
                                                                {if $user.user_type != "administrator"}
                                                                    <a href = "administrator.php?ctg=users&op=users_data&delete_user={$user.login}" onclick = "return confirm('{$smarty.const._AREYOUSUREYOUWANTTODELETEUSER}')" class = "deleteLink"><img border = "0" src = "images/16x16/delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a>
                                                                {else}
                                                                       <img border = "0" src = "images/16x16/delete_gray.png" title = "{$smarty.const._UNPRIVILEGEDATTEMPT}" alt = "{$smarty.const._UNPRIVILEGEDATTEMPT}" />
                                                                 {/if}
                                                                </td></tr></table>
                                                            </td>

                                                    </tr>

                                            {/foreach}
                                                    </table>
                                            {/capture}
                                            {eF_template_printInnerTable title = $smarty.const._UPDATEUSERS data = $smarty.capture.t_users_code image = '/32x32/user1.png'}
                            </td></tr>
        {/if}
    {/capture}

    {/if}

{/if}


{* SELECT LESSONS *}
{if (isset($T_CTG) && $T_CTG == 'lessons_select')}
    {assign var = "category" value = 'lessons'}
    {assign var = "title" value = '<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=lessons_select">'|cat:$smarty.const._LESSONSELECT|cat:'</a>'}
    {capture name = "mylessonsModule"}
        {capture name = "user_to_lesson"}

        {if isset($T_USER_TO_LESSON_FORM)}
                                {if $smarty.session.s_type == "administrator" || ($T_MODULE_HCD_INTERFACE && $T_CTG != "personal")}
                                        <form method="post" action="{$smarty.server.PHP_SELF}?ctg=users&edit_user={$smarty.get.edit_user}">
                                {else}
                                        <form method="post" action="{$smarty.server.PHP_SELF}?ctg=personal&edit_user={$smarty.get.edit_user}">
                                {/if}

                                    <table border = "0" width = "100%" class = "sortedTable" sortBy = "0">
                                        <tr class = "topTitle">
                                            <td class = "topTitle">{$smarty.const._NAME}</td>
                                            <td>{$smarty.const._PARENTDIRECTIONS}</td>
                                {if $smarty.session.s_type == "administrator"}
                                            <td class = "topTitle">{$smarty.const._USERTYPE}</td>
                                            <td class = "topTitle centerAlign">{$smarty.const._STATUS}</td>
                                {elseif $T_MODULE_HCD_INTERFACE == 0}
                                            <td class = "topTitle" align = "center" >{$smarty.const._PRICE}</td>
                                {/if}
                                            <td class = "topTitle" align = "center">{$smarty.const._CHECK}</td>
                                        </tr>

                                {foreach name = 'users_to_lessons_list' key = 'key' item = 'lesson' from = $T_USER_TO_LESSON_FORM}
                                        {strip}
                                        <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                                            <td>
                                    {if isset($lesson.info)}
                                                    {if $lesson.from_timestamp ==0 && $lesson.active ==1 }
                                                         <a href = "javascript:void(0)" class = "info nonEmptyLesson" style="color:red;">{$lesson.name}
                                                    {else}
                                                         <a href = "javascript:void(0)" class = "info nonEmptyLesson">{$lesson.name}
                                                    {/if}


                                                    <img class = "tooltip" border = "0" src="/images/others/tooltip_arrow.gif"/>
                                                    <span>
                                    {if isset($lesson.info.general_description)}<strong>{$smarty.const._GENERALDESCRIPTION|cat:'</strong>:&nbsp;'|cat:$lesson.info.general_description}<br />{/if}
                                    {if isset($lesson.info.assessment)}         <strong>{$smarty.const._ASSESSMENT|cat:'</strong>:&nbsp;'|cat:$lesson.info.assessment}<br/>{/if}
                                    {if isset($lesson.info.objectives)}         <strong>{$smarty.const._OBJECTIVES|cat:'</strong>:&nbsp;'|cat:$lesson.info.objectives}<br/>{/if}
                                    {if isset($lesson.info.lesson_topics)}      <strong>{$smarty.const._LESSONTOPICS|cat:'</strong>:&nbsp;'|cat:$lesson.info.lesson_topics}<br/>{/if}
                                    {if isset($lesson.info.resources)}          <strong>{$smarty.const._RESOURCES|cat:'</strong>:&nbsp;'|cat:$lesson.info.resources}<br/>{/if}
                                    {if isset($lesson.info.other_info)}         <strong>{$smarty.const._OTHERINFO|cat:'</strong>:&nbsp;'|cat:$lesson.info.other_info}<br/>{/if}
                                                    </span>
                                                </a>
                                    {else}
                                            {if $lesson.from_timestamp ==0 && $lesson.active ==1 }
                                                <span style="color:red">{$lesson.name}</span>
                                            {else}
                                                {* MODULE HCD: Make links to classes - this can also be done generally *}
                                                {if $T_MODULE_HCD_INTERFACE}
                                                   <a href="{$smarty.session.s_type}.php?ctg=lessons&edit_lesson={$lesson.id}">{$lesson.name}</a>
                                                {else}
                                                   {$lesson.name}
                                                {/if}
                                            {/if}
                                    {/if}
                                            </td>
                                            <td>
                                    {section name = 'directions_list' loop = $lesson.directions_ID}
                                        {if $smarty.section.directions_list.last}
                                                {$lesson.directions_ID[directions_list].name}
                                        {else}
                                                {$lesson.directions_ID[directions_list].name} ->
                                        {/if}
                                    {/section}
                                            </td>
                                    {* Mpaltas change from (if $smarty.get.ctg == "users") to... *}
                                    {if $smarty.session.s_type == "administrator"}
                                            <td>
                                                <select name = "type_{$lesson.id}">
                                        {foreach name = 'roles_list' key = 'role_key' item = 'role_item' from = $T_ROLES_ARRAY}
                                            {if ($lesson.user_type == $role_key)}
                                                    <option value = "{$role_key}" selected>{$role_item}</option>
                                            {else}
                                                    <option value = "{$role_key}">{$role_item}</option>
                                            {/if}
                                        {/foreach}
                                                </select>
                                            </td>
                                            <td align = "center">{if $lesson.from_timestamp ==0 && $lesson.active ==1 }<img src = "/images/16x16/warning.png" title = "{$smarty.const._APPLICATIONPENDING}" alt = "{$smarty.const._APPLICATIONPENDING}"/>{else}<img src = "/images/16x16/check2.png" title = "{$smarty.const._NORMALSTATUS}" alt = "{$smarty.const._NORMALSTATUS}"/>{/if}</td>
                                    {elseif $smarty.get.ctg == "personal" && $T_MODULE_HCD_INTERFACE == 0}
                                            <td align="center">
                                        {if $lesson.price == 0}
                                                {$smarty.const._FREE}
                                        {else}
                                                {$lesson.price} {$T_CONFIGURATION.currency}
                                        {/if}
                                            </td>
                                    {/if}
                                            <td align = "center">
                                    {if $smarty.get.ctg == "personal"}
                                        {if $lesson.active == 1}
                                                <input class = "inputCheckBox" type = "checkbox" disabled name = "{$lesson.id}" checked>
                                        {else}
                                                <input class = "inputCheckBox" type = "checkbox" name = "{$lesson.id}">
                                        {/if}
                                    {else}
                                        {if $lesson.active == 1}
                                                <input class = "inputCheckBox" type = "checkbox" name = "{$lesson.id}" checked>
                                        {else}
                                                <input class = "inputCheckBox" type = "checkbox" name = "{$lesson.id}">
                                        {/if}
                                    {/if}
                                            </td>
                                        </tr>
                                        {/strip}
                                {/foreach}
                                    </table>
                                    <br />
                                    <table width = "100%">
                                        <tr><td align = "center">
                                {if $smarty.get.ctg == "personal"}
                                            <input  class = "flatButton" name="users_to_lesson" type="submit" value="{$smarty.const._SUBMIT}">
                                {/if}
                                        </td></tr>
                                    </table>
                                </form>

        {else}
                <table width = "100%">
                    <tr><td class = "emptyCategory centerAlign">{$smarty.const._THEREARENOLESSONSDEFINEDFORTHEUSERLANGUAGE}</td></tr>
                </table>
        {/if}
        {/capture}
        {eF_template_printInnerTable title = $smarty.const._LESSONSELECT data = $smarty.capture.user_to_lesson image = '/32x32/books.png'}
    {/capture}
{/if}

{if (isset($T_CTG) && $T_CTG == 'emails')}
   {assign var = "category" value = 'company'}

   {assign var = "title" value = '<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd">'|cat:$smarty.const._ORGANIZATION|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=emails">'|cat:$smarty.const._EMAILS|cat:'</a>'}
   {include file = "emails.tpl"}
{/if}

{if (isset($T_CTG) && $T_CTG == 'module_hcd')}
    {assign var = "category" value = 'company'}

{*moduleHCD: The resuls of control panel*}
    {if $smarty.get.op != 'reports'}
        {assign var = "title" value = '<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd">'|cat:$smarty.const._ORGANIZATION|cat:'</a>'}
    {/if}

    {if $smarty.get.op == "branches"}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd&op=branches">'|cat:$smarty.const._BRANCHES|cat:'</a>'}
        {if $smarty.get.add_branch || $smarty.get.edit_branch}
                {if $smarty.get.edit_branch != ""}
                    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd&op=branches&edit_branch='|cat:$smarty.get.edit_branch|cat:'">'|cat:$smarty.const._BRANCHRECORD|cat:'</a>'}
                {else}
                    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd&op=branches&add_branch=1">'|cat:$smarty.const._BRANCHRECORD|cat:'</a>'}
                {/if}
        {/if}
    {/if}
    {if $smarty.get.op == "skills"}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd&op=skills">'|cat:$smarty.const._SKILLS|cat:'</a>'}
        {if $smarty.get.add_skill || $smarty.get.edit_skill}
                {if $smarty.get.edit_skill != ""}
                    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd&op=skills&edit_skill='|cat:$smarty.get.edit_skill|cat:'">'|cat:$smarty.const._SKILLDATA|cat:'</a>'}
                {else}
                    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd&op=skills&add_skill=1">'|cat:$smarty.const._SKILLDATA|cat:'</a>'}
                {/if}
         {/if}
    {/if}

    {if $smarty.get.op == "job_descriptions"}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd&op=job_descriptions">'|cat:$smarty.const._JOBDESCRIPTIONS|cat:'</a>'}
        {if $smarty.get.add_job_description || $smarty.get.edit_job_description}
            {if $smarty.get.edit_job_description != ""}
                {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd&op=job_descriptions&edit_job_description='|cat:$smarty.get.edit_job_description|cat:'">'|cat:$smarty.const._JOBDESCRIPTIONDATA|cat:'</a>'}
            {else}
                {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd&op=job_descriptions&add_job_description=1">'|cat:$smarty.const._JOBDESCRIPTIONDATA|cat:'</a>'}
            {/if}
        {/if}
    {/if}

    {if $smarty.get.op == 'reports'}
       {if $smarty.session.s_type == "administrator"}
           {assign var = "title" value = '<a class="titleLink" href ="'|cat:$smarty.session.s_type|cat:'.php?ctg=statistics">'|cat:$smarty.const._STATISTICS|cat:'</a>'}
           {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd&op=reports">'|cat:$smarty.const._SEARCHFOREMPLOYEE|cat:'</a>'}
       {else}
           {assign var = "title" value = '<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd&op=reports">'|cat:$smarty.const._SEARCHFOREMPLOYEE|cat:'</a>'}
       {/if}
    {/if}

    {if $smarty.get.op == 'chart'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd&op=chart">'|cat:$smarty.const._ORGANIZATIONCHARTTREE|cat:'</a>'}
    {/if}

    {if $smarty.get.op == "placements"}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd&op=placements">'|cat:$smarty.const._PLACEMENTS|cat:'</a>'}
    {/if}

    {capture name = "moduleHCD"}
                            <tr><td class = "moduleCell">
                                {include file = 'module_hcd.tpl'}
                            </td></tr>
    {/capture}
{/if}
{*TELOS HCD*}


{if (isset($T_SOLVED_TESTS))}
    {capture name = "sideSolvedTests"}
                    {capture name='t_solved_tests_code'}
                        {section name = 'solved_tests_list' loop = $T_SOLVED_TESTS}
                            <a href = "student.php?ctg=tests&view_unit={$T_SOLVED_TESTS[solved_tests_list].content_ID}" title="{$T_SOLVED_TESTS[solved_tests_list].name}">{$T_SOLVED_TESTS[solved_tests_list].name|eF_truncate:22:"...":true} ({$T_SOLVED_TESTS[solved_tests_list].score}%)</a><br/>
                        {sectionelse}
                            <tr><td class = "emptyCategory">{$smarty.const._NOSOLVEDTESTSFOUND}</td></tr>
                        {/section}
                    {/capture}

                    {eF_template_printSide title = $smarty.const._SOLVEDTESTS data = $smarty.capture.t_solved_tests_code array = $T_SOLVED_TESTS id = 'solved_tests'}
    {/capture}
{/if}

{if (isset($T_RELATED_POSTS))}
    {capture name = "sideRelatedPosts"}
                    {capture name='t_related_posts_code'}
                        {section name = 'related_posts_list' loop = $T_RELATED_POSTS}
                            <a href = "" title = "" class = "tree_forum"></a><br/>
                        {sectionelse}
                            <tr><td class = "emptyCategory">{$smarty.const._NORELATEDPOSTSFOUND}</td></tr>
                        {/section}
                    {/capture}

                    {* Display seen content*}
                    {eF_template_printSide title = $smarty.const._ATFORUM data = $smarty.capture.t_related_posts_code array = $T_RELATED_POSTS id = 'related_posts'}
    {/capture}
{/if}


{*///MODULES1*}
{if $T_CTG == 'module'}
    {assign var = "title" value = $T_MODULE_NAVIGATIONAL_LINKS}
    {capture name = "importedModule"}
        <tr><td class = "moduleCell">
            {if $T_MODULE_SMARTY}
                {include file = $T_MODULE_SMARTY}
            {else}
                {$T_MODULE_PAGE}
            {/if}
            {*include file = $smarty.const.G_MODULESPATH|cat:$T_CTG|cat:'/module.tpl'*}
        </td></tr>
    {/capture}
{/if}
{*----------------------------End of Part 2: Modules List------------------------------------------------*}



{*-----------------------------Part 3: Display table-------------------------------------------------*}

<div id = "bookmarks_div" style = "display:none">
</div>

{* Javascript code for changing the category cell color onMouseover and onMouseout *}
{literal}
<script>
function changeItemColor(item, color) {
//alert(document.getElementById(item).style.backgroundColor + ' ' +color);
   document.getElementById(item).style.backgroundColor = color;
}
</script>
{/literal}

<table class = "mainTable" id = "mainTable">
    <tr>
        <td style = "vertical-align: top;">
            <table class = "centerTable">
{if !$smarty.get.popup && !$T_POPUP_MODE}
                <tr>
                    <td class = "topTitle" {* style="border-top: 2px solid #97AFC9" *} id = "title">{$title}</td>         {*Header*}
                    <td class = "topTitle rightAlign" style = "width:1%;white-space:nowrap;">
					   {if $T_CURRENT_LESSON->options.bookmarking}
							<a href = "javascript:void(0)" onclick = "getBookmarks();" title = "{$smarty.const._SHOWBOOKMARKS}"><img src = "images/16x16/bookmark.png" title = "{$smarty.const._SHOWBOOKMARKS}" alt = "{$smarty.const._SHOWBOOKMARKS}" border = "0"/></a>&nbsp;&nbsp;
							<a href = "javascript:void(0)" title = "{$smarty.const._ADDTHISPAGETOYOURBOOKMARKS}" onclick = "if (confirm('{$smarty.const._DOYOUWANTTOADDTHISPAGETOYOURBOOKMARKS}')) addBookmark()"><img src = "images/16x16/bookmark_add.png" title = "{$smarty.const._ADD}" alt = "{$smarty.const._ADD}" border = "0"/></a>&nbsp;&nbsp;
                       {/if}
					   {if $smarty.get.view_unit && $smarty.get.ctg != 'tests'}
                            <a href = "add_comment.php?content_ID={$smarty.get.view_unit}&op=insert" onclick = "eF_js_showDivPopup('{$smarty.const._ADDCOMMENT}', new Array('500px', '300px'))" target = "POPUP_FRAME" title = "{$smarty.const._ADDCOMMENT}"><img src = "images/16x16/note_add.png" title = "{$smarty.const._ADDCOMMENT}" alt = "{$smarty.const._ADDCOMMENT}" border = "0"/></a>&nbsp;&nbsp;
                       {/if}
                       {if $smarty.session.s_lessons_ID != "" && $T_CURRENT_LESSON->options.lesson_info && $T_CTG != 'control_panel' && $smarty.get.ctg != "lessons"}
                            <a href = "{$smarty.server.PHP_SELF}?ctg=control_panel&op=lesson_information&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._LESSONINFORMATION}', 2)" target = "POPUP_FRAME" title = "{$smarty.const._LESSONINFORMATION}"><img src = "images/16x16/about.png" title = "{$smarty.const._LESSONINFORMATION}" alt = "{$smarty.const._LESSONINFORMATION}" border = "0"/></a>&nbsp;&nbsp;
                       {/if}
					   {if ($T_CTG == 'glossary' || isset($smarty.get.view_unit)) && $T_CURRENT_LESSON->options.content_report}
							<a href = "content_report.php?{$smarty.server.QUERY_STRING}" onclick = "eF_js_showDivPopup('{$smarty.const._CONTENTREPORT}', new Array('500px','300px'))" target = "POPUP_FRAME"><img src = "images/16x16/warning.png" title = "{$smarty.const._CONTENTREPORTTOPROFS}" alt = "{$smarty.const._CONTENTREPORTTOPROFS}" border = "0"/></a>&nbsp;&nbsp;
					   {/if}

                       {if $t_show_side_menu}
                            <a id="sidebarslideup" style="display:inline;" href="javascript:void(0)"  onclick="sidebarUp();"><img style="text-align:middle" border="0" src="images/16x16/navigate_right.png" style="vertical-align:top" alt="{$smarty.const._HIDESIDEBAR}" title="{$smarty.const._HIDESIDEBAR}"/></a><a class = "topTitle" href="javascript:void(0)" id="sidebarslidedown"  onclick="sidebarDown();" style="display: none;"><img style="text-align:top" border="0" src="images/16x16/navigate_left.png" style="vertical-align:middle" alt="{$smarty.const._SHOWSIDEBAR}" title="{$smarty.const._SHOWSIDEBAR}"/></a>
                       {/if}
                    </td>
                </tr>
                <script>
                {literal}
                function getBookmarks() {
                    new Ajax.Request('student.php?ajax=1&bookmarks=get', {
                            method:'get',
                            asynchronous:true,
                            onSuccess: function (transport) {
                                $('bookmarks_div').update(transport.responseText);
                                eF_js_showDivPopup('{/literal}{$smarty.const._BOOKMARKS}{literal}', new Array('500px', '300px'), 'bookmarks_div');
                            }
                        });
                }
                function addBookmark() {
                    var name = encodeURIComponent($('title').down('a', $('title').select('a').size() - 1).innerHTML);
                    var url  = encodeURIComponent('student.php' + window.location.search);
                    new Ajax.Request('student.php?ajax=1&bookmarks=add&name='+name+'&url='+url, {
                            method:'get',
                            asynchronous:true,
                            onSuccess: function (transport) {
                                alert(transport.responseText);
                            }
                        });
                }
                function removeBookmark(id) {
                    new Ajax.Request('student.php?ajax=1&bookmarks=remove&id='+id, {
                            method:'get',
                            asynchronous:true,
                            onSuccess: function (transport) {
                                $('popup_data').update(transport.responseText);
                            }
                        });
                }
                {/literal}
                </script>
{/if}
{*
{if $T_SYSTEM_ANNOUNCEMENTS}
                <tr><td class = "systemAnnouncements" colspan = "100%">
                    {foreach key = key item = announcement from = $T_SYSTEM_ANNOUNCEMENTS}
                        {$announcement.title} (#filter:timestamp-{$announcement.timestamp}#): {$announcement.data} <br/>
                    {/foreach}
                </td></tr>
{/if}
*}
{if $T_MESSAGE}
                <tr class = "messageRow">
                    <td colspan = "100%">{eF_template_printMessage message=$T_MESSAGE type=$T_MESSAGE_TYPE}</td>        {*Display Message, if any*}
                </tr>
{/if}
{if $smarty.get.message}
                <tr class = "messageRow">
                    <td colspan = "100%">{eF_template_printMessage message=$smarty.get.message type=$smarty.get.message_type}</td>        {*Display Message passed through get, if any*}
                </tr>
{/if}
{if $T_SEARCH_MESSAGE}
                <tr class = "messageRow">
                    <td colspan = "100%">{eF_template_printMessage message=$T_SEARCH_MESSAGE}</td>        {*Display Search Message, if any*}
                </tr>
{/if}

{if ($T_CTG == 'current_content') || ($T_CTG == 'control_panel' && !isset($T_OP))}        {*Pages with 2-column layout*}

{*LEFT MAIN COLUMN*}
                <tr style = "background-color:#EEEEEE;vertical-align:middle;background:#FAFAFA url('images/others/grey1.png') repeat-x top;">
                	<td colspan = "2">
                		<table style = "width:100%">
                			<tr><td style = "vertical-align:middle;font-size:20px;font-weight:bold;padding:10px 0px 10px 0px;white-space:nowrap;">
			                		<span style = "vertical-align:middle;margin-left:5px">{$T_CURRENT_LESSON->lesson.name}</span>
			                	</td>
			                	<td style = "white-space:nowrap;width:100%;padding-left:10px">
                        		{foreach name = 'header_options_list' item = "item" key = "key" from = $T_HEADER_OPTIONS}
                            		<a href = "{$item.href}" target = "{$item.target}"><img src = "images/{$item.image}" style = "border-width:0px;vertical-align:middle;margin-right:10px" alt = "{$item.text}" title = "{$item.text}" onclick = "{$item.onClick}"></a>
                        		{/foreach}
                            	</td>
                            	<td>
                            		<a href = "{$smarty.server.PHP_SELF}?ctg=lessons"><img src = "images/32x32/back_lessons.png" alt = "{$smarty.const._CHANGELESSON}" title = "{$smarty.const._CHANGELESSON}" style = "border-width:0px;margin-right:10px;vertical-align:middle"></a>
                            	</td>
                			</tr>
                		</table>
                	</td></tr>
                <tr>
                    <td class = "singleColumn" id = "singleColumn" colspan = "100%">
                        <div id="sortableList">
                            <div style="float:left; width:50%; height:100%;margin-left:1px;">
                                <ul class="sortable" id="firstlist" style="height:100px;width:100%;">
                {foreach name=positions_first key=key item=module from=$T_POSITIONS_FIRST}
                	{if $smarty.capture.$module}
                                    <li id="firstlist_{$module}" {if isset($T_POSITIONS_VISIBILITY[$module]) && !$T_POSITIONS_VISIBILITY[$module]}collapsed = "1"{/if}>
                                        <table class = "singleColumnData">
                                            {$smarty.capture.$module}
                                        </table>
                                    </li>
					{/if}
                {/foreach}

                {if !in_array('moduleIconLessonOptions', $T_POSITIONS) && $smarty.capture.moduleIconLessonOptions}
                                    <li id="firstlist_moduleIconLessonOptions">
                                        <table class = "singleColumnData">
                                            {$smarty.capture.moduleIconLessonOptions}
                                        </table>
                                    </li>
                {/if}
                {if !in_array('moduleContentTree', $T_POSITIONS) && $smarty.capture.moduleContentTree}
                                    <li id="firstlist_moduleContentTree">
                                        <table class = "singleColumnData">
                                            {$smarty.capture.moduleContentTree}
                                        </table>
                                    </li>
                {/if}
                {if !in_array('moduleProjectsList', $T_POSITIONS) && $smarty.capture.moduleProjectsList}
                                    <li id="firstlist_moduleProjectsList">
                                        <table class = "singleColumnData">
                                            {$smarty.capture.moduleProjectsList}
                                        </table>
                                    </li>
                {/if}
                                </ul>
                            </div>
                            <div style="float: right; width:49%;height: 100%;margin-right:1px;">
                                <ul class="sortable" id="secondlist" style="height:100px;width:100%;">
                {foreach name=positions_first key=key item=module from=$T_POSITIONS_SECOND}
                	{if $smarty.capture.$module}
                                    <li id="secondlist_{$module}" {if isset($T_POSITIONS_VISIBILITY[$module]) && !$T_POSITIONS_VISIBILITY[$module]}collapsed = "1"{/if}>
                                        <table class = "singleColumnData">
                                            {$smarty.capture.$module}
                                        </table>
                                    </li>
					{/if}
                {/foreach}
                {if !in_array('moduleNewsList', $T_POSITIONS) && $smarty.capture.moduleNewsList}        {*If a module is not defined, $smarty.capture.<module> does not exist. But if we ommit the && clause here, it leaves a gap, due to the <li> element*}
                                    <li id="secondlist_moduleNewsList">
                                        <table class = "singleColumnData">
                                            {$smarty.capture.moduleNewsList}
                                        </table>
                                    </li>
                {/if}
                {if !in_array('modulePersonalMessagesList', $T_POSITIONS) && $smarty.capture.modulePersonalMessagesList}
                                    <li id="secondlist_modulePersonalMessagesList">
                                        <table class = "singleColumnData">
                                            {$smarty.capture.modulePersonalMessagesList}
                                        </table>
                                    </li>
                {/if}
                {if !in_array('moduleForumList', $T_POSITIONS) && $smarty.capture.moduleForumList}
                                    <li id="secondlist_moduleForumList">
                                        <table class = "singleColumnData">
                                            {$smarty.capture.moduleForumList}
                                        </table>
                                    </li>
                {/if}
                {if !in_array('moduleComments', $T_POSITIONS) && $smarty.capture.moduleComments}
                                    <li id="secondlist_moduleComments">
                                        <table class = "singleColumnData">
                                            {$smarty.capture.moduleComments}
                                        </table>
                                    </li>
                {/if}
                {if !in_array('moduleCalendar', $T_POSITIONS) && $smarty.capture.moduleCalendar}
                                    <li id="secondlist_moduleCalendar">
                                        <table class = "singleColumnData">
                                            {$smarty.capture.moduleCalendar}
                                        </table>
                                    </li>
                {/if}
                {if !in_array('moduleDigitalLibrary', $T_POSITIONS) && $smarty.capture.moduleDigitalLibrary}
                                    <li id="secondlist_moduleDigitalLibrary">
                                        <table class = "singleColumnData">
                                            {$smarty.capture.moduleDigitalLibrary}
                                        </table>
                                    </li>
                {/if}

{*///MODULES INNERTABLES APPEARING*}
                {foreach name = 'module_inner_tables_list' key = key item = module from = $T_INNERTABLE_MODULES}
                    {assign var = module_name value = $key|replace:"_":""}
                    {if !in_array($module_name, $T_POSITIONS)}
                            <li id="secondlist_{$module_name}">
                                <table class = "singleColumnData">
                                    {$smarty.capture.$module_name}
                                </table>
                            </li>
                    {/if}
                {/foreach}

                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>

{elseif ($T_CTG == 'settings' || ($T_CTG == 'control_panel' && !$T_OP)) }
                <tr>
                    <td class = "leftColumn" id = "leftColumn">
                        <table class = "leftColumnData">
                            {$smarty.capture.moduleContentTracking}
                            {$smarty.capture.moduleContentTree}
                            {$smarty.capture.moduleProjectsList}
                        </table>
                    </td>
                    <td class = "rightColumn" id = "rightColumn">
                        <table class = "rightColumnData">
                            {$smarty.capture.moduleNewsList}
                            {$smarty.capture.modulePersonalMessagesList}
                            {$smarty.capture.moduleForumList}
                            {$smarty.capture.moduleComments}
                            {$smarty.capture.moduleCalendar}
                            {$smarty.capture.moduleDigitalLibrary}
                        </table>
                    </td>
                </tr>
{else}                                                                          {*Pages with single-column layout*}
{*SINGLE MAIN COLUMN*}
                <tr>
                    <td class = "singleColumn" id = "singleColumn" colspan = "100%" >
                        <table class = "singleColumnData">
                            {$smarty.capture.moduleExercises}
                            {$smarty.capture.moduleLessonInformation}
                            {$smarty.capture.moduleNewsPage}
                            {$smarty.capture.moduleDigitalLibraryFull}
                            {$smarty.capture.moduleCalendarPage}
                            {$smarty.capture.moduleNavigation}
                            {$smarty.capture.moduleNewLessonsList}
                            {$smarty.capture.moduleLessonsList}
                            {$smarty.capture.moduleTests}
                            {$smarty.capture.moduleShowTest}
                            {$smarty.capture.moduleSpecificContent}
                            {$smarty.capture.moduleGlossary}
                            {$smarty.capture.moduleFullCalendar}
                            {$smarty.capture.moduleStatistics}
                            {$smarty.capture.moduleSurvey}
                            {$smarty.capture.modulePersonal}
                            {$smarty.capture.moduleSearchResults}
                            {$smarty.capture.moduleContentData}
                            {$smarty.capture.moduleLessonPlan}
                            {$smarty.capture.importedModule}
                            {$smarty.capture.moduleHCD}
                            {$smarty.capture.moduleNewUser}
                            {$smarty.capture.moduleEmail}
                            {$smarty.capture.moduleUsers}
                            {$smarty.capture.mylessonsModule}
                        </table>
                    </td>
                </tr>
{/if}
            </table>

        </td>
{if ($t_show_side_menu)}                                     {*Pages that need the right side menu*}
        <td class = "sideMenu" id = "sideMenu_td">
            <div  id = "sideMenu" style="overflow: visible;">
            {$smarty.capture.sideContentTree}
            {$smarty.capture.sideProgress}
            {$smarty.capture.sideUnitOperations}
            {$smarty.capture.sideSolvedTests}
            {$smarty.capture.sideRelatedPosts}
            </div>
        {literal}
        <script language="javascript">
        <!--
            sidebarCheck();
        //-->
        </script>
        {/literal}
        </td>
{/if}
    </tr>
{if $T_CONFIGURATION.show_footer && !$smarty.get.popup && !$T_POPUP_MODE}
    {include file = "includes/footer.tpl"}
{/if}

</table>

{/strip}

{*-----------------------------End of Part 3: Display table-------------------------------------------------*}







{*-----------------------------Part 4: Finalization data etc-------------------------------------------------*}
{if ($T_CTG == 'control_panel') && !isset($T_OP)}        {*Pages with 2-column layout*}
    {literal}
        <script type = "text/javascript">
           Sortable.create("firstlist", {
             containment:["firstlist", "secondlist"], constraint:false,
             onUpdate: function() {
                new Ajax.Request('set_positions.php', {
                    method:'post',
                    asynchronous:true,
                    parameters: { firstlist: Sortable.serialize('firstlist'), secondlist: Sortable.serialize('secondlist') },
                    onSuccess: function (transport) {}
                });
            }});
           Sortable.create("secondlist",
             {containment:["firstlist","secondlist"],constraint:false,
             onUpdate: function() {
                new Ajax.Request('set_positions.php', {
                    method:'post',
                    asynchronous:true,
                    parameters: { firstlist: Sortable.serialize('firstlist'), secondlist: Sortable.serialize('secondlist') },
                    onSuccess: function (transport) {}
                });
            }});
        </script>
    {/literal}
{/if}

{include file = "includes/closing.tpl"}

</body>
</html>

{if ($T_MODULE_HCD_INTERFACE && $T_CTG == 'users' && $smarty.get.print == 1 && $smarty.const.MSIE_BROWSER == 1)}
{literal}
<script>
printPartOfPage('singleColumn');
</script>
{/literal}
{/if}


<!-- end -->