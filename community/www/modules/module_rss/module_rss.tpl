{* smarty template for rss *}
{if $T_RSS_RSS_MESSAGE}
    <script>
        re = /\?/;
        !re.test(parent.location) ? parent.location = parent.location+'?message={$T_RSS_RSS_MESSAGE}&message_type=success&tab={$smarty.get.tab}' : parent.location = parent.location+'&message={$T_RSS_RSS_MESSAGE}&message_type=success&tab={$smarty.get.tab}';
    </script>
{/if}

{if ($smarty.get.add_feed) || $smarty.get.edit_feed}
 {capture name = "t_add_feed_code"}
  {eF_template_printForm form=$T_RSS_ADD_RSS_FORM}
 {/capture}
 {eF_template_printBlock title=$smarty.const._RSS_ADDRSS data=$smarty.capture.t_add_feed_code image=$T_RSS_MODULE_BASELINK|cat:'images/rss32.png' absoluteImagePath = 1 help = 'RSS'}
{elseif ($smarty.get.add_feed_provider) || $smarty.get.edit_feed_provider}
 {capture name = "t_add_feed_code"}
  {eF_template_printForm form=$T_RSS_PROVIDE_RSS_FORM}
 {/capture}
 {eF_template_printBlock title=$smarty.const._RSS_ADDRSS data=$smarty.capture.t_add_feed_code image=$T_RSS_MODULE_BASELINK|cat:'images/rss32.png' absoluteImagePath = 1 help = 'RSS'}

{else}

    {capture name = 't_rss_code'}
  <div class = "headerTools">
   <span>
    <img src = {$T_RSS_MODULE_BASELINK|cat:'images/add.png'} alt = "{$smarty.const._RSS_ADDFEED}" title = "{$smarty.const._RSS_ADDFEED}">
    <a href = "{$T_RSS_MODULE_BASEURL}&add_feed=1&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._RSS_ADDFEED}', 0)">{$smarty.const._RSS_ADDFEED}</a>
   </span>
  </div>
        <table class = "sortedTable" style = "width:100%">
         <tr>
          <td class = "topTitle">{$smarty.const._RSS_FEEDTITLE}</td>
          <td class = "topTitle">{$smarty.const._RSS_FEEDURL}</td>
          <td class = "topTitle">{$smarty.const._LESSON}</td>
          <td class = "topTitle centerAlign">{$smarty.const._RSS_ACTIVE}</td>
          <td class = "topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
         </tr>
     {foreach name = 'feed_loop' key = "id" item = "feed" from = $T_RSS_FEEDS}
      <tr id="row_{$feed.id}" class = "{cycle values = "oddRowColor, evenRowColor"} {if !$feed.active}deactivatedTableElement{/if}">
       <td>{$feed.title}</td>
       <td>{$feed.url}</td>
       <td>{$T_LESSON_NAMES[$feed.lessons_ID]}</td>
       <td class = "centerAlign">
      {if $feed.active}
        <img class = "ajaxHandle" src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._RSS_ACTIVE}" title = "{$smarty.const._RSS_ACTIVE}" {if $T_RSS_USERTYPE == 'administrator' || $feed.lessons_ID > 0}onclick = "activateFeed(this, '{$feed.id}', 'client')"{/if}/>
      {else}
        <img class = "ajaxHandle" src = "images/16x16/trafficlight_red.png" alt = "{$smarty.const._RSS_INACTIVE}" title = "{$smarty.const._RSS_INACTIVE}" {if $T_RSS_USERTYPE == 'administrator' || $feed.lessons_ID > 0}onclick = "activateFeed(this, '{$feed.id}', 'client')"{/if}/>
      {/if}
       </td class = "centerAlign">
       <td class = "centerAlign">
       {if $T_RSS_USERTYPE == 'administrator' || $feed.lessons_ID > 0}
        <a href = "{$T_RSS_MODULE_BASEURL}&edit_feed={$feed.id}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._RSS_EDITFEED}', 0)"><img src = "{$T_RSS_MODULE_BASELINK}images/edit.png" alt = "{$smarty.const._EDIT}" title = "{$smarty.const._EDIT}" border = "0"></a>
        <img class = "ajaxHandle" src = "{$T_RSS_MODULE_BASELINK}images/error_delete.png" alt = "{$smarty.const._DELETE}" title = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) deleteFeed(this, '{$feed.id}', 'client');">
       {/if}
       </td>
      </tr>
     {foreachelse}
      <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
     {/foreach}
     </table>
    {/capture}

    {capture name = "t_rss_provider_code"}
  <div class = "headerTools">
   {if $T_RSS_USERTYPE == 'administrator' || $feed.lessons_ID > 0}
   <span>
    <img src = {$T_RSS_MODULE_BASELINK|cat:'images/add.png'} alt = "{$smarty.const._RSS_PROVIDEFEED}" title = "{$smarty.const._RSS_PROVIDEFEED}">
    <a href = "{$T_RSS_MODULE_BASEURL}&add_feed_provider=1&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._RSS_PROVIDEFEED}', 0)">{$smarty.const._RSS_PROVIDEFEED}</a>
   </span>
   {/if}
   <span>
    <img src = {$T_RSS_MODULE_BASELINK|cat:'images/add.png'} alt = "{$smarty.const._RSS_PROVIDELESSONFEED}" title = "{$smarty.const._RSS_PROVIDELESSONFEED}">
    <a href = "{$T_RSS_MODULE_BASEURL}&add_feed_provider=1&lesson=1&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._RSS_PROVIDELESSONFEED}', 1)">{$smarty.const._RSS_PROVIDELESSONFEED}</a>
   </span>
  </div>
        <table class = "sortedTable" style = "width:100%">
         <tr>
          <td class = "topTitle">{$smarty.const._RSS_FEEDMODE}</td>
          <td class = "topTitle">{$smarty.const._RSS_FEEDTYPE}</td>
          <td class = "topTitle">{$smarty.const._LESSON}</td>
          <td class = "topTitle">{$smarty.const._RSS_FEEDURL}</td>
          <td class = "topTitle centerAlign">{$smarty.const._RSS_ACTIVE}</td>
          <td class = "topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
         </tr>
     {foreach name = 'feed_loop' key = "id" item = "feed" from = $T_RSS_PROVIDED_FEEDS}
      <tr id="row_{$feed.id}" class = "{cycle values = "oddRowColor, evenRowColor"} {if !$feed.active}deactivatedTableElement{/if}">
       <td>{$T_RSS_PROVIDED_FEEDS_MODES[$feed.mode]}</td>
       <td>{if $feed.mode == 'system'}{$T_RSS_PROVIDED_FEEDS_TYPES[$feed.type]}{else}{$T_RSS_PROVIDED_FEEDS_LESSON_TYPES[$feed.type]}{/if}</td>
       <td>{$T_LESSON_NAMES[$feed.lessons_ID]}</td>
    <td>
    {if $feed.active}
     <a href = "{$smarty.const.G_SERVERNAME}modules/module_rss/rss.php?mode={$feed.mode}&type={$feed.type}{if $feed.lessons_ID}&lesson={$feed.lessons_ID}{/if}">{$smarty.const.G_SERVERNAME}modules/module_rss/rss.php?mode={$feed.mode}&type={$feed.type}{if $feed.lessons_ID}&lesson={$feed.lessons_ID}{/if}</a></td>
    {else}
     {$smarty.const.G_SERVERNAME}modules/module_rss/rss.php?mode={$feed.mode}&type={$feed.type}{if $feed.lessons_ID}&lesson={$feed.lessons_ID}{/if}
    {/if}
       <td class = "centerAlign">
      {if $feed.active}
        <img class = "ajaxHandle" src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._RSS_ACTIVE}" title = "{$smarty.const._RSS_ACTIVE}" {if $T_RSS_USERTYPE == 'administrator' || $feed.lessons_ID}onclick = "activateFeed(this, '{$feed.id}', 'provider')"{/if}/>
      {else}
        <img class = "ajaxHandle" src = "images/16x16/trafficlight_red.png" alt = "{$smarty.const._RSS_INACTIVE}" title = "{$smarty.const._RSS_INACTIVE}" {if $T_RSS_USERTYPE == 'administrator' || $feed.lessons_ID}onclick = "activateFeed(this, '{$feed.id}', 'provider')"{/if}/>
      {/if}
       </td class = "centerAlign">
       <td class = "centerAlign">
        {if $T_RSS_USERTYPE == 'administrator' || $feed.lessons_ID}
        <a href = "{$T_RSS_MODULE_BASEURL}&edit_feed_provider={$feed.id}{if $feed.mode == 'lesson'}&lesson=1{/if}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._RSS_EDITFEED}', 0)"><img src = "{$T_RSS_MODULE_BASELINK}images/edit.png" alt = "{$smarty.const._EDIT}" title = "{$smarty.const._EDIT}"></a>
        <img class = "ajaxHandle" src = "{$T_RSS_MODULE_BASELINK}images/error_delete.png" alt = "{$smarty.const._DELETE}" title = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) deleteFeed(this, '{$feed.id}', 'provider');">
        {/if}
       </td>
      </tr>
     {foreachelse}
      <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
     {/foreach}
     </table>
    {/capture}

 {capture name = 't_rss_tabs_code'}
 <div class = "tabber">
     {eF_template_printBlock tabber = "rss_feeds" title=$smarty.const._RSS_RSSCLIENT data=$smarty.capture.t_rss_code image=$T_RSS_MODULE_BASELINK|cat:'images/rss32.png' absoluteImagePath = 1}
     {eF_template_printBlock tabber = "rss_provider" title=$smarty.const._RSS_RSSPROVIDER data=$smarty.capture.t_rss_provider_code image=$T_RSS_MODULE_BASELINK|cat:'images/rss32.png' absoluteImagePath = 1}
 </div>
 {/capture}
 {eF_template_printBlock title=$smarty.const._RSS_RSS data=$smarty.capture.t_rss_tabs_code image=$T_RSS_MODULE_BASELINK|cat:'images/rss32.png' absoluteImagePath = 1 help = 'RSS'}

{/if}
