{*Smarty template*}

{capture name = 't_main_code'}
     {$T_SDMS_EXPORT_FORM.javascript}
     <form {$T_SDMS_EXPORT_FORM.attributes}>
     {$T_SDMS_EXPORT_FORM.hidden}
     <table>
{*
         <tr><td class="labelCell">{$smarty.const._MODULE_CARTER_HRISIMPORTFILE}:</td>
          <td class = "elementCell">{$T_SDMS_EXPORT_FORM.hcd_file.html}</td></tr>
         {if isset($T_FILENAME)}
         <tr><td class="labelCell">{$smarty.const._MODULE_CARTER_CURRENTFILE}:</td>
          <td class = "elementCell">{$T_FILENAME}</td></tr>
   {/if}
         <tr><td colspan="2">&nbsp;</td></tr>
         <tr><td class="labelCell">{$smarty.const._MODULE_CARTER_ORGANIZATIONSTRUCTUREFILE}:</td>
          <td class = "elementCell">{$T_SDMS_EXPORT_FORM.hcd_organization_file.html}</td></tr>
         {if isset($T_ORGANIZATION_FILENAME)}
         <tr><td class="labelCell">{$smarty.const._MODULE_CARTER_CURRENTFILE}:</td>
          <td class = "elementCell">{$T_ORGANIZATION_FILENAME}</td></tr>
   {/if}
         <tr><td colspan="2">&nbsp;</td></tr>
         <tr><td class = "labelCell">{$T_SDMS_EXPORT_FORM.timestamp.label}:&nbsp;</td>
          <td class = "elementCell">{$T_SDMS_EXPORT_FORM.timestamp.html}</td></tr>
   <tr><td class = "labelCell">{$T_SDMS_EXPORT_FORM.frequency.label}:&nbsp;</td>
    <td class = "elementCell">{$T_SDMS_EXPORT_FORM.frequency.html}</td></tr>
         <tr><td class="labelCell">{$T_SDMS_EXPORT_FORM.hcd_ommit_users.label}:</td>
          <td class = "elementCell">{$T_SDMS_EXPORT_FORM.hcd_ommit_users.html}</td></tr>
*}
         <tr><td class = "labelCell">{$T_SDMS_EXPORT_FORM.export_type.label}:&nbsp;</td>
          <td class = "elementCell">{$T_SDMS_EXPORT_FORM.export_type.html}</td></tr>
         <tr><td></td>
          <td class = "submitCell">{$T_SDMS_EXPORT_FORM.submit.html}</td></tr>
     </table>
     </form>


{/capture}

{eF_template_printInnerTable title="SDMS module" data=$smarty.capture.t_main_code absoluteImagePath=1 image=$T_SDMS_MODULE_BASELINK|cat:'images/logo.jpg'}
