{*Smarty template*}

{capture name = 't_main_code'}
     {$T_SDMS_EXPORT_FORM.javascript}
     <form {$T_SDMS_EXPORT_FORM.attributes}>
     {$T_SDMS_EXPORT_FORM.hidden}
     <table>
         <tr><td class = "labelCell">{$T_SDMS_EXPORT_FORM.export_type.label}:&nbsp;</td>
          <td class = "elementCell">{$T_SDMS_EXPORT_FORM.export_type.html}</td></tr>
         <tr><td></td>
          <td class = "submitCell">{$T_SDMS_EXPORT_FORM.submit.html}</td></tr>
     </table>
     </form>


{/capture}

{eF_template_printInnerTable title="SDMS module" data=$smarty.capture.t_main_code absoluteImagePath=1 image=$T_SDMS_MODULE_BASELINK|cat:'images/logo.jpg'}
