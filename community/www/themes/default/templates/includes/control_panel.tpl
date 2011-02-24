{if $T_OP == 'search'}
    {*moduleSearchResults: The Search results page*}
    {capture name = "moduleSearchResults"}
  <tr><td class = "moduleCell">
          {include file = "includes/module_search.tpl"}
  </td></tr>
    {/capture}

{elseif isset($T_OP_MODULE)}
     {capture name = "importedModule"}
     <tr><td class = "moduleCell">
         {include file = $smarty.const.G_MODULESPATH|cat:$T_OP|cat:'/module.tpl'}
     </td></tr>
    {/capture}

{else}

 {*moduleNewUsersApplications: The list of inactive users, waiting for activation*}
 {if $T_INACTIVE_USERS}
  {capture name = "moduleNewUsersApplications"}
      <tr><td class = "moduleCell">
          {capture name = 't_inactive_users_code'}
              {section name = 'inactive_users_list' loop = "$T_INACTIVE_USERS"}
         <span class = "counter">{$smarty.section.inactive_users_list.iteration}.</span>
         <a href = "{$smarty.server.PHP_SELF}?ctg=personal&user={$T_INACTIVE_USERS[inactive_users_list].login}&op=profile">#filter:login-{$T_INACTIVE_USERS[inactive_users_list].login}#</a><br/>
              {sectionelse}
         <span class = "emptyCategory">{$smarty.const._NONEWAPPLICATIONS}</span>
              {/section}
          {/capture}

          {eF_template_printBlock title = $smarty.const._NEWUSERS data = $smarty.capture.t_inactive_users_code image = '32x32/users.png' array = $T_INACTIVE_USERS link=$T_INACTIVE_USERS_LINK}
      </td></tr>
  {/capture}
 {/if}

 {*moduleNewsList: A list with system announcements*}
 {if $T_CONFIGURATION.disable_news != 1 && $T_CURRENT_USER->coreAccess.news != 'hidden' && ($_admin_ || $T_CURRENT_LESSON->options.news)}
        {capture name = "moduleNewsList"}
   <tr><td class = "moduleCell">
          {capture name='t_news_code'}
           <table class = "cpanelTable">
           {foreach name = 'news_list' item = "item" key = "key" from = $T_NEWS}
            <tr><td>{$smarty.foreach.news_list.iteration}. <a title = "{$item.title}" href = "{$smarty.server.PHP_SELF}?ctg=news&view={$item.id}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._ANNOUNCEMENT}', 1);">{$item.title}</a></td>
             <td class = "cpanelTime">#filter:user_login-{$item.users_LOGIN}#, <span title = "#filter:timestamp_time-{$item.timestamp}#">{$item.time_since}</span></td></tr>
           {foreachelse}
            <tr><td class = "emptyCategory">{$smarty.const._NOANNOUNCEMENTSPOSTED}</td></tr>
           {/foreach}
           </table>
          {/capture}

          {eF_template_printBlock title = $smarty.const._ANNOUNCEMENTS content = $smarty.capture.t_news_code image = '32x32/announcements.png' options = $T_NEWS_OPTIONS link = $T_NEWS_LINK expand = $T_POSITIONS_VISIBILITY.moduleNewsList}
   </td></tr>
        {/capture}
 {/if}

    {*moduleCalendar: Display the calendar innertable*}
    {if $T_CONFIGURATION.disable_calendar != 1 && $T_CURRENT_USER->coreAccess.calendar != 'hidden' && ($_admin_ || $T_CURRENT_LESSON->options.calendar)}
     {capture name = "moduleCalendar"}
            <tr><td class = "moduleCell">
                {capture name = "t_calendar_code"}
                    {eF_template_printCalendar events=$T_CALENDAR_EVENTS timestamp=$T_VIEW_CALENDAR}
                {/capture}
                {assign var="calendar_title" value = "`$smarty.const._CALENDAR` (#filter:timestamp-`$T_VIEW_CALENDAR`#)"}
                {eF_template_printBlock title=$calendar_title data=$smarty.capture.t_calendar_code image='32x32/calendar.png' options=$T_CALENDAR_OPTIONS link=$T_CALENDAR_LINK expand = $T_POSITIONS_VISIBILITY.moduleCalendar}
            </td></tr>
     {/capture}
    {/if}

 {*moduleNewLessonsApplications: The list of new lessons/courses applications*}
    {if $T_NEW_LESSONS || $T_NEW_COURSES}
       {capture name = "moduleNewLessonsApplications"}
         <tr><td class = "moduleCell">
             {capture name = 't_new_lessons_code'}
                 {section name = 'new_lessons_list' loop = "$T_NEW_LESSONS"}
                             {counter}. <a href = "{$smarty.server.PHP_SELF}?ctg=personal&user={$T_NEW_LESSONS[new_lessons_list].users_LOGIN}&op=user_courses">#filter:login-{$T_NEW_LESSONS[new_lessons_list].users_LOGIN}# ({$T_NEW_LESSONS[new_lessons_list].count} {if $T_NEW_LESSONS[new_lessons_list].count == 1}{$smarty.const._LESSON}{else}{$smarty.const._LESSONS}{/if})</a><br/>
                 {/section}
                 {foreach name = 'new_courses_list' item = "item" key = "key" from = $T_NEW_COURSES}
                  {counter}. <a href = "{$smarty.server.PHP_SELF}?ctg=courses&edit_course={$item.id}&tab=users">#filter:login-{$item.users_LOGIN}# ({$item.name}{if $item.supervisor_LOGIN} - {$smarty.const._SUPERVISORAPPROVAL}{/if})</a> <br/>
                 {/foreach}
             {/capture}

             {eF_template_printBlock title = $smarty.const._LESSONSREGISTRATIONS data = $smarty.capture.t_new_lessons_code image = '32x32/lessons.png' array = $T_NEW_LESSONS link = 'administrator.php?ctg=lessons' }
         </td></tr>
        {/capture}
    {/if}


    {*moduleProjectsList: Lists Projects*}
 {if $T_PROJECTS}
        {capture name = "moduleProjectsList"}
   <tr><td class = "moduleCell">
          {capture name='t_projects_code'}
              {eF_template_printProjects data=$T_PROJECTS limit=5}
          {/capture}

          {eF_template_printBlock title=$smarty.const._PROJECTS data=$smarty.capture.t_projects_code image='32x32/projects.png' options=$T_PROJECTS_OPTIONS link=$T_PROJECTS_LINK expand = $T_POSITIONS_VISIBILITY.moduleProjectsList}
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

          {eF_template_printBlock title=$smarty.const._RECENTMESSAGESATFORUM data=$smarty.capture.t_forum_messages_code image='32x32/forum.png' options=$T_FORUM_OPTIONS link=$T_FORUM_LINK expand = $T_POSITIONS_VISIBILITY.moduleForumList}
   </td></tr>
        {/capture}
    {/if}

    {*modulePersonalMessagesList: Lists Unread personal messages*}
    {if $T_PERSONAL_MESSAGES}
        {capture name = "modulePersonalMessagesList"}
   <tr><td class = "moduleCell">
          {capture name='t_personal_messages_code'}
              {eF_template_printPersonalMessages data=$T_PERSONAL_MESSAGES}
          {/capture}

          {eF_template_printBlock title=$smarty.const._RECENTUNREADPERSONALMESSAGES data=$smarty.capture.t_personal_messages_code image='32x32/mail.png' options=$T_PERSONAL_MESSAGES_OPTIONS link=$T_PERSONAL_MESSAGES_LINK expand = $T_POSITIONS_VISIBILITY.modulePersonalMessagesList}
   </td></tr>
        {/capture}
    {/if}

    {*moduleCommentsList: Lists recent Comments*}
    {if ($T_CURRENT_LESSON->options.comments) && $T_COMMENTS && $T_CONFIGURATION.disable_comments != 1}
        {capture name = "moduleCommentsList"}
   <tr><td class = "moduleCell">
                {capture name='t_comments_code'}
                    {eF_template_printComments data=$T_COMMENTS}
                {/capture}

                {eF_template_printBlock title=$smarty.const._RECENTCOMMENTS data=$smarty.capture.t_comments_code image='32x32/note.png' link=$T_COMMENTS_LINK expand = $T_POSITIONS_VISIBILITY.moduleCommentsList}
   </td></tr>
        {/capture}
    {/if}

    {*moduleDoneTests: Lists students done questions*}
    {if $T_COMPLETED_TESTS}
        {capture name = "moduleDoneTests"}
   <tr><td class = "moduleCell">
          {capture name='t_done_tests_code'}
              <table border = "0" width = "100%">
              {section name = 'completed_test' loop = $T_COMPLETED_TESTS max = 10}
                  <tr><td>
                    <a href = "{$smarty.server.PHP_SELF}?ctg=tests&show_solved_test={$T_COMPLETED_TESTS[completed_test].id}" style = "float:left">
         {$T_COMPLETED_TESTS[completed_test].name|eF_truncate:50}</a>
        <span style = "float:right">#filter:user_login-{$T_COMPLETED_TESTS[completed_test].users_LOGIN}#, #filter:timestamp_interval-{$T_COMPLETED_TESTS[completed_test].timestamp}# {$smarty.const._AGO}</span>
       </td></tr>
              {/section}
              </table>
          {/capture}

          {eF_template_printBlock title=$smarty.const._PENDINGTESTS data=$smarty.capture.t_done_tests_code image='32x32/tests.png' options = $T_DONE_QUESTIONS_OPTIONS link=$T_DONE_QUESTIONS_LINK}
   </td></tr>
        {/capture}
    {/if}

    {*moduleTimeline: Print lessons timeline*}
    {if ($T_CURRENT_LESSON->options.lessons_timeline) && isset($T_TIMELINE_EVENTS)}
  {capture name = "moduleTimeline"}
         <tr><td class = "moduleCell">
             {capture name = "t_timeline_code"}
<!--ajax:lessonTimelineTable-->
                <table class = "sortedTable" style = "width:100%" noFooter = "true" size = "{$T_TIMELINE_EVENTS_SIZE}" sortBy = "0" id = "lessonTimelineTable" useAjax = "1" rowsPerPage = "10" limit="10" url = "{$smarty.server.PHP_SELF}?ctg=social&op=timeline&lessons_ID={$smarty.session.s_lessons_ID}{if isset($smarty.get.topics_ID)}&topics_ID={$smarty.get.topics_ID}{/if}&">
                    <tr style="display:none" class = "topTitle">
                        <td class = "topTitle noSort" name="description">{$smarty.const._SKILL}</td>
                        <td class = "topTitle noSort" name="surname" >{$smarty.const._SPECIFICATION}</td>
                        <td class = "topTitle noSort" name="timestamp" >{$smarty.const._TIMESTAMP}</td>
                    </tr>
            {if isset($T_TIMELINE_EVENTS)}
                {foreach name = 'events_list' key = 'key' item = 'event' from = $T_TIMELINE_EVENTS}
                    <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                        <td class = "elementCell">
       <img src = "view_file.php?file={$event.avatar}" title="{$smarty.const._CURRENTAVATAR}" alt="{$smarty.const._CURRENTAVATAR}" width = "{$event.avatar_width}" height = "{$event.avatar_height}" style="vertical-align:middle" />
                        </td>
                        <td width="1px">&nbsp;</td>
                        <td width="100%">{$event.message} <span class="timeago">{$event.time}</span> <br/>
                    </tr>
                {/foreach}
                </table>
<!--/ajax:lessonTimelineTable-->
            {else}
                <tr><td colspan = 3>
                    <table width = "100%">
                        <tr><td class = "emptyCategory">{$smarty.const._NORELATEDPEOPLEFOUND}</td></tr>
                    </table>
                    </td>
                </tr>
                </table>
<!--/ajax:lessonTimelineTable-->
            {/if}
    {/capture}

          {eF_template_printBlock title=$smarty.const._TIMELINE data=$smarty.capture.t_timeline_code image='32x32/user_timeline.png' options=$T_TIMELINE_OPTIONS link=$T_TIMELINE_LINK }
      </td></tr>
  {/capture}
 {/if}

    {*moduleContentTree: Print the Content Tree*}
    {if $_student_ && $T_CURRENT_USER->coreAccess.content != 'hidden' && $T_CURRENT_LESSON->options.content_tree}
        {capture name = "moduleContentTree"}
         <tr><td class = "moduleCell">
             {capture name = 't_content_tree'}
                 {$T_CONTENT_TREE}
             {/capture}
             {eF_template_printBlock title = $smarty.const._CURRENTCONTENT data = $smarty.capture.t_content_tree image = "32x32/content.png" alt = '<span class = "emptyCategory">'|cat:$smarty.const._NOCONTENTFOUND|cat:'</span>' options = $T_TREE_OPTIONS expand = $T_POSITIONS_VISIBILITY.moduleContentTree}
         </td></tr>
        {/capture}
    {/if}

 {*moduleDigitalLibrary: Print the digital library section and file list*}
    {if $T_FILE_MANAGER && $T_CURRENT_LESSON->options.digital_library}
        {capture name = "moduleDigitalLibrary"}
         <tr><td class = "moduleCell">
             {capture name = 't_digital_library'}
                 {$T_FILE_MANAGER}
             {/capture}

             {eF_template_printBlock title = $smarty.const._SHAREDFILES data = $smarty.capture.t_digital_library image = "32x32/file_explorer.png" link = $T_FILE_LIST_LINK options=$T_FILES_LIST_OPTIONS expand = $T_POSITIONS_VISIBILITY.moduleDigitalLibrary}
         </td></tr>
        {/capture}
    {/if}

    {*moduleIconFunctions: Print icon Table with lesson options*}
    {if $T_CURRENT_USER->coreAccess.control_panel != 'hidden' && (!$_student_ || ($T_CURRENT_LESSON && $T_CURRENT_LESSON->options.show_student_cpanel))}
        {capture name = "moduleIconFunctions"}
     <tr><td class = "moduleCell">
         {eF_template_printBlock title=$smarty.const._OPTIONS columns=4 links=$T_CONTROL_PANEL_OPTIONS image='32x32/options.png' expand = $T_POSITIONS_VISIBILITY.moduleIconFunctions groups = $T_CONTROL_PANEL_GROUPS}
        </td></tr>
        {/capture}
 {/if}

    {*Inner table modules *}
    {foreach name = 'module_inner_tables_list' key = key item = moduleItem from = $T_INNERTABLE_MODULES}
        {capture name = $key|replace:"_":""} {*We cut off the underscore, since scriptaculous does not seem to like them*}
            <tr><td class = "moduleCell">
                {if $moduleItem.smarty_file}
                    {include file = $moduleItem.smarty_file}
                {else}
                    {$moduleItem.html_code}
                {/if}
            </td></tr>
        {/capture}
    {/foreach}

{/if}

{capture name = "moduleControlPanel"}
 {if $_student_ || $_professor_}
  <tr><td class = "moduleCell">
    <table class = "horizontalBlock">
                 <tr><td>
                  <a class = "rightOption" href="javascript:void(0);" onclick="location='{$smarty.server.PHP_SELF}?ctg=lessons';{if $T_NO_HORIZONTAL_MENU}top.sideframe.hideAllLessonSpecific();{/if}"><img src = "images/32x32/go_back.png" alt = "{$smarty.const._CHANGELESSON}" title = "{$smarty.const._CHANGELESSON}" class = "handle"></a>
                        <span class = "leftOption">{$T_CURRENT_LESSON->lesson.name}</span>
                        {foreach name = 'header_options_list' item = "item" key = "key" from = $T_HEADER_OPTIONS}
                         <a class = "leftOption" href = "{$item.href}" target = "{$item.target}"><img src = "images/{$item.image}" alt = "{$item.text}" title = "{$item.text}" onclick = "{$item.onClick}" class = "handle"></a>
                        {/foreach}
                    </td></tr>
    </table>
  </td></tr>
 {/if}
 <tr>
     <td class = "moduleCell">
         <div id="sortableList">
             <div style="float: right; width:49.5%;height: 100%;">
                 <ul class="sortable" id="secondlist" style="width:100%;">
 {foreach name=positions_first key=key item=module from=$T_POSITIONS_SECOND}
                     <li id="secondlist_{$module}">
                         <table class = "singleColumnData">
                             {$smarty.capture.$module}
                         </table>
                     </li>
 {/foreach}
 {if !in_array('modulePersonalMessages', $T_POSITIONS) && $smarty.capture.modulePersonalMessages}
                     <li id="secondlist_modulePersonalMessages">
                         <table class = "singleColumnData">
                             {$smarty.capture.modulePersonalMessages}
                         </table>
                     </li>
 {/if}
 {if !in_array('moduleNewDirection', $T_POSITIONS) && $smarty.capture.moduleNewDirection}
                     <li id="secondlist_moduleNewDirection">
                         <table class = "singleColumnData">
                             {$smarty.capture.moduleNewDirection}
                         </table>
                     </li>
 {/if}
 {if !in_array('moduleNewUsersApplications', $T_POSITIONS) && $smarty.capture.moduleNewUsersApplications}
                     <li id="secondlist_moduleNewUsersApplications">
                         <table class = "singleColumnData">
                             {$smarty.capture.moduleNewUsersApplications}
                         </table>
                     </li>
 {/if}
 {if !in_array('moduleNewLessonsApplications', $T_POSITIONS) && $smarty.capture.moduleNewLessonsApplications}
                     <li id="secondlist_moduleNewLessonsApplications">
                         <table class = "singleColumnData">
                             {$smarty.capture.moduleNewLessonsApplications}
                         </table>
                     </li>
 {/if}
 {if !in_array('moduleNewLesson', $T_POSITIONS) && $smarty.capture.moduleNewLesson}
                     <li id="secondlist_moduleNewLesson">
                         <table class = "singleColumnData">
                             {$smarty.capture.moduleNewLesson}
                         </table>
                     </li>
 {/if}
 {if !in_array('moduleNewsList', $T_POSITIONS) && $smarty.capture.moduleNewsList}
                     <li id="secondlist_moduleNewsList">
                         <table class = "singleColumnData">
                             {$smarty.capture.moduleNewsList}
                         </table>
                     </li>
 {/if}
 {if !in_array('moduleCalendar', $T_POSITIONS) && $smarty.capture.moduleCalendar && $T_CONFIGURATION.disable_calendar != 1}
                     <li id="secondlist_moduleCalendar">
                         <table class = "singleColumnData">
                             {$smarty.capture.moduleCalendar}
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
 {if !in_array('modulePersonalMessagesList', $T_POSITIONS) && $smarty.capture.modulePersonalMessagesList}
                     <li id="secondlist_modulePersonalMessagesList">
                         <table class = "singleColumnData">
                             {$smarty.capture.modulePersonalMessagesList}
                         </table>
                     </li>
 {/if}
 {if !in_array('moduleDoneTests', $T_POSITIONS) && $smarty.capture.moduleDoneTests}
                     <li id="secondlist_moduleDoneTests">
                         <table class = "singleColumnData">
                             {$smarty.capture.moduleDoneTests}
                         </table>
                     </li>
 {/if}
 {if !in_array('moduleCommentsList', $T_POSITIONS) && $smarty.capture.moduleCommentsList && $T_CONFIGURATION.disable_comments != 1}
                     <li id="secondlist_moduleCommentsList">
                         <table class = "singleColumnData">
                          {$smarty.capture.moduleCommentsList}
                      </table>
                     </li>
 {/if}

 {if !in_array('moduleTimeline', $T_POSITIONS) && $smarty.capture.moduleTimeline}
                     <li id="secondlist_moduleTimeline">
                         <table class = "singleColumnData">
                             {$smarty.capture.moduleTimeline}
                         </table>
                     </li>
 {/if}
 {if !in_array('moduleProjectsList', $T_POSITIONS) && $smarty.capture.moduleProjectsList && $T_CONFIGURATION.disable_projects != 1}
                     <li id="firstlist_moduleProjectsList">
                         <table class = "singleColumnData">
                             {$smarty.capture.moduleProjectsList}
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
      <li id = "second_empty" style = "display:none;"></li>
     </ul>
                </div>
                <div style="width:50%; height:100%;">
                    <ul class="sortable" id="firstlist" style="width:100%;">
    {foreach name=positions_first key=key item=module from=$T_POSITIONS_FIRST}
                        <li id="firstlist_{$module}">
                            <table class = "singleColumnData">
                                {$smarty.capture.$module}
                            </table>
                        </li>
    {/foreach}
    {if !in_array('moduleIconFunctions', $T_POSITIONS) && $smarty.capture.moduleIconFunctions}
                        <li id="firstlist_moduleIconFunctions">
                            <table class = "singleColumnData">
                                {$smarty.capture.moduleIconFunctions}
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
    {if !in_array('moduleLessonSettings', $T_POSITIONS) && $smarty.capture.moduleLessonSettings}
                        <li id="firstlist_moduleLessonSettings">
                            <table class = "singleColumnData">
                                {$smarty.capture.moduleLessonSettings}
                            </table>
                        </li>
    {/if}

                        <li id = "first_empty" style = "display:none;"></li>
                    </ul>
                </div>
   </div>
       </td>
   </tr>

{/capture}
