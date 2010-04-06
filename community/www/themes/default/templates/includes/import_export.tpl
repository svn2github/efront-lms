    {*moduleImportUsers: The page to import user data*}
    {capture name = "moduleImportExportUsers"}
        <tr><td class="moduleCell">

                {capture name = "t_import_export_users_code"}

                <script>
                var version = "{$smarty.const.G_VERSIONTYPE}";
                </script>

                <div class = "tabber">
                {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
     {capture name = "t_import_code"}
      {$T_IMPORT_FORM.javascript}
                        <form {$T_IMPORT_FORM.attributes}>
                        {$T_IMPORT_FORM.hidden}
                        <table class = "formElements">
                            <tr><td class = "labelCell">{$smarty.const._DATAFILE}:</td>
                             <td class = "elementCell">{$T_IMPORT_FORM.import_file.html}</td></tr>
                            <tr><td class = "labelCell">{$smarty.const._DATATOIMPORT}:</td>
                             <td class = "elementCell">{$T_IMPORT_FORM.import_type.html}</td></tr>
                            <tr><td class = "labelCell">{$smarty.const._KEEPEXISTINGUSERS}:</td>
                             <td class = "elementCell">{$T_IMPORT_FORM.import_keep.html}</td></tr>
       <tr><td class = "labelCell">{$smarty.const._DATEFORMAT}:</td>
                             <td class = "elementCell">{$T_IMPORT_FORM.date_format.html}</td></tr>
                            <tr><td></td>
                             <td class = "submitCell">{$T_IMPORT_FORM.submit_import.html}</td>
                            </tr>
                        </table>
                        </form>
                        <table width="100%">
                            <tr><td class = "horizontalSeparator"></td></tr>
                            <tr><td>{$smarty.const._CSVIMPORTEXPLAINATION}</td></tr>
                            <tr><td></td></tr>
                            {foreach name = 'help_list' key = "type" item = "fields" from = $T_HELP_IMPORT_INFO}
                            <tr><td id = "{$type}_help" style="display:none">
                              <span style = "color:red">{$fields.mandatory}</span>{if $fields.optional != ""}, {$fields.optional}{/if}
                              {*
                              <table class = "formElements">
                               <tr><td class = "labelCell">{$smarty.const._MANDATORYFIELDS}:</td><td class = "elementCell" style = "color:red">{$fields.mandatory}</td></tr>
          {if $fields.optional != ""}<tr><td class = "labelCell">{$smarty.const._OPTIONALFIELDS}:</td><td>{$fields.optional}</td></tr>{/if}
         </table>*}
                             </td>
                            </tr>
                            {/foreach}
                            <tr><td id ="password_explaination" style="display:none">({$smarty.const._IFEMPTYNEWPASSWORD})</td></tr>
                        </table>
     {/capture}
     {eF_template_printBlock tabber = "import" title=$smarty.const._IMPORTDATA data=$smarty.capture.t_import_code image='32x32/import.png'}
                {/if}
    {capture name = "t_export_code"}
     {$T_EXPORT_FORM.javascript}
                        <form {$T_EXPORT_FORM.attributes}>
                        {$T_EXPORT_FORM.hidden}
                            <table>
                                <tr><td class = "labelCell">{$smarty.const._DATATOEXPORT}:</td>
                                    <td class = "elementCell">{$T_EXPORT_FORM.export_type.html}</td></tr>
                                <tr><td class = "labelCell">{$smarty.const._USERSEXPORTUSINGCSVFORMATCOMMA}:</td>
                                    <td class = "elementCell">{$T_EXPORT_FORM.export_separator.csvA.html}</td></tr>
                                <tr><td class = "labelCell">{$smarty.const._USERSEXPORTUSINGCSVFORMATQM}:</td>
                                    <td class = "elementCell">{$T_EXPORT_FORM.export_separator.csvB.html}</td></tr>
        <tr><td class = "labelCell">{$smarty.const._DATEFORMAT}:</td>
                              <td class = "elementCell">{$T_EXPORT_FORM.date_format.html}</td></tr>
                                <tr><td colspan = "2">&nbsp;</td></tr>
                                <tr><td></td>
                                    <td class = "elementCell">{$T_EXPORT_FORM.submit_export.html}</td></tr>
                            </table>
                        </form>
    {/capture}

    {eF_template_printBlock tabber = "export" title=$smarty.const._EXPORTDATA data=$smarty.capture.t_export_code image='32x32/export.png'}
           </div>
                        {/capture}

                {eF_template_printBlock title=$smarty.const._EXPORTIMPORTDATA data=$smarty.capture.t_import_export_users_code image='32x32/import_export.png' help = 'Export-import'}
        </td></tr>
    {/capture}
