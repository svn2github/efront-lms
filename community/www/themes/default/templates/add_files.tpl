{* Smarty template for add_files.tpl *}

{if isset($smarty.get.close)}
<script language = "JavaScript">
<!--
    self.opener.location.reload(); 
    window.close();
//-->
</script>
{/if}

{if isset($T_MESSAGE)}
    {eF_template_printMessage message = $T_MESSAGE type = $T_MESSAGE_TYPE}
{/if}

{if isset($T_UPLOAD_MESSAGES)}
    {section name = 'upload_messages_list' loop = $T_UPLOAD_MESSAGES}
        {eF_template_printMessage message = $T_UPLOAD_MESSAGES[upload_messages_list] type = $T_UPLOAD_MESSAGES_TYPE[upload_messages_list]}
    {/section}
{/if}

{if isset($smarty.get.op) && $smarty.get.op == 'delete'}
    {eF_template_printMessage message = $T_DELETE_MESSAGE type = $T_DELETE_MESSAGE_TYPE}
    {eF_template_printCloseButton reload = true}
    <meta http-equiv = "refresh" content = "5;url=add_files.php?close=true" />
{elseif isset($smarty.get.op) && $smarty.get.op == 'deletefolder'}
    {eF_template_printMessage message = $T_DELETEFOLDER_MESSAGE type = $T_DELETEFOLDER_MESSAGE_TYPE}
    {eF_template_printCloseButton reload = true}
    <meta http-equiv = "refresh" content = "5;url=add_files.php?close=true" />
{elseif isset($smarty.get.op) && $smarty.get.op == 'createfolder'}
    {if isset($smarty.post.submit)}
        {eF_template_printMessage message = $T_CREATEFOLDER_MESSAGE type = $T_CREATEFOLDER_MESSAGE_TYPE}
        {eF_template_printCloseButton reload = true}
        <meta http-equiv = "refresh" content = "5;url=add_files.php?close=true" />        
    {else}
    <form action = "{$smarty.server.PHP_SELF}?op=createfolder" method = "post">
        <table><tr>
            <td>{$smarty.const._FOLDERNAME}</td>
            <td><input type = "text" name = "foldername" value = "" /></td>
        </tr><tr>
            <td colspan = "2" align = "center"><input type = "submit" name = "submit" value = "{$smarty.const._CREATE}" /></td>
        </tr></table>
        <input type = "hidden" name = "dir" value = "{$T_DIRECTORY}"/>
    </form>
    {/if}
{else}

<center>
    <form action = "add_files.php?dir={$T_DIRECTORY}" method = "post" enctype = "multipart/form-data">
    <table>
    {if !isset($T_FILES)}
            <tr>
                <td valign = "top" align = "center" nowrap colspan = "2">
                	{if $T_LESSONS_ID != ""}
                    	<iframe name = "IMGPICK" src = "/editor/browse.php?for_type=all&lessons_ID={$T_LESSONS_ID}&dir={$T_DIRECTORY}" style = "border: solid black 1px; width: 520px; height:240px; z-index:1"></iframe>
                    {else}
                    		<iframe name = "IMGPICK" src = "/editor/browse.php?for_type=all&dir={$T_DIRECTORY}" style = "border: solid black 1px; width: 520px; height:240px; z-index:1"></iframe>
					{/if}
					<br/><br/>
                </td>
            </tr>
    {/if}

        <input type = "hidden" name = "MAX_FILE_SIZE" value = "{$smarty.const.G_MAXFILESIZE}" />
    {counter name = 'fileupload' start = -1 print = false}
    {section name = 'inputs_list' loop = $T_SIZE}
            <tr>
                <td> {$smarty.const._FILE} ({counter name = files})</td>
                <td nowrap>{if isset($T_COPY_STRING[inputs_list])} {$T_COPY_STRING[inputs_list]} {/if}<input type = "file" name = "fileupload[{counter name = 'fileupload'}]" size = "40"/>
                </td>
            </tr>
    {/section}
        <tr><td colspan = "2">&nbsp;</td></tr>
        <tr><td colspan = "2" ><br/><b>{$smarty.const._NOTES}:</b></td></tr>
        <tr><td colspan = "2" > - {$smarty.const._EACHFILESIZEMUSTBESMALLERTHAN} <b>{$smarty.const.G_MAXFILESIZE/1024}</b> Kbytes
    {if isset($T_ALLOWED_EXTENSIONS)}
        <br/> - {$smarty.const._ALLOWEDEXTENSIONS}: <b>{$T_ALLOWED_EXTENSIONS}</b>
    {/if}
    {if isset($T_DISALLOWED_EXTENSIONS)}
        <br/> - {$smarty.const._DISALLOWEDEXTENSIONS}: <b>{$T_DISALLOWED_EXTENSIONS}</b>
    {/if}
        </td></tr>
        <tr><td colspan = "2" align = "center">
                <br/><input type = "submit" name = "submit" value = "{$smarty.const._SENDFILES}" />&nbsp;
    {if !isset($T_CONTENT_ID)}
        <input type = "submit" onClick = "javascript:window.close()" value = "{$smarty.const._CLOSEWINDOW}" />
    {else}
        <input type = "submit" onClick = "javascript:self.opener.location.reload('professor.php?ctg=lessons&content_ID={$T_CONTENT_ID}'); window.close()" value = "{$smarty.const._CLOSEWINDOW}" />
    {/if}
            </td></tr>
    </table>
    <input type = "hidden" name = "lessons_ID" value = "{$T_LESSONS_ID}"/>
    <input type = "hidden" name = "content_ID" value = "{$T_CONTENT_ID}"/>
    <input type = "hidden" name = "to_dir" value     = "{$T_DIRECTORY}"/>
    </form>
    <iframe name = "upload" height = "60" frameborder = "0"></iframe>
</center>

{/if}

