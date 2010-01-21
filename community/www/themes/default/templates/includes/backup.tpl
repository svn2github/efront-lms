            {capture name = "moduleBackup"}
                <tr><td class = "moduleCell">
                {if $T_DEFAULT_URI} {assign var = "query_string" value = $smarty.server.PHP_SELF|cat:$T_DEFAULT_URI|cat:'&'}
                {else}              {assign var = "query_string" value = $smarty.server.PHP_SELF|cat:'?'}
                {/if}
                {capture name="t_backup_code"}
                    <script>
                    {literal}
                    function restore(el, id) {
                        if (confirm('{/literal}{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}{literal}')) {
                            location = '{/literal}{$smarty.server.PHP_SELF}{literal}?ctg=backup&restore='+id;
                        }
                    }
                    {/literal}
                    </script>

                   {$T_FILE_MANAGER}
                   <div id = "backup_table" style = "display:none;" class = "filemanagerBlock">
                               {$T_BACKUP_FORM.javascript}
                               <form {$T_BACKUP_FORM.attributes}>
                                   {$T_BACKUP_FORM.hidden}
                                   <table class = "uploadBox formElements">
                                       <tr><td class = "labelCell">{$smarty.const._FILENAME}:&nbsp;</td>
                                           <td class = "elementCell">{$T_BACKUP_FORM.backupname.html}</td></tr>
                                       <tr><td class = "labelCell">{$smarty.const._TYPE}:&nbsp;</td>
                                           <td class = "elementCell">{$T_BACKUP_FORM.backuptype.html}</td></tr>
                                       <tr><td colspan = "2">&nbsp;</td></tr>
                                       <tr><td></td><td class = "elementCell">{$T_BACKUP_FORM.submit_backup.html}</td></tr>
                                   </table>
                               </form>
                               <img src = "images/others/progress_big.gif" id = "backup_image" title = "{$smarty.const._UPLOADING}" alt = "{$smarty.const._UPLOADING}" style = "display:none;margin-top:30px;vertical-align:middle;"/>
                   </div>
                {/capture}

                {eF_template_printBlock title = $smarty.const._BACKUP|cat:' - '|cat:$smarty.const._RESTORE data = $smarty.capture.t_backup_code image = '32x32/backup_restore.png'}
            </td></tr>
        {/capture}