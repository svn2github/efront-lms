if ($('loading_rss') && $('rss_list')) {


	var showStatus = 1;
	var pe;													//The variable that holds the periodical executer. This way we set him globally and we can stop him whenever we want            
	function getFeeds() {
		if (pe) {
			pe.stop();
		}

		if ($('loading_rss') && $('rss_list')) {
			$('loading_rss').clonePosition($('rss_list'));
			$('loading_rss').show();
			$('loading_rss').setOpacity(0.9);
		}
		url = rssmodulebaseurl+'&ajax=1';
		new Ajax.Request(url, {
			method:'get',
			asynchronous:true,
			onSuccess: function (transport) {
			$('loading_rss').absolutize();
			$('loading_rss').hide();
			$('rss_list').update(transport.responseText);
			elements = $('rss_list').select('li');
			for (var i = 0; i < 10; i++) {                  	              		
				if (elements[i]) {
					elements[i].show();
				}
			}
			if (elements.length > 10) {
				pe = new PeriodicalExecuter(scrollFeeds, 3);
			}
		}
		});
	}
	getFeeds();
	new PeriodicalExecuter(getFeeds, 600);
}
		function scrollFeeds() {
			elements = $('rss_list').select('li');
			new Effect.Fade(elements[0], {afterFinish: function (s) {$('rss_list').insert(elements[0].remove())} });
			
			new Effect.Appear(elements[11]);
		}

		function pauseList() {
			if (pe) {
				pe.stop();
			}
		}
		function continueList() {
			if (pe) {
				pe.stop();
			}
			elements = $('rss_list').select('li');
        	if (elements.length > 10) {
	        	pe = new PeriodicalExecuter(scrollFeeds, 3);
	        }
		}
		function showHideAll(el) {
    		pauseList();
    		if (showStatus) {
    			$('rss_list').up().setStyle({height:''});
    			$('rss_list').select('li').each(function (s) {s.show()});
		        showStatus = 0;
		        Element.extend(el);
		        el.down().src = rssmodulebaselink+'images/arrow_up_blue.png';  		        			
    		} else {
            	elements = $('rss_list').select('li');            	
            	for (var i = 10; i < elements.length; i++) {                  	              		
            		if (elements[i]) {
            			elements[i].hide();
            		}
            	}
            	if (elements.length > 10) {
		        	pe = new PeriodicalExecuter(scrollFeeds, 3);
		        }  
		        showStatus = 1;
		        Element.extend(el);
		        el.down().src = rssmodulebaselink+'images/arrow_down_blue.png';  			
    		}
		}
