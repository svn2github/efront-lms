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
// This a typical clue
//
function oyCrosswordClue(
 len, // length of the word in symnbols, i.e. for the word "Abstract" this will be 8
 clue, // the text of the word clue given to the user, i.e. for the word "Abstract" this will be "This factory creates an instance of several families of classes"
 answer, // thw word itself, i.e. "Abstract"; maybe be ommited, thus disabling the "Reveal" function
 sign, // MD5 signature of the word itself with puzzle uid, i.e. for the word "Abstract" and uid "5748185539682739085" this will be "26f265b96e01081a5ef26a432eda9cff"
 dir, // word direction; 0 for horizontal and 1 for vertical
 xpos, // zero-based coordinate of the word on X axis, zero on the left, i.e. for the word "Abstract" this will be 12
 ypos // zero-based coordinate of the word on Y axis, zero at t he top, i.e. for the word "Abstract" this will be 6
){
 this.len = len;
 this.clue = clue;
 this.answer = answer;
 this.sign = sign;
 this.dir = dir;
 this.xpos = xpos;
 this.ypos = ypos;
 this.revealed = false;
 this.matched = false;
}
oyCrosswordClue.prototype.completed = function(){
 return this.matched || this.revealed;
}
//
// This a list of clues (across/down)
//
function oyClueList(puzz, name, clues, ns){
 this.puzz = puzz;
 this.name = name;
 this.clues = clues;
 this.ns = ns;
 this.selIdx = -1;
}
oyClueList.prototype.render = function(){
 var buf = this.name;
 buf += "<table class='oyList' border='0' cellspacing='0' cellpadding='0'>";
 for (var i=0; i < this.clues.length; i++){
  if (i != 0){
   buf += "<tr class='oyListSpacer'><td></td></tr>";
  }
  buf += "<tr><td class='oyListNormal' id='" + this.ns + i + "'><b>" + (i + 1) + ".</b> " + this.clues[i].clue + "</td></tr>";
 }
 buf += "</table>";
 return buf;
}
oyClueList.prototype.bind = function(){
 for (var i=0; i < this.clues.length; i++){
  var elem = document.getElementById(this.ns + i);
  this.bindItem(elem, i);
 }
}
oyClueList.prototype.unbind = function(){
 for (var i=0; i < this.clues.length; i++){
  var elem = document.getElementById(this.ns + i);
  elem.onclick = null;
 }
}
oyClueList.prototype.bindItem = function(elem, idx){
 var oThis = this;
 elem.onclick = function(){
  oThis.clickItem(idx);
 };
}
oyClueList.prototype.clickItem = function(idx){
 this.selectItem(idx);
 this.puzz.unfocusOldCell();
 this.puzz.focusNewCell(this.clues[idx].xpos, this.clues[idx].ypos, true, this.clues[idx]);
}
oyClueList.prototype.selectItem = function(idx){
 if (this.selIdx != - 1){
  document.getElementById(this.ns + this.selIdx).className = "oyListNormal";
 }
 if (idx != -1){
  document.getElementById(this.ns + idx).className = "oyListSel";
 }
 this.selIdx = idx;
}
oyClueList.prototype.getClueIndexForPoint = function(x, y){
 for (var i=0; i < this.clues.length; i++){
  if (this.clues[i].dir == 0){
   if (y == this.clues[i].ypos){
    if (x >= this.clues[i].xpos && x < this.clues[i].xpos + this.clues[i].len){
     return i;
    }
   }
  } else {
   if (x == this.clues[i].xpos){
    if (y >= this.clues[i].ypos && y < this.clues[i].ypos + this.clues[i].len){
     return i;
    }
   }
  }
 }
 return -1;
}
