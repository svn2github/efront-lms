{*Smarty template*}
{if $smarty.get.add_banner || $smarty.get.edit_banner}
    {capture name = 't_insert_banner_code'}
                {$T_BANNERS_FORM.javascript}
                <form {$T_BANNERS_FORM.attributes}>
                    {$T_BANNERS_FORM.hidden}
                    <table class = "formElements" style = "width:100%">
                        <tr><td class = "labelCell">{$T_BANNERS_FORM.file_upload.label}:&nbsp;</td>
                            <td class = "elementCell" colspan="3">{$T_BANNERS_FORM.file_upload.html}</td></tr>
                        <tr><td class = "labelCell">{$T_BANNERS_FORM.existing_image.label}:&nbsp;</td>
                            <td class = "elementCell" colspan="1">{$T_BANNERS_FORM.existing_image.html}&nbsp;</td>
                        </tr>
                        <tr><td class = "labelCell">{$smarty.const._BANNERS_LINK}:&nbsp;</td>
                            <td class = "elementCell">{$T_BANNERS_FORM.link.html}</td></tr>
                        <tr><td></td><td >&nbsp;</td></tr>
                        <tr><td></td><td class = "submitCell">{$T_BANNERS_FORM.submit_banner.html}</td></tr>
                    </table>
                </form>

    {/capture}
    {eF_template_printBlock title=$smarty.const._BANNERS_ADDBANNER data=$smarty.capture.t_insert_banner_code image=$T_BANNERS_BASELINK|cat:'images/banners32.png' absoluteImagePath = 1}
{else}
    {capture name = 't_banners_list_code'}
        {if $smarty.session.s_type != 'student'}
            <table>
                <tr><td>
                    <a href = "{$T_BANNERS_BASEURL}&add_banner=1"><img src = "images/16x16/add.png" alt = "{$smarty.const._LINKS_ADDLINK}" title = "{$smarty.const._BANNERS_ADDBANNER}" border = "0" /></a>
                </td><td>
                    <a href = "{$T_BANNERS_BASEURL}&add_banner=1" title = "{$smarty.const._BANNERS_ADDBANNER}">{$smarty.const._BANNERS_ADDBANNER}</a>
                </td></tr>
            </table>
            <div class = "blockHeader" style = "margin-top:10px">{$smarty.const._BANNERS_BANNERS}</div>
            <table style = "margin-left:30px;padding:3px 3px 3px 3px;">
            {section name = 'banners_list' loop = $T_BANNERS.id}
                <tr><td>{$smarty.section.banners_list.iteration}. 
                        <a href = "{$T_BANNERS.link[banners_list]}"><img src="{$T_BANNERS.image_path[banners_list]}" title = "{$T_BANNERS.link[banners_list]}" border = "0" /></a>
                    </td>
                {if $smarty.session.s_type != 'student'}
                    <td><a href = "{$T_BANNERS_BASEURL}&edit_banner={$T_BANNERS.id[banners_list]}"><img src = "images/16x16/edit.png" alt = "{$smarty.const._BANNERS_EDITBANNER}" title = "{$smarty.const._BANNERS_EDITBANNER}" border = "0"/></a>
                        <a href = "{$T_BANNERS_BASEURL}&delete_banner={$T_BANNERS.id[banners_list]}" onclick = "return confirm ('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')"><img src = "images/16x16/error_delete.png" alt = "{$smarty.const._BANNERS_DELETEBANNER}" title = "{$smarty.const._BANNERS_DELETEBANNER}" border = "0"/></a>
                    </td>
                {/if}
                </tr>
                <tr>
                    <td><i>&nbsp;&nbsp;{$T_LINKS.description[links_list]}</i></td>
                </tr>
            {/section}
            </table>
        {else}
            
            {if $T_BANNERS|@sizeof > 0}
            
                {literal}
                    <script type="text/javascript">
                        var bannerarray = new Array(); 
                        var linkarray = new Array(); 
                        var curimg = 0;
                {/literal}
                {$T_BANNERS_JS_INIT}
                {literal}
                        function rotateimages(){
                            document.getElementById("slideshow").setAttribute("src", bannerarray[curimg])
                            document.getElementById("slideshow").setAttribute("title", linkarray[curimg])
                            document.getElementById("link").setAttribute("href", linkarray[curimg])
                            curimg = (curimg < bannerarray.length - 1)? curimg + 1 : 0
                        }
                        
                        window.onload = function(){
                            setInterval("rotateimages()", 2500)
                        }
                    </script>
                {/literal}
                
                <div style="width: 175px; height: 165px; border: 0px;">
                    <a href="{$T_BANNERS.link[0]}" id="link" target = "_blank"><img id="slideshow" border = "0" src = "{$T_BANNERS.image_path[0]}" title="{$T_BANNERS.link[0]}"/></a>
                </div>
            {/if}
        {/if}
    {/capture}
    {eF_template_printBlock title=$smarty.const._BANNERS_BANNERS data=$smarty.capture.t_banners_list_code image= $T_BANNERS_BASELINK|cat:'images/banners32.png' absoluteImagePath = 1}
{/if}


