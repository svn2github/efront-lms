function saveTree(el) {
	parameters = {node_orders:treeObj.getNodeOrders(), transfered: TransferedNodes, method: 'get'};
	var url    = window.location.toString();
	ajaxRequest(el, url, parameters, onSaveTree);	
}
function onSaveTree(el, response) {
	TransferedNodes = response;
	$('save_button').disabled = false;
}

function copyLessonEntity(el, entity) {
	parameters = {entity:entity, method: 'get'};
	var url    = window.location.toString();
	ajaxRequest(el, url, parameters, onCopyLessonEntity);	
} 
function onCopyLessonEntity(el, response) {
	TransferedNodes = response;
}
if ($('autocomplete_lessons_copy')) { 
	new Ajax.Autocompleter("autocomplete_copy", 
						   "autocomplete_lessons_copy", 
						   "ask.php?ask_type=lessons", {paramName: "preffix", 
											   afterUpdateElement : function (t, li) {document.location=document.location+'&from='+li.id;}, 
											   indicator : "busy_copy"}); 
}