var flag=false;

function makeAjaxRequest(url,poststr,type) {
//alert(url);
        if(flag==true)
                return;
        flag=true;
        var http_request = false;

        if (window.XMLHttpRequest) { // Mozilla, Safari,...
                http_request = new XMLHttpRequest();
                if (http_request.overrideMimeType) {
                    http_request.overrideMimeType('text/xml');
                }
        } else if (window.ActiveXObject) { // IE
                try {
                    http_request = new ActiveXObject('Msxml2.XMLHTTP');
                } catch (e) {
                        try {
                          http_request = new ActiveXObject('Microsoft.XMLHTTP');
                        } catch (e) {}
                }
        }

        if (!http_request) {
        alert('Giving up :( Cannot create an XMLHTTP instance');
                return false;
        }


    if(type == 'general')
    {
            if(handleRequest != null)
                    http_request.onreadystatechange = function() { handleRequest(http_request); };
    }
    else if(type == 'sort')
    {
//        if(handleSortRequest != null)
//                    http_request.onreadystatechange = function() { handleSortRequest(http_request); };
    }
    else if(type == 'chat')
    {
        if(handleChatRequest != null)
                    http_request.onreadystatechange = function() { handleChatRequest(http_request); };
    }
    else if(type == 'login')
    {
        if(document.getElementById('message_div')) {
            document.getElementById('message_div').innerHTML='';
        }

        if(document.getElementById('messageP') && document.getElementById('waitMessage')) {
            document.getElementById('messageP').innerHTML="<b>"+document.getElementById('waitMessage').innerHTML+"</b>";
        }

        if(handleLoginRequest != null)
                http_request.onreadystatechange = function() { handleLoginRequest(http_request); };
    }
    else if(type == 'suggestions')
    {
        //alert('setting function');
        if(handleSuggestionsRequest != null)
                    http_request.onreadystatechange = function() { handleSuggestionsRequest(http_request); };
    }
    else if(type == 'set_positions')
    {
        if(handleSetPositions != null)
                    http_request.onreadystatechange = function() { handleSetPositions(http_request); };

    }
    else if(type == 'setSeenContent') {
        if(handleSetSeenContentRequest != null)
                    http_request.onreadystatechange = function() { handleSetSeenContentRequest(http_request); };
    }
    else if(type == 'setCurrentUnit') {
        if(handleSetSeenContentRequest != null)
                    http_request.onreadystatechange = function() { handleSetSeenContentRequest(http_request); };
    }

    if(poststr=='special_get_request')
    {
            http_request.open( 'POST', url, true);
            //http_request.setRequestHeader('Content-Type', 'charset=iso-8859-7');
            http_request.send(null);
    }
    else
    {
            http_request.open( 'POST', url, true);
            http_request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            http_request.setRequestHeader('Content-length', poststr.length);
            http_request.setRequestHeader('Connection', 'close');
            http_request.send(poststr);
    }
    flag=false;
}
//var IE = false;
//if (navigator.appName == "Microsoft Internet Explorer"){IE = true}
//if (!IE){document.captureEvents(Event.MOUSEMOVE)}

function mouseX(evt)
{
    if (evt.pageX)
        return evt.pageX;
    else if (evt.clientX)
        return evt.clientX +
        (document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft);
    else return null;
}

function mouseY(evt)
{
    if (evt.pageY)
        return evt.pageY;
    else if (evt.clientY)
        return evt.clientY +
        (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop);
    else return null;
}

var prev_response=0;
var prev_messages=-1;

function handleRequest(http_request) {

        if (http_request.readyState == 4) {
                if (http_request.status == 200) {
                        var xmldoc = http_request.responseXML;
                        var messages_num_node = xmldoc.getElementsByTagName('number').item(0);
//alert(messages_num_node);
      if (messages_num_node) {
                         var messages_num = messages_num_node.firstChild.data;
                        }
//alert(messages_num);
                        var message_node = xmldoc.getElementsByTagName('text').item(0);

                        if (message_node) {
                         var message = message_node.firstChild.data;
                        }
                        var users_online_node = xmldoc.getElementsByTagName('users').item(0);
                        var users_online = users_online_node.firstChild.data;
                        var users_num_node = xmldoc.getElementsByTagName('users_num').item(0);
                        var users_num = users_num_node.firstChild.data;
            //if(users_num == '0') {
            //    top.location = '/index.php?logout=true&reason=timeout';
            //    return;
            //}
                        //alert(users_online);
            //alert(http_request.responseText);
                        //http_request.responseText.split('-|*||||*|-');
                        //changePrivateMessagesText(response[0],response[1]);
                        //alert("num "+messages_num+"\ntext: "+message);
                        changeMessagesText(messages_num,message,users_online,users_num);

                        // Hack needed to correct sidebar appearance if we go from 0 -> messages or from messages->0
   if (prev_messages == -1) {
    prev_messages = messages_num;
   } else {
    if (prev_messages != messages_num) {
     if (prev_messages == 0 || messages_num == 0) {
      top.sideframe.resizeFunction();
     }
     prev_messages = messages_num;
    }
   }
                }
        }
}




function handleSetSeenContentRequest(http_request) {
        if (http_request.readyState == 4) {
                if (http_request.status == 200) {

                }
        }
}




function splitLargeWords(text, chars_per_word) {
 var newText = text;
 var words = newText.split(" ");

 var indifferent = 0;
 var result = "";
 for (var i = 0 ; i< words.length; i++) {

  if (indifferent) {
   if (words[i] == ">") {
    indifferent--;
   } else if (words[i] == "</a>") {
    indifferent--;
   }
  } else {
   if (words[i].match("<a")) {
    indifferent++; // do not split links
   } else if ( words[i].match("<img") || words[i].match("<image")) {
    indifferent++; // do not split links
    result += words[i] + " ";
    // add the code to move the sidebar on img load - not do it server side for performance reasons				
    words[i] = 'onLoad = "if (document.getElementById(\'chat_content\')) {document.getElementById(\'chat_content\').scrollTop= document.getElementById(\'chat_content\').scrollHeight + 100;}"';
   } else {
    while(words[i].length > chars_per_word) {
     result += words[i].substr(0,chars_per_word-1) + "<br>";
     words[i] = words[i].substr(chars_per_word);
    }
   }
  }

  result += words[i] + " ";

 }
 return result;
}

// Flag variable used to change colours
var colourchange = 0;
// Variable to create a unique id for each message table
var messages_count = 0;

// Function to move the chat scrollbar to the bottom
function fixChatFrameScrollbar() {
 if (test.document.getElementById('chat_content') && test.document.getElementById('message_table_' + messages_count)) {
  test.document.getElementById('chat_content').scrollTop=test.document.getElementById('chat_content').scrollHeight + 100;
  test.document.getElementById('message_table_' + messages_count).scrollIntoView();
 }
}

// Function handling the chat responses as set by the ask_chat.php script
function handleChatRequest(http_request) {
  var special_splitter = "||||";
        try {
        if (http_request.readyState == 4) {
                if (http_request.status == 200) {
                  // "ack" is returned if activity has been presented in the room during the last 5 minutes 
                  if (http_request.responseText == "ack") {

                   $('new_chat_messages').hide();
                   new Effect.Appear($('new_chat_messages'));

                   //alert("ack");
                   } else if (http_request.responseText == "noack") {
                    Effect.Fade($('new_chat_messages'));
                   } else if (http_request.responseText != "") {
                    //var table_style_size = "100%"; 
                    var current_font_size = $('current_font_size').value;
                         var response = http_request.responseText.split(special_splitter);
                         // Check for missing room - check only during new message post, not during during messages reading
       if (response[0] == chatRoomDoesNotExistError) {
        test.document.getElementById("chat_content").innerHTML = '<table class="chatbox" cellspacing="0" cellpadding="0"  style="width:'+table_style_size+'"><tr><td style="font-size:'+current_font_size+'px;color:red;" align="left">'+ translations['chatroomdeleted'] + '<BR>' + redicrectedToEfrontMain + '</td></tr></table>';

        $('current_chatroom_id').value = -1;
        $('last_spoken_login').value = "";
        $('first_time_messages').value = 1;
        $('chat_rooms').value = 0;
        $('delete_room').setStyle({display:'none'});
        $('current_chatroom_id').value = 0;


       } else if (response[0] == chatRoomIsNotEnabled) {
        test.document.getElementById("chat_content").innerHTML = '<table class="chatbox" cellspacing="0" cellpadding="0"  style="width:'+table_style_size+'"><tr><td style="font-size:'+current_font_size+'px;color:red;" align="left">' +chatRoomHasBeenDeactivated + '<BR>' + redicrectedToEfrontMain + '</td></tr></table>';

        // Remove the user from the deactivated room
        var url = "ask_chat.php?chatrooms_ID="+$('chat_rooms').value+"&delete_user=" + sessionLogin;
              new Ajax.Request(url, {method:'get',asynchronous:false});

        $('current_chatroom_id').value = -1;
        $('last_spoken_login').value = "";
        $('first_time_messages').value = 1;
        $('chat_rooms').value = 0;
        $('delete_room').setStyle({display:'none'});
        $('current_chatroom_id').value = 0;

       } else {
        // Create the fix for the IE display (1px left is added to the table)
        // This is used here, because of scrollIntoView, which without a pixel in the message table, moves the entire sideframe 
        var fixCell = '';
        if (navigator.appVersion.match("MSIE")) {
          fixCell = '<td width="1px">&nbsp;</td>';
        }

        if ($('current_chatroom_id').value == response[0]) {

         // The -5 goes because one more special splitter is added in the end
         for (i = response.length-5; i >= 1; i = i - 4) {

          if (response[i] != '') {
           if ($('last_spoken_login').value == response[i]) {
            test.document.getElementById("chat_content").innerHTML = test.document.getElementById("chat_content").innerHTML + '\n<table id = "message_table_'+messages_count+'" class="chatbox" cellspacing="0" cellpadding="0" bgcolor="'+ color+'" style="width:'+table_style_size+'"><tr>' + fixCell + '<td style="color:black;font-size:'+current_font_size+'px;">'+ splitLargeWords(response[i+3], 25) + '</td></tr></table>';
           } else {
            if (colourchange) {
             colourchange = 0;
             color = "#F9F9F9";
            } else {
             colourchange = 1;
             color = "#EAEAEA";
            }
            test.document.getElementById("chat_content").innerHTML = test.document.getElementById("chat_content").innerHTML + '\n<table id = "message_table_'+messages_count+'" class="chatbox" cellspacing="0" cellpadding="0"  bgcolor="'+ color+'" style="width:'+table_style_size+'"><tr>'+fixCell+'<td style="' + response[i+2] + 'font-size:'+current_font_size+'px;" align="left">' + response[i] + '</td><td style="'+ response[i+2] + ';font-size:'+current_font_size+'px;"" align="right">'+ response[i+1] + '</td></tr><tr>'+ fixCell + '<td colspan="2" style="color:black;font-size:'+current_font_size+'px;">'+ splitLargeWords(response[i+3], 25) + '</td></tr></table>';
            $('last_spoken_login').value = response[i];

           }

           fixChatFrameScrollbar();
                             messages_count++;
                            }
                           }
                       }
                      }
                  }
            } else {
                    //alert("There was a problem with the request.");
            }
        }
    }
    catch(e) {}
}

function handleLoginRequest(http_request) {

    try {
        if (http_request.readyState == 4) {
            if (http_request.status == 200) {
        //alert(http_request.responseText);
    //var is_ie;
        //var detect = navigator.userAgent.toLowerCase();
        //detect.indexOf("msie") > 0 ? is_ie = "true" : is_ie = "false";

    if(document.getElementById('messageP')) {
            document.getElementById('messageP').innerHTML="&nbsp;";
    }

            var xmldoc = http_request.responseXML;
            var type_node = xmldoc.getElementsByTagName('type').item(0);
            var type = type_node.firstChild.data;
            var success_node = xmldoc.getElementsByTagName('success').item(0);
            var success = success_node.firstChild.data;
            var message_node = xmldoc.getElementsByTagName('message').item(0);
            var message = message_node.firstChild.data;
            if(success == 'false')
            {
                var msgdiv = document.getElementById('message_div');
                if(msgdiv)
                    msgdiv.innerHTML = message;
            }
            else
            {
                //document.login_form.action = document.login_form.hidden_action.value;
                //alert(document.login_form.action);
                //document.login_form.onSubmit = function() {return true;};
                document.login_form.hidden_value.value = 'true';
                document.login_form.submit_login.click();
            }

                        //var response = http_request.responseText.split(special_splitter);
                        //test.document.getElementById("chat_content").innerHTML=response[0]+"\n"+test.document.getElementById("chat_content").innerHTML;
            //alert("resp = " + response[0] +"\nchat = "+test.document.getElementById("chat_content").innerHTML);
                        //document.getElementById("users_list").innerHTML=response[1];
                        //document.getElementById("rooms_list").innerHTML=response[2];//"sent="+sent_messages+"+"+response[3];
        } else {
                //alert("There was a problem with the request.");
            }
        }
    }
    catch(e) {}
}

function handleSuggestionsRequest(http_request) {
    try {
           if (http_request.readyState == 4) {
                if (http_request.status == 200) {
            //alert(http_request.responseText);
                  //var xmldoc = http_request.responseXML;

                //var ie_str;
                //var detect = navigator.userAgent.toLowerCase();
                //detect.indexOf("msie") > 0 ? ie_str = "&ie=1" :ie_str = "";

                }
           }
    }
    catch(e) {}
}

function handleSetPositions(http_request) {
    try {
           if (http_request.readyState == 4) {
                if (http_request.status == 200) {
                //alert("OK");
                //alert(http_request.responseText);
                  //var xmldoc = http_request.responseXML;

                //var ie_str;
                //var detect = navigator.userAgent.toLowerCase();
                //detect.indexOf("msie") > 0 ? ie_str = "&ie=1" :ie_str = "";

                }
           }
    }
    catch(e) {}
}

function sendMessage(chat_message,chatrooms_ID)
{
        //alert("sending message");
        chat_message.replace("&","&amp;");
        makeAjaxRequest("ask_chat.php?chatrooms_ID="+chatrooms_ID,"chat_message="+encodeURIComponent(chat_message), "chat" );
//makeAjaxRequest('ask_chat.php?chatrooms_ID='+$('current_chatroom_id').value,'special_get_request','chat');        
        document.chat_form.chat_message.value="";
}

function enableButton()
{
        if(document.chat_form.chat_message.value!="")
        {
            document.chat_form.submit.disabled=false;
        }
        else
        {
            document.chat_form.submit.disabled=true;
        }
}

function setFocus()
{
        try {
            document.chat_form.chat_message.focus();
        } catch(e) { }
}

function changeMessagesText(responseInt,responseText,users_online, users_num)
{
        if(responseInt!==prev_response)
        {
                if(document.getElementById('new_private_message'))
                {
                        document.getElementById('new_private_message').innerHTML=responseText;
                }
                if(document.getElementById('recent_unread'))
                {
                        document.getElementById('recent_unread').innerHTML=responseInt;
                }
                if(document.getElementById('recent_unread_left'))
                {

                        if (responseInt == 0)
                        {
                            document.getElementById('unread_img').innerHTML="";
                            document.getElementById('recent_unread_left').innerHTML="";
                        }
                        else
                        {
                            document.getElementById('unread_img').innerHTML="<img src = \"themes/default/images/16x16/mail2."+globalImageExtension+"\" />";
                            document.getElementById('recent_unread_left').innerHTML="(<a href = \"forum/messages_index.php\" target=\"mainframe\">"+responseInt+"</a>)";
                        }

                }
        }
        prev_response=responseInt;
        if(document.getElementById('users_online'))
        {
                document.getElementById('users_online').innerHTML=users_online;

                var tabmenu = document.getElementById('online_users_text').className;
                var text = document.getElementById('online_users_text').value;

                document.getElementById(tabmenu).innerHTML= text + '&nbsp;&nbsp;(' + users_num + ')';
        }
    //alert(users_online);
}

var cur_user="";
var glob_mousex;
var glob_mousey;



function setXY(e)
{
    var posx = 0;
    var posy = 0;
    if (!e) var e = window.event;

    //if(!e) alert('eee!');
    //var e = window.event;
    if( e.pageX || e.pageY ){
        posx = e.pageX;
        posy = e.pageY;
    }
    else if(e.clientX || e.clientY) {
        posx = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
        posy = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
    }
    // posx and posy contain the mouse position relative to the document
    // Do something with this information
    //document.getElementById('user_table').style.left = posX;
    //document.getElementById('user_table').style.top = posX - 10;
    //alert(posx+" "+posy);
    glob_mousex = posx;
    glob_mousey = posy;
}

function simple(e)
{
    if(!e)
        var e = window.event;

    if( e.pageX || e.pageY ){
        posx = e.pageX;
        posy = e.pageY;
    }
    else if(e.clientX || e.clientY) {
        posx = e.clientX + document.body.scrollLeft
            + document.documentElement.scrollLeft;
        posy = e.clientY + document.body.scrollTop
            + document.documentElement.scrollTop;
    }
    //alert(posx+","+posy);

}

function hideObj(obj)
{
        if(obj)
        {
                alert(obj.innerHTML);
                obj.style.display = 'none';
        }
        else
                alert('obj not found');
}

function showObj(obj)
{
        if(obj)
        {
                obj.style.display = '';
        }
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
var obj_toHide = null;

function delayedHidePopup(obj) {
    obj_toHide = obj;
    //setTimeout(hideObjectPopup,2000);
}

function hideObjectPopup() {
    if(obj_toHide) {
        hidePopup(obj_toHide)
    }
}
function hidePopup(obj)
{
        if(!obj)
                return;

      img_obj = obj.childNodes[1];
      span_obj = obj.childNodes[2];
      //img_inner_obj = span_obj.childNodes[1];

      img_obj.style.display='none';
      span_obj.style.display='none';
      //img_inner_obj.style.display='none';

      currentShownPopup = null;
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

//function gotoEditPage(content_ID) {
    //location = 'professor.php?ctg=lessons&content_ID='+content_ID+'&submit_update_content';
    //document.forms['insertUpdateForm'].action = 'professor.php?ctg=lessons&content_ID='+content_ID;
    //document.forms['insertUpdateForm'].submit_update_content.click();
//    location = 'professor.php?ctg=content&edit_unit='+content_ID+'';
//}

// http://www.dreamincode.net/code/snippet293.htm 20/6/2007
function getElementsByName_iefix(tag, name) {

     var elem = document.getElementsByTagName(tag);
     var arr = new Array();
     for(i = 0,iarr = 0; i < elem.length; i++) {
          att = elem[i].getAttribute("name");
          if(att == name) {
               arr[iarr] = elem[i];
               iarr++;
          }
     }
     return arr;
}



var directions_status = "expanded";
function toggleDirections(imageObj) {
   /* elements1 = getElementsByName_iefix('tr','default_visible');   //changed in 20/6/2007 by makriria because of http://www.dreamincode.net/code/snippet293.htm   getElementsByName has problems in IE

    elements2 = document.getElementsByName('default_hidden');

    elements3 = document.getElementsByName('default_visible_image');

    elements4 = document.getElementsByName('default_hidden_image');*/
    elements1 = getElementsByName_iefix('tr','default_visible');
    elements2 = getElementsByName_iefix('tr','default_hidden');
    elements3 = getElementsByName_iefix('img','default_visible_image');
    elements4 = getElementsByName_iefix('img','default_hidden_image');
    if(directions_status == "expanded") {
        for(i=0;i<elements1.length;i++) {
            elements1[i].style.display = 'none';
        }
        for(i=0;i<elements2.length;i++) {
            elements2[i].style.display = 'none';
        }
        for(i=0;i<elements3.length;i++) {
            elements3[i].src = '../themes/default/images/others/plus.png';
        }
        for(i=0;i<elements4.length;i++) {
            elements4[i].src = '../themes/default/images/others/plus.png';
        }
        imageObj.src = '../themes/default/images/others/plus.png';
        directions_status = "collapsed";
    } else {
        for(i=0;i<elements1.length;i++) {
            elements1[i].style.display = '';
        }
        for(i=0;i<elements2.length;i++) {
            elements2[i].style.display = '';
        }
        for(i=0;i<elements3.length;i++) {
            elements3[i].src = '../themes/default/images/others/minus.png';
        }
        for(i=0;i<elements4.length;i++) {
            elements4[i].src = '../themes/default/images/others/minus.png';
        }
        imageObj.src = '../themes/default/images/others/minus.png';
        directions_status = "expanded";
    }
}
function revertDirections(visible_name,hidden_name,visible_image_name,hidden_image_name) {
    var visibles = document.getElementsByName(visible_name);
    var hiddens = document.getElementsByName(hidden_name);
    var visibleImages = document.getElementsByName(visible_image_name);
    var hiddenImages = document.getElementsByName(hidden_image_name);
    for(i=0;i<visibles.length;i++) {
        visibles[i].style.display = '';
    }
    for(i=0;i<hiddens.length;i++) {
        hiddens[i].style.display = 'none';
    }
    for(i=0;i<visibleImages.length;i++) {
        visibleImages[i].src = '../themes/default/images/others/minus.png';
    }
    for(i=0;i<hiddenImages.length;i++) {
        hiddenImages[i].src = '../themes/default/images/others/plus.png';
    }
}
function getScrollXY() {
  var scrOfX = 0, scrOfY = 0;
  if( typeof( window.pageYOffset ) == 'number' ) {
    //Netscape compliant
    scrOfY = window.pageYOffset;
    scrOfX = window.pageXOffset;
  } else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {
    //DOM compliant
    scrOfY = document.body.scrollTop;
    scrOfX = document.body.scrollLeft;
  } else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) {
    //IE6 standards compliant mode
    scrOfY = document.documentElement.scrollTop;
    scrOfX = document.documentElement.scrollLeft;
  }
  return [ scrOfX, scrOfY ];
}

function getWindowSize() {
  var myWidth = 0, myHeight = 0;
  if( typeof( window.innerWidth ) == 'number' ) {
    //Non-IE
    myWidth = window.innerWidth;
    myHeight = window.innerHeight;
  } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
    //IE 6+ in 'standards compliant mode'
    myWidth = document.documentElement.clientWidth;
    myHeight = document.documentElement.clientHeight;
  } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
    //IE 4 compatible
    myWidth = document.body.clientWidth;
    myHeight = document.body.clientHeight;
  }

  return [myWidth, myHeight];
  //window.alert( 'Width = ' + myWidth );
  //window.alert( 'Height = ' + myHeight );
}

//document.onmousemove = setXY
