<script>
translations['_YOUHAVEBEENSUCCESSFULLYADDEDTOTHEGROUP'] = '{$smarty.const._YOUHAVEBEENSUCCESSFULLYADDEDTOTHEGROUP}';
</script>

{if $T_OP == 'tests'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=lessons&op=tests">'|cat:$smarty.const._SKILLGAPTESTS|cat:'</a>'}
        {capture name = "moduleLessonsList"}
 <tr><td class = "moduleCell">
  {if isset($smarty.get.solve_test)}
         {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=lessons&op=tests&solve_test='|cat:$smarty.get.solve_test|cat:'">'|cat:$T_TEST_DATA->test.name|cat:'</a>'}

   {if $T_SHOW_CONFIRMATION}
    {capture name = "t_unsolved_skill_gap_code"}
                            <table class = "testHeader">
                                <tr><td id = "testName">{$T_TEST_DATA->test.name}</td></tr>
                                <tr><td id = "testDescription">{$T_TEST_DATA->test.description}</td></tr>
                                <tr><td>
                                        <table class = "testInfo">
                                            <tr><td rowspan = "3" id = "testInfoImage"><img src = "images/32x32/tests.png" alt = "{$T_TEST_DATA->test.name}" title = "{$T_TEST_DATA->test.name}"/></td>
                                                <td id = "testInfoLabels"></td>
                                                <td></td></tr>
                                            <tr><td>{$smarty.const._NUMOFQUESTIONS}:&nbsp;</td>
                                                <td>{$T_TEST_QUESTIONS_NUM}</td></tr>
                                            <tr><td>{$smarty.const._QUESTIONSARESHOWN}:&nbsp;</td>
                                                <td>{if $T_TEST_DATA->options.onebyone}{$smarty.const._ONEBYONEQUESTIONS}{else}{$smarty.const._ALLTOGETHER}{/if}</td></tr>
                                        </table>
                                    </td>
                                <tr><td id = "testProceed">
                                {if $T_TEST_STATUS.status == 'incomplete' && $T_TEST_DATA->time.pause}
                                    <input class = "flatButton" type = "button" name = "submit_sure" value = "{$smarty.const._RESUMETEST}&nbsp;&raquo;" onclick = "javascript:location=location+'&resume=1'" />
                                {else}
                                    <input class = "flatButton" type = "button" name = "submit_sure" value = "{$smarty.const._PROCEEDTOTEST}&nbsp;&raquo;" onclick = "javascript:location=location+'&confirm=1'" />
                                {/if}
                                </td></tr>
                            </table>
    {/capture}
    {eF_template_printBlock title=$T_TEST_DATA->test.name data = $smarty.capture.t_unsolved_skill_gap_code image='32x32/skill_gap.png'}

            {elseif $smarty.get.test_analysis}
                        {capture name = "t_test_analysis_code"}
                            <div class = "headerTools">
                                <img src = "images/16x16/arrow_left.png" alt = "{$smarty.const._VIEWSOLVEDTEST}" title = "{$smarty.const._VIEWSOLVEDTEST}">
                                <a href = "{$smarty.server.PHP_SELF}?ctg=tests&show_solved_test={$smarty.get.show_solved_test}">{$smarty.const._VIEWSOLVEDTEST}</a>
                            </div>
                            <table style = "width:100%">
                                <tr><td style = "vertical-align:top">{$T_CONTENT_ANALYSIS}</td></tr>
                                <tr><td style = "vertical-align:top"><iframe width = "100%" height = "300px" frameborder = "no" src = "student.php?ctg=content&view_unit={$smarty.get.view_unit}&display_chart=1&test_analysis=1"></iframe></td></tr>
                            </table>
                        {/capture}

                        {eF_template_printBlock title = "`$smarty.const._TESTANALYSIS` `$smarty.const._FORTEST` <span class = "innerTableName">&quot;`$T_TEST_DATA->test.name`&quot;</span> `$smarty.const._ANDUSER` <span class = "innerTableName">&quot;#filter:login-`$T_TEST_DATA->completedTest.login`#&quot;</span>" data = $smarty.capture.t_test_analysis_code image='32x32/tests.png'}
            {else}
    {capture name = "t_skill_gap_test_code"}
                    {if $T_TEST_STATUS.status == '' || $T_TEST_STATUS.status == 'incomplete'}
                        {capture name = "test_footer"}
                        <table class = "formElements" style = "width:100%">
                            <tr><td colspan = "2">&nbsp;</td></tr>
                            <tr><td colspan = "2" class = "submitCell" style = "text-align:center">{$T_TEST_FORM.submit_test.html}&nbsp;{$T_TEST_FORM.pause_test.html}</td></tr>
                        </table>
                        {/capture}
                    {/if}
                    {if !$T_NO_TEST}
                        {$T_TEST_FORM.javascript}
                        <form {$T_TEST_FORM.attributes}>
                            {$T_TEST_FORM.hidden}
                            {$T_TEST}
                            {$smarty.capture.test_footer}
                        </form>
                 {/if}
    {/capture}
    {eF_template_printBlock title=$T_TEST_DATA->test.name data = $smarty.capture.t_skill_gap_test_code image='32x32/skill_gap.png'}
            {/if}
         {else}

            {if $T_TESTS}
             {eF_template_printBlock title=$smarty.const._SKILLGAPTESTSTOBECOMPLETED columns=3 links=$T_TESTS image='32x32/skill_gap.png'}
            {else}
    {capture name = "t_no_skill_gap_test_code"}
                 <table width = "100%">
                     <tr><td class = "emptyCategory">{$smarty.const._NOSKILLGAPTESTSASSIGNEDTOYOU}</td></tr>
                 </table>
    {/capture}
    {eF_template_printBlock title=$smarty.const._NOSKILLGAPTESTSASSIGNEDTOYOU data = $smarty.capture.t_no_skill_gap_test_code image='32x32/skill_gap.png'}
            {/if}
         {/if}

 </td></tr>
        {/capture}
{else}

 {capture name = "moduleLessonsList"}
 <tr><td class = "moduleCell">
  {if $smarty.get.course}
      {include file = "includes/course_settings.tpl"}
  {else}
   {capture name = "t_group_key_code"}
    <table>
           <tr><td colspan = "2">&nbsp;</td></tr>
           <tr><td class = "labelCell">{$smarty.const._UNIQUEGROUPKEY}:&nbsp;</td>
               <td class = "elementCell"><input class = "inputText" type = "text" id = "group_key" /></td></tr>
           <tr><td colspan = "2">&nbsp;</td></tr>
           <tr><td></td>
            <td class = "submitCell"><input class = "flatButton" type = "button" onclick = "addGroupKey(this)" value="{$smarty.const._SUBMIT}" /></td></tr>
           <tr><td colspan = "2"><span id = "resultReport"></span><img id = "progressImg" src = "images/others/progress_big.gif" style = "display:none"/></td></tr>
           <tr><td colspan = "2">&nbsp;</td></tr>
           <tr><td colspan = "2" class = "horizontalSeparatorAbove">{$smarty.const._ENTERGROUPKEYINFO}</td></tr>
       </table>
   {/capture}
   <div id = 'group_key_enter' style = "display:none;">
     {eF_template_printBlock title = $smarty.const._ENTERGROUPKEY data = $smarty.capture.t_group_key_code image = '32x32/key.png'}
   </div>


       {if $smarty.get.catalog}
    {if $smarty.get.checkout}
     {include file = "includes/blocks/cart.tpl" assign = "cart"}
     {eF_template_printBlock title = $smarty.const._SELECTEDLESSONS data = $cart image = '32x32/catalog.png'}
    {else}
     {if $smarty.get.info_lesson || $smarty.get.info_course}
         {capture name = 't_lesson_info_code'}
          {include file = "includes/blocks/lessons_info.tpl"}
         {/capture}
         {if $T_LESSON_INFO}
       {assign var = "title" value = "`$title`<span>&nbsp;&raquo;&nbsp;</span><a href = '`$smarty.server.PHP_SELF`?ctg=lessons'>`$smarty.const._LESSONS`</a><span>&nbsp;&raquo;&nbsp;</span><a href = '`$smarty.server.PHP_SELF`?ctg=lesson_info&lessons_ID=`$smarty.get.lessons_ID`&course=`$smarty.get.course`'>`$smarty.const._INFOFORLESSON`: &quot;`$T_LESSON->lesson.name`&quot;</a>"}
       {assign var = "lesson_title" value = "`$smarty.const._INFORMATIONFORLESSON` <span class = 'innerTableName'>&quot;`$T_LESSON->lesson.name`&quot;</span>"}
      {elseif $T_COURSE_INFO}
       {assign var = "title" value = "`$title`<span>&nbsp;&raquo;&nbsp;</span><a href = '`$smarty.server.PHP_SELF`?ctg=lessons'>`$smarty.const._LESSONS`</a><span>&nbsp;&raquo;&nbsp;</span><a href = '`$smarty.server.PHP_SELF`?ctg=lesson_info&courses_ID=`$smarty.get.courses_ID`'>`$smarty.const._INFOFORCOURSE`: &quot;`$T_COURSE->course.name`&quot;</a>"}
       {assign var = "lesson_title" value = "`$smarty.const._INFORMATIONFORCOURSE` <span class = 'innerTableName'>&quot;`$T_COURSE->course.name`&quot;</span>"}
      {/if}
         {eF_template_printBlock title = $lesson_title data = $smarty.capture.t_lesson_info_code image = "32x32/information.png"}
     {else}
      {*
      {capture name = "t_catalog_code"}
       {eF_template_printCatalog source = $T_DIRECTIONS}
      {/capture}
      {eF_template_printBlock title = $smarty.const._COURSECATALOG data = $smarty.capture.t_catalog_code image = '32x32/catalog.png'}
      *}
      {eF_template_printBlock title = $smarty.const._COURSECATALOG data = $T_DIRECTIONS_TREE image = '32x32/catalog.png'}
     {/if}
     {capture name = "moduleSideOperations"}
      {include file = "includes/blocks/cart.tpl" assign = "cart"}
         {eF_template_printBlock title = $smarty.const._SELECTEDLESSONS content = $cart image = "32x32/shopping_basket.png"}
     {/capture}
    {/if}
       {elseif $T_DIRECTIONS_TREE}

        {*Inner table modules *}
       {foreach name = 'module_inner_tables_list' key = key item = moduleItem from = $T_INNERTABLE_MODULES}
           {capture name = $key|replace:"_":""} {*We cut off the underscore, since scriptaculous does not seem to like them*}
               <tr><td class = "moduleCell">
                   {if $moduleItem.smarty_file}
                       {include file = $moduleItem.smarty_file}
                   {else}
                       {$moduleItem.html_code}
                   {/if}
               </td></tr>
           {/capture}
       {/foreach}

        {capture name = "t_directions_tree_code"}
      <table class = "lessonListOptions">
        <tr>
      {if $T_SUPERVISOR_APPROVALS}
                 <td class = "lessonListOption"><a href = "javascript:void(0)" title="{$smarty.const._SUPERVISORAPPROVAL}" onclick = "eF_js_showDivPopup('{$smarty.const._SUPERVISORAPPROVAL}', 2, 'supervisor_approvals_list')"><img class = "handle" src = "images/32x32/success.png" title = "{$smarty.const._SUPERVISORAPPROVAL}" alt = "{$smarty.const._SUPERVISORAPPROVAL}"></a></td>
                 <td>
                  <script>translations['_SOMEPROBLEMOCCURED'] = '{$smarty.const._SOMEPROBLEMOCCURED}';</script>
                  <a href = "javascript:void(0)" title="{$smarty.const._SUPERVISORAPPROVAL}" onclick = "eF_js_showDivPopup('{$smarty.const._SUPERVISORAPPROVAL}', 1, 'supervisor_approvals_list')">{$smarty.const._SUPERVISORAPPROVAL}</a>
                  <div id = "supervisor_approvals_list" style = "display:none">
                   {capture name = "t_supervisor_approvals_code"}
                   <table style = "width:100%">
                  {foreach name = 'supervisor_approval_list' item = 'item' key = 'key' from = $T_SUPERVISOR_APPROVALS}
                    <tr><td>{$item.name}</td>
                     <td>#filter:login-{$item.users_LOGIN}#</td>
                     <td><img src = "images/16x16/success.png" class = "ajaxHandle" alt = "{$smarty.const._APPROVE}" title = "{$smarty.const._APPROVE}" onclick = "approveCourseAssignment(this, '{$item.id}', '{$item.users_LOGIN}')" /></td>
                     <td><img src = "images/16x16/forbidden.png" class = "ajaxHandle" alt = "{$smarty.const._CANCEL}" title = "{$smarty.const._CANCEL}" onclick = "cancelCourseAssignment(this, '{$item.id}', '{$item.users_LOGIN}')" /></td></tr>
                  {/foreach}
                   </table>
                   {/capture}
                   {eF_template_printBlock title = $smarty.const._SUPERVISORAPPROVAL data = $smarty.capture.t_supervisor_approvals_code image = '32x32/success.png'}
                  </div>
                 </td>
      {/if}
      {if $T_CURRENT_USER->coreAccess.dashboard != 'hidden'}
                 <td class = "lessonListOption"><a href = "{$smarty.server.PHP_SELF}?ctg=personal" title="{$smarty.const._MYACCOUNT}"><img class = "handle" src = "images/32x32/user.png" title = "{$smarty.const._MYACCOUNT}" alt = "{$smarty.const._MYACCOUNT}"></a></td>
                 <td><a href = "{$smarty.server.PHP_SELF}?ctg=personal" title="{$smarty.const._MYACCOUNT}">{$smarty.const._MYACCOUNT}</a></td>
      {/if}
            {if $T_SKILLGAP_TESTS}
                 <td class = "lessonListOption"><a href = "student.php?ctg=lessons&op=tests" title="{$T_SKILLGAP_TESTS}"><img class = "handle" src = "images/32x32/skill_gap.png" title = "{$T_SKILLGAP_TESTS}" alt = "{$T_SKILLGAP_TESTS}" ></a></td>
                 <td><a href = "student.php?ctg=lessons&op=tests" title="{$T_SKILLGAP_TESTS}">{$smarty.const._NEWSKILLGAPTESTS}</a></td>
            {/if}

       {if $T_CONFIGURATION.insert_group_key != 0}
                  <td class = "lessonListOption"><a href = "javascript:void(0)" onclick = "eF_js_showDivPopup('{$smarty.const._ENTERGROUPKEY}', 0, 'group_key_enter')" title = "{$smarty.const._ENTERGROUPKEY}"><img class = "handle" src = "images/32x32/key.png" title = "{$smarty.const._ENTERGROUPKEY}" alt = "{$smarty.const._ENTERGROUPKEY}" /></a></td>
                    <td><a href = "javascript:void(0)" onclick = "eF_js_showDivPopup('{$smarty.const._ENTERGROUPKEY}', 0, 'group_key_enter')" title = "{$smarty.const._ENTERGROUPKEY}">{$smarty.const._ENTERGROUPKEY}</a></td>
                {/if}

            {if $T_CONFIGURATION.lessons_directory != 0}
                 <td class = "lessonListOption"><a href = "{$smarty.server.PHP_SELF}?ctg=lessons&catalog=1"><img class = "handle" src = "images/32x32/catalog.png" title = "{$smarty.const._LESSONSDIRECTORY}" alt = "{$smarty.const._LESSONSDIRECTORY}" ></a></td>
                 <td><a href = "{$smarty.server.PHP_SELF}?ctg=lessons&catalog=1">{$smarty.const._COURSECATALOG}</a></td>
            {/if}






       </tr>
            </table>

        {$T_DIRECTIONS_TREE}
    {/capture}
    {*///MODULES INNERTABLES APPEARING*}
    {foreach name = 'module_inner_tables_list' key = key item = module from = $T_INNERTABLE_MODULES}
           {assign var = module_name value = $key|replace:"_":""}
                            <table class = "singleColumnData">
                                {$smarty.capture.$module_name}
                            </table>




       {/foreach}
    {eF_template_printBlock title = $smarty.const._MYCOURSES data = $smarty.capture.t_directions_tree_code image = '32x32/theory.png'}

   {elseif $T_OP == 'search'}
           {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="javascript:void(0)" onclick = "location.reload()">'|cat:$smarty.const._SEARCHRESULTS|cat:'</a>'}
           {*moduleSearchResults: The Search results page*}
               {capture name = "moduleSearchResults"}
                <tr><td class = "moduleCell">
                        {include file = "includes/module_search.tpl"}
                </td></tr>
               {/capture}

   {else}
    {capture name = "t_empty_lessons_list_code"}
     <table class = "emptyLessonsList">
         <tr><td class = "mediumHeader">{$smarty.const._YOUDONTHAVEANYLESSONS}</td></tr>

     {if $T_CURRENT_USER->coreAccess.dashboard != 'hidden'}
               <tr><td class = "lessonListOption">
        <a href = "{$smarty.server.PHP_SELF}?ctg=personal" title="{$smarty.const._MYACCOUNT}"><img class = "handle" src = "images/32x32/user.png" title = "{$smarty.const._MYACCOUNT}" alt = "{$smarty.const._MYACCOUNT}" ></a>
                <div><a href = "{$smarty.server.PHP_SELF}?ctg=personal" title="{$smarty.const._MYACCOUNT}">{$smarty.const._MYACCOUNT}</a></div>
               </td></tr>
     {/if}

     {if $T_CONFIGURATION.lessons_directory}
               <tr><td class = "lessonListOption">
                   <a href = "{$smarty.server.PHP_SELF}?ctg=lessons&catalog=1" title = "{$smarty.const._LESSONSDIRECTORY}"><img class = "handle" src = "images/32x32/catalog.png" title = "{$smarty.const._LESSONSDIRECTORY}" alt = "{$smarty.const._LESSONSDIRECTORY}"></a>
                   <div><a href = "{$smarty.server.PHP_SELF}?ctg=lessons&catalog=1">{$smarty.const._LESSONSDIRECTORY}</a></div>
               </td></tr>
           {/if}
           {if $T_CONFIGURATION.insert_group_key}
               <tr><td class = "lessonListOption">
                   <a href = "javascript:void(0)" onclick = "eF_js_showDivPopup('{$smarty.const._ENTERGROUPKEY}', 0, 'group_key_enter')" title = "{$smarty.const._ENTERGROUPKEY}"><img class = "handle" src = "images/32x32/key.png" title = "{$smarty.const._ENTERGROUPKEY}" alt = "{$smarty.const._ENTERGROUPKEY}" /></a>
                   <div><a href = "javascript:void(0)" onclick = "eF_js_showDivPopup('{$smarty.const._ENTERGROUPKEY}', 0, 'group_key_enter')" title = "{$smarty.const._ENTERGROUPKEY}">{$smarty.const._ENTERGROUPKEY}</a></div>
               </td></tr>
           {/if}
           {if $T_SKILLGAP_TESTS}
               <tr><td class = "mediumHeader lessonListOption">{$smarty.const._COMPLETETHESKILLGAPTESTSBELOWSOTHATWECANASSIGNLESSONS}</td></tr>
               <tr><td>
                <a href = "student.php?ctg=lessons&op=tests" title="{$T_SKILLGAP_TESTS}"><img class = "handle" src = "images/32x32/skill_gap.png" title = "{$T_SKILLGAP_TESTS}" alt = "{$T_SKILLGAP_TESTS}" ></a>
                <div><a href = "student.php?ctg=lessons&op=tests" title="{$T_SKILLGAP_TESTS}">{$smarty.const._NEWSKILLGAPTESTS}</a></div>
           {elseif $T_SKILLGAP_TESTS_SOLVED}
               <tr><td class = "mediumHeader lessonListOption">{$smarty.const._YOUHAVECOMPLETEDALLSKILLGAPSTESTSASSIGNEDTOYOUWAITTOBEASSIGNEDLESSONS}</td></tr>
           {elseif !$T_CONFIGURATION.lessons_directory && !$T_CONFIGURATION.insert_group_key}
            <tr><td class = "mediumHeader lessonListOption">{$smarty.const._THEADMINISTRATORWILLASSIGNYOULESSONS}</td></tr>
           {/if}
        </table>
       {/capture}
       {eF_template_printBlock title = $smarty.const._MYCOURSES data = $smarty.capture.t_empty_lessons_list_code image = '32x32/catalog.png'}
   {/if}
  {/if}
 </td></tr>
 {/capture}
{/if}
