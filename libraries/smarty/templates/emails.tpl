{* smarty file for emails.php *}
<script>
{literal}
<!--
function eF_js_selectRecipients(recipient) {
    {/literal}
    {* MODULE HCD: Initially disable all new HCD related recipient selects - the one needed will be enabled later*}
    {if $T_MODULE_HCD_INTERFACE}
        {literal}
        document.getElementById('lesson_recipients').disabled    = 'disabled';
        document.getElementById('user_type_recipients').disabled = 'disabled';
        document.getElementById('user_recipients').disabled      = 'disabled';
        document.getElementById('branch_recipients').disabled    = 'disabled';
        document.getElementById('include_subbranches').selected  = 'false';
        document.getElementById('include_subbranches').style.visibility = 'hidden';
        document.getElementById('include_subbranches_label').style.visibility = 'hidden';
        document.getElementById('job_description_recipients').disabled  = 'disabled';
        document.getElementById('skill_recipients').disabled     = 'disabled';
        document.getElementById('group_recipients').disabled     = 'disabled';
        {/literal}
    {/if}
    {literal}
    switch (recipient) {
        case 'all_users':
        case 'active_users':
        document.getElementById('lesson_recipients').disabled    = 'disabled';
        document.getElementById('user_type_recipients').disabled = 'disabled';
        document.getElementById('user_recipients').disabled      = 'disabled';
        break;
        case 'specific_lesson':
        document.getElementById('lesson_recipients').disabled    = '';
        document.getElementById('user_type_recipients').disabled = 'disabled';
        document.getElementById('user_recipients').disabled      = 'disabled';
        break;
        case 'specific_type':
        document.getElementById('lesson_recipients').disabled    = 'disabled';
        document.getElementById('user_type_recipients').disabled = '';
        document.getElementById('user_recipients').disabled      = 'disabled';
        break;
        case 'specific_user':
        document.getElementById('lesson_recipients').disabled    = 'disabled';
        document.getElementById('user_type_recipients').disabled = 'disabled';
        document.getElementById('user_recipients').disabled      = '';
        break;

        {/literal}
        {* MODULE HCD: Enable/disable new HCD related recipient selects *}
        {if $T_MODULE_HCD_INTERFACE}
            {literal}
         case 'specific_branch_job_description':
            // Both branch and job description will be enabled in case a combination is required
            document.getElementById('branch_recipients').disabled = '';
            document.getElementById('job_description_recipients').disabled = '';
            document.getElementById('include_subbranches').style.visibility = 'visible';
            document.getElementById('include_subbranches_label').style.visibility = 'visible';
            break;
         case 'specific_job_description':
            document.getElementById('job_description_recipients').disabled = '';
            break;
         case 'specific_skill':
            document.getElementById('skill_recipients').disabled = '';
            break;
         case 'specific_group':
            document.getElementById('group_recipients').disabled = '';
            break;
            {/literal}
        {/if}
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

                            {* MODULE HCD: Insert new HCD related recipient selects *}
                            {if $T_MODULE_HCD_INTERFACE}
                                {* HCD efront selects - both regural and HCD *}
                                <tr><td>{$T_EMAIL_FORM.recipients.all_users.html}      </td><td>{$smarty.const._ALLSYSTEMUSERS}</td></tr>
                                <tr><td>{$T_EMAIL_FORM.recipients.active_users.html}   </td><td>{$smarty.const._ALLACTIVESYSTEMUSERS}</td></tr>
                                <tr><td class = "labelCell">{$T_EMAIL_FORM.recipients.specific_branch_job_description.html}</td><td>{$smarty.const._EMPLOYEESOFBRANCH}:&nbsp;</td><td>{$T_EMAIL_FORM.branch_recipients.html}</td><td>{$T_EMAIL_FORM.include_subbranches.html}</td><td id="include_subbranches_label" style="visibility:hidden">({$T_EMAIL_FORM.include_subbranches.label})</td></tr>
                                <tr><td class = "labelCell">{$T_EMAIL_FORM.recipients.specific_job_description.html}  </td><td>{$smarty.const._WITHJOBDESCRIPTION}:&nbsp;</td><td>{$T_EMAIL_FORM.job_description_recipients.html}</td><td {if !$T_LESSONS}style = "display:none"{/if}>{$T_EMAIL_FORM.recipients.specific_lesson.html}</td><td {if !$T_LESSONS}style = "display:none"{/if}>{$smarty.const._USERSCONNECTEDTOSPECIFICLESSON}:&nbsp;</td><td>{$T_EMAIL_FORM.lesson.html}</td></tr>
                                <tr><td class = "labelCell">{$T_EMAIL_FORM.recipients.specific_skill.html}  </td><td>{$smarty.const._EMPLOYEESWITHSKILL}:&nbsp;</td><td>{$T_EMAIL_FORM.skill_recipients.html}</td><td>{$T_EMAIL_FORM.recipients.specific_type.html}  </td><td>{$smarty.const._SPECIFICTYPEUSERS}:&nbsp;</td><td>{$T_EMAIL_FORM.user_type.html}</td></tr>
                                <tr><td class = "labelCell">{$T_EMAIL_FORM.recipients.specific_group.html}  </td><td>{$smarty.const._EMPLOYEESINGROUP}:&nbsp;</td><td>{$T_EMAIL_FORM.group_recipients.html}</td><td>{$T_EMAIL_FORM.recipients.specific_user.html}  </td><td>{$smarty.const._SPECIFICUSER}:&nbsp;</td><td>{$T_EMAIL_FORM.user.html}</td></tr>

                            {else}
                                 {* Regular eFront selects *}
                                 <tr><td>{$T_EMAIL_FORM.recipients.all_users.html}      </td><td>{$smarty.const._ALLSYSTEMUSERS}</td></tr>
                                 <tr><td>{$T_EMAIL_FORM.recipients.active_users.html}   </td><td>{$smarty.const._ALLACTIVESYSTEMUSERS}</td></tr>

                                 <tr {if !$T_LESSONS}style = "display:none"{/if}><td>{$T_EMAIL_FORM.recipients.specific_lesson.html}</td><td>{$smarty.const._USERSCONNECTEDTOSPECIFICLESSON}:&nbsp;</td><td>{$T_EMAIL_FORM.lesson.html}</td></tr>
                                 <tr><td>{$T_EMAIL_FORM.recipients.specific_type.html}  </td><td>{$smarty.const._SPECIFICTYPEUSERS}:&nbsp;</td><td>{$T_EMAIL_FORM.user_type.html}</td></tr>
                                 <tr><td>{$T_EMAIL_FORM.recipients.specific_user.html}  </td><td>{$smarty.const._SPECIFICUSER}:&nbsp;</td><td>{$T_EMAIL_FORM.user.html}</td></tr>
                            {/if}

                        </table>
        {/capture}
                        {eF_template_printInnerTable title = $smarty.const._RECIPIENTSSELECTION data = $smarty.capture.t_recipients_code image = '/32x32/address_book3.png'}
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
                        {eF_template_printInnerTable title = $smarty.const._EMAILBODY data = $smarty.capture.t_email_code image = '/32x32/mail_write.png'}
                </form>

{/capture}