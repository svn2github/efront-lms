/**
 *  $Id: vk_iframe.js 546 2009-02-27 08:53:11Z wingedfox $
 *
 *  Keyboard Iframe mode loader
 *
 *  This software is protected by patent No.2009611147 issued on 20.02.2009 by Russian Federal Service for Intellectual Property Patents and Trademarks.
 *
 *  @author Ilya Lebedev
 *  @copyright 2006-2009 Ilya Lebedev <ilya@lebedev.net>
 *  @version $Rev: 546 $
 *  @lastchange $Author: wingedfox $ $Date: 2009-02-27 11:53:11 +0300 (Птн, 27 Фев 2009) $
 *  @class IFrameVirtualKeyboard
 *  @constructor
 */
IFrameVirtualKeyboard=new function(){var i=this;var I=null;var l=null;var o=(function(Q){var _=document.getElementsByTagName('script'),c=new RegExp('^(.*/|)('+Q+')([#?]|$)');for(var C=0,e=_.length;C<e;C++){var v=String(_[C].src).match(c);if(v){if(v[1].match(/^((https?|file)\:\/{2,}|\w:[\\])/))return v[1];if(v[1].indexOf("/")==0)return v[1];b=document.getElementsByTagName('base');if(b[0]&&b[0].href)return b[0].href+v[1];return(document.location.href.match(/(.*[\/\\])/)[0]+v[1]).replace(/^\/+(?=\w:)/,"");}}return null})('vk_iframe.js');i.isOpen=function(){return null!=I&&!I.closed};var O=null;i.attachInput=function(Q){if(I&&I.VirtualKeyboard)return I.VirtualKeyboard.attachInput(Q);return false};i.open=i.show=function(Q,_){var c=false;if('string'==typeof _)_=document.getElementById(_);if(!_)return false;if(!I){I=document.createElement('div');I.innerHTML="<iframe frameborder=\"0\" src=\""+o+"vk_iframe.html\"></iframe>";_.appendChild(I);l=I.firstChild;c=true}if(l&&!i.isOpen()){l.style.display='block';c=true}if(c){if(_!=l.parentNode)_.appendChild(l);O=Q}return false};i.close=i.hide=function(Q){if(i.isOpen())l.style.display='none'};i.isOpen=function(){return l&&'block'==l.style.display};i.toggle=function(Q,_){i.isOpen()?i.close():i.open(Q,_);};i.onload=function(){I=(l.contentWindow||l.contentDocument.window);if('string'==typeof O)O=document.getElementById(O);I.VirtualKeyboard.show(O,I.document.body,I.document.body);I.document.body.className=I.document.body.parentNode.className='VirtualKeyboardPopup';var Q=I.document.body.firstChild;while("virtualKeyboard"!=Q.id){I.document.body.removeChild(Q);Q=I.document.body.firstChild}l.style.height=Q.offsetHeight+'px';l.style.width=Q.offsetWidth+'px'}};
