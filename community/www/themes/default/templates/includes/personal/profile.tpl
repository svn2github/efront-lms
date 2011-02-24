{capture name = "t_personal_form_code"}
 {eF_template_printForm form =$T_PROFILE_FORM}
{/capture}
{eF_template_printBlock title = $smarty.const._PERSONALDATA data = $smarty.capture.t_personal_form_code image = '32x32/user.png' }
