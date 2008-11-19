<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>

<meta http-equiv = "Content-Language" content = "{$smarty.const._HEADERLANGUAGETAG}" />
<meta http-equiv = "keywords"         content = "education" />
<meta http-equiv = "description"      content = "Collaborative Elearning Platform" />
<meta http-equiv = "Content-Type"     content = "text/html; charset = utf-8"/>
<link rel="shortcut icon" href="images/favicon.ico" >
 {if !isset($T_CUSTOM_CSS)}
    <link rel = "stylesheet" type = "text/css" href = "css/css_global.php" />
{else}
    <link rel = "stylesheet" type = "text/css" href = "css/custom_css/{$T_CUSTOM_CSS}" />
{/if}

            <script>{if $T_BROWSER == 'IE6'}{assign var='globalImageExtension' value='gif'}var globalImageExtension = 'gif';{else}{assign var='globalImageExtension' value='png'}var globalImageExtension = 'png';{/if}</script>

            <script>
            {literal}

			// Chat functions that are globally used
			
			// This id relates to the periodical functionality of updating the active chat room, if the chat tab is open
			var chatroomIntervalId = 0;
			// This id relates to the periodical functionality of checking for any chat activity, if the chat tab is closed
			var chatactivityIntervalId = 0;
			function stopAjaxChat() {
				if (chatroomIntervalId > 0) {
					disableChat();
					clearInterval(chatroomIntervalId);
					// Start a new interval to check for room movement every 60 seconds
					// This periodic call is used to check for activity during the last 60 seconds.
					// We note here that the restart_session flag is sent to denote that we want all messages of the last 5 minutes
					// If none are found then the image of messages will dissapear - if it had appeared before
					chatactivityIntervalId = setInterval("makeAjaxRequest('ask_chat.php?chatrooms_ID='+$('current_chatroom_id').value+'&any_activity=1','special_get_request','chat')",4000);
				}
			}
			
			// Function to get the room's messages. During the first time the room is loaded ($('first_time_messages').value =1) and 
			// all messages from the last five minutes should be brought.
			function ajaxGetMessages() {
				// The check here is used for synchronization. If $('current_chatroom_id').value==-1 then value have not been set correctly yet.
				if ($('current_chatroom_id').value != -1) {
					if ($('first_time_messages').value == 1) {
						makeAjaxRequest('ask_chat.php?chatrooms_ID='+$('current_chatroom_id').value+'&restart_session=1','special_get_request','chat')
						$('first_time_messages').value = 0;
					} else {
						makeAjaxRequest('ask_chat.php?chatrooms_ID='+$('current_chatroom_id').value,'special_get_request','chat')
					}
				}			
			}
			 
			function startAjaxChat() {
				// Stop the previous Ajax Chat if the chatting is enabled for this user 
				// otherwise the chatroomIntervalid will remain 0;
				{/literal}{if $T_CHATENABLED == 1}{literal}
				if (chatactivityIntervalId > 0) {
					clearInterval(chatactivityIntervalId);
					Effect.Fade($('new_chat_messages'));
				}
					
				// No idea, why this initialization was put here. Maybe should leave
				if (chatroomIntervalId > 0) {
					disableChat();
					clearInterval(chatroomIntervalId);
				}
					
				enableChat();
				chatroomIntervalId = setInterval("ajaxGetMessages()",2500);
				{/literal}{/if}{literal}
			}	
			
			function resize_iframe()
			{
				{/literal}{if $T_CHATENABLED == 1}{literal}
							
				var height= $('listmenu{/literal}{$T_MENUCOUNT}{literal}').getHeight();//window.innerWidth;//Firefox
				//alert(height);
							
				var offset_to_subtract = {/literal}{if $T_ONLY_VIEW_CHAT == 1}48{else}70{/if}{literal};
				offset_to_subtract = offset_to_subtract{/literal}{if $T_BROWSER == 'IE6' || $T_BROWSER == 'IE7'}-2{elseif $T_BROWSER == 'Safari'}+4{elseif $T_BROWSER == 'Chrome'}+15{/if}{literal};
				
				
				//resize the iframe according to the size of the
				//window (all these should be on the same line)
				
				$('glu').setStyle({height: (height-offset_to_subtract) + 'px'});
				//$('glu').up().setStyle({height: (height-offset_to_subtract) + 'px'});
				//test.document.getElementById("chat_content").style.height = parseInt(height-offset_to_subtract)+ "px";
				if (test && test.document && test.document.getElementById("chat_content")) {
					test.document.getElementById("chat_content").style.height = parseInt(height-offset_to_subtract)+ "px";
				}	
				{/literal}{/if}{literal}
				
			}
            
            // The following functions are used to highlight the correct menu on page load or refresh
            var active_id = '{/literal}{$T_ACTIVE_ID}{literal}';
	
			// This global variable is used to denote whether the chat is currently working or not
			var chatEnabled = 0;
			function enableChat() {
				chatEnabled = 1;
			}
			function disableChat() {
				chatEnabled = 0;
			}
			
            function changeTDcolor(id) {
            	 
                if (!id) {
             return false;
                }
                if(active_id != id)
                {

              if(document.getElementById(active_id))
                    {
                        $(active_id).className = "menuOption";
                    }

               if(document.getElementById(active_id+"_a"))
                    {
                           $(active_id+"_a").className = "menuOption"; //"menuLinkInactive";
                    }
                    active_id = id;

				// If the chat menu is enabled then do not automatically move the menus
                	if(document.getElementById(id)) {
                           $(id).className = "selectedTopTitle";// rightAlign";
                           if ( $(id).up() && $(id).up().up() && chatEnabled == 0) {
                               document.move($(id).up().up());
                           }
                    }
                 	if(document.getElementById(id+"_a") && chatEnabled == 0)
                    {
                           $(active_id+"_a").className = "selectedTopTitle";
                           if ( $(active_id+"_a").up() && $(active_id+"_a").up().up()) {
                               document.move($(active_id+"_a").up().up());
                           }
                    }

                }



            }

            function gotoLessons() {
                top.mainframe.location="http://{/literal}{$smarty.session.s_type}{literal}.php?ctg=lessons";
            }

            function changeColorOnRefresh() {
                {/literal}
                {if $smarty.session.s_type == 'student'}
                      var temp_id = 'control_panel';
                {elseif $smarty.session.s_type == 'professor'}
                      var temp_id = 'control_panel';
                {elseif $smarty.session.s_type == 'administrator'}
                  var temp_id = 'control';
                {/if}
            //alert('mesa apo tin refresh');
                {if $T_CTG == 'lessons'}
                  changeTDcolor(temp_id);
                {else}
                   changeTDcolor("{$T_CTG}");
                {/if}
                {literal}
            }

            </script>


            <script language = "JavaScript" type = "text/javascript" src = "js/EfrontScripts.php"></script>
            <script language = "JavaScript" type = "text/javascript" src = "js/print-script.php"></script>
            <script language = "JavaScript" type = "text/javascript" src = "js/scriptaculous/prototype.php"></script>
            <script language = "JavaScript" type = "text/javascript" src = "js/scriptaculous/effects.php"></script>

            <script language = "JavaScript" type = "text/javascript">
                // Get unread messages
                var ie_str;
                var detect = navigator.userAgent.toLowerCase();
                detect.indexOf("msie") > 0 ? ie_str = "?ie=1" :ie_str = "";
            {/literal}{if !$T_NO_MESSAGES}{literal}
                makeAjaxRequest('ask_unread_messages.php'+ie_str,'special_get_request','general');
                setInterval("makeAjaxRequest('ask_unread_messages.php' + ie_str, 'special_get_request', 'general')", 100000);
            {/literal}{/if}{literal}
                // Initialize toggle arrows
                var arrow_status = "down";
                function initArrows() {
                     var windowSize = getWindowSize();
                     var windowHeight = parseInt(windowSize[1]);
                     scrollHeight = parseInt(document.documentElement.scrollHeight);
                     //alert("windowHeight = "+windowHeight+" scrollHeight = "+scrollHeight);
                     $('loading_sidebar').setStyle({display:'none'});
                }
                window.onresize = resizeFunction;

                function setArrowStatus(status) {
                      arrow_status = status;
                      initArrows();
                }

                // Utility function for debugging
                function myPrint(element) {
                    alert("Element: " + element.id + "\n\nposition.top:\t" + element.style.top + "\npositionedOffset().top:\t" + element.positionedOffset().top + "\nHeight:\t" + element.getHeight() + "\nmargin-top:\t" + element.style.marginTop);
                }


                // Function to fix the height of the curtains used to hide underlying menus - the menus must follow an order menu = <menu1,menu2...,menuN,logout>
                function fixCurtains() {
                       var windowSize = getWindowSize();
                       var windowHeight = parseInt(windowSize[1]);

                       var menus = $('menu').childElements().length - 1; // we do not take "logout" into account
                       var offset;

                       var i = menus;
                       for (i = menus; i > 0; i--) {

                       offset = windowHeight - $('tabmenu').getHeight() - $('logout').getHeight() -1;
                       for (k = 1; k <= i; k++) {
                           offset -= ($('tabmenu'+k).getHeight()+1);
                       }

                       j = i + 1;
                        // Check the next menus
                       while ( $('menu'+j) ) {
                            if ($('menu'+j).status =='down') {
                               offset = offset - $('tabmenu'+j).getHeight() - 1;
                           }
                           j = j + 1;
                       }

                       if (offset > 0) {
                           $('listmenu'+i).setStyle("height: " +(offset)+"px;");
                       }
                    }

                    // Hiding all menus above the selected one: chech menus 1...menus-1
                   for (i = 1; i < menus; i++) {
                       if ( (Object.isUndefined($('menu'+i).status) || $('menu'+i).status == "up") && (Object.isUndefined($('menu'+(i+1)).status) || $('menu'+(i+1)).status != "down")) {
                           if(i==1) {
                               $('listmenu'+i).hide();
                           } else {
                               $('menu'+i).down(1).hide();
                           }
                       } else {
                           break;
                       }
                    }
                    
                    // Code to correct sizes of the iframe.
                    resize_iframe();
                }


                // Function called on resizing the window. Changes the position of the tabheaders and fixes the Curtains by calling fixCurtain
                function resizeFunction() {

                        var windowSize = getWindowSize();
                        var windowHeight = parseInt(windowSize[1]);

                        // Adjust the menu size
                        var menus = $('menu').childElements().length - 1; // we do not take "logout" into account
                        var wholeMenuSize = windowHeight - $('tabmenu').getHeight();
                        var i = 2;

                        for (i = 2; i <= menus; i++) {
                            if ($('menu'+i).status == "down") {
                                wholeMenuSize = wholeMenuSize - $('menu'+i).getHeight();
                            }
                        }

                        {/literal}
                        {if $T_BROWSER == 'IE6'}
                        {literal}
                            $('menu').setAttribute("height", (wholeMenuSize) + 'px');
                        {/literal}
                        {else}
                        {literal}
                        	if (wholeMenuSize > 0) {
                            	$('menu').setStyle({height: (wholeMenuSize) + 'px'});
                            }	
                        {/literal}
                        {/if}
                        {literal}


                        // Adjust logout position
                        //$('logout').setStyle({marginTop: '0px'});
                        var newTop = (windowHeight-$('logout').getHeight());
                        $('logout').setStyle({top: (newTop)+'px'});

                        var logoutLogout = $('logout').style.top.split("px");
                        logoutLogout  = parseInt(logoutLogout[0]);

                        var temp;

                        // Adjust all other menus
                        var i = 2;
                        for (i = 2; i <= menus; i++) {
                            if(Object.isUndefined($('menu'+i).status) || $('menu'+i).status == 'up') {
                                newTop = $('tabmenu').getHeight();
                                temp =(i-2) * $('tabmenu2').getHeight();
                                temp += $('tabmenu'+i).getHeight();
                                newTop = newTop + temp;
                            } else {
                                temp = (menus - i + 2);
                                temp = temp * ($('tabmenu2').getHeight()+1);
                                newTop = windowHeight - temp;
                            }
                            $('menu'+i).setStyle({marginTop: (0)+'px'});
                            $('menu'+i).setStyle({top: (newTop)+'px'});
                        }

                        // Fix the curtains used to hide the menus
                        fixCurtains();
                        
                        
                }
            </script>
            {/literal}

            {literal}
            </head>

            <body class = "sidebar" onLoad = "initArrows();">

            <a id = "arrow_down" href="#bottom" onclick="new Effect.ScrollTo('bottom',{offset:-140}); setArrowStatus('up');   return false;" style="position:{/literal}{if $T_BROWSER == 'IE6'}absolute{else}fixed{/if}{literal}; z-index:50; bottom:5px; right:1px; display:none;">{/literal}<img src="images/16x16/navigate_down.{$globalImageExtension}" border = "0" alt="{$smarty.const._SCROLLTOBOTTOMOFPAGE}" title="{$smarty.const._SCROLLTOBOTTOMOFPAGE}"{literal}/></a>
            <a id = "arrow_up"   href="#top"    onclick="new Effect.ScrollTo('top',{offset:-140});setArrowStatus('down'); return false;" style="position:{/literal}{if $T_BROWSER == 'IE6'}absolute{else}fixed{/if}{literal}; z-index:50; bottom:5px; right:1px; display: none;">{/literal}<img src="images/16x16/navigate_up.{$globalImageExtension}" border = "0" alt="{$smarty.const._SCROLLTOTOPOFPAGE}" title="{$smarty.const._SCROLLTOTOPOFPAGE}"{literal}/></a>
            {/literal}

            <span id = "nobookmarks" style = "display:none">{$smarty.const._YOUHAVENOBOOKMARKS}</span>

            {literal}
                <script language = "JavaScript" type = "text/javascript">
                    var lock = 0;
                     // Function to move menus up and down
                    document.move = function(element) {
                        if (!lock) {
                            lock = 1;
                        	// Check whether you are moving the chatmenu so that you start or stop the ajax requests for the chat
				  			if ($('tab'+element.id).getAttribute("name") && $('tab'+element.id).getAttribute("name") == "chatmenu") {
				  				startAjaxChat();
				  			} else {
				  				stopAjaxChat();
				  			}
                      
                           	//alert("down"+element.up().name);	
                            if (Object.isUndefined(element.status) || element.status == 'up') {

                            //Ypologismos apo to last element (edw logout) mexri to yparxwn
                            var newPos = element.nextSiblings().last().positionedOffset().top - element.positionedOffset().top - (element.nextSiblings().last().getHeight() * (element.nextSiblings().length));

                            for (var i = 0; i < element.nextSiblings().length - 1; i++) {
                                if (Object.isUndefined(element.next(i).status) || element.next(i).status == 'up') {
                                    newPos = element.next(i).nextSiblings().last().positionedOffset().top - element.next(i).positionedOffset().top - (element.next(i).nextSiblings().last().getHeight() * element.next(i).nextSiblings().length);
                                    element.next(i).status = 'down';
                                    element.next(i).down(1).hide();
                                    effect = new Effect.MoveUpDown(element.next(i), newPos);
                                }
                            }

                                if (element.id == 'menu1') {
                                    setTimeout(function(){$('listmenu1').show();}, 200);
                                } else {
                                    setTimeout(function(){element.down(1).show();}, 200); //element.down(1).show();
                                }
                            } else {

                            for (var i = element.previousSiblings().length - 1; i>=0; i--) {
                //alert('to proigoumeno '+i+' einai to '+ element.previous(i).id + ' me katastasi '+ element.previous(i).status);
                                if (element.previous(i).status == 'down') {

                                    newPos = -(element.previous(i).positionedOffset().top - element.previous(i).previousSiblings().last().positionedOffset().top);
                                    size = element.previous(i).previousSiblings().length-1;
                                    for (var j = 1; j <= size; j++) {
                                        newPos += $('tabmenu'+j).getHeight();
                                    }


                                    newPos += $('tabmenu1').getHeight() - 1;
                                    element.previous(i).status = 'up';
                                    element.previous(i).down(1).hide();
                                    effect = new Effect.MoveUpDown(element.previous(i), newPos);
                                }
                            }
                            //alert(element.previousSiblings().last().id + ' edw ' + element.getDimensions().height + ' element ID ' + element.id);
                            //alert(element.positionedOffset().top + ' <= element.top , last.top => ' +  element.previousSiblings().last().positionedOffset().top);
                            var newPos = -(element.positionedOffset().top - element.previousSiblings().last().positionedOffset().top);

                            size = element.previousSiblings().length;
                            for (j = 1; j <= size; j++) {
                                newPos += $('tabmenu'+j).getHeight();
                            }
                            //newPos += $('tabmenu1').getHeight() - 1;

                            element.status = 'up';

                            effect = new Effect.MoveUpDown(element, newPos);

                            setTimeout(function(){$('listmenu1').hide();element.down(1).show();}, 250);

                            }
                            
                            setTimeout(function(){fixCurtains();}, 250);
                            setTimeout(function(){lock = 0;}, 250);
                        }

                    }

                Effect.MoveUpDown = function(element, offset) {
                  element = $(element);
                  var oldStyle = {opacity: element.getInlineOpacity() };
                  return new Effect.Parallel(
                    [ new Effect.Move(element, {x: 0, y: offset, sync: true }),
                      new Effect.Opacity(element, { sync: true, to: 0.5 }) ],
                    Object.extend(
                      { duration: 0.25,
                        beforeSetup: function(effect) {
                          //effect.effects[0].element.makePositioned();
                        },
                        afterFinishInternal: function(effect) {
                          effect.effects[0].element.setStyle(oldStyle);
                        }
                      }, arguments[1] || { }));
                };

                var initUpperTabHeight;
                var photoHeight;


                document.fixUpperMenu = function() {


                            var windowSize = getWindowSize();
                            var windowHeight = parseInt(windowSize[1]);
                            if ($('tabmenu')) {
                                initUpperTabHeight = $('tabmenu').getHeight();
                            }
                            if ($('topPhoto')) {
                                photoHeight = $('topPhoto').getHeight();
                            }
                            {/literal}
                            {if $T_BROWSER == 'IE6'}
                            {literal}
                                if ($('tabmenu')) {
                                    var tempSize = windowHeight - $('tabmenu').getHeight();
                                    if ($('menu')) {
                                        $('menu').setAttribute("height", (tempSize) + 'px');
                                    }
                                }
                            {/literal}
                            {else}
                            {literal}
                                if ($('menu')) {
                                    $('menu').setStyle({height: (windowHeight - $('tabmenu').getHeight()) + 'px'});
                                }
                            {/literal}
                            {/if}
                            {literal}

                            if ($('logout')) {
                                $('logout').setStyle({top: (windowHeight-$('logout').getHeight())+'px'});
                                // The following code is used to move the logout button to the bottom of the page
                                $('logout').status = "down";


                                // The following code is used to set the active menu appearing and the rest to their correct positions
                                var i = 2;
                                var offset =$('logout').style.top.split("px");
                                offset = offset[0];
                            }

                            if ($('menu')) {
                                var menus = $('menu').childElements().length - 1; // we do not take "logout" into account

                                var active_menu = {/literal}{$T_ACTIVE_MENU}{literal};

                                if (active_menu == 1) {
                                    // All menu tabs go down, except the top (which is stable)
                                    for (i = menus; i >= 2; i--) {
                                        if ($('tabmenu'+i) && $('menu'+i)) {
                                            offset = offset - $('tabmenu' + i).getHeight() - 1;
                                            $('menu'+i).setStyle({top: (offset)+'px'});
                                            $('menu'+i).status = 'down';
                                            $('menu'+i).down(1).hide();
                                        }
                                    }
                                } else {

                                    // Only menu tabs after the active menu go down, the rest remain up
                                    for (i = menus; i > active_menu; i--) {
                                        if ($('tabmenu'+i) && $('menu'+i)) {
                                            offset = offset - $('tabmenu' + i).getHeight() - 1;
                                            $('menu'+i).setStyle({top: (offset)+'px'});
                                            $('menu'+i).status = 'down';
                                            $('menu'+i).down(1).hide();
                                        }
                                    }

                                    offset =initUpperTabHeight;

                                    for (i = 2; i <= active_menu; i++) {
                                        if ($('tabmenu'+(i-1)) && $('menu'+i)) {
                                            offset = offset + $('tabmenu' + (i-1)).getHeight() + 1;
                                            $('menu'+i).setStyle({top: (offset)+'px'});
                                            $('menu'+i).status = 'up';
                                        }
                                    }
                                }
                            }

                }

                // Function used to hide and show the upper part of the sidebar
                document.myhide = function() {
                                var element = $('tabmenu');
                                var tempHeight;
                                // Adjust the height of the top frame
                                if (element.status == 'hidden') {
                                    element.status = 'visible';
                                    $('topPhoto').show();
                                } else {
                                    element.status = 'hidden';
                                    $('topPhoto').hide();
                                    tempHeight = initUpperTabHeight-photoHeight;
                                }

                                var menus = $('menu').childElements().length - 1; // we do not take "logout" into account
                                var offset, menuTopOffset;
                               // Adjust all other menus
                                var i =2;
                                for (i = 2; i <= menus; i++) {
                                    if (Object.isUndefined($('menu'+i).status) || $('menu'+i).status == 'up') {
                                        menuTopOffset = $('menu'+i).style.top.split("px");
                                        //eeeedw
                                        // SWSTI SOLUTION: offset = $('tabmenu').getHeight() + (i-1) * $('tabmenu1').getHeight();
                                        offset = $('tabmenu').getHeight() + (i-2) * $('tabmenu2').getHeight();
                                        offset += $('tabmenu1').getHeight();
                                        $('menu'+i).setStyle({top: (offset)+'px'});
                                    }
                                }

                                // Adjust logout menu
                                var windowSize = getWindowSize();
                                var windowHeight = parseInt(windowSize[1]);
                                $('logout').setStyle({top: (windowHeight-$('logout').getHeight())+'px'});
                                {/literal}
                                fixCurtains();

                };


            </script>

			{math assign='T_SB_WIDTH_MINUS_ONE' equation="x-1" x=$T_SIDEBARWIDTH}
			<div id="loading_sidebar" class="loading" style="opacity: 0.9; height: 100%; width: {$T_SB_WIDTH_MINUS_ONE}px; display: block;" ><div style="top: 50%; left:12%; position: absolute;" ><img src="images/others/progress1.gif" style="vertical-align: middle;"/><span style="vertical-align: middle;">{$smarty.const._LOADINGDATA}</span></div></div>

            {* Top menu with photo and name - Hiding on click *}
            <div class = "tabmenu" id = "tabmenu" align="center">
            <br />

                {* Photo *}
                <div class = "topPhoto" id = "topPhoto">
                	<a href = "{if $smarty.session.s_type == "administrator"}administrator.php?ctg=users&edit_user={$smarty.session.s_login}{else}{$smarty.session.s_type}.php?ctg=personal{/if}" target = "mainframe"> 
                    {if isset($T_AVATAR)}	<!--onclick = "javascript:myhide()"-->
                        <img src = "view_file.php?file={$T_AVATAR}" border = "0" title="{$smarty.const._CURRENTAVATAR}" alt="{$smarty.const._CURRENTAVATAR}" 
                        {if isset($T_NEWWIDTH)} width = "{$T_NEWWIDTH}" height = "{$T_NEWHEIGHT}"{/if}
                        onLoad ="javascript:fixUpperMenu()" />
                    {else}
                        <img src = "images/avatars/system_avatars/unknown_small.{$globalImageExtension}" border = "0" title="{$smarty.const._EFRONT}" alt="{$smarty.const._EFRONT}" onLoad ="javascript:fixUpperMenu()" />
                    {/if}
					</a>
                </div>
                <div style="font-size: 10px" id = "personIdentity">
                    <table width="100%" align="center">
                        <tr><td><a href="#" class = "info nonEmptyLesson" id="nameSurname" onmouseover="$('tooltipImg').style.visibility = 'visible';" onmouseout="$('tooltipImg').style.visibility = 'hidden';">{$T_RESULT.name}&nbsp;{$T_RESULT.surname}<img id="tooltipImg" class = "tooltip" border = '0' src='images/others/tooltip_arrow.gif'><span class = 'tooltipSpan' id='userInfo' style="font-size: 10px" >{$T_TYPE}</span></a></td></tr>
                        <tr><td align="center" vertical-align="middle">
                                <table>
                                    <tr><td id="unread_img">{if $T_UNREAD_MESSAGES != 0}<img src = "images/16x16/mail2.{$globalImageExtension}" style="border:0; float: left;" title="{$smarty.const._MESSAGES}" alt="{$smarty.const._MESSAGES}" />{/if}</td>
                                        <td id="recent_unread_left">{if $T_UNREAD_MESSAGES != 0}(<a href = "forum/messages_index.php" target="mainframe">{$T_UNREAD_MESSAGES}</a>){/if}</td>
                                    </tr>
                                </table>

                             </td>
                         </tr>
                    </table>
                </div>

                {* Search div *}
                <div width="100%" align="center">
	                <div style="background:transparent url('images/others/search_bg.png') no-repeat scroll 0%; height:29px; width:175px;">
	                    <form action = "{$smarty.const.G_SERVERNAME}{$smarty.session.s_type}.php?ctg=control_panel&op=search" method = "post" target="mainframe">
	                        <div id="search_suggest"></div>
	                        <input type="text" name="search_text" 
	                            value = "{if isset($smarty.post.search_text)}{$smarty.post.search_text}{else}{$smarty.const._SEARCH}...{/if}"
	                            onFocus = "this.value='';makeAjaxRequest('ask_suggestions.php','special_get_request','suggestions');"
	                            style="background-image:url('images/16x16/search.png'); background-repeat:no-repeat; border:0pt none; float:center; margin:6px 0pt 0pt 6px; padding-left:18px; width:134px}" /> <!-- width:134px;-->
	                        <input type = "hidden" name = "current_location" id = "current_location" value = ""/>
	                    </form>
	                </div>
	            </div>    
            </div>


            {* Basic menu called "menu" includes all other menus in successive order: menu1 (always), menu2,..., menuN, logout (always) *}
            <div class = "menu" id = "menu">

                {*********}
                {* MENUS *}
                {*********}
                {foreach name = 'outer_menu' key = 'menu_key' item = 'menu' from = $T_MENU}
                <div class = "verticalTab" id = "menu{$menu_key}" style = "margin-top:0px" style="background-color:#EEEEEE;">
                    <div class = "tabHeader" onclick = "move($('menu{$menu_key}'));" id="tabmenu{$menu_key}" title = "{$menu.title}">{$menu.title|eF_truncate:30}</div>
                    <div class = "menuList" id="listmenu{$menu_key}">
                        {foreach name = 'options_list' key = 'option_id' item = 'option' from = $menu.options}
                            {if isset($option.html)}
                                <div class = "menuOption">{$option.html}</div>
                            {else}
                                <div class = "menuOption" id="{$option.id}" ><table><tr style="vertical-align:middle;">

                                <td><a  href = "{$option.link}"  target="{$option.target}">
                                    {if isset($option.moduleLink)}
                                        {if isset($option.eFrontExtensions)}
                                            <img src="images/{$option.image}.{$globalImageExtension}" border="0">
                                        {else}
                                            <img src="images/{$option.image}" border="0">
                                        {/if}
                                    {else}
                                    <img src="images/16x16/{$option.image}.{$globalImageExtension}" border="0">
                                    {/if}
                                    </a>
                                </td>
                                <td><a  href = "{$option.link}"  target="{$option.target}">{$option.title}</a></td></tr></table></div>
                            {/if}
                        {/foreach}
                    </div>
                </div>
                {/foreach}

                {*********************************}
                {* 		NEXT MENU : CHAT TAB  	 *}
                {*********************************}
			{if $T_CHATENABLED == 1}                
				{math assign='T_SB_WIDTH_MINUS_5' equation="x-5" x=$T_SIDEBARWIDTH}
                {assign var='generalWidth' value=$T_SB_WIDTH_MINUS_5}

                <div class = "verticalTab" id = "menu{$T_MENUCOUNT}">
					<script type= "text/javascript" src="../js/print-script.js"></script>
	                
					{literal}
					<script>
					function ajaxBringRooms() {

						var url = "{/literal}{$smarty.const.G_SERVERNAME}{literal}ask_chat.php?bring_chatrooms=1";
						
						
						//$('chat_message
				        new Ajax.Request(url, {
				                method:'get',
				                asynchronous:true,
				                onSuccess: function (transport) {
				//alert(transport.responseText);
				
				                    var select_item = document.getElementById('chat_rooms');
				                    // Delete all exept from the default room
				                    while(select_item.length > 1) {
				                        select_item.remove(1);
				                    }
				
				                    var temp = transport.responseText.split('special_splitter');
				                    var elOptNew;
				                    var i;
									var j = 1;
									var selIndex = 0;
									current_room = $('current_chatroom_id').value;
				                    for (i = 0; i < temp.length-2; i = i + 3,j++) {
				                        elOptNew = document.createElement('option');
				                        // The "_" is appended to the id of the room to denote that
				                        // this room's administration belongs to this user
				                        isOwned = temp[i].lastIndexOf("_");
				                        if (isOwned > 0) {
				                        	elOptNew.value = temp[i].substr(0,isOwned);
											// This attribute denotes that the room belongs to this user 
											// and so can be deleted by him. Create the delete link and display it
				                        	elOptNew.setAttribute("isOwned", "1");				                        	
				                        } else {
				                        	elOptNew.value = temp[i];

				                        }	
				                        
				                        if (elOptNew.value == current_room) {
				                            elOptNew.selected = true;
				                            selIndex = j;
												                            
				                        }
				                        
				                        elOptNew.text = temp[i+1]; // + ' (' + temp[i+2] + ')';//parenthesis
				
				                        try {
				                            select_item.add(elOptNew,null);
				                        } catch(ex) {
				                            select_item.add(elOptNew); // IE only
				                        }
				
				                    }	         
				                    
									if (selIndex) {
					                    select_item.selIndex = selIndex;
					                    select_item.selectedIndex = selIndex; //always exists - 'No specific job description' in the branch
									}
				                }
				            });
							
						}
					
					// Only JS: get name from the chat_room select list
					function getChatRoomName() {
						var allText = $('chat_rooms').options[$('chat_rooms').selectedIndex].text;
						name = allText.lastIndexOf("(");
						
						if (name > 0) {
							return allText.substr(0,name - 1); // the minus one for the space before the bracket
						} else {
							return allText;
						}
						
					}
						
					function ajaxEnterRoom(el) {

						var room = $('chat_rooms').value;
						var url = "{/literal}{$smarty.const.G_SERVERNAME}{literal}ask_chat.php?chatrooms_ID="+room+"&add_user={/literal}{$smarty.session.s_login}{literal}&add_user_type={/literal}{$smarty.session.s_type}{literal}";
						//alert(url);
				        new Ajax.Request(url, {
				                method:'get',
				                asynchronous:false,
				                onSuccess: function (transport) {
				                	$('current_chatroom_id').value = -1;	// used for sync
									$('last_spoken_login').value = "";
									$('first_time_messages').value = 1;
									test.document.getElementById("chat_content").innerHTML="";
									$('current_chatroom_id').value = $('chat_rooms').value;  // sync over all values correct
								
									isOwned = $('chat_rooms').options[$('chat_rooms').selectedIndex].getAttribute("isOwned");
									
									if (isOwned == 1) {
										$('delete_room').setStyle({display:'block'});
										$('delete_room_image').setStyle({display:'block'});
									} else {
										$('delete_room').setStyle({display:'none'});
									}	
							
									//alert($('current_chatroom_id').value);
			             	   }
				         });	
					}													

					function ajaxGetRoomUsers(el, event) {
						Element.extend(el);
						url = 'ask_chat.php?chatrooms_ID='+$('chat_rooms').value+'&get_users=1';
						$('room_users_image').writeAttribute({src:'images/others/progress1.gif'}).show();                                                                    

						new Ajax.Request(url, {
                            method:'get',
                            asynchronous:true,
                            onSuccess: function (transport) {
                            	
                            	
                            	name = getChatRoomName();
                            	$('room_users').innerHTML = '<b>' + name + ' {/literal}{$smarty.const._ONLINEUSERS}{literal}</b><br>';
                            	if (transport.responseText == "") {
                            	    $('room_users').innerHTML += "{/literal}{$smarty.const._THEREARENOOTHERUSERSRIGHTNOWINTHISROOM}{literal}";
                            	} else {
                            		$('room_users').innerHTML += transport.responseText;
                            	}			                        
								eF_js_showHideDiv(el, 'room_users', event);
								$('room_users_image').writeAttribute({src:'images/16x16/users3.gif'}).show();
								$('room_users').setStyle({top:'20px',left:'1px'});    	 
                            	
							}
						});
					
					}		
					
					function inviteChatRoom(el) {
						el.href = "forum/new_message.php?chat_invite=" + $('current_chatroom_id').value;
					}
					
					function returnToMainRoom() {
	                	$('current_chatroom_id').value = -1;	// used for sync - lock acquired
						$('last_spoken_login').value = "";
						$('first_time_messages').value = 1;
						$('chat_rooms').value = 0;
						$('delete_room').setStyle({display:'none'});
						test.document.getElementById("chat_content").innerHTML += '{/literal}<span style="font-size:10px;color:red;">{$smarty.const._REDIRECTEDTOEFRONTMAIN}</span>{literal}';
						$('current_chatroom_id').value = 0;     // sync complete - lock released					
					}
					
					function ajaxDeleteRoom() {
						url = 'ask_chat.php?chatrooms_ID='+$('chat_rooms').value+'&delete_room=1';
						$('delete_room_image').writeAttribute({src:'images/others/progress1.gif'}).show();                                                                    

						new Ajax.Request(url, {
                            method:'get',
                            asynchronous:true,
                            onSuccess: function (transport) {
                            	                                                                    
                                $('delete_room_image').hide().setAttribute('src', 'images/16x16/check.png');
                                new Effect.Appear($('delete_room_image'));
                                window.setTimeout('Effect.Fade("delete_room_image")', 2000);
                                window.setTimeout("$('delete_room').setStyle({display:'none'})", 3000);
                                window.setTimeout("$('delete_room_image').writeAttribute({src:'images/16x16/delete.png'})", 3200);
                                
                                //$('delete_room').setStyle({display:'none'});
                            	name = getChatRoomName();
                            	test.document.getElementById("chat_content").innerHTML =  '<span style="font-size:10px;color:red;">' + name + ': {/literal}{$smarty.const._CHATROOMDELETEDBYOWNER}<br>{literal}</span>';
								
								returnToMainRoom();			                            	
                            	
							}
						});
					}	 	
					</script>
					{/literal}	
	                                
                    <div class = "tabHeader" onclick = "move($('menu{$T_MENUCOUNT}'));" name="chatmenu" id="tabmenu{$T_MENUCOUNT}"><table cellpadding="0" cellspacing="0" width="100%"><tr><td align="left">{$smarty.const._CHAT}</td><td align="right"><img id="new_chat_messages" src="images/16x16/messages.png"  style = "vertical-align:middle;display:none" border=0 title = "{$smarty.const._NEWCHATMESSAGES}"/></td><td width="1px"></td></tr></table></div>
                    <div class = "menuList" id="listmenu{$T_MENUCOUNT}" align="center">
                   
						<table cellpadding="0" cellspacing="0">


							<tr><td align="left">
								<table cellpadding="0" cellspacing="0" width="100%"><tr valign="middle"><td width="1"></td>
									<td>{$smarty.const._SELECTCHATROOM}:</td><!--         										
										<img src="images/16x16/users3.png" valign="middle" border=0 />
									-->
									
									<td align="right">
										<table>
											<tr>
												<td valign="middle">
													<a href = "javascript:void(0);" onclick= "ajaxGetRoomUsers(this,event)">
														<img id = "room_users_image" src = "images/16x16/users3.png" alt = "{$smarty.const._SHOWUSERSINROOM}" title = "{$smarty.const._SHOWUSERSINROOM}" border="0" style = "vertical-align:middle"/></a>
													{math assign='T_SB_WIDTH_MINUS_32' equation="x-32" x=$T_SIDEBARWIDTH}
													<div id = 'room_users' onclick = "eF_js_showHideDiv(this, 'room_users', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:{$T_SB_WIDTH_MINUS_32}px;position:absolute;left:0px;top:0px;display:none"></div><!-width:143px;-->	
						                    	</td>
												<td {if $T_ONLY_VIEW_CHAT == 1}style="display:none"{/if}>			
													<a class = "inviteLink" href = "javascript:void(0)" target = "POPUP_FRAME" onclick = "inviteChatRoom(this);eF_js_showDivPopup('{$smarty.const._INVITEUSERS}', new Array('800px', '500px'))">
														<img src="images/16x16/mail_forward.png" alt = "{$smarty.const._INVITEUSERS}" title = "{$smarty.const._INVITEUSERS}" border = "0" style = "vertical-align:middle"/></a>		
												</td>
												<td {if $T_ONLY_VIEW_CHAT == 1}style="display:none"{/if}>
													<a href = "chat/chat_room_options.php?new_public_room=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._NEWPUBLICROOM}', new Array('90px', '30px'))" class = "innerTable"><img src = "images/16x16/add2.png" alt = "{$smarty.const._NEWPUBLICROOM}" title = "{$smarty.const._NEWPUBLICROOM}" border = "0" style = "vertical-align:middle"/></a>
												</td>
												<td><div id="delete_room" {if isset($T_CHATROOM_OWNED)}style="display:block"{else}style="display:none"{/if}><a href="javascript:void(0)" onClick="ajaxDeleteRoom()"><img id="delete_room_image" src="images/16x16/delete.png" border="0" style = "vertical-align:middle"/></a></div></td>
												</td>
												{* Hidden used to denote who is the last user spoken for this room *}
												<td><input type = "hidden" id = "last_spoken_login" name = "last_spoken_login" value = ""/></td>
												{* Hidden used to denote if this is the first time getting messages from a room *}
												<td><input type = "hidden" id = "first_time_messages" name = "first_time_messages" value = "1"/></td>
											</tr>
										</table>
									</td>
									</tr></table></td></tr>
							<tr><td align="center"><select width="{$generalWidth}" STYLE="width: {$generalWidth}px;font-size:10px" id = "chat_rooms" {if $T_BROWSER == 'IE6' || $T_BROWSER == 'IE7'}onfocus{else}onclick{/if}="ajaxBringRooms()" onchange="ajaxEnterRoom(this)">
														<option value="0" {if $T_CHATROOMS_ID == 0}selected{/if}>{$smarty.const._EFRONTMAIN}</option>
														{foreach name = 'chat_rooms' item = 'room' from = $T_CHATROOMS}
															{if $room.users > 0}
															<option value="{$room.id}" {if $T_CHATROOMS_ID == $room.id}selected{/if}>{$room.name}</option> {*>&nbsp;({$room.users})*}
															{/if}
														{/foreach}
														</select>
								</td>
								
							</tr>	
							<tr><td align="center" width="100%"> 
								<iframe name = "test" frameborder = "no" scrolling="no" id="glu" width = "{$generalWidth}" onload="resize_iframe();" src = "chat/blank.php" />{$smarty.const._SORRYNEEDIFRAME}</iframe>
								
								<!--div name="test" style="overflow-y:auto;overflow-x:no" id = "chat_content"  width = "{$generalWidth}" height="50px" bgcolor="white">
								&nbsp;								
								</div-->
								
								</td>
							</tr>
							<form name = "chat_form" action = "javascript:sendMessage(document.chat_form.chat_message.value,$('current_chatroom_id').value); " method = "post">
                            <tr {if $T_ONLY_VIEW_CHAT == 1}style="display:none"{/if}><td>    
                                <table cellpadding="0" cellspacing="0" border="0"><tr>
                                <td width="20"><a href = "chat/smilies.php" onclick = "eF_js_showDivPopup('{$smarty.const._SMILIES}', new Array('250px', '150px'))" target = "POPUP_FRAME"><img src = "images/smilies/icon_smile.gif" style="vertical-align:middle" border = "0"/></a></td>
                                
                                {math assign='T_SB_WIDTH_MINUS_20' equation="x-20" x=$T_SIDEBARWIDTH}
                                
                                <td align="left" width="{$T_SB_WIDTH_MINUS_20}" nowrap>
                                	<input type = "text" name = "chat_message" width="{$T_SB_WIDTH_MINUS_20}" style = "width:97%" valign = "middle" onpaste = "javascript: document.chat_form.submit.disabled = false;" onKeyup = "javascript:enableButton();" onMouseup = "javascript:enableButton();"/>
                                </td>
                                </tr></table>
                                </td>                            
                                <td><input type = "submit" name = "submit" value = "{$smarty.const._SEND}"  class = "flatButton" style="display:none"/>
                                	<input type = "hidden" id = "current_chatroom_id" name = "hidden_chat_room_id" value = "{$T_CHATROOMS_ID}"/></td>
                            </tr>
                            </form>    
						</table>
                    </div>
                </div>
                {math assign='T_MENUCOUNT' equation="x+1" x=$T_MENUCOUNT}
			{/if}

                {*********************************}
                {* NEXT MENU : ONLINE USERS MENU *}
                {*********************************}
                {if isset($T_ONLINE_USERS_LIST)}

                <div class = "verticalTab" id = "menu{$T_MENUCOUNT}" >
                {** IE JS BUGBUGBUG 1923 **}
                    <div class = "tabHeader" onclick = "move($('menu{$T_MENUCOUNT}'));" id="tabmenu{$T_MENUCOUNT}">{$smarty.const._ONLINEUSERS}&nbsp;&nbsp;({$T_ONLINE_USERS_COUNT})</div>
                    <div class = "menuList" id="listmenu{$T_MENUCOUNT}">
                    <div id = "users-online"></div>
                        <table width = "100%">
                                {if $T_ONLINE_USERS_LIST|@count > 20}
                            <tr>
                               <td id = "users_online" style="display:none;">
                                       {*eF_template_printOnlineUsers data = $T_ONLINE_USERS_LIST*}
                                  </td></tr>
                                </tr>
                                {else}
                                   <td id = "users_online">
                                       {*eF_template_printOnlineUsers data = $T_ONLINE_USERS_LIST*}
                                   </td></tr>
                               {/if}
                        </table>

                    </div>
                </div>
                {math assign='menuCount' equation="x+1" x=$menuCount}
                {/if}
			
                {***********************}
                {* FINAL MENU : LOGOUT *}
                {***********************}
                <div class = "verticalTab" id = "logout">
                    <div class = "tabHeader">
                        <table width=100% style="vertical-align:top">
                            <tr style="vertical-align:top">
                                <td style="vertical-align:top;align:right;">
                                    <a href = "{$smarty.const.G_SERVERNAME}index.php?logout=true" style="text-decoration:none;vertical-align=top;" target = "_top">{$smarty.const._LOGOUT}</a>
                                </td><td>
                                    <img src = "images/16x16/exit.{$globalImageExtension}" border = "0" onclick = "top.location='index.php?logout=true'" align="right" title = "{$smarty.const._LOGOUT}"/>
                                </td><td width="1px"></td>
                            </tr>
                        </table>
                    </div>
                </div>

            </div>
            <input type ="hidden" id = "online_users_text" value="{$smarty.const._ONLINEUSERS}" class ="tabmenu{$T_MENUCOUNT}" />

            {literal}
            <script language = "JavaScript" type = "text/javascript">
                document.fixUpperMenu();
            /*
            // The following code is used to move the logout button to the bottom of the page
                $('logout').status = "down";

                var windowSize = getWindowSize();
                var windowHeight = parseInt(windowSize[1]);
                initUpperTabHeight = $('tabmenu').getHeight();
                photoHeight = $('topPhoto').getHeight();
                {/literal}
                {if $T_BROWSER == 'IE6'}
                {literal}
                    var tempSize = windowHeight - $('tabmenu').getHeight();
                    $('menu').setAttribute("height", (tempSize) + 'px');
                {/literal}
                {else}
                {literal}
                    $('menu').setStyle({height: (windowHeight - $('tabmenu').getHeight()) + 'px'});
                {/literal}
                {/if}
                {literal}

                $('logout').setStyle({top: (windowHeight-$('logout').getHeight())+'px'});

                // The following code is used to set the active menu appearing and the rest to their correct positions
                var i = 2;
                var offset =$('logout').style.top.split("px");
                offset = offset[0];

                var menus = $('menu').childElements().length - 1; // we do not take "logout" into account

                var active_menu = {/literal}{$T_ACTIVE_MENU}{literal};

                if (active_menu == 1) {
                    // All menu tabs go down, except the top (which is stable)
                    for (i = menus; i >= 2; i--) {
                        offset = offset - $('tabmenu' + i).getHeight() - 1;
                        $('menu'+i).setStyle({top: (offset)+'px'});
                        $('menu'+i).status = 'down';
                        $('menu'+i).down(1).hide();
            //            alert('menu'+i+' '+$('menu'+i).status );
                    }
                } else {

                    // Only menu tabs after the active menu go down, the rest remain up
                    for (i = menus; i > active_menu; i--) {
                        offset = offset - $('tabmenu' + i).getHeight() - 1;
                        $('menu'+i).setStyle({top: (offset)+'px'});
                        $('menu'+i).status = 'down';
                        $('menu'+i).down(1).hide();

            //alert('menu'+i+' '+$('menu'+i).status );
                    }

                    offset =initUpperTabHeight;

                    for (i = 2; i <= active_menu; i++) {
                        offset = offset + $('tabmenu' + (i-1)).getHeight() + 1;
                        $('menu'+i).setStyle({top: (offset)+'px'});
                        $('menu'+i).status = 'up';
            //alert('menu'+i+' '+$('menu'+i).status );
                    }
                }

                var heightVar = $('tabmenu').getHeight();
                $('tabmenu').setStyle({height: (heightVar) + 'px'});

                var i = 2;

                offset = heightVar;
                var menus = $('menu').childElements().length - 1; // we do not take "logout" into account
                for (i = 2; i <= menus ; i++) {
                    offset = offset + $('tabmenu' + (i-1)).getHeight() + 1;
                    $('menu'+i).setStyle({top: (offset)+'px'});
                }
            */


            </script>
            {/literal}
			
            <img id = "toggleSidebarImage" src = "images/others/blank.gif" onClick = "toggleSidebar('{$smarty.session.s_login}');checkSidebarMode('{$smarty.session.s_login}');" style = "position: absolute; top:4px; right: -1px; cursor: pointer; " align = "right" alt = "{$smarty.const._SHOWHIDE}" title = "{$smarty.const._SHOWHIDE}"/>
            {if ($smarty.session.s_type=='administrator')}
            <img id = "mainPageImage" src = "images/16x16/home.png" onClick = "top.mainframe.location='{$smarty.session.s_type}.php?ctg=control_panel'" style = "position: fixed; top:25px; left: 1000px; cursor: pointer; " align = "right" alt = "{$smarty.const._MAINPAGE}" title = "{$smarty.const._MAINPAGE}"/>
            {else}
            <img id = "mainPageImage" src = "images/16x16/home.png" {if ($smarty.session.s_lessons_ID!='')}onClick = "top.mainframe.location='{$smarty.session.s_type}.php?ctg=control_panel&lessons_ID={$smarty.session.s_lessons_ID}';checkToOpenSidebar('{$smarty.session.s_login}')"{/if} style = "{if ($smarty.session.s_lessons_ID=='')}filter:alpha(opacity=50);-moz-opacity:0.5;opacity:0.5;{/if}position: fixed; top:25px; left: 1000px; {if ($smarty.session.s_lessons_ID!='')}cursor: pointer; {/if}" align = "right" {if ($smarty.session.s_lessons_ID!='')}alt = "{$smarty.const._LESSONMAINPAGE}" title = "{$smarty.const._LESSONMAINPAGE}"{/if}/>
            {/if}
            <img id = "logoutImage" src = "images/16x16/exit.{$globalImageExtension}" onClick = "top.location='index.php?logout=true'" style = "position: fixed; top:45px; left: 1000px;cursor: pointer; " align = "right" {if ($smarty.session.s_lessons_ID!='')}alt = "{$smarty.const._LOGOUT}" title = "{$smarty.const._LOGOUT}"{/if}/>



            <script language = "JavaScript" type = "text/javascript">
                {literal}
				function checkSidebarMode(s_login){
					var value = readCookie(s_login+'_sidebar');
					var valueMode = readCookie(s_login+'_sidebarMode');
					var unit = top.mainframe.location.toString().match('view_unit');
					
					if(unit=='view_unit' && value == 'hidden') {
						createCookie(s_login+'_sidebarMode','automatic',30);
					} else {
						createCookie(s_login+'_sidebarMode','manual',30);
					}
				
				}
            /*
                if (typeof(changeColorOnRefresh) != 'undefined') {
                    changeColorOnRefresh();
                }
            */
                initSidebar({/literal}'{$smarty.session.s_login}'{literal});          //initialization of sidebar according to cookie value

                $('userInfo').setStyle({left: -($('nameSurname').positionedOffset().left) + "px"});
                $('userInfo').setStyle({{/literal}{if $T_BROWSER == 'IE6'}width{else}minWidth{/if}{literal}: ($('tabmenu').getWidth()-30) + "px"});

                {/literal}
            {*
                {if isset($smarty.get.new_lesson_id)}
                    changeTDcolor('lesson_main');
                {else if $T_SB_CTG != ''}
                    changeTDcolor('{$T_SB_CTG}');
                {/if}
                *}

                {literal}
            fixCurtains();
                var menus = $('menu').childElements().length - 1; // we do not take "logout" into account

                for (i = 1 ; i <= menus; i++) {
                    $('listmenu'+i).setStyle({overflowY: 'auto'});
                }

                if(document.getElementById(active_id+"_a"))
                {
                       $(active_id+"_a").className = "selectedTopTitle";
                }

                {/literal}

            </script>


            <!--<script type = "text/javascript" src = "jsslashfiles/menu.js"></script> There is no that file any more....Why?-->
            <div id="dimmer" class = "dimmerDiv" style="display:none;"></div>
            <script>
            if (parent.frames[0].document.getElementById('dimmer')) parent.frames[0].document.getElementById('dimmer').style.display = 'none'


            </script>
            <input type="hidden" value="myhidden" id="hasLoaded" />
</body>
</html>