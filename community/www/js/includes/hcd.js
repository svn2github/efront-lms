if ($('chart_holder')) {
	document.observe("dom:loaded", function() {
		el = document.body;
		parameters = {load_chart:1, method: 'get'};
		ajaxRequest(el, location.toString(), parameters, onLoadChart);
	});
}
function onLoadChart(el, response) {
	var re2         = new RegExp("<!--ajax:chart-->((.*[\n])*)<!--\/ajax:chart-->");	//Does not work with smarty {strip} tags!
	var tableText   = re2.exec(response);
	if (!tableText) {
		var re      = new RegExp("<!--ajax:chart-->((.*[\r\n\u2028\u2029])*)<!--\/ajax:chart-->");	//Does not work with smarty {strip} tags!
		tableText   = re.exec(response);
	}
	$('loading_div').hide();
	$('chart_holder').update(tableText);
}

function toggleOrgChartMode(el) {
	Element.extend(el);
	var orgChartMode = parseInt(readCookie("orgChartMode"));
	orgChartMode ? orgChartMode = 0 : orgChartMode = 1;
	setCookie("orgChartMode", orgChartMode);
	location.reload();
}


function deleteJob(el, job, url) {
	if (!url) {
		var url    = location.toString();
	}
	parameters = {delete_job_description:job, method: 'get'};
	ajaxRequest(el, url, parameters, onDeleteJob);	
}
function onDeleteJob(el, response) {
	new Effect.Fade(el.up().up());
}
function removeJobFromUser(el, user, job) {
	parameters = {remove_user_job:job, user: user, method: 'get'};
	var url    = location.toString();
	ajaxRequest(el, url, parameters, onRemoveJobFromUser);		
}
function onRemoveJobFromUser(el, response) {
	new Effect.Fade(el.up().up());
}
function deleteSkill(el, skill) {
	parameters = {delete_skill:skill, method: 'get'};
	var url    = location.toString();
	ajaxRequest(el, url, parameters, onDeleteSkill);	
}
function onDeleteSkill(el, response) {
	new Effect.Fade(el.up().up());
}
function removeSkillFromUser(el, user, skill) {
	parameters = {remove_user_skill:skill, user: user, method: 'get'};
	var url    = location.toString();
	ajaxRequest(el, url, parameters, onRemoveSkillFromUser);		
}
function onRemoveSkillFromUser(el, response) {
	new Effect.Fade(el.up().up());
}

function deleteBranch(el, id, fatherId) {
	var url    = location.toString();
	parameters = {delete_branch:id, father_ID:fatherId, ajax:'branch', method: 'get'};
	ajaxRequest(el, url, parameters, onDeleteBranch);					
}
function onDeleteBranch(el, response) {
	for (var i = 0; i < sortedTables.size(); i++) {
		if (sortedTables[i].id == 'branchesTable') {
			eF_js_rebuildTable(i, 0, 'null', 'desc');
		}
	}
}

/****************************************************************************
 * Auxilliary functions by Alistair Lattimore to simulate IE options disabled
 * Website:  http://www.lattimore.id.au/
 *****************************************************************************/
function restoreSelection(e) {
	Element.extend(e);
	if (e.options[e.selectedIndex].disabled) {
		e.selectedIndex = e.selIndex;
		return false;
	} else {
		e.selIndex = e.selectedIndex;
		return true;
	}
}

function emulateDisabledOptions(e) {
	Element.extend(e);
	var opSize = e.options.length;
	for (var i=0; i < opSize; i++) {
		if ( e.options[i].disabled) {
			e.options[i].style.color = "#BBA8AC";
		} else {
			e.options[i].style.color = 0;
		}
	}
}

//Auxilliary function to select the option of a selectElement with the specific value
//If the specified value is not found then false is returned
function selectOption(selectElement, value) {
	Element.extend(selectElement);

	var length = selectElement.options.length;
	for (i = 0; i < length; i++) {
		if (selectElement.options[i].value == value) {
			selectElement.options[i].selected = true;
			selectElement.selIndex = i;
			return true;
		}
	}
	return false;
}

//Function for printing in IE6
//Opens a new popup, set its innerHTML like the content we want to print
//then calls window.print and then closes the popup without the user knowing
function printPartOfPage(elementId)
{
	var printContent = document.getElementById(elementId);
	var windowUrl = 'about:blank';
	var uniqueName = new Date();
	var windowName = 'Print' + uniqueName.getTime();
	var printWindow = window.open(windowUrl, windowName, 'left=50000,top=50000,width=0,height=0');
	printWindow.document.write("<link rel = \"stylesheet\" type = \"text/css\" href = \"css/css_global.php\" />");	
	printWindow.document.write(printContent.innerHTML);

	printWindow.document.close();
	printWindow.focus();
	printWindow.print();
	printWindow.close();
}

//REPORTS: Refreshes the results table according to a new url based on the form values of the reports and the other select criteria
function createEmployeeSearchUrl() {
	var newUrl;
	var cut = location.href.split("?");

	// Extended profile criteria
	var customCriteriaFound = false;
	var customCriteria = "";
	if (customProfileSearchCriteria != "") {
		criteria = customProfileSearchCriteria.split(",");
		size = criteria.length;
		for (i = 0; i < size; i++) {
			if (criteria[i] != "") {
				if ($(criteria[i]) && $(criteria[i]).value) {
					customCriteria += "&" + criteria[i] + "=" + $(criteria[i]).value;
					customCriteriaFound = true;
				}
			}
		}
	}

	// Date criteria
	var datesCriteriaFound = false;
	var datesCriteria = "";
	
	if (datesSearchCriteria != "") {
		criteria = datesSearchCriteria.split(",");
		size = criteria.length;
		for (i = 0; i < size; i++) {
			if (criteria[i] != "" && document.getElementById(criteria[i] + "Year").value != "" && document.getElementById('"' + criteria[i] + 'Day"').value != "" && document.getElementById('"' + criteria[i] + 'Month"').value != "") {	
				datesCriteria += "&" + criteria[i] + "=" + document.getElementById('"' + criteria[i] + 'SearchType"').value +
				"&" + criteria[i] + "Day=" + document.getElementById('"' + criteria[i] + 'Day"').value +
				"&" + criteria[i] + "Month=" + document.getElementById('"' + criteria[i] + 'Month"').value +
				"&" + criteria[i] + "Year=" + document.getElementById(criteria[i] + "Year").value;
				datesCriteriaFound = true;		
			}
		}
	}

	
	if ($('search_branch')) {	
		if (document.getElementById('new_login').value   ||document.getElementById('name').value    ||document.getElementById('surname').value ||document.getElementById('email').value   ||document.getElementById('user_type').value|| document.getElementById('father').value    ||document.getElementById('sex').value    ||document.getElementById('birthday').value||document.getElementById('birthplace').value||document.getElementById('birthcountry').value||document.getElementById('mother_tongue').value||document.getElementById('nationality').value  ||document.getElementById('address').value    ||document.getElementById('city').value    ||document.getElementById('country').value ||document.getElementById('homephone').value ||document.getElementById('mobilephone').value||document.getElementById('office').value    ||document.getElementById('company_internal_phone').value    ||document.getElementById('afm').value||document.getElementById('doy').value ||document.getElementById('police_id_number').value    ||document.getElementById('work_permission_data').value||document.getElementById('employement_type').value ||document.getElementById('wage').value    ||document.getElementById('marital_status').value    ||document.getElementById('bank').value    ||document.getElementById('bank_account').value    ||document.getElementById('way_of_working').value  || customCriteriaFound || datesCriteriaFound || document.getElementById('driving_licence').value != "" || document.getElementById('national_service_completed').value != "" || document.getElementById('transport').value != "" || document.getElementById('active2').value != "") {
			newUrl = cut[0] +"?ctg=module_hcd&op=reports&search=1&all=" + document.getElementById('all_criteria').checked + "&branch_ID=" + document.getElementById('search_branch').value +  "&include_sb="+document.getElementById('include_subbranchesId').checked + "&job_description_ID=" + document.getElementById('search_job_description').value + "&skill_ID=" + document.getElementById('search_skill').value + "&login=" + document.getElementById('new_login').value+ "&name=" + document.getElementById('name').value    + "&surname=" + document.getElementById('surname').value+ "&email=" + document.getElementById('email').value    + "&user_type=" + document.getElementById('user_type').value+ "&father=" + document.getElementById('father').value    + "&sex=" + document.getElementById('sex').value+ "&birthday=" + document.getElementById('birthday').value    + "&birthplace=" + document.getElementById('birthplace').value+ "&birthcountry=" + document.getElementById('birthcountry').value    + "&mother_tongue=" + document.getElementById('mother_tongue').value+ "&nationality=" + document.getElementById('nationality').value    + "&address=" + document.getElementById('address').value+ "&city=" + document.getElementById('city').value    + "&country=" + document.getElementById('country').value+ "&homephone=" + document.getElementById('homephone').value   + "&mobilephone=" + document.getElementById('mobilephone').value    + "&office=" + document.getElementById('office').value+ "&company_internal_phone=" + document.getElementById('company_internal_phone').value    + "&afm=" + document.getElementById('afm').value  + "&doy=" + document.getElementById('doy').value    + "&police_id_number=" + document.getElementById('police_id_number').value+ "&work_permission_data=" + document.getElementById('work_permission_data').value+ "&employement_type=" + document.getElementById('employement_type').value+ "&wage=" + document.getElementById('wage').value+ "&marital_status=" + document.getElementById('marital_status').value+ "&bank=" + document.getElementById('bank').value    + "&bank_account=" + document.getElementById('bank_account').value + "&way_of_working=" + document.getElementById('way_of_working').value + "&driving_licence=" + document.getElementById('driving_licence').value + "&national_service_completed=" + document.getElementById('national_service_completed').value + "&transport=" + document.getElementById('transport').value + "&active=" + document.getElementById('active2').value;
		} else {
			newUrl = cut[0] +"?ctg=module_hcd&op=reports&search=1&all=" + document.getElementById('all_criteria').checked + "&branch_ID=" + document.getElementById('search_branch').value +  "&include_sb="+document.getElementById('include_subbranchesId').checked + "&job_description_ID=" + document.getElementById('search_job_description').value + "&skill_ID=" + document.getElementById('search_skill').value;
		}

		var i = 0;
		var other_skills_to_return = "";
	
		while (i++ < __criteria_total_number) {
			if ($('search_skill_'+ i) && $('search_skill_'+ i).value != "0") { 
				if (other_skills_to_return == "") {
					other_skills_to_return = $('search_skill_'+ i).value; 
				} else {
					other_skills_to_return += "_" + $('search_skill_'+ i).value;
				} 
			}
	
		}
	 
		if (other_skills_to_return != "") {
			newUrl += "&other_skills=" + other_skills_to_return;
		}
	} else { 
		
		if (document.getElementById('new_login').value   ||document.getElementById('name').value    ||document.getElementById('surname').value ||document.getElementById('email').value   ||document.getElementById('user_type').value|| customCriteriaFound || datesCriteriaFound || document.getElementById('active2').value != "") {
			newUrl = cut[0] +"?ctg=search_users&search=1&all=" + document.getElementById('all_criteria').checked + "&login=" + document.getElementById('new_login').value+ "&name=" + document.getElementById('name').value    + "&surname=" + document.getElementById('surname').value+ "&email=" + document.getElementById('email').value    + "&user_type=" + document.getElementById('user_type').value + "&active=" + document.getElementById('active2').value;
		} else {
			newUrl = cut[0] +"?ctg=search_users";
		}
		 
	} 
	
	if (customCriteriaFound) {
		newUrl += customCriteria;
	}

	if (datesCriteriaFound) {
		newUrl += datesCriteria;
	}

	return newUrl;
}

function refreshResults()
{
	var newUrl = createEmployeeSearchUrl();
	// Update all form tables
	var tables = sortedTables.size();

	for (i = 0; i < tables; i++) {
		if (sortedTables[i].id == 'foundEmployees') {
			ajaxUrl[i] = newUrl + "&";
			eF_js_rebuildTable(i, 0, 'null', 'desc');
		}
	}
	
	// Refresh statistics
	parameters = {method: 'get'};
	el = $("statsDivCustomGroup"); 

	ajaxRequest(el, newUrl + "&stats=1", parameters, function (el, response) {	// on Success
		var tableId     = "customGroupStats";
		var spanElement = document.createElement('span');
		var re2         = new RegExp("<!--ajax:"+tableId+"-->((.*[\n])*)<!--\/ajax:"+tableId+"-->");	//Does not work with smarty {strip} tags!		
		var tableText   = re2.exec(response);
		if (!tableText) {
			var re      = new RegExp("<!--ajax:"+tableId+"-->((.*[\r\n\u2028\u2029])*)<!--\/ajax:"+tableId+"-->");	//Does not work with smarty {strip} tags!
			tableText   = re.exec(response);
		}
		spanElement.innerHTML += tableText[1];
		$("statsDivCustomGroup").replaceChild(spanElement, $("statsDivCustomGroup").down());
	});

}

//Function used as a wrapper function for refreshing or not results
//in the search employee form, in order to include subbranches:
//If no branch is selected then no refresh of the ajax table is going to take place
function includeSubbranches() {
	if (document.getElementById('search_branch').value != "0") {
		refreshResults();
	}
}

//Function used as a wrapper function for refreshing or not results
//in the search employee form, in order to include subbranches:
//If no branch is selected then no refresh of the ajax table is going to take place
function setAdvancedCriterion(el) {
	refreshResults();
	Element.extend(el);

	var img_id   = 'img_'+ el.id;
	var img_position = eF_js_findPos(el);
	var img      = document.createElement("img");

	img.style.position = 'absolute';
	img.style.top      = Element.positionedOffset(Element.extend(el)).top  + 'px';
	img.style.left     = Element.positionedOffset(Element.extend(el)).left + 6 + Element.getDimensions(Element.extend(el)).width + 'px';

	img.setAttribute("id", img_id);
	img.setAttribute('src', 'themes/default/images/others/transparent.gif');
	Element.extend(img).addClassName('sprite16 sprite16-success');

	el.parentNode.appendChild(img);
	img.style.display = 'none';

	new Effect.Appear(img_id);
	window.setTimeout('Effect.Fade("'+img_id+'")', 2500);
}

//Expands/collapses the branches tree based on a tree attribute called expanded
function expandCollapse(id) {
	var status = document.getElementById(id).collapsed;

	// Status = 0 means that the tree was originally collapsed
	if (status) {
		treeObj.expandAll();
		document.getElementById(id).collapsed = false;
	} else {
		treeObj.collapseAll();
		document.getElementById(id).collapsed = true;
	}


}

//Shows and hides the specification text boxes
function show_hide_spec(i)
{
	var spec = document.getElementById("spec_skill_" + i);
	if (spec.style.visibility == "hidden")
		spec.style.visibility = "visible";
	else
		spec.style.visibility = "hidden";
}

function show_hide_job_selects(i)
{
	var spec_job = document.getElementById("job_selection_row" + i);
	var spec_pos = document.getElementById("position_select_row" + i);
	if (spec_job.style.visibility == "hidden") {
		spec_job.style.visibility = "visible";
		spec_pos.style.visibility = "visible";
	} else {

		spec_job.style.visibility = "hidden";
		spec_pos.style.visibility = "hidden";
	}
}


//Shows and hides the lense next to the select of a branch
function change_branch(element,link, forbidden_link)
{

	var fb   = document.getElementById(element).value;
	var flink = document.getElementById(link);
	if (fb == 0 || fb == "all" || fb == forbidden_link)
		flink.style.visibility = "hidden";
	else {
		flink.style.visibility = "visible";
		var main_url = flink.href.split("?");
		flink.href = main_url[0] + "?ctg=module_hcd&op=branches&edit_branch=" + fb;
	}

	return true;
}

//Shows and hides the lense next to the select of a branch
function change_skill_category(element)
{
//	change_skill_category
	var skill_cat_ID   = document.getElementById(element).value;
	var edit_link = document.getElementById('edit_skill_cat');
	var del_link = document.getElementById('del_skill_cat');
	if (skill_cat_ID == "" || skill_cat_ID == 0) {
		edit_link.style.visibility = "hidden";
		del_link.style.visibility = "hidden";
	} else {
		edit_link.style.visibility = "visible";
		del_link.style.visibility = "visible";
		var main_url = edit_link.href.split("?");
		edit_link.href = main_url[0] + "?ctg=module_hcd&op=skill_cat&popup=1&edit_skill_cat=" + skill_cat_ID;
		del_link.href = main_url[0] + "?ctg=module_hcd&op=skill_cat&del_skill_cat=" + skill_cat_ID;
	}

	return true;
}

function activate(el, user) {
	Element.extend(el);
	if (el.down().src.match('red')) {
		url = sessionType + '.php?ctg=users&activate_user='+user;
		newSource = 'images/16x16/trafficlight_green.png';
		imageText = deactivateConst;
	} else {
		url = sessionType + '.php?ctg=users&deactivate_user='+user;
		newSource = 'images/16x16/trafficlight_red.png';
		imageText = activateConst;
	}

	var img = new Element('img', {id: 'img_'+user, src:'images/others/progress1.gif'}).setStyle({position:'absolute'});
	el.getOffsetParent().insert(img);
	el.down().src = 'images/16x16/trafficlight_yellow.png';
	new Ajax.Request(url, {
		method:'get',
		asynchronous:true,
		onSuccess: function (transport) {
		img.setStyle({display:'none'});
		el.down().src = newSource;
		el.down().title = imageText;
		new Effect.Appear(el.down(), {queue:'end'});

		if (el.down().src.match('green')) {
			// When activated
			$('column_'+user).innerHTML = '<a href = "' + sessionType+ '.php?ctg=personal&user='+user+'&op=profile" class = "editLink">'+user+'</a>';

			var cName = $('row_'+user).className.split(" ");
			$('row_'+user).className = cName[0];
		} else {
			$('column_'+user).innerHTML = user;
			$('row_'+user).className += " deactivatedTableElement";
		}

	}
	});
}



// Wrapper function for any of the 2-3 points where Ajax is used in the module personal
function branchJobsAjaxPost(id, el, table_id) {
	Element.extend(el);

	if (table_id == "lessonsTable") {
		ajaxBranchLessonPost(id, el, table_id);
		return;
	} else if (table_id == "coursesTable") {
		ajaxBranchCoursePost(id, el, table_id);
		return;
	}
	var baseUrl =  sessionType + '.php?ctg=module_hcd&op=branches&edit_branch='+editBranch+'&postAjaxRequest=1';
	if (id) {
		var default_position_n_job = document.getElementById('position_select_' +id).name;

		if (default_position_n_job != "_") {
			var pos = default_position_n_job.split("_");
			var job = pos[0];
			var position = pos[1];
		} else {
			var position = "";
			var job = "";
		}

		var url = baseUrl + '&add_employee=' + document.getElementById('job_selection_'+id).name  + '&add_job=' + encodeURI(document.getElementById('job_selection_'+id).value) +  '&add_position=' + document.getElementById('position_select_' +id).value + '&default_job=' + encodeURI(job) +  '&default_position=' + position + '&insert='+document.getElementById('check_'+id).checked;
		if ((document.getElementById('job_selection_'+id).value).indexOf("__emptybranch_name") !== -1) {
	    	return;
	    }
		
		if (document.getElementById('check_'+id).checked) {

			job = document.getElementById('job_selection_'+id).value ;

			position = document.getElementById('position_select_'+id).value ;

			document.getElementById('position_select_' +id).name = job + "_" + position;
			document.getElementById('none_job_' +id).innerHTML = job;
			document.getElementById('none_position_' +id).innerHTML = position;
		} else {
			document.getElementById('position_select_' +id).name = "_";
			document.getElementById('none_job_' +id).innerHTML = "";
			document.getElementById('none_position_' +id).innerHTML = "";
			document.getElementById('none_check_' +id).innerHTML = "0";

		}
		var img_id   = 'img_'+ id;
	} else if (table_id && table_id == 'branchJobsTable') {
		el.checked ? url = baseUrl + '&addAll=1' : url = baseUrl + '&removeAll=1';
		if ($(table_id+'_currentFilter')) {
			url = url+'&filter='+$(table_id+'_currentFilter').innerHTML;
		}
		var img_id   = 'img_selectAll';
		var massive_operation = 1;

	} else {
		return false;
	}

    
	parameters = {method: 'get'};
	ajaxRequest(el, url, parameters, function (el, transport) {	// on Success
		// Update all form tables
		var tables = sortedTables.size();
		var i;
		for (i = 0; i < tables; i++) {
			if (sortedTables[i].id == 'branchUsersTable') {
				eF_js_rebuildTable(i, 0, 'null', 'desc');
			}

		}


		if (massive_operation) {

			var all_inputs = $('branchJobsTable').getElementsByTagName('input');
			for (i = 0; i<all_inputs.length; i++) {
				// Check according to the naming convention for check boxes
				if (all_inputs[i].id.match("check_row")) {
					show_hide_job_selects(all_inputs[i].id.substr(9));	// strlen('check_row') = 9
				}
			}
		}


	}
	);        
	

}


var _showingAllEmployees = 0; 
function ajaxShowAllSubbranches() {
	if (_showingAllEmployees) {
		prev = 1;
		_showingAllEmployees = 0;
		if ($('andSubbranchesTitle')) {
			$('andSubbranchesTitle').style.visibility = "hidden";
		}
	} else {
		prev = 0;
		_showingAllEmployees = 1;
		if ($('andSubbranchesTitle')) {
			$('andSubbranchesTitle').style.visibility = "visible";
		}
	}

	// Update all form tables
	var tables = sortedTables.size();
	var i;
	for (i = 0; i < tables; i++) {
		if ((sortedTables[i].id == 'branchUsersTable' || sortedTables[i].id == 'usersTable') && ajaxUrl[i]) {
			ajaxUrl[i] = ajaxUrl[i].replace("&showAllEmployees=" + prev, "&showAllEmployees=" +  _showingAllEmployees);
			eF_js_rebuildTable(i, 0, 'null', 'desc');
		}
	}	
	
}


// Wrapper function for any of the 2-3 points where Ajax is used in the module personal
function skillEmployeesAjaxPost(id, el, table_id) {
	table_id == 'skillEmployeesTable' ? ajaxSkillUserPost(id, el, table_id) : usersAjaxPost(id, el, table_id);
}

// type: 1 - inserting/deleting the skill to an employee | 2 - changing the specification
// id: the users_login of the employee to get the skill
// el: the element of the form corresponding to that skill/lesson
// table_id: the id of the ajax-enabled table
function ajaxSkillUserPost(id, el, table_id) {
	Element.extend(el);

	var baseUrl =  sessionType + '.php?ctg=module_hcd&op=skills&edit_skill='+editSkill+'&postAjaxRequest=1';
		if ($('spec_skill_score_'+id) && (isNaN(parseInt($('spec_skill_score_'+id).value)) | $('spec_skill_score_'+id).value > 100 | $('spec_skill_score_'+id).value < 1)) {
			return false;
		} else if (id) {
			var url = baseUrl + '&add_user=' + id + '&insert='+$('skill_to_'+id).checked + '&specification='+$('spec_skill_'+id).value + '&score='+$('spec_skill_score_'+id).value;
			var img_id   = 'img_'+ id;
		} else if (table_id && table_id == 'skillEmployeesTable') {
			el.checked ? url = baseUrl + '&addAll=1' : url = baseUrl + '&removeAll=1';
			if ($(table_id+'_currentFilter')) {
				url = url+'&filter='+$(table_id+'_currentFilter').innerHTML;
			}
			var img_id   = 'img_selectAll';
		}

	parameters = {method: 'get'};
	ajaxRequest(el, url, parameters, function (el, transport) {
		// Update the main form table
		var tables = sortedTables.size();
		var i;
		for (i = 0; i < tables; i++) {
			if (sortedTables[i].id == 'usersSkillsTable') {
				eF_js_rebuildTable(i, 0, 'null', 'desc');
			}
		}
	});

}



function globalAjaxPost(id, el, table_id) {
	Element.extend(el);

	var type;

	if (table_id && table_id == 'skillsTable') {
		type = "skill";
	} else if (table_id && table_id == 'lessonsTable') {
		type = "lesson";
	} else if (table_id && table_id == 'coursesTable') {
		type = "course";
	} else {
		type = el.name;
	}
	
	if (type == "skill" || type == "lesson" || type == "course") {
		if (type == "skill") {
			var baseUrl = sessionType + '.php?ctg=module_hcd&op=job_descriptions&edit_job_description='+editJobDescription+'&postAjaxRequest=1&'+type+'=1&apply_to_all_jd=' + document.getElementById(type + '_changes_apply_to').checked;
		} else {
			var baseUrl =  sessionType + '.php?ctg=module_hcd&op=job_descriptions&edit_job_description='+editJobDescription+'&postAjaxRequest=1&'+type+'=1';	
		}
		if (id) {
			var checked  = $(type+'_'+id).checked;
			var url      = baseUrl + '&add_'+type+'ID=' + id + '&insert='+checked;
			var img_id   = 'img_'+ id;
		} else if (table_id && table_id == 'skillsTable') {
			el.checked ? url = baseUrl + '&addAll=1' : url = baseUrl + '&removeAll=1';
			var img_id   = 'img_selectAll';
		} else if (table_id && table_id == 'lessonsTable') {
			el.checked ? url = baseUrl + '&addAll=1' : url = baseUrl + '&removeAll=1';
			var img_id   = 'img_selectAll';
		} else if (table_id && table_id == 'coursesTable') {
			el.checked ? url = baseUrl + '&addAll=1' : url = baseUrl + '&removeAll=1';
			var img_id   = 'img_selectAll';
		}
		if ($(table_id+'_currentFilter')) {
			url = url+'&filter='+$(table_id+'_currentFilter').innerHTML;
		}



		parameters = {method: 'get'};
		ajaxRequest(el, url, parameters);

		//        var position = eF_js_findPos(el);
		//        var img      = document.createElement("img");
		//
		//        img.style.position = 'absolute';
		//        img.style.top      = Element.positionedOffset(Element.extend(el)).top  + 'px';
		//        img.style.left     = Element.positionedOffset(Element.extend(el)).left + 6 + Element.getDimensions(Element.extend(el)).width + 'px';
		//
		//        img.setAttribute("id", img_id);
		//        img.setAttribute('src', 'images/others/progress1.gif');
		//
		//        el.parentNode.appendChild(img);
		//
		//            new Ajax.Request(url, {
		//                    method:'get',
		//                    asynchronous:true,
		//                    onSuccess: function (transport) {
		//                        img.style.display = 'none';
		//                        img.setAttribute('src', 'images/16x16/success.png');
		//                        new Effect.Appear(img_id);
		//                        window.setTimeout('Effect.Fade("'+img_id+'")', 2500);
		//                        }
		//                });
	} else {
		return false;
	}

}

function applyToAllJobDescriptionsInfo(el, jobDescription) {
	if (el.checked) {
		newValue = "checked";
		alert(futureAssignmentsWill + jobDescription);
	} else {
		newValue = "";
		alert(futureAssignmentsWillNot + jobDescription);
	}
	$('skill_changes_apply_to').checked = newValue;
	//$('lesson_changes_apply_to').checked = newValue;
	//$('course_changes_apply_to').checked = newValue;	
}

function applyToAllJobPositionUsers(el, position_id) {
	Element.extend(el).insert(new Element('img', {src:'themes/default/images/others/progress1.gif'}).addClassName('handle'));

	if (el.id == 'course_changes_apply_to_users') {
		var type = 'course';
	} else if( el.id == 'lesson_changes_apply_to_users') {
		var type = 'lesson';
	}
		
	var url    = location.toString();
	parameters = {applytoallusers:type, method: 'get'};
	ajaxRequest(el, url, parameters, onApplyToAllJobPositionUsers);		
}
function onApplyToAllJobPositionUsers(el, response) {
	el.down().src='themes/default/images/others/transparent.gif';
	setImageSrc(el.down(), 16, 'success');
	new Effect.Fade(el.down());
}

var __criteria_total_number = 0;

//Function for inserting the new job row into the edit_user profile
//The row argument denotes how many placements were initially present
//so that only one extra job may be inserted each time
function add_new_criterium_row(row) {

	var table = document.getElementById('criteriaTable');

	noOfRows = table.rows.length;

	var row = noOfRows;
	var x = table.insertRow(row);

	row = (++__criteria_total_number);
	x.setAttribute("id","row_"+row);
	newCell = x.insertCell(0);
	//    $form -> addElement('select', 'search_skill_template' , null, $skills_list ,'id="search_skill_row" onchange="javascript:refreshResults();"');

	var newCellHTML = searchSkillTemplate;

	// Replacing the "row" strings of the HTML code of the select to the correct row. For example the onclick="change(row)" will become onclick="change(2)"
	newCellHTML = newCellHTML.replace('row', row);
	newCellHTML = newCellHTML.replace('row', row);

	//newCell.innerHTML= '<table><tr><td>'+newCellHTML+'</td></td<td align="right"><a id="courses_details_link_'+row+'" name="courses_details_link" style="visibility:hidden"><img src="images/16x16/search.png" title="'+detailsConst+'" alt="'+detailsConst+'" border="0" /></a></td></tr></table>';
	newCell.innerHTML= newCellHTML;

	newCell = x.insertCell(1);
	newCell.setAttribute("align", "center");

	newCell.innerHTML = '<a id="job_'+row+'" href="javascript:void(0);" onclick="delete_criterium_row(\''+row+'\', this);" class = "deleteLink"><img class="sprite16 sprite16-error_delete handle" src = "themes/default/images/others/transparent.gif" alt = "'+deleteConst+'" title= "'+deleteConst+'"/></a></td>';
	document.getElementById('job_' + row).setAttribute('rowCount', row);


}

//delete row
function delete_criterium_row(id, el)
{
	var criteriaTable = document.getElementById('criteriaTable');

	noOfRows = criteriaTable.rows.length;
	var rowId;
	for (i = 0; i < noOfRows; i++) {
		rowId = "row_"+id;
		if (criteriaTable.rows[i].id == rowId) {
			// el.up.up.id has the form 'row_'*
			//deleteInHidden(rowId);
			criteriaTable.deleteRow(i);
			break;
		}
	}

	refreshResults();
	// If no job descriptions remain then show the "No jobs assigned" message
	/*
    if (criteriaTable.rows.length == 1) {
        var x = criteriaTable.insertRow(1);
        var newCell = x.insertCell(0);
        var newCellHTML = noSearchCriteriaConst;' // @TODO: define noSearchCriteriaConst in module_hcd.tpl
        newCell.innerHTML= newCellHTML;
        newCell.setAttribute("id", "no_criteria_found");
        newCell.colSpan = 5;
        newCell.className = "emptyCategory";
    }
	 */
	return false;
}	


//Used to associate branches to lessons
function onBranchLessonAssignment(el, response) {
	//$('participation' + el.name).innerHTML = parseInt($('participation' + el.name).innerHTML) + parseInt(response); 

//	var tables = sortedTables.size();

//	for (i = 0; i < tables; i++) {
//	if (sortedTables[i].id == 'lessonsTable') {
//	eF_js_rebuildTable(i, 0, 'null', 'desc');
//	}
//	}
}
function ajaxBranchLessonPost(id, el, table_id) {
	var url = location.toString();
	var parameters = {postAjaxRequest:1, method: 'get'};

	if (id) {
		Object.extend(parameters, {add_lesson: id, insert: el.checked});
	} else if (table_id && table_id == 'lessonsTable') {
		el.checked ? Object.extend(parameters, {add_lesson:1, addAll: 1}) : Object.extend(parameters, {add_lesson:1, removeAll: 1});
		if ($(table_id+'_currentFilter')) {
			Object.extend(parameters, {filter: $(table_id+'_currentFilter').innerHTML});
		}
	}

	ajaxRequest(el, url, parameters, onBranchLessonAssignment);
}

function ajaxBranchCoursePost(id, el, table_id) {
	var url = location.toString();
	var parameters = {postAjaxRequest:1, method: 'get'};

	if (id) {
		Object.extend(parameters, {add_course: id, insert: el.checked});
	} else if (table_id && table_id == 'coursesTable') {
		el.checked ? Object.extend(parameters, {add_course:1, addAll: 1}) : Object.extend(parameters, {add_course:1, removeAll: 1});
		if ($(table_id+'_currentFilter')) {
			Object.extend(parameters, {filter: $(table_id+'_currentFilter').innerHTML});
		}
	}

	ajaxRequest(el, url, parameters);//, onBranchCourseAssignment);
}

function onCoursesAssigned(el, response) {
	setImageSrc(el, 16, 'success');	
}

function assignCourseToUsers(el, id) {

	var url = createEmployeeSearchUrl();
	parameters = {postAjaxRequest: '1'};
	url += "&add_course=" + id;
	ajaxRequest(el, url, parameters);
}





//Function for inserting the new job row into the edit_user profile
//The row argument denotes how many placements were initially present
//so that only one extra job may be inserted each time
var __eF_prerequisites_total_number = 0;
function add_job_prerequisite() {

	if (!document.getElementById('noCourses')) {

		if ($('noFooterRow1')) {
			$('noFooterRow1').remove();
		}		
		var table = document.getElementById('prerequisitesTable');
		if (document.getElementById('no_training_found')) {
			document.getElementById(table.deleteRow(1));
		}

		noOfRows = table.rows.length;

		var row = noOfRows;
		var x = table.insertRow(row);
		row = table.rows.length;
		//row = (++__eF_prerequisites_total_number);
		x.setAttribute("id","row_"+row);
		newCell = x.insertCell(0);
		newCell.setAttribute("id","conditions_row_"+row);
		//    $form -> addElement('select', 'search_skill_template' , null, $skills_list ,'id="search_skill_row" onchange="javascript:refreshResults();"');
		var newCellHTML = newTrainingCondition;
		// Replacing the "row" strings of the HTML code of the select to the correct row. For example the onclick="change(row)" will become onclick="change(2)"
		newCellHTML = newCellHTML.replace('row', row);
		newCellHTML = newCellHTML.replace('row', row);
		newCellHTML = newCellHTML.replace('col', 0);

		//newCell.innerHTML= '<table><tr><td>'+newCellHTML+'</td></td<td align="right"><a id="courses_details_link_'+row+'" name="courses_details_link" style="visibility:hidden"><img src="images/16x16/search.png" title="'+detailsConst+'" alt="'+detailsConst+'" border="0" /></a></td></tr></table>';
		Element.extend(newCell).update(newCellHTML);
		newCell = x.insertCell(1);
		Element.extend(newCell).setAttribute("align", "center");
		newCell.update('<a id="training_add_'+row+'" href="javascript:void(0);" onclick="add_prerequisite_alternative(\''+row+'\', this);" class = "deleteLink"><img class="sprite16 sprite16-error_add handle" src = "themes/default/images/others/transparent.gif" alt = "'+addAlternativeTrainingConst+'" title= "'+addAlternativeTrainingConst+'"/></a>' +'&nbsp;<a id="training_'+row+'" href="javascript:void(0);" onclick="delete_job_prerequisite(\''+row+'\', this);" class = "deleteLink"><img class="sprite16 sprite16-error_delete handle" src = "themes/default/images/others/transparent.gif" alt = "'+deleteConst+'" title= "'+deleteConst+'"/></a>');
		//document.getElementById('training_' + row).setAttribute('rowCount', row);
		
		ajaxPostRequiredTraining();
	}

}

//delete row
function delete_job_prerequisite(id, el)
{
	var criteriaTable = document.getElementById('prerequisitesTable');

	if ($('noFooterRow1')) {
		$('noFooterRow1').remove();
	}

	noOfRows = criteriaTable.rows.length;
	var rowId;
	for (i = 0; i < noOfRows; i++) {
		rowId = "row_"+id;
		if (criteriaTable.rows[i].id == rowId) {
			// el.up.up.id has the form 'row_'*
			//deleteInHidden(rowId);
			criteriaTable.deleteRow(i);
			break;
		}
	}

	if (criteriaTable.rows.length == 2) {
		var x = criteriaTable.insertRow(1);
		var newCell = x.insertCell(0);
		var newCellHTML = noTrainingDefinedYet; 
		newCell.update(newCellHTML);
		newCell.setAttribute("id", "no_training_found");
		newCell.colSpan = 5;
		newCell.className = "emptyCategory";
	}
	ajaxPostRequiredTraining();
	return false;
}	

function add_prerequisite_alternative(id, el) {
	var table = document.getElementById('prerequisitesTable');
	var firstRowCondition = $('prerequisites_'+id+'_0');
	var newCellHTML = newTrainingCondition;

	// Replacing the "row" strings of the HTML code of the select to the correct row. For example the onclick="change(row)" will become onclick="change(2)"
	newCellHTML = newCellHTML.replace('row', id);
	newCellHTML = newCellHTML.replace('row', id);

	newCellHTML = newCellHTML.replace('col', firstRowCondition.nextSiblings().length/2+1);
	var orLabelHTML = '<span>&nbsp;'+orConst+'&nbsp;</span>'//	alert(newCellHTML);
	$('conditions_row_'+id).update($('conditions_row_'+id).innerHTML+orLabelHTML+newCellHTML);	
	ajaxPostRequiredTraining();
}

function ajaxPostRequiredTraining() {
	var table = document.getElementById('prerequisitesTable');
	var noOfRows = table.rows.length;
	var newTrainingSet = "";
	for (i = 1; i<=noOfRows; i++) {
		firstRowCondition = $("prerequisites_" + i + "_0");
		if (firstRowCondition) {

			if (newTrainingSet != "") {
				newTrainingSet += ";";
			}
			newTrainingSet += firstRowCondition.value;
			if (firstRowCondition.nextSiblings()) {
				var length = firstRowCondition.nextSiblings().length/2;
				for (j = 1; j <= length; j++) {
					newTrainingSet += "_" + $("prerequisites_" + i + "_" + j).value;
				}
			}
		}
	}

	parameters = {postAjaxRequest: '1',
			apply_to_all: ($('training_changes_apply_to_all').checked == true)?1:0,
					training: newTrainingSet,
					method: 'get'};	
	url = location.toString();

	ajaxRequest($('add_training_img'), url, parameters);		
}

function updateSelectedValue(el) {
	el.options[el.selectedIndex].setAttribute("selected", "selected");
}
function ajaxPost(id, el, table_id) {
	if ($('branchJobsTable')) {
		branchJobsAjaxPost(id, el, table_id);
	} else if ($('skillEmployeesTable')) {
		skillEmployeesAjaxPost(id, el, table_id);
	} else if ($('skillsTable') || (!$('branchJobsTable') &&  ($('lessonsTable') || $('coursesTable')))) {
		globalAjaxPost(id, el, table_id);
	}
}

//Used to update popup controls for inserting found users into a group
function updateNewGroup(el, groupId) {
	if (el.value != 0) { 
		$(groupId).disabled="disabled";
		$(groupId).value ="";
	} else {
		$(groupId).disabled="";
	}
}

function insertFoundUsersIntoGroup(el) {
	var url = createEmployeeSearchUrl();	
	if ($('existing_group_id').value != "0") {
		parameters = {postAjaxRequest: '1'};
		url += "&add_to_existing_group=" + $('existing_group_id').value;
		ajaxRequest(el, url, parameters);
	} else if ($('new_group_id').value != "") {
		parameters = {postAjaxRequest: '1'};		
		url += "&add_to_new_group=" + $('new_group_id').value;
		ajaxRequest(el, url, parameters);
	} else {
		alert(youShouldEitherProvideExistingOrNewGroup);
	}
}
function onNewGroupSubmitRelocate(el, response) {
	stype = location.toString().split("?");
	location.href = stype[0] + "?ctg=user_groups&edit_user_group=" + response;
}

function insertFoundUsersIntoGroupAndGotoIt(el) {
	var url = createEmployeeSearchUrl();	
	if ($('existing_group_id').value != "0") {
		parameters = {postAjaxRequest: '1'};
		url += "&add_to_existing_group=" + $('existing_group_id').value;
		ajaxRequest(el, url, parameters);
		stype = location.toString().split("?");
		location.href = stype[0] + "?ctg=user_groups&edit_user_group=" + $('existing_group_id').value; 
	} else if ($('new_group_id').value != "") {
		parameters = {postAjaxRequest: '1'};		
		url += "&add_to_new_group=" + $('new_group_id').value;
		ajaxRequest(el, url, parameters,onNewGroupSubmitRelocate);
	} else {
		alert(youShouldEitherProvideExistingOrNewGroup);
	}
}

function onDateUpdated(el) {
	if (el.previousSiblings().first() != null) {
		firstDateElement = el.previousSiblings().first().next();
	} else {
		firstDateElement = el;
	}
	while (firstDateElement != null) {
		if (firstDateElement.value == "") {
			return;
		}

		if (firstDateElement.next() != null) {
			firstDateElement = firstDateElement.next();
		} else {
			setAdvancedCriterion(firstDateElement);
			return;
		}
	}

}

function archiveUser(el, user) {
	parameters = {archive_user:user, method: 'get'};	
	ajaxRequest(el, location.toString(), parameters, onArchiveUser);	
}
function onArchiveUser(el, response) {
	new Effect.Fade(el.up().up());
}
function propagateCourse(el, course) {
	var courseCheckbox = $('course_'+course);
	if (courseCheckbox && courseCheckbox.checked) {
		var selected = 1;
	} else {
		var selected = 0;
	}
	parameters = {propagate:course, postAjaxRequest:1, selected: selected, method: 'get'};	
	var url    = location.toString();
	ajaxRequest(el, url, parameters);	
}
