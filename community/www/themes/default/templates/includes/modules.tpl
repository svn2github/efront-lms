        {capture name = "moduleModules"}
                    <tr><td class="moduleCell">
                        {capture name = 't_modules_code'}
                        <script>var activate = '{$smarty.const._ACTIVATE}';var deactivate = '{$smarty.const._DEACTIVATE}';</script>
                        {if !isset($T_CURRENT_USER->coreAccess.modules) || $T_CURRENT_USER->coreAccess.modules == 'change'}
                                <div class = "headerTools">
                                    <span>
                                        <img src = "images/16x16/add.png" title = "{$smarty.const._INSTALLMODULE}" alt = "{$smarty.const._INSTALLMODULE}">
                                        <a href = "javascript:void(0)" onclick = "document.getElementById('upload_file_form').action = '{$smarty.server.PHP_SELF}?ctg=modules'; eF_js_showDivPopup('{$smarty.const._INSTALLMODULE}', 0, 'upload_file_table')" title = "{$smarty.const._INSTALLMODULE}">{$smarty.const._INSTALLMODULE}</a>                                                    
                                    </span>
                                </div>
                                
                                {assign var = "change_modules" value = 1}
                        {/if}
                        
                            <table style = "width:100%" class = "sortedTable">
                                <tr class = "defaultRowHeight">
                                    <td class = "topTitle">{$smarty.const._NAME}</td>
                                    <td class = "topTitle">{$smarty.const._TITLE}</td>
                                    <td class = "topTitle">{$smarty.const._AUTHOR}</td>
                                    <td class = "topTitle">{$smarty.const._VERSION}</td>
                                    <td class = "topTitle centerAlign">{$smarty.const._STATUS}</td>
                                    <td class = "topTitle centerAlign">{$smarty.const._FUNCTIONS}</td>
                                </tr>
                            {section name = 'modules_list' loop = $T_MODULES}
                                <tr id="row_{$T_MODULES[modules_list].className}" class = "{cycle values = "oddRowColor, evenRowColor"} {if !$T_MODULES[modules_list].active}deactivatedTableElement{/if}">
                                    <td>{$T_MODULES[modules_list].className}</td>
                                    <td>{$T_MODULES[modules_list].title}</td>
                                    <td>{$T_MODULES[modules_list].author}</td>
                                    <td>{$T_MODULES[modules_list].version}</td>
                                    <td class = "centerAlign">
                                {if !$T_MODULES[modules_list].errors}
                                    {if $T_MODULES[modules_list].active}
                                        <img class = "ajaxHandle" id="module_status_img" src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._DEACTIVATE}" title = "{$smarty.const._DEACTIVATE}" {if $change_modules}onclick = "activateModule(this, '{$T_MODULES[modules_list].className}')"{/if}>
                                    {else}
                                        <img class = "ajaxHandle" id="module_status_img" src = "images/16x16/trafficlight_red.png"   alt = "{$smarty.const._ACTIVATE}"   title = "{$smarty.const._ACTIVATE}"   {if $change_modules}onclick = "activateModule(this, '{$T_MODULES[modules_list].className}')"{/if}>
                                    {/if}
                                {else}
                                        <img src = "images/16x16/close.png" alt = "{$T_MODULES[modules_list].errors}" title = "{$T_MODULES[modules_list].errors}">
                                {/if}
                                    </td>
                                    <td class = "centerAlign">
                                        <img class = "ajaxHandle" src = "images/16x16/information.png" alt = "{$smarty.const._DESCRIPTION}" title = "{$smarty.const._DESCRIPTION}" onclick = "eF_js_showDivPopup('{$smarty.const._MODULEINFORMATION}', 1, 'module_info_table_{$smarty.section.modules_list.iteration}')"/>
                                    {if $change_modules}
                                        <img class = "ajaxHandle" src = "images/16x16/generic.png" title="{$smarty.const._UPGRADEMODULE}" alt="{$smarty.const._UPGRADEMODULE}" onclick = "document.getElementById('upload_file_form').action = '{$smarty.server.PHP_SELF}?ctg=modules&upgrade={$T_MODULES[modules_list].className}'; eF_js_showDivPopup('{$smarty.const._UPGRADEMODULE} {$T_MODULES[modules_list].className}', 0, 'upload_file_table')"/>
                                        <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "if (confirm ('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) deleteModule(this, '{$T_MODULES[modules_list].className}')"/>
                                        {/if}
                                        <div id = "module_info_table_{$smarty.section.modules_list.iteration}" style = "display:none">
										{capture name = 't_module_info_code'}
                                            <table style = "text-align:left">
                                                <tr style = "border-bottom:1px dotted gray"><td>{$smarty.const._TITLE}:&nbsp;</td><td>{$T_MODULES[modules_list].title}</td></tr>
                                                <tr style = "border-bottom:1px dotted gray"><td>{$smarty.const._AUTHOR}:&nbsp;</td><td>{$T_MODULES[modules_list].author}</td></tr>
                                                <tr style = "border-bottom:1px dotted gray"><td>{$smarty.const._VERSION}:&nbsp;</td><td>{$T_MODULES[modules_list].version}</td></tr>
                                                <tr style = "border-bottom:1px dotted gray"><td>{$smarty.const._DESCRIPTION}:&nbsp;</td><td>{$T_MODULES[modules_list].description}</td></tr>
                                                <tr style = "border-bottom:1px dotted gray"><td>{$smarty.const._VALIDFOR}:&nbsp;</td><td>{$T_MODULES[modules_list].permissions}</td></tr>
                                            </table>
										{/capture}
										{eF_template_printBlock title=$smarty.const._MODULEINFORMATION data=$smarty.capture.t_module_info_code image='32x32/addons.png'}
                                        </div>
                                    </td>
                                </tr>
                            {sectionelse}
                                <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "6">{$smarty.const._NODATAFOUND}</td></tr>
                            {/section}
                            </table>
                            
                            <div id = "upload_file_table" style = "display:none">
							{capture name = 't_upload_module_code'}
                                {$T_UPLOAD_FILE_FORM.javascript}
                                <form {$T_UPLOAD_FILE_FORM.attributes}>
                                    {$T_UPLOAD_FILE_FORM.hidden}
                                    <table class = "formElements uploadBox">
                                        <tr><td class = "labelCell">{$smarty.const._FILENAME}:&nbsp;</td>
                                            <td class = "elementCell">{$T_UPLOAD_FILE_FORM.file_upload.0.html}</td></tr>
                                        <tr><td></td><td class = "infoCell">{$smarty.const._EACHFILESIZEMUSTBESMALLERTHAN} <b>{$smarty.const.G_MAXFILESIZE/1024}</b> {$smarty.const._KB}</td></tr>
                                        {if $T_UPLOAD_FILE_FORM.file_upload.0.error}<tr><td></td><td class = "formError">{$T_UPLOAD_FILE_FORM.file_upload.0.error}</td></tr>{assign var = 'div_error' value = 'upload_file_table'}{/if}
                                        <tr><td></td><td >&nbsp;</td></tr>
                                        <tr><td></td><td class = "submitCell">{$T_UPLOAD_FILE_FORM.submit_upload_file.html}</td></tr>
                                    </table>
                                </form>
							{/capture}
							{eF_template_printBlock title=$smarty.const._INSTALLMODULE data=$smarty.capture.t_upload_module_code image='32x32/addons.png'}
                            </div>
                        {/capture}

                        {eF_template_printBlock title=$smarty.const._MODULES data=$smarty.capture.t_modules_code image='32x32/addons.png'}
				</td></tr>
{/capture}