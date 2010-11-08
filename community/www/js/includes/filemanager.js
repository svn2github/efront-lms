function toggleEditBox(el, fileId) {
 Element.extend(el);
 if (el.hasClassName("sprite16-edit")) {
  setImageSrc(el, 16, 'error_delete.png');
 } else {
  setImageSrc(el, 16, 'edit.png');
 }
 $("edit_"+fileId).toggle();
 $("edit_"+fileId).previous().toggle();
}
function copyFiles(el) {
 Element.extend(el);
 getSelected();
 el.next().show();
 el.hide();
}
function pasteFiles(el, tableId) {
 $('copy_current_directory').value = $(tableId).getAttribute('currentDir');
 el.removeClassName('sprite16').setAttribute('src', 'themes/default/images/others/progress1.gif');
 Element.extend(el);
 $('copy_files_form').request({
  onComplete: function() {
   $('copy_files').value = '';
   el.previous().show();
   el.hide();
   el.addClassName('sprite16').setAttribute('src', 'themes/default/images/others/transparent.gif');
   eF_js_rebuildTable($('filename_'+tableId).down().getAttribute('tableIndex'), 0, '', 'desc', $('copy_current_directory').value);
  }
 });
}
function getSelected() {
 $("copy_files").value = "";
 $(tableId).select("input[type=checkbox]").each(function (s) {
  if (s.checked && s.id) {
   $("copy_files").value ? $("copy_files").value = $("copy_files").value + ","+s.value : $("copy_files").value = s.value;
  }
 });
}
function deleteSelected() {
 $(tableId).select("input[type=checkbox]").each(function (s) {
  if (s.checked && s.id) {
   s.up().previous().select("img").each (function (p) {if (p.className.match(/delete/)) {deleteFile(p, s.value);}});
  }
 });
}
function shareSelected() {
 $(tableId).select("input[type=checkbox]").each(function (s) {
  if (s.checked && s.id) {
   s.up().previous().previous().select("img").each (function (p) {if (p.className.match(/red/)) {shareFile(p, s.value);}});
  }
 });
}
function unshareSelected() {
 $(tableId).select("input[type=checkbox]").each(function (s) {
  if (s.checked && s.id) {
   s.up().previous().previous().select("img").each (function (p) {if (p.className.match(/green/)) {unshareFile(p, s.value);}});
  }
 });
}
function editFile(el, id, name, type, previousName) {
 parameters = {update:id, name:name, type:type, method: 'get'};
 ajaxRequest(el, url, parameters, onUpdateFile);
}
function onUpdateFile(el, response) {
 previousName = response.evalJSON(true).previousName;
 name = response.evalJSON(true).name;
 el.up().up().previous().update(name);
 el.up().up().hide();
 el.up().up().previous().show();
 id = (el.id.replace('editImage_', ''));
 setImageSrc(el, 16, 'success.png');
 $('span_'+id).innerHTML = $('span_'+id).innerHTML.replace(previousName, name);
 el.up().up().up().up().select("a").each(function (s) {s.href = s.href.replace(previousName, name);});
 el.up().up().up().up().select("img").each(function (s) {if (s.hasClassName("error_delete")) {setImageSrc(s, 16, 'edit');}});
}

function deleteFile(el, id) {
 parameters = {'delete':id, method: 'get'};
 ajaxRequest(el, url, parameters, onDeleteFile);
}
function onDeleteFile(el, response) {
 new Effect.Fade(el.up().up());
}
function shareFile(el, id) {
 parameters = {share:id, method: 'get'};
 ajaxRequest(el, url, parameters, onShareFile);
}
function onShareFile(el, response) {
 el.previous().show();
 el.hide();
}

function unshareFile(el, id) {
 parameters = {unshare:id, method: 'get'};
 ajaxRequest(el, url, parameters, onUnshareFile);
}
function onUnshareFile(el, response) {
 el.next().show();
 el.hide();
}

function uncompressFile(el, id) {
 parameters = {uncompress:id, method: 'get'};
 ajaxRequest(el, url, parameters, onUncompressFile);
}
function onUncompressFile(el, response) {
 eF_js_rebuildTable($("filename_"+tableId).down().getAttribute("tableIndex"), 0, "", "desc", $(tableId).getAttribute("currentDir"));
}
function deleteFolder(el, id) {
 parameters = {delete_folder:id, method: 'get'};
 ajaxRequest(el, url, parameters, onDeleteFolder);
}
function onDeleteFolder(el, response) {
 eF_js_rebuildTable($("filename_"+tableId).down().getAttribute("tableIndex"), 0, "", "desc", "");
}
function addUploadBox(el) {
 Element.extend(el);
 var show = false;
 el.up().up().up().select("tr").each(function (s) {
  if (!s.visible() && !show) {
   s.show();
   show = true;
   }
 });
}
function insert_editor(el, id) {
 parameters = {insert_editor_file: 1, file_id: id, method: 'get'};
 var url = location.toString();
 ajaxRequest(el, url, parameters, onInsertEditor);
}
function onInsertEditor(el, response) {
    if (tinyMCEmode) {
  tinyMCE.activeEditor.execCommand('mceInsertContent', false, response);
    } else {
     insertatcursor($(tinyMCE.activeEditor.id), response);
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
function toggleFileManager(el) {
 Element.extend(el);
    if (!$('filemanager_div').visible()) {
     setImageSrc(el.previous(), 16, "navigate_up");
     //el.previous().src = "themes/default/images/16x16/navigate_up.png";
        $('filemanager_div').show();
     $('filemanager_cell').setStyle({width:$('filemanager_div').getDimensions().width+'px', height:$('filemanager_div').getDimensions().height+'px', verticalAlign:'top'});
     $('filemanager_div').absolutize().clonePosition($('filemanager_cell'));
    } else {
     //el.previous().src = "themes/default/images/16x16/navigate_down.png";
     setImageSrc(el.previous(), 16, "navigate_down");
        $('filemanager_div').hide().relativize();
        $('filemanager_cell').setStyle({height: '0px'});
    }
}
function toogleEditorMode(sEditorID) {
    try {
        if(tinyMCEmode) {
            tinyMCE.removeMCEControl(tinyMCE.getEditorId(sEditorID));
            tinyMCEmode = false;
        } else {
            mceAddControlDynamic(sEditorID, 'editor_data' ,'templateEditor');
            tinyMCEmode = true;
        }
    } catch(e) {
        alert('editor error');
    }
}
