

{*moduleUsers: The users functions*}

    {capture name = "moduleUsers"}



             <tr><td class = "moduleCell">
              <script>var activate = '{$smarty.const._ACTIVATE}';var deactivate = '{$smarty.const._DEACTIVATE}';</script>
                    {capture name = 't_users_code'}
                            {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
                                <div class = "headerTools">
                                    <span>
                                        <img src = "images/16x16/add.png" title = "{$smarty.const._NEWUSER}" alt = "{$smarty.const._NEWUSER}">
                                        <a href = "administrator.php?ctg=personal&user={$smarty.session.s_login}&op=profile&add_user=1">{$smarty.const._NEWUSER}</a>
                                    </span>
                                </div>
                                {assign var = "_change_" value = 1}
                            {/if}
<!--ajax:usersTable-->

                                <table style = "width:100%" class = "sortedTable" size = {$T_USERS_SIZE} sortBy = "0" id = "usersTable" useAjax = "1" rowsPerPage = "20" url = "administrator.php?ctg=users&">
                                    <tr class = "topTitle">
                                        <td class = "topTitle" name = "login">{$smarty.const._USER}</td>
                                        <td class = "topTitle" name = "user_type">{$smarty.const._USERTYPE}</td>
                                        <td class = "topTitle centerAlign" name = "groups_num">{$smarty.const._GROUPS}</td>
                                        <td class = "topTitle" name = "last_login">{$smarty.const._LASTLOGIN}</td>
                                        <td class = "topTitle centerAlign" name = "active">{$smarty.const._ACTIVE2}</td>
                                    {if !isset($T_CURRENT_USER->coreAccess.statistics) || $T_CURRENT_USER->coreAccess.statistics != 'hidden'}
                                        <td class = "topTitle centerAlign noSort">{$smarty.const._STATISTICS}</td>
                                    {/if}
                                    {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
                                        <td class = "topTitle centerAlign">{$smarty.const._OPERATIONS}</td>
                                    {/if}
                                    </tr>
                            {foreach name = 'users_list' key = 'key' item = 'user' from = $T_USERS}
                                    <tr id="row_{$user.login}" class = "{cycle values = "oddRowColor, evenRowColor"} {if !$user.active}deactivatedTableElement{/if}">
                                            <td><a href = "{$smarty.server.PHP_SELF}?ctg=personal&user={$user.login}" class = "editLink" {if ($user.pending == 1)}style="color:red;"{/if}><span id="column_{$user.login}" {if !$user.active}style="color:red;"{/if}>#filter:login-{$user.login}#</span></a></td>
                                            <td>{if $user.user_types_ID}{$T_ROLES[$user.user_types_ID]}{else}{$T_ROLES[$user.user_type]}{/if}</td>
                                            <td class = "centerAlign">{$user.groups_num}</td>
                                            <td>{if $user.last_login}#filter:timestamp_time_nosec-{$user.last_login}#{else}{$smarty.const._NEVER}{/if}</td>
                                            <td class = "centerAlign">
           {if !($user.user_type == 'administrator' && $user.user_types_ID == 0 && $T_CURRENT_USER->user.user_type == 'administrator' && $T_CURRENT_USER->user.user_types_ID != 0)}
            {if $user.login != $smarty.session.s_login}
             {if $user.active == 1}
              <img class = "ajaxHandle" src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._DEACTIVATE}" title = "{$smarty.const._DEACTIVATE}" {if $_change_}onclick = "activateUser(this, '{$user.login}')"{/if}>
             {else}
              <img class = "ajaxHandle" src = "images/16x16/trafficlight_red.png" alt = "{$smarty.const._ACTIVATE}" title = "{$smarty.const._ACTIVATE}" {if $_change_}onclick = "activateUser(this, '{$user.login}')"{/if}>
             {/if}
            {else}
             <img class = "inactiveImage" src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._ACTIVE}" title = "{$smarty.const._ACTIVE}">
            {/if}
           {/if}
                                            </td>
                                        {if !isset($T_CURRENT_USER->coreAccess.statistics) || $T_CURRENT_USER->coreAccess.statistics != 'hidden'}
                                            <td class = "centerAlign"><a href = "{$smarty.server.PHP_SELF}?ctg=statistics&option=user&sel_user={$user.login}" title = "{$smarty.const._STATISTICS}"><img src = "images/16x16/reports.png" title = "{$smarty.const._STATISTICS}" alt = "{$smarty.const._STATISTICS}" /></a></td>
                                        {/if}
                                        {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
                                            <td class = "centerAlign">
            {if !($user.user_type == 'administrator' && $user.user_types_ID == 0 && $T_CURRENT_USER->user.user_type == 'administrator' && $T_CURRENT_USER->user.user_types_ID != 0)}
             <a href = "{$smarty.server.PHP_SELF}?ctg=personal&user={$user.login}" class = "editLink"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>&nbsp;
                                                {/if}
            {if !($user.user_type == 'administrator' && $user.user_types_ID == 0 && $T_CURRENT_USER->user.user_type == 'administrator' && $T_CURRENT_USER->user.user_types_ID != 0)}
             {if $smarty.session.s_login != $user.login}







               <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._AREYOUSUREYOUWANTTODELETEUSER}')) deleteUser(this, '{$user.login}')"/>


             {else}
              <img class = "ajaxHandle inactiveImage" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" />
             {/if}
            {/if}
                                            </td>
                                        {/if}
                                    </tr>
                                    {foreachelse}
                                    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
                                    {/foreach}
                                </table>

<!--/ajax:usersTable-->
                 {/capture}
                 {eF_template_printBlock title = $smarty.const._UPDATEUSERS data = $smarty.capture.t_users_code image = '32x32/user.png' help = 'Users'}
                </td></tr>

    {/capture}
