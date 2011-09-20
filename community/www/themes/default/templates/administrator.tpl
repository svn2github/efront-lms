{include file = "includes/header.tpl"}
<script language = "JavaScript" type = "text/javascript">
{if (isset($T_REFRESH_SIDE))}
    if (top.sideframe)
        {if isset($T_PERSONAL_CTG)}
            top.sideframe.location = "new_sidebar.php?sbctg=personal";
        {else}
            top.sideframe.location = "new_sidebar.php?sbctg={$T_CTG}";
        {/if}
{/if}

{if (isset($T_RELOAD_ALL))}
    top.location = top.location;
{/if}

{* The following code checks whether the sideframe is Loaded, by checking the existence of an element defined at the end of the page *}
{* If so, then the changeTDcolor function will be called from here, otherwise the sideframe will reload and the changeTDcolor function *}
{* will be called internally *}




</script>


{*-------------------------------End of Part 1: initialization etc-----------------------------------------------*}
{if !isset($smarty.get.print_preview) && !isset($smarty.get.print) && !isset($smarty.get.pdf)}
 {assign var = "title" value = '<a class = "titleLink" title="'|cat:$smarty.const._HOME|cat:'" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel">'|cat:$smarty.const._HOME|cat:'</a>'}
{/if}

{*-------------------------------Part 2: Modules List ---------------------------------------------*}
{if (isset($T_CTG) && $T_CTG == 'control_panel')}
 {if $T_OP == 'search'}
  {assign var = "title" value = $title}
 {elseif isset($T_OP_MODULE)}
  {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel&op='|cat:$T_OP|cat:'">'|cat:$T_OP_MODULE|cat:'</a>'}
 {/if}
    {include file = "includes/control_panel.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'modules')}
 {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=modules">'|cat:$smarty.const._MODULES|cat:'</a>'}
    {include file = "includes/modules.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'payments')}
 {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=payments">'|cat:$smarty.const._PAYMENTS|cat:'</a>'}
 {capture name = "modulePayments"}
  <tr><td class="moduleCell">
   {include file = "includes/payments.tpl"}
  </td></tr>
    {/capture}
{/if}
{if (isset($T_CTG) && $T_CTG == 'versionkey')}
 {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=versionkey">'|cat:$smarty.const._VERSIONKEY|cat:'</a>'}
    {include file = "includes/versionkey.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'maintenance')}
 {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=maintenance">'|cat:$smarty.const._MAINTENANCE|cat:'</a>'}
    {include file = "includes/maintenance.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'landing_page')}
 {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=landing_page">'|cat:$smarty.const._LANDINGPAGE|cat:'</a>'}
    {include file = "includes/landing_page.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'system_config')}
 {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=system_config">'|cat:$smarty.const._CONFIGURATIONVARIABLES|cat:'</a>'}
    {include file = "includes/system_config.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'import_export')}
 {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=import_export">'|cat:$smarty.const._EXPORTIMPORTDATA|cat:'</a>'}
    {include file = "includes/import_export.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'user_profile')}
 {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=user_profile">'|cat:$smarty.const._CUSTOMIZEUSERSPROFILE|cat:'</a>'}
    {if $smarty.get.edit_field}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=user_profile&edit_field='|cat:$smarty.get.edit_field|cat:'&type='|cat:$smarty.get.type|cat:'">'|cat:$smarty.const._EDITFIELD|cat:'</a>'}
    {elseif $smarty.get.add_field}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=user_profile&add_field=1&type='|cat:$smarty.get.type|cat:'">'|cat:$smarty.const._ADDFIELD|cat:'</a>'}
    {/if}
    {include file = "includes/user_profile.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'news')}
 {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=news">'|cat:$smarty.const._ANNOUNCEMENTS|cat:'</a>'}
    {include file = "includes/news.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'backup')}
 {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=backup">'|cat:$smarty.const._BACKUP|cat:' - '|cat:$smarty.const._RESTORE|cat:'</a>'}
    {include file = "includes/backup.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'languages')}
 {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=languages">'|cat:$smarty.const._LANGUAGES|cat:'</a>'}
    {include file = "includes/languages.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'logout_user')}
 {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=logout_user">'|cat:$smarty.const._CONNECTEDUSERS|cat:'</a>'}
    {include file = "includes/logout_user.tpl"}
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

{if isset($T_CTG) && $T_CTG == 'personal'}
 {capture name = "modulePersonal"}
  {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=users'>`$smarty.const._USERS`</a>"}
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
   {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?ctg=personal&user=`$smarty.get.user`&op=placements'>`$smarty.const._ORGANIZATION`</a>"}
  {elseif $T_OP == 'files'}
   {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?ctg=personal&user=`$smarty.get.user`&op=files'>`$smarty.const._FILES`</a>"}
  {/if}
  <tr><td class = "moduleCell">
    {include file = "includes/personal.tpl"}
  </td></tr>
 {/capture}
{/if}
{if isset($T_CTG) && $T_CTG == 'users'}
    {if !isset($smarty.get.print_preview) && !isset($smarty.get.print) && !$T_POPUP_MODE}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=users">'|cat:$smarty.const._USERS|cat:'</a>'}
    {/if}
    {include file = "includes/users.tpl"}

{/if}
{if (isset($T_CTG) && $T_CTG == 'archive')}
 {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=archive">'|cat:$smarty.const._ARCHIVE|cat:'</a>'}
 {if $smarty.get.type == 'users'}
  {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=archive&type=users">'|cat:$smarty.const._USERS|cat:'</a>'}
 {elseif $smarty.get.type == 'lessons'}
  {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=archive&type=lessons">'|cat:$smarty.const._LESSONS|cat:'</a>'}
 {elseif $smarty.get.type == 'courses'}
  {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=archive&type=courses">'|cat:$smarty.const._COURSES|cat:'</a>'}
 {/if}
    {include file = "includes/archive.tpl"}
{/if}

{if (isset($T_CTG) && $T_CTG == 'lessons')}
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=lessons">'|cat:$smarty.const._LESSONS|cat:'</a>'}
    {if $smarty.get.edit_lesson}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=lessons&edit_lesson='|cat:$smarty.get.edit_lesson|cat:'">'|cat:$smarty.const._EDITLESSON|cat:' <span class = "innerTableName">&quot;'|cat:$T_LESSON_FORM.name.value|cat:'&quot;</span></a>'}
    {elseif $smarty.get.lesson_info}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=lessons&lesson_info='|cat:$smarty.get.lesson_info|cat:'">'|cat:$smarty.const._INFORMATIONFORLESSON|cat:' &quot;'|cat:$T_CURRENT_LESSON->lesson.name|cat:'&quot;</a>'}
  {if $smarty.get.edit_info}
   {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?ctg=lessons&lesson_info=`$smarty.get.lesson_info`&edit_info=1'>`$smarty.const._EDITLESSONINFORMATION`</a>"}
  {/if}
    {elseif $smarty.get.add_lesson}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=lessons&add_lesson=1">'|cat:$smarty.const._NEWLESSON|cat:'</a>'}
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

    {include file = "includes/lessons.tpl"}
{/if}

{if (isset($T_CTG) && $T_CTG == 'file_manager')}
 {include file = "includes/file_manager.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'social')}

    {if $T_OP == 'dashboard'}
        {assign var = "title" value = '<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel">'|cat:$smarty.const._HOME|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=social&op=dashboard">'|cat:$smarty.const._DASHBOARD|cat:'</a>'}
    {elseif $T_OP == 'people'}
        {assign var = "title" value = '<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel">'|cat:$smarty.const._HOME|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=social&op=people">'|cat:$smarty.const._PEOPLE|cat:'</a>'}
    {elseif $T_OP == 'timeline'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel&op=timeline">'|cat:$smarty.const._SYSTEMTIMELINE|cat:'</a>'}
    {/if}
    {capture name = "moduleSocial"}
            <tr>
                <td class = "moduleCell" id = "singleColumn">
                    {include file = 'social.tpl'}
                </td>
            </tr>
    {/capture}

{/if}

{if (isset($T_CTG) && $T_CTG == 'digests')}

    {assign var = "title" value = '<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel">'|cat:$smarty.const._HOME|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=digests">'|cat:$smarty.const._EMAILDIGESTS|cat:'</a>'}
    {if isset($smarty.get.add_notification)}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=digests&add_notification=1">'|cat:$smarty.const._ADDNOTIFICATION|cat:'</a>'}
    {elseif isset($smarty.get.edit_notification)}
        {if isset($smarty.get.event)}
            {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=digests&edit_notification='|cat:$smarty.get.edit_notification|cat:'&event=1">'|cat:$smarty.const._EDITNOTIFICATION|cat:'</a>'}
        {else}
            {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=digests&edit_notification='|cat:$smarty.get.edit_notification|cat:'">'|cat:$smarty.const._EDITNOTIFICATION|cat:'</a>'}
        {/if}
    {/if}
    {capture name = "moduleDigests"}
            <tr>
                <td class = "singleColumn" id = "singleColumn" colspan = "2">
                    {include file = 'includes/digests.tpl'}
                </td>
            </tr>
    {/capture}

{/if}

{if $T_CTG == 'content'}

    {if $T_OP == 'file_manager'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=content&op=file_manager">'|cat:$smarty.const._FILES|cat:'</a>'}
        {*moduleFileManager: The file manager page*}
        {capture name = "moduleFileManager"}
            {if $T_FILE_METADATA}
                    <tr><td class = "moduleCell">
                        {capture name = 't_file_info_code'}
                            <fieldset class = "fieldsetSeparator">
                                <legend>{$smarty.const._FILEMETADATA}</legend>
                                {$T_FILE_METADATA_HTML}
                            </fieldset>
                        {/capture}
                        {eF_template_printBlock title = $smarty.const._INFORMATIONFORFILE|cat:' &quot;'|cat:$T_FILE_METADATA.name|cat:'&quot;' data = $smarty.capture.t_file_info_code image = '32x32/information.png'}
                    </td></tr>
            {else}
                    <tr><td class = "moduleCell">
                        {capture name = 't_file_manager_code'}
                            {$T_FILE_MANAGER}
                        {/capture}
                        {eF_template_printBlock title=$smarty.const._FILEMANAGER data=$smarty.capture.t_file_manager_code image='32x32/file_explorer.png'}
                    </td></tr>
            {/if}
        {/capture}
    {/if}
{/if}
{if (isset($T_CTG) && $T_CTG == 'curriculums')}
 {capture name = "moduleCurriculums"}
 {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?ctg=curriculums'>`$smarty.const._CURRICULUM`</a>"}
 {if $smarty.get.edit}
  {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?ctg=curriculums&edit=`$smarty.get.edit`'>`$smarty.const._EDITCURRICULUM`</a>"}
 {elseif $smarty.get.add}
  {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?ctg=curriculums&add=1'>`$smarty.const._ADDCURRICULUM`</a>"}
 {/if}
  <tr><td class="moduleCell">
   {include file = "includes/curriculums.tpl"}
  </td></tr>
 {/capture}
{/if}
{if (isset($T_CTG) && $T_CTG == 'tests')}
        {*moduleTests: Print the Tests page*}
        {capture name = "moduleTests"}
            {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=tests">'|cat:$smarty.const._SKILLGAPTESTS|cat:'</a>'}
            {if $smarty.get.edit_test}
                {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=tests&edit_test=`$smarty.get.edit_test`'>`$smarty.const._EDITSKILLGAPTEST`<span class='innerTableName'>&nbsp;&quot;`$T_CURRENT_TEST->test.name`&quot;</span></a>"}
            {elseif $smarty.get.add_test && isset($smarty.get.create_quick_test)}
                {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=tests&add_test=1&create_quick_test=1'>`$smarty.const._ADDQUICKSKILLGAP`</a>"}

            {elseif $smarty.get.add_test}
                {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=tests&add_test=1'>`$smarty.const._ADDSKILLGAPTEST`</a>"}
            {elseif $smarty.get.edit_question}
                {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=tests&edit_question=`$smarty.get.edit_question`&question_type=`$smarty.get.question_type`&lessonId=`$smarty.get.lessonId`'>`$smarty.const._EDITQUESTION`</a>"}
            {elseif $smarty.get.add_question}
                {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=tests&add_question=1&question_type=`$smarty.get.question_type`'>`$smarty.const._ADDQUESTION`</a>"}
            {elseif $smarty.get.test_results}
                {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=tests&test_results='|cat:$smarty.get.test_results|cat:'">'|cat:$smarty.const._SKILLGAPTESTRESULTS|cat:'</a>'}
            {elseif $smarty.get.show_test}
                {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=tests&show_test='|cat:$smarty.get.show_test|cat:'">'|cat:$smarty.const._PREVIEW|cat:'</a>'}
            {elseif $smarty.get.show_solved_test}
                {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=tests&test_results=`$T_TEST_DATA->completedTest.testsId`'>`$smarty.const._SKILLGAPTESTRESULTS`</a>"}
                {if !$smarty.get.test_analysis}
                {assign var = "formatted_login" value = $T_TEST_DATA->completedTest.login|formatLogin}
                    {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=tests&show_solved_test=`$T_TEST_DATA->completedTest.id`'>`$smarty.const._VIEWSOLVEDTEST`: &quot;`$T_TEST_DATA->test.name`&quot; `$smarty.const._BYUSER`: `$formatted_login`</a>"}
                {else}
                    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=tests&show_solved_test='|cat:$smarty.get.show_solved_test|cat:'&test_analysis='|cat:$smarty.get.test_analysis|cat:'&user='|cat:$smarty.get.user|cat:'">'|cat:$smarty.const._USERRESULTS|cat:'</a>'}
                {/if}
            {elseif $smarty.get.solved_tests}
                {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=tests&solved_tests=1'>`$smarty.const._SHOWALLSOLVEDSKILLGAPTESTS`</a>"}
            {/if}

            <tr><td class = "moduleTests" style = "vertical-align:top">
                {include file = "includes/module_tests.tpl"}

            </td></tr>
        {/capture}
{/if}

{if (isset($T_CTG) && $T_CTG == 'directions')}
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=directions">'|cat:$smarty.const._CATEGORIES|cat:'</a>'}
    {if $smarty.get.add_direction}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=directions&add_direction=1">'|cat:$smarty.const._NEWCATEGORY|cat:'</a>'}
    {elseif $smarty.get.edit_direction}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=directions&edit_direction='|cat:$smarty.get.edit_direction|cat:'">'|cat:$smarty.const._EDITCATEGORY|cat:'<span class="innerTableName">&nbsp;&quot;'|cat:$T_DIRECTIONS_FORM.name.value|cat:'&quot;</span></a>'}
    {/if}

    {include file = "includes/categories.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'courses')}
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=courses">'|cat:$smarty.const._COURSES|cat:'</a>'}

 {if $smarty.get.edit_course}
     {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=courses&edit_course='|cat:$smarty.get.edit_course|cat:'">'|cat:$smarty.const._EDITCOURSE|cat:'&nbsp;<span class="innerTableName">&quot;'|cat:$T_COURSE_FORM.name.value|cat:'&quot;</span></a>'}
 {elseif $smarty.get.add_course}
     {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=courses&add_course=1">'|cat:$smarty.const._NEWCOURSE|cat:'</a>'}
    {elseif $smarty.get.course}
     {if $T_OP == course_info}
         {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?`$T_BASE_URL`&op=course_info'>`$smarty.const._INFORMATIONFORCOURSE` &quot;`$T_CURRENT_COURSE->course.name`&quot;</a>"}
     {elseif $T_OP == course_certificates}
         {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?`$T_BASE_URL`&op=course_certificates'>`$smarty.const._COMPLETION` &quot;`$T_CURRENT_COURSE->course.name`&quot;</a>"}
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
     {elseif $T_MODULE_TABPAGE}
      {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?`$T_BASE_URL`&op=`$T_MODULE_TABPAGE.tab_page`'>`$T_MODULE_TABPAGE.title`</a>"}
     {/if}
 {/if}
    {include file = "includes/courses.tpl"}
{/if}

{if (isset($T_CTG) && $T_CTG == 'user_types')}
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=user_types">'|cat:$smarty.const._USERTYPES|cat:'</a>'}
    {if $smarty.get.edit_user_type}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=user_types&edit_user_type='|cat:$smarty.get.edit_user_type|cat:'">'|cat:$smarty.const._EDITUSERTYPE|cat:' <span class = "innerTableName">&quot;'|cat:$T_USER_TYPE_NAME|cat:'&quot;</span></a>'}
    {else}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=user_types&add_user_type=1">'|cat:$smarty.const._NEWUSERTYPE|cat:'</a>'}
    {/if}

    {include file = "includes/user_types.tpl"}
{/if}
{if (isset($T_CTG) && $T_CTG == 'user_groups')}
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=user_groups">'|cat:$smarty.const._GROUPS|cat:'</a>'}
    {if $smarty.get.add_user_group}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=user_groups&add_user_group=1">'|cat:$smarty.const._NEWGROUP|cat:'</a>'}
    {elseif $smarty.get.edit_user_group}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=user_groups&edit_user_group='|cat:$smarty.get.edit_user_group|cat:'">'|cat:$smarty.const._EDITGROUP|cat:'&nbsp;<span class="innerTableName">&quot;'|cat:$T_CURRENT_GROUP->group.name|cat:'&quot;</span></a>'}
    {/if}

    {include file = "includes/groups.tpl"}
{/if}


{if (isset($T_CATEGORY) && $T_CATEGORY == 'statistics')}
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics">'|cat:$smarty.const._STATISTICS|cat:'</a>'}
{*moduleStatistics: The administrator statistics page*}
    {if $smarty.get.option == 'user'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=user">'|cat:$smarty.const._USERSTATISTICS|cat:'</a>'}
        {if $smarty.get.sel_user}
   {assign var = "formatted_login" value = $smarty.get.sel_user|formatLogin}
            {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=user&sel_user='|cat:$smarty.get.sel_user|cat:'">'|cat:$formatted_login|cat:'</a>'}
        {/if}
    {elseif $smarty.get.option == 'lesson'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=lesson">'|cat:$smarty.const._LESSONSTATISTICS|cat:'</a>'}
        {if isset($smarty.get.sel_lesson)}
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
    {elseif $smarty.get.option == 'course'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=course">'|cat:$smarty.const._COURSESTATISTICS|cat:'</a>'}
        {if isset($smarty.get.sel_course)}
            {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=course&sel_course='|cat:$smarty.get.sel_course|cat:'">'|cat:$T_CURRENT_COURSE->course.name|cat:'</a>'}
        {/if}
    {elseif $smarty.get.option == 'system'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=system">'|cat:$smarty.const._SYSTEMSTATISTICS|cat:'</a>'}
    {elseif $smarty.get.option == 'queries'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=queries">'|cat:$smarty.const._GENERICQUERIES|cat:'</a>'}
    {elseif $smarty.get.option == 'custom'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=custom">'|cat:$smarty.const._CUSTOMSTATISTICS|cat:'</a>'}
    {elseif $smarty.get.option == 'certificate'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=certificate">'|cat:$smarty.const._CERTIFICATESTATISTICS|cat:'</a>'}
 {elseif $smarty.get.option == 'events'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=events">'|cat:$smarty.const._EVENTSTATISTICS|cat:'</a>'}
 {elseif $smarty.get.option == 'groups'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=groups">'|cat:$smarty.const._GROUPSTATISTICS|cat:'</a>'}
  {if isset($smarty.get.sel_group)}
   {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=groups&sel_group='|cat:$smarty.get.sel_group|cat:'">'|cat:$T_GROUP_NAME|cat:'</a>'}
  {/if}
    {elseif $smarty.get.option == 'advanced_user_reports'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=advanced_user_reports">'|cat:$smarty.const._ADVANCEDUSERREPORTS|cat:'</a>'}
    {elseif $smarty.get.option == 'participation'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=participation">'|cat:$smarty.const._PARTICIPATIONSTATISTICS|cat:'</a>'}
 {/if}
    {capture name = "moduleStatistics"}
            <tr><td class = "moduleCell">
                {include file = "includes/statistics.tpl"}
            </td></tr>
    {/capture}
{/if}
{if ($T_CTG == 'calendar')}
 {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=calendar">'|cat:$smarty.const._CALENDAR|cat:'</a>'}
 {include file = "includes/calendar.tpl"}
{/if}
{if $T_CTG == 'search_courses'}
    {assign var = "title" value = '<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel">'|cat:$smarty.const._HOME|cat:'</a>'}
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=search_courses">'|cat:$smarty.const._SEARCHCOURSEUSERS|cat:'</a>'}
    {*moduleSearchCoursesPage: Display the search courses page*}
    {capture name = "moduleSearchCoursesPage"}
                            <tr><td class = "moduleCell">
                                {include file = "search_courses.tpl"}
                                    {eF_template_printBlock title=$smarty.const._FINDEMPLOYEES data=$smarty.capture.t_search_course_code image='32x32/scorm.png' main_options=$T_TABLE_OPTIONS}
                                    <br />
                                    {eF_template_printBlock title=$smarty.const._USERSFULFILLINGCRITERIA data=$smarty.capture.t_found_employees_code image='32x32/user.png' options = $T_SENDALLMAIL_LINK}
                            </td></tr>
    {/capture}
{/if}
{*///MODULES2*}
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
{if $T_CTG_MODULE}
    {assign var = "title" value = '<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg='|cat:$T_CTG|cat:'">'|cat:$T_CTG_MODULE|cat:'</a>'}
    {capture name = "importedModule"}
                            <tr><td class = "moduleCell">
                                {include file = $smarty.const.G_MODULESPATH|cat:$T_CTG|cat:'/module.tpl'}
                            </td></tr>
    {/capture}
{/if}
{*moduleSearchResults: The Search results page*}
{if (isset($smarty.post.search_text))}
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="javascript:void(0)">'|cat:$smarty.const._SEARCHRESULTS|cat:'</a>'}
    {capture name = "moduleSearchResults"}
                            <tr><td class = "moduleCell">
                                    {include file = "includes/module_search.tpl"}
                            </td></tr>
    {/capture}
{/if}
{*moduleThemes: The themes page*}
{if (isset($T_CTG) && $T_CTG == 'themes')}
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=themes">'|cat:$smarty.const._THEMES|cat:'</a>'}
 {if $smarty.get.edit_page}
  {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=themes&tab=external&edit_page='|cat:$smarty.get.edit_page|cat:'">'|cat:$smarty.const._UPDATEPAGE|cat:'</a>'}
 {elseif $smarty.get.add_page}
  {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=themes&tab=external&add_page=1">'|cat:$smarty.const._NEWPAGE|cat:'</a>'}
 {/if}
 {capture name = "moduleThemes"}
                            <tr><td class = "moduleCell">
                                    {include file = "includes/themes.tpl"}
                            </td></tr>
    {/capture}
{/if}
{* MODULE HCD: *}
{if (isset($T_CTG) && $T_CTG == 'module_hcd')}
{*moduleHCD: The resuls of control panel*}
    {assign var = "title" value = '<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel">'|cat:$smarty.const._HOME|cat:'</a>'}
    {if $smarty.get.op != 'reports' && $smarty.get.op != 'skill_cat'}
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
                    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd&op=skills&edit_skill='|cat:$smarty.get.edit_skill|cat:'">'|cat:$smarty.const._EDITSKILL|cat:' <span class="innerTableName">&quot;'|cat:$T_SKILL_NAME|cat:'&quot;</span></a>'}
                {else}
                    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd&op=skills&add_skill=1">'|cat:$smarty.const._NEWSKILL|cat:'</a>'}
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
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd&op=reports">'|cat:$smarty.const._SEARCHFOREMPLOYEE|cat:'</a>'}
    {/if}
    {if $smarty.get.op == 'imp_exp'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd&op=imp_exp">'|cat:$smarty.const._IMPORTEXPORTORGANIZATIONALDATA|cat:'</a>'}
    {/if}
    {if $smarty.get.op == 'chart'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd&op=chart">'|cat:$smarty.const._ORGANIZATIONCHARTTREE|cat:'</a>'}
    {/if}
    {capture name = "moduleHCD"}
                            <tr><td class = "moduleCell">
                                {include file = 'module_hcd.tpl'}
                            </td></tr>
    {/capture}
{/if}
{*----------------------------End of Part 2: Modules List------------------------------------------------*}
{*-----------------------------Part 3: Display table-------------------------------------------------*}
{if !$T_LAYOUT_CLASS}{assign var = "layoutClass" value = "centerFull"}{else}{assign var = "layoutClass" value = $T_LAYOUT_CLASS}{/if}
{capture name = "center_code"}
 {if $smarty.get.message}{eF_template_printMessageBlock content = $smarty.get.message type = $smarty.get.message_type}{/if}
 {if $T_MESSAGE}{eF_template_printMessageBlock content = $T_MESSAGE type = $T_MESSAGE_TYPE}{/if}
 {if $T_SEARCH_MESSAGE || $smarty.get.search_message}
     {if $smarty.get.search_message}{assign var = T_SEARCH_MESSAGE value = $smarty.get.search_message}{/if}
  {eF_template_printMessageBlock content = $T_SEARCH_MESSAGE type = $T_MESSAGE_TYPE}
 {/if}
 <table class = "centerTable">
  {$smarty.capture.moduleArchive}
  {$smarty.capture.moduleControlPanel}
  {$smarty.capture.moduleUsers}
  {$smarty.capture.moduleNewUser}
  {$smarty.capture.moduleNewLessonDirection}
  {$smarty.capture.moduleLessons}
  {$smarty.capture.moduleNewCourse}
  {$smarty.capture.moduleCourses}
  {$smarty.capture.moduleDirections}
  {$smarty.capture.moduleTests}
  {$smarty.capture.moduleRoles}
  {$smarty.capture.moduleGroups}
  {$smarty.capture.moduleNewsPage}
  {$smarty.capture.moduleCms}
  {$smarty.capture.moduleStatistics}
  {$smarty.capture.moduleBackup}
  {$smarty.capture.moduleLanguages}
  {$smarty.capture.moduleEmail}
  {$smarty.capture.moduleImportExportUsers}
  {$smarty.capture.moduleSearchResults}
  {$smarty.capture.moduleConfig}
  {$smarty.capture.moduleModules}
  {$smarty.capture.moduleFileManager}
  {$smarty.capture.moduleCleanup}
  {$smarty.capture.moduleImportUsers}
  {$smarty.capture.moduleCustomizeUsersProfile}
  {$smarty.capture.importedModule}
  {$smarty.capture.moduleHCD}
  {$smarty.capture.moduleCalendarPage}
  {$smarty.capture.moduleSearchCoursesPage}
  {$smarty.capture.modulePayments}
  {$smarty.capture.moduleLandingPage}
  {$smarty.capture.moduleCurriculums}
  {$smarty.capture.moduleVersionKey}
  {$smarty.capture.moduleSocialAdmin}
  {$smarty.capture.moduleSocial}
  {$smarty.capture.moduleDigests}
  {$smarty.capture.moduleThemes}
  {$smarty.capture.moduleLogoutUser}
  {$smarty.capture.moduleForum}
  {$smarty.capture.moduleMessagesPage}
  {$smarty.capture.moduleShowRoom}
  {$smarty.capture.moduleRoomsList}
    {$smarty.capture.modulePersonal}
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
{include file = "includes/common_layout.tpl"}
{*-----------------------------End of Part 3: Display table-------------------------------------------------*}
{*-----------------------------Part 4: Finalization data etc-------------------------------------------------*}
{include file = "includes/closing.tpl"}
</body>
</html>
