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
// Actions menu
//
function oyCrosswordMenu(puzz){
	this.puzz = puzz;
	
	this.hlist = puzz.hlist;
	this.vlist = puzz.vlist;
	this.footer = puzz.footer;
	
	this.canReveal = puzz.canReveal;
	this.canCheck = puzz.canCheck;		
	
	this.clues = puzz.clues;
	
	this.currentMenu = null;
	this.over = null;
	
	// cell states
	this.cache = new Array();
	for (var i=0; i < this.puzz.h; i++){	
		for (var j=0; j < this.puzz.w; j++){	  
			var key = j + "_" + i; 
			this.cache[key] = -1; 	// -1 - empty, 0 - full, 1 - guessed, 2 - revealed
		}  
	}
	
	// init scores	
	this.checks = 0;
	this.reveals = 0;
	this.deducts = 0;	
	this.matches = 0;
	this.score = 0;
	
	this.rank = -1;
	
	this.xpos = puzz.xpos; 
	this.ypos = puzz.ypos;	
	
	this.name = oyGetCookie("OYG_NICK_NAME"); 
	if (this.name == null || this.name == ""){
		this.name = "Anonymous";
	}
	
	this.server = new oyServer(this.puzz.appHome, this.puzz.ns, this.puzz.canTalkToServer);
	this.scoreSubmittedMatches = 0;	// number of matches for which core was submitted sucesfully
} 

oyCrosswordMenu.prototype.setCellState = function(x, y, value){
	this.cache[x + "_" + y] = value;
}  
 
oyCrosswordMenu.prototype.getCellState = function(x, y){
	return this.cache[x + "_" + y];
}


oyCrosswordMenu.prototype.bind = function(){
	this.inputCache = this.puzz.inputCache;
	 
	this.startOn = new Date();	
}

oyCrosswordMenu.prototype.unbind = function(){
	this.inputCache = null;
}

oyCrosswordMenu.prototype.focusNewCell = function(x, y){
	this.xpos = x; 
	this.ypos = y;
}

oyCrosswordMenu.prototype.invalidateMenu = function(){
	if (this.currentMenu != null){ 
		this.currentMenu();
	}
}

oyCrosswordMenu.prototype.installWelcomeMenu = function(){
	this.currentMenu = this.installWelcomeMenu;

	var target = document.getElementById("oygPuzzleFooter");
	target.innerHTML = "";

	var oThis = this;	
	
	var dispName = escape(this.name);
	dispName = dispName.replace(/%20/g, " ");
	this.addNoneWordAction( 
		target, 
		" <a class='oysTextLink' href='' id='oygWelcomeLink'>" + "" + "</a> "
	);		
	var link = document.getElementById("oygWelcomeLink");
	link.onclick = function(){
		oThis.askNickName();
		oThis.invalidateMenu();
		return false; 
	} 
	 
	this.addNewLine(target);
	
	this.addAction( 
		target, "Start Now", "Starting...", "strt",
		function(){				 
			oThis.puzz.bind();	
			oThis.puzz.hlist.clickItem(0);			
			oThis.installContextMenu();
			
			document.getElementById("oygStatic").innerHTML = "";
			
			oThis.footer.stateOk("Enjoy the game!");
		}
	);	

	this.footer.stateOk("Ready to begin!");
	 
	this.server.trackAction(this.puzz.uid, "wlm");
}

oyCrosswordMenu.prototype.installContextMenu = function(){
	this.currentMenu = this.installContextMenu;
	//var oThis = this;
			//oThis.puzz.bind();	
			//oThis.puzz.hlist.clickItem(0);
	var target = document.getElementById("oygPuzzleFooter");
	target.innerHTML = "";  
	 
	var hidx = this.hlist.getClueIndexForPoint(this.xpos, this.ypos);
	var vidx = this.vlist.getClueIndexForPoint(this.xpos, this.ypos);
	
	// reveals
	if (document.getElementById("crossreveal").value==0){
		this.addNoneWordAction(target, "");
	} else {			
		if (hidx != -1){
			var caption = translationreveal+" <b>" + (hidx + 1) + translationacrossfirstletter+ "</b>"
			if (!this.hlist.clues[hidx].completed()){
				this.addRevealWordAction(
					this.hlist.clues[hidx], target, caption
				);
			} else {
				this.addAction(target, caption, "", null, null);
			}
		}
		if (vidx != -1){
			var caption = translationreveal+" <b>" + (vidx + 1) + translationdownfirstletter+"</b>";
			if (!this.vlist.clues[vidx].completed()){	
				this.addRevealWordAction( 
					this.vlist.clues[vidx], target, caption
				);		
			} else {
				this.addAction(target, caption, "", null, null);
			}	 
		}
	} 
	
	// checks
	if (!this.canCheck){
		this.addNoneWordAction(target, translationcheckdisabled);
	} else {
		var caption = translationcheck + " <b>" + (hidx + 1) + translationacrossfirstletter+ "</b>";
		if (hidx != -1){
			if (!this.hlist.clues[hidx].completed()){
				this.addCheckWordAction(
					this.hlist.clues[hidx], target, caption
				);
			} else {
				this.addAction(target, caption, "", null, null);
			}
		}
		
		var caption = translationcheck+" <b>" + (vidx + 1) + translationdownfirstletter+ "</b>";
		if (vidx != -1){
			if (!this.vlist.clues[vidx].completed()){	 
				this.addCheckWordAction(
					this.vlist.clues[vidx], target, caption
				);		
			} else {
				this.addAction(target, caption, "", null, null);
			}	
		}
		 
		
		var oThis = this;
		this.addAction(target, translationcheckall, translationcheckall, "chkll",
			function(){				
				oThis.checkAll(); 
				oThis.invalidateMenu();		
				return false; 
			}
		);
		
		
		 
		this.addNewLine(target); 
		
		var oThis = this;
		this.addSubmitLeaveMenuItems(target);
	} 
	
	// footer
	this.footer.update(); 
	
	// check game over
	var hasNext = false;	
	for (var i=0; i< this.clues.length; i++){
		if (!this.clues[i].completed()){
			hasNext = true; 
			break;
		} 
	}
	if (!hasNext){
		this.over = true;
	}
		 
	if (this.over){ 
		this.over = true;
		this.puzz.unbind();		
		this.installDoneMenu();
	} 
} 
 
oyCrosswordMenu.prototype.installDoneMenu = function(){	
	this.currentMenu = this.installDoneMenu;

	var target = document.getElementById("oygPuzzleFooter");
	target.innerHTML = "";
	 
	this.addNoneWordAction(target, "Crossword Completed.");	 
	this.addNewLine(target);
	
	var comword = document.getElementById("completewordlength").value;	
	
	var comscore =   Math.round(this.score/comword*100);  
	
	var msg = "You scored " + comscore + " %";
	if (this.rank != -1){
		msg += " (rank <b>" +  this.rank + "</b>)";
	}  
	msg += ".";
	
	this.addNoneWordAction(target, msg);	  
	this.addNewLine(target); 
	
	var oThis = this;
	this.addSubmitLeaveMenuItems(target);
	    
	this.footer.stateOk("Crossword Completed.");
	 
	this.server.trackAction(this.puzz.uid, "ovr");
	
	this.footer.update();
}
 
oyCrosswordMenu.prototype.addSubmitLeaveMenuItems = function(target){
	if (this.puzz.canTalkToServer){
		var caption = "Submit <b>Score</b>";  
		if (this.matches > 0 && this.scoreSubmittedMatches < this.matches){		
			var oThis = this;
			this.addAction(target, caption, "Submitting score...", "sbmt",
				function(){	 	 		 
					oThis.submitScore();
					oThis.invalidateMenu();
					return false; 
				}  
			);
		} else {
			this.addAction(target, caption, "", null, null);
		}
	}
	
	/*var oThis = this;
	this.addAction(target, "Leave <b>Game</b>", "Leaving...", "lv",
		function(){			
			oThis.leaveGameEarly(oThis.puzz.leaveGameURL);
			oThis.footer.stateOk("Done");
			return false; 
		} 
	);*/
	
	var oThis = this;
		this.addAction(target, translationsubmit, "Submit All...", "sub",
			function(){				
				oThis.checkAll(); 
				oThis.invalidateMenu();	
				if(document.getElementById("points").value>0){
				document.crossword_game.submit();	
				}else{
				alert(translationhavenotpoints);
				return false;
				}
				return false; 
			}
		);
}

oyCrosswordMenu.prototype.leaveGameEarly = function(url){
	this.footer.stateBusy("Leaving...");

	var canLeave = true;
	if (this.puzz.started && !this.over){
		canLeave = confirm("Game is in progress. Do you want to leave the game?");
	}	  
	if (canLeave){ 
		window.location = url;
	}
	
	this.footer.stateOk("Done");
}

oyCrosswordMenu.prototype.addAction = function(target, caption, hint, track, lambda){
	caption = caption.replace(" ", "&nbsp;");
	
	var elem = document.createElement("SPAN");
	elem.innerHTML = " &nbsp; ";	
	target.appendChild(elem);	

	var elem = document.createElement("A");
	elem.innerHTML = caption;	
	elem.href = "";				 	
	if (!lambda){
		elem.className = "oyMenuActionDis";
		elem.onclick = function(){
			return false;
		}		
	} else {
		elem.className = "oyMenuAction"; 		
		var oThis = this;
		elem.onclick = function(){
			oThis.footer.stateBusy(hint);
			setTimeout(
				function(){				
					lambda(); 
					oThis.server.trackAction(oThis.puzz.uid, track);
				}, 100
			); 
			return false;
		}		
	}
	
	target.appendChild(elem);	
}

oyCrosswordMenu.prototype.addNewLine = function(target){
	var elem = document.createElement("SPAN");
	elem.innerHTML = "<span style='font-size: 4px;'><br />&nbsp;<br /></span>";
	target.appendChild(elem);	
}

oyCrosswordMenu.prototype.addNoneWordAction = function(target, caption){
	var elem = document.createElement("SPAN");
	elem.className = "oyMenuActionNone";
	elem.innerHTML = caption;	
	target.appendChild(elem);	
	
	var elem = document.createElement("SPAN");
	elem.innerHTML = " ";	
	target.appendChild(elem);		
}

oyCrosswordMenu.prototype.addCheckWordAction = function(clue, target, caption){
	var oThis = this;
	this.addAction(target, caption, translationcheck, "chk",
		function(){				
			oThis.checkWord(clue);						
			oThis.invalidateMenu();		
			return false; 
		}
	);  
}

oyCrosswordMenu.prototype.addRevealWordAction = function(clue, target, caption){
	var oThis = this;
	this.addAction(target, caption, translationreveal, "rvl",
		function(){				
			oThis.revealWord(clue);			
			oThis.invalidateMenu();		
			return false; 
		}
	); 
} 
 
oyCrosswordMenu.prototype.getCurrentValueFor = function(x, y){
	var value = this.inputCache.getElement(x, y).value;
	if (value == " " || value == ""){				
		value = null;
	}
	
	return value;
}

oyCrosswordMenu.prototype.getCellPosListFor = function(clue, left, top){
	var all = new Array();
	  
	for (var i=0; i < clue.len; i++){
		all.push(this.charToPos(clue, i));
	}
	
	return all;
}

oyCrosswordMenu.prototype.charToPos = function(clue, offset){
	var pos = new function (){}
	
	if (clue.dir == 0){	
		pos.x = clue.xpos + offset;
		pos.y = clue.ypos;
	} else {
		pos.x = clue.xpos; 
		pos.y = clue.ypos + offset;
	} 
	
	return pos;
}

oyCrosswordMenu.prototype.showAnswer = function(clue, stateCode){
	for (var i=0; i < clue.len; i++){
		var pos = this.charToPos(clue, i);	
		var input = this.inputCache.getElement(pos.x, pos.y);		
		if (!input.readOnly){			
			input.readOnly = true;			
			input.value = clue.answer.charAt(i).toUpperCase();
			
			this.setCellState(pos.x, pos.y, stateCode); 
 		  	
 		 	var cell = document.getElementById("oyCell" + pos.x + "_" + pos.y);		
 		 	switch(stateCode){
 		 		case 1: 
					cell.className = "oyCellGuessed"; 		 		
 		 			break;
				case 2:
	 		 		cell.className = "oyCellRevealed"; 		 	
 		 			break; 		 			 
	 		 	default: 
	 		 		alert("Bad state code!");		
 		 	} 		 	
		}  
	} 	  
	
	this.puzz.invalidate();
}

oyCrosswordMenu.prototype.checkWordStatus = function(clue){
	var status = new function (){};
	
	status.wrong = 0;
	status.isComplete = true; 
	status.buf = "";
	
	for (var i=0; i < clue.len; i++){			
		var value;
		if (clue.dir == 0){
			value = this.getCurrentValueFor(clue.xpos + i, clue.ypos);
		} else {
			value = this.getCurrentValueFor(clue.xpos, clue.ypos + i);
		}
 
		if (value == null){
			status.isComplete = false;
			status.buf += ".";
		} else {		
			status.buf += value;
		}
		
		if (value != clue.answer.charAt(i).toUpperCase()){
			status.wrong++; 
		}
	} 
    
	return status;
}

oyCrosswordMenu.prototype.askNickName = function(score){
	if (score){
		score = "Score: " + score + ". ";
	} else { 
		score = "";
	}
  
	if (this.name == null){
		this.name = "";
	}

	var oldName = this.name;
	this.name = window.prompt(  
		score + "Enter your NICK NAME or E-MAIL.\n" +  
		"Without e-mail, the score is recorded, but you aren't eligible for the prizes.",
		this.name 
	);
	 
	var result = true; 
	if (this.name == null || this.name == ""){
		this.name = oldName;     
		result = false; 
	} 
	
	if (this.name != null && this.name != ""){  
		oySetCookieForPeriod("OYG_NICK_NAME", this.name, 1000*60*60*24*360, "/");
		return result;
	} else {  
		this.name = "Anonymous";
		return false; 
	}
}

oyCrosswordMenu.prototype.getScoreForMatch = function(clue){
	return clue.len; 
}

oyCrosswordMenu.prototype.getDeductsForReveal = function(clue){
	return clue.len * 2;  
} 

oyCrosswordMenu.prototype.getDeductionForCheck = function(clue){
	var CHECK_FRAQ = 3;
	
	var deduction = (clue.len - clue.len % CHECK_FRAQ) / CHECK_FRAQ;
	if (deduction < 1){
		deduction = 1;
	}
	
	return deduction;
}

oyCrosswordMenu.prototype.revealWord = function(clue){
	this.deducts += this.getDeductsForReveal(clue);	
	this.reveals++; 
	this.showAnswer(clue, 2);	  	 
	
	clue.revealed = true; 	
	clue.matched = false; 	
 
	var status = this.checkWordStatus(clue);	  	
	this.footer.stateOk(translationrevealed+" [" + status.buf + "]!");
}  

oyCrosswordMenu.prototype.checkAll = function(){
	var checked = 0;
	var correct = 0;
	
	if (navigator.appVersion.indexOf("MSIE")>=0)
	{ 
	this.clues.length= this.clues.length-1;
	}
	
	for (var i=0; i < this.clues.length; i++){
		if (this.clues[i].completed()) continue;
		 
		var status = this.checkWordStatus(this.clues[i]);	  
		if (status.isComplete){
			checked++;
			this.checks++; 
			this.deducts += this.getDeductionForCheck(this.clues[i]);			
			if (status.wrong == 0){				 
				this.showAnswer(this.clues[i], 1);	 	
				this.score += this.getScoreForMatch(this.clues[i]);
				
				this.clues[i].matched = true;
				this.clues[i].revealed = false;
				
				correct++; 
				this.matches++;
			}
		} 
	}
		
	if  (checked == 0){
		this.footer.stateError(translationnocompletewordsfound);
	} else {
		this.footer.stateOk(translationchecked+" " + checked + ", " + correct + " "+translationmatched+"!"); 
	}
}  
  
oyCrosswordMenu.prototype.checkWord = function(clue){
	var status = this.checkWordStatus(clue);	  
	if (!status.isComplete){
		this.footer.stateError(translationword+" [" + status.buf + "] "+translationincomplete);
	} else { 
		this.checks++; 
		this.deducts += this.getDeductionForCheck(clue);			
		if (status.wrong != 0){		  
			this.footer.stateError("[" + status.buf + "] "+translationdidnotmatch);
		} else { 
			this.matches++; 
			this.showAnswer(clue, 1);	 	
			this.score += this.getScoreForMatch(clue);
			 
			clue.revealed = false; 	
			clue.matched = true; 	 
			
			this.footer.stateOk("[" + status.buf + "] "+translationmatched);
		}
	}
}

oyCrosswordMenu.prototype.submitScore = function(){
	if (this.matches == 0){   
		this.footer.stateError("Nothing to submit yet!");
		alert("Nothing to submit yet!\nUncover some words first.");
	} else {		  
		var ms = new Date().getTime() - this.puzz.menu.startOn.getTime();
		this.server.submitScore(
			this, this.puzz.uid, 
			this.score, this.deducts, this.checks, this.reveals, this.matches,
			ms, this.name,
			this.puzz.clues
		); 
		this.footer.stateBusy("Submitting score...");
	}
}  