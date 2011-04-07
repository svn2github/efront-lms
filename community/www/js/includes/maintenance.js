function reIndex(el) {
	var parameters = {ajax:1, reindex:1, method:'get'};
    var url    = location.toString();
    ajaxRequest(el, url, parameters);
}
function compressTests(el) {
	var parameters = {ajax:1, compress_tests:1, method:'get'};
    ajaxRequest(el, location.toString(), parameters);
}
function uncompressTests(el) {	
	var parameters = {ajax:1, uncompress_tests:1, method:'get'};
    ajaxRequest(el, location.toString(), parameters);
}
function setPermissions(el, set) {
	$('failed_permissions').update('');
	var parameters = {ajax:1, permissions:set, method:'get'};
    var url    = location.toString();
    ajaxRequest(el, url, parameters, onSetPermissions);
}
function onSetPermissions(el, response) {
	$('failed_permissions').update(response.evalJSON(true).message);
}


function clearCache(el, cache) {
	var parameters = {ajax:1, cache:cache, method:'get'};
    var url    = location.toString();
    ajaxRequest(el, url, parameters);
}

function ajaxPost(login, el, table_id) {
	var url = location.toString();
	var parameters = {postAjaxRequest:1, method: 'get',autologin: 1};

    if (login) {
        Object.extend(parameters, {login: login});
		ajaxRequest(el, url, parameters, showlink, undoCheck);
    } else if (table_id && table_id == 'usersTable') {
        el.checked ? Object.extend(parameters, {addAll: 1}) : Object.extend(parameters, {removeAll: 1});
        if ($(table_id+'_currentFilter')) {
        	Object.extend(parameters, {filter: $(table_id+'_currentFilter').innerHTML});
        }
		ajaxRequest(el, url, parameters, showalllinks, undoCheck);
	}
		
}

function undoCheck(el, response) {
	el.checked ? el.checked = false : el.checked = true;
	alert(decodeURIComponent(response));
}

function showlink(el, response) {
	var login = el.id.substring(8,el.id.length);
	$('link_'+login).innerHTML = response;
}

function showalllinks(el, response) {
//alert('mpika');
	tables = sortedTables.size();
	for (var i = 0; i < tables; i++) {
		if (sortedTables[i].id.match('usersTable') && ajaxUrl[i]) {
	
			eF_js_rebuildTable(i, 0, 'null', 'desc');
		}
	}
}