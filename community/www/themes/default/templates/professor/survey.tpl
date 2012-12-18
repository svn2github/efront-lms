
    {*moduleSurvey: The survey page*}
    {capture name = "moduleSurvey"}
        <tr><td class="moduleCell">
            {if (!isset($smarty.get.screen_survey) && !isset($smarty.get.action) && $smarty.get.screen_survey != '2')}
            {* Format T_SURVEY in html code *}
            {capture name='t_survey_code'}
                {eF_template_printSurveysList data = $T_SURVEY_INFO questions = $T_SURVEY_QUESTIONS user_type = $T_CURRENT_USER->user.user_type lesson_id = $T_LESSON_ID}
            {/capture}
            {eF_template_printBlock title=$smarty.const._SURVEYS data = $smarty.capture.t_survey_code image='32x32/surveys.png' help = 'Surveys'}
        {/if}
        {if (isset($smarty.get.screen_survey) && !isset($smarty.get.action) && $smarty.get.screen_survey == '2')}

        {capture name='t_survey_questions'}
  <table style = "width:100%;text-align:left"><tr><td>
            <table width="100%" border="0px">
                <tr>
                    <td width="2%" class="tableImage" align="right"><img src="images/16x16/add.png" border="0px" /></td>
                    <td class="innerTableHeader" align="left">
                        <select name="question_type" onchange="if(this.options[this.selectedIndex].value != '') document.location='professor.php?ctg=survey&action=question_create&screen_survey=2&surveys_ID={$smarty.get.surveys_ID}&question_type='+this.options[this.selectedIndex].value">
                            <option value="-" selected>{$smarty.const._ADDQUESTION}:</option>
                            <option value="yes_no">{$smarty.const._YES_NO}</option>
                            <option value="development">{$smarty.const._DEVELOPMENT}</option>
                            <option value="dropdown">{$smarty.const._DROPDOWN}</option>
                            <option value="multiple_one">{$smarty.const._SURVEYQUESTIONMULTIPLEONE}</option>
                            <option value="multiple_many">{$smarty.const._SURVEYQUESTIONMULTIPLEMANY}</option>
                        </select>
                    </td>
                </tr>
            </table>
            <table width=" 100%" border="0px" class="sortedTable" >
                <tr class="defaultRowHeight">
                    <td class="topTitle" align="center">{$smarty.const._QUESTIONNUMBER}</td>
                    <td class="topTitle" align="left">{$smarty.const._QUESTIONTITLE}</td>
                    <td class="topTitle" align="left">{$smarty.const._QUESTIONTYPE}</td>
                    <td class="topTitle" align="center">{$smarty.const._NUMBEROFOPTIONS}</td>
                    <td class="topTitle" align="left" colspan="100%">{$smarty.const._OPERATIONS}</td>
                </tr>
            {if ( isset($T_NOQUESTIONSFORSURVEY) )}
                <tr><td class="emptyCategory" colspan="100%">{$smarty.const._NOQUESTIONSFORSURVEY}</td></tr>
                </table>
                </td></tr>
            {else}
            {section name='survey_questions' loop=$T_SURVEY_QUESTIONS_INFO}
                <tr class = "{cycle name = "su_info" values = "oddRowColor, evenRowColor"}">
                    <td class="noSort" align="center">{$T_SURVEY_QUESTIONS_INFO[survey_questions].father_ID}</td>
                    <td class="noSort" align="left">{$T_SURVEY_QUESTIONS_INFO[survey_questions].question}</td>
                    <td class="noSort" align="left">
                    {if ($T_SURVEY_QUESTIONS_INFO[survey_questions].type == 'yes_no') }
                        {$smarty.const._YES_NO}
                    {elseif ($T_SURVEY_QUESTIONS_INFO[survey_questions].type == 'development') }
                        {$smarty.const._DEVELOPMENT}
                    {elseif ($T_SURVEY_QUESTIONS_INFO[survey_questions].type == 'dropdown') }
                        {$smarty.const._DROPDOWN}
                    {elseif ($T_SURVEY_QUESTIONS_INFO[survey_questions].type == 'multiple_one') }
                        {$smarty.const._SURVEYQUESTIONMULTIPLEONE}
                    {elseif ($T_SURVEY_QUESTIONS_INFO[survey_questions].type == 'multiple_many') }
                        {$smarty.const._SURVEYQUESTIONMULTIPLEMANY}
                    {else}
                        {$smarty.const._NOQUESTION}
                    {/if}
                    </td>
                    <td class="noSort" align="center">{$T_SURVEY_QUESTIONS[survey_questions]}</td>
                    <td class="noSort" align="left" valign="baseline">
                    <a href="professor.php?ctg=survey&action=question_create&question_action=update_question&question_type={$T_SURVEY_QUESTIONS_INFO[survey_questions].type}&surveys_ID={$smarty.get.surveys_ID}&question_ID={$T_SURVEY_QUESTIONS_INFO[survey_questions].id}&father_ID={$T_SURVEY_QUESTIONS_INFO[survey_questions].father_ID}"><img src="images/16x16/edit.png" border="0px" title="{$smarty.const._EDIT}" /></a>
                    {if ($smarty.section.survey_questions.iteration == $smarty.section.survey_questions.first)}
                        <a href="professor.php?ctg=survey&surveys_ID={$smarty.get.surveys_ID}&action=swap_question&swap_action=move_down&question_ID={$T_SURVEY_QUESTIONS_INFO[survey_questions].id}&father_ID={$T_SURVEY_QUESTIONS_INFO[survey_questions].father_ID}"><img src="images/16x16/navigate_down.png" border="0px" title="{$smarty.const._DOWN}"/></a>
                    {elseif ($smarty.section.survey_questions.iteration == $smarty.section.survey_questions.last)}
                        <a href="professor.php?ctg=survey&surveys_ID={$smarty.get.surveys_ID}&action=swap_question&swap_action=move_up&question_ID={$T_SURVEY_QUESTIONS_INFO[survey_questions].id}&father_ID={$T_SURVEY_QUESTIONS_INFO[survey_questions].father_ID}"><img src="images/16x16/navigate_up.png" border="0px" title="{$smarty.const._UP}"></a>
                    {else}
                        <a href="professor.php?ctg=survey&surveys_ID={$smarty.get.surveys_ID}&action=swap_question&swap_action=move_up&question_ID={$T_SURVEY_QUESTIONS_INFO[survey_questions].id}&father_ID={$T_SURVEY_QUESTIONS_INFO[survey_questions].father_ID}"><img src="images/16x16/navigate_up.png" border="0px" title="{$smarty.const._DOWN}"></a>
                        <a href="professor.php?ctg=survey&surveys_ID={$smarty.get.surveys_ID}&action=swap_question&swap_action=move_down&question_ID={$T_SURVEY_QUESTIONS_INFO[survey_questions].id}&father_ID={$T_SURVEY_QUESTIONS_INFO[survey_questions].father_ID}"><img src="images/16x16/navigate_down.png" border="0px" title="{$smarty.const._UP}"/></a>
                    {/if}
                    <a href="professor.php?ctg=survey&action=delete_question&question_ID={$T_SURVEY_QUESTIONS_INFO[survey_questions].id}&surveys_ID={$smarty.get.surveys_ID}" onclick="{literal}return confirm('{/literal}{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}{literal}');{/literal}"><img src="images/16x16/error_delete.png" border="0px" title="{$smarty.const._DELETE}" /></a></td></tr>
            {/section}

            </table>
            {/if}
   </td></tr></table>
        {/capture}
        {eF_template_printBlock title=$smarty.const._CREATESURVEYQUESTION data=$smarty.capture.t_survey_questions image='32x32/surveys.png' help = 'Surveys'}
        {/if}
        {if ($smarty.get.action == 'question_create')}
            {if (isset($smarty.get.question_type) && $smarty.get.question_type == '-')}
                <tr><td align="center"><input class="flatButton" type="button" value="{$smarty.const._BACK}" onclick="history.back();" />
            {else}

               <script>
               {literal}
                                //We need a "shuffle" function for our arrays. Add to the array prototype such a function
                                Array.prototype.shuffle = function() {
                                    for (var i = 0; i < this.length; i++) {
                                        // Random item in this array.
                                        var r = parseInt(Math.random() * this.length);
                                        var obj = this[r];

                                        // Swap.
                                        this[r] = this[i];
                                        this[i] = obj;
                                    }
                                }

                                //This function uses the shuffle method we added to the array prototype to randomly reorder the values of the match question boxes.
                                //function shuffle_match() {
                                //    var els = document.getElementsByTagName('input');           //Find all 'input' elements in the document.
                                //    var target_elements = new Array;
                                //    var target_values   = new Array;

                                //    for (var i = 0; i < els.length; i++) {                      //Get the text boxes that will be shuffled
                                //        if (els[i].name.match('correct_match')) {
                                //            target_elements.push(els[i]);
                                //            target_values.push(els[i].value);
                                //        }
                                //    }

                                //    target_values.shuffle();                                    //Shuffle the array

                                //    for (var i = 0; i < target_elements.length; i++) {          //Assign the new values to the text boxes.
                                //        target_elements[i].value = target_values[i];
                                //    }
                                //}


                                function eF_js_addAdditionalChoice(question_type) {
                                    var els = document.getElementsByTagName('input'); //Find all 'input' elements in th document.

                                    var counter = 0;
                                    for (var i = 0; i < els.length; i++) { //Count all the 'input' elements whose names match the selected question type (e.g. multiple_one[0], multiple_one[1] etc)
                                        if (els[i].name.match('^'+question_type) && els[i].type.match('text')) {
                                            counter++;
                                        }
                                    }

                                    if (counter > 1) { //If the counter is less than 2 (where 2 is the default input fields), it means that the selected question type is not one that may have multiple inputs (i.e. it may be raw_text)
                                        var last_node = document.getElementById(question_type+'_last_node'); //This is the node that the new elements will be inserted before

                                        var tr = document.createElement('tr'); //Create a table row to hold the new element
                                        tr.appendChild(document.createElement('td'));
                                        var td = document.createElement('td');//Create a new table cell to hold the new element
                                        tr.appendChild(td);//Append this table cell to the table row we created above

                                        var input = document.createElement('input'); //Create the new input element
                                        input.setAttribute('type', 'text'); //Set the element to be text box
                                        input.className = 'inputText inputText_QuestionChoice'; //Set its class to 'inputText'
                                        input.setAttribute('name', question_type + '['+counter+']'); //We give the new textboxes names if the form multiple_one[0], multiple_one[1] so we can handle them alltogether
                                        td.appendChild(input); //Append the text box to the table cell we created above

                                        var img = document.createElement('img');//Create an image element, that will hold the "delete" icon
                                        img.setAttribute('alt', '_REMOVECHOICE'); //Set alt and title for this image
                                        img.setAttribute('style','white-space:nowrap');
                                        img.setAttribute('title', '_REMOVECHOICE');
                                        img.setAttribute('src', 'images/16x16/error_delete.png'); //Set the icon source
                                        img.setAttribute('onclick', 'eF_js_removeImgNode(this, "'+question_type+'")'); //Set the event that will trigger the deletion
                                        img.onclick = function () {eF_js_removeImgNode(this, "'+question_type+'")}; //Set the event that will trigger the deletion
                                        var img_td = document.createElement('td'); //Create a new table cell to hold the image element
                                        td.appendChild(img); //Append the <td> to the row

                                        var parent_node = last_node.parentNode; //Find the parent element, that will hold the new element
                                        parent_node.insertBefore(tr, last_node); //Append the table row, that holds the input element, to its parent.
                                    }

                                }

                                //This function removes the <tr> element that contains the inserted node.
                                function eF_js_removeImgNode(el, question_type) {
                                    el.parentNode.parentNode.parentNode.removeChild(el.parentNode.parentNode); //It is <tr><td><img></td></tr>, so we need to remove the <tr> element, which is the el.parentNode.parentNode

                                    var els = document.getElementsByTagName('input'); //Find all 'input' elements in th document.

                                    var counter = 0;
                                    for (var i = 0; i < els.length; i++) { //Count all the 'input' elements whose names match the selected question type (e.g. multiple_one[0], multiple_one[1] etc)
                                        if (els[i].name.match('^'+question_type) && els[i].type.match('text')) {
                                            els[i].name = question_type+'['+counter+']'; //If the user deletes a middle choice, the remaining choices will not be continuous. I.e, if we have choices multiple_one[0-5] and the user removes multiple_one[3], then the array will have indexes 0,1,2,4,5. So, here, we re-calculate the number of choices and apply new array indexes
                                            counter++;
                                        }
                                    }

                                    if (question_type == 'multiple_one') { //Adjust the select box accordingly
                                        document.getElementById('correct_multiple_one').removeChild(document.getElementById('correct_multiple_one').lastChild);
                                    } else if (question_type == 'multiple_many' || question_type == 'match') { //For multiple/many (and match) questions, we need to reindex checkboxes (or answer text boxes) as well
                                        var counter = 0;
                                        for (var i = 0; i < els.length; i++) {
                                            if (els[i].name.match('correct_'+question_type)) {
                                                els[i].name = 'correct_'+question_type+'['+counter+']';
                                                counter++;
                                            }
                                        }
                                    }
                                }


                                //This function is used to create the text boxes that correspond to empty spaces.
                                function eF_js_createEmptySpaces() {

                                    if (tinyMCE) { //Get the text from the editor
          var question_text = tinyMCE.get('question_text').getContent();

                                    } else {
                                        var question_text = document.getElementById('question_text').value; //If the editor isn't set, get the question text from the text area
                                    }

                                    var excerpts = question_text.split(/###/g); //Get the question text pieces that are split by ###
                                    var last_node = document.getElementById('empty_spaces_last_node'); //This is the node that the new elements will be inserted before
                                    var parent_node = last_node.parentNode; //Find the parent element, that will hold the new element
                                    if (document.getElementById('spacesRow')) { //If the button was pressed again, remove old row and build a new one
                                        document.getElementById('spacesRow').parentNode.removeChild(document.getElementById('spacesRow'));
                                    }

         var tr = document.createElement('tr'); //Create a table row to hold the new element
                                    tr.setAttribute('id', 'spacesRow'); //We need an id to know which row this is, so we can remove it on demand
         tr.appendChild(document.createElement('td')); //Create a new empty table cell for alignment reasons. Append this table cell to the table row we created above

                                    var td = document.createElement('td'); //Create a new table cell to hold the new element
                                    td.setAttribute('colspan', '100%');
                                    tr.appendChild(td); //Append this table cell to the table row we created above

         code = '';
                                    for (var i = 0; i < excerpts.length; i++) { //For each designated empty space, create a span element that will hold the text and the text boxes
          code += excerpts[i];
                                        if (i != excerpts.length - 1) { //If, for example, we have 3 ###, these split the string to 4 parts. So, we must not insert a text box for the last (trailing) string piece
                                            code += '<input type="text" name = "empty_spaces['+i+']" class = "inputText">';
                                        }
                                    }
         td.innerHTML = code;
                                    parent_node.insertBefore(tr, last_node); //Append the table row, that holds the input element, to its parent.
        }
               {/literal}
               </script>
               {capture name='question'}
                    {$T_ADD_QUESTION.javascript}
                    <form {$T_ADD_QUESTION.attributes}>
                    {$T_ADD_QUESTION.hidden}
                    <table class="formElements">
                        <input type="hidden" name="question_type" value="{$smarty.get.question_type}" />
                        <input type="hidden" name="surveys_ID" value="{$smarty.get.surveys_ID}" />
                        {if ($smarty.get.question_action == 'update_question')}
                            <input type="hidden" name="question_ID" value="{$smarty.get.question_ID}" />
                            <input type="hidden" name="father_ID", value="{$smarty.get.father_ID}" />
                        {/if}
                        <tr><td class = "labelCell">{$smarty.const._QUESTIONTEXT}:</td>
                        <td class = "elementCell">{$T_ADD_QUESTION.question_text.html}</td></tr>
                        {if ($smarty.get.question_type != 'development')}
                        <tr><td class = "labelCell">{$smarty.const._INSERTMULTIPLEQUESTIONS}:</td>
                        {/if}
                        {if ($smarty.get.question_type == 'yes_no')}
                        {if ($smarty.get.question_action == 'update_question') }
                            <td align = "left">
                                {$T_ADD_QUESTION.yes_no[0].html}
                            </td>
                            </tr>
                            <tr><td></td><td>{$T_ADD_QUESTION.yes_no[1].html}</tr></tr>
                        {else}
                            <td align = "left">
                            {$T_ADD_QUESTION.yes_no[0].html}
                            </td>
                            </tr>
                            <tr><td></td><td>{$T_ADD_QUESTION.yes_no[1].html}</tr></tr>
                        {/if}
                    {/if}
                    {if ($smarty.get.question_type == 'development')}
                    {/if}
                    {if ($smarty.get.question_type == 'dropdown')}
                        {if ($smarty.get.question_action == 'update_question')}
                            {foreach name='drop_down_list' item=item key=key from=$T_ADD_QUESTION.drop_down}
                                {if ($smarty.foreach.drop_down_list.index == 0)}
                                    <td>{$item.html}</td>
                                    </tr>
                                {elseif ($smarty.foreach.drop_down_list.index == 1)}
                                    <tr><td></td><td>{$item.html}</td></tr>
                                {else}
                                    <tr><td></td><td>{$item.html}<img style = "vertical-align:middle;" align="center" src="images/16x16/error_delete.png" border="0px" onclick="eF_js_removeImgNode(this, '{$smarty.get.question_type}')" ></td></tr>
                                {/if}
                            {/foreach}
                        {else}
                            <td align = "left">{$T_ADD_QUESTION.drop_down[0].html}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td align = "left">{$T_ADD_QUESTION.drop_down[1].html}</td>
                            </tr>
                        {/if}
                        <tr id = "drop_down_last_node"></tr>
                        <tr><td width="5%" align="right">
                            <a href = "javascript:void(0)" onclick = "eF_js_addAdditionalChoice('drop_down')"><img src = "images/16x16/add.png" alt = "{$smarty.const._ADDQUESTION}" title = "{$smarty.const._ADDQUESTION}" border = "0"/></a>
                        </td><td width="95%" align="left">
                            <a href = "javascript:void(0)" onclick = "eF_js_addAdditionalChoice('drop_down')">{$smarty.const._ADDOPTION}</a></td></tr>
                    {/if}
                    {if ($smarty.get.question_type == 'multiple_one')}
                        {if ($smarty.get.question_action == 'update_question')}
                            {foreach name='multiple_one_list' item=item key=key from=$T_ADD_QUESTION.multiple_one}
                                {if ($smarty.foreach.multiple_one_list.index == 0)}
                                    <td>{$item.html}</td>
                                    </tr>
                                {elseif ($smarty.foreach.multiple_one_list.index == 1)}
                                    <tr><td></td><td>{$item.html}</td></tr>
                                {else}
                                    <tr><td></td><td>{$item.html}<img style = "vertical-align:middle;" align="center" src="images/16x16/error_delete.png" border="0px" onclick="eF_js_removeImgNode(this, '{$smarty.get.question_type}')" ></td></tr>
                                {/if}
                            {/foreach}
                        {else}
                            <td align = "left">{$T_ADD_QUESTION.multiple_one[0].html}</td></tr>
                            <tr><td></td><td align = "left">{$T_ADD_QUESTION.multiple_one[1].html}</td></tr>
                        {/if}
                        <tr id = "multiple_one_last_node"></tr>
                        <tr><td width="10%" align="right">
                            <a href = "javascript:void(0)" onclick = "eF_js_addAdditionalChoice('multiple_one')"><img src = "images/16x16/add.png" alt = "{$smarty.const._ADDQUESTION}" title = "{$smarty.const._ADDQUESTION}" border = "0"/></a></td>
                        </td><td width="90%" align="left">
                            <a href = "javascript:void(0)" onclick = "eF_js_addAdditionalChoice('multiple_one')">{$smarty.const._ADDOPTION}</a></td></tr>
                    {/if}
                    {if ($smarty.get.question_type == 'multiple_many')}
                        {if ($smarty.get.question_action == 'update_question')}
                            {foreach name='multiple_one_list' item=item key=key from=$T_ADD_QUESTION.multiple_many}
                                {if ($smarty.foreach.multiple_many_list.index == 0)}
                                    <td>{$item.html}</td>
                                    </tr>
                                {elseif ($smarty.foreach.multiple_many_list.index == 1)}
                                    <tr><td></td><td>{$item.html}</td></tr>
                                {else}
                                    <tr><td></td><td>{$item.html}<img style = "vertical-align:middle;" align="center" src="images/16x16/error_delete.png" border="0px" onclick="eF_js_removeImgNode(this, '{$smarty.get.question_type}')" ></td></tr>
                                {/if}
                            {/foreach}
                        {else}
                            <td align = "left">{$T_ADD_QUESTION.multiple_many[0].html}</td></tr>
                            <tr><td></td><td align = "left">{$T_ADD_QUESTION.multiple_many[1].html}</td></tr>
                        {/if}
                        <tr id = "multiple_many_last_node"></tr>
                        <tr><td width="10%" align="right">
                            <a href = "javascript:void(0)" onclick = "eF_js_addAdditionalChoice('multiple_many')"><img src = "images/16x16/add.png" alt = "{$smarty.const._ADDQUESTION}" title = "{$smarty.const._ADDQUESTION}" border = "0"/></a></td>
                        </td><td width="90%" align="left">
                            <a href = "javascript:void(0)" onclick = "eF_js_addAdditionalChoice('multiple_many')">{$smarty.const._ADDOPTION}</a></td></tr>
                    {/if}
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td></td>
                        <td align="left"><input class="flatButton" type="submit" value="{$smarty.const._SAVE}" />
                        </td>
                    </tr>
                    </table>
                </form>
            {/capture}
            {eF_template_printBlock title=$smarty.const._CREATESURVEYQUESTION data=$smarty.capture.question image='32x32/surveys.png' help = 'Surveys'}
        {/if}
    {/if}
    {if ($smarty.get.action == 'create_survey' && $smarty.get.screen == '1')}
        {capture name='createSurveyForm'}
            {$T_CREATE_SURVEY.javascript}
            <table class="formElements">
                <form {$T_CREATE_SURVEY.attributes}>
                {$T_CREATE_SURVEY.hidden}
                <tr>
                    <td class = "labelCell">{$smarty.const._SURVEYCODE}:</td>
                    <td>{$T_CREATE_SURVEY.surveyCode.html}</td>
                </tr>
                <tr>
                    <td class = "labelCell">{$smarty.const._SURVEYNAME}:</td>
                    <td>{$T_CREATE_SURVEY.surveyName.html}</td>
                </tr>
                <tr>
                    <td class = "labelCell">{$smarty.const._SURVEYINFO}:</td>
                    <td>{$T_CREATE_SURVEY.surveyInfo.html}</td>
                </tr>
                {if ($smarty.get.survey_action == 'save')}
                    <tr>
                        <td class = "labelCell">{$smarty.const._SURVEYAVALIABLEFROM}:</td>{eF_template_html_select_date instant='1' end_year='+3'}</td>
                    </tr>
                    <tr>
                        <td class = "labelCell">{$smarty.const._SURVEYUNTIL}:</td>
                        <td>{eF_template_html_select_date instant='2' end_year='+3'}</td>
                    </tr>
                {else}
                    <tr>
                        <td class = "labelCell">{$smarty.const._SURVEYAVALIABLEFROM}:</td>
                        <td>{eF_template_html_select_date instant='1' time=$T_START_DATE end_year='+3'} {$smarty.const._TIME}: {html_select_time prefix="from_" time = $T_START_DATE display_seconds = false}</td>
                    </tr>
                    <tr>
                        <td class = "labelCell">{$smarty.const._SURVEYUNTIL}:</td>
                        <td>{eF_template_html_select_date instant='2' time=$T_END_DATE end_year='+3'} {$smarty.const._TIME}: {html_select_time prefix="to_" time = $T_END_DATE display_seconds = false}</td>
                    </tr>
                {/if}
                <tr>
                    <td class = "labelCell">{$smarty.const._SURVEYINTROTEXT}:</td>
                    <td>{$T_CREATE_SURVEY.surveyIntro.html}</td>
                </tr>
                <tr>
                    <td class = "labelCell">{$smarty.const._SURVEYENDTEXT}:</td>
                    <td>{$T_CREATE_SURVEY.surveyEnd.html}</td>
                </tr>
                <tr><td>&nbsp;</td></tr>
                <tr><td></td><td align="left"><input class="flatButton" type="submit" value="{$smarty.const._SAVE}" /></td></tr>
                </form>
            </table>
        {/capture}
        {eF_template_printBlock title=$smarty.const._CREATESURVEY data=$smarty.capture.createSurveyForm image='32x32/surveys.png' help = 'Surveys'}
    {/if}
    {if ($smarty.get.action == 'preview') }
        {capture name='t_no_questions'}
            {if ($T_SIZEOF_QUESTIONS != '0')}
                {eF_template_printSurvey data=$T_SURVEY_INFO questions=$T_SURVEY_QUESTIONS user_type=$T_USER}
            {else}
                <table><tr><td class="emptyCategory">{$smarty.const._NOQUESTIONSFORSURVEY}</td></tr></table>
            {/if}
        {/capture}
        {eF_template_printBlock title=$smarty.const._PREVIEW data=$smarty.capture.t_no_questions image='32x32/surveys.png' help = 'Surveys'}
    {/if}
    {if (isset($smarty.get.action) && $smarty.get.action == 'view_users') }
        {capture name='t_view_users'}
            {if ($T_SIZEOF_USERS != '0') }
                <table class="sortedTable" width="100%">
                    <tr><td class="topTitle">{$smarty.const._USER}</td>
                        <td class="topTitle" align="center">{$smarty.const._SURVEYDONE}</td>
                        <td class="topTitle" align="center">{$smarty.const._OPERATIONS}</td>
                    {section name='t_users' loop=$T_SURVEY_USERS}
                    <tr class = "{cycle name = "su_stats_h_info" values = "oddRowColor, evenRowColor"}">
                        <td>#filter:login-{$T_SURVEY_USERS[t_users].users_LOGIN}#</td>
                        {if ( $T_DONE_SURVEY[t_users]=='true' ) }
                            <td align="center"><img src='images/16x16/success.png' border='0px'><span style = "display:none">1</span></td>
                        {else}
                            <td align="center"><img src='images/16x16/error_delete.png' border='0px'><span style = "display:none">0</span></td>
                        {/if}
                        {if ( $T_DONE_SURVEY[t_users]=='true' ) }
                            <td align="center" valign="middle" style="white-space:nowrap">
                                <a href="professor.php?ctg=survey&action=survey_preview&user={$T_SURVEY_USERS[t_users].users_LOGIN}&surveys_ID={$smarty.get.surveys_ID}"><img src='images/16x16/search.png' border='0px' title='{$smarty.const._PREVIEW}'></a>&nbsp;
                                <a href="professor.php?ctg=survey&action=view_users&preview_action=delete_user&user={$T_SURVEY_USERS[t_users].users_LOGIN}&surveys_ID={$smarty.get.surveys_ID}"><img src='images/16x16/error_delete.png' border='0px' title='{$smarty.const._DELETE}' /></a>
                            </td>
                        {else}
                            <td class="emptyClass" align="center">
                             <img class = "ajaxHandle" src='images/16x16/mail.png' title='{$smarty.const._SENDREMINDER}' alt='{$smarty.const._SENDREMINDER}' onclick = "sendSurveyReminder(this, '{$smarty.get.surveys_ID}', '{$T_SURVEY_USERS[t_users].users_LOGIN}')"/>
       </td>
                        {/if}
                    </tr>
                    {/section}
                </table>
            {else}
                <table><tr><td class="emptyCategory">{$smarty.const._NOUSERFORTHISSURVEYYET}</td></tr></table>
            {/if}
            <script>
            {literal}
            function sendSurveyReminder(el, survey, user) {
             Element.extend(el);
    parameters = {survey:survey, user:user, method: 'get'};
    var url = 'professor.php?ctg=survey&action=reminder';
    ajaxRequest(el, url, parameters);
            }
            {/literal}
            </script>
        {/capture}
            {eF_template_printBlock title=$smarty.const._DONESURVEYUSERS data=$smarty.capture.t_view_users image='32x32/surveys.png' help = 'Surveys'}
    {/if}
    {if ( isset($smarty.get.action) && $smarty.get.action == 'survey_preview' ) }
        {capture name='preview_survey'}
                {eF_template_printSurvey data=$T_SURVEY_INFO questions=$T_SURVEY_QUESTIONS answers=$T_USER_ANSWERS user_type=$T_USER user=$smarty.get.user action=$smarty.get.action}
        {/capture}
        {eF_template_printBlock title=$smarty.const._SURVEYPREVIEWFORUSER|cat:$smarty.get.user data=$smarty.capture.preview_survey image='32x32/surveys.png' help = 'Surveys'}
    {/if}
    {if (isset($smarty.get.action) && $smarty.get.action == 'statistics') }
    {capture name='surveyStatistics'}
            <table width="100%" border="0px">
            {foreach item=general from=$T_SURVEY_INFO}
                {foreach item=item key=key from=$general}
                    {if ($key == 'survey_code')}
                        <tr class = "{cycle name = "su_stats_h_info" values = "oddRowColor, evenRowColor"}"><td class="labellCell" width="10%">{$smarty.const._SURVEYCODE}:</td><td width="90%" colspan="2">{$item}</td></tr>
                    {/if}
                    {if ($key == 'survey_name')}
                        <tr class = "{cycle name = "su_stats_h_info" values = "oddRowColor, evenRowColor"}"><td class="labellCell" width="10%">{$smarty.const._SURVEYNAME}:</td><td width="90%" colspan="2">{$item}</td></tr>
                    {/if}
                    {if ($key == 'survey_info')}
                        <tr class = "{cycle name = "su_stats_h_info" values = "oddRowColor, evenRowColor"}"><td class="labellCell" width="10%">{$smarty.const._SUBTITLE}:</td><td width="90%" colspan="2">{$item}</td></tr>
                    {/if}
                    {if ($key == 'start_date')}
                        <tr class = "{cycle name = "su_stats_h_info" values = "oddRowColor, evenRowColor"}"><td class="labellCell" width="10%">{$smarty.const._SURVEYAVALIABLEFROM}:</td><td width="90%" colspan="2">#filter:timestamp-{$item}#</td></tr>
                    {/if}
                    {if ($key == 'end_date')}
                        <tr class = "{cycle name = "su_stats_h_info" values = "oddRowColor, evenRowColor"}"><td class="labellCell" width="10%">{$smarty.const._SURVEYUNTIL}:</td><td width="90%" colspan="2">#filter:timestamp-{$item}#</td></tr>
                    {/if}
                {/foreach}
            {/foreach}
            <tr class = "{cycle name = "su_stats_h_info" values = "oddRowColor, evenRowColor"}"><td class="labellCell" width="10%">{$smarty.const._USERS}:</td><td align="left" width="90%" colspan="2">{$T_USERS_DONE}/{$T_USERS_OVERALL}</td></tr>
            <tr class = "{cycle name = "su_stats_h_info" values = "oddRowColor, evenRowColor"}"><td class="labellCell" width="10%" style="white-space:nowrap">{$smarty.const._STATISTICS}:</td><td colspan="2"><a href="professor.php?ctg=survey&action=statistics&statistics_action=export&surveys_ID={$smarty.get.surveys_ID}"><img src="images/file_types/xls.png" border="0px" title="{$smarty.const._EXPORTSTATS}" title="{$smarty.const._EXPORTSTATS}"/></a></td></tr>
            <tr><td colspan="3">&nbsp;</td></tr>
        {if ($T_TOTAL_DONE_USERS == '0')}
            <tr><td width="100%" colspan="3" class = "emptyCategory">{$smarty.const._NOUSERHAVEDONETHISSURVEYYET}</td></tr>
        {else}
            {section name='t_survey_statistics' loop=$T_SURVEY_QUESTIONS}
                <tr class = "{cycle name = "su_stats_info" values = "oddRowColor, evenRowColor"}"><td class="questionWeight" colspan="3"><img align="left" style = "vertical-align:middle;" src="images/16x16/surveys.png" border="0px">&nbsp;<b>{$smarty.const._QUESTION}:&nbsp;{$T_SURVEY_QUESTIONS[t_survey_statistics].question}</b></td></tr>
                {foreach from=$T_SURVEY_QUESTIONS_CHOICES[t_survey_statistics] item=choice key=type}
                    {foreach from=$choice item=item key=key}
                        <tr class = "{cycle name = "su_stats_info" values = "oddRowColor, evenRowColor"}">
                            <td valign="top" align="right" width="10%">
                            {$T_SURVEY_ANSWER_RATE[t_survey_statistics].$key}%
                                -
                            {$T_SURVEY_VOTES[t_survey_statistics].$key}/{$T_USERS_DONE}
                            </td>
                            <td valign="top" align="left" width="30%">
                               {$item}
                            </td>
                            <td valign="top" width="60%" height="20px" align="left">
                                <span style = "position:absolute;text-align:center;width:200px;border:1px solid #d3d3d3;vertical-align:middle;z-index:2">{$T_SURVEY_ANSWER_RATE[t_survey_statistics].$key}%</span>
                                    <span style = "background-color:#A0BDEF;width:{$T_SURVEY_ANSWER_RATE[t_survey_statistics].$key*2}px;border:1px dotted #d3d3d3;position:absolute">&nbsp;</span>
                            </td>
                        </tr>
                    {/foreach}
                {/foreach}
                <tr><td colspan="3">&nbsp;</td></tr>
            {/section}
        {/if}
    </table>
    {/capture}
        {eF_template_printBlock title=$smarty.const._SURVEYSTATISTICS data=$smarty.capture.surveyStatistics image='32x32/surveys.png' help = 'Surveys'}
    {/if}
    {if (isset($smarty.get.action) && $smarty.get.action == 'publish') }
        {capture name='t_publish_survey'}
            {$T_PUBLISH_FORM.javascript}
            <form {$T_PUBLISH_FORM.attributes}>
                <table width = "100%" class="sortedTable" rowsPerPage="10">
                    <tr>
                        <td class = "topTitle" align = "left">{$smarty.const._FIRSTNAME}</td>
                        <td class = "topTitle" align = "left">{$smarty.const._LASTNAME}</td>
                        <td class = "topTitle" align = "left">{$smarty.const._EMAIL}</td>
                        <td class = "topTitle" align = "left">{$smarty.const._LOGIN}</td>
                        <td class = "topTitle" align = "left">{$smarty.const._SURVEYSELECTION}</td>
                    </tr>
                    {section name='survey_users' loop=$T_LESSON_USERS}
                        <tr class="{cycle name = "su_users" values = "oddRowColor, evenRowColor"}">
                            <td align="left">{$T_LESSON_USERS[survey_users].name}</td>
                            <td align="left">{$T_LESSON_USERS[survey_users].surname}</td>
                            <td align="left">{$T_LESSON_USERS[survey_users].email}</td>
                            <td align="left">#filter:login-{$T_LESSON_USERS[survey_users].login}#</td>
                            <td>
                                <input type="hidden" name="user_email[{$smarty.section.survey_users.index}]" value="{$T_LESSON_USERS[survey_users].email}"/>
                                <input type="hidden" name="user_login[{$smarty.section.survey_users.index}]" value="{$T_LESSON_USERS[survey_users].login}"/>
                                {if ($T_EXISTS[survey_users] == 'true')}
                                    <input type="checkbox" name="selection[{$smarty.section.survey_users.index}]" checked/>
                                {else}
                                    <input type="checkbox" name="selection[{$smarty.section.survey_users.index}]"/>
                            {/if}
                        </td>
                    </tr>
                    {/section}
                </table>
                <table width="100%" align="center">
                    <tr><td>&nbsp;</td></tr>
                    <tr><td class="horizontalSeparator" colspan="2"></td></tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td class="labelCell">{$smarty.const._ENTERMAILMESSAGE}:</td>
                        <td>{$T_PUBLISH_FORM.email_intro.html}</td>
                    </tr>
                    <tr><td class="labelCell">{$smarty.const._SENDALSOVIAEMAIL}:</td><td>{$T_PUBLISH_FORM.send_email.html}</td></tr>
                    <tr><td class="horizontalSeparator" colspan="2">&nbsp;</td></tr>
                    <tr><td></td><td align="left"><input class="flatButton" type="submit" value="{$smarty.const._SUBMIT}"/></td></tr>
                </table>
            </form>
        {/capture}
            {eF_template_printBlock title=$smarty.const._ADDUSERSTOSURVEY data=$smarty.capture.t_publish_survey image='32x32/surveys.png' help = 'Surveys'}
    {/if}
     </td></tr>
    {/capture}
