
function eF_js_showDivPopup2(popup_title, size, popup_data_id) {
	var sizes = [{width:500, height:300}, 
	             {width:640, height:420}, 
	             {width:720, height:480}, 
	             {width:800, height:500}];
	var dimensions = sizes[size];

	if (!(main_frame = findFrame(top, 'mainframe'))) {
		main_frame = window;
	}
	side_frame = findFrame(top, 'sideframe');

    document.body.appendChild(new Element('div').addClassName('dimmerDiv').setOpacity(0.9).setStyle({height:getDocHeight()+'px'}));
    if (side_frame) {
    	side_frame.document.body.appendChild(new Element('div').addClassName('dimmerDiv').setOpacity(0.9));
    }

    if (popup_data_id) {
		el = $(popup_data_id);
		el.select('img.removePopupIcon').each(function (s) {s.show();});
	} else {
		el = main_frame.$('popup_frame');
	}
    $$('select.hideSelectBox').each(function(s) {s.setStyle({visibility:'hidden'});});	//fix for IE overlay problem with <select> elements: The elements that have the class name 'hideSelectBox' will disappear automatically when the popup is visible
        
    centerPosition(el.addClassName('visiblePopup').show(), dimensions);
}

function closePopup() {
	if (!(main_frame = findFrame(top, 'mainframe'))) {
		main_frame = top;
	}
	side_frame = findFrame(top, 'sideframe');
	
    main_frame.$$('.dimmerDiv').each(function (s) {s.remove();});
    main_frame.$$('.visiblePopup').each(function (s) {s.hide();});
    if (side_frame) {
    	side_frame.$$('.dimmerDiv').each(function (s) {s.remove();});
    }
    $$('select.hideSelectBox').each(function(s) {s.setStyle({visibility:'visible'});});	//fix for IE overlay problem with <select> elements: The elements that have the class name 'hideSelectBox' will disappear automatically when the popup is visible    
}

function findFrame(win, frame_name) {
	if (win.name == frame_name) {
		return win;
	} 
	for (var i = 0; i < win.frames.length; i++) {	
		if (w = findFrame(win.frames[i], frame_name)) {
			return w;
		}
	}	
	return null;
}

function centerPosition(el, dimensions) {

	Element.extend(document.body);
	offset = document.body.cumulativeScrollOffset();
	// retrieve required dimensions
	var browserDims = document.body.up().getDimensions();

	// calculate the center of the page using the browser and element dimensions
	var y  = (browserDims.height - dimensions.height) / 2;
	var x = (browserDims.width - dimensions.width) / 2;

	// set the style of the element so it is centered
	var styles = { position : 'absolute',
		width	 : dimensions.width + 'px',
		height	 : dimensions.height + 'px',
		zIndex	 : 200,
		top      : y + offset.top  + 'px',
		left     : x + offset.left + 'px' };

	el.setStyle(styles);
}

function getDocHeight(D) {
    if (!D) {var D = document;}
    return Math.max(
        Math.max(D.body.scrollHeight, D.documentElement.scrollHeight),
        Math.max(D.body.offsetHeight, D.documentElement.offsetHeight),
        Math.max(D.body.clientHeight, D.documentElement.clientHeight)
    );
}

function eF_js_showDivPopup(popup_title, size, popup_data_id) {

	//From now on there are only 3 possible sizes: small, medium, big. Old values are automatically converted to one of them
	var sizes = [new Array('500px', '300px'), new Array('640px', '400px'), new Array('720px', '500px'), new Array('800px', '550px'), new Array('940px', '650px')];
	var popup_dim = sizes[size];

    parent.mainframe ? main_frame = parent.mainframe : main_frame = window;
    parent.sideframe ? side_frame = parent.sideframe : side_frame = window;

    var popup_table = main_frame.$('popup_table');
    var dimmer      = main_frame.$('dimmer');
    
    if (side_frame.document.getElementById('dimmer')) {
    	var dimmer_side = side_frame.$('dimmer');
	}
	
    if (!popup_table.visible()) {
    	if ($('scormFrameID')) {
    		$('scormFrameID').style.visibility = 'hidden';
    	}
        main_frame.$('popup_title').update(popup_title); 
        if (popup_data_id) {
            main_frame.$('popup_data').insert(main_frame.$(popup_data_id).remove().show());
            //main_frame.document.getElementById('popup_data').parentNode.replaceChild(main_frame.document.getElementById('popup_data'), main_frame.document.getElementById(popup_data_id));
            //main_frame.document.getElementById(popup_data_id).innerHTML     = '';
            main_frame.$('popup_data').show();
            //main_frame.$('frame_data').setStyle({height:'100%'});
            //alert(main_frame.$('frame_data').down());//.down().addClassName("popup");
            main_frame.$('frame_data').hide();
            main_frame.$('popup_close').name = popup_data_id;
        } else {
            main_frame.$('popup_data').hide();
            main_frame.$('frame_data').show();
            //main_frame.$('frame_data').setStyle({height:'100%'});
        }
        $$('select.hideSelectBox').each(function(s) {s.setStyle({visibility:'hidden'});});	//fix for IE overlay problem with <select> elements: The elements that have the class name 'hideSelectBox' will disappear automatically when the popup is visible

        if (dimmer) {
            if (main_frame.document.documentElement) {                                   //IE in strict mode uses documentElement in place of body
                dimmer.style.height = main_frame.document.documentElement.scrollHeight+'px';
            } else {
                dimmer.style.height = main_frame.document.body.scrollHeight+'px';
            }
            
            dimmer.show();
        }
        if (dimmer_side) {
            if (side_frame.document.documentElement) {                                   //IE in strict mode uses documentElement in place of body
                dimmer_side.style.height = side_frame.document.documentElement.scrollHeight+'px';
            } else {
                dimmer_side.style.height = side_frame.document.body.scrollHeight+'px';
            }
            dimmer_side.show();
        }
        popup_table.setStyle({width:popup_dim[0], height:popup_dim[1]});
        popup_table.show();
        //.style.width      = popup_dim[0];
        //popup_table.style.height     = popup_dim[1];
        //popup_table.style.height     = popup_table.down().getDimensions().height;

        main_frame.innerHeight ? window_height = main_frame.innerHeight : window_height = main_frame.document.body.offsetHeight;     //Window height for mozilla/IE
        main_frame.pageYOffset ? scroll_offset = main_frame.pageYOffset : scroll_offset = main_frame.document.documentElement.scrollTop;        //scrolling offset for mozilla/IE

        popup_table.style.marginLeft = popup_table.offsetWidth  < popup_table.parentNode.offsetWidth  ? parseInt((popup_table.parentNode.offsetWidth  - popup_table.offsetWidth)  / 2) + "px" : "0";    //Bring it to the center of the screen
        popup_table.style.marginTop  = popup_table.offsetHeight < window_height ? parseInt(scroll_offset + ((window_height - popup_table.offsetHeight) / 2)) + "px" : scroll_offset + "px";    //Bring it to the middle of the screen        

    } else if (popup_title == '' && size == '') {
    	if ($('scormFrameID')) {
    		$('scormFrameID').style.visibility = 'visible';
    	}
        $$('select.hideSelectBox').each(function(s) {s.setStyle({visibility:'visible'});});	//fix for IE overlay problem with <select> elements: The elements that have the class name 'hideSelectBox' will disappear automatically when the popup is visible
        if (popup_data_id) {
        	Element.extend(main_frame.document.body);
            main_frame.document.body.insert(main_frame.$(popup_data_id).remove().hide());
        }
        popup_table.hide();
        if (dimmer)      dimmer.hide();
        if (dimmer_side) dimmer_side.hide();

        popup_table.style.marginLeft = '0px';
        popup_table.style.marginTop  = '0px';

        main_frame.document.getElementById('popup_title').update('');
        //main_frame.document.getElementById('popup_data').innerHTML  = '';
        main_frame.document.getElementById('popup_frame').src       = '';
        main_frame.document.getElementById('popup_close').name      = '';
    }
}


function eF_js_keypress(e) {
    var kC  = (window.event) ?    // MSIE or Firefox?
             event.keyCode : e.keyCode;
    var Esc = (window.event) ?
            27 : e.DOM_VK_ESCAPE // MSIE : Firefox
    if (kC == Esc) {
    	//closePopup();
        if (typeof(main_frame) != 'undefined' && main_frame.document.getElementById('popup_close') && main_frame.document.getElementById('dimmer').style.display != 'none')
            main_frame.document.getElementById('popup_close').onclick();
    	
    }
}



function show_hide(obj, name) {
    var el = document.getElementById(name);
    if (el.style.display== 'none') {
        el.style.display= '';
        obj.src = 'themes/default/images/others/minus.png';
    } else {
        el.style.display = 'none';
        obj.src = 'themes/default/images/others/plus.png';
    }
}

/**
* Set element display to '' or 'none'
*/
function eF_js_showHide(el_id) {
    el = document.getElementById(el_id);
    if (el.style.display == 'none') {
        el.style.display = '';
    } else {
        el.style.display = 'none';
    }
}


/**
* Set element display to '' or 'none' and position it to the event coordinates
*/
function eF_js_showHideDiv(target, el_id, e) {
    Event.pointerX(e) + $(el_id).getWidth()  > Element.getWidth(document.body)  ? x = Event.pointerX(e) - $(el_id).getWidth()  : x = Event.pointerX(e);
    Event.pointerY(e) + $(el_id).getHeight() > Element.getHeight(document.body) ? y = Event.pointerY(e) - $(el_id).getHeight() : y = Event.pointerY(e);
    $(el_id).setStyle({left:x+'px', top:y+'px'}).toggle();
}

/**
* Toggles visibility of inner tables
*/
function toggleVisibility(obj, img)
{
	if (!obj) {
		return;
	}
	Element.extend(obj);

	if (obj.visible()) {
		obj.hide();
		img && img.hasClassName('sprite16-navigate_up') ? img.removeClassName('sprite16-navigate_up').addClassName('sprite16-navigate_down') : null;
		return 'hidden';
	} else {
		obj.show();
		img && img.hasClassName('sprite16-navigate_down') ? img.removeClassName('sprite16-navigate_down').addClassName('sprite16-navigate_up') : null;
		return 'visible';
	}
	
	
}

function eF_js_findPos(obj) {
	var curleft = curtop = 0;
	if (obj.offsetParent) {
		curleft = obj.offsetLeft
		curtop = obj.offsetTop
		while (obj = obj.offsetParent) {
			curleft += obj.offsetLeft
			curtop += obj.offsetTop
		}
	}
	return [curleft,curtop];
}


function createCookie(name,value,days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}
function getCookie( name ) {
	var start = document.cookie.indexOf( name + "=" );
	var len = start + name.length + 1;
	if ( ( !start ) && ( name != document.cookie.substring( 0, name.length ) ) ) {
		return null;
	}
	if ( start == -1 ) return null;
	var end = document.cookie.indexOf( ';', len );
	if ( end == -1 ) end = document.cookie.length;
	return unescape( document.cookie.substring( len, end ) );
}

function setCookie( name, value, expires, path, domain, secure ) {
	var today = new Date();
	today.setTime( today.getTime() );
	if ( expires ) {
		expires = expires * 1000 * 60 * 60 * 24;
	}
	var expires_date = new Date( today.getTime() + (expires) );
	document.cookie = name+'='+escape( value ) +
		( ( expires ) ? ';expires='+expires_date.toGMTString() : '' ) + //expires.toGMTString()
		( ( path ) ? ';path=' + path : '' ) +
		( ( domain ) ? ';domain=' + domain : '' ) +
		( ( secure ) ? ';secure' : '' );
}

function deleteCookie( name, path, domain ) {
	if ( getCookie( name ) ) document.cookie = name + '=' +
			( ( path ) ? ';path=' + path : '') +
			( ( domain ) ? ';domain=' + domain : '' ) +
			';expires=Thu, 01-Jan-1970 00:00:01 GMT';
}

function showMessage(message, type) {
	if (type == 'failure') {
		message = '<div class = "failure" style = "font-size:16px"><img src = "themes/default/images/32x32/warning.png" style = "float:left"><span style = "vertical-align:middle">'+message+'</span></div>'
	} else {
		message = '<div class = "success" style = "font-size:16px"><img src = "themes/default/images/32x32/success.png" style = "float:left"><span style = "vertical-align:middle">'+message+'</span></div>'
	}
	$('showMessageDiv').update(message);	
	eF_js_showDivPopup('', 0, 'showMessageDiv');
}




/**
 * Function that hides the sidebar
 * 
 * This function hides the left sidebar, without setting any cookie
 * @return
 */
function hideLeftSidebar() {
   	//top.document.getElementById('framesetId').cols = top.global_sideframe_width+", *";

    top.document.getElementById('framesetId').cols = "18, *";
    top.sideframe.document.body.style.paddingLeft = "130px";
    
    top.sideframe.$('toggleSidebarImage').src = 'themes/default/images/others/transparent.gif';
    top.sideframe.$('toggleSidebarImage').addClassName('sprite16').removeClassName('sprite16-navigate_left').addClassName('sprite16-navigate_right');
    top.sideframe.$('toggleSidebarImage').setStyle({position:"absolute", left:'0px', top:'4px'});
    top.sideframe.$('toggleSidebarImage').onclick  = showLeftSidebar;
    //top.sideframe.$('logoutImage').setStyle({position:"absolute", left:'1px', top:'45px'});
    //top.sideframe.$('mainPageImage').setStyle({position:"absolute", left:'1px', top:'25px'});

	var menus = top.sideframe.document.getElementById('menu').childElements().length - 1; 
	var i = 2;
       for (i = 2; i <= menus; i++) {
		if (top.sideframe.document.getElementById('menu'+i)) {
			top.sideframe.document.getElementById('menu'+i).style.visibility = "hidden";
		}        
    }
}

/**
 * Function that shows the sidebar
 * 
 * This function shows the left sidebar, without setting any cookie
 * @return
 */
function showLeftSidebar() {
	if (top.sideframe && top.sideframe.document.body) {
		top.document.getElementById('framesetId').cols = top.global_sideframe_width + ", *";
	    top.sideframe.document.body.style.paddingLeft = "0px";
	    
	    top.sideframe.setArrowStatus('down');
	    top.sideframe.initArrows();
	
	    top.sideframe.$('toggleSidebarImage').src = 'themes/default/images/16x16/navigate_left.png';
	    top.sideframe.$('toggleSidebarImage').addClassName('sprite16').removeClassName('sprite16-navigate_right').addClassName('sprite16-navigate_left');
	    top.sideframe.$('toggleSidebarImage').setStyle({position:"absolute", left:top.global_sideframe_width-16+'px', top:'4px'});
	    //top.sideframe.$('logoutImage').setStyle({position:"absolute", left:'1000px', top:'45px'});
	    //top.sideframe.$('mainPageImage').setStyle({position:"absolute", left:'1000px', top:'25px'});
	    top.sideframe.$('toggleSidebarImage').onclick  = hideLeftSidebar;
/*	
	    var menus = top.sideframe.$('menu').childElements().length - 1;
	    alert('prin') ;
	    for (var i = 2; i <= menus; i++) {
	    alert(i);
	    alert(top.sideframe.$('menu'+i).id);
	        if (top.sideframe.$('menu'+i)) {
	            top.sideframe.$('menu'+i).show();
	        }
	    }
	    alert('meta') ;
*/
		//alert(top.sideframe.document.getElementById('menu'));
		var menus = top.sideframe.document.getElementById('menu').childElements().length - 1;
		var i = 2;
        for (i = 2; i <= menus; i++) {
			if (top.sideframe.document.getElementById('menu'+i)) {
				top.sideframe.document.getElementById('menu'+i).style.visibility = "visible";
			}        
        }	    	
	}
}

/**
 * Function that toggles blocks
 * 
 * This function is used to toggle the inner blocks displayed throughout the system
 * It expands/collapses a block based on its current state  
 * If cookie is defined, it stores the open/close value in a cookie with this name
 * 
 * @param el The html element that was clicked
 * @param cookie Whether to create cookie
 */
function toggleBlock(el, cookie) {
	Element.extend(el);
	hideBlock = el.up().up().select('div.content')[0];

	if (el.hasClassName('open')) {
		new Effect.BlindUp(hideBlock, {duration:0.5});
		el.removeClassName('open').addClassName('close');
		setImageSrc(el, 16, 'navigate_down');
		//.removeClassName('sprite16-navigate_up').addClassName('sprite16-navigate_down');
		//el.src = "themes/default/images/16x16/navigate_down.png";
		cookie ? createCookie('innerTables['+cookie+']', 'hidden') : null;
	} else {
		new Effect.BlindDown(hideBlock, {duration:0.5});
		el.removeClassName('close').addClassName('open');
		setImageSrc(el, 16, 'navigate_up');

		//.removeClassName('sprite16-navigate_down').addClassName('sprite16-navigate_up');
		//el.src = "themes/default/images/16x16/navigate_up.png";
		cookie ? createCookie('innerTables['+cookie+']', 'visible') : null;
	}	
}

/**
 * Function that toggles right sidebar
 * 
 * This function is used to toggle the right sidebar displayed in some student/professor pages
 * It expands/collapses the sidebar based on its current state  
 * If cookie is defined, it stores the open/close value in a cookie with this name
 * 
 * @param el The html element that was clicked
 * @param cookie Whether to create cookie 
 */
function toggleRightSidebar(el, cookie) {
	Element.extend(el);
	if (el.className.match('right')) {
		//new Effect.Fade($('sideColumn'), {duration:1});
		el.removeClassName('sprite16-navigate_right').addClassName('sprite16-navigate_left');
		cookie ? createCookie('rightSideBar', 'hidden') : null;
		$('pageLayout').addClassName("centerFull");//.removeClassName("hideLeft").removeClassName("hideRight");
	} else {
		$('pageLayout').removeClassName("centerFull").addClassName("hideLeft");//.removeClassName("hideRight");
		//new Effect.Appear($('sideColumn'), {duration:0.5});
		el.removeClassName('sprite16-navigate_left').addClassName('sprite16-navigate_right');		
		cookie ? createCookie('rightSideBar', 'visible') : null;
	}	
}

function toggleHorizontalSidebar(el, cookie) {
	Element.extend(el);
	if (el.className.match('up')) {
		el.removeClassName('sprite16-navigate_up').addClassName('sprite16-navigate_down');
		cookie ? createCookie('horizontalSideBar', 'hidden') : null;
		$('logo').setStyle({display:'none'});
		$('horizontalBarRow').removeClassName('header').addClassName('headerHidden');
		$('tab_handles_div').insert($('tab_handles').remove());
		$('tab_handles').setStyle({float:'right'});
	} else {
		el.removeClassName('sprite16-navigate_down').addClassName('sprite16-navigate_up');		
		cookie ? createCookie('horizontalSideBar', 'visible') : null;
		$('logo').setStyle({display:'block'});
		$('horizontalBarRow').removeClassName('headerHidden').addClassName('header');
		$('logout_link').insert($('tab_handles').remove());
		$('tab_handles').setStyle({float:''});
	}	
	
}


function setImageSrc(el, dim, img) {
	if (1) {
		re = new RegExp('sprite'+dim+'-(.*)');
				
		if (el.className.match(re)) {
			currentImage = el.className.match(re)[1];
			el.removeClassName('sprite'+dim+'-'+currentImage).addClassName('sprite'+dim+'-'+img.replace(/\.png/, ''));
		} else {
			el.addClassName('sprite16').addClassName('sprite'+dim+'-'+img.replace(/\.png/, ''));
		}
	} else {
		el.src = 'images/'+dim+'x'+dim+'/'+img;
	}	
}

function getBookmarks(el) {
	parameters = {bookmarks:'get', method: 'get'};
	var url    = window.location.toString().split("?")[0]+'?ctg=content';
	//alert(url);
	ajaxRequest(el, url, parameters, onGetBookmarks);	
}
function onGetBookmarks(el, response) {
	if ($('bookmarks_div')) {
		bookmarks = $('bookmarks_div');
		bookmarks.update('');
	
		if (response.evalJSON(true) != '') {
			$H(response.evalJSON(true)).each(function (s) {
				bookmarks.insert(
							new Element('div')
								.insert(new Element('a', {href:s[1].url}).update(s[1].lesson_name+': '+s[1].name))
								.insert(new Element('span').update('&nbsp;'))
								.insert(new Element('img', {src:'themes/default/images/others/transparent.gif', onclick:'removeBookmark(this, '+s[1].id+')'}).addClassName('handle').addClassName('sprite16').addClassName('sprite16-error_delete'))
							);
			});
			//document.body.appendChild(bookmarks);
		} else {
			bookmarks.insert(new Element('div').update(NODATAFOUND).addClassName('emptyCategory'));
		}
	    eF_js_showDivPopup(BOOKMARKTRANSLATION, 1, 'bookmarks_div_code');
	}
}
function addBookmark(el) {
	parameters = {bookmarks:'add', method: 'get'};
	var url    = window.location.toString();
	ajaxRequest(el, url, parameters);	
}
function removeBookmark(el, id) {
	parameters = {bookmarks:'remove', id: id, method: 'get'};
	var url    = window.location.toString();
	ajaxRequest(el, url, parameters, onRemoveBookmark);	
}
function onRemoveBookmark(el, response) {
	new Effect.Fade(el.up());
}

function changeAccount(login) {
    new Ajax.Request('change_account.php?login='+login, {
        method:'get',
        asynchronous:true,
        onFailure: function (transport) {
            showMessage(transport.responseText, 'failure');
        },
        onSuccess: function (transport) {
            top.location = transport.responseText;
        }
    });
}

function handleException(e) {
	if (typeof(_DEBUG) != 'undefined' && _DEBUG) {
		var errorDetails = '';
		for (var i in e) {
			errorDetails += e[i] + '<br>';
		}
		$('defaultExceptionHandlerDiv').update(errorDetails);
		eF_js_showDivPopup('Error details', 2, 'defaultExceptionHandlerDiv');
	} else {
		alert(e);
	}
}
// used in printBlock for help popups
function PopupCenter(pageURL, title,w,h) {
var left = (screen.width/2)-(w/2);
var top = (screen.height/2)-(h/2);
var targetWin = window.open (pageURL, title, 'scrollbars=yes, width='+w+', height='+h+', top='+top+', left='+left);
}


function periodicUpdater() {
	ajaxRequest(document.body, 'periodic_updater.php', {method:'get'}, onPeriodicUpdater);
}

function onPeriodicUpdater(el, response) {
	if (response.evalJSON().status) {
		messages    = response.evalJSON().messages;
		onlineUsers = response.evalJSON().online;//alert(onlineUsers);
		if ($('header_total_messages')) {
			if (messages > 0) {
				$('header_total_messages').update('&nbsp;('+messages+')');
			} else {
				$('header_total_messages').update('');
			}
		}	
		if ($('header_connected_users')) {
			if (onlineUsers.length > 0) {
				$('header_connected_users').update(onlineUsers.length);
				onlineUsersString = '';
				onlineUsers.each(function (s, i) {
					if (i > 0) {
						s.formattedLogin = ', '+ s.formattedLogin;
					}
					onlineUsersString += s.formattedLogin;
				});
				$('header_connected_users').next().update(onlineUsersString);
			} else {
				$('header_connected_users').update('');
			}
		}	
	}
//{"messages":"0","online":[{"login":"admin","formattedLogin":"Administrator S. (admin)","user_type":"administrator","timestamp_now":"1292775277","session_timestamp":"1292775277","time":{"seconds":14,"minutes":2,"hours":0,"total_seconds":134,"time_string":"2_MINUTESSHORTHAND 14_SECONDSSHORTHAND"}}]}	
}
function startUpdaterFunction() {
    setTimeout("periodicUpdater()", 2500);
    if (typeof(updaterPeriod) != 'undefined') {
    	setInterval("periodicUpdater()", updaterPeriod);
    }
}

if (typeof(startUpdater) != 'undefined' && startUpdater) { startUpdaterFunction();}

