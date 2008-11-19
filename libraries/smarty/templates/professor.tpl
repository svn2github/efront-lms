{include file = "includes/header.tpl"}
{if $smarty.session.s_lessons_ID}
	{assign var = "title" value = '<a class = "titleLink" title = "'|cat:$smarty.const._HOMEOFLESSON|cat:'&nbsp;&quot;'|cat:$T_CURRENT_LESSON->lesson.name|cat:'&quot;" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel">'|cat:$smarty.const._HOME|cat:'</a>'}
{else}
	{assign var = "title" value = '<a class = "titleLink" title = "'|cat:$smarty.const._HOME|cat:'" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel">'|cat:$smarty.const._HOME|cat:'</a>'}
{/if}

{if $T_SCORM}
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
{/if}


<script type="text/javascript">
{* Code for changing the sideframe - reloading it if necessary and selecting the right link *}
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
            top.sideframe.location = "new_sidebar.php?ctg=lessons&new_lesson_id={$smarty.session.s_lessons_ID}";
        {/if}
    {/if}
{/if}

{*
{if ($T_MODULE_HCD_INTERFACE == 0 || $T_CTG != 'users' || $smarty.get.print != 1)}
    {if (isset($T_CHANGE_LESSON) || isset($T_REFRESH_SIDE) || isset($smarty.get.refresh_side))}
        if (top.sideframe)
            top.sideframe.location = "new_sidebar.php?ctg={$T_CTG}";
    {elseif !$T_POPUP_MODE && !$smarty.get.popup}
        * Patch for solving reports problem: reports ~ ctg=module_hcd&op=reports but all ctg=module_hcd should link elsewhere *
        {if ($T_MODULE_HCD_INTERFACE && ($T_OP == "reports") && ($T_CTG == "module_hcd") && ($smarty.session.employee_type == $smarty.const._SUPERVISOR))}
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
           top.sideframe.changeTDcolor('{$T_CTG}');
       {/if}
    {/if}
{literal}
} else {
{/literal}
    {if !$T_POPUP_MODE && !$smarty.get.popup}
        if (top.sideframe)
        {if $T_CTG == 'personal' && isset($smarty.get.tab) && $smarty.get.tab == 'file_record'}
            top.sideframe.location = "new_sidebar.php?sbctg=file_manager";
        {elseif $T_CTG == 'control_panel' && isset($smarty.get.lessons_ID)}
            top.sideframe.location = "new_sidebar.php?sbctg=lesson_main";
        {elseif $T_CTG == 'content' && isset($smarty.get.type) && $smarty.get.type == 'theory'}
            top.sideframe.location = "new_sidebar.php?sbctg=theory";
        {elseif $T_CTG == 'tests'}
            top.sideframe.location = "new_sidebar.php?sbctg=tests";
        {elseif $T_CTG == 'projects'}
            top.sideframe.location = "new_sidebar.php?sbctg=exercises";
        {elseif $T_CTG == 'glossary'}
            top.sideframe.location = "new_sidebar.php?sbctg=glossary";
       {elseif $T_CTG == 'content' && $T_OP == 'file_manager'}
            top.sideframe.location = "new_sidebar.php?sbctg=file_manager";
        {elseif $T_CTG == 'users' && $smarty.session.employee_type == $smarty.const._SUPERVISOR}
            top.sideframe.location = "new_sidebar.php?sbctg=employees";
        {elseif $T_MODULE_HCD_INTERFACE  && ($T_CTG == "module_hcd")}
            {if ($T_OP == "reports")}
                top.sideframe.location = "new_sidebar.php?sbctg=reports";
            {elseif isset($T_OP) && $T_OP != ''}
                top.sideframe.location = "new_sidebar.php?sbctg=placements{$T_OP}";
            {else}
                top.sideframe.location = "new_sidebar.php?sbctg=hcd_control_panel";
            {/if}
        {elseif $T_CTG == 'module'}
            top.sideframe.location = "new_sidebar.php?sbctg={$T_MODULE_HIGHLIGHT}";
        {else}
            top.sideframe.location = "new_sidebar.php?sbctg={$T_CTG}";
        {/if}
    {/if}
{literal}
}
{/literal}

</script>


{if $T_SCORM}
    <script language="JavaScript" type="text/javascript" src="js/LMSFunctions.php?view_unit={$T_VIEW_UNIT}"></script>
{/if}
{*-------------------------------End of Part 1: initialization etc-----------------------------------------------*}
<script>var point2 = new Date().getTime();</script>
{*-------------------------------Part 2: Modules List ---------------------------------------------*}
{if $T_CTG == 'control_panel'}
    {if $T_OP == 'scorm'}
        {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink'  href = '`$smarty.server.PHP_SELF`?ctg=control_panel&op=scorm'>`$smarty.const._SCORMOPTIONS`</a>"}
        {*moduleScormOptions: SCORM options page*}
        {capture name = "moduleScormOptions"}
                                <tr><td class = "moduleCell">
                        {if $smarty.get.scorm_review}
                            {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink'  href = '`$smarty.server.PHP_SELF`?ctg=control_panel&op=scorm&scorm_review=1'>`$smarty.const._SCORMREVIEW`</a>"}
                            {capture name = 'scorm_review_code'}
<!--ajax:scormUsersTable-->
                                            <table style = "width:100%" class = "sortedTable" size = "{$T_USERS_SIZE}" sortBy = "0" id = "scormUsersTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "professor.php?ctg=control_panel&op=scorm&scorm_review=1&">
                                                <tr class = "defaultRowHeight">
                                                    <td class = "topTitle" name = "users_LOGIN">{$smarty.const._USERCAPITAL}</td>
                                                    <td class = "topTitle" name = "name">{$smarty.const._NAME}</td>
                                                    <td class = "topTitle" name = "surname">{$smarty.const._SURNAME}</td>
                                                    <td class = "topTitle" name = "content_name">{$smarty.const._UNIT}</td>
                                                    <td class = "topTitle" name = "timestamp">{$smarty.const._DATE}</td>
                                                    <td class = "topTitle" name = "entry">{$smarty.const._ENTRY}</td>
                                                    <td class = "topTitle" name = "lesson_status">{$smarty.const._STATUS}</td>
                                                    <td class = "topTitle centerAlign" name = "total_time">{$smarty.const._TOTALTIME}</td>
                                                    <td class = "topTitle centerAlign" name = "minscore">{$smarty.const._MINSCORE}</td>
                                                    <td class = "topTitle centerAlign" name = "maxscore">{$smarty.const._MAXSCORE}</td>
                                                    <td class = "topTitle centerAlign" name = "masteryscore">{$smarty.const._MASTERYSCORE}</td>
                                                    <td class = "topTitle centerAlign" name = "score">{$smarty.const._SCORE}</td>
                                                {if !isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change'}
                                                    <td class = "topTitle centerAlign noSort">{$smarty.const._FUNCTIONS}</td>
                                                {/if}
                                                </tr>

                                        {foreach name = 'scorm_data' item = "item" key = "key" from = $T_SCORM_DATA}
                                            <tr class = "{cycle values = "oddRowColor, evenRowColor"} defaultRowHeight">
                                                <td>{$item.users_LOGIN}</td>
                                                <td>{$item.name}</td>
                                                <td>{$item.surname}</td>
                                                <td>{$item.content_name|eF_truncate:30}</td>
                                                <td style = "white-space:nowrap">#filter:timestamp_time-{$item.timestamp}#</td>
                                                <td>{$item.entry}</td>
                                                <td>{$item.lesson_status}</td>
                                                <td class = "centerAlign">{$item.total_time}</td>
                                                <td class = "centerAlign">{$item.min_score}</td>
                                                <td class = "centerAlign">{$item.max_score}</td>
                                                <td class = "centerAlign">{$item.masteryscore}</td>
                                                <td class = "centerAlign">{$item.score}</td>
                                            {if !isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change'}
                                                <td class = "centerAlign"><a href = "javascript:void(0)" onclick = "deleteData(this, {$item.id})"><img src = "images/16x16/delete.png" alt = "{$smarty.const._DELETEDATA}" title = "{$smarty.const._DELETEDATA}" border = "0"></a></td>
                                            {/if}
                                            </tr>
                                        {foreachelse}
                                            <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "emptyCategory centerAlign">{$smarty.const._NODATAFOUND}</td></tr>
                                        {/foreach}
                                            </table>
<!--/ajax:scormUsersTable-->
                                            <script>
                                            {literal}
                                            function deleteData(el, id) {
                                                Element.extend(el);
                                                url = 'professor.php?ctg=control_panel&op=scorm&scorm_review=1&delete='+id;
                                                el.down().src = 'images/others/progress1.gif';
                                                new Ajax.Request(url, {
                                                        method:'get',
                                                        asynchronous:true,
                                                        onFailure: function (transport) {
                                                            el.down().writeAttribute({src:'images/16x16/delete.png', title: transport.responseText}).hide();
                                                            new Effect.Appear(el.down().identify());
                                                            window.setTimeout('Effect.Fade("'+el.down().identify()+'")', 10000);
                                                        },
                                                        onSuccess: function (transport) {
                                                            new Effect.Fade(el.up().up());
                                                            }
                                                    });

                                            }
                                            {/literal}
                                            </script>
                            {/capture}
                            {eF_template_printInnerTable title = $smarty.const._REVIEWSCORMDATAFOR|cat:' &quot;'|cat:$T_CURRENT_LESSON->lesson.name|cat:'&quot;' data = $smarty.capture.scorm_review_code image = '32x32/book_red.png' main_options = $T_TABLE_OPTIONS}

                        {elseif $smarty.get.scorm_import}
                            {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink'  href = '`$smarty.server.PHP_SELF`?ctg=control_panel&op=scorm&scorm_import=1'>`$smarty.const._SCORMIMPORT`</a>"}

                            {capture name = 'scorm_import_code'}
                                {$T_UPLOAD_SCORM_FORM.javascript}
                                <form {$T_UPLOAD_SCORM_FORM.attributes}>
                                    {$T_UPLOAD_SCORM_FORM.hidden}
                                    <table style = "margin-top:15px;">
                                        <tr><td class = "labelCell">{$smarty.const._UPLOADTHESCORMFILEINZIPFORMAT}:
                                            <td class = "elementCell">{$T_UPLOAD_SCORM_FORM.scorm_file.html}</td></tr>
                                        <tr><td></td><td>&nbsp;</td></tr>
                                        <tr><td class = "labelCell"></td>
                                            <td class = "elementCell">{$T_UPLOAD_SCORM_FORM.submit_upload_scorm.html}</td></tr>
                                    </table>
                                </form>
                            {/capture}
                            {eF_template_printInnerTable title = $smarty.const._SCORMIMPORT data = $smarty.capture.scorm_import_code image = '32x32/book_red.png' main_options = $T_TABLE_OPTIONS}

                        {elseif $smarty.get.scorm_export}
                            {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink'  href = '`$smarty.server.PHP_SELF`?ctg=control_panel&op=scorm&scorm_export=1'>`$smarty.const._SCORMEXPORT`</a>"}

                            {capture name = 'scorm_export_code'}
                            {if (isset($T_SCORM_EXPORT_FILE))}
                                <table style = "margin-top:15px;">
                                    <tr>
                                        <td><span style = "vertical-align:middle">{$smarty.const._DOWNLOADSCORMEXPORTEDFILE}:&nbsp;</span>
                                            <a href = "view_file.php?file={$T_SCORM_EXPORT_FILE.path}&action=download" target = "POPUP_FRAME" style = "vertical-align:middle">{$T_SCORM_EXPORT_FILE.name}</a>
                                            <img src = "images/16x16/import1.png" alt = "{$smarty.const._DOWNLOADFILE}" title = "{$smarty.const._DOWNLOADFILE}" border = "0" style = "vertical-align:middle">
                                        </td>
                                    </tr>
                                </table>
                            {/if}
                                    {$T_EXPORT_SCORM_FORM.javascript}
                                    <form {$T_EXPORT_SCORM_FORM.attributes}>
                                        {$T_EXPORT_SCORM_FORM.hidden}
                                        <table style = "margin-top:15px;">
                                            <tr>
                                                <td class = "labelCell">{$smarty.const._SCORMEXPORT}:&nbsp;</td>
                                                <td class = "elementCell">{$T_EXPORT_SCORM_FORM.submit_export_scorm.html}</td>
                                                </tr>
                                        </table>
                                    </form>
                            {/capture}
                            {eF_template_printInnerTable title = $smarty.const._SCORMEXPORT data = $smarty.capture.scorm_export_code image = '32x32/book_red.png' main_options = $T_TABLE_OPTIONS}

                        {else}
                            {capture name = 't_scorm_tree_code'}
                                <script>
                                {literal}
                                    function convertScorm(el, id) {
                                        Element.extend(el);
                                        if (el.up().previous().previous().src.match('scorm_test')) {
                                            newSrc = 'images/drag-drop-tree/scorm.png';
                                            url    = 'professor.php?ctg=control_panel&op=scorm&set_type=scorm&id='+id;
                                            button = 'images/16x16/scorm_to_test.png';
                                        } else {
                                            newSrc = 'images/drag-drop-tree/scorm_test.png';
                                            url    = 'professor.php?ctg=control_panel&op=scorm&set_type=scorm_test&id='+id;
                                            button = 'images/16x16/test_to_scorm.png';
                                        }
                                        el.down().src = 'images/others/progress1.gif';

                                        new Ajax.Request(url, {
                                                method:'get',
                                                asynchronous:true,
                                                onFailure: function (transport) {
                                                    el.down().writeAttribute({src:'images/16x16/delete.png', title: transport.responseText}).hide();
                                                    new Effect.Appear(el.down().identify());
                                                    window.setTimeout('Effect.Fade("'+el.down().identify()+'")', 10000);
                                                },
                                                onSuccess: function (transport) {
                                                    el.up().previous().previous().src = newSrc;
                                                    el.down().src = button;
                                                    img    = new Element('img', {src:'images/16x16/check.png'}).setStyle({verticalAlign:'middle'}).hide();
                                                    el.up().insert(img);
                                                    new Effect.Appear(img.identify());
                                                    window.setTimeout('Effect.Fade("'+img.identify()+'")', 2500);
                                                    }
                                            });
                                    }
                                {/literal}
                                </script>
                                <div id = "expand_collapse_div" expand = "true">
                                    <b><a id = "expand_collapse_link" href = "javascript:void(0)" onclick = "expandCollapse(this)">{$smarty.const._EXPANDALL}</a></b><br/>
                                </div>
                                <table>
                                    <tr><td>
                                        <ul id = "dhtmlContentTree" class = "dhtmlgoodies_tree">
                                            {$T_SCORM_TREE}
                                        </ul>
                                    </td></tr>
                                </table>

                            {/capture}
                            {eF_template_printInnerTable title = $smarty.const._SCORMOPTIONSFOR|cat:' &quot;'|cat:$T_CURRENT_LESSON->lesson.name|cat:'&quot;' data = $smarty.capture.t_scorm_tree_code image = '32x32/book_red.png' main_options = $T_TABLE_OPTIONS}
                        {/if}
                                </td></tr>
        {/capture}
    {elseif $T_OP == 'lesson_information'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel&op=lesson_information">'|cat:$smarty.const._LESSONINFORMATION|cat:'</a>'}
            {*moduleLessonInformation: Show lesson information*}
            {capture name = "moduleLessonInformation"}
                <tr><td class = "moduleCell">
                                {capture name = 't_lesson_info_code'}
                                    <fieldset>
                                        <legend>{$smarty.const._LESSONINFORMATION}</legend>
                                        {$T_LESSON_INFO_HTML}
                                    </fieldset>
                                    <fieldset>
                                        <legend>{$smarty.const._LESSONMETADATA}</legend>
                                        {$T_LESSON_METADATA_HTML}
                                    </fieldset>
{*
                                    <fieldset>
                                        <legend>{$smarty.const._LESSONAVATAR}</legend>
                                    </fieldset>
*}
                                {/capture}
                                {eF_template_printInnerTable title = $smarty.const._INFORMATIONFORLESSON|cat:' &quot;'|cat:$T_CURRENT_LESSON->lesson.name|cat:'&quot;' data = $smarty.capture.t_lesson_info_code image = '32x32/about.png'}
              </td></tr>
        {/capture}
    {elseif $T_OP == 'search'}
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
                                            {if !$T_CURRENT_USER->coreAccess.news || $T_CURRENT_USER->coreAccess.news == 'change'}
                                                <div class = "headerTools">
                                                    <img src = "images/16x16/add2.png" title = "{$smarty.const._ANNOUNCEMENTADD}" alt = "{$smarty.const._ANNOUNCEMENTADD}"/>
                                                    <a href = "news.php?op=insert" onclick = "eF_js_showDivPopup('{$smarty.const._ANNOUNCEMENTADD}', 1)" title = "{$smarty.const._ANNOUNCEMENTADD}" target = "POPUP_FRAME" style = "vertical-align:middle">{$smarty.const._ANNOUNCEMENTADD}</a>
                                            	</div>
                                            {/if}
                                                                                        
                                        	{include file = "news_list.tpl"}
                                        {/capture}

                                        {eF_template_printInnerTable title = $smarty.const._ANNOUNCEMENTS data = $smarty.capture.t_news_code image = '32x32/news.png'}
                                    </td></tr>
            {/capture}

    {elseif $T_OP == 'progress'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel&op=progress">'|cat:$smarty.const._PROGRESS|cat:'</a>'}
        {*moduleProgress: The Progress page*}
            {capture name = "moduleProgress"}
                                    <tr><td class = "moduleCell">

                                    {if $smarty.get.edit_user}
                                        {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href ='`$smarty.server.PHP_SELF`?ctg=control_panel&op=progress&edit_user=`$smarty.get.edit_user`'>`$smarty.const._PROGRESSFORUSER`: `$T_USER_LESSONS_INFO.name` `$T_USER_LESSONS_INFO.surname` (`$T_USER_LESSONS_INFO.login`)</a>"}
                                        {capture name = 't_edit_progress_code'}
                                            <fieldset>
                                                <legend>{$smarty.const._LESSONPROGRESS}</legend>
                                                <table width = "100%">
                                                    <tr><td>
                                                            <table>
                                                                <tr><td colspan = "2" class = "smallHeader leftAlign"><b>{$smarty.const._GENERICLESSONINFO}</b></td></tr>
                                                                <tr><td>{$smarty.const._TIMEINLESSON}:</td>
                                                                    <td>
                                                                        {if $T_USER_TIME.hours}{$T_USER_TIME.hours} {$smarty.const._HOURS}{/if}
                                                                        {if $T_USER_TIME.minutes}{$T_USER_TIME.minutes} {$smarty.const._MINUTES}{/if}
                                                                        {if $T_USER_TIME.seconds}{$T_USER_TIME.seconds} {$smarty.const._SECONDS}{/if}
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                    </td></tr>
                                                    <tr><td>
                                                            <table>
                                                                <tr><td colspan = "2" class = "smallHeader leftAlign"><b>{$smarty.const._CONTENT}</b></td></tr>
                                                                <tr><td>{$smarty.const._CONTENT}:&nbsp;</td>
                                                                    <td class = "progressCell" style = "vertical-align:top">
                                                                        <span class = "progressNumber">#filter:score-{$T_USER_LESSONS_INFO.content_progress}#%</span>
                                                                        <span class = "progressBar" style = "width:{$T_USER_LESSONS_INFO.content_progress}px;">&nbsp;</span>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                    </td></tr>
                                                    <tr><td>
                                                            <table class = "formElements">
                                                                <tr><td colspan = "100%" class = "smallHeader leftAlign"><b>{$smarty.const._TESTS}</b></td></tr>
                                                                {if !empty($T_USER_LESSONS_INFO.done_tests)}
                                                                <tr><td>{$smarty.const._USERAVERAGESCOREFORLESSON}:&nbsp;</td>
                                                                    <td class = "progressCell"  style = "vertical-align:top">
                                                                        <span class = "progressNumber">#filter:score-{$T_USER_LESSONS_INFO.tests_avg_score}#%</span>
                                                                        <span class = "progressBar" style = "width:{$T_USER_LESSONS_INFO.tests_avg_score}px;">&nbsp;</span>
                                                                    </td>
                                                                </tr>
                                                                {/if}
                                                               {foreach name = 'done_tests_list' item = "test" key = "id" from = $T_USER_LESSONS_INFO.done_tests}
                                                                <tr><td>&quot;{$test.name}&quot; ({$smarty.const._AVERAGESCOREON} {$test.times_done} {$smarty.const._EXECUTIONS|lower}):&nbsp;</td>
                                                                    <td class = "progressCell"  style = "vertical-align:top;width:100px;padding-right:10px;">
                                                                        <span class = "progressNumber">#filter:score-{$test.score}#%</span>
                                                                        <span class = "progressBar" style = "width:{$test.score}px;">&nbsp;</span>
                                                                    </td><td></td>
                                                                </tr>
                                                                <tr><td>&quot;{$test.name}&quot; ({$smarty.const._SCOREONLASTEXECUTION}):&nbsp;</td>
                                                                    <td class = "progressCell"  style = "vertical-align:top;width:100px;padding-right:10px;">
                                                                        <span class = "progressNumber">#filter:score-{$test.last_score}#%</span>
                                                                        <span class = "progressBar" style = "width:{$test.last_score}px;">&nbsp;</span>
                                                                    </td><td>
                                                                        <a href = "professor.php?ctg=tests&show_solved_test={$test.last_test_id}">
                                                                            <img src = "images/16x16/view.png" title = "{$smarty.const._VIEWTEST}" alt = "{$smarty.const._VIEWTEST}"></a>
                                                                    </td>
                                                                </tr>
                                                                {foreachelse}
                                                                <tr><td class = "emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
                                                                {/foreach}
                                                            </table>
                                                    </td></tr>
                                                    <tr><td>
                                                            <table>
                                                                <tr><td colspan = "100%" class = "smallHeader leftAlign"><b>{$smarty.const._PROJECTS}</b></td></tr>
                                                                {if !empty($T_USER_LESSONS_INFO.assigned_projects)}
                                                                <tr><td>{$smarty.const._PROJECTAVERAGESCOREFORLESSON}:&nbsp;</td>
                                                                    <td class = "progressCell"  style = "vertical-align:top">
                                                                        <span class = "progressNumber">#filter:score-{$T_USER_LESSONS_INFO.avg_grade_projects}#%</span>
                                                                        <span class = "progressBar" style = "width:{$T_USER_LESSONS_INFO.avg_grade_projects}px;">&nbsp;</span>
                                                                   </td>
                                                                </tr>
                                                                {/if}

                                                                {foreach name = 'done_projects_list' item = "project" key = "id" from = $T_USER_LESSONS_INFO.assigned_projects}
                                                                    <tr><td>{$project.title}:</td>
                                                                    {if $project.grade}
                                                                        <td class = "progressCell"  style = "vertical-align:top;width:100px;padding-right:10px;">
                                                                            <span class = "progressNumber">#filter:score-{$project.grade}#%</span>
                                                                            <span class = "progressBar" style = "width:{$project.grade}px;">&nbsp;</span>
                                                                        </td><td>
                                                                            {if $project.timestamp}(#filter:timestamp-{$project.timestamp}#) &nbsp;{/if}
                                                                        </td>
                                                                    {else}
                                                                        <td class = "emptyCategory" style = "white-space:nowrap" colspan = "2">{$smarty.const._PROJECTPENDING}</td>
                                                                    {/if}
                                                                    </tr>
                                                                {foreachelse}
                                                                    <tr><td class = "emptyCategory" width="100%">{$smarty.const._NODATAFOUND}</td></tr>
                                                                {/foreach}
                                                            </table>
                                                    </td></tr>
                                                </table>
                                            </fieldset>
                                            <fieldset>
                                                <legend>{$smarty.const._COMPLETELESSON}</legend>
                                                {$T_COMPLETE_LESSON_FORM.javascript}
                                                <form {$T_COMPLETE_LESSON_FORM.attributes}>
                                                    {$T_COMPLETE_LESSON_FORM.hidden}
                                                    <table class = "formElements">
                                                        <tr><td class = "labelCell">{$T_COMPLETE_LESSON_FORM.completed.label}&nbsp;:</td>
                                                            <td class = "elementCell">{$T_COMPLETE_LESSON_FORM.completed.html}</td></tr>
                                                        <tr><td class = "labelCell">{$T_COMPLETE_LESSON_FORM.score.label}&nbsp;:</td>
                                                            <td class = "elementCell">{$T_COMPLETE_LESSON_FORM.score.html}</td></tr>
                                                        {if $T_COMPLETE_LESSON_FORM.score.error}<tr><td></td><td class = "formError">{$T_COMPLETE_LESSON_FORM.score.error}</td></tr>{/if}
                                                        <tr><td class = "labelCell">{$T_COMPLETE_LESSON_FORM.comments.label}&nbsp;:</td>
                                                            <td class = "elementCell">{$T_COMPLETE_LESSON_FORM.comments.html}</td></tr>
                                                        {if $T_COMPLETE_LESSON_FORM.comments.error}<tr><td></td><td class = "formError">{$T_COMPLETE_LESSON_FORM.comments.error}</td></tr>{/if}
                                                        <tr><td colspan = "100%">&nbsp;</td></tr>
                                                        <tr><td></td><td>{$T_COMPLETE_LESSON_FORM.submit_lesson_complete.html}</td></tr>
                                                    </table>
                                                </form>
                                            </fieldset>
                                        {/capture}

                                        {eF_template_printInnerTable title = "`$smarty.const._PROGRESSFORUSER`: `$T_USER_LESSONS_INFO.name` `$T_USER_LESSONS_INFO.surname` (`$T_USER_LESSONS_INFO.login`)" data = $smarty.capture.t_edit_progress_code image = '32x32/book_blue_preferences.png'}

                                    {else}
                                            {capture name = 't_progress_code'}
<!--ajax:usersTable-->
                                                    <table style = "width:100%" class = "sortedTable" size = "{$T_USERS_SIZE}" sortBy = "0" id = "usersTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "professor.php?ctg=control_panel&op=progress&">
                                                        <tr class = "topTitle">
                                                            <td class = "topTitle" name = "login">{$smarty.const._LOGIN}</td>
                                                            <td class = "topTitle" name = "name">{$smarty.const._NAME}</td>
                                                            <td class = "topTitle" name = "surname">{$smarty.const._SURNAME}</td>
                                                            <td class = "topTitle centerAlign" name = "conditions_passed" >{$smarty.const._CONDITIONSCOMPLETED}</td>
                                                            <td class = "topTitle centerAlign" name = "completed" >{$smarty.const._LESSONSTATUS}</td>
                                                            <td class = "topTitle centerAlign" name = "score" >{$smarty.const._LESSONSCORE}</td>
                                                            <td class = "topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
                                                        </tr>
                                            {foreach name = 'users_progress_list' item = 'item' key = 'login' from = $T_USERS_PROGRESS}
                                                        <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$item.active}deactivatedTableElement{/if}">
                                                            <td>{$item.login}</td>
                                                            <td>{$item.name}</td>
                                                            <td>{$item.surname}</td>
                                                            <td style = "text-align:center">
                                                                {$item.conditions_passed}/{$item.total_conditions}
                                                            </td>
                                                            <td style = "text-align:center">
                                                                {if $item.completed}
                                                                    <img src = "images/16x16/check.png" title = "{$smarty.const._COMPLETED}" alt = "{$smarty.const._COMPLETED}" />
                                                                {elseif $item.lesson_passed}
                                                                    <img src = "images/16x16/contract.png" title = "{$smarty.const._CONDITIONSMET}" alt = "{$smarty.const._CONDITIONSMET}" />
                                                                {else}
                                                                    <img src = "images/16x16/forbidden.png" title = "{$smarty.const._NOTCOMPLETED}" alt = "{$smarty.const._NOTCOMPLETED}" />
                                                                {/if}
                                                            </td>
                                                            <td style = "text-align:center">{if $item.score}#filter:score-{$item.score}#%{/if}</td>
                                                            <td style = "text-align:center">
                                                        {if $T_MODULE_HCD_INTERFACE}
                                                                <a href = "{$smarty.server.PHP_SELF}?ctg=users&edit_user={$item.login}&add_evaluation=1">
                                                                    <img src="images/16x16/edit.png" title="{$smarty.const._NEWEVALUATION}" alt="{$smarty.const._NEWJOB}"/ border="0">
                                                                </a>
                                                        {/if}
                                                                <a href = "{$smarty.server.PHP_SELF}?ctg=control_panel&op=progress&edit_user={$item.login}" title = "{$smarty.const._VIEWUSERLESSONPROGRESS}">
                                                                    <img src = "images/16x16/clipboard.png" title = "{$smarty.const._VIEWUSERLESSONPROGRESS}" alt = "{$smarty.const._VIEWUSERLESSONPROGRESS}" border = "0"/>
                                                                </a>
                                                            </td>
                                                        </tr>
                                            {foreachelse}
                                                    <tr class = "{cycle values = "oddRowColor, evenRowColor"} defaultRowHeight"><td colspan = "100%" class = "emptyCategory centerAlign">{$smarty.const._NOUSERDATAFOUND}</td></tr>
                                            {/foreach}
                                                </table>
<!--/ajax:usersTable-->

                                            {/capture}
                                            {eF_template_printInnerTable title = $smarty.const._USERSPROGRESS data = $smarty.capture.t_progress_code image = '32x32/book_blue_preferences.png'}
                                    {/if}
                                    </td></tr>
            {/capture}

    {elseif isset($T_OP_MODULE)}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel&op='|cat:$T_OP|cat:'">'|cat:$T_OP_MODULE|cat:'</a>'}
        {capture name = "importedModule"}
                                <tr><td class = "moduleCell">
                                    {include file = $smarty.const.G_MODULESPATH|cat:$T_OP|cat:'/module.tpl'}
                                </td></tr>
        {/capture}

    {else}
        {*moduleIconLessonOptions: Print icon Table with lesson options*}
    {if $T_CURRENT_USER->coreAccess.control_panel != 'hidden'}
        {capture name = "moduleIconLessonOptions"}
                                <tr><td class = "moduleCell">
                                
                                        {eF_template_printIconTable title="`$smarty.const._LESSONOPTIONSFOR` <span class='innerTableName'>&quot;`$T_CURRENT_LESSON->lesson.name`&quot;</span>" columns=4 links=$T_LESSON_OPTIONS image='32x32/gears.png'}
                                </td></tr>
        {/capture}
    {/if}
        {*moduleNewsList: Lists announcements*}
        {capture name = "moduleNewsList"}
            {if $T_CURRENT_USER->coreAccess.news != 'hidden'}
                                <tr><td class = "moduleCell">
                                        {capture name='t_news_code'}
                                            {eF_template_printNews data=$T_NEWS}
                                        {/capture}

                                        {eF_template_printInnerTable title=$smarty.const._ANNOUNCEMENTS data=$smarty.capture.t_news_code image='32x32/news.png' navigation=$T_NEWS_NAV array=$T_NEWS options=$T_NEWS_OPTIONS link=$T_NEWS_LINK}
                                </td></tr>
            {/if}
        {/capture}

        {*moduleForumList: Lists recent Forum messages*}
        {if ($T_CURRENT_LESSON->options.forum) && $T_FORUM_MESSAGES}
            {capture name = "moduleForumList"}
                            <tr><td class = "moduleCell">
                                    {capture name='t_forum_messages_code'}
                                        {eF_template_printForumMessages data=$T_FORUM_MESSAGES forum_lessons_ID = $T_FORUM_LESSONS_ID limit = 3}
                                    {/capture}

                                    {eF_template_printInnerTable title=$smarty.const._RECENTMESSAGESATFORUM data=$smarty.capture.t_forum_messages_code image='32x32/messages.png'  options=$T_FORUM_OPTIONS link=$T_FORUM_LINK}
                            </td></tr>
            {/capture}
        {/if}

        {if $T_PERSONAL_MESSAGES}
        {*modulePersonalMessagesList: Lists Unread personal messages*}
        {capture name = "modulePersonalMessagesList"}
                            <tr><td class = "moduleCell">
                                    {capture name='t_personal_messages_code'}
                                        {eF_template_printPersonalMessages data=$T_PERSONAL_MESSAGES}
                                    {/capture}

                                    {eF_template_printInnerTable title=$smarty.const._RECENTUNREADPERSONALMESSAGES data=$smarty.capture.t_personal_messages_code image='32x32/mail2.png' options=$T_PERSONAL_MESSAGES_OPTIONS link=$T_PERSONAL_MESSAGES_LINK}
                            </td></tr>
        {/capture}
        {/if}

        {*moduleComments: Lists recent Comments*}
        {if ($T_CURRENT_LESSON->options.comments) && $T_COMMENTS}
            {capture name = "moduleComments"}
                            <tr><td class = "moduleCell">
                                    {capture name='t_comments_code'}
                                        {eF_template_printComments data=$T_COMMENTS}
                                    {/capture}

                                    {eF_template_printInnerTable title=$smarty.const._RECENTCOMMENTS data=$smarty.capture.t_comments_code image='32x32/note.png' link=$T_COMMENTS_LINK}
                            </td></tr>
            {/capture}
        {/if}

        {if $T_COMPLETED_TESTS}
        {*moduleDoneTests: Lists students done questions*}
        {capture name = "moduleDoneTests"}
                            <tr><td class = "moduleCell">
                                    {capture name='t_done_tests_code'}
                                        <table border = "0" width = "100%">
                                        {section name = 'completed_test' loop = $T_COMPLETED_TESTS max = 10}
                                            <tr><td><a href = "{$smarty.server.PHP_SELF}?ctg=tests&show_solved_test={$T_COMPLETED_TESTS[completed_test].id}" style = "float:left">{$T_COMPLETED_TESTS[completed_test].name|eF_truncate:50}</a><span style = "float:right">#filter:user_login-{$T_COMPLETED_TESTS[completed_test].users_LOGIN}#, #filter:timestamp_interval-{$T_COMPLETED_TESTS[completed_test].timestamp}# {$smarty.const._AGO}</span></td></tr>
                                        {/section}
                                        </table>
                                    {/capture}

                                    {eF_template_printInnerTable title=$smarty.const._RECENTLYCOMPLETEDTESTS data=$smarty.capture.t_done_tests_code image='32x32/text_marked.png' options = $T_DONE_QUESTIONS_OPTIONS link=$T_DONE_QUESTIONS_LINK}
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
                            {eF_template_printInnerTable title=$calendar_title data=$smarty.capture.t_calendar_code image='32x32/calendar.png' options=$T_CALENDAR_OPTIONS link=$T_CALENDAR_LINK}
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
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=content">'|cat:$smarty.const._CONTENT|cat:'</a>'}
    {if $T_OP == 'copy_content'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=content&op=copy_content">'|cat:$smarty.const._COPYFROMANOTHERLESSON|cat:'</a>'}
        {*moduleCopyContent: Content tree to change order*}
        {capture name = "moduleCopyContent"}
                                <tr><td class = "moduleCell">
                                        {capture name = 't_copy_content_code'}
                                                <table style = "width:100%">
                                                    <tr><td class = "labelCell">{$smarty.const._SELECTLESSONTOCOPYFROM}:&nbsp;</td>
                                                        <td class = "elementCell">
                                                        <select name = 'user_lessons' onchange = "document.location='{$smarty.server.PHP_SELF}?ctg=content&op=copy_content&lesson='+this.options[this.selectedIndex].value">
                                                            <option value = "0">{$smarty.const._SELECTLESSON}</option>
                                                        {foreach name = 'directions_list' key = key1 item = direction from = $T_USER_LESSONS}
                                                            {foreach name = "lessons_list" key = key2 item = lesson from = $direction}
                                                            <option value = "{$lesson.id}" {if $lesson.id == $smarty.get.lesson}selected{/if}>{$key1} -> {$lesson.name}</option>
                                                            {/foreach}
                                                        {/foreach}
                                                        </select>
                                                    </td></tr>
                                                </table>
                                                <br/>
                                            {if $smarty.get.lesson}
                                                <table width = "100%">
                                                    <tr><td style = "vertical-align:top;width:50%">
                                                        {eF_template_printInnerTable title = $smarty.const._DRAGAUNITTOCOPY data = $T_SOURCE_TREE image = "32x32/book_open.png" alt = '<span class = "emptyCategory">'|cat:$smarty.const._NOCONTENTFOUND|cat:'</span>'}
                                                    </td><td style = "vertical-align:top;">
                                                        {eF_template_printInnerTable title = $smarty.const._DROPAUNITTOCOPY data = $T_CONTENT_TREE image = "32x32/book_open.png" alt = '<span class = "emptyCategory">'|cat:$smarty.const._NOCONTENTFOUND|cat:'</span>'}<table>
                                                                <tr><td colspan = "2" style = "text-align:center">&nbsp;</td></tr>
                                                                <tr><td colspan = "2" style = "text-align:center"><input id = "save_button" class = "flatButton" type = "button" onclick = "saveTree()" value = "{$smarty.const._SAVECHANGES}" /></td></tr>
                                                        </table>
                                                    </td></tr>
                                                </table>
                                                <script>{literal}
                                                <!--
												var TransferedNodes = "";
                                                    function saveTree() {
                                                        Element.extend($('save_button'));
                                                        progressImg = new Element('img', {id:'progress_image', src:'images/others/progress1.gif'}).setStyle({position:'absolute'});
                                                        progressImg.style.top      = Element.positionedOffset($('save_button')).top + 1 + 'px';
                                                        progressImg.style.left     = Element.positionedOffset($('save_button')).left + 6 + Element.getDimensions($('save_button')).width + 'px';
                                                        document.body.appendChild(progressImg);
                                                        //alert(treeObj.getNodeOrders());
                                                        new Ajax.Request('{/literal}{$smarty.server.PHP_SELF}{literal}?ctg=content&op=copy_content&lesson={/literal}{$smarty.get.lesson}{literal}&node_orders='+treeObj.getNodeOrders()+'&transfered='+TransferedNodes, {
                                                                method:'get',
                                                                onFailure: function (transport) {
                                                                    progressImg.hide();
                                                                    progressImg.writeAttribute({src:'images/16x16/delete.png', title: transport.responseText}).hide();
                                                                    progressImg.onclick = function () {alert(transport.responseText);};
                                                                    new Effect.Appear('progress_image');
                                                                    //window.setTimeout('Effect.Fade("progress_image")', 10000);
                                                                },
                                                                onSuccess: function (transport) {
																	TransferedNodes = transport.responseText;
	
																	//alert(TransferedNodes);
                                                                    progressImg.hide();
                                                                    progressImg.setAttribute('src', 'images/16x16/check.png');
                                                                    new Effect.Appear('progress_image');
                                                                    window.setTimeout('Effect.Fade("progress_image")', 2500);
                                                                }});
                                                    }
                                                //-->{/literal}
                                                </script>
                                            {/if}
                                        {/capture}
                                        {eF_template_printInnerTable title=$smarty.const._COPYFROMANOTHERLESSON data=$smarty.capture.t_copy_content_code image='32x32/folder_into.png'}
                                </td></tr>
        {/capture}
    {elseif $T_OP == 'file_manager'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=content&op=file_manager">'|cat:$smarty.const._FILES|cat:'</a>'}
        {*moduleFileManager: The file manager page*}
        {capture name = "moduleFileManager"}
            {if $T_FILE_METADATA}
                    <tr><td class = "moduleCell">
                        {capture name = 't_file_info_code'}
                            <fieldset>
                                <legend>{$smarty.const._FILEMETADATA}</legend>
                                {$T_FILE_METADATA_HTML}
                            </fieldset>
                        {/capture}
                        {eF_template_printInnerTable title = $smarty.const._INFORMATIONFORFILE|cat:' &quot;'|cat:$T_FILE_METADATA.name|cat:'&quot;' data = $smarty.capture.t_file_info_code image = '32x32/about.png'}
                    </td></tr>
            {else}
                    <tr><td class = "moduleCell">
                        {capture name = 't_file_manager_code'}
                            {$T_FILE_MANAGER}
                        {/capture}
                        {eF_template_printInnerTable title=$smarty.const._FILEMANAGER data=$smarty.capture.t_file_manager_code image='32x32/folder_view.png'}
                    </td></tr>
            {/if}
        {/capture}
    {elseif $T_OP == 'unit_order'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=content&op=unit_order">'|cat:$smarty.const._LINEARCONTENT|cat:'</a>'}
        {*moduleUnitOrder: Content tree to change order*}
        {capture name = "moduleUnitOrder"}
                                <tr><td class = "moduleCell">
                                        {capture name = "content_tree"}
                                                {$T_UNIT_ORDER_TREE}
                                        {/capture}
                                        {eF_template_printInnerTable title = $smarty.const._DRAGAUNITTOCHANGEITSPOSITION data = $smarty.capture.content_tree image = "32x32/book_blue.png" alt = '<span class = "emptyCategory">'|cat:$smarty.const._NOCONTENTFOUND|cat:'</span>' options = $T_TABLE_OPTIONS}
                                        <input class = "flatButton" type = "button" onclick = "saveTree()" value = "{$smarty.const._SAVECHANGES}" />
                                        <input class = "flatButton" type = "button" onclick = "window.location.reload()" value = "{$smarty.const._UNDOCHANGES}" id = "reload_button"/>
                                        {*<input class = "flatButton" type = "button" onclick = "if (confirm ('{$smarty.const._ORDERWILLPERMANENTLYCHANGE}')) repairTree();" value = "{$smarty.const._REPAIRTREE}" />*}
                                        <script>{literal}
                                        <!--
                                            function repairTree() {
                                                progressImg = new Element('img', {id:'progress_image', src:'images/others/progress1.gif'}).setStyle({position:'absolute'});
                                                progressImg.style.top      = Element.positionedOffset($('reload_button')).top + 1 + 'px';
                                                progressImg.style.left     = Element.positionedOffset($('reload_button')).left + 6 + Element.getDimensions($('reload_button')).width + 'px';
                                                document.body.appendChild(progressImg);
                                                new Ajax.Request('{/literal}{$smarty.server.PHP_SELF}{literal}?ctg=content&op=unit_order&ajax=1&repair_tree=true', {
                                                        method:'get',
                                                        onSuccess: function (transport) {
                                                            location = location+'&message='+transport.responseText+'&message_type=success';
                                                        }
                                                    });
                                            }
                                            function saveTree() {
                                                //$('dimmer').show();
                                                if (!$('progress_image')) {
                                                    progressImg = new Element('img', {id:'progress_image', src:'images/others/progress1.gif'}).setStyle({position:'absolute'});
                                                    document.body.appendChild(progressImg);
                                                } else {
                                                    progressImg     = $('progress_image');
                                                    progressImg.src = 'images/others/progress1.gif';
                                                    progressImg.show();
                                                }
                                                progressImg.style.top      = Element.positionedOffset($('reload_button')).top + 1 + 'px';
                                                progressImg.style.left     = Element.positionedOffset($('reload_button')).left + 6 + Element.getDimensions($('reload_button')).width + 'px';

                                                new Ajax.Request('{/literal}{$smarty.server.PHP_SELF}{literal}?ctg=content&op=unit_order&ajax=1&node_orders='+treeObj.getNodeOrders()+'&delete_nodes='+treeObj.getDeletedUnits()+'&activate_nodes='+treeObj.getActivatedUnits()+'&deactivate_nodes='+treeObj.getDeactivatedUnits(), {
                                                        method:'get',
                                                        onSuccess: function (transport) {
                                                            progressImg.hide();
                                                            progressImg.setAttribute('src', 'images/16x16/check.png');
                                                            new Effect.Appear('progress_image');
                                                            window.setTimeout('Effect.Fade("progress_image")', 2500);
                                                            }
                                                    });
                                            }
                                        //-->{/literal}
                                        </script>
                                </td></tr>
        {/capture}
    {elseif $T_OP == 'metadata'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=content&view_unit='|cat:$smarty.get.view_unit|cat:'">'|cat:$T_CURRENT_UNIT.name|cat:'</a>&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=content&op=metadata&view_unit='|cat:$smarty.get.view_unit|cat:'">'|cat:$smarty.const._CONTENTMETADATA|cat:'</a>'}
            {*moduleContentMetadata: Show content metadata*}
            {capture name = "moduleContentMetadata"}
                <tr><td class = "moduleCell">
                                {capture name = 't_content_info_code'}
                                    <fieldset>
                                        <legend>{$smarty.const._CONTENTMETADATA}</legend>
                                        {$T_CONTENT_METADATA_HTML}
                                    </fieldset>
                                {/capture}
                                {eF_template_printInnerTable title = $smarty.const._METADATAFORUNIT|cat:' &quot;'|cat:$T_CURRENT_UNIT.name|cat:'&quot;' data = $smarty.capture.t_content_info_code image = '32x32/about.png'}
              </td></tr>
        {/capture}

    {else}
        {if $smarty.get.add_unit || $smarty.get.edit_unit}
            {*moduleInsertContent: Print the page that is used to add or update content*}
            {if $smarty.get.add_unit}
                {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=content&add_unit=1">'|cat:$smarty.const._ADDCONTENT|cat:'</a>'}
            {else}
                {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=content&edit_unit='|cat:$smarty.get.edit_unit|cat:'">'|cat:$smarty.const._EDITCONTENT|cat:'&nbsp;<span class="innerTableName">&quot;'|cat:$T_INSERT_CONTENT_FORM.name.value|cat:'&quot;</span></a>'}
            {/if}
            {capture name = "moduleInsertContent"}
                                <tr><td class = "moduleCell">
                                    {capture name = 't_insert_content_code'}
                                        {$T_INSERT_CONTENT_FORM.javascript}
                                        <form {$T_INSERT_CONTENT_FORM.attributes}>
                                            {$T_INSERT_CONTENT_FORM.hidden}
                                            <table class = "formElements" style = "margin-left:0px;">
                                                <tr><td class = "labelCell">{$T_INSERT_CONTENT_FORM.name.label}:&nbsp;</td>
                                                    <td class = "elementCell">{$T_INSERT_CONTENT_FORM.name.html}</td>
                                                </tr>
                                                {if $T_INSERT_CONTENT_FORM.name.error}<tr><td></td><td class = "formError">{$T_INSERT_CONTENT_FORM.name.error}</td></tr>{/if}
                                            {if !$smarty.get.edit_unit}
                                                <tr><td class = "labelCell">{$T_INSERT_CONTENT_FORM.parent_content_ID.label}:&nbsp;</td>
                                                    <td class = "elementCell">{$T_INSERT_CONTENT_FORM.parent_content_ID.html}</td></tr>
                                                {if $T_INSERT_CONTENT_FORM.parent_content_ID.error}<tr><td></td><td class = "formError">{$T_INSERT_CONTENT_FORM.parent_content_ID.error}</td></tr>{/if}
                                            {/if}
                                            {if !$T_SCORM}
                                                <tr><td class = "labelCell">{$T_INSERT_CONTENT_FORM.ctg_type.label}:&nbsp;</td>
                                                    <td class = "elementCell">{$T_INSERT_CONTENT_FORM.ctg_type.html}</td></tr>
                                                {if $T_INSERT_CONTENT_FORM.ctg_type.error}<tr><td></td><td class = "formError">{$T_INSERT_CONTENT_FORM.ctg_type.error}</td></tr>{/if}
                                            {/if}
                                                <tr><td class = "labelCell">{$T_INSERT_CONTENT_FORM.hide_complete_unit.label}:&nbsp;</td>
                                                    <td class = "elementCell">{$T_INSERT_CONTENT_FORM.hide_complete_unit.html}</td></tr>
                                                <tr><td class = "labelCell">{$T_INSERT_CONTENT_FORM.hide_navigation.label}:&nbsp;</td>
                                                    <td class = "elementCell">{$T_INSERT_CONTENT_FORM.hide_navigation.html}</td></tr>
                                                <tr><td class = "labelCell">{$T_INSERT_CONTENT_FORM.indexed.label}:&nbsp;</td>
                                                    <td class = "elementCell">{$T_INSERT_CONTENT_FORM.indexed.html}&nbsp;<span class = "infoCell">
                                                    <img src = "images/16x16/help2.png" alt = "{$smarty.const._CLICKFORHELP}" title = "{$smarty.const._CLICKFORHELP}" onclick = "eF_js_showHideDiv(this, 'help_explain', event)"><div id = 'help_explain' onclick = "eF_js_showHideDiv(this, 'help_explain', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:750px;position:absolute;display:none">{$smarty.const._DIRECTLACCESSIBLEEXPLANATION}{$smarty.const.G_SERVERNAME}view_resource.php?type=content&id={if $smarty.get.edit_unit}{$smarty.get.edit_unit}{else}&lt;unit_id&gt;{/if}</div></span></td></tr>
                                            {if $T_INSERT_CONTENT_FORM.complete_question}
                                                <tr><td class = "labelCell">{$T_INSERT_CONTENT_FORM.complete_question.label}:&nbsp;</td>
                                                    <td class = "elementCell">{$T_INSERT_CONTENT_FORM.complete_question.html}&nbsp;{$T_INSERT_CONTENT_FORM.questions.html}</td></tr>
                                            {/if}
                                                <tr><td class = "labelCell">{$T_INSERT_CONTENT_FORM.pdf_check.label}:&nbsp;</td>
                                                    <td class = "elementCell">{$T_INSERT_CONTENT_FORM.pdf_check.html}</td></tr>
                                                <tr style="display:none;" id="pdf_content"><td class = "labelCell">{$T_INSERT_CONTENT_FORM.pdf_content.label}:&nbsp;</td>
                                                    <td class = "elementCell">{$T_INSERT_CONTENT_FORM.pdf_content.html}</td></tr>
                                                <tr style="display:none;" id="pdf_upload"><td class = "labelCell">{$T_INSERT_CONTENT_FORM.pdf_upload.label}:&nbsp;</td>
                                                    <td class = "elementCell">{$T_INSERT_CONTENT_FORM.pdf_upload.html}</td></tr>

                                            {if $T_SCORM}
                                                <tr><td class = "labelCell">{$T_INSERT_CONTENT_FORM.scorm_size.label}:&nbsp;</td>
                                                    <td class = "elementCell">{$T_INSERT_CONTENT_FORM.scorm_size.html} px</td></tr>
                                                {if $T_INSERT_CONTENT_FORM.scorm_size.error}<tr><td></td><td class = "formError">{$T_INSERT_CONTENT_FORM.scorm_size.error}</td></tr>{/if}
                                                <tr><td></td><td class = "infoCell">{$smarty.const._EXPLICITSIZEEXPLANATION}</td></tr>
                                            {/if}
                                            </table><br/>
                                            {if !$T_SCORM}
                                            <table style = "width:100%;margin-left:0px;" id="nonPdfTable">

                                            <tr><td style = "vertical-align:middle" id="toggleeditor_cell1"><a title="{$smarty.const._OPENCLOSEFILEMANAGER}" href="javascript:void(0)" onclick="toggle_file_manager();" style = "vertical-align:middle"><img id="arrow_down" src="images/16x16/navigate_down.png" border="0" alt="{$smarty.const._OPENCLOSEFILEMANAGER}" title="{$smarty.const._OPENCLOSEFILEMANAGER}" style="vertical-align:middle"/>&nbsp;<span id="open_manager" style = "vertical-align:middle">{$smarty.const._OPENFILEMANAGER}</span></a>&nbsp;&nbsp;<a href="javascript:toogleEditorMode('mce_editor_0');" id="toggleeditor_link"><img src = "images/16x16/replace2.png" title = "{$smarty.const._TOGGLEHTMLEDITORMODE}" alt = "{$smarty.const._TOGGLEHTMLEDITORMODE}" style = "vertical-align:middle" border = "0"/>&nbsp;<span  style = "vertical-align:middle">{$smarty.const._TOGGLEHTMLEDITORMODE}</span></a></td></tr>
                                            <tr><td id="filemanager_cell"></td></tr>
                                            <tr><td id="toggleeditor_cell2"></td></tr>

                                                <tr  id = "editorRow">
                                                    <td class = "elementCell">
                                                        {$T_INSERT_CONTENT_FORM.data.html}
                                                    </td></tr>
                                                {if $T_INSERT_CONTENT_FORM.data.error}<tr><td></td><td class = "formError">{$T_INSERT_CONTENT_FORM.data.error}</td></tr>{/if}
                                            </table>
                                            {/if}
                                            <table class = "formElements" style = "margin-left:0px;" style = "width:100%;">
                                                <tr><td>&nbsp;</td></tr>
                                                <tr><td  class = "submitCell">
                                                    {$T_INSERT_CONTENT_FORM.submit_insert_content.html}</td></tr>
                                            </table>
                                        </form>
                                            <table><tr><td id="fmInitial">
                                                <div  id="filemanager_div" style="display:none;">
                                                    {$T_FILE_MANAGER}
                                                    <br/>
                                                </div>
                                                </td></tr></table>
                                    {if $T_EDITPDFCONTENT}
                                    <script type="text/javascript">
                                        {literal}

                                            $('pdf_upload').toggle();
                                            $('pdf_content').toggle();
                                            $('nonPdfTable').toggle();
                                        {/literal}
                                    </script>
                                    {/if}
                                       <script type="text/javascript">
                                        {literal}

                                           var tinyMCEmode = true;
                                                function toogleEditorMode(sEditorID) {
                                                    try {
                                                        if(tinyMCEmode) {
                                                            tinyMCE.removeMCEControl(tinyMCE.getEditorId(sEditorID));
                                                            tinyMCEmode = false;
                                                        } else {
                                                            mceAddControlDynamic(sEditorID, 'editor_content_data' ,'mceEditor');
                                                            tinyMCEmode = true;
                                                        }
                                                    } catch(e) {
                                                        alert('editor error');
                                                    }
                                                }

                                        //-->{/literal}
                                        </script>

                                    {/capture}
                                    {if $smarty.get.add_unit}
                    					{eF_template_printInnerTable title=$smarty.const._NEWUNITOPTIONS data=$smarty.capture.t_insert_content_code image='32x32/edit.png' }
            						{else}
                    					{eF_template_printInnerTable title="`$smarty.const._UNITOPTIONSFOR` <span class='innerTableName'>&quot;`$T_INSERT_CONTENT_FORM.name.value`&quot;</span>" data=$smarty.capture.t_insert_content_code image='32x32/edit.png' }
                    					
            						{/if}

                                    <script>
                                    {literal}

                                    function insertatcursor(myField, myValue) {

                                        if (document.selection) {
                                            myField.focus();
                                            sel = document.selection.createRange();
                                            sel.text = myValue;
                                        }
                                        else if (myField.selectionStart || myField.selectionStart == '0') {
                                            var startPos = myField.selectionStart;
                                            var endPos = myField.selectionEnd;
                                            myField.value = myField.value.substring(0, startPos)+ myValue+ myField.value.substring(endPos, myField.value.length);
                                        } else {
                                            myField.value += myValue;
                                        }
                                    }

                                    function insert_editor(element, id) {
                                    {/literal}{if !$smarty.get.edit_unit}{literal}
                                        var url = 'professor.php?ctg=content&add_unit=1&postAjaxRequest=1';
                                    {/literal}{else}{literal}
                                        var url = 'professor.php?ctg=content&edit_unit={/literal}{$smarty.get.edit_unit}{literal}&postAjaxRequest=1';
                                    {/literal}{/if}{literal}
                                        new Ajax.Request(url, {
                                            method:'get',
                                            asynchronous:true,
                                            parameters: {file_id: id, editor_mode: tinyMCEmode},
                                            onSuccess: function (transport) {
                                                if(tinyMCEmode) {
                                                    tinyMCE.execInstanceCommand('mce_editor_0','mceInsertContent', false , transport.responseText);
                                                }else {
                                                    insertatcursor(window.document.update_content_form.data, transport.responseText);
                                                }
                                            }
                                        });


                                            }
                                        //-->   {/literal}
                                            </script>

                                    <script>
                                    {literal}
                                    var file_manager_hidden = 1;
                                    function toggle_file_manager(){
                                        if(file_manager_hidden){
                                            $('filemanager_cell').insert($('filemanager_div'));
                                            $('filemanager_div').style.display = "block";
                                            $('arrow_down').src = "images/16x16/navigate_up.png";
                                            $('toggleeditor_cell2').insert($('toggleeditor_link'));
                                            $('open_manager').update('{/literal}{$smarty.const._CLOSEFILEMANAGER}{literal}');
                                            file_manager_hidden = 0;
                                        }else{
                                            $('filemanager_div').style.display = "none";
                                            $('fmInitial').insert($('filemanager_div'));
                                            $('toggleeditor_cell1').insert($('toggleeditor_link'));
                                            $('arrow_down').src = "images/16x16/navigate_down.png";
                                            $('open_manager').update('{/literal}{$smarty.const._OPENFILEMANAGER}{literal}');
                                            file_manager_hidden = 1;
                                        }
                                    }
                                //-->   {/literal}
                                    </script>

                                </td></tr>
            {/capture}
        {else}
            {section name = 'parents_list' loop = $T_PARENT_LIST step = "-1"}
                {assign var = "truncated_name" value = $T_PARENT_LIST[parents_list].name|eF_truncate:40}
                {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = \"titleLink\" href = \"`$smarty.server.PHP_SELF`?ctg=content&view_unit=`$T_PARENT_LIST[parents_list].id`\" title = \"`$T_PARENT_LIST[parents_list].name`\">`$truncated_name`</a>"}
            {/section}
            {*moduleShowUnit: Print the page that shows the Unit content*}
            {capture name = "moduleShowUnit"}
                            <tr><td class = "moduleCell">
                            {if isset($T_SHOW_TOOLS)}
                               {assign var = "sidebar_right" value = '<div style="text-align:right;"><div id="sidebarslideup" style="display: block;" class="sidebarslidecontrol" onclick="sidebarUp();"><img src="images/16x16/navigate_right.png" style="vertical-align:middle" alt="'|cat:$smarty.const._HIDESIDEBAR|cat:'" title="'|cat:$smarty.const._HIDESIDEBAR|cat:'"/></div><div  id="sidebarslidedown" class="sidebarslidecontrol" onclick="sidebarDown();" style="display: none;"><img src="images/16x16/navigate_left.png" style="vertical-align:middle" alt="'|cat:$smarty.const._SHOWSIDEBAR|cat:'" title="'|cat:$smarty.const._SHOWSIDEBAR|cat:'"/></div></div>'}
                            {/if}
                                        <table border = "0" width = "100%">
                            {if !isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change'}
                                                <tr><td colspan = "2">
                                                        <table>
                                                            <tr><td style = "border-right:1px solid black;">
                                                                <a href="{$smarty.server.PHP_SELF}?ctg=content&edit_unit={$T_UNIT.id}"><img border="0" src="images/16x16/edit.png" style="vertical-align:middle" alt="{$smarty.const._UPDATEUNIT}" title="{$smarty.const._UPDATEUNIT}"></a>
                                                                <a href="{$smarty.server.PHP_SELF}?ctg=content&edit_unit={$T_UNIT.id}" style = "vertical-align:middle">{$smarty.const._UPDATEUNIT}</a>&nbsp;
                                                            </td><td style = "border-right:1px solid black;">
                                                                &nbsp;<a href="{$smarty.server.PHP_SELF}?ctg=content&add_unit=1"><img border="0" src="images/16x16/add2.png" style="vertical-align:middle" alt="{$smarty.const._CREATEUNIT}" title="{$smarty.const._CREATEUNIT}"></a>
                                                                <a href="{$smarty.server.PHP_SELF}?ctg=content&add_unit=1" style = "vertical-align:middle">{$smarty.const._CREATEUNIT}</a>&nbsp;
                                                            </td><td>
                                                                &nbsp;<a href="{$smarty.server.PHP_SELF}?ctg=content&add_unit=1{if $T_UNIT.id}&view_unit={$T_UNIT.id}{/if}"><img border="0" src="images/16x16/add2.png" style="vertical-align:middle" alt="{$smarty.const._CREATESUBUNIT}" title="{$smarty.const._CREATESUBUNIT}">
                                                                <a href="{$smarty.server.PHP_SELF}?ctg=content&add_unit=1{if $T_UNIT.id}&view_unit={$T_UNIT.id}{/if}" style = "vertical-align:middle">{$smarty.const._CREATESUBUNIT}</a>
                                                            </td>
                                                            </tr>
                                                        </table>
                                                </td></tr>
                                                <tr><td colspan = "2" class = "horizontalSeparator"></td></tr>
                            {/if}
                                            <tr><td></td>
                                                <td align = "right">{eF_template_printPreviousNext previous = $T_PREVIOUS_UNIT next = $T_NEXT_UNIT}</td></tr>
                                            <tr><td colspan = "2" id = "contentCell" class = "contentCell">{$T_UNIT.data}</td></tr>
                                            <tr><td colspan = "2" align = "right">
                                                    <table id = "navigationDownTable" style = "display:none;">
                                                        <tr><td>
                                                            <a href = "javascript:void(0)" onclick = "$('mainTable').scrollTo()"><img border = "0" src = "images/24x24/navigate_up.png" title = "{$smarty.const._BACKTOTOP}" alt = "{$smarty.const._BACKTOTOP}" /></a>
                                                            {eF_template_printPreviousNext previous = $T_PREVIOUS_UNIT next = $T_NEXT_UNIT}
                                                        </td></tr>
                                                    </table>
                                                </td></tr>
                                        </table>
                                        <br/>
                                        <br/>
                            {if $T_CURRENT_LESSON->options.comments && $T_COMMENTS}
                                {capture name = 't_comments_code'}
                                        <table border = "0" width = "100%">
                                                <tr><td style = "width:1%">
                                       {if !isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change'}
                                                    <a href = "add_comment.php?content_ID={$T_UNIT.id}&op=insert", onclick = "eF_js_showDivPopup('{$smarty.const._ADDCOMMENT}', new Array('500px','300px'))" target = "POPUP_FRAME">
                                                        <img border = "0" src = "images/16x16/add2.png" title = "{$smarty.const._ADDCOMMENT}" alt = "{$smarty.const._ADDCOMMENT}" style = "vertical-align:middle"/>&nbsp;{$smarty.const._ADDCOMMENT}
                                                    </a>
                                        {/if}
                                                </td></tr>
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
                                        {if !isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change'}
                                                    <a href = "add_comment.php?id={$T_COMMENTS[comments_list].id}&op=change", onclick = "eF_js_showDivPopup('{$smarty.const._CORRECTION}', new Array('500px','300px'))" target = "POPUP_FRAME"><img src = "images/16x16/edit.png" alt = "{$smarty.const._CORRECTION}" title = "{$smarty.const._CORRECTION}" border = "0"/></a>&nbsp;
                                                    <a href = "add_comment.php?id={$T_COMMENTS[comments_list].id}&op=delete" target = "POPUP_FRAME" onclick = "return confirm ('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')"><img src = "images/16x16/delete.png" alt = "{$smarty.const._DELETE}" title = "{$smarty.const._DELETE}" border = "0"/></a>
                                        {/if}
                                                </td></tr>
                                    {/section}
                                        </table>
                                        <div id = "comments_div" style = "display:none"></div>
                            {/capture}
                            {eF_template_printInnerTable title=$smarty.const._COMMENTS data=$smarty.capture.t_comments_code image='32x32/note.png'}
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
                                {counter name = "unit_operations"}. <a href = "add_comment.php?content_ID={$T_UNIT.id}&op=insert", onclick = "eF_js_showDivPopup('{$smarty.const._ADDCOMMENT}', new Array('500px','300px'))" target = "POPUP_FRAME">{$smarty.const._ADDCOMMENT}</a>
                            {/if}
            {/capture}
            {eF_template_printSide title = $smarty.const._UNITOPERATIONS data = $smarty.capture.UnitOperationsBar id = 'unit_operations'}
        {/capture}



        {/if}
    {/if}

{elseif ($T_CTG == 'scheduling')}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=scheduling">'|cat:$smarty.const._SCHEDULING|cat:'</a>'}
        {capture name = "moduleInsertPeriod"}
                            <tr><td class = "moduleCell">
                                {capture name='t_insert_period_code'}
                                    {$T_ADD_PERIOD_FORM.javascript}
                                    <form {$T_ADD_PERIOD_FORM.attributes}>
                                        {$T_ADD_PERIOD_FORM.hidden}
                                        <table class = "formElements" style = "margin-left:0px">
                                            <tr><td class = "labelCell">{$smarty.const._CURRENTSCHEDULE}:&nbsp;</td>
                                            {if $T_CURRENT_LESSON->lesson.from_timestamp}
                                                <td class = "elementCell">
                                                    {$smarty.const._FROM} #filter:timestamp_time-{$T_CURRENT_LESSON->lesson.from_timestamp}# {$smarty.const._TO} #filter:timestamp_time-{$T_CURRENT_LESSON->lesson.to_timestamp}# &nbsp;&nbsp;&nbsp;
                                                    {if !isset($T_CURRENT_USER->coreAccess.settings) || $T_CURRENT_USER->coreAccess.settings == 'change'}<a href = "javascript:void(0)" onclick = "deleteSchedule(this)"><img src = "images/16x16/delete.png" title = "{$smarty.const._DELETESCHEDULE}" alt = "{$smarty.const._DELETESCHEDULE}" border = "0" style = "vertical-align:top"></a>{/if}
                                                </td>
                                            {else}
                                                <td class = "elementCell emptyCategory">{$smarty.const._NOSCHEDULESET}</td>
                                            {/if}
                                            </tr>
                                        {if !isset($T_CURRENT_USER->coreAccess.settings) || $T_CURRENT_USER->coreAccess.settings == 'change'}
                                            <tr><td class = "labelCell">{$smarty.const._FROM}:&nbsp;</td>
                                                <td class = "elementCell">{eF_template_html_select_date prefix="from_" time=$T_FROM_TIMESTAMP start_year="-2" end_year="+2" field_order = 'YMD'} {$smarty.const._TIME}: {html_select_time prefix="from_" time = $T_FROM_TIMESTAMP display_seconds = false}</td></tr>
                                            <tr><td class = "labelCell">{$smarty.const._TO}:&nbsp;</td>
                                                <td class = "elementCell">{eF_template_html_select_date prefix="to_"   time=$T_TO_TIMESTAMP   start_year="-2" end_year="+2" field_order = 'YMD'} {$smarty.const._TIME}: {html_select_time prefix="to_"   time = $T_TO_TIMESTAMP   display_seconds = false}</td></tr>
{*                                            <tr><td class = "labelCell">{$T_ADD_PERIOD_FORM.shift.label}:&nbsp;</td>
                                                <td class = "elementCell">{$T_ADD_PERIOD_FORM.shift.html}</td></tr>*}
                                            <tr><td colspan = "2">&nbsp;</td></tr>
                                            <tr><td></td><td>{$T_ADD_PERIOD_FORM.submit_add_period.html}</td></tr>
                                        {/if}
                                        </table>
                                    </form>
                                    <script>
                                    {literal}
                                    function deleteSchedule(el) {
                                        Element.extend(el);
                                        url = 'professor.php?ctg=scheduling&delete_schedule=1';
                                        el.down().src = 'images/others/progress1.gif';
                                        new Ajax.Request(url, {
                                                method:'get',
                                                asynchronous:true,
                                                onFailure: function (transport) {
                                                    el.down().writeAttribute({src:'images/16x16/delete.png', title: transport.responseText}).hide();
                                                    new Effect.Appear(el.down().identify());
                                                    window.setTimeout('Effect.Fade("'+el.down().identify()+'")', 10000);
                                                },
                                                onSuccess: function (transport) {
                                                    el.up().update('{/literal}{$smarty.const._NOSCHEDULESET}{literal}').addClassName('emptyCategory');
                                                }
                                        });

                                    }
                                    {/literal}
                                    </script>
                                    {/capture}
                                    {eF_template_printInnerTable title=$smarty.const._ADDPERIOD data=$smarty.capture.t_insert_period_code image='32x32/date-time.png'}
                            </td></tr>

        {/capture}


{elseif ($T_CTG == 'projects')}
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=projects">'|cat:$smarty.const._PROJECTS|cat:'</a>'}

        {*moduleProjects: Print the page that lists the current projects*}
        {capture name = "moduleProjects"}
                                <tr><td class = "moduleCell">
            {if $smarty.get.add_project || $smarty.get.edit_project}
                {if $smarty.get.add_project}
                    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=projects&add_project=1">'|cat:$smarty.const._ADDPROJECT|cat:'</a>'}
                {else}
                    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=projects&edit_project='|cat:$smarty.get.edit_project|cat:'">'|cat:$smarty.const._EDITPROJECT|cat:' <span class= "innerTableName">&quot;'|cat:$T_CURRENT_PROJECT->project.title|cat:'&quot;</span></a>'}
                {/if}
                {capture name = 't_add_project_code'}
                        <div class = "tabber">
                            <div class = "tabbertab" title = "{$smarty.const._PROJECT}">
                                {$T_ADD_PROJECT_FORM.javascript}
                                <form {$T_ADD_PROJECT_FORM.attributes}>
                                {$T_ADD_PROJECT_FORM.hidden}
                                <table class = "formElements" style = "margin-left:0px">
                                    <tr><td class = "labelCell">{$smarty.const._TITLE}:&nbsp;</td>
                                        <td>{$T_ADD_PROJECT_FORM.title.html}</td></tr>
                                        {if $T_ADD_PROJECT_FORM.title.error}<tr><td></td><td class = "formError">{$T_ADD_PROJECT_FORM.title.error}</td></tr>{/if}
                                    <tr><td class = "labelCell">{$smarty.const._AUTOASSIGNTONEWUSERS}:&nbsp;</td>
                                        <td>{$T_ADD_PROJECT_FORM.auto_assign.html}</td></tr>
                                    <tr><td class = "labelCell">{$smarty.const._DEADLINE}:&nbsp;</td>
                                {if !isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change'}
                                        <td>{eF_template_html_select_date prefix="deadline_" time=$T_DEADLINE_TIMESTAMP start_year="-2" end_year="+2" field_order = 'DMY'} {$smarty.const._TIME}: {html_select_time prefix="deadline_" time = $T_DEADLINE_TIMESTAMP display_seconds = false}</td></tr>
                                {else}
                                        <td>#filter:timestamp_time-{$T_DEADLINE_TIMESTAMP}#</td></tr>
                                {/if}
                                <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
                                <tr>
                                             <td>&nbsp;</td>
                                             <td style = "vertical-align:middle" id="toggleeditor_cell1"><a title="{$smarty.const._OPENCLOSEFILEMANAGER}" href="javascript:void(0)" onclick="toggle_file_manager();" style = "vertical-align:middle"><img id="arrow_down" src="images/16x16/navigate_down.png" border="0" alt="{$smarty.const._OPENCLOSEFILEMANAGER}" title="{$smarty.const._OPENCLOSEFILEMANAGER}" style="vertical-align:middle"/>&nbsp;<span id="open_manager" style = "vertical-align:middle">{$smarty.const._OPENFILEMANAGER}</span></a>&nbsp;&nbsp;<a href="javascript:toogleEditorMode('mce_editor_0');" id="toggleeditor_link"><img src = "images/16x16/replace2.png" title = "{$smarty.const._TOGGLEHTMLEDITORMODE}" alt = "{$smarty.const._TOGGLEHTMLEDITORMODE}" style = "vertical-align:middle" border = "0"/>&nbsp;<span  style = "vertical-align:middle">{$smarty.const._TOGGLEHTMLEDITORMODE}</span></a></td></tr>
                                    <tr><td></td><td id="filemanager_cell"></td></tr>
                                    <tr><td></td><td id="toggleeditor_cell2"></td></tr>

                                    <tr><td class = "labelCell">{$smarty.const._PROJECTDESCRIPTION}:&nbsp;</td>
                                        <td class = "elementCell">{$T_ADD_PROJECT_FORM.data.html}</td></tr>
                                        {if $T_ADD_PROJECT_FORM.data.error}<tr><td></td><td class = "formError">{$T_ADD_PROJECT_FORM.data.error}</td></tr>{/if}


                                    <tr><td colspan = "2">&nbsp;</td></tr>
                                    <tr><td></td><td>{$T_ADD_PROJECT_FORM.submit_add_project.html}</td></tr>
                                </table>
                                </form>
                                <table><tr><td id="fmInitial">
                                                <div  id="filemanager_div" style="display:none;">
                                                    {$T_FILE_MANAGER}
                                                    <br/>
                                                </div>
                                                </td></tr>
                                </table>
                                <script type="text/javascript">
                                        {literal}
                                           var tinyMCEmode = true;
                                                function toogleEditorMode(sEditorID) {
                                                    try {
                                                        if(tinyMCEmode) {
                                                            tinyMCE.removeMCEControl(tinyMCE.getEditorId(sEditorID));
                                                            tinyMCEmode = false;
                                                        } else {
                                                            mceAddControlDynamic(sEditorID, 'editor_project_data' ,'mceEditor');
                                                            tinyMCEmode = true;
                                                        }
                                                    } catch(e) {
                                                        alert('editor error');
                                                    }
                                                }

                                function insertatcursor(myField, myValue) {

                                        if (document.selection) {
                                            myField.focus();
                                            sel = document.selection.createRange();
                                            sel.text = myValue;
                                        }
                                        else if (myField.selectionStart || myField.selectionStart == '0') {
                                            var startPos = myField.selectionStart;
                                            var endPos = myField.selectionEnd;
                                            myField.value = myField.value.substring(0, startPos)+ myValue+ myField.value.substring(endPos, myField.value.length);
                                        } else {
                                            myField.value += myValue;
                                        }
                                    }

                                    function insert_editor(element, id) {
                                    {/literal}{if !$smarty.get.edit_project}{literal}
                                        var url = 'professor.php?ctg=projects&add_project=1&postAjaxRequest_insert=1';
                                    {/literal}{else}{literal}
                                        var url = 'professor.php?ctg=projects&edit_project={/literal}{$smarty.get.edit_project}{literal}&postAjaxRequest_insert=1';
                                    {/literal}{/if}{literal}
                                        new Ajax.Request(url, {
                                            method:'get',
                                            asynchronous:true,
                                            parameters: {file_id: id, editor_mode: tinyMCEmode},
                                            onSuccess: function (transport) {
                                                if(tinyMCEmode) {
                                                    tinyMCE.execInstanceCommand('mce_editor_0','mceInsertContent', false , transport.responseText);
                                                }else {
                                                    insertatcursor(window.document.create_project_form.data, transport.responseText);
                                                }
                                            }
                                        });
                                        }

                                    var file_manager_hidden = 1;
                                    function toggle_file_manager(){
                                        if(file_manager_hidden){
                                            $('filemanager_cell').insert($('filemanager_div'));
                                            $('filemanager_div').style.display = "block";
                                            $('arrow_down').src = "images/16x16/navigate_up.png";
                                            $('toggleeditor_cell2').insert($('toggleeditor_link'));
                                            $('open_manager').update('{/literal}{$smarty.const._CLOSEFILEMANAGER}{literal}');
                                            file_manager_hidden = 0;
                                        }else{
                                            $('filemanager_div').style.display = "none";
                                            $('fmInitial').insert($('filemanager_div'));
                                            $('toggleeditor_cell1').insert($('toggleeditor_link'));
                                            $('arrow_down').src = "images/16x16/navigate_down.png";
                                            $('open_manager').update('{/literal}{$smarty.const._OPENFILEMANAGER}{literal}');
                                            file_manager_hidden = 1;
                                        }
                                    }
                                        {/literal}
                                        </script>
                            </div>

                    {if $smarty.get.edit_project}
                            <div class = "tabbertab{if $smarty.get.tab == 'project_users'} tabbertabdefault{/if}" title = "{$smarty.const._USERS}">

<!--ajax:usersTable-->
                                <table style = "width:100%" class = "sortedTable" size = "{$T_USERS_SIZE}" sortBy = "0" id = "usersTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "professor.php?ctg=projects&edit_project={$smarty.get.edit_project}&tab=project_users&">
                                    <tr>
                                        <td class = "topTitle" name = "login">{$smarty.const._LOGIN}</td>
                                        <td class = "topTitle" name = "name">{$smarty.const._NAME}</td>
                                        <td class = "topTitle" name = "surname">{$smarty.const._SURNAME}</td>
                                        <td class = "topTitle centerAlign" name = "checked">{$smarty.const._CHECK}</td>
                                    </tr>
                                {foreach name = 'users_to_projects_list' key = 'key' item = 'user' from = $T_ALL_USERS}
                                    <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$user.active}deactivatedTableElement{/if}">
                                        <td>{$user.login}</td>
                                        <td>{$user.name}</td>
                                        <td>{$user.surname}</td>
                                        <td class = "centerAlign">
                                    {if !isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change'}
                                            <input class = "inputCheckbox" type = "checkbox" name = "checked_{$user.login}" id = "checked_{$user.login}" onclick = "ajaxPost('{$user.login}', this);" {if $user.checked}checked = "checked"{/if}/>
                                    {else}
                                            {if $user.checked}<img src = "images/16x16/check2.png" alt = "{$smarty.const._PROJECTUSER}" title = "{$smarty.const._PROJECTUSER}">{/if}
                                    {/if}
                                        </td>
                                    </tr>
                                {foreachelse}
                                    <tr class = "defaultRowHeight oddRowColor"><td colspan = "100%" class = "centerAlign emptyCategory">{$smarty.const._NOUSERSFOUND}</td></tr>
                                {/foreach}
                                </table>
<!--/ajax:usersTable-->
                                {literal}
                                <script>
                                function ajaxPost(login, el, table_id) {
                                    var baseUrl = 'professor.php?ctg=projects&edit_project={/literal}{$smarty.get.edit_project}{literal}&postAjaxRequest=1';
                                    if (login) {
                                        var checked  = $('checked_'+login).checked;
                                        var url      = baseUrl + '&login='+login;
                                        var img_id   = 'img_'+login;
                                    } else if (table_id && table_id == 'usersTable') {
                                        el.checked ? url = baseUrl + '&addAll=1' : url = baseUrl + '&removeAll=1';
                                        var img_id   = 'img_selectAll';
                                    }

                                    var position = eF_js_findPos(el);
                                    var img      = document.createElement("img");

                                    img.style.position = 'absolute';
                                    img.style.top      = Element.positionedOffset(Element.extend(el)).top  + 'px';
                                    img.style.left     = Element.positionedOffset(Element.extend(el)).left + 6 + Element.getDimensions(Element.extend(el)).width + 'px';

                                    img.setAttribute("id", img_id);
                                    img.setAttribute('src', 'images/others/progress1.gif');

                                    el.parentNode.appendChild(img);

                                    new Ajax.Request(url, {
                                            method:'get',
                                            asynchronous:true,
                                            onSuccess: function (transport) {
                                                img.style.display = 'none';
                                                img.setAttribute('src', 'images/16x16/check.png');
                                                new Effect.Appear(img_id);
                                                window.setTimeout('Effect.Fade("'+img_id+'")', 2500);
                                                }
                                        });
                                }
                                </script>
                                {/literal}
                            </div>
                    {/if}
                        </div>
                {/capture}
                {if $smarty.get.add_project}{assign var = "innerTableTitle" value = $smarty.const._ADDPROJECT}{else}{assign var = "innerTableTitle" value = "`$smarty.const._OPTIONSFORPROJECT`<span class='innerTableName'> &quot;`$T_CURRENT_PROJECT->project.title`&quot;</span>"}{/if}
                {eF_template_printInnerTable title=$innerTableTitle data=$smarty.capture.t_add_project_code image='32x32/exercises.png'}

            {elseif $smarty.get.project_results}
                {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=projects&project_results='|cat:$smarty.get.project_results|cat:'">'|cat:$smarty.const._RESULTSFORPROJECT|cat:' &quot;'|cat:$T_CURRENT_PROJECT->project.title|cat:'&quot;</a>'}
                {capture name = "t_project_results_code"}
<!--ajax:usersTable-->
                                <table style = "width:100%" class = "sortedTable" size = "{$T_USERS_SIZE}" sortBy = "2" id = "usersTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "professor.php?ctg=projects&project_results={$smarty.get.project_results}&">
                                    <tr>
                                        <td class = "topTitle" name = "users_LOGIN">{$smarty.const._STUDENT}</td>
                                        <td class = "topTitle" name = "file">{$smarty.const._FILENAME}</td>
                                        <td class = "topTitle" name = "upload_timestamp">{$smarty.const._UPLOADEDON}</td>
                                        <td class = "topTitle" name = "comments">{$smarty.const._COMMENTS}</td>
                                        <td class = "topTitle" name = "grade">{$smarty.const._SCORE}</td>
                                    {if !isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change'}
                                        <td class = "topTitle centerAlign">{$smarty.const._OPERATIONS}</td>
                                    {/if}
                                    </tr>
                                {foreach name = 'users_to_projects_list' key = 'key' item = 'user' from = $T_ALL_USERS}
                                    <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$user.active}deactivatedTableElement{/if}">
                                        <td>{$user.name} {$user.surname} ({$user.users_LOGIN})</td>
                                        <td>{if $user.filename}
                                            <a href = "view_file.php?file={$user.filename}"    target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$user.file}', 2)">{$user.file}</a>
                                            <a href = "view_file.php?file={$user.filename}&action=download" target = "POPUP_FRAME"><img src = "images/16x16/import1.png" alt = "{$smarty.const._DOWNLOADFILE} {$user.file}" title = "{$smarty.const._DOWNLOADFILE} {$user.file}" border = "0" style = "vertical-align:middle"></a>
                                            {/if}
                                        </td>
                                        <td>{if $user.upload_timestamp != 'empty'}#filter:timestamp_time-{$user.upload_timestamp}#{/if}</td>    {*'empty' is set inside the php file, so that the sorting can be done correctly*}
                                    {if !isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change'}
                                        <td><input type = "text" id = "comments_{$user.users_LOGIN}" value = "{$user.comments}" size = "50" /></td>
                                        <td><input type = "text" id = "grade_{$user.users_LOGIN}"    value = "{$user.grade}"      size = "5" maxlength = "5" /></td>
                                        <td class = "centerAlign">
                                            <a href = "javascript:void(0)" onclick = "ajaxPost('{$user.users_LOGIN}', this)"><img src = "images/16x16/check2.png" title = "{$smarty.const._SAVE}" alt = "{$smarty.const._SAVE}" border = "0" style = "vertical-align:middle"/></a>
                                        </td>
                                    {else}
                                        <td>{$user.comments}</td>
                                        <td>{$user.grade}</td>
                                    {/if}
                                    </tr>
                                {foreachelse}
                                    <tr class = "defaultRowHeight oddRowColor"><td colspan = "100%" class = "centerAlign emptyCategory">{$smarty.const._NOUSERSFOUND}</td></tr>
                                {/foreach}
                                </table>
<!--/ajax:usersTable-->

                    {literal}
                    <script>
                    function ajaxPost(login, el, table_id) {
                        var baseUrl =  'professor.php?ctg=projects&project_results={/literal}{$smarty.get.project_results}{literal}&postAjaxRequest=1';
                        if (login) {
                            var comments = $('comments_'+login).value;
                            var grade    = $('grade_'+login).value;
                            var url      = baseUrl + '&login='+login+'&grade='+grade+'&comments='+comments;
                            var img_id   = 'img_'+login;
                        } else if (table_id && table_id == 'usersTable') {
                            el.checked ? url = baseUrl + '&addAll=1' : url = baseUrl + '&removeAll=1';
                            var img_id   = 'img_selectAll';
                        }

                        var position = eF_js_findPos(el);
                        var img      = document.createElement("img");

                        img.style.position = 'absolute';
                        img.style.top      = Element.positionedOffset(Element.extend(el)).top  + 'px';
                        img.style.left     = Element.positionedOffset(Element.extend(el)).left + 6 + Element.getDimensions(Element.extend(el)).width + 'px';

                        if ($(img_id)) $(img_id).remove();
                        img.setAttribute("id", img_id);
                        img.setAttribute('src', 'images/others/progress1.gif');

                        el.parentNode.appendChild(img);

                            new Ajax.Request(url, {
                                    method:'get',
                                    asynchronous:true,
                                    onSuccess: function (transport) {
                                        img.style.display = 'none';
                                        img.setAttribute('src', 'images/16x16/check.png');
                                        if (transport.responseText) {
                                            alert(transport.responseText);
                                            img.setAttribute('src', 'images/16x16/delete.png');
                                        }
                                        new Effect.Appear(img_id);
                                        window.setTimeout('Effect.Fade("'+img_id+'")', 2500);
                                        }
                                });
                    }
                    </script>
                    {/literal}

                {/capture}
                {eF_template_printInnerTable title=$smarty.const._RESULTSFORPROJECT|cat:' &quot;'|cat:$T_CURRENT_PROJECT->project.title|cat:'&quot;' data=$smarty.capture.t_project_results_code image='32x32/exercises.png'}

            {else}
                {capture name = "t_print_projects_code"}
                            {if !isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change'}
                                <table border = "0">
                                    <tr><td><a href = "professor.php?ctg=projects&add_project=1"><img src="images/16x16/add2.png" style = "vertical-align: middle;" title="{$smarty.const._ADDPROJECT}" alt="{$smarty.const._ADDPROJECT}" border="0"/></a></td>
                                        <td><a href = "professor.php?ctg=projects&add_project=1">{$smarty.const._ADDPROJECT}</a></td></tr>
                                </table>
                            {/if}
                                <div class = "tabber">
                                    <div class = "tabbertab" title = "{$smarty.const._ACTIVE_PROJECTS} ({$T_ACTIVE_COUNT})">
                                    <table class = "sortedTable" width = "100%">
                                        <tr><td class = "topTitle">{$smarty.const._TITLE}</td>
                                            <td class = "topTitle">{$smarty.const._DEADLINE}</td>
                                            <td class = "topTitle">{$smarty.const._TIMEREMAIN}</td>
                                            <td class = "topTitle">{$smarty.const._CREATOR}</td>
                                            <td class = "topTitle centerAlign">{$smarty.const._STUDENTS}</td>
                                            <td class = "topTitle centerAlign noSort">{$smarty.const._FUNCTIONS}</td>
                                        </tr>
                        {foreach name = 'projects_list' key = 'key' item = 'project' from = $T_CURRENT_PROJECTS}
                                        <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                                            <td><a href = "professor.php?ctg=projects&edit_project={$project->project.id}">{$project->project.title}</a></td>
                                            <td><span style = "display:none">{$project->project.deadline}</span>#filter:timestamp_time_nosec-{$project->project.deadline}#</td>
                                            <td><span style = "display:none">{$project->project.deadline}</span>{$project->timeRemaining}</td>
                                            <td>{$project->project.creator_LOGIN}</td>
                                            <td class = "centerAlign">{$project->doneUsers}/{$project->doneUsers+$project->pendingUsers}</td>
                                            <td class = "centerAlign">
                            {if !isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change'}
                                                <a href = "professor.php?ctg=projects&edit_project={$project->project.id}"> <img border = "0" src = "images/16x16/edit.png"           alt = "{$smarty.const._EDIT}"               title = "{$smarty.const._EDIT}"/></a>
                            {/if}
                                                <a href = "professor.php?ctg=projects&project_results={$project->project.id}"> <img border = "0" src = "images/16x16/text_marked.png" alt = "{$smarty.const._SCORE}"              title = "{$smarty.const._SCORE}"/></a>
                            {if $project->doneUsers > 0}<a href = "professor.php?ctg=projects&compress_data={$project->project.id}"><img border = "0" src = "images/file_types/zip.png"    alt = "{$smarty.const._PROJECTSCOMPRESSED}" title = "{$smarty.const._PROJECTSCOMPRESSED}"/></a>{/if}
                            {if !isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change'}
                                                <a href = "professor.php?ctg=projects&delete_project={$project->project.id}" onclick = "return confirm('{$smarty.const._AREYOUSUREYOUWANTTODELETEPROJECT}')">
                                                    <img border = "0" src = "images/16x16/delete.png" alt = "{$smarty.const._DELETE}" title = "{$smarty.const._DELETE}"/>
                                                </a>
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
                                            <td class = "topTitle centerAlign">{$smarty.const._STUDENTS}</td>
                                            <td class = "topTitle centerAlign noSort">{$smarty.const._FUNCTIONS}</td>
                                        </tr>
                        {foreach name = 'projects_list' key = 'key' item = 'project' from = $T_EXPIRED_PROJECTS}
                                        <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                                            <td><a href = "professor.php?ctg=projects&edit_project={$project->project.id}">{$project->project.title}</a></td>
                                            <td><span style = "display:none">{$project->project.deadline}</span>#filter:timestamp_time_nosec-{$project->project.deadline}#</td>
                                            <td>{$project->project.creator_LOGIN}</td>
                                            <td class = "centerAlign">{$project->doneUsers}/{$project->doneUsers+$project->pendingUsers}</td>
                                            <td class = "centerAlign">
                            {if !isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change'}
                                                <a href = "professor.php?ctg=projects&edit_project={$project->project.id}"> <img border = "0" src = "images/16x16/edit.png"           alt = "{$smarty.const._EDIT}"               title = "{$smarty.const._EDIT}"/></a>
                            {/if}
                                                <a href = "professor.php?ctg=projects&project_results={$project->project.id}"> <img border = "0" src = "images/16x16/text_marked.png" alt = "{$smarty.const._SCORE}"              title = "{$smarty.const._SCORE}"/></a>
                            {if $project->doneUsers > 0}<a href = "professor.php?ctg=projects&compress_data={$project->project.id}"><img border = "0" src = "images/file_types/zip.png"    alt = "{$smarty.const._PROJECTSCOMPRESSED}" title = "{$smarty.const._PROJECTSCOMPRESSED}"/></a>{/if}
                            {if !isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change'}
                                                <a href = "professor.php?ctg=projects&delete_project={$project->project.id}" onclick = "return confirm('{$smarty.const._AREYOUSUREYOUWANTTODELETEPROJECT}')">
                                                    <img border = "0" src = "images/16x16/delete.png" alt = "{$smarty.const._DELETE}" title = "{$smarty.const._DELETE}"/>
                                                </a>
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

                {eF_template_printInnerTable title=$smarty.const._PROJECTS data=$smarty.capture.t_print_projects_code image='32x32/exercises.png'}
            {/if}
                                </td></tr>
        {/capture}
{elseif ($T_CTG == 'tests')}
                        {*moduleTests: Print the Tests page*}
                        {capture name = "moduleTests"}
                            {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=tests">'|cat:$smarty.const._TESTS|cat:'</a>'}
                            {if $smarty.get.edit_test}
                                {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=tests&edit_test=`$smarty.get.edit_test`'>`$smarty.const._EDITTEST` <span class='innerTableName'>&quot;`$T_CURRENT_TEST.name`&quot;</span></a>"}
                            {elseif $smarty.get.add_test}
                                {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=tests&add_test=1'>`$smarty.const._ADDTEST`</a>"}
                            {elseif $smarty.get.edit_question}
                                {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=tests&edit_question=`$smarty.get.edit_question`&question_type=`$smarty.get.question_type`'>`$smarty.const._EDITQUESTION`</a>"}
                            {elseif $smarty.get.add_question}
                                {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=tests&add_question=1&question_type=`$smarty.get.question_type`'>`$smarty.const._ADDQUESTION`</a>"}
                            {elseif $smarty.get.test_results}
                                {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=tests&test_results='|cat:$smarty.get.test_results|cat:'">'|cat:$smarty.const._TESTRESULTS|cat:'</a>'}
				            {elseif $smarty.get.view_unit}
				                {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?view_unit='|cat:$smarty.get.view_unit|cat:'">'|cat:$smarty.const._PREVIEW|cat:'</a>'}
                            {elseif $smarty.get.show_solved_test}
                                {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=tests&test_results=`$T_TEST_DATA->completedTest.testsId`'>`$smarty.const._TESTRESULTS`</a>"}
                                {if !$smarty.get.test_analysis}
                                    {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=tests&show_solved_test=`$T_TEST_DATA->completedTest.id`'>`$smarty.const._VIEWSOLVEDTEST`: &quot;`$T_TEST_DATA->test.name`&quot; `$smarty.const._BYUSER`: `$T_TEST_DATA->completedTest.login`</a>"}
                                {else}
                                    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=tests&show_solved_test='|cat:$smarty.get.show_solved_test|cat:'&test_analysis='|cat:$smarty.get.test_analysis|cat:'&user='|cat:$smarty.get.user|cat:'">'|cat:$smarty.const._USERRESULTS|cat:'</a>'}
                                {/if}
                            {/if}
                            <tr><td class = "moduleCell">
                                {include file = "includes/module_tests.tpl"}
                            </td></tr>
                        {/capture}
                        {capture name = 'sideUnitOperations'}
                            {capture name = 'UnitOperationsBar'}
                                                1. <a href = "{$smarty.server.PHP_SELF}?ctg=tests&show_test={$T_CURRENT_TEST.id}&print=1&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._PRINT}', 2)">{$smarty.const._PRINTERFRIENDLY}</a><br/>
                            {/capture}
                            {eF_template_printSide title = $smarty.const._UNITOPERATIONS data = $smarty.capture.UnitOperationsBar id = 'unit_operations'}
                        {/capture}
                        {if isset($T_SHOW_TOOLS)}
                            {assign var = "sidebar_right" value = '<div style="text-align:right;"><div id="sidebarslideup" style="display: block;" class="sidebarslidecontrol" onclick="sidebarUp();"><img src="images/16x16/navigate_right.png" style="vertical-align:middle" alt="'|cat:$smarty.const._HIDESIDEBAR|cat:'" title="'|cat:$smarty.const._HIDESIDEBAR|cat:'"/></div><div  id="sidebarslidedown" class="sidebarslidecontrol" onclick="sidebarDown();" style="display: none;"><img src="images/16x16/navigate_left.png" style="vertical-align:middle" alt="'|cat:$smarty.const._SHOWSIDEBAR|cat:'" title="'|cat:$smarty.const._SHOWSIDEBAR|cat:'"/></div></div>'}
                        {/if}

{elseif ($T_CTG == 'rules')}
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=rules">'|cat:$smarty.const._LESSONRULES|cat:'</a>'}

    {*moduleRules: Print content Rules list*}
    {capture name = "moduleRules"}
                            <tr><td class = "moduleCell">
                            {if $smarty.get.add_rule || $smarty.get.edit_rule}
                                {if $smarty.get.add_rule}
                                    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=rules&add_rule=1" >'|cat:$smarty.const._ADDRULE|cat:'</a>'}
                                {/if}
                                {capture name = 't_add_rule_code'}
                                    <script type = "text/javascript">
                                    <!--
                                    {literal}
                                    function selectRule(el) {
                                        Element.extend(el);
                                        $('rule_unit').hide();
                                        $('test_unit').hide();
                                        $('test_score').hide();

                                        if (el.options[el.selectedIndex].value == 'hasnot_seen') {
                                            $('rule_unit').show();
                                        } else if (el.options[el.selectedIndex].value == 'hasnot_passed') {
                                            $('test_unit').show();
                                            $('test_score').show();
                                        }
                                    }
                                    {/literal}
                                    //-->
                                    </script>
                                    {$T_ADD_RULE_FORM.javascript}
                                    <form {$T_ADD_RULE_FORM.attributes}>
                                        {$T_ADD_RULE_FORM.hidden}
                                        <fieldset>
                                        <legend>{$smarty.const._ADDCUSTOMRULE}</legend>
                                        <table class = "formElements" style = "margin-left:0px">
                                            <tr><td class = "labelCell">{$smarty.const._VALIDFOR}:&nbsp;</td>
                                                <td>{$T_ADD_RULE_FORM.scope.html}</td></tr>
                                            {if $T_ADD_RULE_FORM.scope.error}<tr><td></td><td class = "formError">{$T_ADD_RULE_FORM.scope.error}</td></tr>{/if}
                                            <tr><td class = "labelCell">{$smarty.const._TOBEEXCLUDEDFROMUNIT}:&nbsp;</td>
                                                <td>{$T_ADD_RULE_FORM.exclusion_unit.html}</td></tr>
                                            {if $T_ADD_RULE_FORM.exclusion_unit.error}<tr><td></td><td class = "formError">{$T_ADD_RULE_FORM.exclusion_unit.error}</td></tr>{/if}
                                            <tr><td class = "labelCell">{$smarty.const._BASEDONTERM}:&nbsp;</td>
                                                <td>{$T_ADD_RULE_FORM.rule_type.html}</td></tr>
                                            {if $T_ADD_RULE_FORM.rule_type.error}<tr><td></td><td class = "formError">{$T_ADD_RULE_FORM.rule_type.error}</td></tr>{/if}
                                            <tr id = "rule_unit" style = "{if $T_CURRENT_RULE != 'hasnot_seen' && $smarty.post.rule_type != 'hasnot_seen'}display:none{/if}"><td class = "labelCell">{$smarty.const._WITHNAME}:&nbsp;</td>
                                                <td>{$T_ADD_RULE_FORM.rule_unit.html}</td></tr>
                                            {if $T_ADD_RULE_FORM.rule_unit.error}<tr><td></td><td class = "formError">{$T_ADD_RULE_FORM.rule_unit.error}</td></tr>{/if}
                                            <tr id = "test_unit" style = "{if $T_CURRENT_RULE != 'hasnot_passed' && $smarty.post.rule_type != 'hasnot_passed'}display:none{/if}"><td class = "labelCell">{$smarty.const._WITHNAME}:&nbsp;</td>
                                                <td>{$T_ADD_RULE_FORM.test_unit.html}</td></tr>
                                            {if $T_ADD_RULE_FORM.test_unit.error}<tr><td></td><td class = "formError">{$T_ADD_RULE_FORM.test_unit.error}</td></tr>{/if}
                                            <tr id = "test_score" style = "{if $T_CURRENT_RULE != 'hasnot_passed' && $smarty.post.rule_type != 'hasnot_passed'}display:none{/if}"><td class = "labelCell">{$smarty.const._ANDSCOREGREATEROREQUAL}:&nbsp;</td>
                                                <td>{$T_ADD_RULE_FORM.score.html}</td></tr>
                                            {if $T_ADD_RULE_FORM.score.error}<tr><td></td><td class = "formError">{$T_ADD_RULE_FORM.score.error}</td></tr>{/if}
                                            <tr><td colspan = "100%">&nbsp;</td></tr>
                                            <tr><td></td><td>{$T_ADD_RULE_FORM.submit_rule.html}</td></tr>
                                        </table>
                                        </fieldset>
                                    </form>
                                    {$T_ADD_READY_RULE_FORM.javascript}
                                    <form {$T_ADD_READY_RULE_FORM.attributes}>
                                        {$T_ADD_READY_RULE_FORM.hidden}                                        <fieldset>
                                        <legend>{$smarty.const._ADDREADYRULE}</legend>
                                        <table>
                                            <tr><td class = "labelCell">{$smarty.const._SERIALRULE}:&nbsp;</td>
                                                <td class = "elementCell">{$T_ADD_READY_RULE_FORM.ready_rule.serial.html}</td></tr>
{*                                            <tr><td class = "labelCell">{$smarty.const._TREERULE}:&nbsp;</td>
                                                <td class = "elementCell">{$T_ADD_READY_RULE_FORM.ready_rule.tree.html}</td></tr>
*}
                                            <tr><td colspan = "100%">&nbsp;</td></tr>
                                            <tr><td class = "labelCell"></td>
                                                <td class = "elementCell">{$T_ADD_READY_RULE_FORM.submit_ready_rule.html}</td></tr>
                                        </table>
                                        </fieldset>
                                    </form>
                                {/capture}

                                {eF_template_printInnerTable title=$smarty.const._RULEPROPERTIES data=$smarty.capture.t_add_rule_code image='32x32/recycle_preferences.png'}
                            {elseif $smarty.get.add_condition || $smarty.get.edit_condition}
                                {if $smarty.get.add_condition}
                                    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=rules&tab=conditions&add_condition=1">'|cat:$smarty.const._ADDCONDITION|cat:'</a>'}
                                {else}
                                    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=rules&tab=conditions&edit_condition='|cat:$smarty.get.edit_condition|cat:'">'|cat:$smarty.const._EDITCONDITION|cat:'</a>'}
                                {/if}

                                {capture name = 't_add_condition_code'}
                                    <script type = "text/javascript">
                                    <!--
                                    {literal}
                                    function eF_js_selectCondition(el) {
                                        document.getElementById('percentage_units').style.display    = 'none';
                                        document.getElementById('specific_unit').style.display       = 'none';
                                        document.getElementById('all_tests').style.display           = 'none';
                                        document.getElementById('specific_test').style.display       = 'none';
                                        document.getElementById('specific_test_score').style.display = 'none';

                                        switch (el.options[el.selectedIndex].value) {
                                            case 'percentage_units' :
                                                document.getElementById('percentage_units').style.display  = '';
                                                break;
                                            case 'specific_unit' :
                                                document.getElementById('specific_unit').style.display  = '';
                                                break;
                                            case 'all_tests' :
                                                document.getElementById('all_tests').style.display  = '';
                                                break;
                                            case 'specific_test' :
                                                document.getElementById('specific_test').style.display  = '';
                                                document.getElementById('specific_test_score').style.display  = '';
                                                break;
                                            default:
                                                break;
                                        }
                                    }
                                    {/literal}
                                    //-->
                                    </script>
                                    {$T_COMPLETE_LESSON_FORM.javascript}
                                    <form {$T_COMPLETE_LESSON_FORM.attributes}>
                                        {$T_COMPLETE_LESSON_FORM.hidden}
                                        <table class = "formElements" style = "margin-left:0px">
                                            <tr><td class = "labelCell">{$smarty.const._THEUSERMUSTHAVE}:&nbsp;</td>
                                                <td>{$T_COMPLETE_LESSON_FORM.condition_types.html}</td></tr>
                                            {if $T_COMPLETE_LESSON_FORM.condition_types.error}<tr><td class = "formError">{$T_COMPLETE_LESSON_FORM.condition_types.error}</td></tr>{/if}

                                            <tr id = "percentage_units" {if $T_CURRENT_CONDITION.type != "percentage_units" && $smarty.post.condition_types != "percentage_units"}style = "display:none"{/if}><td class = "labelCell">{$smarty.const._UNITSPERCENTAGE}:&nbsp;</td>
                                                <td>{$T_COMPLETE_LESSON_FORM.percentage_units.html}%</td></tr>
                                            {if $T_COMPLETE_LESSON_FORM.percentage_units.error}<tr><td class = "formError">{$T_COMPLETE_LESSON_FORM.percentage_units.error}</td></tr>{/if}

                                            <tr id = "specific_unit" {if $T_CURRENT_CONDITION.type != "specific_unit" && $smarty.post.condition_types != "specific_unit"}style = "display:none"{/if}><td class = "labelCell">{$smarty.const._WITHNAME}:&nbsp;</td>
                                                <td>{$T_COMPLETE_LESSON_FORM.specific_unit.html}</td></tr>
                                            {if $T_COMPLETE_LESSON_FORM.specific_unit.error}<tr><td class = "formError">{$T_COMPLETE_LESSON_FORM.specific_unit.error}</td></tr>{/if}

                                            <tr id = "all_tests" {if $T_CURRENT_CONDITION.type != "all_tests" && $smarty.post.condition_types != "all_tests"}style = "display:none"{/if}><td class = "labelCell">{$smarty.const._ANDSCOREGREATEROREQUAL}:&nbsp;</td>
                                                <td>{$T_COMPLETE_LESSON_FORM.all_tests.html}</td></tr>
                                            {if $T_COMPLETE_LESSON_FORM.all_tests.error}<tr><td class = "formError">{$T_COMPLETE_LESSON_FORM.all_tests.error}</td></tr>{/if}

                                            <tr id = "specific_test" {if $T_CURRENT_CONDITION.type != "specific_test" && $smarty.post.condition_types != "specific_test"}style = "display:none"{/if}><td class = "labelCell">{$smarty.const._WITHNAME}:&nbsp;</td>
                                                <td>{$T_COMPLETE_LESSON_FORM.specific_test.html}</td></tr>
                                            {if $T_COMPLETE_LESSON_FORM.specific_test.error}<tr><td class = "formError">{$T_COMPLETE_LESSON_FORM.specific_test.error}</td></tr>{/if}

                                            <tr id = "specific_test_score" {if $T_CURRENT_CONDITION.type != "specific_test" && $smarty.post.condition_types != "specific_test"}style = "display:none"{/if}><td class = "labelCell">{$smarty.const._ANDSCOREGREATEROREQUAL}:&nbsp;</td>
                                                <td>{$T_COMPLETE_LESSON_FORM.specific_test_score.html}%</td></tr>
                                            {if $T_COMPLETE_LESSON_FORM.specific_test_score.error}<tr><td class = "formError">{$T_COMPLETE_LESSON_FORM.specific_test_score.error}</td></tr>{/if}

                                            <tr><td class = "labelCell">{$smarty.const._RELATIONTOOTHERS}:&nbsp;</td>
                                                <td>{$T_COMPLETE_LESSON_FORM.relation.html}</td></tr>
                                            {if $T_COMPLETE_LESSON_FORM.relation.error}<tr><td class = "formError">{$T_COMPLETE_LESSON_FORM.relation.error}</td></tr>{/if}

                                            <tr><td colspan = "100%">&nbsp;</td></tr>
                                            <tr><td></td><td>{$T_COMPLETE_LESSON_FORM.submit_complete_lesson_condition.html}</td></tr>
                                        </table>
                                    </form>
                                {/capture}

                                {eF_template_printInnerTable title=$smarty.const._CONDITIONPROPERTIES data=$smarty.capture.t_add_condition_code image='32x32/recycle_preferences.png'}
                            {else}
                                {capture name = 't_conditions_code'}
                                        <script>
                                        {literal}
                                        function setAutoComplete(el) {
                                            Element.extend(el);
                                            url = 'professor.php?ctg=rules&ajax=1&action=auto_complete';
                                            img = new Element('img', {id:'progress_image', src:'images/others/progress1.gif'}).setStyle({verticalAlign:'middle', borderWidth:'0px'});
                                            //progressImg.style.top      = Element.positionedOffset(el.top + 1 + 'px';
                                            //progressImg.style.left     = Element.positionedOffset(el.left + 6 + Element.getDimensions($('save_button')).width + 'px';
                                            el.parentNode.appendChild(img);

                                            //el.previous().src = 'images/others/progress1.gif';

                                            new Ajax.Request(url, {
                                                    method:'get',
                                                    asynchronous:true,
                                                    onFailure: function (transport) {
                                                        img.writeAttribute({src:'images/16x16/delete.png', title:transport.responseText}).hide();
                                                        new Effect.Appear(img.identify());
                                                        window.setTimeout('Effect.Fade("'+img.identify()+'")', 10000);
                                                    },
                                                    onSuccess: function (transport) {
                                                        img.style.display = 'none';
                                                        img.setAttribute('src', 'images/16x16/check.png');
                                                        new Effect.Appear(img);
                                                        window.setTimeout('Effect.Fade("'+img.identify()+'")', 2500);
                                                        if (transport.responseText == 1) {
                                                            el.update('{/literal}{$smarty.const._AUTOCOMPLETE}:&nbsp;{$smarty.const._YES}{literal}');
                                                        } else {
                                                            el.update('{/literal}{$smarty.const._AUTOCOMPLETE}:&nbsp;{$smarty.const._NO}{literal}');
                                                        }
                                                    }
                                                });

                                        }
                                        {/literal}
                                        </script>
                                    {if !isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change'}
                                        <table>
                                            <tr><td style = "padding-right:5px">
                                                <img border="0" src = "images/16x16/add2.png" title="{$smarty.const._ADDCONDITION}" alt="{$smarty.const._ADDCONDITION}" style = "vertical-align:middle"/>
                                                <a style = "vertical-align:middle"  href = "professor.php?ctg=rules&tab=conditions&add_condition=1">{$smarty.const._ADDCONDITION}</a>&nbsp;
                                            </td>
                                            <td style = "padding-left:5px;border-left:1px solid black">
                                                <img border = "0" src = "images/16x16/book_green.png" title="{$smarty.const._AUTOCOMPLETE}" alt="{$smarty.const._AUTOCOMPLETE}" style = "vertical-align:middle"/>
                                                <a style = "vertical-align:middle" href = "javascript:void(0)" onclick = "setAutoComplete(this)">{$smarty.const._AUTOCOMPLETE}:&nbsp;{if $T_CURRENT_LESSON->options.auto_complete}{$smarty.const._YES}{else}{$smarty.const._NO}{/if}</a>&nbsp;
                                            </td></tr>
                                        </table>
                                    {/if}
                                        <table border = "0" width = "100%" class = "sortedTable" rowsPerPage = "15">
                                            <tr class = "topTitle">
                                                <td class = "topTitle">{$smarty.const._CONDITIONTYPE}</td>
                                                <td class = "topTitle">{$smarty.const._CONDITION}</td>
                                                <td class = "topTitle">{$smarty.const._RELATIONTOOTHERS}</td>
                                                <td class = "topTitle centerAlign noSort">{$smarty.const._FUNCTIONS}</td>

                                    {foreach name = 'conditions_list' key = "key" item = "item" from = $T_LESSON_CONDITIONS}
                                            <tr class = "{cycle values = "oddRowColor, evenRowColor"} defaultRowHeight">
                                                <td>{$T_CONDITION_TYPES[$item.type]}</td>
                                                <td>
                                                    {if $item.type == 'all_units'}
                                                    {elseif $item.type == 'percentage_units'}
                                                        {$item.options.0}%
                                                    {elseif $item.type == 'specific_unit'}
                                                        {$T_TREE_NAMES[$item.options.0]}
                                                    {elseif $item.type == 'all_tests'}
                                                        {$item.options.0}%
                                                    {elseif $item.type == 'specific_test'}
                                                        {$T_TREE_NAMES[$item.options.0]}, {$smarty.const._WITHSCOREATLEAST} {$item.options.1}%
                                                    {/if}
                                                </td>
                                                <td>{if $item.relation == 'or'}{$smarty.const._OR}{else}{$smarty.const._AND}{/if}</td>
                                                <td align = "center">
                                    {if !isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change'}
                                                    <a class = "editLink"   href = "{$smarty.server.PHP_SELF}?ctg=rules&tab=conditions&edit_condition={$item.id}"><img src = "images/16x16/edit.png" alt = "{$smarty.const._CORRECTION}" title = "{$smarty.const._CORRECTION}" border = "0"/></a>
                                                    <a class = "deleteLink" href = "{$smarty.server.PHP_SELF}?ctg=rules&tab=conditions&delete_condition={$item.id}"><img src = "images/16x16/delete.png" alt = "{$smarty.const._DELETE}" title = "{$smarty.const._DELETE}" border = "0" onclick = "return confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')"/></a>
                                    {/if}
                                                </td>
                                            </tr>
                                    {foreachelse}
                                            <tr class = "oddRowColor defaultRowHeight"><td colspan = "5" class = "emptyCategory centerAlign">{$smarty.const._NODATAFOUND}</td></tr>
                                    {/foreach}
                                        </table>
                                {/capture}

                                {capture name = "t_lesson_rules"}
                                    {if !isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change'}
                                            <table>
                                                <tr><td>
                                                        <img border = "0" src = "images/16x16/add2.png" title = "{$smarty.const._ADDRULE}" alt = "{$smarty.const._ADDRULE}"  style = "vertical-align:middle"/>
                                                        <a href = "professor.php?ctg=rules&add_rule=1" style = "vertical-align:middle">{$smarty.const._ADDRULE}</a>&nbsp;
                                                    </td></tr>
                                            </table>
                                    {/if}
                                            <table border = "0" width = "100%" class = "sortedTable">
                                                <tr class = "topTitle defaultRowHeight">
                                                    <td class = "topTitle">{$smarty.const._VALIDFOR}</td>
                                                    <td class = "topTitle">{$smarty.const._EXCLUDECONSTRAINT}</td>
                                                    <td class = "topTitle">{$smarty.const._EXCLUSIONUNIT}</td>
                                                    <td class = "topTitle noSort centerAlign">{$smarty.const._FUNCTIONS}</td>
                                        {foreach name = rules_list key = "key" item = "rule" from = $T_RULES}
                                            {assign var = "rule_content_id" value = $rule.rule_content_ID}
                                            {assign var = "content_id" value = $rule.content_ID}
                                            <tr class = "{cycle values = "oddRowColor, evenRowColor"} defaultRowHeight {if (!$T_TREE_ACTIVE.$content_id || !$T_TREE_ACTIVE.$rule_content_id) && {$rule.rule_type != 'serial' && $rule.rule_type != 'tree'}}deactivatedTableElement{/if}">
                                            {if ($rule.users_LOGIN != '*')}
                                                <td>{$rule.users_LOGIN}</td>
                                            {else}
                                                <td>{$smarty.const._ALLOFTHEM}</td>
                                            {/if}

                                            {if ($rule.rule_type == 'always')}
                                                    <td>{$smarty.const._STUDENTALLWAYS} </td>
                                            {elseif ($rule.rule_type == 'hasnot_seen')}
                                                    <td>{$smarty.const._IFSTUDENTHASNOTSEEN} {$smarty.const._THEUNIT} "{$T_TREE_NAMES.$rule_content_id}"</td>
                                            {elseif ($rule.rule_type == 'hasnot_passed')}
                                                    <td>{$smarty.const._IFSTUDENTHASNOTPASSED} {$smarty.const._THETEST} "{$T_TREE_NAMES.$rule_content_id}" {$smarty.const._WITHSCOREATLEAST} {$rule.rule_option*100}%</td>
                                            {elseif ($rule.rule_type == 'serial')}
                                                    <td>{$smarty.const._SERIALRULE}</td>
                                            {elseif ($rule.rule_type == 'tree')}
                                                    <td>{$smarty.const._TREERULE}</td>
                                            {/if}
                                                    <td>{$T_TREE_NAMES.$content_id}</td>
                                                    <td class = "centerAlign">
                                            {if !isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change'}
                                                {if $rule.rule_type != 'serial' && $rule.rule_type != 'tree'}
                                                        <a class = "editLink"   href = "{$smarty.server.PHP_SELF}?ctg=rules&edit_rule={$rule.id}"><img src = "images/16x16/edit.png" alt = "{$smarty.const._CORRECTION}" title = "{$smarty.const._CORRECTION}" border = "0"/></a>
                                                {/if}
                                                        <a class = "deleteLink" href = "{$smarty.server.PHP_SELF}?ctg=rules&delete_rule={$rule.id}"><img src = "images/16x16/delete.png" alt = "{$smarty.const._DELETE}" title = "{$smarty.const._DELETE}" border = "0" onclick = "return confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')"/></a>
                                            {/if}
                                                    </td></tr>
                                        {foreachelse}
                                                <tr class = "oddRowColor defaultRowHeight"><td colspan = "5" class = "emptyCategory centerAlign">{$smarty.const._NODATAFOUND}</td></tr>
                                        {/foreach}
                                            </table>

                                {/capture}

                                <div class = "tabber">
                                    <div class = "tabbertab">
                                        <h3>{$smarty.const._CONTENTTRAVERSINGRULES}</h3>
                                        {eF_template_printInnerTable title=$smarty.const._LESSONRULES data=$smarty.capture.t_lesson_rules image='32x32/recycle.png'}
                                    </div>
                                    <div class = "tabbertab{if $smarty.get.tab=='conditions'} tabbertabdefault{/if}">
                                        <h3>{$smarty.const._LESSONCONDITIONS}</h3>
                                        {eF_template_printInnerTable title = $smarty.const._LESSONCONDITIONS data = $smarty.capture.t_conditions_code image = '32x32/recycle.png'}
                                    </div>
                                </div>
                            {/if}
                            </td></tr>
    {/capture}

{elseif ($T_CTG == 'glossary')}
{*moduleGlossary: The page listing the Glossary terms*}
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=glossary">'|cat:$smarty.const._GLOSSARY|cat:'</a>'}
    {capture name = "moduleGlossary"}
                            <tr><td class = "moduleCell">
                            {* Format T_GLOSSARY into html code *}
                            {capture name='t_glossary_code'}
                                {eF_template_printGlossary data = $T_GLOSSARY user_type = 'professor'}
                            {/capture}

                            {eF_template_printInnerTable title = $smarty.const._GLOSSARY data = $smarty.capture.t_glossary_code image = '32x32/book_open2.png'}

                            {* Hidden used for the add definition popup to define whether this page should be reloaded on popup close or not *}
                            <input type="hidden" id="reloadHidden" value="" />

                            </td></tr>
    {/capture}

{elseif ($T_CTG == 'calendar')}
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=calendar">'|cat:$smarty.const._CALENDAR|cat:'</a>'}
    {*moduleCalendarPage: Display the calendar page*}
    {capture name = "moduleCalendarPage"}
                            <tr><td class = "moduleCell">
                                {include file = "calendar.tpl"}
                                {eF_template_printInnerTable title=$T_CALENDAR_TITLE data=$smarty.capture.t_calendar_code image='32x32/calendar.png' main_options=$T_CALENDAR_OPTIONS}
                            </td></tr>
    {/capture}


{elseif ($T_CTG == 'settings')}
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=settings">'|cat:$smarty.const._LESSONSETTINGS|cat:'</a>'}
    {capture name = "moduleLessonSettings"}
    {if isset($T_OP) && $T_OP == 'reset_lesson'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=settings&op=reset_lesson">'|cat:$smarty.const._RESTARTLESSON|cat:'</a>'}
    <tr><td class = "moduleCell">
                                    {capture name = 't_reset_lesson_code'}
                                        {$T_RESET_LESSON_FORM.javascript}
                                        <form {$T_RESET_LESSON_FORM.attributes}>
                                            {$T_RESET_LESSON_FORM.hidden}
                                            <table class = "formElements" style = "margin-left:0px">
                                                <tr><td colspan = "100%">{$smarty.const._CHOOSEWHATTODELETE}</td></tr>
                                                <tr><td colspan = "100%">&nbsp;</td></tr>
                                                <tr><td class = "labelCell">{$smarty.const._USERS}:&nbsp;</td>
                                                    <td>{$T_RESET_LESSON_FORM.users.html}</td></tr>
                                                <tr><td class = "labelCell">{$smarty.const._ANNOUNCEMENTS}:&nbsp;</td>
                                                    <td>{$T_RESET_LESSON_FORM.news.html}</td></tr>
                                                <tr><td class = "labelCell">{$smarty.const._COMMENTS}:&nbsp;</td>
                                                    <td>{$T_RESET_LESSON_FORM.comments.html}</td></tr>
                                                <tr><td class = "labelCell">{$smarty.const._ACCESSRULES}:&nbsp;</td>
                                                    <td>{$T_RESET_LESSON_FORM.rules.html}</td></tr>
                                                <tr><td class = "labelCell">{$smarty.const._CALENDAR}:&nbsp;</td>
                                                    <td>{$T_RESET_LESSON_FORM.calendar.html}</td></tr>
                                                <tr><td class = "labelCell">{$smarty.const._GLOSSARY}:&nbsp;</td>
                                                    <td>{$T_RESET_LESSON_FORM.glossary.html}</td></tr>
                                                <tr><td class = "labelCell">{$smarty.const._USERTRACKINGINFORMATION}:&nbsp;</td>
                                                    <td>{$T_RESET_LESSON_FORM.tracking.html}</td></tr>
                                                <tr><td colspan = "100%">&nbsp;</td></tr>
                                                <tr><td></td><td>{$T_RESET_LESSON_FORM.submit_reset_lesson.html}</td></tr>
                                            </table>
                                        </form>
                                    {/capture}
                                    {eF_template_printInnerTable title = $smarty.const._RESTARTLESSON data = $smarty.capture.t_reset_lesson_code image = '32x32/refresh.png' main_options = $T_TABLE_OPTIONS}
    </td></tr>

    {elseif isset($T_OP) && $T_OP == 'import_lesson'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=settings&op=import_lesson">'|cat:$smarty.const._IMPORTLESSON|cat:'</a>'}
    <tr><td class = "moduleCell">
                                    {capture name = 't_import_lesson_code'}
                                        <fieldset>
                                        <legend>{$smarty.const._IMPORTLESSON}</legend>
                                        {$T_IMPORT_LESSON_FORM.javascript}
                                        <form {$T_IMPORT_LESSON_FORM.attributes}>
                                            {$T_IMPORT_LESSON_FORM.hidden}
                                            <table class = "formElements">
                                            	<tr><td colspan = "2">{$smarty.const._IMPORTNOTICE}</td></tr>
                                                <tr><td class = "labelCell">{$smarty.const._LESSONDATAFILE}:&nbsp;</td>
                                                    <td>{$T_IMPORT_LESSON_FORM.file_upload.html}</td></tr>
                                                <tr><td></td><td class = "infoCell">{$smarty.const._EACHFILESIZEMUSTBESMALLERTHAN} <b>{$T_MAX_FILESIZE}</b> {$smarty.const._KB}</td></tr>
                                                <tr><td colspan = "100%">&nbsp;</td></tr>
                                                <tr><td></td><td>{$T_IMPORT_LESSON_FORM.submit_import_lesson.html}</td></tr>
                                            </table>
                                        </form>
                                        </fieldset>
                                    {/capture}
                                    {eF_template_printInnerTable title = $smarty.const._IMPORTLESSON data = $smarty.capture.t_import_lesson_code image = '32x32/import2.png'  main_options = $T_TABLE_OPTIONS}
    </td></tr>
    {elseif isset($T_OP) && $T_OP == 'export_lesson'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=settings&op=export_lesson">'|cat:$smarty.const._EXPORTLESSON|cat:'</a>'}
    <tr><td class = "moduleCell">
                                    {capture name = 't_export_lesson_code'}
                                        <fieldset>
                                        <legend>{$smarty.const._EXPORTLESSON}</legend>
                                        {$T_EXPORT_LESSON_FORM.javascript}
                                        <form {$T_EXPORT_LESSON_FORM.attributes}>
                                            {$T_EXPORT_LESSON_FORM.hidden}
                                            <table class = "formElements" style = "margin-left:0px">                                        {if $T_NEW_EXPORTED_FILE}
                                                <tr><td colspan = "2">{$smarty.const._DOWNLOADEXPORTED}:&nbsp; <a href = "view_file.php?file={$T_NEW_EXPORTED_FILE.id}&action=download">{$T_NEW_EXPORTED_FILE.name}</a> ({$T_NEW_EXPORTED_FILE.size} {$smarty.const.KB}, #filter:timestamp-{$T_NEW_EXPORTED_FILE.timestamp}#)</td></tr>
                                        {elseif $T_EXPORTED_FILE}
                                                <tr><td colspan = "2">{$smarty.const._EXISTINGFILE}:&nbsp;<a href = "view_file.php?file={$T_EXPORTED_FILE.id}&action=download">{$T_EXPORTED_FILE.name}</a> ({$T_EXPORTED_FILE.size} {$smarty.const.KB}, #filter:timestamp-{$T_EXPORTED_FILE.timestamp}#)</td></tr>
                                        {/if}
                                                <tr><td class = "labelCell">{$smarty.const._CLICKTOEXPORT}:&nbsp;</td>
                                                    <td class = "elementCell">{$T_EXPORT_LESSON_FORM.submit_export_lesson.html}</td></tr>
                                            </table>
                                        </form>
                                        </fieldset>
                                    {/capture}
                                    {eF_template_printInnerTable title = $smarty.const._EXPORTLESSON data = $smarty.capture.t_export_lesson_code image = '32x32/export1.png' main_options = $T_TABLE_OPTIONS}
    </td></tr>
    {elseif isset($T_OP) && $T_OP == 'lesson_users'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=settings&op=lesson_users">'|cat:$smarty.const._LESSONUSERS|cat:'</a>'}
    <tr><td class = "moduleCell">

                              {capture name = 't_users_to_lessons_code'}
<!--ajax:usersTable-->
                                                    <table style = "width:100%" class = "sortedTable" size = "{$T_USERS_SIZE}" sortBy = "0" id = "usersTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "professor.php?ctg=settings&op=lesson_users&">
                                                        <tr class = "topTitle">
                                                            <td class = "topTitle" name = "login">{$smarty.const._LOGIN}</td>
                                                            <td class = "topTitle" name = "name">{$smarty.const._NAME}</td>
                                                            <td class = "topTitle" name = "surname">{$smarty.const._SURNAME}</td>
                                                            <td class = "topTitle" name = "user_type">{$smarty.const._USERTYPE}</td>
                                                            <td class = "topTitle" name = "role">{$smarty.const._USERROLEINLESSON}</td>
                                                            {if $T_MODULE_HCD_INTERFACE}
                                                            <td class = "topTitle centerAlign">{$smarty.const._OPERATIONS}</td>
                                                            {/if}
                                                            <td class = "topTitle centerAlign" name = "in_lesson">{$smarty.const._STATUS}</td>
                                                        </tr>
                                {foreach name = 'users_to_lessons_list' key = 'key' item = 'user' from = $T_ALL_USERS}
                                                        <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$user.active}deactivatedTableElement{/if}">
                                                            <td>{$user.login}</td>
                                                            <td>{$user.name}</td>
                                                            <td>{$user.surname}</td>
                                                            <td>{$user.basic_user_type}</td>
                                                            <td>
                                    {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
                                                                <select name="type_{$user.login}" id = "type_{$user.login}" onchange = "$('checked_{$user.login}').checked=true;ajaxPost('{$user.login}', this);">
                                        {foreach name = 'roles_list' key = 'role_key' item = 'role_item' from = $T_ROLES}
                                                                    <option value="{$role_key}" {if ($user.role == $role_key)}selected{/if}>{$role_item}</option>
                                        {/foreach}
                                                                </select>
                                    {else}
                                                                {$T_ROLES[$user.role]}
                                    {/if}
                                                            </td>
                                                            {if $T_MODULE_HCD_INTERFACE}
                                                            <td align="center">
                                                                <a href = "{$smarty.server.PHP_SELF}?ctg=users&edit_user={$user.login}&add_evaluation=1&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._NEWEVALUATION}', new Array('400px', '300px'))">
                                                                    <img src="images/16x16/edit.png" title="{$smarty.const._NEWEVALUATION}" alt="{$smarty.const._NEWEVALUATION}"/ border="0"></a>
                                                                <a href = "{$smarty.server.PHP_SELF}?ctg=evaluations&user={$user.login}">
                                                                    <img src="images/16x16/view.png" title="{$smarty.const._SHOWEVALUATIONS}" alt="{$smarty.const._SHOWEVALUATIONS}"/ border="0"></a>
                                                            </td>
                                                            {/if}
                                                            <td align="center">
                                    {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
                                                                <input class = "inputCheckbox" type = "checkbox" name = "checked_{$user.login}" id = "checked_{$user.login}" onclick = "ajaxPost('{$user.login}', this);" {if in_array($user.login, $T_LESSON_USERS)}checked = "checked"{/if} />
                                    {else}
                                                                 {if in_array($user.login, $T_LESSON_USERS)}<img src = "images/16x16/check2.png" title = "{$smarty.const._LESSONUSER}" alt = "{$smarty.const._LESSONUSER}" >{/if}
                                    {/if}
                                                            </td>
                                                    </tr>
                                {/foreach}
                                </table>
<!--/ajax:usersTable-->

        {literal}
        <script>
        function ajaxPost(login, el, table_id) {
            var baseUrl =  'professor.php?ctg=settings&op=lesson_users&postAjaxRequest=1';
            if (login) {
                var userType = $('type_'+login).options[$('type_'+login).selectedIndex].value;
                var checked  = $('checked_'+login).checked;
                var url      = baseUrl + '&login='+login+'&user_type='+userType;
                var img_id   = 'img_'+login;
            } else if (table_id && table_id == 'usersTable') {
                el.checked ? url = baseUrl + '&addAll=1' : url = baseUrl + '&removeAll=1';
                var img_id   = 'img_selectAll';
            }

            var position = eF_js_findPos(el);
            var img      = document.createElement("img");

            img.style.position = 'absolute';
            img.style.top      = Element.positionedOffset(Element.extend(el)).top  + 'px';
            img.style.left     = Element.positionedOffset(Element.extend(el)).left + 6 + Element.getDimensions(Element.extend(el)).width + 'px';

            img.setAttribute("id", img_id);
            img.setAttribute('src', 'images/others/progress1.gif');

            el.parentNode.appendChild(img);

                new Ajax.Request(url, {
                        method:'get',
                        asynchronous:true,
                        onSuccess: function (transport) {
                            img.style.display = 'none';
                            img.setAttribute('src', 'images/16x16/check.png');
                            new Effect.Appear(img_id);
                            window.setTimeout('Effect.Fade("'+img_id+'")', 2500);
                            }
                    });
        }
        </script>
        {/literal}

        {/capture}
                    {eF_template_printInnerTable title = $smarty.const._UPDATEUSERSTOLESSONS data = $smarty.capture.t_users_to_lessons_code image = '32x32/book_blue_preferences.png' main_options = $T_TABLE_OPTIONS}

    </td></tr>

    {elseif isset($T_OP) && $T_OP == 'lesson_layout'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=settings&op=lesson_layout">'|cat:$smarty.const._LAYOUT|cat:'</a>'}
    <tr><td class = "moduleCell">
        {capture name = "t_layout_code"}
            <table style = "margin-left:auto;margin-right:auto">
                <tr><td style = "width:16px">
                    <input type = "submit" value = "{$smarty.const._SAVECHANGES}" onclick = "updatePositions()"/>
                </td><td style = "width:16px">
                    <img src = "images/others/progress1.gif" id = "progress_image" alt = "{$smarty.const._LOADING}" title = "{$smarty.const._LOADING}" style = "vertical-align:middle;display:none"/>
                </td></tr>
            </table>
            <br/>


                    {capture name = "layout_moduleContentTree"}
                        <li id = "firstlist_moduleContentTree">
                            <table class = "innerTable" style = "width:100%">
                                <tr class = "handle">
                                    <th class = "innerTableHeader">
                                        <img class = "iconTableImage" src = "images/32x32/book_open.png" alt = "{$smarty.const._CURRENTCONTENT}" title = "{$smarty.const._CURRENTCONTENT}">
                                        {$smarty.const._CURRENTCONTENT}
                                    </th>
                                    <td class = "innerTableTd" style = "text-align:right"><img src = "images/others/blank.gif" {if isset($T_DEFAULT_POSITIONS.visibility.moduleContentTree) && $T_DEFAULT_POSITIONS.visibility.moduleContentTree == 0}class = "plus"{else}class = "minus"{/if} onclick = "toggleVisibility(Element.extend(this).up().up().next().next().down(), this);"></td></tr>
                                <tr><td colspan = "2" class = "horizontalSeparator"></td></tr>
                                <tr><td colspan = "2" style = "height:60px;text-align:center;vertical-align:top;{if isset($T_DEFAULT_POSITIONS.visibility.moduleContentTree) && $T_DEFAULT_POSITIONS.visibility.moduleContentTree == 0}display:none;{/if}"><img src = "images/others/content_tree_thumbnail.png"></td></tr>
                            </table>
                        </li>
                    {/capture}
                    {capture name = "layout_moduleProjectsList"}
                        <li id = "firstlist_moduleProjectsList">
                            <table class = "innerTable" style = "width:100%">
                                <tr class = "handle">
                                    <th class = "innerTableHeader">
                                        <img class = "iconTableImage" src = "images/32x32/exercises.png" alt = "{$smarty.const._PROJECTS}" title = "{$smarty.const._PROJECTS}">
                                        {$smarty.const._PROJECTS}
                                    </th>
                                    <td class = "innerTableTd" style = "text-align:right"><img src = "images/others/blank.gif" {if isset($T_DEFAULT_POSITIONS.visibility.moduleProjectsList) && $T_DEFAULT_POSITIONS.visibility.moduleProjectsList == 0}class = "plus"{else}class = "minus"{/if} onclick = "toggleVisibility(Element.extend(this).up().up().next().next().down(), this);"></td></tr>
                                <tr><td colspan = "2" class = "horizontalSeparator"></td></tr>
                                <tr><td colspan = "2" style = "height:60px;text-align:center;vertical-align:top;{if isset($T_DEFAULT_POSITIONS.visibility.moduleProjectsList) && $T_DEFAULT_POSITIONS.visibility.moduleProjectsList == 0}display:none;{/if}"><img src = "images/others/projects_thumbnail.png"></td></tr>
                            </table>
                        </li>
                    {/capture}
                    {capture name = "layout_moduleNewsList"}
                        <li id = "secondlist_moduleNewsList">
                            <table class = "innerTable" style = "width:100%">
                                <tr class = "handle">
                                    <th class = "innerTableHeader">
                                        <img class = "iconTableImage" src = "images/32x32/news.png" alt = "{$smarty.const._ANNOUNCEMENTS}" title = "{$smarty.const._ANNOUNCEMENTS}">
                                        {$smarty.const._ANNOUNCEMENTS}
                                    </th>
                                    <td class = "innerTableTd" style = "text-align:right"><img src = "images/others/blank.gif" {if isset($T_DEFAULT_POSITIONS.visibility.moduleNewsList) && $T_DEFAULT_POSITIONS.visibility.moduleNewsList == 0}class = "plus"{else}class = "minus"{/if} onclick = "toggleVisibility(Element.extend(this).up().up().next().next().down(), this);"></td></tr>
                                <tr><td colspan = "2" class = "horizontalSeparator"></td></tr>
                                <tr><td colspan = "2" style = "height:60px;text-align:center;vertical-align:top;{if isset($T_DEFAULT_POSITIONS.visibility.moduleNewsList) && $T_DEFAULT_POSITIONS.visibility.moduleNewsList == 0}display:none;{/if}"><img src = "images/others/news_thumbnail.png"></td></tr>
                            </table>
                        </li>
                    {/capture}
                    {capture name = "layout_modulePersonalMessagesList"}
                        <li id = "secondlist_modulePersonalMessagesList">
                            <table class = "innerTable" style = "width:100%">
                                <tr class = "handle">
                                    <th class = "innerTableHeader">
                                        <img class = "iconTableImage" src = "images/32x32/mail2.png" alt = "{$smarty.const._PERSONALMESSAGES}" title = "{$smarty.const._PERSONALMESSAGES}">
                                        {$smarty.const._PERSONALMESSAGES}
                                    </th>
                                    <td class = "innerTableTd" style = "text-align:right"><img src = "images/others/blank.gif" {if isset($T_DEFAULT_POSITIONS.visibility.modulePersonalMessagesList) && $T_DEFAULT_POSITIONS.visibility.modulePersonalMessagesList == 0}class = "plus"{else}class = "minus"{/if} onclick = "toggleVisibility(Element.extend(this).up().up().next().next().down(), this);"></td></tr>
                                <tr><td colspan = "2" class = "horizontalSeparator"></td></tr>
                                <tr><td colspan = "2" style = "height:60px;text-align:center;vertical-align:top;{if isset($T_DEFAULT_POSITIONS.visibility.modulePersonalMessagesList) && $T_DEFAULT_POSITIONS.visibility.modulePersonalMessagesList == 0}display:none;{/if}"><img src = "images/others/personal_messages_thumbnail.png"></td></tr>
                            </table>
                        </li>
                    {/capture}
                    {capture name = "layout_moduleForumList"}
                        <li id = "secondlist_moduleForumList">
                            <table class = "innerTable" style = "width:100%">
                                <tr class = "handle">
                                    <th class = "innerTableHeader">
                                        <img class = "iconTableImage" src = "images/32x32/messages.png" alt = "{$smarty.const._RECENTMESSAGESATFORUM}" title = "{$smarty.const._RECENTMESSAGESATFORUM}">
                                        {$smarty.const._RECENTMESSAGESATFORUM}
                                    </th>
                                    <td class = "innerTableTd" style = "text-align:right"><img src = "images/others/blank.gif" {if isset($T_DEFAULT_POSITIONS.visibility.moduleForumList) && $T_DEFAULT_POSITIONS.visibility.moduleForumList == 0}class = "plus"{else}class = "minus"{/if} onclick = "toggleVisibility(Element.extend(this).up().up().next().next().down(), this);"></td></tr>
                                <tr><td colspan = "2" class = "horizontalSeparator"></td></tr>
                                <tr><td colspan = "2" style = "height:60px;text-align:center;vertical-align:top;{if isset($T_DEFAULT_POSITIONS.visibility.moduleForumList) && $T_DEFAULT_POSITIONS.visibility.moduleForumList == 0}display:none;{/if}"><img src = "images/others/forum_thumbnail.png"></td></tr>
                            </table>
                        </li>
                    {/capture}
                    {capture name = "layout_moduleComments"}
                        <li id = "secondlist_moduleComments">
                            <table class = "innerTable" style = "width:100%">
                                <tr class = "handle">
                                    <th class = "innerTableHeader">
                                        <img class = "iconTableImage" src = "images/32x32/note.png" alt = "{$smarty.const._COMMENTS}" title = "{$smarty.const._COMMENTS}">
                                        {$smarty.const._COMMENTS}
                                    </th>
                                    <td class = "innerTableTd" style = "text-align:right"><img src = "images/others/blank.gif" {if isset($T_DEFAULT_POSITIONS.visibility.moduleComments) && $T_DEFAULT_POSITIONS.visibility.moduleComments == 0}class = "plus"{else}class = "minus"{/if} onclick = "toggleVisibility(Element.extend(this).up().up().next().next().down(), this);"></td></tr>
                                <tr><td colspan = "2" class = "horizontalSeparator"></td></tr>
                                <tr><td colspan = "2" style = "height:60px;text-align:center;vertical-align:top;{if isset($T_DEFAULT_POSITIONS.visibility.moduleComments) && $T_DEFAULT_POSITIONS.visibility.moduleComments == 0}display:none;{/if}"><img src = "images/others/comments_thumbnail.png"></td></tr>
                            </table>
                        </li>
                    {/capture}
                    {capture name = "layout_moduleCalendar"}
                        <li id = "secondlist_moduleCalendar">
                            <table class = "innerTable" style = "width:100%">
                                <tr class = "handle">
                                    <th class = "innerTableHeader">
                                        <img class = "iconTableImage" src = "images/32x32/calendar.png" alt = "{$smarty.const._CALENDAR}" title = "{$smarty.const._CALENDAR}">
                                        {$smarty.const._CALENDAR}
                                    </th>
                                    <td class = "innerTableTd" style = "text-align:right"><img src = "images/others/blank.gif" {if isset($T_DEFAULT_POSITIONS.visibility.moduleCalendar) && $T_DEFAULT_POSITIONS.visibility.moduleCalendar == 0}class = "plus"{else}class = "minus"{/if} onclick = "toggleVisibility(Element.extend(this).up().up().next().next().down(), this);"></td></tr>
                                <tr><td colspan = "2" class = "horizontalSeparator"></td></tr>
                                <tr><td colspan = "2" style = "height:60px;text-align:center;vertical-align:top;{if isset($T_DEFAULT_POSITIONS.visibility.moduleCalendar) && $T_DEFAULT_POSITIONS.visibility.moduleCalendar == 0}display:none;{/if}"><img src = "images/others/calendar_thumbnail.png"></td></tr>
                            </table>
                        </li>
                    {/capture}
                    {capture name = "layout_moduleDigitalLibrary"}
                        <li id = "secondlist_moduleDigitalLibrary">
                            <table class = "innerTable" style = "width:100%">
                                <tr class = "handle">
                                    <th class = "innerTableHeader">
                                        <img class = "iconTableImage" src = "images/32x32/folder_view.png" alt = "{$smarty.const._DIGITALLIBRARY}" title = "{$smarty.const._DIGITALLIBRARY}">
                                        {$smarty.const._DIGITALLIBRARY}
                                    </th>
                                    <td class = "innerTableTd" style = "text-align:right"><img src = "images/others/blank.gif" {if isset($T_DEFAULT_POSITIONS.visibility.moduleDigitalLibrary) && $T_DEFAULT_POSITIONS.visibility.moduleDigitalLibrary == 0}class = "plus"{else}class = "minus"{/if} onclick = "toggleVisibility(Element.extend(this).up().up().next().next().down(), this);"></td></tr>
                                <tr><td colspan = "2" class = "horizontalSeparator"></td></tr>
                                <tr><td colspan = "2" style = "height:60px;text-align:center;vertical-align:top;{if isset($T_DEFAULT_POSITIONS.visibility.moduleDigitalLibrary) && $T_DEFAULT_POSITIONS.visibility.moduleDigitalLibrary == 0}display:none;{/if}"><img src = "images/others/digital_library_thumbnail.png"></td></tr>
                            </table>
                        </li>
                    {/capture}
                {foreach name = 'lesson_modules_list' item = "module" key = "class_name" from = $T_LESSON_MODULES}
                    {assign var = module_name value = $class_name|replace:"_":""}
                    {capture name = "layout_"|cat:$module_name}
                        <li id = "secondlist_{$class_name|replace:"_":""}">
                            <table class = "innerTable" style = "width:100%">
                                <tr class = "handle">
                                    <th class = "innerTableHeader">
                                        <img class = "iconTableImage" src = "images/32x32/component_green.png" alt = "{$module.title}" title = "{$module.title}">
                                        {$module.title}
                                    </th>
                                    <td class = "innerTableTd" style = "text-align:right"><img src = "images/others/blank.gif" {if isset($T_DEFAULT_POSITIONS.visibility.$module_name) && $T_DEFAULT_POSITIONS.visibility.$module_name == 0}class = "plus"{else}class = "minus"{/if} onclick = "toggleVisibility(Element.extend(this).up().up().next().next().down(), this);"></td></tr>
                                <tr><td colspan = "2" class = "horizontalSeparator"></td></tr>
                                <tr><td colspan = "2" style = "height:60px;text-align:center;vertical-align:middle;{if isset($T_DEFAULT_POSITIONS.visibility.$module_name) && $T_DEFAULT_POSITIONS.visibility.$module_name == 0}display:none;{/if}"><img src = "images/16x16/component_green.png" alt = "{$smarty.const._MODULE}" title = "{$smarty.const._MODULE}"></td></tr>
                            </table>
                        </li>
                    {/capture}
                {/foreach}
            <div id = "sortableList" style = "width:602px;margin-left:auto;margin-right:auto;">
                <div style = "float:left; width:300px">
                    <ul class = "sortable" id = "firstlist" style = "width:100%">
                    {if !in_array('moduleContentTree', $T_DEFAULT_POSITIONS.first) && !in_array('moduleContentTree', $T_DEFAULT_POSITIONS.second)}{$smarty.capture.layout_moduleContentTree}{/if}
                    {if !in_array('moduleProjectsList', $T_DEFAULT_POSITIONS.first) && !in_array('moduleProjectsList', $T_DEFAULT_POSITIONS.second)}{$smarty.capture.layout_moduleProjectsList}{/if}

                    {foreach name = positions_first key = "key" item = "innerTable" from = $T_DEFAULT_POSITIONS.first}
                        {if !$T_INVALID_OPTIONS.$innerTable}
                            {assign var = "capture_name" value = "layout_"|cat:$innerTable}
                            {$smarty.capture.$capture_name}
                        {/if}
                    {/foreach}
                    </ul>
                </div>
                <div style="float:right; width:300px">
                    <ul class = "sortable" id = "secondlist" style = "width:100%">
                    {if !in_array('moduleNewsList', $T_DEFAULT_POSITIONS.first) && !in_array('moduleNewsList', $T_DEFAULT_POSITIONS.second)}{$smarty.capture.layout_moduleNewsList}{/if}
                    {if !in_array('modulePersonalMessagesList', $T_DEFAULT_POSITIONS.first) && !in_array('modulePersonalMessagesList', $T_DEFAULT_POSITIONS.second)}{$smarty.capture.layout_modulePersonalMessagesList}{/if}
                    {if !in_array('moduleForumList', $T_DEFAULT_POSITIONS.first) && !in_array('moduleForumList', $T_DEFAULT_POSITIONS.second)}{$smarty.capture.layout_moduleForumList}{/if}
                    {if !in_array('moduleComments', $T_DEFAULT_POSITIONS.first) && !in_array('moduleComments', $T_DEFAULT_POSITIONS.second)}{$smarty.capture.layout_moduleComments}{/if}
                    {if !in_array('moduleCalendar', $T_DEFAULT_POSITIONS.first) && !in_array('moduleCalendar', $T_DEFAULT_POSITIONS.second)}{$smarty.capture.layout_moduleCalendar}{/if}
                    {if !in_array('moduleDigitalLibrary', $T_DEFAULT_POSITIONS.first) && !in_array('moduleDigitalLibrary', $T_DEFAULT_POSITIONS.second)}{$smarty.capture.layout_moduleDigitalLibrary}{/if}

                    {foreach name = positions_second key = "key" item = "innerTable" from = $T_DEFAULT_POSITIONS.second}
                        {if !$T_INVALID_OPTIONS.$innerTable}
                            {assign var = "capture_name" value = "layout_"|cat:$innerTable}
                            {$smarty.capture.$capture_name}
                        {/if}
                    {/foreach}
                    {foreach name = 'lesson_modules_list' item = "module" key = "class_name" from = $T_LESSON_MODULES}
                        {assign var = module_name value = $class_name|replace:"_":""}
                        {assign var = layout_name value = "layout_"|cat:$module_name}
                        {if !in_array($module_name, $T_DEFAULT_POSITIONS.first) && !in_array($module_name, $T_DEFAULT_POSITIONS.second)}{$smarty.capture.$layout_name}{/if}
                    {/foreach}
                    </ul>
                </div>
            </div>

        <script type = "text/javascript">
        {literal}
            function updatePositions() {
                var str = '';
                $('firstlist').select('li').each(function (s)  {str += 'visibility['+ s.id.replace(/.*_/, '') + ']=' + (s.select('img')[1].className == 'plus' ? 0 : 1) + '&'});
                $('secondlist').select('li').each(function (s) {str += 'visibility['+ s.id.replace(/.*_/, '') + ']=' + (s.select('img')[1].className == 'plus' ? 0 : 1) + '&'});
                str = str.substring(0, str.length - 1); //Remove trailing &
                $('progress_image').setAttribute('src', 'images/others/progress1.gif');
                $('progress_image').show();
                new Ajax.Request('set_positions.php?set_default=1', {
                    method:'post',
                    asynchronous:true,
                    parameters: { firstlist: Sortable.serialize('firstlist'), secondlist: Sortable.serialize('secondlist'), visibility: str },
                    onSuccess: function (transport) {
                        $('progress_image').hide();
                        $('progress_image').setAttribute('src', 'images/16x16/check.png');
                        new Effect.Appear('progress_image');
                        window.setTimeout('Effect.Fade("progress_image")', 2500);
                    }
                });
            }
            Sortable.create("firstlist", {
                containment:["firstlist","secondlist"], constraint:false
            });
            Sortable.create("secondlist", {
                containment:["firstlist", "secondlist"], constraint:false
            });
        {/literal}
        </script>
        {/capture}
        {eF_template_printInnerTable title = $smarty.const._LAYOUT data = $smarty.capture.t_layout_code image = '32x32/layout_center.png' main_options = $T_TABLE_OPTIONS}
    </td></tr>

    {else}
        {*moduleLessonSettings: Left options list in the Lesson settings page*}
    <tr><td class = "moduleCell">
                                    {eF_template_printIconTable title=$smarty.const._LESSONOPTIONS columns = 4 links = $T_LESSON_SETTINGS image='32x32/book_blue_view.png' options = $T_TABLE_OPTIONS}
                                    <script>
                                    {literal}
                                        function activate(el, action) {
                                            var src = Element.down(el).src;
                                            src.match(/_gray/) ? url = 'professor.php?ctg=settings&ajax=1&activate='+action : url = 'professor.php?ctg=settings&ajax=1&deactivate='+action;;
                                            Element.down(el).blur();
                                            Element.down(el).setAttribute('src', 'images/others/progress_big.gif');
                                            new Ajax.Request(url, {
                                                    method:'get',
                                                    asynchronous:true,
                                                    onSuccess: function (transport) {
                                                        if (src.match(/_gray/)) {
                                                            Element.down(el).setAttribute('src', src.replace(/_gray/, ''));
                                                            el.setStyle({color:'inherit'});
                                                        } else {
                                                            Element.down(el).setAttribute('src', src.replace(/.png/, '_gray.png'));
                                                            el.setStyle({color:'gray'});
                                                        }
                                                        
                                                        parent.sideframe.location = parent.sideframe.location + '&sbctg=settings';
                                                        
                                                    }
                                                });
                                        }
                                    {/literal}
                                    </script>
    </td></tr>
    {/if}
{/capture}

{elseif ($T_CTG == 'lessons')}
    {assign var = "title" value = '<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=lessons">'|cat:$smarty.const._MYLESSONS|cat:'</a>'}
    {capture name = "moduleLessonsList"}
                          <tr><td class = "moduleCell">
                            {if $T_OP == course_info}
                                {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=lessons&course='|cat:$smarty.get.course|cat:'&op=course_info">'|cat:$smarty.const._INFORMATIONFORCOURSE|cat:' &quot;'|cat:$T_CURRENT_COURSE->course.name|cat:'&quot;</a>'}
                                {capture name = 't_course_info_code'}
                                    <fieldset>
                                        <legend>{$smarty.const._COURSEINFORMATION}</legend>
                                        {$T_COURSE_INFO_HTML}
                                    </fieldset>
                                    <fieldset>
                                        <legend>{$smarty.const._COURSEMETADATA}</legend>
                                        {$T_COURSE_METADATA_HTML}
                                    </fieldset>
                                {/capture}
                                {eF_template_printInnerTable title = $smarty.const._INFORMATIONFORCOURSE|cat:' &quot;'|cat:$T_CURRENT_COURSE->course.name|cat:'&quot;' data = $smarty.capture.t_course_info_code image = '32x32/about.png'  main_options = $T_TABLE_OPTIONS}
                            {elseif $T_OP == 'course_certificates'}
                                {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=lessons&course='|cat:$smarty.get.course|cat:'&op=course_certificates">'|cat:$smarty.const._CERTIFICATESFORCOURSE|cat:' &quot;'|cat:$T_CURRENT_COURSE->course.name|cat:'&quot;</a>'}
                                {if $smarty.get.edit_user}
                                    {capture name = 't_course_user_progress'}
                                    <fieldset>
                                        <legend>{$smarty.const._LESSONSPROGRESS}</legend>
                                        <table width = "100%">
                                            <tr>
                                        {foreach name = 'lessons_list' item = "lesson" key = "id" from = $T_USER_PROGRESS.lesson_status}
                                                <td width = "50%">
                                                <table>
                                                    <tr><td colspan = "2" style = "font-weight:bold">{$lesson.lesson_name}</td></tr>
                                                    <tr><td>{$smarty.const._COMPLETED}:&nbsp;</td><td>{if $lesson.completed}{$smarty.const._YES}{else}{$smarty.const._NO}{/if}</td></tr>
                                                    {if $lesson.score}<tr><td>{$smarty.const._SCORE}:&nbsp;</td><td>{$lesson.score}&nbsp;%</td></tr>{/if}
                                                    <tr><td>{$smarty.const._CONTENTDONE}:&nbsp;</td>
                                                        <td class = "progressCell" style = "vertical-align:top">
                                                            <span class = "progressNumber">{$lesson.overall_progress}%</span>
                                                            <span class = "progressBar" style = "width:{$lesson.percentage_done}px;">&nbsp;</span>
                                                        </td></tr>
                                                </table>
                                                </td>
                                            {if $smarty.foreach.lessons_list.iteration%2 == 0}</tr><tr>{/if}
                                        {/foreach}
                                            </tr>
                                        </table>
                                    </fieldset>
                                    <fieldset>
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
                                    {eF_template_printInnerTable title = "`$T_USER_PROGRESS.name` `$T_USER_PROGRESS.surname`&#039s `$smarty.const._PROGRESS`" data = $smarty.capture.t_course_user_progress image = '32x32/books_preferences.png'}
                                {elseif $smarty.get.issue_certificate}

                                {else}
                                    {capture name = 't_course_certificates_code'}
                                                        <table>
                                                            <tr><td style = "padding-right:5px">
                                                                    <img src = "images/16x16/certificate_preferences.png" title = "{$smarty.const._FORMATCERTIFICATE}" alt = "{$smarty.const._FORMATCERTIFICATE}" border = "0" style = "vertical-align:middle"/>
                                                                    <a href = "{$smarty.server.PHP_SELF}?ctg=lessons&course={$smarty.get.course}&op=format_certificate" >
                                                                        {$smarty.const._FORMATCERTIFICATE}
                                                                    </a>
                                                                </td>
                                                                <td style = "padding-right:5px;border-left:1px solid black;padding-left:5px;">
                                                                    <img src = "images/16x16/book_green.png" title = "{$smarty.const._AUTOCOMPLETE}" alt = "{$smarty.const._AUTOCOMPLETE}" border = "0" style = "vertical-align:middle"/>
                                                                    <a href = "{$smarty.server.PHP_SELF}?ctg=lessons&course={$smarty.get.course}&op=course_certificates&auto_complete">
                                                                        {$smarty.const._AUTOCOMPLETE}: {if $T_CURRENT_COURSE->course.auto_complete}{$smarty.const._YES}{else}{$smarty.const._NO}{/if}
                                                                    </a>
                                                                </td>
                                                        {if $T_CURRENT_COURSE->course.auto_complete}
                                                                <td style = "padding-right:5px;border-left:1px solid black;padding-left:5px;">
                                                                    <img src = "images/16x16/certificate_refresh.png" title = "{$smarty.const._AUTOCERTIFICATES}" alt = "{$smarty.const._AUTOCERTIFICATES}" border = "0" style = "vertical-align:middle"/>
                                                                    <a href = "{$smarty.server.PHP_SELF}?ctg=lessons&course={$smarty.get.course}&op=course_certificates&auto_certificate">
                                                                        {$smarty.const._AUTOMATICCERTIFICATES}: {if $T_CURRENT_COURSE->course.auto_certificate}{$smarty.const._YES}{else}{$smarty.const._NO}{/if}
                                                                    </a>
                                                                </td>
                                                        {/if}
                                                            </tr>
                                                        </table>
<!--ajax:usersTable-->
                                                        <table style = "width:100%" class = "sortedTable" size = "{$T_USERS_SIZE}" sortBy = "0" id = "usersTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "professor.php?ctg=lessons&course={$smarty.get.course}&op=course_certificates&">
                                                            <tr class = "topTitle">
                                                                <td class = "topTitle" name = "login">{$smarty.const._LOGIN}</td>
                                                                <td class = "topTitle" name = "name">{$smarty.const._NAME}</td>
                                                                <td class = "topTitle" name = "surname">{$smarty.const._SURNAME}</td>
                                                                <td class = "topTitle centerAlign" name = "conditions_passed">{$smarty.const._LESSONSCOMPLETED}</td>
                                                                <td class = "topTitle centerAlign" name = "completed" >{$smarty.const._COURSESTATUS}</td>
                                                                <td class = "topTitle centerAlign" name = "score">{$smarty.const._COURSESCORE}</td>
                                                                <td class = "topTitle centerAlign" name = "issued_certificate">{$smarty.const._CERTIFICATEISSUED}</td>
                                                                <td class = "topTitle centerAlign noSort">{$smarty.const._FUNCTIONS}</td>
                                                            </tr>
                                                {foreach name = 'users_progress_list' item = 'item' key = 'login' from = $T_USERS_PROGRESS}
                                                            <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$item.active}deactivatedTableElement{/if}">
                                                                <td>{$item.login}</td>
                                                                <td>{$item.name}</td>
                                                                <td>{$item.surname}</td>
                                                                <td style = "text-align:center">
                                                                    {$item.completed_lessons}/{$item.total_lessons}
                                                                </td>
                                                                <td style = "text-align:center">
                                                                    {if $item.completed}
                                                                        <img src = "images/16x16/check.png" title = "{$smarty.const._COMPLETED}" alt = "{$smarty.const._COMPLETED}" />
                                                                    {elseif $item.completed_lessons == $item.total_lessons}
                                                                        <img src = "images/16x16/contract.png" title = "{$smarty.const._LESSONSCOMPLETED}" alt = "{$smarty.const._LESSONSCOMPLETED}" />
                                                                    {else}
                                                                        <img src = "images/16x16/forbidden.png" title = "{$smarty.const._NOTCOMPLETED}" alt = "{$smarty.const._NOTCOMPLETED}" />
                                                                    {/if}
                                                                </td>
                                                                <td style = "text-align:center">{if $item.score}{$item.score}{/if}</td>
                                                                <td style = "text-align:center">{if $item.issued_certificate}{$smarty.const._YES}{else}{$smarty.const._NO}{/if}</td>
                                                                <td style = "text-align:center">{strip}
                                                                    {if $item.completed && $item.issued_certificate}
                                                                        {* Create a write evaluation link for this employee *}
                                                                        <a href = "{$smarty.server.PHP_SELF}?ctg=lessons&course={$smarty.get.course}&op=course_certificates&revoke_certificate={$item.login}" title = "{$smarty.const._REVOKECERTIFICATE}">
                                                                            <img src = "images/16x16/certificate_broken.png" title = "{$smarty.const._REVOKECERTIFICATE}" alt = "{$smarty.const._REVOKECERTIFICATE}" border = "0"/>
                                                                        </a>&nbsp;
                                                                        <a href = "{$smarty.server.PHP_SELF}?ctg=lessons&course={$smarty.get.course}&op=course_certificates&export=rtf&user={$item.login}&course={$smarty.get.course}" target="_blank" title = "{$smarty.const._VIEWCERTIFICATE}">
                                                                            <img src = "images/16x16/certificate_view.png" title = "{$smarty.const._VIEWCERTIFICATE}" alt = "{$smarty.const._VIEWCERTIFICATE}" border = "0"/>
                                                                        </a>&nbsp;
                                                                    {elseif $item.completed}
                                                                        <a href = "{$smarty.server.PHP_SELF}?ctg=lessons&course={$smarty.get.course}&op=course_certificates&issue_certificate={$item.login}&popup=1" title = "{$smarty.const._ISSUECERTIFICATE}">
                                                                            <img src = "images/16x16/certificate.png" title = "{$smarty.const._ISSUECERTIFICATE}" alt = "{$smarty.const._ISSUECERTIFICATE}" border = "0"/>
                                                                        </a>&nbsp;
                                                                    {else}
                                                                        <a href = "javascript:void(0)">
                                                                            <img src = "images/16x16/certificate_gray.png" title = "{$smarty.const._THEUSERHASNOTCOMPLETEDTHELESSON}" alt = "{$smarty.const._THEUSERHASNOTCOMPLETEDTHELESSON}" />&nbsp;
                                                                        </a>
                                                                    {/if}
                                                                        <a href = "{$smarty.server.PHP_SELF}?ctg=lessons&course={$smarty.get.course}&op=course_certificates&edit_user={$item.login}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._PROGRESS}', 2)" title = "{$smarty.const._VIEWUSERLESSONPROGRESS}">
                                                                            <img src = "images/16x16/clipboard.png" title = "{$smarty.const._VIEWUSERCOURSEPROGRESS}" alt = "{$smarty.const._VIEWUSERCOURSEPROGRESS}"/>
                                                                        </a>
                                                                {/strip}
                                                                </td>
                                                            </tr>
                                                {foreachelse}
                                                        <tr class = "{cycle values = "oddRowColor, evenRowColor"} defaultRowHeight"><td colspan = "100%" class = "emptyCategory centerAlign">{$smarty.const._NOUSERDATAFOUND}</td></tr>
                                                {/foreach}
                                                    </table>
<!--/ajax:usersTable-->

                                    {/capture}
                                    {eF_template_printInnerTable title = "&quot;`$T_CURRENT_COURSE->course.name`&quot; `$smarty.const._CERTIFICATES`" data = $smarty.capture.t_course_certificates_code image = '32x32/certificate.png'  main_options = $T_TABLE_OPTIONS}
                                {/if}
                            {elseif $T_OP == 'format_certificate'}
                                {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=lessons&course='|cat:$smarty.get.course|cat:'&op=format_certificate">'|cat:$smarty.const._FORMATCERTIFICATEFORCOURSE|cat:' &quot;'|cat:$T_CURRENT_COURSE->course.name|cat:'&quot;</a>'}
                                            {capture name = 't_certificate_code'}
                                                {$T_CERTIFICATE_FORM.javascript}
                                                <form {$T_CERTIFICATE_FORM.attributes}>
                                                    {$T_CERTIFICATE_FORM.hidden}
                                                    <table class = "formElements" style = "width:100%">
                                                        <tr><td class = "labelCell">{$T_CERTIFICATE_FORM.file_upload.label}:&nbsp;</td>
                                                            <td class = "elementCell" colspan="3">{$T_CERTIFICATE_FORM.file_upload.html}</td></tr>
                                                        <tr><td class = "labelCell">{$T_CERTIFICATE_FORM.existing_certificate.label}:&nbsp;</td>
                                                            <td class = "elementCell" colspan="1">{$T_CERTIFICATE_FORM.existing_certificate.html}&nbsp;</td>
                                                        </tr>
                                                        <tr><td colspan = "1"></td><td class = "infoCell" style = "white-space:normal;" colspan = "3">
                                                            {$smarty.const._CERTIFICATEINSTRUCTIONS}
                                                            </td>
                                                        </tr>
                                                        <tr><td></td>
                                                            <td colspan="3">{$T_CERTIFICATE_FORM.preview.html} &nbsp;
                                                                            {$T_CERTIFICATE_FORM.submit_certificate.html}
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </form>
                                            {/capture}
                                            {eF_template_printInnerTable title = $smarty.const._FORMATCERTIFICATE data = $smarty.capture.t_certificate_code image = '32x32/certificate_preferences.png'  main_options = $T_TABLE_OPTIONS}
                            {elseif $T_OP == 'course_rules'}
                                {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=lessons&course='|cat:$smarty.get.course|cat:'&op=course_rules">'|cat:$smarty.const._RULESFORCOURSE|cat:' &quot;'|cat:$T_CURRENT_COURSE->course.name|cat:'&quot;</a>'}
                                    {capture name = 't_course_rules_code'}
                                                    {$T_COURSE_RULES_FORM.javascript}
                                                    <form {$T_COURSE_RULES_FORM.attributes}>
                                                    {$T_COURSE_RULES_FORM.hidden}
                                                    <table style = "max-width:100%">
                                            {foreach name = 'rules_list' item = 'item' key = 'key' from = $T_COURSE_LESSONS}
                                                        <tr class = "defaultRowHeight {if !$item.active}deactivatedTableElement{/if}">
                                                            <td id = "first_node_{$item.id}" style = "white-space:nowrap">{$item.name}</td>
                                                            <td id = "label_{$item.id}"      style = "white-space:nowrap;">&nbsp;{$smarty.const._GENERALLYAVAILABLE}&nbsp;</td>
                                                            <td id = "insert_node_{$item.id}"></td>
                                                            <td id = "last_node_{$item.id}"  style = "white-space:nowrap;text-align:right;vertical-align:bottom">
                                                                &nbsp;<img src = "images/16x16/delete.png" title = "{$smarty.const._DELETECONDITION}" alt = "{$smarty.const._DELETECONDITION}" border = "0" id = "delete_icon_{$item.id}" onclick = "eF_js_removeCourseRule({$item.id})" style = "display:none"/>
                                                                &nbsp;<img src = "images/16x16/add2.png"   title = "{$smarty.const._ADDCONDITION}"    alt = "{$smarty.const._ADDCONDITION}"    border = "0" id = "add_icon_{$item.id}" onclick = "eF_js_addCourseRule({$item.id})"/></td>
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
                                                    <!--
                                                    var lessonsIds   = new Array();
                                                    var lessonsNames = new Array();
                                            {foreach name = 'lessons_list' item = 'lesson' key = 'key' from = $T_COURSE_LESSONS}    {*Create javascript arrays*}
                                                        lessonsIds.push('{$lesson.id}');
                                                        lessonsNames.push('{$lesson.name}');
                                            {/foreach}

                                                    {literal}
                                                    Array.prototype.inArray = function (value)
                                                    {
                                                        var i;
                                                        for (i = 0; i < this.length; i++) {
                                                            if (this[i] === value) {
                                                                return true;
                                                            }
                                                        }
                                                        return false;
                                                    };

                                                    function eF_js_removeCourseRule(id) {
                                                        var insertCell    = document.getElementById('insert_node_'  + id);
                                                        var numConditions = Math.round(insertCell.parentNode.getElementsByTagName('select').length / 2);

                                                        if (numConditions > 0) {              //This means there are more than 1 conditions set
                                                            child = document.getElementById('lessonCell['+id+']['+numConditions+']');
                                                            child.parentNode.removeChild(child);

                                                            if (numConditions % 5 == 0) {       //This is for wrapping fields (since IE won't automatically wrap them)
                                                                insertCell.removeChild(insertCell.lastChild);
                                                            }
                                                        }
                                                        if (numConditions == 1) {
                                                            document.getElementById('delete_icon_' + id).style.display = 'none';
                                                            document.getElementById('label_' + id).innerHTML = '&nbsp;{/literal}{$smarty.const._GENERALLYAVAILABLE}{literal}&nbsp;';    //Set the correct label
                                                      }
                                                    }

                                                    function eF_js_addCourseRule(id, selectedLesson, selectedCondition) {

                                                        if (!selectedLesson) {
                                                            selectedLesson = 0;
                                                        }
                                                        if (!selectedCondition) {
                                                            selectedCondition = 0;
                                                        }
                                                        var insertCell    = document.getElementById('insert_node_'  + id);
                                                        var numConditions = Math.round(insertCell.parentNode.getElementsByTagName('select').length / 2 + 1);

                                                        selectedValues = new Array();
                                                        for (var i = 1; i < numConditions; i++) {           //Calculate selected options, to remove them from the new selects
                                                            previous_select = document.getElementById('rules['+id+'][lesson]['+(i)+']');
                                                            selectedValues.push(previous_select.options[previous_select.options.selectedIndex].value);
                                                        }

                                                        if (selectedValues.length == lessonsIds.length - 1) {       //This means no more options are left. so return without doing anything
                                                            return false;
                                                        }

                                                        document.getElementById('label_' + id).innerHTML = '&nbsp;{/literal}{$smarty.const._DEPENDSON}{literal}:&nbsp;';    //Set the correct label

                                                        var lessonsSpan = document.createElement('span');
                                                        lessonsSpan.id = 'lessonCell['+id+']['+numConditions+']';
                                                        insertCell.appendChild(lessonsSpan);
                                                        if (numConditions % 5 == 0) {       //This is for wrapping fields (since IE won't automatically wrap them)
                                                            insertCell.appendChild(document.createElement('br'));
                                                        }
                                                        if (numConditions > 1) {              //This means there are other conditions set
                                                            var conditionsSelect           = document.getElementById('conditions').cloneNode(true);
                                                            conditionsSelect.id            = 'rules['+id+'][condition]['+numConditions+']';
                                                            conditionsSelect.name          = conditionsSelect.id;
                                                            conditionsSelect.selectedIndex = selectedCondition;
                                                            conditionsSelect.style.display = '';
                                                            lessonsSpan.appendChild(conditionsSelect);
                                                        }
                                                        //var lessonsSelect  = document.getElementById('lessons_list').cloneNode(true);    //This is the right way to do it, but IE won't cloneNode correctly (sic) so we need to build the select list from scratch
                                                        lessonsSelect = document.createElement('select');
                                                        lessonsSelect.style.marginLeft    = '5px';
                                                        lessonsSelect.style.verticalAlign = 'middle';
                                                        lessonsSelect.id   = 'rules['+id+'][lesson]['+numConditions+']';
                                                        lessonsSelect.name = lessonsSelect.id;

                                                        for (var i = 0; i < lessonsIds.length; i++) {
                                                            if (!selectedValues.inArray(lessonsIds[i])) {
                                                                option           = document.createElement('option');
                                                                option.value     = lessonsIds[i];
                                                                option.innerHTML = lessonsNames[i];
                                                                lessonsSelect.appendChild(option);
                                                            }
                                                        }

                                                        for (i = 0; i < lessonsSelect.options.length; i++) {                                      //Remove selected lesson from list
                                                            if (lessonsSelect.options[i].value == selectedLesson) {
                                                                lessonsSelect.options[i].selected = true;
                                                            }
                                                        }
                                                        //In separate loop, because setting to null seems to reindex select options (in IE)
                                                        for (i = 0; i < lessonsSelect.options.length; i++) {                                      //Remove selected lesson from list
                                                            if (lessonsSelect.options[i].value == id) {
                                                                lessonsSelect.options[i] = null;
                                                            }
                                                        }
                                                        lessonsSelect.style.display = '';
                                                        lessonsSpan.appendChild(lessonsSelect);

                                                        document.getElementById('delete_icon_' + id).style.display = '';

                                                    }//-->
                                                    {/literal}
                                                    </script>
                                                {foreach name = 'course_rules_list' item = "rule" key = "key" from = $T_COURSE_RULES}
                                                    {foreach name = 'lesson_rules' item = "lesson_id" key = "index" from = $rule.lesson}
                                                        {if !$rule.condition.$index || $rule.condition.$index == 'and'}{assign var = 'condition' value = 0}{else}{assign var = 'condition' value = 1}{/if}
                                                        <script>eF_js_addCourseRule({$key}, {$lesson_id}, {$condition})</script>
                                                    {/foreach}
                                                {/foreach}

                                            {/capture}
                                            {eF_template_printInnerTable title = $smarty.const._COURSERULES data = $smarty.capture.t_course_rules_code image = '32x32/recycle.png'  main_options = $T_TABLE_OPTIONS}
                            {elseif $T_OP == 'course_order'}
                                {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=courses&course='|cat:$smarty.get.course|cat:'&op=course_order">'|cat:$smarty.const._ORDERFORCOURSE|cat:' &quot;'|cat:$T_CURRENT_COURSE->course.name|cat:'&quot;</a>'}
                                    {capture name = 't_course_rules_code'}
                                        <fieldset>
                                            <legend>{$smarty.const._DRAGITEMSTOCHANGELESSONSORDER}</legend>
                                            <ul id = "dhtmlgoodies_lessons_tree" class = "dhtmlgoodies_tree">
                                            {foreach name = 'lessons_list' key = 'key' item = 'lesson'  from = $T_COURSE_LESSONS}
                                                <li id = "dragtree_{$lesson.id}" noChildren = "true">
                                                    <a class = "{if !$lesson.active}deactivatedLinkElement{/if}" href = "#">&nbsp;{$lesson.name|eF_truncate:100}</a>
                                                </li>
                                            {/foreach}
                                            </ul>
                                        </fieldset>
                                        <br/>
                                        <input id = "save_button" class = "flatButton" type="button" onclick="saveQuestionTree()" value="{$smarty.const._SAVECHANGES}">
                                    {/capture}
                                    {eF_template_printInnerTable title = $smarty.const._COURSEORDER data = $smarty.capture.t_course_rules_code image = '32x32/replace2.png'  main_options = $T_TABLE_OPTIONS}
                                    <script>
                                    {literal}
                                    function saveQuestionTree() {
                                        Element.extend($('save_button'));
                                        progressImg = new Element('img', {id:'progress_image', src:'images/others/progress1.gif'}).setStyle({position:'absolute'});
                                        progressImg.style.top      = Element.positionedOffset($('save_button')).top + 1 + 'px';
                                        progressImg.style.left     = Element.positionedOffset($('save_button')).left + 6 + Element.getDimensions($('save_button')).width + 'px';
                                        document.body.appendChild(progressImg);
                                        //alert(treeObj.getNodeOrders());
                                        new Ajax.Request('professor.php?ctg=courses&course={/literal}{$smarty.get.course}{literal}&op=course_order&ajax=1&order='+treeObj.getNodeOrders(), {
                                            method:'get',
                                            asynchronous:true,
                                            onSuccess: function (transport) {
                                                progressImg.hide();
                                                progressImg.setAttribute('src', 'images/16x16/check.png');
                                                new Effect.Appear('progress_image');
                                                window.setTimeout('Effect.Fade("progress_image")', 2500);
                                            }
                                        });

                                    }
                                    {/literal}
                                    </script>

                            {elseif $T_OP == 'course_scheduling'}
                                {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=courses&course='|cat:$smarty.get.course|cat:'&op=course_scheduling">'|cat:$smarty.const._SCHEDULINGFORCOURSE|cat:' &quot;'|cat:$T_CURRENT_COURSE->course.name|cat:'&quot;</a>'}

                                    {capture name = 't_course_scheduling_code'}
                                        <table>
                                        {foreach name = 'lessons_list' key = "id" item = "lesson" from = $T_COURSE_LESSONS}
                                            <tr {if !$lesson.active}class = "deactivatedTableElement"{/if}><td>{$lesson.name}:&nbsp;</td>
                                                <td id = "schedule_dates_{$id}">{if $lesson.from_timestamp}{$smarty.const._FROM} #filter:timestamp_time_nosec-{$lesson.from_timestamp}# {$smarty.const._TO} #filter:timestamp_time_nosec-{$lesson.to_timestamp}#{else}<span class = "emptyCategory">{$smarty.const._NOSCHEDULESET}</span>{/if}&nbsp;</td>
                                                <td>
                                                    <span id = "add_schedule_link_{$id}">
                                                        <a href = "javascript:void(0)" onclick = "showEdit({$id})"><img src = "images/16x16/{if $lesson.from_timestamp}edit.png{else}add2.png{/if}" alt = "{$smarty.const._ADDSCHEDULE}" title = "{$smarty.const._ADDSCHEDULE}" style = "vertical-align:middle" border = "0"/></a>
                                                        <a href = "javascript:void(0)" onclick = "deleteSchedule(this, {$id})" {if !$lesson.from_timestamp}style = "display:none"{/if}><img src = "images/16x16/delete.png" alt = "{$smarty.const._DELETESCHEDULE}" title = "{$smarty.const._DELETESCHEDULE}" style = "vertical-align:middle" border = "0"/></a>
                                                    </span>&nbsp;
                                                </td>
                                                <td id = "schedule_dates_form_{$id}" style = "display:none">
                                                    <table>
                                                        <tr><td>{$smarty.const._FROM}&nbsp;</td><td>{eF_template_html_select_date prefix="from_" time=$lesson.from_timestamp start_year="-2" end_year="+2" field_order = 'YMD'} {$smarty.const._TIME}: {html_select_time prefix="from_" time = $lesson.from_timestamp display_seconds = false}&nbsp;</td></tr>
                                                        <tr><td>{$smarty.const._TO}&nbsp;</td><td>{eF_template_html_select_date prefix="to_"   time=$lesson.to_timestamp   start_year="-2" end_year="+2" field_order = 'YMD'} {$smarty.const._TIME}: {html_select_time prefix="to_"   time = $lesson.to_timestamp   display_seconds = false}&nbsp;</td></tr>
                                                    </table>
                                                </td>
                                                <td>
                                                    <a id = "set_schedules_link_{$id}" style = "display:none" href = "javascript:void(0)" onclick = "setSchedule(this, {$id})">
                                                        <img src = "images/16x16/check2.png" alt = "{$smarty.const._SAVE}" title = "{$smarty.const._SAVE}" style = "vertical-align:middle" border = "0"/></a>&nbsp;
                                                    <a id = "remove_schedule_link_{$id}" href = "javascript:void(0)" onclick = "hideEdit({$id})" style = "display:none" onclick = ""><img src = "images/16x16/delete2.png" alt = "{$smarty.const._CANCEL}" title = "{$smarty.const._CANCEL}" style = "vertical-align:middle" border = "0"/></a>
                                                    </td>
                                                </tr>
                                        {/foreach}
                                        </table>
                                        <script>
                                        {literal}
                                        function showEdit(id) {
                                            $('add_schedule_link_'+id).hide();
                                            $('remove_schedule_link_'+id).show();
                                            $('schedule_dates_form_'+id).show();
                                            $('set_schedules_link_'+id).show();
                                        }
                                        function hideEdit(id) {
                                            $('remove_schedule_link_'+id).hide();
                                            $('add_schedule_link_'+id).show();
                                            $('schedule_dates_form_'+id).hide();
                                            $('set_schedules_link_'+id).hide();
                                        }
                                        function setSchedule(el, id) {
                                            Element.extend(el);
                                            url = 'professor.php?ctg=courses&course={/literal}{$T_CURRENT_COURSE->course.id}{literal}&op=course_scheduling&set_schedule='+id;
                                            $('schedule_dates_form_'+id).select('select').each(function (s) {url+='&'+s.name+'='+s.options[s.selectedIndex].value});

                                            el.down().src = 'images/others/progress1.gif';
                                            new Ajax.Request(url, {
                                                    method:'get',
                                                    asynchronous:true,
                                                    encoding: 'UTF-8',
                                                    onFailure: function (transport) {
                                                        el.down().writeAttribute({src:'images/16x16/delete.png', title:transport.responseText}).hide();
                                                        new Effect.Appear(el.down().identify());
                                                        window.setTimeout('Effect.Fade("'+el.down().identify()+'")', 10000);
                                                    },
                                                    onSuccess: function (transport) {
                                                        $('schedule_dates_'+id).update(transport.responseText);
                                                        hideEdit(id);
                                                        el.down().src = 'images/16x16/check2.png';
                                                        $('add_schedule_link_'+id).down().down().src = 'images/16x16/edit.png';
                                                        $('add_schedule_link_'+id).down().next().show();
                                                    }
                                            });
                                        }
                                        function deleteSchedule(el, id) {
                                            Element.extend(el);
                                            url = 'professor.php?ctg=courses&course={/literal}{$T_CURRENT_COURSE->course.id}{literal}&op=course_scheduling&delete_schedule='+id;
                                            el.down().src = 'images/others/progress1.gif';
                                            new Ajax.Request(url, {
                                                    method:'get',
                                                    asynchronous:true,
                                                    onFailure: function (transport) {
                                                        el.down().src = 'images/16x16/delete.png';
                                                        errorImg = new Element('img', {id: 'error_icon', src:'images/16x16/delete2.png', title: transport.responseText, border: 0}).setStyle({verticalAlign:'middle'}).hide();
                                                        el.insert(errorImg);
                                                        new Effect.Appear(errorImg.identify());
                                                        window.setTimeout('Effect.Fade("'+errorImg.identify()+'")', 10000);
                                                    },
                                                    onSuccess: function (transport) {
                                                        $('schedule_dates_'+id).update('<span class = "emptyCategory">{/literal}{$smarty.const._NOSCHEDULESET}{literal}</span>');
                                                        el.down().writeAttribute({src: 'images/16x16/delete.png'});
                                                        el.hide();
                                                        el.previous().down().src = 'images/16x16/add2.png';
                                                    }
                                            });

                                        }
                                        {/literal}
                                        </script>
                                    {/capture}
                                    {eF_template_printInnerTable title = $smarty.const._COURSEORDER data = $smarty.capture.t_course_scheduling_code image = '32x32/calendar.png'  main_options = $T_TABLE_OPTIONS}
                            {else}
            {if $T_DIRECTIONS_TREE}
                {if $T_CONFIGURATION.lessons_directory == '1' || $T_CONFIGURATION.lessons_directory == '2'}
                                    <div style = "float:right;">
                                        <img src = "images/32x32/cabinet.png" title = "{$smarty.const._LESSONSDIRECTORY}" alt = "{$smarty.const._LESSONSDIRECTORY}" style = "vertical-align:middle;">
                                        <a href = "directory.php" style = "vertical-align:middle;">{$smarty.const._LESSONSDIRECTORY}</a>
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
                                    </table>
            {/if}

                            {/if}
                        </td></tr>

    {/capture}
{/if}

{if $T_CTG == 'personal'}
{*modulePersonal: Print the Personal page*}
    {capture name = "modulePersonal"}
                            {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=personal">'|cat:$smarty.const._USERPROFILE|cat:'</a>'}

                            <tr><td class = "moduleCell">
                                    {include file = "includes/module_personal.tpl"}
                            </td></tr>
    {/capture}
{/if}


{if ($T_CTG == 'statistics')}
{assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics">'|cat:$smarty.const._STATISTICS|cat:'</a>'}
{*moduleStatistics: The statistics page*}
    {if $smarty.get.option == 'user'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=user">'|cat:$smarty.const._USERSTATISTICS|cat:'</a>'}
        {if $smarty.get.sel_user}
            {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=user&sel_user='|cat:$smarty.get.sel_user|cat:'">'|cat:$smarty.get.sel_user|cat:'</a>'}
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
    {elseif $smarty.get.option == 'course'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=course">'|cat:$smarty.const._COURSESTATISTICS|cat:'</a>'}
        {if isset($smarty.get.sel_course)}
            {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=course&sel_course='|cat:$smarty.get.sel_course|cat:'">'|cat:$T_COURSE_INFO.name|cat:'</a>'}
        {/if}
    {elseif $smarty.get.option == 'system'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=system">'|cat:$smarty.const._SYSTEMSTATISTICS|cat:'</a>'}
    {elseif $smarty.get.option == 'queries'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=queries">'|cat:$smarty.const._GENERICQUERIES|cat:'</a>'}
    {/if}

    {capture name = "moduleStatistics"}
        <tr><td class = "moduleCell">
            {include file = "module_statistics.tpl"}
        </td></tr>
    {/capture}
{/if}

{if ( $T_CTG == 'survey' ) }
    {*moduleSurvey: The survey page*}
    {capture name = "moduleSurvey"}
        <tr><td class="moduleCell">
            {if (!isset($smarty.get.screen_survey)  && !isset($smarty.get.action) && $smarty.get.screen_survey != '2')}
            {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey">'|cat:$smarty.const._SURVEY|cat:'</a>'}
                    {if (isset($smarty.get.t_enter_create) && $smarty.get.t_enter_create == '1')}
                {eF_template_printMessage message=$smarty.const._SURVEYADDEDSUCCESSFULLY type='success'}
            {elseif (isset($T_ENTER_CREATE) && $T_ENTER_CREATE == '-1')}
                            {eF_template_printMessage message=$smarty.const._FAILEDTOADDSURVEY type='failure'}
            {else}
            {/if}
            {if (isset($smarty.get.t_enter_delete) && $smarty.get.t_enter_delete == '1')}
                {eF_template_printMessage message=$smarty.const._SURVEYDELETEDSUCCESSFULLY type='success'}
            {elseif (isset($smarty.get.t_enter_delete) && $smarty.get.t_enter_delete == '-1')}
                            {eF_template_printMessage message=$smarty.const._SURVEYFAILEDTOBEDELETED type='failure'}
            {else}
            {/if}
            {if (isset($smarty.get.t_enter_update) && $smarty.get.t_enter_update == '1')}
                            {eF_template_printMessage message=$smarty.const._SURVEYDATAUPDATEDSUCCESSFULLY type='success'}
            {elseif (isset($smarty.get.t_enter_update) && $smarty.get.t_enter_update == '-1')}
                {eF_template_printMessage message=$smarty.const._SURVEYDATAFAILEDTOBEUPDATED type='failure'}
            {else}
            {/if}
            {if (isset($smarty.get.published) && $smarty.get.published == '1')}
                {eF_template_printMessage message=$smarty.const._SURVEYPUBLISHEDSUCCESSFULLY type='success'}
            {elseif (isset($smarty.get.published) && $smarty.get.published == '-1')}
                {eF_template_printMessage message=$smarty.const._SURVEYFAILEDTOBEPUBLISHED type='failure'}
            {else}
            {/if}
            {if (isset($smarty.get.t_activate) && $smarty.get.t_activate == '-1')}
                {eF_template_printMessage message=$smarty.const._SURVEYDISABLEDSUCCESSFULLY type='success'}
            {elseif (isset($smarty.get.t_activate) && $smarty.get.t_activate == '1')}
                {eF_template_printMessage message=$smarty.const._SURVEYENABLEDSUCCESSFULLY type='success'}
            {else}
            {/if}
            {if ($smarty.get.survey_user == 'false') }
                {eF_template_printMessage message=$smarty.const._AUSERISALREADYATTHESURVEY type='failure'}
            {/if}
            {* Format T_SURVEY in html code *}
            {capture name='t_survey_code'}
                {eF_template_printSurveysList  data = $T_SURVEY_INFO questions = $T_SURVEY_QUESTIONS user_type = $T_USER lesson_id = $T_LESSON_ID}
            {/capture}
            {eF_template_printInnerTable title=$smarty.const._SURVEY  data = $smarty.capture.t_survey_code  image='32x32/form_green.png'}
        {/if}
        {if (isset($smarty.get.screen_survey) && !isset($smarty.get.action) && $smarty.get.screen_survey == '2')}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo;&nbsp;'|cat:'<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'">'|cat:$T_SURVEYNAME|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href="#">'|cat:$smarty.const._QUESTIONS|cat:'</a>'}
        {if (isset($smarty.get.t_question_added) && $smarty.get.t_question_added == '1')}
                    {eF_template_printMessage message=$smarty.const._QUESTIONADDEDSUCCESSFULLY type='success'}
        {elseif (isset($smarty.get.t_question_added) && $smarty.get.t_question_added == '-1')}
            {eF_template_printMessage message=$smarty.const._QUESTIONFAILEDTOBEADDED type='failure'}
        {else}
        {/if}
        {if (isset($smarty.get.question_added) && $smarty.get.question_added == '1')}
            {eF_template_printMessage message=$smarty.const._QUESTIONADDEDSUCCESSFULLY type='success'}
        {elseif (isset($smarty.get.question_added) && $smarty.get.question_added == '-1')}
            {eF_template_printMessage message=$smarty.const._QUESTIONFAILEDTOBEADDED type='failure'}
        {else}
        {/if}
        {if (isset($smarty.get.question_deleted) && $smarty.get.question_deleted == '1')}
            {eF_template_printMessage message=$smarty.const._QUESTIONDELETEDSUCCESSFULLY type='success'}
        {elseif (isset($smarty.get.question_deleted) && $smarty.get.question_deleted == '-1')}
            {eF_template_printMessage message=$smarty.const._QUESTIONFAILEDTOBEDELETED type='failure'}
        {else}
        {/if}
        {if (isset($smarty.get.question_swap) && $smarty.get.question_swap == '1')}
            {eF_template_printMessage message=$smarty.const._THEQUESTIONWASSUCCESSFULLYMOVED type='success'}
        {elseif (isset($smarty.get.question_swap) && $smarty.get.question_swap == '-1')}
            {eF_template_printMessage message=$smarty.const._THEQUESTIONFAILEDTOBEMOVED type='failure'}
        {elseif (isset($smarty.get.question_swap) && $smarty.get.question_swap == '-2')}
            {eF_template_printMessage message=$smarty.const._NOSUCHOPERATION type='failure'}
        {else}
        {/if}
        {if (isset($smarty.get.question_updated) &&  $smarty.get.question_updated == '1') }
            {eF_template_printMessage message=$smarty.const._QUESTIONUPDATEDSUCCESSFULLY type='success'}
        {elseif (isset($smarty.get.question_updated) &&  $smarty.get.question_updated == '-1')}
            {eF_template_printMessage message=$smarty.const._QUESTIONFAILEDTOBEUPDATED type='failure'}
        {else}
        {/if}
        {capture name='t_survey_questions'}
            <table width="100%" border="0px">
                <tr>
                    <td class="tableImage" align="right"><img src="images/16x16/add2.png" border="0px" /></td>
                    <td class="innerTableHeader" align="left">
                        <select name="question_type" onchange="if(this.options[this.selectedIndex].value != '') document.location='professor.php?ctg=survey&action=question_create&screen_survey=2&surveys_ID={$smarty.get.surveys_ID}&question_type='+this.options[this.selectedIndex].value">
                            <option value="-" selected>{$smarty.const._ADDQUESTION}:</option>
                            <option value="yes_no">{$smarty.const._YES_NO}</option>
                            <option value="development">{$smarty.const._DEVELOPMENT}</option>
                            <option value="dropdown">{$smarty.const._DROPDOWN}</option>
                            <option value="multiple_one">{$smarty.const._SURVEYQUESTIONMULTIPLEONE}</option>
                            <option value="multiple_many">{$smarty.const._SURVEYQUESTIONMULTIPLEMANY}</option>
                        </select>
                    </td>
                </tr>
            </table>
            <table width=" 100%" border="0px" class="sortedTable">
                <tr class="defaultRowHeight">
                    <td class="topTitle" align="center">{$smarty.const._QUESTIONNUMBER}</td>
                    <td class="topTitle" align="left">{$smarty.const._QUESTIONTITLE}</td>
                    <td class="topTitle" align="left">{$smarty.const._QUESTIONTYPE}</td>
                    <td class="topTitle" align="center">{$smarty.const._NUMBEROFOPTIONS}</td>
                    <td class="topTitle" align="left" colspan="100%">{$smarty.const._OPERATIONS}</td>
                </tr>
            {if ( isset($T_NOQUESTIONSFORSURVEY) )}
                <tr><td class="emptyCategory" colspan="100%" align="center">{$smarty.const._NOQUESTIONSFORSURVEY}</td></tr>
                </table>
                </td></tr>
            {else}
            {section name='survey_questions' loop=$T_SURVEY_QUESTIONS_INFO}
                <tr class = "{cycle name = "su_info" values = "oddRowColor, evenRowColor"}">
                    <td class="noSort" align="center">{$T_SURVEY_QUESTIONS_INFO[survey_questions].father_ID}</td>
                    <td class="noSort" align="left">{$T_SURVEY_QUESTIONS_INFO[survey_questions].question}</td>
                    <td class="noSort" align="left">
                    {if ($T_SURVEY_QUESTIONS_INFO[survey_questions].type == 'yes_no') }
                        {$smarty.const._YES_NO}
                    {elseif ($T_SURVEY_QUESTIONS_INFO[survey_questions].type == 'development') }
                        {$smarty.const._DEVELOPMENT}
                    {elseif ($T_SURVEY_QUESTIONS_INFO[survey_questions].type == 'dropdown') }
                        {$smarty.const._DROPDOWN}
                    {elseif ($T_SURVEY_QUESTIONS_INFO[survey_questions].type == 'multiple_one') }
                        {$smarty.const._SURVEYQUESTIONMULTIPLEONE}
                    {elseif ($T_SURVEY_QUESTIONS_INFO[survey_questions].type == 'multiple_many') }
                        {$smarty.const._SURVEYQUESTIONMULTIPLEMANY}
                    {else}
                        {$smarty.const._NOQUESTION}
                    {/if}
                    </td>
                    <td class="noSort" align="center">{$T_SURVEY_QUESTIONS[survey_questions]}</td>
                    <td class="noSort" align="left" valign="baseline">
                    <a href="professor.php?ctg=survey&action=question_create&question_action=update_question&question_type={$T_SURVEY_QUESTIONS_INFO[survey_questions].type}&surveys_ID={$smarty.get.surveys_ID}&question_ID={$T_SURVEY_QUESTIONS_INFO[survey_questions].id}&father_ID={$T_SURVEY_QUESTIONS_INFO[survey_questions].father_ID}"><img src="images/16x16/edit.png" border="0px" title="{$smarty.const._EDIT}" /></a>
                    {if ($smarty.section.survey_questions.iteration == $smarty.section.survey_questions.first)}
                        <a href="professor.php?ctg=survey&surveys_ID={$smarty.get.surveys_ID}&action=swap_question&swap_action=move_down&question_ID={$T_SURVEY_QUESTIONS_INFO[survey_questions].id}&father_ID={$T_SURVEY_QUESTIONS_INFO[survey_questions].father_ID}"><img src="images/16x16/navigate_down.png" border="0px" title="{$smarty.const._DOWN}"/></a>
                    {elseif ($smarty.section.survey_questions.iteration == $smarty.section.survey_questions.last)}
                        <a href="professor.php?ctg=survey&surveys_ID={$smarty.get.surveys_ID}&action=swap_question&swap_action=move_up&question_ID={$T_SURVEY_QUESTIONS_INFO[survey_questions].id}&father_ID={$T_SURVEY_QUESTIONS_INFO[survey_questions].father_ID}"><img src="images/16x16/navigate_up.png" border="0px" title="{$smarty.const._UP}"></a>
                    {else}
                        <a href="professor.php?ctg=survey&surveys_ID={$smarty.get.surveys_ID}&action=swap_question&swap_action=move_up&question_ID={$T_SURVEY_QUESTIONS_INFO[survey_questions].id}&father_ID={$T_SURVEY_QUESTIONS_INFO[survey_questions].father_ID}"><img src="images/16x16/navigate_up.png" border="0px" title="{$smarty.const._DOWN}"></a>
                        <a href="professor.php?ctg=survey&surveys_ID={$smarty.get.surveys_ID}&action=swap_question&swap_action=move_down&question_ID={$T_SURVEY_QUESTIONS_INFO[survey_questions].id}&father_ID={$T_SURVEY_QUESTIONS_INFO[survey_questions].father_ID}"><img src="images/16x16/navigate_down.png" border="0px" title="{$smarty.const._UP}"/></a>
                    {/if}
                    <a href="professor.php?ctg=survey&action=delete_question&question_ID={$T_SURVEY_QUESTIONS_INFO[survey_questions].id}&surveys_ID={$smarty.get.surveys_ID}" onclick="{literal}return confirm('{/literal}{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}{literal}');{/literal}"><img src="images/16x16/delete.png" border="0px" title="{$smarty.const._DELETE}" /></a>
            {/section}
                </tr>
            </table>
            {/if}
        {/capture}
        {eF_template_printInnerTable title=$smarty.const._CREATESURVEYQUESTION data=$smarty.capture.t_survey_questions image='32x32/form_green.png'}
        {/if}
        {if ($smarty.get.action == 'question_create')}
            {if (isset($smarty.get.question_type) && $smarty.get.question_type == '-')}
                {eF_template_printMessage message=$smarty.const._PLEASESELECTAVALIDTYPEOFQUESTION type='error'}
                <tr><td align="center"><input class="flatbutton" type="button" value="{$smarty.const._BACK}" onclick="history.back();" />
            {else}
                {if ($smarty.get.question_type == 'dropdown')}
                    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo;&nbsp;'|cat:'<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'">'|cat:$T_SURVEYNAME|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'&screen_survey=2">'|cat:$smarty.const._QUESTIONS|cat:'</a>&nbsp;&raquo&nbsp;<a class="titleLink" href="#">'|cat:$smarty.const._DROPDOWN|cat:'</a>'}
                {/if}
                {if ($smarty.get.question_type == 'development')}
                    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo;&nbsp;'|cat:'<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'">'|cat:$T_SURVEYNAME|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'&screen_survey=2">'|cat:$smarty.const._QUESTIONS|cat:'</a>&nbsp;&raquo&nbsp;<a class="titleLink" href="#">'|cat:$smarty.const._DEVELOPMENT|cat:'</a>'}
               {/if}
               {if ($smarty.get.question_type == 'yes_no')}
                    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a  class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo;&nbsp;'|cat:'<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'">'|cat:$T_SURVEYNAME|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'&screen_survey=2">'|cat:$smarty.const._QUESTIONS|cat:'</a>&nbsp;&raquo&nbsp;<a class="titleLink" href="#">'|cat:$smarty.const._YES_NO|cat:'</a>'}
               {/if}
               {if ($smarty.get.question_type == 'multiple_one')}
                    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo;&nbsp;'|cat:'<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'">'|cat:$T_SURVEYNAME|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'&screen_survey=2">'|cat:$smarty.const._QUESTIONS|cat:'</a>&nbsp;&raquo&nbsp;<a class="titleLink" href="#">'|cat:$smarty.const._MULTIPLEONE|cat:'</a>'}
               {/if}
               {if ($smarty.get.question_type == 'multiple_many')}
                    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a  class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo;&nbsp;'|cat:'<a  class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'">'|cat:$T_SURVEYNAME|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'&screen_survey=2">'|cat:$smarty.const._QUESTIONS|cat:'</a>&nbsp;&raquo&nbsp;<a class="titleLink" href="#">'|cat:$smarty.const._MULTIPLEMANY|cat:'</a>'}
               {/if}
               <script>
               {literal}

                                    bTextareaWasTinyfied = false; //this should be global, could be stored in a cookie...
                                    function setTextareaToTinyMCE(sEditorID) {
                                        var oEditor = document.getElementById(sEditorID);
                                        if(oEditor && !bTextareaWasTinyfied) {
                                            tinyMCE.execCommand('mceAddControl', true, sEditorID);
                                            bTextareaWasTinyfied = true;
                                        }
                                        return;
                                    }
                                    function unsetTextareaToTinyMCE(sEditorID) {
                                        var oEditor = document.getElementById(sEditorID);
                                        if(oEditor && bTextareaWasTinyfied) {
                                            tinyMCE.execCommand('mceRemoveControl', true, sEditorID);
                                            bTextareaWasTinyfied = false;
                                        }
                                        return;
                                    }

                                //We need a "shuffle" function for our arrays. Add to the array prototype such a function
                                Array.prototype.shuffle = function() {
                                    for (var i = 0; i < this.length; i++) {
                                        // Random item in this array.
                                        var r = parseInt(Math.random() * this.length);
                                        var obj = this[r];

                                        // Swap.
                                        this[r] = this[i];
                                        this[i] = obj;
                                    }
                                }

                                //This function uses the shuffle method we added to the array prototype to randomly reorder the values of the match question boxes.
                                //function shuffle_match() {
                                //    var els = document.getElementsByTagName('input');           //Find all 'input' elements in the document.
                                //    var target_elements = new Array;
                                //    var target_values   = new Array;

                                //    for (var i = 0; i < els.length; i++) {                      //Get the text boxes that will be shuffled
                                //        if (els[i].name.match('correct_match')) {
                                //            target_elements.push(els[i]);
                                //            target_values.push(els[i].value);
                                //        }
                                //    }

                                //    target_values.shuffle();                                    //Shuffle the array

                                //    for (var i = 0; i < target_elements.length; i++) {          //Assign the new values to the text boxes.
                                //        target_elements[i].value = target_values[i];
                                //    }
                                //}


                                function eF_js_addAdditionalChoice(question_type) {
                                    var els = document.getElementsByTagName('input');           //Find all 'input' elements in th document.

                                    var counter = 0;
                                    for (var i = 0; i < els.length; i++) {                      //Count all the 'input' elements whose names match the selected question type (e.g. multiple_one[0], multiple_one[1] etc)
                                        if (els[i].name.match('^'+question_type) && els[i].type.match('text')) {
                                            counter++;
                                        }
                                    }

                                    if (counter > 1) {                                                  //If the counter is less than 2 (where 2 is the default input fields), it means that the selected question type is not one that may have multiple inputs (i.e. it may be raw_text)
                                        var last_node = document.getElementById(question_type+'_last_node');   //This is the node that the new elements will be inserted before

                                        var tr = document.createElement('tr');                          //Create a table row to hold the new element
                                        tr.appendChild(document.createElement('td'));
                                        var td = document.createElement('td');//Create a new table cell to hold the new element
                                        tr.appendChild(td);//Append this table cell to the table row we created above

                                        var input = document.createElement('input');                    //Create the new input element
                                        input.setAttribute('type', 'text');                             //Set the element to be text box
                                        input.className = 'inputText inputText_QuestionChoice';         //Set its class to 'inputText'
                                        input.setAttribute('name', question_type + '['+counter+']');    //We give the new textboxes names if the form multiple_one[0], multiple_one[1] so we can handle them alltogether
                                        td.appendChild(input);                                          //Append the text box to the table cell we created above

                                        var img = document.createElement('img');//Create an image element, that will hold the "delete" icon
                                        img.setAttribute('alt', '_REMOVECHOICE');                       //Set alt and title for this image
                                        img.setAttribute('style','white-space:nowrap');
                                        img.setAttribute('title', '_REMOVECHOICE');
                                        img.setAttribute('src', 'images/16x16/delete.png');      //Set the icon source
                                        img.setAttribute('onclick', 'eF_js_removeImgNode(this, "'+question_type+'")');  //Set the event that will trigger the deletion
                                        img.onclick = function () {eF_js_removeImgNode(this, "'+question_type+'")};  //Set the event that will trigger the deletion
                                        var img_td = document.createElement('td');                      //Create a new table cell to hold the image element
                                        td.appendChild(img);                                         //Append the <td> to the row

                                        var parent_node = last_node.parentNode;                         //Find the parent element, that will hold the new element
                                        parent_node.insertBefore(tr, last_node);                        //Append the table row, that holds the input element, to its parent.
                                    }

                                }

                                //This function removes the <tr> element that contains the inserted node.
                                function eF_js_removeImgNode(el, question_type) {
                                    el.parentNode.parentNode.parentNode.removeChild(el.parentNode.parentNode);      //It is <tr><td><img></td></tr>, so we need to remove the <tr> element, which is the el.parentNode.parentNode

                                    var els = document.getElementsByTagName('input');           //Find all 'input' elements in th document.

                                    var counter = 0;
                                    for (var i = 0; i < els.length; i++) {                      //Count all the 'input' elements whose names match the selected question type (e.g. multiple_one[0], multiple_one[1] etc)
                                        if (els[i].name.match('^'+question_type) && els[i].type.match('text')) {
                                            els[i].name = question_type+'['+counter+']';        //If the user deletes a middle choice, the remaining choices will not be continuous. I.e, if we have choices multiple_one[0-5] and the user removes multiple_one[3], then the array will have indexes 0,1,2,4,5. So, here, we re-calculate the number of choices and apply new array indexes
                                            counter++;
                                        }
                                    }

                                    if (question_type == 'multiple_one') {                      //Adjust the select box accordingly
                                        document.getElementById('correct_multiple_one').removeChild(document.getElementById('correct_multiple_one').lastChild);
                                    } else if (question_type == 'multiple_many' || question_type == 'match') {               //For multiple/many (and match) questions, we need to reindex checkboxes (or answer text boxes) as well
                                        var counter = 0;
                                        for (var i = 0; i < els.length; i++) {
                                            if (els[i].name.match('correct_'+question_type)) {
                                                els[i].name = 'correct_'+question_type+'['+counter+']';
                                                counter++;
                                            }
                                        }
                                    }
                                }


                                //This function is used to create the text boxes that correspond to empty spaces.
                                function eF_js_createEmptySpaces() {

                                    if (tinyMCE) {                                                              //Get the text from the editor
                                        var question_text = tinyMCE.getContent('question_text');
                                    } else {
                                        var question_text = document.getElementById('question_text').value;     //If the editor isn't set, get the question text from the text area
                                    }

                                    var excerpts = question_text.split(/###/g);                         //Get the question text pieces that are split by ###
                                //alert(excerpts);    alert(excerpts.length);
                                    var last_node = document.getElementById('empty_spaces_last_node');  //This is the node that the new elements will be inserted before
                                    var parent_node = last_node.parentNode;                             //Find the parent element, that will hold the new element

                                    if (document.getElementById('spacesRow')) {                         //If the button was pressed again, remove old row and build a new one
                                        document.getElementById('spacesRow').parentNode.removeChild(document.getElementById('spacesRow'));
                                    }

                                    var tr = document.createElement('tr');              //Create a table row to hold the new element
                                    tr.setAttribute('id', 'spacesRow');                 //We need an id to know which row this is, so we can remove it on demand
                                    tr.appendChild(document.createElement('td'));       //Create a new empty table cell for alignment reasons. Append this table cell to the table row we created above

                                    var td = document.createElement('td');              //Create a new table cell to hold the new element
                                    td.setAttribute('colspan', '100%');
                                    tr.appendChild(td);                                 //Append this table cell to the table row we created above

                                    for (var i = 0; i < excerpts.length; i++) {         //For each designated empty space, create a span element that will hold the text and the text boxes
                                        var span = document.createElement('span');
                                        span.innerHTML = excerpts[i];                   //Add each text piece to the span
                                        td.appendChild(span);                                        //Append the span to the table cell.

                                        if (i != excerpts.length - 1) {                                  //If, for example, we have 3 ###, these split the string to 4 parts. So, we must not insert a text box for the last (trailing) string piece
                                            var input = document.createElement('input');                 //Create the new input element
                                            input.setAttribute('type', 'text');                          //Set the element to be text box
                                            input.setAttribute('class', 'inputText');                    //Set its class to 'inputText'
                                            input.setAttribute('name', 'empty_spaces['+i+']');           //We give the new textboxes names if the form empty_spaces[0], empty_spaces[1] so we can handle them alltogether
                                            td.appendChild(input);                                       //Append the text box to the table cell we created above
                                        }
                                    }
                                    parent_node.insertBefore(tr, last_node);             //Append the table row, that holds the input element, to its parent.
                                }

               {/literal}
               </script>
               {capture name='question'}
                    {$T_ADD_QUESTION.javascript}
                    <form {$T_ADD_QUESTION.attributes}>
                    {$T_ADD_QUESTION.hidden}
                    <table class="formElements">
                        <input type="hidden" name="question_type" value="{$smarty.get.question_type}" />
                        <input type="hidden" name="surveys_ID" value="{$smarty.get.surveys_ID}" />
                        {if ($smarty.get.question_action == 'update_question')}
                            <input type="hidden" name="question_ID" value="{$smarty.get.question_ID}" />
                            <input type="hidden" name="father_ID", value="{$smarty.get.father_ID}" />
                        {/if}
                        <tr><td class = "labelCell">{$smarty.const._QUESTIONTEXT}:</td>
                        <td>{$T_ADD_QUESTION.question_text.html}</td></tr>
                        {if ($smarty.get.question_type != 'development')}
                        <tr><td class = "labelCell">{$smarty.const._INSERTMULTIPLEQUESTIONS}:</td>
                        {/if}
                        {if ($smarty.get.question_type == 'yes_no')}
                        {if ($smarty.get.question_action == 'update_question') }
                            <td align = "left">
                                {$T_ADD_QUESTION.yes_no[0].html}
                            </td>
                            </tr>
                            <tr><td></td><td>{$T_ADD_QUESTION.yes_no[1].html}</tr></tr>
                        {else}
                            <td align = "left">
                            {$T_ADD_QUESTION.yes_no[0].html}
                            </td>
                            </tr>
                            <tr><td></td><td>{$T_ADD_QUESTION.yes_no[1].html}</tr></tr>
                        {/if}
                    {/if}
                    {if ($smarty.get.question_type == 'development')}
                    {/if}
                    {if ($smarty.get.question_type == 'dropdown')}
                        {if ($smarty.get.question_action == 'update_question')}
                            {foreach name='drop_down_list' item=item key=key from=$T_ADD_QUESTION.drop_down}
                                {if ($smarty.foreach.drop_down_list.index == 0)}
                                    <td>{$item.html}</td>
                                    </tr>
                                {elseif ($smarty.foreach.drop_down_list.index == 1)}
                                    <tr><td></td><td>{$item.html}</td></tr>
                                {else}
                                    <tr><td></td><td>{$item.html}<img  valign="center" align="center" src="images/16x16/delete.png" border="0px" onclick="eF_js_removeImgNode(this, '{$smarty.get.question_type}')" ></td></tr>
                                {/if}
                            {/foreach}
                        {else}
                            <td align = "left">{$T_ADD_QUESTION.drop_down[0].html}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td align = "left">{$T_ADD_QUESTION.drop_down[1].html}</td>
                            </tr>
                        {/if}
                        <tr id = "drop_down_last_node"></tr>
                        <tr><td width="5%" align="right">
                            <a href = "javascript:void(0)" onclick = "eF_js_addAdditionalChoice('drop_down')"><img src = "images/16x16/add2.png" alt = "{$smarty.const._ADDQUESTION}" title = "{$smarty.const._ADDQUESTION}" border = "0"/></a>
                        </td><td width="95%" align="left">
                            <a href = "javascript:void(0)" onclick = "eF_js_addAdditionalChoice('drop_down')">{$smarty.const._ADDOPTION}</a></td></tr>
                    {/if}
                    {if ($smarty.get.question_type == 'multiple_one')}
                        {if ($smarty.get.question_action == 'update_question')}
                            {foreach name='multiple_one_list' item=item key=key from=$T_ADD_QUESTION.multiple_one}
                                {if ($smarty.foreach.multiple_one_list.index == 0)}
                                    <td>{$item.html}</td>
                                    </tr>
                                {elseif ($smarty.foreach.multiple_one_list.index == 1)}
                                    <tr><td></td><td>{$item.html}</td></tr>
                                {else}
                                    <tr><td></td><td>{$item.html}<img  valign="center" align="center" src="images/16x16/delete.png" border="0px" onclick="eF_js_removeImgNode(this, '{$smarty.get.question_type}')" ></td></tr>
                                {/if}
                            {/foreach}
                        {else}
                            <td align = "left">{$T_ADD_QUESTION.multiple_one[0].html}</td></tr>
                            <tr><td></td><td align = "left">{$T_ADD_QUESTION.multiple_one[1].html}</td></tr>
                        {/if}
                        <tr id = "multiple_one_last_node"></tr>
                        <tr><td width="10%" align="right">
                            <a href = "javascript:void(0)" onclick = "eF_js_addAdditionalChoice('multiple_one')"><img src = "images/16x16/add2.png" alt = "{$smarty.const._ADDQUESTION}" title = "{$smarty.const._ADDQUESTION}" border = "0"/></a></td>
                        </td><td width="90%" align="left">
                            <a href = "javascript:void(0)" onclick = "eF_js_addAdditionalChoice('multiple_one')">{$smarty.const._ADDOPTION}</a></td></tr>
                    {/if}
                    {if ($smarty.get.question_type == 'multiple_many')}
                        {if ($smarty.get.question_action == 'update_question')}
                            {foreach name='multiple_one_list' item=item key=key from=$T_ADD_QUESTION.multiple_many}
                                {if ($smarty.foreach.multiple_many_list.index == 0)}
                                    <td>{$item.html}</td>
                                    </tr>
                                {elseif ($smarty.foreach.multiple_many_list.index == 1)}
                                    <tr><td></td><td>{$item.html}</td></tr>
                                {else}
                                    <tr><td></td><td>{$item.html}<img  valign="center" align="center" src="images/16x16/delete.png" border="0px" onclick="eF_js_removeImgNode(this, '{$smarty.get.question_type}')" ></td></tr>
                                {/if}
                            {/foreach}
                        {else}
                            <td align = "left">{$T_ADD_QUESTION.multiple_many[0].html}</td></tr>
                            <tr><td></td><td align = "left">{$T_ADD_QUESTION.multiple_many[1].html}</td></tr>
                        {/if}
                        <tr id = "multiple_many_last_node"></tr>
                        <tr><td width="10%" align="right">
                            <a href = "javascript:void(0)" onclick = "eF_js_addAdditionalChoice('multiple_many')"><img src = "images/16x16/add2.png" alt = "{$smarty.const._ADDQUESTION}" title = "{$smarty.const._ADDQUESTION}" border = "0"/></a></td>
                        </td><td width="90%" align="left">
                            <a href = "javascript:void(0)" onclick = "eF_js_addAdditionalChoice('multiple_many')">{$smarty.const._ADDOPTION}</a></td></tr>
                    {/if}
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td></td>
                        <td align="left"><input class="flatbutton" type="submit" value="{$smarty.const._SAVE}" />
                        </td>
                    </tr>
                    </table>
                </form>
            {/capture}
            {eF_template_printInnerTable title=$smarty.const._CREATESURVEYQUESTION data=$smarty.capture.question image='32x32/form_green.png'}
        {/if}
    {/if}
    {if ($smarty.get.action == 'create_survey' && $smarty.get.screen == '1')}
        {capture name='createSurveyForm'}
            {$T_CREATE_SURVEY.javascript}
            {if ( $smarty.get.survey_action == 'create' ) }
                {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href="#">'|cat:$smarty.const._CREATESURVEY|cat:'</a>'}
            {/if}
            {if ( $smarty.get.survey_action == 'update' )}
                {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a  class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href="#">'|cat:$smarty.const._EDITSURVEY|cat:'</a>'}
            {/if}
            <table class="formElements">
                <form {$T_CREATE_SURVEY.attributes}>
                {$T_CREATE_SURVEY.hidden}
                <tr>
                    <td class = "labelCell">{$smarty.const._SURVEYCODE}:</td>
                    <td>{$T_CREATE_SURVEY.surveyCode.html}</td>
                </tr>
                <tr>
                    <td class = "labelCell">{$smarty.const._SURVEYNAME}:</td>
                    <td>{$T_CREATE_SURVEY.surveyName.html}</td>
                </tr>
                <tr>
                    <td class = "labelCell">{$smarty.const._SURVEYINFO}:</td>
                    <td>{$T_CREATE_SURVEY.surveyInfo.html}</td>
                </tr>
                {if ($smarty.get.survey_action == 'save')}
                    <tr>
                        <td class = "labelCell">{$smarty.const._SURVEYAVALIABLEFROM}:</td>{eF_template_html_select_date instant='1' end_year='+3'}</td>
                    </tr>
                    <tr>
                        <td class = "labelCell">{$smarty.const._SURVEYUNTIL}:</td>
                        <td>{eF_template_html_select_date instant='2' end_year='+3'}</td>
                    </tr>
                {else}
                    <tr>
                        <td class = "labelCell">{$smarty.const._SURVEYAVALIABLEFROM}:</td>
                        <td>{eF_template_html_select_date instant='1' time=$T_START_DATE end_year='+3'}</td>
                    </tr>
                    <tr>
                        <td class = "labelCell">{$smarty.const._SURVEYUNTIL}:</td>
                        <td>{eF_template_html_select_date instant='2' time=$T_END_DATE end_year='+3'}</td>
                    </tr>
                {/if}
                <tr>
                    <td class = "labelCell">{$smarty.const._SURVEYINTROTEXT}:</td>
                    <td>{$T_CREATE_SURVEY.surveyIntro.html}</td>
                </tr>
                <tr>
                    <td class = "labelCell">{$smarty.const._SURVEYENDTEXT}:</td>
                    <td>{$T_CREATE_SURVEY.surveyEnd.html}</td>
                </tr>
                <tr><td>&nbsp;</td></tr>
                <tr><td></td><td align="left"><input class="flatButton" type="submit" value="{$smarty.const._SAVE}" /></td></tr>
                </form>
            </table>
        {/capture}
        {eF_template_printInnerTable title=$smarty.const._CREATESURVEY  data=$smarty.capture.createSurveyForm image='32x32/form_green.png'}
    {/if}
    {if ($smarty.get.action == 'preview') }
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo;&nbsp;'|cat:'<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'">'|cat:$T_SURVEYNAME|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href="#">'|cat:$smarty.const._PREVIEW|cat:'</a>'}
        {capture name='t_no_questions'}
            {if ($T_SIZEOF_QUESTIONS != '0')}
                {eF_template_printSurvey data=$T_SURVEY_INFO questions=$T_SURVEY_QUESTIONS user_type=$T_USER}
            {else}
                <table><tr><td class="emptyCategory">{$smarty.const._NOQUESTIONSFORSURVEY}</td></tr></table>
            {/if}
        {/capture}
        {eF_template_printInnerTable title=$smarty.const._PREVIEW data=$smarty.capture.t_no_questions image='32x32/form_green.png'}
    {/if}
    {if (isset($smarty.get.action) && $smarty.get.action == 'view_users') }
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo;&nbsp;'|cat:'<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'">'|cat:$T_SURVEYNAME|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href="#">'|cat:$smarty.const._DONESURVEYUSERS|cat:'</a>'}
        {capture name='t_view_users'}
            {if ($T_SIZEOF_USERS != '0') }
                <table class="sortedTable" width="100%">
                    <tr><td class="topTitle">{$smarty.const._LOGIN}</td>
                        <td class="topTitle">{$smarty.const._NAME}</td>
                        <td class="topTitle">{$smarty.const._SURNAME}</td>
                        <td class="topTitle" align="center">{$smarty.const._SURVEYDONE}</td>
                        <td class="topTitle" align="center">{$smarty.const._OPERATIONS}</td>
                    {section name='t_users' loop=$T_SURVEY_USERS}
                    <tr class = "{cycle name = "su_stats_h_info" values = "oddRowColor, evenRowColor"}">
                        <td>{$T_SURVEY_USERS[t_users].users_LOGIN}</td>
                        <td>{$T_SURVEY_USERS[t_users].name}</td>
                        <td>{$T_SURVEY_USERS[t_users].surname}</td>
                        {if ( $T_DONE_SURVEY[t_users]=='true' ) }
                            <td align="center"><img src='images/16x16/check2.png' border='0px'><span style = "display:none">1</span></td>
                        {else}
                            <td align="center"><img src='images/16x16/delete2.png' border='0px'><span style = "display:none">0</span></td>
                        {/if}
                        {if ( $T_DONE_SURVEY[t_users]=='true' ) }
                            <td align="center" valign="middle" style="white-space:nowrap">
                                <a href="professor.php?ctg=survey&action=survey_preview&user={$T_SURVEY_USERS[t_users].users_LOGIN}&surveys_ID={$smarty.get.surveys_ID}"><img src='images/16x16/view.png' border='0px' title='{$smarty.const._PREVIEW}'></a>&nbsp;
                                <a href="professor.php?ctg=survey&action=view_users&preview_action=delete_user&user={$T_SURVEY_USERS[t_users].users_LOGIN}&surveys_ID={$smarty.get.surveys_ID}"><img src='images/16x16/delete.png' border='0px' title='{$smarty.const._DELETE}' />
                            </td>
                        {else}
                            <td class="emptyClass" align="center"> - </td>
                        {/if}
                    </tr>
                    {/section}
                </table>
            {else}
                <table><tr><td class="emptyCategory">{$smarty.const._NOUSERFORTHISSURVEYYET}</td></tr></table>
            {/if}
        {/capture}
            {eF_template_printInnerTable title=$smarty.const._DONESURVEYUSERS data=$smarty.capture.t_view_users image='32x32/form_green.png'}
    {/if}
    {if ( isset($smarty.get.action) && $smarty.get.action == 'survey_preview' ) }
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo;&nbsp;'|cat:'<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'">'|cat:$T_SURVEYNAME|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href="#">'|cat:$smarty.const._SURVEYPREVIEWFORUSER|cat:$smarty.get.user|cat:'</a>'}
            {capture name='preview_survey'}
                {eF_template_printSurvey data=$T_SURVEY_INFO questions=$T_SURVEY_QUESTIONS answers=$T_USER_ANSWERS user_type=$T_USER user=$smarty.get.user action=$smarty.get.action}
        {/capture}
        {eF_template_printInnerTable title=$smarty.const._SURVEYPREVIEWFORUSER|cat:$smarty.get.user data=$smarty.capture.preview_survey image='32x32/form_green.png'}
    {/if}
    {if (isset($smarty.get.action) && $smarty.get.action == 'statistics') }
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo;&nbsp;'|cat:'<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'">'|cat:$T_SURVEYNAME|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href="#">'|cat:$smarty.const._STATISTICS|cat:'</a>'}
    {capture name='surveyStatistics'}
            <table width="100%" border="0px">
            {foreach  item=general from=$T_SURVEY_INFO}
                {foreach item=item key=key from=$general}
                    {if ($key == 'survey_code')}
                        <tr class = "{cycle name = "su_stats_h_info" values = "oddRowColor, evenRowColor"}"><td class="labellCell" width="10%">{$smarty.const._SURVEYCODE}:</td><td width="90%" colspan="2">{$item}</td></tr>
                    {/if}
                    {if ($key == 'survey_name')}
                        <tr class = "{cycle name = "su_stats_h_info" values = "oddRowColor, evenRowColor"}"><td class="labellCell" width="10%">{$smarty.const._SURVEYNAME}:</td><td width="90%" colspan="2">{$item}</td></tr>
                    {/if}
                    {if ($key == 'survey_info')}
                        <tr class = "{cycle name = "su_stats_h_info" values = "oddRowColor, evenRowColor"}"><td class="labellCell" width="10%">{$smarty.const._SUBTITLE}:</td><td width="90%" colspan="2">{$item}</td></tr>
                    {/if}
                    {if ($key == 'start_date')}
                        <tr class = "{cycle name = "su_stats_h_info" values = "oddRowColor, evenRowColor"}"><td class="labellCell" width="10%">{$smarty.const._SURVEYAVALIABLEFROM}:</td><td width="90%" colspan="2">#filter:timestamp-{$item}#</td></tr>
                    {/if}
                    {if ($key == 'end_date')}
                        <tr class = "{cycle name = "su_stats_h_info" values = "oddRowColor, evenRowColor"}"><td class="labellCell" width="10%">{$smarty.const._SURVEYUNTIL}:</td><td width="90%" colspan="2">#filter:timestamp-{$item}#</td></tr>
                    {/if}
                {/foreach}
            {/foreach}
            <tr class = "{cycle name = "su_stats_h_info" values = "oddRowColor, evenRowColor"}"><td class="labellCell" width="10%">{$smarty.const._USERS}:</td><td align="left" width="90%" colspan="2">{$T_USERS_DONE}/{$T_USERS_OVERALL}</td></tr>
            <tr class = "{cycle name = "su_stats_h_info" values = "oddRowColor, evenRowColor"}"><td class="labellCell" width="10%" style="white-space:nowrap">{$smarty.const._STATISTICS}:</td><td colspan="2"><a href="professor.php?ctg=survey&action=statistics&statistics_action=export&surveys_ID={$smarty.get.surveys_ID}"><img src="images/others/xls.ico" border="0px" title="{$smarty.const._EXPORTSTATS}" title="{$smarty.const._EXPORTSTATS}"/></a></td></tr>
            <tr><td colspan="3">&nbsp;</td></tr>
        {if ($T_TOTAL_DONE_USERS == '0')}
            <tr><td width="100%" colspan="3" align="left" class = "emptyCategory">{$smarty.const._NOUSERHAVEDONETHISSURVEYYET}</td></tr>
        {else}
            {section name='t_survey_statistics' loop=$T_SURVEY_QUESTIONS}
                <tr class = "{cycle name = "su_stats_info" values = "oddRowColor, evenRowColor"}"><td class="questionWeight" colspan="3"><img align="left" valign="center" src="images/16x16/form_green.png" border="0px">&nbsp;<b>{$smarty.const._QUESTION}:&nbsp;{$T_SURVEY_QUESTIONS[t_survey_statistics].question}</b></td></tr>
                {foreach from=$T_SURVEY_QUESTIONS_CHOICES[t_survey_statistics]  item=choice key=type}
                    {foreach from=$choice item=item key=key}
                        <tr  class = "{cycle name = "su_stats_info" values = "oddRowColor, evenRowColor"}">
                            <td valign="top" align="right" width="10%">
                            {$T_SURVEY_ANSWER_RATE[t_survey_statistics].$key}%
                                -
                            {$T_SURVEY_VOTES[t_survey_statistics].$key}/{$T_USERS_DONE}
                            </td>
                            <td valign="top" align="left" width="30%">
                               {$item}
                            </td>
                            <td valign="top" width="60%" height="20px" align="left">
                                <span style = "position:absolute;text-align:center;width:200px;border:1px solid #d3d3d3;vertical-align:middle;z-index:2">{$T_SURVEY_ANSWER_RATE[t_survey_statistics].$key}%</span>
                                    <span style = "background-color:#A0BDEF;width:{$T_SURVEY_ANSWER_RATE[t_survey_statistics].$key*2}px;border:1px dotted #d3d3d3;position:absolute">&nbsp;</span>
                            </td>
                        </tr>
                    {/foreach}
                {/foreach}
                <tr><td colspan="3">&nbsp;</td></tr>
            {/section}
        {/if}
    </table>
    {/capture}
        {eF_template_printInnerTable title=$smarty.const._SURVEYSTATISTICS  data=$smarty.capture.surveyStatistics image='32x32/form_green.png'}
    {/if}
    {if (isset($smarty.get.action) && $smarty.get.action == 'publish') }
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey">'|cat:$smarty.const._SURVEY|cat:'</a>&nbsp;&raquo;&nbsp;'|cat:'<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'">'|cat:$T_SURVEYNAME|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=survey&surveys_ID='|cat:$smarty.get.surveys_ID|cat:'&action=publish">'|cat:$smarty.const._PUBLISH|cat:'</a>'}
        {capture name='t_publish_survey'}
            {$T_PUBLISH_FORM.javascript}
            <form {$T_PUBLISH_FORM.attributes}>
                <table width = "100%" class="sortedTable" rowsPerPage="10">
                    <tr>
                        <td class = "topTitle" align = "left">{$smarty.const._NAME}</td>
                        <td class = "topTitle" align = "left">{$smarty.const._SURNAME}</td>
                        <td class = "topTitle" align = "left">{$smarty.const._EMAIL}</td>
                        <td class = "topTitle" align = "left">{$smarty.const._LOGIN}</td>
                        <td class = "topTitle" align = "left">{$smarty.const._SURVEYSELECTION}</td>
                    </tr>
                    {section name='survey_users' loop=$T_LESSON_USERS}
                        <tr class="{cycle name = "su_users" values = "oddRowColor, evenRowColor"}">
                            <td align="left">{$T_LESSON_USERS[survey_users].name}</td>
                            <td align="left">{$T_LESSON_USERS[survey_users].surname}</td>
                            <td align="left">{$T_LESSON_USERS[survey_users].email}</td>
                            <td align="left">{$T_LESSON_USERS[survey_users].login}</td>
                            <td>
                                <input type="hidden" name="user_email[{$smarty.section.survey_users.index}]" value="{$T_LESSON_USERS[survey_users].email}"/>
                                <input type="hidden" name="user_login[{$smarty.section.survey_users.index}]" value="{$T_LESSON_USERS[survey_users].login}"/>
                                {if ($T_EXISTS[survey_users] == 'true')}
                                    <input type="checkbox" name="selection[{$smarty.section.survey_users.index}]" checked/>
                                {else}
                                    <input type="checkbox" name="selection[{$smarty.section.survey_users.index}]"/>
                            {/if}
                        </td>
                    </tr>
                    {/section}
                </table>
                <table width="100%" align="center">
                    <tr><td>&nbsp;</td></tr>
                    <tr><td class="horizontalSeparator" colspan="2"></td></tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td class="labelCell">{$smarty.const._ENTERMAILMESSAGE}:</td>
                        <td>{$T_PUBLISH_FORM.email_intro.html}</td>
                    </tr>
                    <tr><td class="labelCell">{$smarty.const._SENDALSOVIAEMAIL}:</td><td>{$T_PUBLISH_FORM.send_email.html}</td></tr>
                    <tr><td class="horizontalSeparator" colspan="2">&nbsp;</td></tr>
                    <tr><td></td><td  align="left"><input class="flatbutton" type="submit" value="{$smarty.const._SUBMIT}"/></td></tr>
                </table>
            </form>
        {/capture}
            {eF_template_printInnerTable title=$smarty.const._ADDUSERSTOSURVEY data=$smarty.capture.t_publish_survey image='32x32/form_green.png'}
    {/if}
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


{**************}
{* MODULE HCD: *}
{**************}
{*yyyyyyyyyyyyyyyyyyyypopto*}
{if (isset($T_CTG) && $T_CTG == 'users' && $T_MODULE_HCD_INTERFACE)}
    {if !isset($smarty.get.print_preview) && !isset($smarty.get.print)}
    {assign var = "title" value = '<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=users">'|cat:$smarty.const._USERS|cat:'</a>'}
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
                                            {eF_template_printInnerTable title = $smarty.const._UPDATEUSERS data = $smarty.capture.t_users_code image = '32x32/user1.png'}
                            </td></tr>
        {/if}
    {/capture}

    {/if}

{/if}


{if (isset($T_CTG) && $T_CTG == 'emails')}
   {assign var = "title" value = '<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=emails">'|cat:$smarty.const._EMAILS|cat:'</a>'}
   {include file = "emails.tpl"}
{/if}



{if (isset($T_CTG) && $T_CTG == 'module_hcd')}

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

    {capture name = "moduleHCD"}
                            <tr><td class = "moduleCell">
                                {include file = 'module_hcd.tpl'}
                            </td></tr>
    {/capture}
{/if}




{if (isset($T_CTG) && $T_CTG == 'evaluations')}
{*moduleHCD: Show evaluations for this employee *}
        {capture name = "moduleEvaluations"}
        {assign var = "title" value = '<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel">'|cat:$smarty.const._CONTROLPANEL|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel&op=users">'|cat:$smarty.const._USERSMANAGEMENT|cat:'</a>&nbsp;&raquo;&nbsp;<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=evaluations&user='|cat:$smarty.get.user|cat:'">'|cat:$smarty.const._EVALUATIONS|cat:'</a>'}
        {capture name = 't_employee_evaluations_code'}

           <table>
               <tr>
                    <td><a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$smarty.get.user}&add_evaluation=1&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._NEWEVALUATION}', new Array('400px', '300px'))"><img src="images/16x16/add2.png" title="{$smarty.const._NEWEVALUATION}" alt="{$smarty.const._NEWEVALUATION}" border="0" /></a></td>
                    <td><a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$smarty.get.user}&add_evaluation=1&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._NEWEVALUATION}', new Array('400px', '300px'))">{$smarty.const._NEWEVALUATION}</a></td>
               </tr>
           </table>

            <table border = "0" width = "100%" class = "sortedTable">
                <tr class = "topTitle">
                    <td class = "topTitle" width = "35%">{$smarty.const._DATE}</td>
                    <td class = "topTitle">{$smarty.const._SUBJECT}</td>
                    <td class = "topTitle">{$smarty.const._AUTHOR}</td>
                    <td class = "topTitle" align="center">{$smarty.const._OPERATIONS}</td>
                </tr>

            {if isset($T_EVALUATION)}
                {foreach name = 'users_list' key = 'key' item = 'evaluation' from = $T_EVALUATION}
                <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                    <td>#filter:timestamp_time-{$evaluation.timestamp}#</td>
                    <td>{$evaluation.specification}</td>
                    <td>{$evaluation.author}</td>
                    <td align = "center">
                        <table>
                            <tr>
                            <td width="45%">
                                <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$smarty.get.user}&edit_evaluation={$evaluation.event_ID}" class = "editLink"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a></td><td>
                            </td>
                            <td  width="45%">
                                <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$smarty.get.user}&delete_evaluation={$evaluation.event_ID}&tab=evaluations" onclick = "return confirm('{$smarty.const._AREYOUSUREYOUWANTTODELETEEVALUATION}')" class = "deleteLink"><img border = "0" src = "images/16x16/delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a>
                            </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                {/foreach}
            {else}
                <tr>
                    <td colspan=4 class = "emptyCategory centerAlign">{$smarty.const._NOEVALUATIONSASSIGNEDYET}</td>
                </tr>
            {/if}

            </table>
        {/capture}
        {eF_template_printInnerTable title = $smarty.const._EVALUATIONOFEMPLOYEE|cat:'&nbsp;'|cat:$smarty.get.user data = $smarty.capture.t_employee_evaluations_code image = '32x32/cabinet.png'}
        {/capture}
{/if}
{******TELOS HCD *******}

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

{if isset($T_SHOW_TOOLS)}
{*sideTools: Content tools appearing in the right menu bar*}
    {capture name = "sideTools"}
        {capture name = 't_tools_code'}
            <table width = "100%" border = "0">
               <tr><td>
                    <span class = "counter">{counter}.</span>&nbsp;<a title="{$smarty.const._UPLOADFILESANDIMAGES}" href = "{$smarty.server.PHP_SELF}?ctg=content&op=file_manager&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._UPLOADFILESANDIMAGES}', 3)" target = "POPUP_FRAME">{$smarty.const._UPLOADFILESANDIMAGES|eF_truncate:40}</a>
                </td></tr>
        {if !isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change'}
                <tr><td>
                    <span class = "counter">{counter}.</span>&nbsp;<a title="{$smarty.const._COPYFROMANOTHERLESSON}" href = "{$smarty.server.PHP_SELF}?ctg=content&op=copy_content">{$smarty.const._COPYFROMANOTHERLESSON|eF_truncate:40}</a>
                </td></tr>
                <tr><td>
                    <span class = "counter">{counter}.</span>&nbsp;<a title="{$smarty.const._CONTENTTREEMANAGEMENT}" href = "{$smarty.server.PHP_SELF}?ctg=content&op=unit_order">{$smarty.const._CONTENTTREEMANAGEMENT|eF_truncate:40}</a>
                </td></tr>
            {if $T_CURRENT_LESSON->options.scorm}
                <tr><td>
                    <span class = "counter">{counter}.</span>&nbsp;<a  href = "{$smarty.server.PHP_SELF}?ctg=control_panel&op=scorm&scorm_import=1">{$smarty.const._SCORMIMPORT}</a>
                </td></tr>
            {/if}
        {/if}
                <tr><td>
                    <span class = "counter">{counter}.</span>&nbsp;<a title="{$smarty.const._CONTENTMETADATA}" href = "{$smarty.server.PHP_SELF}?ctg=content&op=metadata&view_unit={$T_UNIT.id}">{$smarty.const._CONTENTMETADATA|eF_truncate:40}</a>
                </td></tr>
            </table>
        {/capture}
        {eF_template_printSide title=$smarty.const._TOOLS data=$smarty.capture.t_tools_code  id = 'show_tools'}
    {/capture}
{/if}

{if (isset($T_CONTENT_TREE))}
{*sideContentTree: Content tree appearing in the right menu bar*}
    {capture name = "sideContentTree"}
    {if (!isset($T_OPERATION)) || ($T_OPERATION != 'unit_order')} {*Manos 15-01-07 gia na mh vgainei to palio lesson material sthn Content Order selida*}
        {if (!$T_CONTENT_TREE)}
            {capture name = 't_empty_lesson_code'}
            <table>
                <tr><td class = "emptyCategory">{$smarty.const._NOMATERIALINTHISLESSON}</td></tr>
            </table>
            {/capture}
            {eF_template_printSide title=$smarty.const._LESSONMATERIAL data=$smarty.capture.t_empty_lesson_code id = 'content_tree'}
        {else}
        <form name = "insertUpdateForm" method = "post" style="display:none">
            <input class = "flatButton" type = "submit" name = "submit_update_content" value = "{$smarty.const._UPDATEUNIT}"> -
                  <input class = "flatButton" type = "submit" name = "submit_insert_content" value = "{$smarty.const._CREATESUBUNIT}"> -
                  <input class = "flatButton" type = "submit" name = "submit_insert_test"    value = "{$smarty.const._CREATETEST}">
                  <input type = "hidden" name = "parent_content_ID" value = "{$T_CONTENT_ID}">
        </form>

            {*{eF_template_printSide title=$smarty.const._LESSONMATERIAL data=$T_CONTENT_TREE id = 'content_tree'}*}
            {*{eF_template_printSide3 title=$smarty.const._LESSONMATERIAL tree=$T_NEW_TREE}*}
            {eF_template_printSide title=$smarty.const._LESSONMATERIAL data = $T_CONTENT_TREE}
        {/if}
    {/if}
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

<table class = "mainTable" id = "mainTable">
    <tr>
        <td style = "vertical-align: top;">
            <table  class = "centerTable">
{if !$smarty.get.popup && !$T_POPUP_MODE}
                <tr class = "topTitle">
                    {if (!isset($T_SHOW_TOOLS))}
                    <td colspan = "2" class = "topTitle">{$title}</td>         {*Header*}
                    {else}
                        <td colspan = "2" class = "topTitle">
                            <table width = "100%">
                                <tr><td>{$title}</td>
                                    <td width = "10%" align = "right">{$sidebar_right}</td></tr>
                            </table>
                        </td>
                    {/if}
               </tr>
{/if}
{if $T_MESSAGE}
                <tr class = "messageRow">
                    <td colspan = "2">{eF_template_printMessage message=$T_MESSAGE type=$T_MESSAGE_TYPE}</td>        {*Display Message, if any*}
                </tr>
{/if}
{if $smarty.get.message}
                <tr class = "messageRow">
                    <td colspan = "2">{eF_template_printMessage message=$smarty.get.message type=$smarty.get.message_type}</td>        {*Display Message passed through get, if any*}
                </tr>
{/if}
{if $T_SEARCH_MESSAGE}
                <tr class = "messageRow">
                    <td colspan = "2">{eF_template_printMessage message=$T_SEARCH_MESSAGE}</td>        {*Display Search Message, if any*}
                </tr>
{/if}

{if ($T_OPERATION == 'control_panel') || ($T_CTG == 'control_panel' && !isset($T_OP))}
                <tr>
                    <td class = "singleColumn" id = "singleColumn" colspan = "2">
                        <div id="sortableList">
                            <div style="float:left; width:50%; height:100%;margin-left:1px;">
                                <ul class="sortable" id="firstlist" style="height:100px;width:100%;">
                {foreach name=positions_first key=key item=module from=$T_POSITIONS_FIRST}
                                    <li id="firstlist_{$module}">
                                        <table class = "singleColumnData">
                                            {$smarty.capture.$module}
                                        </table>
                                    </li>
                {/foreach}
                {if !in_array('moduleIconLessonOptions', $T_POSITIONS) && $smarty.capture.moduleIconLessonOptions}
                                    <li id="firstlist_moduleIconLessonOptions">
                                        <table class = "singleColumnData">
                                            {$smarty.capture.moduleIconLessonOptions}
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
                                </ul>
                            </div>
                            <div style="float: right; width:49%;height: 100%;margin-right:1px;">
                                <ul class="sortable" id="secondlist" style="height:100px;width:100%;">
                {foreach name=positions_first key=key item=module from=$T_POSITIONS_SECOND}
                                    <li id="secondlist_{$module}">
                                        <table class = "singleColumnData">
                                            {$smarty.capture.$module}
                                        </table>
                                    </li>
                {/foreach}
                {if !in_array('moduleNewsList', $T_POSITIONS) && $smarty.capture.moduleNewsList}
                                    <li id="secondlist_moduleNewsList">
                                        <table class = "singleColumnData">
                                            {$smarty.capture.moduleNewsList}
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

{else}                                                                          {*Pages with single-column layout*}
{*SINGLE MAIN COLUMN*}
                <tr>
                    <td class = "singleColumn" id = "singleColumn" >
                        <table class = "singleColumnData">
                                {$smarty.capture.moduleCalendarPage}
                                {$smarty.capture.moduleLessonsList}
                                {$smarty.capture.moduleInsertContent}
                                {*{$smarty.capture.moduleResetLesson}*}
                                {$smarty.capture.moduleCompleteLesson}
                                {$smarty.capture.moduleUnitOrder}
                                {$smarty.capture.moduleContentMetadata}
                                {$smarty.capture.moduleFileManager}
                                {$smarty.capture.moduleCopyContent}
                                {$smarty.capture.moduleRules}
                                {$smarty.capture.moduleShowUnit}
                                {$smarty.capture.moduleTests}
                                {$smarty.capture.moduleAddQuestion}
                                {$smarty.capture.moduleAddTest}
                                {*{$smarty.capture.moduleExercises}*}
                                {$smarty.capture.moduleProjects}
                                {$smarty.capture.moduleAddEditExercise}
                                {$smarty.capture.modulePersonal}
                                {$smarty.capture.moduleSearchResults}
                                {$smarty.capture.moduleNewsPage}
                                {$smarty.capture.moduleProgress}
                                {$smarty.capture.moduleScormOptions}
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
                {$smarty.capture.moduleSurvey}
                        </table>
                    </td>
                </tr>
{/if}
            </table>

        </td>
{if ($T_CTG == 'content' && !$smarty.get.add_unit && !$smarty.get.edit_unit && $T_OP != 'unit_order' && $T_OP != 'file_manager' && $T_OP != 'activate_content' && $T_OP != 'copy_content' && $T_OP != 'metadata' && $T_OP != 'delete_content' && $T_OP != 'repair_tree') || ($T_OPERATION == 'content_management' && !$T_UPDATE_CONTENT_FORM) || ($T_CTG != 'lessons' & $T_OPERATION == 'show_unit') || ($T_OPERATION == 'insert_update_content') || ($T_CTG == 'tests' && ($smarty.get.show_test_content_ID || $smarty.get.view_unit) )}                                     {*Pages that need the right side menu*}
{*RIGHT SIDE MENU*}
    {assign var = 't_show_side_menu' value = true}
         <td class = "sideMenu" id="sideMenu_td">
         <div  id = "sideMenu" style="overflow: visible;">      <!---- here tis the point ------------>
            {$smarty.capture.sideTools}
            {$smarty.capture.sideContentTree}
            {$smarty.capture.sideUnitOperations}
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
{*-----------------------------End of Part 3: Display table-------------------------------------------------*}
<script>var point4 = new Date().getTime();</script>
{*-----------------------------Part 4: Finalization data etc-------------------------------------------------*}

{if ($T_OPERATION == 'control_panel') || ($T_CTG == 'control_panel' && $T_OP != 'users' && $T_OP != 'news' && $T_OP != 'search' && $T_OP != 'lesson_information' && $T_OP != 'progress' && $T_OP != 'scorm')}
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
<script type="text/javascript">
{literal}
    var contentCell = document.getElementById('contentCell');                       //Get the table cell that contains the Unit content
    if (contentCell && contentCell.offsetHeight > 300) {                                           //If this cell is bigger than 300 pixel...
        document.getElementById('navigationDownTable').style.display = '';          //...Then make visible the table that contains the navigation handles below the unit
    }
{/literal}
</script>

<script>var point4_3 = new Date().getTime();</script>

<script>var point5 = new Date().getTime();</script>
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
