
function show_hide_dates(id, el) {

    if (el.value == "3") {
        document.getElementById('from_date_cond_' +id).disabled = true;
        document.getElementById('from_date_day_' +id).disabled = true;
        document.getElementById('from_date_month_' +id).disabled = true;
        document.getElementById('from_date_year_' +id).disabled = true;
        document.getElementById('to_date_cond_' +id).disabled = true;
        document.getElementById('to_date_day_' +id).disabled = true;
        document.getElementById('to_date_month_' +id).disabled = true;
        document.getElementById('to_date_year_' +id).disabled = true;
    } else if (el.value == "2") {
        document.getElementById('from_date_cond_' +id).disabled = false;
        document.getElementById('from_date_day_' +id).disabled = false;
        document.getElementById('from_date_month_' +id).disabled = false;
        document.getElementById('from_date_year_' +id).disabled = false;
        document.getElementById('to_date_cond_' +id).disabled = true;
        document.getElementById('to_date_day_' +id).disabled = true;
        document.getElementById('to_date_month_' +id).disabled = true;
        document.getElementById('to_date_year_' +id).disabled = true;
    } else {
        document.getElementById('from_date_cond_' +id).disabled = false;
        document.getElementById('from_date_day_' +id).disabled = false;
        document.getElementById('from_date_month_' +id).disabled = false;
        document.getElementById('from_date_year_' +id).disabled = false;
        document.getElementById('to_date_cond_' +id).disabled = false;
        document.getElementById('to_date_day_' +id).disabled = false;
        document.getElementById('to_date_month_' +id).disabled = false;
        document.getElementById('to_date_year_' +id).disabled = false;
    }

}

//delete row
function delete_criterium_row(id, el)
{
    var criteriaTable = document.getElementById('criteriaTable');
    if ($('noFooterRow1')) {
        $('noFooterRow1').remove();
    }

    noOfRows = criteriaTable.rows.length;
    var rowId;
    for (i = 1; i < noOfRows; i++) {
        rowId = "row_"+id;
        if (criteriaTable.rows[i].id == rowId) {
            // el.up.up.id has the form 'row_'*
            deleteInHidden(rowId);
            criteriaTable.deleteRow(i);
            break;
        }
    }

    // If no job descriptions remain then show the "No jobs assigned" message
    
    if (criteriaTable.rows.length == 2) {

        var x = criteriaTable.insertRow(1);
        var newCell = x.insertCell(0);
        var newCellHTML = noSearchCriteriaDefined;
        newCell.innerHTML= newCellHTML;
        newCell.setAttribute("id", "no_criteria_found");
        newCell.colSpan = 5;
        newCell.className = "emptyCategory";
    }
    return false;
}


// Ajax function to assign jobs through the Placements tab
function ajaxPostDelCriterium(id, el) {

    var course = document.getElementById('courses_' +id);
    // The row is deleted and the corresponding criteria are deleted
    delete_criterium_row(id,el);

    if (course.value != "0") {
        var query = document.getElementById('query');
        var url =  sessionType + '.php?ctg=search_courses'+query.value+'&';
        // Update all form tables
        tables = sortedTables.size();
        var i;
        for (i = 0; i < tables; i++) {
            if (sortedTables[i].id.match('usersTable')) {
                ajaxUrl[i] = url;
                //sortedTables[i].url = url;
                eF_js_rebuildTable(i, 0, 'null', 'desc');
            }
        }
    }

}

// Function to add or replace the value of element id in the hidden with the value
function addValueInHidden(id, value) {

    var query = document.getElementById('query');
    element_string_position = query.value.indexOf('&' + id + '=');
    // If the element does not exist
    if (element_string_position < 0) {
        query.value = query.value + '&' + id + '=' + value;
    } else {
        rest_string = query.value.substr(element_string_position+1); //omitt the '&'
        element_end_position = rest_string.indexOf('&');

        // If this element was the last one
        if (element_end_position < 0) {
            query.value = query.value.substr(0, element_string_position) + '&' + id + '=' + value;
        } else {
            query.value = query.value.substr(0, element_string_position) + '&' + id + '=' + value + rest_string.substr(element_end_position);
        }

    }
}

function deleteInHidden(id) {
    row_id = id.substr(id.indexOf('row_')+4);

    var query = document.getElementById('query');
    delValueInHidden(query,'courses_'+row_id);
    delValueInHidden(query,'condition_'+row_id);
    delValueInHidden(query,'from_date_cond_'+row_id);
    delValueInHidden(query,'from_date_day_'+row_id);
    delValueInHidden(query,'from_date_month_'+row_id);
    delValueInHidden(query,'from_date_year_'+row_id);
    delValueInHidden(query,'to_date_cond_'+row_id);
    delValueInHidden(query,'to_date_day_'+row_id);
    delValueInHidden(query,'to_date_month_'+row_id);
    delValueInHidden(query,'to_date_year_'+row_id);

}


// Function to add or replace the value of element id in the hidden with the value
function delValueInHidden(query, id) {

    element_string_position = query.value.indexOf('&' + id + '=');
    // If the element exists
    if (element_string_position >= 0) {
        rest_string = query.value.substr(element_string_position+1); //omitt the '&'
        element_end_position = rest_string.indexOf('&');

        // If this element was the last one
        if (element_end_position < 0) {
            query.value = query.value.substr(0, element_string_position);
        } else {
            query.value = query.value.substr(0, element_string_position) + rest_string.substr(element_end_position);
        }

    }
}

var __from_date_flag = 0;
var __to_date_flag = 0;

// Ajax function to assign jobs through the Placements tab
function ajaxPostSearch(id, el) {
    var dontPost;

    if (document.getElementById('courses_'+id).value != "0") {
        dontPost = 0;
    } else {
        dontPost = 1;
    }


    if (el.id.match('date')) {
        if (el.id.match('from')) {
            if (document.getElementById('from_date_day_'+id).value != 0 && document.getElementById('from_date_month_'+id).value != 0 && document.getElementById('from_date_year_'+id).value != 0) {
                __from_date_flag = 1;
            } else {
                if (__from_date_flag) {
                    document.getElementById('from_date_day_'+id).value = 0;
                    addValueInHidden('from_date_day_'+id, '');
                    document.getElementById('from_date_month_'+id).value = 0;
                    addValueInHidden('from_date_month_'+id, '');
                    document.getElementById('from_date_year_'+id).value = 0;
                    addValueInHidden('from_date_year_'+id, '');
                } else {
                    dontPost = 1;
                }
                __from_date_flag = 0;
            }
        } else {
            if (document.getElementById('to_date_day_'+id).value != 0 && document.getElementById('to_date_month_'+id).value != 0 && document.getElementById('to_date_year_'+id).value != 0) {
                __to_date_flag = 1;
            } else {
                if (__to_date_flag) {
                    document.getElementById('to_date_day_'+id).value = 0;
                    addValueInHidden('to_date_day_'+id, '');
                    document.getElementById('to_date_month_'+id).value = 0;
                    addValueInHidden('to_date_month_'+id, '');
                    document.getElementById('to_date_year_'+id).value = 0;
                    addValueInHidden('to_date_year_'+id, '');
                } else {
                    dontPost = 1;
                }
                __to_date_flag = 0;

            }
        }


    }

    addValueInHidden(el.id, el.value);

    if (!dontPost) {

        var query = document.getElementById('query');
        var url =  sessionType + '.php?ctg=search_courses'+query.value+'&';
        // Update all form tables
        tables = sortedTables.size();
        var i;
        for (i = 0; i < tables; i++) {
            if (sortedTables[i].id.match('usersTable')) {
                ajaxUrl[i] = url;
                //sortedTables[i].url = url;
                eF_js_rebuildTable(i, 0, 'null', 'desc');
            }
        }
    }


}

var __criteria_total_number = 0;

// Function for inserting the new job row into the edit_user profile
// The row argument denotes how many placements were initially present
// so that only one extra job may be inserted each time
function add_new_criterium_row(row) {

    var table = document.getElementById('criteriaTable');

    if (document.getElementById('no_criteria_found')) {
         document.getElementById('criteriaTable').deleteRow(1);
    }
    if ($('noFooterRow1')) {
        $('noFooterRow1').remove();
    }
    noOfRows = table.rows.length;

    var row = noOfRows;
    var x = table.insertRow(row);

    row = (++__criteria_total_number);
    x.setAttribute("id","row_"+row);
    newCell = x.insertCell(0);
    var newCellHTML = searchCourseUsersFormCourses;

    // Replacing the "row" strings of the HTML code of the select to the correct row. For example the onclick="change(row)" will become onclick="change(2)"
    newCellHTML = newCellHTML.replace('row', row);
    newCellHTML = newCellHTML.replace('row', row);
    newCellHTML = newCellHTML.replace('row', row);
    newCellHTML = newCellHTML.replace('row', row);
    newCellHTML = newCellHTML.replace('row', row);

    newCell.innerHTML= "<table><tr><td>"+newCellHTML+"</td><td align='right'><a id='courses_details_link_"+row+"' name='courses_details_link' style='visibility:hidden'><img src='themes/default/images/others/transparent.png' class = 'sprite16 sprite16-search handle' title='"+detailsConst+" alt='"+detailsConst+"' /></a></td></tr></table>";

    newCell = x.insertCell(1);
    newCellHTML = searchCourseUsersFormCondition;
    newCellHTML = newCellHTML.replace('row', row);
    newCellHTML = newCellHTML.replace('row', row);
    newCellHTML = newCellHTML.replace('row', row);

    newCell.innerHTML= newCellHTML;

    newCell = x.insertCell(2);
    var newCellHTML = searchCourseUsersFormDateFrom;
    newCellHTML = newCellHTML.replace('row', row);
    newCellHTML = newCellHTML.replace('row', row);
    newCellHTML = newCellHTML.replace('row', row);
    newCellHTML = newCellHTML.replace('row', row);
    newCellHTML = newCellHTML.replace('row', row);
    newCellHTML = newCellHTML.replace('row', row);
    newCellHTML = newCellHTML.replace('row', row);
    newCellHTML = newCellHTML.replace('row', row);
    newCell.innerHTML= newCellHTML;

    newCell = x.insertCell(3);
    var newCellHTML = searchCourseUsersFormDateTo;
    newCellHTML = newCellHTML.replace('row', row);
    newCellHTML = newCellHTML.replace('row', row);
    newCellHTML = newCellHTML.replace('row', row);
    newCellHTML = newCellHTML.replace('row', row);
    newCellHTML = newCellHTML.replace('row', row);
    newCellHTML = newCellHTML.replace('row', row);
    newCellHTML = newCellHTML.replace('row', row);
    newCellHTML = newCellHTML.replace('row', row);
    newCell.innerHTML= newCellHTML;

    newCell = x.insertCell(4);
    newCell.setAttribute("align", "center");
    newCell.innerHTML = '<a id="job_'+row+'" href="javascript:void(0);" onclick="ajaxPostDelCriterium(\''+row+'\', this);" class = "deleteLink"><img class="sprite16 sprite16-error_delete handle" src = "themes/default/images/others/transparent.png" title = "'+row+'" alt = "'+deleteConst+'" title="'+deleteConst+'" /></a></td>';

    document.getElementById('job_' + row).setAttribute('rowCount', row);

    document.getElementById('courses_' +row).options[0].disabled = true;

}
