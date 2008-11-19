{if $smarty.get.op == "chart"}
    {literal}
        <script language = "JavaScript" type = "text/javascript" src = "js/drag-drop-folder-tree.php"> </script>
    {/literal}
{/if}

{literal}

<script type="text/JavaScript">
{/literal}
{if $smarty.const.MSIE_BROWSER == 1}
{literal}
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


function simulateJobSelects() {
    {/literal}
    {foreach name = 'users_list' key = 'key' item = 'user' from = $T_EMPLOYEES}
    {if $user.active}
    {literal}
    var select_item = document.getElementById('job_selection_row{/literal}{$user.login}{literal}');
    if (select_item) {
        if (!select_item.selectedIndex) {
            select_item.selIndex = 1;
            select_item.selectedIndex = 1; //always exists - 'No specific job description' in the branch
        }
		emulateDisabledOptions(select_item);
    }
    {/literal}
    {/if}
    {/foreach}
{literal}
}
{/literal}
{/if}
{literal}

// REPORTS: Reloads to a new url based on the form values of the reports and the other select criteria
function refreshResults()
{
    var cut = location.href.split("?");

    if (document.getElementById('new_login').value   ||document.getElementById('name').value    ||document.getElementById('surname').value ||document.getElementById('email').value   ||document.getElementById('user_types').value||document.getElementById('registration').value||document.getElementById('father').value    ||document.getElementById('sex').value    ||document.getElementById('birthday').value||document.getElementById('birthplace').value||document.getElementById('birthcountry').value||document.getElementById('mother_tongue').value||document.getElementById('nationality').value  ||document.getElementById('address').value    ||document.getElementById('city').value    ||document.getElementById('country').value ||document.getElementById('homephone').value ||document.getElementById('mobilephone').value||document.getElementById('office').value    ||document.getElementById('company_internal_phone').value    ||document.getElementById('afm').value||document.getElementById('doy').value ||document.getElementById('police_id_number').value    ||document.getElementById('work_permission_data').value||document.getElementById('employement_type').value    ||document.getElementById('hired_on').value    ||document.getElementById('left_on').value    ||document.getElementById('wage').value    ||document.getElementById('marital_status').value    ||document.getElementById('bank').value    ||document.getElementById('bank_account').value    ||document.getElementById('way_of_working').value  ) {
        newUrl = cut[0] +"?ctg=module_hcd&op=reports&search=1&all=" + document.getElementById('all_criteria').checked + "&branch_ID=" + document.getElementById('search_branch').value +  "&include_sb="+document.getElementById('include_subbranchesId').checked + "&job_description_ID=" + document.getElementById('search_job_description').value + "&skill_ID=" + document.getElementById('search_skill').value + "&new_login=" + document.getElementById('new_login').value+ "&name=" + document.getElementById('name').value    + "&surname=" + document.getElementById('surname').value+ "&email=" + document.getElementById('email').value    + "&user_types=" + document.getElementById('user_types').value+ "&registration=" + document.getElementById('registration').value+ "&father=" + document.getElementById('father').value    + "&sex=" + document.getElementById('sex').value+ "&birthday=" + document.getElementById('birthday').value    + "&birthplace=" + document.getElementById('birthplace').value+ "&birthcountry=" + document.getElementById('birthcountry').value    + "&mother_tongue=" + document.getElementById('mother_tongue').value+ "&nationality=" + document.getElementById('nationality').value    + "&address=" + document.getElementById('address').value+ "&city=" + document.getElementById('city').value    + "&country=" + document.getElementById('country').value+ "&homephone=" + document.getElementById('homephone').value   + "&mobilephone=" + document.getElementById('mobilephone').value    + "&office=" + document.getElementById('office').value+ "&company_internal_phone=" + document.getElementById('company_internal_phone').value    + "&afm=" + document.getElementById('afm').value  + "&doy=" + document.getElementById('doy').value    + "&police_id_number=" + document.getElementById('police_id_number').value+ "&work_permission_data=" + document.getElementById('work_permission_data').value+ "&employement_type=" + document.getElementById('employement_type').value+ "&hired_on=" + document.getElementById('hired_on').value   + "&left_on=" + document.getElementById('left_on').value       + "&wage=" + document.getElementById('wage').value+ "&marital_status=" + document.getElementById('marital_status').value+ "&bank=" + document.getElementById('bank').value    + "&bank_account=" + document.getElementById('bank_account').value + "&way_of_working=" + document.getElementById('way_of_working').value;
    } else {
        newUrl = cut[0] +"?ctg=module_hcd&op=reports&search=1&all=" + document.getElementById('all_criteria').checked + "&branch_ID=" + document.getElementById('search_branch').value +  "&include_sb="+document.getElementById('include_subbranchesId').checked + "&job_description_ID=" + document.getElementById('search_job_description').value + "&skill_ID=" + document.getElementById('search_skill').value;
    }

    if (document.getElementById('driving_licence').checked) {
        newUrl += "&driving_licence=" + document.getElementById('driving_licence').value;
    }
    if (document.getElementById('national_service_completed').checked) {
        newUrl += "&national_service_completed=" + document.getElementById('national_service_completed').value;
    }
    if (document.getElementById('transport').checked) {
        newUrl += "&transport=" + document.getElementById('transport').value;
    }
    if (document.getElementById('active').checked) {
        newUrl += "&active=" + document.getElementById('active').value;
    }

    // Update all form tables
    var tables = sortedTables.size();
    var i;
    for (i = 0; i < tables; i++) {
        ajaxUrl[i] = newUrl + "&";
        if (sortedTables[i].id == 'foundEmployees') {
            eF_js_rebuildTable(i, 0, 'null', 'desc');
        }
    }
    //location.href = newUrl;
}

// Function used as a wrapper function for refreshing or not results
// in the search employee form, in order to include subbranches:
// If no branch is selected then no refresh of the ajax table is going to take place
function includeSubbranches() {
    if (document.getElementById('search_branch').value != "0") {
        refreshResults();
    }
}

// Function used as a wrapper function for refreshing or not results
// in the search employee form, in order to include subbranches:
// If no branch is selected then no refresh of the ajax table is going to take place
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
    img.setAttribute('src', 'images/others/progress1.gif');

    el.parentNode.appendChild(img);

    img.style.display = 'none';
    img.setAttribute('src', 'images/16x16/check.png');
    new Effect.Appear(img_id);
    window.setTimeout('Effect.Fade("'+img_id+'")', 2500);
}

// Expands/collapses the branches tree based on a tree attribute called expanded
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

// Shows and hides the specification text boxes
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


// Shows and hides the lense next to the select of a branch
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

// Shows and hides the lense next to the select of a branch
function change_skill_category(element)
{
//change_skill_category
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
        edit_link.href = main_url[0] + "?ctg=module_hcd&op=skill_cat&edit_skill_cat=" + skill_cat_ID;
        del_link.href = main_url[0] + "?ctg=module_hcd&op=skill_cat&del_skill_cat=" + skill_cat_ID;
    }

    return true;
}


</script>
{/literal}


{* ---------------------------------------- MODULE DESCRIPTION ------------------------------------ *}
{******************* EMPLOYEES ******************}
{if $smarty.get.op == 'employees'}
    {assign var = "title" value = '<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?op=employees">'|cat:$smarty.const._EMPLOYEES|cat:'</a>'}

    {**moduleShowemployees: Show employees*}
    {capture name = 't_employees_code'}
    <table border = "0" >

        {* Link to add employee only for administrator and supervisor *}
        <tr>
            <td>
        {if !$T_CURRENT_USER->coreAccess.users || $T_CURRENT_USER->coreAccess.users == 'change'}
            <a href="{$smarty.session.s_type}.php?ctg=users&add_user=1"><img src="images/16x16/add2.png" title="{$smarty.const._NEWEMPLOYEE}" alt="{$smarty.const._NEWUSER}"/ border="0"></a></td><td><a href="{$smarty.session.s_type}.php?ctg=users&add_user=1">{$smarty.const._NEWEMPLOYEE}</a>
        {/if}
            </td>
        </tr>

    </table>

<!--ajax:usersTable-->
        <table style = "width:100%" class = "sortedTable" size = "{$T_EMPLOYEES_SIZE}" sortBy = "0" id = "usersTable" useAjax = "1" rowsPerPage = "20" url = "{$smarty.session.s_type}.php?ctg=users&">
        <tr class = "topTitle">
            <td class = "topTitle" name = "login">{$smarty.const._LOGIN}</td>
            <td class = "topTitle" name = "name">{$smarty.const._NAME}</td>
            <td class = "topTitle" name = "surname">{$smarty.const._SURNAME}</td>
            <td class = "topTitle" name = "languages_NAME">{$smarty.const._LANGUAGE}</td>
            <td class = "topTitle" name = "timestamp">{$smarty.const._REGISTRATIONDATE}</td>
            <td class = "topTitle centerAlign" name = "jobs_num">{$smarty.const._JOBSASSIGNED}</td>
            <td class = "topTitle noSort centerAlign">{$smarty.const._EMPLOYEEFORM}</td>
    {if $smarty.session.s_type == "administrator"}
            <td class = "topTitle centerAlign" name = "active">{$smarty.const._ACTIVE2}</td>
    	{if !isset($T_CURRENT_USER->coreAccess.statistics) || $T_CURRENT_USER->coreAccess.statistics != 'hidden'}
            <td class = "topTitle noSort centerAlign">{$smarty.const._STATISTICS}</td>
        {/if}
    {/if}
    {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
            <td class = "topTitle noSort centerAlign">{$smarty.const._OPERATIONS}</td>
    {/if}
        </tr>

        {foreach name = 'users_list' key = 'key' item = 'user' from = $T_EMPLOYEES}
        <tr id="row_{$user.login}" class = "{cycle values = "oddRowColor, evenRowColor"} {if !$user.active}deactivatedTableElement{/if}">
            <td id="column_{$user.login}">
                {if $user.active == 1}
                    <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$user.login}" class = "editLink">{$user.login}</a>
                {else}
                    {$user.login}
                {/if}
            </td>
    
            <td>{$user.name}</td>
            <td>{$user.surname}</td>
            <td>{$user.languages_NAME}</td>
            <td>#filter:timestamp-{$user.timestamp}#</td>
            <td class = "centerAlign">{$user.jobs_num}</td>
            <td class = "centerAlign">
                {if $user.login != $smarty.session.s_login && $user.user_type != 'administrator'}
                    <a href="{$smarty.session.s_type}.php?ctg=users&edit_user={$user.login}&print_preview=1" onclick = "eF_js_showDivPopup('{$smarty.const._EMPLOYEEFORMPRINTPREVIEW}', new Array('800px','500px'))" target = "POPUP_FRAME"><img src='images/16x16/form_blue.png' title=  '{$smarty.const._PRINTPREVIEW}' alt = '{$smarty.const._PRINTPREVIEW}' border='0' /></a>
                {else}
                    <img src='images/16x16/form_red.png' title=  '{$smarty.const._PRINTPREVIEW}' alt = '{$smarty.const._PRINTPREVIEW}' border='0' />
                {/if}
            </td>
        {if $smarty.session.s_type == "administrator"}
            <td class = "centerAlign">
            {if $user.login != $smarty.session.s_login}
                <a href = "javascript:void(0);" {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}onclick = "activate(this, '{$user.login}')"{/if}>
                {if $user.active == 1}
                    <img src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._DEACTIVATE}" title = "{$smarty.const._DEACTIVATE}" border = "0">
                {else}
                    <img src = "images/16x16/trafficlight_red.png" alt = "{$smarty.const._ACTIVATE}" title = "{$smarty.const._ACTIVATE}" border = "0">
                {/if}
                </a>
            {else}
                <img src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._ACTIVE}" title = "{$smarty.const._ACTIVE}" border = "0">
            {/if}
            </td>
	        {if !isset($T_CURRENT_USER->coreAccess.statistics) || $T_CURRENT_USER->coreAccess.statistics != 'hidden'}
            <td class = "centerAlign"><a href="{$smarty.session.s_type}.php?ctg=statistics&option=user&sel_user={$user.login}"><img border = "0" src = "images/16x16/chart.png" title = "{$smarty.const._STATISTICS}" alt = "{$smarty.const._STATISTICS}" /></a></td>
            {/if}
        {/if}
        {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
	        <td class = "centerAlign">
                <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$user.login}" class = "editLink"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>    
                <a href = "{$smarty.session.s_type}.php?ctg=users&op=users_data&delete_user={$user.login}" onclick = "return confirm('{$smarty.const._AREYOUSUREYOUWANTTOFIREEMPLOYEE}')" class = "deleteLink"><img border = "0" src = "images/16x16/delete.png" title = "{$smarty.const._FIRE}" alt = "{$smarty.const._FIRE}" /></a>
            </td>
        {/if}
        </tr>
        {/foreach}

    </table>
<!--/ajax:usersTable-->

    <script>
    {literal}
    function activate(el, user) {
        Element.extend(el);
        if (el.down().src.match('red')) {
            url = '{/literal}{$smarty.session.s_type}{literal}.php?ctg=users&activate_user='+user;
            newSource = 'images/16x16/trafficlight_green.png';
        } else {
            url = '{/literal}{$smarty.session.s_type}{literal}.php?ctg=users&deactivate_user='+user;
            newSource = 'images/16x16/trafficlight_red.png';
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
                new Effect.Appear(el.down(), {queue:'end'});

                if (el.down().src.match('green')) {
                    // When activated
                    $('column_'+user).innerHTML = '<a href = "{/literal}{$smarty.session.s_type}{literal}.php?ctg=users&edit_user='+user+'" class = "editLink">'+user+'</a>';

                    var cName = $('row_'+user).className.split(" ");
                    $('row_'+user).className = cName[0];
                } else {
                    $('column_'+user).innerHTML = user;
                    $('row_'+user).className += " deactivatedTableElement";
                }

                }
            });
    }
    {/literal}
    </script>


    {/capture}
    {* end of t_employees_code capture *}

    <tr>
        <td>
        {eF_template_printInnerTable title = $smarty.const._UPDATEEMPLOYEES data = $smarty.capture.t_employees_code image = '/32x32/user1.png'}
        </td>
    </tr>
{/if}

{* ****************** BRANCHES ************************** *}
{if $smarty.get.op == 'branches'}
    {if $smarty.get.add_branch || $smarty.get.edit_branch}
    <table width = "100%">
        <tr><td class = "topAlign" width = "50%">

        {***************************************************************
         This is the form that contains the branch's data
         ***************************************************************}
        {capture name = 't_branch_code'}
        {$T_BRANCH_FORM.javascript}

        <table width = "75%">
            <tr>
                <td width="70%">
                    <form {$T_BRANCH_FORM.attributes}>
                        {$T_BRANCH_FORM.hidden}

                        <table class = "formElements">
                            <tr>
                                <td class = "labelCell">{$T_BRANCH_FORM.branch_name.label}:&nbsp;</td>
                                <td>{$T_BRANCH_FORM.branch_name.html}</td>
                            </tr>

                            {if $T_BRANCH_FORM.branch_name.error}<tr><td></td><td class = "formError">{$T_BRANCH_FORM.branch_name.error}</td></tr>{/if}

                            <tr><td class = "labelCell">{$T_BRANCH_FORM.address.label}:&nbsp;</td><td>{$T_BRANCH_FORM.address.html}</td></tr>
                            <tr><td class = "labelCell">{$T_BRANCH_FORM.city.label}:&nbsp;</td><td>{$T_BRANCH_FORM.city.html}</td></tr>
                            <tr><td class = "labelCell">{$T_BRANCH_FORM.country.label}:&nbsp;</td><td>{$T_BRANCH_FORM.country.html}</td></tr>
                            <tr><td class = "labelCell">{$T_BRANCH_FORM.telephone.label}:&nbsp;</td><td>{$T_BRANCH_FORM.telephone.html}</td></tr>
                            <tr><td class = "labelCell">{$T_BRANCH_FORM.email.label}:&nbsp;</td><td>{$T_BRANCH_FORM.email.html}</td></tr>

                            {if $T_SHOWFATHER}
                            <tr><td class = "labelCell">{$T_BRANCH_FORM.fatherBranch.label}:&nbsp;</td>
                                <td>
                                    <table>
                                         <tr><td>{$T_BRANCH_FORM.fatherBranch.html}</td><td align="right"><a id="details_link" name="details_link" {$T_FATHER_BRANCH_INFO} {if ($T_FATHER_BRANCH_INFO == "") || ($T_FATHER_BRANCH_ID == 0) || ($smarty.get.add_branch == 1 && !isset($smarty.get.add_branch_to)) || isset($T_FORBID_LINK)}style="visibility:hidden"{/if}><img src="images/16x16/view.png" title="{$smarty.const._DETAILS}" alt="{$smarty.const.DETAILS}" border="0"></a></td></tr>
                                    </table>
                                </td>
                            </tr>
                            {/if}

                            {if $smarty.get.edit_branch}
                                {literal}
                                <script>
                                var branch_select = document.getElementById('fatherBranch');
                                for (i = 0; i < branch_select.options.length; i++) {
                                    // Select the correct father
                                    if (branch_select.options[i].value == {/literal}{$T_FATHER_BRANCH_ID}{literal}) {
                                         branch_select.options[i].selected = true;
                                    }

                                    // Disable yourself as parent
                                    if (branch_select.options[i].value == {/literal}{$smarty.get.edit_branch}{literal}) {
                                         branch_select.options[i].disabled = true;
                                    }
                                }
                                </script>
                                {/literal}
                            {/if}

                            <tr><td colspan = "2">&nbsp;</td></tr>

                            {* Only supervisors and administrators may change branch data - currently all - TODO: selected *}
                            <tr><td></td><td class = "submitCell" style = "text-align:left">{$T_BRANCH_FORM.submit_branch_details.html}</td></tr>
                        </table>
                    </form>
                </td>
            </tr>
        </table>

        {* For correct footer appearance *}
        {if $smarty.get.add_branch}
            </td>
            </tr>
            </table>
        {/if}
        {/capture}

        {if $smarty.get.edit_branch}
        {capture name = 't_employees_code'}
<!--ajax:branchUsersTable-->
        <table style = "width:100%" class = "sortedTable" size = "{$T_EMPLOYEES_SIZE}" sortBy = "0" id = "branchUsersTable" useAjax = "1" rowsPerPage = "20"  url = "{$smarty.session.s_type}.php?ctg=module_hcd&op=branches&edit_branch={$smarty.get.edit_branch}&">
            <tr class = "topTitle">
                <td class = "topTitle" name="login">{$smarty.const._LOGIN}</td>
                <td class = "topTitle" name="name">{$smarty.const._NAME}</td>
                <td class = "topTitle" name="surname">{$smarty.const._SURNAME}</td>
                <td class = "topTitle" name="description">{$smarty.const._JOBDESCRIPTION}</td>
                <td class = "topTitle" name="supervisor">{$smarty.const._EMPLOYEEPOSITION}</td>
                <td class = "topTitle noSort" align="center">{$smarty.const._OPERATIONS}</td>
            </tr>

        {foreach name = 'users_list' key = 'key' item = 'user' from = $T_EMPLOYEES}
            {if $user.branch_ID == $smarty.get.edit_branch}
                {assign var = "employees_found" value = '1'}
                <tr class = "{cycle values = "oddRowColor, evenRowColor"} {if !$user.active}deactivatedTableElement{/if}">
                    <td>
                    {if ($user.pending == 1)}
                        <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$user.login}" class = "editLink" style="color:red;">{$user.login}</a>
                    {elseif ($user.active == 1)}
                        <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$user.login}" class = "editLink">{$user.login}</a>
                    {else}
                        {$user.login}
                    {/if}
                    </td>

                    <td>{$user.name}</td>
                    <td>{$user.surname}</td>
                    <td><a href = "{$smarty.session.s_type}.php?ctg=module_hcd&op=job_descriptions&edit_job_description={$user.job_description_ID}" class = "editLink">{$user.description}</a></td>
                    <td>{if $user.supervisor == '1'}{$smarty.const._SUPERVISOR}{else}{$smarty.const._EMPLOYEE} {/if} </td>
                    <td align = "center">
                        <table>
                            <tr><td width="45%">
                                {if $user.active == 1}
                                    <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$user.login}" class = "editLink"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
                                {else}
                                    <img border = "0" src = "images/16x16/edit_gray.png" title = "{$smarty.const._UNPRIVILEGEDATTEMPT}" alt = "{$smarty.const._UNPRIVILEGEDATTEMPT}" />
                                {/if}

                                </td><td></td>
                                <td width="45%">
                                {if $user.login != $smarty.session.s_login}
                                    <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$user.login}&delete_job_employee={$user.login}&delete_job={$user.job_description_ID}&delete_job_at_branch={$smarty.get.edit_branch}&supervises_branch={$user.supervisor}&father_ID={$T_FATHER_BRANCH_ID}&tab=placements" onclick = "return confirm('{$smarty.const._AREYOUSUREYOUWANTTOCANCELJOB}')" class = "deleteLink"><img border = "0" src = "images/16x16/delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a>
                                {else}
                                    <img border = "0" src = "images/16x16/delete_gray.png" title = "{$smarty.const._UNPRIVILEGEDATTEMPT}" alt = "{$smarty.const._UNPRIVILEGEDATTEMPT}" />
                                {/if}
                                </td>
                            </tr>
                        </table>
                    </td>

                </tr>
            {/if}
        {/foreach}

        {if !$employees_found}
            <tr><td colspan="6" class = "emptyCategory" align="center">{$smarty.const._NOEMPLOYEESWORKATBRANCH}</td></tr>
        {/if}
        </table>
<!--/ajax:branchUsersTable-->

        {/capture}


        {capture name = 't_employees_to_branch'}
<!--ajax:branchJobsTable-->
                <table style = "width:100%" class = "sortedTable" size = "{$T_EMPLOYEES_SIZE}" sortBy = "0" id = "branchJobsTable" useAjax = "1" rowsPerPage = "20"  url = "{$smarty.session.s_type}.php?ctg=module_hcd&op=branches&edit_branch={$smarty.get.edit_branch}&">
                    <tr class = "topTitle">
                        <td class = "topTitle" name="login">{$smarty.const._LOGIN}</td>
                        <td class = "topTitle" name="name">{$smarty.const._NAME}</td>
                        <td class = "topTitle" name="surname">{$smarty.const._SURNAME}</td>
                        <td class = "topTitle" name="description">{$smarty.const._JOBDESCRIPTION}</td>
                        <td class = "topTitle" name="supervisor">{$smarty.const._EMPLOYEEPOSITION}</td>
                        <td class = "topTitle" name="branch_ID" align="center">{$smarty.const._CHECK}</td>
                    </tr>

                    {foreach name = 'users_list' key = 'key' item = 'user' from = $T_EMPLOYEES}
                    {if $user.active}
                        <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                            <td>
                            {if ($user.pending == 1)}
                                <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$user.login}" class = "editLink" style="color:red;">{$user.login}</a>
                            {elseif ($user.active == 1)}
                                <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$user.login}" class = "editLink">{$user.login}</a>
                            {else}
                                {$user.login}
                            {/if}
                            </td>

                            <td>{$user.name}</td>
                            <td>{$user.surname}</td>
                            <td><span style="display:none" id="none_job_row{$user.login}">{if $user.description}{$user.description}{else}_{/if}</span>{$user.job_select}</td>
                            <td><span style="display:none" id="none_position_row{$user.login}">{$user.supervisor}</span>{$user.position_select}</td>
                            <td align = "center">
                                <span style="display:none" id="none_check_row{$user.login}">{if $user.branch_ID == $smarty.get.edit_branch}1{else}0{/if}</span>
                                <input class = "inputCheckBox" type = "checkbox"
                                {if $user.login == $smarty.session.s_login}
                                    disabled = "true"
                                {/if} onclick="javascript:show_hide_job_selects('{$user.login}'); ajaxPost('row{$user.login}', this);" name = "check_{$user.login}" id = "check_row{$user.login}"
                                {if $user.branch_ID == $smarty.get.edit_branch}
                                 checked
                                {/if}
                                >
                            </td>
                        </tr>
                    {/if}
                    {foreachelse}
                        {if isset($T_NOBRANCHJOBSERROR)}
                            <tr><td colspan="6" class = "emptyCategory" align="center">{$smarty.const._NOBRANCHJOBSERROR}</td></tr>
                        {else}
                            <tr><td colspan="6" class = "emptyCategory" align="center">{$smarty.const._NOEMPLOYEESFOUND}</td></tr>
                        {/if}
                    {/foreach}

                    {if $smarty.const.MSIE_BROWSER == 1}
                    <img style="display:none" src="images/16x16/pens.png" onLoad="javascript:simulateJobSelects();" />
                    {/if}
                    </table>
<!--/ajax:branchJobsTable-->

            {* Script for posting ajax requests regarding skill to employees assignments *}
            {literal}
            <script>
            // Wrapper function for any of the 2-3 points where Ajax is used in the module personal
            function ajaxPost(id, el, table_id) {
                Element.extend(el);

                var baseUrl =  '{/literal}{$smarty.session.s_type}{literal}.php?ctg=module_hcd&op=branches&edit_branch={/literal}{$smarty.get.edit_branch}{literal}&postAjaxRequest=1';
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
                    var img_id   = 'img_selectAll';
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
                            onSuccess: function (transport) {

                                // Update all form tables
                                var tables = sortedTables.size();
                                var i;
                                for (i = 0; i < tables; i++) {
                                    if (sortedTables[i].id == 'branchUsersTable') {
                                        eF_js_rebuildTable(i, 0, 'null', 'desc');
                                    }
                                }

                                img.style.display = 'none';
                                img.setAttribute('src', 'images/16x16/check.png');
                                new Effect.Appear(img_id);

                                window.setTimeout('Effect.Fade("'+img_id+'")', 2500);

                            }
                        });

            }
            </script>
            {/literal}

        {/capture}


        {**Sub-Branches: moduleAllBranches: Show subbranches *}
        {capture name = 't_subbranches_code'}

            <table border = "0" >
                <tr><td>
                    {if $smarty.session.employee_type != _EMPLOYEE}
                        <a href="{$smarty.session.s_type}.php?ctg=module_hcd&op=branches&add_branch=1&add_branch_to={$smarty.get.edit_branch}"><img src="images/16x16/add2.png" title="{$smarty.const._NEWSUBBRANCH}" alt="{$smarty.const._NEWSUBBRANCH}"/ border="0"></a></td><td><a href="{$smarty.session.s_type}.php?ctg=module_hcd&op=branches&add_branch=1&add_branch_to={$smarty.get.edit_branch}">{$smarty.const._NEWSUBBRANCH}</a>
                    {/if}
                    </td>
                </tr>
            </table>

            <table border = "0" width = "100%" class = "sortedTable">
                <tr class = "topTitle">
                    <td class = "topTitle">{$smarty.const._BRANCHNAME}</td>
                    <td class = "topTitle">{$smarty.const._CITY}</td>
                    <td class = "topTitle">{$smarty.const._ADDRESS}</td>
                    <td class = "topTitle" align="center">{$smarty.const._EMPLOYEES}</td>
                    <td class = "topTitle noSort" align="center">{$smarty.const._OPERATIONS}</td>
                </tr>

        {if isset($T_SUBBRANCHES)}
            {foreach name = 'branch_list' key = 'key' item = 'branch' from = $T_SUBBRANCHES}
                <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                    <td><a href = "{$smarty.session.s_type}.php?ctg=module_hcd&op=branches&edit_branch={$branch.branch_ID}" class = "editLink">{$branch.name}</a></td>
                    <td> {$branch.city}</td>
                    <td> {$branch.address}</td>
                    <td align ="center">{$branch.employees}</td>
                    <td align = "center">
                    <table>
                        <tr><td width="45%">
                            <a href = "{$smarty.session.s_type}.php?ctg=module_hcd&op=branches&edit_branch={$branch.branch_ID}" class = "editLink"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
                            </td><td></td><td  width="45%">

                                <a href = "{$smarty.session.s_type}.php?ctg=module_hcd&op=branches&delete_branch={$branch.branch_ID}&father_ID={$branch.father_ID}" onclick = "return confirm('{$smarty.const._AREYOUSUREYOUWANTTODISMISSTHEBRANCH}')" class = "deleteLink"><img border = "0" src = "images/16x16/delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a>

                            </td>
                        </tr>
                    </table>
                    </td>
                </tr>
            {/foreach}
        {else}
            <tr><td colspan = 6>
                <table width = "100%">
                    <tr><td class = "emptyCategory" align="center">{$smarty.const._NOBRANCHESHAVEBEENREGISTERED}</td></tr>
                </table>
                </td>
            </tr>
        {/if}
        </table>

        {/capture}


        {*Show job_descriptions of this branch*}
        {capture name = 't_branch_jobs'}

            <table border = "0" >
                <tr><td>
                    <a href="{$smarty.session.s_type}.php?ctg=module_hcd&op=job_descriptions&add_job_description=1&add_to_branch={$smarty.get.edit_branch}"><img src="images/16x16/add2.png" title="{$smarty.const._NEWJOBDESCRIPTION}" alt="{$smarty.const._NEWJOBDESCRIPTION}"/ border="0"></a></td><td><a href="{$smarty.session.s_type}.php?ctg=module_hcd&op=job_descriptions&add_job_description=1&add_to_branch={$smarty.get.edit_branch}">{$smarty.const._NEWJOBDESCRIPTION}</a>
                    </td>
                </tr>
            </table>

            <table border = "0" width = "100%" class = "sortedTable">
                <tr class = "topTitle">
                    <td class = "topTitle" width="25%">{$smarty.const._JOBDESCRIPTION}</td>
                    <td class = "topTitle" align="center">{$smarty.const._CURRENTLYEMPLOYEED}</td>
                    <td class = "topTitle" align="center">{$smarty.const._VACANCIES}</td>
                    <td class = "topTitle" align="center">{$smarty.const._SKILLSREQUIRED}</td>
                    <td class = "topTitle noSort" align="center">{$smarty.const._OPERATIONS}</td>
                </tr>

       {if isset($T_JOB_DESCRIPTIONS)}
                {foreach name = 'job_description_list' key = 'key' item = 'job_description' from = $T_JOB_DESCRIPTIONS}
                <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                    <td><a href = "{$smarty.session.s_type}.php?ctg=module_hcd&op=job_descriptions&edit_job_description={$job_description.job_description_ID}" class = "editLink">{$job_description.description}</a></td>
                    <td align = "center"> {$job_description.Employees}</td>
                    <td align = "center"> {if $job_description.more_needed > 0}{$job_description.more_needed}{else}0{/if} </td>
                    <td align = "center"> {$job_description.skill_req}</td>
                    <td align = "center">
                        <table>
                            <tr>
                                <td width="45%">
                                    <a href = "{$smarty.session.s_type}.php?ctg=module_hcd&op=job_descriptions&edit_job_description={$job_description.job_description_ID}" class = "editLink"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a></td><td>
                                </td>
                                {*
                                <td  width="33%">
                                    <a href = "{$smarty.session.s_type}.php?ctg=module_hcd&op=job_descriptions&export_vacancies_for_job_description={$job_description.job_description_ID}" class = "editLink"><img border = "0" src = "images/16x16/note_pinned.png" title = "{$smarty.const._EXPORTVACANCIES}" alt = "{$smarty.const._EXPORTVACANCIES}" /></a>
                                </td>
                                *}
                                <td  width="45%">
                                    <a href = "{$smarty.session.s_type}.php?ctg=module_hcd&op=job_descriptions&delete_job_description={$job_description.job_description_ID}" onclick = "return confirm('{$smarty.const._AREYOUSUREYOUWANTTOREMOVETHATJOBDESCRIPTION}')" class = "deleteLink"><img border = "0" src = "images/16x16/delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                {/foreach}
       {else}
          <tr><td colspan = 6>
          <table width = "100%">
              <tr><td class = "emptyCategory" align="center">{$smarty.const._NOJOBDESCRIPTIONSHAVEBEENREGISTERED}</td></tr>
          </table>
          </td></tr>
       {/if}
            </table>

        </table>
        {/capture}

        {/if}

            {*  **************************************************************
                DISPLAYING THE CAPTURED TABLES
                **************************************************************    *}
                <table border = "0" width = "100%" cellspacing = "5">
                    <tr><td valign = "top">

                    {if $smarty.session.employee_type != _EMPLOYEE}
                        <div class="tabber">
                            <div class="tabbertab">
                                <h3>{$smarty.const._EDITBRANCH}</h3>
                                {if $smarty.get.edit_branch != ""}
                                    {eF_template_printInnerTable title = $smarty.const._BRANCHRECORD data = $smarty.capture.t_branch_code image = '/24x24/cube_yellow.png' options = $T_DELETE_LINK}
                                    {eF_template_printInnerTable title = $smarty.const._EMPLOYEES|cat:$smarty.const._ATBRANCH|cat:$T_BRANCH_NAME data = $smarty.capture.t_employees_code image = '/32x32/user1.png'}
                                {else}
                                    {eF_template_printInnerTable title = $smarty.const._NEWBRANCH data = $smarty.capture.t_branch_code image = '/24x24/cube_yellow.png'}
                                {/if}
                            </div>

                            {if $smarty.get.edit_branch}

                            <div class="tabbertab {if ($smarty.get.tab == "assign_employees"  || isset($smarty.post.employees_to_branches)) } tabbertabdefault {/if}">
                                <script>var myform = "branch_to_employees";</script>
                                <h3>{$smarty.const._ASSIGNEMPLOYEES}</h3>
                                {eF_template_printInnerTable title = $smarty.const._ASSIGNEMPLOYEESTOBRANCH|cat:$T_BRANCH_NAME data = $smarty.capture.t_employees_to_branch image = '/32x32/wrench.png'}
                            </div>
                            <div class="tabbertab {if ($smarty.get.tab == "subbranches")} tabbertabdefault {/if}">
                                <h3>{$smarty.const._SUBBRANCHES}</h3>
                                {eF_template_printInnerTable title = $smarty.const._SUBBRANCHES|cat:$smarty.const._OFBRANCH|cat:$T_BRANCH_NAME data = $smarty.capture.t_subbranches_code image = '/24x24/cube_yellow.png'}
                            </div>
                            <div class="tabbertab {if ($smarty.get.tab == "jobs")} tabbertabdefault {/if}">
                                <h3>{$smarty.const._JOBDESCRIPTIONS}</h3>
                                {eF_template_printInnerTable title = $smarty.const._JOBDESCRIPTIONS|cat:$smarty.const._OFBRANCH|cat:$T_BRANCH_NAME data = $smarty.capture.t_branch_jobs image = '/32x32/note.png'}
                            </div>
                            {/if}
                        </div>
                    {else}
                        {eF_template_printInnerTable title = $smarty.const._BRANCHRECORD data = $smarty.capture.t_branch_code image = '/24x24/cube_yellow.png'}
                    {/if}
                        </td>
                    </tr>
                </table>

            </td></tr>

    {else}
        {**moduleAllBranches: Show branches *}
        {capture name = 't_branches_code'}

            <table border = "0" >
                {* Only supervisors and administrators may change branch data - currently all - TODO: selected *}
                <tr><td>

                {if $smarty.session.employee_type != _EMPLOYEE}
                    <a href="{$smarty.session.s_type}.php?ctg=module_hcd&op=branches&add_branch=1"><img src="images/16x16/add2.png" title="{$smarty.const._NEWBRANCH}" alt="{$smarty.const._NEWBRANCH}"/ border="0"></a></td><td><a href="{$smarty.session.s_type}.php?ctg=module_hcd&op=branches&add_branch=1">{$smarty.const._NEWBRANCH}</a>
                {/if}

                    </td>
                </tr>
            </table>

<!--ajax:branchesTable-->
            <table style = "width:100%" class = "sortedTable" size = "{$T_BRANCHES_SIZE}"  sortBy = "0" id = "branchesTable" useAjax = "1" rowsPerPage = "20" url = "{$smarty.session.s_type}.php?ctg=module_hcd&op=branches&">
                <tr class = "topTitle">
                    <td class = "topTitle" name = "name">{$smarty.const._BRANCHNAME}</td>
                    <td class = "topTitle" name = "city">{$smarty.const._CITY}</td>
                    <td class = "topTitle" name = "address">{$smarty.const._ADDRESS}</td>
                    <td class = "topTitle" name = "employees" align="center">{$smarty.const._EMPLOYEES}</td>
                    <td class = "topTitle" name = "father_ID">{$smarty.const._FATHERBRANCHNAME}</td>
                    <td class = "topTitle noSort" name="operations" align="center">{$smarty.const._OPERATIONS}</td>
                </tr>

        {if isset($T_BRANCHES)}
            {foreach name = 'branch_list' key = 'key' item = 'branch' from = $T_BRANCHES}
                <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                    <td>
                    {if $smarty.session.s_type == "administrator" || $branch.supervisor == 1}
                        <a href = "{$smarty.session.s_type}.php?ctg=module_hcd&op=branches&edit_branch={$branch.branch_ID}" class = "editLink">{$branch.name}</a></td>
                    {else}
                        {$branch.name}
                    {/if}

                    <td> {$branch.city}</td>

                    <td> {$branch.address}</td>

                    <td align ="center">{$branch.employees}</td>

                    <td> {if $smarty.session.s_type == "administrator" || $branch.father_supervisor == 1}<a href = "{$smarty.session.s_type}.php?ctg=module_hcd&op=branches&edit_branch={$branch.father_ID}" class = "editLink">{$branch.father}{else}{$branch.father}{/if}</a></td>
                    <td align = "center">
                    <table>
                        <tr><td width="45%">
                            {if $smarty.session.s_type == "administrator" || $branch.supervisor == 1}
                                <a href = "{$smarty.session.s_type}.php?ctg=module_hcd&op=branches&edit_branch={$branch.branch_ID}" class = "editLink"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
                            {else}
                                <img border = "0" src = "images/16x16/edit_gray.png" title = "{$smarty.const._UNPRIVILEGEDATTEMPT}" alt = "{$smarty.const._UNPRIVILEGEDATTEMPT}" />
                            {/if}
                            </td><td></td><td  width="45%">
                            {if $smarty.session.s_type == "administrator" || $branch.supervisor == 1}
                                <a href = "{$smarty.session.s_type}.php?ctg=module_hcd&op=branches&delete_branch={$branch.branch_ID}&father_ID={$branch.father_ID}" onclick = "return confirm('{$smarty.const._AREYOUSUREYOUWANTTODISMISSTHEBRANCH}')" class = "deleteLink"><img border = "0" src = "images/16x16/delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a>
                            {else}
                                <img border = "0" src = "images/16x16/delete_gray.png" title = "{$smarty.const._UNPRIVILEGEDATTEMPT}" alt = "{$smarty.const._UNPRIVILEGEDATTEMPT}" />
                            {/if}
                            </td>
                        </tr>
                    </table>
                    </td>
                </tr>

            {/foreach}

       {else}
           <tr><td colspan = 6>
               <table width = "100%">
                   <tr><td class = "emptyCategory" align="center">{$smarty.const._NOBRANCHESHAVEBEENREGISTERED}</td></tr>
               </table>
               </td>
           </tr>
       {/if}
            </table>
<!--/ajax:branchesTable-->

        {/capture}
        {if $smarty.session.employee_type != _EMPLOYEE}
           {eF_template_printInnerTable title = $smarty.const._UPDATEBRANCHES data = $smarty.capture.t_branches_code image = '/24x24/cube_yellow.png'}
        {else}
           {eF_template_printInnerTable title = $smarty.const._VIEWBRANCHES data = $smarty.capture.t_branches_code image = '/24x24/cube_yellow.png'}
        {/if}

    {/if}
{/if}
{* ****************** SKILLS ************************** *}
{if $smarty.get.op == 'skills'}
    {assign var = "title" value = '<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?op=skills">'|cat:$smarty.const._SKILLS|cat:'</a>'}
    {if $smarty.get.add_skill || $smarty.get.edit_skill}
            {if $smarty.get.edit_skill != ""}
                {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd&op=skills&edit_skill='|cat:$smarty.get.edit_skill|cat:'">'|cat:$smarty.const._SKILLDATA|cat:'</a>'}
            {else}
                {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd&op=skills&add_skill=1">'|cat:$smarty.const._SKILLDATA|cat:'</a>'}
            {/if}

            <table width = "100%">
                <tr><td class = "topAlign" width = "50%">

 {* **************************************************************
    This is the form that contains the skill's data
    ************************************************************** *}
    {capture name = 't_skill_code'}
    {$T_SKILLS_FORM.javascript}
                 <table width = "75%">
                     <tr>
                         <td width="70%">
                              <form {$T_SKILLS_FORM.attributes}>
                              {$T_SKILLS_FORM.hidden}
                                  <table class = "formElements" width="55%">
                                      <tr>
                                          <td class = "labelCell">{$T_SKILLS_FORM.skill_description.label}:&nbsp;</td>
                                          <td colspan="4">{$T_SKILLS_FORM.skill_description.html}</td>
                                      </tr>
                                      {if $T_SKILLS_FORM.skill_description.error}<tr><td></td><td class = "formError">{$T_SKILLS_FORM.skill_description.error}</td></tr>{/if}

                                      <tr>
                                          <td class = "labelCell">{$T_SKILLS_FORM.category.label}:&nbsp;</td>
                                          <td>{$T_SKILLS_FORM.category.html}</td>
                                          <td><a href="{$smarty.session.s_type}.php?ctg=module_hcd&op=skill_cat&add_skill_cat=1" onclick = "eF_js_showDivPopup('{$smarty.const._ADDSKILLCATEGORY}', new Array('800px','500px'))" target = "POPUP_FRAME"><img src='images/16x16/add2.png' title=  '{$smarty.const._ADDSKILLCATEGORY}' alt = '{$smarty.const._ADDSKILLCATEGORY}' border='0' /></a></td>
                                              <td><a id = "edit_skill_cat" href="{$smarty.session.s_type}.php?ctg=module_hcd&op=skill_cat&edit_skill_cat={$T_DEFAULT_CATEGORY}" onclick = "eF_js_showDivPopup('{$smarty.const._EDITSKILLCATEGORY}', new Array('800px','500px'))" target = "POPUP_FRAME"  {if $T_DEFAULT_CATEGORY == ""}style="visibility:hidden"{/if}><img src='images/16x16/edit.png' title=  '{$smarty.const._EDITSKILLCATEGORY}' alt = '{$smarty.const._EDITSKILLCATEGORY}' border='0' /></a></td>
                                              <td><a id = "del_skill_cat" href="{$smarty.session.s_type}.php?ctg=module_hcd&op=skill_cat&del_skill_cat={$T_DEFAULT_CATEGORY}" onclick = "return confirm('{$smarty.const._AREYOUSUREYOUWANTTODISMISSTHISSKILLCATEGORY}')" {if $T_DEFAULT_CATEGORY == ""}style="visibility:hidden"{/if}><img src='images/16x16/delete.png' title=  '{$smarty.const._DELETESKILLCATEGORY}' alt = '{$smarty.const._DELETESKILLCATEGORY}' border='0' /></a></td>
                                          </td>
                                      </tr>



                                      <tr><td colspan = "2">&nbsp;</td></tr>

                                      <tr><td></td><td class = "submitCell" style = "text-align:left">
                                          {$T_SKILLS_FORM.submit_skill_details.html}</td>
                                      </tr>

                             </table>
                            </form>
                        </td>
                    </tr>
                </table>

                {/capture}




        {* **************************************************************
           This is the table with all employees having the skill
           ************************************************************** *}
                {if $smarty.get.edit_skill}
                {capture name = 't_employees_code'}

<!--ajax:usersSkillsTable-->
                <table style = "width:100%" class = "sortedTable" size = "{$T_EMPLOYEES_SIZE}" sortBy = "0" id = "usersSkillsTable" useAjax = "1" rowsPerPage = "20" url = "{$smarty.session.s_type}.php?ctg=module_hcd&op=skills&edit_skill={$smarty.get.edit_skill}&">
                    <tr class = "topTitle">
                        <td class = "topTitle" name="login">{$smarty.const._LOGIN}</td>
                        <td class = "topTitle" name="name">{$smarty.const._NAME}</td>
                        <td class = "topTitle" name="surname">{$smarty.const._SURNAME}</td>
                        <td class = "topTitle" name="specification">{$smarty.const._SPECIFICATION}</td>
                        <td class = "topTitle" name="stats" noSort align="center">{$smarty.const._STATISTICS}</td>
                        <td class = "topTitle noSort" name="ops" noSort align="center">{$smarty.const._OPERATIONS}</td>
                    </tr>

                {if isset($T_EMPLOYEES)}
                    {foreach name = 'users_list' key = 'key' item = 'user' from = $T_EMPLOYEES}
                    {if $user.skill_ID == $smarty.get.edit_skill}
                        {assign var = "employees_found" value = '1'}
                        <tr class = "{cycle values = "oddRowColor, evenRowColor"} {if !$user.active}deactivatedTableElement{/if}">
                            <td>
                            {if ($user.pending == 1)}
                                <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$user.login}" class = "editLink" style="color:red;">{$user.login}</a>
                            {elseif ($user.active == 1)}
                                <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$user.login}" class = "editLink">{$user.login}</a>
                            {else}
                                {$user.login}
                            {/if}
                             </td>

                            <td>{$user.name}</td>
                            <td>{$user.surname}</td>
                            <td>{$user.specification}</td>
                            <td align="center"><a href="{$smarty.session.s_type}.php?ctg=statistics&option=user&sel_user={$user.login}"><img border = "0" src = "images/16x16/chart.png" title = "{$smarty.const._STATISTICS}" alt = "{$smarty.const._STATISTICS}" /></a></td>
                            <td align = "center">
                                <table>
                                    <tr><td width="45%">
                                    {if $user.active == 1}
                                        <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$user.login}" class = "editLink"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
                                    {else}
                                        <img border = "0" src = "images/16x16/edit_gray.png" title = "{$smarty.const._UNPRIVILEGEDATTEMPT}" alt = "{$smarty.const._UNPRIVILEGEDATTEMPT}" />
                                    {/if}

                                    </td><td></td>
                                    <td width="45%">
                                        <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$user.login}&delete_skill={$smarty.get.edit_skill}&tab=skills" onclick = "return confirm('{$smarty.const._AREYOUSUREYOUWANTTOREMOVETHATSKILLFROMTHISEMPLOYEE}')" class = "deleteLink"><img border = "0" src = "images/16x16/delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a>
                                    </td></tr>
                                </table>
                            </td>
                        </tr>
                    {/if}
                    {/foreach}

                    {if !$employees_found}
                        <tr><td colspan=5>
                            <table width = "100%">
                                <tr><td class = "emptyCategory" align="center">{$smarty.const._NOEMPLOYEESPOSSESSSKILL}</td></tr>
                            </table>
                            </td>
                        </tr>
                    {/if}
                {else}
                   <tr><td colspan=6>
                   <table width = "100%">
                    <tr><td class = "emptyCategory" align="center">{$smarty.const._NOEMPLOYEESPOSSESSSKILL}</td></tr>
                   </table>
                   </td></tr>
                {/if}
                </table>
<!--/ajax:usersSkillsTable-->

                {/capture}

                {capture name = 't_employees_to_skill'}

                <form method="post" action="{$smarty.server.PHP_SELF}?ctg=module_hcd&op=skills&edit_skill={$smarty.get.edit_skill}"&tab="assign_employees">
<!--ajax:skillEmployeesTable-->
                <table style = "width:100%" class = "sortedTable" size = "{$T_EMPLOYEES_SIZE}" sortBy = "0" id = "skillEmployeesTable" useAjax = "1" rowsPerPage = "20" url = "{$smarty.session.s_type}.php?ctg=module_hcd&op=skills&edit_skill={$smarty.get.edit_skill}&show_all=1&">

                    <tr class = "topTitle">
                        <td class = "topTitle" name="users_login">{$smarty.const._LOGIN}</td>
                        <td class = "topTitle" name="name">{$smarty.const._NAME}</td>
                        <td class = "topTitle" name="surname">{$smarty.const._SURNAME}</td>
                        <td class = "topTitle" name="specification">{$smarty.const._SPECIFICATION}</td>
                        <td class = "topTitle" name="skill_ID" align="center">{$smarty.const._CHECK}</td>
                    </tr>

                {if isset($T_EMPLOYEES)}
                    {assign var = "employees_found" value = '0'}
                    {foreach name = 'users_list' key = 'key' item = 'user' from = $T_EMPLOYEES}
                    {if $user.active}
                    <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                        <td>
                        {if ($user.pending == 1)}
                            <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$user.login}" class = "editLink" style="color:red;">{$user.login}</a>
                        {elseif ($user.active == 1)}
                            <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$user.login}" class = "editLink">{$user.login}</a>
                        {else}
                            {$user.login}
                        {/if}
                        </td>
                        <td>{$user.name}</td>
                        <td>{$user.surname}</td>

                        <td><input class="inputText" width = "*" type="text" name="spec_skill_{$user.login}" id="spec_skill_{$user.login}" value="{$user.specification}" onchange="ajaxSkillUserPost(2,'{$user.login}', this);"
                        {if $user.skill_ID != $smarty.get.edit_skill}
                         style="visibility:hidden"
                        {/if}
                         >
                        </td>
                        <td align="center">
                            <input  type = "checkbox" class = "inputCheckBox" name = "{$user.login}" id="skill_to_{$user.login}" onclick="javascript:show_hide_spec('{$user.login}'); ajaxSkillUserPost(1,'{$user.login}', this);"
                            {if $user.skill_ID == $smarty.get.edit_skill}
                             checked
                            {/if}
                            >
                        </td>
                    </tr>
                    {/if}
                    {/foreach}
                    </table>
<!--/ajax:skillEmployeesTable-->

                    </form>

                {else}
                    <tr><td colspan=5>
                        <table width = "100%">
                            <tr><td class = "emptyCategory" align="center">{$smarty.const._NOEMPLOYEESFOUND}</td></tr>
                        </table>
                        </td>
                    </tr>
                </table>
<!--/ajax:skillEmployeesTable-->

                {/if}

                {/capture}

                {/if}

            {* Script for posting ajax requests regarding skill to employees assignments *}
            {literal}
            <script>
            // Wrapper function for any of the 2-3 points where Ajax is used in the module personal
            function ajaxPost(id, el, table_id) {
                table_id == 'skillEmployeesTable' ? ajaxSkillUserPost(1, id, el, table_id) : usersAjaxPost(id, el, table_id);
            }

            // type: 1 - inserting/deleting the skill to an employee | 2 - changing the specification
            // id: the users_login of the employee to get the skill
            // el: the element of the form corresponding to that skill/lesson
            // table_id: the id of the ajax-enabled table
            function ajaxSkillUserPost(type, id, el, table_id) {
                Element.extend(el);

                var baseUrl =  '{/literal}{$smarty.session.s_type}{literal}.php?ctg=module_hcd&op=skills&edit_skill={/literal}{$smarty.get.edit_skill}{literal}&postAjaxRequest=1';
                if (type == 1) {
                    if (id) {
                        var url = baseUrl + '&add_user=' + id + '&insert='+el.checked + '&specification='+document.getElementById('spec_skill_'+id).value;
                        var img_id   = 'img_'+ id;
                    } else if (table_id && table_id == 'skillEmployeesTable') {
                        el.checked ? url = baseUrl + '&addAll=1' : url = baseUrl + '&removeAll=1';
                        var img_id   = 'img_selectAll';
                    }
                } else if (type == 2) {
                    if (id) {
                        var url = baseUrl + '&add_user=' + id + '&insert=true&specification='+el.value;
                        var img_id   = 'img_'+ id;
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
                            onSuccess: function (transport) {
                                // Update all form tables
                                var tables = sortedTables.size();
                                var i;
                                for (i = 0; i < tables; i++) {
                                    if (sortedTables[i].id == 'usersSkillsTable') {
                                        eF_js_rebuildTable(i, 0, 'null', 'desc');
                                    }
                                }

                                img.style.display = 'none';
                                img.setAttribute('src', 'images/16x16/check.png');
                                new Effect.Appear(img_id);
                                window.setTimeout('Effect.Fade("'+img_id+'")', 2500);

                                }
                        });

            }
            </script>
            {/literal}

            {*  **************************************************************
                DISPLAYING THE CAPTURED TABLES
                ************************************************************** *}
                <table border = "0" width = "100%" cellspacing = "5">
                    <tr><td valign = "top">

                    <div class="tabber">
                        <div class="tabbertab">
                            <h3>{$smarty.const._EDITSKILL}</h3>
                            {if $smarty.get.edit_skill != ""}
                                {eF_template_printInnerTable title = $smarty.const._SKILLDATA data = $smarty.capture.t_skill_code image = '/32x32/wrench.png'}
                                {eF_template_printInnerTable title = $smarty.const._EMPLOYEES|cat:$smarty.const._HAVINGSKILL|cat:$T_SKILL_NAME data = $smarty.capture.t_employees_code image = '/32x32/user1.png'}
                            {else}
                                {eF_template_printInnerTable title = $smarty.const._NEWSKILL data = $smarty.capture.t_skill_code image = '/24x24/cube_yellow.png'}
                            {/if}
                        </div>

                        {if $smarty.get.edit_skill}
                   <script> var myform = "employees_to_skill";</script>
                        <div class="tabbertab {if ($smarty.get.tab == "assign_employees"  || isset($smarty.post.employees_to_skill)) } tabbertabdefault {/if}">
                            <h3>{$smarty.const._ASSIGNSKILL}</h3>
                            {eF_template_printInnerTable title = $smarty.const._ASSIGNEMPLOYEESTHESKILL|cat:$T_SKILL_NAME data = $smarty.capture.t_employees_to_skill image = '/32x32/wrench.png'}
                        </div>
                        {/if}
                     <div>

                        </td>
                   </tr>
                </table>

            </td></tr>
        </table>
    {else}
        {**moduleAllSkills: Show skills *}

        {capture name = 't_skills_code'}

            {if $smarty.session.s_type == "administrator"}
            <table border = "0" >
                <tr><td align ="left">
                    <a href="{$smarty.session.s_type}.php?ctg=module_hcd&op=skills&add_skill=1"><img src="images/16x16/add2.png" title="{$smarty.const._NEWSKILL}" alt="{$smarty.const._NEWSKILL}"/ border="0"></a></td><td><a href="{$smarty.session.s_type}.php?ctg=module_hcd&op=skills&add_skill=1">{$smarty.const._NEWSKILL}</a>
                    </td>
                </tr>
            </table>
            {/if}

            <table border = "0" width = "100%" class = "sortedTable">
                <tr class = "topTitle">
                    <td width = "35%" class = "topTitle">{$smarty.const._SKILLDESCRIPTION}</td>
                    <td width = "30%" class = "topTitle">{$smarty.const._SKILLCATEGORY}</td>
                    <td class = "topTitle" align="center">{$smarty.const._EMPLOYEESWITHTHATSKILL}</td>
                    <td class = "topTitle noSort" align="center">{$smarty.const._OPERATIONS}</td>
                </tr>

       {if isset($T_SKILLS)}
                {foreach name = 'skill_list' key = 'key' item = 'skill' from = $T_SKILLS}
                <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                    <td><a href = "{$smarty.session.s_type}.php?ctg=module_hcd&op=skills&edit_skill={$skill.skill_ID}" class = "editLink">{$skill.description}</a></td>
                    <td align = "left"> {$skill.category_description}</td>
                    <td align = "center"> {$skill.Employees}</td>
                    <td align = "center">
                        <table>
                            <tr>
                                <td width="45%">
                                    <a href = "{$smarty.session.s_type}.php?ctg=module_hcd&op=skills&edit_skill={$skill.skill_ID}" class = "editLink"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a></td><td>
                                </td>
                                <td  width="45%">
                                    <a href = "{$smarty.session.s_type}.php?ctg=module_hcd&op=skills&delete_skill={$skill.skill_ID}" onclick = "return confirm('{$smarty.const._AREYOUSUREYOUWANTTOREMOVETHATSKILL}')" class = "deleteLink"><img border = "0" src = "images/16x16/delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                {/foreach}
       {else}
          <tr><td colspan=4>
          <table width = "100%">
              <tr><td class = "emptyCategory" align="center">{$smarty.const._NOSKILLSHAVEBEENREGISTERED}</td></tr>
          </table>
          </td></tr>
       {/if}


   </table>
        {/capture}
        {eF_template_printInnerTable title = $smarty.const._UPDATESKILLS data = $smarty.capture.t_skills_code image = '/32x32/wrench.png'}
    {/if}
{/if}



{* ****************** CATEGORIES FOR SKILLS ************************** *}
{if $smarty.get.op == 'skill_cat'}
    {if $smarty.get.add_skill_cat || $smarty.get.edit_skill_cat}

 {* **************************************************************
    This is the form that contains the skill's data
    ************************************************************** *}
    {capture name = 't_skill_cat_code'}
    {$T_SKILL_CAT_FORM.javascript}
                 <table width = "75%">
                     <tr>
                         <td width="70%">
                              <form {$T_SKILL_CAT_FORM.attributes}>
                              {$T_SKILL_CAT_FORM.hidden}
                                  <table class = "formElements">
                                      <tr>
                                          <td class = "labelCell">{$T_SKILL_CAT_FORM.skill_cat_description.label}:&nbsp;</td>
                                          <td>{$T_SKILL_CAT_FORM.skill_cat_description.html}</td>
                                      </tr>
                                      {if $T_SKILL_CAT_FORM.skill_cat_description.error}<tr><td></td><td class = "formError">{$T_SKILLS_FORM.skill_description.error}</td></tr>{/if}

                                      <tr><td colspan = "2">&nbsp;</td></tr>

                                      <tr><td></td><td class = "submitCell" style = "text-align:left">
                                          {$T_SKILL_CAT_FORM.submit_skill_details.html}</td>
                                      </tr>

                                 </table>
                            </form>
                        </td>
                    </tr>
                </table>

    {/capture}
    {/if}
{eF_template_printInnerTable title = $smarty.const._UPDATESKILLSCATEGORY data = $smarty.capture.t_skill_cat_code image = '/32x32/wrench.png'}
{/if}

{* ********************* JOB DESCRIPTIONS ************************** *}
{if $smarty.get.op == 'job_descriptions'}
    {if $smarty.get.add_job_description || $smarty.get.edit_job_description}


    {* **************************************************************
       This is the form that contains the job_description's data
       ************************************************************** *}
    {capture name = 't_job_description_code'}
    {$T_JOB_DESCRIPTIONS_FORM.javascript}
                 <table width = "75%">
                     <tr>
                         <td width="70%">
                              <form {$T_JOB_DESCRIPTIONS_FORM.attributes}>
                              {$T_JOB_DESCRIPTIONS_FORM.hidden}
                                  <table class = "formElements">
                                      <tr>
                                          <td class = "labelCell">{$T_JOB_DESCRIPTIONS_FORM.job_description_name.label}:&nbsp;</td>
                                          <td>{$T_JOB_DESCRIPTIONS_FORM.job_description_name.html}</td>
                                      </tr>
                                      {if $T_JOB_DESCRIPTIONS_FORM.job_description_name.error}<tr><td></td><td class = "formError">{$T_JOB_DESCRIPTIONS_FORM.job_description_name.error}</td></tr>{/if}

                                      <tr><td class = "labelCell">{$T_JOB_DESCRIPTIONS_FORM.branch.label}:&nbsp;</td>
                                          <td>
                                              <table>
                                                   <tr><td>{$T_JOB_DESCRIPTIONS_FORM.branch.html}</td>
                                                       <td align="right"><a id="details_link" name="details_link" {$T_BRANCH_INFO} {if $T_BRANCH_INFO == "" || (isset($smarty.get.add_job_description) && !isset($smarty.get.add_to_branch))}style="visibility:hidden"{/if}><img src="images/16x16/view.png" title="{$smarty.const._DETAILS}" alt="{$smarty.const.DETAILS}" border="0"></a></td>
                                                   </tr>
                                              </table>
                                          </td>
                                      </tr>

                                      {if $smarty.get.edit_job_description}
                                          {literal}
                                          <script>
                                          var branch_select = document.getElementById('branch');
                                          for (i = 0; i < branch_select.options.length; i++) {
                                              if (branch_select.options[i].value == {/literal}{$T_BRANCH_ID}{literal}) {
                                                   branch_select.options[i].selected = true;
                                                   break;
                                              }
                                          }
                                          </script>
                                          {/literal}
                                      {/if}

                                      <tr>
                                          <td class = "labelCell">{$T_JOB_DESCRIPTIONS_FORM.job_role_description.label}:&nbsp;</td>
                                          <td>{$T_JOB_DESCRIPTIONS_FORM.job_role_description.html}</td>
                                      </tr>

                                      <tr>
                                          <td class = "labelCell">{$T_JOB_DESCRIPTIONS_FORM.placements.label}:&nbsp;</td>
                                          <td>{$T_JOB_DESCRIPTIONS_FORM.placements.html}</td>
                                      </tr>
                                      {* {if $T_JOB_DESCRIPTIONS_FORM.placements.error}<tr><td></td><td class = "formError">{$T_JOB_DESCRIPTIONS_FORM.placements.error}</td></tr>{/if}  *}

                                      <tr><td colspan = "2">&nbsp;</td></tr>

                                      <tr><td></td><td class = "submitCell" style = "text-align:left">
                                          {$T_JOB_DESCRIPTIONS_FORM.submit_job_description_details.html}</td>
                                      </tr>

                             </table>
                            </form>
                        </td>
                    </tr>
                </table>
                {/capture}

               {***************************************************************
                This is the table with all employees having the job_description
                ************************************************************** *}
                {if $smarty.get.edit_job_description}
                {capture name = 't_employees_code'}

                <table border = "0" width = "100%" class = "sortedTable" sortBy = "0">
                    <tr class = "topTitle">
                        <td class = "topTitle">{$smarty.const._LOGIN}</td>
                        <td class = "topTitle">{$smarty.const._NAME}</td>
                        <td class = "topTitle">{$smarty.const._SURNAME}</td>
                        {*<td class = "topTitle">{$smarty.const._EMPLOYEEPOSITION}</td>*}
                        <td class = "topTitle noSort" align="center">{$smarty.const._OPERATIONS}</td>
                    </tr>

                {if isset($T_EMPLOYEES)}
                    {foreach name = 'users_list' key = 'key' item = 'user' from = $T_EMPLOYEES}
                    <tr class = "{cycle values = "oddRowColor, evenRowColor"} {if !$user.active}deactivatedTableElement{/if}">
                        <td>
                        {if ($user.pending == 1)}
                            <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$user.login}" class = "editLink" style="color:red;">{$user.login}</a>
                        {elseif ($user.active == 1)}
                            <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$user.login}" class = "editLink">{$user.login}</a>
                        {else}
                            {$user.login}
                        {/if}
                         </td>

                        <td>{$user.name}</td>
                        <td>{$user.surname}</td>
                        {*<td>{if $user.supervisor == '1'}{$smarty.const._SUPERVISOR}{else}{$smarty.const._EMPLOYEE} {/if} </td>*}

                        <td align = "center">
                            <table>
                                <tr><td width="45%">
                                {if $user.active == 1}
                                    <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$user.login}&tab=placements" class = "editLink"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
                                {else}
                                    <img border = "0" src = "images/16x16/edit_gray.png" title = "{$smarty.const._UNPRIVILEGEDATTEMPT}" alt = "{$smarty.const._UNPRIVILEGEDATTEMPT}" />
                                {/if}

                                </td><td></td><td  width="45%">
                                    <a href = "{$smarty.session.s_type}.php?ctg=users&op=users_data&edit_user={$user.login}&delete_job={$smarty.get.edit_job_description}&tab=placements" onclick = "return confirm('{$smarty.const._AREYOUSUREYOUWANTTOCANCELJOB}')" class = "deleteLink"><img border = "0" src = "images/16x16/delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a>
                                </td></tr>
                            </table>
                        </td>
                    </tr>
                    {/foreach}
                {else}
                   <tr><td colspan=6>
                   <table width = "100%">
                    <tr><td class = "emptyCategory" align="center">{$smarty.const._NOEMPLOYEESPOSSESSJOBDESCRIPTION}</td></tr>
                   </table>
                   </td></tr>
                {/if}
                </table>

                {/capture}
                {/if}

            {*  ****************************************************
                This is the form that contains the job's required skills
                **************************************************** *}

                {capture name = 't_job_to_skills'}
                <form method="post" action="{$smarty.server.PHP_SELF}?ctg=module_hcd&op=job_descriptions&edit_job_description={$smarty.get.edit_job_description}">

                {if $smarty.get.edit_job_description != ""}
                <table width="100%">
                    <tr>
                        {if $smarty.session.s_type == "administrator"}
                        <td align="left">
                            <table><tr><td>
                            <a href="{$smarty.server.PHP_SELF}?ctg=module_hcd&op=skills&add_skill=1"><img src="images/16x16/add2.png" title="{$smarty.const._NEWSKILL}" alt="{$smarty.const._NEWSKILL}"/ border="0"></a></td><td><a href="{$smarty.server.PHP_SELF}?ctg=module_hcd&op=skills&add_skill=1">{$smarty.const._NEWSKILL}</a>
                            </td></tr></table>
                        </td>
                        {/if}
                        {if $smarty.session.s_type == "administrator" || $smarty.session.employee_type == $smarty.const._SUPERVISOR}
                        <td align ="right">

                            <table><tr><td>{$smarty.const._APPLYTOALLDESCRIPTIONSWITHDESCRIPTION|cat:$T_JOB_DESCRIPTION_NAME}</td>
                                       <td><input class = "inputCheckBox" type = "checkbox" id="skill_changes_apply_to" name = "skill_changes_apply_to"></td>
                                   </tr>
                            </table>
                        </td>
                        {/if}
                    </tr>
                </table>
                {/if}

<!--ajax:skillsTable-->
                <table style = "width:100%" class = "sortedTable" size = "{$T_SKILLS_SIZE}" sortBy = "0" id = "skillsTable" useAjax = "1" rowsPerPage = "20" url = "{$smarty.server.PHP_SELF}?ctg=module_hcd&op=job_descriptions&edit_job_description={$smarty.get.edit_job_description}&tab=skills&">
                    <tr class = "topTitle">
                        <td class = "topTitle" name="description">{$smarty.const._SKILL}</td>
                        <td class = "topTitle" name="job_description_ID" align="center">{$smarty.const._CHECK}</td>
                    </tr>

            {if isset($T_SKILLS)}
                    {foreach name = 'skill_list' key = 'key' item = 'skill' from = $T_SKILLS}
                    <tr class = "{cycle values = 'oddRowColor, evenRowColor'}">
                        <td>
                            {if $smarty.session.s_type == "administrator"}
                                <a href="{$smarty.server.PHP_SELF}?ctg=module_hcd&op=skills&edit_skill={$skill.skill_ID}">{$skill.description}</a>
                            {else}
                                {$skill.description}
                            {/if}
                        </td>
                        <td align="center">
                            <input class = "inputCheckBox" type = "checkbox" id="skill_{$skill.skill_ID}" name = "skill" onclick = "ajaxPost('{$skill.skill_ID}', this);"
                            {if $skill.job_description_ID == $smarty.get.edit_job_description}
                             checked
                            {/if}
                            >
                        </td>

                    </tr>
                    {/foreach}

                </table>
<!--/ajax:skillsTable-->

            {else}
                    <tr><td colspan=2>
                        <table width = "100%">
                            <tr><td class = "emptyCategory centerAlign">{$smarty.const._NOSKILLSREGISTEREDASPREREQUISITES}</td></tr>
                        </table>
                        </td>
                    </tr>
                </table>
<!--/ajax:skillsTable-->

            {/if}
            </form>

            {/capture}


            {if $smarty.session.s_type == "administrator"}
                {capture name = 't_job_to_lessons'}
                    <table width="100%">
                        <tr><td align ="right">
                            <table><tr><td>{$smarty.const._APPLYTOALLDESCRIPTIONSWITHDESCRIPTION|cat:$T_JOB_DESCRIPTION_NAME}</td>
                                       <td><input class = "inputCheckBox" type = "checkbox" id="lesson_changes_apply_to" name = "lesson_changes_apply_to"></td>
                                   </tr>
                            </table>
                        </td>
                        </tr>
                    </table>
<!--ajax:lessonsTable-->

                                                    <table style = "width:100%" class = "sortedTable" size = "{$T_LESSONS_SIZE}" sortBy = "0" id = "lessonsTable" useAjax = "1" rowsPerPage = "20" url = "administrator.php?ctg=module_hcd&op=job_descriptions&edit_job_description={$smarty.get.edit_job_description}&tab=lessons&">
                                                        <tr class = "topTitle">
                                                            <td class = "topTitle" name = "name">{$smarty.const._NAME} </td>
                                                            <td class = "topTitle" name = "direction_name">{$smarty.const._DIRECTION}</td>
                                                            <td class = "topTitle" name = "languages_NAME">{$smarty.const._LANGUAGE}</td>

                                                        {* MODULE HCD: Prices are replaced by the number of skills offered *}
                                                        {if $T_MODULE_HCD_INTERFACE}
                                                            <td class = "topTitle" name = "skills_offered" align ="center">{$smarty.const._SKILLSOFFERED}</td>
                                                        {else}
                                                            <td class = "topTitle" name = "price">{$smarty.const._PRICE}</td>
                                                        {/if}

                                                            <td class = "topTitle" name = "job_description_ID" style = "text-align:center">{$smarty.const._CHECK}</td>
                                                        </tr>

                                        {foreach name = 'lessons_list2' key = 'key' item = 'lesson' from = $T_LESSONS_DATA}
                                                        <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                                                            <td>
                                                {if ($lesson.info)}
                                                                <a href = "{$smarty.server.PHP_SELF}?ctg=lessons&edit_lesson={$lesson.id}" class = "info nonEmptyLesson">
                                                                    {$lesson.name}
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
                                                {else}
                                                                <a href = "{$smarty.server.PHP_SELF}?ctg=lessons&edit_lesson={$lesson.id}" class = "editLink">{$lesson.name}</a>
                                                {/if}
                                                            </td>
                                                            <td>{$lesson.direction_name}</td>
                                                            <td>{$lesson.languages_NAME}</td>

                                                        {* MODULE HCD: Prices are replaced by the number of skills offered *}
                                                         <td align ="center">{if $lesson.skills_offered == 0}{$smarty.const._NOSKILLSOFFERED}{else}{$lesson.skills_offered}{/if}</td>

                                                        <td align="center">
                                                            <input class = "inputCheckBox" type = "checkbox" id="lesson_{$lesson.id}" name = "lesson" onclick = "ajaxPost('{$lesson.id}', this);"
                                                            {if $lesson.job_description_ID == $smarty.get.edit_job_description}
                                                             checked
                                                            {/if}
                                                            >
                                                        </td>
                                                        </tr>
                                        {foreachelse}
                                                    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory centerAlign" colspan = "100%">{$smarty.const._NOLESSONSFOUND}</td></tr>
                                        {/foreach}
                                                    </table>
<!--/ajax:lessonsTable-->

                {/capture}


                {capture name = 't_job_to_courses'}
                    <table width="100%">
                        <tr><td align ="right">
                            <table><tr><td>{$smarty.const._APPLYTOALLDESCRIPTIONSWITHDESCRIPTION|cat:$T_JOB_DESCRIPTION_NAME}</td>
                                       <td><input class = "inputCheckBox" type = "checkbox" id="course_changes_apply_to" name = "course_changes_apply_to"></td>
                                   </tr>
                            </table>
                        </td>
                        </tr>
                    </table>
<!--ajax:coursesTable-->

                                                    <table style = "width:100%" class = "sortedTable" size = "{$T_COURSES_SIZE}" sortBy = "0" id = "coursesTable" useAjax = "1" rowsPerPage = "20" url = "administrator.php?ctg=module_hcd&op=job_descriptions&edit_job_description={$smarty.get.edit_job_description}&tab=courses&">
                                                        <tr class = "topTitle">
                                                            <td class = "topTitle" name = "name">{$smarty.const._NAME} </td>
                                                            <td class = "topTitle" name = "direction_name">{$smarty.const._DIRECTION}</td>
                                                            <td class = "topTitle" name = "languages_NAME">{$smarty.const._LANGUAGE}</td>

                                                        {* MODULE HCD: Prices are replaced by the number of skills offered *}
                                                        {if $T_MODULE_HCD_INTERFACE}
                                                            <td class = "topTitle" name = "skills_offered" align ="center">{$smarty.const._SKILLSOFFERED}</td>
                                                        {else}
                                                            <td class = "topTitle" name = "price">{$smarty.const._PRICE}</td>
                                                        {/if}

                                                            <td class = "topTitle" name = "job_description_ID" style = "text-align:center">{$smarty.const._CHECK}</td>
                                                        </tr>

                                        {foreach name = 'courses_list2' key = 'key' item = 'course' from = $T_COURSES_DATA}
                                                        <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                                                            <td>
                                                {if ($course.info)}
                                                                <a href = {if $course.active == 1}"{$smarty.server.PHP_SELF}?ctg=courses&edit_course={$course.id}"{else}"javascript:void(0)"{/if} class = "info nonEmptyCourse">
                                                                    {$course.name}
                                                                    <img class = "tooltip" border = "0" src="images/others/tooltip_arrow.gif"/>
                                                                    <span class="tooltipSpan">
                                                                        {if isset($course.info.general_description)}<strong>{$smarty.const._GENERALDESCRIPTION|cat:'</strong>:&nbsp;'|cat:$course.info.general_description}<br/>{/if}
                                                                        {if isset($course.info.assessment)}         <strong>{$smarty.const._ASSESSMENT|cat:'</strong>:&nbsp;'|cat:$course.info.assessment}<br/>                 {/if}
                                                                        {if isset($course.info.objectives)}         <strong>{$smarty.const._OBJECTIVES|cat:'</strong>:&nbsp;'|cat:$course.info.objectives}<br/>                 {/if}
                                                                        {if isset($course.info.course_topics)}      <strong>{$smarty.const._COURSETOPICS|cat:'</strong>:&nbsp;'|cat:$course.info.course_topics}<br/>            {/if}
                                                                        {if isset($course.info.resources)}          <strong>{$smarty.const._RESOURCES|cat:'</strong>:&nbsp;'|cat:$course.info.resources}<br/>                   {/if}
                                                                        {if isset($course.info.other_info)}         <strong>{$smarty.const._OTHERINFO|cat:'</strong>:&nbsp;'|cat:$course.info.other_info}<br/>                  {/if}
                                                                    </span>
                                                                </a>
                                                {else}
                                                                {if $course.active == 1}<a href = "{$smarty.server.PHP_SELF}?ctg=courses&edit_course={$course.id}" class = "editLink">{$course.name}</a>{else}{$course.name}{/if}
                                                {/if}
                                                            </td>
                                                            <td>{$course.direction_name}</td>
                                                            <td>{$course.languages_NAME}</td>

                                                        {* MODULE HCD: Prices are replaced by the number of skills offered *}
                                                         <td align ="center">{if $course.skills_offered == 0}{$smarty.const._NOSKILLSOFFERED}{else}{$course.skills_offered}{/if}</td>

                                                        <td align="center">
                                                            <input class = "inputCheckBox" type = "checkbox" id="course_{$course.id}" name = "course" onclick = "ajaxPost('{$course.id}', this);"
                                                            {if $course.job_description_ID == $smarty.get.edit_job_description}
                                                             checked
                                                            {/if}
                                                            >
                                                        </td>
                                                        </tr>
                                        {foreachelse}
                                                    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory centerAlign" colspan = "100%">{$smarty.const._NOCOURSESFOUND}</td></tr>
                                        {/foreach}
                                                    </table>
<!--/ajax:coursesTable-->

                {/capture}
            {/if}

            {* Script for posting ajax requests regarding skills and lessons assignments *}
            {literal}
            <script>
            // id: the skill or lessons id
            // el: the element of the form corresponding to that skill/lesson
            // table_id: the id of the ajax-enabled table
            function ajaxPost(id, el, table_id) {
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
                    var baseUrl =  '{/literal}{$smarty.session.s_type}{literal}.php?ctg=module_hcd&op=job_descriptions&edit_job_description={/literal}{$smarty.get.edit_job_description}{literal}&postAjaxRequest=1&'+type+'=1&apply_to_all_jd=' + document.getElementById(type + '_changes_apply_to').checked;

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
                                onSuccess: function (transport) {
                                    img.style.display = 'none';
                                    img.setAttribute('src', 'images/16x16/check.png');
                                    new Effect.Appear(img_id);
                                    window.setTimeout('Effect.Fade("'+img_id+'")', 2500);
                                    }
                            });
                } else {
                    return false;
                }

            }
            </script>
            {/literal}

             {* **************************************************************
                DISPLAYING THE CAPTURED TABLES
                **************************************************************    *}
                <table border = "0" width = "100%" cellspacing = "5">
                    <tr><td valign = "top">

                    <div class="tabber">
                        <div class="tabbertab">
                            <h3>{$smarty.const._EDITJOBDESCRIPTION}</h3>
                            {if $smarty.get.edit_job_description != ""}
                                {eF_template_printInnerTable title = $smarty.const._JOBDESCRIPTIONDATA data = $smarty.capture.t_job_description_code image = '/32x32/note.png'}
                                {eF_template_printInnerTable title = $smarty.const._EMPLOYEES|cat:$smarty.const._HAVINGJOBDESCRIPTION|cat:$T_JOB_DESCRIPTION_NAME data = $smarty.capture.t_employees_code image = '/32x32/user1.png'}
                            {else}
                                {eF_template_printInnerTable title = $smarty.const._NEWJOBDESCRIPTION data = $smarty.capture.t_job_description_code image = '/32x32/note.png'}
                            {/if}
                        </div>

                        {if $smarty.get.edit_job_description}
                        <div class="tabbertab {if ($smarty.get.tab == "skills"  || isset($smarty.post.job_to_skills)) } tabbertabdefault {/if}">
                            <h3>{$smarty.const._SKILLSREQUIRED}</h3>
                            {eF_template_printInnerTable title = $smarty.const._SKILLS data = $smarty.capture.t_job_to_skills image = '/32x32/wrench.png'}
                        </div>

                            {if $smarty.session.s_type == "administrator"}
                                <div class="tabbertab {if ($smarty.get.tab == "lessons"  || isset($smarty.post.job_to_lessons)) } tabbertabdefault {/if}">
                                    <h3>{$smarty.const._ASSOCIATEDLESSONS}</h3>
                                    {eF_template_printInnerTable title = $smarty.const._LESSONS data = $smarty.capture.t_job_to_lessons image = '/32x32/board.png'}
                                </div>

                                <div class="tabbertab {if ($smarty.get.tab == "courses"  || isset($smarty.post.job_to_courses)) } tabbertabdefault {/if}">
                                    <h3>{$smarty.const._ASSOCIATEDCOURSES}</h3>
                                    {eF_template_printInnerTable title = $smarty.const._COURSES data = $smarty.capture.t_job_to_courses image = '/32x32/books.png'}
                                </div>
                            {/if}
                        {/if}
                     </div>

                        </td>
                   </tr>
                </table>
    {else}
        {**moduleAllSkills: Show job_descriptions *}
        {capture name = 't_job_descriptions_code'}
            <table border = "0" >
                <tr><td>
                    <a href="{$smarty.session.s_type}.php?ctg=module_hcd&op=job_descriptions&add_job_description=1"><img src="images/16x16/add2.png" title="{$smarty.const._NEWJOBDESCRIPTION}" alt="{$smarty.const._NEWJOBDESCRIPTION}"/ border="0"></a></td><td><a href="{$smarty.session.s_type}.php?ctg=module_hcd&op=job_descriptions&add_job_description=1">{$smarty.const._NEWJOBDESCRIPTION}</a>
                    </td>
                </tr>
            </table>

<!--ajax:jobsTable-->
            <table style = "width:100%" class = "sortedTable" size = "{$T_JOB_DESCRIPTIONS_SIZE}" sortBy = "0" id = "jobsTable" useAjax = "1" rowsPerPage = "20" url = "{$smarty.session.s_type}.php?ctg=module_hcd&op=job_descriptions&">
                <tr class = "topTitle">
                    <td class = "topTitle" name = "description" width="25%">{$smarty.const._JOBDESCRIPTION}</td>
                    <td class = "topTitle" name = "name">{$smarty.const._BRANCHNAME}</td>
                    <td class = "topTitle" name = "Employees" align="center">{$smarty.const._CURRENTLYEMPLOYEED}</td>
                    <td class = "topTitle" name = "more_needed" align="center">{$smarty.const._VACANCIES}</td>
                    <td class = "topTitle" name = "skill_req" align="center">{$smarty.const._SKILLSREQUIRED}</td>
                    <td class = "topTitle noSort" align="center">{$smarty.const._OPERATIONS}</td>
                </tr>

       {if isset($T_JOB_DESCRIPTIONS)}
                {foreach name = 'job_description_list' key = 'key' item = 'job_description' from = $T_JOB_DESCRIPTIONS}
                <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                    <td><a href = "{$smarty.session.s_type}.php?ctg=module_hcd&op=job_descriptions&edit_job_description={$job_description.job_description_ID}" class = "editLink">{$job_description.description}</a></td>
                    <td><a href = "{$smarty.session.s_type}.php?ctg=module_hcd&op=branches&edit_branch={$job_description.branch_ID}" class = "editLink">{$job_description.name}</a></td>
                    <td align = "center"> {$job_description.Employees}</td>
                    <td align = "center"> {if $job_description.more_needed > 0}{$job_description.more_needed}{else}0{/if} </td>
                    <td align = "center"> {$job_description.skill_req}</td>
                    <td align = "center">
                        <table>
                            <tr>
                                <td width="45%">
                                    <a href = "{$smarty.session.s_type}.php?ctg=module_hcd&op=job_descriptions&edit_job_description={$job_description.job_description_ID}" class = "editLink"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a></td><td>
                                </td>
                                {*
                                <td  width="33%">
                                    <a href = "{$smarty.session.s_type}.php?ctg=module_hcd&op=job_descriptions&export_vacancies_for_job_description={$job_description.job_description_ID}" class = "editLink"><img border = "0" src = "images/16x16/note_pinned.png" title = "{$smarty.const._EXPORTVACANCIES}" alt = "{$smarty.const._EXPORTVACANCIES}" /></a>
                                </td>
                                *}
                                <td width="45%">
                                    <a href = "{$smarty.session.s_type}.php?ctg=module_hcd&op=job_descriptions&delete_job_description={$job_description.job_description_ID}" onclick = "return confirm('{$smarty.const._AREYOUSUREYOUWANTTOREMOVETHATJOBDESCRIPTION}')" class = "deleteLink"><img border = "0" src = "images/16x16/delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                {/foreach}
       {else}
          <tr><td colspan = 6>
          <table width = "100%">
              <tr><td class = "emptyCategory" align="center">{$smarty.const._NOJOBDESCRIPTIONSHAVEBEENREGISTERED}</td></tr>
          </table>
          </td></tr>
       {/if}
            </table>
<!--/ajax:jobsTable-->
        {/capture}
        {eF_template_printInnerTable title = $smarty.const._UPDATEJOBDESCRIPTIONS data = $smarty.capture.t_job_descriptions_code image = '/32x32/note.png'}
    {/if}
{/if}


{* ****************** REPORTS ************************** *}
{if $smarty.get.op == 'reports'}
    {**moduleReports: Show employees satisfying some criteria *}
       <tr><td class = "moduleCell">


       {capture name = 't_reports_code'}
            <table class = "formElements">
            <tr><td>{$T_REPORT_FORM.criteria.all_criteria.html}   </td><td>{$smarty.const._SATISFYALLCRITERIA}</td></tr>
            <tr><td>{$T_REPORT_FORM.criteria.any_criteria.html}   </td><td>{$smarty.const._SATISFYANYCRITERIA}</td></tr>
                {if isset($smarty.get.all) && $smarty.get.all=="false" }
                    {literal}
                    <script>document.getElementById('any_criteria').checked = true;</script>
                    {/literal}
                {else}
                    {literal}
                    <script>document.getElementById('all_criteria').checked = true;</script>
                    {/literal}
                {/if}
            <tr><td>&nbsp;</td></tr>
            <table>
            <br>

            <table><!--style="visibility:hidden"-->
            <tr><td>{$T_REPORT_FORM.search_branch.label}:&nbsp;</td><td>{$T_REPORT_FORM.search_branch.html}</td><td>{$T_REPORT_FORM.include_subbranches.html}</td><td id="include_subbranches_label">({$T_REPORT_FORM.include_subbranches.label})</td></tr>
                {if isset($smarty.get.branch_ID) && $smarty.get.branch_ID != 0}
                    {literal}
                    <script>
                    var branch_select = document.getElementById('search_branch');
                    for (i = 0; i < branch_select.options.length; i++) {
                        if (branch_select.options[i].value == {/literal}{$smarty.get.branch_ID}{literal}) {
                             branch_select.options[i].selected = true;
                             break;
                        }
                    }
                    </script>
                    {/literal}
                {/if}
            <tr><td>{$T_REPORT_FORM.search_job_description.label}:&nbsp;</td><td>{$T_REPORT_FORM.search_job_description.html}</td></tr>
                {if isset($smarty.get.job_description_ID) && $smarty.get.job_description_ID != ""}
                    {literal}
                    <script>
                    var a;
                    var job_description_select = document.getElementById('search_job_description');
                    for (i = 0; i < job_description_select.options.length; i++) {
                        a = new String("{/literal}{$smarty.get.job_description_ID}{literal}");
                        if (job_description_select.options[i].value.toString() == a) {
                             job_description_select.options[i].selected = true;
                        } else if (job_description_select.options[i].value == "__emptybranch_name" || job_description_select.options[i].value == "__emptyother_branch") {
                            job_description_select.options[i].disabled = true;
                        }
                    }
                    </script>
                    {/literal}
                {/if}

            <tr><td>{$T_REPORT_FORM.search_skill.label}:&nbsp;</td><td>{$T_REPORT_FORM.search_skill.html}</td></tr>
                {if isset($smarty.get.skill_ID) && $smarty.get.skill_ID != 0}
                    {literal}
                    <script>
                    var skill_select = document.getElementById('search_skill');
                    for (i = 0; i < skill_select.options.length; i++) {
                        if (skill_select.options[i].value == {/literal}{$smarty.get.skill_ID}{literal}) {
                             skill_select.options[i].selected = true;
                             break;
                        }
                    }
                    </script>
                    {/literal}
                {/if}
            <tr><td>&nbsp;</td></tr>
            </table>
       {/capture}

       {* Form with all advanced search criteria *}
       {capture name = 't_reports_advanced_search'}
            <table class = "formElements">
                <tr><td width = "33%">
                    <table>
                        <tr><td class = "labelCell">{$T_REPORT_FORM.new_login.label}:&nbsp;</td><td>{$T_REPORT_FORM.new_login.html}</td></tr>
                        <tr><td class = "labelCell">{$T_REPORT_FORM.name.label}:&nbsp;</td><td>{$T_REPORT_FORM.name.html}</td></tr>
                        <tr><td class = "labelCell">{$T_REPORT_FORM.surname.label}:&nbsp;</td><td>{$T_REPORT_FORM.surname.html}</td></tr>
                        <tr><td class = "labelCell">{$T_REPORT_FORM.father.label}:&nbsp;</td><td>{$T_REPORT_FORM.father.html}</td></tr>
                        <tr><td class = "labelCell">{$T_REPORT_FORM.sex.label}:&nbsp;</td><td>{$T_REPORT_FORM.sex.html}</td></tr>
                        <tr><td class = "labelCell">{$T_REPORT_FORM.marital_status.label}:&nbsp;</td><td>{$T_REPORT_FORM.marital_status.html}</td></tr>
                        <tr><td class = "labelCell">{$T_REPORT_FORM.birthday.label}:&nbsp;</td><td>{$T_REPORT_FORM.birthday.html}</td></tr>
                        <tr><td class = "labelCell">{$T_REPORT_FORM.birthplace.label}:&nbsp;</td><td>{$T_REPORT_FORM.birthplace.html}</td></tr>
                        <tr><td class = "labelCell">{$T_REPORT_FORM.birthcountry.label}:&nbsp;</td><td>{$T_REPORT_FORM.birthcountry.html}</td></tr>
                        <tr><td class = "labelCell">{$T_REPORT_FORM.mother_tongue.label}:&nbsp;</td><td>{$T_REPORT_FORM.mother_tongue.html}</td></tr>
                        <tr><td class = "labelCell">{$T_REPORT_FORM.nationality.label}:&nbsp;</td><td>{$T_REPORT_FORM.nationality.html}</td></tr>
                        <tr><td colspan=2>&nbsp;</td></tr>
                        <tr><td class = "labelCell">{$T_REPORT_FORM.office.label}:&nbsp;</td><td>{$T_REPORT_FORM.office.html}</td></tr>
                        <tr><td class = "labelCell">{$T_REPORT_FORM.company_internal_phone.label}:&nbsp;</td><td>{$T_REPORT_FORM.company_internal_phone.html}</td></tr>
                        <tr><td class = "labelCell">{$T_REPORT_FORM.email.label}:&nbsp;</td><td>{$T_REPORT_FORM.email.html}</td></tr>

                        <tr><td class = "labelCell">{$T_REPORT_FORM.user_type.label}:&nbsp;</td><td>{$T_REPORT_FORM.user_type.html}</td></tr>
                        <tr><td class = "labelCell">{$T_REPORT_FORM.active.label}:&nbsp;</td><td>{$T_REPORT_FORM.active.html}</td></tr>

                        {foreach name = 'profile_fields' key = key item = item from = $T_USER_PROFILE_FIELDS }
                            <tr><td class = "labelCell">{$T_REPORT_FORM.$item.label}:&nbsp;</td>
                                <td class = "elementCell">{$T_REPORT_FORM.$item.html}</td></tr>
                            {if $T_REPORT_FORM.$item.error}<tr><td></td><td class = "formError">{$T_REPORT_FORM.$item.error}</td></tr>{/if}
                        {/foreach}

                        <tr><td class = "labelCell">{$T_REPORT_FORM.registration.label}:&nbsp;</td><td>{$T_REPORT_FORM.registration.html}</td></tr>
                    </table>
                    </td>
                    <td width = "15%">
                    &nbsp;
                    </td>
                    <td width = "*">
                    <table>
                        <tr><td colspan=2>&nbsp;</td></tr>
                        <tr><td class = "labelCell">{$T_REPORT_FORM.address.label}:&nbsp;</td><td>{$T_REPORT_FORM.address.html}</td></tr>
                        <tr><td class = "labelCell">{$T_REPORT_FORM.city.label}:&nbsp;</td><td>{$T_REPORT_FORM.city.html}</td></tr>
                        <tr><td class = "labelCell">{$T_REPORT_FORM.country.label}:&nbsp;</td><td>{$T_REPORT_FORM.country.html}</td></tr>
                        <tr><td class = "labelCell">{$T_REPORT_FORM.homephone.label}:&nbsp;</td><td>{$T_REPORT_FORM.homephone.html}</td></tr>
                        <tr><td class = "labelCell">{$T_REPORT_FORM.mobilephone.label}:&nbsp;</td><td>{$T_REPORT_FORM.mobilephone.html}</td></tr>
                        <tr><td class = "labelCell">{$T_REPORT_FORM.hired_on.label}:&nbsp;</td><td>{$T_REPORT_FORM.hired_on.html}</td></tr>
                        <tr><td class = "labelCell">{$T_REPORT_FORM.left_on.label}:&nbsp;</td><td>{$T_REPORT_FORM.left_on.html}</td></tr>
                        <tr><td colspan=2>&nbsp;</td></tr>
                        <tr><td class = "labelCell">{$T_REPORT_FORM.employement_type.label}:&nbsp;</td><td>{$T_REPORT_FORM.employement_type.html}</td></tr>
                        <tr><td class = "labelCell">{$T_REPORT_FORM.way_of_working.label}:&nbsp;</td><td>{$T_REPORT_FORM.way_of_working.html}</td></tr>
                        <tr><td class = "labelCell">{$T_REPORT_FORM.work_permission_data.label}:&nbsp;</td><td>{$T_REPORT_FORM.work_permission_data.html}</td></tr>
                        <tr><td class = "labelCell">{$T_REPORT_FORM.police_id_number.label}:&nbsp;</td><td>{$T_REPORT_FORM.police_id_number.html}</td></tr>
                        <tr><td colspan=2>&nbsp;</td></tr>
                        <tr><td class = "labelCell">{$T_REPORT_FORM.afm.label}:&nbsp;</td><td>{$T_REPORT_FORM.afm.html}</td></tr>
                        <tr><td class = "labelCell">{$T_REPORT_FORM.doy.label}:&nbsp;</td><td>{$T_REPORT_FORM.doy.html}</td></tr>
                        <tr><td class = "labelCell">{$T_REPORT_FORM.wage.label}:&nbsp;</td><td>{$T_REPORT_FORM.wage.html}</td></tr>
                        <tr><td class = "labelCell">{$T_REPORT_FORM.bank.label}:&nbsp;</td><td>{$T_REPORT_FORM.bank.html}</td></tr>
                        <tr><td class = "labelCell">{$T_REPORT_FORM.bank_account.label}:&nbsp;</td><td>{$T_REPORT_FORM.bank_account.html}</td></tr>
                        <tr><td colspan=2>&nbsp;</td></tr>
                        <tr><td class = "labelCell">{$T_REPORT_FORM.driving_licence.label}:&nbsp;</td><td>{$T_REPORT_FORM.driving_licence.html}</td></tr>
                        <tr><td class = "labelCell">{$T_REPORT_FORM.national_service_completed.label}:&nbsp;</td><td>{$T_REPORT_FORM.national_service_completed.html}</td></tr>
                        <tr><td class = "labelCell">{$T_REPORT_FORM.transport.label}:&nbsp;</td><td>{$T_REPORT_FORM.transport.html}</td></tr>
                    </table>
                    </td>
                </tr>
            </table> {* And of main table of class = formelements *}

            {* Print the new centrally aligned submit button - This table is closed </table> by the closing tab of the main table of the eFront normal interface *}
            <table width ="66%">
                <tr><td>&nbsp;</td></tr>
                <tr><td class = "submitCell" style = "text-align:center" align="center">{$T_REPORT_FORM.submit_personal_details.html}</td></tr>
           </table>
       {/capture}


        {**moduleShowemployees: Show employees*}
        {capture name = 't_employees_code'}
{**222222222222*}
<!--ajax:foundEmployees-->
        <table style = "width:100%" class = "sortedTable" size = "{$T_EMPLOYEES_SIZE}" sortBy = "0" id = "foundEmployees" useAjax = "1" rowsPerPage = "20" url = "{$smarty.session.s_type}.php?ctg=module_hcd&op=reports&">
{*        <table border = "0" width = "100%" class = "sortedTable" sortBy = "0">*}
            <tr class = "topTitle">
                <td class = "topTitle">{$smarty.const._LOGIN}</td>
                <td class = "topTitle">{$smarty.const._NAME}</td>
                <td class = "topTitle">{$smarty.const._SURNAME}</td>
                <td class = "topTitle">{$smarty.const._LANGUAGE}</td>
                <td class = "topTitle" align="center">{$smarty.const._JOBSASSIGNED}</td>
                <td class = "topTitle" align="center">{$smarty.const._SENDMESSAGE}</td>
                <td class = "topTitle noSort" align="center">{$smarty.const._STATISTICS}</td>
                <td class = "topTitle noSort" align="center">{$smarty.const._OPERATIONS}</td>
            </tr>

       {if $T_EMPLOYEES_SIZE > 0}
            {foreach name = 'users_list' key = 'key' item = 'user' from = $T_EMPLOYEES}
            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
            <td>
                {if ($user.pending == 1)}
                <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$user.login}" class = "editLink" style="color:red;">{$user.login}</a>
                {elseif ($user.active == 1)}
                <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$user.login}" class = "editLink">{$user.login}</a>
                {else}
                {$user.login}
                {/if}
            </td>

            <td>{$user.name}</td>
            <td>{$user.surname}</td>
            <td>{$user.languages_NAME}</td>

            {if $user.jobs}
                <td align="center"><a href="#" class = "info nonEmptyLesson" id="jobsDetails_{$user.login}" onmouseover="$('tooltipImg_{$user.login}').style.visibility = 'visible';" onmouseout="$('tooltipImg_{$user.login}').style.visibility = 'hidden';">{$user.jobs_num}<img id="tooltipImg_{$user.login}" class = "tooltip" border = '0' src='images/others/tooltip_arrow.gif'><span class = 'tooltipSpan' id='userInfo_{$user.login}' style="font-size: 10px" >
                {foreach name = 'jobs_list' item = 'job' from = $user.jobs}
                {$job.description}&nbsp;{$smarty.const._ATBRANCH}&nbsp;{$job.name}<br>
                {/foreach}
                </span></a>
                </td>

                {literal}
                <script>
                user_login = '{/literal}{$user.login}{literal}';
                div_half_size = {/literal}{$user.div_size}{literal};
                $('userInfo_' + user_login).setStyle({left: -(div_half_size) + "px"});
                $('userInfo_' + user_login).setStyle({{/literal}{if $T_BROWSER == 'IE6'}width{else}minWidth{/if}{literal}: (2*div_half_size) + "px"});
                </script>
                {/literal}
            {else}
                <td align="center">{$user.jobs_num}</td>
            {/if}
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
                 <tr><td colspan ="8" class = "emptyCategory centerAlign">{$smarty.const._NOEMPLOYEESFULFILLTHESPECIFIEDCRITERIA}</td></tr>

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
<!--/ajax:foundEmployees-->
        {/capture}

            {*  **************************************************************
                DISPLAYING THE CAPTURED TABLES
                **************************************************************    *}
{capture name = 't_search_all'}

                <tr><td>
                <table border = "0" width = "100%" cellspacing = "5">
                    <tr><td valign = "top">
                    {$T_REPORT_FORM.javascript}
                        <form {$T_REPORT_FORM.attributes}>
                            {$T_REPORT_FORM.hidden}
                            <div class="tabber">
                                <div class="tabbertab">
                                    <h3>{$smarty.const._BASICCRITERIA}</h3>
                                    {eF_template_printInnerTable title = $smarty.const._BASICSEARCHOPTIONS data = $smarty.capture.t_reports_code image = '/32x32/view.png'}
                                    <br>
                                    {eF_template_printInnerTable title = $smarty.const._EMPLOYEESFULFILLINGCRITERIA data = $smarty.capture.t_employees_code image = '/32x32/user1.png' options = $T_SENDALLMAIL_LINK}
                                </div>

                                <div class="tabbertab">
                                    <h3>{$smarty.const._ADVANCED}</h3>
                                    {eF_template_printInnerTable title = $smarty.const._ADVANCEDSEARCH data = $smarty.capture.t_reports_advanced_search image = '/32x32/view.png'}
                                </div>
                             <div>
                        </form>
                        </td>
                   </tr>
                </table>
       </td></tr>
{/capture}
       {eF_template_printInnerTable title = $smarty.const._FINDEMPLOYEES data = $smarty.capture.t_search_all image = '/32x32/book_red.png' main_options = $T_TABLE_OPTIONS}
{/if}

{* ****************** CHART ************************** *}
{if $smarty.get.op == 'chart'}
    {**moduleChart: Show the organization's chart *}
       <tr><td class = "moduleCell">

       {capture name = 't_chart_code'}

           {if $T_CHART_TREE != ''}
               <a href = "javascript:void(0)" onClick = "expandCollapse('dhtmlgoodies_branches_tree');">{$smarty.const._EXPANDCOLLAPSE}</a><br>
               {$T_CHART_TREE}
           {else}
               <table width = "100%">
                <tr><td class = "emptyCategory" align="center">{$smarty.const._NOBRANCHESHAVEBEENREGISTERED}</td></tr>
               </table>
           {/if}
        {/capture}

        {eF_template_printInnerTable title = $smarty.const._ORGANIZATIONCHARTTREE data = $smarty.capture.t_chart_code image = '/32x32/cubes.png'}


       </td></tr>


{/if}


{* ****************** PLACEMENTS ************************** *}
{if $smarty.get.op == 'placements'}
{*  **************************************************************
    This is the form that contains the employee's job descriptions
    **************************************************************    *}
    {capture name = 't_employee_jobs'}
        {* Check permissions for allowing user to assign a new job *}
        {if $smarty.session.s_type == "administrator" || ($smarty.session.employee_type == $smarty.const._SUPERVISOR && $T_CTG != 'personal')}
        <table>
            <tr>
                <td><a href="#" onclick="add_new_job_row({$T_PLACEMENTS_SIZE})"><img src="/images/16x16/add2.png" title="{$smarty.const._NEWJOBDESCRIPTION}" alt="{$smarty.const._NEWJOBDESCRIPTION}"/ border="0"></a></td><td><a href="#" onclick="add_new_job_row({$T_PLACEMENTS_SIZE})">{$smarty.const._NEWJOBDESCRIPTION}</a></td>
            </tr>
        </table>
        {/if}

            <table border = "0" width = "100%" class = "sortedTable" id="jobsTable">
                <tr class = "topTitle">
                    <td class = "topTitle">{$smarty.const._BRANCHNAME}</td>
                    <td class = "topTitle">{$smarty.const._JOBDESCRIPTION}</td>
                    <td class = "topTitle">{$smarty.const._EMPLOYEEPOSITION}</td>
                </tr>

            {if isset($T_PLACEMENTS)}
                {foreach name = 'users_list' key = 'key' item = 'placement' from = $T_PLACEMENTS}
                <tr>
                    <td>{if $placement.supervisor == 1} <a href="{$smarty.session.s_type}.php?ctg=module_hcd&op=branches&edit_branch={$placement.branch_ID}">{$placement.name}</a>{else}{$placement.name}{/if}</td>
                    <td>{$placement.description}</td>
                    <td>{if $placement.supervisor == 0} {$smarty.const._EMPLOYEE} {else} {$smarty.const._SUPERVISOR} {/if}</td>
                </tr>
                {/foreach}
            {else}
                 <tr id="no_jobs_found">
                    <td colspan=4 class = "emptyCategory centerAlign">{$smarty.const._NOPLACEMENTSASSIGNEDYET}</td>
                 </tr>
            {/if}
            <tr><td>&nbsp;</td></tr>
            </table>

    {/capture}

    {eF_template_printInnerTable title = $smarty.const._JOBDESCRIPTIONS data = $smarty.capture.t_employee_jobs image = '/32x32/workstation1.png'}
{/if}

{* ****************** MAIN HCD PAGE ************************** *}
{if !isset($smarty.get.op)}
    {*assign var = "title" value = '<a class = "titleLink" href ="$smarty.server.PHP_SELF">'|cat:$smarty.const._HCD|cat:'</a>'*}
            {eF_template_printIconTable title=$smarty.const._OPTIONS columns=3 links=$T_ADMIN_OPTIONS image='/32x32/gears.png'}
{/if}
