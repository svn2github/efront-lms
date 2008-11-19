{* Smarty template for new_message.php *}
{include file = "includes/header.tpl"}
{if $T_MESSAGE}

        {eF_template_printMessage message = $T_MESSAGE type = $T_MESSAGE_TYPE}
        {literal}
        <script>
        if (top.sideframe.document.getElementById('dimmer')) {
        	top.sideframe.document.getElementById('dimmer').style.display = 'none';
        }	
        </script>
        {/literal}
{/if}

<script>
if(top.sideframe)
    top.sideframe.changeTDcolor('{$T_MENUCTG}');



{literal}
/**
Function to check whether recipients have been selected and whether a subject has been defined
Did not use rules of Quickform due to the fact that the first rule is a composite one
*/

function eF_js_checkRecipients() {
    if (document.forms[0].recipients[0].checked && document.getElementById('autocomplete').value == "") {
        alert("{/literal}{$smarty.const._NORECIPIENTSHAVEBEENSELECTED}{literal}");
        return false;
    } else {
        if (document.getElementById('msg_subject').value == "") {
            alert("{/literal}{$smarty.const._THEFIELD} " + "'{$smarty.const._SUBJECT}'" + " {$smarty.const._ISMANDATORY}{literal}");
            return false;
        } else {
            return true;
        }
    }
}

var additional_recipients_hidden = 1;
var additional_recipients_lock = 1;
function show_hide_additional_recipients() {
    if(additional_recipients_lock) {
        additional_recipients_lock = 0;
        if (additional_recipients_hidden) {
            additional_recipients_hidden = 0;
            $('arrow_down').setStyle("display:none;");
            $('arrow_up').setStyle("display:block;");
            new Effect.toggle( $('additional_recipients_categories'),'BLIND',{queue:{scope:'myscope', position:'end', limit: 2}, duration:1.0});
            $('autocomplete').value = "";

        } else {
            additional_recipients_hidden = 1;
            $('arrow_up').setStyle("display:none;");
            $('arrow_down').setStyle("display:block;");
            new Effect.toggle( $('additional_recipients_categories'),'BLIND',{queue:{scope:'myscope', position:'end', limit: 2}, duration:1.0});
            $('only_specific_users').checked = "true";
            eF_js_selectRecipients('only_specific_users');
        }
    }
    setTimeout(function(){ additional_recipients_lock = 1;}, 1001);

}


function eF_js_selectRecipients(recipient) {
    {/literal}
    {* MODULE HCD: Initially disable all new HCD related recipient selects - the one needed will be enabled later*}
    {if $T_MODULE_HCD_INTERFACE}
        {literal}
        document.getElementById('lesson_recipients').disabled    = 'disabled';
        document.getElementById('user_type_recipients').disabled = 'disabled';
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
    document.getElementById('course_recipients').disabled    = 'disabled';
    document.getElementById('specific_course_completed_check').selected  = 'false';
    document.getElementById('specific_course_completed_check').style.visibility = 'hidden';
    document.getElementById('specific_course_completed_label').style.visibility = 'hidden';
    document.getElementById('group_recipients').disabled = 'disabled';
    document.getElementById('lesson_recipients').disabled    = 'disabled';
    document.getElementById('user_type_recipients').disabled = 'disabled';
    document.getElementById('lesson_professor_recipients').disabled    = 'disabled';

    switch (recipient) {
        case 'specific_lesson':
            document.getElementById('lesson_recipients').disabled    = '';
            break;
        case 'specific_course':
            document.getElementById('course_recipients').disabled    = '';
            document.getElementById('specific_course_completed_label').style.visibility = 'visible';
            document.getElementById('specific_course_completed_check').style.visibility = 'visible';
            break;
        case 'specific_lesson_professor':
            document.getElementById('lesson_professor_recipients').disabled = '';
            break;
        case 'specific_type':
            document.getElementById('user_type_recipients').disabled = '';
            break;
        case 'specific_group':
            document.getElementById('group_recipients').disabled = '';
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
            {/literal}
        {/if}
        {literal}
    }
}

{/literal}
</script>

{if $T_MESSAGE}
    {if $T_MESSAGE_TYPE == 'success' || $T_RELOAD_PARENT}
        <script>
//            re = /\?/;
//            !re.test(parent.location) ? parent.location = parent.location+'?message={$T_MESSAGE}&message_type={$T_MESSAGE_TYPE}' : parent.location = parent.location+'&message={$T_MESSAGE}&message_type={$T_MESSAGE_TYPE}';
        </script>
    {else}
        {eF_template_printMessage message = $T_MESSAGE type = $T_MESSAGE_TYPE}
    {/if}
{/if}

<table class = "mainTable">
    <tr>
        <td class = "centerTable">

            <table class = "centerTable" >
               <tr id="titleBar" class = "topTitle">
                    <td colspan = "2" class = "topTitle">
                            <a class = "titleLink" href ="{$smarty.session.s_type}.php?ctg=control_panel">{$smarty.const._HOME}</a>&nbsp;&raquo;&nbsp;
                            <a class = "titleLink" href = "forum/messages_index.php" class = "topTitle" >{$smarty.const._PERSONALMESSAGES}</a> &nbsp;&raquo;&nbsp;
                            <a class = "titleLink" href = "forum/new_message.php" class = "topTitle" >{$smarty.const._NEWMESSAGE}</a>
                    </td>
               </tr>
               <tr id="titleBar2" class = "topTitle" style="display:none;">
                    <td colspan = "2" class = "topTitle">
                            {$smarty.const._NEWMESSAGE}
                    </td>
               </tr>

            <tr><td class = "moduleCell">
                {capture name = 't_recipients_code'}
                        <table class = "formElements" width="100%">
                            <tr><td class = "labelCell">{$smarty.const._RECIPIENTS}:&nbsp;</td>
                                <td class = "elementCell">
                                    <table><tr><td>{$T_ADD_MESSAGE_FORM.recipient.html}</td>
                                        <td><img id = "busy" src="images/12x12/hourglass.png" style="display:none;" alt="working ..."/> </td>
                                        <td>
                                            <a href ="javascript:void(0);" onclick="show_hide_additional_recipients()"><img id="arrow_down" src="images/16x16/navigate_down.png" border="0" alt="{$smarty.const._SHOWRECIPIENTSCATEGORIES}" title="{$smarty.const._SHOWRECIPIENTSCATEGORIES}"/><img id="arrow_up" src="images/16x16/navigate_up.png" border="0" alt="{$smarty.const._HIDERECIPIENTSCATEGORIES}" title="{$smarty.const._HIDERECIPIENTSCATEGORIES}" style="display:none;" /></a>
                                        </td>
                                  <!--      <td>
    
                                    <a href="javascript:void(0);" class = "info nonEmptyLesson" id="help_recipients" onmouseover="$('tooltipImg').style.visibility = 'visible';" onmouseout="$('tooltipImg').style.visibility = 'hidden';"><img id="tooltipImg" class = "tooltip" border = '0' src='images/others/tooltip_arrow.gif'><span class = 'tooltipSpan' id='userInfo' style="font-size: 10px" >Hello world</span></a>
    
                                    </td>-->
                                        </tr>
                                    </table></td></tr>

                            <div id="autocomplete_choices" class="autocomplete"></div>
                            <tr><td></td><td class = "infoCell">{$smarty.const._STARTTYPINGFORRELEVENTMATCHES} </td></tr>
                            <tr><td></td><td class = "infoCell">{$smarty.const._SEPARATEMULTIPLEUSERS}</td></tr>


                        </table>

                        <div id="additional_recipients_categories" style="display:none;">
                            <div style="background-color:#F8F8F8;">
                                <table>
                                    {* MODULE HCD: Insert new HCD related recipient selects *}
                                    {if $T_MODULE_HCD_INTERFACE}
                                        {* HCD efront selects - both regural and HCD *}
                                        <tr style="display:none;"><td>{$T_ADD_MESSAGE_FORM.recipients.only_specific_users.html}   </td><td>{$smarty.const._ONLYRECIPIENTSDEFINEDBELOW}</td></tr>
                                        <tr><td>{$T_ADD_MESSAGE_FORM.recipients.active_users.html}   </td><td colspan=2 align="left">{$smarty.const._ALLACTIVESYSTEMUSERS}</td></tr>
                                        <tr {if $smarty.session.s_type == "administrator"}style="display:none;"{/if}><td>{$T_ADD_MESSAGE_FORM.recipients.to_supervisors.html}   </td><td colspan=2 align="left">{$smarty.const._TOYOURSUPERVISORS}</td></tr>
                                        <tr {if $smarty.session.s_type == "administrator"}style="display:none;"{/if}><td>{$T_ADD_MESSAGE_FORM.recipients.to_branch_supervisors.html}   </td><td colspan=2 align="left">{$smarty.const._TOBRANCHSUPERVISORS}</td></tr>

                                        <tr {if !$T_COURSES}style = "display:none"{/if}><td>{$T_ADD_MESSAGE_FORM.recipients.specific_course.html}</td><td width="27%">{$smarty.const._USERSCONNECTEDTOSPECIFICCOURSE}:&nbsp;</td><td>{$T_ADD_MESSAGE_FORM.specific_course.html}</td><td>{$T_ADD_MESSAGE_FORM.specific_course_completed.html}</td><td id="specific_course_completed_label" style="visibility:hidden">{$T_ADD_MESSAGE_FORM.specific_course_completed.label}</td></tr>

                                        <tr {if !$T_LESSONS}style = "display:none"{/if}><td>{$T_ADD_MESSAGE_FORM.recipients.specific_lesson.html}</td><td>{$smarty.const._USERSCONNECTEDTOSPECIFICLESSON}:&nbsp;</td><td>{$T_ADD_MESSAGE_FORM.lesson.html}</td></tr>
                                        <tr {if !$T_LESSONS}style = "display:none"{/if}><td>{$T_ADD_MESSAGE_FORM.recipients.specific_lesson_professor.html}</td><td>{$smarty.const._PROFESSORSOFLESSON}:&nbsp;</td><td>{$T_ADD_MESSAGE_FORM.professor.html}</td></tr>
                                        <tr><td>{$T_ADD_MESSAGE_FORM.recipients.specific_branch_job_description.html}</td><td width="27%">{$smarty.const._EMPLOYEESOFBRANCH}:&nbsp;</td><td>{$T_ADD_MESSAGE_FORM.branch_recipients.html}</td><td>{$T_ADD_MESSAGE_FORM.include_subbranches.html}</td><td id="include_subbranches_label" style="visibility:hidden;white-space:nowrap;">({$T_ADD_MESSAGE_FORM.include_subbranches.label})</td></tr>
                                        <tr><td>{$T_ADD_MESSAGE_FORM.recipients.specific_job_description.html}  </td><td>{$smarty.const._WITHJOBDESCRIPTION}:&nbsp;</td><td>{$T_ADD_MESSAGE_FORM.job_description_recipients.html}</td></tr>
                                        <tr><td>{$T_ADD_MESSAGE_FORM.recipients.specific_skill.html}  </td><td>{$smarty.const._EMPLOYEESWITHSKILL}:&nbsp;</td><td>{$T_ADD_MESSAGE_FORM.skill_recipients.html}</td></tr>
                                        <tr><td>{$T_ADD_MESSAGE_FORM.recipients.specific_group.html}  </td><td>{$smarty.const._EMPLOYEESINGROUP}:&nbsp;</td><td>{$T_ADD_MESSAGE_FORM.group_recipients.html}</td></tr>
                                        <tr><td>{$T_ADD_MESSAGE_FORM.recipients.specific_type.html}  </td><td>{$smarty.const._SPECIFICTYPEUSERS}:&nbsp;</td><td>{$T_ADD_MESSAGE_FORM.user_type.html}</td></tr>

                                    {else}
                                         {* Regular eFront selects *}
                                         <tr style="display:none;"<td>{$T_ADD_MESSAGE_FORM.recipients.only_specific_users.html}   </td><td>{$smarty.const._ONLYRECIPIENTSDEFINEDBELOW}</td></tr>
                                         <tr><td>{$T_ADD_MESSAGE_FORM.recipients.active_users.html}   </td><td>{$smarty.const._ALLACTIVESYSTEMUSERS}</td></tr>
                                         <tr {if !$T_COURSES}style = "display:none"{/if}><td>{$T_ADD_MESSAGE_FORM.recipients.specific_course.html}</td><td width="27%">{$smarty.const._USERSCONNECTEDTOSPECIFICCOURSE}:&nbsp;</td><td>{$T_ADD_MESSAGE_FORM.specific_course.html}</td><td>{$T_ADD_MESSAGE_FORM.specific_course_completed.html}</td><td id="specific_course_completed_label" style="visibility:hidden">{$T_ADD_MESSAGE_FORM.specific_course_completed.label}</td></tr>
                                         <tr {if !$T_LESSONS}style = "display:none"{/if}><td>{$T_ADD_MESSAGE_FORM.recipients.specific_lesson.html}</td><td>{$smarty.const._USERSCONNECTEDTOSPECIFICLESSON}:&nbsp;</td><td>{$T_ADD_MESSAGE_FORM.lesson.html}</td></tr>
                                         <tr {if !$T_LESSONS}style = "display:none"{/if}><td>{$T_ADD_MESSAGE_FORM.recipients.specific_lesson_professor.html}</td><td>{$smarty.const._PROFESSORSOFLESSON}:&nbsp;</td><td>{$T_ADD_MESSAGE_FORM.professor.html}</td></tr>
                                         <tr><td>{$T_ADD_MESSAGE_FORM.recipients.specific_type.html}  </td><td>{$smarty.const._SPECIFICTYPEUSERS}:&nbsp;</td><td>{$T_ADD_MESSAGE_FORM.user_type.html}</td></tr>
                                        <tr><td>{$T_ADD_MESSAGE_FORM.recipients.specific_group.html}  </td><td>{$smarty.const._USERSINGROUP}:&nbsp;</td><td>{$T_ADD_MESSAGE_FORM.group_recipients.html}</td></tr>

                                    {/if}

                                </table>
                            </div>
                        </div>
                {/capture}


        {capture name = "t_new_message_code"}
            {if $smarty.post.preview}
                <table border = "0" cellpadding = "3" width = "100%">
                    <tr height = "30"><td valign = "top" width = "10%"><b>{$smarty.const._PREVIEW}</b>:</td>
                        <td class = "previewPane" colspan = "2">{$T_BODY_PREVIEW}</td></tr>
                    </tr>
                </table>
                <br/>
            {/if}

                <table class = "formElements">
                    <tr><td class = "labelCell">{$smarty.const._SUBJECT}:&nbsp;</td>
                        <td class = "elementCell">{$T_ADD_MESSAGE_FORM.subject.html}&nbsp;<span class="formRequired">*</span></td></tr>
                    <tr><td class = "labelCell">{$smarty.const._SENDASEMAILALSO}:&nbsp;</td>
                        <td class = "elementCell">{$T_ADD_MESSAGE_FORM.email.html}</td></tr>
                        {if $T_ADD_MESSAGE_FORM.email.error}<tr><td></td><td class = "formError">{$T_ADD_MESSAGE_FORM.email.error}</td></tr>{/if}
                    <tr><td class = "labelCell">{$smarty.const._BODY}:&nbsp;</td>
                        <td class = "elementCell">{$T_ADD_MESSAGE_FORM.body.html}</td></tr>
                        {if $T_ADD_MESSAGE_FORM.body.error}<tr><td></td><td class = "formError">{$T_ADD_MESSAGE_FORM.body.error}</td></tr>{/if}
                    <tr><td class = "labelCell">{$smarty.const._ATTACHMENTS}:&nbsp;</td>
                        <td class = "elementCell">{$T_ADD_MESSAGE_FORM.attachment.0.html}</td></tr>
                        {if $T_ADD_MESSAGE_FORM.attachment.0.error}<tr><td></td><td class = "formError">{$T_ADD_MESSAGE_FORM.attachment.0.error}</td></tr>{/if}
                    <tr><td colspan = "2">&nbsp;</td></tr>
                    <tr><td></td><td class = "submitCell">{*{$T_ADD_MESSAGE_FORM.submit_preview_message.html}&nbsp;*}{$T_ADD_MESSAGE_FORM.submit_send_message.html}</td></tr>
                </table>
        {/capture}

            {$T_ADD_MESSAGE_FORM.javascript}
            <form {$T_ADD_MESSAGE_FORM.attributes} onSubmit = "return eF_js_checkRecipients()">
            {$T_ADD_MESSAGE_FORM.hidden}
                {eF_template_printInnerTable title = $smarty.const._RECIPIENTSSELECTION data = $smarty.capture.t_recipients_code image = '/32x32/address_book3.png'}
                <br/>
                {eF_template_printInnerTable title = $smarty.const._MESSAGEBODY data = $smarty.capture.t_new_message_code image = '/32x32/mail_write.png'}
            </form>
            </td></tr>
        </table>
        </td>
    </tr>

{if $T_SHOWFOOTER}
    {include file = "includes/footer.tpl"}
{/if}
</table>

{literal}
    <script language = "JavaScript" type = "text/javascript">
    if (this.name == "POPUP_FRAME") {
        $('titleBar').setStyle("display:none;");
        $('titleBar2').setStyle("display:block;");
        $('new_message_form').target = "_parent";
    }

<!--
function updateField(item) {

    var new_value    = item.innerHTML;
    var field = document.getElementById('autocomplete');
    var question_mark = '';

    if (field.value != "") {
        question_mark = field.value.substr(0,field.value.lastIndexOf(';')+1);
    }

//    var ending = new_value.substr(0,new_value.indexOf('<'));
    var ending = item.id;

    if (question_mark == "") {
        field.value = ending;
    } else {
        field.value = question_mark+ending;
    }

}
    new Ajax.Autocompleter("autocomplete", "autocomplete_choices", "ask_users.php", {paramName: "preffix", updateElement:updateField, indicator : "busy"});
//-->
    </script>
{/literal}

