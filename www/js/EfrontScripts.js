//This function is used to show / hide the div popup
function eF_js_showDivPopup(popup_title, size, popup_data_id) {

	//From now on there are only 3 possible sizes: small, medium, big. Old values are automatically converted to one of them
	var sizes = [new Array('500px', '300px'), new Array('640px', '400px'), new Array('720px', '480px'), new Array('800px', '500px')];
	if (size instanceof Array) {
		if (size[2] == 'string') {							//If there is a third argument, 'string', then specifically use specified dimensions
			popup_dim = size;
		} else if (parseInt(size[0]) < 640) {
			popup_dim = sizes[0];
		} else if (parseInt(size[0]) < 720) {
			popup_dim = sizes[1];
		} else {
			popup_dim = sizes[2];
		}
	} else {
		popup_dim = sizes[size];
	}

    parent.mainframe ? main_frame = parent.mainframe : main_frame = window;
    parent.sideframe ? side_frame = parent.sideframe : side_frame = window;

    var popup_table = main_frame.document.getElementById('popup_table');
    var dimmer      = main_frame.document.getElementById('dimmer');
    var dimmer_side = side_frame.document.getElementById('dimmer');

    if (popup_table.style.display == 'none') {
        main_frame.document.getElementById('popup_title').innerHTML = popup_title; 
        if (popup_data_id) {
            main_frame.document.getElementById('popup_data').innerHTML      = main_frame.document.getElementById(popup_data_id).innerHTML;
            //main_frame.document.getElementById('popup_data').parentNode.replaceChild(main_frame.document.getElementById('popup_data'), main_frame.document.getElementById(popup_data_id));
            main_frame.document.getElementById(popup_data_id).innerHTML     = '';
            main_frame.document.getElementById('popup_data').style.display  = '';
            main_frame.document.getElementById('frame_data').style.height   = '0%';
            main_frame.document.getElementById('popup_frame').style.display = 'none';
            main_frame.document.getElementById('popup_close').name          = popup_data_id;
        } else {
            main_frame.document.getElementById('popup_data').style.display  = 'none';
            main_frame.document.getElementById('popup_frame').style.display = '';
            main_frame.document.getElementById('frame_data').style.height   = '100%';
        }

        if (dimmer) {
            if (main_frame.document.documentElement) {                                   //IE in strict mode uses documentElement in place of body
                dimmer.style.height = main_frame.document.documentElement.scrollHeight+'px';
            } else {
                dimmer.style.height = main_frame.document.body.scrollHeight+'px';
            }
            
            dimmer.style.display = '';
        }
        if (dimmer_side) {
            dimmer_side.style.height  = side_frame.document.body.scrollHeight+'px';
            dimmer_side.style.display = '';
        }
        popup_table.style.display    = '';
        popup_table.style.width      = popup_dim[0];
        popup_table.style.height     = popup_dim[1];

        main_frame.innerHeight ? window_height = main_frame.innerHeight : window_height = main_frame.document.body.offsetHeight;     //Window height for mozilla/IE
        main_frame.pageYOffset ? scroll_offset = main_frame.pageYOffset : scroll_offset = main_frame.document.documentElement.scrollTop;        //scrolling offset for mozilla/IE

        popup_table.style.marginLeft = popup_table.offsetWidth  < popup_table.parentNode.offsetWidth  ? parseInt((popup_table.parentNode.offsetWidth  - popup_table.offsetWidth)  / 2) + "px" : "0";    //Bring it to the center of the screen
        popup_table.style.marginTop  = popup_table.offsetHeight < window_height ? parseInt(scroll_offset + ((window_height - popup_table.offsetHeight) / 2)) + "px" : scroll_offset + "px";    //Bring it to the middle of the screen        

    } else {
        if (popup_data_id) {
            main_frame.document.getElementById(popup_data_id).innerHTML = main_frame.document.getElementById('popup_data').innerHTML;
        }
        popup_table.style.display = 'none';
        if (dimmer)      dimmer.style.display     = 'none';
        if (dimmer_side) dimmer_side.style.display = 'none';

        popup_table.style.marginLeft = '0px';
        popup_table.style.marginTop  = '0px';

        main_frame.document.getElementById('popup_title').innerHTML = '';
        main_frame.document.getElementById('popup_data').innerHTML  = '';
        main_frame.document.getElementById('popup_frame').src       = '';
        main_frame.document.getElementById('popup_close').name      = '';
    }
}

function eF_js_keypress(e) {
    top.mainframe ? main_frame = top.mainframe : main_frame = window;

    var kC  = (window.event) ?    // MSIE or Firefox?
             event.keyCode : e.keyCode;
    var Esc = (window.event) ?
            27 : e.DOM_VK_ESCAPE // MSIE : Firefox
    if (kC == Esc) {
        if (main_frame.document.getElementById('popup_close') && main_frame.document.getElementById('dimmer').style.display != 'none')
            main_frame.document.getElementById('popup_close').onclick();
    }
}

/**
* This function is used to resize scorm iframe, so that it spans through the entire page
*/
function eF_js_setCorrectIframeSize(setHeight)
{
    if (frame = window.document.getElementById('scormFrameID')) {
        innerDoc    = (frame.contentDocument) ? frame.contentDocument : frame.contentWindow.document;

        objToResize = (frame.style) ? frame.style : frame;
        if (setHeight) {
        	objToResize.height = setHeight + 'px';
        } else {
	        if (frame.document) {
	            objToResize.height = Math.max(innerDoc.body.scrollHeight, frame.document.body.scrollHeight) + 500 + 'px';
	        } else {
	            objToResize.height = innerDoc.body.scrollHeight + 500 + 'px';
	        }
        }
        //alert(objToResize.height);
    }
}

//This function sets the main page to display 1 or 2 columns and to display the right side menu or not
function eFsetDisplay(single, side) {
    var singleColumn = document.getElementById('singleColumn');
    var leftColumn   = document.getElementById('leftColumn');
    var rightColumn  = document.getElementById('rightColumn');
    var sideMenu     = document.getElementById('sideMenu');

    if (single) {//alert(leftColumn);
        leftColumn.style.display   = "none";
        rightColumn.style.display  = "none";
        singleColumn.style.display = "";
    } else {
        leftColumn.style.display   = "";
        rightColumn.style.display  = "";
        singleColumn.style.display = "none";
    }

    if (side) {
        sideMenu.style.display = "";
    } else {
        sideMenu.style.display = "none";
    }
}


function show_hide(obj, name) {
    var el = document.getElementById(name);
    if (el.style.display== 'none') {
        el.style.display= '';
        obj.src = 'images/others/minus.png';
    } else {
        el.style.display = 'none';
        obj.src = 'images/others/plus.png';
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
* Set element visibility to '' or 'none'
*/
function eF_js_showHideVisible(el_id) {
    el = document.getElementById(el_id);
    if (el.style.visibility == 'hidden') {
        el.style.visibility = 'visible';
    } else {
        el.style.visibility = 'hidden';
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
		img ? img.className = 'plus' : null;
		return 'hidden';
	} else {
		obj.show();
		img ? img.className = 'minus' : null;
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

var currentShownPopup = null;
function togglePopup(obj)
{
        if(!obj)
                return;

      img_obj = obj.childNodes[1];
      span_obj = obj.childNodes[2];
      //img_inner_obj = span_obj.childNodes[0];

        if(img_obj.style.display!='block')
        {
          img_obj.style.display='block';
          span_obj.style.display='block';
          //img_inner_obj.style.display='block';
          if(currentShownPopup!=null)
            hidePopup(currentShownPopup);
          currentShownPopup = obj;
        }
        else
        {
          img_obj.style.display='none';
          span_obj.style.display='none';
          //img_inner_obj.style.display='none';
          currentShownPopup = null;
        }

      obj.blur();
      return;
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

var sidebar_width = 18;
function initSidebar(s_login)
{
    var is_ie;
        var detect = navigator.userAgent.toLowerCase();
        detect.indexOf("msie") > 0 ? is_ie = "true" : is_ie = "false";
	createCookie(s_login+'_sidebarMode','automatic',30);
    var value = readCookie(s_login+'_sidebar');
    if(value == 'hidden')
    {
        top.document.getElementById('framesetId').cols = ""+sidebar_width+", *";
        top.sideframe.document.body.style.paddingLeft = "20px";
        top.sideframe.document.getElementById('arrow_down').style.right = "300px";
        top.sideframe.document.getElementById('arrow_up').style.right = "300px";

        if(top.sideframe.document.getElementById('toggleSidebarImage').src)
            top.sideframe.document.getElementById('toggleSidebarImage').src = 'images/16x16/navigate_right.'+globalImageExtension;

        if(is_ie == "true")
        {
            top.sideframe.document.getElementById('toggleSidebarImage').style.position="absolute";
            top.sideframe.document.getElementById('toggleSidebarImage').style.left = "0px";
            top.sideframe.document.getElementById('toggleSidebarImage').style.top = "4px";          
        }
        top.sideframe.document.getElementById('logoutImage').style.position="absolute";
        top.sideframe.document.getElementById('logoutImage').style.left = "1px";
        top.sideframe.document.getElementById('logoutImage').style.top = "45px";
        top.sideframe.document.getElementById('mainPageImage').style.position="absolute";
        top.sideframe.document.getElementById('mainPageImage').style.left = "1px";
        top.sideframe.document.getElementById('mainPageImage').style.top = "25px";

        //changeImage(top.sideframe.document.getElementById('toggleSidebarImage'));
        //changeImage(top.sideframe.document.getElementById('logoutImage'));
        //changeImage(top.sideframe.document.getElementById('mainPageImage'));
    }
    else
    {
        top.document.getElementById('framesetId').cols = top.global_sideframe_width + ", *";
        top.sideframe.document.body.style.paddingLeft = "0px";
        top.sideframe.document.getElementById('arrow_down').style.right = "1px";
        top.sideframe.document.getElementById('arrow_up').style.right = "1px";

        if(top.sideframe.document.getElementById('toggleSidebarImage').src)
            top.sideframe.document.getElementById('toggleSidebarImage').src = 'images/16x16/navigate_left.'+globalImageExtension;


        if(is_ie == "true")
        {
            top.sideframe.document.getElementById('toggleSidebarImage').style.position="absolute";
            top.sideframe.document.getElementById('toggleSidebarImage').style.left = (top.global_sideframe_width - 16) + "px";
            top.sideframe.document.getElementById('toggleSidebarImage').style.top = "4px";            
        }
        top.sideframe.document.getElementById('logoutImage').style.position="absolute";
        top.sideframe.document.getElementById('logoutImage').style.left = "1000px";
        top.sideframe.document.getElementById('logoutImage').style.top = "45px";
        top.sideframe.document.getElementById('mainPageImage').style.position="absolute";
        top.sideframe.document.getElementById('mainPageImage').style.left = "1000px";
        top.sideframe.document.getElementById('mainPageImage').style.top = "25px";


    }

}
function checkToOpenSidebar(s_login)
{
	var value = readCookie(s_login+'_sidebarMode');
if(value == 'automatic'){
		toggleSidebar(s_login);
} 
}

function toggleSidebar(s_login)
{

    var is_ie;
        var detect = navigator.userAgent.toLowerCase();
        detect.indexOf("msie") > 0 ? is_ie = "true" : is_ie = "false";
    //var value = readCookie('sidebar');
    var value = readCookie(s_login+'_sidebar');

    if(value == 'hidden')
    {
        createCookie(s_login+'_sidebar','visible',30);
        top.document.getElementById('framesetId').cols = top.global_sideframe_width + ", *";
        top.sideframe.document.body.style.paddingLeft = "0px";
        top.sideframe.document.getElementById('arrow_down').style.right = "1px";
        top.sideframe.document.getElementById('arrow_up').style.right = "1px";
        setArrowStatus('down');
        initArrows();
        top.sideframe.document.getElementById('toggleSidebarImage').src = 'images/16x16/navigate_left.'+globalImageExtension;
        if(is_ie == "true")
        {
            top.sideframe.document.getElementById('toggleSidebarImage').style.position="absolute";
            top.sideframe.document.getElementById('toggleSidebarImage').style.left = (top.global_sideframe_width - 16) + "px";
            top.sideframe.document.getElementById('toggleSidebarImage').style.top = "4px";
                 
        }
        top.sideframe.document.getElementById('logoutImage').style.position="absolute";
        top.sideframe.document.getElementById('logoutImage').style.left = "1000px";
        top.sideframe.document.getElementById('logoutImage').style.top = "45px";
        top.sideframe.document.getElementById('mainPageImage').style.position="absolute";
        top.sideframe.document.getElementById('mainPageImage').style.left = "1000px";
        top.sideframe.document.getElementById('mainPageImage').style.top = "25px";

        //top.sideframe.document.getElementById('toggleSidebarImage').style= "position: absolute; left: 0px";
        //changeImage(top.sideframe.document.getElementById('toggleSidebarImage'));
        //changeImage(top.sideframe.document.getElementById('logoutImage'));
        //changeImage(top.sideframe.document.getElementById('mainPageImage'))
        
	var menus = top.sideframe.document.getElementById('menu').childElements().length - 1; 
	var i = 2;
        for (i = 2; i <= menus; i++) {
		if (top.sideframe.document.getElementById('menu'+i)) {
			top.sideframe.document.getElementById('menu'+i).style.visibility = "visible";
		}        
        }	

    }
    else
    {

        createCookie(s_login+'_sidebar','hidden',30);
        top.document.getElementById('framesetId').cols = ""+sidebar_width+", *";
        top.sideframe.document.body.style.paddingLeft = "130px";
        top.sideframe.document.getElementById('arrow_down').style.right = "300px";
        top.sideframe.document.getElementById('arrow_up').style.right = "300px";
        top.sideframe.document.getElementById('toggleSidebarImage').src = 'images/16x16/navigate_right.'+globalImageExtension;

        if(is_ie == "true")
        {
            top.sideframe.document.getElementById('toggleSidebarImage').style.position="absolute";
            top.sideframe.document.getElementById('toggleSidebarImage').style.left = "0px";
            top.sideframe.document.getElementById('toggleSidebarImage').style.top = "4px";
        }
        top.sideframe.document.getElementById('logoutImage').style.position="absolute";
        top.sideframe.document.getElementById('logoutImage').style.left = "1px";
        top.sideframe.document.getElementById('logoutImage').style.top = "45px";
        top.sideframe.document.getElementById('mainPageImage').style.position="absolute";
        top.sideframe.document.getElementById('mainPageImage').style.left = "1px";
        top.sideframe.document.getElementById('mainPageImage').style.top = "25px";

        //top.sideframe.document.getElementById('toggleSidebarImage').style.position = "absolute";position: absolute; left: 0px";
        //changeImage(top.sideframe.document.getElementById('toggleSidebarImage'));
        //changeImage(top.sideframe.document.getElementById('logoutImage'));
        //changeImage(top.sideframe.document.getElementById('mainPageImage'));
	var menus = top.sideframe.document.getElementById('menu').childElements().length - 1; 
	var i = 2;
        for (i = 2; i <= menus; i++) {
		if (top.sideframe.document.getElementById('menu'+i)) {
			top.sideframe.document.getElementById('menu'+i).style.visibility = "hidden";
		}        
        }	
		        
    }
}



/*
var sidebar_width = 18;
function initSidebar(s_login)
{alert('a');
    var is_ie;
        var detect = navigator.userAgent.toLowerCase();
        detect.indexOf("msie") > 0 ? is_ie = "true" : is_ie = "false";

    var value = readCookie(s_login+'_sidebar');
    if(value == 'hidden')
    {
        top.document.getElementById('framesetId').cols = ""+sidebar_width+", *";
        top.sideframe.document.body.style.paddingLeft = "20px";
        top.sideframe.document.getElementById('arrow_down').style.right = "300px";
        top.sideframe.document.getElementById('arrow_up').style.right = "300px";

        if(top.sideframe.document.getElementById('toggleSidebarImage').src)
            top.sideframe.document.getElementById('toggleSidebarImage').src = 'images/16x16/navigate_right.'+globalImageExtension;

        if(is_ie == "true")
        {
            top.sideframe.document.getElementById('toggleSidebarImage').style.position="absolute";
            top.sideframe.document.getElementById('toggleSidebarImage').style.left = "0px";
            top.sideframe.document.getElementById('toggleSidebarImage').style.top = "4px";          
        }
        top.sideframe.document.getElementById('logoutImage').style.position="absolute";
        top.sideframe.document.getElementById('logoutImage').style.left = "1px";
        top.sideframe.document.getElementById('logoutImage').style.top = "45px";
        top.sideframe.document.getElementById('mainPageImage').style.position="absolute";
        top.sideframe.document.getElementById('mainPageImage').style.left = "1px";
        top.sideframe.document.getElementById('mainPageImage').style.top = "25px";

        //changeImage(top.sideframe.document.getElementById('toggleSidebarImage'));
        //changeImage(top.sideframe.document.getElementById('logoutImage'));
        //changeImage(top.sideframe.document.getElementById('mainPageImage'));
    }
    else
    {
        top.document.getElementById('framesetId').cols = "175, *";
        top.sideframe.document.body.style.paddingLeft = "0px";
        top.sideframe.document.getElementById('arrow_down').style.right = "1px";
        top.sideframe.document.getElementById('arrow_up').style.right = "1px";

        if(top.sideframe.document.getElementById('toggleSidebarImage').src)
            top.sideframe.document.getElementById('toggleSidebarImage').src = 'images/16x16/navigate_left.'+globalImageExtension;


        if(is_ie == "true")
        {
            top.sideframe.document.getElementById('toggleSidebarImage').style.position="absolute";
            top.sideframe.document.getElementById('toggleSidebarImage').style.left = "159px";
            top.sideframe.document.getElementById('toggleSidebarImage').style.top = "4px";
        }
        top.sideframe.document.getElementById('logoutImage').style.position="absolute";
        top.sideframe.document.getElementById('logoutImage').style.left = "1000px";
        top.sideframe.document.getElementById('logoutImage').style.top = "45px";
        top.sideframe.document.getElementById('mainPageImage').style.position="absolute";
        top.sideframe.document.getElementById('mainPageImage').style.left = "1000px";
        top.sideframe.document.getElementById('mainPageImage').style.top = "25px";

        //changeImage(top.sideframe.document.getElementById('toggleSidebarImage'));
        //changeImage(top.sideframe.document.getElementById('logoutImage'));
        //changeImage(top.sideframe.document.getElementById('mainPageImage'));
    }

}

function toggleSidebar(s_login)
{
    var is_ie;
        var detect = navigator.userAgent.toLowerCase();
        detect.indexOf("msie") > 0 ? is_ie = "true" : is_ie = "false";
    //var value = readCookie('sidebar');
    var value = readCookie(s_login+'_sidebar');
    
    if(value == 'hidden')
    {
        createCookie(s_login+'_sidebar','visible',30);
        top.document.getElementById('framesetId').cols = "175, *";
        top.sideframe.document.body.style.paddingLeft = "0px";
        top.sideframe.document.getElementById('arrow_down').style.right = "1px";
        top.sideframe.document.getElementById('arrow_up').style.right = "1px";
        setArrowStatus('down');
        initArrows();
        top.sideframe.document.getElementById('toggleSidebarImage').src = 'images/16x16/navigate_left.'+globalImageExtension;
        if(is_ie == "true")
        {
            top.sideframe.document.getElementById('toggleSidebarImage').style.position="absolute";
            top.sideframe.document.getElementById('toggleSidebarImage').style.left = "159px";
            top.sideframe.document.getElementById('toggleSidebarImage').style.top = "4px";
        }
        top.sideframe.document.getElementById('logoutImage').style.position="absolute";
        top.sideframe.document.getElementById('logoutImage').style.left = "1000px";
        top.sideframe.document.getElementById('logoutImage').style.top = "45px";
        top.sideframe.document.getElementById('mainPageImage').style.position="absolute";
        top.sideframe.document.getElementById('mainPageImage').style.left = "1000px";
        top.sideframe.document.getElementById('mainPageImage').style.top = "25px";

        //top.sideframe.document.getElementById('toggleSidebarImage').style= "position: absolute; left: 0px";
        //changeImage(top.sideframe.document.getElementById('toggleSidebarImage'));
        //changeImage(top.sideframe.document.getElementById('logoutImage'));
        //changeImage(top.sideframe.document.getElementById('mainPageImage'))
        
	var menus = top.sideframe.document.getElementById('menu').childElements().length - 1; 
	var i = 2;
        for (i = 2; i <= menus; i++) {
		if (top.sideframe.document.getElementById('menu'+i)) {
			top.sideframe.document.getElementById('menu'+i).style.visibility = "visible";
		}        
        }	
        
			   
		
    }
    else
    {

        createCookie(s_login+'_sidebar','hidden',30);
        top.document.getElementById('framesetId').cols = ""+sidebar_width+", *";
        top.sideframe.document.body.style.paddingLeft = "130px";
        top.sideframe.document.getElementById('arrow_down').style.right = "300px";
        top.sideframe.document.getElementById('arrow_up').style.right = "300px";
        top.sideframe.document.getElementById('toggleSidebarImage').src = 'images/16x16/navigate_right.'+globalImageExtension;

        if(is_ie == "true")
        {
            top.sideframe.document.getElementById('toggleSidebarImage').style.position="absolute";
            top.sideframe.document.getElementById('toggleSidebarImage').style.left = "0px";
            top.sideframe.document.getElementById('toggleSidebarImage').style.top = "4px";
        }
        top.sideframe.document.getElementById('logoutImage').style.position="absolute";
        top.sideframe.document.getElementById('logoutImage').style.left = "1px";
        top.sideframe.document.getElementById('logoutImage').style.top = "45px";
        top.sideframe.document.getElementById('mainPageImage').style.position="absolute";
        top.sideframe.document.getElementById('mainPageImage').style.left = "1px";
        top.sideframe.document.getElementById('mainPageImage').style.top = "25px";

        //top.sideframe.document.getElementById('toggleSidebarImage').style.position = "absolute";position: absolute; left: 0px";
        //changeImage(top.sideframe.document.getElementById('toggleSidebarImage'));
        //changeImage(top.sideframe.document.getElementById('logoutImage'));
        //changeImage(top.sideframe.document.getElementById('mainPageImage'));
	var menus = top.sideframe.document.getElementById('menu').childElements().length - 1; 
	var i = 2;
        for (i = 2; i <= menus; i++) {
		if (top.sideframe.document.getElementById('menu'+i)) {
			top.sideframe.document.getElementById('menu'+i).style.visibility = "hidden";
		}        
        }	
	        
    }
}
*/

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

function getCheckedValue(radioObj) {
	if(!radioObj)
		return "";
	var radioLength = radioObj.length;
	if (radioLength == undefined)
		if(radioObj.checked)
			return radioObj.value;
		else
			return "";
	for(var i = 0; i < radioLength; i++) {
		if(radioObj[i].checked) {
			return radioObj[i].value;
		}
	}
	return "";
}

function showMessage(message, type) {
	if (type == 'failure') {
		message = '<div class = "failure" style = "font-size:16px"><img src = "images/32x32/warning.png" style = "float:left"><span style = "vertical-align:middle">'+message+'</span></div>'
	} else {
		message = '<div class = "success" style = "font-size:16px"><img src = "images/32x32/check2.png" style = "float:left"><span style = "vertical-align:middle">'+message+'</span></div>'
	}
	$('showMessageDiv').update(message);	
	eF_js_showDivPopup('', 0, 'showMessageDiv');
}
