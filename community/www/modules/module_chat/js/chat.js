


var windowFocus = true;
var username;
var chatHeartbeatCount = 0;
var minChatHeartbeat = 2000;
var maxChatHeartbeat = 12000;
var chatHeartbeatTime = minChatHeartbeat; // How often will new messages be searched?
var originalTitle;
var blinkOrder = 0;
var user_list = "open";
var refresh_rate = 30000; // How fast will the user list be refreshed?
var scrollalert_timeout= 0;
var chatheartbeat_timeout = 0;
var scrollalertNotCaringCss_timeout = 0;

var resizeTimer = null;
var ul_sem = 0;

var soundEmbed = null;
var browserName;
var verOffset;
var nAgt = navigator.userAgent;

var lessons = new Array();
var chatboxFocus = new Array();
var newMessages = new Array();
var newMessagesWin = new Array();
var chatBoxes = new Array();
var blink_win = new Array();

var ls_available = 'localStorage' in window;

var objImage = new Image();


var $J = jQuery.noConflict();

if ((verOffset=nAgt.indexOf("MSIE"))!=-1) {
 browserName = "ie";
}
else if ((verOffset=nAgt.indexOf("Opera"))!=-1) {
 browserName = "opera";
}
else{
 browserName = "other";
}

$J(document).ready(function(){

 originalTitle = document.title;
 initChat();
 //startChatSession();
 chatHeartbeatTime = minChatHeartbeat;
if ($J.cookie("chat_on") == null){
  $J.cookie("chat_on", "on");
}

 var chat_status = $J.cookie('chat_on');
 //scrollalertNotCaringCss();

 if (chat_status == "off"){

  $J('#user_list').css("visibility","hidden");
  $J('#user_list').css("height","0px");
  $J('#chat_bar').css("height","25px");
  $J('#chat_bar').css("width","23px");
  $J('#chat_bar').css("text-align","center");
  $J('#chat_bar').css("border-width","1px");
  //$J('#chat_bar').css("border-top","none");
  //$J('#chat_bar').css("border-left","none");
  //$J('#chat_bar').css("margin-left","102.8em");
  //$J('#chat_bar').css("float","right");

  $J('#status').html(' ');
  $J('#status').hide();
  $J('#first').hide();
 }
 else{
  if(ls_available && localStorage.getItem('totalItems'))
   $J('#status').html('<img src="'+ modulechatbaselink +'img/chat16x16.png" />'+ ' Chat (' +localStorage.getItem('totalItems')+')');

  user_list = "closed";
  $J('#chat_bar').css("height","25px");
  $J('#user_list').css("visibility","hidden");
  $J('#user_list').css("height","0px");
  $J('#chat_bar').css("width","216px");
  $J('#chat_bar').css("border-width","1px");
  clearTimeout(scrollalert_timeout);

  scrollalertNotCaringCss();
  //scrollalertNotCaringCss_timeout = setTimeout('scrollalertNotCaringCss();', refresh_rate);
  startChatSession();
  //updatestatus();/* ADDED FOR THE USER LIST CONTENT BOX */
 }

 $J([window, document]).blur(function(){
  windowFocus = false;
 }).focus(function(){
  windowFocus = true;
  document.title = originalTitle;
 });



 objImage.src=modulechatbaselink+"img/loading.gif";

 setWindowsWidth();

 $J(window).resize(function() {
   if (resizeTimer)
    clearTimeout(resizeTimer);
   resizeTimer = setTimeout(setWindowsWidth, 100);
 });

 $J('body').click(function() {
          if (user_list == "open")
           toggle_users();
         });

 $J('#chat_bar').click(function(event){
     event.stopPropagation();
 });
});


function setWindowsWidth(){
 var spaceleft = $J(window).width()- 216; // chatbar size
 var remain = spaceleft % 225; //chatbox size
 var width = spaceleft - remain +10;
 $J("#windowspace").css("max-width",width+"px");
 //alert(width);
}

function getChatheartbeat(){

 $J.get(modulechatbaselink+"admin.php?force=getChatHeartbeat", function(data){
      minChatHeartbeat = data;
   $J.cookie("Chatheartbeat", data);
   });


}
function getRefresh_rate(){

 $J.get(modulechatbaselink+"admin.php?force=getRefresh_rate", function(data){
      refresh_rate = data;
   $J.cookie("Refresh_rate", data);
   });

}
function initChat(){

 if ($J.cookie("Chatheartbeat") == null){
  getChatheartbeat();
 }
 if ($J.cookie("Refresh_rate") == null){
  getRefresh_rate();
 }
}


/* Update status - Fix user list content box height*/
function updatestatus(){
 ul_sem = 1;
 var chat_status = $J.cookie("chat_on");

 if (chat_status == 'on'){

  //Show number of loaded items
  var totalItems=$J('#content p').length;
  var height = totalItems*20;
  if ( height > 600)
   height = 600;
  $J('#user_list').height(height);
  height = height+25;
  $J('#chat_bar').height(height);
  $J('#status').html('<img src="'+ modulechatbaselink +'img/chat16x16.png" />'+ ' Chat (' +totalItems +')');
  if(ls_available)
   localStorage.setItem('totalItems', totalItems);
  ul_sem = 0;

 }
}

function updatestatusNotCaringCss(){

 var chat_status = $J.cookie("chat_on");

 if (chat_status == 'on'){
  //Show number of loaded items
  var totalItems=$J('#content p').length;
  $J('#status').html('<img src="'+ modulechatbaselink +'img/chat16x16.png" />'+ ' Chat (' +totalItems +')');
  if(ls_available) localStorage.setItem('totalItems', totalItems);

 }
}

function scrollalert(){

 var chat_status = $J.cookie("chat_on");
 ul_sem = 1;
 if (chat_status == 'on'){

  $J('#status').html('<img src="'+ modulechatbaselink +'img/chat16x16.png" />'+' Chat'+'   <img src="'+ modulechatbaselink +'img/loading.gif" width="15" height="15"/>');
  //fetch new users
  //$J('#status').text('Loading Users...');
  $J.get(modulechatbaselink+'new-items.php', '', function(newitems){
   $J('#content').html(newitems);
   updatestatus();
  });
 }
 scrollalert_timeout = setTimeout('scrollalert();', refresh_rate);
}


function scrollalertNotCaringCss(){

 var chat_status = $J.cookie("chat_on");

 if (chat_status == 'on'){
  //$J('#status').html('<img src="'+ modulechatbaselink +'img/chat16x16.png" />'+' Chat'+'<img src="'+ modulechatbaselink +'img/loading.gif" width="20" height="20"/>');
  //fetch new users
  //$J('#status').text('Loading Users...');
  //$J('#status').html('<img src="'+ modulechatbaselink +'img/chat16x16.png" />'+ ' Chat----');
  $J.get(modulechatbaselink+'new-items.php', '', function(newitems){
   $J('#content').html(newitems);
   updatestatusNotCaringCss();
  });
 }
 scrollalertNotCaringCss_timeout = setTimeout('scrollalertNotCaringCss();', refresh_rate);
}

/* Turn the Chat System ON or OFF*/
function on_off() {
 var chat_status = $J.cookie('chat_on');

 //Closing chat module
 if (chat_status == "on"){
  $J('#user_list').css("visibility","hidden");
  $J('#user_list').css("height","0px");
  $J('#chat_bar').css("height","25px");
  $J('#chat_bar').css("width","23px");
  $J('#chat_bar').css("text-align","center");
  //$J('#chat_bar').css("border-top","none");
  //$J('#chat_bar').css("border-left","none");
  //$J('#chat_bar').css("float","right");

  $J('#status').html(' ');
  $J('#status').hide();
  $J('#first').hide();

  user_list = "closed";
  clearTimeout(scrollalert_timeout);
  clearTimeout(chatheartbeat_timeout);
  $J.get(modulechatbaselink+'chat.php?action=logoutfromchat');


  for (x in chatBoxes) { // close all open chatboxes
   if (chatBoxes.hasOwnProperty(x)){
    //if ($J("#chatbox_"+chatBoxes[x]).css('display') != 'none') {
    if ($J("#chatbox_"+chatBoxes[x]).is(":visible")){
     closeChatBox(chatBoxes[x]);
    }
   }
  }

  $J.cookie("chat_on", "off");
 }
 //Opening Chat module
 else{
  $J('#status').text('Connecting.......  ');
  $J('#user_list').css("visibility","visible");
  $J('#chat_bar').css("width","216px");
  $J('#chat_bar').css("text-align","left");
  //$J('#chat_bar').css("float","right");
  //$J('#chat_bar').css("border","1px solid #999999");
  $J('#status').show();
  $J('#first').show();


  $J.cookie("chat_on", "on");
  toggle_users(true);


  $J.get(modulechatbaselink+'chat.php?action=logintochat');

  chatHeartbeat();
 }
}


function toggle_users(forced) {

 if (ul_sem == 1 && !forced)
  return;

 ul_sem = 1;

 var chat_status = $J.cookie('chat_on');

 if (user_list == "open"){

  $J('#user_list').css("visibility","hidden");
  $J('#user_list').css("height","0px");
  $J('#chat_bar').css("height","25px");
  user_list = "closed";

  clearTimeout(scrollalert_timeout);
  scrollalertNotCaringCss_timeout = setTimeout('scrollalertNotCaringCss();', refresh_rate);
  ul_sem = 0;

 }
 else{
  $J('#user_list').css("visibility","visible");
  user_list = "open";
  clearTimeout(scrollalertNotCaringCss_timeout);
  scrollalert();
 }
}


function restructureChatBoxes() {

 align = 0;
 for (x in chatBoxes) {
  if (chatBoxes.hasOwnProperty(x)){
   chatboxtitle = chatBoxes[x];

   if ($J("#chatbox_"+chatboxtitle).css('display') != 'none') {
    /*if (align == 0) {

					$J("#chatbox_"+chatboxtitle).css('right', '220px');

				} else {

					width = (align)*(225+1)+220;

					$J("#chatbox_"+chatboxtitle).css('right', width+'px');

				}*/
    align++;
   }
  }
 }
}
function chatWith(chatuser) {
 createChatBox(chatuser);
 $J("#chatbox_"+chatuser+" .chatboxtextarea").focus();
}
function chatWithLesson(chatuser) {
 createChatBox(chatuser);
 $J("#chatbox_"+chatuser+" .chatboxtextarea").focus();
}
function createChatBox(chatboxtitle,minimizeChatBox) {
 var chatBoxeslength = 0;
 if ($J("#chatbox_"+chatboxtitle).length > 0) { //if chatbox was already opened before

  for (x in chatBoxes) { // minimize all other open chatboxes
   if (chatBoxes.hasOwnProperty(x) && chatboxtitle != chatBoxes[x]){
    //if ($J("#chatbox_"+chatBoxes[x]).css('display') != 'none') {
    if ( $J("#chatbox_"+chatBoxes[x]).is(":visible") ){
     chatBoxeslength++;
     //$J("#chatbox_"+chatBoxes[x]+" .chatboxinput").css('display','none');
     //$J("#chatbox_"+chatBoxes[x]+" .chatboxcontent").css('display','none');
     $J("#chatbox_"+chatBoxes[x]+" .chatboxinput").hide();
     $J("#chatbox_"+chatBoxes[x]+" .chatboxcontent").hide();
    }
    //$J("#chatbox_"+chatBoxes[x]).css('margin-top','275px');
   }
  }

  //var width = (chatBoxeslength+1)*227;
  //$J('#windows').css('width',width);

  //if ($J("#chatbox_"+chatboxtitle).css('display') == 'none') {
  if ($J("#chatbox_"+chatboxtitle).is(":hidden")){

   //$J("#chatbox_"+chatboxtitle).css('display','block');
   $J("#chatbox_"+chatboxtitle).show();
   //$J("#chatbox_"+chatboxtitle).css('margin-top','7px');
   //$J("#chatbox_"+chatboxtitle+" .chatboxcontent").css('display', 'block');
   //$J('#chatbox_'+chatboxtitle+' .chatboxinput').css('display','block');
   $J("#chatbox_"+chatboxtitle+" .chatboxcontent").show();
   $J('#chatbox_'+chatboxtitle+' .chatboxinput').show();
   restructureChatBoxes();

  }
  else{
   //$J("#chatbox_"+chatboxtitle+" .chatboxcontent").css('display', 'block');
   //$J('#chatbox_'+chatboxtitle+' .chatboxinput').css('display','block');
   $J("#chatbox_"+chatboxtitle+" .chatboxcontent").show();
   $J('#chatbox_'+chatboxtitle+' .chatboxinput').show();
  }
  //$J("#chatbox_"+chatboxtitle).css('margin-top','7px');
  return;
 }// END if chatbox was already open before

 $J(" <div />" ).attr("id","chatbox_"+chatboxtitle)
 .addClass("chatbox")
 .html('<div class="chatboxhead" onclick="javascript:toggleChatBoxGrowth(\''+chatboxtitle+'\')"><div class="chatboxtitle">'+chatboxtitle.substring(0,30)+'</div><div class="chatboxoptions"><a href="javascript:void(0)" onclick="javascript:closeChatBox(\''+chatboxtitle+'\')"><img src="'+ modulechatbaselink +'img/x.png" /></a></div><br clear="all"/></div><div class="chatboxcontent"></div><div class="chatboxinput"><textarea class="chatboxtextarea" onKeyUp="javascript: return checkChatBoxInputKey(event,this,\''+chatboxtitle+'\');"></textarea></div>')
 .prependTo($J( "#windows" ));

 $J("#chatbox_"+chatboxtitle).css('bottom', '0px');

 chatBoxeslength = 0;

 for (x in chatBoxes) { // minimize all other open chatboxes
  if (chatBoxes.hasOwnProperty(x)){
   //if ($J("#chatbox_"+chatBoxes[x]).css('display') != 'none') {
   if ($J("#chatbox_"+chatBoxes[x]).is(":visible")){
    chatBoxeslength++;
    //$J("#chatbox_"+chatBoxes[x]+" .chatboxinput").css('display','none');
    //$J("#chatbox_"+chatBoxes[x]+" .chatboxcontent").css('display','none');
    $J("#chatbox_"+chatBoxes[x]+" .chatboxinput").hide();
    $J("#chatbox_"+chatBoxes[x]+" .chatboxcontent").hide();
    //$J("#chatbox_"+chatBoxes[x]).css('margin-top', '275px');

   }
  }
 }

 //var width = (chatBoxeslength+1)*227;
 //$J('#windows').css('width',width);
 /*if (chatBoxeslength == 0) {

		$J("#chatbox_"+chatboxtitle).css('right', '220px');

	} else {

		width = (chatBoxeslength)*(225+1)+220;

		$J("#chatbox_"+chatboxtitle).css('right', width+'px');

	}*/
 chatBoxes.push(chatboxtitle);
 if (minimizeChatBox == 1) {
  /*minimizedChatBoxes = new Array();



		if ($J.cookie('chatbox_minimized')) {

			minimizedChatBoxes = $J.cookie('chatbox_minimized').split(/\|/);

		}

		minimize = 0;

		for (j=0;j<minimizedChatBoxes.length;j++) {

			if (minimizedChatBoxes[j] == chatboxtitle) {

				minimize = 1;

			}

		}



		if (minimize == 1) {*/
   //$J('#chatbox_'+chatboxtitle+' .chatboxcontent').css('display','none');
   //$J('#chatbox_'+chatboxtitle+' .chatboxinput').css('display','none');
   $J('#chatbox_'+chatboxtitle+' .chatboxcontent').hide();
   $J('#chatbox_'+chatboxtitle+' .chatboxinput').hide();
   //$J('#chatbox_'+chatboxtitle).css('margin-top','275px');
  //}
 }
 else{
  $J.cookie("openchatbox", chatboxtitle);
 }
 chatboxFocus[chatboxtitle] = false;
 $J("#chatbox_"+chatboxtitle+" .chatboxtextarea").blur(function(){
     chatboxFocus[chatboxtitle] = false;
     $J("#chatbox_"+chatboxtitle+" .chatboxtextarea").removeClass('chatboxtextareaselected');
 }).focus(function(){
     chatboxFocus[chatboxtitle] = true;
     newMessages[chatboxtitle] = false;
     $J('#chatbox_'+chatboxtitle+' .chatboxhead').removeClass('chatboxblink');
     $J("#chatbox_"+chatboxtitle+" .chatboxtextarea").addClass('chatboxtextareaselected');
 });
 $J("#chatbox_"+chatboxtitle+ " .chatboxcontent").click(function(){
            if (!($J("#chatbox_"+chatboxtitle+" .chatboxtextarea").is(":focus")))
             $J("#chatbox_"+chatboxtitle+" .chatboxtextarea").focus();
            blink_win[chatboxtitle] = false;
            $J('#chatbox_'+chatboxtitle+' .chatboxhead').removeClass('chatboxblink');
            });
 $J("#chatbox_"+chatboxtitle).show();
}
function chatHeartbeat(){
  var itemsfound = 0;
  if (windowFocus == false) {
   var blinkNumber = 0;
   var titleChanged = 0;
   for (x in newMessagesWin) {
    if (newMessagesWin[x] == true) {
     ++blinkNumber;
     if (blinkNumber >= blinkOrder) {
      document.title = x+' says...';
      titleChanged = 1;
      break;
     }
    }
   }
   if (titleChanged == 0) {
    document.title = originalTitle;
    blinkOrder = 0;
   } else {
    ++blinkOrder;
   }
  }
  else {
   for (x in newMessagesWin) {
    newMessagesWin[x] = false;
   }
  }
  for (x in newMessages) {
   if (newMessages[x] == true) {
    if (blink_win[x] == true) {
     $J('#chatbox_'+x+' .chatboxhead').toggleClass('chatboxblink');
    }
   }
  }
  var scrolldown = false;
  $J.ajax({
    url: modulechatbaselink+"chat.php?action=chatheartbeat",
    cache: false,
    dataType: "json",
    error:function (xhr, ajaxOptions, thrownError){
     clearTimeout(chatheartbeat_timeout);
     chatHeartbeat();

                },
    success: function(data) {

   if (data==null)
    return;
   $J.each(data.items, function(i,item){
    if (item) { // fix strange ie bug


     chatboxtitle = item.t;
     //alert(chatboxtitle);

     if ($J("#chatbox_"+chatboxtitle).length <= 0) {
      createChatBox(chatboxtitle);
     }
     else if ($J("#chatbox_"+chatboxtitle).is(":hidden")){
      //var width = ($J('#windows').width())+227;
      //$J('#windows').css('width',width);
      //$J("#chatbox_"+chatboxtitle).css('display','block');
      //$J('#chatbox_'+chatboxtitle+' .chatboxcontent').css('display','block');
      //$J('#chatbox_'+chatboxtitle+' .chatboxinput').css('display','block');
      $J("#chatbox_"+chatboxtitle).show();
      $J('#chatbox_'+chatboxtitle+' .chatboxcontent').show();
      $J('#chatbox_'+chatboxtitle+' .chatboxinput').show();
      $J("#chatbox_"+chatboxtitle+" .chatboxcontent").empty();
      //$J('#chatbox_'+chatboxtitle).css('margin-top', '7px');
      //restructureChatBoxes();
     }

     var from;
     if (item.s == 1) {
      from = username;
     }
     else
      from = item.f

     //if (item.s == 2) {
     //	$J("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatboxmessage"><span class="chatboxinfo">'+careLinksEmoticons(item.m)+'</span></div>');
     //} else {
     var elem = $J("#chatbox_"+chatboxtitle+" .chatboxcontent");
     if (elem[0].scrollHeight - elem.scrollTop() == elem.outerHeight()) {
       scrolldown = true;
     }

      newMessages[chatboxtitle] = true;
      newMessagesWin[chatboxtitle] = true;
      blink_win[chatboxtitle] = true;
      $J("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatboxmessage"><span class="chatboxmessagefrom">'+from+':&nbsp;&nbsp;</span><span class="chatboxmessagecontent">'+careLinksEmoticons(item.m)+'</span></div>');
     //}

     if (scrolldown == true)
      $J("#chatbox_"+chatboxtitle+" .chatboxcontent").scrollTop($J("#chatbox_"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);
     if (itemsfound == 0){
      /*if (ie!=1)

							msg_alert("sound1");

						else

							msg_alert_ie(modulechatbaselink+"sound/msg.wav");*/
      if (browserName == "other") //all except ie and opera
       soundPlay();
      else if (browserName == "ie")
       msg_alert_ie(modulechatbaselink+"sound/msg.wav");
     }
     itemsfound += 1;
    }
    if (!($J('#chatbox_'+chatboxtitle+' .chatboxcontent').is(":hidden"))){
     $J("#chatbox_"+chatboxtitle+" .chatboxtextarea").blur();
     $J("#chatbox_"+chatboxtitle+" .chatboxtextarea").focus();
    }
   });
   chatHeartbeatCount++;
   if (itemsfound > 0) {
    chatHeartbeatTime = minChatHeartbeat;
    chatHeartbeatCount = 1;
   } else if (chatHeartbeatCount >= 10) {
    chatHeartbeatTime *= 2;
    chatHeartbeatCount = 1;
    if (chatHeartbeatTime > maxChatHeartbeat) {
     chatHeartbeatTime = maxChatHeartbeat;
    }
   }

   chatheartbeat_timeout = setTimeout('chatHeartbeat();',chatHeartbeatTime);
  }});

}

function closeChatBox(chatboxtitle) {



 //$J('#chatbox_'+chatboxtitle).css('display','none');
 $J('#chatbox_'+chatboxtitle).remove();
 //var width = $J('#windows').width() - 227;
 //$J('#windows').css('width',width);
 //$J("#chatbox_"+chatboxtitle+" .chatboxcontent").html(' ');
 //restructureChatBoxes();

 $J.post(modulechatbaselink+"chat.php?action=closechat", { chatbox: chatboxtitle} , function(data){
 });

 //$J('.chatboxmessage').html(''); //added to prevent duplicate of chat history after chat window has closed

}

function toggleChatBoxGrowth(chatboxtitle) {
 if ($J('#chatbox_'+chatboxtitle+' .chatboxcontent').is(":hidden")){

  var minimizedChatBoxes = new Array();

  if ($J.cookie('chatbox_minimized')) {
   minimizedChatBoxes = $J.cookie('chatbox_minimized').split(/\|/);
  }

  var newCookie = '';

  for (i=0;i<minimizedChatBoxes.length;i++) {
   if (minimizedChatBoxes[i] != chatboxtitle) {
    //newCookie += chatboxtitle+'|'; //DEBUG changed for not toggling minimized chat windows
    newCookie += minimizedChatBoxes[i]+'|';
   }
  }

  newCookie = newCookie.slice(0, -1);

  //$J('#chatbox_'+chatboxtitle).css('margin-top', '7px');

  $J.cookie('chatbox_minimized', newCookie);
  //$J('#chatbox_'+chatboxtitle+' .chatboxcontent').css('display','block');
  //$J('#chatbox_'+chatboxtitle+' .chatboxinput').css('display','block');
  $J('#chatbox_'+chatboxtitle+' .chatboxcontent').show();
  $J('#chatbox_'+chatboxtitle+' .chatboxinput').show();
  $J("#chatbox_"+chatboxtitle+" .chatboxtextarea").focus();
  $J("#chatbox_"+chatboxtitle+" .chatboxcontent").scrollTop($J("#chatbox_"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);

  for (x in chatBoxes) { // minimize all other open chatboxes
   if (chatBoxes.hasOwnProperty(x) && chatboxtitle != chatBoxes[x]){

    if ($J("#chatbox_"+chatBoxes[x]).is(":visible")){
     //$J("#chatbox_"+chatBoxes[x]+" .chatboxinput").css('display','none');
     //$J("#chatbox_"+chatBoxes[x]+" .chatboxcontent").css('display','none');
     $J("#chatbox_"+chatBoxes[x]+" .chatboxinput").hide();
     $J("#chatbox_"+chatBoxes[x]+" .chatboxcontent").hide();
     //$J("#chatbox_"+chatBoxes[x]).css('margin-top','275px');
    }
   }
  }
  $J.cookie("openchatbox", chatboxtitle);
 }
 else {
  $J("#chatbox_"+chatboxtitle+" .chatboxtextarea").focus();
  $J("#chatbox_"+chatboxtitle+" .chatboxtextarea").blur();
  var newCookie = chatboxtitle;

  if ($J.cookie('chatbox_minimized')) {
   newCookie += '|'+$J.cookie('chatbox_minimized');
  }


  $J.cookie('chatbox_minimized',newCookie);
  //$J('#chatbox_'+chatboxtitle+' .chatboxcontent').css('display','none');
  //$J('#chatbox_'+chatboxtitle+' .chatboxinput').css('display','none');
  $J('#chatbox_'+chatboxtitle+' .chatboxcontent').hide();
  $J('#chatbox_'+chatboxtitle+' .chatboxinput').hide();
  //$J('#chatbox_'+chatboxtitle).css('margin-top','275px');
  $J.cookie("openchatbox", null);
 }

}

function checkChatBoxInputKey(event,chatboxtextarea,chatboxtitle) {
 //alert("me");
 $J(chatboxtextarea).focus();

 if(event.keyCode == 13 && event.shiftKey == 0) {
  message = $J(chatboxtextarea).val();
  message = message.replace(/^\s+|\s+$/g,"");
  $J(chatboxtextarea).val('');
  $J(chatboxtextarea).focus();
  $J(chatboxtextarea).css('height','30px');
  if (message != '') {
   /*$J.post(modulechatbaselink+"chat.php?action=sendchat", {to: chatboxtitle, message: message} , function(data){



				message = message.replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/\"/g,"&quot;");

				$J("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatboxmessage"><span class="chatboxmessagefrom">'+username+':&nbsp;&nbsp;</span><span class="chatboxmessagecontent">'+message+'</span></div>');

				$J("#chatbox_"+chatboxtitle+" .chatboxcontent").scrollTop($J("#chatbox_"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);

				

				

			});*/
   msg = message.replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/\"/g,"&quot;");
   msg = careLinksEmoticons(msg);
    $J("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatboxmessage"><span class="chatboxmessagefrom">'+username+':&nbsp;</span><span class="chatboxmessagecontent">'+msg+'</span></div>');
    $J("#chatbox_"+chatboxtitle+" .chatboxcontent").scrollTop($J("#chatbox_"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);
   $J.post(modulechatbaselink+"chat.php?action=sendchat", {to: chatboxtitle, message: message}, function (data){});
  }
  chatHeartbeatTime = minChatHeartbeat;
  chatHeartbeatCount = 1;
  return false;
 }
 var adjustedHeight = chatboxtextarea.clientHeight;
 var maxHeight = 30;
 if (maxHeight > adjustedHeight) {
  adjustedHeight = Math.max(chatboxtextarea.scrollHeight, adjustedHeight);
  if (maxHeight)
   adjustedHeight = Math.min(maxHeight, adjustedHeight);
  if (adjustedHeight > chatboxtextarea.clientHeight)
   $J(chatboxtextarea).css('height',adjustedHeight +'px');
 } else {
  $J(chatboxtextarea).css('overflow','auto');
 }
}

function startChatSession(){
  $J.ajax({
  url: modulechatbaselink+"chat.php?action=startchatsession",
  cache: false,
  dataType: "json",
  success: function(data) {

   username = data.username;
   $J.each(data.items, function(i,item){

    if (item) { // fix strange ie bug

     chatboxtitle = item.t;

     if ($J("#chatbox_"+chatboxtitle).length <= 0) {
      createChatBox(chatboxtitle,1);
     }

     if (item.s == 1) {
      item.f = username;
     }

     if (item.s == 2) {
      $J("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatboxmessage"><span class="chatboxinfo">'+careLinksEmoticons(item.m)+'</span></div>');
     }
     else {
      $J("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatboxmessage"><span class="chatboxmessagefrom">'+item.f+':&nbsp;&nbsp;</span><span class="chatboxmessagecontent">'+careLinksEmoticons(item.m)+'</span></div>');
     }


    }
   });

   if ($J.cookie("openchatbox")){
    $J("#chatbox_"+$J.cookie("openchatbox")+" .chatboxcontent").show();
    $J("#chatbox_"+$J.cookie("openchatbox")+" .chatboxinput").show();
   }
   for (i=0;i<chatBoxes.length;i++) {
     chatboxtitle = chatBoxes[i];
     $J("#chatbox_"+chatboxtitle+" .chatboxcontent").scrollTop($J("#chatbox_"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);
     setTimeout('$J("#chatbox_"+chatboxtitle+" .chatboxcontent").scrollTop($J("#chatbox_"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);', 100); // yet another strange ie bug
   }

   chatHeartbeat();

  }
 });

}


function disableSelection(target){

    if (typeof target.onselectstart!="undefined") //IE route
        target.onselectstart=function(){return false}

    else if (typeof target.style.MozUserSelect!="undefined") //Firefox route
        target.style.MozUserSelect="none"

    else //All other route (ie: Opera)
        target.onmousedown=function(){return false}

    target.style.cursor = "default"
}


function careLinksEmoticons(input){

    return input
    .replace(/(ftp|http|https|file):\/\/[\S]+(\b|$)/gim,
'<a href="$&" class="my_link" target="_blank">$&</a>')
    .replace(/(^|[^\/])(www\.[\S]+(\b|$))/gim,
'$1<a href="http://$2" class="my_link" target="_blank">$2</a>')
 .replace(/:\)/g, "<img src=\""+ modulechatbaselink +"img/emoticons/smile.gif\" />")
 .replace(/:\(/g, "<img src=\""+ modulechatbaselink +"img/emoticons/sad.gif\" />")
 .replace(/:D|:d/g, "<img src=\""+ modulechatbaselink +"img/emoticons/bigsmile.gif\" />")
 .replace(/:'\(/g, "<img src=\""+ modulechatbaselink +"img/emoticons/cry.gif\" />")
 .replace(/:P|:p/g, "<img src=\""+ modulechatbaselink +"img/emoticons/glwssa.gif\" />")
 .replace(/;\)/g, "<img src=\""+ modulechatbaselink +"img/emoticons/wink.gif\" />");;
}



/*function msg_alert(sid) {

  var thissound=document.getElementById(sid);

  thissound.Play();

}*/
function msg_alert_ie() {
  document.all.sound.src = modulechatbaselink+"sound/msg.wav";
}
function soundPlay(){
 if (!soundEmbed){
  soundEmbed = document.createElement("embed");
  soundEmbed.setAttribute("src", modulechatbaselink+"sound/msg.wav");
  soundEmbed.setAttribute("hidden", true);
  soundEmbed.setAttribute("autostart", true);
 }
 else{
  document.body.removeChild(soundEmbed);
  soundEmbed.removed = true;
  soundEmbed = null;
  soundEmbed = document.createElement("embed");
  soundEmbed.setAttribute("src", modulechatbaselink+"sound/msg.wav");
  soundEmbed.setAttribute("hidden", true);
  soundEmbed.setAttribute("autostart", true);
 }
 soundEmbed.removed = false;
 document.body.appendChild(soundEmbed);
}
jQuery.extend(jQuery.expr[':'], {
    focus: function(element) {
        return element == document.activeElement;
    }
});


/**

 * Cookie plugin

 *

 * Copyright (c) 2006 Klaus Hartl (stilbuero.de)

 * Dual licensed under the MIT and GPL licenses:

 * http://www.opensource.org/licenses/mit-license.php

 * http://www.gnu.org/licenses/gpl.html

 *

 */
jQuery.cookie = function(name, value, options) {
    if (typeof value != 'undefined') { // name and value given, set cookie
        options = options || {};
        if (value === null) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
        }
        // CAUTION: Needed to parenthesize options.path and options.domain
        // in the following expressions, otherwise they evaluate to undefined
        // in the packed version for some reason...
        var path = options.path ? '; path=' + (options.path) : '';
        var domain = options.domain ? '; domain=' + (options.domain) : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { // only name given, get cookie
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};
