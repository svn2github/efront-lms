{* smarty file for emails.php *}
<script>
{literal}
<!--
function eF_js_selectRecipients(recipient) {
    {/literal}
    {literal}
    switch (recipient) {
        case 'all_users':
        case 'active_users':
        document.getElementById('lesson_recipients').disabled = 'disabled';
        document.getElementById('user_type_recipients').disabled = 'disabled';
        document.getElementById('user_recipients').disabled = 'disabled';
        break;
        case 'specific_lesson':
        document.getElementById('lesson_recipients').disabled = '';
        document.getElementById('user_type_recipients').disabled = 'disabled';
        document.getElementById('user_recipients').disabled = 'disabled';
        break;
        case 'specific_type':
        document.getElementById('lesson_recipients').disabled = 'disabled';
        document.getElementById('user_type_recipients').disabled = '';
        document.getElementById('user_recipients').disabled = 'disabled';
        break;
        case 'specific_user':
        document.getElementById('lesson_recipients').disabled = 'disabled';
        document.getElementById('user_type_recipients').disabled = 'disabled';
        document.getElementById('user_recipients').disabled = '';
        break;
        {/literal}
        {literal}
    }
}
//-->
{/literal}
</script>
{**moduleEmail: The email page*}
{capture name = "moduleEmail"}
        <tr><td class = "moduleCell">
                {$T_EMAIL_FORM.javascript}
                <form {$T_EMAIL_FORM.attributes}>
                {$T_EMAIL_FORM.hidden}
        {capture name = 't_recipients_code'}
                        <table class = "formElements">
                                 {* Regular eFront selects *}
                                 <tr><td>{$T_EMAIL_FORM.recipients.all_users.html} </td><td>{$smarty.const._ALLSYSTEMUSERS}</td></tr>
                                 <tr><td>{$T_EMAIL_FORM.recipients.active_users.html} </td><td>{$smarty.const._ALLACTIVESYSTEMUSERS}</td></tr>
                                 <tr {if !$T_LESSONS}style = "display:none"{/if}><td>{$T_EMAIL_FORM.recipients.specific_lesson.html}</td><td>{$smarty.const._USERSCONNECTEDTOSPECIFICLESSON}:&nbsp;</td><td>{$T_EMAIL_FORM.lesson.html}</td></tr>
                                 <tr><td>{$T_EMAIL_FORM.recipients.specific_type.html} </td><td>{$smarty.const._SPECIFICTYPEUSERS}:&nbsp;</td><td>{$T_EMAIL_FORM.user_type.html}</td></tr>
                                 <tr><td>{$T_EMAIL_FORM.recipients.specific_user.html} </td><td>{$smarty.const._SPECIFICUSER}:&nbsp;</td><td>{$T_EMAIL_FORM.user.html}</td></tr>
                        </table>
        {/capture}
                        {eF_template_printBlock title = $smarty.const._RECIPIENTSSELECTION data = $smarty.capture.t_recipients_code image = '32x32/directory.png'}
                        <br/>
        {capture name = 't_email_code'}
                        <table class = "formElements" style = "width:100%">
                            <tr><td class = "labelCell">{$smarty.const._SUBJECT}:&nbsp;</td>
                                <td class = "elementCell">{$T_EMAIL_FORM.subject.html}</td></tr>
                            <tr><td class = "labelCell">{$smarty.const._BODY}:&nbsp;</td>
                                <td class = "elementCell">{$T_EMAIL_FORM.body.html}</td></tr>
                            <tr><td>&nbsp;</td><td></td></tr>
                            <tr><td></td><td class = "submitCell">{$T_EMAIL_FORM.send_email.html}</td></tr>
                        </table>
        {/capture}
                        {eF_template_printBlock title = $smarty.const._EMAILBODY data = $smarty.capture.t_email_code image = '32x32/mail.png'}
                </form>
{/capture}
