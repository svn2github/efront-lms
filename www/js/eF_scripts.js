//This function is used to show / hide the div popup
function eF_js_showDivPopup(popup_title, popup_dim, popup_data_id) {

    parent.mainframe ? main_frame = parent.mainframe : main_frame = window;
    parent.sideframe ? side_frame = parent.sideframe : side_frame = window;

    var popup_table = main_frame.document.getElementById('popup_table');
    var dimmer      = main_frame.document.getElementById('dimmer');
    var dimmer_side = side_frame.document.getElementById('dimmer');

    if (popup_table.style.display == 'none') {
        main_frame.document.getElementById('popup_title').innerHTML = popup_title;
        if (popup_data_id) {
            main_frame.document.getElementById('popup_data').innerHTML      = main_frame.document.getElementById(popup_data_id).innerHTML;            
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
function eF_js_setCorrectIframeSize()
{
    if (frame = window.document.getElementById('scormFrameID')) {
        innerDoc    = (frame.contentDocument) ? frame.contentDocument : frame.contentWindow.document;
        objToResize = (frame.style) ? frame.style : frame;
        if (frame.document) {
            objToResize.height = Math.max(innerDoc.body.scrollHeight, frame.document.body.scrollHeight) + 500 + 'px';
        } else {
            objToResize.height = innerDoc.body.scrollHeight + 500 + 'px';
        }
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

    el = document.getElementById(el_id);

    if (el.style.display == 'none') {
        el.style.display  = '';
        el.style.position = "absolute";
        el.style.top  = (typeof(e.pageY) != 'undefined') ? e.pageY + 2 + 'px' : e.clientY + 2 + 'px';
        var click_point_X = (typeof(e.pageX) != 'undefined') ? e.pageX + 2 : e.clientX + 2;
		var window_width = (typeof(window.innerWidth) != 'undefined') ? window.innerWidth : document.body.offsetWidth
        if (el.clientWidth + parseInt(click_point_X) < document.body.offsetWidth) {
            el.style.left = click_point_X + 'px';
        } else {
            el.style.left = '0px';
        }

    } else {
        el.style.display = 'none';
    }
}



/**
* Select Children Tree Nodes.
*
* This javascript function is used to automatically select/deselect all subnodes of a tree node,
* when this node is checked
*
* @param obj The checkbox that was clicked
*/
function eF_js_selectAllChildren(obj) {
    if (obj.name == 'all') {                                            //If the check box name is 'all', then it is a checkbox that is used to check/uncheck all checkboxes
        var all_elements = document.getElementsByTagName('input');

        for (var i = 0; i < all_elements.length; i++) {
            if (all_elements[i].type == 'checkbox' && all_elements[i].name.substring(0, 8) == 'content[') {
                all_elements[i].checked = obj.checked;
            }
        }
    } else {
        var reg = /content\[(.*)\]/i.exec(obj.name);

        var parents        = new Array(reg[1]);
        var parent_objects = new Array(obj);

        var all_elements = document.getElementsByTagName('input');

        for (var i = 0; i < all_elements.length; i++) {
            if (all_elements[i].type == 'checkbox' && all_elements[i].name.substring(0, 8) == 'content[') {
                for (var j = 0; j < parents.length; j++) {
                    if (all_elements[i].getAttribute('parent') == parents[j]) {
                        all_elements[i].checked = parent_objects[j].checked;

                        reg = /content\[(.*)\]/i.exec(all_elements[i].name);
                        parents.push(reg[1]);
                        parent_objects.push(all_elements[i]);
                    }
                }
            }
        }
    }

}



function tryChangeTDcolor() {
    if(top.sideframe && typeof(top.sideframe.changeTDcolor)!='undefined')
    {
        top.sideframe.changeTDcolor(ctg);
        clearInterval(interval);
        return;
    }
    if(counter >= 5) {
        clearInterval(interval);
    } else {
        counter = counter + 1;
    }
}


function checkInput( form )
{
    if(form.user.value =="") {
        alert("{$smarty.const_USERNAMEERROR}");
        form.user.focus();
        return false;
    }
    if(form.personal.value =="") {
        alert("{$smarty.const_CVERROR}");
        form.personal.focus();
        return false;
    }
    if(form.update.checked==false) {
      alert("Please check the check box to update data!");
      form.update.focus();
      return false;
    }
    return true;
}

function show(a)
{
    i1=document.frm.input0
    i2=document.frm.input1
    i3=document.frm.input2
    i4=document.frm.input3

    if(a>1)
        i2.style.visibility="visible"
    else
        i2.style.visibility="hidden"
    if(a>2)
        i3.style.visibility="visible"
    else
        i3.style.visibility="hidden"
    if(a>3)
        i4.style.visibility="visible"
    else
        i4.style.visibility="hidden"
}

// Call this function if you want to save it by a form.
function saveMyTree_byForm()
{
    document.myForm.elements["saveString"].value = treeObj.getNodeOrders();
    document.myForm.submit();
}

/**
* This function is used to display the main data table and hide the "please wait" table
*/
function jeF_initialize()
{
    if (window._editor_url) initEditor();

    //eF_js_setCorrectIframeSize();
    focus();                                                            //This is needed here in order for the body to catch onkeypress event

//    if (changeImages)
//            changeImages();
}

/**
* This function is used to bring up a new browser window. It is a shortcut to window.open
*/
function popUp(URL, width, height, resize)
{
    var left = (screen.width - width) / 2
    var top  = (screen.height-height) / 2
    var resizeable = 0;
    if (resize == 1) {
        resizeable = 1;
    }
    popup = window.open(URL, '', 'toolbar = 0, scrollbars = 1, location = 0, statusbar = 1, menubar = 0, resizable = '+resizeable+', width = '+width+', height = '+height+', left = '+left+', top = '+top);
    return popup;
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