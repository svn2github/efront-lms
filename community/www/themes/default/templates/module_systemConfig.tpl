<script>
<!--
{literal}
function setSecure(el) {
    var server = document.getElementById("ldap_server");
    var port   = document.getElementById("ldap_port");

    if (el.checked) {
        server.value = "ldaps://";
        port.value = "636";
    } else {
        server.value = "";
        port.value = "389";
    }
}
{/literal}
//-->
</script>

{capture name="view_config"}

<div class="tabber">
    <div class="tabbertab {if ($smarty.get.tab == 'vars')}tabbertabdefault{/if}">
        <h3>{$smarty.const._CONFIGURATIONVARIABLES}</h3>
        {capture name="system_vars"}
        {$T_SYSTEM_VARIABLES_FORM.javascript}
        <form {$T_SYSTEM_VARIABLES_FORM.attributes}>
            {$T_SYSTEM_VARIABLES_FORM.hidden}
            <table class = "formElements">
                <tr><td class = "labelCell">{$smarty.const._ADMINEMAIL}:&nbsp;</td>
                    <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.system_email.html}</td></tr>
                {if $T_SYSTEM_VARIABLES_FORM.system_email.error}<tr><td></td><td class = "formError">{$T_SYSTEM_VARIABLES_FORM.system_email.error}</td></tr>{/if}
                <tr><td class = "labelCell">{$smarty.const._MAXFILESIZE}:&nbsp;</td>
                    <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.max_file_size.html}</td></tr>
                {if $T_SYSTEM_VARIABLES_FORM.max_file_size.error}<tr><td></td><td class = "formError">{$T_SYSTEM_VARIABLES_FORM.max_file_size.error}</td></tr>{/if}
                <tr><td class = "labelCell">{$smarty.const._ALLOWEDIPS}<span style = "vertical-align:super;font-size:8px">1</span>:&nbsp;</td>
                    <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.ip_white_list.html}</td></tr>
                {if $T_SYSTEM_VARIABLES_FORM.ip_white_list.error}<tr><td></td><td class = "formError">{$T_SYSTEM_VARIABLES_FORM.ip_white_list.error}</td></tr>{/if}
                <tr><td class = "labelCell">{$smarty.const._DISALLOWEDIPS}<span style = "vertical-align:super;font-size:8px">1</span>:&nbsp;</td>
                    <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.ip_black_list.html}</td></tr>
                {if $T_SYSTEM_VARIABLES_FORM.ip_black_list.error}<tr><td></td><td class = "formError">{$T_SYSTEM_VARIABLES_FORM.ip_black_list.error}</td></tr>{/if}
                <tr><td class = "labelCell">{$smarty.const._ALLOWEDEXTENSIONS}<span style = "vertical-align:super;font-size:8px">2</span>:&nbsp;</td>
                    <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.file_white_list.html}</td></tr>
                {if $T_SYSTEM_VARIABLES_FORM.file_white_list.error}<tr><td></td><td class = "formError">{$T_SYSTEM_VARIABLES_FORM.file_white_list.error}</td></tr>{/if}
                <tr><td class = "labelCell">{$smarty.const._DISALLOWEDEXTENSIONS}<span style = "vertical-align:super;font-size:8px">2</span>:&nbsp;</td>
                    <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.file_black_list.html}</td></tr>
                {if $T_SYSTEM_VARIABLES_FORM.file_black_list.error}<tr><td></td><td class = "formError">{$T_SYSTEM_VARIABLES_FORM.file_black_list.error}</td></tr>{/if}
                <tr><td class = "labelCell">{$smarty.const._DEFAULTLANGUAGE}:&nbsp;</td>
                    <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.default_language.html}</td></tr>
                {if $T_SYSTEM_VARIABLES_FORM.default_language.error}<tr><td></td><td class = "formError">{$T_SYSTEM_VARIABLES_FORM.default_language.error}</td></tr>{/if}
                <tr><td class = "labelCell">{$smarty.const._EXTERNALLYSIGNUP}:&nbsp;</td>
                    <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.signup.html}</td></tr>
                <tr><td class = "labelCell">{$smarty.const._AUTOMATICUSERACTIVATION}:&nbsp;</td>
                    <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.activation.html}</td></tr>
                <tr><td class = "labelCell">{$smarty.const._MAILUSERACTIVATION}:&nbsp;</td>
                    <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.mail_activation.html}</td></tr>
                <tr><td class = "labelCell">{$smarty.const._ONLYONELANGUAGE}:&nbsp;</td>
                    <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.onelanguage.html}</td></tr>
                <tr><td class = "labelCell">{$smarty.const._SHOWFOOTER}:&nbsp;</td>
                    <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.show_footer.html}</td></tr>
                <tr><td class = "labelCell">{$smarty.const._ENABLEDAPI}:&nbsp;</td>
                    <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.api.html}</td></tr>
                <tr><td colspan = "2">&nbsp;</td></tr>
                <tr><td></td><td class = "submitCell">{$T_SYSTEM_VARIABLES_FORM.submit_system_variables.html}</td></tr>
                <tr><td width="50%" colspan="2" align="center"class="horizontalSeparator">&nbsp;</td></tr>
                <tr><td align="left" colspan = "2"><span style= "vertical-align:super;font-size:8px;">1</span>{$smarty.const._COMMASEPARATEDLISTASTERISKEXAMPLE}</td></tr>
                <tr><td align="left" colspan = "2"><span style = "vertical-align:super;font-size:8px;">2</span>{$smarty.const._COMMASEPARATEDLISTASTERISKEXTENSIONEXAMPLE}</td></tr>
                <tr><td>&nbsp;</td></tr>
                <tr><td align="left" colspan = "2"><b>{$smarty.const._NOTE}: </b>{$smarty.const._DENIALTAKESPRECEDENCE}</td></tr>
            </table>
        </form>
        {/capture}
        {eF_template_printBlock title=$smarty.const._CONFIGURATIONVARIABLES data=$smarty.capture.system_vars image='32x32/tests.png'}
    </div>
    
    <div class="tabbertab {if ($smarty.get.tab == 'ldap')}tabbertabdefault{/if}">
        <h3>{$smarty.const._LDAPCONFIGURATION}</h3>
        {capture name = "ldap_vars"}
        {$T_LDAP_VARIABLES_FORM.javascript}
        <form {$T_LDAP_VARIABLES_FORM.attributes}>
            {$T_LDAP_VARIABLES_FORM.hidden}
            <table class = "formElements">
                <tr><td class = "labelCell">{$smarty.const._ACTIVATELDAP}:&nbsp;</td>
                    <td class = "elementCell">{$T_LDAP_VARIABLES_FORM.activate_ldap.html}</td></tr>
                <tr><td class = "labelCell">{$smarty.const._SUPPORTONLYLDAP}:&nbsp;</td>
                    <td class = "elementCell">{$T_LDAP_VARIABLES_FORM.only_ldap.html}</td></tr>
                <tr><td colspan = "2">&nbsp;</td></tr>
{*                <tr><td class = "labelCell">{$smarty.const._USESSL}:&nbsp;</td>
                    <td class = "elementCell">{$T_LDAP_VARIABLES_FORM.ldap_ssl.html}</td></tr>
                {if $T_LDAP_VARIABLES_FORM.ldap_ssl.error}<tr><td></td><td class = "formError">{$T_LDAP_VARIABLES_FORM.ldap_ssl.error}</td></tr>{/if}
*}
                <tr><td class = "labelCell">{$smarty.const._LDAPSERVER}:&nbsp;</td>
                    <td class = "elementCell">{$T_LDAP_VARIABLES_FORM.ldap_server.html}</td></tr>
                {if $T_LDAP_VARIABLES_FORM.ldap_server.error}<tr><td></td><td class = "formError">{$T_LDAP_VARIABLES_FORM.ldap_server.error}</td></tr>{/if}
                <tr><td class = "labelCell">{$smarty.const._LDAPPORT}:&nbsp;</td>
                    <td class = "elementCell">{$T_LDAP_VARIABLES_FORM.ldap_port.html}</td></tr>
                {if $T_LDAP_VARIABLES_FORM.ldap_port.error}<tr><td></td><td class = "formError">{$T_LDAP_VARIABLES_FORM.ldap_port.error}</td></tr>{/if}
                <tr><td class = "labelCell">{$smarty.const._LDAPBINDDN}:&nbsp;</td>
                    <td class = "elementCell">{$T_LDAP_VARIABLES_FORM.ldap_binddn.html}</td></tr>
                {if $T_LDAP_VARIABLES_FORM.ldap_binddn.error}<tr><td></td><td class = "formError">{$T_LDAP_VARIABLES_FORM.ldap_binddn.error}</td></tr>{/if}
                <tr><td class = "labelCell">{$smarty.const._LDAPPASSWORD}:&nbsp;</td>
                    <td class = "elementCell">{$T_LDAP_VARIABLES_FORM.ldap_password.html}</td></tr>
                {if $T_LDAP_VARIABLES_FORM.ldap_password.error}<tr><td></td><td class = "formError">{$T_LDAP_VARIABLES_FORM.ldap_password.error}</td></tr>{/if}
                <tr><td class = "labelCell">{$smarty.const._LDAPBASEDN}:&nbsp;</td>
                    <td class = "elementCell">{$T_LDAP_VARIABLES_FORM.ldap_basedn.html}</td></tr>
                {if $T_LDAP_VARIABLES_FORM.ldap_basedn.error}<tr><td></td><td class = "formError">{$T_LDAP_VARIABLES_FORM.ldap_basedn.error}</td></tr>{/if}
                <tr><td class = "labelCell">{$smarty.const._LDAPPROTOCOLVERSION}:&nbsp;</td>
                    <td class = "elementCell">{$T_LDAP_VARIABLES_FORM.ldap_protocol.html}</td></tr>
                {if $T_LDAP_VARIABLES_FORM.ldap_protocol.error}<tr><td></td><td class = "formError">{$T_LDAP_VARIABLES_FORM.ldap_protocol.error}</td></tr>{/if}
                <tr><td colspan = "2">&nbsp;</td></tr>
                <tr><td class = "labelCell">{$smarty.const._LOGINATTRIBUTE}:&nbsp;</td>
                    <td class = "elementCell">{$T_LDAP_VARIABLES_FORM.ldap_uid.html}</td></tr>
                {if $T_LDAP_VARIABLES_FORM.ldap_uid.error}<tr><td></td><td class = "formError">{$T_LDAP_VARIABLES_FORM.ldap_uid.error}</td></tr>{/if}
                <tr><td class = "labelCell">{$smarty.const._LDAPCOMMONNAME}:&nbsp;</td>
                    <td class = "elementCell">{$T_LDAP_VARIABLES_FORM.ldap_cn.html}</td></tr>
                {if $T_LDAP_VARIABLES_FORM.ldap_cn.error}<tr><td></td><td class = "formError">{$T_LDAP_VARIABLES_FORM.ldap_cn.error}</td></tr>{/if}
                <tr><td class = "labelCell">{$smarty.const._LDAPADDRESS}:&nbsp;</td>
                    <td class = "elementCell">{$T_LDAP_VARIABLES_FORM.ldap_postaladdress.html}</td></tr>
                {if $T_LDAP_VARIABLES_FORM.ldap_postaladdress.error}<tr><td></td><td class = "formError">{$T_LDAP_VARIABLES_FORM.ldap_postaladdress.error}</td></tr>{/if}
                <tr><td class = "labelCell">{$smarty.const._LDAPLOCALITY}:&nbsp;</td>
                    <td class = "elementCell">{$T_LDAP_VARIABLES_FORM.ldap_l.html}</td></tr>
                {if $T_LDAP_VARIABLES_FORM.ldap_l.error}<tr><td></td><td class = "formError">{$T_LDAP_VARIABLES_FORM.ldap_l.error}</td></tr>{/if}
                <tr><td class = "labelCell">{$smarty.const._LDAPTELEPHONENUMBER}:&nbsp;</td>
                    <td class = "elementCell">{$T_LDAP_VARIABLES_FORM.ldap_telephonenumber.html}</td></tr>
                {if $T_LDAP_VARIABLES_FORM.ldap_telephonenumber.error}<tr><td></td><td class = "formError">{$T_LDAP_VARIABLES_FORM.ldap_telephonenumber.error}</td></tr>{/if}
                <tr><td class = "labelCell">{$smarty.const._LDAPMAIL}:&nbsp;</td>
                    <td class = "elementCell">{$T_LDAP_VARIABLES_FORM.ldap_mail.html}</td></tr>
                {if $T_LDAP_VARIABLES_FORM.ldap_mail.error}<tr><td></td><td class = "formError">{$T_LDAP_VARIABLES_FORM.ldap_mail.error}</td></tr>{/if}
                <tr><td class = "labelCell">{$smarty.const._LDAPLANGUAGE}:&nbsp;</td>
                    <td class = "elementCell">{$T_LDAP_VARIABLES_FORM.ldap_preferredlanguage.html}</td></tr>
                {if $T_LDAP_VARIABLES_FORM.ldap_preferredlanguage.error}<tr><td></td><td class = "formError">{$T_LDAP_VARIABLES_FORM.ldap_preferredlanguage.error}</td></tr>{/if}
                <tr><td colspan = "2">&nbsp;</td></tr>
                <tr><td></td><td class = "submitCell">{$T_LDAP_VARIABLES_FORM.check_ldap.html}&nbsp;{$T_LDAP_VARIABLES_FORM.submit_ldap_variables.html}</td></tr>
            </table>
        </form>
        {/capture}
        {eF_template_printBlock title=$smarty.const._LDAPVARIABLES data=$smarty.capture.ldap_vars image='32x32/directory.png'}
    </div>
    
    <div class="tabbertab {if ($smarty.get.tab == 'smtp')}tabbertabdefault{/if}">
        {if ($smarty.get.email_conf == '1')}
            {eF_template_printMessage message=$smarty.const._SMTPCONFIGURATIONARECORRECT type='success'}
        {elseif ($smarty.get.email_conf == '-1')}
            {eF_template_printMessage message=$smarty.const._SMTPCONFIGURATIONERROR type='failure'}
        {else}
        {/if}
        <h3>{$smarty.const._EMAILCONFIGURATIONS}</h3>
        {capture name = "ldap_vars"}
        {$T_SMTP_VARIABLES_FORM.javascript}
        <form {$T_SMTP_VARIABLES_FORM.attributes}>
            {$T_SMTP_VARIABLES_FORM.hidden}
            <table class = "formElements">
                <tr><td class = "labelCell">{$smarty.const._SMTPSERVER}:&nbsp;</td>
                    <td class = "elementCell">{$T_SMTP_VARIABLES_FORM.smtp_host.html}</td></tr>
                {if $T_SMTP_VARIABLES_FORM.smtp_host.error}<tr><td></td><td class = "formError">{$T_SMTP_VARIABLES_FORM.smtp_host.error}</td></tr>{/if}
                <tr><td class = "labelCell">{$smarty.const._SMTPPORT}:&nbsp;</td>
                    <td class = "elementCell">{$T_SMTP_VARIABLES_FORM.smtp_port.html}</td></tr>
                {if $T_SMTP_VARIABLES_FORM.smtp_port.error}<tr><td></td><td class = "formError">{$T_SMTP_VARIABLES_FORM.smtp_port.error}</td></tr>{/if}
                <tr><td class = "labelCell">{$smarty.const._SMTPUSER}:&nbsp;</td>
                    <td class = "elementCell">{$T_SMTP_VARIABLES_FORM.smtp_user.html}</td></tr>
                {if $T_SMTP_VARIABLES_FORM.smtp_user.error}<tr><td></td><td class = "formError">{$T_SMTP_VARIABLES_FORM.smtp_user.error}</td></tr>{/if}
                <tr><td class = "labelCell">{$smarty.const._SMTPPASSWORD}:&nbsp;</td>
                    <td class = "elementCell">{$T_SMTP_VARIABLES_FORM.smtp_pass.html}</td></tr>
                {if $T_SMTP_VARIABLES_FORM.smtp_pass.error}<tr><td></td><td class = "formError">{$T_SMTP_VARIABLES_FORM.smtp_pass.error}</td></tr>{/if}
{*                <tr><td class = "labelCell">{$smarty.const._USESSL}:&nbsp;</td>
                    <td class = "elementCell">{$T_SMTP_VARIABLES_FORM.smtp_ssl.html}</td></tr>*}
                <tr><td class = "labelCell">{$smarty.const._SMTPAUTH}:&nbsp;</td>
                    <td class = "elementCell">{$T_SMTP_VARIABLES_FORM.smtp_auth.html}</td></tr>
                <tr><td colspan = "2">&nbsp;</td></tr>
                <tr><td></td><td class = "submitCell">{$T_SMTP_VARIABLES_FORM.check_smtp.html}&nbsp;{$T_SMTP_VARIABLES_FORM.submit_smtp_variables.html}</td></tr>

            </table>
        </form>
        {/capture}
        
        {eF_template_printBlock title=$smarty.const._SMTPSERVERCONFIGURATIONS data=$smarty.capture.ldap_vars image='32x32/mail.png'}
    </div>
</div>
{/capture}
{eF_template_printBlock title = $smarty.const._CONFIGURATIONVARIABLES data = $smarty.capture.view_config image='32x32/edit.png'}
