{*moduleFileManager: The file manager page*}
{capture name = "moduleFileManager"}
    {if $T_FILE_METADATA}
            <tr><td class = "moduleCell">
                {capture name = 't_file_info_code'}
                    <fieldset class = "fieldsetSeparator">
                        <legend>{$smarty.const._FILEMETADATA}</legend>
                        {$T_FILE_METADATA_HTML}
                    </fieldset>
                {/capture}
                {eF_template_printBlock title = $smarty.const._INFORMATIONFORFILE|cat:' &quot;'|cat:$T_FILE_METADATA.name|cat:'&quot;' data = $smarty.capture.t_file_info_code image = '32x32/information.png'}
            </td></tr>
    {else}
            <tr><td class = "moduleCell">
                {capture name = 't_file_manager_code'}
                    {$T_FILE_MANAGER}
                {/capture}
                {eF_template_printBlock title=$smarty.const._FILEMANAGER data=$smarty.capture.t_file_manager_code image='32x32/file_explorer.png'}
            </td></tr>
    {/if}
{/capture}
