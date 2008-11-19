
                            {if $smarty.get.add_test || $smarty.get.edit_test}

                                {capture name = 't_edit_test_code'}
                                        <div class = "tabber">
                                            <div class = "tabbertab" id="test_options" title = "{$smarty.const._TESTOPTIONS}">
                                                {$T_TEST_FORM.javascript}
                                                <form {$T_TEST_FORM.attributes}>
                                                    {$T_TEST_FORM.hidden}
                                                    <table class = "formElements">
                                                    {if $T_TEST_FORM.parent_content}
                                                        <tr><td class = "labelCell">{$T_TEST_FORM.parent_content.label}:&nbsp;</td>
                                                            <td class = "elementCell">{$T_TEST_FORM.parent_content.html}</td></tr>
                                                        {if $T_TEST_FORM.parent_content.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.parent_content.error}</td></tr>{/if}
                                                    {/if}
                                                        <tr><td class = "labelCell">{$smarty.const._NAME}:&nbsp;</td>
                                                            <td class = "elementCell">{$T_TEST_FORM.name.html}</td></tr>
                                                        {if $T_TEST_FORM.name.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.name.error}</td></tr>{/if}
                                                    {*Exclude from skillgap tests*}
                                                    {if !$T_SKILLGAP_TEST}
                                                        <tr><td class = "labelCell">{$smarty.const._DURATIONINMINUTES}:&nbsp;</td>
                                                            <td class = "elementCell">{$T_TEST_FORM.duration.html} <span class = "infoCell">{$smarty.const._BLANKFORNOLIMIT}</span></td></tr>
                                                        {if $T_TEST_FORM.duration.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.duration.error}</td></tr>{/if}
                                                        <tr><td class = "labelCell">{$smarty.const._REDOABLE}:&nbsp;</td>
                                                            <td class = "elementCell">{$T_TEST_FORM.redoable.html} <span class = "infoCell">{$smarty.const._BLANKFORUNLIMITED}</span></td></tr>
                                                        {if $T_TEST_FORM.redoable.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.redoable.error}</td></tr>{/if}
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
                                                            <td class = "elementCell"><table align="left"><tr><td>{$T_TEST_FORM.automatic_assignment.html}</td>
                                                            <td align="left"><img src = "images/16x16/help2.png" alt = "help" title = "help" onclick = "eF_js_showHideDiv(this, 'automatic_assignment_info', event)"><div id = 'automatic_assignment_info' onclick = "eF_js_showHideDiv(this, 'automatic_assignment_info', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:450px;height:50px;position:absolute;display:none">{$smarty.const._AUTOMATICASSIGNMENTINFO}</div></td></tr></table></td>
                                                    {/if}

                                                        <tr><td class = "labelCell">{$smarty.const._PUBLISH}:&nbsp;</td>
                                                            <td class = "elementCell">{$T_TEST_FORM.publish.html}</td></tr>
                                                        {if $T_TEST_FORM.publish.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.publish.error}</td></tr>{/if}
                                                        <tr><td class = "labelCell">{$smarty.const._ONEBYONE}:&nbsp;</td>
                                                            <td class = "elementCell">{$T_TEST_FORM.onebyone.html}</td></tr>
                                                        {if $T_TEST_FORM.onebyone.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.onebyone.error}</td></tr>{/if}    
                                                    {*Exclude from skillgap tests*}
                                                    {if !$T_SKILLGAP_TEST}                                                            
                                                        <tr><td class = "labelCell" style = "white-space:normal">{$smarty.const._SHOWGIVENANSWERS}:&nbsp;</td>
                                                            <td class = "elementCell">{$T_TEST_FORM.given_answers.html}</td></tr>
                                                        {if $T_TEST_FORM.given_answers.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.given_answers.error}</td></tr>{/if}
                                                        <tr><td class = "labelCell" style = "white-space:normal">{$smarty.const._SHOWRIGHTANSWERS}:&nbsp;</td>
                                                            <td class = "elementCell">{$T_TEST_FORM.answers.html}</td></tr>
                                                        {if $T_TEST_FORM.answers.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.answers.error}</td></tr>{/if}
                                                    {/if}            
                                                        <tr><td class = "labelCell">{$smarty.const._SHUFFLEANSWERS}:&nbsp;</td>
                                                            <td class = "elementCell">{$T_TEST_FORM.shuffle_answers.html}</td></tr>
                                                        {if $T_TEST_FORM.shuffle_answers.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.shuffle_answers.error}</td></tr>{/if}
                                                        <tr><td class = "labelCell">{$smarty.const._SHUFFLEQUESTIONS}:&nbsp;</td>
                                                            <td class = "elementCell">{$T_TEST_FORM.shuffle_questions.html}</td></tr>
                                                        <tr><td class = "labelCell">{$smarty.const._DISPLAYORDEREDLIST}:&nbsp;</td>
                                                            <td class = "elementCell"><table><tr><td>{$T_TEST_FORM.display_list.html}</td>
                                                            <td align="left"><img src = "images/16x16/help2.png" alt = "help" title = "help" onclick = "eF_js_showHideDiv(this, 'display_ordered', event)"><div id = 'display_ordered' onclick = "eF_js_showHideDiv(this, 'display_ordered', event)" class = "popUpInfoDiv" style = "padding:1em 1em 1em 1em;width:450px;position:absolute;z-index:100;display:none">{$smarty.const._DISPLAYORDEREDLISTINFO}</div></td></tr></table></td></tr>
                                                            
                                                    {*Exclude from skillgap tests*}
                                                    {if !$T_SKILLGAP_TEST}
                                                        {if $T_TEST_FORM.shuffle_questions.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.shuffle_questions.error}</td></tr>{/if}
                                                        <tr><td class = "labelCell">{$smarty.const._TESTCANBEPAUSED}:&nbsp;</td>
                                                            <td class = "elementCell">{$T_TEST_FORM.pause_test.html}</td></tr>
                                                        {if $T_TEST_FORM.pause_test.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.pause_test.error}</td></tr>{/if}
                                                        <tr><td class = "labelCell">{$smarty.const._DISPLAYQUESTIONWEIGHTS}:&nbsp;</td>
                                                            <td class = "elementCell">{$T_TEST_FORM.display_weights.html}</td></tr>
                                                        {if $T_TEST_FORM.display_weights.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.display_weights.error}</td></tr>{/if}
                                                    {/if}

                                                        <tr>
                                                            <td>&nbsp;</td>
                                                            <td style = "vertical-align:middle" id="toggleeditor_cell1"><a title="{$smarty.const._OPENCLOSEFILEMANAGER}" href="javascript:void(0)" onclick="toggle_file_manager();" style = "vertical-align:middle"><img id="arrow_down" src="images/16x16/navigate_down.png" border="0" alt="{$smarty.const._OPENCLOSEFILEMANAGER}" title="{$smarty.const._OPENCLOSEFILEMANAGER}" style="vertical-align:middle"/>&nbsp;<span id="open_manager" style = "vertical-align:middle">{$smarty.const._OPENFILEMANAGER}</span></a>&nbsp;&nbsp;<a href="javascript:toogleEditorMode('mce_editor_0');" id="toggleeditor_link"><img src = "images/16x16/replace2.png" title = "{$smarty.const._TOGGLEHTMLEDITORMODE}" alt = "{$smarty.const._TOGGLEHTMLEDITORMODE}" style = "vertical-align:middle" border = "0"/>&nbsp;<span  style = "vertical-align:middle">{$smarty.const._TOGGLEHTMLEDITORMODE}</span></a></td></tr>
                                                        <tr><td></td><td id="filemanager_cell"></td></tr>
                                                        <tr><td></td><td id="toggleeditor_cell2"></td></tr>

                                                        <tr><td class = "labelCell">{$smarty.const._DESCRIPTION}:&nbsp;</td>
                                                            <td class = "elementCell">{$T_TEST_FORM.description.html}</td></tr>
                                                        {if $T_TEST_FORM.description.error}<tr><td></td><td class = "formError">{$T_TEST_FORM.description.error}</td></tr>{/if}
                                                        <tr><td colspan = "2">&nbsp;</td></tr>
                                                        <tr><td></td>
                                                            <td class = "elementCell">
                                                                {$T_TEST_FORM.submit_test.html}&nbsp;
                                                                {if $smarty.get.edit_test}{$T_TEST_FORM.submit_test_new.html}{/if}
                                                            </td></tr>
{*
                                                        <tr><td></td><td>
                                                                {$T_TEST_FORM.submit_stay.html}
                                                                {$T_TEST_FORM.submit_return.html}
                                                                {$T_TEST_FORM.submit_new_stay.html}
                                                                {$T_TEST_FORM.submit_new_return.html}
                                                            </td></tr>
*}
                                                    </table>
                                                </form>
                                                <table><tr><td id="fmInitial">
                                                <div  id="filemanager_div" style="display:none;">
                                                    {$T_FILE_MANAGER}
                                                    <br/>
                                                </div>
                                                </td></tr>
                                                </table>
                                               <script type="text/javascript">
                                        {literal}

                                           var tinyMCEmode = true;
                                                function toogleEditorMode(sEditorID) {
                                                    try {
                                                        if(tinyMCEmode) {
                                                            tinyMCE.removeMCEControl(tinyMCE.getEditorId(sEditorID));
                                                            tinyMCEmode = false;
                                                        } else {
                                                            mceAddControlDynamic(sEditorID, 'editor_test_data' ,'mceEditor');
                                                            tinyMCEmode = true;
                                                        }
                                                    } catch(e) {
                                                        alert('editor error');
                                                    }
                                                }
                                        function insertatcursor(myField, myValue) {

                                        if (document.selection) {
                                            myField.focus();
                                            sel = document.selection.createRange();
                                            sel.text = myValue;
                                        }
                                        else if (myField.selectionStart || myField.selectionStart == '0') {
                                            var startPos = myField.selectionStart;
                                            var endPos = myField.selectionEnd;
                                            myField.value = myField.value.substring(0, startPos)+ myValue+ myField.value.substring(endPos, myField.value.length);
                                        } else {
                                            myField.value += myValue;
                                        }
                                    }

                                    function insert_editor(element, id) {
                                    {/literal}{if !$smarty.get.edit_test}{literal}
                                        var url = '{/literal}{$T_BASENAME_PHPSELF}{literal}?ctg=tests&add_test=1&postAjaxRequest_tests_insert=1';
                                    {/literal}{else}{literal}
                                        var url = '{/literal}{$T_BASENAME_PHPSELF}{literal}?ctg=tests&edit_test={/literal}{$smarty.get.edit_test}{literal}&postAjaxRequest_tests_insert=1';
                                    {/literal}{/if}{literal}
                                        new Ajax.Request(url, {
                                            method:'get',
                                            asynchronous:true,
                                            parameters: {file_id: id, editor_mode: tinyMCEmode},
                                            onSuccess: function (transport) {
                                                if(tinyMCEmode) {
                                                    tinyMCE.execInstanceCommand('mce_editor_0','mceInsertContent', false , transport.responseText);
                                                }else {
                                                    insertatcursor(window.document.question_form.description, transport.responseText);
                                                }
                                            }
                                        });
                                        }

                                    var file_manager_hidden = 1;
                                    function toggle_file_manager(){
                                        if(file_manager_hidden){
                                            $('filemanager_cell').insert($('filemanager_div'));
                                            $('filemanager_div').style.display = "block";
                                            $('arrow_down').src = "images/16x16/navigate_up.png";
                                            $('toggleeditor_cell2').insert($('toggleeditor_link'));
                                            $('open_manager').update('{/literal}{$smarty.const._CLOSEFILEMANAGER}{literal}');
                                            file_manager_hidden = 0;
                                        }else{
                                            $('filemanager_div').style.display = "none";
                                            $('fmInitial').insert($('filemanager_div'));
                                            $('toggleeditor_cell1').insert($('toggleeditor_link'));
                                            $('arrow_down').src = "images/16x16/navigate_down.png";
                                            $('open_manager').update('{/literal}{$smarty.const._OPENFILEMANAGER}{literal}');
                                            file_manager_hidden = 1;
                                        }
                                    }
                                        {/literal}
                                        </script>

                                            </div>
                                        {if $smarty.get.edit_test}
                                            <div id = 'random_questions' style = "border:1pz solid green;display:none;text-align:center">
                                                <table style = "width:10%;margin-left:auto;margin-right:auto;">
                                                    <tr><td class = "labelCell">{$smarty.const._LOWDIFFICULTY}:&nbsp;</td>
                                                        <td class = "elementCell" colspan = "2">{$T_RANDOM_FORM.random_low.html}</td></tr>
                                                    <tr><td class = "labelCell">{$smarty.const._MEDIUMDIFFICULTY}:&nbsp;</td>
                                                        <td class = "elementCell">{$T_RANDOM_FORM.random_medium.html}&nbsp;</td>
                                                        <td class = "elementCell" style = "width:100%">{$T_RANDOM_FORM.randomize.html}</td></tr>
                                                    <tr><td class = "labelCell">{$smarty.const._HIGHDIFFICULTY}:&nbsp;</td>
                                                        <td class = "elementCell" colspan = "2">{$T_RANDOM_FORM.random_high.html}</td></tr>
                                                </table>
                                            </div>
                                                                                        
                                            <div class = "tabbertab {if $smarty.get.tab == 'question' || $smarty.get.tab == 'questions'}tabbertabdefault{/if}" id = "test_questions" title = "{$smarty.const._TESTQUESTIONS}">
                                            <h3>{$smarty.const._TESTQUESTIONS}</h3>
                                                <table width="40%">
                                                    <tr><td>
                                                            <img src = "images/16x16/recycle.png" alt = "{$smarty.const._SELECTRANDOMQUESTIONS}" title = "{$smarty.const._SELECTRANDOMQUESTIONS}" style = "vertical-align:middle"/>
                                                            <a href = "javascript:void(0)" onclick = "eF_js_showDivPopup('{$smarty.const._SELECTRANDOMQUESTIONS}', 0, 'random_questions')" style = "vertical-align:middle">{$smarty.const._SELECTRANDOMQUESTIONS}</a>
                                                        </td>
                                                    
														{if !$T_SKILLGAP_TEST}
	                                                        <td class = "labelCell">{$smarty.const._RANDOMPOOLTEST}:&nbsp;</td>
	                                                        <td class = "elementCell">{$T_TEST_FORM.random_pool.html} {$smarty.const._QUESTIONS|@mb_strtolower}</td>
                                                        {/if}                                                        
                                                    </tr>
                                                </table>

                                                {literal}
                                                <script>
                                                    function randomize(el) {
                                                        Element.extend(el);
                                                        var low     = $('select_low').options[$('select_low').options.selectedIndex].value;
                                                        var medium = $('select_medium').options[$('select_medium').options.selectedIndex].value;
                                                        var high   = $('select_high').options[$('select_high').options.selectedIndex].value;
                                                        url = '{/literal}{$smarty.server.PHP_SELF}{literal}?ctg=tests&edit_test={/literal}{$smarty.get.edit_test}{literal}&ajax=randomize&low='+low+'&medium='+medium+'&high='+high;

                                                        if ($('progress_img')) {
                                                            $('progress_img').writeAttribute('src', 'images/others/progress1.gif').show();
                                                        } else {
                                                         	el.up().insert(new Element('img', {id:'progress_img', src:'images/others/progress1.gif'}).setStyle({position:'absolute'}));
                                                        }

                                                        new Ajax.Request(url, {
                                                                method:'get',
                                                                asynchronous:true,
                                                                onFailure: function (transport) {
                                                                    $('progress_img').writeAttribute({src:'images/16x16/delete.png', title:transport.responseText}).hide();                                                                    
                                                                    new Effect.Appear($('progress_img'));
                                                                    window.setTimeout('Effect.Fade("progress_img")', 10000);
                                                                },
                                                                onSuccess: function (transport) {
                                                                    $('progress_img').hide().setAttribute('src', 'images/16x16/check.png');
                                                                    new Effect.Appear($('progress_img'));
                                                                    window.setTimeout('Effect.Fade("progress_img")', 2500);
                                                                    eF_js_showDivPopup(false, false, 'random_questions');
	                                                            
	                                                            	// Reload the test users table
		                                                            tables = sortedTables.size();
		                                                            var i;
		                                                            for (i = 0; i < tables; i++) {
		                                                                if (sortedTables[i].id.match('questionsTable') && ajaxUrl[i]) {
		                                                                    eF_js_rebuildTable(i, 0, 'null', 'desc');
		                                                                }
		                                                            }
		                                                                    
                                                                    eF_js_changePage(1, 0);
                                                                }
                                                            });
                                                   	}
                                                   	
                                                    function ajaxSetRandomPool(id, el) {
                                                        Element.extend(el);
                                                        if (!el.value.match(/^\d*$/)) {
                                                        	alert('{/literal}{$smarty.const._THEFIELD} {$smarty.const._RANDOMPOOLTEST} {$smarty.const._MUSTBENUMERIC}{literal}');
                                                        	el.value = 0;
                                                        	return false;
                                                        } 
                                                        url = '{/literal}{$smarty.server.PHP_SELF}{literal}?ctg=tests&edit_test={/literal}{$smarty.get.edit_test}{literal}&ajax=set_random_pool&random_questions='+el.value;
                                                        if ($('progress_img')) {
                                                            $('progress_img').writeAttribute('src', 'images/others/progress1.gif').show();
                                                        } else {
                                                         	el.up().insert(new Element('img', {id:'rp_progress_img', src:'images/others/progress1.gif'}).setStyle({position:'absolute'}));
                                                        }

                                                        new Ajax.Request(url, {
                                                                method:'get',
                                                                asynchronous:true,
                                                                onFailure: function (transport) {
                                                                    $('rp_progress_img').writeAttribute({src:'images/16x16/delete.png', title:transport.responseText}).hide();                                                                    
                                                                    new Effect.Appear($('rp_progress_img'));
                                                                    window.setTimeout('Effect.Fade("rp_progress_img")', 10000);
                                                                },
                                                                onSuccess: function (transport) {
                                                                    $('rp_progress_img').hide().setAttribute('src', 'images/16x16/check.png');
                                                                    new Effect.Appear($('rp_progress_img'));
                                                                    window.setTimeout('Effect.Fade("rp_progress_img")', 2500);
                                                                }
                                                            });
                                                   	}	
                                                </script>
                                                {/literal}
{*This is the ajax table for the questions inside the edit test*}
<!--ajax:questionsTable-->
                                                <table class = "QuestionsListTable sortedTable" id = "questionsTable" size = "{$T_QUESTIONS_SIZE}" sortBy = "0" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=tests&edit_test={$smarty.get.edit_test}&">
                                                    <tr><td class = "topTitle" name = "text">{$smarty.const._QUESTIONTEXT}</td>
                                                    {*Exclude from skillgap tests*}
                                                    {if !$T_SKILLGAP_TEST}
                                                        <td class = "topTitle" name = "parent_name">{$smarty.const._UNITNAME}</td>
                                                    {else}
                                                        <td name="name" class = "topTitle">{$smarty.const._ASSOCIATEDWITH}</td>
                                                    {/if}
                                                        <td class = "topTitle centerAlign" name = "type">{$smarty.const._QUESTIONTYPE}</td>
                                                        <td class = "topTitle centerAlign" name = "difficulty">{$smarty.const._DIFFICULTY}</td>
                                                    {*Exclude from skillgap tests*}
                                                    {if !$T_SKILLGAP_TEST}
                                                        <td class = "topTitle centerAlign" name = "weight">{$smarty.const._QUESTIONWEIGHT}</td>
                                                    {/if}
                                                        <td class = "topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
                                                        <td class = "topTitle centerAlign" name = "partof">{$smarty.const._USEQUESTION}</td></tr>
                                            {foreach name = "questions_list" key = "key" item = "item" from = $T_UNIT_QUESTIONS}
                                                    <tr class = "{cycle name = "main_cycle" values="oddRowColor, evenRowColor"}">
                                                        <td><a  class = "editLink" href = "{$smarty.server.PHP_SELF}?ctg=tests&edit_question={$item.id}&question_type={$item.type}&lessonId={$item.lessons_ID}" title="{$item.text}"> {$item.text|eF_truncate:50}</a></td>
                                                    {*Exclude from skillgap tests*}
                                                    {if !$T_SKILLGAP_TEST}
                                                        <td>{if $item.parent_name}{$item.parent_name}{else}{$smarty.const._NONE}{/if}</td>
                                                    {else}
                                                        <td>{if $item.name && $item.name != $smarty.const._SKILLGAPTESTS}{$smarty.const._LESSON}:&nbsp;"{$item.name}"{else}{$smarty.const._SKILLGAPTESTS}{/if}</td>
                                                    {/if}
                                                        <td class = "centerAlign">
                                                            {if $item.type == 'match'}             <img src = "images/16x16/component.png"      title = "{$smarty.const._MATCH}"        alt = "{$smarty.const._MATCH}" />
                                                            {elseif $item.type == 'raw_text'}      <img src = "images/16x16/pens.png"           title = "{$smarty.const._RAWTEXT}"      alt = "{$smarty.const._RAWTEXT}" />
                                                            {elseif $item.type == 'multiple_one'}  <img src = "images/16x16/branch_element.png" title = "{$smarty.const._MULTIPLEONE}"  alt = "{$smarty.const._MULTIPLEONE}" />
                                                            {elseif $item.type == 'multiple_many'} <img src = "images/16x16/branch.png"         title = "{$smarty.const._MULTIPLEMANY}" alt = "{$smarty.const._MULTIPLEMANY}" />
                                                            {elseif $item.type == 'true_false'}    <img src = "images/16x16/yinyang.png"        title = "{$smarty.const._TRUEFALSE}"    alt = "{$smarty.const._TRUEFALSE}" />
                                                            {elseif $item.type == 'empty_spaces'}  <img src = "images/16x16/dot-chart.png"      title = "{$smarty.const._EMPTYSPACES}"  alt = "{$smarty.const._EMPTYSPACES}" />
                                                            {/if}
                                                            <span style = "display:none">{$item.type}</span>{*We put this here in order to be able to sort by type*}
                                                        </td>
                                                        <td class = "centerAlign">
                                                            {if $item.difficulty == 'low'}        <img src = "images/16x16/flag_green.png" title = "{$smarty.const._LOW}"    alt = "{$smarty.const._LOW}" />
                                                            {elseif $item.difficulty == 'medium'} <img src = "images/16x16/flag_blue.png"  title = "{$smarty.const._MEDIUM}" alt = "{$smarty.const._MEDIUM}" />
                                                            {elseif $item.difficulty == 'high'}   <img src = "images/16x16/flag_red.png"   title = "{$smarty.const._HIGH}"   alt = "{$smarty.const._HIGH}" />
                                                            {/if}
                                                            <span style = "display:none">{$item.difficulty}</span>{*We put this here in order to be able to sort by type*}
                                                        </td>
                                                    {if !$T_SKILLGAP_TEST}
                                                        <td class = "centerAlign">{$T_TEST_FORM.question_weight[$key].html}</td>
                                                    {/if}
                                                        <td class = "centerAlign">
                                                            <a href = "{$smarty.server.PHP_SELF}?ctg=tests&show_question={$item.id}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._PREVIEW}', new Array('750px', '500px'))"><img src = "images/16x16/view.png" alt = "{$smarty.const._PREVIEW}" title = "{$smarty.const._PREVIEW}" /></a>
                                                            {if $T_SKILLGAP_TEST}
                                                            <a href = "{$smarty.server.PHP_SELF}?ctg=tests&ctg=tests&edit_question={$item.id}&lessonId={$item.lessons_ID}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._CORRELATESKILLSTOQUESTION}', 2)"><img src = "images/16x16/gear_add.png" alt = "{$smarty.const._CORRELATESKILLSTOQUESTION}" title = "{$smarty.const._CORRELATESKILLSTOQUESTION}" /></a>
                                                            {/if}
                                                            <a href = "{$smarty.server.PHP_SELF}?ctg=tests&edit_question={$item.id}&question_type={$item.type}&lessonId={$item.lessons_ID}"><img src = "images/16x16/edit.png" alt = "{$smarty.const._EDIT}" title = "{$smarty.const._EDIT}"/></a>
                                                        </td>
                                                        <td class = "centerAlign">{$T_TEST_FORM.questions[$key].html}<span style = "display:none">{$T_TEST_FORM.questions[$key].value}</span></td> {*span is used for sorting*}
                                                    </tr>
                                            {foreachelse}
                                                    <tr class = "oddRowColor defaultRowHeight"><td class = "emptyCategory centerAlign" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
                                            {/foreach}
                                                </table>
<!--/ajax:questionsTable-->

                                    {literal}
                                    <script>
                                        // Wrapper function to distinguish between question and user assignment to tests posts
                                        function ajaxPost(id, el, table_id) {
                                            Element.extend(el);
                                            table_id == 'testUsersTable' ? usersAjaxPost(id, el, table_id) : questionsAjaxPost(id, el, table_id);
                                        }

                                        function questionsAjaxPost(id, el, table_id) {
                                            Element.extend(el);
                                            var baseUrl =  '{/literal}{$smarty.server.PHP_SELF}{literal}?ctg=tests&edit_test={/literal}{$smarty.get.edit_test}{literal}&postAjaxRequest=1';

                                            if (id) {
                                                if ($('weight_'+id)) {
                                                	if (el.id.match('checked_') && !$('checked_'+id).checked) {
                                                		var url      = baseUrl + '&question='+id+'&remove=1';
                                                	} else { 
	                                                    var weight = $('weight_'+id).options[$('weight_'+id).selectedIndex].value;
	                                                    var url      = baseUrl + '&question='+id+'&weight='+weight;
	                                                }    
                                                } else {
                                                	if (el.id.match('checked_') && !$('checked_'+id).checked) {
                                                    	var url      = baseUrl + '&question='+id+'&remove=1';
                                                    } else {
                                                    	var url      = baseUrl + '&question='+id;
                                                    }	
                                                }
                                                
                                            } else if (table_id && table_id == 'questionsTable') {
                                                el.checked ? url = baseUrl + '&addAll=1' : url = baseUrl + '&removeAll=1';
                                            }

                                            if ($('img_'+id)) {
                                                $('img_'+id).writeAttribute('src', 'images/others/progress1.gif').show();
                                            } else {
                                                el.up().insert(new Element('img', {id:'img_'+id, src:'images/others/progress1.gif'}).setStyle({position:'absolute'}));
                                            }

                                            new Ajax.Request(url, {
                                                    method:'get',
                                                    asynchronous:true,
                                                    onFailure: function (transport) {
                                                        img.writeAttribute({src:'images/16x16/delete.png', title:transport.responseText}).hide();
                                                        new Effect.Appear(img.identify());
                                                        window.setTimeout('Effect.Fade("'+img.identify()+'")', 10000);
                                                    },
                                                    onSuccess: function (transport) {
                                                        $('img_'+id).hide().setAttribute('src', 'images/16x16/check.png');
                                                        new Effect.Appear($('img_'+id));
                                                        window.setTimeout('Effect.Fade("'+('img_'+id)+'")', 2500);
                                                    }
                                                });
                                        }


                                        function  usersAjaxPost(login, el, table_id) {
                                            Element.extend(el);
                                            var baseUrl =  '{/literal}{$smarty.server.PHP_SELF}{literal}?ctg=tests&edit_test={/literal}{$smarty.get.edit_test}{literal}&postAjaxRequest=1';

                                            if (login) {
                                                var checked  = $('checked_'+login).checked;
                                                var url      = baseUrl + '&login='+login+"&insert="+checked;
                                                var img_id   = 'img_'+login;
                                            } else if (table_id && table_id == 'testUsersTable') {
                                                el.checked ? url = baseUrl + '&login=1&addAll=1' : url = baseUrl + '&login=1&removeAll=1';
                                                var img_id   = 'img_selectAll';
                                            }

                                            var position = eF_js_findPos(el);
                                            var img      = Element.extend(document.createElement("img"));

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
                                        }

                                        function  ajaxAssignAllNew(el) {
                                            Element.extend(el);
                                            var baseUrl =  '{/literal}{$smarty.server.PHP_SELF}{literal}?ctg=tests&edit_test={/literal}{$smarty.get.edit_test}{literal}&postAjaxRequest=1&auto_assign=' + el.checked;
                                            var img_id   = 'img_'+login;

                                            var position = eF_js_findPos(el);
                                            var img      = Element.extend(document.createElement("img"));

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
                                        }

                                        // Function to remove a solved test
                                        function  ajaxRemoveSolvedTest(el, login, completed_test_id, test_id) {
                                            Element.extend(el);
                                            var url =  '{/literal}{$smarty.server.PHP_SELF}{literal}?ctg=tests&delete_solved_test=' + completed_test_id + '&test_id=' + test_id + '&users_login=' + login + '&postAjaxRequest=1';
                                            var img_id   = 'img_'+login;

                                            var position = eF_js_findPos(el);
                                            var img      = Element.extend(document.createElement("img"));

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

                                                            // Reload the test users table
                                                            tables = sortedTables.size();
                                                            var i;
                                                            for (i = 0; i < tables; i++) {
                                                                if (sortedTables[i].id.match('testUsersTable') && ajaxUrl[i]) {
                                                                    eF_js_rebuildTable(i, 0, 'null', 'desc');
                                                                }
                                                            }

                                                            }
                                                    });
                                        }

                                        </script>
                                        {/literal}

                                        </div>
                                    {/if}


                                    {*Interface to ajax-assign users to a test*}
                                    {if $T_SKILLGAP_TEST && $smarty.get.edit_test}
                                    <div class = "tabbertab" id = "test_users" title = "{$smarty.const._USERS}">
<!--ajax:testUsersTable-->
                                        <table style = "width:100%" class = "sortedTable" size = "{$T_USERS_SIZE}" sortBy = "0" id = "testUsersTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}"  url = "{$smarty.server.PHP_SELF}?ctg=tests&edit_test={$smarty.get.edit_test}&">
                                            <tr class = "topTitle">
                                                <td class = "topTitle" name = "login">{$smarty.const._LOGIN}</td>
                                                <td class = "topTitle" name = "name">{$smarty.const._NAME}</td>
                                                <td class = "topTitle" name = "surname">{$smarty.const._SURNAME}</td>
                                                <td class = "topTitle centerAlign" name = "partof">{$smarty.const._CHECK}</td>
                                            </tr>
                                    {foreach name = 'users_to_lessons_list' key = 'key' item = 'user' from = $T_ALL_USERS}
                                            <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"}">
                                                <td>{$user.login}</td>
                                                <td>{$user.name}</td>
                                                <td>{$user.surname}</td>
                                                <td class = "centerAlign">
                                                    {if $user.solved == 1}
                                                    <a href = "{$smarty.server.PHP_SELF}?ctg=tests&show_solved_test={$user.completed_test_id}&test_analysis={$smarty.get.edit_test}&user={$user.login}"><img border="0" src="images/16x16/text_view.png" style="vertical-align:middle" alt="{$smarty.const._TESTSOLVEDVIEWTOSEESKILLGAPANALYSIS}" title="{$smarty.const._TESTSOLVEDVIEWTOSEESKILLGAPANALYSIS}" /></a>
                                                    <a href = "javascript:void(0);" onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) ajaxRemoveSolvedTest(this, '{$user.login}', '{$user.completed_test_id}','{$smarty.get.edit_test}');"/><img border="0" src="images/16x16/delete.png" style="vertical-align:middle" alt="{$smarty.const._DELETESKILLGAPTESTRECORD}" title="{$smarty.const._DELETESKILLGAPTESTRECORD}"/> </a>
                                                    {else}
                                                    <input class = "inputCheckbox" type = "checkbox" name = "checked_{$user.login}" id = "checked_{$user.login}" onclick = "ajaxPost('{$user.login}', this,'testUsersTable');" {if isset($user.tests_ID)}checked = "checked"{/if} />{if in_array($user.login, $T_LESSON_USERS)}<span style = "display:none">checked</span>{/if} {*Text for sorting*}
                                                    {/if}
                                                </td>
                                            </tr>
                                    {foreachelse}
                                            <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory centerAlign" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
                                    {/foreach}
                                        </table>
<!--/ajax:testUsersTable-->
                                    </div>
                                    {/if}


                                    </div>
                                {/capture}

                                {* Different icons and titles used between lesson and skillgap tests *}
                                {if $smarty.get.edit_test}
                                    {if $T_SKILLGAP_TEST}
                                        {eF_template_printInnerTable title = "`$smarty.const._OPTIONSFORSKILLGAPTEST` <span class = 'innerTableName'>&quot;`$T_CURRENT_TEST.name`&quot;</span>" data=$smarty.capture.t_edit_test_code image='32x32/pda_write.png'}
                                    {else}
                                        {eF_template_printInnerTable title = "`$smarty.const._OPTIONSFORTEST` <span class = 'innerTableName'>&quot;`$T_CURRENT_TEST.name`&quot;</span>" data = $smarty.capture.t_edit_test_code image = '32x32/document_edit.png'}
                                    {/if}

                                {elseif $smarty.get.add_test}
                                    {if $T_SKILLGAP_TEST}
                                        {eF_template_printInnerTable title = "`$smarty.const._ADDSKILLGAPTEST`" data=$smarty.capture.t_edit_test_code image='32x32/pda_write.png'}
                                    {else}
                                        {eF_template_printInnerTable title = $smarty.const._ADDTEST data = $smarty.capture.t_edit_test_code image = '32x32/document_edit.png'}
                                    {/if}
                                {/if}

                            {elseif $smarty.get.show_test || isset ($T_TEST_UNSOLVED)}
                                    {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?view_unit=`$T_CURRENT_TEST.content_ID`'>`$smarty.const._VIEWTEST`: `$T_CURRENT_TEST.name`</a>"}
                                    <table id="shown_test" width = "100%" align = "center" tyle = "border:1px solid black">
                                       {if !isset($smarty.get.popup)}
                                        <tr><td colspan = "2">
                                                <table>
                                                    <tr><td style = "border-right:1px solid black;">
                                                        <a href="{$smarty.server.PHP_SELF}?ctg=tests&{if !$T_SKILLGAP_TEST}edit_unit={$T_UNIT.id}{else}edit_test={$smarty.get.show_test}{/if}"><img border="0" src="images/16x16/edit.png" style="vertical-align:middle" alt="{$smarty.const._UPDATETEST}" title="{$smarty.const._UPDATETEST}"></a>
                                                        <a href="{$smarty.server.PHP_SELF}?ctg=tests&{if !$T_SKILLGAP_TEST}edit_unit={$T_UNIT.id}{else}edit_test={$smarty.get.show_test}{/if}" style = "vertical-align:middle">{$smarty.const._EDITTEST}</a>&nbsp;
                                                    </td><td style = "border-right:1px solid black;">
                                                        &nbsp;<a href="{$smarty.server.PHP_SELF}?ctg=tests&add_test=1"><img border="0" src="images/16x16/add2.png" style="vertical-align:middle" alt="{$smarty.const._CREATETEST}" title="{$smarty.const._CREATETEST}"></a>
                                                        <a href="{$smarty.server.PHP_SELF}?ctg=tests&add_test=1" style = "vertical-align:middle">{$smarty.const._CREATETEST}</a>&nbsp;
                                                    </td>
                                                    {*Exclude from skillgap tests - No units here*}
                                                    {if !$T_SKILLGAP_TEST}
                                                    <td>
                                                        &nbsp;<a href="{$smarty.server.PHP_SELF}?ctg=content&add_unit=1"><img border="0" src="images/16x16/add2.png" style="vertical-align:middle" alt="{$smarty.const._CREATEUNIT}" title="{$smarty.const._CREATEUNIT}"></a>
                                                        <a href="{$smarty.server.PHP_SELF}?ctg=content&add_unit=1" style = "vertical-align:middle">{$smarty.const._CREATEUNIT}</a>&nbsp;
                                                    </td>
                                                    {/if}
                                                    </tr>
                                                </table>
                                        </td></tr>
                                        <tr><td colspan = "2" class = "horizontalSeparator"></td></tr>
                                        <tr class = "defaultRowHeight"><td style = "text-align:right">{eF_template_printPreviousNext previous = $T_PREVIOUS_UNIT next = $T_NEXT_UNIT}</td></tr>
                                        {/if}
                                        <tr><td>
                                        {if $smarty.get.print}
                                        
                                        {literal}
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
                                            <table style = "width:100%;">
                                                <tr><td style = "padding-top:10px;padding-bottom:15px;text-align:center">
                                                    <input class = "flatButton" type = "submit" onClick = "printPartOfPage('shown_test');" value = "{$smarty.const._PRINTIT}"/>
                                                </td></tr>
                                            </table>
                                        {/if}
                                            {$T_TEST_UNSOLVED}
                                        </td></tr>
                                    </table>
                                <br/><br/>
                            {elseif $smarty.get.show_solved_test}
                                {if !$smarty.get.test_analysis}
                                    {$T_TEST_SOLVED}

                                    {* Skillgap hack to change the redo test link functionality*}
                                    {if $T_SKILLGAP_TEST}
                                    {literal}
                                    <script>
                                    $('redoLinkHref').href = "{/literal}{$smarty.session.s_type}{literal}.php?ctg=tests&delete_solved_test={/literal}{$smarty.get.show_solved_test}{literal}&test_id={/literal}{$T_TEST_DATA->test.id}{literal}&users_login={/literal}{$T_TEST_DATA->completedTest.login}{literal}";
                                    $('redoLinkHref').onclick = "";
                                    $('testAnalysisLinkHref').href = $('testAnalysisLinkHref').href + "&user={/literal}{$T_TEST_DATA->completedTest.login}{literal}";
                                    </script>
                                    {/literal}
                                    {/if}
                                {else}
                                    {if $T_SKILLGAP_TEST}

            {capture name = 't_user_code'}
            {literal}
            <script>
            // Wrapper used from the select_all method"
            function ajaxPost(id, el, table_id) {

                Element.extend(el);
                var baseUrl =  '{/literal}{$smarty.session.s_type}{literal}.php?ctg=users&edit_user={/literal}{$smarty.get.user}{literal}&postAjaxRequest=1';

                if (table_id == ('proposedLessonsTable')) {
                    if (id) {
                        var url = baseUrl + '&add_lesson=' + id + '&insert=' + document.getElementById('lesson_'+id).checked + '&user_type=student';
                        var img_id   = 'img_'+ id;
                    } else {


                        el.checked ? url = baseUrl + '&addAllLessonsFromTest={/literal}{$smarty.get.show_solved_test}{literal}' : url = baseUrl + '&removeAllFromTest=1';
                        url += '&add_lesson=1';
                        alltables = sortedTables.size();
                        var j;
                        for (j = 0; j < alltables; j++) {
                            if (sortedTables[j].id.match('proposedLessonsTable') && ajaxUrl[j]) {
                                // Get from the proposedLessonsTable all skills that are missing/existing according to the existing mapping (choices of admin in the first tab)
                                userId = "&user={/literal}{$smarty.get.user}{literal}";
                                url += ajaxUrl[j].substr(ajaxUrl[j].search(userId) + userId.length);
                                break;
                            }
                        }

                        var img_id   = 'img_selectAll';
                    }
                } else if (table_id == ('proposedCoursesTable')) {
                    if (id) {
                        var url = baseUrl + '&add_course=' + id + '&insert=' + document.getElementById('course_'+id).checked + '&user_type=student';
                        var img_id   = 'img_'+ id;
                    } else {
                        el.checked ? url = baseUrl + '&addAllCoursesFromTest={/literal}{$smarty.get.show_solved_test}{literal}' : url = baseUrl + '&removeAll=1';
                        url += '&add_course=1';
                        alltables = sortedTables.size();
                        var j;
                        for (j = 0; j < alltables; j++) {
                            if (sortedTables[j].id.match('proposedCoursesTable') && ajaxUrl[j]) {
                                // Get from the proposedCoursesTable all skills that are missing/existing according to the existing mapping (choices of admin in the first tab)
                                // NOTE: both tables have the same skill-set descriptions - we are differentiating them just for annotation reasons
                                userId = "&user={/literal}{$smarty.get.user}{literal}";
                                url += ajaxUrl[j].substr(ajaxUrl[j].search(userId) + userId.length);
                                break;
                            }
                        }

                        var img_id   = 'img_selectAll';
                    }
                } else if (table_id == ('assignedLessonsTable')) {
                    if (id) {
                        var url = baseUrl + '&add_lesson=' + id + '&insert=' + document.getElementById('lesson_'+id).checked + '&user_type=student';
                        var img_id   = 'img_'+ id;
                    } else {
                        el.checked ? url = baseUrl + '&addAll=1' : url = baseUrl + '&removeAll=1';
                        url += '&add_lesson=1';
                        var img_id   = 'img_selectAll';
                    }
                } else if (table_id == ('assignedCoursesTable')) {
                    if (id) {
                        var url = baseUrl + '&add_course=' + id + '&insert=' + document.getElementById('course_'+id).checked + '&user_type=student';
                        var img_id   = 'img_'+ id;
                    } else {
                        el.checked ? url = baseUrl + '&addAll=1' : url = baseUrl + '&removeAll=1';
                        url += '&add_course=1';
                        var img_id   = 'img_selectAll';
                    }
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

                            tables = sortedTables.size();
                            var i;
                            for (i = 0; i < tables; i++) {
                                if ( table_id.match('Lessons') && (sortedTables[i].id.match('LessonsTable'))  && ajaxUrl[i]) {
                                    // Both tables need to be updated after a lesson assignment/removal
                                    eF_js_rebuildTable(i, 0, 'null', 'desc');
                                } else if ( table_id.match('Courses') && (sortedTables[i].id.match('CoursesTable'))  && ajaxUrl[i]) {
                                    // Both tables need to be updated after a course assignment/removal
                                    eF_js_rebuildTable(i, 0, 'null', 'desc');
                                }
                            }
                        }
                    });
            }

            // Function that changes the colour of the corresponding skill bar and reloads the proposed lessons
            // if the corresponding parameter=true nad if the threshold has changed in a way that a possible change
            // in the proposed lessons might have been triggered
            function eF_thresholdChange(skillName, skillScore, reloadProposals) {


                if ($(skillName+'_threshold').value.match(/^\d{1,2}(\.\d{1,2})?$/)  ) {
                    previous_val = parseFloat($(skillName+'_previous_threshold').value);
                    new_val = parseFloat($(skillName+'_threshold').value);
                    skillScore = parseFloat(skillScore);
	
                    changed = 0;
                    if (skillScore >= previous_val  && new_val > skillScore) {
                        $(skillName + '_bar').setStyle({backgroundColor:'#FF0000'});
                        changed = 2;
                    } else if (skillScore < previous_val && new_val <= skillScore) {
                        $(skillName + '_bar').setStyle({backgroundColor:'#00FF00'});
                        changed = 1;
                    }

                    $(skillName+'_previous_threshold').value = $(skillName+'_threshold').value
    
                    if (changed) {
                        tables = sortedTables.size();
                        var i;
                        for (i = 0; i < tables; i++) {
                            if ((sortedTables[i].id.match('proposedLessonsTable') || sortedTables[i].id.match('proposedCoursesTable')) && ajaxUrl[i]) {
                                // We update the url of the ajaxed table and reload it. Keep in mind that ALL skills
                                // found from the analysis are initially contained in this string, with 1 or zero values
                                // denoting that they are missing or not from the user's skill list respectively '
                                // The changed var is used minus 1 to retrieve 0 for skill missing and 1 for skill not missing
                                ajaxUrl[i] = ajaxUrl[i].replace("&" + skillName + "=" + (2-changed), "&" + skillName + "=" + (changed-1));
                                //ajaxUrl[i] = "administrator.php?ctg=tests&show_solved_test={/literal}{$smarty.get.show_solved_test}{literal}&test_analysis={/literal}{$smarty.get.test_analysis}&user={$smarty.get.user}{literal}"
                                if(reloadProposals) {
                                    eF_js_rebuildTable(i, 0, 'null', 'desc');
                                }
                            }
                        }

                        return true;
                    }

                } else {
                    $(skillName+'_threshold').value = $(skillName+'_previous_threshold').value;
                }
                return false;
            }


            function eF_generalThresholdChange(newThreshold) {
            	// Acceptable formats: 2,23,23.1,23.10
				if (newThreshold.match(/^\d{1,2}(\.\d{1,2})?$/)  ) {
                    previous_val = parseFloat($('general_previous_threshold').value);
                    new_val = parseFloat($('shold').value);
                    skillScore = parseFloat(newThreshold);

                    // Change all thresholds to the new value and call the onChange function for each of them
                    var skillTableInputs = $('skillScoresTable').getElementsByTagName('input');                   //Get all the \"input\" elements of the skills table

                    anyChanges = false;
                    for (var i = 0; i < skillTableInputs.length; i++) {
                        funcName = skillTableInputs[i].getAttribute("onChange");
                        if (funcName) {
                            skillTableInputs[i].value = newThreshold;
                            funcValues = funcName.split("'");
                            temp = eF_thresholdChange(funcValues[1],funcValues[3], false);

                            if (!anyChanges) {
                                anyChanges = temp;

                            }
                        }
                    }
                    if (anyChanges) {
                        tables = sortedTables.size();
                        var i;
                        for (i = 0; i < tables; i++) {
                            if ((sortedTables[i].id.match('proposedLessonsTable') || sortedTables[i].id.match('proposedCoursesTable')) && ajaxUrl[i]) {
                                eF_js_rebuildTable(i, 0, 'null', 'desc');
                            }
                        }
                    }

                    $('general_previous_threshold').value = $('shold').value
                } else {
                    $('shold').value = $('general_previous_threshold').value;
                }
                return true;

            }
            </script>
            {/literal}
                <div class="tabber" >
                    <div class="tabbertab">
                        <h3>{$smarty.const._SKILLSCORES}</h3>
                        <table id="skillScoresTable" width="100%" border = "0" width = "100%"  class = "sortedTable" sortBy = "0">
                            <tr class = "topTitle">
                                <td class = "topTitle">{$smarty.const._SKILL}</td>
                                <td class = "topTitle">{$smarty.const._SCORE}</td>
                                <td class = "topTitle">{$smarty.const._THRESHOLD}</td>
                                {*<td class = "topTitle centerAlign">{$smarty.const._OPERATIONS}</td>*}
                            </tr>

                            {foreach name = 'skills_gap_list' key = 'key' item = 'skill' from = $T_SKILLSGAP}
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                                <td>{$skill.skill}</td>
                                <td class = "progressCell">
                                    <span style = "display:none">{$skill.score}</span>
                                    <span class = "progressNumber">#filter:score-{$skill.score}#%</span>
                                    <span id="{$skill.id}_bar" class = "progressBar" style = "background-color:{if $skill.score >= $T_TEST_DATA->options.general_threshold}#00FF00{else}#FF0000{/if};width:{$skill.score}px;">&nbsp;</span>&nbsp;
                                </td>

                                <td><input type="text" id="{$skill.id}_threshold" value="{$T_TEST_DATA->options.general_threshold}" onChange="eF_thresholdChange('{$skill.id}', '{$skill.score}',true)" />&nbsp;%<input type="hidden" id="{$skill.id}_previous_threshold" value = "{$T_TEST_DATA->options.general_threshold}" /></td>
                                {*<td>Score</td>*}
                            </tr>
                            {foreachelse}
                            <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory centerAlign" colspan = "100%">{$smarty.const._NOSKILLSCORRELATEDWITHTHETESTSQUESTIONS}</td></tr>
                            {/foreach}
                        </table>

                        {if $T_SKILLSGAP}
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
                                <div class="tabbertab">
                       <h3>{$smarty.const._LESSONS}</h3>

                        {* Proposed assignments (rrrrrrrrrrrrr')*}
<!--ajax:proposedLessonsTable-->
                        <table style = "width:100%" class = "sortedTable" size = "{$T_PROPOSED_LESSONS_SIZE}" sortBy = "0" id = "proposedLessonsTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "administrator.php?ctg=tests&show_solved_test={$smarty.get.show_solved_test}&test_analysis={$smarty.get.test_analysis}&user={$smarty.get.user}{$T_MISSING_SKILLS_URL}&">
                                        <tr class = "topTitle">
                                            <td class = "topTitle" name = "name">{$smarty.const._NAME} </td>
                                            <td class = "topTitle" name = "direction_name">{$smarty.const._CATEGORY}</td>
                                            <td class = "topTitle" name = "languages_NAME">{$smarty.const._LANGUAGE}</td>
                                        {* MODULE HCD: Prices are replaced by the number of skills offered *}
                                        {if $T_MODULE_HCD_INTERFACE}
                                            <td class = "topTitle centerAlign" name ="skills_offered">{$smarty.const._SKILLSOFFERED}</td>
                                        {else}
                                            <td class = "topTitle centerAlign" name = "price">{$smarty.const._PRICE}</td>
                                        {/if}
                                            <td class = "topTitle centerAlign">{$smarty.const._CHECK}</td>
                                        </tr>
                        {foreach name = 'lessons_list2' key = 'key' item = 'proposed_lesson' from = $T_PROPOSED_LESSONS_DATA}
                                        <tr id="row_{$proposed_lesson.id}" class = "{cycle values = "oddRowColor, evenRowColor"} {if !$proposed_lesson.active}deactivatedTableElement{/if}">
                                            <td id = "column_{$proposed_lesson.id}" class = "editLink">{$proposed_lesson.link}</td>
                                            <td>{$proposed_lesson.direction_name}</td>
                                            <td>{$proposed_lesson.languages_NAME}</td>
                                        {* MODULE HCD: Prices are replaced by the number of skills offered *}
                                        {if $T_MODULE_HCD_INTERFACE}
                                            <td align="center">{if $proposed_lesson.skills_offered == 0}{$smarty.const._NOSKILLSOFFERED}{else}{$proposed_lesson.skills_offered}{/if}</td>
                                        {else}
                                            <td align="center">{if $proposed_lesson.price == 0}{$smarty.const._FREE}{else}{$proposed_lesson.price} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}{/if}</td>
                                        {/if}

                                        {if $T_SKILLGAP_TEST && (!isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change')}
                                            <td class = "centerAlign">
                                                <input class = "inputCheckBox" type = "checkbox" id = "lesson_{$proposed_lesson.id}"  name = "lesson_{$proposed_lesson.id}"  onclick ="ajaxPost('{$proposed_lesson.id}', this,'proposedLessonsTable');">
                                            </td>
                                        {/if}

                                        </tr>

                        {foreachelse}
                                    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory centerAlign" colspan = "100%">{$smarty.const._NOLESSONSPROPOSEDACCORDINGTOANALYSIS}</td></tr>
                        {/foreach}
                    </table>
<!--/ajax:proposedLessonsTable-->
                            </div>
                            <div class="tabbertab">
                               <h3>{$smarty.const._COURSES}</h3>
<!--ajax:proposedCoursesTable-->
                                <table style = "width:100%" class = "sortedTable" size = "{$T_PROPOSED_COURSES_SIZE}" sortBy = "0" id = "proposedCoursesTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "administrator.php?ctg=tests&show_solved_test={$smarty.get.show_solved_test}&test_analysis={$smarty.get.test_analysis}&user={$smarty.get.user}{$T_MISSING_SKILLS_URL}&">
                                                <tr class = "topTitle">
                                                    <td class = "topTitle" name = "name">{$smarty.const._NAME} </td>
                                                    <td class = "topTitle" name = "direction_name">{$smarty.const._CATEGORY}</td>
                                                    <td class = "topTitle" name = "languages_NAME">{$smarty.const._LANGUAGE}</td>
                                                {* MODULE HCD: Prices are replaced by the number of skills offered *}
                                                {if $T_MODULE_HCD_INTERFACE}
                                                    <td class = "topTitle centerAlign" name ="skills_offered">{$smarty.const._SKILLSOFFERED}</td>
                                                {else}
                                                    <td class = "topTitle centerAlign" name = "price">{$smarty.const._PRICE}</td>
                                                {/if}
                                                    <td class = "topTitle centerAlign">{$smarty.const._CHECK}</td>
                                                </tr>
                                {foreach name = 'courses_list2' key = 'key' item = 'proposed_course' from = $T_PROPOSED_COURSES_DATA}
                                                <tr id="row_{$proposed_course.id}" class = "{cycle values = "oddRowColor, evenRowColor"} {if !$proposed_course.active}deactivatedTableElement{/if}">
                                                    <td id = "column_{$proposed_course.id}" class = "editLink">{$proposed_course.link}</td>
                                                    <td>{$proposed_course.direction_name}</td>
                                                    <td>{$proposed_course.languages_NAME}</td>
                                                {* MODULE HCD: Prices are replaced by the number of skills offered *}
                                                {if $T_MODULE_HCD_INTERFACE}
                                                    <td align="center">{if $proposed_course.skills_offered == 0}{$smarty.const._NOSKILLSOFFERED}{else}{$proposed_course.skills_offered}{/if}</td>
                                                {else}
                                                    <td align="center">{if $proposed_course.price == 0}{$smarty.const._FREE}{else}{$proposed_course.price} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}{/if}</td>
                                                {/if}

                                                {if $T_SKILLGAP_TEST && (!isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change')}
                                                    <td class = "centerAlign">
                                                        <input class = "inputCheckBox" type = "checkbox" id = "course_{$proposed_course.id}"  name = "course_{$proposed_course.id}"  onclick ="ajaxPost('{$proposed_course.id}', this,'proposedCoursesTable');">
                                                    </td>
                                                {/if}

                                                </tr>

                                {foreachelse}
                                            <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory centerAlign" colspan = "100%">{$smarty.const._NOCOURSESPROPOSEDACCORDINGTOANALYSIS}</td></tr>
                                {/foreach}
                            </table>
<!--/ajax:proposedCoursesTable-->

                            </div>
                        </div>
                    </div>

                    <div class="tabbertab">
                        <h3>{$smarty.const._ATTENDING}</h3>

                        <div class="tabber">
                            <div class="tabbertab">
                            <h3>{$smarty.const._LESSONS}</h3>

<!--ajax:assignedLessonsTable-->
                                <table style = "width:100%" class = "sortedTable" size = "{$T_ASSIGNED_LESSONS_SIZE}" sortBy = "0" id = "assignedLessonsTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "administrator.php?ctg=tests&show_solved_test={$smarty.get.show_solved_test}&test_analysis={$smarty.get.test_analysis}&user={$smarty.get.user}&">
                                                <tr class = "topTitle">
                                                    <td class = "topTitle" name = "name">{$smarty.const._NAME} </td>
                                                    <td class = "topTitle" name = "direction_name">{$smarty.const._CATEGORY}</td>
                                                    <td class = "topTitle" name = "languages_NAME">{$smarty.const._LANGUAGE}</td>
                                                {* MODULE HCD: Prices are replaced by the number of skills offered *}
                                                {if $T_MODULE_HCD_INTERFACE}
                                                    <td class = "topTitle centerAlign" name ="skills_offered">{$smarty.const._SKILLSOFFERED}</td>
                                                {else}
                                                    <td class = "topTitle centerAlign" name = "price">{$smarty.const._PRICE}</td>
                                                {/if}
                                                    <td class = "topTitle centerAlign">{$smarty.const._CHECK}</td>
                                                </tr>
                                {foreach name = 'lessons_list2' key = 'key' item = 'assigned_lesson' from = $T_ASSIGNED_LESSONS_DATA}
                                                <tr id="row_{$assigned_lesson.id}" class = "{cycle values = "oddRowColor, evenRowColor"} {if !$assigned_lesson.active}deactivatedTableElement{/if}">
                                                    <td id = "column_{$assigned_lesson.id}" class = "editLink">{$assigned_lesson.link}</td>
                                                    <td>{$assigned_lesson.direction_name}</td>
                                                    <td>{$assigned_lesson.languages_NAME}</td>
                                                {* MODULE HCD: Prices are replaced by the number of skills offered *}
                                                {if $T_MODULE_HCD_INTERFACE}
                                                    <td align="center">{if $assigned_lesson.skills_offered == 0}{$smarty.const._NOSKILLSOFFERED}{else}{$assigned_lesson.skills_offered}{/if}</td>
                                                {else}
                                                    <td align="center">{if $assigned_lesson.price == 0}{$smarty.const._FREE}{else}{$assigned_lesson.price} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}{/if}</td>
                                                {/if}

                                                {if $T_SKILLGAP_TEST && (!isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change')}
                                                    <td class = "centerAlign">
                                                        <input class = "inputCheckBox" type = "checkbox" id = "lesson_{$assigned_lesson.id}"  name = "lesson_{$assigned_lesson.id}"  onclick ="ajaxPost('{$assigned_lesson.id}', this,'assignedLessonsTable');" checked>
                                                    </td>
                                                {/if}

                                                </tr>
                                {foreachelse}
                                            <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory centerAlign" colspan = "100%">{$smarty.const._NOLESSONSFOUND}</td></tr>
                                {/foreach}
                            </table>
<!--/ajax:assignedLessonsTable-->
                           </div>

                            <div class="tabbertab">
                            <h3>{$smarty.const._COURSES}</h3>

<!--ajax:assignedCoursesTable-->
                                <table style = "width:100%" class = "sortedTable" size = "{$T_ASSIGNED_COURSES_SIZE}" sortBy = "0" id = "assignedCoursesTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "administrator.php?ctg=tests&show_solved_test={$smarty.get.show_solved_test}&test_analysis={$smarty.get.test_analysis}&user={$smarty.get.user}&">
                                                <tr class = "topTitle">
                                                    <td class = "topTitle" name = "name">{$smarty.const._NAME} </td>
                                                    <td class = "topTitle" name = "direction_name">{$smarty.const._CATEGORY}</td>
                                                    <td class = "topTitle" name = "languages_NAME">{$smarty.const._LANGUAGE}</td>
                                                {* MODULE HCD: Prices are replaced by the number of skills offered *}
                                                {if $T_MODULE_HCD_INTERFACE}
                                                    <td class = "topTitle centerAlign" name ="skills_offered">{$smarty.const._SKILLSOFFERED}</td>
                                                {else}
                                                    <td class = "topTitle centerAlign" name = "price">{$smarty.const._PRICE}</td>
                                                {/if}
                                                    <td class = "topTitle centerAlign">{$smarty.const._CHECK}</td>
                                                </tr>
                                {foreach name = 'courses_list2' key = 'key' item = 'assigned_course' from = $T_ASSIGNED_COURSES_DATA}
                                                <tr id="row_{$assigned_course.id}" class = "{cycle values = "oddRowColor, evenRowColor"} {if !$assigned_course.active}deactivatedTableElement{/if}">
                                                    <td id = "column_{$assigned_course.id}" class = "editLink">{$assigned_course.link}</td>
                                                    <td>{$assigned_course.direction_name}</td>
                                                    <td>{$assigned_course.languages_NAME}</td>
                                                {* MODULE HCD: Prices are replaced by the number of skills offered *}
                                                {if $T_MODULE_HCD_INTERFACE}
                                                    <td align="center">{if $assigned_course.skills_offered == 0}{$smarty.const._NOSKILLSOFFERED}{else}{$assigned_course.skills_offered}{/if}</td>
                                                {else}
                                                    <td align="center">{if $assigned_course.price == 0}{$smarty.const._FREE}{else}{$assigned_course.price} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}{/if}</td>
                                                {/if}

                                                {if $T_SKILLGAP_TEST && (!isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change')}
                                                    <td class = "centerAlign">
                                                        <input class = "inputCheckBox" type = "checkbox" id = "course_{$assigned_course.id}"  name = "course_{$assigned_course.id}"  onclick ="ajaxPost('{$assigned_course.id}', this,'assignedCoursesTable');" checked>
                                                    </td>
                                                {/if}

                                                </tr>
                                {foreachelse}
                                            <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory centerAlign" colspan = "100%">{$smarty.const._NOCOURSESFOUND}</td></tr>
                                {/foreach}
                            </table>
<!--/ajax:assignedCoursesTable-->
                            </div>
                        </div>
                    </div>

                </div>
            {/capture}

            {eF_template_printInnerTable title = $smarty.const._SKILLGAPANALYSISFORUSER|cat:'&nbsp;<i>'|cat:$T_USER_INFO.name|cat:'&nbsp;'|cat:$T_USER_INFO.surname|cat:'</i>&nbsp;'|cat:$smarty.const._ACCORDINGTOTEST|cat:'&nbsp;<i>'|cat:$T_TEST_DATA->test.name|cat:'</i>' data = $smarty.capture.t_user_code image = '/32x32/businessman.png'}








                                    {else}
                                        {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=tests&show_solved_test=`$T_TEST_DATA->completedTest.id`&test_analysis=1'>`$smarty.const._TESTANALYSISFORTEST` &quot;`$T_TEST_DATA->test.name`&quot;</a>"}

                                        {capture name = "t_test_analysis_code"}
                                            <div class = "headerTools">
                                            	<span>
                                                    <img src = "images/16x16/arrow_left_blue.png" alt = "{$smarty.const._VIEWSOLVEDTEST}" title = "{$smarty.const._VIEWSOLVEDTEST}">
                                                    <a href = "{$smarty.server.PHP_SELF}?ctg=tests&show_solved_test={$smarty.get.show_solved_test}">{$smarty.const._VIEWSOLVEDTEST}</a>
                                                </span>
											{if $T_TEST_STATUS.testIds|@sizeof > 1}
                                                <span>
                                                    <img src = "images/16x16/redo.png" alt = "{$smarty.const._JUMPTOEXECUTION}" title = "{$smarty.const._JUMPTOEXECUTION}">
													{$smarty.const._JUMPTOEXECUTION}
													<select  style = "vertical-align:middle" onchange = "location.toString().match(/show_solved_test/) ? location = location.toString().replace(/show_solved_test=\d+/, 'show_solved_test='+this.options[this.selectedIndex].value) : location = location + '&show_solved_test='+this.options[this.selectedIndex].value">
														{foreach name = "test_analysis_list" item = "item" key = "key" from = $T_TEST_STATUS.testIds}
															<option value = "{$item}" {if $smarty.get.show_solved_test == $item}selected{/if}>#{$smarty.foreach.test_analysis_list.iteration} - #filter:timestamp_time-{$T_TEST_STATUS.timestamps[$key]}#</option>
														{/foreach}
													</select>
                                                </span>
                                            {/if}
                                            </div>
                                            <table style = "width:100%">
                                                <tr><td style = "vertical-align:top">{$T_CONTENT_ANALYSIS}</td></tr>
                                                <tr><td style = "vertical-align:top"><iframe width = "750px" height = "550px" id = "analysis_frame" frameborder = "no" src = "{$smarty.server.PHP_SELF}?ctg=tests&show_solved_test={$smarty.get.show_solved_test}&test_analysis={$T_TEST_DATA->completedTest.id}&selected_unit={$smarty.get.selected_unit}&display_chart=1"></iframe></td></tr>
                                            </table>
                                        {/capture}

                                        {eF_template_printInnerTable title = "`$smarty.const._TESTANALYSIS` `$smarty.const._FORTEST` &quot;`$T_TEST_DATA->test.name`&quot; `$smarty.const._ANDUSER` &quot;`$T_TEST_DATA->completedTest.login`&quot;" data = $smarty.capture.t_test_analysis_code image='32x32/text_view.png'}
                                    {/if}
                                {/if}
                            {elseif $smarty.get.questions_order}

                                {capture name = 'questions_tree'}
                                    <ul id = "dhtmlgoodies_question_tree" class = "dhtmlgoodies_tree">
                                    {foreach name = 'questions_list' key = 'id' item = 'question'  from = $T_QUESTIONS}
                                        <li id = "dragtree_{$id}" noChildren = "true">
                                            <a class = "drag_tree_questions" href = "javascript:void(0)" onmouseover = "eF_js_showHideDiv(this, 'div_{$id}', event)" onmouseout = "$('div_{$id}').hide()">: {$question.text|eF_truncate:100}</a>
                                        </li>
                                    {/foreach}
                                    </ul>
                                    {foreach name = 'questions_list' key = 'id' item = 'question'  from = $T_QUESTIONS}
                                        {*We put this in a separate loop, since if it is put in the same loop as the <li> it causes tree to malfunction at submission*}
                                        <div id = "div_{$id}" style = "display:none;width:70%" class = "popUpInfoDiv">{$question.text}</div>
                                    {/foreach}
                                {/capture}

                                <table style = "width:100%">
                                    <tr><td class = "mediumHeader popUpInfoDiv">{$smarty.const._DRAGITEMSTOCHANGEQUESTIONSORDER}</td></tr>
                                    <tr><td>{$smarty.capture.questions_tree}</td></tr>
                                    <tr><td>&nbsp;</td></tr>
                                    <tr><td><input class = "flatButton" type="button" onclick="saveQuestionTree()" value="{$smarty.const._SAVECHANGES}"></td></tr>
                                </table>
                                <div id = "expand_collapse_div" style = "display:none"></div>

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
                                {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=tests&show_question=`$T_QUESTION.id`'>`$smarty.const._VIEWQUESTION`</a>"}
                                <br/>
                                    <table width = "100%" align = "center" tyle = "border:1px solid black">
                                        <tr><td>
                                            {$T_QUESTION_PREVIEW}
                                        </td></tr>
                                    </table>
                                <br/><br/>
                            {elseif $smarty.get.add_question || $smarty.get.edit_question}

                                <script>
                                <!--
                                {literal}

                                    bTextareaWasTinyfied = false; //this should be global, could be stored in a cookie...
                                    function setTextareaToTinyMCE(sEditorID) {
                                        var oEditor = document.getElementById(sEditorID);
                                        if(oEditor && !bTextareaWasTinyfied) {
                                            tinyMCE.execCommand('mceAddControl', true, sEditorID);
                                            bTextareaWasTinyfied = true;
                                        }
                                        return;
                                    }
                                    function unsetTextareaToTinyMCE(sEditorID) {
                                        var oEditor = document.getElementById(sEditorID);
                                        if(oEditor && bTextareaWasTinyfied) {
                                            tinyMCE.execCommand('mceRemoveControl', true, sEditorID);
                                            bTextareaWasTinyfied = false;
                                        }
                                        return;
                                    }

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

                                function eF_js_addAdditionalChoice(question_type) {
                                    var els = document.getElementsByTagName('input');           //Find all 'input' elements in th document.

                                    var counter = 0;
                                    for (var i = 0; i < els.length; i++) {                      //Count all the 'input' elements whose names match the selected question type (e.g. multiple_one[0], multiple_one[1] etc)
                                        if (els[i].name.match('^'+question_type) && els[i].type.match('text')) {
                                            counter++;
                                        }
                                    }

                                    if (counter > 1) {                                                  //If the counter is less than 2 (where 2 is the default input fields), it means that the selected question type is not one that may have multiple inputs (i.e. it may be raw_text)
                                        var last_node = document.getElementById(question_type+'_last_node');   //This is the node that the new elements will be inserted before

                                        var tr = document.createElement('tr');                          //Create a table row to hold the new element

                                        var td = document.createElement('td');                          //Create a new table cell to hold the new element
                                        tr.appendChild(td);                                             //Append this table cell to the table row we created above

                                        var input = document.createElement('input');                    //Create the new input element
                                        input.setAttribute('type', 'text');                             //Set the element to be text box
                                        input.className = 'inputText inputText_QuestionChoice';         //Set its class to 'inputText'
                                        input.setAttribute('name', question_type + '['+counter+']');    //We give the new textboxes names if the form multiple_one[0], multiple_one[1] so we can handle them alltogether
                                        td.appendChild(input);                                          //Append the text box to the table cell we created above

                                        if (question_type == 'multiple_one') {
                                            var option = document.createElement('option');              //Create a new option for the correct answer list
                                            option.setAttribute('value', counter);                      //This option is the next inserted
                                            option.innerHTML = counter + 1;
                                            document.getElementById('correct_multiple_one').appendChild(option); //The select box holding the right answer for multiple_one: adjust its size
                                        } else if (question_type == 'multiple_many') {
                                            var td_check = document.createElement('td');                //Create a new table cell to hold the new checkbox
                                            tr.appendChild(td_check);

                                            var check = document.createElement('input');
                                            check.setAttribute('type', 'checkbox');
                                            //check.setAttribute('class', 'inputCheckbox');
                                            check.className = 'inputCheckbox';
                                            check.setAttribute('name', 'correct_multiple_many['+counter+']');
                                            check.setAttribute('value', '1');
                                            td_check.appendChild(check);
                                        } else if (question_type == 'match') {
                                            var td_middle = document.createElement('td');               //Create a new table cell to hold the new raquos
                                            td_middle.innerHTML = '&nbsp;&raquo;&raquo;&nbsp;';
                                            var td_right  = document.createElement('td');               //Create a new table cell to hold the new text box
                                            tr.appendChild(td_middle);
                                            tr.appendChild(td_right);

                                            var check = document.createElement('input');
                                            check.setAttribute('type', 'text');
                                            check.setAttribute('class', 'inputText inputText_QuestionChoice');
                                            check.setAttribute('name', 'correct_match['+counter+']')
                                            td_right.appendChild(check);
                                        }

                                        var img = document.createElement('img');                        //Create an image element, that will hold the "delete" icon
                                        img.setAttribute('alt', '_REMOVECHOICE');                       //Set alt and title for this image
                                        img.setAttribute('title', '_REMOVECHOICE');
                                        img.setAttribute('src', 'images/16x16/delete.png');      //Set the icon source
                                        img.setAttribute('onclick', 'eF_js_removeImgNode(this, "'+question_type+'")');  //Set the event that will trigger the deletion
                                        img.onclick = function () {eF_js_removeImgNode(this, "'+question_type+'")};  //Set the event that will trigger the deletion
                                        var img_td = document.createElement('td');                      //Create a new table cell to hold the image element
                                        img_td.appendChild(img);                                        //Append the image to this cell
                                        tr.appendChild(img_td);                                         //Append the <td> to the row

                                        var parent_node = last_node.parentNode;                         //Find the parent element, that will hold the new element
                                        parent_node.insertBefore(tr, last_node);                        //Append the table row, that holds the input element, to its parent.
                                    }

                                }

                                //This function removes the <tr> element that contains the inserted node.
                                function eF_js_removeImgNode(el, question_type) {
                                    el.parentNode.parentNode.parentNode.removeChild(el.parentNode.parentNode);      //It is <tr><td><img></td></tr>, so we need to remove the <tr> element, which is the el.parentNode.parentNode

                                    var els = document.getElementsByTagName('input');           //Find all 'input' elements in th document.

                                    var counter = 0;
                                    for (var i = 0; i < els.length; i++) {                      //Count all the 'input' elements whose names match the selected question type (e.g. multiple_one[0], multiple_one[1] etc)
                                        if (els[i].name.match('^'+question_type) && els[i].type.match('text')) {
                                            els[i].name = question_type+'['+counter+']';        //If the user deletes a middle choice, the remaining choices will not be continuous. I.e, if we have choices multiple_one[0-5] and the user removes multiple_one[3], then the array will have indexes 0,1,2,4,5. So, here, we re-calculate the number of choices and apply new array indexes
                                            counter++;
                                        }
                                    }

                                    if (question_type == 'multiple_one') {                      //Adjust the select box accordingly
                                        document.getElementById('correct_multiple_one').removeChild(document.getElementById('correct_multiple_one').lastChild);
                                    } else if (question_type == 'multiple_many' || question_type == 'match') {               //For multiple/many (and match) questions, we need to reindex checkboxes (or answer text boxes) as well
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

                                    if (tinyMCE) {                                                              //Get the text from the editor
                                        var question_text = tinyMCE.getContent('question_text');
                                    } else {
                                        var question_text = document.getElementById('question_text').value;     //If the editor isn't set, get the question text from the text area
                                    }

                                    var excerpts = question_text.split(/###/g);                         //Get the question text pieces that are split by ###
                                //alert(excerpts);    alert(excerpts.length);
                                    var last_node = document.getElementById('empty_spaces_last_node');  //This is the node that the new elements will be inserted before
                                    var parent_node = last_node.parentNode;                             //Find the parent element, that will hold the new element

                                    if (document.getElementById('spacesRow')) {                         //If the button was pressed again, remove old row and build a new one
                                        document.getElementById('spacesRow').parentNode.removeChild(document.getElementById('spacesRow'));
                                    }

                                    var tr = document.createElement('tr');              //Create a table row to hold the new element
                                    tr.setAttribute('id', 'spacesRow');                 //We need an id to know which row this is, so we can remove it on demand
                                    tr.appendChild(document.createElement('td'));       //Create a new empty table cell for alignment reasons. Append this table cell to the table row we created above

                                    var td = document.createElement('td');              //Create a new table cell to hold the new element
                                    td.setAttribute('colspan', '100%');
                                    tr.appendChild(td);                                 //Append this table cell to the table row we created above

                                    for (var i = 0; i < excerpts.length; i++) {         //For each designated empty space, create a span element that will hold the text and the text boxes
                                        var span = document.createElement('span');
                                        span.innerHTML = excerpts[i];                   //Add each text piece to the span
                                        td.appendChild(span);                                        //Append the span to the table cell.

                                        if (i != excerpts.length - 1) {                                  //If, for example, we have 3 ###, these split the string to 4 parts. So, we must not insert a text box for the last (trailing) string piece
                                            var input = document.createElement('input');                 //Create the new input element
                                            input.setAttribute('type', 'text');                          //Set the element to be text box
                                            input.setAttribute('class', 'inputText');                    //Set its class to 'inputText'
                                            input.setAttribute('name', 'empty_spaces['+i+']');           //We give the new textboxes names if the form empty_spaces[0], empty_spaces[1] so we can handle them alltogether
                                            td.appendChild(input);                                       //Append the text box to the table cell we created above
                                        }
                                    }
                                    parent_node.insertBefore(tr, last_node);             //Append the table row, that holds the input element, to its parent.
                                }


                                {/literal}
                                //-->
                                </script>


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
                                                            <tr><td colspan = "2" class = "horizontalSeparator">&nbsp;</td></tr>
                                                            <tr><td colspan = "2">&nbsp;</td></tr>
                                                            {if $smarty.get.question_type == 'empty_spaces'}
                                                            <tr><td></td>
                                                                <td>{$smarty.const._EMPTYSPACESEXPLANATION}</td></tr>
                                                            {/if}

                                                            <tr>
                                                            <td>&nbsp;</td>
                                                            <td style = "vertical-align:middle" id="toggleeditor_cell1"><a title="{$smarty.const._OPENCLOSEFILEMANAGER}" href="javascript:void(0)" onclick="toggle_file_manager();" style = "vertical-align:middle"><img id="arrow_down" src="images/16x16/navigate_down.png" border="0" alt="{$smarty.const._OPENCLOSEFILEMANAGER}" title="{$smarty.const._OPENCLOSEFILEMANAGER}" style="vertical-align:middle"/>&nbsp;<span id="open_manager" style = "vertical-align:middle">{$smarty.const._OPENFILEMANAGER}</span></a>&nbsp;&nbsp;<a href="javascript:toogleEditorMode('mce_editor_1');" id="toggleeditor_link"><img src = "images/16x16/replace2.png" title = "{$smarty.const._TOGGLEHTMLEDITORMODE}" alt = "{$smarty.const._TOGGLEHTMLEDITORMODE}" style = "vertical-align:middle" border = "0"/>&nbsp;<span  style = "vertical-align:middle">{$smarty.const._TOGGLEHTMLEDITORMODE}</span></a></td></tr>
                                                            <tr><td></td><td id="filemanager_cell"></td></tr>
                                                            <tr><td></td><td id="toggleeditor_cell2"></td></tr>

                                                            <tr><td class = "labelCell">{$T_QUESTION_FORM.question_text.label}:&nbsp;</td>
                                                                <td class = "elementCell">{$T_QUESTION_FORM.question_text.html}</td></tr>
                                        {if $T_QUESTION_FORM.question_text.error}<tr><td></td><td class = "formError">{$T_QUESTION_FORM.question_text.error}</td></tr>{/if}

                                                            <tr><td colspan = "2" class = "horizontalSeparator">&nbsp;</td></tr>
                                                            <tr><td colspan = "2">&nbsp;</td></tr>
                                    {if $smarty.get.question_type == 'raw_text'}
                                                            <tr><td class = "labelCell">{$smarty.const._EXAMPLEANSWER}:&nbsp;</td>
                                                                <td class = "elementCell">{$T_QUESTION_FORM.example_answer.html}</td></tr>
                                        {if $T_QUESTION_FORM.example_answer.error}
                                                            <tr><td colspan = "2" class = "formError">{$T_QUESTION_FORM.example_answer.error}</td></tr>
                                        {/if}

                                    {elseif $smarty.get.question_type == 'true_false'}
                                                            <tr><td class = "labelCell">{$smarty.const._THISQUESTIONIS}:&nbsp;</td>
                                                                <td class = "elementCell">{$T_QUESTION_FORM.correct_true_false.html}</td></tr>
                                        {if $T_QUESTION_FORM.correct_true_false.error}
                                                            <tr><td colspan = "2" class = "formError">{$T_QUESTION_FORM.correct_true_false.error}</td></tr>
                                        {/if}

                                    {elseif $smarty.get.question_type == 'empty_spaces'}
                                                                <tr><td class = "labelCell"></td>
                                                                    <td class = "elementCell">{$T_QUESTION_FORM.generate_empty_spaces.html}</td></tr>
                                                                <tr><td></td>
                                                                    <td class = "infoCell">{$smarty.const._SEPARATEALTERNATIVESEXAMPLE}</td></tr>
                                                                <tr><td colspan = "2" >&nbsp;</td></tr>
                                                                <tr id = "spacesRow"><td></td><td>
                                        {foreach name = 'empty_spaces_list' key = key item = item from = $T_QUESTION_FORM.empty_spaces}
                                                                    {$T_EXCERPTS.$key} {$item.html}
                                                                    {if $item.error}{$item.error}{/if}
                                        {/foreach}
                                                                    {$T_EXCERPTS[$smarty.foreach.empty_spaces_list.iteration]}
                                                                    </td></tr>
                                                                <tr id = "empty_spaces_last_node"><td colspan = "2" >&nbsp;</td></tr>
                                    {elseif $smarty.get.question_type == 'multiple_one'}
                                                            <tr><td class = "labelCell questionLabel">{$smarty.const._INSERTMULTIPLEQUESTIONS}:</td>
                                                                <td><table>
                                        {foreach name = 'multiple_one_list' key = key item = item from = $T_QUESTION_FORM.multiple_one}
                                                                        <tr><td>{$item.html}</td>
                                            {if $smarty.foreach.multiple_one_list.iteration > 2}                  {*The if smarty.iteration is put here, so that the user cannot remove that last 2 rows *}
                                                                            <td><a href = "javascript:void(0)" onclick = "eF_js_removeImgNode(this, 'multiple_one')">
                                                                                    <img src = "images/16x16/delete.png" alt = "{$smarty.const._REMOVECHOICE}" title = "{$smarty.const._REMOVECHOICE}" />
                                                                                </a></td>
                                            {/if}
                                                                        </tr>
                                            {if $item.error}
                                                                        <tr><td></td><td class = "formError">{$item.error}</td></tr>
                                            {/if}
                                        {/foreach}
                                                                        <tr id = "multiple_one_last_node"></tr>
                                                                    </table>
                                                                </td></tr>
                                                            <tr><td class = "labelCell">
                                                                    <a href = "javascript:void(0)" onclick = "eF_js_addAdditionalChoice('multiple_one')"><img src = "images/16x16/add2.png" alt = "{$smarty.const._ADDQUESTION}" title = "{$smarty.const._ADDQUESTION}" border = "0"/></a>
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
                                                                            <td>
                                                {if $smarty.foreach.multiple_many_list.iteration > 2}             {*The if smarty.iteration is put here, so that the user cannot remove that last 2 rows *}
                                                                                <a href = "javascript:void(0)" onclick = "eF_js_removeImgNode(this, 'multiple_many')">
                                                                                    <img src = "images/16x16/delete.png" alt = "{$smarty.const._REMOVECHOICE}" title = "{$smarty.const._REMOVECHOICE}" /></a>
                                                {/if}
                                                                            </td></tr>
                                                {if $item.error}
                                                                        <tr><td></td><td class = "formError">{$item.error}</td></tr>
                                                {/if}
                                            {/foreach}
                                                                        <tr id = "multiple_many_last_node"></tr>
                                                                    </table>
                                                                </td></tr>
                                                                <tr><td class = "labelCell">
                                                                    <a href = "javascript:void(0)" onclick = "eF_js_addAdditionalChoice('multiple_many')"><img src = "images/16x16/add2.png" alt = "{$smarty.const._ADDQUESTION}" title = "{$smarty.const._ADDQUESTION}" border = "0"/></a>
                                                                </td><td>
                                                                    <a href = "javascript:void(0)" onclick = "eF_js_addAdditionalChoice('multiple_many')">{$smarty.const._ADDOPTION}</a>
                                                                </td></tr>

                                    {elseif $smarty.get.question_type == 'match'}
                                                            <tr><td class = "labelCell questionLabel">{$smarty.const._INSERTMATCHCOUPLES}:</td>
                                                                <td><table>
                                        {section name = 'match_list' loop = $T_QUESTION_FORM.match}
                                                                        <tr><td style = "width:1%;white-space:nowrap">{$T_QUESTION_FORM.match[match_list].html}</td>
                                                                            <td style = "width:1%;white-space:nowrap">&nbsp;&raquo;&raquo;&nbsp;</td>
                                                                            <td style = "width:1%;white-space:nowrap">{$T_QUESTION_FORM.correct_match[match_list].html}</td>
                                                                            <td>
                                                {if $smarty.section.match_list.iteration > 2}                   {*The if smarty.iteration is put here, so that the user cannot remove that last 2 rows*}
                                                                                <a href = "javascript:void(0)" onclick = "eF_js_removeImgNode(this, 'match')">
                                                                                    <img src = "images/16x16/delete.png" border = "no" alt = "{$smarty.const._REMOVECHOICE}" title = "{$smarty.const._REMOVECHOICE}" /></a>
                                                {/if}
                                                                            </td></tr>
                                                {if $T_QUESTION_FORM.match[match_list].error || $T_QUESTION_FORM.correct_match[match_list].error }
                                                                        <tr><td class = "formError">{$T_QUESTION_FORM.match[match_list].error}</td>
                                                                            <td>{$T_QUESTION_FORM.correct_match[match_list].error}</td></tr>
                                                {/if}
                                        {/section}
                                                                        <tr id = "match_last_node"></tr>
                                                                    </table>
                                                                </td></tr>
                                                                <tr><td class = "labelCell">
                                                                    <a href = "javascript:void(0)" onclick = "eF_js_addAdditionalChoice('match')"><img src = "images/16x16/add2.png" alt = "{$smarty.const._ADDQUESTION}" title = "{$smarty.const._ADDQUESTION}" border = "0"/></a>
                                                                </td><td>
                                                                    <a href = "javascript:void(0)" onclick = "eF_js_addAdditionalChoice('match')">{$smarty.const._ADDOPTION}</a>
                                                                </td></tr>
                                    {/if}
                                            <tr><td colspan = "2" class = "horizontalSeparator">&nbsp;</td></tr>
                                            <tr><td colspan = "2" >&nbsp;</td></tr>
                                            <tr><td></td><td class = "elementCell">
                                                    <img src = "images/16x16/add2.png" alt = "{$smarty.const._INSERTEXPLANATION}" title = "{$smarty.const._INSERTEXPLANATION}">
                                                    <a href = "javascript:void(0)" onclick = "eF_js_showHide('explanation');">{$smarty.const._INSERTEXPLANATION}</a></td></tr>
                                            <tr id = "explanation" {if !$T_HAS_EXPLANATION}style = "display:none"{/if}>
                                                <td class = "labelCell">{$T_QUESTION_FORM.explanation.label}:</td>
                                                <td class = "elementCell">{$T_QUESTION_FORM.explanation.html}</td></tr>
                                            {if $T_QUESTION_FORM.explanation.error}<tr><td></td><td class = "formError">{$T_QUESTION_FORM.explanation.error}</td></tr>{/if}
                                            <tr><td colspan = "2" >&nbsp;</td></tr>
                                            <tr><td></td><td class = "elementCell">
                                                {$T_QUESTION_FORM.submit_question.html}
                                            {if $smarty.get.add_question}
                                                &nbsp;{$T_QUESTION_FORM.submit_question_another.html}
                                            {else}
                                                &nbsp;{$T_QUESTION_FORM.submit_new_question.html}
                                            {/if}
                                                </td></tr>
                                </table>
                                <table><tr><td id="fmInitial">
                                                <div  id="filemanager_div" style="display:none;">
                                                    {$T_FILE_MANAGER}
                                                    <br/>
                                                </div>
                                                </td></tr>
                                </table>
                                <script type="text/javascript">
                                        {literal}
                                           var tinyMCEmode = true;
                                                function toogleEditorMode(sEditorID) {
                                                    try {
                                                        if(tinyMCEmode) {
                                                            tinyMCE.removeMCEControl(tinyMCE.getEditorId(sEditorID));
                                                            tinyMCEmode = false;
                                                        } else {
                                                            mceAddControlDynamic(sEditorID, 'question_text' ,'mceEditor');
                                                            tinyMCEmode = true;
                                                        }
                                                    } catch(e) {
                                                        alert('editor error');
                                                    }
                                                }

                                        function insertatcursor(myField, myValue) {

                                        if (document.selection) {
                                            myField.focus();
                                            sel = document.selection.createRange();
                                            sel.text = myValue;
                                        }
                                        else if (myField.selectionStart || myField.selectionStart == '0') {
                                            var startPos = myField.selectionStart;
                                            var endPos = myField.selectionEnd;
                                            myField.value = myField.value.substring(0, startPos)+ myValue+ myField.value.substring(endPos, myField.value.length);
                                        } else {
                                            myField.value += myValue;
                                        }
                                    }

                                    function insert_editor(element, id) {
                                    {/literal}{if !$smarty.get.edit_question}{literal}
                                        var url = '{/literal}{$T_BASENAME_PHPSELF}{literal}?ctg=tests&add_question=1&question_type={/literal}{$smarty.get.question_type}{literal}&postAjaxRequest_questions_insert=1';
                                    {/literal}{else}{literal}
                                        var url = '{/literal}{$T_BASENAME_PHPSELF}{literal}?ctg=tests&edit_question={/literal}{$smarty.get.edit_question}{literal}&question_type={/literal}{$smarty.get.question_type}{literal}&postAjaxRequest_questions_insert=1';
                                    {/literal}{/if}{literal}
                                        new Ajax.Request(url, {
                                            method:'get',
                                            asynchronous:true,
                                            parameters: {file_id: id, editor_mode: tinyMCEmode},
                                            onSuccess: function (transport) {
                                                if(tinyMCEmode) {
                                                    tinyMCE.execInstanceCommand('mce_editor_1','mceInsertContent', false , transport.responseText);
                                                }else {
                                                    insertatcursor(window.document.question_form.question_text, transport.responseText);
                                                }
                                            }
                                        });
                                        }

                                    var file_manager_hidden = 1;
                                    function toggle_file_manager(){
                                        if(file_manager_hidden){
                                            $('filemanager_cell').insert($('filemanager_div'));
                                            $('filemanager_div').style.display = "block";
                                            $('arrow_down').src = "images/16x16/navigate_up.png";
                                            $('toggleeditor_cell2').insert($('toggleeditor_link'));
                                            $('open_manager').update('{/literal}{$smarty.const._CLOSEFILEMANAGER}{literal}');
                                            file_manager_hidden = 0;
                                        }else{
                                            $('filemanager_div').style.display = "none";
                                            $('fmInitial').insert($('filemanager_div'));
                                            $('toggleeditor_cell1').insert($('toggleeditor_link'));
                                            $('arrow_down').src = "images/16x16/navigate_down.png";
                                            $('open_manager').update('{/literal}{$smarty.const._OPENFILEMANAGER}{literal}');
                                            file_manager_hidden = 1;
                                        }
                                    }

                                        {/literal}
                                        </script>

                                </form>
                            {/capture}

                    {* The edit_question menu can be in three modes: For lesson tests, for skillgap tests, and for skill correlation of skillgap *}
                    {* tests: the last is the popup version, where only the correlation table should appear *}
                    {if $T_SKILLGAP_TEST && !isset($smarty.get.popup)}
                    <div class="tabber" >
                        <div class="tabbertab">
                            <h3>{$smarty.const._QUESTIONINFO}</h3>
                    {/if}

                    {if !isset($smarty.get.popup)}
                        {eF_template_printInnerTable title = $smarty.const._QUESTIONINFO data = $smarty.capture.t_questions_info image = '/32x32/question_and_answer.png'}
                    {/if}

                    {if $T_SKILLGAP_TEST}
                        </div>
                        {if  $smarty.get.edit_question}
                            {if !isset($smarty.get.popup)}
                        <div class="tabbertab">
                            <h3>{$smarty.const._ASSOCIATEDSKILLS}</h3>
                            {/if}
                            {capture name='t_skills_to_questions'}
                            {literal}
                            <script>
                            function ajaxPost(id, el, table_id) {
                                Element.extend(el);
                                var baseUrl =  '{/literal}{$smarty.server.PHP_SELF}{literal}?ctg=tests&edit_question={/literal}{$smarty.get.edit_question}{literal}&postAjaxRequest=1';

                                if (id) {
                                    var relevance = $('skill_relevance_'+id).options[$('skill_relevance_'+id).selectedIndex].value;
                                    $('span_skill_relevance_'+id).innerHTML = relevance;
                                    if ($('skill_'+id).checked) {
                                    	$('span_skill_checked_'+id).innerHTML = 1;
                                        if (el.id == 'skill_relevance_'+id) {
                                            var url = baseUrl + '&skill='+id+'&insert=update&relevance='+relevance;
                                        } else {
                                            var url = baseUrl + '&skill='+id+'&insert=true&relevance='+relevance;
                                        }
                                    } else {
                                        if (el.id == 'skill_relevance_'+id) {
                                        	$('span_skill_checked_'+id).innerHTML = 1;
                                            var url = baseUrl + '&skill='+id+'&insert=true&relevance='+relevance;
                                        } else {
                                        	$('span_skill_checked_'+id).innerHTML = 0;
                                            var url = baseUrl + '&skill='+id+'&insert=false&relevance='+relevance;
                                        }
                                    }
                                   // var url      = baseUrl + '&skill='+id+'&insert='+$('skill_'+id).checked +'&relevance='+relevance;
                                } else if (table_id && table_id == 'questionSkillTable') {
                                    el.checked ? url = baseUrl + '&skill=1&addAll=1' : url = baseUrl + '&skill=1&removeAll=1';
                                }

                                if ($('img_'+id)) {
                                    $('img_'+id).writeAttribute('src', 'images/others/progress1.gif').show();
                                } else {     
                                    el.up().insert(new Element('img', {id:'img_'+id, src:'images/others/progress1.gif'}).setStyle({position:'absolute'}));
                                }

                                new Ajax.Request(url, {
                                        method:'get',
                                        asynchronous:true,
                                        onFailure: function (transport) {
                                            img.writeAttribute({src:'images/16x16/delete.png', title:transport.responseText}).hide();
                                            new Effect.Appear(img.identify());
                                            window.setTimeout('Effect.Fade("'+img.identify()+'")', 10000);
                                        },
                                        onSuccess: function (transport) {
                                            $('img_'+id).hide().setAttribute('src', 'images/16x16/check.png');
                                            new Effect.Appear($('img_'+id));
                                            window.setTimeout('Effect.Fade("'+('img_'+id)+'")', 2500);
                                        }
                                    });
                            }
                            </script>
                            {/literal}

                        <table id="questionSkillTable" width="100%" border = "0" width = "100%"  class = "sortedTable" sortBy = "0">
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
                                    <input class = "inputCheckBox" type = "checkbox" id = "skill_{$skill.skill_ID}"  name = "skill_{$skill.skill_ID}"  onclick ="ajaxPost('{$skill.skill_ID}', this, 'questionSkillTable');" {if isset($skill.questions_ID)}checked{/if}>
                                </td>
                            </tr>
                            {foreachelse}
                            <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory centerAlign" colspan = "6">{$smarty.const._NODATAFOUND}</td></tr>
                            {/foreach}
                        </table>

                        {/capture}
                        {eF_template_printInnerTable title = $smarty.const._CORRELATESKILLSTOQUESTION|cat:':&nbsp;<i>'|cat:$T_QUESTION_TEXT|cat:'</i>' data = $smarty.capture.t_skills_to_questions image = '/32x32/gear.png'}
                            {if !isset($smarty.get.popup)}
                            </div>
                            {/if}
                        {/if}

                        {if !isset($smarty.get.popup)}
                        </div>
                   </div>
                   {/if}
{/if}
                            {* Show results for all users of each specific*}
                            {elseif $smarty.get.test_results}
                                {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=tests&test_results=`$smarty.get.test_results`'>`$smarty.const._TESTRESULTS`</a>"}
                                {capture name = 't_test_results_code'}
                                    <table class = "sortedTable" style = "width:100%">
                                        <tr class="defaultRowHeight"><td class = "topTitle">{$smarty.const._USER}</td>
                                            {if !$T_SKILLGAP_TEST}
                                            <td class = "topTitle centerAlign">{$smarty.const._TIMESDONE}</td>
                                            {/if}
                                            <td class = "topTitle centerAlign">{$smarty.const._AVERAGESCORE}</td>
                                            <td class = "topTitle centerAlign">{$smarty.const._MAXSCORE}</td>
                                            <td class = "topTitle centerAlign">{$smarty.const._MINSCORE}</td>
                                            <td class = "topTitle centerAlign">{$smarty.const._FUNCTIONS}</td></tr>
                                    {foreach name = "questions_list" key = "key" item = "item" from = $T_DONE_TESTS}
                                        <tr class = "{cycle name = "main_cycle" values = "oddRowColor, evenRowColor"} defaultRowHeight">
                                            <td>{$key} ({$item.surname} {$item.name})</td>
                                            {if !$T_SKILLGAP_TEST}
                                            <td class = "centerAlign">{$item.times_done}</td>
                                            {/if}
                                            <td class = "centerAlign">#filter:score-{$item.average_score}#%</td>
                                            <td class = "centerAlign">#filter:score-{$item.max_score}#%</td>
                                            <td class = "centerAlign">#filter:score-{$item.min_score}#%</td>
                                            <td class = "centerAlign">
                                                <a href = "{$smarty.server.PHP_SELF}?ctg=tests&show_solved_test={$item.last_test_id}">
                                                    <img src = "images/16x16/view.png" alt = "{$smarty.const._VIEWTEST}" title = "{$smarty.const._VIEWTEST}" border = "0"/></a>
                                                <a href = "{$smarty.server.PHP_SELF}?ctg=tests&show_solved_test={$item.last_test_id}&test_analysis=1&user={$key}">
                                                    <img src = "images/16x16/text_view.png" alt = "{$smarty.const._TESTANALYSIS}" title = "{$smarty.const._TESTANALYSIS}" border = "0"/></a>
                                                {if !$T_SKILLGAP_TEST}
                                                <a href = "javascript:void(0)" onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) deleteAllTests(this, '{$key}')">
                                                    <img src = "images/16x16/delete.png" alt = "{$smarty.const._RESETALLTESTSSTATUS}" title = "{$smarty.const._RESETALLTESTSSTATUS}" border = "0"/></a>
                                                {else}
                                                <a href = "{$smarty.server.PHP_SELF}?ctg=tests&delete_solved_test={$item.last_test_id}&test_id={$smarty.get.test_results}&users_login={$key}" onclick = "return confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}');"/>
                                                    <img border="0" src="images/16x16/delete.png" style="vertical-align:middle" alt="{$smarty.const._DELETESKILLGAPTESTRECORD}" title="{$smarty.const._DELETESKILLGAPTESTRECORD}" </a>
                                                {/if}
                                            </td></tr>
                                    {foreachelse}
                                            <tr class = "oddRowColor defaultRowHeight"><td class = "emptyCategory centerAlign" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
                                    {/foreach}
                                    </table>
                                {/capture}
                                {literal}
                                    <script>
                                        function deleteAllTests(el, login) {
                                            Element.extend(el);
                                            url = "{/literal}{$smarty.server.PHP_SELF}{literal}?ctg=tests&test_results={/literal}{$smarty.get.test_results}{literal}&login="+login+"&ajax=1&reset_all=1";

                                            if ($("progress_img")) {
                                                $("progress_img").writeAttribute("src", "images/others/progress1.gif").show();
                                            } else {
                                                el.up().insert(new Element("img", {id:"progress_img", src:"images/others/progress1.gif"}).setStyle({verticalAlign:"middle", borderWidth:"0px"}));
                                            }
                                            new Ajax.Request(url, {
                                                method:"get",
                                                asynchronous:true,
                                                onFailure: function (transport) {
                                                    $("progress_img").writeAttribute({src:"images/16x16/delete.png", title:transport.responseText}).hide();
                                                    new Effect.Appear($("progress_img"));
                                                    window.setTimeout('Effect.Fade("progress_img")', 10000);
                                                },
                                                onSuccess: function (transport) {
                                                    new Effect.Fade(el.up().up());
                                                }
                                            });
                                        }
                                    </script>
                                {/literal}
                                {eF_template_printInnerTable title = $smarty.const._TESTRESULTS data = $smarty.capture.t_test_results_code image='32x32/text_marked.png'}
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
                                                        <td>{$recent_test.users_LOGIN}&nbsp;({$recent_test.surname}&nbsp;{$recent_test.name})</td>
                                                        <td class = "centerAlign">{if $recent_test.score}{$recent_test.score}%{else}0.00%{/if}</td>
                                                        <td align = "center">
                                                            <a href = "{$smarty.server.PHP_SELF}?ctg=tests&show_solved_test={$recent_test.id}">
                                                                <img src = "images/16x16/view.png" alt = "{$smarty.const._VIEWTEST}" title = "{$smarty.const._VIEWTEST}" border = "0"/></a>
                                                            <a href = "{$smarty.server.PHP_SELF}?ctg=tests&show_solved_test={$recent_test.id}&test_analysis=1&user={$recent_test.users_LOGIN}">
                                                                <img src = "images/16x16/text_view.png" alt = "{$smarty.const._TESTANALYSIS}" title = "{$smarty.const._TESTANALYSIS}" border = "0"/></a>
                                                            <a href = "{$smarty.server.PHP_SELF}?ctg=tests&delete_solved_test={$recent_test.id}&test_id={$recent_test.tests_ID}&users_login={$recent_test.users_LOGIN}" onclick = "return confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}');"/>
                                                                <img border="0" src="images/16x16/delete.png" style="vertical-align:middle" alt="{$smarty.const._DELETESKILLGAPTESTRECORD}" title="{$smarty.const._DELETESKILLGAPTESTRECORD}" </a>
                                                        </td>
                                                    </tr>
                                    {foreachelse}
                                                    <tr><td class = "emptyCategory oddRowColor" colspan = "100%" style = "text-align:center">{$smarty.const._NOCOMPLETEDSKILLGAP}</td></tr>
                                    {/foreach}
                                                </table>

                                {/capture}
                                {eF_template_printInnerTable title=$smarty.const._SKILLGAPTESTS data=$smarty.capture.t_recently_completed image='32x32/pda2_preferences.png'}

                            {else}
                                    {capture name = "t_tests_and_questions_code"}
                                        {if $T_SET_CONTENT}
                                                        <table class = "formElements">
                                                            <tr><td class = "labelCell">{$smarty.const._SHOWDATAFORUNIT}:&nbsp;</td>
                                                                <td class = "elementCell">
                                                                    <select name = "select_unit" onchange = "var tab = 'tests';{literal}$$('div.tabbertab').each(function (s) {if (!s.hasClassName('tabbertabhide')) {tab = s.id;} });{/literal}document.location='{$smarty.server.PHP_SELF}?ctg=tests&from_unit='+this.options[this.selectedIndex].value+'&tab='+tab">
                                                                        <option value = "-1" {if $smarty.get.from_unit == -1}selected{/if}>{$smarty.const._ALLUNITS}</option>
                                                                        <option value = "-2">-----------</option>
                                                                    {foreach name = 'unit_options' key = 'id' item = 'unit' from = $T_UNITS}
                                                                        <option value = "{$id}" {if $id == $smarty.get.from_unit}selected{/if}>{$unit}</option>
                                                                    {/foreach}
                                                                    </select>
                                                                </td></tr>
                                                        </table>
                                        {/if}
                                        {capture name = 't_tests_code'}
                                                    {if !isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change'}
                                                        <table class = "headerTools">
                                                        	<tr><td><img src = "images/16x16/add2.png" title = "{if $T_SKILLGAP_TEST}{$smarty.const._ADDSKILLGAPTEST}{else}{$smarty.const._ADDTEST}{/if}" alt = "{if $T_SKILLGAP_TEST}{$smarty.const._ADDSKILLGAPTEST}{else}{$smarty.const._ADDTEST}{/if}"/></td>
                                                        		<td><a href = "{$smarty.server.PHP_SELF}?ctg=tests&add_test=1{if $smarty.get.view_unit}&view_unit={$smarty.get.view_unit}{/if}">{if $T_SKILLGAP_TEST}{$smarty.const._ADDSKILLGAPTEST}{else}{$smarty.const._ADDTEST}{/if}</a></td>
                                                        	</tr>	
                                                        </table>
                                                    {/if}

                                                        <table width = "100%" class = "sortedTable">
                                                            <tr class = "defaultRowHeight">
                                                                <td class = "topTitle">{$smarty.const._NAME}</td>
                                                            {*Exclude from skillgap tests*}
                                                            {if !$T_SKILLGAP_TEST}
                                                                <td class = "topTitle">{$smarty.const._UNITPARENT}</td>
				                                            {else}
				                                            <td class = "topTitle centerAlign">{$smarty.const._GENERALTHRESHOLD}</td>
				                                            {/if}                                                                

																<td class = "topTitle centerAlign">{$smarty.const._PUBLISHED}</td>
                                                                <td class = "topTitle centerAlign">{$smarty.const._QUESTIONS}</td>
                                                                <td class = "topTitle centerAlign">{$smarty.const._AVERAGESCORE}</td>
                                                                <td class = "topTitle centerAlign noSort">{$smarty.const._FUNCTIONS}</td>
                                                            </tr>
                                            {foreach name = 'tests_list' key = "key" item = "test" from = $T_TESTS}
                                                            <tr class = "{cycle name = "main_cycle" values="oddRowColor,evenRowColor"} defaultRowHeight">
                                                                <td><a class = "editLink" href = "{$smarty.server.PHP_SELF}?ctg=tests&edit_test={$test.id}">{$test.name|eF_truncate:40}</a></td>
                                                            {*Exclude from skillgap tests*}
                                                            {if !$T_SKILLGAP_TEST}
                                                                <td>{$test.parent_unit}</td>
                                                            {else}    
                                                                <td class = "centerAlign">{$test.options.general_threshold}%</td>
                                                            {/if}
                                                                <td class = "centerAlign"><a href = "javascript:void(0)" onclick = "publish(this, {$test.id})">{if $test.publish}<img src = "images/16x16/check.png" alt = "{$smarty.const._PUBLISHED}" title = "{$smarty.const._PUBLISHED}">{else}<img src = "images/16x16/forbidden.png" alt = "{$smarty.const._NOTPUBLISHED}" title = "{$smarty.const._NOTPUBLISHED}">{/if}</a></td>
                                                                <td class = "centerAlign">{$test.questions_num}</td>
                                                                <td class = "centerAlign">{if isset($test.average_score) || $test.average_score === 0}#filter:score-{$test.average_score}#&nbsp;%{else}-{/if}</td>
                                                                <td style = "white-space:nowrap;text-align:center">
                                                                    <a href = "{$smarty.server.PHP_SELF}?ctg=tests&test_results={$test.id}"><img src = "images/16x16/text_marked.png" alt = "{$smarty.const._RESULTS}" title = "{$smarty.const._RESULTS}" border = "0"/></a>
                                                                    <a href = "{$smarty.server.PHP_SELF}?{if !$T_SKILLGAP_TEST}view_unit={$test.content_ID}{else}ctg=tests&show_test={$test.id}{/if}"><img src = "images/16x16/view.png" alt = "{$smarty.const._PREVIEW}" title = "{$smarty.const._PREVIEW}" border = "0"/></a>
                                                                    <a href = "{$smarty.server.PHP_SELF}?ctg=tests&show_test={$test.id}&print=1&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._PRINT}', 2)"><img src = "images/16x16/printer.png" alt = "{$smarty.const._PRINT}" title = "{$smarty.const._PRINT}" border = "0"/></a>
                                                                {if !isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change'}
                                                                    {if !$test.options.shuffle_questions && !$test.options.random_pool}
                                                                    	{if $T_SKILLGAP_TEST && isset($T_CURRENT_USER->coreAccess.skillgaptests) && $T_CURRENT_USER->coreAccess.skillgaptests != 'change'}
																			<a href = "javascript:void(0)"><img src = "images/16x16/replace2_gray.png" alt = "{$smarty.const._UNAVAILABLEOPTION}" title = "{$smarty.const._UNAVAILABLEOPTION}"/></a>
																		{else}                                                                    
                                                                    		<a href = "{$smarty.server.PHP_SELF}?ctg=tests&questions_order={$test.id}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._CHANGEORDER}', 2)"><img src = "images/16x16/replace2.png" alt = "{$smarty.const._CHANGEORDER}" title = "{$smarty.const._CHANGEORDER}"/></a>
                                                                    	{/if}	
                                                                    {else}
                                                                    <a href = "javascript:void(0)"><img src = "images/16x16/replace2_gray.png" alt = "{$smarty.const._UNAVAILABLEOPTION}" title = "{$smarty.const._UNAVAILABLEOPTION}"/></a>
                                                                    {/if}
                                                                    <a class = "editLink"    href = "{$smarty.server.PHP_SELF}?ctg=tests&edit_test={$test.id}"><img src = "images/16x16/edit.png" alt = "{$smarty.const._EDIT}" title = "{$smarty.const._EDIT}" border = "0"/></a>
                                                                    <a class = "deleteLink"  href = "javascript:void(0)" onclick = "if (confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) deleteTest(this, '{$test.id}');"><img src = "images/16x16/delete.png" alt = "{$smarty.const._DELETE}" title = "{$smarty.const._DELETE}" border = "0"/></a>
                                                                {/if}
                                                                    </td></tr>
                                            {foreachelse}
                                                            <tr class = "defaultRowHeight oddRowColor"><td colspan = "100%" class = "emptyCategory centerAlign">{$smarty.const._NOTESTSSETFORTHISUNIT}</td></tr>
                                            {/foreach}
                                                        </table>
                                                        <script>
                                                        {literal}
                                                        function deleteTest(el, id) {
                                                            Element.extend(el);
                                                            var url = '{/literal}{$smarty.server.PHP_SELF}{literal}?ctg=tests&delete_test='+id;
                                                            el.down().src = "images/others/progress1.gif";

                                                            new Ajax.Request(url, {
                                                                method:"get",
                                                                asynchronous:true,
                                                                onFailure: function (transport) {
                                                                    el.down().writeAttribute({src:"images/16x16/delete.png", title:transport.responseText}).hide();
                                                                    new Effect.Appear(el.down());
                                                                    window.setTimeout('Effect.Fade("'+el.down().identify()+'")', 10000);
                                                                },
                                                                onSuccess: function (transport) {
                                                                    new Effect.Fade(el.up().up());
                                                                    location.href=location.href;
                                                                }
                                                            });

                                                        }
                                                        function publish(el, id) {
                                                            Element.extend(el);
                                                            var url = '{/literal}{$smarty.server.PHP_SELF}{literal}?ctg=tests&publish_test='+id;
                                                            el.down().src = "images/others/progress1.gif";
                                                            new Ajax.Request(url, {
                                                                method:"get",
                                                                asynchronous:true,
                                                                onFailure: function (transport) {
                                                                    el.down().writeAttribute({src:"images/16x16/delete.png", title:transport.responseText}).hide();
                                                                    new Effect.Appear(el.down());
                                                                    window.setTimeout('Effect.Fade("'+el.down().identify()+'")', 10000);
                                                                },
                                                                onSuccess: function (transport) {
                                                                	el.down().hide();
                                                                    if (transport.responseText == 1) {
                                                                    	el.down().src = "images/16x16/check.png";
                                                                    	el.down().alt = el.down().title = '{/literal}{$smarty.const._PUBLISHED}{literal}';
                                                                    } else {
                                                                    	el.down().src = "images/16x16/forbidden.png";
                                                                    	el.down().alt = el.down().title = '{/literal}{$smarty.const._NOTPUBLISHED}{literal}';
                                                                    }
                                                                    new Effect.Appear(el.down()); 
                                                                }
                                                            });
                                                        }
                                                        {/literal}
                                                        </script>
                                        {/capture}

                                        {capture name = 't_questions_code'}
                                                    {if !isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change'}
                                                        <table class = "headerTools">
                                                        	<tr><td><img src = "images/16x16/add2.png" title = "{$smarty.const._ADDQUESTION}" alt = "{$smarty.const._ADDQUESTION}"/></td>
                              									<td>{$T_QUESTION_TYPE.question_type.html}</td>                          	
                                                        	</tr>
                                                        </table>
                                                    {/if}
                                                        <form name = "questions_form" method = "post" action = "{$smarty.server.PHP_SELF}?ctg=tests&tab=question">

<!--ajax:questionsTable-->
                                                <table class = "QuestionsListTable sortedTable" id = "questionsTable" size = "{$T_QUESTIONS_SIZE}" sortBy = "0" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=tests&from_unit={$smarty.get.from_unit}&">
                                                            <tr class = "defaultRowHeight">
                                                                <td name="text" class = "topTitle">{$smarty.const._QUESTION}</td>
                                                            {*Exclude from skillgap tests*}
                                                            {if !$T_SKILLGAP_TEST}
                                                                <td name="parent_unit" class = "topTitle">{$smarty.const._UNIT}</td>
                                                            {else}
                                                                <td name="name" class = "topTitle">{$smarty.const._ASSOCIATEDWITH}</td>
                                                            {/if}
                                                                <td name="type" class = "topTitle centerAlign">{$smarty.const._QUESTIONTYPE}</td>
                                                                <td name="difficulty" class = "topTitle centerAlign">{$smarty.const._DIFFICULTY}</td>
                                                                <td class = "topTitle centerAlign noSort">{$smarty.const._FUNCTIONS}</td>
                                                            {*if !isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change'}
                                                                <td class = "topTitle centerAlign noSort">{$smarty.const._SELECT}</td>
                                                            {/if*}
                                                            </tr>
                                            {foreach name = 'questions_list' key = 'key' item = 'question' from = $T_QUESTIONS}
                                                            <tr class = "{cycle name = "main_cycle" values="oddRowColor,evenRowColor"} defaultRowHeight">
                                                                <td>
                                                            {if !isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change'}
                                                                    <a class = "editLink" href = "{$smarty.server.PHP_SELF}?ctg=tests&edit_question={$question.id}&question_type={$question.type}&lessonId={$question.lessons_ID}" title= "{$question.text}">{$question.text|eF_truncate:70}</a>
                                                            {else}
                                                                    {$question.text|eF_truncate:70}
                                                            {/if}
                                                                </td>
                                                            {*Exclude from skillgap tests*}
                                                            {if !$T_SKILLGAP_TEST}
                                                                <td>{$question.parent_unit}</td>
                                                            {else}
                                                                <td>{if $question.name && $question.name != $smarty.const._SKILLGAPTESTS}{$smarty.const._LESSON}:&nbsp;"{$question.name}"{else}{$smarty.const._SKILLGAPTESTS}{/if}</td>
                                                            {/if}
                                                                <td class = "centerAlign">
                                                                    {if $question.type == 'match'}             <img src = "images/16x16/component.png"      title = "{$smarty.const._MATCH}"        alt = "{$smarty.const._MATCH}" />
                                                                    {elseif $question.type == 'raw_text'}      <img src = "images/16x16/pens.png"           title = "{$smarty.const._RAWTEXT}"      alt = "{$smarty.const._RAWTEXT}" />
                                                                    {elseif $question.type == 'multiple_one'}  <img src = "images/16x16/branch_element.png" title = "{$smarty.const._MULTIPLEONE}"  alt = "{$smarty.const._MULTIPLEONE}" />
                                                                    {elseif $question.type == 'multiple_many'} <img src = "images/16x16/branch.png"         title = "{$smarty.const._MULTIPLEMANY}" alt = "{$smarty.const._MULTIPLEMANY}" />
                                                                    {elseif $question.type == 'true_false'}    <img src = "images/16x16/yinyang.png"        title = "{$smarty.const._TRUEFALSE}"    alt = "{$smarty.const._TRUEFALSE}" />
                                                                    {elseif $question.type == 'empty_spaces'}  <img src = "images/16x16/dot-chart.png"      title = "{$smarty.const._EMPTYSPACES}"  alt = "{$smarty.const._EMPTYSPACES}" />
                                                                    {/if}
                                                                    <span style = "display:none">{$question.type}</span>
                                                                </td>
                                                                <td class = "centerAlign">
                                                                    {if $question.difficulty == 'low'}        <img src = "images/16x16/flag_green.png" title = "{$smarty.const._LOW}"    alt = "{$smarty.const._LOW}" />
                                                                    {elseif $question.difficulty == 'medium'} <img src = "images/16x16/flag_blue.png"  title = "{$smarty.const._MEDIUM}" alt = "{$smarty.const._MEDIUM}" />
                                                                    {elseif $question.difficulty == 'high'}   <img src = "images/16x16/flag_red.png"   title = "{$smarty.const._HIGH}"   alt = "{$smarty.const._HIGH}" />
                                                                    {/if}
                                                                    <span style = "display:none">{$question.difficulty}</span>
                                                                </td>
                                                                <td class = "centerAlign">
                                                                    <a href = "{$smarty.server.PHP_SELF}?ctg=tests&show_question={$question.id}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._PREVIEW}', 1)"><img src = "images/16x16/view.png" alt = "{$smarty.const._PREVIEW}" title = "{$smarty.const._PREVIEW}" /></a>
                                                                {if !isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change'}
                                                                    {if $T_SKILLGAP_TEST && (!isset($T_CURRENT_USER->coreAccess.skillgaptests) || $T_CURRENT_USER->coreAccess.skillgaptests == 'change')}
                                                                    <a href = "{$smarty.server.PHP_SELF}?ctg=tests&ctg=tests&edit_question={$question.id}&lessonId={$question.lessons_ID}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._CORRELATESKILLSTOQUESTION}', 2)"><img src = "images/16x16/gear_add.png" alt = "{$smarty.const._CORRELATESKILLSTOQUESTION}" title = "{$smarty.const._CORRELATESKILLSTOQUESTION}" /></a>
                                                                    {/if}
                                                                    <a class = "editLink"   href = "{$smarty.server.PHP_SELF}?ctg=tests&edit_question={$question.id}&question_type={$question.type}&lessonId={$question.lessons_ID}"><img src = "images/16x16/edit.png" alt = "{$smarty.const._CORRECTION}" title = "{$smarty.const._CORRECTION}"/></a>
                                                                    <a class = "deleteLink" href = "{$smarty.server.PHP_SELF}?ctg=tests&delete_question={$question.id}&delete=true" onclick = "return confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')"><img src = "images/16x16/delete.png" alt = "{$smarty.const._DELETE}" title = "{$smarty.const._DELETE}" /></a>
                                                                {/if}
                                                                </td>
                                                                {*if !isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change'}
                                                                    <td class = "centerAlign"><input type = "checkbox" name = "questions[{$question.id}]" value = "{$question.id}" class = "inputCheckbox" /></td>
                                                                {/if*}
                                                            </tr>
                                            {foreachelse}
                                                            <tr class = "oddRowColor defaultRowHeight"><td class = "emptyCategory centerAlign" colspan = "100%">{$smarty.const._NOQUESTIONSSETFORTHISUNIT}</td></tr>
                                            {/foreach}
                                                        </table>
<!--/ajax:questionsTable-->
                                            {if $T_QUESTIONS && (!isset($T_CURRENT_USER->coreAccess.content) || $T_CURRENT_USER->coreAccess.content == 'change')}
                                                        <table style = "width:100%">
                                                            <tr><td class = "horizontalSeparatorAbove" style = "vertical-align:middle">{$smarty.const._WITHSELECTED}:
                                                                <span>
                                                                    <input name = "delete_selected"   type = "image" src = "images/16x16/delete.png" border = "0" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" onclick = "questions_form.selected_action.value = 'delete';return confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}');" style = "vertical-align:middle"/>
                                                                    <input type = "hidden" name = "selected_action" value = ""/>
                                                                </span>
                                                                </td></tr>
                                                        </table>
                                            {/if}
                                                        </form>
                                        {/capture}


                                            <div class = "tabber">
                                                <div class = "tabbertab" title = "{$smarty.const._TESTS}" id = "tests">
                                                        {$smarty.capture.t_tests_code}
                                                        
                                                        <br>
                                                        {* Show 5 most recently completed tests*}
                                                        {capture name = 't_recently_completed'}
                                                                        <table width = "100%" class = "sortedTable">
                                                                            <tr class = "defaultRowHeight">
                                                                                <td class = "topTitle">{$smarty.const._DATE}</td>
                                                                                <td class = "topTitle">{$smarty.const._NAME}</td>
                                                                                <td class = "topTitle">{$smarty.const._STUDENT}</td>
                                                                                <td class = "topTitle centerAlign">{$smarty.const._SCORE}</td>
                                                                                <td class = "topTitle centerAlign noSort">{$smarty.const._FUNCTIONS}</td>
                                                                            </tr>
													        {section name = 'recent_test' loop = $T_RECENT_TESTS max = 5}	
                                                                            <tr class = "{cycle name = "main_cycle" values="oddRowColor,evenRowColor"} defaultRowHeight">
                                                                                <td>#filter:timestamp_time-{$T_RECENT_TESTS[recent_test].timestamp}#</td>
                                                                              	<td>{$T_RECENT_TESTS[recent_test].name}</td>
                                                                              	<td>{$T_RECENT_TESTS[recent_test].users_LOGIN}&nbsp;({$T_RECENT_TESTS[recent_test].surname}&nbsp;{$T_RECENT_TESTS[recent_test].username})</td>
                                                                              	<td class = "centerAlign">{if $T_RECENT_TESTS[recent_test].score}{$T_RECENT_TESTS[recent_test].score}%{else}0.00%{/if}</td>
                                                                                <td align = "center">
                                                                <a href = "{$smarty.server.PHP_SELF}?ctg=tests&show_solved_test={$T_RECENT_TESTS[recent_test].id}">
                                                                    <img src = "images/16x16/view.png" alt = "{$smarty.const._VIEWTEST}" title = "{$smarty.const._VIEWTEST}" border = "0"/></a>
                                                                <a href = "{$smarty.server.PHP_SELF}?ctg=tests&show_solved_test={$T_RECENT_TESTS[recent_test].id}&test_analysis=1&user={$T_RECENT_TESTS[recent_test].users_LOGIN}">
                                                                    <img src = "images/16x16/text_view.png" alt = "{$smarty.const._TESTANALYSIS}" title = "{$smarty.const._TESTANALYSIS}" border = "0"/></a>
                                                                <a href = "{$smarty.server.PHP_SELF}?ctg=tests&delete_solved_test={$T_RECENT_TESTS[recent_test].id}&test_id={$T_RECENT_TESTS[recent_test].tests_ID}&users_login={$T_RECENT_TESTS[recent_test].users_LOGIN}" onclick = "return confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}');"/>
                                                                    <img border="0" src="images/16x16/delete.png" style="vertical-align:middle" alt="{$smarty.const._DELETESKILLGAPTESTRECORD}" title="{$smarty.const._DELETESKILLGAPTESTRECORD}"> </a>
                                                                                </td>
                                                                            </tr>
                                                            {sectionelse}
                                                                            <tr><td class = "emptyCategory oddRowColor" colspan = "100%" style = "text-align:center">{$smarty.const._NODATAFOUND}</td></tr>
                                                            {/section}
                                                                        </table>

                                                        {/capture}
                                                        {if $T_SKILLGAP_TEST}
                                                        	{eF_template_printInnerTable title=$smarty.const._RECENTLYCOMPLETEDSKILLGAP data=$smarty.capture.t_recently_completed image='32x32/pda2_preferences.png' options = $T_RECENTLY_SKILLGAP_OPTIONS}
                                                        {else}
                                                        	{eF_template_printInnerTable title=$smarty.const._RECENTLYCOMPLETEDTESTS data=$smarty.capture.t_recently_completed image='32x32/text_marked.png'}
                                                        {/if}
                                                        
                                                </div>
                                                <div class = "tabbertab {if $smarty.get.tab == 'question' || $smarty.get.tab == 'questions'} tabbertabdefault{/if}"  id = "question" title = "{$smarty.const._QUESTIONS}">
                                                <h3>{$smarty.const._QUESTIONS}</h3>
                                                        {$smarty.capture.t_questions_code}
                                                </div>

                                            </div>
                                    {/capture}
                                {*Exclude from skillgap tests*}
                                {if !$T_SKILLGAP_TEST}
                                    {eF_template_printInnerTable title=$smarty.const._UNITANDSUBUNITSTESTS data=$smarty.capture.t_tests_and_questions_code image='32x32/document_edit.png'}
                                {else}
                                    {eF_template_printInnerTable title=$smarty.const._SKILLGAPTESTS data=$smarty.capture.t_tests_and_questions_code image='32x32/pda_write.png'}

                                {/if}
                            {/if}
