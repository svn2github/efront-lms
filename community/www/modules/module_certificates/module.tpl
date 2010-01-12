{if isset($T_CERTIFICATES_PROFESSOR)}
    {if $T_MODOP == 'format_certificate'}
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
                                    {$smarty.const._CERTIFICATES_CERTIFICATEINSTRUCTIONS}
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
                    {eF_template_printBlock title = $smarty.const._FORMATCERTIFICATE data = $smarty.capture.t_certificate_code image = $T_CERTIFICATES_MODULE_BASELINK|cat:'images/certificate32.png' absoluteImagePath=1  main_options = $T_TABLE_OPTIONS}
    {else}
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
            {eF_template_printBlock title = "`$T_USER_PROGRESS.name` `$T_USER_PROGRESS.surname`&#039s `$smarty.const._PROGRESS`" data = $smarty.capture.t_course_user_progress image = $T_CERTIFICATES_MODULE_BASELINK|cat:'images/users.png' absoluteImagePath=1}
        {else}
            {capture name = 't_lesson_certificates_code'}
                                <table>
                                    <tr><td style = "padding-right:5px">
                                            <img src = {$T_CERTIFICATES_MODULE_BASELINK|cat:"images/certificate_preferences16.png"} title = "{$smarty.const._FORMATCERTIFICATE}" alt = "{$smarty.const._FORMATCERTIFICATE}" border = "0" style = "vertical-align:middle"/>
                                            <a href = "{$smarty.server.PHP_SELF}?ctg=module&op=module_certificates&modop=format_certificate" >
                                                {$smarty.const._FORMATCERTIFICATE}
                                            </a>
											{if $T_SHOW_AUTO == 1}
											<img src = {$T_CERTIFICATES_MODULE_BASELINK|cat:"images/certificate_refresh.png"} title = "{$smarty.const._AUTOCERTIFICATES}" alt = "{$smarty.const._AUTOCERTIFICATES}" border = "0" style = "vertical-align:middle"/>
                                                <a href = "{$smarty.server.PHP_SELF}?ctg=module&op=module_certificates&modop=auto_certificate">
                                                    {$smarty.const._AUTOMATICCERTIFICATES}: {if $T_CERTIFICATE_DATA.0.auto_certificate == 1}{$smarty.const._YES}{else}{$smarty.const._NO}{/if}
                                            </a>
											{/if}
                                        </td>
                                    </tr>
                                </table>
<!--ajax:usersTable-->
                                <table style = "width:100%" class = "sortedTable" size = "{$T_USERS_SIZE}" sortBy = "0" id = "usersTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "professor.php?ctg=module&op=module_certificates&">
                                    <tr class = "topTitle">
                                        <td class = "topTitle" name = "login">{$smarty.const._USER}</td>
                                        <td class = "topTitle centerAlign" name = "conditions_passed" >{$smarty.const._CONDITIONSCOMPLETED}</td>
                                        <td class = "topTitle centerAlign" name = "completed" >{$smarty.const._LESSONSTATUS}</td>
                                        <td class = "topTitle centerAlign" name = "score" >{$smarty.const._LESSONSCORE}</td>
                                        <td class = "topTitle centerAlign" name = "issued_certificate">{$smarty.const._CERTIFICATEISSUED}</td>
                                        <td class = "topTitle centerAlign noSort">{$smarty.const._FUNCTIONS}</td>
                                    </tr>
                        {foreach name = 'users_progress_list' item = 'item' key = 'login' from = $T_USERS_PROGRESS}
                                    <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$item.active}deactivatedTableElement{/if}">
                                        <td>{$item.login} ({$item.surname} {$item.name|eF_truncate:1:""}.)</td>
                                        <td style = "text-align:center">
                                            {$item.conditions_passed}/{$item.total_conditions}
                                        </td>
                                        <td style = "text-align:center">
                                            {if $item.completed}
                                                <img src = {$T_CERTIFICATES_MODULE_BASELINK|cat:"images/success.png"} title = "{$smarty.const._COMPLETED}" alt = "{$smarty.const._COMPLETED}" />
                                            {elseif $item.lesson_passed}
                                                <img src = {$T_CERTIFICATES_MODULE_BASELINK|cat:"images/contract.png"} title = "{$smarty.const._CONDITIONSMET}" alt = "{$smarty.const._CONDITIONSMET}" />
                                            {else}
                                                <img src = {$T_CERTIFICATES_MODULE_BASELINK|cat:"images/error_delete.png"} title = "{$smarty.const._NOTCOMPLETED}" alt = "{$smarty.const._NOTCOMPLETED}" />
                                            {/if}
                                        </td>
                                        <td style = "text-align:center">{if $item.score}#filter:score-{$item.score}#%{/if}</td>
                                        <td style = "text-align:center">{if $item.issued_certificate}{$smarty.const._YES}{else}{$smarty.const._NO}{/if}</td>
                                        <td style = "text-align:center">{strip}
                                            {if $item.completed && $item.issued_certificate}
                                                {* Create a write evaluation link for this employee *}
                                                <a href = "{$smarty.server.PHP_SELF}?ctg=module&op=module_certificates&revoke_certificate={$item.login}" title = "{$smarty.const._REVOKECERTIFICATE}">
                                                    <img src = {$T_CERTIFICATES_MODULE_BASELINK|cat:"images/certificate_broken.png"} border = "0" title = "{$smarty.const._REVOKECERTIFICATE}" alt = "{$smarty.const._REVOKECERTIFICATE}" border = "0"/>
                                                </a>&nbsp;
                                                <a href = "{$smarty.server.PHP_SELF}?ctg=module&op=module_certificates&export=rtf&user={$item.login}" target="_blank" title = "{$smarty.const._VIEWCERTIFICATE}">
                                                    <img src = {$T_CERTIFICATES_MODULE_BASELINK|cat:"images/certificate_view.png"} border = "0" title = "{$smarty.const._VIEWCERTIFICATE}" alt = "{$smarty.const._VIEWCERTIFICATE}" border = "0"/>
                                                </a>&nbsp;
                                            {elseif $item.completed}
                                                <a href = "{$smarty.server.PHP_SELF}?ctg=module&op=module_certificates&issue_certificate={$item.login}" title = "{$smarty.const._ISSUECERTIFICATE}">
                                                    <img src = {$T_CERTIFICATES_MODULE_BASELINK|cat:"images/certificate16.png"} border = "0" title = "{$smarty.const._ISSUECERTIFICATE}" alt = "{$smarty.const._ISSUECERTIFICATE}" border = "0"/>
                                                </a>&nbsp;
                                            {else}
                                                <a href = "javascript:void(0)">
                                                    <img src = {$T_CERTIFICATES_MODULE_BASELINK|cat:"images/certificate16.png"} class = "inactiveImage" border = "0" title = "{$smarty.const._THEUSERHASNOTCOMPLETEDTHELESSON}" alt = "{$smarty.const._THEUSERHASNOTCOMPLETEDTHELESSON}" />&nbsp;
                                                </a>
                                            {/if}
                                        {/strip}
                                        </td>
                                    </tr>
                        {foreachelse}
                                <tr class = "{cycle values = "oddRowColor, evenRowColor"} defaultRowHeight"><td colspan = "100%" class = "emptyCategory">{$smarty.const._NOUSERDATAFOUND}</td></tr>
                        {/foreach}
                            </table>
<!--/ajax:usersTable-->
            {/capture}
            {eF_template_printBlock title = "&quot;`$T_CERTIFICATES_CURRENTLESSON->lesson.name`&quot; `$smarty.const._CERTIFICATES`" data = $smarty.capture.t_lesson_certificates_code absoluteImagePath=1 image = $T_CERTIFICATES_MODULE_BASELINK|cat:'images/certificate32.png'  main_options = $T_TABLE_OPTIONS}
        {/if}
    {/if}
{else}
    {capture name = 't_userlesson_certificate'}
        <table>
            <tr><td style = "padding-right:5px">
                {if isset($T_USERLESSON_CERTIFICATE_EXISTS)}
                    <img src = {$T_CERTIFICATES_MODULE_BASELINK|cat:"images/certificate_view.png"} title = "{$smarty.const._VIEWCERTIFICATE}" alt = "{$smarty.const._VIEWCERTIFICATE}" border = "0" style = "vertical-align:middle"/>
                    <a href = "{$smarty.server.PHP_SELF}?ctg=module&op=module_certificates&export=rtf&user={$T_CERTIFICATES_USERLOGIN}" >
                        {$smarty.const._VIEWCERTIFICATE}
                    </a>
                {else}
                    <i>{$smarty.const._CERTIFICATES_NOISSUEDCERTIFICATEEXISTS}</i>
                {/if}
                </td>
            </tr>
        </table>
    {/capture}
            {eF_template_printBlock title = "&quot;`$T_CERTIFICATES_CURRENTLESSON->lesson.name`&quot; `$smarty.const._CERTIFICATES`" data = $smarty.capture.t_userlesson_certificate absoluteImagePath=1 image = $T_CERTIFICATES_MODULE_BASELINK|cat:'images/certificate32.png'  main_options = $T_TABLE_OPTIONS}
{/if}