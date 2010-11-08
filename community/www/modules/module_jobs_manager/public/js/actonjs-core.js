/**
 * Actonjs Library v. 1.0
 * Author: Actonbit.gr < sales@actonbit.gr >
 * Copyrights: Actonbit.gr < sales@actonbit.gr >
 */

window["undefined"]=window["undefined"];

Actonjs=function () {
 var ua=navigator.userAgent.toLowerCase(),isOpera=ua.indexOf("opera")>-1,isSafari=(/webkit|khtml/).test(ua),isIE=!isOpera&&ua.indexOf("msie")>-1,isIE7=!isOpera&&ua.indexOf("msie 7")>-1,isGecko=!isSafari&&ua.indexOf("gecko")>-1,isWindows=(ua.indexOf("windows")!=-1||ua.indexOf("win32")!=-1),isMac=(ua.indexOf("macintosh")!=-1||ua.indexOf("mac os x")!=-1),isLinux=(ua.indexOf("linux")!=-1),isSecure=window.location.href.toLowerCase().indexOf("https")===0,isStrict=document.compatMode=="CSS1Compat",isReady=false,_timer=false,_queue,_lang,_MASK_COUNTER=0;
 //function get(elId){var el=document.getElementById(elId);return el?el:((typeof elId == 'Object' && typeof != 'string') ? elId : false);}
 function get(elId){var el=document.getElementById(elId);return el?el:false;}
 function getDom(elId){var el=document.getElementById(elId);return el?el.dom:false;}
 function addEvent(obj,ev,fn){var success=false;var obj=(typeof obj=="string")?(get(obj)||false):obj;if (obj){if(obj.addEventListener){obj.addEventListener(ev,fn,false);success=true;}else if(obj.attachEvent){success=obj.attachEvent("on"+ev,fn);}}return success;}
 function removeEvent(obj,ev,fn,uC){var success=false;if (obj.removeEventListener){obj.removeEventListener(ev,fn,uC);success=true;}else if(obj.detachEvent){success=obj.detachEvent("on"+ev,fn);}return success;}
 function load() {if(isGecko||isOpera){if(document.addEventListener){var res = document.addEventListener("DOMContentLoaded",setLoaded,false);}}else if(isSafari){var _timer=setInterval(function(){if(/loaded|complete/.test(document.readyState)){setLoaded();}},10);}else if(isIE) {document.write("<script id=__ie_onload defer src=javascript:void(0)><\/script>");var script = document.getElementById("__ie_onload");script.onreadystatechange=function(){if(this.readyState=="complete"){setLoaded();}};}else{window.onload=setLoaded();}}load();
 function setLoaded(){isReady=true;}

 function onReady(fn){
  if(_timer)clearInterval(_timer);
  if(typeof(_queue)!='object')_queue=new Array();
  if(!isReady){
   if (fn) _queue[_queue.length]=fn;
   _timer=setInterval(function(){onReady(false);},250);
  }
  else{
   if(_timer){
    clearInterval(_timer);
   }
   for(var i=0;i<_queue.length;i++)_queue[i]();
   return
  }
 }

 function namespace(ns){var a=ns,i,j,o=null;var d=a.split(".");var root=d[0];eval("if (typeof "+root+" == \"undefined\"){"+root+" = {};} o = "+root+";");for(j=1;j<d.length;++j){o[d[j]]=o[d[j]]||{};o=o[d[j]];}}
 function LTrim(str){var whitespace=new String(" \t\n\r");var s=new String(str);if(whitespace.indexOf(s.charAt(0))!=-1){var j=0,i=s.length;while(j<i&&whitespace.indexOf(s.charAt(j))!=-1)j++;s=s.substring(j,i);}return s;}
 function RTrim(str){var whitespace=new String(" \t\n\r");var s=new String(str);if(whitespace.indexOf(s.charAt(s.length-1))!=-1){var i=s.length-1;while(i>=0&&whitespace.indexOf(s.charAt(i))!=-1){i--;}s=s.substring(0,i+1);}return s;}
 function Trim(str){return RTrim(LTrim(str));}

 function _utf8_encode (string) {
        string = string.replace(/\r\n/g,"\n");
        var utftext = "";

        for (var n = 0; n < string.length; n++) {
            var c = string.charCodeAt(n);
            if (c < 128) {
                utftext += String.fromCharCode(c);
            }
            else if((c > 127) && (c < 2048)) {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            }
            else {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }
        }
        return utftext;
    }

    function _utf8_decode (utftext) {
        var string = "";
        var i = 0;
        var c = c1 = c2 = 0;

        while ( i < utftext.length ) {
            c = utftext.charCodeAt(i);
            if (c < 128) {
                string += String.fromCharCode(c);
                i++;
            }
            else if((c > 191) && (c < 224)) {
                c2 = utftext.charCodeAt(i+1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            }
            else {
                c2 = utftext.charCodeAt(i+1);
                c3 = utftext.charCodeAt(i+2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }
        }

        return string;
    }

 return {
  init: function() {
   if(isIE&&!isIE7){try{document.execCommand("BackgroundImageCache",false,true)}catch(e){}}
   //debugger;
   //findLang();
   //if (!isIE) window.loadFirebugConsole();
  },

  isOpera: function() {return isOpera;},
  isSafari: function() {return isSafari;},
  isGecko: function() {return isGecko;},
  isIE: function() {return isIE;},
  isIE7: function() {return isIE7;},
  isWin: function() {return isWin;},
  isMac: function() {return isMac;},
  isLinux: function() {return isLinux;},

  ltrim: function(string) {return LTrim(string);},
  rtrim: function(string) {return RTrim(string);},
  trim: function(string) {return Trim(' '+string+' ');},

  namespace: function(args) {return namespace(args);},
  addEvent: function(obj,ev,fn) {return addEvent(obj,ev,fn);},
  removeEvent: function(obj,ev,fn,aC) {return removeEvent(obj,ev,fn,aC);},
  get: function(id){return get(id);},
  getDom: function(id){return getDom(id);},

  onReady: function(fn){onReady(fn)},

  utf8encode: function(string){return escape(_utf8_encode(string));},
  utf8decode: function(string){return _utf8_decode(unescape(string));},

  callback: function() {alert('llllaaaaaa');},
  getLang: function() {return findLang();},

  // Library version here ... 
  version: "1.0"
 }
}();

Actonjs.init();
