{* smarty template for rss *}
{if $T_RSS_RSS_MESSAGE}
    <script>
        re = /\?/;
        !re.test(parent.location) ? parent.location = parent.location+'?message={$T_RSS_RSS_MESSAGE}&message_type=success' : parent.location = parent.location+'&message={$T_RSS_RSS_MESSAGE}&message_type=success';            
    </script>
{/if}

{if ($smarty.get.add_feed) || $smarty.get.edit_feed}
	{capture name = "t_add_feed_code"}
        {$T_RSS_ADD_RSS_FORM.javascript}
        <form {$T_RSS_ADD_RSS_FORM.attributes}>
        {$T_RSS_ADD_RSS_FORM.hidden}
        <table>
            <tr><td class = "labelCell">{$T_RSS_ADD_RSS_FORM.title.label}:&nbsp;</td>
                <td class = "elementCell">{$T_RSS_ADD_RSS_FORM.title.html}</td></tr>
            {if $T_RSS_ADD_RSS_FORM.title.error}<tr><td></td><td class = "formError">{$T_RSS_ADD_RSS_FORM.title.error}</td></tr>{/if}
            <tr><td class = "labelCell">{$T_RSS_ADD_RSS_FORM.url.label}:&nbsp;</td>
                <td class = "elementCell">{$T_RSS_ADD_RSS_FORM.url.html}</td></tr>
            {if $T_RSS_ADD_RSS_FORM.url.error}<tr><td></td><td class = "formError">{$T_RSS_ADD_RSS_FORM.url.error}</td></tr>{/if}
            <tr><td class = "labelCell">{$T_RSS_ADD_RSS_FORM.lessons_ID.label}:&nbsp;</td>
                <td class = "elementCell">{$T_RSS_ADD_RSS_FORM.lessons_ID.html}</td></tr>
            {if $T_RSS_ADD_RSS_FORM.lessons_ID.error}<tr><td></td><td class = "formError">{$T_RSS_ADD_RSS_FORM.lessons_ID.error}</td></tr>{/if}
            <tr><td class = "labelCell">{$T_RSS_ADD_RSS_FORM.active.label}:&nbsp;</td>
                <td class = "elementCell">{$T_RSS_ADD_RSS_FORM.active.html}</td></tr>
            <tr><td colspan = "2">&nbsp;</td></tr>
            <tr><td></td><td class = "elementCell">{$T_RSS_ADD_RSS_FORM.submit.html}</td></tr>
        </table>
        </form>
	{/capture}
	{eF_template_printBlock title=$smarty.const._RSS_ADDRSS data=$smarty.capture.t_add_feed_code image=$T_RSS_MODULE_BASELINK|cat:'images/rss32.png' absoluteImagePath = 1}

{else} 

    {capture name = 't_rss_code'}
    	<table>
    		<tr><td>
    				<img src = {$T_RSS_MODULE_BASELINK|cat:'images/add.png'} alt = "{$smarty.const._RSS_ADDFEED}" title = "{$smarty.const._RSS_ADDFEED}" style = "vertical-align:middle">
    				<a href = "{$T_RSS_MODULE_BASEURL}&add_feed=1&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._RSS_ADDFEED}', 0)">{$smarty.const._RSS_ADDFEED}</a>
    			</td></tr>
    	</table>
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
    			<td>{$feed.lesson}</td>
    			<td class = "centerAlign">
    				<a href = "javascript:void(0)" onclick = "activateFeed(this, {$feed.id})">
    		{if $feed.active}
    					<img src = "{$T_RSS_MODULE_BASELINK}images/trafficlight_green.png" alt = "{$smarty.const._RSS_ACTIVE}" title = "{$smarty.const._RSS_ACTIVE}" border = "0"/>
    		{else}
    					<img src = "{$T_RSS_MODULE_BASELINK}images/trafficlight_red.png" alt = "{$smarty.const._RSS_INACTIVE}" title = "{$smarty.const._RSS_INACTIVE}" border = "0"/>
    		{/if}
    				</a>
    			</td class = "centerAlign">
    			<td class = "centerAlign">
    				<a href = "{$T_RSS_MODULE_BASEURL}&edit_feed={$feed.id}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._RSS_EDITFEED}', 0)"><img src = "{$T_RSS_MODULE_BASELINK}images/edit.png" alt = "{$smarty.const._EDIT}" title = "{$smarty.const._EDIT}" border = "0"></a>
    				<a href = "javascript:void(0)" onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) deleteFeed(this, {$feed.id});"><img src = "{$T_RSS_MODULE_BASELINK}images/error_delete.png" alt = "{$smarty.const._DELETE}" title = "{$smarty.const._DELETE}" border = "0"></a>
    			</td>
    		</tr>
    	{foreachelse}
    		<tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>    		
    	{/foreach}
    	</table>	
    {/capture}
    
    {eF_template_printBlock title=$smarty.const._RSS_RSS data=$smarty.capture.t_rss_code image=$T_RSS_MODULE_BASELINK|cat:'images/rss32.png' absoluteImagePath = 1}

    <script>
    {literal}
    function deleteFeed(el, id) {
        Element.extend(el);
        url = '{/literal}{$T_RSS_MODULE_BASEURL}{literal}&delete_feed='+id;

        var img = new Element('img', {id: 'img_'+id, src:'{/literal}{$T_RSS_MODULE_BASELINK}{literal}images/progress1.gif'}).setStyle({position:'absolute'});
        img_id = img.identify();
        el.up().insert(img);

        new Ajax.Request(url, {
            method:'get',
            asynchronous:true,
            onFailure: function (transport) {
                img.writeAttribute({src:'{/literal}{$T_RSS_MODULE_BASELINK}{literal}images/error_delete.png', title: transport.responseText}).hide();
                new Effect.Appear(img_id);
                window.setTimeout('Effect.Fade("'+img_id+'")', 10000);
            },
            onSuccess: function (transport) {
                img.hide();
                new Effect.Fade(el.up().up(), {queue:'end'});
                }
            });
    }
 
    function activateFeed(el, id) {
        Element.extend(el);
        if (el.down().src.match('red')) {
            url = '{/literal}{$T_RSS_MODULE_BASEURL}{literal}&activate_feed='+id;
            newSource = '{/literal}{$T_RSS_MODULE_BASELINK}{literal}images/trafficlight_green.png';
        } else {
            url = '{/literal}{$T_RSS_MODULE_BASEURL}{literal}&deactivate_feed='+id;
            newSource = '{/literal}{$T_RSS_MODULE_BASELINK}{literal}images/trafficlight_red.png';
        }

        var img = new Element('img', {id: 'img_'+id, src:'{/literal}{$T_RSS_MODULE_BASELINK}{literal}images/progress1.gif'}).setStyle({position:'absolute'});
        el.up().insert(img);
        el.down().src = '{/literal}{$T_RSS_MODULE_BASELINK}{literal}images/trafficlight_yellow.png';
        new Ajax.Request(url, {
            method:'get',
            asynchronous:true,
            onFailure: function (transport) {
                img.writeAttribute({src:'{/literal}{$T_RSS_MODULE_BASELINK}{literal}images/error_delete.png', title: transport.responseText}).hide();
                new Effect.Appear(img_id);
                window.setTimeout('Effect.Fade("'+img_id+'")', 10000);
            },
            onSuccess: function (transport) {
                img.hide();
                el.down().src = newSource;
                new Effect.Appear(el.down(), {queue:'end'});

                if (el.down().src.match('green')) {
                    var cName = $('row_'+id).className.split(" ");
                    $('row_'+id).className = cName[0];
                } else {
                    $('row_'+id).className += " deactivatedTableElement";
                }
                }
            });
    }
    {/literal}
    </script>

{/if}
