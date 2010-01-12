function saveTree(el) {
	parameters = {node_orders:treeObj.getNodeOrders(), transfered: TransferedNodes, method: 'get'};
	var url    = window.location.toString();
	ajaxRequest(el, url, parameters, onSaveTree);	
}
function onSaveTree(el, response) {
	TransferedNodes = response;
}

function copyLessonEntity(el, entity) {
	parameters = {entity:entity, method: 'get'};
	var url    = window.location.toString();
	ajaxRequest(el, url, parameters, onCopyLessonEntity);	
} 
function onCopyLessonEntity(el, response) {
	TransferedNodes = response;
}