
bTextareaWasTinyfied = false; //this should be global, could be stored in a cookie...
function setTextareaToTinyMCE(sEditorID) {
	var oEditor = document.getElementById(sEditorID);
	if(oEditor && !bTextareaWasTinyfied) {
		tinyMCE.execCommand('mceAddControl', true, sEditorID);
		bTextareaWasTinyfied = true;
	}
	return;
}
function unsetTextareaToTinyMCE(sEditorID) {
	var oEditor = document.getElementById(sEditorID);
	if(oEditor && bTextareaWasTinyfied) {
		tinyMCE.execCommand('mceRemoveControl', true, sEditorID);
		bTextareaWasTinyfied = false;
	}
	return;
}

//We need a "shuffle" function for our arrays. Add to the array prototype such a function
Array.prototype.shuffle = function() {
	for (var i = 0; i < this.length; i++) {
		// Random item in this array.
		var r = parseInt(Math.random() * this.length);
		var obj = this[r];

		// Swap.
		this[r] = this[i];
		this[i] = obj;
	}
}

function eF_js_addAdditionalChoice(question_type) {
	var els = document.getElementsByTagName('input');           //Find all 'input' elements in th document.

	var counter = 0;
	for (var i = 0; i < els.length; i++) {                      //Count all the 'input' elements whose names match the selected question type (e.g. multiple_one[0], multiple_one[1] etc)
		if (els[i].name.match('^'+question_type) && els[i].type.match('text')) {
			counter++;
		}
	}

	if (counter > 1) {                                                  //If the counter is less than 2 (where 2 is the default input fields), it means that the selected question type is not one that may have multiple inputs (i.e. it may be raw_text)
		var last_node = document.getElementById(question_type+'_last_node');   //This is the node that the new elements will be inserted before

		var tr = document.createElement('tr');                          //Create a table row to hold the new element

		var td = document.createElement('td');                          //Create a new table cell to hold the new element
		tr.appendChild(td);                                             //Append this table cell to the table row we created above

		var input = document.createElement('input');                    //Create the new input element
		input.setAttribute('type', 'text');                             //Set the element to be text box
		input.className = 'inputText inputText_QuestionChoice';         //Set its class to 'inputText'
		input.setAttribute('name', question_type + '['+counter+']');    //We give the new textboxes names if the form multiple_one[0], multiple_one[1] so we can handle them alltogether
		td.appendChild(input);                                          //Append the text box to the table cell we created above                                        

		if (question_type == 'multiple_one') {
			var option = document.createElement('option');              //Create a new option for the correct answer list
			option.setAttribute('value', counter);                      //This option is the next inserted
			option.innerHTML = counter + 1;
			document.getElementById('correct_multiple_one').appendChild(option); //The select box holding the right answer for multiple_one: adjust its size
		} else if (question_type == 'multiple_many') {
			var td_check = document.createElement('td');                //Create a new table cell to hold the new checkbox
			tr.appendChild(td_check);

			var check = document.createElement('input');
			check.setAttribute('type', 'checkbox');
			//check.setAttribute('class', 'inputCheckbox');
			check.className = 'inputCheckbox';
			check.setAttribute('name', 'correct_multiple_many['+counter+']');
			check.setAttribute('value', '1');
			td_check.appendChild(check);
		} else if (question_type == 'match') {
			var td_middle = document.createElement('td');               //Create a new table cell to hold the new raquos
			td_middle.innerHTML = '&nbsp;&raquo;&raquo;&nbsp;';
			var td_right  = document.createElement('td');               //Create a new table cell to hold the new text box
			tr.appendChild(td_middle);
			tr.appendChild(td_right);

			var check = document.createElement('input');
			check.setAttribute('type', 'text');
			check.setAttribute('class', 'inputText inputText_QuestionChoice');
			check.setAttribute('name', 'correct_match['+counter+']');
			td_right.appendChild(check);
		} else if (question_type == 'drag_drop') {
			var td_middle = document.createElement('td');               //Create a new table cell to hold the new raquos
			td_middle.innerHTML = '&nbsp;&raquo;&raquo;&nbsp;';
			var td_right  = document.createElement('td');               //Create a new table cell to hold the new text box
			tr.appendChild(td_middle);
			tr.appendChild(td_right);

			var check = document.createElement('input');
			check.setAttribute('type', 'text');
			check.setAttribute('class', 'inputText inputText_QuestionChoice');
			check.setAttribute('name', 'correct_drag_drop['+counter+']');
			td_right.appendChild(check);
		}

		var img = document.createElement('img');                        //Create an image element, that will hold the "delete" icon
		img.setAttribute('alt', removechoice);                       //Set alt and title for this image
		img.setAttribute('title', removechoice);
		img.setAttribute('src', 'themes/default/images/others/transparent.png');      //Set the icon source
		img.addClassName('sprite16').addClassName('sprite16-error_delete');
		img.setAttribute('onclick', 'eF_js_removeImgNode(this, "'+question_type+'")');  //Set the event that will trigger the deletion
		img.onclick = function () {eF_js_removeImgNode(this, question_type);};  //Set the event that will trigger the deletion
		var img_td = document.createElement('td');                      //Create a new table cell to hold the image element
		img_td.appendChild(img);                                        //Append the image to this cell
		tr.appendChild(img_td);                                         //Append the <td> to the row
		//Element.extend(td).insert(new Element('input', {type:'text'}));
		var img = new Element('img', {src:'themes/default/images/others/transparent.png', alt:insertexplanation, title:insertexplanation}).addClassName('sprite16').addClassName('sprite16-add').setStyle({marginRight:'5px', verticalAlign:'middle'}).observe('click', function (e) {Element.extend(this).next().toggle();});
		td = new Element('td').setStyle({paddingLeft:'30px'}).insert(img).insert(new Element('input', {type:'text', name:'answers_explanation['+counter+']'}).addClassName('inputText').hide());
		Element.extend(tr).insert(td);

		var parent_node = last_node.parentNode;                         //Find the parent element, that will hold the new element
		parent_node.insertBefore(tr, last_node);                        //Append the table row, that holds the input element, to its parent.
	}

}

//This function removes the <tr> element that contains the inserted node.
function eF_js_removeImgNode(el, question_type) {
	el.parentNode.parentNode.parentNode.removeChild(el.parentNode.parentNode);      //It is <tr><td><img></td></tr>, so we need to remove the <tr> element, which is the el.parentNode.parentNode

	var els = document.getElementsByTagName('input');           //Find all 'input' elements in th document.

	var counter = 0;
	for (var i = 0; i < els.length; i++) {                      //Count all the 'input' elements whose names match the selected question type (e.g. multiple_one[0], multiple_one[1] etc)
		if (els[i].name.match('^'+question_type) && els[i].type.match('text')) {
			els[i].name = question_type+'['+counter+']';        //If the user deletes a middle choice, the remaining choices will not be continuous. I.e, if we have choices multiple_one[0-5] and the user removes multiple_one[3], then the array will have indexes 0,1,2,4,5. So, here, we re-calculate the number of choices and apply new array indexes
			counter++;
		}
	}
    var counter = 0;
    for (var i = 0; i < els.length; i++) {                      //Count all the 'input' elements whose are explanations (e.g. answers_explanation[0], answers_explanation[1] etc)
        if (els[i].name.match('^answers_explanation') && els[i].type.match('text')) {
            els[i].name = 'answers_explanation['+counter+']';        //If the user deletes a middle choice, the remaining choices will not be continuous. I.e, if we have choices multiple_one[0-5] and the user removes multiple_one[3], then the array will have indexes 0,1,2,4,5. So, here, we re-calculate the number of choices and apply new array indexes
            counter++;
        }
    }

	if (question_type == 'multiple_one') {                      //Adjust the select box accordingly
		document.getElementById('correct_multiple_one').removeChild(document.getElementById('correct_multiple_one').lastChild);
	} else if (question_type == 'multiple_many' || question_type == 'match' || question_type == 'drag_drop') {               //For multiple/many (and match) questions, we need to reindex checkboxes (or answer text boxes) as well
		var counter = 0;
		for (var i = 0; i < els.length; i++) {
			if (els[i].name.match('correct_'+question_type)) {
				els[i].name = 'correct_'+question_type+'['+counter+']';
				counter++;
			}
		}
    	var counter = 0;
        for (var i = 0; i < els.length; i++) {
            if (els[i].name.match('answers_explanation')) {
                els[i].name = 'answers_explanation['+counter+']';
                counter++;
            }
        }
	} else {
		throw 'Unsupported question type';
    }
}

//This function is used to create the text boxes that correspond to empty spaces.
function eF_js_createEmptySpaces() {

	if (tinyMCE) {                                                              //Get the text from the editor
		var question_text = tinyMCE.get('editor_content_data').getContent();
	} else {
		var question_text = document.getElementById('editor_content_data').value;     //If the editor isn't set, get the question text from the text area
	}

	var excerpts = question_text.split(/###/g);                         //Get the question text pieces that are split by ###
	var last_node = document.getElementById('empty_spaces_last_node');  //This is the node that the new elements will be inserted before
	var parent_node = last_node.parentNode;                             //Find the parent element, that will hold the new element
	if (document.getElementById('spacesRow')) {                         //If the button was pressed again, remove old row and build a new one
		document.getElementById('spacesRow').parentNode.removeChild(document.getElementById('spacesRow'));
	}

	var tr = document.createElement('tr');              //Create a table row to hold the new element
	tr.setAttribute('id', 'spacesRow');                 //We need an id to know which row this is, so we can remove it on demand
	tr.appendChild(document.createElement('td'));       //Create a new empty table cell for alignment reasons. Append this table cell to the table row we created above

	var td = document.createElement('td');              //Create a new table cell to hold the new element
	td.setAttribute('colspan', '100%');
	tr.appendChild(td);                                 //Append this table cell to the table row we created above

	code = '';
	for (var i = 0; i < excerpts.length; i++) {         //For each designated empty space, create a span element that will hold the text and the text boxes
		code += excerpts[i];
		if (i != excerpts.length - 1) {                                  //If, for example, we have 3 ###, these split the string to 4 parts. So, we must not insert a text box for the last (trailing) string piece
			code += '<input type="text" name = "empty_spaces['+i+']" class = "inputText">';
		}
	}						
	td.innerHTML = code;
	parent_node.insertBefore(tr, last_node);             //Append the table row, that holds the input element, to its parent.
}

function onSortedTableComplete() {
	var heightValue;
	if (sortedTables[tableIndex].getDimensions().height != 0) {
		heightValue = parseInt(sortedTables[tableIndex].getDimensions().height+50);
	} else {
		heightValue = 0;
	}
	if (sortedTables[tableIndex].id == 'filesTable') {
		$('filemanager_cell').setStyle({width:sortedTables[tableIndex].getDimensions().width+'px', height:heightValue+'px', verticalAlign:'top'});
	}                                        	    	
}

function ajaxPost(id, el, table_id) {
	var url = location.toString();
	var parameters = {postAjaxRequest:1, method: 'get'};

	//Element.extend(el);
    //var baseUrl =  '{/literal}{$smarty.server.PHP_SELF}{literal}?ctg=tests&edit_question={/literal}{$smarty.get.edit_question}{literal}&postAjaxRequest=1';

    if (id) {
        var relevance = $('skill_relevance_'+id).options[$('skill_relevance_'+id).selectedIndex].value;
        $('span_skill_relevance_'+id).innerHTML = relevance;
        if ($('skill_'+id).checked) {
        	$('span_skill_checked_'+id).innerHTML = 1;
            if (el.id == 'skill_relevance_'+id) {
            	Object.extend(parameters, {skill: id, insert: 'update', relevance: relevance});
                //var url = baseUrl + '&skill='+id+'&insert=update&relevance='+relevance;
            } else {
            	Object.extend(parameters, {skill: id, insert: 'true', relevance: relevance});
                //var url = baseUrl + '&skill='+id+'&insert=true&relevance='+relevance;
            }
        } else {
            if (el.id == 'skill_relevance_'+id) {
            	$('span_skill_checked_'+id).innerHTML = 1;
            	Object.extend(parameters, {skill: id, insert: 'true', relevance: relevance});
                //var url = baseUrl + '&skill='+id+'&insert=true&relevance='+relevance;
            } else {
            	$('span_skill_checked_'+id).innerHTML = 0;
            	Object.extend(parameters, {skill: id, insert: 'false', relevance: relevance});
                //var url = baseUrl + '&skill='+id+'&insert=false&relevance='+relevance;
            }
        }
       // var url      = baseUrl + '&skill='+id+'&insert='+$('skill_'+id).checked +'&relevance='+relevance;
    } else if (table_id && table_id == 'questionSkillTable') {
		el.checked ? Object.extend(parameters, {skill: 1, addAll: 1}) : Object.extend(parameters, {skill: 1, removeAll: 1});
        //el.checked ? url = baseUrl + '&skill=1&addAll=1' : url = baseUrl + '&skill=1&removeAll=1';
		if ($(table_id+'_currentFilter')) {
			Object.extend(parameters, {filter: $(table_id+'_currentFilter').innerHTML});
        	//url = url+'&filter='+$(table_id+'_currentFilter').innerHTML;
        }    
    }
    
    ajaxRequest(el, url, parameters);
}

function checkSuggestedSkills(el) {
	var url = location.toString();
	var parameters = {postAjaxRequest:1, get_proposed_skills :1, method: 'get'};
	ajaxRequest(el, url, parameters, onCheckSuggestedSkills, onNoSkillsProposed);
} 
function onCheckSuggestedSkills(el, response) {
	
	newSkills = response.split(" ");
	at_least_one = 0;
	
	for (var i = 0 ; i < newSkills.length ; i++) {
		if(!$('skill_' + newSkills[i]).checked) {
			$('skill_' + newSkills[i]).checked = "checked";
			ajaxPost(newSkills[i], $('skill_' + newSkills[i]), 'questionSkillTable');
			at_least_one = 1;
		}
	}
	
	$('suggestedSkillsImage').down().writeAttribute('src', 'themes/default/images/others/transparent.png').addClassName('sprite16').addClassName('sprite16-examples').show();
	if (!at_least_one) {
        alert(correlated_message);
	}
}
function onNoSkillsProposed(el, response) {
	alert(noSkillsFoundOrNoSkillsCorrelated);
}