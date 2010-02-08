{*moduleRoles: The user types list*}
    {capture name = "moduleRoles"}
                            <tr><td class = "moduleCell">
                        {if $smarty.get.add_user_type || $smarty.get.edit_user_type}
                                 {capture name='t_new_role_code'}
                                         <table id = "user_type_options">
                                             <tr><td class = "topAlign">
                                                 {$T_USERTYPES_FORM.javascript}
                                                 <form {$T_USERTYPES_FORM.attributes}>
                                                 {$T_USERTYPES_FORM.hidden}
                                                 <table class = "formElements">
                                                     <tr><td class = "labelCell">{$T_USERTYPES_FORM.name.label}:&nbsp;</td>
                                                         <td class = "elementCell">{$T_USERTYPES_FORM.name.html}</td></tr>
                                                     {if $T_USERTYPES_FORM.name.error}<tr><td></td><td class = "formError">{$T_USERTYPES_FORM.name.error}</td></tr>{/if}
                                                     <tr><td class = "labelCell">{$T_USERTYPES_FORM.basic_user_type.label}:&nbsp;</td>
                                                         <td class = "elementCell">{$T_USERTYPES_FORM.basic_user_type.html}</td></tr>
                                                     {if $T_USERTYPES_FORM.basic_user_type.error}<tr><td></td><td class = "formError">{$T_USERTYPES_FORM.basic_user_type.error}</td></tr>{/if}

                                         {foreach name = 'usertype_options' key = 'option' item = 'value' from = $T_USERTYPES_OPTIONS}
                                                     <tr><td class = "labelCell">{$T_USERTYPES_FORM.core_access.$option.label}:&nbsp;</td>
                                                         <td class = "elementCell">{$T_USERTYPES_FORM.core_access.$option.html}</td></tr>
                                                     {if $T_USERTYPES_FORM.core_access.$option.error}<tr><td></td><td class = "formError">{$T_USERTYPES_FORM.core_access.$option.error}</td></tr>{/if}
                                         {/foreach}
                                                     <tr><td colspan = "2">&nbsp;</td></tr>
                                                     <tr><td class = "labelCell">{$smarty.const._SETALLTO}:&nbsp;</td>
                                                         <td class = "elementCell">
                                                           <select id = "set_options_selected" onchange = "$('user_type_options').select('select').each(function(s)  {ldelim}if (s.id != 'basic_user_type') s.options.selectedIndex = $('set_options_selected').options.selectedIndex; {rdelim});">
                                                           <option>{$smarty.const._CHANGE}</option>
                                                           <option>{$smarty.const._VIEW}</option>
                                                           <option>{$smarty.const._HIDE}</option>
                                                          </select>
                                                         </td></tr>
                                                     <tr><td></td>
                                                         <td class = "submitCell">{$T_USERTYPES_FORM.submit_type.html}</td></tr>
                                                 </table>
                                                 </form>
                                             </td></tr>
                                         </table>
                                 {/capture}

                                 {if $smarty.get.edit_user_type}
                                    {eF_template_printBlock title = $smarty.const._OPTIONSUSERTYPEFOR|cat:"&nbsp;<span class = 'innerTableName'>&quot;"|cat:$T_USER_TYPE_NAME|cat:"&quot;</span>" data = $smarty.capture.t_new_role_code image = '32x32/user_types.png'}
                                {else}
                                    {eF_template_printBlock title = $smarty.const._NEWUSERTYPE data = $smarty.capture.t_new_role_code image = '32x32/user_types.png'}
                                {/if}

                        {else}
                            {capture name = 't_roles_code'}
                             <script>var activate = '{$smarty.const._ACTIVATE}';var deactivate = '{$smarty.const._DEACTIVATE}';</script>
                                {if !isset($T_CURRENT_USER->coreAccess.configuration) || $T_CURRENT_USER->coreAccess.configuration == 'change'}
                                   <div class = "headerTools">
                                       <span>
                                           <img src = "images/16x16/add.png" title = "{$smarty.const._NEWUSERTYPE}" alt = "{$smarty.const._NEWUSERTYPE}">
                                           <a href = "administrator.php?ctg=user_types&add_user_type=1" title = "{$smarty.const._NEWUSERTYPE}" >{$smarty.const._NEWUSERTYPE}</a>
                                       </span>
                                   </div>

                                   {assign var = "change_user_types" value = 1}
                                {/if}
                                                    <table style = "width:100%" class = "sortedTable" sortBy = "0">
                                                        <tr class = "topTitle">
                                                            <td class = "topTitle">{$smarty.const._NAME}</td>
                                                            <td class = "topTitle">{$smarty.const._BASICUSERTYPE}</td>
                                                            <td class = "topTitle centerAlign">{$smarty.const._ACTIVE2}</td>
                                                        {if $change_user_types}
                                                            <td class = "topTitle centerAlign">{$smarty.const._OPERATIONS}</td>
                                                        {/if}
                                                        </tr>
                                {foreach name = 'usertype_list' key = 'key' item = 'type' from = $T_USERTYPES_DATA}
                                                        <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                                                            <td>
                                                                <a href = "administrator.php?ctg=user_types&edit_user_type={$type.id}" class = "editLink">{$type.name}</a>
                                                            </td>
                                                            <td>{$T_BASIC_USER_TYPES[$type.basic_user_type]}</td>
                                                            <td class = "centerAlign">
                                                            {if $type.active == 1}
                                                                <img class = "ajaxHandle" src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._DEACTIVATE}" title = "{$smarty.const._DEACTIVATE}" {if $change_user_types}onclick = "activateUserType(this, '{$type.id}')"{/if}>
                                                            {else}
                                                                <img class = "ajaxHandle" src = "images/16x16/trafficlight_red.png" alt = "{$smarty.const._ACTIVATE}" title = "{$smarty.const._ACTIVATE}" {if $change_user_types}onclick = "activateUserType(this, '{$type.id}')"{/if}>
                                                            {/if}
                                                            </td>
                                                        {if $change_user_types}
                                                            <td class = "centerAlign">
                                                                <a href = "administrator.php?ctg=user_types&edit_user_type={$type.id}" class = "editLink"><img src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
                                                                {if $type.id != $T_CURRENT_USER->user.user_types_ID}
                                                                    <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._AREYOUSUREYOUWANTTODELETEUSERTYPE}')) deleteUserType(this, '{$type.id}');"/>
                                                                {else}
                                                                    <img class = "inactiveImage" src = "images/16x16/error_delete.png" title = "{$smarty.const._CANNOTDELETEOWNTYPE}" alt = "{$smarty.const._CANNOTDELETEOWNTYPE}" />
                                                                {/if}
                                                            </td>
                                                        {/if}
                                                        </tr>
                                {foreachelse}
                                                    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
                                {/foreach}
                                                    </table>
                            {/capture}
                            {eF_template_printBlock title = $smarty.const._UPDATEUSERTYPES data = $smarty.capture.t_roles_code image = '32x32/user_types.png'}
                        {/if}
                            </td></tr>
        {/capture}
