                                {capture name = "t_files_code"}
                                    <table>
                                        <tr><td style = "padding-right:1em">
                                                <a href = "javascript:void(0)" onclick = "eF_js_showDivPopup('{$smarty.const._UPLOADFILE}', new Array('200px', '100px'), 'upload_file_table')" title = "{$smarty.const._UPLOADFILE}"><img src = "images/16x16/add2.png" title = "{$smarty.const._UPLOADFILE}" alt = "{$smarty.const._UPLOADFILE}" border = "0" style="vertical-align:middle" /></a>
                                                <a href = "javascript:void(0)" onclick = "eF_js_showDivPopup('{$smarty.const._UPLOADFILE}', new Array('200px', '100px'), 'upload_file_table')" title = "{$smarty.const._UPLOADFILE}" style = "vertical-align:middle">{$smarty.const._UPLOADFILE}</a>
                                            </td><td style = "border-left:1px solid black;padding-left:1em;padding-right:1em">
                                                <a href = "javascript:void(0)" onclick = "eF_js_showDivPopup('{$smarty.const._CREATEFOLDER}', new Array('200px', '100px'), 'create_directory_table')" title = "{$smarty.const._CREATEFOLDER}"><img src = "images/16x16/folder_add.png" title = "{$smarty.const._CREATEFOLDER}" alt = "{$smarty.const._CREATEFOLDER}" border = "0" style="vertical-align:middle" /></a>
                                                <a href = "javascript:void(0)" onclick = "eF_js_showDivPopup('{$smarty.const._CREATEFOLDER}', new Array('200px', '100px'), 'create_directory_table')" title = "{$smarty.const._CREATEFOLDER}" style = "vertical-align:middle">{$smarty.const._CREATEFOLDER}</a>
                                            </td>
                                    {if $T_CURRENT_LESSON->lesson.options.digital_library}
                                            <td style = "border-left:1px solid black;padding-left:1em">
                                                <a href = "{$smarty.server.PHP_SELF}?ctg=content&op=file_manager&digital_library=1" style = "vertical-align:middle"><img src = "images/16x16/arrow_right_blue.png" title = "{$smarty.const._DIGITALLIBRARY}" alt = "{$smarty.const._DIGITALLIBRARY}" border = "0" style="vertical-align:middle" /></a>
                                                <a href = "{$smarty.server.PHP_SELF}?ctg=content&op=file_manager&digital_library=1" style = "vertical-align:middle">{$smarty.const._DIGITALLIBRARY}</a>
                                            </td>
                                    {/if}
                                    	</tr>
                                        <tr><td colspan = "2"></td></tr>
                                    </table>
                                    
                                    {* MODULE HCD CUSTOMIZATION: RETURN TO THE RIGHT TAG *}
                                    {if $T_MODULE_HCD_INTERFACE && isset($T_REFERER)}
                                        <form name = "files_form" method = "post" action = "{$T_REFERER}&tab=file_record{if $smarty.get.dir}&dir={$smarty.get.dir}{/if}">
                                    {else}                                                
                                        <form name = "files_form" method = "post" action = "{$smarty.server.PHP_SELF}?ctg=content&op=file_manager{if $smarty.get.dir}&dir={$smarty.get.dir}{/if}">
                                    {/if} 
                                    <table width = "100%" class = "sortedTable">
                                        <tr class = "defaultRowHeight">
                                            <td class = "topTitle">{$smarty.const._TYPE}</td>
                                            <td class = "topTitle">{$smarty.const._FILENAME}</td>
                                            <td class = "topTitle">{$smarty.const._SIZE}</td>
                                            <td class = "topTitle">{$smarty.const._LASTMODIFIED}</td>
                                            <td class = "topTitle centerAlign noSort">{$smarty.const._FUNCTIONS}</td>
                                            <td class = "topTitle centerAlign">{$smarty.const._SELECT}</td>
                                        </tr>
                                        
                                        
                                    {foreach name = 'files_list' key = key item = file from = $T_FILES}
                                        {if $smarty.foreach.files_list.first && $smarty.get.dir}
                                            <tr>
                                                <td><img src = "images/16x16/folder_up.png" alt = "{$smarty.const._FOLDER}" title = "{$smarty.const._FOLDER}"/></td>
                                                {* MODULE HCD CUSTOMIZATION: RETURN TO THE RIGHT TAG *}
                                                {if $T_MODULE_HCD_INTERFACE && isset($T_REFERER)}
                                                    <td><a href = "{$T_REFERER}&tab=file_record{if $T_PARENT_DIR}&dir={$T_PARENT_DIR}{/if}" title = "{$smarty.const._UPONELEVEL}">.. ({$smarty.const._UPONELEVEL})</a></td><td colspan = "3"></td>
                                                {else}                                                
                                                    <td><a href = "{$smarty.server.PHP_SELF}?ctg=content&op=file_manager{if $T_PARENT_DIR}&dir={$T_PARENT_DIR}{/if}" title = "{$smarty.const._UPONELEVEL}">.. ({$smarty.const._UPONELEVEL})</a></td><td colspan = "3"></td>
                                                {/if}    
                                            </tr>
                                        {/if}
                                        <tr class = "{cycle values = "oddRowColor, evenRowColor"} defaultRowHeight">
                                            <td><span style = "display:none">{$file.extension}{*Used for sorting*}</span>{if $file.type == 'directory'}<img src = "images/16x16/folder.png" alt = "{$smarty.const._FOLDER}" title = "{$smarty.const._FOLDER}"/>{else}<img src = "images/file_types/{$file.image}" alt = "{$file.mime_type}" title = "{$file.mime_type}"/>{/if}</td>
                                            <td>
                                        {if $file.type == 'directory'}
                                                {* MODULE HCD CUSTOMIZATION: RETURN TO THE RIGHT TAG *}
                                                {if $T_MODULE_HCD_INTERFACE && isset($T_REFERER)}
                                                    <a href = "{$T_REFERER}&tab=file_record&dir={$file.id}" title = "{$file.original_name}">{$file.original_name}</a>
                                                {else}
                                                    <a href = "{$smarty.server.PHP_SELF}?ctg=content&op=file_manager&dir={$file.id}" title = "{$file.original_name}">{$file.original_name}</a>
                                                {/if}   
                                        {else}
                                                <a href = "view_file.php?file={$file.id}" title = "{$smarty.const._PREVIEW}" target = "PREVIEW_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._PREVIEW}', new Array('500px', '400px'), 'preview_table');">{$file.original_name}</a>
                                        {/if}
                                            </td>
                                            <td>{if $file.type != 'directory'}{$file.size} {$smarty.const._KB}{/if}</td>
                                            <td>#filter:timestamp_time-{$file.timestamp}#</td>
                                            <td style = "text-align:center;white-space:nowrap">
                                                {if $file.type != 'directory'}
                                                	<a href = "downloadfile.php?file={$file.id}" target = "PREVIEW_FRAME"><img src = "images/16x16/import2.png" border = "0" title = "{$smarty.const._DOWNLOADFILE}" alt = "{$smarty.const._DOWNLOADFILE}"/></a>
{*
                                                    {if $T_MODULE_HCD_INTERFACE && isset($T_REFERER)}                                                
                                                        <a href = "{$T_REFERER}&tab=file_record{if $smarty.get.dir}&dir={$smarty.get.dir}{/if}&share={$file.id}"><img src = "images/16x16/{if $file.shared}folder_forbidden.png{else}folder_refresh.png{/if}" border = "0" title = "{$smarty.const._SHARE}" alt = "{$smarty.const._SHARE}" /></a>
                                                    {else}
                                                        <a href = "{$smarty.server.PHP_SELF}?ctg=content&op=file_manager{if $smarty.get.dir}&dir={$smarty.get.dir}{/if}&share={$file.id}"><img src = "images/16x16/{if $file.shared}folder_forbidden.png{else}folder_refresh.png{/if}" border = "0" title = "{$smarty.const._SHARE}" alt = "{$smarty.const._SHARE}" /></a>
                                                    {/if}
*}
                                                {/if}
                                                {if $file.extension == 'zip' || $file.extension == 'gz'}
                                            		<a href = "{$smarty.server.PHP_SELF}?ctg=content&op=file_manager{if $smarty.get.dir}&dir={$smarty.get.dir}{/if}&uncompress={$file.id}"><img src = "images/16x16/box.png" border = "0" title = "{$smarty.const._UNCOMPRESS}" alt = "{$smarty.const._UNCOMPRESS}"/></a>
                                                {elseif $file.type == 'directory'}
                                                	<a href = "{$smarty.server.PHP_SELF}?ctg=content&op=file_manager{if $smarty.get.dir}&dir={$smarty.get.dir}{/if}&compress={$file.id}"><img src = "images/16x16/import1.png" border = "0" title = "{$smarty.const._COMPRESSDOWNLOAD}" alt = "{$smarty.const._COMPRESSDOWNLOAD}"/></a>
                                                {/if}
                                                <a href = "javascript:void(0)" onclick = "eF_js_showDivPopup('{$smarty.const._COPY}', new Array('200px', '100px'), 'copy_table');document.getElementById('copy_file').value='{$file.id}';document.getElementById('copy_submit').value='{$smarty.const._COPY}';document.getElementById('type').value='{$file.type}';"><img src = "images/16x16/copy.png" border = "0" title = "{$smarty.const._COPY}" alt = "{$smarty.const._COPY}"/></a>
                                                <a href = "javascript:void(0)" onclick = "eF_js_showDivPopup('{$smarty.const._EDIT}', new Array('200px', '100px'), 'edit_table');document.getElementById('edit_file').value='{$file.original_name}';document.getElementById('edit_file_from').value='{$file.id}';document.getElementById('edit_file_desc').value='{$file.description}';"><img src = "images/16x16/edit.png" border = "0" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}"/></a>
                                                {* MODULE HCD CUSTOMIZATION: RETURN TO THE RIGHT TAG *}
                                                {if $T_MODULE_HCD_INTERFACE && isset($T_REFERER)}                                                
                                                    <a href = "{$T_REFERER}&tab=file_record{if $smarty.get.dir}&dir={$smarty.get.dir}{/if}&delete={$file.physical_name}"><img src = "images/16x16/delete.png" border = "0" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "return confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')"/></a>
                                                {else}
                                                    <a href = "{$smarty.server.PHP_SELF}?ctg=content&op=file_manager{if $smarty.get.dir}&dir={$smarty.get.dir}{/if}&delete={$file.physical_name}"><img src = "images/16x16/delete.png" border = "0" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "return confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')"/></a>
                                                {/if}
                                            </td>
                                            <td class = "centerAlign"><input type = "checkbox" name = "files[{$file.id}]" class = "inputCheckbox" {if $file.type == 'directory'}style = "display:none" disabled{/if}/></td> {*We use filename as the input name and extension as its value, since php would convert . to _ and if we used for example test.zip as the field name, it would then become test_zip at the POST data*}
                                        </tr>
                                    {foreachelse}
                                        {assign var = "empty_files_list" value = true}  {*This is used to conditionally print the "With selected files" function table right below*}
                                        {if $smarty.foreach.files_list.first && $smarty.get.dir}
                                            <tr>
                                                <td><img src = "images/16x16/folder_up.png" alt = "{$smarty.const._FOLDER}" title = "{$smarty.const._FOLDER}"/></td>
                                                {* MODULE HCD CUSTOMIZATION: RETURN TO THE RIGHT TAG *}
                                                {if $T_MODULE_HCD_INTERFACE && isset($T_REFERER)}
                                                    <td><a href = "{$T_REFERER}&tab=file_record{if $T_PARENT_DIR}&dir={$T_PARENT_DIR}{/if}" title = "{$smarty.const._UPONELEVEL}">.. ({$smarty.const._UPONELEVEL})</a></td><td colspan = "3"></td>
                                                {else}                                             
                                                    <td><a href = "{$smarty.server.PHP_SELF}?ctg=content&op=file_manager{if $T_PARENT_DIR}&dir={$T_PARENT_DIR}{/if}" title = "{$smarty.const._UPONELEVEL}">.. ({$smarty.const._UPONELEVEL})</a></td><td colspan = "3"></td>
                                                {/if}    
                                            </tr>
                                        {/if}
                                        <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "emptyCategory centerAlign">{$smarty.const._NOFILESFOUND}</td></tr>
                                    {/foreach}
                                    </table>
                                    
                                    {if !$empty_files_list}
                                    <table style = "width:100%">
                                        <tr><td class = "horizontalSeparatorAbove">{$smarty.const._WITHSELECTEDFILES}:
{*
                                            <span>
                                                <input name = "download_selected" type = "image" src = "images/16x16/import1.png"          border = "0" title = "{$smarty.const._ZIPANDDOWNLOAD}" alt = "{$smarty.const._ZIPANDDOWNLOAD}" onclick = "files_form.selected_action.value = 'download';"/>
                                                <input name = "compress_selected"   type = "image" src = "images/16x16/box_closed.png"       border = "0" title = "{$smarty.const._COMPRESSTOZIP}"  alt = "{$smarty.const._COMPRESSTOZIP}"  onclick = "files_form.selected_action.value = 'compress';"/>
                                                <input name = "copy_selected"       type = "image" src = "images/16x16/copy.png"             border = "0" title = "{$smarty.const._COPY}"           alt = "{$smarty.const._COPY}"           onclick = "files_form.selected_action.value = 'copy';"/>
                                                <input name = "move_selected"       type = "image" src = "images/16x16/arrow_right_blue.png" border = "0" title = "{$smarty.const._MOVE}"           alt = "{$smarty.const._MOVE}"           onclick = "files_form.selected_action.value = 'move';"/>
                                            </span>
*}
                                                <input name = "delete_selected"     type = "image" src = "images/16x16/delete.png"           border = "0" title = "{$smarty.const._DELETE}"         alt = "{$smarty.const._DELETE}"         onclick = "files_form.selected_action.value = 'delete';return confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}');" style = "vertical-align:middle"/>
                                                <input type = "hidden" name = "selected_action" value = ""/>
                                            </td></tr>
                                    </table>
                                    {/if}
                                    
                                {/capture}
                                {eF_template_printInnerTable title = $smarty.const._FILES data = $smarty.capture.t_files_code image = "/32x32/folder_view.png"}
                                    <div id = "preview_table" style = "display:none">
                                        {*<object width = "100" height = "100" id = "preview_object" data = "downloadfile.php?offset=14&action=link&filename=kef5_84.jpg" ype = "image/jpg" tyle = "width:100%;height:100%;"></object>*}
                                        <iframe name = "PREVIEW_FRAME" style = "border-width:0px;width:100%;height:100%;padding:0px 0px 0px 0px"></iframe>
                                    </div>
                                    <div id = "create_directory_table" style = "display:none">
                                        {$T_CREATE_DIRECTORY_FORM.javascript}                                    
                                        <form {$T_CREATE_DIRECTORY_FORM.attributes}>
                                            {$T_CREATE_DIRECTORY_FORM.hidden}
                                            <table style = "margin: 2em 2em 2em 2em" class = "formElements">
                                                <tr><td class = "labelCell">{$smarty.const._FOLDERNAME}:&nbsp;</td><td class = "elementCell">{$T_CREATE_DIRECTORY_FORM.name.html}</td></tr>
                                                {if $T_CREATE_DIRECTORY_FORM.name.error}<tr><td></td><td class = "formError">{$T_CREATE_DIRECTORY_FORM.name.error}</td></tr>{assign var = 'div_error' value = 'create_directory_table'}{/if}
                                                <tr><td></td><td>&nbsp;</td></tr>
                                                <tr><td></td><td class = "submitCell">{$T_CREATE_DIRECTORY_FORM.submit_create_directory.html}</td></tr>
                                            </table>
                                        </form>
                                    </div>
                                    <div id = "upload_file_table" style = "display:none">
                                        {$T_UPLOAD_FILE_FORM.javascript}                                    
                                        <form {$T_UPLOAD_FILE_FORM.attributes}>
                                            {$T_UPLOAD_FILE_FORM.hidden}
                                            <table style = "margin: 2em 2em 2em 2em" class = "formElements">
                                                <tr><td class = "labelCell">{$smarty.const._FILENAME}:&nbsp;</td><td colspan = "2" class = "elementCell">{$T_UPLOAD_FILE_FORM.file_upload.html}</td></tr>
                                                <tr><td></td><td colspan = "2" class = "infoCell">{$smarty.const._EACHFILESIZEMUSTBESMALLERTHAN} <b>{$T_MAX_FILE_SIZE}</b> {$smarty.const._KB}</td></tr>
                                                {if $T_UPLOAD_FILE_FORM.file_upload.error}<tr><td></td><td class = "formError" colspan = "2">{$T_UPLOAD_FILE_FORM.file_upload.error}</td></tr>{assign var = 'div_error' value = 'upload_file_table'}{/if}
                                                <tr><td class = "labelCell">{$smarty.const._UNZIPFILE}:&nbsp;</td><td class = "elementCell">{$T_UPLOAD_FILE_FORM.unzip.html}</td><td class = "infoCell">({$smarty.const._FILEWILLUNZIPINFOLDERWITHSAMENAME})</td></tr>
                                                <tr><td class = "labelCell">{$smarty.const._OVERWRITEFILESWITHSAMENAME}:&nbsp;</td><td colspan = "2" class = "elementCell">{$T_UPLOAD_FILE_FORM.overwrite.html}</td></tr>
                                                {if $T_CURRENT_LESSON->options.digital_library}<tr><td class = "labelCell">{$smarty.const._UPLOADTODIGITALLIBRARY}:&nbsp;</td><td colspan = "2" class = "elementCell">{$T_UPLOAD_FILE_FORM.digital_library.html}</td></tr>{/if}
                                                <tr><td></td><td colspan = "2">&nbsp;</td></tr>
                                                <tr><td></td><td colspan = "2" class = "submitCell">{$T_UPLOAD_FILE_FORM.submit_upload_file.html}</td></tr>
                                            </table>
                                        </form>
                                    </div>
                                    <div id = "edit_table" style = "display:none;">
                                        {$T_EDIT_FORM.javascript}                                    
                                        <form {$T_EDIT_FORM.attributes}>
                                            {$T_EDIT_FORM.hidden}
                                            <table style = "margin: 2em 2em 2em 2em" class = "formElements">
                                                <tr><td class = "labelCell">{$smarty.const._NAME}:&nbsp;</td>
                                                	<td class = "elementCell">{$T_EDIT_FORM.name.html}</td></tr>
                                                {if $T_EDIT_FORM.name.error}<tr><td></td><td class = "formError">{$T_EDIT_FORM.name.error}</td></tr>{assign var = 'div_error' value = 'edit_table'}{/if}
                                                <tr><td class = "labelCell">{$smarty.const._DESCRIPTION}:&nbsp;</td>
                                                	<td class = "elementCell">{$T_EDIT_FORM.description.html}</td></tr>
                                                {if $T_EDIT_FORM.description.error}<tr><td></td><td class = "formError">{$T_EDIT_FORM.description.error}</td></tr>{assign var = 'div_error' value = 'edit_table'}{/if}
                                                <tr><td></td><td>&nbsp;</td></tr>
                                                <tr><td></td><td class = "submitCell">{$T_EDIT_FORM.submit_edit.html}</td></tr>
                                            </table>
                                        </form>
                                    </div>
                                    <div id = "copy_table" style = "display:none;">
                                    {if $T_COPY_FORM}
                                        {$T_COPY_FORM.javascript}                                    
                                        <form {$T_COPY_FORM.attributes}>
                                            {$T_COPY_FORM.hidden}
                                            <table style = "margin: 2em 2em 2em 2em" class = "formElements">
                                                <tr><td class = "labelCell">{$smarty.const._YOUWANTTO}:&nbsp;</td>
                                                	<td class = "elementCell">{$T_COPY_FORM.action.html} {$smarty.const._SELECTEDENTRYTO} {$T_COPY_FORM.destination.html}</td></tr>
                                                {if $T_COPY_FORM.destination.error}<tr><td></td><td class = "formError">{$T_COPY_FORM.destination.error}</td></tr>{assign var = 'div_error' value = 'copy_table'}{/if}
                                                <tr><td></td><td>&nbsp;</td></tr>
                                                <tr><td></td><td class = "submitCell">{$T_COPY_FORM.submit_copy.html}</td></tr>
                                            </table>
                                        </form>
                                    {else}
                                            <table style = "margin: 2em 2em 2em 2em">
                                                <tr><td>{$smarty.const._NOFOLDERSFOUNDTOCOPYTO}</td></tr>
                                            </table>
                                    {/if}
                                    </div>
