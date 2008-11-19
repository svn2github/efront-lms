{literal}
<script>

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
    if (criteriaTable.rows.length == 1) {

        var x = criteriaTable.insertRow(1);
        var newCell = x.insertCell(0);
        var newCellHTML = '{/literal}{$smarty.const._NOSEARCHCRITERIADEFINED}{literal}';
        newCell.innerHTML= newCellHTML;
        newCell.setAttribute("id", "no_criteria_found");
        newCell.colSpan = 5;
        newCell.className = "emptyCategory centerAlign";
    }
    return false;
}


// Ajax function to assign jobs through the Placements tab
function ajaxPostDelCriterium(id, el) {

//    if (!confirm('{/literal}{$smarty.const._AREYOUSUREYOUWANTTOCANCELJOB}{literal}')) {
//          return false;
//    }
    var course = document.getElementById('courses_' +id);
    // The row is deleted and the corresponding criteria are deleted
    delete_criterium_row(id,el);

    if (course.value != "0") {
        var query = document.getElementById('query');
        var url =  '{/literal}{$smarty.session.s_type}{literal}.php?ctg=search_courses'+query.value+'&';
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
        var url =  '{/literal}{$smarty.session.s_type}{literal}.php?ctg=search_courses'+query.value+'&';
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
    var newCellHTML = '{/literal}{$T_SEARCH_COURSE_USERS_FORM.courses.html|replace:"\n":""}{literal}';

    // Replacing the "row" strings of the HTML code of the select to the correct row. For example the onclick="change(row)" will become onclick="change(2)"
    newCellHTML = newCellHTML.replace('row', row);
    newCellHTML = newCellHTML.replace('row', row);
    newCellHTML = newCellHTML.replace('row', row);
    newCellHTML = newCellHTML.replace('row', row);
    newCellHTML = newCellHTML.replace('row', row);

    newCell.innerHTML= '<table><tr><td>'+newCellHTML+'</td><td align="right"><a id="courses_details_link_'+row+'" name="courses_details_link" style="visibility:hidden"><img src="images/16x16/view.png" title="{/literal}{$smarty.const._DETAILS}{literal}" alt="{/literal}{$smarty.const.DETAILS}{literal}" border="0"></a></td></tr></table>';

    newCell = x.insertCell(1);
    newCellHTML = '{/literal}{$T_SEARCH_COURSE_USERS_FORM.condition.html|replace:"\n":""}{literal}';
    newCellHTML = newCellHTML.replace('row', row);
    newCellHTML = newCellHTML.replace('row', row);
    newCellHTML = newCellHTML.replace('row', row);

    newCell.innerHTML= newCellHTML;

    newCell = x.insertCell(2);
    var newCellHTML = '<table><tr><td>{/literal}{$T_SEARCH_COURSE_USERS_FORM.from_date_cond.html|replace:"\n":""}{literal}</td><td>{/literal}{$T_SEARCH_COURSE_USERS_FORM.from_date_day.html|replace:"\n":""}{literal}</td><td>{/literal}{$T_SEARCH_COURSE_USERS_FORM.from_date_month.html|replace:"\n":""}{literal}</td><td>{/literal}{$T_SEARCH_COURSE_USERS_FORM.from_date_year.html|replace:"\n":""}{literal}</td></tr></table>';
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
    var newCellHTML = '<table><tr><td>{/literal}{$T_SEARCH_COURSE_USERS_FORM.to_date_cond.html|replace:"\n":""}{literal}</td><td>{/literal}{$T_SEARCH_COURSE_USERS_FORM.to_date_day.html|replace:"\n":""}{literal}</td><td>{/literal}{$T_SEARCH_COURSE_USERS_FORM.to_date_month.html|replace:"\n":""}{literal}</td><td>{/literal}{$T_SEARCH_COURSE_USERS_FORM.to_date_year.html|replace:"\n":""}{literal}</td></tr></table>';
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
    newCell.innerHTML = '<a id="job_'+row+'" href="javascript:void(0);" onclick="ajaxPostDelCriterium(\''+row+'\', this);" class = "deleteLink"><img border = "0" src = "images/16x16/delete.png" title = "'+row+'" alt = "{$smarty.const._DELETE}" /></a></td>';

    document.getElementById('job_' + row).setAttribute('rowCount', row);

    document.getElementById('courses_' +row).options[0].disabled = true;

}

</script>

{/literal}
{capture name = 't_search_course_code'}

    {* Check permissions for allowing user to assign a new job *}
    <table>
        <tr>
            <td><a href="{$smarty.session.referer}#" onclick="add_new_criterium_row({$T_PLACEMENTS_SIZE})"><img src="images/16x16/add2.png" title="{$smarty.const._NEWSEARCHCRITERIUM}" alt="{$smarty.const._NEWSEARCHCRITERIUM}"/ border="0"></a></td><td><a href="{$smarty.session.referer}#" onclick="add_new_criterium_row({$T_PLACEMENTS_SIZE})">{$smarty.const._NEWSEARCHCRITERIUM}</a></td>
        </tr>
    </table>

        {$T_SEARCH_COURSE_USERS_FORM.hidden}
        <table border = "0" width = "100%" class = "sortedTable" id="criteriaTable" noFooter="true">
            <tr class = "topTitle">
                <td class = "topTitle noSort" >{$smarty.const._COURSE}</td>
                <td class = "topTitle noSort">{$smarty.const._STATUS}</td>
                <td class = "topTitle noSort">{$smarty.const._REGISTRATIONDATE}</td>
                <td class = "topTitle noSort">{$smarty.const._COMPLETIONDATE}</td>
                <td class = "topTitle noSort" align="center">{$smarty.const._OPERATIONS}</td>
            </tr>

             <tr id="no_criteria_found">
                <td colspan=5 class = "emptyCategory centerAlign">{$smarty.const._NOSEARCHCRITERIADEFINED}</td>
             </tr>
        </table>
{/capture}


{capture name = 't_found_employees_code'}

<!--ajax:usersTable-->
        <table style = "width:100%" class = "sortedTable" size = "{$T_EMPLOYEES_SIZE}" sortBy = "0" id = "usersTable" useAjax = "1" rowsPerPage = "20" url = "{$smarty.session.s_type}.php?ctg=search_courses&">
        <tr class = "topTitle">
            <td class = "topTitle" name = "login">{$smarty.const._LOGIN}</td>
            <td class = "topTitle" name = "name">{$smarty.const._NAME}</td>
            <td class = "topTitle" name = "surname">{$smarty.const._SURNAME}</td>
            <td class = "topTitle" name = "languages_NAME">{$smarty.const._LANGUAGE}</td>
{*            <td class = "topTitle" name = "timestamp">{$smarty.const._DETAILS}</td>*}
            <td class = "topTitle noSort" align="center">{$smarty.const._EMPLOYEEFORM}</td>
            <td class = "topTitle noSort" align="center">{$smarty.const._SENDMESSAGE}</td>
            <td class = "topTitle noSort" align="center">{$smarty.const._STATISTICS}</td>
            <td class = "topTitle noSort" align="center">{$smarty.const._OPERATIONS}</td>
        </tr>

        {if isset($T_EMPLOYEES_SIZE) && $T_EMPLOYEES_SIZE > 0}

            {foreach name = 'users_list' key = 'key' item = 'user' from = $T_EMPLOYEES}
            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">

            <td>
                <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$user.login}" class = "editLink">{$user.login}</a>
            </td>

            <td>{$user.name}</td>
            <td>{$user.surname}</td>
            <td>{$user.languages_NAME}</td>
    {*1111111111111
            <td>
                <a href = "javascript:void(0)" class = "info nonEmptyLesson">
                    {$smarty.const._COURSES}
                    <img class = "tooltip" border = "0" src="images/others/tooltip_arrow.gif"/>
                    <span class="tooltipSpan">
                        {if isset($lesson.info.general_description)}<strong>{$smarty.const._GENERALDESCRIPTION|cat:'</strong>:&nbsp;'|cat:$lesson.info.general_description}<br/>{/if}
                        {if isset($lesson.info.assessment)}         <strong>{$smarty.const._ASSESSMENT|cat:'</strong>:&nbsp;'|cat:$lesson.info.assessment}<br/>                 {/if}
                        {if isset($lesson.info.objectives)}         <strong>{$smarty.const._OBJECTIVES|cat:'</strong>:&nbsp;'|cat:$lesson.info.objectives}<br/>                 {/if}
                        {if isset($lesson.info.lesson_topics)}      <strong>{$smarty.const._LESSONTOPICS|cat:'</strong>:&nbsp;'|cat:$lesson.info.lesson_topics}<br/>            {/if}
                        {if isset($lesson.info.resources)}          <strong>{$smarty.const._RESOURCES|cat:'</strong>:&nbsp;'|cat:$lesson.info.resources}<br/>                   {/if}
                        {if isset($lesson.info.other_info)}         <strong>{$smarty.const._OTHERINFO|cat:'</strong>:&nbsp;'|cat:$lesson.info.other_info}<br/>                  {/if}
                    </span>
                </a>
            </td>
    *}
            <td align="center">
                {if $user.user_type != 'administrator'}
                    <a href="{$smarty.session.s_type}.php?ctg=users&edit_user={$user.login}&print_preview=1" onclick = "eF_js_showDivPopup('{$smarty.const._EMPLOYEEFORMPRINTPREVIEW}', new Array('800px','500px'))" target = "POPUP_FRAME"><img src='images/16x16/form_blue.png' title=  '{$smarty.const._PRINTPREVIEW}' alt = '{$smarty.const._PRINTPREVIEW}' border='0' /></a>
                {else}
                    <img src='images/16x16/form_red.png' title=  '{$smarty.const._PRINTPREVIEW}' alt = '{$smarty.const._PRINTPREVIEW}' border='0' />
                {/if}
            </td>

            <td align="center"><a style="" href="forum/new_message.php?recipient={$user.login}" onclick='eF_js_showDivPopup("{$smarty.const._SENDMESSAGE}", new Array("750px", "450px"))' target="POPUP_FRAME"><img src="images/12x12/mail_icon.png" border="0"></a></td>
            <td align="center"><a href="{$smarty.session.s_type}.php?ctg=statistics&option=user&sel_user={$user.login}"><img border = "0" src = "images/16x16/chart.png" title = "{$smarty.const._STATISTICS}" alt = "{$smarty.const._STATISTICS}" /></a></td>
            <td align = "center">
                <table>
                <tr><td width="45%">
                {if $user.active == 1}
                    <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$user.login}" class = "editLink"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
                {else}
                    <img border = "0" src = "images/16x16/edit_gray.png" title = "{$smarty.const._UNPRIVILEGEDATTEMPT}" alt = "{$smarty.const._UNPRIVILEGEDATTEMPT}" />
                {/if}

                </td><td></td><td  width="45%"
                    <a href = "{$smarty.session.s_type}.php?ctg=users&op=users_data&delete_user={$user.login}" onclick = "return confirm('{$smarty.const._AREYOUSUREYOUWANTTOFIREEMPLOYEE}')" class = "deleteLink"><img border = "0" src = "images/16x16/delete.png" title = "{$smarty.const._FIRE}" alt = "{$smarty.const._FIRE}" /></a>
                </td></tr>
                </table>
            </td>

    {*        <td align="center"><a href="{$smarty.session.s_type}.php?ctg=statistics&option=user&sel_user={$user.login}"><img border = "0" src = "images/16x16/chart.png" title = "{$smarty.const._STATISTICS}" alt = "{$smarty.const._STATISTICS}" /></a></td>*}
            </tr>
            {/foreach}

             <tr style="display:none"><td><input type="hidden" id="sendAllRecipients" value="{$T_SENDALLMAIL_URL}" /></td></tr>
             {if $smarty.const.MSIE_BROWSER == 1}
                 <img style="display:none" src="images/16x16/pens.png" onLoad="javascript:new Effect.Appear('sendToAllId');" />
             {else}
                 <script>
                 new Effect.Appear('sendToAllId');
                 </script>
             {/if}
        {else}
             <tr><td colspan="10" class = "emptyCategory centerAlign">{$smarty.const._NOEMPLOYEESFULFILLTHESPECIFIEDCRITERIA}</td></tr>

             <tr style="display:none"><td><input type="hidden" id="sendAllRecipients" value="{$T_SENDALLMAIL_URL}" /></td></tr>
             {if $smarty.const.MSIE_BROWSER == 1}
                <img style="display:none" src="images/16x16/pens.png" onLoad="javascript:$('sendToAllId').style.display='none';" />
             {else}
                 <script>
                 $('sendToAllId').style.display = 'none';
                 </script>
             {/if}
        {/if}

    </table>
<!--/ajax:usersTable-->

{/capture}