

        {*moduleScormOptions: SCORM options page*}
        {capture name = "moduleScormOptions"}
                                <tr><td class = "moduleCell">
                        {if $smarty.get.scorm_review}
                            {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink'  href = '`$smarty.server.PHP_SELF`?ctg=scorm&scorm_review=1'>`$smarty.const._SCORMREVIEW`</a>"}
                            {capture name = 'scorm_review_code'}
<!--ajax:scormUsersTable-->
                                            <table style = "width:100%" class = "sortedTable" size = "{$T_USERS_SIZE}" sortBy = "0" id = "scormUsersTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "professor.php?ctg=scorm&scorm_review=1&">
                                                <tr class = "defaultRowHeight">
                                                    <td class = "topTitle" name = "users_LOGIN">{$smarty.const._USERCAPITAL}</td>
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
                                                <td>#filter:login-{$item.users_LOGIN}#</td>
                                                <td>{$item.content_name|eF_truncate:30}</td>
                                                <td style = "white-space:nowrap">#filter:timestamp_time-{$item.timestamp}#</td>
                                                <td>{$item.entry}</td>
                                                <td>{$item.lesson_status}</td>
                                                <td class = "centerAlign">{$item.total_time}</td>
                                                <td class = "centerAlign">{if isset($item.minscore)} #filter:score-{$item.minscore}#%{/if}</td>
                                                <td class = "centerAlign">#filter:score-{$item.maxscore}#%</td>
                                                <td class = "centerAlign">{if $item.masteryscore} #filter:score-{$item.masteryscore}#%{/if}</td>
                                                <td class = "centerAlign">{$item.score|formatScore}</td>
                                            {if !isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change'}
                                                <td class = "centerAlign"><img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._DELETEDATA}" title = "{$smarty.const._DELETEDATA}" onclick = "deleteData(this, {$item.id})"></td>
                                            {/if}
                                            </tr>
                                        {foreachelse}
                                            <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
                                        {/foreach}
                                            </table>
<!--/ajax:scormUsersTable-->
                            {/capture}
                            {eF_template_printBlock title = $smarty.const._REVIEWSCORMDATAFOR|cat:' &quot;'|cat:$T_CURRENT_LESSON->lesson.name|cat:'&quot;' data = $smarty.capture.scorm_review_code image = '32x32/scorm.png' main_options = $T_TABLE_OPTIONS help = 'SCORM_/_IMS'}

                        {elseif $smarty.get.scorm_import}
                            {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink'  href = '`$smarty.server.PHP_SELF`?ctg=scorm&scorm_import=1'>`$smarty.const._SCORMIMPORT`</a>"}

                            {capture name = 'scorm_import_code'}
                                {$T_UPLOAD_SCORM_FORM.javascript}
                                <form {$T_UPLOAD_SCORM_FORM.attributes}>
                                    {$T_UPLOAD_SCORM_FORM.hidden}
                                    <table style = "margin-top:15px;">
          <tr><td class = "labelCell">{$T_UPLOAD_SCORM_FORM.scorm_file[0].label}:&nbsp;</td>
           <td class = "elementCell">{$T_UPLOAD_SCORM_FORM.scorm_file[0].html} <img src = "images/16x16/add.png" alt = "{$smarty.const._ADDBOX}" title = "{$smarty.const._ADDBOX}" onclick = "Element.extend(this);this.up().up().next().show();this.hide();"></td></tr>
         {foreach name = 'file_upload_list' item = "item" key = "key" from = $T_UPLOAD_SCORM_FORM.scorm_file}
          {if $key > 0}
          <tr style = "display:none"><td class = "labelCell"></td>
           <td class = "elementCell">{$T_UPLOAD_SCORM_FORM.scorm_file[$key].html} <img src = "images/16x16/add.png" alt = "{$smarty.const._ADDBOX}" title = "{$smarty.const._ADDBOX}" onclick = "Element.extend(this);this.up().up().next().show();this.hide();"></td></tr>
          {/if}
         {/foreach}
                                        <tr><td></td>
                                            <td class = "infoCell">{$smarty.const._EACHFILESIZEMUSTBESMALLERTHAN} <b>{$T_MAX_FILE_SIZE}</b> {$smarty.const._KB}</td></tr>
                                        <tr><td class = "labelCell">{$T_UPLOAD_SCORM_FORM.url_upload.label}:
                                            <td class = "elementCell">{$T_UPLOAD_SCORM_FORM.url_upload.html}</td></tr>
                                        <tr><td class = "labelCell">{$T_UPLOAD_SCORM_FORM.embed_type.label}:
                                            <td class = "elementCell">{$T_UPLOAD_SCORM_FORM.embed_type.html}</td></tr>
                                        <tr><td class = "labelCell">{$T_UPLOAD_SCORM_FORM.popup_parameters.label}:
                                            <td class = "elementCell">{$T_UPLOAD_SCORM_FORM.popup_parameters.html}</td></tr>
                                        <tr><td class = "labelCell"></td>
                                            <td class = "submitCell">{$T_UPLOAD_SCORM_FORM.submit_upload_scorm.html}</td></tr>
                                    </table>
                                </form>
                            {/capture}
                            {eF_template_printBlock title = $smarty.const._SCORMIMPORT data = $smarty.capture.scorm_import_code image = '32x32/scorm.png' main_options = $T_TABLE_OPTIONS help = 'SCORM_/_IMS'}

                        {elseif $smarty.get.scorm_export}
                            {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink'  href = '`$smarty.server.PHP_SELF`?ctg=scorm&scorm_export=1'>`$smarty.const._SCORMEXPORT`</a>"}

                            {capture name = 'scorm_export_code'}
                            {if (isset($T_SCORM_EXPORT_FILE))}
                                <table style = "margin-top:15px;">
                                    <tr>
                                        <td><span style = "vertical-align:middle">{$smarty.const._DOWNLOADSCORMEXPORTEDFILE}:&nbsp;</span>
                                            <a href = "view_file.php?file={$T_SCORM_EXPORT_FILE.path}&action=download" target = "POPUP_FRAME" style = "vertical-align:middle">{$T_SCORM_EXPORT_FILE.name}</a>
                                            <img src = "images/16x16/import.png" alt = "{$smarty.const._DOWNLOADFILE}" title = "{$smarty.const._DOWNLOADFILE}" border = "0" style = "vertical-align:middle">
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
                            {eF_template_printBlock title = $smarty.const._SCORMEXPORT data = $smarty.capture.scorm_export_code image = '32x32/scorm.png' main_options = $T_TABLE_OPTIONS help = 'SCORM_/_IMS'}

                        {else}
                            {capture name = 't_scorm_tree_code'}
                                <table>
                                    <tr><td>
                                        {$T_SCORM_TREE}
                                    </td></tr>
                                </table>

                            {/capture}
                            {eF_template_printBlock title = $smarty.const._SCORMOPTIONSFOR|cat:' &quot;'|cat:$T_CURRENT_LESSON->lesson.name|cat:'&quot;' data = $smarty.capture.t_scorm_tree_code image = '32x32/scorm.png' main_options = $T_TABLE_OPTIONS help = 'SCORM_/_IMS'}
                        {/if}
                                </td></tr>
        {/capture}
