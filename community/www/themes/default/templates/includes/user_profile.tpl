        {*moduleCustomizeUsersProfile: The users profile customization page*}
        {capture name = "moduleCustomizeUsersProfile"}
            <tr><td class="moduleCell">
                {if $smarty.get.add_field || $smarty.get.edit_field}
                            {capture name = 'field_form_code'}
                                {$T_FIELD_FORM.javascript}
                                <form {$T_FIELD_FORM.attributes}>
                                    {$T_FIELD_FORM.hidden}
                                    <table>
                                        <tr><td class = "labelCell">{$smarty.const._FIELDNAME}:&nbsp;</td>
                                            <td class = "elementCell">{$T_FIELD_FORM.name.html}</td></tr>
                                        <tr><td></td>
                                         <td class = "infoCell">{$smarty.const._CHARACTERSMAX20ONLYNOSPACES}</td></tr>
                                        {if $T_FIELD_FORM.name.error}<tr><td></td><td class = "formError">{$T_FIELD_FORM.name.error}</td></tr>{/if}
                                        <tr><td class = "labelCell">{$smarty.const._FIELDDESCRIPTION}:&nbsp;</td>
                                            <td class = "elementCell">{$T_FIELD_FORM.description.html}</td></tr>
                                        <tr><td></td>
                                         <td class = "infoCell">{$smarty.const._INTHESELECTEDLANGUAGE}</td></tr>
                                        <tr id ="database_type_row" ><td class = "labelCell">{$smarty.const._DBTYPE}:&nbsp;</td>
                                            <td class = "elementCell">{$T_FIELD_FORM.db_type.html}</td></tr>
                                        <tr><td class = "labelCell">{$smarty.const._TYPE}:&nbsp;</td>
                                            <td class = "elementCell">{$T_FIELD_FORM.type.html}</td></tr>
                                        <tr id = "select_field" style = "display:none"><td class = "labelCell">{$smarty.const._VALUES}:&nbsp;</td>
                                            <td class = "elementCell">{$T_FIELD_FORM.values[0].html}&nbsp;<img src = "images/16x16/add.png" title = "{$smarty.const._ADDVALUES}" alt = "{$smarty.const._ADDVALUES}" border = "0" style = "vertical-align:middle" onclick = "addValue()"></td></tr>
                                        {if $smarty.get.edit_field}
                                            {section name = 'field_list' loop = $T_FIELD_FORM.values}
                                                {if !$smarty.section.field_list.first}
                                        <tr><td></td>
                                         <td class = "elementCell">{$T_FIELD_FORM.values[field_list].html}&nbsp;<img src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETEVALUE}" alt = "{$smarty.const._DELETEVALUE}" border = "0" style = "vertical-align:middle" onclick = "event.findElement('tr').remove();elementCount--;"/></td></tr>
                                                {/if}
                                            {/section}
                                        {/if}
                                        <tr id = "text_field"><td class = "labelCell">{$smarty.const._DEFAULTVALUE}:&nbsp;</td>
                                            <td class = "elementCell">{$T_FIELD_FORM.default_value.html}</td></tr>
                                        <tr id = "textarea_field" style = "display:none"><td class = "labelCell">{$smarty.const._DEFAULTVALUE}:&nbsp;</td>
                                            <td class = "elementCell">{$T_FIELD_FORM.default_textarea.html}</td></tr>






                                        <tr><td class = "labelCell">{$smarty.const._ACTIVENEUTRAL}:&nbsp;</td>
                                            <td class = "elementCell">{$T_FIELD_FORM.active.html}</td></tr>
                                        <tr><td class = "labelCell">{$smarty.const._ISVISIBLEFROMOTHERUSERS}&nbsp;</td>
                                            <td class = "elementCell">{$T_FIELD_FORM.visible.html}</td></tr>
                                        <tr><td class = "labelCell">{$smarty.const._ISMANDATORY}:&nbsp;</td>
                                            <td class = "elementCell">{$T_FIELD_FORM.mandatory.html}</td></tr>
                                        <tr><td class = "labelCell">{$smarty.const._LANGUAGE}:&nbsp;</td>
                                            <td class = "elementCell">{$T_FIELD_FORM.languages_NAME.html}</td></tr>
                                        <tr><td colspan = "2">&nbsp;</td></tr>
                                        <tr><td></td><td>{$T_FIELD_FORM.submit_field.html}</td></tr>
                                    </table>
                                </form>

                                <script>

                                var elementCount = 0;
                                var profileType = "";

                                {if isset($T_PROFILE_TYPE)}profileType = "{$T_PROFILE_TYPE}";{/if}
                                {if isset($T_SELECT_OPTIONS)}elementCount = {$T_SELECT_OPTIONS};{/if}
                                </script>
                            {/capture}
                            {eF_template_printBlock title = $smarty.const._CUSTOMIZEUSERSPROFILE data = $smarty.capture.field_form_code image = '32x32/profile_add.png'}
                {else}
                    {capture name = 't_fields_list'}
                        {if !isset($T_CURRENT_USER->coreAccess.configuration) || $T_CURRENT_USER->coreAccess.configuration == 'change'}
                          <div class = "headerTools">
                              <span>
                                  <img src = "images/16x16/add.png" alt = "{$smarty.const._ADDNEWFIELD}" title = "{$smarty.const._ADDNEWFIELD}">
                                  <a href = "administrator.php?ctg=user_profile&add_field=1" title = "{$smarty.const._ADDNEWFIELD}">{$smarty.const._ADDNEWFIELD}</a>
                              </span>
                          </div>

                          {assign var = "change_fields" value = "1"}
                        {/if}
                                <table width = "100%" class = "sortedTable">
                                    <tr class = "topTitle defaultRowHeight">
                                        <td class = "topTitle">{$smarty.const._FIELDNAME}</td>
                                        <td class = "topTitle">{$smarty.const._DESCRIPTION}</td>
                                        <td class = "topTitle">{$smarty.const._TYPE}</td>
                                        <td class = "topTitle">{$smarty.const._LANGUAGE}</td>
                                        <td class = "topTitle centerAlign">{$smarty.const._STATUS}</td>
                                    {if !isset($T_CURRENT_USER->coreAccess.configuration) || $T_CURRENT_USER->coreAccess.configuration == 'change'}
                                        <td class = "topTitle centerAlign">{$smarty.const._OPERATIONS}</td>
                                    {/if}
                                    </tr>
                    {foreach name = 'fields_list' key = "key" item = "field" from = $T_PROFILE_FIELDS}
                                    <tr id="row_{$field.name}" class = "{cycle values = "oddRowColor, evenRowColor"} {if !$field.active}deactivatedTableElement{/if}">
                                        <td>
                                    {if !isset($T_CURRENT_USER->coreAccess.configuration) || $T_CURRENT_USER->coreAccess.configuration == 'change'}
                                            <a href = "administrator.php?ctg=user_profile&edit_field={$field.name}" class = "editLink"><span id="column_{$field.name}" {if !$field.active}style="color:red"{/if}>{$field.name}</span></a>
                                    {else}
                                            {$field.name}
                                    {/if}
                                        </td>
                                        <td>{$field.description|eF_truncate:40}</td>
                                        <td>
           {if $field.type == 'text'}{$smarty.const._TEXTBOX}
           {elseif $field.type == 'select'}{$smarty.const._SELECTBOX}
           {elseif $field.type == 'textarea'}{$smarty.const._TEXTAREA}
           {elseif $field.type == 'branchinfo'}{$smarty.const._BRANCHINFORMATION}
           {elseif $field.type == 'date'}{$smarty.const._DATE}
           {else}{$field.type}{/if}</td>
                                        <td>{$field.languages_NAME}</td>
                                        <td class = "centerAlign">
                                            {if $field.active}
                                                <img class = "ajaxHandle" src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._DEACTIVATE}" title = "{$smarty.const._DEACTIVATE}" {if $change_fields}onclick = "activateField(this, '{$field.name}')"{/if}>
                                            {else}
                                                <img class = "ajaxHandle" src = "images/16x16/trafficlight_red.png" alt = "{$smarty.const._ACTIVATE}" title = "{$smarty.const._ACTIVATE}" {if $change_fields}onclick = "activateField(this, '{$field.name}')"{/if}>
                                            {/if}
                                        </td>
                                    {if !isset($T_CURRENT_USER->coreAccess.configuration) || $T_CURRENT_USER->coreAccess.configuration == 'change'}
                                        <td class = "centerAlign">
                                            <a href = "administrator.php?ctg=user_profile&edit_field={$field.name}"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
                                            <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) deleteField(this, '{$field.name}');"/>
                                        </td>
                                    {/if}
                                    </tr>
                    {foreachelse}
                                    <tr class = "defaultRowHeight oddRowColor"><td colspan = "100%" class = "emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
                    {/foreach}
                                </table>
                        {/capture}
                        {eF_template_printBlock title = $smarty.const._CUSTOMIZEUSERSPROFILE data = $smarty.capture.t_fields_list image = '32x32/profile_add.png' help = 'Extend_user_profile'}
                {/if}
            </td></tr>
        {/capture}
