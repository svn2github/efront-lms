{*Smarty template*}

{capture name = 't_change_login_code'}
 {eF_template_printForm form = $T_TOOLS_FORM}
    <div id = "module_administrator_tools_autocomplete_users_div" class = "autocomplete"></div>
{/capture}

{capture name = 't_administrator_tools_code'}
 <div class = "tabber">
  {eF_template_printBlock tabber = "change_login" title = $smarty.const._MODULE_CANCELLATIONS_CHANGELOGIN data = $smarty.capture.t_change_login_code absoluteImagePath=1 image=$T_MODULE_CANCELLATIONS_BASELINK|cat:'images/tools.png'}
 </div>
{/capture}
{eF_template_printBlock title = $smarty.const._MODULE_ADMINISTRATOR_TOOLS data = $smarty.capture.t_administrator_tools_code absoluteImagePath=1 image=$T_MODULE_CANCELLATIONS_BASELINK|cat:'images/tools.png'}
