{*Smarty template*}

{capture name = 't_change_login_code'}
 {eF_template_printForm form = $T_TOOLS_FORM}
    <div id = "module_administrator_tools_autocomplete_users_div" class = "autocomplete"></div>
{/capture}

{capture name = 't_global_settings_code'}
        {eF_template_printBlock title = $smarty.const._MODULE_ADMINISTRATOR_TOOLS_GLOBALLESSONSETTINGS columns = 4 links = $T_LESSON_SETTINGS image='32x32/lessons.png' main_options = $T_TABLE_OPTIONS groups = $T_LESSON_SETTINGS_GROUPS help = 'Administration'}
{/capture}

{capture name = 't_administrator_tools_code'}
 <div class = "tabber">
  {eF_template_printBlock tabber = "change_login" title = $smarty.const._MODULE_ADMINISTRATOR_TOOLS_CHANGELOGIN data = $smarty.capture.t_change_login_code absoluteImagePath=1 image=$T_MODULE_ADMINISTRATOR_TOOLS_BASELINK|cat:'images/tools.png'}
  {eF_template_printBlock tabber = "global_settings" title = $smarty.const._MODULE_ADMINISTRATOR_TOOLS_GLOBALLESSONSETTINGS data = $smarty.capture.t_global_settings_code absoluteImagePath=1 image=$T_MODULE_ADMINISTRATOR_TOOLS_BASELINK|cat:'images/tools.png'}
 </div>
{/capture}
{eF_template_printBlock title = $smarty.const._MODULE_ADMINISTRATOR_TOOLS data = $smarty.capture.t_administrator_tools_code absoluteImagePath=1 image=$T_MODULE_ADMINISTRATOR_TOOLS_BASELINK|cat:'images/tools.png'}
