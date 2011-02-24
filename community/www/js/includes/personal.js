function updateBranchJobs(el) {
 var branch = el.options[el.options.selectedIndex].value;
 var parameters = {method:'get', ajax:1, jobs_for_branch:branch};
 ajaxRequest(el, location.toString(), parameters, onUpdateBranchJobs);
}
function onUpdateBranchJobs(el, response) {
 if (response.evalJSON(true).status) {
  $('jobs_for_branch').select('option').each(function (s) {s.remove();});
  $H(response.evalJSON(true).jobs).each(function (s) {$('jobs_for_branch').insert(new Element('option', {value:s[0]}).update(s[1]));});
 }
}
function deleteJob(el, job) {
 var parameters = {method:'get', ajax:1, delete_job:job};
 ajaxRequest(el, location.toString(), parameters, onDeleteJob);
}
function onDeleteJob(el, response) {
 if (response.evalJSON(true).status) {
  new Effect.Fade(el.up().up());
 }
}
function deleteHistory(el, event_id) {
    var url = location.toString();
    parameters = {delete_event:event_id, ajax:1, method: 'get'};
 ajaxRequest(el, url, parameters, onDeleteHistory);
}
function onDeleteHistory(el, transport) {
 new Effect.Fade(el.up().up());
}
function deleteEvaluation(el, evaluation_id) {
    var url = location.toString();
    parameters = {delete_evaluation:evaluation_id, ajax:1, method: 'get'};
 ajaxRequest(el, url, parameters, onDeleteEvaluation);
}
function onDeleteEvaluation(el, transport) {
 new Effect.Fade(el.up().up());
}
function addAccount(el) {
    parameters = {method: 'get', ajax: 'additional_accounts', login:$('account_login').value, pwd:$('account_password').value};
 ajaxRequest(el, location.toString(), parameters, onAddAccountSuccess);

}
function onAddAccountSuccess(el, responseText) {
 if (top.sideframe) {
  top.sideframe.location.reload();
  $('additional_accounts').insert(new Element('tr').insert(new Element('td').update(login)).insert(new Element('td').insert(img)));
  if ($('empty_accounts')) {
      $('empty_accounts').remove();
  }
  $('add_account').hide();
  var login = $('account_login').value;
  $('account_login').value = '';
  $('account_password').value = '';
  el.removeClassName('sprite16-progress1').addClassName('sprite16-check2');

  var img = new Element('img').writeAttribute({src: 'themes/default/images/others/transparent.gif', alt:'', title:'', onclick:'deleteAccount(this, \''+login+'\')'}).addClassName('sprite16 sprite16-error_delete handle');
 } else {
  window.location = window.location.toString().replace(/op=\w+/, "op=mapped_accounts");
 }
}
function deleteAccount(el, login) {
  parameters = {method: 'get', ajax: 'additional_accounts', login:login, 'delete':1};
  var url = location.toString();
 ajaxRequest(el, url, parameters, onDeleteAccountSuccess, onDeleteAccountFailure);
}
function onDeleteAccountFailure(el, responseText) {
 showMessage(responseText, 'failure');
 el.removeClassName('sprite16-progress1').addClassName('sprite16-delete');
}
function onDeleteAccountSuccess(el, responseText) {
 el.hide();
    new Effect.Fade(el.up().up());
 if (top.sideframe) {
  top.sideframe.location.reload();
 }
}
//Wrapper used from the select_all method
function ajaxPost(id, el, table_id) {
    if (table_id == 'skillsTable') {
     ajaxUserSkillPost(id, el, table_id);
    } else if (table_id == 'lessonsTable') {
     ajaxUserLessonPost(id, el, table_id);
    } else if (table_id == 'coursesTable') {
     ajaxUserCoursePost(id, el, table_id);
    } else if (table_id == 'instancesTable') {
     ajaxUserInstancePost(id, el, table_id);
    } else if (table_id == 'groupsTable'){
     ajaxUserGroupPost(id, el, table_id);
    }
}
function ajaxUserSkillPost(id, el, table_id) {
 if ($('spec_skill_score_'+id) && ( isNaN(parseInt($('spec_skill_score_'+id).value)) || $('spec_skill_score_'+id).value > 100 || $('spec_skill_score_'+id).value < 1)) {
  return false;
 } else if (id) {
  parameters = {method:'get',add_skill:id, insert:$('skill_'+id).checked, specification:encodeURI($('spec_skill_'+id).value), score:encodeURI($('spec_skill_score_'+id).value)};
    } else if (table_id && table_id == 'skillsTable') {
     parameters = {method:'get', add_skill:1};
        el.checked ? parameters = Object.extend(parameters, {addAll:1}) : Object.extend(parameters, {removeAll:1});
        if ($(table_id+'_currentFilter')) {
         Object.extend(parameters, {filter:$(table_id+'_currentFilter').innerHTML});
        }
    }
 Object.extend(parameters, {postAjaxRequest:1});

 url=location.toString();
 ajaxRequest(el, url, parameters, onAjaxUserSkillPost);
}
function onAjaxUserSkillPost(el, response) {
 eF_js_redrawPage('skillsTable');
}
function ajaxUserGroupPost(id, el, table_id) {
    if (id) {
  parameters = {method:'get',add_group:id, insert:$('group_'+id).checked};
    } else if (table_id && table_id == ('groupsTable') ) {
     parameters = {method:'get', add_skill:1};
        el.checked ? parameters = Object.extend(parameters, {addAll:1}) : Object.extend(parameters, {removeAll:1});
        if ($(table_id+'_currentFilter')) {
         Object.extend(parameters, {filter:$(table_id+'_currentFilter').innerHTML});
        }
    }
 Object.extend(parameters, {postAjaxRequest:1});

 url=location.toString();
 ajaxRequest(el, url, parameters, onAjaxUserGroupPost);
}
function onAjaxUserGroupPost(el, response) {
 eF_js_redrawPage('groupsTable');
}
function ajaxUserCoursePost(id, el, table_id) {
 var baseUrl = augmentUrl(table_id) + '&postAjaxRequest=1';
    if (id) {
        var url = baseUrl + '&add_course=' + id + '&insert=' + $('course_'+id).checked + '&user_type='+encodeURI($('course_type_'+id).value);
    } else if (table_id && table_id == ('coursesTable')) {
        el.checked ? url = baseUrl + '&addAll=1' : url = baseUrl + '&removeAll=1';
        if ($(table_id+'_currentFilter')) {
            url = url+'&filter='+$(table_id+'_currentFilter').innerHTML;
        }
        url += '&add_course=1';
    }
 ajaxRequest(el, url, {ajax:1}, onAjaxUserCoursePost);
}
function onAjaxUserCoursePost(el, response) {
 eF_js_redrawPage('coursesTable');
}
function ajaxUserInstancePost(id, el, table_id) {
 var baseUrl = augmentUrl(table_id) + '&postAjaxRequest=1';
    if (id) {
        var url = baseUrl + '&add_course=' + id + '&tab=courses&insert=' + $('course_'+id).checked + '&user_type='+encodeURI($('course_type_'+id).value);
    } else if (table_id && table_id == ('instancesTable')) {
        el.checked ? url = baseUrl + '&addAll=1' : url = baseUrl + '&removeAll=1';
        if ($(table_id+'_currentFilter')) {
            url = url+'&filter='+$(table_id+'_currentFilter').innerHTML;
        }
        url += '&add_course=1&tab=courses';
    }
 ajaxRequest(el, url, {ajax:1}, onAjaxUserInstancePost);
}
function onAjaxUserInstancePost(el, response) {
 eF_js_redrawPage('instancesTable');
}
function ajaxUserLessonPost(id, el, table_id) {
 if (id) {
        var url = location.toString() + '&postAjaxRequest=1&add_lesson=' + id + '&insert=' + $('lesson_'+id).checked + '&user_type='+encodeURI($('lesson_type_'+id).value);
    } else if (table_id && table_id == ('lessonsTable') ) {
        el.checked ? url = baseUrl + '&addAll=1' : url = baseUrl + '&removeAll=1';
        if ($(table_id+'_currentFilter')) {
            url = url+'&filter='+$(table_id+'_currentFilter').innerHTML;
        }
        url += '&add_lesson=1';
    }
 ajaxRequest(el, url, {ajax:1}, onAjaxUserLessonPost);
}
function onAjaxUserLessonPost(el, response) {
 eF_js_redrawPage('lessonsTable');
}
function toggleUserAccess(el, id, type) {
 parameters = {ajax:'toggle_user', type: type, id: id, method: 'get'};
 var url = location.toString();

 ajaxRequest(el, url, parameters, onToggleUserAccess);
}
function onToggleUserAccess(el, response) {
 if (response.evalJSON(true).status) {
  if (response.evalJSON(true).access == 1) {
   setImageSrc(el, 16, 'success');
      el.writeAttribute({title:translationsToJS['_USERACCESSGRANTED'], alt:translationsToJS['_USERACCESSGRANTED']});
  } else {
   setImageSrc(el, 16, 'warning');
      el.writeAttribute({title:translationsToJS['_APPLICATIONPENDING'], alt:translationsToJS['_APPLICATIONPENDING']});
  }
 } else {
  alert('Some problem emerged');
 }
}
function showFormAdditionalDetails(el, id) {
 Element.extend(el);
 $('form_tr_'+id).down().toggle();
 $('form_tr_'+id).down().visible() ? img = 'minus2' : img = 'plus2';
 setImageSrc(el, 16, img);
}
function resetFormRows(el) {
 $$('tr.form_additional_info').each(function(s) {$(s.id+'_previous').insert({after:s.remove()});});
 setCookie("setUserFormSelectedSort", el.down().id+'--'+(el.down().hasClassName('sortAscending') ? 'asc' : 'desc'));
}
function showFormAdditionalDetails(el, id) {
 Element.extend(el);
 $('form_tr_'+id).down().toggle();
 $('form_tr_'+id).down().visible() ? img = 'minus2' : img = 'plus2';
 setImageSrc(el, 16, img);
}
//social
var __initStatus;
var __noChangeEscape = 0;
function showStatusChange() {
    __initStatus = $('inputStatusText').value;
    $('statusText').hide();
    $('inputStatusText').show();//style.display = 'block';
    $('inputStatusText').focus();
}

function changeStatus() {
    if (__initStatus != $('inputStatusText').value) {
     var url = location.toString()+"&postAjaxRequest=1&setStatus=" + $('inputStatusText').value;
        $('inputStatusText').hide();

        if ($('inputStatusText').value != '') {
            $('statusText').innerHTML = "\"<i>" + $('inputStatusText').value + "</i>\"";
            if (top.sideframe && top.sideframe.$('statusText')) { // for default theme this is called from sidebar.js
             top.sideframe.$('statusText').innerHTML = $('inputStatusText').value;
             top.sideframe.$('inputStatusText').value = $('inputStatusText').value;
            }
        } else {
            $('statusText').innerHTML = "<i>[" + clickToChangeStatus + "]</i>";
            if (top.sideframe && top.sideframe.$('statusText')) {
             top.sideframe.$('statusText').innerHTML = "[" + clickToChangeStatus + "]";
             top.sideframe.$('inputStatusText').value = "";
          }
        }
        $('statusText').show();

        parameters = {method: 'get'};
        ajaxRequest($('statusTextProgressImg'), url, parameters);

    } else {
        $('inputStatusText').hide();
        $('statusText').show();
    }
    __noChangeEscape = 0;
}
function ExpandCollapseFormRows() {
 setFormRowsHidden = parseInt(readCookie("setFormRowsHidden"));
 if (!setFormRowsHidden) {
  $$('tr.form_additional_info').each(function(s) {s.down().show();setImageSrc($(s.id+'_previous').down().down(), 16, 'minus2');});
  setFormRowsHidden = 1;
 } else {
  $$('tr.form_additional_info').each(function(s) {s.down().hide();setImageSrc($(s.id+'_previous').down().down(), 16, 'plus2');});
  setFormRowsHidden = 0;
 }
 setCookie("setFormRowsHidden", setFormRowsHidden);
}
ExpandCollapseFormRows();ExpandCollapseFormRows(); //2 calls in order to set the expand status to the correct state (because 0 calls does nothing, 1 call reverts it)

readCookieForSortedTablePreset = 'setUserFormSelectedSort';




/*

function restoreSelection(e) {



    Element.extend(e);

    var previous_value = e.selIndex;

    if (e.options[e.selectedIndex].disabled) {

        e.selectedIndex = previous_value;

        return false;

    } else {

       e.selIndex = e.selectedIndex;

       return true;

    }

}



function emulateDisabledOptions(e) {

    Element.extend(e);

    for (var i=0, option; option = e.options[i]; i++) {

        if (option.disabled) {

            option.style.color = "#BBA8AC";

        } else {

            option.style.color = 0;

        }

    }

}





// Function for printing in IE6

// Opens a new popup, set its innerHTML like the content we want to print

// then calls window.print and then closes the popup without the user knowing

function printPartOfPage(elementId)

{

    var printContent = $(elementId);

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



// Auxilliary function to select the option of a selectElement with the specific value

// If the specified value is not found then false is returned

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





// Ajax function to assign jobs through the Placements tab

function ajaxPostDelJob(id, el) {

    Element.extend(el);



    if (!confirm(areYouSureYouWantToCancelConst)) {

        return false;

    }

    var branch = document.getElementById('branches_' +id);



    // Enter only if the branch is defined

    if (branch.value == '0') {

        delete_job_row(id,el);

        return false;

    }



    var job = document.getElementById('job_descriptions_'+id);

    var position = document.getElementById('branch_position_'+id);



    // Check if the job description exists

    var table = document.getElementById('jobsTable');



    var baseUrl =  sessionType + '.php?ctg=users&edit_user=' + editUserLogin + '&postAjaxRequest=1';

    var url = baseUrl + '&add_branch=' + branch.value + '&add_job=' + encodeURI(job.value)  +  '&add_position=' + position.value + '&insert=0';



    parameters = {method: 'get'};

	ajaxRequest(el, url, parameters, function(el, transport) {

						                // Actions to be taken after success: update preSelected job at the main form = the preSelected job is the last one existing when the form was loaded

						                var jobsTable = document.getElementById('jobsTable');

						                var preselectedJob = jobsTable.getAttribute('preSelectedJob');

						

						                delete_job_row(id,el);

						                // Changed the job in the main form: if no job is left then show nothing, else show the job at the last line of the table

						                if (preselectedJob && preselectedJob == id) {

						                    var noOfRows = jobsTable.rows.length;

						                    //IE fix

						                    Element.extend(jobsTable.rows[1]);

						

						                    if (noOfRows > 2 || jobsTable.rows[1].down().id != 'no_jobs_found') {

						                        var newPreselectedRow = noOfRows - 2;

						                        selectOption(document.getElementById('branches_main'), document.getElementById('branches_' +newPreselectedRow).value);

						                        change_branch('branches_main','details_link','jobs_main', document.getElementById('job_descriptions_'+newPreselectedRow).value);

						                        selectOption(document.getElementById('jobs_main'), document.getElementById('job_descriptions_'+newPreselectedRow).value);

						                        selectOption(document.getElementById('placement'), document.getElementById('branch_position_'+newPreselectedRow).value);

						                        jobsTable.setAttribute('preSelectedJob', newPreselectedRow);

						                    } else {

						                        selectOption(document.getElementById('branches_main'), "0");

						                        change_branch('branches_main','details_link','jobs_main');

						                        selectOption(document.getElementById('jobs_main'), "0");

						                        selectOption(document.getElementById('placement'), "0");

						                        jobsTable.setAttribute('preSelectedJob',null);

						                    }

						                }

						

						                // Update all form tables

						                tables = sortedTables.size();

						                var i;

						                for (i = 0; i < tables; i++) {

						                    if (sortedTables[i].id.match('JobsFormTable')) {

						                        eF_js_rebuildTable(i, 0, 'null', 'desc');

						                    } else if (sortedTables[i].id.match('historyFormTable')) {

						                        eF_js_rebuildTable(i, 0, 'timestamp', 'asc');

						                    }

						                }

						  }, 

						  function (el, transport) {

						  	alert(transport);

						  

						  });





}





// Ajax function to assign jobs through the Placements tab

function ajaxPostJob(id, el) {



    Element.extend(el);



    var branch = document.getElementById('branches_' +id);



    var job = document.getElementById('job_descriptions_'+id);



    // Post job only if the branch is defined



    if (branch.value == '0') {



        var defBranch = branch.getAttribute("defaultVal");

        var defJob = job.getAttribute("defaultVal");

        if (defBranch) {

            selectOption(branch, defBranch);

            change_branch('branches_'+id,'branches_details_link_'+id, 'job_descriptions_'+id, defJob);

        }

        return false;

    }





    var position = document.getElementById('branch_position_'+id);



    // Check if the job description exists

    var table = document.getElementById('jobsTable');

    var noOfRows = table.rows.length;

    var i = 1;

    for (i=1; i< noOfRows; i++) {



        if (i != id && document.getElementById('branches_' +i) && document.getElementById('branches_' +i).value == branch.value && document.getElementById('job_descriptions_' +i).value == job.value && document.getElementById('branch_position_' +id).value == position.value) {

            var default_branch = branch.getAttribute("defaultVal");



            if (default_branch) {

                branch.value = default_branch;

                defJob = job.getAttribute("defaultVal");

                change_branch('branches_'+id,'branches_details_link_'+id,'job_descriptions_'+id, defJob);

                position.value = position.getAttribute("defaultVal");

            } else {

                branch.value = 0;

                job.value = 0;

                position.value = 0;

            }

            alert(jobAlreadyAssignedConst);

            return;

        }

    }



    if (!isInfoToolDisabled) {

    	var infoToolTipEl = $('job_analytical_description_'+id);

    	if (infoToolTipEl) {

    		infoToolTipEl.innerHTML = "";

    	}

    }

    var baseUrl =  sessionType + '.php?ctg=users&edit_user=' + editUserLogin + '&postAjaxRequest=1';



    // Post the existing branch, description and position values to delete the previous job placement if one such exists

    var default_branch = branch.getAttribute("defaultVal");

    if (default_branch) {

        var defjob = job.getAttribute("defaultVal");

        var defposition = position.getAttribute("defaultVal");

    } else {

       default_branch = "";

       var defjob = "";

       var defposition = "";

    }



    var url = baseUrl + '&add_branch=' + branch.value + '&add_job=' + encodeURI(job.value)  +  '&add_position=' + position.value + '&default_branch=' + default_branch + '&default_job=' + encodeURI(defjob) +  '&default_position=' + defposition + '&insert=1';



    branch.setAttribute("defaultVal", branch.value) ;

    job.setAttribute("defaultVal", job.value) ;

    position.setAttribute("defaultVal", position.value) ;



    var img_id   = 'img_'+ id;



    // For branches selection appear the img next to the branches lense

    if (el.id == 'branches_'+id) {

        document.getElementById('branches_details_link_'+id).style.display="none";

    }



    parameters = {method: 'get'};

	ajaxRequest(el, url, parameters, function(el, transport) {

						                // Actions to be taken after success: update preSelected job at the main form = the preSelected job is the last one added

						                var jobsTable = document.getElementById('jobsTable');

						                selectOption(document.getElementById('branches_main'), branch.value);

						                change_branch('branches_main','details_link','jobs_main', job.value);

						                selectOption(document.getElementById('jobs_main'), job.value);

						                selectOption(document.getElementById('placement'), position.value);

						                jobsTable.setAttribute('preSelectedJob',id);

						

						                // Update all form tables

						                tables = sortedTables.size();

						                var i;

						                for (i = 0; i < tables; i++) {

						                    if (sortedTables[i].id) {

						                        if (sortedTables[i].id.match('JobsFormTable')) {

						                            eF_js_rebuildTable(i, 0, 'null', 'desc');

						                        } else if (sortedTables[i].id.match('historyFormTable')) {

						                            eF_js_rebuildTable(i, 0, 'timestamp', 'asc');

						                        }

						                    }

						                }

						                if (el.id == 'branches_'+id) {

						                    Effect.Appear("branches_details_link_"+id);

						                }

                });	



	



}







//delete row

function delete_job_row(id, el)

{

    Element.extend(el);

    if ($('noFooterRow1')) {



        $('noFooterRow1').remove();

    }

    var jobsTable = document.getElementById('jobsTable');

    noOfRows = jobsTable.rows.length;



    for (i = 1; i < noOfRows; i++) {

        if (jobsTable.rows[i].id == el.up().up().id) {

            jobsTable.deleteRow(i);

            break;

        }

    }



    // If no job descriptions remain then show the "No jobs assigned" message

    if (jobsTable.rows.length == 2) {

        var x = jobsTable.insertRow(1);

        var newCell = x.insertCell(0);

        var newCellHTML = noPlacementsAssigned;

        newCell.innerHTML= newCellHTML;

        newCell.setAttribute("id", "no_jobs_found");

        newCell.colSpan = 4;

        newCell.className = "emptyCategory";

    }

    return false;

}

	

// Function for inserting the new job row into the edit_user profile

// The row argument denotes how many placements were initially present

// so that only one extra job may be inserted each time

function add_new_job_row(row) {



    // Only if branches exist

    if (!document.getElementById('noBranches')) {

        var table = document.getElementById('jobsTable');



        if (document.getElementById('no_jobs_found')) {

             document.getElementById('jobsTable').deleteRow(1);

        }



        noOfRows = table.rows.length;

        i = noOfRows -1;



        // Remove footer if exists

        if ($('noFooterRow1')) {

            $('noFooterRow1').remove();

        }



        noOfRows = table.rows.length;



        var row = noOfRows;

        var x = table.insertRow(row);

        x.setAttribute("id","row_"+row);



        newCell = x.insertCell(0);

        var newCellHTML = branchesHTML; 



        // Replacing the "row" strings of the HTML code of the select to the correct row. For example the onclick="change(row)" will become onclick="change(2)"

        newCellHTML = newCellHTML.replace('row', row);

        newCellHTML = newCellHTML.replace('row', row);

        newCellHTML = newCellHTML.replace('row', row);

        newCellHTML = newCellHTML.replace('row', row);

        newCellHTML = newCellHTML.replace('row', row);

        newCellHTML = newCellHTML.replace('row', row);



        newCell.innerHTML= "<table><tr><td>"+newCellHTML+"</td><td align='right'><a id='branches_details_link_"+row+"' name='branches_details_link' style='visibility:hidden'><img class='sprite16 sprite16-search handle' src='themes/default/images/others/transparent.gif' title='"+detailsConst+"' alt='"+detailsConst+"' /></a></td></tr></table>";



        newCell = x.insertCell(1);

        if (isInfoToolDisabled) {

        	newCellHTML = '<span id = "job_descriptions_'+row+'_span">' + jobDescriptionsHTML +'</span>';

        } else {

        	newCellHTML = '<table><tr><td><span id = "job_descriptions_'+row+'_span">' + jobDescriptionsHTML +'</span></td><td><a class = "info" url = "ask_information.php?branch_ID='+$("branches_"+row)+'&job_description='+$("job_descriptions_"+row)+'&type=job_description" ><img class="sprite16 sprite16-help" src = "themes/default/images/others/transparent.gif" style="display:none" /><span class = "tooltipSpan" id="job_analytical_description_'+row+'"></span></a></td></tr></table>';

        }

        newCellHTML = newCellHTML.replace('row', row);

        newCellHTML = newCellHTML.replace('row', row);

        newCellHTML = newCellHTML.replace('row', row);



        newCell.innerHTML= newCellHTML;



        newCell = x.insertCell(2);

        var newCellHTML = branchPositionHTML;

        newCellHTML = newCellHTML.replace('row', row);

        newCellHTML = newCellHTML.replace('row', row);



        newCell.innerHTML= newCellHTML;



        //isInfoToolDisabled

        

        newCell = x.insertCell(3);

        newCell.setAttribute("align", "center");

        newCell.innerHTML = "<a id='job_"+row+"' href='javascript:void(0);' onclick='ajaxPostDelJob(\""+row+"\", this);' class = 'deleteLink'><img id='del_img"+row+"' class='sprite16 sprite16-error_delete handle' src = 'themes/default/images/others/transparent.gif' title = '"+row+"' alt = '" + deleteConst + "' /></a></td>";

        document.getElementById('job_' + row).setAttribute('rowCount', row);



    }

}



function updateActivateCheckbox(el) {

	if (el.value != "") {

		$('activeCheckbox').checked = "";

		$('activeCheckbox').disabled = true;

	} else {

		$('activeCheckbox').checked = true;

		$('activeCheckbox').disabled = false;

	}

	

}



// Function which checks if the extension of the file given for the avatar is valid

function testFileExtension()

{

    var file_types = new Array('.jpg', '.jpeg', '.gif', '.png', '.bmp', '.tif', '.tiff', '.ico', '.JPG','.JPEG', '.GIF', '.PNG', '.BMP', '.TIF', '.TIFF', '.ICO');

    var file_name = document.getElementById('avatar').value;

    if (!file_name) {

        return true;

    }



    var dots     = file_name.split(".");

    var file_type = "." + dots[dots.length-1];



    if (file_types.join("").indexOf(file_type) == -1) {

		alert(onlyImageFilesAreValid);

        return false;

    } else {

        return true;

    }

}





// Function which shows and hides the text boxes

function show_hide_spec(i)

{

    var spec = document.getElementById("spec_skill_" + i);

    if (spec.style.visibility == "hidden")

        spec.style.visibility = "visible";

    else

        spec.style.visibility = "hidden";

}







// Function which shows and hides the lense next to the select of a branch

// It also changes the element with id=jobs_select_id into the select with

// the job descriptions of this branch according to a relevant ajax request

function change_branch(element,branch_link,jobs_select_id, defJob)

{

    var fb   = document.getElementById(element).value;

    var link = document.getElementById(branch_link);

    if (fb == 0 || fb == "all") {

        link.style.visibility = "hidden";

        document.getElementById(jobs_select_id).disabled = "disabled";

    } else {

        link.style.visibility = "visible";

        var main_url = location.href.split("?");

        link.href = main_url[0] + "?ctg=module_hcd&op=branches&edit_branch=" + fb;



        // Change the apperance of the job select to match this branch with AjaxRequest

        url = sessionType + ".php?ctg=module_hcd&op=branches&postAjaxRequest=1&getJobSelect=1&edit_branch="+fb+"&jobSelectId="+jobs_select_id+defJob;

        new Ajax.Request(url, {

                method:'get',

                asynchronous:false,

                onSuccess: function (transport) {



                    var select_item = document.getElementById(jobs_select_id);

                    document.getElementById(jobs_select_id).disabled = false;

                    while(select_item.length) {

                        select_item.remove(0);

                    }



                    var temp = transport.responseText.split('<option>');

                    var elOptNew;

                    var i;



                    for (i = 0; i < temp.length-1; i = i + 2) {

                        elOptNew = document.createElement('option');

                        elOptNew.value = temp[i];

                        if (temp[i].match('__emptybranch_name') || temp[i].match('__emptyother_branch')) {

                            elOptNew.disabled = true;

                        } else if (defJob && temp[i] == defJob) {

                            elOptNew.selected = true;

                        }

                        elOptNew.text = temp[i+1];



                        try {

                            select_item.add(elOptNew,null);

                        } catch(ex) {

                            select_item.add(elOptNew); // IE only

                        }



                    }



                    if (!select_item.selectedIndex) {

                        select_item.selIndex = 1;

                        select_item.selectedIndex = 1; //always exists - 'No specific job description' in the branch

                    } else {

                        selectOption(select_item, select_item.getAttribute("defaultVal"));

                    }



                    select_item.setAttribute("defaultVal", select_item.value);

                    if (msieBrowser == 1) {

	                    select_item.onfocus = function(){ this.selIndex = this.selectedIndex; };

	                    emulateDisabledOptions(select_item);

					}

                }

            });

        

    }

    

    return true;   

}



function change_supervisors(element, supervisors_id) {

	

    var fb   = document.getElementById(element).value;

    if (fb == 0 || fb == "all") {

        link.style.visibility = "hidden";

        document.getElementById(supervisors_id).disabled = "disabled";

    } else {	

    	url = sessionType + ".php?ctg=module_hcd&op=branches&postAjaxRequest=1&getSupervisorsSelect=1&edit_branch="+fb;

        new Ajax.Request(url, {

            method:'get',

            asynchronous:false,

            onSuccess: function (transport) {



                var select_item = document.getElementById(supervisors_id);

                document.getElementById(supervisors_id).disabled = false;

                while(select_item.length) {

                    select_item.remove(0);

                }



                var temp = transport.responseText.split('<option>');

                var elOptNew;

                var i;



                for (i = 0; i < temp.length-1; i = i + 2) {

                    elOptNew = document.createElement('option');

                    elOptNew.value = temp[i];

                    elOptNew.text = temp[i+1];



                    try {

                        select_item.add(elOptNew,null);

                    } catch(ex) {

                        select_item.add(elOptNew); // IE only

                    }



                }

            }

        });

        updateActivateCheckbox($('branch_supervisors'));

    }



    return true;

}

















// social

var __initStatus;

var __noChangeEscape = 0;

function showStatusChange() {

    __initStatus = $('inputStatusText').value;

    $('statusText').hide();

    $('inputStatusText').show();//style.display = 'block';

    $('inputStatusText').focus();

}



function changeStatus() {

    if (__initStatus != $('inputStatusText').value) {

        if (sessionType != "administrator") {

        	var url = serverName+sessionType+".php?ctg=personal&postAjaxRequest=1&setStatus=" + $('inputStatusText').value;

        } else {

        	var url = serverName+sessionType+".php?ctg=users&edit_user=" + sessionLogin + "&postAjaxRequest=1&setStatus=" + $('inputStatusText').value;

        }

        $('inputStatusText').hide();



        //$('statusTextProgressImg').show();

        //$('statusTextProgressImg').writeAttribute('src', 'images/others/progress_big.gif').show();



        if ($('inputStatusText').value != '') {

            $('statusText').innerHTML = "\"<i>" + $('inputStatusText').value + "</i>\"";

            if (top.sideframe && top.sideframe.document.getElementById('statusText')) {   // for default theme this is called from sidebar.js

            	top.sideframe.document.getElementById('statusText').innerHTML = $('inputStatusText').value;

            	top.sideframe.document.getElementById('inputStatusText').value = $('inputStatusText').value;

            }

        } else {

            $('statusText').innerHTML = "<i>[" + clickToChangeStatus + "]</i>";

            if (top.sideframe && top.sideframe.document.getElementById('statusText')) {

            	top.sideframe.document.getElementById('statusText').innerHTML = "[" + clickToChangeStatus + "]";

            	top.sideframe.document.getElementById('inputStatusText').value = "";

         	}

        }

        $('statusText').show();

        

        //$('statusTextProgressImg').setAttribute("position", "relative");

        parameters = {method: 'get'};

        ajaxRequest($('statusTextProgressImg'), url, parameters, onChangeAccountSuccess);

        

    } else {

        $('inputStatusText').hide();

        $('statusText').show();//style.display = 'block';

    }

    __noChangeEscape = 0;



}

function onChangeAccountSuccess(el, responseText) {



	//$('statusTextProgressImg').hide().setAttribute('src', 'images/32x32/check.png');

	//new Effect.Appear($('statusTextProgressImg'));

	//window.setTimeout('Effect.Fade("statusTextProgressImg")', 2500);

	//window.setTimeout("$('statusTextProgressImg').removeClassNamewriteAttribute('src', 'images/32x32/edit.png')", 4000);

//window.setTimeout("new Effect.Appear($('statusTextProgressImg'));", 4000);

}





function checkIfEnter(event) {

    //event.keyCode;



    if (event.keyCode == Event.KEY_RETURN) {

        $('inputStatusText').blur();

    } else if (event.keyCode == 27) {           // Escape

        __noChangeEscape = 1;

        $('inputStatusText').value = __initStatus;

        $('inputStatusText').blur();

        $('inputStatusText').style.display="none";

        $('statusText').style.display = 'block';

    }

}







            

            

function deleteFacebookAccount(el, login) {

    Element.extend(el);

    el.src = "images/others/progress1.gif";

    new Ajax.Request(additionalAccountsUrl+'&ajax=additional_accounts&fb_login='+login+'&delete=1', {

        method:'get',

        asynchronous:true,

        onFailure: function (transport) {

            showMessage(transport.responseText, 'failure');

            el.src = "images/16x16/delete.png";

        },

        onSuccess: function (transport) {

            el.hide();

            el.up().remove();

            $('facebook_accounts').insert(new Element('div').writeAttribute({id:'empty_fb_accounts'}).addClassName('emptyCategory').update(youHaventSetAdditionalAccounts));

            if (openFacebookSession) {

            	top.location = "index.php?logout=true";

            }

        }

    });

}                        



function onBeforeSortedTable(table) {

	if (table.id == 'coursesTable') {

		//$('coursesTable').insert({after:$('instancesTable').hide().remove()});

	}

}



if (typeof(jobsRows) != 'undefined') {

jobsAvailable = jobsRows.length;

var j = 0;

for (j = 0; j < jobsAvailable; j++) {

	row = jobsRows[j];

	branch_select = document.getElementById('branches_' + row);

	selectOption(branch_select, branchesValues[j]);

	branch_select.setAttribute("defaultVal", branchesValues[j]);



    job_select = document.getElementById('job_descriptions_' + row);

    selectOption(job_select, jobValues[j]);

    job_select.setAttribute("defaultVal", jobValues[j]);



    branch_position_select = document.getElementById('branch_position_' + row);

    selectOption(branch_position_select, branchPositionValues[j]);

    branch_position_select.setAttribute("defaultVal", branchPositionValues[j]) ;

	change_branch('branches_' + row,'branches_details_link_'+row,'job_descriptions_'+row, document.getElementById('job_descriptions_'+row).value)

}

}

if ($('statusTextProgressImg')) {

	$('statusTextProgressImg').setAttribute("position", "relative");

 }

 

 

if (typeof(enableMyJobSelect) != 'undefined') {            

	var length = document.getElementById('jobs_main').options.length;

	document.getElementById('jobs_main').options[0].disabled = true;

	

	for (i = 1; i < length; i++) {

	    if (document.getElementById('jobs_main').options[i].value == "__emptyother_branch") {

	         document.getElementById('jobs_main').options[i].disabled = "true";

	         break;

	    }

	}

	

	if (msieBrowser) {

		emulateDisabledOptions(document.getElementById('jobs_main'));

	}

} 





*/
