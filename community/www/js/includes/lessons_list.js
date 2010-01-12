
function addGroupKey(el) {
	parameters = {group_key:$('group_key').value, method: 'get'};
	var url    = window.location.toString();
	ajaxRequest(el, url, parameters, onAddGroupKey);	
}
function onAddGroupKey(el, response) {
	if (response == "WK") {
		$('resultReport').innerHTML = wrongGroupLessonKey;
	} else if (response == "NL") {
		$('resultReport').innerHTML = theGroupHasnotLessons;
	} else if (response == "NA") {
		$('resultReport').innerHTML = theGroupisnotActive;
	} else if (response == "KE") {
		$('resultReport').innerHTML = theGroupKeyhasExpired;
	} else if (response == "0") {
		$('resultReport').innerHTML = alreadyAttend;
	} else {
		response = response.split("_");
		newlessons = response[0];
		newcourses = response[1];

		var answer = youHaveAssigned;
		if (newlessons != "0") {
			answer += " " + newlessons + " " + lcNewLessons;
		}
		if (newlessons != "0" && newcourses != "0") {
			answer += " " + and;
		}

		if (newcourses != "0") {
			answer += " " + newcourses + " " + lcNewCourses;
		}

		$('resultReport').innerHTML = answer;
		$('resultReport').setStyle({align:'center'});
		if (!(w = findFrame(top, 'mainframe'))) {
			w = window;
		}
		w.location = w.location;
	}	
}