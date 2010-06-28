function deleteQuestion(el, id) {
 parameters = {delete_question:id, method: 'get'};
 var url = location.toString();
 ajaxRequest(el, url, parameters, onDeleteQuestion);
}
function onDeleteQuestion(el, reponse) {
 new Effect.Fade(el.up().up());
}
function deleteTest(el, id) {
 parameters = {delete_test:id, method: 'get'};
 var url = location.toString();
 ajaxRequest(el, url, parameters, onDeleteTest);
}
function onDeleteTest(el, reponse) {
 new Effect.Fade(el.up().up());
}
function publish(el, id) {
 parameters = {publish_test:id, method: 'get'};
 var url = location.toString();
 ajaxRequest(el, url, parameters, onPublish);
}
function onPublish(el, response) {
 if (response == 1) {
  setImageSrc(el, 16, 'success');
  el.alt = el.title = published;
 } else {
  setImageSrc(el, 16, 'forbidden');
  el.alt = el.title = notpublished;
 }
}

function onSortedTableComplete() {
 var heightValue;
 if (sortedTables[tableIndex].getDimensions().height != 0) {
  heightValue = parseInt(sortedTables[tableIndex].getDimensions().height+50);
 } else {
  heightValue = 0;
 }
 if (sortedTables[tableIndex].id == 'filesTable') {
  $('filemanager_cell').setStyle({width:sortedTables[tableIndex].getDimensions().width+'px', height:heightValue+'px', verticalAlign:'top'});
 }
}

function setRandomPool(el) {
 parameters = {ajax:'random_pool', method: 'get'};
 parameters = Object.extend(parameters, $('advanced_form').serialize(true));
 var url = location.toString();
 ajaxRequest(el, url, parameters, onSetRandomPool);
}
function onSetRandomPool(el, response) {
 updateSettings(response);
}

function randomize(el, mode) {
 var parameters = $('general_form').serialize(true);
    if (mode == 'difficulty') {
        parameters = Object.extend(parameters, $('difficulty_form').serialize(true));
        //alert($('difficulty_form').serialize());
    } else if (mode == 'type') {
     parameters = Object.extend(parameters, $('type_form').serialize(true));
    } else if (mode == 'percentage') {
     parameters = Object.extend(parameters, $('percentage_form').serialize(true));
    }
    Object.extend(parameters, {ajax:'randomize', method:'get'});

    ajaxRequest(el, url, parameters, onRandomize);
}
function onRandomize(el, response) {
 updateSettings(response);

    tables = sortedTables.size();
    var i;
    for (i = 0; i < tables; i++) {
        if (sortedTables[i].id.match('questionsTable') && ajaxUrl[i]) {
            eF_js_rebuildTable(i, 0, 'partof', 'asc');
        }
    }

    eF_js_changePage(1, 0);
}

function updateSettings(response) {
 response = response.evalJSON();
 try {
     $('test_settings').show();
     $('questions_number').update(response.multitude);

     $('difficulty_form').getElements().each(function (s) {s.type.match('select') ? s.options.selectedIndex = 1 : null;});
     $('type_form').getElements().each(function (s) {s.type.match('select') ? s.options.selectedIndex = 1 : null;});
     $('percentage_form').getElements().each(function (s) {s.type.match('select') ? s.options.selectedIndex = 0 : null;});
     $$('td.unit_to_accurate_percentage').each(function (s) {s.update('0%');});

  Object.keys(response.difficulties).each(function(s) {
    Object.keys(response.difficulties[s]).each (function(p) {
     $('unit_to_difficulty['+s+']['+p+']').options.selectedIndex = response.difficulties[s][p]+1;
    });
  });
  Object.keys(response.types).each(function(s) {
   Object.keys(response.types[s]).each (function(p) {
    $('unit_to_type['+s+']['+p+']').options.selectedIndex = response.types[s][p]+1;
   });
  });
  Object.keys(response.percentage).each(function(s) {
   $('unit_to_percentage['+s+']').options.selectedIndex = Math.round(response.percentage[s]);
   $('unit_to_accurate_percentage['+s+']').update(Math.round(response.percentage[s]*1000)/100 + '%');
  });

  var totalTime = response.total_duration;
  response.duration.hours ? $('questions_time_hours').update(response.duration.hours + hoursshorthand) : $('questions_time_hours').update(' ');
  response.duration.minutes ? $('questions_time_minutes').update(response.duration.minutes + minutesshorthand) : $('questions_time_minutes').update(' ');
  response.duration.seconds ? $('questions_time_seconds').update(response.duration.seconds + secondsshorthand) : $('questions_time_seconds').update(' ');

  if (!response.duration.seconds && !response.duration.minutes && !response.duration.hours) {
   $('questions_time_seconds').update('0'+minutesshorthand);
  }

     $('questions_random_pool').update(response.random_pool);
     if (!response.random_pool) {
      $('questions_random_pool').up().hide();
     } else {
      $('questions_random_pool').up().show();
     }

     $('test_duration').value = Math.round(response.test_duration/60);

     $('inner_test_settings').update($('test_settings').innerHTML);
 } catch (e) {
  alert(e);
 }
}


//Wrapper function to distinguish between question and user assignment to tests posts
function ajaxPost(id, el, table_id) {
 switch (table_id) {
  case 'testUsersTable': usersAjaxPost(id, el, table_id); break;
  case 'proposedLessonsTable': proposedLessonsAjaxPost(id, el, table_id);break;
  case 'proposedCoursesTable': proposedCoursesAjaxPost(id, el, table_id);break;
  case 'assignedLessonsTable': assignedLessonsAjaxPost(id, el, table_id);break;
  case 'assignedCoursesTable': assignedCoursesAjaxPost(id, el, table_id); break;
  default: questionsAjaxPost(id, el, table_id); break;
 }
 //table_id == 'testUsersTable' ? usersAjaxPost(id, el, table_id) : questionsAjaxPost(id, el, table_id);
}
function proposedLessonsAjaxPost(id, el, table_id) {
 var url = location.toString();
 var parameters = {postAjaxRequest:1, method: 'get'};

    if (id) {
     Object.extend(parameters, {add_lesson: id, insert: $('lesson_'+id).checked, user_type: 'student'});
    } else {
     el.checked ? Object.extend(parameters, {add_lesson: 1, addAllLessonsFromTest: url.toString().match(/show_solved_test=(\d*)/)[1]}) : Object.extend(parameters, {removeAllFromTest: 1});
        alltables = sortedTables.size();
        for (var j = 0; j < alltables; j++) {
            if (sortedTables[j].id.match('proposedLessonsTable') && ajaxUrl[j]) {
                // Get from the proposedLessonsTable all skills that are missing/existing according to the existing mapping (choices of admin in the first tab)
                userId = "&user="+url.toString().match(/user=(.*)\W/)[1];
                url += ajaxUrl[j].substr(ajaxUrl[j].search(userId) + userId.length);
                break;
            }
        }
        if ($(table_id+'_currentFilter')) {
         Object.extend(parameters, {filter: $(table_id+'_currentFilter').innerHTML});
  }
    }

    ajaxRequest(el, url, parameters);
}

function proposedCoursesAjaxPost(id, el, table_id) {
 var url = location.toString();
 var parameters = {postAjaxRequest:1, method: 'get'};

    if (id) {
     Object.extend(parameters, {add_course: id, insert: $('course_'+id).checked, user_type: 'student'});
    } else {
     el.checked ? Object.extend(parameters, {add_course: 1, addAllCoursesFromTest: url.toString().match(/show_solved_test=(\d*)/)[1]}) : Object.extend(parameters, {removeAll: 1});
        alltables = sortedTables.size();
        for (var j = 0; j < alltables; j++) {
            if (sortedTables[j].id.match('proposedCoursesTable') && ajaxUrl[j]) {
                // Get from the proposedCoursesTable all skills that are missing/existing according to the existing mapping (choices of admin in the first tab)
                // NOTE: both tables have the same skill-set descriptions - we are differentiating them just for annotation reasons
                userId = "&user="+url.toString().match(/user=(.*)\W/)[1];
                url += ajaxUrl[j].substr(ajaxUrl[j].search(userId) + userId.length);
                break;
            }
        }

        if ($(table_id+'_currentFilter')) {
         Object.extend(parameters, {filter: $(table_id+'_currentFilter').innerHTML});
  }
    }

    ajaxRequest(el, url, parameters);
}
function assignedLessonsAjaxPost(id, el, table_id) {
 var url = location.toString();
 var parameters = {postAjaxRequest:1, method: 'get'};

    if (id) {
     Object.extend(parameters, {add_lesson: id, insert: $('lesson_'+id).checked, user_type: 'student'});
    } else {
     el.checked ? Object.extend(parameters, {add_lesson: 1, addAll: 1}) : Object.extend(parameters, {add_lesson: 1, removeAll: 1});
        if ($(table_id+'_currentFilter')) {
         Object.extend(parameters, {filter: $(table_id+'_currentFilter').innerHTML});
  }
    }

    ajaxRequest(el, url, parameters);
}
function assignedCoursesAjaxPost(id, el, table_id) {
 var url = location.toString();
 var parameters = {postAjaxRequest:1, method: 'get'};

    if (id) {
     Object.extend(parameters, {add_course: id, insert: $('course_'+id).checked, user_type: 'student'});
    } else {
     el.checked ? Object.extend(parameters, {add_course: 1, addAll: 1}) : Object.extend(parameters, {add_course: 1, removeAll: 1});
        if ($(table_id+'_currentFilter')) {
         Object.extend(parameters, {filter: $(table_id+'_currentFilter').innerHTML});
  }
    }

    ajaxRequest(el, url, parameters);
}

function questionsAjaxPost(id, el, table_id) {
 var url = location.toString();
 var parameters = {postAjaxRequest:1, method: 'get'};

 if (id) {
  Object.extend(parameters, {question: id});
  if ($('weight_'+id)) {
   if (el.id.match('checked_') && !$('checked_'+id).checked) {
    Object.extend(parameters, {remove: 1});
   } else {
    var weight = $('weight_'+id).options[$('weight_'+id).selectedIndex].value;
    Object.extend(parameters, {weight: weight});
   }
  } else {
   if (el.id.match('checked_') && !$('checked_'+id).checked) {
    Object.extend(parameters, {remove: 1});
   }
  }

 } else if (table_id && table_id == 'questionsTable') {
  el.checked ? Object.extend(parameters, {addAll: 1}) : Object.extend(parameters, {removeAll: 1});
  if ($(table_id+'_currentFilter')) {
   Object.extend(parameters, {filter: $(table_id+'_currentFilter').innerHTML});
  }
 }

 ajaxRequest(el, url, parameters);
}


function usersAjaxPost(login, el, table_id) {
 var url = location.toString();
 var parameters = {postAjaxRequest:1, method: 'get'};

 if (login) {
  Object.extend(parameters, {login: login, checked: $('checked_'+login).checked});
 } else if (table_id && table_id == 'testUsersTable') {
  el.checked ? Object.extend(parameters, {login: 1, addAll: 1}) : Object.extend(parameters, {login: 1, removeAll: 1});
  if ($(table_id+'_currentFilter')) {
   Object.extend(parameters, {filter: $(table_id+'_currentFilter').innerHTML});
  }
 }
 ajaxRequest(el, url, parameters);
}

// Function that changes the colour of the corresponding skill bar and reloads the proposed lessons
// if the corresponding parameter=true nad if the threshold has changed in a way that a possible change
// in the proposed lessons might have been triggered
function eF_thresholdChange(skillName, skillScore, reloadProposals) {
    if ($(skillName+'_threshold').value.match(/^\d{1,2}(\.\d{1,2})?$/) ) {
        previous_val = parseFloat($(skillName+'_previous_threshold').value);
        new_val = parseFloat($(skillName+'_threshold').value);
        skillScore = parseFloat(skillScore);

        changed = 0;
        if (skillScore >= previous_val && new_val > skillScore) {
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

function eF_addTestSkills() {
 // We will post an ajax request to include the results of the skill gap test in the skillset of the user
 var url = sessionType + ".php?ctg=users&edit_user=" + editedUser;
 var parameters = {postAjaxRequest:1, add_skill: 1, from_skillgap_test:1, method: 'get'};

 // Change all thresholds to the new value and call the onChange function for each of them
    var skillTableInputs = $('skillScoresTable').getElementsByTagName('input'); //Get all the \"input\" elements of the skills table

    for (var i = 0; i < skillTableInputs.length; i++) {
     threshold = skillTableInputs[i].value;
     skillId = skillTableInputs[i].id;
        separator = skillId.lastIndexOf("_");
  skillId = skillTableInputs[i].id.substr(0, separator);

        funcName = skillTableInputs[i].getAttribute("onChange");
        if (funcName) {
         // Unorthodox (!) method of checking whether the skill is acquired
         skillScore = $(skillId + "_bar").previous(1).innerHTML;
         url += "&skill"+skillId+"="+skillScore.substr(0,5)+ "&succeed"+skillId+"="+((parseFloat(skillScore) >= parseFloat(threshold))?1:0);

         //Object.extend(parameters, {"skill"+skillId: skillScore.substr(0,5), "succeed"+skillId: ((parseFloat(skillScore) >= parseFloat(threshold))?1:0)});
  }

    }

    ajaxRequest($('addToSkillSetImg'), url, parameters);
}

function eF_addSingleTestSkill(skillId) {
 // We will post an ajax request to include the results of the skill gap test in the skillset of the user
 var url = sessionType + '.php?ctg=users&edit_user='+ editedUser +'&postAjaxRequest=1&add_skill=1&from_skillgap_test=1';
 var parameters = {postAjaxRequest:1, add_skill: 1, from_skillgap_test:1, method: 'get'};

 // Change all thresholds to the new value and call the onChange function for each of them                                
   threshold = $(skillId + "_threshold").value;

    // Unorthodox (!) method of checking whether the skill is acquired
    skillScore = $(skillId + "_bar").previous(1).innerHTML;
    url += "&skill"+skillId+"="+skillScore.substr(0,5)+ "&succeed"+skillId+"="+((parseFloat(skillScore) >= parseFloat(threshold))?1:0);

    //Object.extend(parameters, {"skill"+skillId: skillScore.substr(0,5), "succeed"+skillId: ((parseFloat(skillScore) >= parseFloat(threshold))?1:0)});

    ajaxRequest($('addToSkillSetImg'+skillId), url, parameters);
}
function eF_generalThresholdChange(newThreshold) {
 // Acceptable formats: 2,23,23.1,23.10
 if (newThreshold.match(/^\d{1,2}(\.\d{1,2})?$/) ) {
        previous_val = parseFloat($('general_previous_threshold').value);
        new_val = parseFloat($('shold').value);
        skillScore = parseFloat(newThreshold);

        // Change all thresholds to the new value and call the onChange function for each of them
        var skillTableInputs = $('skillScoresTable').getElementsByTagName('input'); //Get all the \"input\" elements of the skills table

        anyChanges = false;
        for (var i = 0; i < skillTableInputs.length; i++) {
            funcName = skillTableInputs[i].getAttribute("onChange");

            if (funcName) {
             funcName = String(funcName);
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
function ajaxAssignAllNew(el) {
 parameters = {postAjaxRequest:1, auto_assign: el.checked, method: 'get'};
 var url = location.toString();
 ajaxRequest(el, url, parameters);
}

//Function to remove a solved test
function ajaxRemoveSolvedTest(el, login, completed_test_id, test_id) {
 parameters = {postAjaxRequest: 1, edit_test:0, delete_solved_test:completed_test_id, test_id: test_id, users_login: login, method: 'get'};//We put edit_test=0 because otherwise we end up in a different branch of the tests page
 var url = location.toString();
 ajaxRequest(el, url, parameters, onAjaxRemoveSolvedTest);
}
function onAjaxRemoveSolvedTest(el, response) {
 tables = sortedTables.size();
 for (var i = 0; i < tables; i++) {
  if (sortedTables[i].id.match('testUsersTable') || (sortedTables[i].id.match('pendingTable')) && ajaxUrl[i]) {
   eF_js_rebuildTable(i, 0, 'null', 'desc');
  }
 }
}

function initSlider() {
 new Control.Slider('slider_handle','slider', {
     range: $R(0, 100),
     sliderValue: 50,
     onSlide: function(value) {
      value = Math.round(value);
      $('balance').value = value;
      $('balance_value_questions').update(value);
      $('balance_value_duration').update(100 - value);
     },
     onChange: function(value) {
      value = Math.round(value);
      $('balance').value = value;
      $('balance_value_questions').update(value);
      $('balance_value_duration').update(100 - value);
     }
 });
 return 1;
}


function delete_criterium_row(id, category)
{
 if (category != "skills" && category != "lessons") {
  return 0;
 } else {
  criteriaTable = category + 'Table';
 }
    var criteriaTable = document.getElementById(criteriaTable);
    if ($('noFooterRow0')) {
        $('noFooterRow0').remove();
    }
 if ($('noFooterRow1')) {
        $('noFooterRow1').remove();
    }

    noOfRows = criteriaTable.rows.length;
    var rowId;
    for (i = 1; i < noOfRows; i++) {
        rowId = category+"_row_"+id;
        if (criteriaTable.rows[i].id == rowId) {
            // el.up.up.id has the form 'row_'*
            criteriaTable.deleteRow(i);
            break;
        }
    }

    // If no job descriptions remain then show the "No jobs assigned" message
    if (criteriaTable.rows.length == 1) {

        var x = criteriaTable.insertRow(1);
        x.setAttribute("id", "no_"+category+"_criteria_defined");
        var newCell = x.insertCell(0);

        if (category == "lessons") {
         var newCellHTML = setAssociatedDirections;
        } else {
         var newCellHTML = setAssociatedSkills;
        }

        newCell.innerHTML= newCellHTML;
        //newCell.setAttribute("id", "no_"+category+"_criteria_found");
        newCell.colSpan = 3;
        newCell.className = "emptyCategory";
    }
    return false;
}

function getQuestionsCount(lessonName) {
 start = lessonName.lastIndexOf("(");
 end = lessonName.lastIndexOf(")");
 return parseInt(lessonName.substr(start+1,end-2));
}

// Recreate the questions select with a new select of questions_count elements
function recreateQuestionsSelect(questions_el, questions_count) {
 Element.extend(questions_el);
                                        // Delete all exept from the default room
    while(questions_el.length > 0) {

        questions_el.remove(0);
    }

    for (i = 1; i <= questions_count; i = i+1) {
        elOptNew = document.createElement('option');
        elOptNew.value = i;
        elOptNew.text = i;

        try {
            questions_el.add(elOptNew,null);
        } catch(ex) {
            questions_el.add(elOptNew); // IE only
        }
 }

}

function createQuestionsSelect(el) {
 Element.extend(el);
 category = el.id.indexOf("_"); // category = educational_ (cat=11) or skills_ (cat=8)
 row = el.id.substr(el.id.lastIndexOf("_")+1);
 if (category == 11) {
  questions_el = document.getElementById("educational_questions_"+row);

 } else {
  questions_el = document.getElementById("skill_questions_"+row);
 }

 text = el.options[el.selectedIndex].text;
 questions_count = getQuestionsCount(text);

 if (questions_count == 0) {
  if (category == 11) {
   alert(noQuestionsDefinedForLesson);
  } else {
   alert(noQuestionsDefinedForSkill);
  }

  el.selectedIndex = el.getAttribute("mySelectedIndex");

 } else {
  el.setAttribute("mySelectedIndex", el.selectedIndex);
  recreateQuestionsSelect(questions_el, questions_count);


 }

}


// Do that to select the first non zero element - if non found return false
function selectFirstNonzero(select_element,questions_el) {
 Element.extend(select_element);

 for (i = 0; i < select_element.length; i++) {
  questions_count = getQuestionsCount(select_element.options[i].text);
  if (questions_count > 0) {
   select_element.selectedIndex = i;
   select_element.setAttribute("mySelectedIndex", i);
   recreateQuestionsSelect(questions_el, questions_count);
   return true;
  }
 }
 return false;

}


// Function for inserting the new job row into the edit_user profile
// The row argument denotes how many placements were initially present
// so that only one extra job may be inserted each time
function add_new_criterium_row(row, category) {

 if (category != "skills" && category != "lessons") {
  return 0;
 } else {
  criteriaTable = category + 'Table';
 }


    var table = document.getElementById(criteriaTable);

    if (document.getElementById('no_'+category+'_criteria_defined')) {


         document.getElementById(criteriaTable).deleteRow(1);
    }
    if ($('noFooterRow0')) {
        $('noFooterRow0').remove();
    }
    if ($('noFooterRow1')) {
        $('noFooterRow1').remove();
    }
    noOfRows = table.rows.length;

    var row = noOfRows;
    var x = table.insertRow(row);

    row = (++__criteria_total_number);
    x.setAttribute("id",category+"_row_"+row);

    newCell = x.insertCell(0);

    if (category == "lessons") {
     var newCellHTML = quickformLessonCourses;

 } else {
  var newCellHTML = quickformSkills;
 }
    // Replacing the "row" strings of the HTML code of the select to the correct row. For example the onclick="change(row)" will become onclick="change(2)"
    newCellHTML = newCellHTML.replace('row', row);
    newCellHTML = newCellHTML.replace('row', row);

    newCell.innerHTML= newCellHTML;

    newCell = x.insertCell(1);

    if (category == "lessons") {
     newCellHTML = quickformeducationalCount;
  newCellHTML = newCellHTML.replace('row', row);
     newCellHTML = newCellHTML.replace('row', row);

     newCell.innerHTML= newCellHTML;

     //.setAttribute("mySelectedIndex", 1);
     select_element = document.getElementById("educational_criteria_"+row);
     questions_element =document.getElementById("educational_questions_"+row);
    } else {

     newCellHTML = quickformSkillQuestCount;
  newCellHTML = newCellHTML.replace('row', row);
     newCellHTML = newCellHTML.replace('row', row);

     newCell.innerHTML= newCellHTML;

  select_element = document.getElementById("skills_criteria_"+ row);
     questions_element = document.getElementById("skill_questions_"+row);//.setAttribute("mySelectedIndex", 1);
    }

    newCell = x.insertCell(2);
    newCell.setAttribute("align", "center");
    newCell.innerHTML = '<a id="job_'+row+'" href="javascript:void(0);" onclick="delete_criterium_row(\''+row+'\', \''+category+'\');" class = "deleteLink"><img id="del_img'+row+'" class="sprite16 sprite16-error_delete handle" src = "themes/default/images/others/transparent.gif" title = "'+row+'" alt = "' + deleteConst + '" /></a></td>';

    document.getElementById('job_' + row).setAttribute('rowCount', row);


 if (!selectFirstNonzero(select_element,questions_element)) {
  delete_criterium_row(row, category);
  alert(noQuestionsFound);
 }

    //document.getElementById('courses_' +row).options[0].disabled = true;

}

function checkQuickTestForm() {
 if ($('testName').value == "") {
    alert(theFieldNameIsMandatory);
    return false;
 }

 if ($('no_lessons_criteria_defined')) {
  if ($('skillsTable')) {
   if ($('no_skills_criteria_defined')) {
    alert(noQuestionSelection);
    return false;
   }
  } else {
   alert(noQuestionSelection);
   return false;
  }
 }

 if (confirm(doYouWantToFurtherEdit)) {
  $('question_form').action += "&redirect_to_edit=1";
 }

 return true;

}

function eF_js_printTimer() {
    if (hours <= 0 && minutes <= 0 && seconds <= 0 && duration) {
        alert(timeup);
     document.test_form.submit();
    } else {
        if (seconds >= 1) {seconds--;}
        else {
            if (seconds == 0 ) {seconds = 59;}
            if (minutes >= 1) {minutes--;}
            else {
                if (minutes == 0) {minutes = 59;}
                if (hours >= 1) {hours--;}
                else {hours = 0;}
            }
        }
        min = minutes.toString();
        sec = seconds.toString()
        if (min.length == 1) {min = "0" + min;}
        if (sec.length == 1) {sec = "0" + sec;}

        $("time_left").update(hours + ":" + min + ":" + sec);
        setTimeout("eF_js_printTimer()", 1000);
    }
}


function eF_js_printQuestionTimer(id) {
 //The time is up!
    if (questionHours[id] <= 0 && questionMinutes[id] <= 0 && questionSeconds[id] <= 0 && questionDuration[id]) {

     //Overlay a div to disable question.
     $("question_content_" + id).up().insert(new Element("div", {id:"question_overlay_" + id}).addClassName("expiredQuestion").setOpacity(0.3));

     //If it is the current question that expired, disabled it
     if (id == $("question_"+current_question).down().innerHTML) {
      $("question_overlay_" + id).clonePosition($("question_" + current_question));
     }
    } else {
        if (questionSeconds[id] >= 1) {questionSeconds[id]--;}
        else {
            if (questionSeconds[id] == 0 ) {questionSeconds[id] = 59;}
            if (questionMinutes[id] >= 1) {questionMinutes[id]--;}
            else {
                if (questionMinutes[id] == 0) {questionMinutes[id] = 59;}
                if (questionHours[id] >= 1) {questionHours[id]--;}
                else {questionHours[id] = 0;}
            }
        }
        questionMin[id] = questionMinutes[id].toString();
        questionSec[id] = questionSeconds[id].toString()
        if (questionMin[id].length == 1) {questionMin[id] = "0" + questionMin[id];}
        if (questionSec[id].length == 1) {questionSec[id] = "0" + questionSec[id];}

        $("question_"+id+"_time_left").update(questionHours[id] + ":" + questionMin[id] + ":" + questionSec[id]+ " "+remainingtime);
        $("question_"+id+"_time_left_input").value = parseInt(questionHours[id])*3600+parseInt(questionMinutes[id])*60+parseInt(questionSeconds[id]);
        setTimeout("eF_js_printQuestionTimer("+id+")", 1000);
    }
}

function showTestQuestion(question_num) {

    if (question_num == 'next') {
        current_question < total_questions ? question_num = parseInt(current_question) + 1 : question_num = current_question;
    } else if (question_num == 'previous') {
        current_question > 1 ? question_num = parseInt(current_question) - 1 : question_num = current_question;
    }

    $('question_' + current_question).hide();
    $('question_' + question_num).show();
    var questionId = $('question_' + question_num).down().innerHTML;

    if ($('question_overlay_' + questionId)) {
     $('question_overlay_' + questionId).clonePosition($('question_' + question_num));
 }
    current_question = question_num;
    if ($('previous_question_button')) {
        current_question <= 1 ? $('previous_question_button').hide() : $('previous_question_button').show();
    }
    if ($('next_question_button')) {
     current_question >= total_questions ? $('next_question_button').hide() : $('next_question_button').show();
    }
    if ($('goto_question')) {
     $('goto_question').selectedIndex = current_question - 1;
    }
    if ($('question_'+questionId+'_time_left')) {
        $('question_'+questionId+'_time_left').up().select('span').each(function (s) {s.hide()});
        $('question_'+questionId+'_time_left').show();
    }

    if (questionDuration && questionDuration[questionId]) {
     eF_js_printQuestionTimer(questionId);
    }
}

function checkQuestions() {
 var finished = new Array();
 var count = 0;
 $$('.unsolvedQuestion').each(function (r) {
  finished[count] = 0;
     if (r.hasClassName('trueFalseQuestion')) {
      r.select('input[type=radio]').each(function (s) {s.checked ? finished[count] = 1 : null;});
     } else if (r.hasClassName('multipleOneQuestion')) {
      r.select('input[type=radio]').each(function (s) {s.checked ? finished[count] = 1 : null;});
     } else if (r.hasClassName('multipleManyQuestion')) {
      r.select('input[type=checkbox]').each(function (s) {s.checked ? finished[count] = 1 : null;});
     } else if (r.hasClassName('matchQuestion')) {
      //r.select('select').each(function (s) {s.checked ? finished[count] = 1 : null;});
      finished[count] = 1;
     } else if (r.hasClassName('emptySpacesQuestion')) {
      r.select('input[type=text]').each(function (s) {s.value ? finished[count] = 1 : null;});
     } else if (r.hasClassName('rawTextQuestion')) {
      r.select('textarea').each(function (s) {s.value ? finished[count] = 1 : null;});
     } else if (r.hasClassName('dragDropQuestion')) {
      r.select('td.dragDropTarget').each(function (s) {s.childElements().length ? finished[count] = 1 : null;});
     }
     count++;
 });

 var unfinished = new Array();
 for (var i = 0; i < finished.length; i++) {
  if (!finished[i]) { unfinished.push(i+1); }
 }
 if (unfinished.length) {
  if (force_answer_all == 1) {
   alert (translations['youhavetoanswerallquestions']+': '+unfinished);
   return false;
  } else if(force_answer_all == 0) {
   return confirm(translations['youhavenotcompletedquestions']+': '+unfinished+'. '+translations['areyousureyouwanttosubmittest']);
  }

 }
}
function handleDrop(s,d, e) {
 s.setStyle({left:'auto', top:'auto'});
 d.next().insert(s.remove());
 s.down().value = d.down().value;
 dragdrop[s.id]=d.id
 Droppables.remove(d);
}
function handleDrag(s, e) {
 if (dragdrop[s.element.id]) {
  Droppables.add($(dragdrop[s.element.id]), {accept:'draggable', onDrop:handleDrop});
  $('source_'+questionId+'_'+s.element.id.match(/firstlist_\d+_(\d+)/)[1]).insert(s.element.remove());
 }
}
function initDragDrop(questionId, keys) {
 for (var i = 0; i < keys.length; i++) {
  Droppables.add('secondlist_'+questionId+'_'+keys[i], {accept:'draggable', onDrop:handleDrop});
  new Draggable('firstlist_'+questionId+'_'+keys[i], {revert:'failure', onStart:handleDrag});
 }
}

function toggleAdvancedParameters() {

 $('onebyone').toggle();
 $('only_forward').toggle();
 if ($('given_answers')) {
  $('given_answers').toggle();
 }
 if ($('answers')) {
  $('answers').toggle();
 }
 if ($('redirect')) {
  $('redirect').toggle();
 }
 $('shuffle_answers').toggle();
 $('shuffle_questions').toggle();
 if ($('pause_test')) {
  $('pause_test').toggle();
 }
 $('publish').toggle();
 $('display_list').toggle();
 if ($('redo_wrong')) {
  $('redo_wrong').toggle();
 }
 if ($('answer_all')) {
  $('answer_all').toggle();
 }
 if ($('display_weights')) {
  $('display_weights').toggle();
 }
 if ($('advenced_parameter_image').className.match("down")) {
  setImageSrc($('advenced_parameter_image'), 16, 'navigate_up.png');
 } else {
  setImageSrc($('advenced_parameter_image'), 16, 'navigate_down.png');
 }
}


//These must be run when a test is shown
if (typeof(showtest) != 'undefined' && showtest) {
 //alert(window.initTimer);
 if (window.initTimer) {initTimer();};
 if (typeof(current_question) != 'undefined') {
  showTestQuestion(current_question);
 }
}

dragdrop = new Object();
if (typeof(dragDropQuestions) != 'undefined') {
 dragDropQuestions.each(function (s) {initDragDrop(s, dragDropQuestionKeys[s]);})
}
