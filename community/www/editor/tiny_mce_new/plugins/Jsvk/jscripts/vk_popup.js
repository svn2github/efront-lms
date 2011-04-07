/**
 *  $Id: vk_popup.js 546 2009-02-27 08:53:11Z wingedfox $
 *
 *  Keyboard Iframe mode loader
 *
 *  This software is protected by patent No.2009611147 issued on 20.02.2009 by Russian Federal Service for Intellectual Property Patents and Trademarks.
 *
 *  @author Ilya Lebedev
 *  @copyright 2006-2009 Ilya Lebedev <ilya@lebedev.net>
 *  @version $Rev: 546 $
 *  @lastchange $Author: wingedfox $ $Date: 2009-02-27 11:53:11 +0300 (Птн, 27 Фев 2009) $
 *  @class PopupVirtualKeyboard
 *  @constructor
 */
PopupVirtualKeyboard=new function(){var i=this;var I=null;var l=(function(O){var Q=document.getElementsByTagName('script'),_=new RegExp('^(.*/|)('+O+')([#?]|$)');for(var c=0,C=Q.length;c<C;c++){var e=String(Q[c].src).match(_);if(e){if(e[1].match(/^((https?|file)\:\/{2,}|\w:[\\])/))return e[1];if(e[1].indexOf("/")==0)return e[1];b=document.getElementsByTagName('base');if(b[0]&&b[0].href)return b[0].href+e[1];return(document.location.href.match(/(.*[\/\\])/)[0]+e[1]).replace(/^\/+(?=\w:)/,"");}}return null})('vk_popup.js');i.isOpen=function(){return null!=I&&!I.closed};var o=null;i.attachInput=function(O){if(I&&!I.closed&&I.VirtualKeyboard){return I.VirtualKeyboard.attachInput(O);}return false};i.open=i.show=function(O){if(!I||I.closed){I=(window.showModelessDialog||window.open)(l+"vk_popup.html",window.showModelessDialog?window:"_blank","status=0,title=0,dependent=yes,resizable=no,scrollbars=no,width=500,height=500");o=O;return true}return false};i.close=i.hide=function(O){if(!I||I.closed)return false;if(I.VirtualKeyboard.isOpen())I.VirtualKeyboard.hide();I.close();I=null};i.toggle=function(O){i.isOpen()?i.close():i.open(O);};i.onload=function(){if('string'==typeof o)o=document.getElementById(o);I.VirtualKeyboard.show(o,I.document.body,I.document.body.parentNode);I.document.body.className=I.document.body.parentNode.className='VirtualKeyboardPopup';if(I.sizeToContent){I.sizeToContent();}else{var O=I.document.body.firstChild;while("virtualKeyboard"!=O.id){I.document.body.removeChild(O);O=I.document.body.firstChild}I.dialogHeight=O.offsetHeight+'px';I.dialogWidth=O.offsetWidth+'px';I.resizeTo(O.offsetWidth+I.DOM.getOffsetWidth()-I.DOM.getClientWidth(),O.offsetHeight+I.DOM.getOffsetHeight()-I.DOM.getClientHeight());}I.onunload=i.close};if(window.attachEvent)window.attachEvent('onunload',i.close);else if(window.addEventListener)window.addEventListener('unload',i.close,false);};
