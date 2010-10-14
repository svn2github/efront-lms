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
// This is connection endpoint
//
function oygEndpoint(){
 this.cookie = oygNextRandomInt(); // each endpoint is unique
 this.seq = 0; // all requests are sequentially numbered
 this.oob = 0; // out of ban responces
 this.badcookie = 0; // reply not to my cookie
}
oygEndpoint.noendpoint = 0; // counter of cases when reply received to non-existent end point
function oygCompletionPort(endpoint, url){
 this.endpoint = endpoint;
 endpoint.seq++;
 this.seq = endpoint.seq;
 this.onError = null;
 this.onTimeout = null;
 this.onComplete = null;
 this.onDone = null;
 this.timeout = 15 * 1000;
 this.ajax = new JSONscriptRequest(url + "&seq=" + this.seq+ "&cookie=" + endpoint.cookie);
}
oygCompletionPort.prototype.init = function(){
 this.ajax.init();
 var oThis = this;
 this.timer = setTimeout(
  function(){
   var onTimeout = oThis.onTimeout;
   oThis.finit();
   if (onTimeout != null){
    onTimeout();
   }
  }, this.timeout
 );
 this.ajax.submit();
}
oygCompletionPort.prototype.finit = function(){
 this.onError = null;
 this.onTimeout = null;
 this.onComplete = null;
 this.ajax.finit();
 this.ajax = null;
 clearTimeout(this.timer);
 this.timer = null;
}
function oyServer(appHome, ns, canTalkToServer){
 this.appHome = appHome;
 this.ns = ns;
 this.canTalkToServer = canTalkToServer;
 this.ep = new oygEndpoint();
 this.md5 = new oySign();
 this.trackSeq = 0;
 this.trackURL = this.appHome + "/app/trackAction.php";
 this.submitURL = this.appHome + "/app/submitScore.php";
}
//
// compute state matrix [0, 1, ,1, 0, ...] and 
// concatenate all completed clue answers
//
oyServer.prototype.computeMatrix = function(clues){
 var result = new function(){};

 var concat = "";
 var states = "";
 for (var i=0; i < clues.length; i++){
  if (clues[i].matched) {
   states += "1";
   concat += clues[i].answer;
  } else {
   states += "0";
  }
 }

 result.states = states;
 result.concat = concat;

 return result;
}

oyServer.prototype.trackAction = function(uid, verb){
 if (this.canTalkToServer && verb != null){
  var key = escape(uid);
  var data =
   "uid=" + key +
   "&ns=" + escape(this.ns) +
   "&verb=" + escape(verb);
  var sign = this.md5.hex_hmac_md5(key, data);
  var qstr = "data=" + escape(data) + "&sign=" + sign + "&seq=" + this.trackSeq;
  var url = this.trackURL + "?" + qstr;

  document.getElementById("oygTrackAction").src = url;

  this.trackSeq++;
 }
}

oyServer.prototype.submitScore = function (target, uid, score, deducts, checks, reveals, matches, time, name, clues){
 var key = uid;
 var matrix = this.computeMatrix(clues);
 var concat = this.md5.hex_hmac_md5(key, matrix.concat);
 var data =
  "uid=" + key +
  "&ns=" + escape(this.ns) +
  "&states=" + matrix.states +
  "&concat=" + concat +
  "&score=" + score +
  "&deducts" + deducts +
  "&checks=" + checks +
  "&reveals=" + reveals +
  "&matches=" + matches +
  "&time=" + time +
  "&name=" + escape(name);
 var sign = this.md5.hex_hmac_md5(key, data);
 var qstr = "uid=" + key + "&data=" + escape(data) + "&sign=" + sign;
 var url = this.submitURL + "?" + qstr;

 this.submitScoreAjaxAnywhere(this.ep, target, url, matches);
}

oyServer.prototype.submitScoreAjaxAnywhere = function (ep, target, url, matches){
 var oThis = target;
 var cp = new oygCompletionPort(ep, url);

 cp.onComplete = function(data){
  oThis.scoreSubmittedMatches = matches;
  oThis.rank = data.rank;
  oThis.invalidateMenu();
  oThis.footer.stateOk("Score submitted!");
 }

 cp.onTimeout = function(){
  oThis.footer.stateError("Timeout waiting for server to reply!");
  alert("Failed to submit score. Server didn't reply!");
 }

 cp.onError = function(msg){
  oThis.footer.stateError("Failed to submit score!");
  alert("Failed to submit score. Server replied with:\n\n" + msg);
 }

 oygSubmitScoreCompletionPoint = cp;
 cp.init();
}

var oygSubmitScoreCompletionPoint;

function oygSubmitScoreJSONComplete(response, seq){
 var cp = oygSubmitScoreCompletionPoint;
 if (cp != null){
  if (response.envelope.cookie == cp.endpoint.cookie){
   if (response.envelope.seq == cp.seq){
    var onComplete = cp.onComplete;
    var onError = cp.onError;
    cp.finit();

    if (response.envelope.success){
     if (onComplete != null){
      onComplete(response.data);
     }
     } else {
     if (onError!= null){
      onError(response.envelope.msg);
     }
    }
   } else {
    cp.endpoint.oob++;
   }
  } else {
   cp.endpoint.badcookie++;
  }
 } else {
  oygEndpoint.noendpoint++;
 }
}
