{capture name = "t_org_form_code"}
 {eF_template_printForm form =$T_PROFILE_FORM}
{/capture}
{eF_template_printBlock title = $smarty.const._ORGANIZATIONALDATA data = $smarty.capture.t_org_form_code image = '32x32/user.png' }
