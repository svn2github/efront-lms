{capture name = "t_branches_table_code"}
{if $smarty.get.edit_branch}
  {assign var = "branchesAjaxUrl" value = "`$smarty.server.PHP_SELF`?ctg=module_hcd&op=branches&edit_branch=`$smarty.get.edit_branch`&"}
{else}
  {assign var = "branchesAjaxUrl" value = "`$smarty.server.PHP_SELF`?ctg=module_hcd&op=branches&"}
{/if}
{if !$T_SORTED_TABLE || $T_SORTED_TABLE == 'branchesTable'}
<!--ajax:branchesTable-->
            <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "4" id = "branchesTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$branchesAjaxUrl}">
                <tr class = "topTitle">
                    <td class = "topTitle" name = "name">{$smarty.const._BRANCHNAME}</td>
                    <td class = "topTitle" name = "city">{$smarty.const._CITY}</td>
                    <td class = "topTitle" name = "address">{$smarty.const._ADDRESS}</td>
                    <td class = "topTitle centerAlign" name = "employees">{$smarty.const._ACTIVEUSERS}</td>
                    <td class = "topTitle centerAlign" name = "inactive_employees">{$smarty.const._INACTIVEUSERS}</td>
                    <td class = "topTitle" name = "father">{$smarty.const._FATHERBRANCHNAME}</td>
                    <td class = "topTitle centerAlign noSort" name="operations">{$smarty.const._OPERATIONS}</td>
                </tr>

            {foreach name = 'branch_list' key = 'key' item = 'branch' from = $T_DATA_SOURCE}
                <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                    <td>
                    {if $smarty.session.s_type == "administrator" || $branch.supervisor == 1}
                        <a href = "{$smarty.server.PHP_SELF}?ctg=module_hcd&op=branches&edit_branch={$branch.branch_ID}" class = "editLink">{$branch.name}</a>
                    {else}
                        {$branch.name}
                    {/if}
                    </td>
                    <td>{$branch.city}</td>
                    <td>{$branch.address}</td>
                    <td class = "centerAlign">{$branch.employees}</td>
                    <td class = "centerAlign">{$branch.inactive_employees}</td>
                    <td>
     {if $smarty.session.s_type == "administrator" || $branch.father_supervisor == 1}
      <a href = "{$smarty.server.PHP_SELF}?ctg=module_hcd&op=branches&edit_branch={$branch.father_ID}" class = "editLink">{$branch.father}</a>
     {else}{$branch.father}{/if}
     </td>
                    <td class = "centerAlign">
                    {if $smarty.session.s_type == "administrator" || $branch.supervisor == 1}
                        <a href = "{$smarty.server.PHP_SELF}?ctg=module_hcd&op=branches&edit_branch={$branch.branch_ID}" class = "editLink">
                         <img class = "handle" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}"/>
                        </a>
                        <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._AREYOUSUREYOUWANTTODISMISSTHEBRANCH}')) deleteBranch(this, '{$branch.branch_ID}', '{$branch.father_ID}')"/>
                    {/if}
                    </td>
                </tr>
            {foreachelse}
             <tr class = "defaultRowHeight oddRowColor"><td colspan = "6" class = "emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
            {/foreach}
            </table>
<!--/ajax:branchesTable-->
{/if}
{/capture}

    {if $smarty.get.add_branch || $smarty.get.edit_branch}

  {capture name = 't_branch_code'}
   {eF_template_printForm form=$T_BRANCH_FORM}

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
  {/capture}

  {if $smarty.get.edit_branch}
   {capture name = 't_employees_code'}
{if !$T_SORTED_TABLE || $T_SORTED_TABLE == 'branchUsersTable'}
<!--ajax:branchUsersTable-->
          <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "0" id = "branchUsersTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.session.s_type}.php?ctg=module_hcd&op=branches&edit_branch={$smarty.get.edit_branch}&showAllEmployees={if isset($smarty.get.showAllEmployees)}{$smarty.get.showAllEmployees}{else}0{/if}&">
              <tr class = "topTitle">
                  <td class = "topTitle" name="login">{$smarty.const._USER}</td>
                  <td class = "topTitle" name="description">{$smarty.const._JOBDESCRIPTION}</td>
                  <td class = "topTitle" name="supervisor">{$smarty.const._EMPLOYEEPOSITION}</td>
                  {if isset($smarty.get.showAllEmployees) && $smarty.get.showAllEmployees == 1}
                  <td class = "topTitle" name="bname">{$smarty.const._BRANCHNAME}</td>
                  {/if}
                  {if !isset($T_CURRENT_USER->coreAccess.statistics) || $T_CURRENT_USER->coreAccess.statistics != 'hidden'}
                  <td class = "topTitle noSort centerAlign">{$smarty.const._STATISTICS}</td>
                  {/if}
                  <td class = "topTitle noSort centerAlign">{$smarty.const._OPERATIONS}</td>
              </tr>
          {foreach name = 'users_list' key = 'key' item = 'user' from = $T_DATA_SOURCE}
              {if $user.branch_ID == $smarty.get.edit_branch || $smarty.get.showAllEmployees == 1}
                  {assign var = "employees_found" value = '1'}
                  <tr class = "{cycle values = "oddRowColor, evenRowColor"} {if !$user.active}deactivatedTableElement{/if}">
                      <td>
                     {if ($user.pending == 1)}
                          <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$user.login}" class = "editLink" style="color:red;">#filter:login-{$user.login}#</a>
                     {elseif ($user.active == 1)}
                          <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$user.login}" class = "editLink">#filter:login-{$user.login}#</a>
                     {else}
                          #filter:login-{$user.login}#
                     {/if}
                      </td>
                      <td><a href = "{$smarty.session.s_type}.php?ctg=module_hcd&op=job_descriptions&edit_job_description={$user.job_description_ID}" class = "editLink">{$user.description}</a></td>
                      <td>{if $user.supervisor == '1'}{$smarty.const._SUPERVISOR}{else}{$smarty.const._EMPLOYEE} {/if} </td>
                  {if isset($smarty.get.showAllEmployees) && $smarty.get.showAllEmployees == 1}
                   <td>{$user.bname}</td>
                  {/if}
            {if !isset($T_CURRENT_USER->coreAccess.statistics) || $T_CURRENT_USER->coreAccess.statistics != 'hidden'}
                <td class = "centerAlign"><a href="{$smarty.session.s_type}.php?ctg=statistics&option=user&sel_user={$user.login}"><img border = "0" src = "images/16x16/reports.png" title = "{$smarty.const._STATISTICS}" alt = "{$smarty.const._STATISTICS}" /></a></td>
               {/if}
                      <td class = "centerAlign">
                      {if $user.active == 1}
                          <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$user.login}" class = "editLink">
                           <img class = "handle" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
                      {/if}
                      {if $user.login != $smarty.session.s_login}
                          <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "removeUserFromBranch(this, '{$user.login}', '{$user.job_description_ID}', '{$smarty.get.edit_branch}', '{$user.supervisor}', '{$T_FATHER_BRANCH_ID}');"/>
                      {/if}
                      </td>

                  </tr>
              {/if}
          {/foreach}
          {if !$employees_found}
              <tr class = "oddRowColor defaultRowHeight"><td colspan = "6" class = "emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
          {/if}
          </table>
<!--/ajax:branchUsersTable-->
{/if}
   {/capture}

   {capture name = 't_employees_to_branch'}

<!--ajax:branchJobsTable-->

   <table style = "width:100%" class = "sortedTable" size = "{$T_EMPLOYEES_SIZE}" sortBy = "0" id = "branchJobsTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.session.s_type}.php?ctg=module_hcd&op=branches&edit_branch={$smarty.get.edit_branch}&">
          <tr class = "topTitle">
              <td class = "topTitle" name="login">{$smarty.const._USER}</td>
              <td class = "topTitle" name="description">{$smarty.const._JOBDESCRIPTION}</td>
              <td class = "topTitle" name="supervisor">{$smarty.const._EMPLOYEEPOSITION}</td>
              <td class = "topTitle" name="branch_ID" align="center">{$smarty.const._CHECK}</td>
          </tr>
          {foreach name = 'users_list' key = 'key' item = 'user' from = $T_EMPLOYEES}
              <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                  <td>
     {if ($user.pending == 1)}
             <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$user.login}" class = "editLink" style="color:red;">#filter:login-{$user.login}#</a>
              {elseif ($user.active == 1)}
             <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$user.login}" class = "editLink">#filter:login-{$user.login}#</a>
              {else}
             #filter:login-{$user.login}#
              {/if}
                  </td>
                  <td><span style="display:none" id="none_job_row{$user.login}">{if $user.description}{$user.description}{else}_{/if}</span>{$user.job_select}</td>
                  <td><span style="display:none" id="none_position_row{$user.login}">{$user.supervisor}</span>{$user.position_select}</td>
                  <td class = "centerAlign">
             <span style="display:none" id="none_check_row{$user.login}">{if $user.branch_ID == $smarty.get.edit_branch}1{else}0{/if}</span>
             <input class = "inputCheckBox" type = "checkbox" {if $user.login == $smarty.session.s_login}disabled = "true"{/if} onclick="javascript:show_hide_job_selects('{$user.login}'); ajaxPost('row{$user.login}', this);" name = "check_{$user.login}" id = "check_row{$user.login}"{if $user.branch_ID == $smarty.get.edit_branch}checked{/if}>
                  </td>
              </tr>
          {foreachelse}
              <tr class = "oddRowColor defaultRowHeight"><td colspan = "6" class = "emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
          {/foreach}
          {if $smarty.const.MSIE_BROWSER == 1}
           <img style = "display:none" src="images/16x16/question_type_free_text.png" onLoad = "javascript:simulateJobSelects();" />
       {/if}
          </table>

<!--/ajax:branchJobsTable-->

   {/capture}

      {*Sub-Branches: moduleAllBranches: Show subbranches *}
      {capture name = 't_subbranches_code'}
       {if $smarty.session.employee_type != _EMPLOYEE}
     <div class = "headerTools">
      <span>
       <img src = "images/16x16/add.png" title = "{$smarty.const._NEWSUBBRANCH}" alt = "{$smarty.const._NEWSUBBRANCH}" >
             <a href = "{$smarty.session.s_type}.php?ctg=module_hcd&op=branches&add_branch=1&add_branch_to={$smarty.get.edit_branch}">{$smarty.const._NEWSUBBRANCH}</a>
            </span>
     </div>
          {/if}

    {$smarty.capture.t_branches_table_code}
      {/capture}

      {*Show job_descriptions of this branch*}
   {capture name = 't_branch_jobs'}
    <div class = "headerTools">
     <span>
      <img src = "images/16x16/add.png" title = "{$smarty.const._NEWJOBDESCRIPTION}" alt = "{$smarty.const._NEWJOBDESCRIPTION}" >
            <a href = "{$smarty.session.s_type}.php?ctg=module_hcd&op=job_descriptions&add_job_description=1&add_to_branch={$smarty.get.edit_branch}">{$smarty.const._NEWJOBDESCRIPTION}</a>
           </span>
    </div>

          <table width = "100%" class = "sortedTable">
              <tr class = "topTitle">
                  <td class = "topTitle">{$smarty.const._JOBDESCRIPTION}</td>
                  <td class = "topTitle centerAlign" >{$smarty.const._CURRENTLYEMPLOYEED}</td>
                  <td class = "topTitle centerAlign" >{$smarty.const._VACANCIES}</td>
                  <td class = "topTitle centerAlign" >{$smarty.const._SKILLSREQUIRED}</td>
                  <td class = "topTitle noSort centerAlign" >{$smarty.const._OPERATIONS}</td>
              </tr>
              {foreach name = 'job_description_list' key = 'key' item = 'job_description' from = $T_JOB_DESCRIPTIONS}
              <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                  <td><a href = "{$smarty.server.PHP_SELF}?ctg=module_hcd&op=job_descriptions&edit_job_description={$job_description.job_description_ID}" class = "editLink">{$job_description.description}</a></td>
                  <td class = "centerAlign">{$job_description.Employees}</td>
                  <td class = "centerAlign">{$job_description.more_needed} </td>
                  <td class = "centerAlign">{$job_description.skill_req}</td>
                  <td class = "centerAlign">
                   <a href = "{$smarty.server.PHP_SELF}?ctg=module_hcd&op=job_descriptions&edit_job_description={$job_description.job_description_ID}" class = "editLink"><img class="handle" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
                   <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._AREYOUSUREYOUWANTTOREMOVETHATJOBDESCRIPTION}')) deleteJob(this, '{$job_description.job_description_ID}', '{$smarty.server.PHP_SELF}?ctg=module_hcd&op=job_descriptions')" />
                  </td>
              </tr>
     {foreachelse}
        <tr class = "oddRowColor defaultRowHeight"><td colspan = "6" class = "emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
              {/foreach}
          </table>
      {/capture}

      {capture name ='t_branch_lessons'}
<!--ajax:lessonsTable-->
                                <table style = "width:100%" class = "sortedTable" size = "{$T_LESSONS_SIZE}" sortBy = "0" useAjax = "1" id = "lessonsTable" rowsPerPage = "20" url = "{$smarty.session.s_type}.php?ctg=module_hcd&op=branches&edit_branch={$smarty.get.edit_branch}&">
                                    <tr class = "topTitle">
                                        <td class = "topTitle" name = "name">{$smarty.const._NAME} </td>
                                        <td class = "topTitle" name = "direction_name">{$smarty.const._CATEGORY}</td>
                                        <td class = "topTitle" name = "languages_NAME">{$smarty.const._LANGUAGE}</td>
                                        {*<td class = "topTitle centerAlign" name = "students">{$smarty.const._PARTICIPATION}</td>*}
                                        <td class = "topTitle centerAlign" name ="skills_offered" width="12%">{$smarty.const._SKILLS}</td>
                                        <td class = "topTitle" name = "created">{$smarty.const._CREATED}</td>
                                        <td class = "topTitle centerAlign" name ="id" >{$smarty.const._CHECKED}</td>
                                    </tr>
                    {foreach name = 'lessons_list2' key = 'key' item = 'lesson' from = $T_LESSONS_DATA}
                                    <tr id = "row_{$lesson.id}" class = "{cycle values = "oddRowColor, evenRowColor"} {if !$lesson.active}deactivatedTableElement{/if}">
                                        <td id = "column_{$lesson.id}" class = "editLink">
            <a href = "{$smarty.server.PHP_SELF}?ctg=lessons&edit_lesson={$lesson.id}" >{$lesson.name}</a>
                                        </td>
                                        <td>{$lesson.direction_name}</td>
                                        <td>{$lesson.languages_NAME}</td>
                                        {*<td id="participation{$lesson.id}" class = "centerAlign">{if $lesson.max_users}{$lesson.students}/{$lesson.max_users}{else}{$lesson.students}{/if}</td>*}
                                        <td class = "centerAlign">{if $lesson.skills_offered == 0}{$smarty.const._NONESKILL}{else}{$lesson.skills_offered}{/if}</td>
                                        <td>#filter:timestamp-{$lesson.created}#</td>
                         <td class = "centerAlign">
                         {if $smarty.session.s_type == "administrator" || $branch.supervisor == 1}
                          <input class = "inputCheckBox" type = "checkbox" name = "{$lesson.id}" onclick="javascript:ajaxBranchLessonPost('{$lesson.id}', this);" {if $lesson.branches_ID == $smarty.get.edit_branch} checked {/if} >
                         {/if}
                         </td>
                                    </tr>
                    {foreachelse}
                                <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
                    {/foreach}
                                </table>
<!--/ajax:lessonsTable-->
                                    {/capture}


  {assign var = "courses_url" value = "`$smarty.server.PHP_SELF`?ctg=module_hcd&op=branches&edit_branch=`$smarty.get.edit_branch`&"}
  {assign var = "_change_handles_" value = true}
  {capture name ='t_branch_courses'}
   {include file = "includes/common/courses_list.tpl"}
  {/capture}
  {/if}


  <table style = "width:100%">
      <tr><td>
   {if $smarty.session.employee_type != _EMPLOYEE && $smarty.get.edit_branch}
    {capture name = 't_branch_properties_code'}
                        <div class="tabber">
                            <div class="tabbertab">
                                <h3>{$smarty.const._EDITBRANCH}</h3>
                                {eF_template_printBlock title = $smarty.const._BRANCHRECORD|cat:"<span class='innerTableName'>&nbsp;&quot;`$T_BRANCH_NAME`&quot;</span>" data = $smarty.capture.t_branch_code image = '32x32/branch.png' options = $T_DELETE_LINK}
                                {eF_template_printBlock title = $smarty.const._EMPLOYEES|cat:$smarty.const._ATBRANCH|cat:"<span class='innerTableName'>&quot;`$T_BRANCH_NAME`&quot;</span><span id='andSubbranchesTitle' style='visibility:hidden'>&nbsp;`$smarty.const._ANDSUBBRANCHES`</span>" data = $smarty.capture.t_employees_code image = '32x32/user.png' options = $T_SUBBRANCHES_LINK}
                            </div>
                            <div class="tabbertab {if ($smarty.get.tab == "assign_employees"  || isset($smarty.post.employees_to_branches)) } tabbertabdefault {/if}">
                                <script>var myform = "branch_to_employees";</script>
                                <h3>{$smarty.const._ASSIGNEMPLOYEES}</h3>
                                {eF_template_printBlock title = $smarty.const._ASSIGNEMPLOYEESTOBRANCH|cat:"<span class='innerTableName'>&nbsp;&quot;`$T_BRANCH_NAME`&quot;</span>" data = $smarty.capture.t_employees_to_branch image = '32x32/tools.png'}
                            </div>
                            <div class="tabbertab {if ($smarty.get.tab == "subbranches")} tabbertabdefault {/if}">
                                <h3>{$smarty.const._SUBBRANCHES}</h3>
                                {eF_template_printBlock title = $smarty.const._SUBBRANCHES|cat:$smarty.const._OFBRANCH|cat:"<span class='innerTableName'>&nbsp;&quot;`$T_BRANCH_NAME`&quot;</span>" data = $smarty.capture.t_subbranches_code image = '32x32/branch.png'}
                            </div>
                            <div class="tabbertab {if ($smarty.get.tab == "jobs")} tabbertabdefault {/if}">
                                <h3>{$smarty.const._JOBDESCRIPTIONS}</h3>
                                {eF_template_printBlock title = $smarty.const._JOBDESCRIPTIONS|cat:$smarty.const._OFBRANCH|cat:"<span class='innerTableName'>&nbsp;&quot;`$T_BRANCH_NAME`&quot;</span>" data = $smarty.capture.t_branch_jobs image = '32x32/note.png'}
                            </div>
{*
                            <div class="tabbertab {if ($smarty.get.tab == "lessons")} tabbertabdefault {/if}">
                                <h3>{$smarty.const._LESSONS}</h3>
                                {eF_template_printBlock title = $smarty.const._LESSONS|cat:$smarty.const._OFBRANCH|cat:"<span class='innerTableName'>&nbsp;&quot;`$T_BRANCH_NAME`&quot;</span>" data = $smarty.capture.t_branch_lessons image = '32x32/lesson.png'}
                            </div>
*}
                            <div class="tabbertab {if ($smarty.get.tab == "courses")} tabbertabdefault {/if}">
                                <h3>{$smarty.const._COURSES}</h3>
                                {eF_template_printBlock title = $smarty.const._COURSES|cat:$smarty.const._OFBRANCH|cat:"<span class='innerTableName'>&nbsp;&quot;`$T_BRANCH_NAME`&quot;</span>" data = $smarty.capture.t_branch_courses image = '32x32/courses.png'}
                            </div>

                        </div>
    {/capture}
    {eF_template_printBlock title = $smarty.const._BRANCHRECORD data = $smarty.capture.t_branch_properties_code image = '32x32/branch.png'}
   {else}
                {eF_template_printBlock title = $smarty.const._BRANCHRECORD data = $smarty.capture.t_branch_code image = '32x32/branch.png'}
            {/if}
   </td></tr>
  </table>
    {else}
        {*moduleAllBranches: Show branches *}
        {capture name = 't_branches_code'}
            {* Only supervisors and administrators may change branch data - currently all - TODO: selected *}
         {if $smarty.session.employee_type != _EMPLOYEE}
    <div class = "headerTools">
     <span>
      <img src = "images/16x16/add.png" title = "{$smarty.const._NEWBRANCH}" alt = "{$smarty.const._NEWBRANCH}" >
               <a href="{$smarty.session.s_type}.php?ctg=module_hcd&op=branches&add_branch=1" title = "{$smarty.const._NEWBRANCH}" >{$smarty.const._NEWBRANCH}</a>
              </span>
    </div>
            {/if}

  {$smarty.capture.t_branches_table_code}

        {/capture}
        {if $smarty.session.employee_type != _EMPLOYEE}
           {eF_template_printBlock title = $smarty.const._UPDATEBRANCHES data = $smarty.capture.t_branches_code image = '32x32/branch.png'}
        {else}
           {eF_template_printBlock title = $smarty.const._VIEWBRANCHES data = $smarty.capture.t_branches_code image = '32x32/branch.png'}
        {/if}

    {/if}
