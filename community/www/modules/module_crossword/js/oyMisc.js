/*



	CWORD JavaScript Crossword Engine



	Copyright (C) 2007-2010 Pavel Simakov

	http://www.softwaresecretweapons.com/jspwiki/cword



	This library is free software; you can redistribute it and/or

	modify it under the terms of the GNU Lesser General Public

	License as published by the Free Software Foundation; either

	version 2.1 of the License, or (at your option) any later version.



	This library is distributed in the hope that it will be useful,

	but WITHOUT ANY WARRANTY; without even the implied warranty of

	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU

	Lesser General Public License for more details.



	You should have received a copy of the GNU Lesser General Public

	License along with this library; if not, write to the Free Software

	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA



*/
//
// Footer manager
//
function oyCrosswordFooter(puzz){
 this.puzz = puzz;
}
oyCrosswordFooter.prototype.bind = function(){
 var oThis = this;
 this.clockTime = setInterval(
  function(){
   oThis.clockUpdate();
  }, 1000
 );
}
oyCrosswordFooter.prototype.unbind = function(){
 clearInterval(this.clockTime);
 this.clockTime = null;
}
oyCrosswordFooter.prototype.stateOk = function(text){
 var target = document.getElementById("oygState");
 target.innerHTML = "Status: <font color='#008000'>" + text + "</font>&nbsp;";
 target.className = "ousStateOk";
}
oyCrosswordFooter.prototype.stateBusy = function(text){
 var target = document.getElementById("oygState");
 target.innerHTML = "Status: <font color='#0000FF'>" + text + "</font>&nbsp;";
 target.className = "ousStateBusy";
}
oyCrosswordFooter.prototype.stateError = function(text){
 var target = document.getElementById("oygState");
 target.innerHTML = "Status: <font color='#FF0000'>" + text + "</font>&nbsp;";
 target.className = "ousStateError";
}
oyCrosswordFooter.prototype.clockUpdate = function(){
 var pad = function (i)
 {
 if (i<10)
   {i="0" + i}
   return i
 }
 if (this.puzz.started){
  var ms = new Date().getTime() - this.puzz.menu.startOn.getTime();
  var sec = Math.round(ms / 1000);
  var min = 0;
  if (sec >= 60){
   min = Math.round(sec / 60);
   sec = sec % 60;
  }
  document.getElementById("oygFooterClock").innerHTML = "&nbsp;Time: <b>" + pad(min) + "</b>:<b>" + pad(sec) +"</b>";
  document.getElementById("crosstime").value = pad(min) + ":" + pad(sec) ;
 }
}
oyCrosswordFooter.prototype.update = function(){
 var buf = "";
 if (!this.puzz.started){
  buf += "Game has not yet started!";
 } else {
 var comword = document.getElementById("completewordlength").value;
 var comscore = Math.round(this.puzz.menu.score/comword*100);
  buf += "Score: <span class = 'progressNumber'>" + comscore + " %</span><span class = 'progressBar' style = 'width:"+comscore+"px;'>&nbsp;</span>";
  if(this.puzz.menu.rank != -1){
   buf += " (rank <b>" + this.puzz.menu.rank + "</b>)";
  }
 }
 document.getElementById("oygFooterStatus").innerHTML = buf;
 document.getElementById("points").value = this.puzz.menu.score;
}
//
// This is cache for speeding up document.getElementById()
//
function oyGridElementCache(w, h, ns){
 this.ns = ns;
 this.cache = new Array();
 for (var i=0; i < h; i++){
  for (var j=0; j < w; j++){
   var key;
   key = this.ns + j + "_" + i;
   this.cache[key] = document.getElementById(key);
  }
 }
}
oyGridElementCache.prototype.getElement = function(x, y){
 return this.cache[this.ns + x + "_" + y];
}
//
// Global functions
//

var ie4 = (document.all) ? true : false;
var ns4 = (document.layers) ? true : false;
var ns6 = (document.getElementById && !document.all) ? true : false;

function oyShowLayer(lay) {
 if (ie4) {
  document.all[lay].style.visibility = "visible";
  document.all[lay].style.display = "block";
 }
 if (ns4) {
  document.layers[lay].visibility = "show";
 }
 if (ns6) {
  document.getElementById([lay]).style.visibility = "visible";
  document.getElementById([lay]).style.display = "block";
 }
}

function oyHideLayer(lay) {
 if (ie4) {
  document.all[lay].style.visibility = "hidden";
  document.all[lay].style.display = "none";
 }
 if (ns4) {
  document.layers[lay].visibility = "hide";
 }
 if (ns6) {
  document.getElementById([lay]).style.visibility = "hidden";
  document.getElementById([lay]).style.display = "none";
 }
}


 /**

 * Sets a Cookie with the given name and value.

 *

 * name       Name of the cookie

 * value      Value of the cookie

 * [expires]  Expiration date of the cookie (default: end of current session)

 * [path]     Path where the cookie is valid (default: path of calling document)

 * [domain]   Domain where the cookie is valid

 *              (default: domain of calling document)

 * [secure]   Boolean value indicating if the cookie transmission requires a

 *              secure transmission

 */
function oySetCookie(name, value, expires, path, domain, secure)
{
    document.cookie= name + "=" + escape(value) +
        ((expires) ? "; expires=" + expires.toGMTString() : "") +
        ((path) ? "; path=" + path : "") +
        ((domain) ? "; domain=" + domain : "") +
        ((secure) ? "; secure" : "");
}
/**

 * Gets the value of the specified cookie.

 *

 * name  Name of the desired cookie.

 *

 * Returns a string containing value of specified cookie,

 *   or null if cookie does not exist.

 */
function oyGetCookie(name)
{
    var dc = document.cookie;
    var prefix = name + "=";
    var begin = dc.indexOf("; " + prefix);
    if (begin == -1)
    {
        begin = dc.indexOf(prefix);
        if (begin != 0)
        return null;
    }
    else
    {
        begin += 2;
    }
    var end = document.cookie.indexOf(";", begin);
    if (end == -1)
    {
        end = dc.length;
    }
    return unescape(dc.substring(begin + prefix.length, end));
}
 /**

 *	expires can be set to 1000*60*60*24*90 if you want the cookie expire in 90 days

 */
function oySetCookieForPeriod(name, value, expires, path, domain, secure){
    var expdate = new Date ();
    expdate.setTime (expdate.getTime() + expires);
    oySetCookie(name, value, expdate, path, domain, secure)
}
function oyBrowserDetection(){
 this.agt=navigator.userAgent.toLowerCase();
 this.browser='';
 this.version=0;
 this.compleVersion=0;
 this.isIE=false;
 this.isNetscape=false;
 this.isFirefox=false;
 this.isGood=false;
 this.sf=false;
 this.isWin=((this.agt.indexOf("win")!=-1)||(this.agt.indexOf("16bit")!=-1));
 this.isMac=(this.agt.indexOf("mac")!=-1);
 this.isLinux = (this.agt.indexOf("linux")!=-1);
 if (navigator.userAgent.indexOf('MSIE') != -1 && navigator.userAgent.indexOf('AOL')==-1 ){
  this.browser = 'IE'
   this.isIE = true;
   reg = /(MSIE)(.)(\d+)(.)(\d+)/i
  ar = reg.exec(navigator.userAgent)
  this.version = ar[3]
   this.compleVersion = ar[3]+ar[4]+ar[5]
 } else if (navigator.userAgent.indexOf('Firefox') != -1 ){
  this.browser = 'Firefox'
  this.isFirefox =true;
  reg = /(Firefox)(.)(\d+)(.)(\d+)/i
  ar = reg.exec(navigator.userAgent)
  this.version = ar[3]
  this.compleVersion = ar[3]+ar[4]+ar[5]
 } else if (navigator.userAgent.indexOf('Netscape') != -1 ){
  this.browser = 'Netscape'
  this.isNetscape=true;
  reg = /(Netscape)(.)(\d+)(.)(\d+)/i
  ar = reg.exec(navigator.userAgent)
  this.version = ar[3]
  this.compleVersion = ar[3]+ar[4]+ar[5]
 }else if(navigator.userAgent.indexOf("Safari") !=-1){
  this.sf=true;
 }
 if (
  (this.isIE && this.version>=5) ||
  (this.isNetscape && this.version >=6) ||
  (this.isFirefox && this.version >=1)
 ){
  this.isGood = true;
 }
}
function oygBind(puzz){
 var isSupported = new oyBrowserDetection().isGood;
 if (!isSupported){
  var msg =
   "Your current browser is not ideal for accessing this site.\n" +
   "We support Microsoft IE 6.0+, Firefox 1.0+, Netscape6.0+ versions.\n\n" +
   "It might still work OK, do you want to try?";
  if (confirm(msg)){
   isSupported = true;
  }
 }
 if (!isSupported){
  oygError = "This browser is not supported.";
 } else {
  var div = document.getElementById("oygContext");
  if (div == null){
   oygError = "Bad template file.";
  } else {
   var frame = document.getElementById("oygPuzzle");
   if (frame == null){
    oygError = "Failed to load puzzle file.";
   } else {
    puzz.init();
    puzz.render();
    puzz.bind();
    puzz.hlist.clickItem(0);
    puzz.menu.installContextMenu();
   }
  }
 }
}
function oygNextRandomInt(){
 var rnd = "" + Math.random();
 var idx = rnd.indexOf(".");
 return rnd.substring(idx + 1);
}
//
// Let others know that we are done.
//
oygInit = true;
oygError = null;
