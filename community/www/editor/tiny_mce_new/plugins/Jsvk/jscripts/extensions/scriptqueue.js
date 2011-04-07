/**
 *  $Id: scriptqueue.js 475 2008-09-09 07:58:34Z wingedfox $
 *  $HeadURL: https://svn.debugger.ru/repos/jslibs/BrowserExtensions/tags/BrowserExtensions.023/scriptqueue.js $
 *
 *  Dynamically load scripts and script queues (when load order is important)
 *
 **********NOTE********
 *  If you need to load any scripts before ScriptQueue exists, use the following snippet
 *  <code>
 *      if (!(window.ScriptQueueIncludes instanceof Array)) window.ScriptQueueIncludes = []
 *      window.ScriptQueueIncludes = window.ScriptQueueIncludes.concat(scriptsarray);
 *  </code>
 *  ScriptQueue loads all the scripts, queued before its' load in the ScriptQueueIncludes
 **********
 *
 *  @author Ilya Lebedev <ilya@lebedev.net>
 *  @modified $Date: 2008-09-09 11:58:34 +0400 (Втр, 09 Сен 2008) $
 *  @version $Rev: 475 $
 *  @license LGPL 2.1 or later
 *
 *  @class ScriptQueue
 *  @param {Function} optional callback function, called on each successful script load
 *  @scope public
 */
ScriptQueue=function(i){var I=this,static=arguments.callee;if('function'!=typeof i)i=function(){};var o=[];I.load=function(C){O(C,i);};I.queue=function(C){var e=o.length;o[e]=C;if(!e)O(C,_);};var O=function(C,i){var e,v=static.scripts;if(e=v.hash[C]){v=static.scripts[e];if(v[2]){i(C,v[2]);}else{v[1].push(i);}}else{e=v.length;v[e]=[C,[i],false];v.hash[C]=e;Q(C);}};var Q=function(C){if(document.body){var e=document.createElement('script'),v=document.getElementsByTagName("head")[0];e.type="text/javascript";e.charset="UTF-8";e.src=C;e.rSrc=C;e.onerror=e.onload=e.onreadystatechange=c;v.appendChild(e);}else{document.write("<scr"+"ipt onload=\"\" src=\""+C+"\" charset=\"UTF-8\"></scr"+"ipt>");c.call({'rSrc':C},{'type':'load'});}};var _=function(C,e){i(C,e);o.splice(0,1);if(o.length&&e)O(o[0],arguments.callee);else i(null,e)};var c=function(C){var e=static.scripts,v=e.hash[this.rSrc],C=C||window.event,V;e=e[v];if(e&&!e[2]){if('load'==C.type||'complete'==this.readyState||'loaded'==this.readyState){e[2]=V=true}else if('error'==C.type){V=false}if(null!=V){for(var x=0,i=e[1],X=i.length;x<X;x++){i[x](e[0],e[2]);}if(!V){delete static.scripts.hash[this.rSrc];delete static.scripts[v]}}}}};ScriptQueue.scripts=[false];ScriptQueue.scripts.hash={};ScriptQueue.queue=function(i,I){if(!i.length)return;var l=new ScriptQueue(I);for(var o=0,O=i.length;o<O;o++){l.queue(i[o]);}};ScriptQueue.load=function(i,I){if(i){(new ScriptQueue(I)).load(i);}};if(window.ScriptQueueIncludes instanceof Array){ScriptQueue.queue(window.ScriptQueueIncludes);}
