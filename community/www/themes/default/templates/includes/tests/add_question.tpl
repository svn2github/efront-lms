 <script>var correlated_message = '{$smarty.const._ALLPROPOSEDSKILLSAREALREADYCORRELATED}';var removechoice = '{$smarty.const._REMOVECHOICE}'; var insertexplanation = '{$smarty.const._INSERTEXPLANATION}';var noSkillsFoundOrNoSkillsCorrelated = "{$smarty.const._NOCORRELATEDSKILLSHAVEBEENFOUND}";</script>

 {capture name='t_questions_info'}
    {$T_QUESTION_FORM.javascript}
    <form {$T_QUESTION_FORM.attributes}>
     {$T_QUESTION_FORM.hidden}
        <table class = "formElements" style = "width:100%">
        {if $T_QUESTION_FORM.content_ID}
            <tr><td class = "labelCell">{$T_QUESTION_FORM.content_ID.label}:&nbsp;</td>
             <td class = "elementCell">{$T_QUESTION_FORM.content_ID.html}</td></tr>
   {if $T_QUESTION_FORM.content_ID.error}<tr><td></td><td class = "formError">{$T_QUESTION_FORM.content_ID.error}</td></tr>{/if}
        {/if}
         <tr><td class = "labelCell">{$T_QUESTION_FORM.question_type.label}:&nbsp;</td>
    <td class = "elementCell">{$T_QUESTION_FORM.question_type.html}</td></tr>
  {if $T_QUESTION_FORM.question_type.error}<tr><td></td><td class = "formError">{$T_QUESTION_FORM.question_type.error}</td></tr>{/if}
         <tr><td class = "labelCell">{$T_QUESTION_FORM.difficulty.label}:&nbsp;</td>
          <td class = "elementCell">{$T_QUESTION_FORM.difficulty.html}</td></tr>
        {if $T_QUESTION_FORM.difficulty.error}<tr><td></td><td class = "formError">{$T_QUESTION_FORM.difficulty.error}</td></tr>{/if}
         <tr><td class = "labelCell">{$T_QUESTION_FORM.estimate_min.label}:&nbsp;</td>
          <td class = "elementCell">{$T_QUESTION_FORM.estimate_min.html}{$smarty.const._MINUTESSHORTHAND} : {$T_QUESTION_FORM.estimate_sec.html}{$smarty.const._SECONDSSHORTHAND}</td></tr>
        {if $T_QUESTION_FORM.estimate_min.error}<tr><td></td><td class = "formError">{$T_QUESTION_FORM.estimate_min.error}</td></tr>{/if}
        {if $T_QUESTION_FORM.estimate_sec.error}<tr><td></td><td class = "formError">{$T_QUESTION_FORM.estimate_sec.error}</td></tr>{/if}
   <tr><td></td><td id = "toggleeditor_cell1">
    <div class = "headerTools">
     <span>
      <img onclick = "toggleFileManager(Element.extend(this).next());" class = "handle" id = "arrow_down" src = "images/16x16/navigate_down.png" alt = "{$smarty.const._OPENCLOSEFILEMANAGER}" title = "{$smarty.const._OPENCLOSEFILEMANAGER}"/>&nbsp;
      <a href = "javascript:void(0)" onclick = "toggleFileManager(this);">{$smarty.const._TOGGLEFILEMANAGER}</a>
     </span>
     <span>
      <img onclick = "toggledInstanceEditor = 'editor_content_data';javascript:toggleEditor('editor_content_data','mceEditor');" class = "handle" src = "images/16x16/order.png" title = "{$smarty.const._TOGGLEHTMLEDITORMODE}" alt = "{$smarty.const._TOGGLEHTMLEDITORMODE}" />&nbsp;
      <a href = "javascript:void(0)" onclick = "toggledInstanceEditor = 'editor_content_data';javascript:toggleEditor('editor_content_data','mceEditor');" id = "toggleeditor_link">{$smarty.const._TOGGLEHTMLEDITORMODE}</a>
     </span>
    </div>
    </td></tr>
   <tr><td></td><td id = "filemanager_cell"></td></tr>
         <tr><td class = "labelCell">{$T_QUESTION_FORM.question_text.label}:&nbsp;</td>
             <td class = "elementCell">{$T_QUESTION_FORM.question_text.html}</td></tr>
  {if $T_QUESTION_FORM.question_text.error}<tr><td></td><td class = "formError">{$T_QUESTION_FORM.question_text.error}</td></tr>{/if}
        {if $smarty.get.question_type == 'empty_spaces'}
         <tr><td></td>
          <td class = "infoCell">{$smarty.const._EMPTYSPACESEXPLANATION}</td></tr>
        {/if}
 {if $smarty.get.question_type == 'raw_text'}
   <tr><td class = "labelCell">{$T_QUESTION_FORM.force_correct.label}:&nbsp;</td>
             <td class = "elementCell">{$T_QUESTION_FORM.force_correct.html}</td></tr>
  {if $T_QUESTION_FORM.force_correct.error}<tr><td colspan = "2" class = "formError">{$T_QUESTION_FORM.force_correct.error}</td></tr>{/if}
   <tr id = "autocorrect" {if !$T_QUESTION_SETTINGS.autocorrect}style = "display:none"{/if}>
    <td class = "labelCell">{$smarty.const._AUTOCORRECTOPTIONS}:&nbsp;</td>
    <td class = "elementCell">
     <table id = "autocorrect_options">
     {foreach name = "autocorrect_list" item = "item" key = "key" from = $T_QUESTION_SETTINGS.autocorrect}
      <tr class = "autocorrect_options">
       <td>
        <select name = "autocorrect_contains[]">
         <option value = "1" {if $item.contains == 1}selected{/if}>{$smarty.const._CONTAINS}</option>
         <option value = "0" {if $item.contains == 0}selected{/if}>{$smarty.const._NOTCONTAINS}</option>
        </select>
       </td><td>
        <input value = "{$item.words|@implode:'|'}" type = "text" name = "autocorrect_words[]" value = "{$smarty.const._SEPARATEWORDSWITHPIPE}" onclick = "eF_js_editFreeTextChoice(this)" class = "inputText"/>
       </td><td>
        <select name = "autocorrect_score[]">
         <option value = "">{$smarty.const._POINTS}</option>
         {section loop = "11" name = "options"}
         <option value = "{$smarty.section.options.iteration-6}" {if $item.score == $smarty.section.options.iteration-6}selected{/if}>{$smarty.section.options.iteration-6}</option>
         {/section}
        </select>
        <img src = "images/16x16/error_delete.png" alt = "{$smarty.const._REMOVEOPTION}" title = "{$smarty.const._REMOVEOPTION}" onclick = "eF_js_removeFreeTextChoice(this)"/>
      </td></tr>
     {foreachelse}
      <tr class = "autocorrect_options">
       <td>
        <select name = "autocorrect_contains[]">
         <option value = "1">{$smarty.const._CONTAINS}</option>
         <option value = "0">{$smarty.const._NOTCONTAINS}</option>
        </select>
       </td><td>
        <input type = "text" name = "autocorrect_words[]" value = "{$smarty.const._SEPARATEWORDSWITHPIPE}" onclick = "eF_js_editFreeTextChoice(this)" class = "inputText emptyCategory infoCell"/>
       </td><td>
        <select name = "autocorrect_score[]">
         <option value = "">{$smarty.const._POINTS}</option>
         {section loop = "11" name = "options"}
         <option value = "{$smarty.section.options.iteration-6}">{$smarty.section.options.iteration-6}</option>
         {/section}
        </select>
        <img src = "images/16x16/error_delete.png" alt = "{$smarty.const._REMOVEOPTION}" title = "{$smarty.const._REMOVEOPTION}" onclick = "eF_js_removeFreeTextChoice(this)"/>
      </td></tr>
     {/foreach}
      <tr><td colspan = "3">
       <img class = "ajaxHandle" src = "images/16x16/add.png" alt = "{$smarty.const._ADDOPTION}" title = "{$smarty.const._ADDOPTION}" onclick = "eF_js_addFreeTextChoice()"/>
       <a href = "javascript:void(0)" onclick = "eF_js_addFreeTextChoice()">{$smarty.const._ADDOPTION}</a>
      </td></tr>
      <tr id = "autocorrect_score">
       <td colspan = "2" class = "labelCell">
        {$smarty.const._CONSIDERCORRECTWHENSCOREISGREATERTHAN}:&nbsp;
       </td><td>
        <input type = "text" name = "autocorrect_threshold" size="4" value = "{$T_QUESTION_SETTINGS.threshold}"/>
      </td></tr>
     </table>
     <hr/>
    </td></tr>

         <tr><td class = "labelCell">{$smarty.const._EXAMPLEANSWER}:&nbsp;</td>
             <td class = "elementCell">{$T_QUESTION_FORM.example_answer.html}</td></tr>
  {if $T_QUESTION_FORM.example_answer.error}<tr><td colspan = "2" class = "formError">{$T_QUESTION_FORM.example_answer.error}</td></tr>{/if}

 {elseif $smarty.get.question_type == 'true_false'}
         <tr><td class = "labelCell">{$smarty.const._THISQUESTIONIS}:&nbsp;</td>
             <td class = "elementCell">{$T_QUESTION_FORM.correct_true_false.html}</td></tr>
     {if $T_QUESTION_FORM.correct_true_false.error}<tr><td colspan = "2" class = "formError">{$T_QUESTION_FORM.correct_true_false.error}</td></tr>{/if}

 {elseif $smarty.get.question_type == 'empty_spaces'}
            <tr><td class = "labelCell"></td>
                <td class = "elementCell">{$T_QUESTION_FORM.generate_empty_spaces.html}</td></tr>
            <tr><td colspan = "2" >&nbsp;</td></tr>
            <tr id = "spacesRow"><td></td><td>
  {foreach name = 'empty_spaces_list' key = key item = item from = $T_QUESTION_FORM.empty_spaces}
         {$T_EXCERPTS.$key} {$item.html} {if $item.error}{$item.error}{/if}
  {/foreach}
            {$T_EXCERPTS[$smarty.foreach.empty_spaces_list.iteration]}
                </td></tr>
            <tr id = "empty_spaces_last_node"><td colspan = "2" ></td></tr>
            <tr><td></td>
                <td class = "infoCell">{$smarty.const._SEPARATEALTERNATIVESEXAMPLE}. {$smarty.const._YOUCANUSEASTERISK}</td></tr>
   <tr><td class = "labelCell">{$T_QUESTION_FORM.select_list.label}:&nbsp;</td>
             <td class = "elementCell">{$T_QUESTION_FORM.select_list.html}</td></tr>
   {if $T_QUESTION_FORM.select_list.error}<tr><td colspan = "2" class = "formError">{$T_QUESTION_FORM.select_list.error}</td></tr>{/if}
            <tr><td></td>
                <td class = "infoCell">{$smarty.const._CHECKINGTHISWILLDISPLAYLISTBOXFIRSTISCORRECT}</td></tr>
 {elseif $smarty.get.question_type == 'multiple_one'}
         <tr><td class = "labelCell questionLabel">{$smarty.const._INSERTMULTIPLEQUESTIONS}:</td>
             <td><table>
     {foreach name = 'multiple_one_list' key = key item = item from = $T_QUESTION_FORM.multiple_one}
           <tr><td>{$item.html}</td>
            <td>
   {if $smarty.foreach.multiple_one_list.iteration > 2} {*The if smarty.iteration is put here, so that the user cannot remove that last 2 rows *}
                        <a href = "javascript:void(0)" onclick = "eF_js_removeImgNode(this, 'multiple_one')">
                         <img src = "images/16x16/error_delete.png" alt = "{$smarty.const._REMOVECHOICE}" title = "{$smarty.const._REMOVECHOICE}" />
                        </a>
            {/if}
               </td><td style = "padding-left:30px">
                          <img onclick = "Element.extend(this).next().toggle()" src = "images/16x16/add.png" alt = "{$smarty.const._INSERTEXPLANATION}" title = "{$smarty.const._INSERTEXPLANATION}" style = "margin-right:5px;vertical-align:middle">{$T_QUESTION_FORM.answers_explanation[$key].html}
                        </td></tr>
   {if $item.error}<tr><td></td><td class = "formError">{$item.error}</td></tr>{/if}
  {/foreach}
                    <tr id = "multiple_one_last_node"></tr>
                </table>
             </td></tr>
         <tr><td class = "labelCell">
                 <a href = "javascript:void(0)" onclick = "eF_js_addAdditionalChoice('multiple_one')"><img src = "images/16x16/add.png" alt = "{$smarty.const._ADDQUESTION}" title = "{$smarty.const._ADDQUESTION}" border = "0"/></a>
             </td><td>
                 <a href = "javascript:void(0)" onclick = "eF_js_addAdditionalChoice('multiple_one')">{$smarty.const._ADDOPTION}</a>
             </td></tr>
         <tr><td colspan = "2">&nbsp;</td></tr>
         <tr><td class = "labelCell">{$T_QUESTION_FORM.correct_multiple_one.label}:&nbsp;</td>
             <td class = "elementCell">{$T_QUESTION_FORM.correct_multiple_one.html}</td></tr>

 {elseif $smarty.get.question_type == 'multiple_many'}
         <tr><td class = "labelCell questionLabel">{$smarty.const._INSERTMULTIPLEQUESTIONS}:</td>
             <td><table>
  {foreach name = 'multiple_many_list' key = key item = item from = $T_QUESTION_FORM.multiple_many}
                    <tr><td style = "width:1%;white-space:nowrap">{$item.html}</td>
                        <td style = "width:1%">{$T_QUESTION_FORM.correct_multiple_many.$key.html}</td>
                        <td style = "width:1%">
   {if $smarty.foreach.multiple_many_list.iteration > 2} {*The if smarty.iteration is put here, so that the user cannot remove that last 2 rows *}
                            <a href = "javascript:void(0)" onclick = "eF_js_removeImgNode(this, 'multiple_many')">
                                <img src = "images/16x16/error_delete.png" alt = "{$smarty.const._REMOVECHOICE}" title = "{$smarty.const._REMOVECHOICE}" /></a>
            {/if}
                        </td><td style = "padding-left:30px">
                 <img onclick = "Element.extend(this).next().toggle()" src = "images/16x16/add.png" alt = "{$smarty.const._INSERTEXPLANATION}" title = "{$smarty.const._INSERTEXPLANATION}" style = "margin-right:5px;vertical-align:middle">{$T_QUESTION_FORM.answers_explanation[$key].html}
                        </td></tr>
   {if $item.error}<tr><td></td><td class = "formError">{$item.error}</td></tr>{/if}
        {/foreach}
                    <tr id = "multiple_many_last_node"></tr>
                </table>
            </td></tr>
            <tr><td class = "labelCell">
                <a href = "javascript:void(0)" onclick = "eF_js_addAdditionalChoice('multiple_many')"><img src = "images/16x16/add.png" alt = "{$smarty.const._ADDQUESTION}" title = "{$smarty.const._ADDQUESTION}" border = "0"/></a>
            </td><td>
                <a href = "javascript:void(0)" onclick = "eF_js_addAdditionalChoice('multiple_many')">{$smarty.const._ADDOPTION}</a>
            </td></tr>
   <tr><td class = "labelCell">{$T_QUESTION_FORM.answers_logic.label}:&nbsp;</td>
             <td class = "elementCell">{$T_QUESTION_FORM.answers_logic.html}</td></tr>
   {if $T_QUESTION_FORM.answers_logic.error}<tr><td colspan = "2" class = "formError">{$T_QUESTION_FORM.answers_logic.error}</td></tr>{/if}
 {elseif $smarty.get.question_type == 'match'}
         <tr><td class = "labelCell questionLabel">{$smarty.const._INSERTMATCHCOUPLES}:</td>
             <td><table>
        {section name = 'match_list' loop = $T_QUESTION_FORM.match}
                    <tr><td style = "width:1%;white-space:nowrap">{$T_QUESTION_FORM.match[match_list].html}</td>
                        <td style = "width:1%;white-space:nowrap">&nbsp;&raquo;&raquo;&nbsp;</td>
                        <td style = "width:1%;white-space:nowrap">{$T_QUESTION_FORM.correct_match[match_list].html}</td>
                        <td style = "width:1%;white-space:nowrap">
         {if $smarty.section.match_list.iteration > 2} {*The if smarty.iteration is put here, so that the user cannot remove that last 2 rows*}
                            <a href = "javascript:void(0)" onclick = "eF_js_removeImgNode(this, 'match')">
                                <img src = "images/16x16/error_delete.png" border = "no" alt = "{$smarty.const._REMOVECHOICE}" title = "{$smarty.const._REMOVECHOICE}" /></a>
            {/if}
                        </td><td style = "padding-left:30px">
                              <img onclick = "Element.extend(this).next().toggle()" src = "images/16x16/add.png" alt = "{$smarty.const._INSERTEXPLANATION}" title = "{$smarty.const._INSERTEXPLANATION}" style = "margin-right:5px;vertical-align:middle">{$T_QUESTION_FORM.answers_explanation[match_list].html}
                        </td></tr>
   {if $T_QUESTION_FORM.match[match_list].error || $T_QUESTION_FORM.correct_match[match_list].error }<tr><td class = "formError">{$T_QUESTION_FORM.match[match_list].error}</td><td>{$T_QUESTION_FORM.correct_match[match_list].error}</td></tr>{/if}
  {/section}
                    <tr id = "match_last_node"></tr>
                </table>
             </td></tr>
             <tr><td class = "labelCell">
                 <a href = "javascript:void(0)" onclick = "eF_js_addAdditionalChoice('match')"><img src = "images/16x16/add.png" alt = "{$smarty.const._ADDQUESTION}" title = "{$smarty.const._ADDQUESTION}" border = "0"/></a>
             </td><td>
                 <a href = "javascript:void(0)" onclick = "eF_js_addAdditionalChoice('match')">{$smarty.const._ADDOPTION}</a>
             </td></tr>
 {elseif $smarty.get.question_type == 'drag_drop'}
         <tr><td class = "labelCell questionLabel">{$smarty.const._INSERTDRAGDROPCOUPLES}:</td>
             <td><table>
     {section name = 'drag_drop_list' loop = $T_QUESTION_FORM.drag_drop}
                    <tr><td style = "width:1%;white-space:nowrap">{$T_QUESTION_FORM.drag_drop[drag_drop_list].html}</td>
                        <td style = "width:1%;white-space:nowrap">&nbsp;&raquo;&raquo;&nbsp;</td>
                        <td style = "width:1%;white-space:nowrap">{$T_QUESTION_FORM.correct_drag_drop[drag_drop_list].html}</td>
                        <td style = "width:1%;white-space:nowrap">
         {if $smarty.section.drag_drop_list.iteration > 2} {*The if smarty.iteration is put here, so that the user cannot remove that last 2 rows*}
                            <a href = "javascript:void(0)" onclick = "eF_js_removeImgNode(this, 'drag_drop')">
                                <img src = "images/16x16/error_delete.png" border = "no" alt = "{$smarty.const._REMOVECHOICE}" title = "{$smarty.const._REMOVECHOICE}" /></a>
            {/if}
                        </td><td style = "padding-left:30px;white-space:nowrap">
                              <img onclick = "Element.extend(this).next().toggle()" src = "images/16x16/add.png" alt = "{$smarty.const._INSERTEXPLANATION}" title = "{$smarty.const._INSERTEXPLANATION}" style = "margin-right:5px;vertical-align:middle">{$T_QUESTION_FORM.answers_explanation[drag_drop_list].html}
                        </td></tr>
   {if $T_QUESTION_FORM.drag_drop[drag_drop_list].error || $T_QUESTION_FORM.correct_drag_drop[drag_drop_list].error }<tr><td class = "formError">{$T_QUESTION_FORM.drag_drop[drag_drop_list].error}</td><td>{$T_QUESTION_FORM.correct_drag_drop[drag_drop_list].error}</td></tr>{/if}
  {/section}
                    <tr id = "drag_drop_last_node"></tr>
                </table>
             </td></tr>
             <tr><td class = "labelCell">
                 <a href = "javascript:void(0)" onclick = "eF_js_addAdditionalChoice('drag_drop')"><img src = "images/16x16/add.png" alt = "{$smarty.const._ADDQUESTION}" title = "{$smarty.const._ADDQUESTION}" border = "0"/></a>
             </td><td>
                 <a href = "javascript:void(0)" onclick = "eF_js_addAdditionalChoice('drag_drop')">{$smarty.const._ADDOPTION}</a>
             </td></tr>
 {elseif $T_QUESTION_FILE}
  {include file = $T_QUESTION_FILE}
    {/if}

         <tr><td colspan = "2" >&nbsp;</td></tr>
         <tr><td></td><td class = "elementCell">
         <div class = "headerTools">
          <span>
           <img src = "images/16x16/add.png" alt = "{$smarty.const._INSERTEXPLANATION}" title = "{$smarty.const._INSERTEXPLANATION}">
           <a href = "javascript:void(0)" onclick = "eF_js_showHide('explanation');">{$smarty.const._INSERTEXPLANATION}</a>
          </span>
   </div>
   </td></tr>
         <tr id = "explanation" {if !$T_HAS_EXPLANATION}style = "display:none"{/if}>
          <td class = "labelCell">{$T_QUESTION_FORM.explanation.label}:</td>
             <td class = "elementCell"><img onclick = "toggledInstanceEditor = 'question_explanation_data';javascript:toggleEditor('question_explanation_data','mceEditor');" class = "handle" src = "images/16x16/order.png" title = {$smarty.const._TOGGLEHTMLEDITORMODE} alt = {$smarty.const._TOGGLEHTMLEDITORMODE} />&nbsp;<a href = "javascript:void(0)" onclick = "toggledInstanceEditor = 'question_explanation_data';javascript:toggleEditor('question_explanation_data','mceEditor');">{$smarty.const._TOGGLEHTMLEDITORMODE}</a><br/>{$T_QUESTION_FORM.explanation.html}</td></tr>
  {if $T_QUESTION_FORM.explanation.error}<tr><td></td><td class = "formError">{$T_QUESTION_FORM.explanation.error}</td></tr>{/if}
         <tr><td></td>
          <td class = "elementCell">
           {$T_QUESTION_FORM.submit_question.html}
     {if $smarty.get.add_question}
                  &nbsp;{$T_QUESTION_FORM.submit_question_another.html}
                 {else}
                  &nbsp;{$T_QUESTION_FORM.submit_new_question.html}
                 {/if}
             </td></tr>
     </table>
    </form>
 <div id = "fmInitial"><div id = "filemanager_div" style = "display:none;">{$T_FILE_MANAGER}</div></div>
 {/capture}

    {* The edit_question menu can be in three modes: For lesson tests, for skillgap tests, and for skill correlation of skillgap *}
    {* tests: the last is the popup version, where only the correlation table should appear *}
    {if $T_SKILLGAP_TEST && !isset($smarty.get.popup)}
     <div class="tabber" >
         <div class="tabbertab">
             <h3>{$smarty.const._QUESTIONINFO}</h3>
     {/if}

     {if !isset($smarty.get.popup)}
         {eF_template_printBlock title = $smarty.const._QUESTIONINFO data = $smarty.capture.t_questions_info image = '32x32/question_and_answer.png'}
     {/if}

     {if $T_SKILLGAP_TEST}
         </div>
         {if $smarty.get.edit_question}
             {if !isset($smarty.get.popup)}
         <div class="tabbertab">
             <h3>{$smarty.const._ASSOCIATEDSKILLS}</h3>
             {/if}
             {capture name='t_skills_to_questions'}
         <table id="questionSkillTable" width="100%" border = "0" width = "100%" class = "sortedTable" sortBy = "0">
             <tr class = "topTitle">
                 <td class = "topTitle">{$smarty.const._SKILL}</td>
                 <td class = "topTitle">{$smarty.const._RELEVANCE}</td>
                 <td class = "topTitle centerAlign">{$smarty.const._CHECK}</td>
             </tr>

             {foreach name = 'skills_list' key = 'key' item = 'skill' from = $T_QUESTION_SKILLS}
             <tr>
                 <td>{$skill.description}</td>
                 <td>
                  <span id = "span_skill_relevance_{$skill.skill_ID}" style="display:none">{if !isset($skill.relevance)}2{else}{$skill.relevance}{/if}</span>
                     <select name = "skill_relevance_{$skill.skill_ID}" id = "skill_relevance_{$skill.skill_ID}" onChange = "ajaxPost('{$skill.skill_ID}', this, 'questionSkillTable');document.getElementById('skill_{$skill.skill_ID}').checked = true;">
             <option value = "1" {if ($skill.relevance == "1")}selected{/if}>{$smarty.const._LOW}</option>
             <option value = "2" {if (!isset($skill.relevance) || $skill.relevance == "2")}selected{/if}>{$smarty.const._MEDIUM}</option>
             <option value = "3" {if ($skill.relevance == "3")}selected{/if}>{$smarty.const._HIGH}</option>
                     </select>
                 </td>
                 <td class = "centerAlign">
                  <span id = "span_skill_checked_{$skill.skill_ID}" style="display:none">{if isset($skill.questions_ID)}1{else}0{/if}</span>
                     <input class = "inputCheckBox" type = "checkbox" id = "skill_{$skill.skill_ID}" name = "skill_{$skill.skill_ID}" onClick ="ajaxPost('{$skill.skill_ID}', this, 'questionSkillTable');" {if isset($skill.questions_ID)}checked{/if}>
                 </td>
             </tr>
             {foreachelse}
             <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory" colspan = "6">{$smarty.const._NODATAFOUND}</td></tr>
             {/foreach}
         </table>

         {/capture}
         {eF_template_printBlock title = $smarty.const._CORRELATESKILLSTOQUESTION|cat:':&nbsp;<i>'|cat:$T_QUESTION_TEXT|cat:'</i>' data = $smarty.capture.t_skills_to_questions image = '32x32/generic.png' options = $T_SUGGEST_QUESTION_SKILLS}
             {if !isset($smarty.get.popup)}
             </div>
             {/if}
         {/if}

         {if !isset($smarty.get.popup)}
         </div>
  </div>
   {/if}
  {/if}
