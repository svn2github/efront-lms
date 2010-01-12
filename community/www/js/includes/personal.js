/****************************************************************************
* Auxilliary functions by Alistair Lattimore to simulate IE options disabled
* Website:  http://www.lattimore.id.au/
*****************************************************************************/
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

/*
    var img_id   = 'img_'+ id;

    var img_position = eF_js_findPos(el);

//    var img      = document.createElement("img");

    var img      = document.getElementById("del_img" + id);
//    img.style.position = 'absolute';
//    img.style.top      = Element.positionedOffset(Element.extend(el)).top  + 'px';
//    img.style.left     = Element.positionedOffset(Element.extend(el)).left + 6 + Element.getDimensions(Element.extend(el)).width + 'px';

//    img.setAttribute("id", img_id);
    img.setAttribute('src', 'images/others/progress1.gif');

//    el.parentNode.appendChild(img);

    new Ajax.Request(url, {
            method:'get',
            asynchronous:true,
            onSuccess: function (transport) {
                img.style.display = 'none';
                img.setAttribute('src', 'images/16x16/check.png');
                //new Effect.Appear(img_id);
                //window.setTimeout('Effect.Fade("'+img_id+'")', 2500);

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



            }
        });
*/
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
				/*
						                img.style.display = 'none';
						                img.setAttribute('src', 'images/16x16/check.png');
						                new Effect.Appear(img_id);
						                window.setTimeout('Effect.Fade("'+img_id+'")', 2500);
						                if (el.id == 'branches_'+id) {
						                    window.setTimeout('Effect.Appear("branches_details_link_'+id+'")', 2500);
						                }	
						                */
						                if (el.id == 'branches_'+id) {
						                    Effect.Appear("branches_details_link_"+id);
						                }
                });	


/*
    var img_position = eF_js_findPos(el);
    var img      = document.createElement("img");

    img.style.position = 'absolute';
    img.style.top      = Element.positionedOffset(Element.extend(el)).top  + 'px';
    img.style.left     = Element.positionedOffset(Element.extend(el)).left + 6 + Element.getDimensions(Element.extend(el)).width + 'px';

    img.setAttribute("id", img_id);
    img.setAttribute('src', 'images/others/progress1.gif');

    el.parentNode.appendChild(img);
    new Ajax.Request(url, {
            method:'get',
            asynchronous:true,
            onSuccess: function (transport) {
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
                img.style.display = 'none';
                img.setAttribute('src', 'images/16x16/check.png');
                new Effect.Appear(img_id);
                window.setTimeout('Effect.Fade("'+img_id+'")', 2500);
                if (el.id == 'branches_'+id) {
                    window.setTimeout('Effect.Appear("branches_details_link_'+id+'")', 2500);
                }
            }
        });
*/
}



//delete row
function delete_job_row(id, el)
{
    Element.extend(el);
    if ($('noFooterRow1')) {

        $('noFooterRow1').remove();
    }
    var jobsTable = document.getElementById('jobsTable');
/*
    noOfRows = jobsTable.rows.length;

    if (jobsTable.rows[(noOfRows-1)] && jobsTable.rows[(noOfRows-1)].down() && jobsTable.rows[(noOfRows-1)].down().getAttribute('class') == "sortedTableFooter") {
        jobsTable.deleteRow((noOfRows-1));
    }
*/
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

        newCell.innerHTML= "<table><tr><td>"+newCellHTML+"</td><td align='right'><a id='branches_details_link_"+row+"' name='branches_details_link' style='visibility:hidden'><img class='sprite16 sprite16-search handle' src='themes/default/images/others/transparent.png' title='"+detailsConst+"' alt='"+detailsConst+"' /></a></td></tr></table>";

        newCell = x.insertCell(1);
        newCellHTML = '<span id = "job_descriptions_'+row+'_span">' + jobDescriptionsHTML +'</span>';
        newCellHTML = newCellHTML.replace('row', row);
        newCellHTML = newCellHTML.replace('row', row);
        newCellHTML = newCellHTML.replace('row', row);


        newCell.innerHTML= newCellHTML;

        newCell = x.insertCell(2);
        
        var newCellHTML = branchPositionHTML;
        newCellHTML = newCellHTML.replace('row', row);
        newCellHTML = newCellHTML.replace('row', row);

        newCell.innerHTML= newCellHTML;

        newCell = x.insertCell(3);
        newCell.setAttribute("align", "center");
        newCell.innerHTML = "<a id='job_"+row+"' href='javascript:void(0);' onclick='ajaxPostDelJob(\""+row+"\", this);' class = 'deleteLink'><img id='del_img"+row+"' class='sprite16 sprite16-error_delete handle' src = 'themes/default/images/others/transparent.png' title = '"+row+"' alt = '" + deleteConst + "' /></a></td>";
        document.getElementById('job_' + row).setAttribute('rowCount', row);

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

    var dots     = file_name.split(".")
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
	                    select_item.onfocus = function(){ this.selIndex = this.selectedIndex; }
	                    emulateDisabledOptions(select_item);
					}
                }
            });

    }

    return true;
}


// Wrapper used from the select_all method
function ajaxPost(id, el, table_id) {

    if (table_id == 'skillsTable') {
        ajaxUserPost('skill', id, el, table_id);
    } else if (table_id == 'lessonsTable') {
        ajaxUserPost('lesson', id, el, table_id);
    } else if (table_id == 'coursesTable') {
        ajaxUserPost('course', id, el, table_id);
    } else {
        ajaxUserPost('group', id, el, table_id);
    }
}

// Function to make ajax requests
// type: 'skill'
// id: the users_login of the employee to get the skill
// el: the element of the form corresponding to that skill/lesson
// table_id: the id of the ajax-enabled table
function ajaxUserPost(type, id, el, table_id) {
    Element.extend(el);

    var baseUrl =  sessionType + '.php?ctg=users&edit_user=' + editUserLogin + '&postAjaxRequest=1';
    if (type == 'skill') {
        if (id) {
            var url = baseUrl + '&add_skill=' + id + '&insert=' + document.getElementById('skill_'+id).checked + '&specification='+encodeURI(document.getElementById('spec_skill_'+id).value);

            var img_id   = 'img_'+ id;
        } else if (table_id && table_id == 'skillsTable') {
            el.checked ? url = baseUrl + '&addAll=1' : url = baseUrl + '&removeAll=1';
            url += '&add_skill=1';
            if ($(table_id+'_currentFilter')) {
                url = url+'&filter='+$(table_id+'_currentFilter').innerHTML;
            }
            var img_id   = 'img_selectAll';
        }


    } else if (type == 'lesson' || type == 'course') {
        if (id) {
            var url = baseUrl + '&add_'+type+'=' + id + '&tab='+type+'s&insert=' + document.getElementById(type+'_'+id).checked + '&user_type='+encodeURI(document.getElementById(type+'_type_'+id).value);
            var img_id   = 'img_'+ id;
        } else if (table_id && table_id == (type+'sTable') ) {
            el.checked ? url = baseUrl + '&addAll=1' : url = baseUrl + '&removeAll=1';
            if ($(table_id+'_currentFilter')) {
                url = url+'&filter='+$(table_id+'_currentFilter').innerHTML;
            }
            url += '&add_'+type+'=1&tab='+type+'s';
            var img_id   = 'img_selectAll';
        }

    } else if (type == 'group') {
        if (id) {
            var url = baseUrl + '&add_'+type+'=' + id + +'&tab=groups&insert=' + document.getElementById(type+'_'+id).checked;
            var img_id   = 'img_'+ id;
        } else if (table_id && table_id == (type+'sTable') ) {
            el.checked ? url = baseUrl + '&addAll=1' : url = baseUrl + '&removeAll=1';
            if ($(table_id+'_currentFilter')) {
                url = url+'&filter='+$(table_id+'_currentFilter').innerHTML;
            }
            url += '&add_'+type+'=1&tab=groups';
            var img_id   = 'img_selectAll';
        }
    } else {
        return false;
    }

    
    parameters = {method: 'get'};

	ajaxRequest(el, url, parameters, function(el, transport) {
	
						 				// Update history table
					                    if (type == 'skill') {
					                        tables = sortedTables.size();
					                        var i;
					                        // If the select all chech is used then all skills tables on the form will be updated
					                        if (table_id == 'skillsTable') {
					                            tableToUpload = 'skillFormTable';
					                        } else {
					                            // otherwise the table_id denotes the category id of the skill whose table that must be updated
					                            tableToUpload = table_id + 'skillFormTable';
					                        }
					                        for (i = 0; i < tables; i++) {
					                            if (sortedTables[i].id.match(tableToUpload)) {
					                                eF_js_rebuildTable(i, 0, 'null', 'desc');
					                            } else if (sortedTables[i].id.match('historyFormTable')) {
					                                eF_js_rebuildTable(i, 0, 'timestamp', 'asc');
					                            }
					                        }
					                    } else if (type == 'course') {
					                        tables = sortedTables.size();
					                        var i;
					                        for (i = 0; i < tables; i++) {
					                            if (sortedTables[i].id == 'lessonsTable') {
					                                //eF_js_rebuildTable(i, 0, 'null', 'desc');
					                            }
					                        }
					                    } else if (type == 'group') {
					                        var group_select = document.getElementById('group');
					                        if (id) {
					                            group_select.options[0].selected = true;
					                            group_size = group_select.options.length;
					                            for (i = 1 ; i < group_size; i++) {
					                                if (document.getElementById('group_'+i).checked) {
					                                    group_select.options[i].selected = true;
					                                    break;
					                                }
					                            }
					                        } else {
					                            // When the toggle all is clicked then either select always the first or no group
					                            if (el.checked) {
					                                group_select.options[1].selected = true;
					                            } else {
					                                group_select.options[0].selected = true;
					                            }
					
					                        }
					                    }
					              });  
}


// History
    // Ajaxed history deletion
    function deleteHistory(el, event_id) {

        if (confirm(areYouSureYouWantToDeleteHist)) {

            var url = sessionType + ".php?ctg=users&edit_user=" + editUserLogin + "&delete_evaluation=" + event_id + "&ajax=1";
		    parameters = {method: 'get'};
		
			ajaxRequest(el, url, parameters, function(el, transport) {
                    // Update all form tables
                    tables = sortedTables.size();
                    var i;
                    for (i = 0; i < tables; i++) {
                        if (sortedTables[i].id.match('historyFormTable')) {
                            eF_js_rebuildTable(i, 0, 'timestamp', 'asc');
                        }
                    }



                }
            );
        }
    }


function confirmUser(el, id, type) {
	parameters = {ajax:'confirm_user', type: type, id: id, method: 'get'};
	var url    = location.toString();
	ajaxRequest(el, url, parameters, onConfirmUser);		
}
function onConfirmUser(el, response) {
	setImageSrc(el, 16, 'success');
    el.writeAttribute({title:userHasLesson, alt:userHasLesson});	
}

// social
var __initStatus;
var __noChangeEscape = 0;
function showStatusChange() {
    __initStatus = $('inputStatusText').value;
    $('statusText').style.display = 'none';
    $('inputStatusText').style.display = 'block';
    $('inputStatusText').focus();
}

function changeStatus() {
    if (__initStatus != $('inputStatusText').value) {
        if (sessionType != "administrator") {
        	var url = serverName+sessionType+".php?ctg=personal&postAjaxRequest=1&setStatus=" + $('inputStatusText').value;
        } else {
        	var url = serverName+sessionType+".php?ctg=users&edit_user=" + sessionLogin + "&postAjaxRequest=1&setStatus=" + $('inputStatusText').value;
        }
        $('inputStatusText').style.display = 'none';

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
        $('statusText').style.display = 'block';
        
        //$('statusTextProgressImg').setAttribute("position", "relative");
        parameters = {method: 'get'};
        ajaxRequest($('statusTextProgressImg'), url, parameters, onChangeAccountSuccess);
        
    } else {
        $('inputStatusText').style.display="none";
        $('statusText').style.display = 'block';
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

// Additional accounts
function addAccount(el) {
    var login = $('account_login').value;
    var pwd   = $('account_password').value;
    
    parameters = {method: 'get', ajax: 'additional_accounts', login:login, pwd:pwd};
    var url    = additionalAccountsUrl;
    
//	var url    = additionalAccountsUrl+'&ajax=additional_accounts&login='+login+'&pwd='+pwd;
	ajaxRequest(el, url, parameters, onAddAccountSuccess);    
    
}
function onAddAccountSuccess(el, responseText) {
	$('add_account').hide();
	var login = $('account_login').value;
	$('account_login').value    = '';
	$('account_password').value = '';
	el.removeClassName('sprite16-progress1').addClassName('sprite16-check2');
	//el.src = "images/16x16/check2.png";//el.removeClass('sprite16-edit').addClass('sprite16-delete')
	
	var img = new Element('img').writeAttribute({src: 'themes/default/images/others/transparent.png', alt:'', title:'', onclick:'deleteAccount(this, \''+login+'\')'}).addClassName('sprite16 sprite16-error_delete handle'); 
	$('additional_accounts').insert(new Element('tr').insert(new Element('td').update(login)).insert(new Element('td').insert(img)));
	if ($('empty_accounts')) {
	    $('empty_accounts').remove();
	}
	if (top.sideframe) {
		top.sideframe.location.reload();
	}
}

function deleteAccount(el, login) {
    //var login = $('account_login').value;
	parameters = {method: 'get', ajax: 'additional_accounts', login:login};
    //var url    = additionalAccountsUrl;
    //parameters = {method: 'get'};
	var url    = additionalAccountsUrl+'&delete=1';
	ajaxRequest(el, url, parameters, onDeleteAccountSuccess, onDeleteAccountFailure);    
}
function onDeleteAccountFailure(el, responseText) {
	showMessage(responseText, 'failure');
	el.removeClassName('sprite16-progress1').addClassName('sprite16-delete');
}
function onDeleteAccountSuccess(el, responseText) {
	el.hide();
    new Effect.Fade(el.up().up());
    top.sideframe.location.reload();
    //$('additional_accounts').insert(new Element('div').writeAttribute({id:'empty_accounts'}).addClassName('emptyCategory').update(youHaventSetAdditionalAccounts));
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

if ($('statusTextProgressImg')) {
	$('statusTextProgressImg').setAttribute("position", "relative");
 }
 
 
if (enableMyJobSelect) {            
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