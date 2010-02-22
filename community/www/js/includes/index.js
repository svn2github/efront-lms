

// Function which shows and hides the lense next to the select of a branch
// It also changes the element with id=jobs_select_id into the select with
// the job descriptions of this branch according to a relevant ajax request
function change_branch(element,jobs_select_id, defJob)
{
    var fb = document.getElementById(element).value;

    if (fb == 0 || fb == "all") {
        document.getElementById(jobs_select_id).disabled = "disabled";
    } else {

        // Change the apperance of the job select to match this branch with AjaxRequest
        url = "index.php?ctg=signup&postAjaxRequest=1&getJobSelect=1&branch="+fb+"&jobSelectId="+jobs_select_id+defJob;
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

function change_supervisors(element, supervisors_id) {

    var fb = document.getElementById(element).value;
    if (fb == 0 || fb == "all") {
        document.getElementById(supervisors_id).disabled = "disabled";
    } else {
     url = "index.php?ctg=signup&postAjaxRequest=1&getSupervisorsSelect=1&branch="+fb;
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
                    /*
                    if (defJob && temp[i] == defJob) {
                        elOptNew.selected = true;
                    }
                    */
                    elOptNew.text = temp[i+1];

                    try {
                        select_item.add(elOptNew,null);
                    } catch(ex) {
                        select_item.add(elOptNew); // IE only
                    }

                }
/*
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
*/
            }
        });
        //updateActivateCheckbox($('branch_supervisors'));
    }

    return true;
}
