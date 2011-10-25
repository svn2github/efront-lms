{if $T_SECURITY_FEEDS}
    {capture name = "t_code"}
         <table style = "width:100%;">
          <tr><td style = "vertical-align:top;">
                 <ul style = "padding-left:0px;margin-left:0px;list-style-type:none;">
      {foreach name = 'issues_list' item = "item" key = "key" from = $T_LOCAL_ISSUES}
       <li>{$smarty.const._MODULE_SECURITY_LOCALISSUE}: {$item}</li>
      {/foreach}
                 </ul>
                 <ul style = "padding-left:0px;margin-left:0px;list-style-type:none;">
      {$T_SECURITY_FEEDS}
                 </ul>
          </td></tr>
         </table>
    {/capture}

    {eF_template_printBlock title=$smarty.const._MODULE_SECURITY_MODULESECURITY data=$smarty.capture.t_code image= $T_MODULE_BASEURL|cat:'images/generic.png' absoluteImagePath = 1}
{/if}
