        {capture name="moduleLanguages"}
            <tr><td class = "moduleCell">
                {capture name = "languageAdmin"}
                	<script>var activate = '{$smarty.const._ACTIVATE}';var deactivate = '{$smarty.const._DEACTIVATE}';</script>
                    {if !isset($T_CURRENT_USER->coreAccess.configuration) || $T_CURRENT_USER->coreAccess.configuration == 'change'}
                        <div class = "headerTools">
                            <span>
                                <img src = "images/16x16/add.png" alt = "{$smarty.const._ADDLANGUAGE}" title = "{$smarty.const._ADDLANGUAGE}">
                                <a href = "javascript:void(0)"  title = "{$smarty.const._ADDLANGUAGE}" onclick = "eF_js_showDivPopup('{$smarty.const._ADDLANGUAGE}', 0, 'language_table');$('language_name').value = '';$('language_translation').value = '';$('selected_language').value = '';$('language_rtl').checked = '';">{$smarty.const._ADDLANGUAGE}</a>
                            </span>
                        </div>
                        
                        {assign var = "change_languages" value = "1"}
                    {/if}
<!--ajax:languagesTable-->
                        <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "2" id = "languagesTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=languages&">
                            <tr class = "defaultRowHeight">
                                <td class = "topTitle" name = "name">{$smarty.const._CURRENTLANGUAGES}</td>
                                <td class = "topTitle" name = "translation">{$smarty.const._TRANSLATION}</td>
                                <td class = "topTitle centerAlign" name = "active">{$smarty.const._STATUS}</td>
                    {if $change_languages}
                               <td class = "topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
                    {/if}
                            </tr>
                    {foreach name = 'language_list' key = "name" item = "language" from = $T_DATA_SOURCE}
                            <tr id="row_{$language.name}" class = "{cycle name = "languages" values = "oddRowColor, evenRowColor"} {if !$language.active}deactivatedTableElement{/if}">
                                <td>{$language.name}</td>
                                <td>{$language.translation}</td>
                                <td class = "centerAlign"><span style = "display:none">{$language.active}</span>
                                    {if $language.active}
                                        <img class = "ajaxHandle" src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._DEACTIVATE}" title = "{$smarty.const._DEACTIVATE}" {if $change_languages}onclick = "activateLanguage(this, '{$language.name}')"{/if}>
                                    {else}
                                        <img class = "ajaxHandle" src = "images/16x16/trafficlight_red.png"   alt = "{$smarty.const._ACTIVATE}"   title = "{$smarty.const._ACTIVATE}"   {if $change_languages}onclick = "activateLanguage(this, '{$language.name}')"{/if}>
                                    {/if}
                                </td>
                    {if $change_languages}
                                <td class = "centerAlign">
                                    <a href = "view_file.php?file={$language.file_path}&action=download"><img src = "images/16x16/import.png" title = "{$smarty.const._DOWNLOADLANGUAGEFILE}" alt = "{$smarty.const._DOWNLOADLANGUAGEFILE}" /></a>
                        {if $name != 'english'}
                                    <img class = "ajaxHandle" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" onclick = "eF_js_showDivPopup('{$smarty.const._EDIT}', 0, 'language_table');$('language_name').value = '{$language.name}';$('language_translation').value = '{$language.translation}';$('selected_language').value = '{$language.name}';$('language_rtl').checked = {$language.rtl};"/>
                                    <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}"  onclick = "if (confirm ('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) deleteLanguage(this, '{$language.name}')"/>
                        {/if}
                                </td>
                    {/if}
                             </tr>
                    {foreachelse}
                            <tr class = "oddRowColor defaultRowHeight"><td class = "emptyCategory" colspan = "3">{$smarty.const._NODATAFOUND}</td></tr>
                    {/foreach}
                        </table>
<!--/ajax:languagesTable-->
                        <div id = "language_table" style = "display:none;">
						{capture name = "language_code"}
                            {$T_CREATE_LANGUAGE_FORM.javascript}
                            <form {$T_CREATE_LANGUAGE_FORM.attributes}>
                                {$T_CREATE_LANGUAGE_FORM.hidden}
                                <table class = "formElements">
                                    <tr><td class = "labelCell">{$smarty.const._ENGLISHNAME}:&nbsp;</td>
                                        <td class = "elementCell">{$T_CREATE_LANGUAGE_FORM.english_name.html}</td></tr>
                                    {if $T_CREATE_LANGUAGE_FORM.english_name.error}<tr><td></td><td class = "formError" colspan = "2">{$T_CREATE_LANGUAGE_FORM.english_name.error}</td></tr>{assign var = 'div_error' value = 'upload_language_table'|cat:$smarty.section.form_list.index}{/if}
                                    <tr><td class = "labelCell">{$smarty.const._TRANSLATION}:&nbsp;</td>
                                        <td class = "elementCell">{$T_CREATE_LANGUAGE_FORM.translation.html}</td></tr>
                                    {if $T_CREATE_LANGUAGE_FORM.translation.error}<tr><td></td><td class = "formError" colspan = "2">{$T_CREATE_LANGUAGE_FORM.translation.error}</td></tr>{assign var = 'div_error' value = 'upload_language_table'|cat:$smarty.section.form_list.index}{/if}
                                    <tr><td class = "labelCell">{$smarty.const._RTLLANGUAGE}:&nbsp;</td>
                                        <td class = "elementCell">{$T_CREATE_LANGUAGE_FORM.rtl.html}</td></tr>
                                    <tr><td class = "labelCell">{$smarty.const._FILENAME}:&nbsp;</td>
                                        <td class = "elementCell">{$T_CREATE_LANGUAGE_FORM.language_upload.html}</td></tr>
                                    <tr><td></td>
                                        <td class = "infoCell">{$smarty.const._EACHFILESIZEMUSTBESMALLERTHAN} <b>{$T_MAX_FILE_SIZE}</b> {$smarty.const._KB}</td></tr>
                                    {if $T_CREATE_LANGUAGE_FORM.language_upload.error}<tr><td></td><td class = "formError" colspan = "2">{$T_CREATE_LANGUAGE_FORM.language_upload.error}</td></tr>{assign var = 'div_error' value = 'upload_language_table'|cat:$smarty.section.form_list.index}{/if}
                                    <tr><td colspan = "2">&nbsp;</td></tr>
                                    <tr><td></td>
                                        <td class = "elementCell">{$T_CREATE_LANGUAGE_FORM.submit_upload_language.html}</td></tr>
                                </table>
                            </form>
						{/capture}
						{eF_template_printBlock title = $smarty.const._LANGUAGEADMINISTRATION data = $smarty.capture.language_code image = '32x32/languages.png'}
                        </div>

                 {/capture}

                {eF_template_printBlock title = $smarty.const._LANGUAGEADMINISTRATION data = $smarty.capture.languageAdmin image = '32x32/languages.png'}
            </td></tr>
        {/capture}