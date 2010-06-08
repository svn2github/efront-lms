 {if $smarty.get.add_job_description || $smarty.get.edit_job_description}


 {* **************************************************************
    This is the form that contains the job_descriptions data
    **************************************************************
    *}
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
                <td align="right"><a id="details_link" name="details_link" {$T_BRANCH_INFO} {if $T_BRANCH_INFO == ""}style="visibility:hidden"{/if}><img src="images/16x16/search.png" title="{$smarty.const._DETAILS}" alt="{$smarty.const.DETAILS}" border="0"></a></td>
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
           {* {if $T_JOB_DESCRIPTIONS_FORM.placements.error}<tr><td></td><td class = "formError">{$T_JOB_DESCRIPTIONS_FORM.placements.error}</td></tr>{/if} *}

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

      {*This is the table with all employees having the job_description*}
    {if $smarty.get.edit_job_description}
    {capture name = 't_employees_code'}

    <table border = "0" width = "100%" class = "sortedTable" >
     <tr class = "topTitle">
      <td class = "topTitle">{$smarty.const._USER}</td>
      <td class = "topTitle centerAlign">{$smarty.const._HASREQUIREDTRAINING}</td>
      {*<td class = "topTitle">{$smarty.const._EMPLOYEEPOSITION}</td>*}
      {if !isset($T_CURRENT_USER->coreAccess.statistics) || $T_CURRENT_USER->coreAccess.statistics != 'hidden'}
       <td class = "topTitle" noSort" align="center">{$smarty.const._STATISTICS}</td>
      {/if}
      <td class = "topTitle noSort" align="center">{$smarty.const._OPERATIONS}</td>
     </tr>

    {if isset($T_EMPLOYEES)}
     {foreach name = 'users_list' key = 'key' item = 'user' from = $T_EMPLOYEES}
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
       <td align="center">{if $user.fulfillsTraining}<img src = "images/16x16/success.png" alt = "{$smarty.const._YES}" title = "{$smarty.const._YES}">{else}<img src = "images/16x16/forbidden.png" alt = "{$smarty.const._NO}" title = "{$smarty.const._NO}">{/if}</td>
      {*<td>{if $user.supervisor == '1'}{$smarty.const._SUPERVISOR}{else}{$smarty.const._EMPLOYEE} {/if} </td>*}
      {if !isset($T_CURRENT_USER->coreAccess.statistics) || $T_CURRENT_USER->coreAccess.statistics != 'hidden'}
      <td class = "centerAlign"><a href="{$smarty.session.s_type}.php?ctg=statistics&option=user&sel_user={$user.login}"><img border = "0" src = "images/16x16/reports.png" title = "{$smarty.const._STATISTICS}" alt = "{$smarty.const._STATISTICS}" /></a></td>
      {/if}
      <td align="center">
       {if $user.active == 1}
        <a href = "{$smarty.session.s_type}.php?ctg=users&edit_user={$user.login}&tab=placements" class = "editLink"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
       {else}
        <img src = "images/16x16/edit.png" class = "inactiveImage" title = "{$smarty.const._UNPRIVILEGEDATTEMPT}" alt = "{$smarty.const._UNPRIVILEGEDATTEMPT}" />
       {/if}
       <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._AREYOUSUREYOUWANTTOCANCELJOB}')) removeJobFromUser(this, '{$user.login}', '{$smarty.get.edit_job_description}');" />
      </td>
     </tr>
     {/foreach}
    {else}
       <tr><td colspan=6>
       <table width = "100%">
     <tr><td class = "emptyCategory">{$smarty.const._NOEMPLOYEESPOSSESSJOBDESCRIPTION}</td></tr>
       </table>
       </td></tr>
    {/if}
    </table>

    {/capture}
    {/if}

   {* ****************************************************
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
       <a href="{$smarty.server.PHP_SELF}?ctg=module_hcd&op=skills&add_skill=1"><img src="images/16x16/add.png" title="{$smarty.const._NEWSKILL}" alt="{$smarty.const._NEWSKILL}"/ border="0"></a></td><td><a href="{$smarty.server.PHP_SELF}?ctg=module_hcd&op=skills&add_skill=1">{$smarty.const._NEWSKILL}</a>
       </td></tr></table>
      </td>
      {/if}
      {if $smarty.session.s_type == "administrator" || $smarty.session.employee_type == $smarty.const._SUPERVISOR}
      <td align ="right">

       <table><tr><td>{$smarty.const._APPLYTOALLDESCRIPTIONSWITHDESCRIPTION|cat:$T_JOB_DESCRIPTION_NAME}</td>
            <td><input class = "inputCheckBox" type = "checkbox" id="skill_changes_apply_to" name = "skill_changes_apply_to" onclick= "applyToAllJobDescriptionsInfo(this, '{$T_JOB_DESCRIPTION_NAME}');"></td>
           </tr>
       </table>
      </td>
      {/if}
     </tr>
    </table>
    {/if}

<!--ajax:skillsTable-->
    <table style = "width:100%" class = "sortedTable" size = "{$T_SKILLS_SIZE}" sortBy = "0" id = "skillsTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=module_hcd&op=job_descriptions&edit_job_description={$smarty.get.edit_job_description}&tab=skills&">
     <tr class = "topTitle">
      <td class = "topTitle" name="description" width="25%">{$smarty.const._SKILL}</td>
      <td class = "topTitle" name="category">{$smarty.const._CATEGORY}</td>
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
      <td>{$skill.category}</td>
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
       <tr><td class = "emptyCategory">{$smarty.const._NOSKILLSREGISTEREDASPREREQUISITES}</td></tr>
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
            <td><input class = "inputCheckBox" type = "checkbox" id="lesson_changes_apply_to" name = "lesson_changes_apply_to" onclick= "applyToAllJobDescriptionsInfo(this, '{$T_JOB_DESCRIPTION_NAME}');"></td>
           </tr>
       </table>
      </td>
      </tr>
     </table>
<!--ajax:lessonsTable-->

             <table style = "width:100%" class = "sortedTable" size = "{$T_LESSONS_SIZE}" sortBy = "0" id = "lessonsTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "administrator.php?ctg=module_hcd&op=job_descriptions&edit_job_description={$smarty.get.edit_job_description}&tab=lessons&">
              <tr class = "topTitle">
               <td class = "topTitle" name = "name">{$smarty.const._NAME} </td>
               <td class = "topTitle" name = "direction_name">{$smarty.const._DIRECTION}</td>
               <td class = "topTitle" name = "languages_NAME">{$smarty.const._LANGUAGE}</td>






               <td class = "topTitle" name = "price">{$smarty.const._PRICE}</td>


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
                  {if isset($lesson.info.assessment)} <strong>{$smarty.const._ASSESSMENT|cat:'</strong>:&nbsp;'|cat:$lesson.info.assessment}<br/> {/if}
                  {if isset($lesson.info.objectives)} <strong>{$smarty.const._OBJECTIVES|cat:'</strong>:&nbsp;'|cat:$lesson.info.objectives}<br/> {/if}
                  {if isset($lesson.info.lesson_topics)} <strong>{$smarty.const._LESSONTOPICS|cat:'</strong>:&nbsp;'|cat:$lesson.info.lesson_topics}<br/> {/if}
                  {if isset($lesson.info.resources)} <strong>{$smarty.const._RESOURCES|cat:'</strong>:&nbsp;'|cat:$lesson.info.resources}<br/> {/if}
                  {if isset($lesson.info.other_info)} <strong>{$smarty.const._OTHERINFO|cat:'</strong>:&nbsp;'|cat:$lesson.info.other_info}<br/> {/if}
                 </span>
                </a>
            {else}
                <a href = "{$smarty.server.PHP_SELF}?ctg=lessons&edit_lesson={$lesson.id}" class = "editLink">{$lesson.name}</a>
            {/if}
               </td>
               <td>{$lesson.direction_name}</td>
               <td>{$lesson.languages_NAME}</td>

              {* enterprise version: Prices are replaced by the number of skills offered *}
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
             <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NOLESSONSFOUND}</td></tr>
          {/foreach}
             </table>
<!--/ajax:lessonsTable-->

    {/capture}


    {capture name = 't_job_to_courses'}
     <table width="100%">
      <tr><td align ="right">
       <table><tr><td>{$smarty.const._APPLYTOALLDESCRIPTIONSWITHDESCRIPTION|cat:$T_JOB_DESCRIPTION_NAME}</td>
            <td><input class = "inputCheckBox" type = "checkbox" id="course_changes_apply_to" name = "course_changes_apply_to" onclick= "applyToAllJobDescriptionsInfo(this, '{$T_JOB_DESCRIPTION_NAME}');" /></td>
           </tr>
       </table>
      </td>
      </tr>
     </table>
  {assign var = "courses_url" value = "`$smarty.server.PHP_SELF`?ctg=module_hcd&op=job_descriptions&edit_job_description=`$smarty.get.edit_job_description`&"}
  {assign var = "_change_handles_" value = true}

   {include file = "includes/common/courses_list.tpl"}

{if 0}
<!--ajax:coursesTable-->

             <table style = "width:100%" class = "sortedTable" size = "{$T_COURSES_SIZE}" sortBy = "0" id = "coursesTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "administrator.php?ctg=module_hcd&op=job_descriptions&edit_job_description={$smarty.get.edit_job_description}&tab=courses&">
              <tr class = "topTitle">
               <td class = "topTitle" name = "name">{$smarty.const._NAME} </td>
               <td class = "topTitle" name = "direction_name">{$smarty.const._DIRECTION}</td>
               <td class = "topTitle" name = "languages_NAME">{$smarty.const._LANGUAGE}</td>

              {* enterprise version: Prices are replaced by the number of skills offered *}



               <td class = "topTitle" name = "price">{$smarty.const._PRICE}</td>


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
                  {if isset($course.info.assessment)} <strong>{$smarty.const._ASSESSMENT|cat:'</strong>:&nbsp;'|cat:$course.info.assessment}<br/> {/if}
                  {if isset($course.info.objectives)} <strong>{$smarty.const._OBJECTIVES|cat:'</strong>:&nbsp;'|cat:$course.info.objectives}<br/> {/if}
                  {if isset($course.info.course_topics)} <strong>{$smarty.const._COURSETOPICS|cat:'</strong>:&nbsp;'|cat:$course.info.course_topics}<br/> {/if}
                  {if isset($course.info.resources)} <strong>{$smarty.const._RESOURCES|cat:'</strong>:&nbsp;'|cat:$course.info.resources}<br/> {/if}
                  {if isset($course.info.other_info)} <strong>{$smarty.const._OTHERINFO|cat:'</strong>:&nbsp;'|cat:$course.info.other_info}<br/> {/if}
                 </span>
                </a>
            {else}
                {if $course.active == 1}<a href = "{$smarty.server.PHP_SELF}?ctg=courses&edit_course={$course.id}" class = "editLink">{$course.name}</a>{else}{$course.name}{/if}
            {/if}
               </td>
               <td>{$course.direction_name}</td>
               <td>{$course.languages_NAME}</td>

              {* enterprise version: Prices are replaced by the number of skills offered *}
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
             <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NOCOURSESFOUND}</td></tr>
          {/foreach}
             </table>
<!--/ajax:coursesTable-->
{/if}
    {/capture}


    {capture name = 't_job_prerequisites'}
     <script>
     var newTrainingCondition = '{$T_JOB_DESCRIPTIONS_FORM.prerequisites_row_col.html|replace:"\n":""|replace:"'":"\'"}';
     var noTrainingDefinedYet = '{$smarty.const._NOREQUIREDTRAININGSETYET}';
     var addAlternativeTrainingConst = '{$smarty.const._ADDALTERNATIVETRAINING}';
     var orConst = '{$smarty.const._OR}';
     </script>
     <table width="100%">
      <tr>
       <td align ="left">
       {if ($smarty.session.s_type == "administrator" || ($smarty.session.employee_type == $smarty.const._SUPERVISOR))}
       <table>
        <tr>
         <td><a href="javascript:void(0);" onclick="add_job_prerequisite({$T_PREREQUISITES_SIZE});"><img id="add_training_img" src="images/16x16/add.png" title="{$smarty.const._NEWJOBPLACEMENT}" alt="{$smarty.const._NEWJOBPLACEMENT}"/ border="0"></a></td><td><a href="javascript:void(0);" onclick="add_job_prerequisite({$T_PREREQUISITES_SIZE});">{$smarty.const._NEWREQUIREDTRAININGCOURSE}</a></td>
        </tr>
       </table>
       {/if}

       </td>
       <td align ="right">
       <table><tr><td>{$smarty.const._APPLYTOALLDESCRIPTIONSWITHDESCRIPTION|cat:$T_JOB_DESCRIPTION_NAME}</td>
            <td><input class = "inputCheckBox" type = "checkbox" id="training_changes_apply_to_all" name = "training_changes_apply_to" onclick= "ajaxPostRequiredTraining();" /></td>
           </tr>
       </table>
       </td>
      </tr>
     </table>

     <table style = "width:100%" class = "sortedTable" id = "prerequisitesTable" noFooter="true">
      <tr class = "topTitle">
       <td class = "topTitle" name = "name" width="80%">{$smarty.const._REQUIREDTRAININGCOURSES} </td>
       <td class = "topTitle centerAlign" name = "direction_name" >{$smarty.const._OPERATIONS}</td>
      </tr>

      {if !isset($T_JOB_DESCRIPTIONS_FORM.prerequisites_row_col)}
       <tr>
        <td colspan=4 class = "emptyCategory" id = "noCourses">{$smarty.const._NOCOURSESHAVEBEENREGISTERED}</td>
       </tr>
      {else}
       {if isset($T_PREREQUISITES)}

        {foreach name = 'exclusive_list' key = 'row' item = 'rowConditions' from = $T_PREREQUISITES}
        <tr id = "row_{$row}">
         <td id ="conditions_row_{$row}">
         {foreach name = 'alternatives_list' key = 'column' item = 'condition' from = $rowConditions}
         {if $T_OR_SPANS.$condition == 1}
          <span>{$smarty.const._OR}</span>
         {/if}
         {$T_JOB_DESCRIPTIONS_FORM.$condition.html}
         {/foreach}
         </td>
         <td align="center"><a id="training_add_{$row}" href="javascript:void(0);" onclick="add_prerequisite_alternative('{$row}', this);" class = "editLink"><img class="handle" src = "images/16x16/add.png" alt = "{$smarty.const._ADDALTERNATIVETRAINING}" title= "{$smarty.const._ADDALTERNATIVETRAINING}"/></a>&nbsp;<a id="training_{$row}" href="javascript:void(0);" onclick="delete_job_prerequisite('{$row}', this);" class = "deleteLink"><img class="handle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._DELETE}" title= "{$smarty.const._DELETE}"/></a></td>
        </tr>
        {/foreach}

       {else}
         <tr id="no_training_found">
         <td colspan=4 class = "emptyCategory">{$smarty.const._NOREQUIREDTRAININGSETYET}</td>
         </tr>
       {/if}

      {/if}
      </table>

    {/capture}
   {/if}

   {* Script for posting ajax requests regarding skills and lessons assignments *}
   {literal}
   <script>
   // id: the skill or lessons id
   // el: the element of the form corresponding to that skill/lesson
   // table_id: the id of the ajax-enabled table

   </script>
   {/literal}

    {* **************************************************************
    DISPLAYING THE CAPTURED TABLES
    ************************************************************** *}
    {capture name = 't_add_job_description_code'}
    <table border = "0" width = "100%" cellspacing = "5">
     <tr><td valign = "top">

     <div class="tabber">
      <div class="tabbertab">
       <h3>{$smarty.const._EDITJOBDESCRIPTION}</h3>
       {if $smarty.get.edit_job_description != ""}


        {eF_template_printBlock title = $smarty.const._JOBDESCRIPTIONDATA|cat:"<span class='innerTableName'>&nbsp;&quot;`$T_JOB_DESCRIPTION_NAME`&quot;</span>&nbsp;`$smarty.const._ATBRANCH`<span class='innerTableName'>&nbsp;&quot;`$T_JOB_DESCRIPTION_BRANCH_NAME`&quot;</span>" data = $smarty.capture.t_job_description_code image = '32x32/note.png'}
        {eF_template_printBlock title = $smarty.const._EMPLOYEES|cat:$smarty.const._HAVINGJOBDESCRIPTION|cat:"<span class='innerTableName'>&nbsp;&quot;`$T_JOB_DESCRIPTION_NAME`&quot;</span>&nbsp;`$smarty.const._ATBRANCH`<span class='innerTableName'>&nbsp;&quot;`$T_JOB_DESCRIPTION_BRANCH_NAME`&quot;</span>" data = $smarty.capture.t_employees_code image = '32x32/user.png'}
       {else}
        {eF_template_printBlock title = $smarty.const._NEWJOBDESCRIPTION data = $smarty.capture.t_job_description_code image = '32x32/note.png'}
       {/if}
      </div>

      {if $smarty.get.edit_job_description}
      <div class="tabbertab {if ($smarty.get.tab == "skills"  || isset($smarty.post.job_to_skills)) } tabbertabdefault {/if}">
       <h3>{$smarty.const._SKILLSREQUIRED}</h3>
       {eF_template_printBlock title = $smarty.const._SKILLSREQUIRED|cat:"&nbsp;`$smarty.const._FORTHEJOBDESCRIPTION`&nbsp;<span class='innerTableName'>&nbsp;&quot;`$T_JOB_DESCRIPTION_NAME`&quot;</span>&nbsp;`$smarty.const._ATBRANCH`<span class='innerTableName'>&nbsp;&quot;`$T_JOB_DESCRIPTION_BRANCH_NAME`&quot;</span>" data = $smarty.capture.t_job_to_skills image = '32x32/tools.png'}
      </div>

       {if $smarty.session.s_type == "administrator"}
        <div class="tabbertab {if ($smarty.get.tab == "lessons"  || isset($smarty.post.job_to_lessons)) } tabbertabdefault {/if}">
         <h3>{$smarty.const._ASSOCIATEDLESSONS}</h3>
         {eF_template_printBlock title = $smarty.const._ASSOCIATEDLESSONS|cat:"&nbsp;`$smarty.const._WITHTHEJOBDESCRIPTION`&nbsp;<span class='innerTableName'>&nbsp;&quot;`$T_JOB_DESCRIPTION_NAME`&quot;</span>&nbsp;`$smarty.const._ATBRANCH`<span class='innerTableName'>&nbsp;&quot;`$T_JOB_DESCRIPTION_BRANCH_NAME`&quot;</span>" data = $smarty.capture.t_job_to_lessons image = '32x32/lessons.png'}
        </div>

        <div class="tabbertab {if ($smarty.get.tab == "courses"  || isset($smarty.post.job_to_courses)) } tabbertabdefault {/if}">
         <h3>{$smarty.const._ASSOCIATEDCOURSES}</h3>
         {eF_template_printBlock title = $smarty.const._ASSOCIATEDCOURSES|cat:"&nbsp;`$smarty.const._WITHTHEJOBDESCRIPTION`&nbsp;<span class='innerTableName'>&nbsp;&quot;`$T_JOB_DESCRIPTION_NAME`&quot;</span>&nbsp;`$smarty.const._ATBRANCH`<span class='innerTableName'>&nbsp;&quot;`$T_JOB_DESCRIPTION_BRANCH_NAME`&quot;</span>" data = $smarty.capture.t_job_to_courses image = '32x32/courses.png'}
        </div>

        <div class="tabbertab {if $smarty.get.tab == "training"} tabbertabdefault {/if}">
         <h3>{$smarty.const._REQUIREDTRAINING}</h3>
         {eF_template_printBlock title = $smarty.const._REQUIREDTRAINING|cat:"&nbsp;`$smarty.const._FORTHEJOBDESCRIPTION`&nbsp;<span class='innerTableName'>&nbsp;&quot;`$T_JOB_DESCRIPTION_NAME`&quot;</span>&nbsp;`$smarty.const._ATBRANCH`<span class='innerTableName'>&nbsp;&quot;`$T_JOB_DESCRIPTION_BRANCH_NAME`&quot;</span>" data = $smarty.capture.t_job_prerequisites image = '32x32/courses.png'}
        </div>

       {/if}
      {/if}
      </div>

      </td>
       </tr>
    </table>
    {/capture}
    {eF_template_printBlock title = $smarty.const._JOBDESCRIPTIONDATA data = $smarty.capture.t_add_job_description_code image = '32x32/courses.png'}
 {else}
  {*moduleAllSkills: Show job_descriptions *}
  {capture name = 't_job_descriptions_code'}
   <div class = "headerTools">
    <span>
     <img src = "images/16x16/add.png" title = "{$smarty.const._NEWJOBDESCRIPTION}" alt = "{$smarty.const._NEWJOBDESCRIPTION}" >
        <a href = "{$smarty.server.PHP_SELF}?ctg=module_hcd&op=job_descriptions&add_job_description=1">{$smarty.const._NEWJOBDESCRIPTION}</a>
       </span>
   </div>
{if !$T_SORTED_TABLE || $T_SORTED_TABLE == 'jobsTable'}
<!--ajax:jobsTable-->
   <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "0" id = "jobsTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.session.s_type}.php?ctg=module_hcd&op=job_descriptions&">
    <tr class = "topTitle">
     <td class = "topTitle" name = "description" width="25%">{$smarty.const._JOBDESCRIPTION}</td>
     <td class = "topTitle" name = "name">{$smarty.const._BRANCHNAME}</td>
     <td class = "topTitle" name = "Employees" align="center">{$smarty.const._CURRENTLYEMPLOYEED}</td>
     <td class = "topTitle" name = "more_needed" align="center">{$smarty.const._VACANCIES}</td>
     <td class = "topTitle" name = "skill_req" align="center">{$smarty.const._SKILLSREQUIRED}</td>
     <td class = "topTitle noSort" align="center">{$smarty.const._OPERATIONS}</td>
    </tr>

    {foreach name = 'job_description_list' key = 'key' item = 'job_description' from = $T_DATA_SOURCE}
    <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
     <td><a href = "{$smarty.session.s_type}.php?ctg=module_hcd&op=job_descriptions&edit_job_description={$job_description.job_description_ID}" class = "editLink">{$job_description.description}</a></td>
     <td><a href = "{$smarty.session.s_type}.php?ctg=module_hcd&op=branches&edit_branch={$job_description.branch_ID}" class = "editLink">{$job_description.name}</a></td>
     <td class = "centerAlign"> {$job_description.Employees}</td>
     <td class = "centerAlign"> {$job_description.more_needed}</td>
     <td class = "centerAlign"> {$job_description.skill_req}</td>
     <td class = "centerAlign">
      <a href = "{$smarty.session.s_type}.php?ctg=module_hcd&op=job_descriptions&edit_job_description={$job_description.job_description_ID}" class = "editLink"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
      <img class = "ajaxHandle" src = "images/16x16/error_delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._AREYOUSUREYOUWANTTOREMOVETHATJOBDESCRIPTION}')) deleteJob(this, '{$job_description.job_description_ID}');"/>
     </td>
    </tr>
    {foreachelse}
    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "6">{$smarty.const._NODATAFOUND}</td></tr>
    {/foreach}
   </table>
<!--/ajax:jobsTable-->
{/if}
  {/capture}
  {eF_template_printBlock title = $smarty.const._UPDATEJOBDESCRIPTIONS data = $smarty.capture.t_job_descriptions_code image = '32x32/note.png'}
 {/if}
