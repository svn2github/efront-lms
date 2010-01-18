function deleteForumEntity(el, id, type) {
	parameters = {'delete':id, type: type, method: 'get'};
	var url    = location.toString();
	ajaxRequest(el, url, parameters, onDeleteForumEntity);	
}
function onDeleteForumEntity(el, response) {
	new Effect.Fade(el.up().up());
}
function deleteForumMessage(el, id) {
	parameters = {'delete':id, type: 'message', method: 'get'};
	var url    = location.toString();
	ajaxRequest(el, url, parameters, onDeleteForumMessage);		
}
function onDeleteForumMessage(el, response) {
	new Effect.Fade(el.up().up().up());
}

function lockForum(el, id) {
	parameters = {lock:id, type: type, method: 'get'};
	var url    = location.toString();
	ajaxRequest(el, url, parameters, onLockForum);		
}
function onLockForum(el, response) {
	if (response == '3') {
		el.removeClassName('sprite16-unlock').addClassName('sprite16-lock');
	} else if (response == '2') {
		el.removeClassName('sprite16-unlock').addClassName('sprite16-lock');
	} else {
		el.removeClassName('sprite16-unlock').addClassName('sprite16-lock');
	}
}

function addAdditionalChoice() {
    var els = document.getElementsByTagName('input');           //Find all 'input' elements in th document.

    var counter = els.length;
    if (counter > 1) {                                                  //If the counter is less than 2 (where 2 is the default input fields), it means that the selected question type is not one that may have multiple inputs (i.e. it may be raw_text)
        var last_node = document.getElementById('last_node');           //This is the node that the new elements will be inserted before
        var tr = document.createElement('tr');                          //Create a table row to hold the new element
        var td = document.createElement('td');                          //Create a new table cell to hold the new element
        tr.appendChild(td);                                             //Append this table cell to the table row we created above

        var input = document.createElement('input');                    //Create the new input element
        input.setAttribute('type', 'text');                             //Set the element to be text box
        input.className = 'inputText inputText_QuestionChoice';         //Set its class to 'inputText'
        input.setAttribute('name', 'options['+counter+']');             //We give the new textboxes names if the form multiple_one[0], multiple_one[1] so we can handle them alltogether
        td.appendChild(input);                                          //Append the text box to the table cell we created above

        var img = new Element('img', {alt: removechoice, title: removechoice, src: 'themes/default/images/others/transparent.png'}).addClassName('sprite16').addClassName('sprite16-error_delete').setStyle({whiteSpace: 'nowrap'}).addClassName('ajaxHandle');						//Create an image element, that will hold the "delete" icon
        img.onclick = function() {removeImgNode(this, "options");};
        td.appendChild(img);                                            //Append the <td> to the row

        var parent_node = last_node.parentNode;                         //Find the parent element, that will hold the new element
        parent_node.insertBefore(tr, last_node);                        //Append the table row, that holds the input element, to its parent.
    }

}

//This function removes the <tr> element that contains the inserted node.
function removeImgNode(el, question_type) {

    var counter = 0;        
    var els = document.getElementsByTagName('input');           //Find all 'input' elements in th document.
    for (var i = 0; i < els.length; i++) {                      //Count all the 'input' elements whose names match the selected question type (e.g. multiple_one[0], multiple_one[1] etc)
		if (els[i].name.match(question_type) && els[i].type.match('text')) {                                            
			counter++;
        }
    }

    if (counter  <= 2  ){
        alert(twooptionsminimum);
    } else {
        el.parentNode.parentNode.parentNode.removeChild(el.parentNode.parentNode);      //It is <tr><td><img></td></tr>, so we need to remove the <tr> element, which is the el.parentNode.parentNode

        for (var i = 0; i < els.length; i++) {                      //Count all the 'input' elements whose names match the selected question type (e.g. multiple_one[0], multiple_one[1] etc)
            if (els[i].name.match(question_type) && els[i].type.match('text')) {
                els[i].name = question_type+'['+counter+']';        //If the user deletes a middle choice, the remaining choices will not be continuous. I.e, if we have choices multiple_one[0-5] and the user removes multiple_one[3], then the array will have indexes 0,1,2,4,5. So, here, we re-calculate the number of choices and apply new array indexes
                counter++;
            }
        }
    }
}