{* smarty template for rss - control panel menu*}
{if $T_RSS_NUM_FEEDS > 0}
    {capture name = "t_rss_code"}
        	
        	<table style = "width:100%;">
        		<tr><td style = "height:220px;vertical-align:top;">
{*
                	<ul id = "rss_list2" nload = "getFeeds()" style = "height:250px;border:1px solid red">
                		<li style = "display:none"><a href = "">MOVE ME1</a></li>
                		<li style = "display:none"><a href = "">MOVE ME2</a></li>
                		<li style = "display:none"><a href = "">MOVE ME3</a></li>
                		<li style = "display:none"><a href = "">MOVE ME4</a></li>
                		<li style = "display:none"><a href = "">MOVE ME5</a></li>
                		<li style = "display:none"><a href = "">MOVE ME6</a></li>
                		<li style = "display:none"><a href = "">MOVE ME7</a></li>
                		<li style = "display:none"><a href = "">MOVE ME8</a></li>
                	</ul>
*}                	
                	<ul id = "rss_list" style = "padding-left:0px;margin-left:0px">
                	
                	</ul>
        		</td></tr>
{*        		<tr><td><a href = "javascript:void(0)" onclick = "sss()" style = >click!</a></td></tr>*}
        	</table>

        	<div id = "loading_rss" style = "background-color:#F8F8F8;">
        		<div style = "top:50%;left:45%;position:absolute">
        			<img src = "{$T_RSS_MODULE_BASELINK}images/progress_big.gif" style = "vertical-align:middle">
        		</div>
        	</div>
			<script>
				var rssmodulebaseurl = '{$T_RSS_MODULE_BASEURL}';
				var rssmodulebaselink = '{$T_RSS_MODULE_BASELINK}';
			</script>
    {/capture}
    
    {eF_template_printBlock title=$smarty.const._RSS_RSS data=$smarty.capture.t_rss_code image= $T_RSS_MODULE_BASELINK|cat:'images/rss32.png' absoluteImagePath = 1 options = $T_RSS_OPTIONS}
{/if}


{*
/*
		var count = 0;
		function scrollFeeds() {
			scrollMe();						
		}
		
		function scrollMe() {
    			$('rss_list2').select('li').each(function (obj) {
    				new Effect.Appear(obj, {delay:count++, afterFinish: function (s) {if (obj.positionedOffset().top > 100) {new Effect.Fade($('rss_list2').select('li')[0], {afterFinish: function (s) {$('rss_list2').insert($('rss_list2').select('li')[0].remove())}})}; if (!obj.next()) {scrollMe()} }});    				
    			});
		}    

        function scrollFeeds() {
        	//$('rss_list').select('li')[count++].show(); 
        	$('rss_list2').select('li').each(function (obj) {       	
            	new Effect.Appear(obj, {queue:'front'});
            	new Effect.Move(obj, { x: 0, y: -100, mode: 'relative', duration: 8, fps: 20});
            	new Effect.Fade(obj, {delay:7, afterFinish: function (s) {alert(obj)} });
            });
        }
*/   
*}