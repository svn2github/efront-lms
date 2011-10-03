    {if isset($T_OP) && $T_OP == 'reset_lesson'}
                                    {capture name = 't_reset_lesson_code'}
                                        {$T_RESET_LESSON_FORM.javascript}
                                        <form {$T_RESET_LESSON_FORM.attributes}>
                                            {$T_RESET_LESSON_FORM.hidden}
                                            <table class = "formElements" style = "margin-left:0px">
                                                <tr><td colspan = "100%">{$smarty.const._CHOOSEWHATTODELETE}</td></tr>
                                                <tr><td colspan = "100%">&nbsp;</td></tr>
           {foreach name = "reset_lesson_options" item = "item" key = "key" from = $T_RESET_LESSON_FORM.options}
                                                <tr><td class = "labelCell">{$item.label}:&nbsp;</td>
                                                    <td>{$item.html}</td></tr>
           {/foreach}
                                                <tr><td colspan = "100%">&nbsp;</td></tr>
                                                <tr><td></td><td>{$T_RESET_LESSON_FORM.submit_reset_lesson.html}</td></tr>
                                            </table>
                                        </form>
                                    {/capture}
                                    {eF_template_printBlock title = "`$smarty.const._RESTARTLESSON`<span class = 'innerTableName'>&nbsp;&quot;`$T_CURRENT_LESSON->lesson.name`&quot;</span>" data = $smarty.capture.t_reset_lesson_code image = '32x32/lessons.png' main_options = $T_TABLE_OPTIONS help = 'Administration'}

    {elseif isset($T_OP) && $T_OP == 'import_lesson'}
                                    {capture name = 't_import_lesson_code'}
                                        {$T_IMPORT_LESSON_FORM.javascript}
                                        <form {$T_IMPORT_LESSON_FORM.attributes}>
                                            {$T_IMPORT_LESSON_FORM.hidden}
                                            <table class = "formElements">
                                             <tr><td colspan = "2">{$smarty.const._RESETLESSONDATA}:</td></tr>
                                                <tr><td colspan = "100%">&nbsp;</td></tr>
           {foreach name = "reset_lesson_options" item = "item" key = "key" from = $T_IMPORT_LESSON_FORM.options}
                                                <tr><td class = "labelCell">{$item.label}:&nbsp;</td>
                                                    <td>{$item.html}</td></tr>
           {/foreach}
                                                <tr><td colspan = "2">&nbsp;</td></tr>
                                                <tr><td class = "labelCell">{$smarty.const._LESSONDATAFILE}:&nbsp;</td>
                                                    <td>{$T_IMPORT_LESSON_FORM.file_upload.html}</td></tr>
                                                <tr><td></td><td class = "infoCell">{$smarty.const._EACHFILESIZEMUSTBESMALLERTHAN} <b>{$T_MAX_FILESIZE}</b> {$smarty.const._KB}</td></tr>
                                                <tr><td colspan = "2">&nbsp;</td></tr>
                                                <tr><td></td><td>{$T_IMPORT_LESSON_FORM.submit_import_lesson.html}</td></tr>
                                            </table>
                                        </form>
                                    {/capture}
                                    {eF_template_printBlock title = "`$smarty.const._IMPORTLESSON`<span class = 'innerTableName'>&nbsp;&quot;`$T_CURRENT_LESSON->lesson.name`&quot;</span>" data = $smarty.capture.t_import_lesson_code image = '32x32/import.png' main_options = $T_TABLE_OPTIONS help = 'Administration'}
    {elseif isset($T_OP) && $T_OP == 'export_lesson'}
                                   {capture name = 't_export_lesson_code'}
                                        <fieldset class = "fieldsetSeparator">
                                        <legend>{$smarty.const._EXPORTLESSON}</legend>
                                        {$T_EXPORT_LESSON_FORM.javascript}
                                        <form {$T_EXPORT_LESSON_FORM.attributes}>
                                            {$T_EXPORT_LESSON_FORM.hidden}
                                            <table class = "formElements" style = "margin-left:0px">
          {if $T_NEW_EXPORTED_FILE}
                                                <tr><td colspan = "2">{$smarty.const._DOWNLOADEXPORTED}:&nbsp; <a href = "view_file.php?file={$T_NEW_EXPORTED_FILE.id}&action=download">{$T_NEW_EXPORTED_FILE.name}</a> ({$T_NEW_EXPORTED_FILE.size} {$smarty.const.KB}, #filter:timestamp-{$T_NEW_EXPORTED_FILE.timestamp}#)</td></tr>
                                        {elseif $T_EXPORTED_FILE}
                                                <tr><td colspan = "2">{$smarty.const._EXISTINGFILE}:&nbsp;<a href = "view_file.php?file={$T_EXPORTED_FILE.id}&action=download">{$T_EXPORTED_FILE.name}</a> ({$T_EXPORTED_FILE.size} {$smarty.const.KB}, #filter:timestamp-{$T_EXPORTED_FILE.timestamp}#)</td></tr>
                                        {/if}
                                                <tr><td class = "labelCell">{$T_EXPORT_LESSON_FORM.export_files.label}:&nbsp;</td>
                                                 <td class = "elementCell">{$T_EXPORT_LESSON_FORM.export_files.html}</td></tr>
                                                <tr><td class = "labelCell">{$smarty.const._CLICKTOEXPORT}:&nbsp;</td>
                                                    <td class = "submitCell">{$T_EXPORT_LESSON_FORM.submit_export_lesson.html}</td></tr>
                                            </table>
                                        </form>
                                        </fieldset>
                                    {/capture}
                                    {eF_template_printBlock title = "`$smarty.const._EXPORTLESSON`<span class = 'innerTableName'>&nbsp;&quot;`$T_CURRENT_LESSON->lesson.name`&quot;</span>" data = $smarty.capture.t_export_lesson_code image = '32x32/export.png' main_options = $T_TABLE_OPTIONS help = 'Administration'}
    {elseif isset($T_OP) && $T_OP == 'lesson_users'}

                              {capture name = 't_users_to_lessons_code'}
<!--ajax:usersTable-->
                                                    <table style = "width:100%" class = "sortedTable" size = "{$T_USERS_SIZE}" sortBy = "0" id = "usersTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?{$T_BASE_URL}&op=lesson_users&">
                                                        <tr class = "topTitle">
                                                            <td class = "topTitle" name = "login">{$smarty.const._LOGIN}</td>
                                                            <td class = "topTitle" name = "name">{$smarty.const._FIRSTNAME}</td>
                                                            <td class = "topTitle" name = "surname">{$smarty.const._LASTNAME}</td>
                                                            <td class = "topTitle" name = "user_type">{$smarty.const._USERTYPE}</td>
                                                            <td class = "topTitle" name = "role">{$smarty.const._USERROLEINLESSON}</td>
                                                            <td class = "topTitle centerAlign">{$smarty.const._OPERATIONS}</td>
                                                            <td class = "topTitle centerAlign" name = "in_lesson">{$smarty.const._STATUS}</td>
                                                        </tr>
                                {foreach name = 'users_to_lessons_list' key = 'key' item = 'user' from = $T_ALL_USERS}
                                                        <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$user.active}deactivatedTableElement{/if}">
                                                            <td>#filter:login-{$user.login}#</td>
                                                            <td>{$user.name}</td>
                                                            <td>{$user.surname}</td>
                                                            <td>{$T_ROLES[$user.basic_user_type]}</td>
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
                                                            <td align="center">






                                                            {if $user.basic_user_type == 'student'}
                                                                    <img class = "ajaxHandle" src="images/16x16/refresh.png" title="{$smarty.const._RESETPROGRESSDATA}" alt="{$smarty.const._RESETPROGRESSDATA}" onclick = "if (confirm(translations['_IRREVERSIBLEACTIONAREYOUSURE'])) resetProgress(this, '{$user.login}');">
                                                            {/if}
                                                            </td>
                                                            <td align="center">
                                    {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
                                                                <input class = "inputCheckbox" type = "checkbox" name = "checked_{$user.login}" id = "checked_{$user.login}" onclick = "ajaxPost('{$user.login}', this);" {if in_array($user.login, $T_LESSON_USERS)}checked = "checked"{/if} />
                                    {else}
                                                                 {if in_array($user.login, $T_LESSON_USERS)}<img src = "images/16x16/success.png" title = "{$smarty.const._LESSONUSER}" alt = "{$smarty.const._LESSONUSER}" >{/if}
                                    {/if}
                                                            </td>
                                                    </tr>
                                {/foreach}
                                </table>
<!--/ajax:usersTable-->
        {/capture}
                    {eF_template_printBlock title = "`$smarty.const._UPDATEUSERSTOLESSONS`<span class = 'innerTableName'>&nbsp;&quot;`$T_CURRENT_LESSON->lesson.name`&quot;</span>" data = $smarty.capture.t_users_to_lessons_code image = '32x32/users.png' main_options = $T_TABLE_OPTIONS help = 'Administration'}


    {elseif isset($T_OP) && $T_OP == 'lesson_layout'}
         {capture name = "t_layout_code"}
                    {capture name = "layout_moduleIconFunctions"}
                        <li id = "layoutfirstlist_moduleIconFunctions">
                            <table class = "innerTable" style = "width:300px">
                                <tr class = "handle">
                                    <th class = "innerTableHeader">
                                        <img class = "iconTableImage" src = "images/32x32/options.png" alt = "{$smarty.const._CURRENTCONTENT}" title = "{$smarty.const._CONTROLPANEL}">
                                        {$smarty.const._CONTROLPANEL}
                                    </th>
                                    <td class = "innerTableHeader" style = "text-align:right"><img src = "images/16x16/{if isset($T_DEFAULT_POSITIONS.visibility.moduleIconFunctions) && $T_DEFAULT_POSITIONS.visibility.moduleIconFunctions == 0}navigate_down.png{else}navigate_up.png{/if}" onclick = "toggleVisibility(Element.extend(this).up().up().next().down(), this);"></td></tr>
                                <tr><td colspan = "2" style = "height:60px;text-align:center;vertical-align:top;{if isset($T_DEFAULT_POSITIONS.visibility.moduleIconFunctions) && $T_DEFAULT_POSITIONS.visibility.moduleIconFunctions == 0}display:none;{/if}"><img src = "images/others/control_panel_thumbnail.png"></td></tr>
                            </table>
                        </li>
                    {/capture}
                    {capture name = "layout_moduleContentTree"}
                        <li id = "layoutfirstlist_moduleContentTree">
                            <table class = "innerTable" style = "width:300px">
                                <tr class = "handle">
                                    <th class = "innerTableHeader">
                                        <img class = "iconTableImage" src = "images/32x32/content.png" alt = "{$smarty.const._CURRENTCONTENT}" title = "{$smarty.const._CURRENTCONTENT}">
                                        {$smarty.const._CURRENTCONTENT}
                                    </th>
                                    <td class = "innerTableHeader" style = "text-align:right"><img src = "images/16x16/{if isset($T_DEFAULT_POSITIONS.visibility.moduleContentTree) && $T_DEFAULT_POSITIONS.visibility.moduleContentTree == 0}navigate_down.png{else}navigate_up.png{/if}" onclick = "toggleVisibility(Element.extend(this).up().up().next().down(), this);"></td></tr>
                                <tr><td colspan = "2" style = "height:60px;text-align:center;vertical-align:top;{if isset($T_DEFAULT_POSITIONS.visibility.moduleContentTree) && $T_DEFAULT_POSITIONS.visibility.moduleContentTree == 0}display:none;{/if}"><img src = "images/others/content_tree_thumbnail.png"></td></tr>
                            </table>
                        </li>
                    {/capture}
     {if $T_CONFIGURATION.disable_projects != 1}
                    {capture name = "layout_moduleProjectsList"}
                        <li id = "layoutfirstlist_moduleProjectsList">
                            <table class = "innerTable" style = "width:300px">
                                <tr class = "handle">
                                    <th class = "innerTableHeader">
                                        <img class = "iconTableImage" src = "images/32x32/projects.png" alt = "{$smarty.const._PROJECTS}" title = "{$smarty.const._PROJECTS}">
                                        {$smarty.const._PROJECTS}
                                    </th>
                                    <td class = "innerTableHeader" style = "text-align:right"><img src = "images/16x16/{if isset($T_DEFAULT_POSITIONS.visibility.moduleProjectsList) && $T_DEFAULT_POSITIONS.visibility.moduleProjectsList == 0}navigate_down.png{else}navigate_up.png{/if}" onclick = "toggleVisibility(Element.extend(this).up().up().next().down(), this);"></td></tr>

                                <tr><td colspan = "2" style = "height:60px;text-align:center;vertical-align:top;{if isset($T_DEFAULT_POSITIONS.visibility.moduleProjectsList) && $T_DEFAULT_POSITIONS.visibility.moduleProjectsList == 0}display:none;{/if}"><img src = "images/others/projects_thumbnail.png"></td></tr>
                            </table>
                        </li>
                    {/capture}
     {/if}
                    {if $T_CONFIGURATION.disable_news != 1}
      {capture name = "layout_moduleNewsList"}
                        <li id = "layoutsecondlist_moduleNewsList">
                            <table class = "innerTable" style = "width:300px">
                                <tr class = "handle">
                                    <th class = "innerTableHeader">
                                        <img class = "iconTableImage" src = "images/32x32/announcements.png" alt = "{$smarty.const._ANNOUNCEMENTS}" title = "{$smarty.const._ANNOUNCEMENTS}">
                                        {$smarty.const._ANNOUNCEMENTS}
                                    </th>
                                    <td class = "innerTableHeader" style = "text-align:right"><img src = "images/16x16/{if isset($T_DEFAULT_POSITIONS.visibility.moduleNewsList) && $T_DEFAULT_POSITIONS.visibility.moduleNewsList == 0}navigate_down.png{else}navigate_up.png{/if}" onclick = "toggleVisibility(Element.extend(this).up().up().next().down(), this);"></td></tr>

                                <tr><td colspan = "2" style = "height:60px;text-align:center;vertical-align:top;{if isset($T_DEFAULT_POSITIONS.visibility.moduleNewsList) && $T_DEFAULT_POSITIONS.visibility.moduleNewsList == 0}display:none;{/if}"><img src = "images/others/news_thumbnail.png"></td></tr>
                            </table>
                        </li>
      {/capture}
     {/if}
     {if $T_CONFIGURATION.disable_messages != 1}
                    {capture name = "layout_modulePersonalMessagesList"}
                        <li id = "layoutsecondlist_modulePersonalMessagesList">
                            <table class = "innerTable" style = "width:300px">
                                <tr class = "handle">
                                    <th class = "innerTableHeader">
                                        <img class = "iconTableImage" src = "images/32x32/mail.png" alt = "{$smarty.const._PERSONALMESSAGES}" title = "{$smarty.const._PERSONALMESSAGES}">
                                        {$smarty.const._PERSONALMESSAGES}
                                    </th>
                                    <td class = "innerTableHeader" style = "text-align:right"><img src = "images/16x16/{if isset($T_DEFAULT_POSITIONS.visibility.modulePersonalMessagesList) && $T_DEFAULT_POSITIONS.visibility.modulePersonalMessagesList == 0}navigate_down.png{else}navigate_up.png{/if}" onclick = "toggleVisibility(Element.extend(this).up().up().next().down(), this);"></td></tr>

                                <tr><td colspan = "2" style = "height:60px;text-align:center;vertical-align:top;{if isset($T_DEFAULT_POSITIONS.visibility.modulePersonalMessagesList) && $T_DEFAULT_POSITIONS.visibility.modulePersonalMessagesList == 0}display:none;{/if}"><img src = "images/others/personal_messages_thumbnail.png"></td></tr>
                            </table>
                        </li>
                    {/capture}
     {/if}
     {if $T_CONFIGURATION.disable_forum != 1}
                    {capture name = "layout_moduleForumList"}
                        <li id = "layoutsecondlist_moduleForumList">
                            <table class = "innerTable" style = "width:300px">
                                <tr class = "handle">
                                    <th class = "innerTableHeader">
                                        <img class = "iconTableImage" src = "images/32x32/forum.png" alt = "{$smarty.const._RECENTMESSAGESATFORUM}" title = "{$smarty.const._RECENTMESSAGESATFORUM}">
                                        {$smarty.const._RECENTMESSAGESATFORUM}
                                    </th>
                                    <td class = "innerTableHeader" style = "text-align:right"><img src = "images/16x16/{if isset($T_DEFAULT_POSITIONS.visibility.moduleForumList) && $T_DEFAULT_POSITIONS.visibility.moduleForumList == 0}navigate_down.png{else}navigate_up.png{/if}" onclick = "toggleVisibility(Element.extend(this).up().up().next().down(), this);"></td></tr>

                                <tr><td colspan = "2" style = "height:60px;text-align:center;vertical-align:top;{if isset($T_DEFAULT_POSITIONS.visibility.moduleForumList) && $T_DEFAULT_POSITIONS.visibility.moduleForumList == 0}display:none;{/if}"><img src = "images/others/forum_thumbnail.png"></td></tr>
                            </table>
                        </li>
                    {/capture}
     {/if}
     {if $T_CONFIGURATION.disable_comments != 1}
                    {capture name = "layout_moduleComments"}
                        <li id = "layoutsecondlist_moduleComments">
                            <table class = "innerTable" style = "width:300px">
                                <tr class = "handle">
                                    <th class = "innerTableHeader">
                                        <img class = "iconTableImage" src = "images/32x32/note.png" alt = "{$smarty.const._COMMENTS}" title = "{$smarty.const._COMMENTS}">
                                        {$smarty.const._COMMENTS}
                                    </th>
                                    <td class = "innerTableHeader" style = "text-align:right"><img src = "images/16x16/{if isset($T_DEFAULT_POSITIONS.visibility.moduleComments) && $T_DEFAULT_POSITIONS.visibility.moduleComments == 0}navigate_down.png{else}navigate_up.png{/if}" onclick = "toggleVisibility(Element.extend(this).up().up().next().down(), this);"></td></tr>

                                <tr><td colspan = "2" style = "height:60px;text-align:center;vertical-align:top;{if isset($T_DEFAULT_POSITIONS.visibility.moduleComments) && $T_DEFAULT_POSITIONS.visibility.moduleComments == 0}display:none;{/if}"><img src = "images/others/comments_thumbnail.png"></td></tr>
                            </table>
                        </li>
                    {/capture}
     {/if}
     {if $T_CONFIGURATION.disable_calendar != 1}
                    {capture name = "layout_moduleCalendar"}
                        <li id = "layoutsecondlist_moduleCalendar">
                            <table class = "innerTable" style = "width:300px">
                                <tr class = "handle">
                                    <th class = "innerTableHeader">
                                        <img class = "iconTableImage" src = "images/32x32/calendar.png" alt = "{$smarty.const._CALENDAR}" title = "{$smarty.const._CALENDAR}">
                                        {$smarty.const._CALENDAR}
                                    </th>
                                    <td class = "innerTableHeader" style = "text-align:right"><img src = "images/16x16/{if isset($T_DEFAULT_POSITIONS.visibility.moduleCalendar) && $T_DEFAULT_POSITIONS.visibility.moduleCalendar == 0}navigate_down.png{else}navigate_up.png{/if}" onclick = "toggleVisibility(Element.extend(this).up().up().next().down(), this);"></td></tr>

                                <tr><td colspan = "2" style = "height:60px;text-align:center;vertical-align:top;{if isset($T_DEFAULT_POSITIONS.visibility.moduleCalendar) && $T_DEFAULT_POSITIONS.visibility.moduleCalendar == 0}display:none;{/if}"><img src = "images/others/calendar_thumbnail.png"></td></tr>
                            </table>
                        </li>
                    {/capture}
     {/if}
                    {capture name = "layout_moduleDigitalLibrary"}
                        <li id = "layoutsecondlist_moduleDigitalLibrary">
                            <table class = "innerTable" style = "width:300px">
                                <tr class = "handle">
                                    <th class = "innerTableHeader">
                                        <img class = "iconTableImage" src = "images/32x32/file_explorer.png" alt = "{$smarty.const._DIGITALLIBRARY}" title = "{$smarty.const._DIGITALLIBRARY}">
                                        {$smarty.const._DIGITALLIBRARY}
                                    </th>
                                    <td class = "innerTableHeader" style = "text-align:right"><img src = "images/16x16/{if isset($T_DEFAULT_POSITIONS.visibility.moduleDigitalLibrary) && $T_DEFAULT_POSITIONS.visibility.moduleDigitalLibrary == 0}navigate_down.png{else}navigate_up.png{/if}" onclick = "toggleVisibility(Element.extend(this).up().up().next().down(), this);"></td></tr>

                                <tr><td colspan = "2" style = "height:60px;text-align:center;vertical-align:top;{if isset($T_DEFAULT_POSITIONS.visibility.moduleDigitalLibrary) && $T_DEFAULT_POSITIONS.visibility.moduleDigitalLibrary == 0}display:none;{/if}"><img src = "images/others/digital_library_thumbnail.png"></td></tr>
                            </table>
                        </li>
                    {/capture}
                {foreach name = 'lesson_modules_list' item = "module" key = "class_name" from = $T_LESSON_MODULES}
                    {assign var = module_name value = $class_name|replace:"_":""}
                    {capture name = "layout_"|cat:$module_name}
                        <li id = "layoutsecondlist_{$class_name|replace:"_":""}">
                            <table class = "innerTable" style = "width:300px">
                                <tr class = "handle">
                                    <th class = "innerTableHeader">
                                        <img class = "iconTableImage" src = "images/32x32/addons.png" alt = "{$module.title}" title = "{$module.title}">
                                        {$module.title}
                                    </th>
                                    <td class = "innerTableHeader" style = "text-align:right"><img src = "images/16x16/{if isset($T_DEFAULT_POSITIONS.visibility.$module_name) && $T_DEFAULT_POSITIONS.visibility.$module_name == 0}navigate_down.png{else}navigate_up.png{/if}" onclick = "toggleVisibility(Element.extend(this).up().up().next().down(), this);"></td></tr>
                                <tr><td colspan = "2" style = "height:60px;text-align:center;vertical-align:middle;{if isset($T_DEFAULT_POSITIONS.visibility.$module_name) && $T_DEFAULT_POSITIONS.visibility.$module_name == 0}display:none;{/if}"><img src = "images/16x16/layout.png" alt = "{$smarty.const._MODULE}" title = "{$smarty.const._MODULE}"></td></tr>
                            </table>
                        </li>
                    {/capture}
                {/foreach}
            <div id = "sortableList" class = "lessonLayout">
             <table>
              <tr><td>
                      <ul class = "sortable" id = "layoutfirstlist">
                      {if !in_array('moduleIconFunctions', $T_DEFAULT_POSITIONS.first) && !in_array('moduleIconFunctions', $T_DEFAULT_POSITIONS.second) && !$T_INVALID_OPTIONS.moduleIconFunctions}{$smarty.capture.layout_moduleIconFunctions}{/if}
                      {if !in_array('moduleContentTree', $T_DEFAULT_POSITIONS.first) && !in_array('moduleContentTree', $T_DEFAULT_POSITIONS.second) && !$T_INVALID_OPTIONS.moduleContentTree}{$smarty.capture.layout_moduleContentTree}{/if}
                      {if !in_array('moduleProjectsList', $T_DEFAULT_POSITIONS.first) && !in_array('moduleProjectsList', $T_DEFAULT_POSITIONS.second) && !$T_INVALID_OPTIONS.moduleProjectsList}{$smarty.capture.layout_moduleProjectsList}{/if}
                      {foreach name = positions_first key = "key" item = "innerTable" from = $T_DEFAULT_POSITIONS.first}
                          {if !$T_INVALID_OPTIONS.$innerTable}
                              {assign var = "capture_name" value = "layout_"|cat:$innerTable}
                              {$smarty.capture.$capture_name}
                          {/if}
                      {/foreach}
                      </ul>
                  </td><td>
                      <ul class = "sortable" id = "layoutsecondlist">
                      {if !in_array('moduleNewsList', $T_DEFAULT_POSITIONS.first) && !in_array('moduleNewsList', $T_DEFAULT_POSITIONS.second) && !$T_INVALID_OPTIONS.moduleNewsList}{$smarty.capture.layout_moduleNewsList}{/if}
                      {if !in_array('modulePersonalMessagesList', $T_DEFAULT_POSITIONS.first) && !in_array('modulePersonalMessagesList', $T_DEFAULT_POSITIONS.second) && !$T_INVALID_OPTIONS.modulePersonalMessagesList}{$smarty.capture.layout_modulePersonalMessagesList}{/if}
                      {if !in_array('moduleForumList', $T_DEFAULT_POSITIONS.first) && !in_array('moduleForumList', $T_DEFAULT_POSITIONS.second) && !$T_INVALID_OPTIONS.moduleForumList}{$smarty.capture.layout_moduleForumList}{/if}
       {if !in_array('moduleComments', $T_DEFAULT_POSITIONS.first) && !in_array('moduleComments', $T_DEFAULT_POSITIONS.second) && !$T_INVALID_OPTIONS.moduleComments}{$smarty.capture.layout_moduleComments}{/if}
       {if !in_array('moduleCalendar', $T_DEFAULT_POSITIONS.first) && !in_array('moduleCalendar', $T_DEFAULT_POSITIONS.second) && !$T_INVALID_OPTIONS.moduleCalendar}{$smarty.capture.layout_moduleCalendar}{/if}
                      {if !in_array('moduleDigitalLibrary', $T_DEFAULT_POSITIONS.first) && !in_array('moduleDigitalLibrary', $T_DEFAULT_POSITIONS.second) && !$T_INVALID_OPTIONS.moduleDigitalLibrary}{$smarty.capture.layout_moduleDigitalLibrary}{/if}
                      {if !in_array('moduleTimeline', $T_DEFAULT_POSITIONS.first) && !in_array('moduleTimeline', $T_DEFAULT_POSITIONS.second) && !$T_INVALID_OPTIONS.moduleTimeline}{$smarty.capture.layout_moduleTimeline}{/if}
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
                 </td></tr>
                 <tr><td colspan = "2" id = "submitLayout"><input type = "button" value = "{$smarty.const._SAVECHANGES}" onclick = "updatePositions(this, '{$T_LESSON_ID}')" class = "flatButton"/></td></tr>
             </table>
            <br/>
            </div>
        {/capture}
        {eF_template_printBlock title = "`$smarty.const._LAYOUTFORLESSON`<span class = 'innerTableName'>&nbsp;&quot;`$T_CURRENT_LESSON->lesson.name`&quot;</span>" data = $smarty.capture.t_layout_code image = '32x32/layout.png' main_options = $T_TABLE_OPTIONS help = 'Administration'}
    {else}
        {*moduleLessonSettings: Left options list in the Lesson settings page*}
        {eF_template_printBlock title = "`$smarty.const._OPTIONSFORLESSON`<span class = 'innerTableName'>&nbsp;&quot;`$T_CURRENT_LESSON->lesson.name`&quot;</span>" columns = 4 links = $T_LESSON_SETTINGS image='32x32/lessons.png' main_options = $T_TABLE_OPTIONS groups = $T_LESSON_SETTINGS_GROUPS help = 'Administration'}
    {/if}
