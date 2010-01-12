{capture name = 't_users_online'}
  <span> {$T_NUMBER} &nbsp;{$smarty.const._USERSONLINE}</span>
{/capture}


<table width="20%"><tr><td>
{eF_template_printBlock title = $smarty.const._ONLINEUSERS data = $smarty.capture.t_users_online image = '16x16/goto_student.png'}
</td></tr></table>