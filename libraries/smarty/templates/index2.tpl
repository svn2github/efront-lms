{include file = "includes/header.tpl"}
{strip}

{*--------------------------------------------Login part--------------------------------------------*}
{if (isset($T_CTG) && $T_CTG == 'login') }
    {capture name = "t_main_table_code"}
    	{$T_LOGIN_FORM.javascript}
        <form {$T_LOGIN_FORM.attributes}>
            {$T_LOGIN_FORM.hidden}
    		<table class = "indexTable">
            	<tr><td class = "indexHeader" colspan = "2">{$smarty.const._EFRONTLOGIN}</td></tr>
                <tr><td colspan = "2">
                        <table class = "indexForm">
                            <tr><td class = "labelCell">{$T_LOGIN_FORM.login.label}:&nbsp;</td>
                                <td class = "elementCell">{$T_LOGIN_FORM.login.html}</td></tr>
                            {if $T_LOGIN_FORM.login.error}<tr><td></td><td class = "formError">{$T_LOGIN_FORM.login.error}</td></tr>{/if}
                            <tr><td class = "labelCell">{$T_LOGIN_FORM.password.label}:&nbsp;</td>
                                <td class = "elementCell">{$T_LOGIN_FORM.password.html}</td></tr>
                            {if $T_LOGIN_FORM.password.error}<tr><td></td><td class = "formError">{$T_LOGIN_FORM.password.error}</td></tr>{/if}
                            <tr><td class = "labelCell">{$T_LOGIN_FORM.remember.label}:&nbsp;</td>
                                <td class = "elementCell">{$T_LOGIN_FORM.remember.html}</td></tr>
                            <tr><td colspan = "2">{$T_LOGIN_FORM.submit_login.html}</td></tr>
                        </table>
                </td></tr>
            {if $T_SHOW_DIRECTORY == 0}
            	<tr><td class = "indexHeader">
            			<img src = "images/24x24/user1_new.png" title = "{$smarty.const._REGISTERANEWACCOUNT}" alt = "{$smarty.const._REGISTERANEWACCOUNT}" />
    				{if $T_EXTERNALLYSIGNUP && !$T_ONLY_LDAP}
                        <a href = "{$smarty.server.PHP_SELF}?ctg=signup{$T_BYPASSLANG}" class = "indexLink">{$smarty.const._REGISTERANEWACCOUNT}</a>
                    {else}
                        <a href = "javascript:void(0)" class = "inactiveLink" title = "{if $T_ONLY_LDAP}{$smarty.const._USELDAPACCOUNT}{else}{$smarty.const._YOUMAYNOTSIGNUPCONTACTADMINISTRATOR}{/if}">{$smarty.const._REGISTERANEWACCOUNT}</a>
                    {/if}
    				</td>
    				<td class = "indexHeader" id = "right">
    					<a href = "{$smarty.server.PHP_SELF}?ctg=reset_pwd{$T_BYPASSLANG}" class = "indexLink">{$smarty.const._PASSWORDLOST}</a>
    					<img src = "images/24x24/keys.png" title = "{$smarty.const._PASSWORDLOST}" alt = "{$smarty.const._PASSWORDLOST}" />
    				</td></tr>
    		{else}
            	<tr><td class = "indexHeader">
    				<img src = "images/24x24/user1_new.png" title = "{$smarty.const._REGISTERANEWACCOUNT} - {$smarty.const._PASSWORDLOST}" alt="{$smarty.const._REGISTERANEWACCOUNT} - {$smarty.const._PASSWORDLOST}" />
                    {if $T_EXTERNALLYSIGNUP && !$T_ONLY_LDAP}
                        <a href = "{$smarty.server.PHP_SELF}?ctg=signup{$T_BYPASSLANG}" class = "indexLink">{$smarty.const._REGISTERANEWACCOUNT}</a>
                    {else}
                        <a href = "#" class = "inactiveLink" title = "{if $T_ONLY_LDAP}{$smarty.const._USELDAPACCOUNT}{else}{$smarty.const._YOUMAYNOTSIGNUPCONTACTADMINISTRATOR}{/if}">{$smarty.const._REGISTERANEWACCOUNT}</a>
                    {/if}
                    <br>(<a href = "{$smarty.server.PHP_SELF}?ctg=reset_pwd{$T_BYPASSLANG}" class = "indexLink">{$smarty.const._PASSWORDLOST}</a>)
    				</td>
    				<td class = "indexHeader" id = "right">
    					<a href = "directory.php" class = "indexLink">{$smarty.const._LESSONSDIRECTORY}</a>
        				<img src = "images/24x24/books.png" title="{$smarty.const._LESSONSDIRECTORY}" alt="{$smarty.const._LESSONSDIRECTORY}" />
        			</td></tr>
    		{/if}
    		</table>
        </form>
    {/capture}
{/if}
{*--------------------------------------------End of Login part--------------------------------------------*}





{*--------------------------------------------Reset password part--------------------------------------------*}

{if (isset($T_CTG) && $T_CTG == 'reset_pwd') }
{capture name = "t_main_table_code"}

    {$T_RESET_PASSWORD_FORM.javascript}
    <table class = "indexTable">
        <tr><td class = "indexHeader">
                <table border = "0" width = "100%">
                    <tr><td style = "width:1%"><img src = "images/32x32/keys.png" title="{$smarty.const._RESETPASSWORD}" alt="{$smarty.const._RESETPASSWORD}"/></td>
                        <td>&nbsp;</td>
                        <td class = "indexHeader">{$smarty.const._RESETPASSWORD}</td></tr>
                </table>
            </td></tr>
        <tr><td align = "center">
            <form {$T_RESET_PASSWORD_FORM.attributes}>
                <div>{$T_RESET_PASSWORD_FORM.hidden}</div>
                <table class = "loginForm">
                    <tr><td class = "labelCell">{$T_RESET_PASSWORD_FORM.login_or_pwd.label}:&nbsp;</td>
                        <td class = "elementCell">{$T_RESET_PASSWORD_FORM.login_or_pwd.html}</td></tr>
                    {if $T_RESET_PASSWORD_FORM.login_or_pwd.error}<tr><td></td><td class = "formError">{$T_RESET_PASSWORD_FORM.login_or_pwd.error}</td></tr>{/if}

                    <tr><td>&nbsp;</td></tr>
                    <tr><td colspan = "2" class = "submitCell centerAlign">
                           {eF_template_printBackButton} {$T_RESET_PASSWORD_FORM.submit_reset_password.html}</td>
            </tr>
                </table>
            </form>
         </td></tr>
     </table>
{/capture}
{/if}
{*--------------------------------------------End of Reset password part--------------------------------------------*}


{*--------------------------------------------Contact form part--------------------------------------------*}
{if (isset($T_CTG) && $T_CTG == 'contact') }
{capture name = "t_main_table_code"}
    {$T_CONTACT_FORM.javascript}
    <table class = "indexTable">
        <tr><td class = "indexHeader">
                <table border = "0" width = "100%">
                    <tr><td style = "width:1%"><img src = "images/32x32/mail2.png" title="{$smarty.const._CONTACT}" alt="{$smarty.const._CONTACT}"/></td>
                        <td>&nbsp;</td>
                        <td class = "indexHeader">{$smarty.const._CONTACT}</td></tr>
                </table>
            </td></tr>
        <tr><td align = "center">
            <form {$T_CONTACT_FORM.attributes}>
                <div>{$T_CONTACT_FORM.hidden}</div>
                <table class = "formElements">
                    <tr><td class = "labelCell">{$T_CONTACT_FORM.email.label}:&nbsp;</td>
                        <td class = "elementCell">{$T_CONTACT_FORM.email.html}</td></tr>
                    {if $T_CONTACT_FORM.email.error}<tr><td></td><td class = "formError">{$T_CONTACT_FORM.email.error}</td></tr>{/if}

                    <tr><td class = "labelCell">{$T_CONTACT_FORM.message_subject.label}:&nbsp;</td>
                        <td class = "elementCell">{$T_CONTACT_FORM.message_subject.html}</td></tr>
                    {if $T_CONTACT_FORM.message_subject.error}<tr><td></td><td class = "formError">{$T_CONTACT_FORM.message_subject.error}</td></tr>{/if}

                    <tr><td class = "labelCell">{$T_CONTACT_FORM.message_body.label}:&nbsp;</td>
                        <td class = "elementCell">{$T_CONTACT_FORM.message_body.html}</td></tr>

                    <tr><td></td><td class = "formRequired">{$T_CONTACT_FORM.requirednote}</td></tr>

                    <tr><td colspan = "2" class = "submitCell" style = "text-align:center">
                             {eF_template_printBackButton}
                            {$T_CONTACT_FORM.submit_contact.html}</td></tr>
                </table>
            </form>
         </td></tr>
     </table>
{/capture}
{/if}
{*--------------------------------------------End of Contact form part--------------------------------------------*}


{*--------------------------------------------Sign up part--------------------------------------------*}
{if (isset($T_CTG) && $T_CTG == 'signup') }

{capture name = "t_main_table_code"}
    {$T_PERSONAL_INFO_FORM.javascript}
    <table class = "indexTable">

        <tr><td class = "indexHeader" >
                <table border = "0" width = "100%">
                    <tr><td style = "width:1%"><img src = "images/32x32/user1_new.png" title="{$smarty.const._REGISTERANEWACCOUNT}" alt="{$smarty.const._REGISTERANEWACCOUNT}" /></td>
                        <td>&nbsp;</td>
                        <td class = "indexHeader">{$smarty.const._REGISTERANEWACCOUNT}</td></tr>
                </table>
            </td></tr>

        <tr><td align = "center">
            <form {$T_PERSONAL_INFO_FORM.attributes}>
                <div>{$T_PERSONAL_INFO_FORM.hidden}</div>
                <table class = "formElements">
                    <tr><td class = "labelCell">{$T_PERSONAL_INFO_FORM.languages_NAME.label}:&nbsp;</td>
                        <td class = "elementCell">{$T_PERSONAL_INFO_FORM.languages_NAME.html}</td></tr>

                    <tr><td colspan = "2" class = "horizontalSeparator"></td></tr>

                    <tr><td class = "labelCell">{$T_PERSONAL_INFO_FORM.login.label}:&nbsp;</td>
                        <td class = "elementCell">{$T_PERSONAL_INFO_FORM.login.html}</td></tr>
                    <tr><td></td><td class = "infoCell">{$smarty.const._ONLYALLOWEDCHARACTERSLOGIN}</td></tr>
                    {if $T_PERSONAL_INFO_FORM.login.error}<tr><td></td><td class = "formError">{$T_PERSONAL_INFO_FORM.login.error}</td></tr>{/if}

                    <tr><td class = "labelCell">{$T_PERSONAL_INFO_FORM.password.label}:&nbsp;</td>
                        <td class = "elementCell">{$T_PERSONAL_INFO_FORM.password.html}</td></tr>
                    <tr><td></td><td class = "infoCell">{$smarty.const._PASSWORDMUSTBE6CHARACTERS}</td></tr>
                    {if $T_PERSONAL_INFO_FORM.password.error}<tr><td></td><td class = "formError">{$T_PERSONAL_INFO_FORM.password.error}</td></tr>{/if}

                    <tr><td class = "labelCell">{$T_PERSONAL_INFO_FORM.passrepeat.label}:&nbsp;</td>
                        <td class = "elementCell">{$T_PERSONAL_INFO_FORM.passrepeat.html}</td></tr>
                    {if $T_PERSONAL_INFO_FORM.passrepeat.error}<tr><td></td><td class = "formError">{$T_PERSONAL_INFO_FORM.passrepeat.error}</td></tr>{/if}

                    <tr><td colspan = "2" class = "horizontalSeparator"></td></tr>

                    <tr><td class = "labelCell">{$T_PERSONAL_INFO_FORM.email.label}:&nbsp;</td>
                        <td class = "elementCell">{$T_PERSONAL_INFO_FORM.email.html}</td></tr>
                    {if $T_PERSONAL_INFO_FORM.email.error}<tr><td></td><td class = "formError">{$T_PERSONAL_INFO_FORM.email.error}</td></tr>{/if}

                    <tr><td class = "labelCell">{$T_PERSONAL_INFO_FORM.firstName.label}:&nbsp;</td>
                        <td class = "elementCell">{$T_PERSONAL_INFO_FORM.firstName.html}</td></tr>

                    <tr><td class = "labelCell">{$T_PERSONAL_INFO_FORM.lastName.label}:&nbsp;</td>
                        <td class = "elementCell">{$T_PERSONAL_INFO_FORM.lastName.html}</td></tr>

        {foreach name = 'profile_fields' key = key item = item from = $T_USER_PROFILE_FIELDS }
                    <tr><td class = "labelCell">{$T_PERSONAL_INFO_FORM.$item.label}:&nbsp;</td>
                        <td class = "elementCell">{$T_PERSONAL_INFO_FORM.$item.html}</td></tr>
                    {if $T_PERSONAL_INFO_FORM.$item.error}<tr><td></td><td class = "formError">{$T_PERSONAL_INFO_FORM.$item.error}</td></tr>{/if}
        {/foreach}

                    <tr><td class = "labelCell">{$T_PERSONAL_INFO_FORM.comments.label}:&nbsp;</td>
                        <td class = "elementCell">{$T_PERSONAL_INFO_FORM.comments.html}</td></tr>

                    <tr><td colspan = "2" class = "horizontalSeparator"></td></tr>

{*
                    <tr><td colspan = "2">
                            <table style = "margin-left:auto;margin-right:auto;">
        {foreach key = direction item = lessons from=$T_PERSONAL_INFO_FORM.lessons }
                                <tr><td colspan = "2" class = "smallHeader horizontalSeparator">{$smarty.const._DIRECTION}: {$direction}</td></tr>
            {foreach key = lesson_id item = lesson from = $lessons}
                                <tr><td class = "labelCell">{$lesson.label}</td>
                                    <td>{$lesson.html}</td><td><a href = "javascript:void(0)"><img src = "images/16x16/about.png" title = "{$smarty.const._PRICE}: {$T_LESSONS_INFO.$lesson_id.price}{if $T_LESSONS_INFO.$lesson_id.info}, {$smarty.const._LESSONINFO}: {$T_LESSONS_INFO.$lesson_id.info}{/if}" alt = "{$T_LESSONS_INFO.$lesson_id.price}" border = "0"/></a></td></tr>
            {/foreach}
        {/foreach}
                            </table>
                        </td></tr>

                    <tr><td colspan = "2" class = "horizontalSeparator"></td></tr>

                    <tr><td class = "labelCell">{$T_PERSONAL_INFO_FORM.terms.label}:&nbsp;</td>
                        <td class = "elementCell">{$T_PERSONAL_INFO_FORM.terms.html}</td></tr>

                    <tr><td class = "labelCell">{$T_PERSONAL_INFO_FORM.accept_terms.label}:&nbsp;</td>
                        <td class = "elementCell">{$T_PERSONAL_INFO_FORM.accept_terms.html}</td></tr>
                    {if $T_PERSONAL_INFO_FORM.accept_terms.error}<tr><td></td><td class = "formError">{$T_PERSONAL_INFO_FORM.accept_terms.error}</td></tr>{/if}
*}
                    <tr><td></td><td class = "formRequired">{$T_PERSONAL_INFO_FORM.requirednote}</td></tr>

                    <tr><td colspan = "2" class = "submitCell centerAlign"><center><br>
 {eF_template_printBackButton} {$T_PERSONAL_INFO_FORM.submit_register.html} </td></tr>
                </table>
            </form>
            </td></tr>
    </table>
{/capture}
{/if}
{*--------------------------------------------End of sign up part--------------------------------------------*}


<table style = "width:100%;height:100%;padding:0px;margin:0px">
    <tr><td style = "vertical-align:top">
            {if ($T_MESSAGE)}
                    {eF_template_printMessage message=$T_MESSAGE type=$T_MESSAGE_TYPE}
            {/if}
            {if $smarty.get.message}
                    {eF_template_printMessage message=$smarty.get.message type=$smarty.get.message_type}        {*Display Message passed through get, if any*}
            {/if}
        </td>
    </tr>
    <tr><td style = "height:80%">
        {$smarty.capture.t_main_table_code}
    </td></tr>
    <tr><td style = "vertical-align:bottom">
            <table class = "indexFoot" style = "width:100%;">
                <tr><td style = "white-space:nowrap;text-align:center">
 <a href = ""><img src = "images/{$T_LOGO}" title="{$smarty.const._EFRONT}" alt="{$smarty.const._EFRONT}" style = "vertical-align:middle; border:0;"
 {if isset($T_NEWWIDTH)} width = "{$T_NEWWIDTH}" height = "{$T_NEWHEIGHT}"{/if}
 /></a>
 						<br/>{if $T_CONFIGURATION.site_name}{$T_CONFIGURATION.site_name}{/if}<br>
                        <a href = "http://www.efrontlearning.net">{$smarty.const._EFRONT}</a> &bull; <b> version {$smarty.const.G_VERSION_NUM}</b> &bull;
                        <a href = "{$smarty.server.PHP_SELF}?ctg=contact{$T_BYPASSLANG}"> {$smarty.const._CONTACTUS}</a>
                    </td></tr>
            </table>
   </td>
   </tr>
</table>
</body>
</html>
{/strip}