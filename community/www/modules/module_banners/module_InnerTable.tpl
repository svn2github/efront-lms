{* template functions for inner table *}
{capture name = 't_inner_table_code}
    <table>
       <td>
        {if $T_BANNERS.link|@sizeof > 0}
            {literal}
                <script type="text/javascript">
                    var bannerarray = new Array(); 
                    var linkarray = new Array(); 
                    var curimg = 0;
            {/literal}
            {$T_BANNERS_JS_INIT}
            {literal}
                    function rotateimages(){
                        document.getElementById("slideshow_i").setAttribute("src", bannerarray[curimg])
                        document.getElementById("slideshow_i").setAttribute("title", linkarray[curimg])
                        document.getElementById("link_i").setAttribute("href", linkarray[curimg])
                        curimg = (curimg < bannerarray.length - 1)? curimg + 1 : 0
                    }
                    
                    window.onload = function(){
                        setInterval("rotateimages()", 10000)
                    }
                </script>
            {/literal}
            <a href="{$T_BANNERS.link[0]}" id="link_i" target = "_blank"><img id="slideshow_i" border = "0" src = "{$T_BANNERS.image_path[0]}" title="{$T_BANNERS.link[0]}"/></a>
        {else}
            <i>{$smarty.const._BANNERS_NOBANNERFOUND}</i>
        {/if}
       </td>
    </table>
{/capture}

{eF_template_printBlock title = $smarty.const._BANNERS_BANNERS data = $smarty.capture.t_inner_table_code image = $T_BANNERS_BASELINK|cat:'images/banners32.png' options = $T_BANNERS_INNERTABLE_OPTIONS absoluteImagePath = 1}
