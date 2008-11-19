{* Smarty template for module_personal.php *}

<script>{if $T_BROWSER == 'IE6'}{assign var='globalImageExtension' value='gif'}var globalImageExtension = 'gif';{else}{assign var='globalImageExtension' value='png'}var globalImageExtension = 'png';{/if}</script>
<script type = "text/JavaScript">

{if $smarty.const.MSIE_BROWSER == 1}
{literal}
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
{/literal}
{/if}
{literal}
// Function for printing in IE6
// Opens a new popup, set its innerHTML like the content we want to print
// then calls window.print and then closes the popup without the user knowing
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

    if (!confirm('{/literal}{$smarty.const._AREYOUSUREYOUWANTTOCANCELJOB}{literal}')) {
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

    var baseUrl =  '{/literal}{$smarty.session.s_type}{literal}.php?ctg=users&edit_user={/literal}{$smarty.get.edit_user}{literal}&postAjaxRequest=1';
    var url = baseUrl + '&add_branch=' + branch.value + '&add_job=' + encodeURI(job.value)  +  '&add_position=' + position.value + '&insert=0';

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
                        var newPreselectedRow = noOfRows - 1;
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
            alert("{/literal}{$smarty.const._JOBALREADYASSIGNED}{literal}");
            return;
        }
    }

    var baseUrl =  '{/literal}{$smarty.session.s_type}{literal}.php?ctg=users&edit_user={/literal}{$smarty.get.edit_user}{literal}&postAjaxRequest=1';

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
                    if (sortedTables[i].id.match('JobsFormTable')) {
                        eF_js_rebuildTable(i, 0, 'null', 'desc');
                    } else if (sortedTables[i].id.match('historyFormTable')) {
                        eF_js_rebuildTable(i, 0, 'timestamp', 'asc');
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
    if (jobsTable.rows.length == 1) {

        var x = jobsTable.insertRow(1);
        var newCell = x.insertCell(0);
        var newCellHTML = '{/literal}{$smarty.const._NOPLACEMENTSASSIGNEDYET}{literal}';
        newCell.innerHTML= newCellHTML;
        newCell.setAttribute("id", "no_jobs_found");
        newCell.colSpan = 4;
        newCell.className = "emptyCategory centerAlign";
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
        var newCellHTML = '{/literal}{$T_PERSONAL_DATA_FORM.branches.html|replace:"\n":""}{literal}';

        // Replacing the "row" strings of the HTML code of the select to the correct row. For example the onclick="change(row)" will become onclick="change(2)"
        newCellHTML = newCellHTML.replace('row', row);
        newCellHTML = newCellHTML.replace('row', row);
        newCellHTML = newCellHTML.replace('row', row);
        newCellHTML = newCellHTML.replace('row', row);
        newCellHTML = newCellHTML.replace('row', row);
        newCellHTML = newCellHTML.replace('row', row);

        newCell.innerHTML= '<table><tr><td>'+newCellHTML+'</td><td align="right"><a id="branches_details_link_'+row+'" name="branches_details_link" style="visibility:hidden"><img src="images/16x16/view.png" title="{/literal}{$smarty.const._DETAILS}{literal}" alt="{/literal}{$smarty.const.DETAILS}{literal}" border="0"></a></td></tr></table>';

        newCell = x.insertCell(1);
        newCellHTML = '{/literal}<span id = "job_descriptions_'+row+'_span">{$T_PERSONAL_DATA_FORM.job_descriptions.html|replace:"\n":""}</span>{literal}';
        newCellHTML = newCellHTML.replace('row', row);
        newCellHTML = newCellHTML.replace('row', row);
        newCellHTML = newCellHTML.replace('row', row);


        newCell.innerHTML= newCellHTML;

        newCell = x.insertCell(2);
        var newCellHTML = '{/literal}{$T_PERSONAL_DATA_FORM.branch_position.html|replace:"\n":""}{literal}';
        newCellHTML = newCellHTML.replace('row', row);
        newCellHTML = newCellHTML.replace('row', row);

        newCell.innerHTML= newCellHTML;

        newCell = x.insertCell(3);
        newCell.setAttribute("align", "center");
        newCell.innerHTML = '<a id="job_'+row+'" href="javascript:void(0);" onclick="ajaxPostDelJob(\''+row+'\', this);" class = "deleteLink"><img id="del_img'+row+'" border = "0" src = "images/16x16/delete.png" title = "'+row+'" alt = "{$smarty.const._DELETE}" /></a></td>';
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
{/literal}
        alert ('{$smarty.const._ONLYIMAGEFILESAREVALID}');
{literal}
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
        url = "{/literal}{$smarty.session.s_type}{literal}.php?ctg=module_hcd&op=branches&postAjaxRequest=1&getJobSelect=1&edit_branch="+fb+"&jobSelectId="+jobs_select_id+defJob;
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
                    {/literal}
                    {if $smarty.const.MSIE_BROWSER == 1}
                    {literal}
                    select_item.onfocus = function(){ this.selIndex = this.selectedIndex; }
                    emulateDisabledOptions(select_item);
                    {/literal}
                    {/if}
                    {literal}

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

    var baseUrl =  '{/literal}{$smarty.session.s_type}{literal}.php?ctg=users&edit_user={/literal}{$smarty.get.edit_user}{literal}&postAjaxRequest=1';
    if (type == 'skill') {
        if (id) {
            var url = baseUrl + '&add_skill=' + id + '&insert=' + document.getElementById('skill_'+id).checked + '&specification='+encodeURI(document.getElementById('spec_skill_'+id).value);

            var img_id   = 'img_'+ id;
        } else if (table_id && table_id == 'skillsTable') {
            el.checked ? url = baseUrl + '&addAll=1' : url = baseUrl + '&removeAll=1';
            url += '&add_skill=1';
            var img_id   = 'img_selectAll';
        }


    } else if (type == 'lesson' || type == 'course') {
        if (id) {
            var url = baseUrl + '&add_'+type+'=' + id + '&insert=' + document.getElementById(type+'_'+id).checked + '&user_type='+encodeURI(document.getElementById(type+'_type_'+id).value);
            var img_id   = 'img_'+ id;
        } else if (table_id && table_id == (type+'sTable') ) {
            el.checked ? url = baseUrl + '&addAll=1' : url = baseUrl + '&removeAll=1';
            url += '&add_'+type+'=1';
            var img_id   = 'img_selectAll';
        }

    } else if (type == 'group') {
        if (id) {
            var url = baseUrl + '&add_'+type+'=' + id + '&insert=' + document.getElementById(type+'_'+id).checked;
            var img_id   = 'img_'+ id;
        } else if (table_id && table_id == (type+'sTable') ) {
            el.checked ? url = baseUrl + '&addAll=1' : url = baseUrl + '&removeAll=1';
            url += '&add_'+type+'=1';
            var img_id   = 'img_selectAll';
        }
    } else {
        return false;
    }

    var position = eF_js_findPos(el);
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
                onFailure: function (transport) {
                    img.writeAttribute({src:'images/16x16/delete.png', title: transport.responseText}).hide();
                    new Effect.Appear(img_id);
                    window.setTimeout('Effect.Fade("'+img_id+'")', 10000);
                },
                onSuccess: function (transport) {
                    img.style.display = 'none';
                    img.setAttribute('src', 'images/16x16/check.png');
                    new Effect.Appear(img_id);
                    window.setTimeout('Effect.Fade("'+img_id+'")', 2500);

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
                }
            });


}



{/literal}

</script>

    {*This is the form that contains the user personal data*}
    {capture name = 't_personal_data_code'}
        {$T_PERSONAL_DATA_FORM.javascript}
        <form {$T_PERSONAL_DATA_FORM.attributes}>
            {$T_PERSONAL_DATA_FORM.hidden}
            <table class = "formElements" width="90%">

            {* MODULE_HCD: Insert a second column - new table *}
            {if $T_MODULE_HCD_INTERFACE}
                <tr><td>
                <table width = "50%">
            {/if}

                {if (isset($smarty.get.add_user))}

                    <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.new_login.label}:&nbsp;</td>
                        <td style="white-space:nowrap;">{$T_PERSONAL_DATA_FORM.new_login.html}</td></tr>
                     <tr><td></td><td class = "infoCell">{$smarty.const._ONLYALLOWEDCHARACTERSLOGIN}</td></tr>
                    {if $T_PERSONAL_DATA_FORM.new_login.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.new_login.error}</td></tr>{/if}
                    <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.password_.label}:&nbsp;</td>
                        <td style="white-space:nowrap;">{$T_PERSONAL_DATA_FORM.password_.html}</td></tr>
                    <tr><td></td><td class = "infoCell">{$smarty.const._PASSWORDMUSTBE6CHARACTERS}</td></tr>
                    {if $T_PERSONAL_DATA_FORM.password_.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.password_.error}</td></tr>{/if}

                    <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.passrepeat.label}:&nbsp;</td>
                        <td style="white-space:nowrap;">{$T_PERSONAL_DATA_FORM.passrepeat.html}</td></tr>
                    {if $T_PERSONAL_DATA_FORM.passrepeat.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.passrepeat.error}</td></tr>{/if}
                {else}
                    {if !$T_LDAP_USER}
                        <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.password_.label}:&nbsp;</td>
                            <td style="white-space:nowrap;">{$T_PERSONAL_DATA_FORM.password_.html}</td></tr>
                        <tr><td></td><td class = "infoCell">{$smarty.const._PASSWORDMUSTBE6CHARACTERS}</td></tr>
                        {if $T_PERSONAL_DATA_FORM.password_.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.password_.error}</td></tr>{/if}

                        <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.passrepeat.label}:&nbsp;</td>
                            <td style="white-space:nowrap;">{$T_PERSONAL_DATA_FORM.passrepeat.html}</td></tr>
                        {if $T_PERSONAL_DATA_FORM.passrepeat.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.passrepeat.error}</td></tr>{/if}
                    {else}
                        <tr><td class = "labelCell">{$smarty.const._PASSWORD}:&nbsp;</td>
                            <td style="white-space:nowrap;">{$smarty.const._LDAPUSER}</td></tr>
                    {/if}
                {/if}
                <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.name.label}:&nbsp;</td>
                    <td style="white-space:nowrap;">{$T_PERSONAL_DATA_FORM.name.html}</td></tr>
                {if $T_PERSONAL_DATA_FORM.name.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.name.error}</td></tr>{/if}

                <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.surname.label}:&nbsp;</td>
                    <td style="white-space:nowrap;">{$T_PERSONAL_DATA_FORM.surname.html}</td></tr>
                {if $T_PERSONAL_DATA_FORM.surname.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.surname.error}</td></tr>{/if}

                {* MODULE_HCD: Insert fields here so that two columns are about equal *}
                {if $T_MODULE_HCD_INTERFACE}
                    <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.father.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.father.html}</td></tr>
                    {if $T_PERSONAL_DATA_FORM.father.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.father.error}</td></tr>{/if}
                    <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.sex.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.sex.html}</td></tr>
                    <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.marital_status.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.marital_status.html}</td></tr>
                    <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.birthday.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.birthday.html}</td></tr>
                    <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.birthplace.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.birthplace.html}</td></tr>
                    <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.birthcountry.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.birthcountry.html}</td></tr>
                    <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.mother_tongue.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.mother_tongue.html}</td></tr>
                    <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.nationality.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.nationality.html}</td></tr>
                    <tr><td colspan=2>&nbsp;</td></tr>

                    {if $smarty.get.ctg != 'personal' || $smarty.session.s_type == 'administrator'}
                        <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.branches_main.label}:&nbsp;</td>
                        <td>
                        <table>
                             <tr><td>{$T_PERSONAL_DATA_FORM.branches_main.html}</td>
                                 <td align="right"><a id="details_link" name="details_link" {$T_BRANCH_INFO} {if ($T_BRANCH_INFO == "") || ($smarty.get.add_branch == 1 && !isset($smarty.get.add_branch_to))}style="visibility:hidden"{/if}><img src="images/16x16/view.png" title="{$smarty.const._DETAILS}" alt="{$smarty.const.DETAILS}" border="0"></a></td>
                             </tr>
                        </table>
{*2222222222222222222222222222 *}
{if $T_PERSONAL_DATA_FORM.all_jobs.html}
<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.all_jobs.label}:&nbsp;</td><td><span id="jobs_main_span">{$T_PERSONAL_DATA_FORM.all_jobs.html}</span></td></tr>
{/if}
              {if isset($my_jobs_html)}
                {literal}
                        <script>
                        var length = document.getElementById('jobs_main').options.length;
                        document.getElementById('jobs_main').options[0].disabled = true;

                        for (i = 1; i < length; i++) {
                            if (document.getElementById('jobs_main').options[i].value == "__emptyother_branch") {
                                 document.getElementById('jobs_main').options[i].disabled = "true";
                                 break;
                            }
                        }

                        {/literal}
                        {if $smarty.const.MSIE_BROWSER == 1}
                        {literal}
                        emulateDisabledOptions(document.getElementById('jobs_main'));
                        {/literal}
                        {/if}
                        {literal}

                        </script>
                  {/literal}
                {/if}

{*11111*}

                        <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.placement.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.placement.html}</td></tr>

                        <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.group.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.group.html}</td></tr>
                    {/if}
                    <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.office.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.office.html}</td></tr>
                    <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.company_internal_phone.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.company_internal_phone.html}</td></tr>
                {/if}

                <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.email.label}:&nbsp;</td>
                    <td>{$T_PERSONAL_DATA_FORM.email.html}</td></tr>
                {if $T_PERSONAL_DATA_FORM.email.error && $T_MODULE_HCD_INTERFACE == 0}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.email.error}</td></tr>{/if}

                {if ($smarty.session.s_type == "administrator" || ($T_MODULE_HCD_INTERFACE && $T_CTG != "personal"))}
                        {if $T_MODULE_HCD_INTERFACE == 0}
                        <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.group.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.group.html}</td></tr>
                        {/if}
					{if $T_CURRENTUSERROLEID == 0}
                        <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.user_type.label}:&nbsp;</td>
                        <td>{$T_PERSONAL_DATA_FORM.user_type.html}</td></tr>
                        {if $T_PERSONAL_DATA_FORM.user_type.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.user_type.error}</td></tr>{/if}
					{/if}
                    {if $T_PERSONAL_DATA_FORM.languages_NAME.label != ""}
                        <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.languages_NAME.label}:&nbsp;</td>
                            <td>{$T_PERSONAL_DATA_FORM.languages_NAME.html}</td></tr>
                            {if $T_PERSONAL_DATA_FORM.languages_NAME.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.languages_NAME.error}</td></tr>{/if}
                    {/if}

                    <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.active.label}:&nbsp;</td>
                        <td>{$T_PERSONAL_DATA_FORM.active.html}</td></tr>
                        {if $T_PERSONAL_DATA_FORM.active.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.active.error}</td></tr>{/if}
                {/if}


                {foreach name = 'profile_fields' key = key item = item from = $T_USER_PROFILE_FIELDS }
                    <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.$item.label}:&nbsp;</td>
                        <td class = "elementCell">{$T_PERSONAL_DATA_FORM.$item.html}</td></tr>
                    {if $T_PERSONAL_DATA_FORM.$item.error}<tr><td></td><td class = "formError">{$T_PERSONAL_DATA_FORM.$item.error}</td></tr>{/if}
                {/foreach}



                {if (!isset($smarty.get.add_user))}
                <tr><td class = "labelCell">{$smarty.const._REGISTRATIONDATE}:&nbsp;</td>
                    <td>#filter:timestamp-{$T_REGISTRATION_DATE}#</td></tr>
               {/if}

                {* MODULE_HCD: If no module then submit button here, else insert the second column of data and submit will be inserted later elsewhere *}
                {if !$T_MODULE_HCD_INTERFACE}
                    <tr><td colspan = "2">&nbsp;</td></tr>
                    <tr><td></td><td class = "submitCell" style = "text-align:left">
                             {$T_PERSONAL_DATA_FORM.submit_personal_details.html}</td></tr>
                {else}
                    </table>
                </td><td>
                    <table width="50%" align="left">
                        <tr><td colspan=2>&nbsp;</td></tr>
                        <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.address.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.address.html}</td></tr>
                        <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.city.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.city.html}</td></tr>
                        <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.country.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.country.html}</td></tr>
                        <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.homephone.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.homephone.html}</td></tr>
                        <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.mobilephone.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.mobilephone.html}</td></tr>
                        <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.hired_on.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.hired_on.html}</td></tr>
                        <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.left_on.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.left_on.html}</td></tr>
                        <tr><td colspan=2>&nbsp;</td></tr>
                        <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.employement_type.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.employement_type.html}</td></tr>
                        <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.way_of_working.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.way_of_working.html}</td></tr>
                        <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.work_permission_data.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.work_permission_data.html}</td></tr>
                        <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.police_id_number.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.police_id_number.html}</td></tr>
                        <tr><td colspan=2>&nbsp;</td></tr>
                        <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.afm.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.afm.html}</td></tr>
                        <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.doy.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.doy.html}</td></tr>
                        <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.wage.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.wage.html}</td></tr>
                        <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.bank.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.bank.html}</td></tr>
                        <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.bank_account.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.bank_account.html}</td></tr>
                        <tr><td colspan=2>&nbsp;</td></tr>
                        <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.driving_licence.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.driving_licence.html}</td></tr>
                        <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.national_service_completed.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.national_service_completed.html}</td></tr>
                        <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.transport.label}:&nbsp;</td><td>{$T_PERSONAL_DATA_FORM.transport.html}</td></tr>
                    </table>
                </td></tr>

        </table> {* And of main table of class = formelements *}

                {* Print the new centrally aligned submit button - This table is closed </table> by the closing tab of the main table of the eFront normal interface *}
                <table width ="66%">
                <tr><td>&nbsp;</td></tr>
                <tr><td class = "submitCell" style = "text-align:center" align="center">{$T_PERSONAL_DATA_FORM.submit_personal_details.html}</td></tr>

                {/if}
            </table>
        </form>
{*avatar code*}
        {if (!isset($smarty.get.add_user))}
        <fieldset>
        <legend>{$smarty.const._SETAVATAR}</legend>
        {$T_AVATAR_FORM.javascript}
        <form {$T_AVATAR_FORM.attributes}>
            {$T_AVATAR_FORM.hidden}
            <table class = "formElements">
                <tr><td class = "labelCell">{$smarty.const._CURRENTAVATAR}:&nbsp;</td>
                    <td class = "elementCell"><img src = "view_file.php?file={$T_AVATAR}" title="{$smarty.const._CURRENTAVATAR}" alt="{$smarty.const._CURRENTAVATAR}"
                    {if isset($T_NEWWIDTH)} width = "{$T_NEWWIDTH}" height = "{$T_NEWHEIGHT}"{/if}
                    /></td></tr>
            {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
                <tr><td class = "labelCell">{$T_AVATAR_FORM.delete_avatar.label}:&nbsp;</td>
                    <td class = "elementCell">{$T_AVATAR_FORM.delete_avatar.html}</td></tr>
                <tr><td class = "labelCell">{$T_AVATAR_FORM.file_upload.label}:&nbsp;</td>
                    <td class = "elementCell">{$T_AVATAR_FORM.file_upload.html}</td></tr>
                <tr><td class = "labelCell">{$T_AVATAR_FORM.system_avatar.label}:&nbsp;</td>
                    <td class = "elementCell">{$T_AVATAR_FORM.system_avatar.html}&nbsp;(<a href = "show_avatars.php" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._VIEWLIST}', 2)">{$smarty.const._VIEWLIST}</a>)</td></tr>
                <tr><td colspan = "2">&nbsp;</td></tr>
                <tr><td></td>
                    <td class = "elementCell">{$T_AVATAR_FORM.submit_upload_file.html}</td></tr>
            {/if}
            </table>
        </form>
        </fieldset>
        {/if}
    {/capture}

    {* MODULE HCD: Create the tabs for the job_descriptions/skills/history *}
    {if $T_MODULE_HCD_INTERFACE}

    {*  **************************************************************
        This is the form that contains the employee's job descriptions
        **************************************************************    *}
        {capture name = 't_employee_jobs'}
            {* Check permissions for allowing user to assign a new job *}
            {if isset($T_PERSONAL_DATA_FORM.branches) && ($smarty.session.s_type == "administrator" || ($smarty.session.employee_type == $smarty.const._SUPERVISOR && $T_CTG != 'personal'))}
            <table>
                <tr>
                    <td><a href="{$smarty.session.referer}#" onclick="add_new_job_row({$T_PLACEMENTS_SIZE})"><img src="images/16x16/add2.png" title="{$smarty.const._NEWJOBPLACEMENT}" alt="{$smarty.const._NEWJOBPLACEMENT}"/ border="0"></a></td><td><a href="{$smarty.session.referer}#" onclick="add_new_job_row({$T_PLACEMENTS_SIZE})">{$smarty.const._NEWJOBPLACEMENT}</a></td>
                </tr>
            </table>
            {/if}

                <table border = "0" width = "100%" class = "sortedTable" id="jobsTable" noFooter="true">
                    <tr class = "topTitle">
                        <td class = "topTitle">{$smarty.const._BRANCHNAME}</td>
                        <td class = "topTitle">{$smarty.const._JOBDESCRIPTION}</td>
                        <td class = "topTitle">{$smarty.const._EMPLOYEEPOSITION}</td>
                        <td class = "topTitle" align="center">{$smarty.const._OPERATIONS}</td>
                    </tr>

                {if !isset($T_PERSONAL_DATA_FORM.branches) && $T_CTG != "personal"}
                    <tr>
                        <td colspan=4 class = "emptyCategory centerAlign" id = "noBranches">{$smarty.const._NOBRANCHESHAVEBEENREGISTERED}</td>
                    </tr>
                {else}
                    {if isset($T_PLACEMENTS)}
                        {assign var = "jobs_found" value = '1'}
                        {foreach name = 'users_list' key = 'key' item = 'placement' from = $T_PLACEMENTS}
                        <tr id = "row_{$jobs_found}">

                            {if ($T_CTG != "personal" || $smarty.session.s_type == 'administrator')}
                                <td><table><tr><td>{$T_PERSONAL_DATA_FORM.branches.html|replace:"row":$jobs_found|replace:"\'":"'"}</td><td align="right"><a href="{$smarty.session.s_type}.php?ctg=module_hcd&op=branches&edit_branch={$placement.branch_ID}" id="branches_details_link_{$jobs_found}"><img src="images/16x16/view.png" title="{$smarty.const._DETAILS}" alt="{$smarty.const.DETAILS}" border="0"></a></td></tr></table></td>
                                <td><span id = "job_descriptions_{$jobs_found}_span">{$T_PERSONAL_DATA_FORM.job_descriptions.html|replace:"row":$jobs_found|replace:"\'":"'"}</span></td>
                                <td>{$T_PERSONAL_DATA_FORM.branch_position.html|replace:"row":$jobs_found|replace:"\'":"'"}</td>
                                <td align = "center"><a id="job_{$jobs_found}" href = "javascript:void(0);" onclick="ajaxPostDelJob('{$jobs_found}', this);" class = "deleteLink"><img id="del_img{$jobs_found}" border = "0" src = "images/16x16/delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a></td>
                            {else}
                                <td>{$placement.name}</td>
                                <td>{$placement.description}</td>
                                <td>{if $placement.supervisor == 0} {$smarty.const._EMPLOYEE} {else} {$smarty.const._SUPERVISOR} {/if}</td>
                                <td align = "center"><img border = "0" src = "images/16x16/delete_gray.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a></td>
                            {/if}
                        </tr>

                            {if $T_CTG != "personal" || $smarty.session.s_type == 'administrator'}
                                {literal}
                                <script type = "text/JavaScript">
                                    row = '{/literal}{$jobs_found}{literal}';

                                    branch_select = document.getElementById('branches_' + row);
                                    value = '{/literal}{$placement.branch_ID}{literal}';
                                    selectOption(branch_select, value);
                                    branch_select.setAttribute("defaultVal", value);

                                    job_select = document.getElementById('job_descriptions_' + row);
                                    value = '{/literal}{$placement.description}{literal}';
                                    selectOption(job_select, value);
                                    job_select.setAttribute("defaultVal", value) ;

                                    branch_position_select = document.getElementById('branch_position_' + row);
                                    value = '{/literal}{$placement.supervisor}{literal}';
                                    selectOption(branch_position_select, value);
                                    branch_position_select.setAttribute("defaultVal", value) ;

                                    change_branch('branches_' + row,'branches_details_link_'+row,'job_descriptions_'+row, document.getElementById('job_descriptions_'+row).value)
                                </script>
                                {/literal}
                            {/if}
                            {math assign="jobs_found" equation="x+1" x=$jobs_found}
                        {/foreach}
                        {literal}
                        <script type = "text/JavaScript">
                            document.getElementById('jobsTable').setAttribute('preSelectedJob', {/literal}{$jobs_found}{literal}-1);
                        </script>
                        {/literal}
                    {else}
                         <tr id="no_jobs_found">
                            <td colspan=4 class = "emptyCategory centerAlign">{$smarty.const._NOPLACEMENTSASSIGNEDYET}</td>
                         </tr>
                    {/if}



                {/if}

                </table>

        {/capture}



    {*  ****************************************************
        This is the form that contains the employee's skills
        **************************************************** *}
        {capture name = 't_employee_skills'}
                {if $smarty.session.s_type == "administrator"}
                <table>
                    <tr>
                        <td><a href="{$smarty.server.PHP_SELF}?ctg=module_hcd&op=skills&add_skill=1"><img src="images/16x16/add2.png" title="{$smarty.const._NEWSKILL}" alt="{$smarty.const._NEWSKILL}"/ border="0"></a></td><td><a href="{$smarty.server.PHP_SELF}?ctg=module_hcd&op=skills&add_skill=1">{$smarty.const._NEWSKILL}</a></td>
                    </tr>
                </table>
                {/if}

<!--ajax:skillsTable-->
                <table style = "width:100%" class = "sortedTable" size = "{$T_SKILLS_SIZE}" sortBy = "0" id = "skillsTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=users&edit_user={$smarty.get.edit_user}&">
                    <tr class = "topTitle">
                        <td class = "topTitle" name="description"   width="55%">{$smarty.const._SKILL}</td>
                        <td class = "topTitle" name="specification" width="*">{$smarty.const._SPECIFICATION}</td>
                        <td class = "topTitle" name="skill_ID"      width="10%" align="center">{$smarty.const._CHECK}</td>
                    </tr>

            {if isset($T_SKILLS)}
                {foreach name = 'skill_list' key = 'key' item = 'skill' from = $T_SKILLS}
                    <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                        <td>
                            {if $smarty.session.s_type == "administrator"}
                            <a href="{$smarty.server.PHP_SELF}?ctg=module_hcd&op=skills&edit_skill={$skill.skill_ID}">{$skill.description}</a>
                            {else}
                            {$skill.description}
                            {/if}
                        </td>
                        <td><input class = "inputText" width = "*" type="text" name="spec_skill_{$skill.skill_ID}"  id="spec_skill_{$skill.skill_ID}" onchange="ajaxUserPost('skill','{$skill.skill_ID}', this , '{$skill.categories_ID}');" value="{$skill.specification}"{if $skill.users_login != $smarty.get.edit_user} style="visibility:hidden" {/if}></td>
                        <td align="center"><input class = "inputCheckBox" type = "checkbox" name = "{$skill.skill_ID}" id = "skill_{$skill.skill_ID}" onclick="javascript:show_hide_spec({$skill.skill_ID}); ajaxUserPost('skill','{$skill.skill_ID}', this, '{$skill.categories_ID}');" {if $skill.users_login == $smarty.get.edit_user} checked {/if} ></td>
                    </tr>
                {/foreach}
                </table>
<!--/ajax:skillsTable-->
            {else}
                    <tr><td colspan = 3>
                        <table width = "100%">
                            <tr><td class = "emptyCategory centerAlign">{$smarty.const._NOSKILLSHAVEBEENREGISTERED}</td></tr>
                        </table>
                        </td>
                    </tr>
                </table>
<!--/ajax:skillsTable-->
            {/if}
        {/capture}



     {* **************************************************************
        This is the form that enables uploading files for an employee using the filemanager
        **************************************************************    *}
        {capture name = 't_file_record_code'}
            {$T_FILE_MANAGER}
        {/capture}



     {*  **************************************************************
         This is the form that contains the history of the employee in the company
         **************************************************************    *}

        {**moduleHistory: Show history *}
        {capture name = 't_history_code'}

<!--ajax:historyFormTable-->
            <table width="100%" size = "{$T_HISTORY_SIZE}" id = "historyFormTable" sortBy = "0" class = "sortedTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" order="asc" url = "{$smarty.server.PHP_SELF}?ctg=users&edit_user={$smarty.get.edit_user}&history=1&">
            <tr class = "topTitle">
                <td class = "topTitle" name="timestamp" width = "35%">{$smarty.const._DATE}</td>
                <td class = "topTitle" name="specification" width = "*">{$smarty.const._SUBJECT}</td>
                {if $smarty.session.s_type == "administrator" || ($T_MODULE_HCD_INTERFACE && $T_CTG != "personal")}
                <td class = "topTitle noSort" width = "*" align="center">{$smarty.const._OPERATIONS}</td>
                {/if}
            </tr>

            {if isset($T_HISTORY)}
            {foreach name = 'history_list' key = 'key' item = 'history' from = $T_HISTORY}
            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                <td>#filter:timestamp_time-{$history.timestamp}#</td>
                <td>{$history.specification}</td>
                {if $smarty.session.s_type == "administrator" || ($T_MODULE_HCD_INTERFACE && $T_CTG != "personal")}
                <td align="center"><a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$smarty.get.edit_user}&delete_evaluation={$history.event_ID}&tab=evaluations" onclick = "return confirm('{$smarty.const._AREYOUSUREYOUWANTTODELETETHEHISOTYRECORD}')" class = "deleteLink"><img border = "0" src = "images/16x16/delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a></td>
                {/if}
            </tr>
            {/foreach}
            {else}
                <tr><td colspan = 5>
            <table width = "100%">
                <tr><td class = "emptyCategory centerAlign">{$smarty.const._NOHISTORYREGARDINGTHISEMPLOYEE}</td></tr>
            </table>
            </td></tr>
            {/if}
            </table>
<!--/ajax:historyFormTable-->
        {/capture}


        {capture name = 't_employee_evaluations_code'}
           <table>
                <tr><td>

                   {if ($T_CTG != "personal")}
                   <table>
                       <tr>
                            <td><a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$smarty.get.edit_user}&add_evaluation=1&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._NEWEVALUATION}', new Array('400px', '300px'))"><img src="images/16x16/add2.png" title="{$smarty.const._NEWEVALUATION}" alt="{$smarty.const._NEWEVALUATION}" border="0" /></a></td>
                            <td><a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$smarty.get.edit_user}&add_evaluation=1&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._NEWEVALUATION}', new Array('400px', '300px'))">{$smarty.const._NEWEVALUATION}</a></td>
                       </tr>
                   </table>
                   {/if}
                   {*<a href="{$smarty.session.s_type}.php?ctg=users&edit_user={$smarty.get.edit_user}&add_evaluation=1"><img src="images/16x16/add2.png" title="{$smarty.const._NEWEVALUATION}" alt="{$smarty.const._NEWJOB}"/ border="0"></a></td><td><a href="{$smarty.session.s_type}.php?ctg=users&edit_user={$smarty.get.edit_user}&add_evaluation=1">{$smarty.const._NEWEVALUATION}</a> *}

                </td></tr>
            </table>

            <table border = "0" width = "100%" class = "sortedTable">
                <tr class = "topTitle">
                    <td class = "topTitle" width = "35%">{$smarty.const._DATE}</td>
                    <td class = "topTitle">{$smarty.const._SUBJECT}</td>
                    <td class = "topTitle">{$smarty.const._AUTHOR}</td>
                    <td class = "topTitle" align="center">{$smarty.const._OPERATIONS}</td>
                </tr>

            {if isset($T_EVALUATION)}
                {foreach name = 'users_list' key = 'key' item = 'evaluation' from = $T_EVALUATION}
                <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                    <td>#filter:timestamp_time-{$evaluation.timestamp}#</td>
                    <td>{$evaluation.specification}</td>
                    <td>{$evaluation.author}</td>
                    <td align = "center">
                        <table>
                            <tr>
                            <td width="45%">
                                <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$smarty.get.edit_user}&edit_evaluation={$evaluation.event_ID}&popup=1" target = "POPUP_FRAME" class = "editLink" onclick = "eF_js_showDivPopup('{$smarty.const._NEWEVALUATION}', new Array('400px', '300px'))"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
                            </td>
                            <td width="45%">
                                <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$smarty.get.edit_user}&delete_evaluation={$evaluation.event_ID}&tab=evaluations" onclick = "return confirm('{$smarty.const._AREYOUSUREYOUWANTTODELETEEVALUATION}')" class = "deleteLink"><img border = "0" src = "images/16x16/delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a>
                            </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                {/foreach}
            {else}
                <tr>
                    <td colspan=4 class = "emptyCategory centerAlign">{$smarty.const._NOEVALUATIONSASSIGNEDYET}</td>
                </tr>
            {/if}

            </table>
        {/capture}

        {capture name = 't_evaluations_code'}
                 {$T_EVALUATIONS_FORM.javascript}
                 <table width = "75%">
                     <tr>
                         <td width="70%">
                              <form {$T_EVALUATIONS_FORM.attributes}>
                              {$T_EVALUATIONS_FORM.hidden}
                                  <table class = "formElements">
                                      <tr>
                                          <td class = "labelCell">{$T_EVALUATIONS_FORM.specification.label}:&nbsp;</td>
                                          <td style="white-space:nowrap;">{$T_EVALUATIONS_FORM.specification.html}</td>
                                      </tr>
                                      {if $T_EVALUATIONS_FORM.specification.error}<tr><td></td><td class = "formError">{$T_EVALUATIONS_FORM.specification.error}</td></tr>{/if}

                                      <tr><td colspan = "2">&nbsp;</td></tr>

                                      <tr><td></td><td class = "submitCell" style = "text-align:left">
                                          {$T_EVALUATIONS_FORM.submit_evaluation_details.html}</td>
                                      </tr>

                             </table>
                            </form>
                        </td>
                    </tr>
                </table>
        {/capture}
    {/if}


    {if isset($T_USER_TO_GROUP_FORM)}
        {capture name = 't_users_to_groups_code'}
                <table border = "0" width = "100%" id = "groupsTable" class = "sortedTable" sortBy = "0">
                    <tr class = "topTitle">
                        <td class = "topTitle" width="30%">{$smarty.const._NAME}</td>
                        <td class = "topTitle" width="50%">{$smarty.const._DESCRIPTION}</td>
                        <td class = "topTitle centerAlign" width="20%">{$smarty.const._CHECK}</td>
                    </tr>

                {foreach name = 'users_to_groups_list' key = 'key' item = 'group' from = $T_USER_TO_GROUP_FORM}
                    {strip}
                    <tr class = "{cycle values = "oddRowColor, evenRowColor"} {if !$group.active}deactivatedTableElement{/if}">
                        <td width="30%">{$group.name}</td>
                        <td width="50%">{$group.description}</td>
                        <td align = "center" width="20%">
                        {if ($smarty.get.ctg == "personal" && $smarty.session.s_type != 'administrator') || (isset($T_CURRENT_USER->coreAccess.users) && $T_CURRENT_USER->coreAccess.users != 'change')}
                            {if $group.partof == 1}
                                <img src = "images/16x16/check2.png" alt = "{$smarty.const._PARTOFTHISGROUP}" title = "{$smarty.const._PARTOFTHISGROUP}" />
                            {/if}
                        {else}
                            {if $group.partof == 1}
                                <input class = "inputCheckBox" type = "checkbox" id = "group_{$group.id}" name = "{$group.id}" onclick ="ajaxUserPost('group', '{$group.id}', this);" checked>
                            {else}
                                <input class = "inputCheckBox" type = "checkbox" id = "group_{$group.id}" name = "{$group.id}" onclick ="ajaxUserPost('group', '{$group.id}', this);">
                            {/if}
                        {/if}
                        </td>
                    </tr>
                    {/strip}
                {/foreach}
                </table>
        {/capture}
    {else}
        {capture name = 't_users_to_groups_code'}
            <table width = "100%">
                <tr><td class = "emptyCategory centerAlign">{$smarty.const._NOGROUPSAREDEFINED}</td></tr>
            </table>
        {/capture}
    {/if}








    {*This is the form that contains the user personal data*}
    {if $T_MODULE_HCD_INTERFACE}
    {capture name = 't_personal_form_data_code'}
        {$T_PERSONAL_DATA_FORM.javascript}
        <form {$T_PERSONAL_DATA_FORM.attributes}>
            {$T_PERSONAL_DATA_FORM.hidden}
            <table style="white-space:nowrap;">
                <tr>
                    <td width = "30px">&nbsp</td>
                    <td width = "*">
                        <table width="100%">
                            <tr><td>
                                <table width = "100%">
                                    <tr><td colspan=2 width="300px">&nbsp;</td></tr>
                                    <tr><td width="35%" align = "center" style="min-width:100px;"><img src = "{if ($T_AVATAR)}view_file.php?file={$T_AVATAR}{else}images/avatars/system_avatars/unknown_small.{$globalImageExtension}{/if}" title="{$smarty.const._CURRENTAVATAR}" alt="{$smarty.const._CURRENTAVATAR}" /></td>
                                        <td width="*">
                                            <table>
                                                <tr><td class = "labelFormHalfCell">{$T_PERSONAL_DATA_FORM.name.label}:&nbsp;</td><td class="elementFormCell">{$T_USERNAME}</td></tr>
                                                {if $T_EMPLOYEE.birthday}<tr><td class = "labelFormHalfCell">{$T_PERSONAL_DATA_FORM.birthday.label}:&nbsp;</td><td class="elementFormCell">{$T_EMPLOYEE.birthday}</td></tr>{/if}
                                                {if $T_EMPLOYEE.address}<tr><td class = "labelFormHalfCell">{$T_PERSONAL_DATA_FORM.address.label}:&nbsp;</td><td class="elementFormCell">{$T_EMPLOYEE.address}</td></tr>{/if}
                                                {if $T_EMPLOYEE.city}<tr><td class = "labelFormHalfCell">{$T_PERSONAL_DATA_FORM.city.label}:&nbsp;</td><td class="elementFormCell">{$T_EMPLOYEE.city}</td></tr>{/if}
                                                {if $T_EMPLOYEE.hired_on}<tr><td class = "labelFormHalfCell">{$T_PERSONAL_DATA_FORM.hired_on.label}:&nbsp;</td><td class="elementFormCell">{$T_EMPLOYEE.hired_on}</td></tr>{/if}
                                                {if $T_EMPLOYEE.left_on}<tr><td class = "labelFormHalfCell">{$T_PERSONAL_DATA_FORM.left_on.label}:&nbsp;</td><td class="elementFormCell">{$T_EMPLOYEE.left_on}</td></tr>{/if}
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                </td>
                            </tr>

                            <tr><td>&nbsp;</td></tr>
                            <tr><td class="labelFormCellTitle">{$smarty.const._PLACEMENTS}</td><td></td></tr>
                            <tr><td>
<!--ajax:JobsFormTable-->
                                <table width="100%" size = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" id = "JobsFormTable" class = "sortedTable" noFooter="true" {if $smarty.get.print != 1}useAjax = "1" url = "{$smarty.server.PHP_SELF}?ctg=users&edit_user={$smarty.get.edit_user}&placements=1&"{/if}>
                                    <tr height="1px"><td class = "labelFormCell noSort" name="name"></td><td class = "elementFormCell noSort" name="description"></td><td class = "elementFormCell noSort" name="supervisor"></td></tr>
                                    {foreach name = 'placements' item = 'placement' from = $T_FORM_PLACEMENTS}
                                    <tr><td class = "labelFormCell" width="30%" name="name">{$placement.name}:&nbsp;</td><td name="description" width="69%">{$placement.description}&nbsp;{if $placement.supervisor}({$smarty.const._SUPERVISOR}){/if}</td><td class="elementFormCell" name="description" width="1%">&nbsp;</td></tr>
                                    {foreachelse}
                                    <tr><td colspan=3>{$smarty.const._NOPLACEMENTSASSIGNEDYET}</td></tr>
                                    {/foreach}
                                </table>
<!--/ajax:JobsFormTable-->
                                </td>
                            </tr>

                            <tr><td>&nbsp;</td></tr>
                            <tr><td class="labelFormCellTitle">{$smarty.const._EVALUATIONS}</td></tr>
                            <tr><td><table width="100%">
                                {foreach name = 'evaluation' item = 'evaluation' from = $T_EVALUATIONS}
                                        <tr><td class = "labelFormCell">#filter:timestamp-{$evaluation.timestamp}#:&nbsp;</td><td class = "elementFormCell">{$evaluation.specification}&nbsp;[{$evaluation.surname}&nbsp;{$evaluation.name}]</td></tr>
                                {foreachelse}
                                        <tr><td colspan=3>{$smarty.const._NOEVALUATIONSASSIGNEDYET}</td></tr>
                                {/foreach}
                                    </table>
                                </td>
                            </tr>

                            <tr><td>&nbsp;</td></tr>
                            <tr><td class="labelFormCellTitle">{$smarty.const._SKILLS}</td></tr>
                            {foreach name = 'skill_categories' item = 'skill_category' from = $T_SKILL_CATEGORIES}
                            <tr><td class="labelForm" style="font-weight:bold;">{$skill_category.description}</td><td></td></tr>
                            <tr><td>
<!--ajax:{$skill_category.id}skillFormTable-->
                                <table width="100%" size = "{$skill_category.size}" id = "{$skill_category.id}skillFormTable" class = "sortedTable" {if $smarty.get.print != 1}useAjax = "1" noFooter="true" url = "{$smarty.server.PHP_SELF}?ctg=users&edit_user={$smarty.get.edit_user}&skills=1&"{/if}>
                                    <tr height="1px"><td class = "labelFormCell noSort" name="description"></td><td class = "elementFormCell noSort" name="specification"></td></tr>
                                    {foreach name = 'skill_$skill_category.id' item = 'skill' from = $skill_category.skills}
                                    <tr><td class = "labelFormCell" name="description">{$skill.description}:</td><td class="elementFormCell" name="specification">&nbsp;{$skill.specification}&nbsp;[{$skill.surname}&nbsp;{$skill.name}]</td></tr>
                                    {foreachelse}
                                    <tr><td>{$smarty.const._NOSKILLSASSIGNED}</td></tr>
                                    {/foreach}
                                </table>
<!--/ajax:{$skill_category.id}skillFormTable-->
                                </td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>
                            {foreachelse}
                            <tr><td>{$smarty.const._NOSKILLSHAVEBEENREGISTERED}</td></tr>
                            {/foreach}

                            {if !isset($T_NOTRAINING)}
                            <tr><td class="labelFormCellTitle">{$smarty.const._TRAININGCAP}</td></tr>
                            <tr><td>
                                    <table>
                                        <tr><td>&nbsp;</td></tr>
                                        {foreach name = 'courses_list' item = 'course' from = $T_COURSES}
                                        <tr><td class="labelFormCellTitle">{$course.name}{if $course.completed}&nbsp;{$course.score}%{/if}</td></tr>
                                        <tr><td><table width="100%">
                                            {foreach name = 'lessons_list' item = 'lesson' from = $course.lessons}
                                                    <tr><td class="labelForm" style="font-weight:bold;">{$lesson.name}{if $lesson.completed}&nbsp;{$lesson.score}%{/if}</td></tr>
                                                    <tr><td>{$smarty.const._COMPLETED}:&nbsp;{if $lesson.completed}#filter:timestamp-{$lesson.to_timestamp}#{else}-{/if}</td></tr>
                                                    <tr><td><table width="100%">
                                                                {foreach name = 'tests_list' item = 'test' from = $lesson.tests}
                                                                <tr><td class = "labelFormCell">{$test.name}:</td><td><table><tr><td><table bgcolor = {if $test.score > 60} "#00FF00" {else}"#FF0000"{/if} border="1"><tr><td>{$test.score}%</td></tr></table></td></tr></table></td><td>(#filter:timestamp-{$test.timestamp}#)</td><td>{if $test.comments != ''}({$test.comments}){/if}</td></tr>
                                                                {/foreach}
                                                                {if $lesson.tests_count > 0}
                                                                <tr><td><b>{$smarty.const._AVERAGESCORE}:</b></td><td><table><tr><td><table bgcolor = {if $lesson.tests_average > 60} "#00FF00" {else}"#FF0000"{/if} border="1"><tr><td>{$lesson.tests_average}%</td></tr></table></td></tr></table></td></tr>
                                                                {/if}
                                                            </table>
                                                        </td>
                                                    </tr>

                                                    <tr><td>&nbsp;</td></tr>
                                            {/foreach}
                                                </table>
                                            </td>
                                        </tr>
                                        {/foreach}
                                   </table>
                                </td>
                            </tr>

                            <tr><td><table width="100%">
                                {foreach name = 'lessons_list' item = 'lesson' from = $T_LESSONS}
                                        <tr><td class="labelFormCellTitle">{$lesson.name}{if $lesson.completed}&nbsp;{$lesson.score}%{/if}</td></tr>
                                        <tr><td>{$smarty.const._COMPLETED}:&nbsp;{if $lesson.completed}#filter:timestamp-{$lesson.to_timestamp}#{else}-{/if}</td></tr>
                                        <tr><td>
                                                <table>
                                                    {foreach name = 'tests_list' key = 'key' item = 'test' from = $lesson.tests}
                                                    <tr><td class = "labelFormCell">{$test.name}:</td>
                                                        <td>
                                                            <table>
                                                                <tr><td>
                                                                        <table bgcolor = {if $test.score > 60} "#00FF00" {else}"#FF0000"{/if} border="1">
                                                                            <tr><td>{$test.score}%</td></tr>
                                                                        </table>
                                                                    </td></tr>
                                                            </table>
                                                        </td><td>(#filter:timestamp-{$test.timestamp}#)</td>
                                                             <td>{if $test.comments != ''}({$test.comments}){/if}</td></tr>
                                                    {/foreach}
                                                    {if $lesson.tests_count > 0}
                                                    <tr><td><b>{$smarty.const._AVERAGESCORE}:</b></td>
                                                        <td>
                                                            <table>
                                                                <tr><td>
                                                                        <table bgcolor = {if $lesson.tests_average > 60} "#00FF00" {else}"#FF0000"{/if} border="1">
                                                                            <tr><td>{$lesson.tests_average}%</td></tr>
                                                                        </table>
                                                                    </td></tr>
                                                            </table>
                                                        </td></tr>
                                                    {/if}
                                                </table>
                                            </td>
                                        </tr>
                                        <tr><td>&nbsp;</td></tr>
                                {/foreach}
                                    </table>
                                </td>
                            </tr>

                            <tr><td>&nbsp;</td></tr>
                            <tr>
                                <td>
                                    <table>
                                        {foreach name = 'averages_list' item = 'average' from = $T_AVERAGES}
                                        <tr><td class="labelForm" style="font-weight:bold;">{$average.title}:&nbsp;<td><table bgcolor = {if $average.avg > 60} "#00FF00" {else}"#FF0000"{/if} border="1"><tr><td>{$average.avg}%</td></tr></table></td></tr>
                                        {/foreach}
                                    </table>
                                </td>
                            </tr>
                            {/if}
                        </table>
                    </td>
                    <td width="30px">&nbsp;</td>
                </tr>
            </table>
        </form>
        </td>
    </tr>
    {/capture}
    {/if}



							<script>
							{literal}
								function confirmUser(el, id, type) {
									Element.extend(el);
									src = el.down().src;
									el.down().src = 'images/others/progress1.gif';
									url = 'administrator.php?ctg=users&edit_user={/literal}{$smarty.get.edit_user}{literal}&ajax=confirm_user&type='+type+'&id='+id;
                                    new Ajax.Request(url, {
                                            method:'get',
                                            asynchronous:true,
                                            onFailure: function (transport) {
                                            	el.down().src = src;
                                            	showMessage(transport.responseText, 'failure');
                                            },
                                            onSuccess: function (transport) {
                                            	el.down().hide();
                                            	el.down().writeAttribute({src:'images/16x16/check2.png', title:'{/literal}{$smarty.const._USERHASTHELESSON}{literal}', alt:'{/literal}{$smarty.const._USERHASTHELESSON}{literal}'});
                                            	new Effect.Appear(el.down().identify());
                                            }
                                   });
								}
							{/literal}
							</script>

        {capture name = 't_users_to_lessons_code'}
<!--ajax:lessonsTable-->
                                            <table style = "width:100%" size = "{$T_LESSONS_SIZE}" id = "lessonsTable" class = "sortedTable" useAjax = "1" url = "{$smarty.server.PHP_SELF}?{if $smarty.session.s_type == "administrator"}ctg=users&edit_user={$smarty.get.edit_user}{else}ctg=personal{/if}&lessons=1&">
                                                <tr class = "topTitle">
                                                    <td name = "name" class = "topTitle">{$smarty.const._NAME}</td>
                                                    <td name = "directions_ID">{$smarty.const._PARENTDIRECTIONS}</td>
                            {if $smarty.session.s_type == "administrator"}
                                                    <td name = "user_type" class = "topTitle">{$smarty.const._USERTYPE}</td>
                                                    <td name = "active" class = "topTitle centerAlign">{$smarty.const._STATUS}</td>
                            {/if}
                            {if $T_MODULE_HCD_INTERFACE == 0}
                                                    <td name = "price" class = "topTitle centerAlign">{$smarty.const._PRICE}</td>
                            {/if}
                                                    <td name = "completed" class = "topTitle centerAlign">{$smarty.const._COMPLETED}</td>
                                                    <td name = "score" class = "topTitle centerAlign">{$smarty.const._SCORE}</td>
                            {if $smarty.session.s_type == "administrator" && (!isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change')}
                                                    <td name = "partof" class = "topTitle centerAlign">{$smarty.const._CHECK}</td>
                            {/if}
                                                </tr>
                            {foreach name = 'users_to_lessons_list' key = 'key' item = 'lesson' from = $T_LESSONS_DATA}
                                                <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$lesson.active}deactivatedTableElement{/if}">
                                                    <td>{$lesson.name}</td>
                                                    <td>{$lesson.directions_name}</td>
                                {if $smarty.session.s_type == "administrator"}
                                                    <td>
                                    {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
                                                        <select name = "type_{$lesson.id}" id = "lesson_type_{$lesson.id}" onChange = "document.getElementById('lesson_{$lesson.id}').checked = true;ajaxUserPost('lesson', '{$lesson.id}', this);">
                                        {foreach name = 'roles_list' key = 'role_key' item = 'role_item' from = $T_ROLES_ARRAY}
                                                            <option value = "{$role_key}" {if ($lesson.user_type == $role_key)}selected{/if}>{$role_item}</option>
                                        {/foreach}
                                        {assign var = "selected" value = ""}
                                                        </select>
                                    {else}
                                                        {$T_ROLES_ARRAY[$lesson.user_type]}
                                    {/if}
                                                    </td>
                                                    <td class = "centerAlign">
                                    {if $lesson.from_timestamp == 0 && $lesson.partof}
                                                        <a href = "javascript:void(0)" onclick = "confirmUser(this, {$lesson.id}, 'lesson')"><img src = "images/16x16/warning.png" title = "{$smarty.const._APPLICATIONPENDING}" alt = "{$smarty.const._APPLICATIONPENDING}"/></a>
                                    {elseif $lesson.partof}
                                                        <img src = "images/16x16/check2.png" title = "{$smarty.const._USERHASTHELESSON}" alt = "{$smarty.const._USERHASTHELESSON}" style = "vertical-align:middle"/>
                                    {else}
                                                        <img src = "images/16x16/book_red_gray.png" title = "{$smarty.const._USERHASNOTTHELESSON}" alt = "{$smarty.const._USERHASNOTTHELESSON}" style = "vertical-align:middle"/>
                                    {/if}
                                {/if}
                                                    </td>
                                {if $T_MODULE_HCD_INTERFACE == 0}
                                                    <td class = "centerAlign">{if $lesson.price == 0}{$smarty.const._FREELESSON}{else}{$lesson.price} {$T_CONFIGURATION.currency}{/if}</td>
                                {/if}
                                                    <td class = "centerAlign">{if $lesson.partof && $lesson.user_type == 'student'}{if $lesson.completed}<img src = "images/16x16/check.png" alt = "{$smarty.const._YES}" title = "{$smarty.const._YES}">{else}<img src = "images/16x16/forbidden.png" alt = "{$smarty.const._NO}" title = "{$smarty.const._NO}">{/if}{/if}</td>
                                                    <td class = "centerAlign">{if $lesson.partof && $lesson.user_type == 'student'}#filter:score-{$lesson.score}#%{/if}</td>
                                {if $smarty.session.s_type == "administrator" && (!isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change')}
                                                    <td class = "centerAlign">
                                                        <input class = "inputCheckBox" type = "checkbox" id = "lesson_{$lesson.id}"  name = "lesson_{$lesson.id}"  onclick ="ajaxUserPost('lesson', '{$lesson.id}', this);" {if $lesson.partof == 1}checked{/if}>
                                                    </td>
                                {/if}
                                                </tr>
                            {foreachelse}
                                                <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory centerAlign" colspan = "6">{$smarty.const._NODATAFOUND}</td></tr>
                            {/foreach}
                                            </table>
<!--/ajax:lessonsTable-->
        {/capture}

        {capture name = 't_users_to_courses_code'}
<!--ajax:coursesTable-->
                                                <table style = "width:100%" size = "{$T_COURSES_SIZE}" id = "coursesTable" class = "sortedTable" useAjax = "1" url = "{$smarty.server.PHP_SELF}?{if $smarty.session.s_type == "administrator"}ctg=users&edit_user={$smarty.get.edit_user}{else}ctg=personal{/if}&courses=1&">
                                                    <tr class = "topTitle">
                                                        <td name = "name" class = "topTitle">{$smarty.const._NAME}</td>
                                                        <td name = "directions_ID">{$smarty.const._PARENTDIRECTIONS}</td>
                                                        <td name = "active" class = "topTitle centerAlign">{$smarty.const._STATUS}</td>
                                                        <td name = "completed" class = "topTitle centerAlign">{$smarty.const._COMPLETED}</td>
                                                        <td name = "score" class = "topTitle centerAlign">{$smarty.const._SCORE}</td>
                                                    {if $smarty.get.ctg == "users"}
                                                        <td name = "user_type" class = "topTitle">{$smarty.const._USERTYPE}</td>
                                                        <td name = "partof" class = "topTitle centerAlign">{$smarty.const._CHECK}</td>
                                                    {else}
                            {if $T_MODULE_HCD_INTERFACE == 0}
                                                        <td name = "price" class = "topTitle centerAlign">{$smarty.const._PRICE}</td>
                            {/if}
                                                    {/if}

                                                    </tr>
                            {foreach name = 'users_to_courses_list' key = 'key' item = 'course' from = $T_COURSES_DATA}
                                                    <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$course.active}deactivatedTableElement{/if}">
                                                        <td>{$course.name}</td>
                                                        <td>{$course.directions_name}</td>
                                                        <td class = "centerAlign">
                                    {if $course.from_timestamp == 0 && $course.partof}
	                                                        <a href = "javascript:void(0)" onclick = "confirmUser(this, {$course.id}, 'course')"><img src = "images/16x16/warning.png" title = "{$smarty.const._APPLICATIONPENDING}" alt = "{$smarty.const._APPLICATIONPENDING}"/></a>
                                    {elseif $course.partof}
    	                                                    <img src = "images/16x16/check2.png" title = "{$smarty.const._USERHASTHECOURSE}" alt = "{$smarty.const._USERHASTHECOURSE}" style = "vertical-align:middle"/>
                                    {else}
        	                                                <img src = "images/16x16/book_red_gray.png" title = "{$smarty.const._USERHASNOTTHECOURSE}" alt = "{$smarty.const._USERHASNOTTHECOURSE}" style = "vertical-align:middle"/>
                                    {/if}
                                                        </td>
                                                        <td class = "centerAlign">{if $course.partof && $course.user_type == 'student'}{if $course.completed}<img src = "images/16x16/check.png" alt = "{$smarty.const._YES}" title = "{$smarty.const._YES}">{else}<img src = "images/16x16/forbidden.png" alt = "{$smarty.const._NO}" title = "{$smarty.const._NO}">{/if}{/if}</td>
                                                        <td class = "centerAlign">{if $course.partof && $course.user_type == 'student'}#filter:score-{$course.score}#%{/if}</td>
                                                {if $smarty.get.ctg == "users"}
                                                        <td>
                                                    {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
                                                            <select name = "course_type_{$course.id}" id = "course_type_{$course.id}" onChange = "document.getElementById('course_{$course.id}').checked = true;ajaxUserPost('course', '{$course.id}', this);">
                                                        {foreach name = 'roles_list' key = 'role_key' item = 'role_item' from = $T_ROLES_ARRAY}
                                                                <option value = "{$role_key}" {if ($course.user_type == $role_key)}selected{/if}>{$role_item}</option>
                                                        {/foreach}
                                                            </select>
                                                    {else}
                                                        {$T_ROLES_ARRAY[$course.user_type]}
                                                    {/if}
                                                        </td>
                                                        <td class = "centerAlign">
                                                    {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
                                                            <input  class = "inputCheckBox" type="checkbox" id="course_{$course.id}" name="{$course.id}" {if $course.partof == 1}checked{/if} onclick ="ajaxUserPost('course', '{$course.id}', this);">
                                                    {else}
                                                            {if $course.partof == 1}<img src = "images/16x16/check2.png" alt = "{$smarty.const._COURSEUSER}" title = "{$smarty.const._COURSEUSER}">{/if}
                                                    {/if}
                                                        </td>
                                                {elseif $smarty.get.ctg == "personal"}
                            {if $T_MODULE_HCD_INTERFACE == 0}
                                                            <td class = "centerAlign">{if $course.price == 0}{$smarty.const._FREECOURSE}{else}{$course.price} {$T_CONFIGURATION.currency}{/if}</td>
                            {/if}
                                                {/if}
                                                    </tr>
                            {foreachelse}
                                                    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory centerAlign" colspan = "10">{$smarty.const._NODATAFOUND}</td></tr>
                            {/foreach}
                                                </table>
<!--/ajax:coursesTable-->
        {/capture}

            <table border = "0" width = "100%" cellspacing = "5">
                <tr><td valign = "top">
             {capture name = 't_user_code'}
                    <div class="tabber" >
                       <div class="tabbertab">
                            <h3>{$smarty.const._EDITUSER}</h3>
                            {eF_template_printInnerTable title = $smarty.const._PERSONALDATA data = $smarty.capture.t_personal_data_code image = '/32x32/businessman.png'}
                        </div>

                    {if $T_MODULE_HCD_INTERFACE && $smarty.get.edit_user}
                            <div class="tabbertab {if ($smarty.get.tab == "placements"  || isset($smarty.post.employee_to_job)) } tabbertabdefault {/if}">
                                <h3>{$smarty.const._PLACEMENTS}</h3>
                                {eF_template_printInnerTable title = $smarty.const._JOBPLACEMENTS data = $smarty.capture.t_employee_jobs image = '/32x32/workstation1.png'}
                            </div>
                        {if $smarty.get.ctg != 'personal'}
                            <div class="tabbertab {if ($smarty.get.tab == "skills"  || isset($smarty.post.employee_to_skills)) } tabbertabdefault {/if}">
                                <h3>{$smarty.const._SKILLS}</h3>
                                <script>var myform = "employee_to_skills";</script>
                                {eF_template_printInnerTable title = $smarty.const._SKILLS data = $smarty.capture.t_employee_skills image = '/32x32/wrench.png'}
                            </div>

                            <div class="tabbertab {if ($smarty.get.tab == "evaluations")} tabbertabdefault {/if}">
                                <h3>{$smarty.const._HISTORY}</h3>
                                {eF_template_printInnerTable title = $smarty.const._EVALUATIONOFEMPLOYEE|cat:'&nbsp;'|cat:$smarty.get.edit_user data = $smarty.capture.t_employee_evaluations_code image = '/32x32/cabinet.png'}
                                {eF_template_printInnerTable title = $smarty.const._HISTORYOFEMPLOYEE|cat:'&nbsp;'|cat:$smarty.get.edit_user data = $smarty.capture.t_history_code image = '/32x32/cabinet.png'}
                            </div>
                        {/if}

                        <div class="tabbertab {if ($smarty.get.tab == "file_record"  || isset($smarty.post.t_file_record)) } tabbertabdefault {/if}">
                            <h3>{$smarty.const._FILERECORD}</h3>
                            {$smarty.capture.t_file_record_code}
                        </div>

                        {if $smarty.get.ctg != 'personal'}
                        <div class="tabbertab {if ($smarty.get.tab == "plaisio_form") } tabbertabdefault {/if}">
                            <h3>{$smarty.const._EMPLOYEEFORM}</h3>
                            {eF_template_printInnerTable alt= $T_USERNAME title = $T_EMPLOYEE_FORM_CAPTION titleStyle = 'font-size:16px;font-weight:bold;' data = $smarty.capture.t_personal_form_data_code image = $T_LOGO options=$T_EMPLOYEE_FORM_OPTIONS}
                            {*{eF_template_printInnerTable title = $smarty.const._EMPLOYEEFORM data = $smarty.capture.t_personal_form_data_code image = $T_LOGO options=$T_EMPLOYEE_FORM_OPTIONS}*}
                        </div>
                        {/if}
                    {/if}

                {if isset($T_USER_TO_GROUP_FORM)}
                    <div class="tabbertab {if ($smarty.get.tab == "groups" || isset($smarty.post.users_to_group)) } tabbertabdefault {/if}">
                        <h3>{$smarty.const._GROUPS}</h3>
                        {$smarty.capture.t_users_to_groups_code}
                    </div>
                {/if}

                {if $smarty.get.edit_user}
                    {if $T_USER_TYPE != 'administrator' && !($T_USER_TYPE == 'professor' && $T_CTG == 'personal') }
                        <div class="tabbertab {if ($smarty.get.tab == "attending" || isset($smarty.post.users_attending)) } tabbertabdefault {/if}">
                            <h3>{$smarty.const._ATTENDING}</h3>
                            <div class="tabber">
                                <div class="tabbertab {if ($smarty.get.tab == "lessons" || isset($smarty.post.users_to_lesson)) } tabbertabdefault {/if}">
                                    <h3>{$smarty.const._LESSONS}</h3>
                                    {if $smarty.get.ctg != personal}
                                        {$smarty.capture.t_users_to_lessons_code}
                                    {else}
                                        {$smarty.capture.t_users_to_lessons_code}  <!--na fygei to else an telika meinei etsi -->
                                    {/if}
                                </div>

                                <div class="tabbertab {if  isset($smarty.post.users_to_existing_course) } tabbertabdefault {/if}">
                                    <h3>{$smarty.const._COURSES}</h3>
                                    {if $smarty.get.ctg != personal}
                                    {$smarty.capture.t_users_to_courses_code}
                                    {else}
                                    {$smarty.capture.t_users_to_courses_code}  <!--na fygei to else an telika meinei etsi -->
                                    {/if}
                                </div>
                            </div>
                        </div>
            {if $T_USER_TRANSACTIONS_NUM > 0}
                        <div class="tabbertab">
                            <h3>{$smarty.const._PAYPALMYTRANSACTIONS}</h3>
                            <div class="tabber">

                <table border = "0" width = "100%" class = "sortedTable" sortBy = "0">
                    <tr class = "topTitle">
                    <td class = "topTitle" width="25%">{$smarty.const._PAYPALTRANSACTIONCODE}</td>
                    <td class = "topTitle" width="20%">{$smarty.const._PAYPALTABLEDATEPAYPAL}</td>
                    <td class = "topTitle" width="20%">{$smarty.const._PAYPALTABLEPRICE}</td>
                    <td class = "topTitle" width="20%">{$smarty.const._STATUS}</td>
                    <td class = "topTitle" width="15%" align="center">{$smarty.const._PAYPALORDERINFO}</td>
                    </tr>
                    {foreach name = 'user_transactions' key = 'key' item = 'trans' from = $T_USER_TRANSACTIONS}
                    <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                    <td>{$trans.txn_id}</td>
                    <td>#filter:timestamp_time-{$trans.timestamp_finish}#</td>
                    <td>{$trans.mc_gross} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}</td>
                    <td>{$trans.payment_status}</td>
                    <td align="center">
                    <a href = "javascript:void(0)" onclick = "eF_js_showDivPopup('{$smarty.const._PAYPALPURCHASEORDER}', new Array('400px', '300px'),
                        'payment_view_{$trans.id}')" title = "{$smarty.const._PAYPALORDERINFO}">
                        <img src = "images/16x16/about.png" alt = "{$smarty.const._PAYPALORDERINFO}" title = "{$smarty.const._PAYPALORDERINFO}" border = "0"/>
                    </a>
                    <div id = "payment_view_{$trans.id}" style = "display:none;">
                    <table style = "width:100%">
                        <tr>
                            <td align="left" height="50" width="50%"><b>{$smarty.const._PAYPALPURCHASEORDERFOR}:</b></td><td align="left" height="50">{$trans.business}</td>
                        </tr>
                        <tr>
                            <td colspan="2" align="left" style="background:#D3D3D3 none repeat scroll 0%;"><strong>{$smarty.const._PAYPALORDERINFO}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="horizontalSeparator"></td>
                        </tr>
                        <tr>
                            <td colspan="2" width=100%>
                            <table width=100%>
                                <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                                    <td align="left" width="50%"><b>{$smarty.const._PAYPALTRANSACTIONCODE}:</b></td><td align="left" width="50%">{$trans.txn_id}</td>
                                </tr>
                                <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                                    <td align="left" width="50%"><b>{$smarty.const._PAYPALTABLEDATEPAYPAL}:</b></td><td align="left" width="50%">#filter:timestamp_time-{$trans.timestamp_finish}#</td>
                                </tr>
                                <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                                    <td align="left" width="50%"><b>{$smarty.const._STATUS}:</b></td><td align="left" width="50%">{$trans.payment_status}</td>
                                </tr>
                            </table>
                            </td>
                        </tr>
                        <tr><td colspan="2"><p></p></td></tr>
                        <tr>
                            <td colspan="2" align="left" style="background:#D3D3D3 none repeat scroll 0%;"><strong>{$smarty.const._PAYPALCUSTOMERINFO}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="horizontalSeparator"></td>
                        </tr>
                        <tr>
                            <td colspan="2" width=100%>
                            <table width=100%>
                                <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                                    <td align="left" width="25%"><b>{$smarty.const._SURNAME}:</b></td><td align="left" width="25%">{$trans.last_name}</td>
                                    <td align="left" width="25%"><b>{$smarty.const._ADDRESS}:</b></td><td align="left" width="25%">{$trans.address_street}</td>

                                </tr>
                                <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                                    <td align="left" width="25%"><b>{$smarty.const._NAME}:</b></td><td align="left" width="25%">{$trans.first_name}</td>
                                    <td align="left" width="25%"><b>{$smarty.const._POSTCODE}:</b></td><td align="left" width="25%">{$trans.address_zip}</td>
                                </tr>
                                <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                                    <td align="left" width="25%"><b>{$smarty.const._COUNTRY}:</b></td><td align="left" width="25%">{$trans.address_country}</td>
                                    <td align="left" width="25%"><b>{$smarty.const._CITY}:</b></td><td align="left" width="25%">{$trans.address_city}</td>
                                </tr>
                                <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                                    <td align="left" width="25%"><b>{$smarty.const._EMAILADDRESS}:</b></td><td align="left" width="75%" colspan="3">{$trans.payer_email}</td>
                                </tr>
                            </table>
                            </td>
                        </tr>
                        <tr><td colspan="2"><p></p></td></tr>
                        <tr>
                            <td colspan="2" align="left" style="background:#D3D3D3 none repeat scroll 0%;"><strong>{$smarty.const._PAYPALITEMSINFO}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="horizontalSeparator"></td>
                        </tr>
                        <tr>
                            <td colspan="2" width=100%>
                            <table width=100%>
                                <tr>
                                    <td align="left" width="60%"><b>{$smarty.const._NAME}</b></td>
                                    <td align="left" width="20%"><b>{$smarty.const._PAYPALITEMCODE}</b></td>
                                    <td align="left" width="20%"><b>{$smarty.const._PRICE}</b></td>
                                </tr>
                                <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                                    <td align="left">{$trans.item_name}</td>
                                    <td align="left">{$trans.item_number}</td>
                                    <td align="left">{$trans.mc_gross} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}</td>
                                </tr>
                            </table>
                            </td>
                        </tr>
                    </table>
                    </div>
                    </td>
                   </tr>
                       {/foreach}
                </table>
                </div>

                        </div>
            {/if}
                    {/if}
                {/if}
                </div>
            {/capture}

            {assign var = 'newTitle' value = $smarty.const._PERSONALOPTIONSFOR}
            {if ($T_MODULE_HCD_INTERFACE && (isset($smarty.get.add_evaluation) || isset($smarty.get.edit_evaluation)))}
                  {eF_template_printInnerTable title = $smarty.const._EVALUATIONOFEMPLOYEE|cat:'&nbsp;'|cat:$smarty.get.edit_user data = $smarty.capture.t_evaluations_code image = '/32x32/cabinet.png'}
            {else}
                {if $smarty.get.edit_user != ""}
                    {if $smarty.get.print_preview == 1}
                        {eF_template_printInnerTable alt= $T_USERNAME title = $T_EMPLOYEE_FORM_CAPTION titleStyle = 'font-size:16px;font-weight:bold;' data = $smarty.capture.t_personal_form_data_code image = $T_LOGO options=$T_EMPLOYEE_FORM_OPTIONS}

                        {*eF_template_printInnerTable title = '<span style=\'font-size:16px;font-weight:bold;\'>'|cat:$smarty.const._EMPLOYEEFORM|cat:':&nbsp;'|cat:$T_USERNAME|cat:'</span>' data = $smarty.capture.t_personal_form_data_code image = $T_LOGO options=$T_EMPLOYEE_FORM_OPTIONS*}
                    {elseif $smarty.get.print == 1}
                        {eF_template_printInnerTable alt= $T_USERNAME title = $T_EMPLOYEE_FORM_CAPTION titleStyle = 'font-size:16px;font-weight:bold;' data = $smarty.capture.t_personal_form_data_code image = $T_LOGO options=$T_EMPLOYEE_FORM_OPTIONS}

                        {*eF_template_printInnerTable title = '<span style=\'font-size:16px;font-weight:bold;\'>'|cat:$smarty.const._EMPLOYEEFORM|cat:':&nbsp;'|cat:$T_USERNAME|cat:'</span>' data = $smarty.capture.t_personal_form_data_code image = $T_LOGO*}

                        {if $smarty.const.MSIE_BROWSER == 0}
                        {literal}
                        <script>
                        window.print();
                        </script>
                        {/literal}
                        {/if}
                    {else}
                        {eF_template_printInnerTable title = "`$newTitle` <span class = 'innerTableName'>&quot;`$smarty.get.edit_user`&quot;</span>" data = $smarty.capture.t_user_code image = '/32x32/user1.png'}
                    {/if}
                {elseif ($smarty.get.ctg == "personal")}
                    {eF_template_printInnerTable title = "`$newTitle` <span class = 'innerTableName'>&quot;`$smarty.session.s_login`&quot;</span>" data = $smarty.capture.t_user_code image = '/32x32/user1.png'}
                {else}

                   {eF_template_printInnerTable title = $smarty.const._NEWUSER data = $smarty.capture.t_user_code image = '/32x32/user1.png'}


                {/if}
            {/if}
            </td></tr>

       </table>
