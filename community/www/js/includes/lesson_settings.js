function activate(el, action) {
	Element.extend(el);
	parameters = {ajax:1, method: 'get'};	
	el.down().className.match(/inactiveImage/) ? parameters = Object.extend(parameters, {activate:action}) : parameters = Object.extend(parameters, {deactivate:action}) ;
	el.down().setAttribute('src', 'themes/default/images/others/progress_big.gif');
	el.down().removeClassName('sprite32');
	var url    = location.toString();
	ajaxRequest(el, url, parameters, onActivate);

}
function onActivate(el, response) {
	el.down().setAttribute('src', 'themes/default/images/others/transparent.png');
	el.down().addClassName('sprite32');
	if (el.down().className.match(/inactiveImage/)) {
		el.down().removeClassName('inactiveImage');
	} else {
		el.down().addClassName('inactiveImage');
	}
	
	if (top.sideframe) {
		top.sideframe.location.reload();
	}
}

function ajaxPost(login, el, table_id) {
	var url = location.toString();
	var parameters = {postAjaxRequest:1, method: 'get'};
    if (login) {
        var userType = $('type_'+login).options[$('type_'+login).selectedIndex].value;
        Object.extend(parameters, {login: login, user_type: userType});
    } else if (table_id && table_id == 'usersTable') {
        el.checked ? Object.extend(parameters, {addAll: 1}) : Object.extend(parameters, {removeAll: 1});
        if ($(table_id+'_currentFilter')) {
        	Object.extend(parameters, {filter: $(table_id+'_currentFilter').innerHTML});
        }
    }
	ajaxRequest(el, url, parameters);	
}

function updatePositions(el, lessonId) {
	var str = '';
    $('firstlist').select('li').each(function (s)  {str += 'visibility['+ s.id.replace(/.*_/, '') + ']=' + (s.select('img')[1].className == 'plus' ? 0 : 1) + '&'});
    $('secondlist').select('li').each(function (s) {str += 'visibility['+ s.id.replace(/.*_/, '') + ']=' + (s.select('img')[1].className == 'plus' ? 0 : 1) + '&'});
    str = str.substring(0, str.length - 1); //Remove trailing &

	parameters = {lessons_ID:lessonId, 
				  set_default:1, 
				  firstlist: Sortable.serialize('firstlist'), 
				  secondlist: Sortable.serialize('secondlist'), 
				  visibility: str, 
				  method: 'post'};	
    var url = 'set_positions.php';    
	ajaxRequest(el, url, parameters);
}
if ($('firstlist')) {
	Sortable.create("firstlist", {
	    containment:["firstlist","secondlist"], constraint:false
	});
	Sortable.create("secondlist", {
	    containment:["firstlist", "secondlist"], constraint:false
	});
}


