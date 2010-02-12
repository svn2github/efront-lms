    {*moduleImportUsers: The page to import user data*}
    {capture name = "moduleImportExportUsers"}
        <tr><td class="moduleCell">
                {capture name = "t_import_export_users_code"}
                <div class = "tabber">
                {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
     {capture name = "t_import_users_code"}
        {$T_IMPORT_USERS_FORM.javascript}
                        <form {$T_IMPORT_USERS_FORM.attributes}>
                        {$T_IMPORT_USERS_FORM.hidden}
                        <table class = "formElements">
                            <tr><td class = "labelCell">{$smarty.const._DATAFILE}:</td>
                             <td class = "elementCell">{$T_IMPORT_USERS_FORM.users_file.html}</td></tr>
                            <tr><td class = "labelCell">{$smarty.const._KEEPEXISTINGUSERS}:</td>
                             <td class = "elementCell">{$T_IMPORT_USERS_FORM.replace_users.keep.html}</td></tr>
                            <tr><td class = "labelCell">{$smarty.const._REPLACEEXISTINGUSERS}:</td>
                             <td class = "elementCell">{$T_IMPORT_USERS_FORM.replace_users.replace.html}</td></tr>
                            <tr><td class = "labelCell">{$smarty.const._SENDINFOVIAEMAIL}:</td>
                             <td class = "elementCell">{$T_IMPORT_USERS_FORM.send_email.html}</td></tr>
                            <tr><td></td>
                             <td class = "submitCell">{$T_IMPORT_USERS_FORM.submit_import_users.html}</td>
                            </tr>
                        </table>
                        </form>
                        <table>
                            <tr><td class = "horizontalSeparator"></td></tr>
                            <tr><td>{$smarty.const._THEFIELDSINYOURCSVFILEMUSTCONTAINTHEFOLLOWINGFIELDS} (<a href = "{$smarty.server.PHP_SELF}?ctg=import_export&csv_sample=1">{$smarty.const._DOWNLOADEXAMPLE}</a>):</td></tr>
                            <tr><td>
                                    {section name='fields' loop=$T_FIELDS}
                                        <span {if ($T_FIELDS[fields] == 'login' || $T_FIELDS[fields] == 'email' || $T_FIELDS[fields] == 'name' || $T_FIELDS[fields] == 'surname')}style = "color:red"{/if}>{$T_FIELDS[fields]};</span>
                                    {/section}
                                </td></tr>
                            <tr><td>({$smarty.const._IFEMPTYNEWPASSWORD})</td></tr>
                        </table>
     {/capture}
     {eF_template_printBlock tabber = "import" title=$smarty.const._USERSIMPORT data=$smarty.capture.t_import_users_code image='32x32/import.png'}
                {/if}
    {capture name = "t_export_users_code"}
     {$T_EXPORT_USERS_FORM.javascript}
                        <form {$T_EXPORT_USERS_FORM.attributes}>
                        {$T_EXPORT_USERS_FORM.hidden}
                            <table>
                                <tr><td class = "labelCell">{$smarty.const._USERSEXPORTUSINGCSVFORMATCOMMA}:</td>
                                    <td class = "elementCell">{$T_EXPORT_USERS_FORM.export_users.csvA.html}</td></tr>
                                <tr><td class = "labelCell">{$smarty.const._USERSEXPORTUSINGCSVFORMATQM}:</td>
                                    <td class = "elementCell">{$T_EXPORT_USERS_FORM.export_users.csvB.html}</td></tr>
                                <tr><td colspan = "2">&nbsp;</td></tr>
                                <tr><td></td>
                                    <td class = "elementCell">{$T_EXPORT_USERS_FORM.submit_export_users.html}</td></tr>
                            </table>
                        </form>
    {/capture}
    {eF_template_printBlock tabber = "export" title=$smarty.const._USERSEXPORT data=$smarty.capture.t_export_users_code image='32x32/export.png'}
                </div>
                {/capture}
                {eF_template_printBlock title=$smarty.const._EXPORTIMPORTDATA data=$smarty.capture.t_import_export_users_code image='32x32/import_export.png'}
        </td></tr>
    {/capture}
