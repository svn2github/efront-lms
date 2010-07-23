function getFeeds(refresh) {
 el = $('loading_rss');
 url = rssmodulebaseurl+'&ajax=1';
 parameters = {method: 'get'};
 if (refresh) {
  Object.extend(parameters, {refresh:1})
 }
 ajaxRequest(el, url, parameters, onLoadRss);
}
function onLoadRss(el, response) {
 el.absolutize();
 el.hide();
 $('rss_list').update(response);
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

function scrollFeeds() {
 elements = $('rss_list').select('li');
 $('rss_list').setStyle({height:$('rss_list').getDimensions().height+'px'});
 new Effect.Fade(elements[0], {afterFinish: function (s) {$('rss_list').insert(elements[0].remove())} });
 if (elements.length > 10) {
  new Effect.Appear(elements[11]);
 } else {
  new Effect.Appear(elements[elements.length-1]);
 }
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
  $('rss_list').setStyle({height:''});
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
if ($('loading_rss') && $('rss_list')) {
 var showStatus = 1;
 var pe; //The variable that holds the periodical executer. This way we set him globally and we can stop him whenever we want            
 if (pe) {
  pe.stop();
 }

 if ($('loading_rss') && $('rss_list')) {
  $('loading_rss').clonePosition($('rss_list'));
  $('loading_rss').show();
  $('loading_rss').setOpacity(0.9);
 }
 getFeeds();
 new PeriodicalExecuter(getFeeds, 600);
}

function deleteFeed(el, id, type) {
 parameters = {'delete_feed':id, type:type, method: 'get'};
 var url = location.toString().toString();
 ajaxRequest(el, url, parameters, onDeleteFeed);
}
function onDeleteFeed(el, response) {
 new Effect.Fade(el.up().up());
}
function activateFeed(el, feed, type) {
 if (el.className.match('red')) {
     parameters = {activate_feed:feed, type:type, method: 'get'};
 } else {
  parameters = {deactivate_feed:feed, type:type, method: 'get'};
 }
    var url = location.toString();
    ajaxRequest(el, url, parameters, onActivateFeed);
}
function onActivateFeed(el, response) {
    if (response == 0) {
     setImageSrc(el, 16, "trafficlight_red.png");
        //el.writeAttribute({alt:activate, title:activate});
        el.up().up().addClassName('deactivatedTableElement');
    } else if (response == 1) {
     setImageSrc(el, 16, "trafficlight_green.png");
        //el.writeAttribute({alt:deactivate, title:deactivate});
        el.up().up().removeClassName('deactivatedTableElement');
    }
}
