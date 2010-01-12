
{capture name = 't_inner_table_mail_code}

{if $T_MODULE_CURRENT_USER == "professor"}

	<a title="{$smarty.const._MAILS_TOLESSONSSTUDENTS}" href = "{$T_MODULE_MAIL_BASEURL}&rec=lesson_students&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._MAILS_TOLESSONSSTUDENTS}', 3)" target = "POPUP_FRAME"  style = "vertical-align:middle"><img src = "images/16x16/users.png" title = "{$smarty.const._MAILS_TOLESSONSSTUDENTS}" alt = "{$smarty.const._MAILS_TOLESSONSSTUDENTS}" style = "vertical-align:middle" border = "0"/>&nbsp;<span  style = "vertical-align:middle">{$smarty.const._MAILS_TOLESSONSSTUDENTS}</span></a>&nbsp;&nbsp;&nbsp;
	<a title="{$smarty.const._MAILS_TOADMIN}" href = "{$T_MODULE_MAIL_BASEURL}&rec=admin&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._MAILS_TOADMIN}', 3)" target = "POPUP_FRAME"  style = "vertical-align:middle"><img src = "images/16x16/user.png" title = "{$smarty.const._MAILS_TOADMIN}" alt = "{$smarty.const._MAILS_TOADMIN}" style = "vertical-align:middle" border = "0"/>&nbsp;<span  style = "vertical-align:middle">{$smarty.const._MAILS_TOADMIN}</span></a>
{else if $T_MODULE_CURRENT_USER == "student"}
	<a title="{$smarty.const._MAILS_TOLESSONPROFESSORS}" href = "{$T_MODULE_MAIL_BASEURL}&rec=lesson_professors&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._MAILS_TOLESSONPROFESSORS}', 3)" target = "POPUP_FRAME"  style = "vertical-align:middle"><img src = "images/16x16/users.png" title = "{$smarty.const._MAILS_TOLESSONPROFESSORS}" alt = "{$smarty.const._MAILS_TOLESSONPROFESSORS}" style = "vertical-align:middle" border = "0"/>&nbsp;<span  style = "vertical-align:middle">{$smarty.const._MAILS_TOLESSONPROFESSORS}</span></a>
{/if}
{/capture}


{eF_template_printBlock title = $smarty.const._MAILS_MODULEMAILS data = $smarty.capture.t_inner_table_mail_code image = 'images/32x32/mail.png' absoluteImagePath=1}