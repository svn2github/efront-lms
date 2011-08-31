{include file = "includes/header.tpl"}
{if $smarty.session.s_lessons_ID}
 {assign var=lessonName value=$T_CURRENT_LESSON->lesson.name}
    {if $T_NO_HORIZONTAL_MENU}{assign var = "title_onclick" value = "top.sideframe.hideAllLessonSpecific();"}{/if}
  {assign var = "title" value = "<a class = 'titleLink' title = '`$smarty.const._CHANGELESSON`' href = '`$smarty.server.PHP_SELF`?ctg=lessons' onclick = '`$title_onclick`'>`$smarty.const._MYCOURSES`</a>"}
  {if isset($T_CURRENT_COURSE_NAME)}
   {assign var = "titleCourse" value = "`$T_CURRENT_COURSE_NAME`&nbsp;&raquo;&nbsp;"}
   {assign var = "title" value = "`$title`<span>&nbsp;&raquo;&nbsp;</span><a class = 'titleLink' title = '`$T_CURRENT_COURSE_NAME`' href ='`$smarty.server.PHP_SELF`?ctg=control_panel'>`$T_CURRENT_COURSE_NAME`</a>"}
  {/if}
  {if $T_CURRENT_LESSON}
   {assign var = "title" value = "`$title`<span>&nbsp;&raquo;&nbsp;</span><a class = 'titleLink' title = '`$T_CURRENT_CATEGORY_PATH`&nbsp;&raquo;&nbsp;`$titleCourse``$T_CURRENT_LESSON->lesson.name`' href ='`$smarty.server.PHP_SELF`?ctg=control_panel'>`$lessonName`</a>"}
  {/if}
 {else}
  {assign var = "title" value = '<a class = "titleLink" title = "'|cat:$smarty.const._HOME|cat:'" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel">'|cat:$smarty.const._HOME|cat:'</a>'}
 {/if}

{strip}

{if (isset($T_CTG) && $T_CTG == 'control_panel')}
 {if $T_OP == 'search'}
  {assign var = "title" value = $title}
 {elseif isset($T_OP_MODULE)}
  {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel&op='|cat:$T_OP|cat:'">'|cat:$T_OP_MODULE|cat:'</a>'}
 {/if}
    {include file = "includes/control_panel.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'landing_page')}
 {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=landing_page">'|cat:$smarty.const._LANDINGPAGE|cat:'</a>'}
    {include file = "includes/landing_page.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'lesson_information')}
 {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=lesson_information">'|cat:$smarty.const._LESSONINFORMATION|cat:'</a>'}
    {include file = "includes/lesson_information.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'digital_library')}
 {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=digital_library">'|cat:$smarty.const._SHAREDFILES|cat:'</a>'}
    {capture name = "moduleDigitalLibraryFull"}
    <tr><td class = "moduleCell">
            {capture name = 't_digital_library'}
                {$T_FILE_MANAGER}
            {/capture}

            {eF_template_printBlock title = $smarty.const._SHAREDFILES data = $smarty.capture.t_digital_library image = "32x32/file_explorer.png"}
    </td></tr>
    {/capture}

{/if}
{if (isset($T_CTG) && $T_CTG == 'news')}
 {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=news">'|cat:$smarty.const._ANNOUNCEMENTS|cat:'</a>'}
    {include file = "includes/news.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'progress')}
 {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=progress">'|cat:$smarty.const._PROGRESS|cat:'</a>'}
 {if $T_USER_LESSONS_INFO}
  {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?ctg=progress&edit_user=`$smarty.get.edit_user`'>`$smarty.const._PROGRESSFORUSER`1: #filter:login-`$T_USER_LESSONS_INFO.users_LOGIN`#</a>"}
 {/if}
    {include file = "includes/progress.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'comments')}
    {include file = "includes/comments.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'content')}
    {if $smarty.get.add}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=content&add=1">'|cat:$smarty.const._ADDCONTENT|cat:'</a>'}
    {elseif $smarty.get.edit}
        {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?ctg=content&view_unit=`$smarty.get.edit`'>`$T_ENTITY_FORM.name.value`</a>"}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=content&edit='|cat:$smarty.get.edit|cat:'">'|cat:$smarty.const._EDITCONTENT|cat:'&nbsp;<span class="innerTableName">&quot;'|cat:$T_ENTITY_FORM.name.value|cat:'&quot;</span></a>'}
    {elseif $smarty.get.type == 'theory'}
        {assign var = "title" value = $title|cat:' &raquo; <a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=content&type=theory">'|cat:$smarty.const._THEORY|cat:'</a>'}
    {elseif $smarty.get.type == 'examples'}
        {assign var = "title" value = $title|cat:' &raquo; <a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=content&type=examples">'|cat:$smarty.const._EXAMPLES|cat:'</a>'}
    {elseif $smarty.get.type == 'tests'}
        {assign var = "title" value = $title|cat:' &raquo; <a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=content&type=tests">'|cat:$smarty.const._TESTS|cat:'</a>'}
    {elseif !$T_UNIT}
        {assign var = "title" value = $title|cat:' &raquo; <a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=content">'|cat:$smarty.const._CONTENT|cat:'</a>'}
 {else}
        {section name = 'parents_list' loop = $T_PARENT_LIST step = "-1"}
            {assign var = "truncated_name" value = $T_PARENT_LIST[parents_list].name|eF_truncate:40}
            {if $T_PARENT_LIST[parents_list].data != '' || $T_PARENT_LIST[parents_list].ctg_type == 'tests'}
                {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = \"titleLink\" href = \"`$smarty.server.PHP_SELF`?ctg=content&view_unit=`$T_PARENT_LIST[parents_list].id`\" title = \"`$T_PARENT_LIST[parents_list].name`\">`$truncated_name`</a>"}
            {else}
                {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = \"inactiveLink\" href = \"javascript:void(0)\" title = \"`$T_PARENT_LIST[parents_list].name` (`$smarty.const._EMPTYUNIT`)\">`$truncated_name`</a>"}
            {/if}
        {/section}
 {/if}
    {include file = "includes/common_content.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'projects')}
    {assign var = "category" value = 'lessons'}
 {assign var = "title" value = $title|cat:' &raquo; <a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=projects">'|cat:$smarty.const._PROJECTS|cat:'</a>'}
    {if $smarty.get.view_project}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=projects&view_project='|cat:$smarty.get.view_project|cat:'">'|cat:$smarty.const._VIEWPROJECT|cat:' &quot;'|cat:$T_CURRENT_PROJECT->project.title|cat:'&quot;</a>'}
    {/if}
    {include file = "includes/projects.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'tests')}

    {include file = "includes/tests.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'feedback')}
    {include file = "includes/tests.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'glossary')}
 {assign var = "category" value = 'lessons'}
 {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=glossary">'|cat:$smarty.const._GLOSSARY|cat:'</a>'}
    {include file = "includes/glossary.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'survey')}
 {if ($smarty.get.screen_survey == '1')}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&screen_survey=1&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo&nbsp;<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&screen_survey=1&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'">'|cat:$T_SURVEYNAME|cat:'</a>'}
    {/if}
 {if ($smarty.get.screen_survey == '2')}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class="titleLink" href ="'|cat:$smarty.server.REQUEST_URI|cat:'">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo&nbsp;<a class="titleLink" href="'|cat:$smarty.server.REQUEST_URI|cat:'">'|cat:$T_SURVEYNAME|cat:'</a>'}
    {/if}
 {if ($smarty.get.screen_survey == '3')}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:$smarty.const._SURVEY}
 {/if}
    {include file = "student/survey.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'lessons')}
    {if $T_OP == 'tests'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=lessons&op=tests">'|cat:$smarty.const._SKILLGAPTESTS|cat:'</a>'}
        {if isset($smarty.get.solve_test)}
   {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=lessons&op=tests&solve_test='|cat:$smarty.get.solve_test|cat:'">'|cat:$T_TEST_DATA->test.name|cat:'</a>'}
  {/if}
 {else}
  {if $smarty.get.course}
      {if $T_OP == course_info}
          {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?`$T_BASE_URL`&op=course_info'>`$smarty.const._INFORMATIONFORCOURSE` &quot;`$T_CURRENT_COURSE->course.name`&quot;</a>"}
      {elseif $T_OP == course_certificates}
          {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?`$T_BASE_URL`&op=course_certificates'>`$smarty.const._CERTIFICATESFORCOURSE` &quot;`$T_CURRENT_COURSE->course.name`&quot;</a>"}
      {elseif $T_OP == format_certificate}
          {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?`$T_BASE_URL`&op=format_certificate'>`$smarty.const._FORMATCERTIFICATEFORCOURSE` &quot;`$T_CURRENT_COURSE->course.name`&quot;</a>"}
      {elseif $T_OP == format_certificate_docx}
          {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?`$T_BASE_URL`&op=format_certificate_docx'>`$smarty.const._FORMATCERTIFICATEFORCOURSE` &quot;`$T_CURRENT_COURSE->course.name`&quot;</a>"}
      {elseif $T_OP == course_rules}
          {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?`$T_BASE_URL`&op=course_rules'>`$smarty.const._RULESFORCOURSE` &quot;`$T_CURRENT_COURSE->course.name`&quot;</a>"}
      {elseif $T_OP == course_order}
          {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?`$T_BASE_URL`&op=course_order'>`$smarty.const._ORDERFORCOURSE` &quot;`$T_CURRENT_COURSE->course.name`&quot;</a>"}
      {elseif $T_OP == course_scheduling}
          {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?`$T_BASE_URL`&op=course_scheduling'>`$smarty.const._SCHEDULINGFORCOURSE` &quot;`$T_CURRENT_COURSE->course.name`&quot;</a>"}
      {elseif $T_OP == export_course}
          {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?`$T_BASE_URL`&op=export_course'>`$smarty.const._EXPORTCOURSE` &quot;`$T_CURRENT_COURSE->course.name`&quot;</a>"}
      {elseif $T_OP == import_course}
          {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?`$T_BASE_URL`&op=import_course'>`$smarty.const._IMPORTCOURSE` &quot;`$T_CURRENT_COURSE->course.name`&quot;</a>"}
      {/if}
  {/if}
  {if $T_OP == 'search'}
   {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="javascript:void(0)">'|cat:$smarty.const._SEARCHRESULTS|cat:'</a>'}
  {/if}
 {/if}
 {if $smarty.get.catalog}
  {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=lessons&catalog=1'>`$smarty.const._COURSECATALOG`</a>"}
  {if $smarty.get.checkout}
   {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=lessons&catalog=1&checkout=1'>`$smarty.const._REVIEWANDCHECKOUT`</a>"}
  {/if}
 {/if}
    {include file = "includes/lessons_list.tpl"}
{/if}
{if $T_CTG == 'calendar'}
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=calendar">'|cat:$smarty.const._CALENDAR|cat:'</a>'}
 {include file = "includes/calendar.tpl"}
{/if}
{if $T_CTG == 'statistics'}
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
 {elseif $smarty.get.option == 'branches'}






    {elseif $smarty.get.option == 'advanced_user_reports'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=advanced_user_reports">'|cat:$smarty.const._ADVANCEDUSERREPORTS|cat:'</a>'}
    {elseif $smarty.get.option == 'course'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=course">'|cat:$smarty.const._COURSESTATISTICS|cat:'</a>'}
        {if isset($smarty.get.sel_course)}
            {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=course&sel_course='|cat:$smarty.get.sel_course|cat:'">'|cat:$T_CURRENT_COURSE->course.name|cat:'</a>'}
        {/if}
    {/if}

    {capture name = "moduleStatistics"}
     <tr><td class = "moduleCell">
     {if $T_TEST_SOLVED}
         {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=statistics&show_solved_test=`$T_SOLVED_TEST_DATA.id`&lesson=`$smarty.get.lesson`'>`$smarty.const._VIEWSOLVEDTEST`: &quot;`$T_CURRENT_TEST.name`&quot; `$smarty.const._BYUSER`: #filter:login-`$T_SOLVED_TEST_DATA.users_LOGIN`#</a>"}
         {$T_TEST_SOLVED}
     {else}
         {include file="includes/statistics.tpl"}
     {/if}
     </td></tr>
    {/capture}
{/if}
{if (isset($T_CTG) && $T_CTG == 'forum')}
 {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=forum">'|cat:$smarty.const._FORUMS|cat:'</a>'}
 {foreach name = 'title_loop' item = "item" key = "key" from = $T_FORUM_PARENTS}
  {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=forum&forum='|cat:$key|cat:'">'|cat:$item|cat:'</a>'}
 {/foreach}
 {if $smarty.get.topic}
     {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=forum&topic='|cat:$smarty.get.topic|cat:'">'|cat:$T_TOPIC.title|cat:'</a>'}
 {elseif $smarty.get.poll}
     {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=forum&poll='|cat:$smarty.get.poll|cat:'">'|cat:$T_POLL.title|cat:'</a>'}
 {/if}

    {include file = "includes/forum.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'messages')}
 {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=messages'>`$smarty.const._MESSAGES`</a>"}
 {if $smarty.get.view}
  {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=messages&view=`$smarty.get.view`'>`$T_PERSONALMESSAGE.title`</a>"}
 {elseif !$smarty.get.folders && $smarty.get.add}
  {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=messages&add=1'>`$smarty.const._NEWMESSAGE`</a>"}
 {/if}
 {include file = "includes/messages.tpl"}
{/if}

{if $T_CTG == 'personal'}
 {capture name = "modulePersonal"}
  {if $smarty.get.user != $smarty.session.s_login}
   {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=users'>`$smarty.const._USERS`</a>"}
  {/if}
  {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=personal&user=`$T_EDITEDUSER->user.login`'>#filter:login-`$T_EDITEDUSER->user.login`#</a>"}
  {if $T_OP == 'dashboard'}
   {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?ctg=personal&user=`$smarty.get.user`&op=dashboard'>`$smarty.const._DASHBOARD`</a>"}
  {elseif $T_OP == 'profile' && $smarty.get.add_user}
   {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?ctg=personal&user=`$T_EDITEDUSER->user.login`&op=profile&add_user=1'>`$smarty.const._NEWUSER`</a>"}
  {elseif $T_OP == 'profile' || $T_OP == 'user_groups' || $T_OP == 'mapped_accounts' || $T_OP == 'payments'}
   {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?ctg=personal&user=`$smarty.get.user`&op=profile'>`$smarty.const._ACCOUNT`</a>"}
  {elseif $T_OP == 'user_courses' || $T_OP == 'user_lessons' || $T_OP == 'certificates' || $T_OP == 'user_form'}
   {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?ctg=personal&user=`$smarty.get.user`&op=user_courses'>`$smarty.const._LEARNING`</a>"}
  {elseif $T_OP == 'placements' || $T_OP == 'history' || $T_OP == 'skills' || $T_OP == 'evaluations' || $T_OP =='org_form'}
   {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?ctg=personal&user=`$smarty.get.user`&op=placements'>`$smarty.const._ORGANIZATION`</a>"}
  {elseif $T_OP == 'files'}
   {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?ctg=personal&user=`$smarty.get.user`&op=files'>`$smarty.const._FILES`</a>"}
  {/if}
  <tr><td class = "moduleCell">
    {include file = "includes/personal.tpl"}
  </td></tr>
 {/capture}
{/if}
{if $T_CTG_MODULE}
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
{* SELECT LESSONS *}
{if (isset($T_CTG) && $T_CTG == 'lessons_select')}
    {assign var = "category" value = 'lessons'}
    {assign var = "title" value = '<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=lessons_select">'|cat:$smarty.const._LESSONSELECT|cat:'</a>'}
    {capture name = "mylessonsModule"}
        {capture name = "user_to_lesson"}
        {if isset($T_USER_TO_LESSON_FORM)}
                                        <form method="post" action="{$smarty.server.PHP_SELF}?ctg=personal&user={$smarty.get.user}">
                                    <table border = "0" width = "100%" class = "sortedTable" sortBy = "0">
                                        <tr class = "topTitle">
                                            <td class = "topTitle">{$smarty.const._NAME}</td>
                                            <td>{$smarty.const._PARENTDIRECTIONS}</td>
                                {if $smarty.session.s_type == "administrator"}
                                            <td class = "topTitle">{$smarty.const._USERTYPE}</td>
                                            <td class = "topTitle centerAlign">{$smarty.const._STATUS}</td>
                                {elseif $smarty.const.G_VERSIONTYPE != 'enterprise'}
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
                                                    <span class = "tooltipSpan">
                                    {if isset($lesson.info.general_description)}<strong>{$smarty.const._DESCRIPTION|cat:'</strong>:&nbsp;'|cat:$lesson.info.general_description}<br />{/if}
                                    {if isset($lesson.info.assessment)} <strong>{$smarty.const._ASSESSMENT|cat:'</strong>:&nbsp;'|cat:$lesson.info.assessment}<br/>{/if}
                                    {if isset($lesson.info.objectives)} <strong>{$smarty.const._OBJECTIVES|cat:'</strong>:&nbsp;'|cat:$lesson.info.objectives}<br/>{/if}
                                    {if isset($lesson.info.lesson_topics)} <strong>{$smarty.const._LESSONTOPICS|cat:'</strong>:&nbsp;'|cat:$lesson.info.lesson_topics}<br/>{/if}
                                    {if isset($lesson.info.resources)} <strong>{$smarty.const._RESOURCES|cat:'</strong>:&nbsp;'|cat:$lesson.info.resources}<br/>{/if}
                                    {if isset($lesson.info.other_info)} <strong>{$smarty.const._OTHERINFO|cat:'</strong>:&nbsp;'|cat:$lesson.info.other_info}<br/>{/if}
                                                    </span>
                                                </a>
                                    {else}
                                            {if $lesson.from_timestamp ==0 && $lesson.active ==1 }
                                                <span style="color:red">{$lesson.name}</span>
                                            {else}
                                                {* MODULE HCD: Make links to classes - this can also be done generally *}
                                                   {$lesson.name}
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
                                            <td align = "center">{if $lesson.from_timestamp ==0 && $lesson.active ==1 }<img src = "/images/16x16/warning.png" title = "{$smarty.const._APPLICATIONPENDING}" alt = "{$smarty.const._APPLICATIONPENDING}"/>{else}<img src = "/images/16x16/success.png" title = "{$smarty.const._NORMALSTATUS}" alt = "{$smarty.const._NORMALSTATUS}"/>{/if}</td>
                                    {elseif $smarty.get.ctg == "personal" && $smarty.const.G_VERSIONTYPE != 'enterprise'}
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
                                            <input class = "flatButton" name="users_to_lesson" type="submit" value="{$smarty.const._SUBMIT}">
                                {/if}
                                        </td></tr>
                                    </table>
                                </form>
        {else}
                <table width = "100%">
                    <tr><td class = "emptyCategory">{$smarty.const._THEREARENOLESSONSDEFINEDFORTHEUSERLANGUAGE}</td></tr>
                </table>
        {/if}
        {/capture}
        {eF_template_printBlock title = $smarty.const._LESSONSELECT data = $smarty.capture.user_to_lesson image = '32x32/courses.png'}
    {/capture}
{/if}
{if (isset($T_CTG) && $T_CTG == 'social')}
    {if $T_OP == 'dashboard'}
        {assign var = "title" value = '<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel">'|cat:$smarty.const._HOME|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=social&op=dashboard">'|cat:$smarty.const._DASHBOARD|cat:'</a>'}
    {elseif $T_OP == 'people'}
     {if $smarty.session.s_lessons_ID != ""}
   {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?ctg=social&op=people&display=2'>`$smarty.const._PEOPLE`</a>"}
        {else}
   {assign var = "title" value = '<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel">'|cat:$smarty.const._HOME|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=social&op=people">'|cat:$smarty.const._PEOPLE|cat:'</a>'}
     {/if}
    {elseif $T_OP == 'timeline'}
     {if isset($smarty.get.lessons_ID)}
          {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?ctg=social&op=timeline&lessons_ID=`$smarty.session.s_lessons_ID`&all=1'>`$smarty.const._TIMELINE`</a>"}
        {else}
         {assign var = "title" value = '<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel">'|cat:$smarty.const._HOME|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=social&op=timeline">'|cat:$smarty.const._TIMELINE|cat:'</a>'}
        {/if}
    {/if}
    {capture name = "moduleSocial"}
  <tr><td class = "moduleCell">
    {include file = 'social.tpl'}
                </td>
            </tr>
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
        {assign var = "title" value = '<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel">'|cat:$smarty.const._HOME|cat:'</a>'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd">'|cat:$smarty.const._ORGANIZATION|cat:'</a>'}
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
            {assign var = "title" value = '<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel">'|cat:$smarty.const._HOME|cat:'</a>'}
            {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd">'|cat:$smarty.const._ORGANIZATION|cat:'</a>'}
            {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd&op=reports">'|cat:$smarty.const._SEARCHEMPLOYEE|cat:'</a>'}
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
<div id = "bookmarks_div_code" style = "display:none">
{capture name = "t_bookmarks_code"}
 <div id = "bookmarks_div"></div>
{/capture}
{eF_template_printBlock title = $smarty.const._SHOWBOOKMARKS data = $smarty.capture.t_bookmarks_code image = "32x32/bookmark.png"}
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
{capture name = "center_code"}
 {if $smarty.get.message}{eF_template_printMessageBlock content = $smarty.get.message type = $smarty.get.message_type}{/if}
 {if $T_MESSAGE}{eF_template_printMessageBlock content = $T_MESSAGE type = $T_MESSAGE_TYPE}{/if}
 {if $T_SEARCH_MESSAGE || $smarty.get.search_message}
     {if $smarty.get.search_message}{assign var = T_SEARCH_MESSAGE value = $smarty.get.search_message}{/if}
  {eF_template_printMessageBlock content = $T_SEARCH_MESSAGE type = $T_MESSAGE_TYPE}
 {/if}
 <table class = "centerTable">
  {$smarty.capture.moduleControlPanel}
  {$smarty.capture.moduleProjects}
  {$smarty.capture.moduleLessonInformation}
  {$smarty.capture.moduleNewsPage}
  {$smarty.capture.moduleComments}
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
  {$smarty.capture.moduleShowUnit}
  {$smarty.capture.moduleLessonPlan}
  {$smarty.capture.importedModule}
  {$smarty.capture.moduleHCD}
  {$smarty.capture.moduleNewUser}
  {$smarty.capture.moduleEmail}
  {$smarty.capture.moduleUsers}
  {$smarty.capture.mylessonsModule}
  {$smarty.capture.moduleSocial}
  {$smarty.capture.moduleFileManager}
  {$smarty.capture.moduleMessagesPage}
  {$smarty.capture.moduleForum}
  {$smarty.capture.moduleProgress}
  {$smarty.capture.moduleLandingPage}
 </table>
{/capture}
{if !$T_LAYOUT_CLASS}{assign var = "layoutClass" value = "centerFull"}{else}{assign var = "layoutClass" value = $T_LAYOUT_CLASS}{/if}
{capture name = "left_code"}
 <table class = "centerTable">
  {$smarty.capture.moduleSideOperations}
 </table>
{/capture}
{capture name = "right_code"}
 <table class = "centerTable">
  {$smarty.capture.moduleSideOperations}
 </table>
{/capture}
{capture name = "t_path_additional_code"}
 <span id = "tab_handles" class = "headerText" {if (!$T_CURRENT_LESSON->options.show_horizontal_bar && $_student_) || $smarty.cookies.horizontalSideBar == 'hidden'}style = "float:right"{/if}>
 {if $smarty.session.s_lessons_ID != '' && !$T_CONFIGURATION.disable_bookmarks && $T_CURRENT_LESSON->options.bookmarking}
  <img class = "ajaxHandle" src = "images/16x16/bookmark.png" alt = "{$smarty.const._SHOWBOOKMARKS}" title = "{$smarty.const._SHOWBOOKMARKS}" onclick = "getBookmarks(this);"/>
 {/if}
 {if $T_CTG == 'content'}
  {if $T_UNIT.options.maximize_viewport !=1}
      <img class = "ajaxHandle" src = "images/16x16/navigate_{if (!$T_CURRENT_LESSON->options.show_right_bar && $_student_) || $smarty.cookies.rightSideBar == 'hidden'}left{else}right{/if}.png" alt = "{$smarty.const._TOGGLESIDEBAR}" title = "{$smarty.const._TOGGLESIDEBAR}" onclick = "toggleRightSidebar(this, true)"/>
  {/if}
  {if $T_HORIZONTAL_BAR == 1}
   <img class = "ajaxHandle" src = "images/16x16/navigate_{if (!$T_CURRENT_LESSON->options.show_horizontal_bar && $_student_) || $smarty.cookies.horizontalSideBar == 'hidden'}down{else}up{/if}.png" alt = "{$smarty.const._TOGGLESIDEBAR}" title = "{$smarty.const._TOGGLESIDEBAR}" onclick = "toggleHorizontalSidebar(this, true)"/>
  {/if}
 {/if}
 </span>
{/capture}
{include file = "includes/common_layout.tpl"}
{/strip}
{*-----------------------------End of Part 3: Display table-------------------------------------------------*}
{*-----------------------------Part 4: Finalization data etc-------------------------------------------------*}
{include file = "includes/closing.tpl"}
<script>
{if $T_CTG == 'content'} {*Only for student, because in the case of 'content' the ctg is not the same with the menu option (which is either 'theory' or 'exercise')*}
    var ctg = '{$smarty.get.type}';
{else}
    var ctg = '{$T_CTG}';
{/if}
{if $T_NO_HORIZONTAL_MENU && $T_CTG != 'content' && $T_CTG != 'tests' && !$T_POPUP_MODE}
 showLeftSidebar();
{/if}
{* Making the previously loaded current lesson options appear again *}
{if $T_SHOW_LOADED_LESSON_OPTIONS}
if (top.sideframe && top.sideframe.hideAllLessonGeneral)
    top.sideframe.hideAllLessonGeneral();
{/if}
{* Code for changing the sideframe - reloading it if necessary and selecting the right link *}
{if (isset($T_CHANGE_LESSON) || isset($T_REFRESH_SIDE))}
    {if isset($T_PERSONAL_CTG)}
        {if $smarty.session.s_lessons_ID != ''}
         if (top.sideframe) top.sideframe.location = "new_sidebar.php?sbctg=personal&new_lesson_id={$smarty.session.s_lessons_ID}";
        {else}
         if (top.sideframe) top.sideframe.location = "new_sidebar.php?sbctg=personal";
        {/if}
    {else}
     {if isset($T_SPECIFIC_LESSON_CTG)}
      if (top.sideframe) top.sideframe.location = "new_sidebar.php?ctg=lessons&new_lesson_id={$smarty.session.s_lessons_ID}&sbctg={$T_SPECIFIC_LESSON_CTG}";
        {else}
         if (top.sideframe) top.sideframe.location = "new_sidebar.php?ctg=lessons&new_lesson_id={$smarty.session.s_lessons_ID}";
        {/if}
    {/if}
{/if}
</script>
</body>
</html>
