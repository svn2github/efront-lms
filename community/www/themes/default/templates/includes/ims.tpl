        {*moduleIMSOptions: IMS options page*}
        {capture name = "moduleIMSOptions"}
       <tr><td class = "moduleCell">
                        {if !$smarty.get.ims_export}
                            {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink'  href = '`$smarty.server.PHP_SELF`?ctg=ims&ims_import=1'>`$smarty.const._IMSIMPORT`</a>"}

                            {capture name = 'ims_import_code'}
                                {$T_UPLOAD_IMS_FORM.javascript}
                                <form {$T_UPLOAD_IMS_FORM.attributes}>
                                    {$T_UPLOAD_IMS_FORM.hidden}
                                    <table style = "margin-top:15px;">
          <tr><td class = "labelCell">{$T_UPLOAD_IMS_FORM.ims_file[0].label}:&nbsp;</td>
           <td class = "elementCell">{$T_UPLOAD_IMS_FORM.ims_file[0].html} <img src = "images/16x16/add.png" alt = "{$smarty.const._ADDBOX}" title = "{$smarty.const._ADDBOX}" onclick = "Element.extend(this);this.up().up().next().show();this.hide();"></td></tr>
         {foreach name = 'file_upload_list' item = "item" key = "key" from = $T_UPLOAD_IMS_FORM.ims_file}
          {if $key > 0}
          <tr style = "display:none"><td class = "labelCell"></td>
           <td class = "elementCell">{$T_UPLOAD_IMS_FORM.ims_file[$key].html} <img src = "images/16x16/add.png" alt = "{$smarty.const._ADDBOX}" title = "{$smarty.const._ADDBOX}" onclick = "Element.extend(this);this.up().up().next().show();this.hide();"></td></tr>
          {/if}
         {/foreach}
                                        <tr><td></td>
                                            <td class = "infoCell">{$smarty.const._EACHFILESIZEMUSTBESMALLERTHAN} <b>{$T_MAX_FILE_SIZE}</b> {$smarty.const._KB}</td></tr>
                                        <tr><td class = "labelCell">{$T_UPLOAD_IMS_FORM.url_upload.label}:
                                            <td class = "elementCell">{$T_UPLOAD_IMS_FORM.url_upload.html}</td></tr>
                                        <tr><td class = "labelCell">{$T_UPLOAD_IMS_FORM.embed_type.label}:
                                            <td class = "elementCell">{$T_UPLOAD_IMS_FORM.embed_type.html}</td></tr>
                                        <tr><td class = "labelCell">{$T_UPLOAD_IMS_FORM.popup_parameters.label}:
                                            <td class = "elementCell">{$T_UPLOAD_IMS_FORM.popup_parameters.html}</td></tr>
                                        <tr><td class = "labelCell"></td>
                                            <td class = "submitCell">{$T_UPLOAD_IMS_FORM.submit_upload_ims.html}</td></tr>
                                    </table>
                                </form>
                            {/capture}

                            {eF_template_printBlock title = $smarty.const._IMSIMPORT data = $smarty.capture.ims_import_code image = '32x32/autocomplete.png' main_options = $T_TABLE_OPTIONS help = 'SCORM_/_IMS'}

                        {elseif $smarty.get.ims_export}
                            {assign var = "title" value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink'  href = '`$smarty.server.PHP_SELF`?ctg=ims&ims_export=1'>`$smarty.const._IMSEXPORT`</a>"}

                            {capture name = 'ims_export_code'}
                            {if (isset($T_IMS_EXPORT_FILE))}
                                <table style = "margin-top:15px;">
                                    <tr>
                                        <td><span style = "vertical-align:middle">{$smarty.const._DOWNLOADIMSEXPORTEDFILE}:&nbsp;</span>
                                            <a href = "view_file.php?file={$T_IMS_EXPORT_FILE.path}&action=download" target = "POPUP_FRAME" style = "vertical-align:middle">{$T_IMS_EXPORT_FILE.name}</a>
                                            <img src = "images/16x16/import.png" alt = "{$smarty.const._DOWNLOADFILE}" title = "{$smarty.const._DOWNLOADFILE}" border = "0" style = "vertical-align:middle">
                                        </td>
                                    </tr>
                                </table>
                            {/if}
                                    {$T_EXPORT_IMS_FORM.javascript}
                                    <form {$T_EXPORT_IMS_FORM.attributes}>
                                        {$T_EXPORT_IMS_FORM.hidden}
                                        <table style = "margin-top:15px;">
                                            <tr>
                                                <td class = "labelCell">{$smarty.const._IMSEXPORT}:&nbsp;</td>
                                                <td class = "elementCell">{$T_EXPORT_IMS_FORM.submit_export_ims.html}</td>
                                                </tr>
                                        </table>
                                    </form>
                            {/capture}
                            {eF_template_printBlock title = $smarty.const._IMSEXPORT data = $smarty.capture.ims_export_code image = '32x32/autocomplete.png' main_options = $T_TABLE_OPTIONS help = 'SCORM_/_IMS'}
                        {/if}
                                </td></tr>
        {/capture}
