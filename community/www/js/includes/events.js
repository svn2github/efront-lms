    $$('.bubbleInfo').each(function(s){
    //alert('bubbleInfo ' + s.id);

         var distance = 10;
         var time = 250;
         var hideDelay = 500;

         var hideDelayTimer = null;

         var beingShown = false;
         var shown = false;

         children = s.childElements();

         for (i=0; i<children.length; i++) {
            if (children[i].className == "trigger") {
                trigger = children[i];
            } else if (children[i].className == "bubblePopup") {
                info = children[i];
                info.style.display = 'none';
            }
         }

         if (trigger && info) {
            trigger.onmouseover = function () {

            //alert(beingShown+"*tr*"+shown);
                if (hideDelayTimer) clearTimeout(hideDelayTimer);

                if (beingShown || shown) {
                    //window.setTimeout('beingShown=false;', time+10);

                    // don't trigger the animation again
                    return;
                } else {
               // alert('to being shown ginetai true apo ton trigger');
                    // reset position of info box
                    beingShown = true;

                    info.style.display = 'none';
                    Effect.Appear(info.id);

                    window.setTimeout('beingShown=false;', time+10);
                    window.setTimeout('shown=true;', time+20);

                }

                return false;
            };
            info.onmouseover = function () {
                if (hideDelayTimer) clearTimeout(hideDelayTimer);
                //alert(beingShown+"*inf*"+shown);
                if (beingShown || shown) {
                    // don't trigger the animation again
                    return;
                } else {
                //alert('to being shown ginetai true apo ton info');
                    // reset position of info box
                    beingShown = true;

                    info.style.display = 'none';
                    Effect.Appear(info.id, {duration: time});

                    window.setTimeout('beingShown=false;', time+10);
                    window.setTimeout('shown=true;', time+20);

                }

                return false;
            };


            s.onmouseout = function () {
               // alert(beingShown+"*triggout*"+shown);
                  if (hideDelayTimer) clearTimeout(hideDelayTimer);

                hideDelayTimer = setTimeout('hideDelayTimer = null;Effect.Fade("'+ info.id + '");shown=false;beingShown=false;' , hideDelay);
           // alert('hiot3');

                return false;

            };
            /*
            s.onmouseout = function () {
               // alert(beingShown+"*infout*"+shown);
                if (hideDelayTimer) clearTimeout(hideDelayTimer);

                hideDelayTimer = setTimeout('hideDelayTimer = null;Effect.Fade("'+ info.id + '");shown=false;beingShown=false;' , hideDelay);

            //alert('hiot2');
                return false;

            };
            */
        }

    });

    var __isIE =  navigator.appVersion.match(/MSIE/);
    var __userAgent = navigator.userAgent;
    var __isFireFox = __userAgent.match(/firefox/i);
    var __isFireFoxOld = __isFireFox && (__userAgent.match(/firefox\/2./i) || __userAgent.match(/firefox\/1./i));
    var __isFireFoxNew = __isFireFox && !__isFireFoxOld;

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

    function __parseBorderWidth(width) {
        var res = 0;
       if (typeof(width) == "string" && width != null && width != "" ) {
          var p = width.indexOf("px");
           if (p >= 0) {
               res = parseInt(width.substring(0, p));
         }
           else {
               //do not know how to calculate other values (such as 0.5em or 0.1cm) correctly now
               //so just set the width to 1 pixel
               res = 1;
           }
       }
       return res;
   }


   //returns border width for some element
   function __getBorderWidth(element) {
       var res = new Object();
       res.left = 0; res.top = 0; res.right = 0; res.bottom = 0;
       if (window.getComputedStyle) {
           //for Firefox
           var elStyle = window.getComputedStyle(element, null);
           res.left = parseInt(elStyle.borderLeftWidth.slice(0, -2));
           res.top = parseInt(elStyle.borderTopWidth.slice(0, -2));
           res.right = parseInt(elStyle.borderRightWidth.slice(0, -2));
           res.bottom = parseInt(elStyle.borderBottomWidth.slice(0, -2));
       }
       else {
           //for other browsers
           res.left = __parseBorderWidth(element.style.borderLeftWidth);
           res.top = __parseBorderWidth(element.style.borderTopWidth);
           res.right = __parseBorderWidth(element.style.borderRightWidth);
           res.bottom = __parseBorderWidth(element.style.borderBottomWidth);
       }

       return res;
   }

   //returns absolute position of some element within document
   function getAbsolutePos(element) {
       var res = new Object();
       res.x = 0; res.y = 0;
       if (element !== null) {
           res.x = element.offsetLeft;
           res.y = element.offsetTop;

           var offsetParent = element.offsetParent;
           var parentNode = element.parentNode;
           var borderWidth = null;

           while (offsetParent != null) {
               res.x += offsetParent.offsetLeft;
               res.y += offsetParent.offsetTop;

               var parentTagName = offsetParent.tagName.toLowerCase();

               if ((__isIE && parentTagName != "table") || (__isFireFoxNew && parentTagName == "td")) {
                   borderWidth = __getBorderWidth(offsetParent);
                   res.x += borderWidth.left;
                   res.y += borderWidth.top;
               }

              if (offsetParent != document.body && offsetParent != document.documentElement) {
                   res.x -= offsetParent.scrollLeft;
                   res.y -= offsetParent.scrollTop;
               }

               //next lines are necessary to support FireFox problem with offsetParent
               if (!__isIE) {
                   while (offsetParent != parentNode && parentNode !== null) {
                       res.x -= parentNode.scrollLeft;
                      res.y -= parentNode.scrollTop;

                       if (__isFireFoxOld) {
                           borderWidth = kGetBorderWidth(parentNode);
                           res.x += borderWidth.left;
                           res.y += borderWidth.top;
                       }
                       parentNode = parentNode.parentNode;
                   }
               }

               parentNode = offsetParent.parentNode;
               offsetParent = offsetParent.offsetParent;
           }
       }
       return res;
   }


    function setPopupPosition(trigger, info) {

        var windowWidth = 0, windowHeight = 0;
        if( typeof( window.innerWidth ) == 'number' ) {
            //Non-IE
            windowWidth = window.innerWidth;
            windowHeight = window.innerHeight;
        } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
            //IE 6+ in 'standards compliant mode'
            windowWidth = document.documentElement.clientWidth;
            windowHeight = document.documentElement.clientHeight;
        } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
            //IE 4 compatible
            windowWidth = document.body.clientWidth;
            windowHeight = document.body.clientHeight;
        }

        info.setStyle({width: (windowWidth/3) + "px"});
        info.style.width  = info.getStyle("width");
        infoWidth  = parseInt(info.style.width);
        info.style.height = info.getStyle("height");
        infoHeight = parseInt(info.style.height);

        triggerHeight = parseInt(trigger.getStyle("height"));
        triggerWidth  = parseInt(trigger.getStyle("width"));
        triggerPosition = getAbsolutePos(trigger);


        // Scrolled down distance
        scrolledDown = parseInt(document.documentElement.scrollTop);
        distanceFromVisibleTop = triggerPosition.y - scrolledDown;

        // Check if upper left corner of trigger item is showing. If not, put entire popup below the trigger
        if (distanceFromVisibleTop > 0) {

            if (windowHeight - distanceFromVisibleTop - triggerHeight > 0) {
                // can be set next to the trigger element
                downCondition = windowHeight - distanceFromVisibleTop;

                // the entire info div will go below the trigger element
                strictDownCondition = downCondition - triggerHeight;

                strictUpCondition = distanceFromVisibleTop;
                upCondition = distanceFromVisibleTop + triggerHeight;
            } else {
                strictUpCondition = distanceFromVisibleTop;
                //alert(strictUpCondition);
                downCondition = 0;
                upCondition = 0;
                strictDownCondition = 0;
            }
        } else {
            strictDownCondition = windowHeight - distanceFromVisibleTop - triggerHeight;
            downCondition = 0;
            upCondition = 0;
            strictUpCondition = 0;
        }

        //alert(downCondition + " " + strictDownCondition +  " " + upCondition + "  " + strictUpCondition + " and " + infoHeight);

        if (downCondition > infoHeight) {
            // Down on the same level
            info.setStyle({top: 0+'px'});
        } else if (strictDownCondition > infoHeight) {
            // Down
            info.setStyle({top: triggerHeight+'px'});
        } else if (upCondition > infoHeight) {
            // Up on the same level
            info.setStyle({top: (-infoHeight+triggerHeight)+'px'});
        } else if (strictUpCondition > infoHeight) {
            // Up
            info.setStyle({top: (-infoHeight)+'px'});
        }
        /*
        if (downCondition > infoHeight) {
            // Down on the same level
            info.setStyle({top: 0+'px'});
        } else if (strictDownCondition > infoHeight) {
            // Down
            info.setStyle({top: triggerHeight+'px'});
        } else if (upCondition > infoHeight) {
            // Up on the same level
            info.setStyle({top: (-infoHeight+triggerHeight)+'px'});
        } else if (strictUpCondition > infoHeight) {
            // Up
            alert('up');
            info.setStyle({top: (-infoHeight)+'px'});
        }*/

        // Scrolled right distance
        scrolledRight = parseInt(document.documentElement.scrollLeft);
        distanceFromVisibleLeft = triggerPosition.x - scrolledRight;

        // Check if leftper left corner of trigger item is showing. If not, put entire popleft below the trigger
        if (distanceFromVisibleLeft > 0) {

            if (windowWidth - distanceFromVisibleLeft - triggerWidth > 0) {
                // can be set next to the trigger element
                rightCondition = windowWidth - distanceFromVisibleLeft;

                // the entire info div will go below the trigger element
                strictRightCondition = rightCondition - triggerWidth;

                strictLeftCondition = distanceFromVisibleLeft;
                leftCondition = distanceFromVisibleLeft + triggerWidth;
            } else {
                strictLeftCondition = distanceFromVisibleLeft;
                //alert(strictLeftCondition);
                rightCondition = 0;
                leftCondition = 0;
                strictRightCondition = 0;
            }
        } else {
            strictRightCondition = windowWidth - distanceFromVisibleLeft - triggerWidth;
            rightCondition = 0;
            leftCondition = 0;
            strictLeftCondition = 0;
        }


        if (strictRightCondition > infoWidth) {
            // Right
            //alert('strict rigjt');
            //alert(trigger.getStyle("left"));
            info.setStyle({left: triggerWidth+'px'});

        } else if (rightCondition > infoWidth) {
            //alert('rigjt');
            // Right on the same level
            //alert(trigger.getStyle("left"));
            info.setStyle({left: 0+'px'});

        } else if (strictLeftCondition > infoWidth) {
            // Left
            //alert('strict left');
            //alert(trigger.getStyle("left"));
            info.setStyle({left: (-infoWidth)+'px'});
        } else if (leftCondition > infoWidth) {
            //alert('left');
            // Left on the same level
            //alert(trigger.getStyle("left"));
            info.setStyle({left: (-infoWidth+triggerWidth)+'px'});
        }
        //alert(infoWidth + " " + infoWidth + ") (" + triggerPosition.x + " " + triggerPosition.y + ") (" + parseInt(document.documentElement.scrollWidth) + " " + parseInt(document.documentElement.scrollLeft) + ") (" + windowWidth + " " + windowWidth);

    }



    var bubbleTriggers = new Array();
    var bubbleInfos = new Array();

    $$('.bubbleInfo').each(function(s){
    //alert('bubbleInfo ' + s.id);

         var distance = 10;
         var time = 125;
         var hideDelay = 500;

         var hideDelayTimer = null;

         // status: 0-hidden, 1-beingShown, 2-shown
         //var status = 0;
         var beingShown = false;
         var shown = false;

         children = s.childElements();
         s.setAttribute("status", 0);

         var ajaxUrl = false;
         var loaded = false;

         for (i=0; i<children.length; i++) {
            if (children[i].className == "trigger") {
                trigger = children[i];
                //alert(trigger.id);
                //break;
            } else if (children[i].className == "bubblePopup") {
                info = children[i];
                temp = info.getAttribute("ajax");
                if (temp) {
                    ajaxUrl = info.getAttribute("ajax");
                }
                info.style.display = 'none';

            }
         }

         bubbleTriggers[s.id] = trigger;
         bubbleInfos[s.id] = info;

        /*
         children = trigger.childElements();
         for (i=0; i<children.length; i++) {
            if (children[i].className == "bubblePopup") {
                info = children[i];
                temp = info.getAttribute("ajax");
                if (temp) {
                    ajaxUrl = info.getAttribute("ajax");
                }
                info.style.display = 'none';

            }
         }
         */
         if (trigger && info) {
            trigger.onmouseover = function () {

                trigger = bubbleTriggers[s.id];
                info = bubbleInfos[s.id];

                //alert("*trigger*"+s.id);

                if (hideDelayTimer) {
                    clearTimeout(hideDelayTimer);
                }

                status = s.getAttribute("status");
                if (status != "0") {
                    // don't trigger the animation again
                    return;
                } else {

                    // reset position of info box
                    s.setAttribute("status", 1);

                    //info.style.display = 'none';

                    if (ajaxUrl && !loaded) {

                        info.innerHTML = "loading...";
                    //  alert(ajaxUrl);
                    }
                    Effect.Appear(info.id);

                    if (ajaxUrl && !loaded) {
                        new Ajax.Request(ajaxUrl, {
                            method:'get',
                            asynchronous:true,
                            onSuccess: function (transport) {
                                info.setStyle({width: "50%"});

                                info.innerHTML = transport.responseText;
                                setPopupPosition(trigger, info);
                            }
                        });

                        loaded = true;

                    } else {
                        info.setStyle({width: "50%"});
                      // alert(info.id);
                        setPopupPosition(trigger, info);

                    }
                    window.setTimeout('document.getElementById("'+s.id+'").setAttribute("status", 2);', time+10);

                }

                return false;
            };
            info.onmouseover = function () {
                trigger = bubbleTriggers[s.id];
                info = bubbleInfos[s.id];
                //  alert("*info*"+status);

                if (hideDelayTimer) {
                    clearTimeout(hideDelayTimer);
                }

                status = s.getAttribute("status");
                if (status != "0") {
                    // don't trigger the animation again
                    return;
                } else {

                    // reset position of info box
                    s.setAttribute("status", 1);
                    if (ajaxUrl && !loaded) {

                        info.innerHTML = "loading...";
                    //  alert(ajaxUrl);
                    }
                    Effect.Appear(info.id);

                    if (ajaxUrl && !loaded) {
                        new Ajax.Request(ajaxUrl, {
                            method:'get',
                            asynchronous:true,
                            onSuccess: function (transport) {
                                info.setStyle({width: "50%"});

                                info.innerHTML = transport.responseText;
                                                        setPopupPosition(trigger, info);

                            }
                        });

                        loaded = true;

                    } else {
                            info.setStyle({width: "50%"});
                        setPopupPosition(trigger, info);

                    }


                    window.setTimeout('document.getElementById("'+s.id+'").setAttribute("status", 2);', time+10);
                }

                return false;
            };


            info.onmouseout = function () {
                trigger = bubbleTriggers[s.id];
                info = bubbleInfos[s.id];
                // alert(beingShown+"*triggout*"+shown);
                if (hideDelayTimer) {
                    clearTimeout(hideDelayTimer);
                }

                hideDelayTimer = setTimeout('hideDelayTimer = null;Effect.Fade("'+ info.id + '");document.getElementById("'+s.id+'").setAttribute("status", 0);' , hideDelay);

                return false;

            };
            trigger.onmouseout = function () {
                trigger = bubbleTriggers[s.id];
                info = bubbleInfos[s.id];
                // alert(beingShown+"*triggout*"+shown);
                if (hideDelayTimer) {
                    clearTimeout(hideDelayTimer);
                }

                hideDelayTimer = setTimeout('hideDelayTimer = null;Effect.Fade("'+ info.id + '");window.setTimeout(\'document.getElementById("'+s.id+'").setAttribute("status", 0);\', '+time+');' , hideDelay);

                return false;

            };

            /*
            s.onmouseout = function () {
               // alert(beingShown+"*triggout*"+shown);
                  if (hideDelayTimer) clearTimeout(hideDelayTimer);

                hideDelayTimer = setTimeout('hideDelayTimer = null;Effect.Fade("'+ info.id + '");window.setTimeout(\'document.getElementById("'+s.id+'").setAttribute("status", 0);\', '+time+');' , hideDelay);
           // alert('hiot3');

                return false;

            };

            s.onmouseout = function () {
               // alert(beingShown+"*infout*"+shown);
                if (hideDelayTimer) clearTimeout(hideDelayTimer);

                hideDelayTimer = setTimeout('hideDelayTimer = null;Effect.Fade("'+ info.id + '");shown=false;beingShown=false;' , hideDelay);

            //alert('hiot2');
                return false;

            };
            */
        }

    });