{* smarty template for rss - control panel menu*}
{if $T_RSS_NUM_FEEDS > 0}
    {capture name = "t_rss_code"}
{strip}

         <table style = "width:100%;">
          <tr><td style = "vertical-align:top;">
                 <ul id = "rss_list" style = "padding-left:0px;margin-left:0px;list-style-type:none;">

                 </ul>
          </td></tr>
         </table>

         <div id = "loading_rss" style = "background-color:#F8F8F8;">
          <div tyle = "top:50%;left:45%;position:absolute">
           <img src = "{$T_RSS_MODULE_BASELINK}images/progress_big.gif" style = "vertical-align:middle">
          </div>
         </div>
   <script>
    var rssmodulebaseurl = '{$T_RSS_MODULE_BASEURL}';
    var rssmodulebaselink = '{$T_RSS_MODULE_BASELINK}';
   </script>
{/strip}
    {/capture}

    {eF_template_printBlock title=$smarty.const._RSS_RSS data=$smarty.capture.t_rss_code image= $T_RSS_MODULE_BASELINK|cat:'images/rss32.png' absoluteImagePath = 1 options = $T_RSS_OPTIONS link = $T_RSS_MODULE_BASEURL}
{/if}
