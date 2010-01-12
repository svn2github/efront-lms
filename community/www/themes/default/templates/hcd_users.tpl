
    {if !isset($smarty.get.print_preview) && !isset($smarty.get.print)}
    {assign var = "category" value = 'company'}
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd">'|cat:$smarty.const._ORGANIZATION|cat:'</a>'}
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=users">'|cat:$smarty.const._EMPLOYEES|cat:'</a>'}

    {/if}

    {if $smarty.get.add_user || $smarty.get.edit_user}
    {*moduleNewUser: Create a new user*}
            {capture name = "moduleNewUser"}
                                <tr><td class = "moduleCell">
                                {if !isset($smarty.get.print_preview) && !isset($smarty.get.print)}
                                    {if $smarty.get.edit_user != ""}
                                        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=users&edit_user='|cat:$smarty.get.edit_user|cat:'">'|cat:$smarty.const._EDITUSER|cat:'</a>'}
                                    {else}
                                        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=users&add_user=1">'|cat:$smarty.const._NEWUSER|cat:'</a>'}
                                    {/if}
                                {/if}
                                        <table width = "100%">
                                            <tr><td class = "topAlign" width = "50%">
                                                    {if isset($T_PERSONAL)}
                                                        {include file = "includes/personal.tpl"}
                                                    {/if}
                                                </td>
                                            </tr>
                                        </table>
                                </td></tr>
        {/capture}
    {else}
{*moduleUsers: The users functions*}
    {capture name = "moduleUsers"}



                            <tr><td class = "moduleCell">
                                    {capture name = 't_users_code'}
                                                    <table border = "0" >
                                                        <tr><td>
                                                            <a href="administrator.php?ctg=users&add_user=1"><img src="images/16x16/add.png" title="{$smarty.const._NEWUSER}" alt="{$smarty.const._NEWUSER}"/ border="0"></a></td><td><a href="administrator.php?ctg=users&add_user=1">{$smarty.const._NEWUSER}</a>
                                                        </td></tr>
                                                    </table>
                                                    <table border = "0" width = "100%" class = "sortedTable" sortBy = "0">
                                                    <tr class = "topTitle">
                                                        <td class = "topTitle">{$smarty.const._USER}</td>
                                                        <td class = "topTitle">{$smarty.const._USERTYPE}</td>
                                                        <td class = "topTitle">{$smarty.const._LANGUAGE}</td>
                                                        <td class = "topTitle" align="center">{$smarty.const._LESSONSNUMBER}</td>
                                                        <td class = "topTitle" align="center">{$smarty.const._ACTIVE2}</td>
                                                        <td class = "topTitle noSort" align="center">{$smarty.const._STATISTICS}</td>
                                                        <td class = "topTitle" align="center">{$smarty.const._OPERATIONS}</td>
                                                    </tr>
                                            {foreach name = 'users_list' key = 'key' item = 'user' from = $T_USERS}
                                                        <tr class = "{cycle values = "oddRowColor, evenRowColor"}">

                                                            <td>
                                                            {if ($user.pending == 1)}
                                                                 <a href = "administrator.php?ctg=users&edit_user={$user.login}" class = "editLink" style="color:red;">#filter:login-{$user.login}#</a>
                                                            {elseif ($user.active == 1)}
                                                                 <a href = "administrator.php?ctg=users&edit_user={$user.login}" class = "editLink">#filter:login-{$user.login}#</a>
                                                            {else}
                                                                #filter:login-{$user.login}#
                                                            {/if}
                                                            </td>
                                                            <td>{$user.user_type}</td>
                                                            <td>{$user.languages_NAME}</td>
                                                            <td align="center">{$user.lessons_num}</td>


                                                            <td align = "center">
                                                           {if $user.user_type != $smarty.const._ADMINISTRATOR}
                                                                {if $user.active == 1}
                                                                        <a href="administrator.php?ctg=users&deactivate_user={$user.login}"><img src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._DEACTIVATE}" title = "{$smarty.const._DEACTIVATE}" border = "0"></a>
                                                                {else}
                                                                        <a href="administrator.php?ctg=users&activate_user={$user.login}"><img src = "images/16x16/trafficlight_red.png" alt = "{$smarty.const._ACTIVATE}" title = "{$smarty.const._ACTIVATE}" border = "0"></a>
                                                                {/if}
                                                            {else}
                                                                <img src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._ACTIVE}" title = "{$smarty.const._ACTIVE}" border = "0">
                                                            {/if}
                                                            </td>
                                                            <td align="center"><a href="administrator.php?ctg=statistics&option=user&sel_user={$user.login}"><img border = "0" src = "images/16x16/reports.png" title = "{$smarty.const._STATISTICS}" alt = "{$smarty.const._STATISTICS}" /></a></td>
                                                            <td align = "center">
                                                                <table><tr><td width="45%">
                                                                    {if $user.active == 1}
                                                                        <a href = "administrator.php?ctg=users&edit_user={$user.login}" class = "editLink"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
                                                                    {else}
                                                                        <img border = "0" class = "inactiveImage" src = "images/16x16/edit.png" title = "{$smarty.const._UNPRIVILEGEDATTEMPT}" alt = "{$smarty.const._UNPRIVILEGEDATTEMPT}" />
                                                                     {/if}
                                                                </td><td></td><td width="45%">
                                                                {if $user.user_type != "administrator"}
                                                                    <a href = "administrator.php?ctg=users&op=users_data&delete_user={$user.login}" onclick = "return confirm('{$smarty.const._AREYOUSUREYOUWANTTODELETEUSER}')" class = "deleteLink"><img border = "0" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a>
                                                                    {else}
                                                                       <img border = "0" class = "inactiveImage" src = "images/16x16/error_delete.png" title = "{$smarty.const._UNPRIVILEGEDATTEMPT}" alt = "{$smarty.const._UNPRIVILEGEDATTEMPT}" />
                                                                     {/if}
                                                                </td></tr></table>
                                                            </td>

                                                    </tr>

                                            {/foreach}
                                                    </table>
                                            {/capture}
                                            {eF_template_printBlock title = $smarty.const._UPDATEUSERS data = $smarty.capture.t_users_code image = '32x32/user.png'}
                            </td></tr>

    {/capture}

    {/if}
