{* Smarty template for includes/personal.php *}
<script>{if $T_BROWSER == 'IE6'}{assign var='globalImageExtension' value='gif'}var globalImageExtension = 'gif';{else}{assign var='globalImageExtension' value='png'}var globalImageExtension = 'png';{/if}</script>
<script>

 var areYouSureYouWantToCancelConst ='{$smarty.const._AREYOUSUREYOUWANTTOCANCELJOB}';
 var sessionType ='{$smarty.session.s_type}';
 var editUserLogin ='{$smarty.get.edit_user}';
 var jobAlreadyAssignedConst ='{$smarty.const._JOBALREADYASSIGNED}';
 var noPlacementsAssigned ='{$smarty.const._NOPLACEMENTSASSIGNEDYET}';
 var onlyImageFilesAreValid ='{$smarty.const._ONLYIMAGEFILESAREVALID}';
 var areYouSureYouWantToDeleteHist ='{$smarty.const._AREYOUSUREYOUWANTTODELETETHEHISOTYRECORD}';
 var userHasLesson ='{$smarty.const._USERHASTHELESSON}';
 var serverName ='{$smarty.const.G_SERVERNAME}';

 var msieBrowser ='{$smarty.const.MSIE_BROWSER}';
 var sessionLogin ='{$smarty.session.s_login}';
 var clickToChangeStatus ='{$smarty.const._CLICKTOCHANGESTATUS}';
 var youHaventSetAdditionalAccounts ='{$smarty.const._MAPPEDACCOUNTSUCCESSFULLYDELETED}';
 var openFacebookSession ='{$T_OPEN_FACEBOOK_SESSION}';
 var currentOperation ='{$T_OP}';


var jobsRows = new Array();
var branchesValues = new Array();
var jobValues = new Array();
var branchPositionValues = new Array();

var tabberLoadingConst = "{$smarty.const._LOADINGDATA}";
var enableMyJobSelect = false;
</script>


    {*This is the form that contains the user personal data*}
    {capture name = 't_personal_data_code'}
        {$T_PERSONAL_DATA_FORM.javascript}
        <form {$T_PERSONAL_DATA_FORM.attributes}>
            {$T_PERSONAL_DATA_FORM.hidden}
            <table class = "formElements" width="90%">

            {* enterprise edition: Insert a second column - new table *}





                {if (isset($smarty.get.add_user))}

                    <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.new_login.label}:&nbsp;</td>
                        <td style="white-space:nowrap;">{$T_PERSONAL_DATA_FORM.new_login.html}</td></tr>
                     <tr><td></td><td class = "infoCell">{$smarty.const._ONLYALLOWEDCHARACTERSLOGIN}</td></tr>
                    {if $T_PERSONAL_DATA_FORM.new_login.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.new_login.error}</td></tr>{/if}
                    <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.password_.label}:&nbsp;</td>
                        <td style="white-space:nowrap;">{$T_PERSONAL_DATA_FORM.password_.html}</td></tr>
                    <tr><td></td><td class = "infoCell">{$smarty.const._PASSWORDMUSTBE6CHARACTERS|replace:"%x":$T_CONFIGURATION.password_length}</td></tr>
                    {if $T_PERSONAL_DATA_FORM.password_.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.password_.error}</td></tr>{/if}

                    <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.passrepeat.label}:&nbsp;</td>
                        <td style="white-space:nowrap;">{$T_PERSONAL_DATA_FORM.passrepeat.html}</td></tr>
                    {if $T_PERSONAL_DATA_FORM.passrepeat.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.passrepeat.error}</td></tr>{/if}
                {else}
                    {if !$T_LDAP_USER}
                        <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.password_.label}:&nbsp;</td>
                            <td style="white-space:nowrap;">{$T_PERSONAL_DATA_FORM.password_.html}</td></tr>
                        <tr><td></td><td class = "infoCell">{$smarty.const._PASSWORDMUSTBE6CHARACTERS|replace:"%x":$T_CONFIGURATION.password_length}</td></tr>
                        {if $T_PERSONAL_DATA_FORM.password_.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.password_.error}</td></tr>{/if}

                        <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.passrepeat.label}:&nbsp;</td>
                            <td style="white-space:nowrap;">{$T_PERSONAL_DATA_FORM.passrepeat.html}</td></tr>
                        {if $T_PERSONAL_DATA_FORM.passrepeat.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.passrepeat.error}</td></tr>{/if}
                    {else}
                        <tr><td class = "labelCell">{$smarty.const._PASSWORD}:&nbsp;</td>
                            <td style="white-space:nowrap;">{$smarty.const._LDAPUSER}</td></tr>
                    {/if}
                {/if}
                <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.name.label}:&nbsp;</td>
                    <td style="white-space:nowrap;">{$T_PERSONAL_DATA_FORM.name.html}</td></tr>
                {if $T_PERSONAL_DATA_FORM.name.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.name.error}</td></tr>{/if}

                <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.surname.label}:&nbsp;</td>
                    <td style="white-space:nowrap;">{$T_PERSONAL_DATA_FORM.surname.html}</td></tr>
                {if $T_PERSONAL_DATA_FORM.surname.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.surname.error}</td></tr>{/if}
                <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.email.label}:&nbsp;</td>
                    <td>{$T_PERSONAL_DATA_FORM.email.html}</td></tr>
                {if $T_PERSONAL_DATA_FORM.email.error && $smarty.const.G_VERSIONTYPE != 'enterprise'}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.email.error}</td></tr>{/if}
                {if ($smarty.session.s_type == "administrator" || ($smarty.const.G_VERSIONTYPE == 'enterprise' && $T_CTG != "personal"))}
                        <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.group.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.group.html}</td></tr>
                    {* if $T_CURRENTUSERROLEID == 0*} <!-- Removed in order to allowed to subadmins to change user type -->
                        <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.user_type.label}:&nbsp;</td>
                        <td>{$T_PERSONAL_DATA_FORM.user_type.html}</td></tr>
                        {if $T_PERSONAL_DATA_FORM.user_type.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.user_type.error}</td></tr>{/if}
                    {*/if*}
                {/if}
                {if $T_PERSONAL_DATA_FORM.languages_NAME.label != ""}
                    <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.languages_NAME.label}:&nbsp;</td>
                        <td>{$T_PERSONAL_DATA_FORM.languages_NAME.html}</td></tr>
                        {if $T_PERSONAL_DATA_FORM.languages_NAME.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.languages_NAME.error}</td></tr>{/if}
                {/if}
       <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.timezone.label}:&nbsp;</td>
                               <td>{$T_PERSONAL_DATA_FORM.timezone.html}</td></tr>
    {if ($smarty.session.s_type == "administrator" || ($smarty.const.G_VERSIONTYPE == 'enterprise' && $T_CTG != "personal"))}
                    <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.active.label}:&nbsp;</td>
                        <td>{$T_PERSONAL_DATA_FORM.active.html}</td></tr>
                        {if $T_PERSONAL_DATA_FORM.active.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.active.error}</td></tr>{/if}
                {/if}
                {foreach name = 'profile_fields' key = key item = item from = $T_USER_PROFILE_FIELDS }
                    <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.$item.label}:&nbsp;</td>
                        <td class = "elementCell">{$T_PERSONAL_DATA_FORM.$item.html}</td></tr>
                    {if $T_PERSONAL_DATA_FORM.$item.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.$item.error}</td></tr>{/if}
                {/foreach}
                {if (!isset($smarty.get.add_user))}
                <tr><td class = "labelCell">{$smarty.const._REGISTRATIONDATE}:&nbsp;</td>
                    <td>#filter:timestamp-{$T_REGISTRATION_DATE}#</td></tr>
               {/if}
                {* enterprise version: If no module then submit button here, else insert the second column of data and submit will be inserted later elsewhere *}
                    <tr><td></td><td class = "submitCell" style = "text-align:left">
                             {$T_PERSONAL_DATA_FORM.submit_personal_details.html}</td></tr>
            </table>
        </form>
{*avatar code*}
        {if (!isset($smarty.get.add_user)) && isset($T_PERSONAL_CTG) && !isset($T_SOCIAL_INTERFACE)}
        <fieldset class = "fieldsetSeparator">
        <legend>{$smarty.const._SETAVATAR}</legend>
        {$T_AVATAR_FORM.javascript}
        <form {$T_AVATAR_FORM.attributes}>
            {$T_AVATAR_FORM.hidden}
            <table class = "formElements">
                <tr><td class = "labelCell">{$smarty.const._CURRENTAVATAR}:&nbsp;</td>
                    <td class = "elementCell"><img src = "view_file.php?file={$T_AVATAR}" title="{$smarty.const._CURRENTAVATAR}" alt="{$smarty.const._CURRENTAVATAR}"
                    {if isset($T_NEWWIDTH)} width = "{$T_NEWWIDTH}" height = "{$T_NEWHEIGHT}"{/if}
                    /></td></tr>
            {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
                <tr><td class = "labelCell">{$T_AVATAR_FORM.delete_avatar.label}:&nbsp;</td>
                    <td class = "elementCell">{$T_AVATAR_FORM.delete_avatar.html}</td></tr>
                <tr><td class = "labelCell">{$T_AVATAR_FORM.file_upload.label}:&nbsp;</td>
                    <td class = "elementCell">{$T_AVATAR_FORM.file_upload.html}</td></tr>
                <tr><td class = "labelCell">{$T_AVATAR_FORM.system_avatar.label}:&nbsp;</td>
                    <td class = "elementCell">{$T_AVATAR_FORM.system_avatar.html}&nbsp;(<a href = "{$smarty.server.PHP_SELF}?{if $smarty.get.edit_user}ctg=users&edit_user={$smarty.get.edit_user}{else}ctg=personal{/if}&show_avatars_list=1&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._VIEWLIST}', 2)">{$smarty.const._VIEWLIST}</a>)</td></tr>
                <tr><td colspan = "2">&nbsp;</td></tr>
                <tr><td></td>
                    <td class = "elementCell">{$T_AVATAR_FORM.submit_upload_file.html}</td></tr>
            {/if}
            </table>
        </form>
        </fieldset>
        {/if}
    {/capture}
    {if (!isset($smarty.get.add_user)) && (isset($T_PERSONAL_CTG) || ($smarty.session.s_type == "administrator" || $smarty.session.employee_type == $smarty.const._SUPERVISOR) ) && isset($T_SOCIAL_INTERFACE)}
    {capture name = 't_user_profile_code'}
        {$T_AVATAR_FORM.javascript}
        <form {$T_AVATAR_FORM.attributes}>
            {$T_AVATAR_FORM.hidden}
            <table class = "formElements">
                {if ($smarty.get.personal) || ($smarty.get.edit_user == $smarty.session.s_login)}
                {/if}
                <tr><td class = "labelCell">{$T_AVATAR_FORM.short_description.label}:&nbsp;</td>
                    <td class = "elementCell">{$T_AVATAR_FORM.short_description.html}</td></tr>
                <tr><td colspan = "2">&nbsp;</td></tr>
                <tr><td class = "labelCell">{$smarty.const._CURRENTAVATAR}:&nbsp;</td>
                    <td class = "elementCell"><img src = "view_file.php?file={$T_AVATAR}" title="{$smarty.const._CURRENTAVATAR}" alt="{$smarty.const._CURRENTAVATAR}"
                    {if isset($T_NEWWIDTH)} width = "{$T_NEWWIDTH}" height = "{$T_NEWHEIGHT}"{/if}
                    /></td></tr>
            {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
                <tr><td class = "labelCell">{$T_AVATAR_FORM.delete_avatar.label}:&nbsp;</td>
                    <td class = "elementCell">{$T_AVATAR_FORM.delete_avatar.html}</td></tr>
                <tr><td class = "labelCell">{$T_AVATAR_FORM.file_upload.label}:&nbsp;</td>
                    <td class = "elementCell">{$T_AVATAR_FORM.file_upload.html}</td></tr>
                <tr><td class = "labelCell">{$T_AVATAR_FORM.system_avatar.label}:&nbsp;</td>
                    <td class = "elementCell">{$T_AVATAR_FORM.system_avatar.html}&nbsp;(<a href = "{$smarty.server.PHP_SELF}?{if $_admin_}ctg=users&edit_user={$smarty.get.edit_user}{else}ctg=personal{/if}&show_avatars_list=1&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._VIEWLIST}', 2)">{$smarty.const._VIEWLIST}</a>)</td></tr>
                <tr><td colspan = "2">&nbsp;</td></tr>
                <tr><td></td>
                    <td class = "elementCell">{$T_AVATAR_FORM.submit_upload_file.html}</td></tr>
            {/if}
            </table>
        </form>
    {/capture}
    {/if}
    {if isset($T_USER_TO_GROUP_FORM)}
        {capture name = 't_users_to_groups_code'}
                <table border = "0" width = "100%" id = "groupsTable" class = "sortedTable" sortBy = "0">
                    <tr class = "topTitle">
                        <td class = "topTitle" width="30%">{$smarty.const._NAME}</td>
                        <td class = "topTitle" width="50%">{$smarty.const._DESCRIPTION}</td>
                        <td class = "topTitle centerAlign" width="20%">{$smarty.const._CHECK}</td>
                    </tr>
                {foreach name = 'users_to_groups_list' key = 'key' item = 'group' from = $T_USER_TO_GROUP_FORM}
                    {strip}
                    <tr class = "{cycle values = "oddRowColor, evenRowColor"} {if !$group.active}deactivatedTableElement{/if}">
                        <td width="30%">{$group.name}</td>
                        <td width="50%">{$group.description}</td>
                        <td align = "center" width="20%">
                        {if ($smarty.get.ctg == "personal" && $smarty.session.s_type != 'administrator') || (isset($T_CURRENT_USER->coreAccess.users) && $T_CURRENT_USER->coreAccess.users != 'change')}
                            {if $group.partof == 1}
                                <img src = "images/16x16/success.png" alt = "{$smarty.const._PARTOFTHISGROUP}" title = "{$smarty.const._PARTOFTHISGROUP}" />
                            {/if}
                        {else}
                            {if $group.partof == 1}
                                <input class = "inputCheckBox" type = "checkbox" id = "group_{$group.id}" name = "{$group.id}" onclick ="ajaxUserPost('group', '{$group.id}', this);" checked>
                            {else}
                                <input class = "inputCheckBox" type = "checkbox" id = "group_{$group.id}" name = "{$group.id}" onclick ="ajaxUserPost('group', '{$group.id}', this);">
                            {/if}
                        {/if}
                        </td>
                    </tr>
                    {/strip}
                {/foreach}
                </table>
        {/capture}
    {else}
        {capture name = 't_users_to_groups_code'}
            <table width = "100%">
                <tr><td class = "emptyCategory">{$smarty.const._NOGROUPSAREDEFINED}</td></tr>
            </table>
        {/capture}
    {/if}
        {capture name = 't_users_to_lessons_code'}
<!--ajax:lessonsTable-->
                                            <table style = "width:100%" size = "{$T_LESSONS_SIZE}" id = "lessonsTable" class = "sortedTable" useAjax = "1" url = "{$smarty.server.PHP_SELF}?{if ($smarty.session.s_type == "administrator") || ($smarty.const.G_VERSIONTYPE == 'enterprise' && $smarty.session.employee_type == "Supervisor")}ctg=users&edit_user={$smarty.get.edit_user}{else}ctg=personal{/if}&lessons=1&">
                                                <tr class = "topTitle">
                                                    <td name = "name" class = "topTitle">{$smarty.const._NAME}</td>
                                                    <td name = "directions_ID">{$smarty.const._PARENTDIRECTIONS}</td>
                            {if $smarty.session.s_type == "administrator"}
                                                    <td name = "user_type" class = "topTitle">{$smarty.const._USERTYPE}</td>
                                                    <td name = "active" class = "topTitle centerAlign">{$smarty.const._STATUS}</td>
                            {/if}
                                                    <td name = "price" class = "topTitle centerAlign">{$smarty.const._PRICE}</td>
                                                    <td name = "completed" class = "topTitle centerAlign">{$smarty.const._COMPLETED}</td>
                                                    <td name = "score" class = "topTitle centerAlign">{$smarty.const._SCORE}</td>
                            {if $smarty.session.s_type == "administrator" && (!isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change')}
                                                    <td name = "partof" class = "topTitle centerAlign">{$smarty.const._CHECK}</td>
                            {/if}
                                                </tr>
                            {foreach name = 'users_to_lessons_list' key = 'key' item = 'lesson' from = $T_LESSONS_DATA}
                                                <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$lesson.active}deactivatedTableElement{/if}">
                                                    <td>{$lesson.name}</td>
                                                    <td>{$lesson.directions_name}</td>
                                {if $smarty.session.s_type == "administrator"}
                                                    <td>
                                    {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
                                                        <select name = "type_{$lesson.id}" id = "lesson_type_{$lesson.id}" onChange = "document.getElementById('lesson_{$lesson.id}').checked = true;ajaxUserPost('lesson', '{$lesson.id}', this);">
                                        {foreach name = 'roles_list' key = 'role_key' item = 'role_item' from = $T_ROLES_ARRAY}
                                                            <option value = "{$role_key}" {if ($lesson.user_type == $role_key)}selected{/if}>{$role_item}</option>
                                        {/foreach}
                                        {assign var = "selected" value = ""}
                                                        </select>
                                    {else}
                                                        {$T_ROLES_ARRAY[$lesson.user_type]}
                                    {/if}
                                                    </td>
                                                    <td class = "centerAlign">
                                    {if $lesson.from_timestamp == 0 && $lesson.partof}
                                                        <img class = "ajaxHandle" src = "images/16x16/warning.png" title = "{$smarty.const._APPLICATIONPENDING}" alt = "{$smarty.const._APPLICATIONPENDING}" onclick = "confirmUser(this, {$lesson.id}, 'lesson')"/>
                                    {elseif $lesson.partof}
                                                        <img src = "images/16x16/success.png" title = "{$smarty.const._USERHASTHELESSON}" alt = "{$smarty.const._USERHASTHELESSON}"/>
                                    {/if}
                                {/if}
                                                    </td>
                                                    <td class = "centerAlign">{$lesson.price_string}</td>
                                                    <td class = "centerAlign">{if $lesson.partof && $T_BASIC_ROLES_ARRAY[$lesson.user_type] == 'student'}{if $lesson.completed}<img src = "images/16x16/success.png" alt = "{$smarty.const._YES}" title = "{$smarty.const._YES}">{else}<img src = "images/16x16/forbidden.png" alt = "{$smarty.const._NO}" title = "{$smarty.const._NO}">{/if}{/if}</td>
                                                    <td class = "centerAlign">{if $lesson.partof && $T_BASIC_ROLES_ARRAY[$lesson.user_type] == 'student'}#filter:score-{$lesson.score}#%{/if}</td>
                                {if $smarty.session.s_type == "administrator" && (!isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change')}
                                                    <td class = "centerAlign">
                                                        <input class = "inputCheckBox" type = "checkbox" id = "lesson_{$lesson.id}" name = "lesson_{$lesson.id}" onclick ="ajaxUserPost('lesson', '{$lesson.id}', this);" {if $lesson.partof == 1}checked{/if}>
                                                    </td>
                                {/if}
                                                </tr>
                            {foreachelse}
                                                <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "6">{$smarty.const._NODATAFOUND}</td></tr>
                            {/foreach}
                                            </table>
<!--/ajax:lessonsTable-->
        {/capture}
        {capture name = 't_users_to_courses_code'}
<!--ajax:coursesTable-->
                                                <table style = "width:100%" size = "{$T_COURSES_SIZE}" id = "coursesTable" class = "sortedTable" useAjax = "1" url = "{$smarty.server.PHP_SELF}?{if $smarty.session.s_type == "administrator" || ($smarty.const.G_VERSIONTYPE == 'enterprise' && $smarty.session.employee_type == "Supervisor")}ctg=users&edit_user={$smarty.get.edit_user}{else}ctg=personal{/if}&courses=1&">
                                                    <tr class = "topTitle">
                                                        <td name = "name" class = "topTitle">{$smarty.const._NAME}</td>
                                                        <td name = "directions_ID">{$smarty.const._PARENTDIRECTIONS}</td>
                                                    {if $smarty.get.ctg == "users"}
                                                        <td name = "user_type" class = "topTitle">{$smarty.const._USERTYPE}</td>
                                                    {/if}
                                                    {if $smarty.session.s_type == "administrator"}
                                                        <td name = "active" class = "topTitle centerAlign">{$smarty.const._STATUS}</td>
                                                    {/if}
                                                        <td name = "completed" class = "topTitle centerAlign">{$smarty.const._COMPLETED}</td>
                                                        <td name = "score" class = "topTitle centerAlign">{$smarty.const._SCORE}</td>
                                                    {if $smarty.get.ctg == "users"}
                                                        <td name = "partof" class = "topTitle centerAlign">{$smarty.const._CHECK}</td>
                                                    {else}
                                                        <td name = "price" class = "topTitle centerAlign">{$smarty.const._PRICE}</td>
                                                    {/if}
                                                    </tr>
                            {foreach name = 'users_to_courses_list' key = 'key' item = 'course' from = $T_COURSES_DATA}
                                                    <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$course.active}deactivatedTableElement{/if}">
                                                        <td>{$course.name}</td>
                                                        <td>{$course.directions_name}</td>
                                                        {if $smarty.get.ctg == "users"}
                                                         <td>
                                                      {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
                                                              <select name = "course_type_{$course.id}" id = "course_type_{$course.id}" onChange = "document.getElementById('course_{$course.id}').checked = true;ajaxUserPost('course', '{$course.id}', this);">
                                                          {foreach name = 'roles_list' key = 'role_key' item = 'role_item' from = $T_ROLES_ARRAY}
                                                                  <option value = "{$role_key}" {if ($course.user_type == $role_key)}selected{/if}>{$role_item}</option>
                                                          {/foreach}
                                                              </select>
                                                      {else}
                                                          {$T_ROLES_ARRAY[$course.user_type]}
                                                      {/if}
                                                         </td>
                                                        {/if}
                                                        {if $smarty.session.s_type == "administrator"}
                                                        <td class = "centerAlign">
                                    {if $course.from_timestamp == 0 && $course.partof}
                                                            <img class = "ajaxHandle" src = "images/16x16/warning.png" title = "{$smarty.const._APPLICATIONPENDING}" alt = "{$smarty.const._APPLICATIONPENDING}" onclick = "confirmUser(this, {$course.id}, 'course')"/>
                                    {elseif $course.partof}
                                                            <img src = "images/16x16/success.png" title = "{$smarty.const._USERHASTHECOURSE}" alt = "{$smarty.const._USERHASTHECOURSE}" style = "vertical-align:middle"/>
                                    {/if}
                                                        </td>
                                                        {/if}
                                                        <td class = "centerAlign">{if $course.partof && $T_BASIC_ROLES_ARRAY[$course.user_type] == 'student'}{if $course.completed}<img src = "images/16x16/success.png" alt = "{$smarty.const._YES}" title = "{$smarty.const._YES}">{else}<img src = "images/16x16/forbidden.png" alt = "{$smarty.const._NO}" title = "{$smarty.const._NO}">{/if}{/if}</td>
                                                        <td class = "centerAlign">{if $course.partof && $T_BASIC_ROLES_ARRAY[$course.user_type] == 'student'}#filter:score-{$course.score}#%{/if}</td>
                                                {if $smarty.get.ctg == "users"}
                                                        <td class = "centerAlign">
                                                    {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
                                                            <input class = "inputCheckBox" type="checkbox" id="course_{$course.id}" name="{$course.id}" {if $course.partof == 1}checked{/if} onclick ="ajaxUserPost('course', '{$course.id}', this);">
                                                    {else}
                                                            {if $course.partof == 1}<img src = "images/16x16/success.png" alt = "{$smarty.const._COURSEUSER}" title = "{$smarty.const._COURSEUSER}">{/if}
                                                    {/if}
                                                        </td>
                                                {elseif $smarty.get.ctg == "personal"}
                                                            <td class = "centerAlign">{$course.price_string}</td>
                                                {/if}
                                                    </tr>
                            {foreachelse}
                                                    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "10">{$smarty.const._NODATAFOUND}</td></tr>
                            {/foreach}
                                                </table>
<!--/ajax:coursesTable-->
        {/capture}
             {capture name = 't_user_code'}
                    <div class="tabber" >
                        {if !isset($smarty.get.add_user) && (isset($T_PERSONAL_CTG))}
                        <div class="tabbertab">
                            <h3>{$smarty.const._DASHBOARD}</h3>
                            {include file = "social.tpl"}
                        </div>
                        {/if}
                        <div class="tabbertab">
       {if !isset($T_PERSONAL_CTG)}
        {assign var = 'titleTemp' value = "`$smarty.const._EDITUSER`"}
       {else}
        {assign var = 'titleTemp' value = "`$smarty.const._MYSETTINGS`"}
       {/if}
                            <h3>{$titleTemp}</h3>
                            {eF_template_printBlock title = "`$titleTemp`" data = $smarty.capture.t_personal_data_code image = '32x32/profile.png'}
                        </div>
                      {if (!isset($smarty.get.add_user)) && (isset($T_PERSONAL_CTG) || ($smarty.session.s_type == "administrator" || $smarty.session.employee_type == $smarty.const._SUPERVISOR) ) && $T_SOCIAL_INTERFACE == 1}
                       <div class="tabbertab {if ($smarty.get.tab == "my_profile")} tabbertabdefault {/if}" >
      {if !isset($T_PERSONAL_CTG)}
       {assign var = 'tempTitle' value = $smarty.const._USERPROFILE}
      {else}
       {assign var = 'tempTitle' value = $smarty.const._MYPROFILE}
      {/if}
                            <h3>{$tempTitle}</h3>
                            {eF_template_printBlock title = "`$tempTitle`" data = $smarty.capture.t_user_profile_code image = '32x32/user_timeline.png'}
                       </div>
                      {/if}
                {if isset($T_ADDITIONAL_ACCOUNTS) && $T_CONFIGURATION.mapped_accounts == 0 || ($T_CONFIGURATION.mapped_accounts == 1 && $T_CURRENT_USER->user.user_type != 'student') || ($T_CONFIGURATION.mapped_accounts == 2 && $T_CURRENT_USER->user.user_type == 'administrator')}
               <div class="tabbertab">
                    <h3>{if !isset($T_PERSONAL_CTG)}{$smarty.const._ADDITIONALACCOUNTS}{else}{$smarty.const._MAPPEDACCOUNTS}{/if}</h3>
                    {capture name = "t_additional_accounts_code"}
                        <div class = "headerTools">
                            <span>
                                <img src = "images/16x16/add.png" alt = "{$smarty.const._ADDACCOUNT}" title = "{$smarty.const._ADDACCOUNT}">
                                <a href = "javascript:void(0)" onclick = "$('add_account').show();">{$smarty.const._ADDACCOUNT}</a>
                            </span>
                        </div>
                        <div id = "add_account" style = "display:none">
                            {$smarty.const._LOGIN}: <input type = "text" name = "account_login" id = "account_login">
                            {$smarty.const._PASSWORD}: <input type = "password" name = "account_password" id = "account_password">
                            <img class = "ajaxHandle" src = "images/16x16/success.png" alt = "{$smarty.const._ADD}" title = "{$smarty.const._ADD}" onclick = "addAccount(this)">
                            <img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._CANCEL}" title = "{$smarty.const._CANCEL}" onclick = "$('add_account').hide();">
                        </div>
                        <br/>
                        <fieldset class = "fieldsetSeparator">
                            <legend>{$smarty.const._ADDITIONALACCOUNTS}</legend>
                            <table id = "additional_accounts">
                            {foreach name = 'additional_accounts_list' item = "item" key = "key" from = $T_ADDITIONAL_ACCOUNTS}
                             <tr><td>{$item}</td>
                              <td><img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._DELETEACCOUNT}" title = "{$smarty.const._DELETEACCOUNT}" onclick = "deleteAccount(this, '{$item}')"></td>
                            {foreachelse}
                            <tr id = "empty_accounts"><td class = "emptyCategory">{$smarty.const._YOUHAVENTSETADDITIONALACCOUNTS}</td></tr>
                            {/foreach}
                            </table>
                        </fieldset>
                        {if $T_FACEBOOK_ENABLED}
                        <fieldset class = "fieldsetSeparator" id = "facebook_accounts">
                            <legend>{$smarty.const._FACEBOOKMAPPEDACCOUNT}</legend>
                            {if $T_FB_ACCOUNT}
                            <div>{$T_FB_ACCOUNT.fb_name} <img style = "vertical-align:middle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._DELETEACCOUNT}" title = "{$smarty.const._DELETEACCOUNT}" onclick = "deleteFacebookAccount(this, '{$T_FB_ACCOUNT.users_LOGIN}')"></div>
                            {else}
                            <div class = "emptyCategory" id = "empty_fb_accounts">{$smarty.const._YOUHAVENTSETFACEBOOKACCOUNT}</div>
                            {/if}
                        </fieldset>
                        {/if}
                        <script>
                        {if $smarty.get.ctg == 'personal'}var additionalAccountsUrl = '{$smarty.server.PHP_SELF}?ctg=personal';{else}var additionalAccountsUrl = '{$smarty.server.PHP_SELF}?ctg=users&edit_user={$smarty.get.edit_user}';{/if}
                        </script>
                    {/capture}
                    {eF_template_printBlock title = $smarty.const._ADDITIONALACCOUNTS data = $smarty.capture.t_additional_accounts_code image = '32x32/users.png'}
               </div>
                {/if}
                {* Checking here whether to show the user groups div - if so the lessons tab will be 7th else 6th *}
                {if isset($T_USER_TO_GROUP_FORM)}
     {if !isset($T_PERSONAL_CTG)}
      {assign var = 'titleTemp' value ="`$smarty.const._GROUPS`"}
     {else}
      {assign var = 'titleTemp' value ="`$smarty.const._MYGROUPS`"}
     {/if}
     {eF_template_printBlock tabber="groups" title = "`$titleTemp`" data = $smarty.capture.t_users_to_groups_code image = '32x32/users.png'}
                {/if}
                {if $smarty.get.edit_user}
                    {if $T_USER_TYPE != 'administrator'}
                        <div class="tabbertab {if ($smarty.get.tab == "attending" || isset($smarty.post.users_attending)) } tabbertabdefault {/if} {if $smarty.const.G_VERSIONTYPE == 'enterprise' && $T_USER_TYPE == 'administrator'}useAjax{/if}" {if $smarty.const.G_VERSIONTYPE == 'enterprise' && $T_USER_TYPE == 'administrator'}id="tabbertab{$T_TABBERAJAX.lessons}"{/if}>
<!--tabberajax:tabbertab{$T_TABBERAJAX.lessons}-->
                            <h3>{if !isset($T_PERSONAL_CTG)}{$smarty.const._ATTENDING}{else}{$smarty.const._MYLESSONS}{/if}</h3>
{if !($smarty.const.G_VERSIONTYPE == 'enterprise' && $T_USER_TYPE == 'administrator') || ($smarty.get.tabberajax == $T_TABBERAJAX.lessons || ($smarty.get.tab == "attending" || isset($smarty.post.users_attending)))}
                            <div class="tabber">
       {eF_template_printBlock tabber="lessons" title = "`$smarty.const._LESSONS`" data = $smarty.capture.t_users_to_lessons_code image = '32x32/lessons.png'}
       {eF_template_printBlock tabber="courses" title = "`$smarty.const._COURSES`" data = $smarty.capture.t_users_to_courses_code image = '32x32/courses.png'}
                            </div>
{/if}
<!--/tabberajax:tabbertab{$T_TABBERAJAX.lessons}-->
                        </div>
                    {/if}
                {/if}
                </div>
            {/capture}
  {if $smarty.get.show_avatars_list}
   <table width = "100%" cellpadding = "5" class = "filemanagerBlock">
       <tr>
   {foreach name = "avatars_list" item = "item" key = "key" from = $T_SYSTEM_AVATARS}
           <td align = "center">
               <a href = "javascript:void(0)" onclick = "parent.document.getElementById('select_avatar').selectedIndex = {$smarty.foreach.avatars_list.index}{if $T_SOCIAL_INTERFACE}+1{/if};parent.document.getElementById('popup_close').onclick();window.close();">
               <img src = "{$smarty.const.G_SYSTEMAVATARSURL}{$item}" border = "0" / >
               <br/>{$item}</a>
           </td>
       {if $smarty.foreach.avatars_list.iteration % 4 == 0}
           </tr><tr>
       {/if}
   {/foreach}
       </tr>
   </table>
  {else}
            <table border = "0" width = "100%" cellspacing = "5">
                <tr><td valign = "top">
                {** The following code show the name-editable header status for the social eFront personal page **}
                {if $T_SOCIAL_INTERFACE == 1 && isset($smarty.get.edit_user)}
                    <table class = "horizontalBlock">
                    <tr>
                        <td colspan = "2">
                            <table style = "width:100%">
                                <tr><td style = "vertical-align:middle;font-size:20px;font-weight:bold;padding:10px 0px 10px 0px;white-space:nowrap;">
                                        <span style = "vertical-align:middle;margin-left:5px" >
                                        {$T_SIMPLEUSERNAME}&nbsp;
                                        </span>
                                    </td>
                                    {* Show user status only if that module is active - only same user may change it *}
                                    {if !$T_HIDE_USER_STATUS}
                                        <td style = "vertical-align:middle;font-size:20px;font-weight:bold;padding:10px 0px 10px 0px;white-space:nowrap;">
                                        </td>
                                        <td style = "white-space:nowrap;width:100%;padding-left:10px">
                                    {/if}
                                </tr>
                            </table>
                        </td></tr>
                </table>
                <tr><td valign = "top">
                {/if}
             {assign var = 'newTitle' value = $smarty.const._PERSONALOPTIONSFOR}
       {if (isset($smarty.get.add_evaluation) || isset($smarty.get.edit_evaluation))}
                   {eF_template_printBlock title = $smarty.const._EVALUATIONOFEMPLOYEE|cat:'&nbsp;'|cat:$smarty.get.edit_user data = $smarty.capture.t_evaluations_code image = '32x32/catalog.png'}
             {else}
                 {if $smarty.get.edit_user != ""}
                     {if $smarty.get.print_preview == 1}
                         {eF_template_printBlock alt= $T_USERNAME title = $T_EMPLOYEE_FORM_CAPTION titleStyle = 'font-size:16px;font-weight:bold;' data = $smarty.capture.t_personal_form_data_code image = $T_SYSTEMLOGO options=$T_EMPLOYEE_FORM_OPTIONS}
                         {*eF_template_printBlock title = '<span style=\'font-size:16px;font-weight:bold;\'>'|cat:$smarty.const._EMPLOYEEFORM|cat:':&nbsp;'|cat:$T_USERNAME|cat:'</span>' data = $smarty.capture.t_personal_form_data_code image = $T_SYSTEMLOGO options=$T_EMPLOYEE_FORM_OPTIONS*}
                     {elseif $smarty.get.print == 1}
                         {eF_template_printBlock alt= $T_USERNAME title = $T_EMPLOYEE_FORM_CAPTION titleStyle = 'font-size:16px;font-weight:bold;' data = $smarty.capture.t_personal_form_data_code image = $T_SYSTEMLOGO options=$T_EMPLOYEE_FORM_OPTIONS}
                         {*eF_template_printBlock title = '<span style=\'font-size:16px;font-weight:bold;\'>'|cat:$smarty.const._EMPLOYEEFORM|cat:':&nbsp;'|cat:$T_USERNAME|cat:'</span>' data = $smarty.capture.t_personal_form_data_code image = $T_SYSTEMLOGO*}
                         {if $smarty.const.MSIE_BROWSER == 0}
                         <script>window.print();</script>
                         {/if}
                     {else}
                         {if $T_SOCIAL_INTERFACE == 1}
                             {$smarty.capture.t_user_code}
                         {else}
        {eF_template_printBlock title = "`$newTitle` <span class = 'innerTableName'>&quot;`$smarty.get.edit_user`&quot;</span>" data = $smarty.capture.t_user_code image = '32x32/user.png'}
                         {/if}
                     {/if}
                 {elseif ($smarty.get.ctg == "personal")}
                     {if $T_SOCIAL_INTERFACE == 1}
                         {$smarty.capture.t_user_code}
                     {else}
                         {eF_template_printBlock title = "`$newTitle` <span class = 'innerTableName'>&quot;#filter:login-`$smarty.session.s_login`#&quot;</span>" data = $smarty.capture.t_user_code image = '32x32/user.png'}
                     {/if}
                 {else}
                    {eF_template_printBlock title = $smarty.const._NEWUSER data = $smarty.capture.t_user_code image = '32x32/user.png'}
                 {/if}
       {/if}
             </td></tr>
        </table>
       <div style = "width:100%"></div>
       {/if}
