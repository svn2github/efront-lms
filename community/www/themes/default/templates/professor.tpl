{include file = "includes/header.tpl"}
{if $smarty.session.s_lessons_ID}
 {assign var=lessonName value=$T_CURRENT_LESSON->lesson.name}
 {if $T_NO_HORIZONTAL_MENU}{assign var = "title_onclick" value = "top.sideframe.hideAllLessonSpecific();"}{/if}
  {assign var = "title" value = "<a class = 'titleLink' title = '`$smarty.const._HOME`' href = '`$T_HOME_LINK`' onclick = '`$title_onclick`'>`$smarty.const._HOME`</a>"}
  {if isset($T_CURRENT_COURSE_NAME)}
  {*We use &nbsp;&raquo;&nbsp; for text in title path but &nbsp;&rarr;&nbsp; for onmouseover info in order to preserve eF_formatTitlePath*}
   {assign var = "titleCourse" value = $T_CURRENT_COURSE_NAME|replace:"&nbsp;&raquo;&nbsp;":"&nbsp;&rarr;&nbsp;"}
   {assign var = "title" value = "`$title`<span>&nbsp;&raquo;&nbsp;</span><a class = 'titleLink' title = '`$titleCourse`' href ='`$smarty.server.PHP_SELF`?ctg=lessons&course=`$T_CURRENT_COURSE_ID`&op=course_info'>`$T_CURRENT_COURSE_NAME`</a>"}
  {/if}
  {if $T_CURRENT_LESSON}
   {assign var = "title" value = "`$title`<span>&nbsp;&raquo;&nbsp;</span><a class = 'titleLink' title = '`$T_CURRENT_CATEGORY_PATH`&nbsp;&rarr;&nbsp;`$titleCourse`&nbsp;&rarr;&nbsp;`$T_CURRENT_LESSON->lesson.name`' href ='`$smarty.server.PHP_SELF`?ctg=control_panel'>`$lessonName`</a>"}
  {/if}
{else}
 {assign var = "title" value = "<a class = 'titleLink' title = '`$smarty.const._HOME`' href = '`$T_HOME_LINK`'>`$smarty.const._HOME`</a>"}
{/if}

{* Making the previously loaded current lesson options appear again *}
<script>
{* Code for changing the sideframe - reloading it if necessary and selecting the right link *}
{if (!isset($T_THEME_SETTINGS->options.sidebar_interface) || $T_THEME_SETTINGS->options.sidebar_interface == 0)}
 {if (isset($T_CHANGE_LESSON) || isset($T_REFRESH_SIDE))}
  {if isset($T_PERSONAL_CTG)}
   {if isset($smarty.session.s_lessons_ID)}
    top.sideframe.location = "new_sidebar.php?sbctg=personal&new_lesson_id={$smarty.session.s_lessons_ID}";
   {else}
    top.sideframe.location = "new_sidebar.php?sbctg=personal";
   {/if}
  {else}
   {if $T_OP == "import_lesson"}
    top.sideframe.location = "new_sidebar.php?ctg=lessons&new_lesson_id={$smarty.session.s_lessons_ID}&sbctg=settings";
   {else}
    {if isset($T_SPECIFIC_LESSON_CTG)}
     top.sideframe.location = "new_sidebar.php?ctg=lessons&new_lesson_id={$smarty.session.s_lessons_ID}&sbctg={$T_SPECIFIC_LESSON_CTG}";
       {else}
        top.sideframe.location = "new_sidebar.php?ctg=lessons&new_lesson_id={$smarty.session.s_lessons_ID}";
       {/if}
   {/if}
  {/if}
 {/if}

 {* Making the previously loaded current lesson options appear again *}
 {if $T_SHOW_LOADED_LESSON_OPTIONS}
 if (top.sideframe)
  top.sideframe.hideAllLessonGeneral();
 {/if}
{/if}

</script>



<script type="text/javascript">

{*
{if ($smarty.const.G_VERSIONTYPE != 'enterprise' || $T_CTG != 'users' || $smarty.get.print != 1)}
 {if (isset($T_CHANGE_LESSON) || isset($T_REFRESH_SIDE) || isset($smarty.get.refresh_side))}
  if (top.sideframe)
   top.sideframe.location = "new_sidebar.php?ctg={$T_CTG}";
 {elseif !$T_POPUP_MODE && !$smarty.get.popup}
  * Patch for solving reports problem: reports ~ ctg=module_hcd&op=reports but all ctg=module_hcd should link elsewhere *
  {if ($smarty.const.G_VERSIONTYPE == 'enterprise' && ($T_OP == "reports") && ($T_CTG == "module_hcd") && ($smarty.session.employee_type == $smarty.const._SUPERVISOR))}
  if (top.sideframe)
   top.sideframe.changeTDcolor('reports');
  {else}
  if (top.sideframe)
   top.sideframe.changeTDcolor('{$T_CTG}');
  {/if}

 {/if}
{/if}
*}

{* Used to distinguish forms in javascript functions called by smarty - Needed for checkall*}




{* The following code checks whether the sideframe is Loaded, by checking the existence of an element defined at the end of the page *}
{* If so, then the changeTDcolor function will be called from here, otherwise the sideframe will reload and the changeTDcolor function *}
{* will be called internally *}
{literal}
if (top.sideframe && top.sideframe.document.getElementById('hasLoaded')) {
{/literal}
   {if !$T_POPUP_MODE && !$smarty.get.popup}
    if (top.sideframe)
    {if $T_CTG == 'personal' && isset($smarty.get.tab) && $smarty.get.tab == 'file_record'}
     top.sideframe.changeTDcolor('file_manager');
    {elseif $T_CTG == 'control_panel'}
     top.sideframe.changeTDcolor('lesson_main');
    {elseif $T_CTG == 'content' && isset($smarty.get.type) && $smarty.get.type == 'theory'}
     top.sideframe.changeTDcolor('theory');
    {elseif $T_CTG == 'tests'}
     top.sideframe.changeTDcolor('tests');
    {elseif $T_CTG == 'projects'}
     top.sideframe.changeTDcolor('exercises');
    {elseif $T_CTG == 'glossary'}
     top.sideframe.changeTDcolor('glossary');
    {elseif $T_CTG == 'content' && $T_OP == 'file_manager'}
     top.sideframe.changeTDcolor('file_manager');
    {elseif $T_CTG == 'users' && $smarty.session.employee_type == $smarty.const._SUPERVISOR}
     top.sideframe.changeTDcolor('employees');
    {elseif ($T_CTG == "module_hcd")}
   {if ($T_OP == "reports")}
    top.sideframe.changeTDcolor('search_employee');
   {elseif isset($T_OP) && $T_OP != ''}
    top.sideframe.changeTDcolor('{$T_OP}');
   {else}
    top.sideframe.changeTDcolor('hcd_control_panel');
   {/if}
    {elseif $T_CTG == 'social'}
   {if $T_OP == 'people'}
    top.sideframe.changeTDcolor('people');
   {elseif $T_OP == 'timeline'}
    {if isset($smarty.get.lessons_ID)}
     top.sideframe.changeTDcolor('timeline');
    {else}
     top.sideframe.changeTDcolor('system_timeline');
    {/if}
   {/if}
    {elseif $T_CTG == 'module'}
   top.sideframe.changeTDcolor('{$T_MODULE_HIGHLIGHT}');
    {else}
     top.sideframe.changeTDcolor('{$T_CTG}');
    {/if}
 {/if}
{literal}
} else {
{/literal}
 {if !$T_POPUP_MODE && !$smarty.get.popup}
  if (top.sideframe)
  {if $T_CTG == 'personal' && isset($smarty.get.tab) && $smarty.get.tab == 'file_record'}
   top.sideframe.location = "new_sidebar.php?sbctg=file_manager&new_lesson_id={$smarty.session.s_lessons_ID}";
  {elseif $T_CTG == 'control_panel' && isset($smarty.get.lessons_ID)}
   top.sideframe.location = "new_sidebar.php?sbctg=lesson_main&new_lesson_id={$smarty.session.s_lessons_ID}";
  {elseif $T_CTG == 'content' && isset($smarty.get.type) && $smarty.get.type == 'theory'}
   top.sideframe.location = "new_sidebar.php?sbctg=theory&new_lesson_id={$smarty.session.s_lessons_ID}";
  {elseif $T_CTG == 'tests'}
   top.sideframe.location = "new_sidebar.php?sbctg=tests&new_lesson_id={$smarty.session.s_lessons_ID}";
  {elseif $T_CTG == 'projects'}
   top.sideframe.location = "new_sidebar.php?sbctg=exercises&new_lesson_id={$smarty.session.s_lessons_ID}";
  {elseif $T_CTG == 'glossary'}
   top.sideframe.location = "new_sidebar.php?sbctg=glossary&new_lesson_id={$smarty.session.s_lessons_ID}";
    {elseif $T_CTG == 'content' && $T_OP == 'file_manager'}
   top.sideframe.location = "new_sidebar.php?sbctg=file_manager&new_lesson_id={$smarty.session.s_lessons_ID}";
  {elseif $T_CTG == 'users' && $smarty.session.employee_type == $smarty.const._SUPERVISOR}
   top.sideframe.location = "new_sidebar.php?sbctg=employees&new_lesson_id={$smarty.session.s_lessons_ID}";

  {elseif ($T_CTG == "module_hcd")}
   {if ($T_OP == "reports")}
    top.sideframe.location = "new_sidebar.php?sbctg=reports&new_lesson_id={$smarty.session.s_lessons_ID}";
   {elseif isset($T_OP) && $T_OP != ''}
    top.sideframe.location = "new_sidebar.php?sbctg=placements{$T_OP}&new_lesson_id={$smarty.session.s_lessons_ID}";
   {else}
    top.sideframe.location = "new_sidebar.php?sbctg=hcd_control_panel&new_lesson_id={$smarty.session.s_lessons_ID}";
   {/if}

    {elseif $T_CTG == 'social'}
   {if $T_OP == 'people'}
    top.sideframe.location = "new_sidebar.php?sbctg=people&new_lesson_id={$smarty.session.s_lessons_ID}";
   {elseif $T_OP == 'timeline'}
    {if isset($smarty.get.lessons_ID)}
     top.sideframe.location = "new_sidebar.php?sbctg=timeline&new_lesson_id={$smarty.session.s_lessons_ID}";
    {else}
     top.sideframe.location = "new_sidebar.php?sbctg=system_timeline&new_lesson_id={$smarty.session.s_lessons_ID}";
    {/if}
   {/if}
  {elseif $T_CTG == 'module'}
   top.sideframe.location = "new_sidebar.php?sbctg={$T_MODULE_HIGHLIGHT}&new_lesson_id={$smarty.session.s_lessons_ID}";
  {else}
   top.sideframe.location = "new_sidebar.php?sbctg={$T_CTG}&new_lesson_id={$smarty.session.s_lessons_ID}";
  {/if}
 {/if}
{literal}
}
{/literal}


</script>



{*-------------------------------End of Part 1: initialization etc-----------------------------------------------*}
<script>var point2 = new Date().getTime();</script>
{*-------------------------------Part 2: Modules List ---------------------------------------------*}
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
{if (isset($T_CTG) && $T_CTG == 'import')}
 {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink'  href = '`$smarty.server.PHP_SELF`?ctg=import'>`$smarty.const._IMPORT`</a>"}
 {include file = "includes/import.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'scorm')}
 {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink'  href = '`$smarty.server.PHP_SELF`?ctg=scorm'>`$smarty.const._SCORMOPTIONS`</a>"}
 {if $smarty.get.scorm_review}
  {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink'  href = '`$smarty.server.PHP_SELF`?ctg=scorm&scorm_review=1'>`$smarty.const._SCORMREVIEW`</a>"}
 {elseif $smarty.get.scorm_import}
  {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink'  href = '`$smarty.server.PHP_SELF`?ctg=scorm&scorm_import=1'>`$smarty.const._SCORMIMPORT`</a>"}
 {elseif $smarty.get.scorm_export}
  {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink'  href = '`$smarty.server.PHP_SELF`?ctg=scorm&scorm_export=1'>`$smarty.const._SCORMEXPORT`</a>"}
 {/if}
 {include file = "includes/scorm.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'ims')}
 {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink'  href = '`$smarty.server.PHP_SELF`?ctg=ims'>`$smarty.const._IMSOPTIONS`</a>"}
 {if $smarty.get.scorm_review}
  {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink'  href = '`$smarty.server.PHP_SELF`?ctg=ims&scorm_review=1'>`$smarty.const._SCORMREVIEW`</a>"}
 {elseif $smarty.get.scorm_import}
  {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink'  href = '`$smarty.server.PHP_SELF`?ctg=ims&scorm_import=1'>`$smarty.const._SCORMIMPORT`</a>"}
 {elseif $smarty.get.scorm_export}
  {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink'  href = '`$smarty.server.PHP_SELF`?ctg=ims&scorm_export=1'>`$smarty.const._SCORMEXPORT`</a>"}
 {/if}
 {include file = "includes/ims.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'lesson_information')}
 {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=lesson_information">'|cat:$smarty.const._LESSONINFORMATION|cat:'</a>'}
 {if $smarty.get.edit_info}
  {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=lesson_information&edit_info=1">'|cat:$smarty.const._EDITLESSONINFORMATION|cat:'</a>'}
 {/if}
 {include file = "includes/lesson_information.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'progress')}
 {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=progress">'|cat:$smarty.const._PROGRESS|cat:'</a>'}
 {if $smarty.get.edit_user}
  {assign var = "formatted_login" value = $T_USER_LESSONS_INFO.users_LOGIN|formatLogin}
  {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?ctg=progress&edit_user=`$smarty.get.edit_user`'>`$smarty.const._PROGRESSFORUSER`: `$formatted_login`</a>"}
 {/if}
 {include file = "includes/progress.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'news')}
 {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=news">'|cat:$smarty.const._ANNOUNCEMENTS|cat:'</a>'}

   {include file = "includes/news.tpl"}
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
   {elseif $T_SCORM_2004_TITLE}
    {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = \"inactiveLink\" href = \"javascript:void(0)\" title = \"`$T_PARENT_LIST[parents_list].name`\">`$truncated_name`</a>"}
   {else}
    {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = \"inactiveLink\" href = \"javascript:void(0)\" title = \"`$T_PARENT_LIST[parents_list].name` (`$smarty.const._EMPTYUNIT`)\">`$truncated_name`</a>"}
   {/if}
  {/section}
 {/if}

 {include file = "includes/common_content.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'metadata')}
 {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=content&view_unit='|cat:$smarty.get.view_unit|cat:'">'|cat:$T_CURRENT_UNIT.name|cat:'</a>&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=content&op=metadata&view_unit='|cat:$smarty.get.view_unit|cat:'">'|cat:$smarty.const._CONTENTMETADATA|cat:'</a>'}
 {include file = "includes/metadata.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'copy')}
 {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=copy">'|cat:$smarty.const._COPYFROMANOTHERLESSON|cat:'</a>'}
 {include file = "includes/copy.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'order')}
 {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=order">'|cat:$smarty.const._LINEARCONTENT|cat:'</a>'}
 {include file = "includes/order.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'file_manager')}
 {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=file_manager">'|cat:$smarty.const._FILES|cat:'</a>'}
 {include file = "includes/file_manager.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'scheduling')}
 {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=scheduling">'|cat:$smarty.const._SCHEDULING|cat:'</a>'}
 {include file = "includes/scheduling.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'projects')}
 {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=projects">'|cat:$smarty.const._PROJECTS|cat:'</a>'}
 {if $smarty.get.add_project}
  {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=projects&add_project=1">'|cat:$smarty.const._ADDPROJECT|cat:'</a>'}
 {elseif $smarty.get.edit_project}
  {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=projects&edit_project='|cat:$smarty.get.edit_project|cat:'">'|cat:$smarty.const._EDITPROJECT|cat:' <span class= "innerTableName">&quot;'|cat:$T_CURRENT_PROJECT->project.title|cat:'&quot;</span></a>'}
 {elseif $smarty.get.project_results}
  {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=projects&project_results='|cat:$smarty.get.project_results|cat:'">'|cat:$smarty.const._RESULTSFORPROJECT|cat:' &quot;'|cat:$T_CURRENT_PROJECT->project.title|cat:'&quot;</a>'}
 {elseif $smarty.get.view_project}
  {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=projects&view_project='|cat:$smarty.get.view_project|cat:'">'|cat:$smarty.const._VIEWPROJECT|cat:' &quot;'|cat:$T_CURRENT_PROJECT->project.title|cat:'&quot;</a>'}
 {/if}
 {include file = "includes/projects.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'rules')}
 {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=rules">'|cat:$smarty.const._LESSONRULES|cat:'</a>'}
 {if $smarty.get.add_rule}
  {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=rules&add_rule=1" >'|cat:$smarty.const._ADDRULE|cat:'</a>'}
 {elseif $smarty.get.edit_rule}
  {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=rules&edit_rule='|cat:$smarty.get.edit_rule|cat:'" >'|cat:$smarty.const._RULEPROPERTIES|cat:'</a>'}
 {elseif $smarty.get.add_condition}
  {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=rules&tab=conditions&add_condition=1">'|cat:$smarty.const._ADDCONDITION|cat:'</a>'}
 {elseif $smarty.get.edit_condition}
  {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=rules&tab=conditions&edit_condition='|cat:$smarty.get.edit_condition|cat:'">'|cat:$smarty.const._EDITCONDITION|cat:'</a>'}
 {/if}

 {include file = "includes/rules.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'glossary')}
 {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=glossary">'|cat:$smarty.const._GLOSSARY|cat:'</a>'}
 {include file = "includes/glossary.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'lessons')}

 {if $title == ""}
  {assign var = "title" value = '<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=lessons">'|cat:$smarty.const._MYCOURSES|cat:'</a>'}
 {/if}
 {if $T_OP == 'tests'}
  {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=lessons&op=tests">'|cat:$smarty.const._SKILLGAPTESTS|cat:'</a>'}
  {if isset($smarty.get.solve_test)}
   {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=lessons&op=tests&solve_test='|cat:$smarty.get.solve_test|cat:'">'|cat:$T_TEST_DATA->test.name|cat:'</a>'}
   {if $smarty.get.test_analysis}
    {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=content&view_unit=`$smarty.get.view_unit`&test_analysis=1'>`$smarty.const._TESTANALYSISFORTEST` &quot;`$T_TEST_DATA->test.name`&quot;</a>"}
   {/if}
  {/if}
 {else}

  {if $smarty.get.course}
   {assign var ="title" value = "<a class = 'titleLink' title = '`$smarty.const._CHANGELESSON`' href = '`$smarty.server.PHP_SELF`?ctg=lessons' onclick = '`$title_onclick`'>`$smarty.const._MYCOURSES`</a><span>&nbsp;&raquo;&nbsp;</span>"}
   {if $T_OP == course_info}
    {assign var = 'title' value = "`$title`<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?`$T_BASE_URL`&op=course_info'>`$smarty.const._INFORMATIONFORCOURSE` &quot;`$T_CURRENT_COURSE->course.name`&quot;</a>"}
   {elseif $T_OP == course_certificates}
    {assign var = 'title' value = "`$title`<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?`$T_BASE_URL`&op=course_certificates'>`$smarty.const._COMPLETION` &quot;`$T_CURRENT_COURSE->course.name`&quot;</a>"}
   {elseif $T_OP == format_certificate}
    {assign var = 'title' value = "`$title`<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?`$T_BASE_URL`&op=format_certificate'>`$smarty.const._FORMATCERTIFICATEFORCOURSE` &quot;`$T_CURRENT_COURSE->course.name`&quot;</a>"}
   {elseif $T_OP == format_certificate_docx}
    {assign var = 'title' value = "`$title`<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?`$T_BASE_URL`&op=format_certificate_docx'>`$smarty.const._FORMATCERTIFICATEFORCOURSE` &quot;`$T_CURRENT_COURSE->course.name`&quot;</a>"}
   {elseif $T_OP == course_rules}
    {assign var = 'title' value = "`$title`<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?`$T_BASE_URL`&op=course_rules'>`$smarty.const._RULESFORCOURSE` &quot;`$T_CURRENT_COURSE->course.name`&quot;</a>"}
   {elseif $T_OP == course_order}
    {assign var = 'title' value = "`$title`<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?`$T_BASE_URL`&op=course_order'>`$smarty.const._ORDERFORCOURSE` &quot;`$T_CURRENT_COURSE->course.name`&quot;</a>"}
   {elseif $T_OP == course_scheduling}
    {assign var = 'title' value = "`$title`<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?`$T_BASE_URL`&op=course_scheduling'>`$smarty.const._SCHEDULINGFORCOURSE` &quot;`$T_CURRENT_COURSE->course.name`&quot;</a>"}
   {elseif $T_OP == export_course}
    {assign var = 'title' value = "`$title`<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?`$T_BASE_URL`&op=export_course'>`$smarty.const._EXPORTCOURSE` &quot;`$T_CURRENT_COURSE->course.name`&quot;</a>"}
   {elseif $T_OP == import_course}
    {assign var = 'title' value = "`$title`<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?`$T_BASE_URL`&op=import_course'>`$smarty.const._IMPORTCOURSE` &quot;`$T_CURRENT_COURSE->course.name`&quot;</a>"}
   {elseif $T_MODULE_TABPAGE}
    {assign var = 'title' value = "`$title`<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?`$T_BASE_URL`&op=`$T_MODULE_TABPAGE.tab_page`'>`$T_MODULE_TABPAGE.title`</a>"}
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
{if (isset($T_CTG) && $T_CTG == 'survey')}

  {if (!isset($smarty.get.screen_survey) && !isset($smarty.get.action) && $smarty.get.screen_survey != '2')}
   {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey">'|cat:$smarty.const._SURVEYS|cat:'</a>'}
   {if (isset($smarty.get.t_enter_create) && $smarty.get.t_enter_create == '1')}
    {assign var = "T_MESSAGE" value = $smarty.const._SURVEYADDEDSUCCESSFULLY}
    {assign var = "T_MESSAGE_TYPE" value='success'}
   {elseif (isset($T_ENTER_CREATE) && $T_ENTER_CREATE == '-1')}
    {assign var = "T_MESSAGE" value = $smarty.const._FAILEDTOADDSURVEY}
   {/if}

   {if (isset($smarty.get.t_enter_delete) && $smarty.get.t_enter_delete == '1')}
    {assign var = "T_MESSAGE" value = $smarty.const._SURVEYDELETEDSUCCESSFULLY}
    {assign var = "T_MESSAGE_TYPE" value='success'}
   {elseif (isset($smarty.get.t_enter_delete) && $smarty.get.t_enter_delete == '-1')}
    {assign var = "T_MESSAGE" value = $smarty.const._SURVEYFAILEDTOBEDELETED}
   {else}
   {/if}
   {if (isset($smarty.get.t_enter_update) && $smarty.get.t_enter_update == '1')}
       {assign var = "T_MESSAGE" value = $smarty.const._SURVEYDATAUPDATEDSUCCESSFULLY}
       {assign var = "T_MESSAGE_TYPE" value='success'}
   {elseif (isset($smarty.get.t_enter_update) && $smarty.get.t_enter_update == '-1')}
    {assign var = "T_MESSAGE" value = $smarty.const._SURVEYDATAFAILEDTOBEUPDATED}
   {else}
   {/if}
   {if (isset($smarty.get.published) && $smarty.get.published == '1')}
    {assign var = "T_MESSAGE" value = $smarty.const._SURVEYPUBLISHEDSUCCESSFULLY}
    {assign var = "T_MESSAGE_TYPE" value='success'}
   {elseif (isset($smarty.get.published) && $smarty.get.published == '-1')}
    {assign var = "T_MESSAGE" value = $smarty.const._SURVEYFAILEDTOBEPUBLISHED}
   {else}
   {/if}
   {if (isset($smarty.get.t_activate) && $smarty.get.t_activate == '-1')}
    {assign var = "T_MESSAGE" value = $smarty.const._SURVEYDISABLEDSUCCESSFULLY}
    {assign var = "T_MESSAGE_TYPE" value='success'}
   {elseif (isset($smarty.get.t_activate) && $smarty.get.t_activate == '1')}
    {assign var = "T_MESSAGE" value = $smarty.const._SURVEYENABLEDSUCCESSFULLY}
    {assign var = "T_MESSAGE_TYPE" value='success'}
   {else}
   {/if}
   {if ($smarty.get.survey_user == 'false') }
    {assign var = "T_MESSAGE" value = $smarty.const._AUSERISALREADYATTHESURVEY}
   {/if}
  {/if}

 {if (isset($smarty.get.screen_survey) && !isset($smarty.get.action) && $smarty.get.screen_survey == '2')}
  {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo;&nbsp;'|cat:'<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&action=create_survey&survey_action=update&screen=1&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'">'|cat:$T_SURVEYNAME|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'&screen_survey=2">'|cat:$smarty.const._QUESTIONS|cat:'</a>'}
  {if (isset($smarty.get.t_question_added) && $smarty.get.t_question_added == '1')}
     {assign var = "T_MESSAGE" value = $smarty.const._QUESTIONADDEDSUCCESSFULLY}
     {assign var = "T_MESSAGE_TYPE" value='success'}
  {elseif (isset($smarty.get.t_question_added) && $smarty.get.t_question_added == '-1')}
   {assign var = "T_MESSAGE" value = $smarty.const._QUESTIONFAILEDTOBEADDED}
  {else}
  {/if}
  {if (isset($smarty.get.question_added) && $smarty.get.question_added == '1')}
   {assign var = "T_MESSAGE" value = $smarty.const._QUESTIONADDEDSUCCESSFULLY}
   {assign var = "T_MESSAGE_TYPE" value='success'}
  {elseif (isset($smarty.get.question_added) && $smarty.get.question_added == '-1')}
   {assign var = "T_MESSAGE" value = $smarty.const._QUESTIONFAILEDTOBEADDED}
  {else}
  {/if}
  {if (isset($smarty.get.question_deleted) && $smarty.get.question_deleted == '1')}
   {assign var = "T_MESSAGE" value = $smarty.const._QUESTIONDELETEDSUCCESSFULLY}
   {assign var = "T_MESSAGE_TYPE" value='success'}
  {elseif (isset($smarty.get.question_deleted) && $smarty.get.question_deleted == '-1')}
   {assign var = "T_MESSAGE" value = $smarty.const._QUESTIONFAILEDTOBEDELETED}
  {else}
  {/if}
  {if (isset($smarty.get.question_swap) && $smarty.get.question_swap == '1')}
   {assign var = "T_MESSAGE" value = $smarty.const._THEQUESTIONWASSUCCESSFULLYMOVED}
   {assign var = "T_MESSAGE_TYPE" value='success'}
  {elseif (isset($smarty.get.question_swap) && $smarty.get.question_swap == '-1')}
   {assign var = "T_MESSAGE" value = $smarty.const._THEQUESTIONFAILEDTOBEMOVED}
  {elseif (isset($smarty.get.question_swap) && $smarty.get.question_swap == '-2')}
   {assign var = "T_MESSAGE" value = $smarty.const._NOSUCHOPERATION}
  {else}
  {/if}
  {if (isset($smarty.get.question_updated) && $smarty.get.question_updated == '1') }
   {assign var = "T_MESSAGE" value = $smarty.const._QUESTIONUPDATEDSUCCESSFULLY}
   {assign var = "T_MESSAGE_TYPE" value='success'}
  {elseif (isset($smarty.get.question_updated) && $smarty.get.question_updated == '-1')}
   {assign var = "T_MESSAGE" value = $smarty.const._QUESTIONFAILEDTOBEUPDATED}
  {else}
  {/if}
 {/if}

  {if ($smarty.get.action == 'question_create')}
   {if (isset($smarty.get.question_type) && $smarty.get.question_type == '-')}
    {assign var = "T_MESSAGE" value = $smarty.const._PLEASESELECTAVALIDTYPEOFQUESTION}
   {else}
    {if ($smarty.get.question_type == 'dropdown')}
     {if ($smarty.get.question_action == 'update_question')}
      {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a  class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo;&nbsp;'|cat:'<a  class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&action=create_survey&survey_action=update&screen=1&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'">'|cat:$T_SURVEYNAME|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'&screen_survey=2">'|cat:$smarty.const._QUESTIONS|cat:'</a>&nbsp;&raquo&nbsp;<a class="titleLink" href="'|cat:$smarty.server.REQUEST_URI|cat:'">'|cat:$smarty.const._DROPDOWN|cat:'</a>'}
     {else}
      {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo;&nbsp;'|cat:'<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&action=create_survey&survey_action=update&screen=1&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'">'|cat:$T_SURVEYNAME|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'&screen_survey=2">'|cat:$smarty.const._QUESTIONS|cat:'</a>&nbsp;&raquo&nbsp;<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'&screen_survey=2&question_type=dropdown&action=question_create">'|cat:$smarty.const._DROPDOWN|cat:'</a>'}
     {/if}
    {/if}
    {if ($smarty.get.question_type == 'development')}
     {if ($smarty.get.question_action == 'update_question')}
      {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a  class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo;&nbsp;'|cat:'<a  class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&action=create_survey&survey_action=update&screen=1&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'">'|cat:$T_SURVEYNAME|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'&screen_survey=2">'|cat:$smarty.const._QUESTIONS|cat:'</a>&nbsp;&raquo&nbsp;<a class="titleLink" href="'|cat:$smarty.server.REQUEST_URI|cat:'">'|cat:$smarty.const._DEVELOPMENT|cat:'</a>'}
     {else}
      {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo;&nbsp;'|cat:'<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&action=create_survey&survey_action=update&screen=1&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'">'|cat:$T_SURVEYNAME|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'&screen_survey=2">'|cat:$smarty.const._QUESTIONS|cat:'</a>&nbsp;&raquo&nbsp;<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'&screen_survey=2&question_type=development&action=question_create">'|cat:$smarty.const._DEVELOPMENT|cat:'</a>'}
     {/if}
      {/if}
      {if ($smarty.get.question_type == 'yes_no')}
     {if ($smarty.get.question_action == 'update_question')}
      {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a  class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo;&nbsp;'|cat:'<a  class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&action=create_survey&survey_action=update&screen=1&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'">'|cat:$T_SURVEYNAME|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'&screen_survey=2">'|cat:$smarty.const._QUESTIONS|cat:'</a>&nbsp;&raquo&nbsp;<a class="titleLink" href="'|cat:$smarty.server.REQUEST_URI|cat:'">'|cat:$smarty.const._YES_NO|cat:'</a>'}
     {else}
      {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a  class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo;&nbsp;'|cat:'<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&action=create_survey&survey_action=update&screen=1&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'">'|cat:$T_SURVEYNAME|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'&screen_survey=2">'|cat:$smarty.const._QUESTIONS|cat:'</a>&nbsp;&raquo&nbsp;<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'&screen_survey=2&question_type=yes_no&action=question_create">'|cat:$smarty.const._YES_NO|cat:'</a>'}
     {/if}
      {/if}
      {if ($smarty.get.question_type == 'multiple_one')}
     {if ($smarty.get.question_action == 'update_question')}
      {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a  class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo;&nbsp;'|cat:'<a  class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&action=create_survey&survey_action=update&screen=1&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'">'|cat:$T_SURVEYNAME|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'&screen_survey=2">'|cat:$smarty.const._QUESTIONS|cat:'</a>&nbsp;&raquo&nbsp;<a class="titleLink" href="'|cat:$smarty.server.REQUEST_URI|cat:'">'|cat:$smarty.const._SURVEYQUESTIONMULTIPLEONE|cat:'</a>'}
     {else}
      {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo;&nbsp;'|cat:'<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&action=create_survey&survey_action=update&screen=1&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'">'|cat:$T_SURVEYNAME|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'&screen_survey=2">'|cat:$smarty.const._QUESTIONS|cat:'</a>&nbsp;&raquo&nbsp;<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'&screen_survey=2&question_type=multiple_one&action=question_create">'|cat:$smarty.const._SURVEYQUESTIONMULTIPLEONE|cat:'</a>'}
     {/if}
      {/if}
      {if ($smarty.get.question_type == 'multiple_many')}
     {if ($smarty.get.question_action == 'update_question')}
      {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a  class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo;&nbsp;'|cat:'<a  class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&action=create_survey&survey_action=update&screen=1&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'">'|cat:$T_SURVEYNAME|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'&screen_survey=2">'|cat:$smarty.const._QUESTIONS|cat:'</a>&nbsp;&raquo&nbsp;<a class="titleLink" href="'|cat:$smarty.server.REQUEST_URI|cat:'">'|cat:$smarty.const._SURVEYQUESTIONMULTIPLEMANY|cat:'</a>'}
     {else}
      {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a  class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo;&nbsp;'|cat:'<a  class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&action=create_survey&survey_action=update&screen=1&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'">'|cat:$T_SURVEYNAME|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'&screen_survey=2">'|cat:$smarty.const._QUESTIONS|cat:'</a>&nbsp;&raquo&nbsp;<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'&screen_survey=2&question_type=multiple_many&action=question_create">'|cat:$smarty.const._SURVEYQUESTIONMULTIPLEMANY|cat:'</a>'}
     {/if}
      {/if}
   {/if}
  {/if}

  {if ($smarty.get.action == 'create_survey' && $smarty.get.screen == '1')}
   {if ( $smarty.get.survey_action == 'create' ) }
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&action=create_survey&survey_action=create&screen=1&lessons_ID='|cat:$smarty.get.lessons_ID|cat:'">'|cat:$smarty.const._CREATESURVEY|cat:'</a>'}
   {/if}
   {if ( $smarty.get.survey_action == 'update' )}
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a  class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&action=create_survey&survey_action=update&screen=1&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'">'|cat:$smarty.const._EDITSURVEY|cat:'</a>'}
   {/if}
  {/if}

  {if ($smarty.get.action == 'preview') }
   {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo;&nbsp;'|cat:'<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&action=create_survey&survey_action=update&screen=1&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'">'|cat:$T_SURVEYNAME|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href="'|cat:$smarty.server.REQUEST_URI|cat:'">'|cat:$smarty.const._PREVIEW|cat:'</a>'}
  {/if}

  {if (isset($smarty.get.action) && $smarty.get.action == 'view_users') }
   {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo;&nbsp;'|cat:'<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&action=create_survey&survey_action=update&screen=1&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'">'|cat:$T_SURVEYNAME|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href="'|cat:$smarty.server.REQUEST_URI|cat:'">'|cat:$smarty.const._DONESURVEYUSERS|cat:'</a>'}
  {/if}

  {if ( isset($smarty.get.action) && $smarty.get.action == 'survey_preview' ) }
   {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo;&nbsp;'|cat:'<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&action=create_survey&survey_action=update&screen=1&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'">'|cat:$T_SURVEYNAME|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href="'|cat:$smarty.server.REQUEST_URI|cat:'">'|cat:$smarty.const._SURVEYPREVIEWFORUSER|cat:$smarty.get.user|cat:'</a>'}
  {/if}

  {if (isset($smarty.get.action) && $smarty.get.action == 'statistics') }
   {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo;&nbsp;'|cat:'<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&action=create_survey&survey_action=update&screen=1&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'">'|cat:$T_SURVEYNAME|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href="'|cat:$smarty.server.REQUEST_URI|cat:'">'|cat:$smarty.const._STATISTICS|cat:'</a>'}
  {/if}
  {if (isset($smarty.get.action) && $smarty.get.action == 'publish') }
   {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo;&nbsp;'|cat:'<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&action=create_survey&survey_action=update&screen=1&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'">'|cat:$T_SURVEYNAME|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'&action=publish">'|cat:$smarty.const._PUBLISH|cat:'</a>'}
  {/if}
 {include file = "professor/survey.tpl"} {/if}

{if (isset($T_CTG) && $T_CTG == 'tests')} {*moduleTests: Print the Tests page*}
      {capture name = "moduleTests"}
       {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=tests">'|cat:$smarty.const._TESTS|cat:'</a>'}
       {if $smarty.get.edit_test}
        {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=tests&edit_test=`$smarty.get.edit_test`'>`$smarty.const._EDITTEST` <span class='innerTableName'>&quot;`$T_CURRENT_TEST->test.name`&quot;</span></a>"}
       {elseif $smarty.get.add_test}
        {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=tests&add_test=1'>`$smarty.const._ADDTEST`</a>"}
       {elseif $smarty.get.edit_question}
        {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=tests&edit_question=`$smarty.get.edit_question`&question_type=`$smarty.get.question_type`'>`$smarty.const._EDITQUESTION`</a>"}
       {elseif $smarty.get.add_question}
        {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=tests&add_question=1&question_type=`$smarty.get.question_type`'>`$smarty.const._ADDQUESTION`</a>"}
       {elseif $smarty.get.test_results}
        {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=tests&test_results=`$smarty.get.test_results`'>&quot;`$T_TEST->test.name`&quot; `$smarty.const._RESULTS`</a>"}
       {elseif $smarty.get.view_unit}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?view_unit='|cat:$smarty.get.view_unit|cat:'">'|cat:$smarty.const._PREVIEW|cat:'</a>'}
       {elseif $smarty.get.show_solved_test}
        {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=tests&test_results=`$T_TEST_DATA->completedTest.testsId`'>`$smarty.const._TESTRESULTS`</a>"}
        {if !$smarty.get.test_analysis}
         {assign var = "formatted_login" value = $T_TEST_DATA->completedTest.login|formatLogin}
         {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=tests&show_solved_test=`$T_TEST_DATA->completedTest.id`'>`$smarty.const._VIEWSOLVEDTEST`: &quot;`$T_TEST_DATA->test.name`&quot; `$smarty.const._BYUSER`: `$formatted_login`</a>"}
        {else}
         {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=tests&show_solved_test='|cat:$smarty.get.show_solved_test|cat:'&test_analysis='|cat:$smarty.get.test_analysis|cat:'&user='|cat:$smarty.get.user|cat:'">'|cat:$smarty.const._USERRESULTS|cat:'</a>'}
        {/if}
       {/if}
       <tr><td class = "moduleCell">

        {include file = "includes/module_tests.tpl"}
       </td></tr>
      {/capture}
{/if}
{if (isset($T_CTG) && $T_CTG == 'feedback')} {*moduleFeedback: Print the Feedback page*}
      {capture name = "moduleFeedback"}
       {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=feedback">'|cat:$smarty.const._FEEDBACK|cat:'</a>'}
       {if $smarty.get.edit_test}
        {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=feedback&edit_test=`$smarty.get.edit_test`'>`$smarty.const._EDITFEEDBACK` <span class='innerTableName'>&quot;`$T_CURRENT_TEST->test.name`&quot;</span></a>"}
       {elseif $smarty.get.add_test}
        {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=feedback&add_test=1'>`$smarty.const._ADDFEEDBACSFEEDBACK`</a>"}
       {elseif $smarty.get.edit_question}
        {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=feedback&edit_question=`$smarty.get.edit_question`&question_type=`$smarty.get.question_type`'>`$smarty.const._EDITQUESTION`</a>"}
       {elseif $smarty.get.add_question}
        {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=feedback&add_question=1&question_type=`$smarty.get.question_type`'>`$smarty.const._ADDQUESTION`</a>"}
       {elseif $smarty.get.test_results}
        {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=feedback&test_results=`$smarty.get.test_results`'>&quot;`$T_TEST->test.name`&quot; `$smarty.const._RESULTS`</a>"}
       {elseif $smarty.get.view_unit}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?view_unit='|cat:$smarty.get.view_unit|cat:'">'|cat:$smarty.const._PREVIEW|cat:'</a>'}
       {elseif $smarty.get.show_solved_test}
        {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=feedback&test_results=`$T_TEST_DATA->completedTest.testsId`'>`$smarty.const._FEEDBACKRESULTS`</a>"}
        {if !$smarty.get.test_analysis}
         {assign var = "formatted_login" value = $T_TEST_DATA->completedTest.login|formatLogin}
         {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=feedback&show_solved_test=`$T_TEST_DATA->completedTest.id`'>`$smarty.const._VIEWFEEDBACK`: &quot;`$T_TEST_DATA->test.name`&quot; `$smarty.const._BYUSER`: `$formatted_login`</a>"}
        {else}
         {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=feedback&show_solved_test='|cat:$smarty.get.show_solved_test|cat:'&test_analysis='|cat:$smarty.get.test_analysis|cat:'&user='|cat:$smarty.get.user|cat:'">'|cat:$smarty.const._USERRESULTS|cat:'</a>'}
        {/if}
       {/if}
       <tr><td class = "moduleCell">
        {include file = "includes/module_tests.tpl"}
       </td></tr>
      {/capture}
{/if}
{if (isset($T_CTG) && $T_CTG == 'calendar')}
 {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=calendar">'|cat:$smarty.const._CALENDAR|cat:'</a>'}
 {include file = "includes/calendar.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'settings')}
 {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?`$T_BASE_URL`'>`$smarty.const._OPTIONSFORLESSON` &quot;`$T_CURRENT_LESSON->lesson.name`&quot;</a>"}
 {if isset($T_OP) && $T_OP == reset_lesson}
  {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?`$T_BASE_URL`&op=reset_lesson'>`$smarty.const._RESTARTLESSON`</a>"}
 {elseif isset($T_OP) && $T_OP == import_lesson}
  {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?`$T_BASE_URL`&op=import_lesson'>`$smarty.const._IMPORTLESSON`</a>"}
 {elseif isset($T_OP) && $T_OP == export_lesson}
  {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?`$T_BASE_URL`&op=export_lesson'>`$smarty.const._EXPORTLESSON`</a>"}
 {elseif isset($T_OP) && $T_OP == lesson_users}
  {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?`$T_BASE_URL`&op=lesson_users'>`$smarty.const._LESSONUSERS`</a>"}
 {elseif isset($T_OP) && $T_OP == lesson_layout}
  {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?`$T_BASE_URL`&op=lesson_layout'>`$smarty.const._LAYOUT`</a>"}
 {/if}

 {capture name = "moduleLessonSettings"}
 <tr><td class = "moduleCell">
  {include file = "includes/lesson_settings.tpl"}
 </td></tr>
 {/capture}
{/if}
{if (isset($T_CTG) && $T_CTG == 'courses')}
 {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=courses'>`$smarty.const._COURSES`</a>"}
 {if $smarty.get.edit_course}
     {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=courses&edit_course='|cat:$smarty.get.edit_course|cat:'">'|cat:$smarty.const._EDITCOURSE|cat:'&nbsp;<span class="innerTableName">&quot;'|cat:$T_COURSE_FORM.name.value|cat:'&quot;</span></a>'}
 {elseif $smarty.get.add_course}
     {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=courses&add_course=1">'|cat:$smarty.const._NEWCOURSE|cat:'</a>'}
 {/if}
 <tr><td class = "moduleCell">
 {include file = "includes/professor_courses.tpl"}
 </td></tr>
{/if}
{if (isset($T_CTG) && $T_CTG == 'professor_lessons')}
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=professor_lessons">'|cat:$smarty.const._LESSONS|cat:'</a>'}
    {if $smarty.get.edit_lesson}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=professor_lessons&edit_lesson='|cat:$smarty.get.edit_lesson|cat:'">'|cat:$smarty.const._EDITLESSON|cat:' <span class = "innerTableName">&quot;'|cat:$T_LESSON_FORM.name.value|cat:'&quot;</span></a>'}
    {elseif $smarty.get.lesson_info}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=professor_lessons&lesson_info='|cat:$smarty.get.lesson_info|cat:'">'|cat:$smarty.const._INFORMATIONFORLESSON|cat:' &quot;'|cat:$T_CURRENT_LESSON->lesson.name|cat:'&quot;</a>'}
  {if $smarty.get.edit_info}
   {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?ctg=professor_lessons&lesson_info=`$smarty.get.lesson_info`&edit_info=1'>`$smarty.const._EDITLESSONINFORMATION`</a>"}
  {/if}
    {elseif $smarty.get.add_lesson}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=professor_lessons&add_lesson=1">'|cat:$smarty.const._NEWLESSON|cat:'</a>'}
    {elseif $smarty.get.lesson_settings}
            {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?`$T_BASE_URL`'>`$smarty.const._OPTIONSFORLESSON` &quot;`$T_CURRENT_LESSON->lesson.name`&quot;</a>"}
            {if isset($T_OP) && $T_OP == reset_lesson}
                {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?`$T_BASE_URL`&op=reset_lesson'>`$smarty.const._RESTARTLESSON`</a>"}
            {elseif isset($T_OP) && $T_OP == import_lesson}
                {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?`$T_BASE_URL`&op=import_lesson'>`$smarty.const._IMPORTLESSON`</a>"}
            {elseif isset($T_OP) && $T_OP == export_lesson}
                {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?`$T_BASE_URL`&op=export_lesson'>`$smarty.const._EXPORTLESSON`</a>"}
            {elseif isset($T_OP) && $T_OP == lesson_users}
                {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?`$T_BASE_URL`&op=lesson_users'>`$smarty.const._LESSONUSERS`</a>"}
            {elseif isset($T_OP) && $T_OP == lesson_layout}
                {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?`$T_BASE_URL`&op=lesson_layout'>`$smarty.const._LAYOUT`</a>"}
            {/if}
    {/if}

 <tr><td class = "moduleCell">
 {include file = "includes/professor_lessons.tpl"}
 </td></tr>
{/if}
{if $T_CTG == 'personal'}
{*modulePersonal: Print the Personal page*}
 {capture name = "modulePersonal"}
  {if $smarty.get.user != $smarty.session.s_login}
   {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=users'>`$smarty.const._USERS`</a>"}
  {/if}
  {assign var = "formatted_login" value = $T_EDITEDUSER->user.login|formatLogin}
  {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=personal&user=`$T_EDITEDUSER->user.login`'>`$formatted_login`</a>"}
  {if $T_OP == 'dashboard'}
   {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?ctg=personal&user=`$smarty.get.user`&op=dashboard'>`$smarty.const._DASHBOARD`</a>"}
  {elseif $T_OP == 'profile' && $smarty.get.add_user}
   {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?ctg=personal&user=`$T_EDITEDUSER->user.login`&op=profile&add_user=1'>`$smarty.const._NEWUSER`</a>"}
  {elseif $T_OP == 'profile' || $T_OP == 'user_groups' || $T_OP == 'mapped_accounts' || $T_OP == 'payments'}
   {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?ctg=personal&user=`$smarty.get.user`&op=profile'>`$smarty.const._ACCOUNT`</a>"}
  {elseif $T_OP == 'user_courses' || $T_OP == 'user_lessons' || $T_OP == 'certificates' || $T_OP == 'user_form'}
   {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?ctg=personal&user=`$smarty.get.user`&op=user_courses'>`$smarty.const._LEARNING`</a>"}
  {elseif $T_OP == 'placements' || $T_OP == 'history' || $T_OP == 'skills' || $T_OP == 'evaluations' || $T_OP =='org_form'}
   {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?ctg=personal&user=`$smarty.get.user`&op=placements'>`$smarty.const._MYROLE`</a>"}
  {elseif $T_OP == 'files'}
   {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?ctg=personal&user=`$smarty.get.user`&op=files'>`$smarty.const._FILES`</a>"}
  {/if}
  <tr><td class = "moduleCell">
    {include file = "includes/personal.tpl"}
  </td></tr>
 {/capture}
{/if}
{if ($T_CTG == 'statistics')}
 {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics">'|cat:$smarty.const._STATISTICS|cat:'</a>'}
 {*moduleStatistics: The statistics page*}
 {if $smarty.get.option == 'user'}
  {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=user">'|cat:$smarty.const._USERSTATISTICS|cat:'</a>'}
  {if $smarty.get.sel_user}
   {assign var = "formatted_login" value = $smarty.get.sel_user|formatLogin}
   {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=user&sel_user='|cat:$smarty.get.sel_user|cat:'">'|cat:$formatted_login|cat:'</a>'}
  {/if}
 {elseif $smarty.get.option == 'lesson'}
  {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=lesson">'|cat:$smarty.const._LESSONSTATISTICS|cat:'</a>'}
  {if isset($T_INFO_LESSON.name)}
   {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=lesson&sel_lesson='|cat:$smarty.get.sel_lesson|cat:'">'|cat:$T_INFO_LESSON.name|cat:'</a>'}
  {/if}
 {elseif $smarty.get.option == 'test'}
  {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=test">'|cat:$smarty.const._TESTSTATISTICS|cat:'</a>'}
  {if isset($smarty.get.sel_test)}
   {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=test&sel_test='|cat:$smarty.get.sel_test|cat:'">'|cat:$T_TEST_INFO.general.name|cat:'</a>'}
  {/if}
 {elseif $smarty.get.option == 'feedback'}
  {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=feedback">'|cat:$smarty.const._FEEDBACKSTATISTICS|cat:'</a>'}
  {if isset($smarty.get.sel_test)}
   {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=feedback&sel_test='|cat:$smarty.get.sel_test|cat:'">'|cat:$T_TEST_INFO.general.name|cat:'</a>'}
  {/if}
  {if isset($smarty.get.question_ID)}
   {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=feedback&sel_test='|cat:$smarty.get.sel_test|cat:'&question_ID='|cat:$smarty.get.question_ID|cat:'">'|cat:$T_TEST_QUESTIONS[$smarty.get.question_ID]->question.text|cat:'</a>'}
  {/if}
 {elseif $smarty.get.option == 'branches'}






 {elseif $smarty.get.option == 'course'}
  {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=course">'|cat:$smarty.const._COURSESTATISTICS|cat:'</a>'}
  {if isset($smarty.get.sel_course)}
   {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=course&sel_course='|cat:$smarty.get.sel_course|cat:'">'|cat:$T_CURRENT_COURSE->course.name|cat:'</a>'}
  {/if}
 {elseif $smarty.get.option == 'advanced_user_reports'}
  {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=advanced_user_reports">'|cat:$smarty.const._ADVANCEDUSERREPORTS|cat:'</a>'}
 {elseif $smarty.get.option == 'system'}
  {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=system">'|cat:$smarty.const._SYSTEMSTATISTICS|cat:'</a>'}
 {elseif $smarty.get.option == 'queries'}
  {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=queries">'|cat:$smarty.const._GENERICQUERIES|cat:'</a>'}
 {/if}

 {capture name = "moduleStatistics"}
  <tr><td class = "moduleCell">
   {include file = "includes/statistics.tpl"}
  </td></tr>
 {/capture}
{/if}
{if $T_CTG_MODULE}
 {assign var = "title" value = '<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg='|cat:$T_CTG|cat:'">'|cat:$T_CTG_MODULE|cat:'</a>'}
 {capture name = "importedModule"}
       <tr><td class = "moduleCell">
        {include file = $smarty.const.G_MODULESPATH|cat:$T_CTG|cat:'/module.tpl'}
       </td></tr>
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
   <tr>
    <td class = "moduleCell" id = "singleColumn">
     {include file = 'social.tpl'}
    </td>
   </tr>
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
<script>var point3 = new Date().getTime();</script>
{*-----------------------------Part 3: Display table-------------------------------------------------*}
<div id = "bookmarks_div_code" style = "display:none">
{capture name = "t_bookmarks_code"}
 <div id = "bookmarks_div"></div>
{/capture}
{eF_template_printBlock title = $smarty.const._SHOWBOOKMARKS data = $smarty.capture.t_bookmarks_code image = "32x32/bookmark.png"}
</div>
{if !$T_LAYOUT_CLASS}{assign var = "layoutClass" value = "centerFull"}{else}{assign var = "layoutClass" value = $T_LAYOUT_CLASS}{/if}
{capture name = "center_code"}
 {if $smarty.get.message}{eF_template_printMessageBlock content = $smarty.get.message type = $smarty.get.message_type}{/if}
 {if $T_MESSAGE}{eF_template_printMessageBlock content = $T_MESSAGE type = $T_MESSAGE_TYPE}{/if}
 {if $T_SEARCH_MESSAGE || $smarty.get.search_message}
  {if $smarty.get.search_message}{assign var = T_SEARCH_MESSAGE value = $smarty.get.search_message}{/if}
  {eF_template_printMessageBlock content = $T_SEARCH_MESSAGE type = $T_MESSAGE_TYPE}
 {/if}
 <table class = "centerTable">
  {$smarty.capture.moduleControlPanel}
  {$smarty.capture.moduleCalendarPage}
  {$smarty.capture.moduleLessonsList}
  {$smarty.capture.moduleInsertContent}
  {$smarty.capture.moduleCompleteLesson}
  {$smarty.capture.moduleComments}
  {$smarty.capture.moduleUnitOrder}
  {$smarty.capture.moduleContentMetadata}
  {$smarty.capture.moduleFileManager}
  {$smarty.capture.moduleCopyContent}
  {$smarty.capture.moduleRules}
  {$smarty.capture.moduleShowUnit}
  {$smarty.capture.moduleTests}
  {$smarty.capture.moduleFeedback}
  {$smarty.capture.moduleAddQuestion}
  {$smarty.capture.moduleAddTest}
  {$smarty.capture.moduleProjects}
  {$smarty.capture.moduleAddEditExercise}
  {$smarty.capture.modulePersonal}
  {$smarty.capture.moduleSearchResults}
  {$smarty.capture.moduleNewsPage}
  {$smarty.capture.moduleProgress}
  {$smarty.capture.moduleScormOptions}
  {$smarty.capture.moduleIMSOptions}
  {$smarty.capture.moduleLessonInformation}
  {$smarty.capture.moduleGlossary}
  {$smarty.capture.moduleStatistics}
  {$smarty.capture.moduleLessonsManagement}
  {$smarty.capture.moduleUsersManagement}
  {$smarty.capture.moduleNewLesson}
  {$smarty.capture.moduleRepairTree}
  {$smarty.capture.moduleInsertPeriod}
  {$smarty.capture.moduleLessonSettings}
  {$smarty.capture.importedModule}
  {$smarty.capture.moduleHCD}
  {$smarty.capture.moduleNewUser}
  {$smarty.capture.moduleEmail}
  {$smarty.capture.moduleUsers}
  {$smarty.capture.moduleEvaluations}
  {$smarty.capture.moduleSocial}
  {$smarty.capture.moduleSpecificContent}
  {$smarty.capture.moduleImport}
  {$smarty.capture.moduleSurvey}
  {$smarty.capture.moduleMessagesPage}
  {$smarty.capture.moduleForum}
  {$smarty.capture.moduleTopics}
  {$smarty.capture.modulePoll}
  {$smarty.capture.moduleLandingPage}
  {$smarty.capture.moduleCourses}
  {$smarty.capture.moduleLessons}
  {$smarty.capture.moduleNewLessonDirection}
 </table>
{/capture}
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
 <span id = "tab_handles" class = "headerText">
 {if $smarty.session.s_lessons_ID != '' && !$T_CONFIGURATION.disable_bookmarks}
  <img class = "ajaxHandle" src = "images/16x16/bookmark.png" alt = "{$smarty.const._SHOWBOOKMARKS}" title = "{$smarty.const._SHOWBOOKMARKS}" onclick = "getBookmarks(this);"/>
 {/if}
 {if $T_CTG == 'content'}
  <img class = "ajaxHandle" src = "images/16x16/navigate_{if $smarty.cookies.rightSideBar == 'hidden'}left{else}right{/if}.png" alt = "{$smarty.const._TOGGLESIDEBAR}" title = "{$smarty.const._TOGGLESIDEBAR}" onclick = "toggleRightSidebar(this, true)"/>
  {if $T_HORIZONTAL_BAR == 1}
   <img class = "ajaxHandle" src = "images/16x16/navigate_{if $smarty.cookies.horizontalSideBar == 'hidden'}down{else}up{/if}.png" alt = "{$smarty.const._TOGGLESIDEBAR}" title = "{$smarty.const._TOGGLESIDEBAR}" onclick = "toggleHorizontalSidebar(this, true)"/>
  {/if}
 {/if}
 </span>
{/capture}
{include file = "includes/common_layout.tpl"}
{*-----------------------------End of Part 3: Display table-------------------------------------------------*}
<script>var point4 = new Date().getTime();</script>
{*-----------------------------Part 4: Finalization data etc-------------------------------------------------*}
<script type="text/javascript">
{literal}
 var contentCell = document.getElementById('contentCell'); //Get the table cell that contains the Unit content
 if (contentCell && contentCell.offsetHeight > 300) { //If this cell is bigger than 300 pixel...
  document.getElementById('navigationDownTable').style.display = ''; //...Then make visible the table that contains the navigation handles below the unit
 }
{/literal}
</script>
<script>var point4_3 = new Date().getTime();</script>
<script>var point5 = new Date().getTime();</script>
{include file = "includes/closing.tpl"}
</body>
</html>
