        {*moduleCustomizeUsersProfile: The users profile customization page*}
        {capture name = "moduleCustomizeUsersProfile"}
            <tr><td class="moduleCell">
                {if $smarty.get.add_field || $smarty.get.edit_field}
                            {capture name = 'field_form_code'}
                    {eF_template_printForm form = $T_FORM}
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
<!--ajax:fieldsTable-->
        <table style = "width:100%" class = "sortedTable" size = "{$T_USERS_SIZE}" id = "fieldsTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=user_profile&">
                                    <tr class = "topTitle defaultRowHeight">
                                        <td class = "topTitle" name = "name">{$smarty.const._FIELDNAME}</td>
                                        <td class = "topTitle" name = "description">{$smarty.const._DESCRIPTION}</td>
                                        <td class = "topTitle" name = "type">{$smarty.const._TYPE}</td>
                                        <td class = "topTitle" name = "language">{$smarty.const._LANGUAGE}</td>
                                        <td class = "topTitle centerAlign" name = "active">{$smarty.const._STATUS}</td>
                                        <td class = "topTitle centerAlign" name = "field_order">{$smarty.const._ORDER}</td>
                                    {if !isset($T_CURRENT_USER->coreAccess.configuration) || $T_CURRENT_USER->coreAccess.configuration == 'change'}
                                        <td class = "topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
                                    {/if}
                                    </tr>
                    {foreach name = 'fields_list' key = "key" item = "field" from = $T_DATA_SOURCE}
                                    <tr id="row_{$field.name}" class = "{cycle values = "oddRowColor, evenRowColor"} {if !$field.active}deactivatedTableElement{/if}">
                                        <td>
                                    {if !isset($T_CURRENT_USER->coreAccess.configuration) || $T_CURRENT_USER->coreAccess.configuration == 'change'}
                                            <a href = "administrator.php?ctg=user_profile&edit_field={$field.name}&type={$field.type}" class = "editLink"><span id="column_{$field.name}" {if !$field.active}style="color:red"{/if}>{$field.name}</span></a>
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
           {elseif $field.type == 'groupinfo'}{$smarty.const._GROUPINFORMATION}
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
                                        <td class = "centerAlign">
                                         {if $smarty.foreach.fields_list.total > 1 && $field.field_order > 0}
                                            <img class = "ajaxHandle" src = "images/16x16/navigate_up.png" title = "{$smarty.const._UP}" alt = "{$smarty.const._UP}" onclick = "moveField(this, '{$field.name}', 'up');"/>
                                            {/if}
                                            {if $smarty.foreach.fields_list.total > 1 && $field.field_order < $smarty.foreach.fields_list.total-1}
                                            <img class = "ajaxHandle" src = "images/16x16/navigate_down.png" title = "{$smarty.const._DOWN}" alt = "{$smarty.const._DOWN}" onclick = "moveField(this, '{$field.name}', 'down');"/>
                                            {/if}
                                        </td>
                                    {if !isset($T_CURRENT_USER->coreAccess.configuration) || $T_CURRENT_USER->coreAccess.configuration == 'change'}
                                        <td class = "centerAlign">
                                            <a href = "administrator.php?ctg=user_profile&edit_field={$field.name}&type={$field.type}"><img class = "handle" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
                                            <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) deleteField(this, '{$field.name}');"/>
                                        </td>
                                    {/if}
                                    </tr>
                    {foreachelse}
                                    <tr class = "defaultRowHeight oddRowColor"><td colspan = "100%" class = "emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
                    {/foreach}
                                </table>
<!--/ajax:fieldsTable-->
                        {/capture}
                        {eF_template_printBlock title = $smarty.const._CUSTOMIZEUSERSPROFILE data = $smarty.capture.t_fields_list image = '32x32/profile_add.png' help = 'Extend_user_profile'}
                {/if}
            </td></tr>
        {/capture}
