/**
 * $Id: editor_plugin.js 531 2009-02-05 07:37:13Z wingedfox $
 * $HeadURL: https://svn.debugger.ru/repos/jslibs/Virtual%20Keyboard/tags/VirtualKeyboard.v3.6.1/plugins/tinymce3/editor_plugin.js $
 *
 * Virtual Keyboard plugin for TinyMCE v3 editor.
 * (C) 2006-2007 Ilya Lebedev <ilya@lebedev.net>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * See http://www.gnu.org/copyleft/lesser.html
 *
 * Do not remove this comment if you want to use script!
 * Не удаляйте данный комментарий, если вы хотите использовать скрипт!
 *
 * @author Ilya Lebedev <ilya@lebedev.net>
 * @version $Rev: 531 $
 * @lastchange $Author: wingedfox $
 */
tinymce.PluginManager.requireLangPack('Jsvk');tinymce.create('tinymce.plugins.VirtualKeyboard',new function(){var i=this,I="winxp",l="",o="",O=null,Q;i.VirtualKeyboard=function(C,e){C.addCommand("mceVirtualKeyboard",function(){c(C)});I=C.getParam('vk_skin',I);l=C.getParam('vk_layout',l);o=C.getParam('vk_mode',o);C.addButton('Jsvk',{title:'Jsvk.desc',cmd:'mceVirtualKeyboard',image:e+'/img/jsvk.gif'});C.onInit.add(_);};i.getInfo=function(){return{longname:'VirtualKeyboard plugin',author:'Ilya Lebedev AKA WingedFox',authorurl:'http://www.debugger.ru',infourl:'http://www.debugger.ru/projects/virtualkeyboard/',version:"1.1"}};var _=function(){if(Q)return;Q=true;var C=document.createElement('script');C.src=tinymce.baseURL+'/plugins/Jsvk/jscripts/vk_'+(o.toLowerCase()||'loader')+'.js?vk_skin='+I+'&vk_layout='+l;C.type="text/javascript";C.charset="UTF-8";document.getElementsByTagName('head')[0].appendChild(C);};var c=function(C){var e,v=window[o+'VirtualKeyboard'];if(this._curId===C.editorId&&v.isOpen()){v.close();this._curId=null}else{if(null!=this._curId&&(e=document.getElementById('VirtualKeyboard_'+this._curId))){v.close();}if(!(e=document.getElementById('VirtualKeyboard_'+C.editorId))){e=document.getElementById(C.editorId+"_parent").getElementsByTagName('table')[0];e.insertRow(e.rows.length);e=e.rows[e.rows.length-1];e.id='VirtualKeyboard_'+C.editorId;e.align='center';e.appendChild(document.createElement('td'));}e=e.firstChild;v.open(C.editorId+'_ifr',e);this._curId=C.editorId}}});tinymce.PluginManager.add('Jsvk',tinymce.plugins.VirtualKeyboard);
