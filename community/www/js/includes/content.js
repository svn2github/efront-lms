/**

 * Set unit seen status

 * This function makes an ajax call to the server to set the current unit's status to either 'seen' or 'unseen', based on

 * the status parameter 

 */
function setSeenUnit(status) {
    if (typeof(status) == 'undefined') { //If "status" parameter is not set, then toggle the seen status based on whether the user has seen the lesson
        hasSeen ? status = 0 : status = 1;
    } else { //If "status" parameter is set, then toggle the seen status based on this parameter
     status ? status = 1 : status = 0;
    }
    //el = $('seenLink');					//For backwards-compatibility, we don't specify el in the parameters list
    if ($('seenLink')) {
     $('seenLink').blur();
     el = $('seenLink').down();
    } else {
     el = new Element('div');
    }
 parameters = {set_seen:status, method: 'get'};
 var url = window.location.toString();
 ajaxRequest(el, url, parameters, onSetSeenUnit);
}
/*

 * This function is executed when the ajax call of setSeen returns, to set the appropriate text and

 * depict the unit status, both in the 'set seen' icon and the content tree as well 

 */
function onSetSeenUnit(el, response) {
 try {
  results = response.evalJSON();
        if (hasSeen) {
         if ($('seenLink')) {
          setImageSrc($('seenLink').down(), 32, 'unit.png');
          $('seenLink').down().next().update(sawunit);
         }
         if ($('tree_image_'+unitId)) {
          setImageSrc($('tree_image_'+unitId), 16, unitType+'.png');
         }
        } else {
         if ($('seenLink')) {
          setImageSrc($('seenLink').down(), 32, 'unit_completed.png');
          $('seenLink').down().next().update(notsawunit);
         }
         if ($('tree_image_'+unitId)) {
          setImageSrc($('tree_image_'+unitId), 16, unitType+'_passed.png');
         }
        }
        if ($('seenLink')) {
         new Effect.Appear($('seenLink'));
        }
        hasSeen = !hasSeen;
        if ($('progress_bar')) {
            $('progress_bar').select('span.progressNumber')[0].update(parseFloat(results[0]) + '%');
            $('progress_bar').select('span.progressBar')[0].setStyle({width:parseFloat(results[0]) + 'px'});
            if ($('passed_conditions')) {
             $('passed_conditions').update(parseInt(results[1]));
            }
            if ($('lesson_passed')) {
             results[2] == true ? $('lesson_passed').down().removeClassName('failure').addClassName('success') : $('lesson_passed').down().removeClassName('success').addClassName('failure');
            }
        }
 } catch (e) {alert(e);}
}
/**

 * This function automatically navigates to the next unit, if any

 */
function nextUnit() {
 if (typeof(nextId) != 'undefined') {
  window.location = window.location.toString().replace(/view_unit=\d*/, "view_unit="+nextId);
 }
}
/**

 * This function automatically navigates to the previous unit, if any

 */
function previousUnit() {
 if (typeof(previousId) != 'undefined') {
  window.location = window.location.toString().replace(/view_unit=\d*/, "view_unit="+previousId);
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
function togglePdf() {
 $('pdf_upload').toggle();
 $('pdf_content').toggle();
 $('nonPdfTable').toggle();
 $('toggleTools').toggle();
}
function answerQuestion(el) {
 Element.extend(el);
 $('correct_answer').hide();
 $('wrong_answer').hide();
 el.up().insert(new Element('img', {src:'themes/default/images/others/progress1.gif', id:'progress_image'}).setStyle({verticalAlign:'middle', marginLeft:'5px'}));
 $('question_form').request({
  onFailure: function(transport) {
  $('progress_image').remove();
  showMessage(transport.responseText, 'failure');
 },
 onSuccess:function(transport) {
  if (transport.responseText == 'correct') {
   new Effect.Appear($('correct_answer'));
  } else {
   new Effect.Appear($('wrong_answer'));
  }
  $('progress_image').remove();
  setSeenUnit();
 }
 });
}
function addBookmark(el) {
 parameters = {bookmarks:'add', method: 'get'};
 var url = window.location.toString();
 ajaxRequest(el, url, parameters);
}
function removeBookmark(el, id) {
 parameters = {bookmarks:'remove', id: id, method: 'get'};
 var url = window.location.toString();
 ajaxRequest(el, url, parameters, onRemoveBookmark);
}
function onRemoveBookmark(el, response) {
 new Effect.Fade(el.up());
}
/**

* This function prevents a link from loading upon click.

*/
function disableLink (s)
{
 s.stop()
}
function updateProgress(obj) {
 if (!(w = findFrame(top, 'mainframe'))) {
  w = window;
 }
 try {
  progress = obj[0];
  conditions = obj[1];
  lesson_passed = obj[2];
  unitStatus = obj[5];
     if (w.$('progress_bar')) {
      w.$('progress_bar').select('span.progressNumber')[0].update(parseFloat(progress) + '%');
      w.$('progress_bar').select('span.progressBar')[0].setStyle({width:parseFloat(progress) + 'px'});
      if (w.$('passed_conditions')) {
       w.$('passed_conditions').update(parseInt(conditions));
      }
      if (w.$('lesson_passed')) {
       lesson_passed == true ? w.$('lesson_passed').down().removeClassName('failure').addClassName('success') : w.$('lesson_passed').down().removeClassName('success').addClassName('failure');
      }
     }

     for (var i in unitStatus) {
      if (unitStatus[i].completion_status == 'completed' || unitStatus[i].success_status == 'passed') {
       status = '_passed';
      } else if (unitStatus[i].completion_status == 'incomplete' && unitStatus[i].success_status == 'unknown') {
       status = '_incomplete';
      } else if (unitStatus[i].success_status == 'failed') {
       status = '_failed';
      } else {
       status = '';
      }

      if (w.$('tree_image_'+i)) {
       w.$('tree_image_'+i).className.match(/tests/) ? type = 'tests' : type = 'theory';
       setImageSrc(w.$('tree_image_'+i), 16, type+status);
      }
     }

     if (nodesStatus = obj[3]) {
      if ($('navigate_continue')) {
       if (nodesStatus['continue'] == 'enabled') {
        $('navigate_continue').removeClassName('inactiveImage');
     $('navigate_continue').writeAttribute("onclick", "");
     $('navigate_continue').show();

       } else if (nodesStatus['continue'] == 'disabled') {
     $('navigate_continue').addClassName('inactiveImage');
     $('navigate_continue').writeAttribute("onclick", "return false");
     $('navigate_continue').show();

       } else if (nodesStatus['continue'] == 'hidden') {
        $('navigate_continue').hide();
       }
      }
      if ($('navigate_previous')) {
       if (nodesStatus['previous'] == 'enabled') {
        $('navigate_previous').removeClassName('inactiveImage');
     $('navigate_previous').writeAttribute("onclick", "");
     $('navigate_previous').show();

       } else if (nodesStatus['previous'] == 'disabled') {
        $('navigate_previous').addClassName('inactiveImage');
     $('navigate_previous').writeAttribute("onclick", "return false");
     $('navigate_previous').show();

       } else if (nodesStatus['previous'] == 'hidden') {
        $('navigate_previous').hide();
       }
      }
      if ($('navigate_exitAll')) {
       if (nodesStatus['exitAll'] == 'enabled') {
        $('navigate_exitAll').removeClassName('inactiveImage');
     $('navigate_exitAll').writeAttribute("onclick", "");
     $('navigate_exitAll').show();

       } else if (nodesStatus['exitAll'] == 'disabled') {
        $('navigate_exitAll').addClassName('inactiveImage');
     $('navigate_exitAll').writeAttribute("onclick", "return false");
     $('navigate_exitAll').show();

       } else if (nodesStatus['exitAll'] == 'hidden') {
        $('navigate_exitAll').hide();
       }
      }
      if ($('navigate_suspendAll')) {
       if (nodesStatus['suspendAll'] == 'enabled') {
        $('navigate_suspendAll').removeClassName('inactiveImage');
     $('navigate_suspendAll').writeAttribute("onclick", "");
     $('navigate_suspendAll').show();

       } else if (nodesStatus['suspendAll'] == 'disabled') {
        $('navigate_suspendAll').addClassName('inactiveImage');
     $('navigate_suspendAll').writeAttribute("onclick", "return false");
     $('navigate_suspendAll').show();

       } else if (nodesStatus['suspendAll'] == 'hidden') {
        $('navigate_suspendAll').hide();
       }
      }
      if ($('navigate_abandon')) {
       if (nodesStatus['abandon'] == 'enabled') {
        $('navigate_abandon').removeClassName('inactiveImage');
     $('navigate_abandon').writeAttribute("onclick", "");
     $('navigate_abandon').show();

       } else if (nodesStatus['abandon'] == 'disabled') {
        $('navigate_abandon').addClassName('inactiveImage');
     $('navigate_abandon').writeAttribute("onclick", "return false");
     $('navigate_abandon').show();

       } else if (nodesStatus['abandon'] == 'hidden') {
        $('navigate_abandon').hide();
       }
      }
      if ($('navigate_abandonAll')) {
       if (nodesStatus['abandonAll'] == 'enabled') {
        $('navigate_abandonAll').removeClassName('inactiveImage');
     $('navigate_abandonAll').writeAttribute("onclick", "");
     $('navigate_abandonAll').show();

       } else if (nodesStatus['abandonAll'] == 'disabled') {
        $('navigate_abandonAll').addClassName('inactiveImage');
     $('navigate_abandonAll').writeAttribute("onclick", "return false");
     $('navigate_abandonAll').show();

       } else if (nodesStatus['abandonAll'] == 'hidden') {
        $('navigate_abandonAll').hide();
       }
      }

      for (var i in nodesStatus['choice']) {

       if (nodesStatus['choice'][i] == 'enabled') {
        $('node'+i).select('a')[0].removeClassName('inactiveLink');
     $('node'+i).select('a')[0].writeAttribute("onclick", "");
        $('node'+i).show();

    } else if (nodesStatus['choice'][i] == 'disabled') {
        $('node'+i).select('a')[0].addClassName('inactiveLink');
     $('node'+i).select('a')[0].writeAttribute("onclick", "return false");
        $('node'+i).show();

       } else if (nodesStatus['choice'][i] == 'hidden') {
        $('node'+i).hide();
       }
      }
     }
 } catch (e) {
  alert(e);
 }
}

if (top.sideframe && !usingHorizontalInterface) {
    if (top.sideframe.chatEnabled || typeof(show_left_bar) == 'undefined' || parseInt(show_left_bar)) {
     showLeftSidebar();
    } else {
     hideLeftSidebar();
    }
}
/**

* This function is used to resize scorm iframe, so that it spans through the entire page

*/
function eF_js_setCorrectIframeSize(setHeight)
{
    if (frame = window.document.getElementById('scormFrameID')) {
        innerDoc = (frame.contentDocument) ? frame.contentDocument : frame.contentWindow.document;
        //Some contents send the final commit after the page closes, thus causing the tree and progress to not be updated on time.
        //For this reason, we copy the onunload event to a beforeunload event. In order to make for some weird contents that use the
        //onunload in other circumstances as well, there is a flag called "noBeforeUnload" that disables this event copying               
        if (typeof(noBeforeUnload) == 'undefined' || !noBeforeUnload) {
            //Firefox
         if ($('scormFrameID').contentWindow.onunload) {
          Event.observe($('scormFrameID').contentWindow, 'beforeunload', $('scormFrameID').contentWindow.onunload);
         }
         //IE
         else if ($('scormFrameID').contentWindow.document.body.onunload) {
          Event.observe($('scormFrameID').contentWindow, 'beforeunload', $('scormFrameID').contentWindow.document.body.onunload);
         }
         //Sub frames: in case the main doc is frameset, we must go through its frames and apply the same
         else if ($('scormFrameID').contentWindow.frames && $('scormFrameID').contentWindow.frames.length > 0) {
          for (var i = 0; i < $('scormFrameID').contentWindow.frames.length; i++) {
           w = $('scormFrameID').contentWindow.frames[i];
           //FF
           if (w.onunload) {
            Event.observe(w, 'beforeunload', w.onunload);
           }
           //IE
           else if (w.document.body.onunload) {
            Event.observe($('scormFrameID').contentWindow, 'beforeunload', w.document.body.onunload);
           }
          }
         }
        }
        objToResize = (frame.style) ? frame.style : frame;
        if (setHeight) {
         objToResize.height = setHeight + 'px';
        } else {
         if (frame.document) {
             objToResize.height = Math.max(innerDoc.body.scrollHeight, frame.document.body.scrollHeight) + 500 + 'px';
         } else {
             objToResize.height = innerDoc.body.scrollHeight + 500 + 'px';
         }
        }
    }
}

if (typeof(editPdfContent) != 'undefined' && editPdfContent) {
    togglePdf();
}

if (typeof(setIframeSize) != 'undefined' && setIframeSize) {
 eF_js_setCorrectIframeSize();
}
