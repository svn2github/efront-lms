{*Smarty template*}
    {if $smarty.get.add_youtube || $smarty.get.edit_youtube}
        {capture name = 't_insert_youtube_code'}
                    {$T_YOUTUBE_FORM.javascript}
                    <form {$T_YOUTUBE_FORM.attributes}>
                        {$T_YOUTUBE_FORM.hidden}
                        <table class = "formElements">
                            <tr><td class = "labelCell">{$smarty.const._YOUTUBE_NAME}:&nbsp;</td>
                                <td class = "elementCell">{$T_YOUTUBE_FORM.title.html}</td>
                                <td class = "formError">{$T_YOUTUBE_FORM.title.error}</td></tr>

                            <tr><td class = "labelCell">{$smarty.const._YOUTUBE_VIDEOLINK}:&nbsp;</td>
                                <td class = "elementCell"><table><tr><td>{$T_YOUTUBE_FORM.link.html}</td><td>({$smarty.const._YOUTUBE_EXAMPLE}:</td><td>"http://www.youtube.com/watch?v=eLS0kRAsSoo")</td></tr></table></td>
                                <td class = "formError">{$T_YOUTUBE_FORM.link.error}</td></tr>

                            <tr><td class = "labelCell">{$smarty.const._YOUTUBE_DESCRIPTION}:&nbsp;</td>
                                <td class = "elementCell">{$T_YOUTUBE_FORM.description.html}</td></tr>
                            <tr><td></td><td >&nbsp;</td></tr>

                            <tr><td></td><td class = "submitCell">{$T_YOUTUBE_FORM.submit_youtube.html}</td></tr>
                        </table>
                    </form>

        {/capture}

        {eF_template_printBlock title=$smarty.const._YOUTUBE_YOUTUBEVIDEODATA data=$smarty.capture.t_insert_youtube_code absoluteImagePath=1 image=$T_YOUTUBE_MODULE_BASELINK|cat:'images/youtube32.png' help = 'Youtube'}

    {else}
        {capture name = 't_youtube_list_code'}
            {if $T_USERLESSONTYPE == "professor"}
            <table>
                <tr><td>
                    <a href = "{$T_YOUTUBE_MODULE_BASEURL}&add_youtube=1"><img src = "images/16x16/add.png" alt = "{$smarty.const._YOUTUBE_ADDYOUTUBE}" title = "{$smarty.const._YOUTUBE_ADDYOUTUBE}" border = "0" /></a>
                </td><td>
                    <a href = "{$T_YOUTUBE_MODULE_BASEURL}&add_youtube=1" title = "{$smarty.const._YOUTUBE_ADDYOUTUBE}">{$smarty.const._YOUTUBE_ADDYOUTUBE}</a>
                </td></tr>
            </table>
            {/if}

            <table border = "0" width = "100%" class="sortedTable" sortBy = "0">
                <tr class = "topTitle">
                    <td class = "topTitle">{$smarty.const._YOUTUBE_PREVIEW}</td>
                    <td class = "topTitle">{$smarty.const._YOUTUBE_NAME}</td>
                    <td class = "topTitle" width="40%">{$smarty.const._YOUTUBE_VIDEOLINK}</td>
                    {if $T_USERLESSONTYPE == "professor"}
                    <td class = "topTitle" align="center">{$smarty.const._OPERATIONS}</td>
                    {/if}
                </tr>

                {foreach name =youtube_list item=youtube from = $T_YOUTUBE}
                <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                    <td><a href = "http://www.youtube.com/watch?v={$youtube.link}" target ="_blank" class = "editLink"><img src = "http://img.youtube.com/vi/{$youtube.link}/default.jpg" /></a></td>
                    <td>{$youtube.title}</td>
                    <td><a href = "http://www.youtube.com/watch?v={$youtube.link}" target ="_blank" class = "editLink">http://www.youtube.com/watch?v={$youtube.link}</a></td>
     {if $T_USERLESSONTYPE == "professor"}

                    <td align = "center">
                        <table>
                            <tr>
                            <td width="45%">
                                <a href = "{$T_YOUTUBE_MODULE_BASEURL}&edit_youtube={$youtube.id}" class = "editLink"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
                            </td>
                            <td width="45%">
                                <a href = "{$T_YOUTUBE_MODULE_BASEURL}&delete_youtube={$youtube.id}" onclick = "return confirm('{$smarty.const._YOUTUBEAREYOUSUREYOUWANTTODELETEEVENT}')" class = "deleteLink"><img border = "0" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a>
                            </td>
                            </tr>
                         </table>
                    </td>
                    {/if}
                </tr>
                {foreachelse}
                <tr><td colspan="4" class = "emptyCategory">{$smarty.const._YOUTUBENOMEETINGSCHEDULED}</td></tr>
                {/foreach}
            </table>
        {/capture}


        {eF_template_printBlock title=$smarty.const._YOUTUBE_YOUTUBELIST data=$smarty.capture.t_youtube_list_code absoluteImagePath=1 image=$T_YOUTUBE_MODULE_BASELINK|cat:'images/youtube32.png' help = 'Youtube'}
    {/if}
