function deleteSchedule(el, id) {
	parameters = {delete_schedule:1, method: 'get'};
	var url    = location.toString();
	ajaxRequest(el, url, parameters, onDeleteSchedule);	
}
function onDeleteSchedule(el, response) {
	el.up().update(noscheduleset).addClassName('emptyCategory');
}