{* Translations *}
<script>
var sessionType = "{$smarty.session.s_type}";
var editedUser = "{$smarty.get.user}";
var setAssociatedDirections = '{$smarty.const._SELECTASSOCIATEDDIRECTIONSCOURSESANDLESSONS}';
var setAssociatedSkills = '{$smarty.const._SELECTASSOCIATEDSKILLSORSKILLCATEGORIES}';
var noQuestionsDefinedForLesson = '{$smarty.const._NOQUESTIONSDEFINEDFORTHISLESSON}';
var noQuestionsDefinedForSkill = '{$smarty.const._NOQUESTIONSDEFINEDFORTHISSKILL}';
var theFieldNameIsMandatory = "{$smarty.const._THEFIELD} {$smarty.const._NAME} {$smarty.const._ISMANDATORY}";
var noQuestionSelection = "{$smarty.const._NOQUESTIONSELECTIONSHAVEBEENMADE}";
var doYouWantToFurtherEdit = "{$smarty.const._DOYOUWANTTOFURTHEREDITTHETEST}";
var noQuestionsFound = "{$smarty.const._NOQUESTIONSFOUND}";
var deleteConst ='{$smarty.const._DELETE}';
translations['_YOUCANNOTREMOVETHELASTELEMENT'] = '{$smarty.const._YOUCANNOTREMOVETHELASTELEMENT}';
translations['_SEPARATEWORDSWITHPIPE'] = '{$smarty.const._SEPARATEWORDSWITHPIPE}';
</script>


{if $smarty.get.add_test && $smarty.get.create_quick_test}

<script>
var quickformLessonCourses = '{$T_QUICKFORM_LESSON_COURSES_SELECT|replace:"\n":""}';
var quickformSkills = '{$T_QUICKFORM_SKILLS_SELECT|replace:"\n":""}';
var quickformeducationalCount = '{$T_QUICKTEST_FORM.educational_questions_count_row.html|replace:"\n":""}';
var quickformSkillQuestCount = '{$T_QUICKTEST_FORM.skill_questions_count_row.html|replace:"\n":""}';
</script>

    {$T_QUICKTEST_FORM.javascript}
    <form {$T_QUICKTEST_FORM.attributes}>
    {capture name = 't_create_quick_test_code'}
     {*<div class = "tabber">
         <div class = "tabbertab" id="test_options" title = "{$smarty.const._TESTOPTIONS}">*}
                {$T_QUICKTEST_FORM.hidden}
                <table class = "formElements">
                    <tr><td class = "labelCell">{$smarty.const._NAME}:&nbsp;</td>
                        <td class = "elementCell">{$T_QUICKTEST_FORM.name.html}&nbsp;*</td></tr>
                    {if $T_QUICKTEST_FORM.name.error}<tr><td></td><td class = "formError">{$T_QUICKTEST_FORM.name.error}</td></tr>{/if}
{*
                <tr><td class = "labelCell">{$smarty.const._TOTALQUESTIONS}:&nbsp;</td>
                    <td class = "elementCell">{$T_QUICKTEST_FORM.total_questions.html}</td></tr>
                {if $T_QUICKTEST_FORM.total_questions.error}<tr><td></td><td class = "formError">{$T_QUICKTEST_FORM.total_questions.error}</td></tr>{/if}


                <tr><td class = "labelCell">{$smarty.const._ASSIGNTOALLNEWSTUDENTS}:&nbsp;</td>
                    <td class = "elementCell">{$T_QUICKTEST_FORM.assign_to_new.html}</td></tr>
                <tr><td class = "labelCell">{$smarty.const._AUTOMATICALLYASSIGNLESSONS}:&nbsp;</td>
                    <td class = "elementCell"><table align="left"><tr><td>{$T_QUICKTEST_FORM.automatic_assignment.html}</td>
                    <td align="left"><img src = "images/16x16/help.png" alt = "help" title = "help" onclick = "eF_js_showHideDiv(this, 'automatic_assignment_info', event)"><div id = 'automatic_assignment_info' onclick = "eF_js_showHideDiv(this, 'automatic_assignment_info', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:450px;height:50px;position:absolute;display:none">{$smarty.const._AUTOMATICASSIGNMENTINFO}</div></td></tr></table></td>
*}
                <tr><td class = "labelCell">{$smarty.const._DESCRIPTION}:&nbsp;</td>
                    <td class = "elementCell">{$T_QUICKTEST_FORM.description.html}</td></tr>
                {if $T_QUICKTEST_FORM.description.error}<tr><td></td><td class = "formError">{$T_QUICKTEST_FORM.description.error}</td></tr>{/if}
                {*
                <tr><td colspan = "2">&nbsp;</td></tr>
                <tr><td></td>
                    <td class = "elementCell">

                    </td>
                </tr>
                *}
            </table>
 {/capture}

 {capture name = 't_create_quick_test_code_lessons'}
  <script>var __criteria_total_number = 0;</script>
    {*</div>*}

    {*<div class = "tabbertab" id="test_options" title = "{$smarty.const._ASSOCIATEDLESSONS}">*}
     <table>
   <tr>
    <td><a href="javascript:void(0);" onclick="add_new_criterium_row(0, 'lessons')"><img src="images/16x16/add.png" title="{$smarty.const._NEWSELECTION}" alt="{$smarty.const._NEWSELECTION}"/ border="0"></a></td><td><a href="javascript:void(0);" onclick="add_new_criterium_row(0, 'lessons')">{$smarty.const._NEWSELECTION}</a></td>
   </tr>
  </table>
     <table id = "lessonsTable" class="sortedTable" width="100%" noFooter="true">
     <tr class = "topTitle">
      <td class = "topTitle noSort" width= "40%"><span style="color:maroon">{$smarty.const._DIRECTION}</span>, <span style="color:green">{$smarty.const._COURSE}</span>&nbsp;{$smarty.const._OR}&nbsp;{$smarty.const._LESSON}&nbsp;({$smarty.const._QUESTIONS})</td>
   <td class = "topTitle noSort" width= "40%">{$smarty.const._QUESTIONS}</td>
   <td class = "topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
  </tr>

     <tr id= 'no_lessons_criteria_defined'><td class= "emptyCategory" colspan="3" >{$smarty.const._SELECTASSOCIATEDDIRECTIONSCOURSESANDLESSONS}</td></tr>
     </table>
    {*</div>*}
    {/capture}
 {*</div>*}
 {eF_template_printBlock title = $smarty.const._ADDQUICKSKILLGAP data = $smarty.capture.t_create_quick_test_code image = '32x32/wizard.png'}
 <br />
 {eF_template_printBlock title = $smarty.const._SELECTQUESTIONSBASEDONLESSONS data = $smarty.capture.t_create_quick_test_code_lessons image = '32x32/lessons.png'}
 <br />
 <table width ="100%">
  <tr><td align="center"><input type="submit" value="{$smarty.const._CREATETEST}" name="submit_test" class="flatButton" onClick = "if(checkQuickTestForm()) return true; else return false;"/></td></tr>
    </table>
    <br />
{elseif $smarty.get.add_test || $smarty.get.edit_test}
 <script type="text/javascript">var tinyMCEmode = true;</script>
    {capture name = "t_test_properties"}
        {$T_TEST_FORM.javascript}
        <form {$T_TEST_FORM.attributes}>
            {$T_TEST_FORM.hidden}
            <table class = "formElements" >
   {if $T_CTG != "feedback"}
    {if $smarty.get.edit_test && $T_CONFIGURATION.use_sso == 'sumtotal'}
     <tr><td class = "labelCell">{$smarty.const._HACPURL}:&nbsp;</td>
      <td class = "elementCell">{$smarty.const.G_SERVERNAME}hacp.php?sso=sumtotal&view_unit={$T_CURRENT_TEST->test.content_ID}</td></tr>
    {/if}
    {if $T_TEST_FORM.parent_content}
     <tr><td class = "labelCell">{$T_TEST_FORM.parent_content.label}:&nbsp;</td>
      <td class = "elementCell">{$T_TEST_FORM.parent_content.html}</td></tr>
     {if $T_TEST_FORM.parent_content.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.parent_content.error}</td></tr>{/if}
    {/if}
     <tr><td class = "labelCell">{$smarty.const._NAME}:&nbsp;</td>
      <td class = "elementCell">{$T_TEST_FORM.name.html}</td></tr>
     {if $T_TEST_FORM.name.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.name.error}</td></tr>{/if}
     <tr><td class = "labelCell">{$smarty.const._DURATIONINMINUTES}:&nbsp;</td>
      <td class = "elementCell">{$T_TEST_FORM.duration.html}&nbsp;<span class = "infoCell">{$smarty.const._BLANKFORNOLIMIT}</span></td></tr>
     {if $T_TEST_FORM.duration.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.duration.error}</td></tr>{/if}
    {if !$T_SKILLGAP_TEST}
     <tr><td class = "labelCell">{$smarty.const._REDOABLE}:&nbsp;</td>
      <td class = "elementCell">{$T_TEST_FORM.redoable.html} <span class = "infoCell">{$smarty.const._BLANKFORUNLIMITED}</span></td></tr>
     {if $T_TEST_FORM.redoable.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.redoable.error}</td></tr>{/if}
     <tr><td class = "labelCell">{$smarty.const._MAINTAINHISTORY}:&nbsp;</td>
      <td class = "elementCell">{$T_TEST_FORM.maintain_history.html}<span> {$smarty.const._REPETITIONS} </span><span class = "infoCell">({$smarty.const._BLANKFORUNLIMITED})</span></td></tr>
     {if $T_TEST_FORM.mastery_score.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.mastery_score.error}</td></tr>{/if}
     <tr><td class = "labelCell">{$smarty.const._MASTERYSCORE}:&nbsp;</td>
      <td class = "elementCell">{$T_TEST_FORM.mastery_score.html} %</td></tr>
     {if $T_TEST_FORM.mastery_score.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.mastery_score.error}</td></tr>{/if}
    {else}
     <tr><td class = "labelCell">{$smarty.const._GENERALTHRESHOLD}:&nbsp;</td>
      <td class = "elementCell">{$T_TEST_FORM.general_threshold.html}</td></tr>
     {if $T_TEST_FORM.general_threshold.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.general_threshold.error}</td></tr>{/if}
     <tr><td class = "labelCell">{$smarty.const._ASSIGNTOALLNEWSTUDENTS}:&nbsp;</td>
      <td class = "elementCell">{$T_TEST_FORM.assign_to_new.html}</td></tr>
     <tr><td class = "labelCell">{$smarty.const._AUTOMATICALLYASSIGNLESSONS}:&nbsp;</td>
      <td class = "elementCell">{$T_TEST_FORM.automatic_assignment.html}<img src = "images/16x16/help.png" alt = "help" title = "help" onclick = "eF_js_showHideDiv(this, 'automatic_assignment_info', event)"><div id = 'automatic_assignment_info' onclick = "eF_js_showHideDiv(this, 'automatic_assignment_info', event)" class = "popUpInfoDiv" style = "display:none">{$smarty.const._AUTOMATICASSIGNMENTINFO}</div></td>
     <tr><td class = "labelCell">{$smarty.const._DISPLAYRESULTSTOSTUDENTS}:&nbsp;</td>
      <td class = "elementCell">{$T_TEST_FORM.student_results.html}</td>
    {/if}
     <tr><td></td><td class = "elementCell">
      <span>
       <a href = "javascript:void(0)" onclick = "toggleAdvancedParameters();"><img class = "handle" id = "advenced_parameter_image" src = "images/16x16/navigate_down.png" alt = "{$smarty.const._TOGGLEADVENCEDPARAMETERS}" title = "{$smarty.const._TOGGLEADVENCEDPARAMETERS}"/>&nbsp;{$smarty.const._TOGGLEADVENCEDPARAMETERS}</a>
      </span>
     </td></tr>
     <tr style="display:none;" id = "publish"><td class = "labelCell">{$smarty.const._PUBLISH}:&nbsp;</td>
      <td class = "elementCell">{$T_TEST_FORM.publish.html}</td></tr>
     {if $T_TEST_FORM.publish.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.publish.error}</td></tr>{/if}
     <tr style="display:none;" id = "onebyone"><td class = "labelCell">{$smarty.const._ONEBYONE}:&nbsp;</td>
      <td class = "elementCell">{$T_TEST_FORM.onebyone.html}</td></tr>
     {if $T_TEST_FORM.onebyone.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.onebyone.error}</td></tr>{/if}
     <tr style="display:none;" id = "only_forward"><td class = "labelCell">{$smarty.const._ONLYFORWARD}:&nbsp;</td>
      <td class = "elementCell">{$T_TEST_FORM.only_forward.html} <span class = "infoCell">{$smarty.const._APPLICABLETOONEBYONE}</span></td></tr>
     {if $T_TEST_FORM.only_forward.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.only_forward.error}</td></tr>{/if}
    {if !$T_SKILLGAP_TEST}
<!-- <tr style="display:none;" id = "given_answers"><td class = "labelCell" style = "white-space:normal">{$smarty.const._SHOWGIVENANSWERS}:&nbsp;</td>
      <td class = "elementCell">{$T_TEST_FORM.given_answers.html}</td></tr>
     {if $T_TEST_FORM.given_answers.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.given_answers.error}</td></tr>{/if}
     <tr style="display:none;" id = "show_score"><td class = "labelCell" style = "white-space:normal">{$smarty.const._SHOWSCORE}:&nbsp;</td>
      <td class = "elementCell">{$T_TEST_FORM.show_score.html}</td></tr>
     {if $T_TEST_FORM.show_score.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.show_score.error}</td></tr>{/if}
     <tr style="display:none;" id = "answers"><td class = "labelCell" style = "white-space:normal">{$smarty.const._SHOWRIGHTANSWERS}:&nbsp;</td>
      <td class = "elementCell">{$T_TEST_FORM.answers.html}</td></tr>
     {if $T_TEST_FORM.answers.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.answers.error}</td></tr>{/if}
     <tr style="display:none;" id = "show_answers_if_pass"><td class = "labelCell" style = "white-space:normal">{$smarty.const._SHOWANSWERSIFSTUDENTPASSED}:&nbsp;</td>
      <td class = "elementCell">{$T_TEST_FORM.show_answers_if_pass.html}</td></tr>
     {if $T_TEST_FORM.show_answers_if_pass.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.show_answers_if_pass.error}</td></tr>{/if}
     <tr style="display:none;" id = "redirect"><td class = "labelCell" style = "white-space:normal">{$smarty.const._DONOTSHOWTESTAFTERSUBMITTING}:&nbsp;</td>
      <td class = "elementCell">{$T_TEST_FORM.redirect.html}</td></tr>
     {if $T_TEST_FORM.redirect.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.redirect.error}</td></tr>{/if}
-->
<tr style="display:none;" id = "action_on_submit"><td class = "labelCell" style = "white-space:normal">{$T_TEST_FORM.action_on_submit.label}:&nbsp;</td>
      <td class = "elementCell">{$T_TEST_FORM.action_on_submit.html}</td></tr>
     {if $T_TEST_FORM.action_on_submit.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.action_on_submit.error}</td></tr>{/if}
    {/if}
     <tr style="display:none;" id = "shuffle_answers"><td class = "labelCell">{$smarty.const._SHUFFLEANSWERS}:&nbsp;</td>
      <td class = "elementCell">{$T_TEST_FORM.shuffle_answers.html}</td></tr>
     {if $T_TEST_FORM.shuffle_answers.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.shuffle_answers.error}</td></tr>{/if}
     <tr style="display:none;" id = "shuffle_questions"><td class = "labelCell">{$smarty.const._SHUFFLEQUESTIONS}:&nbsp;</td>
      <td class = "elementCell">{$T_TEST_FORM.shuffle_questions.html}</td></tr>
     {if $T_TEST_FORM.shuffle_questions.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.shuffle_questions.error}</td></tr>{/if}
     <tr style="display:none;" id = "display_list"><td class = "labelCell">{$smarty.const._DISPLAYORDEREDLIST}:&nbsp;</td>
      <td class = "elementCell">{$T_TEST_FORM.display_list.html} <span class = "infoCell">{$smarty.const._DISPLAYORDEREDLISTINFO}</span></td></tr>
    {if !$T_SKILLGAP_TEST}
     <tr style="display:none;" id = "pause_test"><td class = "labelCell">{$smarty.const._TESTCANBEPAUSED}:&nbsp;</td>
      <td class = "elementCell">{$T_TEST_FORM.pause_test.html}</td></tr>
     {if $T_TEST_FORM.pause_test.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.pause_test.error}</td></tr>{/if}
     <tr style="display:none;" id = "display_weights"><td class = "labelCell">{$smarty.const._DISPLAYQUESTIONWEIGHTS}:&nbsp;</td>
      <td class = "elementCell">{$T_TEST_FORM.display_weights.html}</td></tr>
     {if $T_TEST_FORM.display_weights.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.display_weights.error}</td></tr>{/if}
     <tr style="display:none;" id = "answer_all"><td class = "labelCell">{$smarty.const._FORCEUSERANSERALLQUESTIONS}:&nbsp;</td>
      <td class = "elementCell">{$T_TEST_FORM.answer_all.html}</td></tr>
     {if $T_TEST_FORM.answer_all.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.answer_all.error}</td></tr>{/if}
     <tr style="display:none;" id = "keep_best"><td class = "labelCell">{$smarty.const._RETAINBESTEXECUTION}:&nbsp;</td>
      <td class = "elementCell">{$T_TEST_FORM.keep_best.html}</td></tr>
     {if $T_TEST_FORM.keep_best.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.keep_best.error}</td></tr>{/if}
    {/if}
   {else}
    {if $T_TEST_FORM.parent_content}
     <tr><td class = "labelCell">{$T_TEST_FORM.parent_content.label}:&nbsp;</td>
      <td class = "elementCell">{$T_TEST_FORM.parent_content.html}</td></tr>
     {if $T_TEST_FORM.parent_content.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.parent_content.error}</td></tr>{/if}
    {/if}
    <tr><td class = "labelCell">{$smarty.const._NAME}:&nbsp;</td>
     <td class = "elementCell">{$T_TEST_FORM.name.html}</td></tr>
     {if $T_TEST_FORM.name.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.name.error}</td></tr>{/if}
    <tr id = "publish"><td class = "labelCell">{$smarty.const._PUBLISH}:&nbsp;</td>
      <td class = "elementCell">{$T_TEST_FORM.publish.html}</td></tr>
   {/if}
     <tr><td></td><td id = "toggleeditor_cell1">
      <div class = "headerTools">
       <span>
        <img onclick = "toggleFileManager(Element.extend(this).next());" class = "ajaxHandle" id = "arrow_down" src = "images/16x16/navigate_down.png" alt = "{$smarty.const._OPENCLOSEFILEMANAGER}" title = "{$smarty.const._OPENCLOSEFILEMANAGER}"/>&nbsp;
        <a href = "javascript:void(0)" onclick = "toggleFileManager(this);">{$smarty.const._TOGGLEFILEMANAGER}</a>
       </span>
       <span>
        <img onclick = "toggledInstanceEditor = 'editor_content_data';javascript:toggleEditor('editor_content_data','mceEditor');" class = "ajaxHandle" src = "images/16x16/order.png" title = "{$smarty.const._TOGGLEHTMLEDITORMODE}" alt = "{$smarty.const._TOGGLEHTMLEDITORMODE}" />&nbsp;
        <a href = "javascript:void(0)" onclick = "toggledInstanceEditor = 'editor_content_data';javascript:toggleEditor('editor_content_data','mceEditor');" id = "toggleeditor_link">{$smarty.const._TOGGLEHTMLEDITORMODE}</a>
       </span>
      </div>
      </td></tr>
     <tr><td></td><td id = "filemanager_cell"></td></tr>
     <tr><td class = "labelCell">{$smarty.const._DESCRIPTION}:&nbsp;</td>
      <td class = "elementCell">{$T_TEST_FORM.description.html}</td></tr>
     {if $T_TEST_FORM.description.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.description.error}</td></tr>{/if}
     <tr><td colspan = "2">&nbsp;</td></tr>
     <tr><td></td>
      <td class = "elementCell">
       {$T_TEST_FORM.submit_test.html}&nbsp;
       {if $smarty.get.edit_test}{$T_TEST_FORM.submit_test_new.html}{/if}
      </td></tr>
   </table>
  </form>
  <div id = "fmInitial"><div id = "filemanager_div" style = "display:none;">{$T_FILE_MANAGER}</div></div>
 {/capture}
 {capture name = 't_random_test_wizard}
  <script>var hoursshorthand = '{$smarty.const._HOURSSHORTHAND}';var minutesshorthand = '{$smarty.const._MINUTESSHORTHAND}';var secondsshorthand = '{$smarty.const._SECONDSSHORTHAND}';</script>
  <div id = "random_wizard_div" style = "display:none">
  {capture name = 't_random_test_wizard_code'}
   <div class = "tabber">
       <div class = "tabbertab" title = "{$smarty.const._BASICTESTPARAMETERS}">
     <form id = "general_form">
      <table class = "randomTest" style = "width:100%;">
       <tr><td>{$smarty.const._SELECTOPTION}:</td></tr>
       <tr><td>1. {$smarty.const._CREATERANDOMTESTTHATLASTS} <input type = "text" name = "duration" size = "5"> {$smarty.const._MINUTES} </td></tr>
       <tr><td>2. {$smarty.const._CREATERANDOMTESTTHATHAS} <input type = "text" name = "multitude" size = "5"> {$smarty.const._QUESTIONS|@mb_strtolower}</td></tr>
  {*
       <tr><td>3. {$smarty.const._MEANDIFFICULTY}
         <select name = "mean_difficulty">
          <option value = "0">{$smarty.const._ANY}</option>
          {foreach name = "question_difficulties" item = "item" key = "difficulty" from = "$T_QUESTION_DIFFICULTIES"}
          <option value = "{$difficulty}">{$item}</option>
          {/foreach}
         </select>
        </td></tr>
  *}
       <tr><td>{$smarty.const._IMPORTANCEOFQUESTIONSVSDURATION}: <span id = "balance_value_questions">50</span>% {$smarty.const._QUESTIONS} - <span id = "balance_value_duration">50</span>% {$smarty.const._DURATION}
         <div id="slider" style = "width:256px; margin:10px 0; background-color:#ccc; height:10px; position: relative;">
             <div id="slider_handle" style = "width:16px; height:16px; background-image:url('images/16x16/navigate_up.png'); cursor:move; position: absolute;"></div>
             <input type = "hidden" id = "balance" name = "balance" value = "50">
            </div>
           </td></tr>
       {*<tr><td><input type = "checkbox" id = "timeless_questions" checked>Consider timeless questions as well</td></tr>*}
       <tr><td>
         <input type = "button" class = "flatButton" value = "{$smarty.const._OK}" onclick = "randomize(this)">
        </td></tr>
      </table>
     </form>
    </div>
          <div class = "tabbertab" title = "{$smarty.const._CHANGEQUESTIONSBASEDONDIFFICULTY}">
     <form id = "difficulty_form">
      <table class = "randomTest randomTestMatrix">
       <tr><td></td><td colspan = "4">{$smarty.const._DIFFICULTYLEVELS}</td></tr>
       <tr><td>{$smarty.const._UNITS}</td>
       {foreach name = "question_difficulties" item = "item" key = "difficulty" from = "$T_QUESTION_DIFFICULTIES"}
        <td><input type = "checkbox" checked name = "difficulty[{$difficulty}]"><img src = "{$T_QUESTION_DIFFICULTIES_ICONS[$difficulty]}" alt = "{$item}" title = "{$item}"></td>
       {/foreach}
       </tr>
       {foreach name = "units_list" item = "unit" key = "id" from = $T_UNITS_NAMES}
  {if $T_UNITS_TO_QUESTIONS_DIFFICULTIES[$id]}
       <tr><td style = "text-align:left"><input type = "checkbox" name = "unit[{$id}]" checked> <span>{$unit|eF_truncate:30}</span></td>
        {foreach name = "question_difficulties" item = "item" key = "difficulty" from = "$T_QUESTION_DIFFICULTIES"}
        <td>
   {if $T_UNITS_TO_QUESTIONS_DIFFICULTIES[$id].$difficulty}
        <select name = "unit_to_difficulty[{$id}][{$difficulty}]" id = "unit_to_difficulty[{$id}][{$difficulty}]">
          <option value = "any">{$smarty.const._ANY}</option>
          <option value = "0" selected>0</option>
          {section name = "questions_list" loop = $T_UNITS_TO_QUESTIONS_DIFFICULTIES[$id].$difficulty}
          <option value = "{$smarty.section.questions_list.iteration}" {if $smarty.section.questions_list.iteration == $T_TEST_QUESTIONS_STATISTICS.difficulties[$id].$difficulty}selected{/if}>
           {$smarty.section.questions_list.iteration}</option>
          {/section}
         </select>
   {/if}
         </td>
        {/foreach}
       </tr>
  {/if}
       {/foreach}
       <tr><td colspan = "5"><input type = "button" class = "flatButton" value = "{$smarty.const._OK}" onclick = "randomize(this, 'difficulty')"></td></tr>
      </table>
     </form>
    </div>
          <div class = "tabbertab" title = "{$smarty.const._ADJUSTTYPE}">
     <form id = "type_form">
      <table class = "randomTest randomTestMatrix">
       <tr><td></td><td colspan = "7">{$smarty.const._QUESTIONTYPES}</td></tr>
       <tr><td>{$smarty.const._UNITS}</td>
       {foreach name = "question_types" item = "item" key = "type" from = "$T_QUESTION_TYPES"}
        <td><input type = "checkbox" checked name = "type[{$type}]"><img src = "{$T_QUESTION_TYPES_ICONS[$type]}" alt = "{$item}" title = "{$item}"></td>
       {/foreach}
       </tr>
       {foreach name = "units_list" item = "unit" key = "id" from = $T_UNITS_NAMES}
  {if $T_UNITS_TO_QUESTIONS_TYPES[$id]}
       <tr><td style = "text-align:left"><input type = "checkbox" name = "unit[{$id}]" checked> <span>{$unit|eF_truncate:30}</span></td>
        {foreach name = "question_types" item = "item" key = "type" from = $T_QUESTION_TYPES}
        <td>
   {if $T_UNITS_TO_QUESTIONS_TYPES[$id].$type}
        <select name = "unit_to_type[{$id}][{$type}]" id = "unit_to_type[{$id}][{$type}]">
          <option value = "any">{$smarty.const._ANY}</option>
          <option value = "0" selected>0</option>
          {section name = "questions_list" loop = $T_UNITS_TO_QUESTIONS_TYPES[$id].$type}
          <option value = "{$smarty.section.questions_list.iteration}" {if $smarty.section.questions_list.iteration == $T_TEST_QUESTIONS_STATISTICS.types[$id].$type}selected{/if}>
           {$smarty.section.questions_list.iteration}</option>
          {/section}
         </select>
   {/if}
         </td>
        {/foreach}
       </tr>
  {/if}
       {/foreach}
       <tr><td colspan = "8"><input type = "button" class = "flatButton" value = "{$smarty.const._OK}" onclick = "randomize(this, 'type')"></td></tr>
      </table>
     </form>
    </div>
          <div class = "tabbertab" title = "{$smarty.const._QUESTIONSBYPERCENTAGE}">
     <form id = "percentage_form">
      <table class = "randomTest randomTestMatrix">
       <tr><td>{$smarty.const._UNITS}</td><td>{$smarty.const._QUESTIONPERCENTAGE} (%)</td><td>{$smarty.const._ACCURATEPERCENTAGE}</td></tr>
       {foreach name = "units_list" item = "unit" key = "id" from = $T_UNITS_NAMES}
  {if $T_UNITS_TO_QUESTIONS_TYPES[$id]}
       <tr><td style = "text-align:left"><span>{$unit|eF_truncate:30}</span></td>
        <td><select name = "unit_to_percentage[{$id}]" id = "unit_to_percentage[{$id}]">
          <option value = "0">0</option>
          {section name = "questions_list" loop = 10}
          <option value = "{$smarty.section.questions_list.iteration*10}" {if $smarty.section.questions_list.iteration == $T_TEST_QUESTIONS_STATISTICS.percentage[$id]|@round}selected{/if}>
           {$smarty.section.questions_list.iteration*10}</option>
          {/section}
         </select>
        </td>
        <td class = "unit_to_accurate_percentage" id = "unit_to_accurate_percentage[{$id}]">{$T_TEST_QUESTIONS_STATISTICS.percentage[$id]*10}%</td>
       </tr>
  {/if}
       {/foreach}
       <tr><td colspan = "3"><input type = "button" class = "flatButton" value = "{$smarty.const._OK}" onclick = "randomize(this, 'percentage')"></td></tr>
      </table>
     </form>
    </div>
          <div class = "tabbertab" title = "{$smarty.const._ADVANCEDSETTINGS}">
     <form id = "advanced_form">
      <table class = "randomTest" style = "width:100%">
       <tr><td>{$smarty.const._SELECTOPTION}:</td></tr>
       <tr><td>{$smarty.const._USE} <input name = "random_pool" type = "text" size = "5" value = "{$T_TEST_QUESTIONS_STATISTICS.random_pool}"> {$smarty.const._QUESTIONSINEACHEXECUTION}</td></tr>
       <tr><td><input type = "checkbox" name = "show_incomplete" {if $T_TEST_QUESTIONS_STATISTICS.show_incomplete}checked{/if}> {$smarty.const._SHOWINCOMPLETEQUESTIONSEACHTIME}</td></tr>
       <tr><td><input type = "checkbox" name = "user_configurable" {if $T_TEST_QUESTIONS_STATISTICS.user_configurable}checked{/if}> {$smarty.const._ALLOWSTUDENTSTOSPECIFYTOTALQUESTIONS}</td></tr>
       <tr><td><input type = "checkbox" name = "update_test_time"> {$smarty.const._UPDATETOTALTESTTIME}</td></tr>
       <tr><td><input type = "button" class = "flatButton" value = "{$smarty.const._OK}" onclick = "setRandomPool(this)"></td></tr>
      </table>
     </form>
    </div>
   </div>
   <div id = "inner_test_settings">
    {$smarty.const._CURRENTTESTHAS}
    <span id = "questions_number">{$T_TEST_QUESTIONS_STATISTICS.multitude}</span>
    {$smarty.const._QUESTIONSOFTOTALTIME}
    <span id = "questions_time_hours">{if $T_TEST_QUESTIONS_STATISTICS.duration.hours}{$T_TEST_QUESTIONS_STATISTICS.duration.hours}{$smarty.const._HOURSSHORTHAND} {/if}</span>
          <span id = "questions_time_minutes">{if $T_TEST_QUESTIONS_STATISTICS.duration.minutes}{$T_TEST_QUESTIONS_STATISTICS.duration.minutes}{$smarty.const._MINUTESSHORTHAND} {/if}</span>
          <span id = "questions_time_seconds">{if $T_TEST_QUESTIONS_STATISTICS.duration.seconds}{$T_TEST_QUESTIONS_STATISTICS.duration.seconds}{$smarty.const._SECONDSSHORTHAND}{/if} {if !$T_TEST_QUESTIONS_STATISTICS.duration.seconds && !$T_TEST_QUESTIONS_STATISTICS.duration.minutes && !$T_TEST_QUESTIONS_STATISTICS.duration.hours}0{$smarty.const._MINUTESSHORTHAND}{/if}</span>
    <span {if !$T_TEST_QUESTIONS_STATISTICS.random_pool}style = "display:none"{/if}>{$smarty.const._WHEREARANDOMPOOLOF} <span>{$T_TEST_QUESTIONS_STATISTICS.random_pool}</span> {$smarty.const._QUESTIONSISUSEDEACHTIME}</span>
   </div>
  {/capture}
   {eF_template_printBlock title = $smarty.const._ADJUSTQUESTIONS data = $smarty.capture.t_random_test_wizard_code image = '32x32/tests.png'}
  </div>
 {/capture}
 {capture name = "t_test_questions"}
     <div class = "headerTools">
      {if !$T_SKILLGAP_TEST && $T_CTG != 'feedback'}
        <span>
    <img src = "images/16x16/rules.png" title = "{$smarty.const._ADJUSTQUESTIONS}" alt = "{$smarty.const._ADJUSTQUESTIONS}"/>
          <a href = "javascript:void(0)" onclick = "initSlider();eF_js_showDivPopup('{$smarty.const._ADJUSTQUESTIONS}', 2, 'random_wizard_div')">{$smarty.const._ADJUSTQUESTIONS}</a>
         </span>
         {/if}
   {if $T_CTG != 'feedback'}
        <span>
          <img src = "images/16x16/add.png" title = "{$smarty.const._ADDQUESTION}" alt = "{$smarty.const._ADDQUESTION}"/>
    <select name = "question_type" onchange = "if (this.options[this.options.selectedIndex].value) window.location='{$smarty.server.PHP_SELF}?ctg=tests&add_question=1&question_type='+this.options[this.options.selectedIndex].value">
     <option value = "" selected>{$smarty.const._ADDQUESTIONOFTYPE}</option>
     <option value = "" >---------------</option>
     {foreach name = 'question_types' item = "item" key = "key" from = $T_QUESTIONTYPESTRANSLATIONS}
      {if !$T_SKILLGAP_TEST || $key != 'raw_text'}
      <option value = "{$key}">{$item}</option>
      {/if}
     {/foreach}
    </select>
         </span>
   {/if}
   {if !$T_SKILLGAP_TEST && !$T_CONFIGURATION.disable_questions_pool}
    <span style = "float:right;border-width:0px">
    {if !$smarty.get.showall}
         <a href = "{$smarty.server.PHP_SELF}?ctg={$T_CTG}&edit_test={$smarty.get.edit_test}&showall=1&tab=questions" ><img src = "images/16x16/order.png" alt = "{$smarty.const._SHOWQUESTIONSPOOL}" title = "{$smarty.const._SHOWQUESTIONSPOOL}"/>&nbsp;{$smarty.const._SHOWQUESTIONSPOOL}</a>
        {else}
         <a href = "{$smarty.server.PHP_SELF}?ctg={$T_CTG}&edit_test={$smarty.get.edit_test}&showall={if !$smarty.get.showall}1{else}0{/if}&tab=questions" ><img src = "images/16x16/order.png" alt = "{$smarty.const._SHOWLESSONQUESTIONS}" title = "{$smarty.const._SHOWLESSONQUESTIONS}"/>&nbsp;{$smarty.const._SHOWLESSONQUESTIONS}</a>
        {/if}
        </span>
   {/if}
  </div>
  {$smarty.capture.t_random_test_wizard}
  <div id = "test_settings">
  {if $T_CTG != 'feedback'}
   {$smarty.const._CURRENTTESTHAS}
   <span id = "questions_number">{$T_TEST_QUESTIONS_STATISTICS.multitude}</span>
   {$smarty.const._QUESTIONSOFTOTALTIME}
   <span id = "questions_time_hours">{if $T_TEST_QUESTIONS_STATISTICS.duration.hours}{$T_TEST_QUESTIONS_STATISTICS.duration.hours}{$smarty.const._HOURSSHORTHAND} {/if}</span>
         <span id = "questions_time_minutes">{if $T_TEST_QUESTIONS_STATISTICS.duration.minutes}{$T_TEST_QUESTIONS_STATISTICS.duration.minutes}{$smarty.const._MINUTESSHORTHAND} {/if}</span>
         <span id = "questions_time_seconds">{if $T_TEST_QUESTIONS_STATISTICS.duration.seconds}{$T_TEST_QUESTIONS_STATISTICS.duration.seconds}{$smarty.const._SECONDSSHORTHAND}{/if} {if !$T_TEST_QUESTIONS_STATISTICS.duration.seconds && !$T_TEST_QUESTIONS_STATISTICS.duration.minutes && !$T_TEST_QUESTIONS_STATISTICS.duration.hours}0{$smarty.const._MINUTESSHORTHAND}{/if}</span>
  {/if}
   <span {if !$T_TEST_QUESTIONS_STATISTICS.random_pool}style = "display:none"{/if}>{$smarty.const._WHEREARANDOMPOOLOF} <span id = "questions_random_pool">{$T_TEST_QUESTIONS_STATISTICS.random_pool}</span> {$smarty.const._QUESTIONSISUSEDEACHTIME}</span>
  </div>
{*This is the ajax table for the questions inside the edit test*}
{if !$T_SORTED_TABLE || $T_SORTED_TABLE == 'questionsTable'}
{*This is the ajax table for the common questions pool *}
  {if $smarty.get.showall && !$T_CONFIGURATION.disable_questions_pool}
<!--ajax:questionsTable-->
        <table class = "QuestionsListTable sortedTable" id = "questionsTable" size = "{$T_QUESTIONS_SIZE}" sortBy = "7" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg={$T_CTG}&edit_test={$smarty.get.edit_test}&tab={$smarty.get.tab}&showall={$smarty.get.showall}&">
            <tr><td class = "topTitle" name = "text">{$smarty.const._QUESTIONTEXT}</td>
            {if $smarty.get.showall && !$T_CONFIGURATION.disable_questions_pool}
          <td name = "lesson_name" class = "topTitle">{$smarty.const._LESSON}</td>
         {/if}
                <td class = "topTitle centerAlign" name = "type">{$smarty.const._QUESTIONTYPE}</td>
   {if $T_CTG != 'feedback'}
                <td class = "topTitle centerAlign" name = "difficulty">{$smarty.const._DIFFICULTY}</td>
   {/if}
            {if !$T_SKILLGAP_TEST && $T_CTG != 'feedback'}
                <td class = "topTitle centerAlign" name = "weight">{$smarty.const._QUESTIONWEIGHT}</td>
            {/if}
   {if $T_CTG != 'feedback'}
             <td class = "topTitle centerAlign" name = "estimate">{$smarty.const._TIME}</td>
   {/if}
                <td class = "topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
                <td class = "topTitle centerAlign" name = "partof">{$smarty.const._USEQUESTION}</td></tr>
   {foreach name = "questions_list" key = "key" item = "item" from = $T_UNIT_QUESTIONS}
            {if $T_CTG == 'tests' || ($T_CTG == 'feedback' && $item.type != 'true_false')}
    <tr class = "{cycle name = "main_cycle" values="oddRowColor, evenRowColor"}">
    {if $item.lessons_ID == $smarty.session.s_lessons_ID}
     <td><a class = "editLink" href = "{$smarty.server.PHP_SELF}?ctg=tests&edit_question={$item.id}&question_type={$item.type}&lessonId={$item.lessons_ID}" title="{$item.text}"> {$item.text|eF_truncate:50}</a></td>
    {else}
     <td> {$item.text|eF_truncate:50}</td>
    {/if}
    {if $smarty.get.showall && !$T_CONFIGURATION.disable_questions_pool}
           <td><span title = "{$T_LESSONS[$item.lessons_ID].lesson_path}">{$item.lesson_name}</span></td>
          {/if}
     <td class = "centerAlign">
      <img src = "{$T_QUESTION_TYPE_ICONS[$item.type]}" title = "{$T_QUESTION_TYPES[$item.type]}" alt = "{$T_QUESTION_TYPES[$item.type]}" />
      <span style = "display:none">{$item.type}</span>{*We put this here in order to be able to sort by type*}
     </td>
    {if $T_CTG != 'feedback'}
     <td class = "centerAlign">
      <img src = "{$T_QUESTION_DIFFICULTY_ICONS[$item.difficulty]}" title = "{$T_QUESTION_DIFFICULTIES[$item.difficulty]}" alt = "{$T_QUESTION_DIFFICULTIES[$item.difficulty]}" />
      <span style = "display:none">{$item.difficulty}</span>{*We put this here in order to be able to sort by type*}
     </td>
    {/if}
    {if !$T_SKILLGAP_TEST && $T_CTG != 'feedback'}
     <td class = "centerAlign">{$T_TEST_FORM.question_weight[$key].html}</td>
    {/if}
    {if $T_CTG != 'feedback'}
     <td class = "centerAlign">{if !$item.estimate}-{else}{if $item.estimate_interval.minutes}{$item.estimate_interval.minutes}{$smarty.const._MINUTESSHORTHAND}{/if} {if $item.estimate_interval.seconds}{$item.estimate_interval.seconds}{$smarty.const._SECONDSSHORTHAND}{/if}{/if}</td>
    {/if}
     <td class = "centerAlign">
      <a href = "{$smarty.server.PHP_SELF}?ctg=tests&show_question={$item.id}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._PREVIEW}', 2)"><img src = "images/16x16/search.png" alt = "{$smarty.const._PREVIEW}" title = "{$smarty.const._PREVIEW}" /></a>
      {if $T_SKILLGAP_TEST}
       <a href = "{$smarty.server.PHP_SELF}?ctg=tests&edit_question={$item.id}&lessonId={$item.lessons_ID}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._CORRELATESKILLSTOQUESTION}', 2)"><img src = "images/16x16/tools.png" alt = "{$smarty.const._CORRELATESKILLSTOQUESTION}" title = "{$smarty.const._CORRELATESKILLSTOQUESTION}" /></a>
      {/if}
      {if ($T_CTG != 'feedback' && $item.lessons_ID == $smarty.session.s_lessons_ID) || $T_SKILLGAP_TEST}
       <a href = "{$smarty.server.PHP_SELF}?ctg=tests&edit_question={$item.id}&question_type={$item.type}&lessonId={$item.lessons_ID}"><img src = "images/16x16/edit.png" alt = "{$smarty.const._EDIT}" title = "{$smarty.const._EDIT}"/></a>
      {/if}
     </td>
     <td class = "centerAlign">{$T_TEST_FORM.questions[$key].html}<span style = "display:none">{$T_TEST_FORM.questions[$key].value}</span></td> {*span is used for sorting*}
    </tr>
   {/if}
            {foreachelse}
            <tr class = "oddRowColor defaultRowHeight"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
            {/foreach}
        </table>
<!--/ajax:questionsTable-->
  {else}
<!--ajax:questionsTable-->
        <table class = "QuestionsListTable sortedTable" id = "questionsTable" size = "{$T_QUESTIONS_SIZE}" sortBy = "7" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg={$T_CTG}&edit_test={$smarty.get.edit_test}&tab={$smarty.get.tab}&showall={$smarty.get.showall}&">
            <tr><td class = "topTitle" name = "text">{$smarty.const._QUESTIONTEXT}</td>
            {if !$T_SKILLGAP_TEST}
                <td class = "topTitle" name = "parent_name">{$smarty.const._UNITNAME}</td>
            {else}
                <td name="name" class = "topTitle">{$smarty.const._ASSOCIATEDWITH}</td>
            {/if}
                <td class = "topTitle centerAlign" name = "type">{$smarty.const._QUESTIONTYPE}</td>
   {if $T_CTG != 'feedback'}
                <td class = "topTitle centerAlign" name = "difficulty">{$smarty.const._DIFFICULTY}</td>
   {/if}
            {if !$T_SKILLGAP_TEST && $T_CTG != 'feedback'}
                <td class = "topTitle centerAlign" name = "weight">{$smarty.const._QUESTIONWEIGHT}</td>
            {/if}
   {if $T_CTG != 'feedback'}
             <td class = "topTitle centerAlign" name = "estimate">{$smarty.const._TIME}</td>
   {/if}
                <td class = "topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
                <td class = "topTitle centerAlign" name = "partof">{$smarty.const._USEQUESTION}</td></tr>
   {foreach name = "questions_list" key = "key" item = "item" from = $T_UNIT_QUESTIONS}
            {if $T_CTG == 'tests' || ($T_CTG == 'feedback' && $item.type != 'true_false')}
    <tr class = "{cycle name = "main_cycle" values="oddRowColor, evenRowColor"}">
    {if $item.lessons_ID == $smarty.session.s_lessons_ID}
     <td><a class = "editLink" href = "{$smarty.server.PHP_SELF}?ctg=tests&edit_question={$item.id}&question_type={$item.type}&lessonId={$item.lessons_ID}" title="{$item.text}"> {$item.text|eF_truncate:50}</a></td>
    {else}
     <td>{$item.text|eF_truncate:50}</td>
    {/if}
    {if !$T_SKILLGAP_TEST}
     <td>{if $item.parent_name}{$item.parent_name}{else}{$smarty.const._NONE}{/if}</td>
    {else}
     <td>{$item.name}</td>
    {/if}
     <td class = "centerAlign">
      <img src = "{$T_QUESTION_TYPE_ICONS[$item.type]}" title = "{$T_QUESTION_TYPES[$item.type]}" alt = "{$T_QUESTION_TYPES[$item.type]}" />
      <span style = "display:none">{$item.type}</span>{*We put this here in order to be able to sort by type*}
     </td>
    {if $T_CTG != 'feedback'}
     <td class = "centerAlign">
      <img src = "{$T_QUESTION_DIFFICULTY_ICONS[$item.difficulty]}" title = "{$T_QUESTION_DIFFICULTIES[$item.difficulty]}" alt = "{$T_QUESTION_DIFFICULTIES[$item.difficulty]}" />
      <span style = "display:none">{$item.difficulty}</span>{*We put this here in order to be able to sort by type*}
     </td>
    {/if}
    {if !$T_SKILLGAP_TEST && $T_CTG != 'feedback'}
     <td class = "centerAlign">{$T_TEST_FORM.question_weight[$key].html}</td>
    {/if}
    {if $T_CTG != 'feedback'}
     <td class = "centerAlign">{if !$item.estimate}-{else}{if $item.estimate_interval.minutes}{$item.estimate_interval.minutes}{$smarty.const._MINUTESSHORTHAND}{/if} {if $item.estimate_interval.seconds}{$item.estimate_interval.seconds}{$smarty.const._SECONDSSHORTHAND}{/if}{/if}</td>
    {/if}
     <td class = "centerAlign">
      <a href = "{$smarty.server.PHP_SELF}?ctg=tests&show_question={$item.id}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._PREVIEW}', 2)"><img src = "images/16x16/search.png" alt = "{$smarty.const._PREVIEW}" title = "{$smarty.const._PREVIEW}" /></a>
    {if $T_SKILLGAP_TEST}
      <a href = "{$smarty.server.PHP_SELF}?ctg=tests&edit_question={$item.id}&lessonId={$item.lessons_ID}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._CORRELATESKILLSTOQUESTION}', 2)"><img src = "images/16x16/tools.png" alt = "{$smarty.const._CORRELATESKILLSTOQUESTION}" title = "{$smarty.const._CORRELATESKILLSTOQUESTION}" /></a>
    {/if}
    {if $T_CTG != 'feedback' && $item.lessons_ID == $smarty.session.s_lessons_ID}
     <a href = "{$smarty.server.PHP_SELF}?ctg=tests&edit_question={$item.id}&question_type={$item.type}&lessonId={$item.lessons_ID}"><img src = "images/16x16/edit.png" alt = "{$smarty.const._EDIT}" title = "{$smarty.const._EDIT}"/></a>
    {/if}
     </td>
     <td class = "centerAlign">{$T_TEST_FORM.questions[$key].html}<span style = "display:none">{$T_TEST_FORM.questions[$key].value}</span></td> {*span is used for sorting*}
    </tr>
   {/if}
            {foreachelse}
            <tr class = "oddRowColor defaultRowHeight"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
            {/foreach}
        </table>
<!--/ajax:questionsTable-->
  {/if}
{/if}
 {/capture}
 {capture name = 't_edit_test_code'}
  {if $T_CTG != "feedback"}
   {assign var = 'tempTitle' value = $smarty.const._TESTOPTIONS}
   {assign var = 'questionsTitle' value = $smarty.const._TESTQUESTIONS}
  {else}
   {assign var = 'tempTitle' value = $smarty.const._FEEDBACKOPTIONS}
   {assign var = 'questionsTitle' value = $smarty.const._QUESTIONS}
  {/if}
  <div class = "tabber">
         <div class = "tabbertab" id="test_options" title = "{$tempTitle}">
    {eF_template_printBlock title=$tempTitle data=$smarty.capture.t_test_properties image='32x32/generic.png'}
            </div>
        {if $smarty.get.edit_test}
         <div class = "tabbertab {if $smarty.get.tab == 'question' || $smarty.get.tab == 'questions'}tabbertabdefault{/if}" id = "test_questions" title = "{$questionsTitle}">
    {eF_template_printBlock title=$questionsTitle data=$smarty.capture.t_test_questions image='32x32/question_and_answer.png'}
            </div>
  {/if}
  {*Interface to ajax-assign users to a test*}
        {if $T_SKILLGAP_TEST && $smarty.get.edit_test}
   {capture name = "t_test_users_code"}
{if !$T_SORTED_TABLE || $T_SORTED_TABLE == 'testUsersTable'}
<!--ajax:testUsersTable-->
         <table style = "width:100%" class = "sortedTable" size = "{$T_USERS_SIZE}" sortBy = "0" id = "testUsersTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=tests&edit_test={$smarty.get.edit_test}&">
             <tr class = "topTitle">
                 <td class = "topTitle" name = "login">{$smarty.const._USER}</td>
                 <td class = "topTitle centerAlign" name = "partof">{$smarty.const._CHECK}</td>
             </tr>
     {foreach name = 'users_to_lessons_list' key = 'key' item = 'user' from = $T_ALL_USERS}
             <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"}">
                 <td>#filter:login-{$user.login}#</td>
                 <td class = "centerAlign">
                     {if $user.solved == 1}
                     <a href = "{$smarty.server.PHP_SELF}?ctg=tests&show_solved_test={$user.completed_test_id}&test_analysis={$smarty.get.edit_test}&user={$user.login}"><img border="0" src="images/16x16/analysis.png" style="vertical-align:middle" alt="{$smarty.const._TESTSOLVEDVIEWTOSEESKILLGAPANALYSIS}" title="{$smarty.const._TESTSOLVEDVIEWTOSEESKILLGAPANALYSIS}" /></a>
                     <a href = "javascript:void(0);" onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) ajaxRemoveSolvedTest(this, '{$user.login}', '{$user.completed_test_id}','{$smarty.get.edit_test}');"/><img border="0" src="images/16x16/error_delete.png" style="vertical-align:middle" alt="{$smarty.const._DELETESKILLGAPTESTRECORD}" title="{$smarty.const._DELETESKILLGAPTESTRECORD}"/> </a>
                     {else}
                     <input class = "inputCheckbox" type = "checkbox" name = "checked_{$user.login}" id = "checked_{$user.login}" onclick = "ajaxPost('{$user.login}', this,'testUsersTable');" {if isset($user.partof)}checked = "checked"{/if} />{if in_array($user.login, $T_LESSON_USERS)}<span style = "display:none">checked</span>{/if} {*Text for sorting*}
                     {/if}
                 </td>
             </tr>
     {foreachelse}
             <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
     {/foreach}
         </table>
<!--/ajax:testUsersTable-->
{/if}
   {/capture}
         <div class = "tabbertab" id = "test_users" title = "{$smarty.const._USERS}">
    {eF_template_printBlock title=$smarty.const._USERS data=$smarty.capture.t_test_users_code image='32x32/users.png'}
      </div>
     {/if}
  </div>
    {/capture}
 {* Different icons and titles used between lesson and skillgap tests *}
 {if $smarty.get.edit_test}
     {if $T_SKILLGAP_TEST}
         {eF_template_printBlock title = "`$smarty.const._OPTIONSFORSKILLGAPTEST` <span class = 'innerTableName'>&quot;`$T_CURRENT_TEST->test.name`&quot;</span>" data=$smarty.capture.t_edit_test_code image='32x32/skill_gap.png'}
     {elseif $T_CTG != "feedback"}
         {eF_template_printBlock title = "`$smarty.const._OPTIONSFORTEST` <span class = 'innerTableName'>&quot;`$T_CURRENT_TEST->test.name`&quot;</span>" data = $smarty.capture.t_edit_test_code image = '32x32/tests.png'}
     {else}
    {eF_template_printBlock title = "`$smarty.const._OPTIONSFORFEEDBACK` <span class = 'innerTableName'>&quot;`$T_CURRENT_TEST->test.name`&quot;</span>" data = $smarty.capture.t_edit_test_code image = '32x32/feedback.png'}
  {/if}
 {elseif $smarty.get.add_test}
     {if $T_SKILLGAP_TEST}
         {eF_template_printBlock title = "`$smarty.const._ADDSKILLGAPTEST`" data=$smarty.capture.t_edit_test_code image='32x32/skill_gap.png'}
     {elseif $T_CTG != "feedback"}
         {eF_template_printBlock title = $smarty.const._ADDTEST data = $smarty.capture.t_edit_test_code image = '32x32/tests.png'}
  {else}
   {eF_template_printBlock title = $smarty.const._ADDFEEDBACSFEEDBACK data = $smarty.capture.t_edit_test_code image = '32x32/tests.png'}
     {/if}
 {/if}
{elseif $smarty.get.add_question || $smarty.get.edit_question}
 {include file = "includes/tests/add_question.tpl"}
{elseif $smarty.get.show_test || isset ($T_TEST_UNSOLVED)}
 {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?view_unit=`$T_CURRENT_TEST->test.content_ID`'>`$smarty.const._VIEWTEST`: `$T_CURRENT_TEST->test.name`</a>"}
 {capture name = 't_show_test'}
 <table id="shown_test" width = "100%" align = "center" >
    {if !isset($smarty.get.popup)}
     <tr><td colspan = "2">
             <table>
                 <tr><td style = "border-right:1px solid black;">
                     <a href="{$smarty.server.PHP_SELF}?ctg=tests&{if !$T_SKILLGAP_TEST}edit_unit={$T_UNIT.id}{else}edit_test={$smarty.get.show_test}{/if}"><img border="0" src="images/16x16/edit.png" style="vertical-align:middle" alt="{$smarty.const._UPDATETEST}" title="{$smarty.const._UPDATETEST}"></a>
                     <a href="{$smarty.server.PHP_SELF}?ctg=tests&{if !$T_SKILLGAP_TEST}edit_unit={$T_UNIT.id}{else}edit_test={$smarty.get.show_test}{/if}" style = "vertical-align:middle">{$smarty.const._EDITTEST}</a>&nbsp;
                 </td><td style = "border-right:1px solid black;">
                     &nbsp;<a href="{$smarty.server.PHP_SELF}?ctg=tests&add_test=1"><img border="0" src="images/16x16/add.png" style="vertical-align:middle" alt="{$smarty.const._CREATETEST}" title="{$smarty.const._CREATETEST}"></a>
                     <a href="{$smarty.server.PHP_SELF}?ctg=tests&add_test=1" style = "vertical-align:middle">{$smarty.const._CREATETEST}</a>&nbsp;
                 </td>
                 {*Exclude from skillgap tests - No units here*}
                 {if !$T_SKILLGAP_TEST}
                 <td>
                     &nbsp;<a href="{$smarty.server.PHP_SELF}?ctg=content&add_unit=1"><img border="0" src="images/16x16/add.png" style="vertical-align:middle" alt="{$smarty.const._CREATEUNIT}" title="{$smarty.const._CREATEUNIT}"></a>
                     <a href="{$smarty.server.PHP_SELF}?ctg=content&add_unit=1" style = "vertical-align:middle">{$smarty.const._CREATEUNIT}</a>&nbsp;
                 </td>
                 {/if}
                 </tr>
             </table>
     </td></tr>
     <tr><td colspan = "2" class = "horizontalSeparator"></td></tr>
     {/if}
     <tr><td id = "singleColumn">
     {if $smarty.get.print}
      {literal}
      <style>.rawTextQuestion {width:100%;height:400px;}/*For print version, display larger textareas*/</style>
   <script>
    // Function for printing in IE6
    // Opens a new popup, set its innerHTML like the content we want to print
    // then calls window.print and then closes the popup without the user knowing
    function printPartOfPage(elementId)
    {
        var printContent = document.getElementById(elementId);
        var windowUrl = 'about:blank';
        var uniqueName = new Date();
        var windowName = 'Print' + uniqueName.getTime();
        var printWindow = window.open(windowUrl, windowName, 'left=350,top=200,width=1,height=1,z-lock=yes');
        printWindow.document.write("<link rel = \"stylesheet\" type = \"text/css\" href = \"css/css_global.php\" />");
        printWindow.document.write(printContent.innerHTML);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    }
   </script>
   {/literal}
     <!-- <table style = "width:100%;">
             <tr><td style = "padding-top:10px;padding-bottom:15px;text-align:center">
                 <input class = "flatButton" type = "submit" onClick = "printPartOfPage('shown_test');" value = "{$smarty.const._PRINTIT}"/>
             </td></tr>
         </table> -->
     {/if}
     {$T_TEST_UNSOLVED}
     </td></tr>
 </table>
 {/capture}
 {eF_template_printBlock title = $smarty.const._PREVIEW data = $smarty.capture.t_show_test image = '32x32/generic.png'}
 <br/><br/>
{elseif $smarty.get.quick_test_add}
 {capture name = 't_random_questions_from_skills'}
 <table id="skillQuestionsTable" width="100%" border = "0" width = "100%" class = "sortedTable" sortBy = "0">
     <tr class = "topTitle">
         <td class = "topTitle">{$smarty.const._SKILL}</td>
         <td class = "topTitle">{$smarty.const._QUESTIONS}</td>
     </tr>
     {foreach name = '_list' key = 'key' item = 'skill' from = $T_QUESTION_SKILLS}
     <tr>
         <td>{$skill.description}</td>
         <td>
          <span id = "span_skill_checked_{$skill.skill_ID}" style="display:none">{if isset($skill.questions_ID)}1{else}0{/if}</span>
             <input class = "inputText" type = "checkbox" id = "questions_for_skill_{$skill.skill_ID}" name = "questions_for_skill_{$skill.skill_ID}">
         </td>
     </tr>
     {foreachelse}
     <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "6">{$smarty.const._NODATAFOUND}</td></tr>
     {/foreach}
 </table>
 {/capture}
 {eF_template_printBlock title = $smarty.const._CORRELATETOQUESTION|cat:':&nbsp;<i>'|cat:$T_QUESTION_TEXT|cat:'</i>' data = $smarty.capture.t_random_questions_from_skills image = '32x32/generic.png' options = $T_SUGGEST_QUESTION_}
{elseif $smarty.get.show_solved_test}
 {if !$smarty.get.test_analysis}
  {capture name = "t_solved_test_code"}
      {if $smarty.get.print}
      <p style = "text-align:center"><input class = "flatButton" type = "submit" onClick = "window.print()" value = "{$smarty.const._PRINTIT}"/></p>
         {/if}
      {$T_TEST_SOLVED}
      {* Skillgap hack to change the redo test link functionality*}
      {if $T_SKILLGAP_TEST}
       {literal}
        <script>
        if (document.getElementById('redoLinkHref')) {
         document.getElementById('redoLinkHref').href = "{/literal}{$smarty.session.s_type}{literal}.php?ctg=tests&delete_solved_test={/literal}{$smarty.get.show_solved_test}{literal}&test_id={/literal}{$T_TEST_DATA->test.id}{literal}&users_login={/literal}{$T_TEST_DATA->completedTest.login}{literal}";
         document.getElementById('redoLinkHref').onclick = "";
        }
        document.getElementById('testAnalysisLinkHref').href = document.getElementById('testAnalysisLinkHref').href + "&user={/literal}{$T_TEST_DATA->completedTest.login}{literal}";
        </script>
       {/literal}
      {/if}
     {/capture}
   {if $T_CTG != "feedback"}
    {eF_template_printBlock title = "`$smarty.const._SOLVEDTEST` `$smarty.const._FORTEST` <span class = \"innerTableName\">&quot;`$T_TEST_DATA->test.name`&quot;</span> `$smarty.const._ANDUSER` <span class = \"innerTableName\">&quot;#filter:login-`$T_TEST_DATA->completedTest.login`#&quot;</span>" data = $smarty.capture.t_solved_test_code image='32x32/tests.png'}
   {else}
    {eF_template_printBlock title = "`$smarty.const._FEEDBACK` <span class = \"innerTableName\">&quot;`$T_TEST_DATA->test.name`&quot;</span> `$smarty.const._ANDUSER` <span class = \"innerTableName\">&quot;#filter:login-`$T_TEST_DATA->completedTest.login`#&quot;</span>" data = $smarty.capture.t_solved_test_code image='32x32/feedback.png'}
   {/if}
  {else}
     {if $T_SKILLGAP_TEST}
            {capture name = 't_user_code'}
                <div class="tabber" >
                    <div class="tabbertab">
                        <h3>{$smarty.const._SKILLSCORES}</h3>
                        <table id="skillScoresTable" border = "0" width = "100%" class = "sortedTable" sortBy = "0">
                            <tr class = "topTitle">
                                <td class = "topTitle">{$smarty.const._SKILL}</td>
                                <td class = "topTitle">{$smarty.const._SCORE}</td>
                                <td class = "topTitle">{$smarty.const._THRESHOLD}</td>
                            </tr>
                            {foreach name = 'skills_gap_list' key = 'key' item = 'skill' from = $T_SKILLSGAP}
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                                <td>{$skill.skill}</td>
                                <td class = "progressCell">
                                    <span style = "display:none">{$skill.score}</span>
                                    <span class = "progressNumber">#filter:score-{$skill.score}#%</span>
                                    <span id="{$skill.id}_bar" class = "progressBar" style = "background-color:{if $skill.score >= $T_TEST_DATA->options.general_threshold}#00FF00{else}#FF0000{/if};width:{$skill.score}px;">&nbsp;</span>&nbsp;
                                </td>
                                <td>
                                {if $_change_}
                                 <input type="text" id="{$skill.id}_threshold" value="{$T_TEST_DATA->options.general_threshold}" onChange="eF_thresholdChange('{$skill.id}', '{$skill.score}',true)" />&nbsp;%<input type="hidden" id="{$skill.id}_previous_threshold" value = "{$T_TEST_DATA->options.general_threshold}" />
                                {else}
                                 {$T_TEST_DATA->options.general_threshold}
                                {/if}
                                </td>
                            </tr>
                            {foreachelse}
                            <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NOSKILLSCORRELATEDWITHTHETESTSQUESTIONS}</td></tr>
                            {/foreach}
                        </table>
                        {if $T_SKILLSGAP && $_change_}
                        <br />
                        <table>
                            <tr>
                                <td>{$smarty.const._GENERALTHRESHOLD}:&nbsp;</td>
                                <td><input type="text" id="shold" value="{$T_TEST_DATA->options.general_threshold}" onChange="javascript:eF_generalThresholdChange(this.value)" />&nbsp;%<input type="hidden" id="general_previous_threshold" value = "{$T_TEST_DATA->options.general_threshold}" /></td>
                            </tr>
                        </table>
                        {/if}
                    </div>
                    <div class="tabbertab">
                        <h3>{$smarty.const._PROPOSEDASSIGNMENTS}</h3>
                         <div class="tabber" >
                        {if $T_CONFIGURATION.lesson_enroll} {*stand-alone lessons*}
                                <div class="tabbertab">
                       <h3>{$smarty.const._LESSONS}</h3>
<!--ajax:proposedLessonsTable-->
                        <table style = "width:100%" class = "sortedTable" size = "{$T_PROPOSED_LESSONS_SIZE}" sortBy = "0" id = "proposedLessonsTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=tests&show_solved_test={$smarty.get.show_solved_test}&test_analysis={$smarty.get.test_analysis}&user={$smarty.get.user}{$T_MISSING_SKILLS_URL}&">
                                        <tr class = "topTitle">
                                            <td class = "topTitle" name = "name">{$smarty.const._NAME} </td>
                                            <td class = "topTitle" name = "direction_name">{$smarty.const._CATEGORY}</td>
                                            <td class = "topTitle" name = "languages_NAME">{$smarty.const._LANGUAGE}</td>
                                        {* MODULE HCD: Prices are replaced by the number of skills offered *}
                                            <td class = "topTitle centerAlign" name = "price">{$smarty.const._PRICE}</td>
                                            <td class = "topTitle centerAlign">{$smarty.const._CHECK}</td>
                                        </tr>
                        {foreach name = 'lessons_list2' key = 'key' item = 'proposed_lesson' from = $T_PROPOSED_LESSONS_DATA}
                                        <tr id="row_{$proposed_lesson.id}" class = "{cycle values = "oddRowColor, evenRowColor"} {if !$proposed_lesson.active}deactivatedTableElement{/if}">
                                            <td id = "column_{$proposed_lesson.id}" class = "editLink">{$proposed_lesson.link}</td>
                                            <td>{$proposed_lesson.direction_name}</td>
                                            <td>{$proposed_lesson.languages_NAME}</td>
                                        {* MODULE HCD: Prices are replaced by the number of skills offered *}
                                            <td align="center">{if $proposed_lesson.price == 0}{$smarty.const._FREE}{else}{$proposed_lesson.price} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}{/if}</td>
                                        {if $T_SKILLGAP_TEST && (!isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change')}
                                            <td class = "centerAlign">
                                                {if $smarty.session.s_type == 'administrator'}
                                                 <img class = "ajaxHandle" src = "images/16x16/arrow_right.png" id = "lesson_{$proposed_lesson.id}" name = "lesson_{$proposed_lesson.id}" onclick ="ajaxPost('{$proposed_lesson.id}', this,'proposedLessonsTable');">
                                                {elseif $proposed_course.show_catalog}
                                                    <a href = "{$smarty.server.PHP_SELF}?ctg=lessons&catalog=1&info_lesson={$proposed_lesson.id}"><img src = "images/16x16/view.png" alt = "{$smarty.const._MOREINFO}" title = "{$smarty.const._MOREINFO}"/></a>
                                                {/if}
                                                {*<input class = "inputCheckBox" type = "checkbox" id = "lesson_{$proposed_lesson.id}" name = "lesson_{$proposed_lesson.id}" onclick ="ajaxPost('{$proposed_lesson.id}', this,'proposedLessonsTable');">*}
                                            </td>
                                        {/if}
                                        </tr>
                        {foreachelse}
                                    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NOLESSONSPROPOSEDACCORDINGTOANALYSIS}</td></tr>
                        {/foreach}
                    </table>
<!--/ajax:proposedLessonsTable-->
                            </div>
                            {/if}
                            <div class="tabbertab">
                               <h3>{$smarty.const._COURSES}</h3>
<!--ajax:proposedCoursesTable-->
                                <table style = "width:100%" class = "sortedTable" size = "{$T_PROPOSED_COURSES_SIZE}" sortBy = "0" id = "proposedCoursesTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=tests&show_solved_test={$smarty.get.show_solved_test}&test_analysis={$smarty.get.test_analysis}&user={$smarty.get.user}{$T_MISSING_SKILLS_URL}&">
                                                <tr class = "topTitle">
                                                    <td class = "topTitle" name = "name">{$smarty.const._NAME} </td>
                                                    <td class = "topTitle" name = "direction_name">{$smarty.const._CATEGORY}</td>
                                                    <td class = "topTitle" name = "languages_NAME">{$smarty.const._LANGUAGE}</td>
                                                {* MODULE HCD: Prices are replaced by the number of skills offered *}
                                                    <td class = "topTitle centerAlign" name = "price">{$smarty.const._PRICE}</td>
                                                    <td class = "topTitle centerAlign">{$smarty.const._OPTIONS}</td>
                                                </tr>
                                {foreach name = 'courses_list2' key = 'key' item = 'proposed_course' from = $T_PROPOSED_COURSES_DATA}
                                                <tr id="row_{$proposed_course.id}" class = "{cycle values = "oddRowColor, evenRowColor"} {if !$proposed_course.active}deactivatedTableElement{/if}">
                                                    <td id = "column_{$proposed_course.id}" class = "editLink">{$proposed_course.link}</td>
                                                    <td>{$proposed_course.direction_name}</td>
                                                    <td>{$proposed_course.languages_NAME}</td>
                                                {* MODULE HCD: Prices are replaced by the number of skills offered *}
                                                    <td align="center">{if $proposed_course.price == 0}{$smarty.const._FREE}{else}{$proposed_course.price} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}{/if}</td>
                                                {if $T_SKILLGAP_TEST && (!isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change')}
                                                    <td class = "centerAlign">
                                                    {if $smarty.session.s_type == 'administrator'}
                                                        <input class = "inputCheckBox" type = "checkbox" id = "course_{$proposed_course.id}" name = "course_{$proposed_course.id}" onclick ="ajaxPost('{$proposed_course.id}', this,'proposedCoursesTable');">
                                                    {elseif $proposed_course.show_catalog}
                                                     <a href = "{$smarty.server.PHP_SELF}?ctg=lessons&catalog=1&info_course={$proposed_course.id}"><img src = "images/16x16/view.png" alt = "{$smarty.const._MOREINFO}" title = "{$smarty.const._MOREINFO}"/></a>
                                                    {/if}
                                                    </td>
                                                {/if}
                                                </tr>
                                {foreachelse}
                                            <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NOCOURSESPROPOSEDACCORDINGTOANALYSIS}</td></tr>
                                {/foreach}
                            </table>
<!--/ajax:proposedCoursesTable-->
                            </div>
                        </div>
                    </div>
{if $smarty.session.s_type == 'administrator'}
                    <div class="tabbertab">
                        <h3>{$smarty.const._ATTENDING}</h3>
                        <div class="tabber">
                        {if $T_CONFIGURATION.lesson_enroll} {*stand-alone lessons*}
                            <div class="tabbertab">
                            <h3>{$smarty.const._LESSONS}</h3>
<!--ajax:assignedLessonsTable-->
                                <table style = "width:100%" class = "sortedTable" size = "{$T_ASSIGNED_LESSONS_SIZE}" sortBy = "0" id = "assignedLessonsTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=tests&show_solved_test={$smarty.get.show_solved_test}&test_analysis={$smarty.get.test_analysis}&user={$smarty.get.user}&">
                                                <tr class = "topTitle">
                                                    <td class = "topTitle" name = "name">{$smarty.const._NAME} </td>
                                                    <td class = "topTitle" name = "direction_name">{$smarty.const._CATEGORY}</td>
                                                    <td class = "topTitle" name = "languages_NAME">{$smarty.const._LANGUAGE}</td>
                                                {* MODULE HCD: Prices are replaced by the number of skills offered *}
                                                    <td class = "topTitle centerAlign" name = "price">{$smarty.const._PRICE}</td>
                                                </tr>
                                {foreach name = 'lessons_list2' key = 'key' item = 'assigned_lesson' from = $T_ASSIGNED_LESSONS_DATA}
                                                <tr id="row_{$assigned_lesson.id}" class = "{cycle values = "oddRowColor, evenRowColor"} {if !$assigned_lesson.active}deactivatedTableElement{/if}">
                                                    <td id = "column_{$assigned_lesson.id}" class = "editLink">{$assigned_lesson.link}</td>
                                                    <td>{$assigned_lesson.direction_name}</td>
                                                    <td>{$assigned_lesson.languages_NAME}</td>
                                                {* MODULE HCD: Prices are replaced by the number of skills offered *}
                                                    <td align="center">{if $assigned_lesson.price == 0}{$smarty.const._FREE}{else}{$assigned_lesson.price} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}{/if}</td>
                                                </tr>
                                {foreachelse}
                                            <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NOLESSONSFOUND}</td></tr>
                                {/foreach}
                            </table>
<!--/ajax:assignedLessonsTable-->
                           </div>
      {/if}
                            <div class="tabbertab">
                            <h3>{$smarty.const._COURSES}</h3>
<!--ajax:assignedCoursesTable-->
                                <table style = "width:100%" class = "sortedTable" size = "{$T_ASSIGNED_COURSES_SIZE}" sortBy = "0" id = "assignedCoursesTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=tests&show_solved_test={$smarty.get.show_solved_test}&test_analysis={$smarty.get.test_analysis}&user={$smarty.get.user}&">
                                                <tr class = "topTitle">
                                                    <td class = "topTitle" name = "name">{$smarty.const._NAME} </td>
                                                    <td class = "topTitle" name = "direction_name">{$smarty.const._CATEGORY}</td>
                                                    <td class = "topTitle" name = "languages_NAME">{$smarty.const._LANGUAGE}</td>
                                                {* MODULE HCD: Prices are replaced by the number of skills offered *}
                                                    <td class = "topTitle centerAlign" name = "price">{$smarty.const._PRICE}</td>
                                                </tr>
                                {foreach name = 'courses_list2' key = 'key' item = 'assigned_course' from = $T_ASSIGNED_COURSES_DATA}
                                                <tr id="row_{$assigned_course.id}" class = "{cycle values = "oddRowColor, evenRowColor"} {if !$assigned_course.active}deactivatedTableElement{/if}">
                                                    <td id = "column_{$assigned_course.id}" class = "editLink">{$assigned_course.name}</td>
                                                    <td>{$assigned_course.directions_name}</td>
                                                    <td>{$assigned_course.languages_NAME}</td>
                                                {* MODULE HCD: Prices are replaced by the number of skills offered *}
                                                    <td align="center">{if $assigned_course.price == 0}{$smarty.const._FREE}{else}{$assigned_course.price} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}{/if}</td>
                                                </tr>
                                {foreachelse}
                                            <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NOCOURSESFOUND}</td></tr>
                                {/foreach}
                            </table>
<!--/ajax:assignedCoursesTable-->
                            </div>
                        </div>
                    </div>
{/if}
                </div>
            {/capture}
            {eF_template_printBlock title = $smarty.const._SKILLGAPANALYSISFORUSER|cat:'&nbsp;<i>'|cat:$T_USER_INFO.name|cat:'&nbsp;'|cat:$T_USER_INFO.surname|cat:'</i>&nbsp;'|cat:$smarty.const._ACCORDINGTOTEST|cat:'&nbsp;<i>'|cat:$T_TEST_DATA->test.name|cat:'</i>' data = $smarty.capture.t_user_code image = '32x32/profile.png' options=$T_USER_LINK}
  {else}
   {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=tests&show_solved_test=`$T_TEST_DATA->completedTest.id`&test_analysis=1'>`$smarty.const._TESTANALYSISFORTEST` &quot;`$T_TEST_DATA->test.name`&quot;</a>"}
   {capture name = "t_test_analysis_code"}
             <div class = "headerTools">
                 <span>
                     <img src = "images/16x16/arrow_left.png" alt = "{$smarty.const._VIEWSOLVEDTEST}" title = "{$smarty.const._VIEWSOLVEDTEST}">
                        <a href = "{$smarty.server.PHP_SELF}?ctg=tests&show_solved_test={$smarty.get.show_solved_test}">{$smarty.const._VIEWSOLVEDTEST}</a>
                    </span>
     {if $T_TEST_STATUS.testIds|@sizeof > 1}
                    <span>
                        <img src = "images/16x16/go_into.png" alt = "{$smarty.const._JUMPTOEXECUTION}" title = "{$smarty.const._JUMPTOEXECUTION}">
                     {$smarty.const._JUMPTOEXECUTION}
                     <select style = "vertical-align:middle" onchange = "location.toString().match(/show_solved_test/) ? location = location.toString().replace(/show_solved_test=\d+/, 'show_solved_test='+this.options[this.selectedIndex].value) : location = location + '&show_solved_test='+this.options[this.selectedIndex].value">
                      {foreach name = "test_analysis_list" item = "item" key = "key" from = $T_TEST_STATUS.testIds}
                       <option value = "{$item}" {if $smarty.get.show_solved_test == $item}selected{/if}>#{$smarty.foreach.test_analysis_list.iteration} - #filter:timestamp_time-{$T_TEST_STATUS.timestamps[$key]}#</option>
                      {/foreach}
                     </select>
                    </span>
     {/if}
                </div>
                <table style = "width:100%">
                    <tr><td style = "vertical-align:top">{$T_CONTENT_ANALYSIS}</td></tr>
                    <tr><td>
                     <div id = "graph_table"><div id = "proto_chart" class = "proto_graph"></div></div>
                     <script>var show_test_graph = true;</script>
                    </td></tr>
                </table>
            {/capture}
            {eF_template_printBlock title = "`$smarty.const._TESTANALYSIS` `$smarty.const._FORTEST` <span class = \"innerTableName\">&quot;`$T_TEST_DATA->test.name`&quot;</span> `$smarty.const._ANDUSER` <span class = \"innerTableName\">&quot;#filter:login-`$T_TEST_DATA->completedTest.login`#&quot;</span>" data = $smarty.capture.t_test_analysis_code image='32x32/tests.png'}
        {/if}
    {/if}
{elseif $smarty.get.questions_order}
                                {capture name = 'questions_tree'}
                                    <ul id = "dhtmlgoodies_question_tree" class = "dhtmlgoodies_tree">
                                    {foreach name = 'questions_list' key = 'id' item = 'question' from = $T_QUESTIONS}
                                        <li id = "dragtree_{$id}" noChildren = "true">
                                            <a class = "drag_tree_questions" href = "javascript:void(0)" onmouseover = "eF_js_showHideDiv(this, 'div_{$id}', event)" onmouseout = "$('div_{$id}').hide()">: {$question.text|eF_truncate:100}</a>
                                        </li>
                                    {/foreach}
                                    </ul>
                                    {foreach name = 'questions_list' key = 'id' item = 'question' from = $T_QUESTIONS}
                                        {*We put this in a separate loop, since if it is put in the same loop as the <li> it causes tree to malfunction at submission*}
                                        <div id = "div_{$id}" style = "display:none;width:70%" class = "popUpInfoDiv">{$question.text}</div>
                                    {/foreach}
                                {/capture}
        {capture name = 'questions_treeTotal'}
                                <table style = "width:100%">
                                    <tr><td class = "mediumHeader popUpInfoDiv" style = "width:90%">{$smarty.const._DRAGITEMSTOCHANGEQUESTIONSORDER}</td></tr>
         <tr><td>&nbsp;</td></tr>
         <tr><td>&nbsp;</td></tr>
                                    <tr><td>{$smarty.capture.questions_tree}</td></tr>
                                    <tr><td>&nbsp;</td></tr>
                                    <tr><td><input class = "flatButton" type="button" onclick="saveQuestionTree()" value="{$smarty.const._SAVECHANGES}"></td></tr>
                                </table>
                                {*<div id = "expand_collapse_div" style = "display:none"></div>*}
        {/capture}
        {eF_template_printBlock title = $smarty.const._CHANGEORDER data = $smarty.capture.questions_treeTotal image = '32x32/order.png'}
                                <script>
                                {literal}
                                function saveQuestionTree() {
                                    //alert(treeObj.getNodeOrders());
                                    new Ajax.Request('{/literal}{$smarty.server.PHP_SELF}{literal}?ctg=tests&questions_order={/literal}{$smarty.get.questions_order}{literal}&ajax=1&order='+treeObj.getNodeOrders(), {
                                        method:'get',
                                        asynchronous:true,
                                        onSuccess: function (transport) {
                                            alert(transport.responseText);
                                        }
                                    });
                                }
                                {/literal}
                                </script>
{elseif $smarty.get.show_question}
        {capture name = "t_show_question_code"}
                                {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=tests&show_question=`$T_QUESTION.id`'>`$smarty.const._VIEWQUESTION`</a>"}
                                <br/>
                                 {literal}<style type = "text/css">span.orderedList{display:none;}</style>{/literal}
                                    <table width = "100%" align = "center" tyle = "border:1px solid black">
                                        <tr><td>
                                            {$T_QUESTION_PREVIEW}
                                        </td></tr>
                                    </table>
        {/capture}
        {eF_template_printBlock title = $smarty.const._PREVIEW data = $smarty.capture.t_show_question_code image = '32x32/search.png'}
                                <br/><br/>
                            {* Show results for all users of each specific*}
{elseif $smarty.get.test_results}
                                {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=`$T_CTG`&test_results=`$smarty.get.test_results`'>`$T_TEST->test.name` `$smarty.const._RESULTS`</a>"}
                                {capture name = 't_test_results_code'}
        <div class = "headerTools">
         <span>
          <img src = "images/16x16/error_delete.png" alt = "{$smarty.const._RESETEXECUTIONSFORALLUSERS}" title = "{$smarty.const._RESETEXECUTIONSFORALLUSERS}"/>
          <a href = "javascript:void(0)" onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) deleteAllTestsForAllUsers(this);">{$smarty.const._RESETEXECUTIONSFORALLUSERS}</a>
         </span>
        </div>
                                    <table class = "sortedTable" style = "width:100%">
                                        <tr class="defaultRowHeight"><td class = "topTitle">{$smarty.const._USER}</td>
                                            {if !$T_SKILLGAP_TEST}
                                            <td class = "topTitle centerAlign">{$smarty.const._PENDING}</td>
            {if $T_CTG != 'feedback'}
             <td class = "topTitle centerAlign">{$smarty.const._TIMESDONE}</td>
            {/if}
                                            {/if}
           {if $T_CTG != 'feedback'}
            <td class = "topTitle centerAlign">{$smarty.const._AVERAGESCORE}</td>
            <td class = "topTitle centerAlign">{$smarty.const._MAXSCORE}</td>
            <td class = "topTitle centerAlign">{$smarty.const._MINSCORE}</td>
           {/if}
           <td class = "topTitle centerAlign">{$smarty.const._FUNCTIONS}</td></tr>
                                    {foreach name = "questions_list" key = "key" item = "item" from = $T_DONE_TESTS}
                                        <tr class = "{cycle name = "main_cycle" values = "oddRowColor, evenRowColor"} defaultRowHeight">
                                            <td>{$key} ({$item.surname} {$item.name})</td>
                                            {if !$T_SKILLGAP_TEST}
                                            <td class = "centerAlign">{if $item[$item.last_test_id].pending}{$smarty.const._YES}{else}{$smarty.const._NO}{/if}</td>
            {if $T_CTG != 'feedback'}
             <td class = "centerAlign">{$item.times_done}</td>
            {/if}
                                            {/if}
           {if $T_CTG != 'feedback'}
            <td class = "centerAlign">#filter:score-{$item.average_score}#%</td>
            <td class = "centerAlign">#filter:score-{$item.max_score}#%</td>
            <td class = "centerAlign">#filter:score-{$item.min_score}#%</td>
           {/if}
                                            <td class = "centerAlign">
                    <a href = "{$smarty.server.PHP_SELF}?ctg={$T_CTG}&show_solved_test={$item.last_test_id}">
                        <img src = "images/16x16/search.png" alt = "{$smarty.const._VIEWTEST}" title = "{$smarty.const._VIEWTEST}" border = "0"/></a>
                    {if $T_CTG != 'feedback'}
      <a href = "{$smarty.server.PHP_SELF}?ctg=tests&show_solved_test={$item.last_test_id}&test_analysis=1&user={$key}">
       <img src = "images/16x16/analysis.png" alt = "{$smarty.const._TESTANALYSIS}" title = "{$smarty.const._TESTANALYSIS}" border = "0"/></a>
                    {/if}
     {if !$T_SKILLGAP_TEST}
                    <a href = "javascript:void(0)" onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) deleteAllTests(this, '{$key}')">
                        <img src = "images/16x16/error_delete.png" alt = "{$smarty.const._RESETALLTESTSSTATUS}" title = "{$smarty.const._RESETALLTESTSSTATUS}" border = "0"/></a>
                    {else}
                    <a href = "{$smarty.server.PHP_SELF}?ctg=tests&delete_solved_test={$item.last_test_id}&test_id={$smarty.get.test_results}&users_login={$key}" onclick = "return confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}');"/>
                        <img border="0" src="images/16x16/error_delete.png" style="vertical-align:middle" alt="{$smarty.const._DELETESKILLGAPTESTRECORD}" title="{$smarty.const._DELETESKILLGAPTESTRECORD}" </a>
                    {/if}
                                            </td></tr>
                                    {foreachelse}
                                            <tr class = "oddRowColor defaultRowHeight"><td class = "emptyCategory" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
                                    {/foreach}
                                    </table>
                                {/capture}
        {if $T_CTG != 'feedback'}
         {eF_template_printBlock title = $smarty.const._TESTRESULTS data = $smarty.capture.t_test_results_code image='32x32/tests.png'}
        {else}
         {eF_template_printBlock title = $smarty.const._FEEDBACKRESULTS data = $smarty.capture.t_test_results_code image='32x32/feedback.png'}
        {/if}
       {* Show list of all solved tests *}
{elseif $smarty.get.solved_tests}
                                {capture name = 't_recently_completed'}
                                                <table width = "100%" class = "sortedTable">
                                                    <tr class = "defaultRowHeight">
                                                        <td class = "topTitle">{$smarty.const._DATE}</td>
                                                        <td class = "topTitle">{$smarty.const._NAME}</td>
                                                        <td class = "topTitle">{$smarty.const._STUDENT}</td>
                                                        <td class = "topTitle centerAlign">{$smarty.const._SCORE}</td>
                                                        <td class = "topTitle centerAlign noSort">{$smarty.const._FUNCTIONS}</td>
                                                    </tr>
                                    {foreach name = 't_recently_completed_tests' item = 'recent_test' from = $T_RECENT_TESTS}
                                                    <tr class = "{cycle name = "main_cycle" values="oddRowColor,evenRowColor"} defaultRowHeight">
                                                        <td>#filter:timestamp_time-{$recent_test.timestamp}#</td>
                                                        <td>{$recent_test.name}</td>
                                                        <td>#filter:login-{$recent_test.users_LOGIN}#</td>
                                                        <td class = "centerAlign">{if $recent_test.score}{$recent_test.score}%{else}0.00%{/if}</td>
                                                        <td align = "center">
                                                            <a href = "{$smarty.server.PHP_SELF}?ctg=tests&show_solved_test={$recent_test.id}">
                                                                <img src = "images/16x16/search.png" alt = "{$smarty.const._VIEWTEST}" title = "{$smarty.const._VIEWTEST}" border = "0"/></a>
                                                            <a href = "{$smarty.server.PHP_SELF}?ctg=tests&show_solved_test={$recent_test.id}&test_analysis=1&user={$recent_test.users_LOGIN}">
                                                                <img src = "images/16x16/analysis.png" alt = "{$smarty.const._TESTANALYSIS}" title = "{$smarty.const._TESTANALYSIS}" border = "0"/></a>
                                                            <a href = "{$smarty.server.PHP_SELF}?ctg=tests&delete_solved_test={$recent_test.id}&test_id={$recent_test.tests_ID}&users_login={$recent_test.users_LOGIN}" onclick = "return confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}');"/>
                                                                <img src="images/16x16/error_delete.png" style="vertical-align:middle" alt="{$smarty.const._DELETESKILLGAPTESTRECORD}" title="{$smarty.const._DELETESKILLGAPTESTRECORD}"> </a>
                                                        </td>
                                                    </tr>
                                    {foreachelse}
                                                    <tr><td class = "emptyCategory oddRowColor" colspan = "100%" style = "text-align:center">{$smarty.const._NOCOMPLETEDSKILLGAP}</td></tr>
                                    {/foreach}
                                                </table>
                                {/capture}
                                {eF_template_printBlock title=$smarty.const._SKILLGAPTESTS data=$smarty.capture.t_recently_completed image='32x32/skill_gap.png'}
{else}
 {capture name = 't_tests_code'}
  <script>var published = '{$smarty.const._PUBLISHED}';var notpublished = '{$smarty.const._NOTPUBLISHED}';</script>
     {if $_change_}
         <div class = "headerTools">
          <span>
    {if $T_CTG != "feedback"}
           <img src = "images/16x16/add.png" title = "{if $T_SKILLGAP_TEST}{$smarty.const._ADDSKILLGAPTEST}{else}{$smarty.const._ADDTEST}{/if}" alt = "{if $T_SKILLGAP_TEST}{$smarty.const._ADDSKILLGAPTEST}{else}{$smarty.const._ADDTEST}{/if}"/>
           <a href = "{$smarty.server.PHP_SELF}?ctg=tests&add_test=1{if $smarty.get.from_unit}&from_unit={$smarty.get.from_unit}{/if}">{if $T_SKILLGAP_TEST}{$smarty.const._ADDSKILLGAPTEST}{else}{$smarty.const._ADDTEST}{/if}</a>
          {else}
  {* {if $T_TESTS|@sizeof < 1} *}
      <img src = "images/16x16/add.png" title = "{$smarty.const._ADDFEEDBACSFEEDBACK}" alt = "{$smarty.const._ADDFEEDBACSFEEDBACK}"/>
      <a href = "{$smarty.server.PHP_SELF}?ctg=feedback&add_test=1{if $smarty.get.from_unit}&from_unit={$smarty.get.from_unit}{/if}">{$smarty.const._ADDFEEDBACSFEEDBACK}</a>
   {* {/if} *}
    {/if}
    </span>
      {if $T_SKILLGAP_TEST}
          <span>
              <img src = "images/16x16/wizard.png" alt = "{$smarty.const._ADDQUICKSKILLGAP}" title = "{$smarty.const._ADDQUICKSKILLGAP}" />
                 <a href = "{$smarty.server.PHP_SELF}?ctg=tests&add_test=1&create_quick_test=1">{$smarty.const._ADDQUICKSKILLGAP}</a>
             </span>
      {/if}
         </div>
     {/if}
     <table width = "100%" class = "sortedTable">
         <tr class = "defaultRowHeight">
             <td class = "topTitle">{$smarty.const._NAME}</td>
         {if !$T_SKILLGAP_TEST && $T_CTG != "feedback"}
             <td class = "topTitle">{$smarty.const._UNITPARENT}</td>
   {elseif $T_CTG != "feedback"}
    <td class = "topTitle centerAlign">{$smarty.const._GENERALTHRESHOLD}</td>
   {/if}
    <td class = "topTitle centerAlign">{$smarty.const._PUBLISHED}</td>
             <td class = "topTitle centerAlign">{$smarty.const._QUESTIONS}</td>
             {if $T_CTG != "feedback"}
     <td class = "topTitle centerAlign">{$smarty.const._AVERAGESCORE}</td>
             {/if}
    <td class = "topTitle centerAlign noSort">{$smarty.const._FUNCTIONS}</td>
         </tr>
   {foreach name = 'tests_list' key = "key" item = "test" from = $T_TESTS}
         <tr class = "{cycle name = "main_cycle" values="oddRowColor,evenRowColor"} defaultRowHeight">
             <td><a class = "editLink" href = "{$smarty.server.PHP_SELF}?ctg={$T_CTG}&edit_test={$test.id}">{$test.name|eF_truncate:40}</a></td>
         {if !$T_SKILLGAP_TEST && $T_CTG != "feedback"}
             <td>{$test.parent_unit}</td>
         {elseif $T_CTG != "feedback"}
             <td class = "centerAlign">{$test.options.general_threshold}%</td>
         {/if}
             <td class = "centerAlign">{if $test.publish}<img src = "images/16x16/success.png" alt = "{$smarty.const._PUBLISHED}" title = "{$smarty.const._PUBLISHED}" onclick = "publish(this, {$test.id})" class = "ajaxHandle">{else}<img src = "images/16x16/forbidden.png" alt = "{$smarty.const._NOTPUBLISHED}" title = "{$smarty.const._NOTPUBLISHED}" onclick = "publish(this, {$test.id})" class = "ajaxHandle">{/if}</td>
             <td class = "centerAlign">{$test.questions_num}</td>
             {if $T_CTG != "feedback"}
     <td class = "centerAlign">{if isset($test.average_score) || $test.average_score === 0}#filter:score-{$test.average_score}#&nbsp;%{else}-{/if}</td>
             {/if}
    <td class = "noWrap centerAlign">
                 <a href = "{$smarty.server.PHP_SELF}?ctg={$T_CTG}&test_results={$test.id}"><img src = "images/16x16/unit.png" alt = "{$smarty.const._RESULTS}" title = "{$smarty.const._RESULTS}" /></a>
                 <a href = "{$smarty.server.PHP_SELF}?{if !$T_SKILLGAP_TEST}view_unit={$test.content_ID}{else}ctg={$T_CTG}&show_test={$test.id}{/if}"><img src = "images/16x16/search.png" alt = "{$smarty.const._PREVIEW}" title = "{$smarty.const._PREVIEW}"/></a>
                 <a href = "{$smarty.server.PHP_SELF}?ctg={$T_CTG}&show_test={$test.id}&print=1&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._PRINT}', 2)"><img src = "images/16x16/printer.png" alt = "{$smarty.const._PRINT}" title = "{$smarty.const._PRINT}" /></a>
         {if $_change_}
                 {if !$test.options.shuffle_questions && !$test.options.random_pool}
                 <a href = "{$smarty.server.PHP_SELF}?ctg={$T_CTG}&questions_order={$test.id}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._CHANGEORDER}', 2)"><img src = "images/16x16/order.png" alt = "{$smarty.const._CHANGEORDER}" title = "{$smarty.const._CHANGEORDER}"/></a>
                 {/if}
                 <a href = "{$smarty.server.PHP_SELF}?ctg={$T_CTG}&edit_test={$test.id}"><img src = "images/16x16/edit.png" alt = "{$smarty.const._EDIT}" title = "{$smarty.const._EDIT}" /></a>
                 <img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._DELETE}" title = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) deleteTest(this, '{$test.id}');"/>
         {/if}
             </td></tr>
         {foreachelse}
         <tr class = "defaultRowHeight oddRowColor"><td colspan = "7" class = "emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
         {/foreach}
     </table>
 {/capture}
 {capture name = 't_questions_code'}
  {if $_change_ && $T_CTG != 'feedback'}
  <div class = "headerTools">
   <span>
    <img src = "images/16x16/add.png" title = "{$smarty.const._ADDQUESTION}" alt = "{$smarty.const._ADDQUESTION}"/>
    <select name = "question_type" onchange = "if (this.options[this.options.selectedIndex].value) window.location='{$smarty.server.PHP_SELF}?ctg=tests&add_question=1&question_type='+this.options[this.options.selectedIndex].value">
     <option value = "">{$smarty.const._ADDQUESTIONOFTYPE}</option>
     <option value = "">---------------</option>
     {foreach name = 'question_types' item = "item" key = "key" from = $T_QUESTIONTYPESTRANSLATIONS}
      {if !$T_SKILLGAP_TEST || $key != 'raw_text'}
      <option value = "{$key}">{$item}</option>
      {/if}
     {/foreach}
    </select>
   </span>
   {if !$T_SKILLGAP_TEST && !$T_CONFIGURATION.disable_questions_pool}
    <span style = "float:right;border-width:0px">
    {if !$smarty.get.showall}
         <a href = "{$smarty.server.PHP_SELF}?ctg={$T_CTG}&showall=1&tab=questions" ><img src = "images/16x16/order.png" alt = "{$smarty.const._SHOWQUESTIONSPOOL}" title = "{$smarty.const._SHOWQUESTIONSPOOL}"/>&nbsp;{$smarty.const._SHOWQUESTIONSPOOL}</a>
        {else}
         <a href = "{$smarty.server.PHP_SELF}?ctg={$T_CTG}&showall=0&tab=questions" ><img src = "images/16x16/order.png" alt = "{$smarty.const._SHOWLESSONQUESTIONS}" title = "{$smarty.const._SHOWLESSONQUESTIONS}"/>&nbsp;{$smarty.const._SHOWLESSONQUESTIONS}</a>
          {/if}
        </span>
       {/if}
  </div>
  {/if}
{if $smarty.get.showall && !$T_CONFIGURATION.disable_questions_pool}
<!--ajax:questionsTable-->
  <table class = "QuestionsListTable sortedTable" id = "questionsTable" size = "{$T_QUESTIONS_SIZE}" sortBy = "0" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg={$T_CTG}&from_unit={$smarty.get.from_unit}&tab={$smarty.get.tab}&showall={$smarty.get.showall}&">
         <tr class = "defaultRowHeight">
             <td name = "text" class = "topTitle">{$smarty.const._QUESTION}</td>
         {if $smarty.get.showall}
          <td name = "lesson_name" class = "topTitle">{$smarty.const._LESSON}</td>
         {/if}
             <td name = "type" class = "topTitle centerAlign">{$smarty.const._QUESTIONTYPE}</td>
   {if $T_CTG != 'feedback'}
             <td name = "difficulty" class = "topTitle centerAlign">{$smarty.const._DIFFICULTY}</td>
             <td class = "topTitle centerAlign" name = "estimate">{$smarty.const._TIME}</td>
   {/if}
             <td class = "topTitle centerAlign noSort">{$smarty.const._FUNCTIONS}</td>
         </tr>
   {foreach name = 'questions_list' key = 'key' item = 'question' from = $T_QUESTIONS}
   {if $T_CTG == 'tests' || ($T_CTG == 'feedback' && $question.type != 'true_false')}
    <tr class = "{cycle name = "main_cycle" values="oddRowColor,evenRowColor"} defaultRowHeight">
     <td>
    {if $_change_ && $question.lessons_ID == $smarty.session.s_lessons_ID}
      <a class = "editLink" href = "{$smarty.server.PHP_SELF}?ctg=tests&edit_question={$question.id}&question_type={$question.type}&lessonId={$question.lessons_ID}" title= "{$question.text}">{$question.text|eF_truncate:70}</a>
    {else}{$question.text|eF_truncate:70}{/if}
     </td>
    {if $smarty.get.showall}
           <td><span title = "{$T_LESSONS[$question.lessons_ID].lesson_path}">{$question.lesson_name}</span></td>
          {/if}
     <td class = "centerAlign">
      <img src = "{$T_QUESTION_TYPE_ICONS[$question.type]}" title = "{$T_QUESTION_TYPES[$question.type]}" alt = "{$T_QUESTION_TYPES[$question.type]}" />
      <span style = "display:none">{$question.type}</span>
     </td>
    {if $T_CTG != 'feedback'}
     <td class = "centerAlign">
      <img src = "{$T_QUESTION_DIFFICULTY_ICONS[$question.difficulty]}" title = "{$T_QUESTION_DIFFICULTIES[$question.difficulty]}" alt = "{$T_QUESTION_DIFFICULTIES[$question.difficulty]}" />
      <span style = "display:none">{$question.difficulty}</span>
     </td>
     <td class = "centerAlign">{if !$question.estimate}-{else}{if $question.estimate_interval.minutes}{$question.estimate_interval.minutes}{$smarty.const._MINUTESSHORTHAND}{/if} {if $question.estimate_interval.seconds}{$question.estimate_interval.seconds}{$smarty.const._SECONDSSHORTHAND}{/if}{/if}</td>
    {/if}
       <td class = "centerAlign noWrap">
      <a href = "{$smarty.server.PHP_SELF}?ctg=tests&show_question={$question.id}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._PREVIEW}', 1)"><img src = "images/16x16/search.png" alt = "{$smarty.const._PREVIEW}" title = "{$smarty.const._PREVIEW}" /></a>
     {if !isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change'}
      {if $T_CTG != 'feedback' && $question.lessons_ID == $smarty.session.s_lessons_ID }
       <a class = "editLink" href = "{$smarty.server.PHP_SELF}?ctg=tests&edit_question={$question.id}&question_type={$question.type}&lessonId={$question.lessons_ID}"><img src = "images/16x16/edit.png" alt = "{$smarty.const._CORRECTION}" title = "{$smarty.const._CORRECTION}"/></a>
       <img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._DELETE}" title = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) deleteQuestion(this, '{$question.id}')"/>
      {/if}
     {/if}
     </td>
    </tr>
   {/if}
   {foreachelse}
         <tr class = "oddRowColor defaultRowHeight"><td class = "emptyCategory" colspan = "6">{$smarty.const._NOQUESTIONSSETFORTHISUNIT}</td></tr>
         {/foreach}
     </table>
<!--/ajax:questionsTable-->
{else}
<!--ajax:questionsTable-->
  <table class = "QuestionsListTable sortedTable" id = "questionsTable" size = "{$T_QUESTIONS_SIZE}" sortBy = "0" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg={$T_CTG}&from_unit={$smarty.get.from_unit}&tab={$smarty.get.tab}&showall={$smarty.get.showall}&">
         <tr class = "defaultRowHeight">
             <td name = "text" class = "topTitle">{$smarty.const._QUESTION}</td>
         {if $smarty.get.showall}
          <td name = "lesson" class = "topTitle">{$smarty.const._LESSON}</td>
         {/if}
         {if !$T_SKILLGAP_TEST}
             <td name = "parent_unit" class = "topTitle">{$smarty.const._UNIT}</td>
         {else}
             <td name = "name" class = "topTitle">{$smarty.const._ASSOCIATEDWITH}</td>
         {/if}
             <td name = "type" class = "topTitle centerAlign">{$smarty.const._QUESTIONTYPE}</td>
   {if $T_CTG != 'feedback'}
             <td name = "difficulty" class = "topTitle centerAlign">{$smarty.const._DIFFICULTY}</td>
             <td class = "topTitle centerAlign" name = "estimate">{$smarty.const._TIME}</td>
   {/if}
             <td class = "topTitle centerAlign noSort">{$smarty.const._FUNCTIONS}</td>
         </tr>
   {foreach name = 'questions_list' key = 'key' item = 'question' from = $T_QUESTIONS}
   {if $T_CTG == 'tests' || ($T_CTG == 'feedback' && $question.type != 'true_false')}
    <tr class = "{cycle name = "main_cycle" values="oddRowColor,evenRowColor"} defaultRowHeight">
     <td>
    {if $_change_}
      <a class = "editLink" href = "{$smarty.server.PHP_SELF}?ctg=tests&edit_question={$question.id}&question_type={$question.type}&lessonId={$question.lessons_ID}" title= "{$question.text}">{$question.text|eF_truncate:70}</a>
    {else}{$question.text|eF_truncate:70}{/if}
     </td>
    {if $smarty.get.showall}
           <td><span title = "{$T_LESSONS[$question.lessons_ID].lesson_path}">{$T_LESSONS[$question.lessons_ID].name}</span></td>
          {/if}
    {if !$T_SKILLGAP_TEST}
     <td>{$question.parent_unit}</td>
    {else}
     <td>{$question.name}</td>
    {/if}
     <td class = "centerAlign">
      <img src = "{$T_QUESTION_TYPE_ICONS[$question.type]}" title = "{$T_QUESTION_TYPES[$question.type]}" alt = "{$T_QUESTION_TYPES[$question.type]}" />
      <span style = "display:none">{$question.type}</span>
     </td>
    {if $T_CTG != 'feedback'}
     <td class = "centerAlign">
      <img src = "{$T_QUESTION_DIFFICULTY_ICONS[$question.difficulty]}" title = "{$T_QUESTION_DIFFICULTIES[$question.difficulty]}" alt = "{$T_QUESTION_DIFFICULTIES[$question.difficulty]}" />
      <span style = "display:none">{$question.difficulty}</span>
     </td>
     <td class = "centerAlign">{if !$question.estimate}-{else}{if $question.estimate_interval.minutes}{$question.estimate_interval.minutes}{$smarty.const._MINUTESSHORTHAND}{/if} {if $question.estimate_interval.seconds}{$question.estimate_interval.seconds}{$smarty.const._SECONDSSHORTHAND}{/if}{/if}</td>
    {/if}
       <td class = "centerAlign noWrap">
      <a href = "{$smarty.server.PHP_SELF}?ctg=tests&show_question={$question.id}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._PREVIEW}', 1)"><img src = "images/16x16/search.png" alt = "{$smarty.const._PREVIEW}" title = "{$smarty.const._PREVIEW}" /></a>
     {if !isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change'}
      {if $T_SKILLGAP_TEST && (!isset($T_CURRENT_USER->coreAccess.skillgaptests) || $T_CURRENT_USER->coreAccess.skillgaptests == 'change')}
      <a href = "{$smarty.server.PHP_SELF}?ctg=tests&edit_question={$question.id}&lessonId={$question.lessons_ID}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._CORRELATESKILLSTOQUESTION}', 2)"><img src = "images/16x16/tools.png" alt = "{$smarty.const._CORRELATESKILLSTOQUESTION}" title = "{$smarty.const._CORRELATESKILLSTOQUESTION}" /></a>
      {/if}
      {if $T_CTG != 'feedback'}
       <a class = "editLink" href = "{$smarty.server.PHP_SELF}?ctg=tests&edit_question={$question.id}&question_type={$question.type}&lessonId={$question.lessons_ID}"><img src = "images/16x16/edit.png" alt = "{$smarty.const._CORRECTION}" title = "{$smarty.const._CORRECTION}"/></a>
       <img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._DELETE}" title = "{$smarty.const._DELETE}" onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) deleteQuestion(this, '{$question.id}')"/>
      {/if}
     {/if}
     </td>
    </tr>
   {/if}
   {foreachelse}
         <tr class = "oddRowColor defaultRowHeight"><td class = "emptyCategory" colspan = "6">{$smarty.const._NOQUESTIONSSETFORTHISUNIT}</td></tr>
         {/foreach}
     </table>
<!--/ajax:questionsTable-->
{/if}
 {/capture}
 {capture name = "t_tests_and_questions_code"}
     {if !$T_SKILLGAP_TEST && $T_CTG != "feedback"}
  <div class = "headerTools">
   <span>{$smarty.const._SHOWDATAFORUNIT}:&nbsp;</span>
   <select name = "select_unit" onchange = "var tab = 'tests';$$('div.tabbertab').each(function (s) {ldelim}if (!s.hasClassName('tabbertabhide')) {ldelim}tab = s.id;{rdelim}{rdelim});document.location='{$smarty.server.PHP_SELF}?ctg=tests&from_unit='+this.options[this.selectedIndex].value+'&tab='+tab">
          <option value = "-1" {if $smarty.get.from_unit == -1}selected{/if}>{$smarty.const._ALLUNITS}</option>
             <option value = "-2">-----------</option>
   {foreach name = 'unit_options' key = 'id' item = 'unit' from = $T_UNITS}
       <option value = "{$id}" {if $id == $smarty.get.from_unit}selected{/if}>{$unit}</option>
   {/foreach}
   </select>
  </div>
     {/if}
 {capture name = "t_all_tests_code"}
    {$smarty.capture.t_tests_code}
          <br>
          {* Show pending tests*}
          {capture name = 't_pending_tests'}
<!--ajax:pendingTable-->
    <table style = "width:100%" class = "sortedTable" id = "pendingTable" size = "{$T_PENDING_SIZE}" sortBy = "0" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg={$T_CTG}&">
           <tr class = "defaultRowHeight">
               <td class = "topTitle" name = "time_end">{$smarty.const._COMPLETEDON}</td>
               <td class = "topTitle" name = "name">{$smarty.const._NAME}</td>
               <td class = "topTitle" name = "users_LOGIN">{$smarty.const._STUDENT}</td>
     {if $T_CTG != "feedback"}
               <td class = "topTitle" name = "pending">{$smarty.const._PENDING}</td>
               <td class = "topTitle centerAlign" name = "score" >{$smarty.const._SCORE}</td>
     {/if}
               <td class = "topTitle centerAlign noSort">{$smarty.const._FUNCTIONS}</td>
           </tr>
  {foreach name = 'pending_tests_loop' item = "item" key = "key" from = $T_PENDING_TESTS}
              <tr class = "{cycle name = "main_cycle" values="oddRowColor,evenRowColor"} defaultRowHeight">
                  <td>#filter:timestamp_time-{if isset($item.time_end)}{$item.time_end}{else}{$item.timestamp}{/if}#</td>
              {if $T_CTG != "feedback"}
                 <td><a class="editLink" href = "{$smarty.server.PHP_SELF}?ctg=tests&show_solved_test={$item.id}">{$item.name}</a></td>
              {else}
                <td><a class="editLink" href = "{$smarty.server.PHP_SELF}?ctg=feedback&show_solved_test={$item.id}">{$item.name}</a></td>
              {/if}
                 <td>#filter:login-{$item.users_LOGIN}#</td>
     {if $T_CTG != "feedback"}
                 <td>{if $item.pending}{$smarty.const._YES}{else}{$smarty.const._NO}{/if}</td>
                 <td class = "centerAlign">{if $item.score}{$item.score|formatScore}%{else}0.00%{/if}</td>
     {/if}
                  <td class = "centerAlign">
       <a href = "{$smarty.server.PHP_SELF}?ctg={$T_CTG}&show_solved_test={$item.id}">
                   <img src = "images/16x16/search.png" alt = "{$smarty.const._VIEWTEST}" title = "{$smarty.const._VIEWTEST}"/></a>
              {if $T_CTG != "feedback"}
       <a href = "{$smarty.server.PHP_SELF}?ctg=tests&show_solved_test={$item.id}&test_analysis=1&user={$item.users_LOGIN}">
                   <img src = "images/16x16/analysis.png" alt = "{$smarty.const._TESTANALYSIS}" title = "{$smarty.const._TESTANALYSIS}"/></a>
     {/if}
      <img class = "ajaxHandle" src="images/16x16/error_delete.png" alt="{$smarty.const._RESETTESTSTATUS}" title="{$smarty.const._RESETTESTSTATUS}" onclick = "ajaxRemoveSolvedTest(this, '{$item.users_LOGIN}', '{$item.id}','{$item.tests_ID}')"> </a>
               </td>
     </tr>
  {foreachelse}
           <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "6">{$smarty.const._NODATAFOUND}</td></tr>
  {/foreach}
    </table>
<!--/ajax:pendingTable-->
    {/capture}
    {if $T_SKILLGAP_TEST}
              {eF_template_printBlock title=$smarty.const._RECENTLYCOMPLETEDSKILLGAP data=$smarty.capture.t_pending_tests image='32x32/skill_gap.png' options = $T_RECENTLY_SKILLGAP_OPTIONS}
          {elseif $T_CTG != "feedback"}
              {eF_template_printBlock title=$smarty.const._RECENTLYCOMPLETEDTESTS data=$smarty.capture.t_pending_tests image='32x32/tests.png'}
          {else}
     {eF_template_printBlock title=$smarty.const._RECENTLYCOMPLETEDFEEDBACK data=$smarty.capture.t_pending_tests image='32x32/feedback.png'}
    {/if}
  {/capture}
  {if $T_CTG != "feedback"}
   {assign var = 'tempTitle' value = $smarty.const._TESTS}
   {assign var = 'tempImage' value = 'tests'}
  {else}
   {assign var = 'tempTitle' value = $smarty.const._FEEDBACK}
   {assign var = 'tempImage' value = 'feedback'}
  {/if}
  <div class = "tabber">
      <div class = "tabbertab" title = "{$tempTitle}" id = "tests">
    {eF_template_printBlock title=$tempTitle data=$smarty.capture.t_all_tests_code image="32x32/`$tempImage`.png"}
   </div>
      <div title = "{$smarty.const._QUESTIONS}" class = "tabbertab {if $smarty.get.tab == 'question' || $smarty.get.tab == 'questions'} tabbertabdefault{/if}" id = "question" title = "{$smarty.const._QUESTIONS}">
    {eF_template_printBlock title=$smarty.const._QUESTIONS data=$smarty.capture.t_questions_code image='32x32/question_and_answer.png'}
      </div>
  </div>
 {/capture}
 {*Exclude from skillgap tests*}
 {if !$T_SKILLGAP_TEST}
  {if $T_CTG != "feedback"}
   {eF_template_printBlock title=$smarty.const._UNITANDSUBUNITSTESTS data=$smarty.capture.t_tests_and_questions_code image='32x32/tests.png' help = 'Tests'}
  {else}
   {eF_template_printBlock title=$smarty.const._FEEDBACK data=$smarty.capture.t_tests_and_questions_code image='32x32/feedback.png' help = 'Feedback'}
  {/if}
 {else}
  {eF_template_printBlock title=$smarty.const._SKILLGAPTESTS data=$smarty.capture.t_tests_and_questions_code image='32x32/skill_gap.png' help = 'Skill_gap_tests'}
 {/if}
{/if}
