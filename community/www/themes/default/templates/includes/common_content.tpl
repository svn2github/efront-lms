 {capture name = "t_scorm_form_code"}
   <script language="JavaScript" type="text/javascript" src="js/LMSFunctions{if $T_SCORM_VERSION == '1.3'}2004{/if}.php?view_unit={if $smarty.get.view_unit}{$smarty.get.view_unit}{elseif $smarty.get.target}{$smarty.get.target}{else}{$smarty.get.package_ID}{/if}"></script>
   <form id = "scorm_form" name = "scorm_form" method = "post" action = "{$smarty.server.PHP_SELF}?ctg=content&ajax=1&commit_lms=1&scorm_version={if $T_SCORM_VERSION == '1.3'}2004&package_ID={$smarty.get.package_ID}{else}1.2&view_unit={$smarty.get.view_unit}{/if}" style = "display:none">
    <input type = "hidden" name = "id" id = "id" />
    <input type = "hidden" name = "content_ID" id = "content_ID" />
    <input type = "hidden" name = "users_LOGIN" id = "users_LOGIN" />
    <input type = "hidden" name = "lesson_location" id = "lesson_location" />
    <input type = "hidden" name = "maxtimeallowed" id = "maxtimeallowed" />
    <input type = "hidden" name = "timelimitaction" id = "timelimitaction" />
    <input type = "hidden" name = "masteryscore" id = "masteryscore" />
    <input type = "hidden" name = "datafromlms" id = "datafromlms" />
    <input type = "hidden" name = "entry" id = "entry" />
    <input type = "hidden" name = "total_time" id = "total_time" />
    <input type = "hidden" name = "comments" id = "comments" />
    <input type = "hidden" name = "comments_from_lms" id = "comments_from_lms" />
    <input type = "hidden" name = "completion_status" id = "completion_status" />
    <input type = "hidden" name = "lesson_status" id = "lesson_status" />
    <input type = "hidden" name = "score" id = "score" />
    <input type = "hidden" name = "scorm_exit" id = "scorm_exit" />
    <input type = "hidden" name = "minscore" id = "minscore" />
    <input type = "hidden" name = "maxscore" id = "maxscore" />
    <input type = "hidden" name = "suspend_data" id = "suspend_data" />
    <input type = "hidden" name = "session_time" id = "session_time" />
    <input type = "hidden" name = "credit" id = "credit" />

    <input type = "hidden" name = "navigation" id = "navigation" />
    <input type = "hidden" name = "success_status" id = "success_status" />
    <input type = "hidden" name = "score_scaled" id = "score_scaled" />
    <input type = "hidden" name = "progress_measure" id = "progress_measure" />
    <input type = "hidden" name = "objectives" id = "objectives" />
    <input type = "hidden" name = "shared_data" id = "shared_data" />

    <input type = "hidden" name = "comments_from_lms" id = "comments_from_lms" />
    <input type = "hidden" name = "interactions" id = "interactions" />
    <input type = "hidden" name = "comments_from_learner" id = "comments_from_learner" />
    <input type = "hidden" name = "learner_preferences" id = "learner_preferences" />
    <input type = "hidden" name = "finish" id = "finish" />

   </form>
 {/capture}

 {assign var = "category" value = 'lessons'}
 {if $smarty.get.add || $smarty.get.edit}
  {*moduleInsertContent: Print the page that is used to add or update content*}
  {capture name = "moduleInsertContent"}
   <tr><td class = "moduleCell">
    {capture name = 't_insert_content_code'}
     {$T_ENTITY_FORM.javascript}
     <form {$T_ENTITY_FORM.attributes}>
      {$T_ENTITY_FORM.hidden}
      <table class = "formElements" width="100%">
       <tr><td class = "labelCell">{$T_ENTITY_FORM.name.label}:&nbsp;</td>
        <td class = "elementCell">{$T_ENTITY_FORM.name.html}</td>
       </tr>
       {if $T_ENTITY_FORM.name.error}<tr><td></td><td class = "formError">{$T_ENTITY_FORM.name.error}</td></tr>{/if}
       <tr><td class = "labelCell">{$T_ENTITY_FORM.parent_content_ID.label}:&nbsp;</td>
        <td class = "elementCell">{$T_ENTITY_FORM.parent_content_ID.html}</td></tr>
       {if $T_ENTITY_FORM.parent_content_ID.error}<tr><td></td><td class = "formError">{$T_ENTITY_FORM.parent_content_ID.error}</td></tr>{/if}
      {if !$T_SCORM}
       <tr><td class = "labelCell">{$T_ENTITY_FORM.ctg_type.label}:&nbsp;</td>
        <td class = "elementCell">{$T_ENTITY_FORM.ctg_type.html}</td></tr>
       {if $T_ENTITY_FORM.ctg_type.error}<tr><td></td><td class = "formError">{$T_ENTITY_FORM.ctg_type.error}</td></tr>{/if}
      {/if}
       <tr><td class = "labelCell">{$T_ENTITY_FORM.hide_navigation.label}:&nbsp;</td>
        <td class = "elementCell">{$T_ENTITY_FORM.hide_navigation.html}</td></tr>
     {if !$T_SCORM}
       <tr><td class = "labelCell">{$T_ENTITY_FORM.hide_complete_unit.label}:&nbsp;</td>
        <td class = "elementCell">{$T_ENTITY_FORM.hide_complete_unit.html}</td></tr>
       <tr><td class = "labelCell">{$T_ENTITY_FORM.auto_complete.label}:&nbsp;</td>
        <td class = "elementCell">{$T_ENTITY_FORM.auto_complete.html}</td></tr>
      {if $T_ENTITY_FORM.complete_time}
       <tr><td class = "labelCell">{$T_ENTITY_FORM.complete_time.label}:&nbsp;</td>
        <td class = "elementCell">{$T_ENTITY_FORM.complete_time.html}</td></tr>
      {/if}
      {if $T_ENTITY_FORM.complete_question}
       <tr><td class = "labelCell">{$T_ENTITY_FORM.complete_question.label}:&nbsp;</td>
        <td class = "elementCell">{$T_ENTITY_FORM.complete_question.html}&nbsp;{$T_ENTITY_FORM.questions.html}</td></tr>
      {/if}
        <tr><td class = "labelCell">{$T_ENTITY_FORM.pdf_check.label}:&nbsp;</td>
         <td class = "elementCell">{$T_ENTITY_FORM.pdf_check.html}</td></tr>
     {/if}
       <tr style="display:none;" id="pdf_content"><td class = "labelCell">{$T_ENTITY_FORM.pdf_content.label}:&nbsp;</td>
        <td class = "elementCell">{$T_ENTITY_FORM.pdf_content.html}</td></tr>
       <tr style="display:none;" id="pdf_upload"><td class = "labelCell">{$T_ENTITY_FORM.pdf_upload.label}:&nbsp;</td>
        <td class = "elementCell">{$T_ENTITY_FORM.pdf_upload.html}</td></tr>
                            <tr style="display:none;" id="pdf_upload_max_size"><td></td><td class = "infoCell">{$smarty.const._FILESIZEMUSTBESMALLERTHAN} <b>{$T_MAX_FILE_SIZE}</b> {$smarty.const._KB}</td></tr>

       <tr><td></td><td class = "elementCell">
        <span>
         <img class = "handle" id = "advenced_parameter_image" src = "images/16x16/navigate_down.png" alt = "{$smarty.const._TOGGLEADVENCEDPARAMETERS}" title = "{$smarty.const._TOGGLEADVENCEDPARAMETERS}"/>&nbsp;
         <a href = "javascript:void(0)" onclick = "toggleAdvancedParameters();">{$smarty.const._TOGGLEADVENCEDPARAMETERS}</a>
        </span>
       </td></tr>

       <tr style="display:none;" id = "maximize_viewport"><td class = "labelCell">{$T_ENTITY_FORM.maximize_viewport.label}:&nbsp;</td>
        <td class = "elementCell">{$T_ENTITY_FORM.maximize_viewport.html}</td></tr>
       <tr style="display:none;" id = "object_ids"><td class = "labelCell">{$T_ENTITY_FORM.object_ids.label}:&nbsp;</td>
        <td class = "elementCell">{$T_ENTITY_FORM.object_ids.html}&nbsp;<span class = "infoCell">{$smarty.const._COMMASEPARATEDLIST}</span></td></tr>
       <tr style="display:none;" id = "no_before_unload"><td class = "labelCell">{$T_ENTITY_FORM.no_before_unload.label}:&nbsp;</td>
        <td class = "elementCell">{$T_ENTITY_FORM.no_before_unload.html}</td></tr>
       <tr style="display:none;" id = "indexed"><td class = "labelCell">{$T_ENTITY_FORM.indexed.label}:&nbsp;</td>
        <td class = "elementCell">{$T_ENTITY_FORM.indexed.html}&nbsp;</td></tr>
       <tr style="display:none;" id = "accessible_explanation"><td></td><td class = "infoCell">{$smarty.const._DIRECTLACCESSIBLEEXPLANATION}<br/>{$smarty.const.G_SERVERNAME}view_resource.php?type=content&id={if $smarty.get.edit}{$smarty.get.edit}{else}&lt;unit_id&gt;{/if}</td></tr>

      {if $T_SCORM}
       <tr style="display:none;" id = "scorm_asynchronous"><td class = "labelCell">{$T_ENTITY_FORM.scorm_asynchronous.label}:&nbsp;</td>
        <td class = "elementCell">{$T_ENTITY_FORM.scorm_asynchronous.html}</td></tr>
       <tr style="display:none;" id = "scorm_asynchronous_explanation"><td></td><td class = "infoCell">{$smarty.const._SCORMASYNCHRONOUSEXPLANATION}</td></tr>
       <tr><td class = "labelCell">{$T_ENTITY_FORM.scorm_size.label}:&nbsp;</td>
        <td class = "elementCell">{$T_ENTITY_FORM.scorm_size.html} px</td></tr>
       {if $T_ENTITY_FORM.scorm_size.error}<tr><td></td><td class = "formError">{$T_ENTITY_FORM.scorm_size.error}</td></tr>{/if}
       <tr><td></td><td class = "infoCell">{$smarty.const._EXPLICITSIZEEXPLANATION}</td></tr>
       <tr><td class = "labelCell">{$T_ENTITY_FORM.reentry_action.label}:&nbsp;</td>
        <td class = "elementCell">{$T_ENTITY_FORM.reentry_action.html}</td></tr>
       {if $T_ENTITY_FORM.reentry_action.error}<tr><td></td><td class = "formError">{$T_ENTITY_FORM.reentry_action.error}</td></tr>{/if}
       {if $T_ENTITY_FORM.embed_type}
        <tr><td class = "labelCell">{$T_ENTITY_FORM.embed_type.label}:
         <td class = "elementCell">{$T_ENTITY_FORM.embed_type.html}</td></tr>
        <tr><td class = "labelCell">{$T_ENTITY_FORM.popup_parameters.label}:
         <td class = "elementCell">{$T_ENTITY_FORM.popup_parameters.html}</td></tr>
       {/if}
      {else}
       <tr id = "toggleTools"><td colspan = "2" id = "toggleeditor_cell1">
        <div class = "headerTools">
         <span>
          <img onclick = "toggleFileManager(Element.extend(this).next());" class = "ajaxHandle" id = "arrow_down" src = "images/16x16/navigate_down.png" alt = "{$smarty.const._OPENCLOSEFILEMANAGER}" title = "{$smarty.const._OPENCLOSEFILEMANAGER}"/>&nbsp;
          <a href = "javascript:void(0)" onclick = "toggleFileManager(this);">{$smarty.const._TOGGLEFILEMANAGER}</a>
         </span>
         <span>
          <img src = "images/16x16/order.png" onclick = "toggledInstanceEditor = 'editor_content_data'; javascript:toggleEditor('editor_content_data','mceEditor');" class = "ajaxHandle" title = "{$smarty.const._TOGGLEHTMLEDITORMODE}" alt = "{$smarty.const._TOGGLEHTMLEDITORMODE}" />&nbsp;
          <a href = "javascript:void(0)" onclick = "toggledInstanceEditor = 'editor_content_data';javascript:toggleEditor('editor_content_data','mceEditor');" id = "toggleeditor_link">{$smarty.const._TOGGLEHTMLEDITORMODE}</a>
         </span>
        </div>
        </td></tr>
       <tr><td colspan = "2" id = "filemanager_cell"></td></tr>
       <tr id = "nonPdfTable" width="100%"><td width="100%" colspan = "2" class = "elementCell">{$T_ENTITY_FORM.data.html}</td></tr>
       {if $T_ENTITY_FORM.data.error}<tr><td colspan = "2" class = "formError">{$T_ENTITY_FORM.data.error}</td></tr>{/if}
      {/if}
       <tr><td colspan = "2" class = "submitCell">{$T_ENTITY_FORM.submit_insert_content.html}</td></tr>
      </table>
     </form>
     <script>var editPdfContent = {if $T_EDITPDFCONTENT}true{else}false{/if};</script>
     <div id = "fmInitial"><div id = "filemanager_div" style = "display:none;">{$T_FILE_MANAGER}</div></div>
    {/capture}
    {eF_template_printBlock title=$smarty.const._UNITPROPERTIES data=$smarty.capture.t_insert_content_code image='32x32/edit.png' help ='Content'}
   </td></tr>
  {/capture}
 {elseif $smarty.get.apply_all}
  {capture name = 't_all_units_properties_code'}
   {eF_template_printForm form = $T_ALL_UNITS_PROPERTIES_FORM}
  {/capture}
  {capture name = "moduleInsertContent"}
   {eF_template_printBlock title=$smarty.const._ALLUNITSPROPERTIES data=$smarty.capture.t_all_units_properties_code image='32x32/edit.png'}
  {/capture}
 {elseif !$T_UNIT && $_student_}
  {if $smarty.get.type == 'theory'}
   {assign var = "specific_title" value = $smarty.const._THEORY}
   {assign var = "image" value = "32x32/theory.png"}
  {elseif $smarty.get.type == 'examples'}
   {assign var = "specific_title" value = $smarty.const._EXAMPLES}
   {assign var = "image" value = "32x32/examples.png"}
  {elseif $smarty.get.type == 'tests'}
   {assign var = "specific_title" value = $smarty.const._TESTS}
   {assign var = "image" value = "32x32/tests.png"}
  {else}
   {assign var = "specific_title" value = $smarty.const._CONTENT}
   {assign var = "image" value = "32x32/content.png"}
  {/if}
  {*moduleSpecificContent: Show content based on its type*}
  {capture name = "moduleSpecificContent"}
   <tr><td class = "moduleCell">
    {capture name = 't_theory_tree_code'}
     {$T_THEORY_TREE}
    {/capture}
    {eF_template_printBlock title=$specific_title data=$smarty.capture.t_theory_tree_code image=$image}
   </td></tr>
  {/capture}
 {elseif $smarty.get.bare}
  <span style = "display:none" id = "user_total_time_in_unit">{$T_USER_TIME_IN_UNIT.total_seconds}</span>
  <span style = "display:none" id = "user_current_time_in_unit">{$T_USER_CURRENT_TIME_IN_UNIT}</span>
  <span style = "display:none" id = "required_time_in_unit">{$T_REQUIRED_TIME_IN_UNIT}</span>
  <span style = "display:none" id = "user_time_in_lesson">{$T_USER_CURRENT_TIME_IN_LESSON}</span>
  <span style = "display:none" id = "required_time_in_lesson">{$T_REQUIRED_TIME_IN_LESSON}</span>
  {if $T_SCORM}
   {$smarty.capture.t_scorm_form_code}
  {/if}
  {$T_UNIT.data}
 {else}
  <span style = "display:none" id = "user_total_time_in_unit">{$T_USER_TIME_IN_UNIT.total_seconds}</span>
  <span style = "display:none" id = "user_current_time_in_unit">{$T_USER_CURRENT_TIME_IN_UNIT}</span>
  <span style = "display:none" id = "required_time_in_unit">{$T_REQUIRED_TIME_IN_UNIT}</span>
  <span style = "display:none" id = "user_time_in_lesson">{$T_USER_CURRENT_TIME_IN_LESSON}</span>
  <span style = "display:none" id = "required_time_in_lesson">{$T_REQUIRED_TIME_IN_LESSON}</span>
  {capture name = 't_content_footer_code'}
       <table class = "navigationTable">
        <tr>
         <td class = "previousUnitHandleIcon">
    {if ($T_UNIT.data || $T_UNIT.ctg_type == 'tests' || $T_UNIT.ctg_type == 'feedback') && !$T_TEST_UNDERGOING && $T_UNIT.options.hide_navigation != 1 && $T_UNIT.options.hide_navigation != 3}
     {if $T_PREVIOUS_UNIT}
      {assign var = "show_content_footer" value = 1}
          <a href = "{$smarty.server.PHP_SELF}?view_unit={$T_PREVIOUS_UNIT.id}" title = "{$T_PREVIOUS_UNIT.name}">
           <img class = "handle" src = "images/32x32/navigate_left.png" title = "{$T_PREVIOUS_UNIT.name}" alt = "{$T_PREVIOUS_UNIT.name}" />
          </a>
     {/if}
    {/if}
         </td>
         <td class = "previousUnitHandle">
    {if ($T_UNIT.data || $T_UNIT.ctg_type == 'tests' || $T_UNIT.ctg_type == 'feedback') && !$T_TEST_UNDERGOING && $T_UNIT.options.hide_navigation != 1 && $T_UNIT.options.hide_navigation != 3}
     {if $T_PREVIOUS_UNIT}
      {assign var = "show_content_footer" value = 1}
          <a href = "{$smarty.server.PHP_SELF}?view_unit={$T_PREVIOUS_UNIT.id}" title = "{$T_PREVIOUS_UNIT.name}">
           {$T_PREVIOUS_UNIT.name|eF_truncate:30}
          </a>
     {/if}
    {/if}
         </td>
         <td class = "completeUnitHandle">
     {if !$T_UNIT.options.hide_complete_unit && $T_UNIT.ctg_type != 'tests' && $T_UNIT.ctg_type != 'feedback'}{assign var = "hideStyle" value = ''}{assign var = "show_content_footer" value = 1}{else}{assign var = "hideStyle" value = 'style = "visibility:hidden"'}{/if}
     {if $T_QUESTION}
      {assign var = "show_content_footer" value = 1}
          <div class = "unitQuestionArea" {$hideStyle}>
           <form id = "question_form" method = "post" action = "{$smarty.server.PHP_SELF}?view_unit={$smarty.get.view_unit}">{$T_QUESTION}</form>
           <span id = "contentQuestionAnswer">
            <input class = "flatButton" type = "button" value = "{$smarty.const._SUBMIT}" onclick = "answerQuestion(this)">
            <img class = "ajaxHandle" style = "display:none" id = "correct_answer" src = "images/32x32/success.png" alt = "{$smarty.const._CORRECTANSWER}" title = "{$smarty.const._CORRECTANSWER}">
            <img class = "ajaxHandle" style = "display:none" id = "wrong_answer" src = "images/32x32/error_delete.png" alt = "{$smarty.const._WRONGANSWER}" title = "{$smarty.const._WRONGANSWER}">
           </span>
          </div>
     {elseif $_change_ && $_student_}
          <a {if !$hideStyle}id = "seenLink"{/if} href = "javascript:void(0)" onclick = "setSeenUnit();" {$hideStyle}>
            {if $T_SEEN_UNIT}
             <img class = "handle" src = "images/32x32/unit_completed.png" title = "{$smarty.const._NOTSAWUNIT}" alt = "{$smarty.const._NOTSAWUNIT}" />
             <div>{$smarty.const._NOTSAWUNIT}</div>
            {else}
             <img class = "handle" src = "images/32x32/unit.png" title = "{$smarty.const._SAWUNIT}" alt = "{$smarty.const._SAWUNIT}" />
             <div>{$smarty.const._SAWUNIT}</div>
            {/if}
          </a>
     {/if}
         </td>
         <td class = "nextUnitHandle">
    {if ($T_UNIT.data || $T_UNIT.ctg_type == 'tests' || $T_UNIT.ctg_type == 'feedback') && !$T_TEST_UNDERGOING && $T_UNIT.options.hide_navigation != 1 && $T_UNIT.options.hide_navigation != 3}
     {if $T_NEXT_UNIT}
      {assign var = "show_content_footer" value = 1}
          <a href = "{$smarty.server.PHP_SELF}?view_unit={$T_NEXT_UNIT.id}" title = "{$T_NEXT_UNIT.name}">
           {$T_NEXT_UNIT.name|eF_truncate:30}
          </a>
     {/if}
    {/if}
         </td>
         <td class = "nextUnitHandleIcon">
    {if ($T_UNIT.data || $T_UNIT.ctg_type == 'tests' || $T_UNIT.ctg_type == 'feedback') && !$T_TEST_UNDERGOING && $T_UNIT.options.hide_navigation != 1 && $T_UNIT.options.hide_navigation != 3}
     {if $T_NEXT_UNIT}
      {assign var = "show_content_footer" value = 1}
          <a href = "{$smarty.server.PHP_SELF}?view_unit={$T_NEXT_UNIT.id}" title = "{$T_NEXT_UNIT.name}">
           <img class = "handle" src = "images/32x32/navigate_right.png" title = "{$T_NEXT_UNIT.name}" alt = "{$T_NEXT_UNIT.name}" />
          </a>
     {/if}
    {/if}
         </td>
       </table>
  {/capture}

  {capture name = 't_content_code'}
    {*Variables needed in js functions*}
    <script>var sawunit = '{$smarty.const._SAWUNIT}';var notsawunit = '{$smarty.const._NOTSAWUNIT}';var unitId = '{$T_UNIT.id}';
      var unitType = '{$T_UNIT.ctg_type}';var hasSeen = '{$T_SEEN_UNIT}';var nextId = '{$T_NEXT_UNIT.id}';var previousId = '{$T_PREVIOUS_UNIT.id}';</script>
    {if $T_UNIT.options.object_ids}
      <script>var matchscreenobjectid= '{$T_UNIT.options.object_ids}';</script>
    {/if}

    {if $_change_ && !$_student_}
     <div class = "headerTools">
      <span>
       <img src = "images/16x16/add.png" title = "{$smarty.const._CREATEUNIT}" alt = "{$smarty.const._CREATEUNIT}"/>
       <a href = "{$smarty.server.PHP_SELF}?ctg=content&add=1" title = "{$smarty.const._CREATEUNIT}">{$smarty.const._CREATEUNIT}</a>
      </span>
     {if $T_UNIT}
      {if !$T_SCORM}
      <span>
       <img src = "images/16x16/add.png" title = "{$smarty.const._CREATESUBUNIT}" alt = "{$smarty.const._CREATESUBUNIT}"/>
       <a href = "{$smarty.server.PHP_SELF}?ctg=content&add=1&view_unit={if $smarty.get.view_unit !=""}{$smarty.get.view_unit}{else}{$T_CURRENTUNITID}{/if}" title = "{$smarty.const._CREATESUBUNIT}">{$smarty.const._CREATESUBUNIT}</a>
      </span>
      {/if}
      {if !$T_SCORM_2004_TITLE}
      <span>
       <img src = "images/16x16/edit.png" title = "{$smarty.const._UPDATEUNIT}" alt = "{$smarty.const._UPDATEUNIT}"/>
       <a href = "{$smarty.server.PHP_SELF}?ctg={if $T_UNIT.ctg_type == 'tests'}tests{elseif $T_UNIT.ctg_type == 'feedback'}feedback{else}content{/if}&edit={if $smarty.get.view_unit !=""}{$smarty.get.view_unit}{else}{$T_CURRENTUNITID}{/if}" title = "{$smarty.const._UPDATEUNIT}">{$smarty.const._UPDATEUNIT}</a>
      </span>
      {/if}
     {/if}
     </div>
    {/if}
     <table id = "unitContent">
   {if !$_student_ || !$T_RULE_CHECK_FAILED}
      <tr><td class = "unitContent">
        {if $T_UNIT.ctg_type == 'tests' || $T_UNIT.ctg_type == 'feedback'}
         {include file = "includes/tests/show_unsolved_test.tpl"}
        {else}
         {if $T_UNIT.data}
          {$T_UNIT.data}
         {elseif $T_NO_START}
          {$smarty.const._CHOOSEUNIT}: {$T_SUBTREE}
         {else}
          <div class = "emptyCategory">{$smarty.const._NODATAFOUND}</div>
         {/if}
        {/if}
       </td></tr>
      {if $show_content_footer}
      <tr><td>
       {$smarty.capture.t_content_footer_code}
      </td></tr>
      {/if}
   {/if}
   {if $T_CONFIGURATION.disable_comments != 1 && $T_CURRENT_LESSON->options.comments && $T_COMMENTS}
    {section name = 'comments_list' loop = $T_COMMENTS}
      <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
       <td>
        <div style = "float:right">
        {if $T_COMMENTS[comments_list].users_LOGIN == $T_CURRENT_USER->user.login}
         <a href = "{$smarty.server.PHP_SELF}?ctg=comments&edit={$T_COMMENTS[comments_list].id}&popup=1", onclick = "eF_js_showDivPopup('{$smarty.const._EDITCOMMENT}', 1)" target = "POPUP_FRAME"><img class = "handle" src = "images/16x16/edit.png" alt = "{$smarty.const._CORRECTION}" title = "{$smarty.const._CORRECTION}"/></a>&nbsp;
        {/if}
        {if $T_COMMENTS[comments_list].users_LOGIN == $T_CURRENT_USER->user.login || $_professor_}
         <img class = "ajaxHandle" src = "images/16x16/error_delete.png" alt = "{$smarty.const._DELETE}" title = "{$smarty.const._DELETE}" onclick = "if (confirm ('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) deleteComment(this, '{$T_COMMENTS[comments_list].id}')"/>
        {/if}
        </div>
        #filter:login-{$T_COMMENTS[comments_list].users_LOGIN}#: {$T_COMMENTS[comments_list].data}
      </td></tr>
    {/section}
       {*<tr><td colspan = "2"><input type = "text" name = "comment" id = "commentBox" value = "{$smarty.const._ADDCOMMENT}" onclick = "this.value = '';this.style.color='inherit'">&nbsp;<img src = "images/16x16/success.png" class = "ajaxHandle" onclick = "postComment()"></td></tr>*}
   {/if}
     </table>
  {/capture}

  {capture name = 't_progress_bar'}
   {if $T_CURRENT_LESSON->options.tracking && $_change_ && $_student_}
    <div id = "progress_bar">
    {$smarty.const._PROGRESS}:&nbsp;
     <span class = "progressNumber">{$T_USER_PROGRESS.overall_progress}%</span>
     <span class = "progressBar" style = "width:{$T_USER_PROGRESS.overall_progress}px;">&nbsp;</span>&nbsp;
    {if $T_USER_PROGRESS.total_conditions > 0 && $T_CURRENT_LESSON->options.lesson_info}
     <a id = "lesson_passed" href = "{$smarty.server.PHP_SELF}?ctg=lesson_information&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._LESSONINFORMATION}', 2)" target = "POPUP_FRAME">
      <span class = "{if $T_USER_PROGRESS.lesson_passed}success{else}failure{/if}">{$smarty.const._CONDITIONSCOMPLETED}: <span id = "passed_conditions">{$T_USER_PROGRESS.conditions_passed}</span> {$smarty.const._OUTOF} {$T_USER_PROGRESS.total_conditions}</span>
     </a>
    {/if}
    </div>
    {if $T_SCORM && $T_NOCREDIT}
     <div><script>var nocredit = false</script>{$smarty.const._YOUAREREVISITINGCHANGESNOTTAKENINTOACCOUNT}</div>
    {/if}

    <div {if !$T_CURRENT_LESSON->options.timers}style = "display:none"{/if}>{$smarty.const._TOTALTIMESPENTONTHISUNIT}:&nbsp;<span id = "user_time_in_unit_display">{$T_USER_TIME_IN_UNIT.hours}:{$T_USER_TIME_IN_UNIT.minutes}:{$T_USER_TIME_IN_UNIT.seconds}</span></div>
    <div {if !$T_CURRENT_LESSON->options.timers}style = "display:none"{/if}>{$smarty.const._TOTALTIMESPENTONTHISLESSON}:&nbsp;<span id = "user_time_in_lesson_display">{$T_USER_TIME_IN_LESSON.hours}:{$T_USER_TIME_IN_LESSON.minutes}:{$T_USER_TIME_IN_LESSON.seconds}</span></div>
    <script>
    var seconds = {$T_USER_TIME_IN_UNIT.seconds};
    var minutes = {$T_USER_TIME_IN_UNIT.minutes};
    var hours = {$T_USER_TIME_IN_UNIT.hours};
    var lesson_seconds = {$T_USER_TIME_IN_LESSON.seconds};
    var lesson_minutes = {$T_USER_TIME_IN_LESSON.minutes};
    var lesson_hours = {$T_USER_TIME_IN_LESSON.hours};
    var start_timer = true;
    </script>
   {/if}
  {/capture}

  {capture name = 't_unit_operations'}
   {if $T_CURRENT_LESSON->options.print_content && !$T_SCORM}
    <div>{counter name = "unit_operations"}. <a href = "{$smarty.server.PHP_SELF}?ctg=content&view_unit={$T_UNIT.id}&popup=1&print=1", onclick = "eF_js_showDivPopup('{$smarty.const._PRINTERFRIENDLY}', 2)" target = "POPUP_FRAME">{$smarty.const._PRINTERFRIENDLY}</a></div>
   {/if}
   {if $T_CONFIGURATION.disable_comments != 1 && $T_CURRENT_LESSON->options.comments && $_change_ && !$T_RULE_CHECK_FAILED}
    <div>{counter name = "unit_operations"}. <a href = "{$smarty.server.PHP_SELF}?ctg=comments&view_unit={$T_UNIT.id}&add=1&popup=1", onclick = "eF_js_showDivPopup('{$smarty.const._ADDCOMMENT}', 1)" target = "POPUP_FRAME">{$smarty.const._ADDCOMMENT}</a></div>
   {/if}
   {if $_student_ && $T_CURRENT_LESSON->options.content_report}
    <div>{counter name = "unit_operations"}. <a href = "content_report.php?{$smarty.server.QUERY_STRING}" onclick = "eF_js_showDivPopup('{$smarty.const._CONTENTREPORT}', 1)" target = "POPUP_FRAME">{$smarty.const._CONTENTREPORTTOPROFS}</a></div>
   {/if}
   {if $T_LESSON_FORUM}
    <div>{counter name = "unit_operations"}. <a href = "{$smarty.server.PHP_SELF}?ctg=forum&add=1&type=topic&forum_id={$T_LESSON_FORUM}&subject={$T_UNIT.name}", onclick = "eF_js_showDivPopup('{$smarty.const._ADDFORUMPOSTONTHISUNIT}', 2)" target = "POPUP_FRAME" title="{$smarty.const._ADDFORUMPOSTONTHISUNIT}">{$smarty.const._ADDFORUMPOSTONTHISUNIT|eF_truncate:25:"...":true}</a></div>
   {/if}
   {if !$T_SCORM}
   <div>{counter name = "unit_operations"}. <a href = "javascript:void(0)", onclick = "window.open('{$smarty.server.PHP_SELF}?ctg=content&view_unit={$T_UNIT.id}&popup=1','unit_popup','width=900,height=600,scrollbars=yes,resizable=yes,status=yes,toolbar=no,location=no,menubar=no')">{$smarty.const._OPENUNITINPOPUP}</a></div>
   {/if}
  {/capture}

  {capture name = "t_content_tools"}
   <div>{counter name = "content_tools"}. <a title = "{$smarty.const._UPLOADFILESANDIMAGES}" href = "{$smarty.server.PHP_SELF}?ctg=file_manager&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._UPLOADFILESANDIMAGES}', 3)" target = "POPUP_FRAME">{$smarty.const._UPLOADFILESANDIMAGES|eF_truncate:40}</a></div>
   <div>{counter name = "content_tools"}. <a title = "{$smarty.const._COPYFROMANOTHERLESSON}" href = "{$smarty.server.PHP_SELF}?ctg=copy">{$smarty.const._COPYFROMANOTHERLESSON|eF_truncate:40}</a></div>
   <div>{counter name = "content_tools"}. <a title = "{$smarty.const._CONTENTTREEMANAGEMENT}" href = "{$smarty.server.PHP_SELF}?ctg=order">{$smarty.const._CONTENTTREEMANAGEMENT|eF_truncate:40}</a></div>
   <div>{counter name = "content_tools"}. <a title = "{$smarty.const._SCORMIMPORT}" href = "{$smarty.server.PHP_SELF}?ctg=scorm&scorm_import=1">{$smarty.const._SCORMIMPORT}</a></div>
   {if $T_UNIT}
   <div>{counter name = "content_tools"}. <a title = "{$smarty.const._CONTENTMETADATA}" href = "{$smarty.server.PHP_SELF}?ctg=metadata&unit={$T_UNIT.id}">{$smarty.const._CONTENTMETADATA|eF_truncate:40}</a></div>
   {/if}
   <div>{counter name = "content_tools"}. <a title = "{$smarty.const._APPLYFUNCTIONTOALLUNITS}" href = "{$smarty.server.PHP_SELF}?ctg=content&apply_all=1">{$smarty.const._APPLYFUNCTIONTOALLUNITS|eF_truncate:40}</a></div>
  {/capture}

  {capture name = "t_end_of_lesson_code"}
   <div>
    <p class = "smallHeader">{$smarty.const._FINISHLESSONMESSAGE}</p>
    <p class = "smallHeader">
     <input type = "button" class = "flatButton" value = "{$smarty.const._NEXTLESSON}" onclick = "nextLesson(this)">
             {if !isset($T_CURRENT_LESSON->options.show_dashboard) || $T_CURRENT_LESSON->options.show_dashboard}
     <input type = "button" class = "flatButton" value = "{$smarty.const._CONTROLPANEL}" onclick = "location='{$smarty.server.PHP_SELF}?ctg=control_panel'">
    {/if}
     <input type = "button" class = "flatButton" value = "{$smarty.const._MYCOURSES}" onclick = "location='{$smarty.server.PHP_SELF}?ctg=lessons'">
    </p>
    <p class = "smallHeader"><a href = "javascript:void(0)" onclick = "setCookie('hide_complete_lesson_{$T_CURRENT_LESSON->lesson.id}', true);new Effect.Fade($('completed_block').down());" class = "infoCell">{$smarty.const._HIDE}</a></p>
   </div>
  {/capture}

  {*moduleShowUnit: A specific content page*}
  {capture name = "moduleShowUnit"}
   <tr><td class = "moduleCell" style = "height:100%">
   {if $T_AUTO_SET_SEEN_UNIT && $T_UNIT.ctg_type !='scorm' && $T_UNIT.ctg_type != 'scorm_test'}<script>autoSetSeenUnit = 1;</script>{/if}
   {if $T_UNIT.options.previous}{assign var = "T_PREVIOUS_UNIT" value = ""}{/if}
   {if $T_UNIT.options.continue}{assign var = "T_NEXT_UNIT" value = ""}{/if}
   {if $T_UNIT.name}{assign var = "unit_name" value = $T_UNIT.name}{else}{assign var = "unit_name" value = $smarty.const._NOCONTENT}{/if}
   {if $T_UNIT.ctg_type == 'tests'}{assign var = "image" value = "32x32/tests.png"}{elseif $T_UNIT.ctg_type == 'feedback'}{assign var = "image" value = "32x32/feedback.png"}{elseif $T_UNIT.ctg_type == 'examples'}{assign var = "image" value = "32x32/examples.png"}{else }{assign var = "image" value = "32x32/theory.png"}{/if}
   {if !$T_TEST_UNDERGOING}{assign var = "unit_options" value = $T_UNIT_OPTIONS}{else}{assign var = "unit_options" value = ""}{/if}
   <script>
    var nextUnit = '{$T_NEXT_UNIT.id}';var previousUnit = '{$T_PREVIOUS_UNIT.id}';{if $T_UNIT.options.no_before_unload}var noBeforeUnload = true;{else}var noBeforeUnload = false;{/if}
    translations['_YOUAREATTHELASTLESSONYOUMAYVISIT'] = '{$smarty.const._YOUAREATTHELASTLESSONYOUMAYVISIT}';
    </script>{*The first 2 are needed for nextUnit()/previousUnit() js functions and the last for scorm function*}
   <table class = "contentArea">
    <tr>
     <td id = "centerColumn">
        {if $smarty.get.print}
         {if !$T_DISABLEPRINTUNIT}
         <p style = "text-align:center"><input class = "flatButton" type = "submit" onClick = "window.print()" value = "{$smarty.const._PRINTIT}"/></p>
      {/if}
      {if $T_UNIT.ctg_type == 'tests' || $T_UNIT.ctg_type == 'feedback'}
       {include file = "includes/tests/show_unsolved_test.tpl"}
      {else}
      {eF_template_printBlock title = $T_UNIT.name data = $T_UNIT.data image = '32x32/printer.png'}
      {/if}
        {else}
         <span id = "completed_block" {if !$T_USER_PROGRESS.lesson_passed && !$T_USER_PROGRESS.completed}style = "display:none"{/if}>
      {assign var = "cookie_value" value = "hide_complete_lesson_`$T_CURRENT_LESSON->lesson.id`"}
      {if !$smarty.cookies.$cookie_value}
          {eF_template_printBlock title = $smarty.const._LESSONFINISHED data = $smarty.capture.t_end_of_lesson_code image = '32x32/information.png'}
         {/if}
         </span>
      {eF_template_printBlock title = $unit_name data=$smarty.capture.t_content_code image=$image options = $unit_options settings = $T_UNIT_SETTINGS}
        {/if}
     </td>
     </tr>
   </table>
  {/capture}
  {capture name = "moduleSideOperations"}
    <tr>
     <td id = "sideColumn">
     {if !$_student_}
      {eF_template_printBlock title = $smarty.const._CONTENTTOOLS data = $smarty.capture.t_content_tools image = "32x32/tools.png"}
     {/if}
      {eF_template_printBlock title = $smarty.const._LESSONMATERIAL data = $T_CONTENT_TREE image = "32x32/theory.png"}
     {if $_student_ && (!isset($T_CURRENT_LESSON->options.show_percentage) || $T_CURRENT_LESSON->options.show_percentage)}
      {eF_template_printBlock title = $smarty.const._LESSONPROGRESS data = $smarty.capture.t_progress_bar image = "32x32/status.png"}
     {/if}
     {if !isset($T_CURRENT_LESSON->options.show_content_tools) || $T_CURRENT_LESSON->options.show_content_tools}
      {eF_template_printBlock title = $smarty.const._UNITOPERATIONS data = $smarty.capture.t_unit_operations image = "32x32/options.png"}
      {*Content side table modules *}
      {foreach name = 'module_content_side_list' key = key item = moduleItem from = $T_CONTENT_SIDE_MODULES}
       {capture name = $key|replace:"_":""} {*We cut off the underscore, since scriptaculous does not seem to like them*}
        {assign var = module_name value = $key|replace:"_":""}
        {if $moduleItem.smarty_file}{include file = $moduleItem.smarty_file}{else}{$moduleItem.html_code}{/if}
       {/capture}
       {eF_template_printBlock title = $moduleItem.title data = $smarty.capture.$module_name id = $module_name|cat:'_id'}
      {/foreach}
     {/if}
     </td>
    </tr>
  {/capture}

  <script>
   var show_left_bar = {if $_student_}'{$T_CURRENT_LESSON->options.show_left_bar}'{else}1{/if};
  </script>

  {if $T_SCORM}
   {$smarty.capture.t_scorm_form_code}
   {if $T_SCORM_ASYNCHRONOUS}<script>var scorm_asynchronous = true;</script>{else}<script>var scorm_asynchronous = false;</script>{/if}
  {/if}

 {/if}
