function showVersionFileDetails() {
 el = $('download_version');
 url = location.toString();
 parameters = {request_file:true, method:'get'};
 ajaxRequest(el, url, parameters, onShowVersionFileDetails, onProcessFailure);
}
function onShowVersionFileDetails(el, response) {
 try {
  values = response.evalJSON(true);

  $('version_contact_server').hide();
  $('version_filename').update(values.filename);
  $('version_filesize').update(Math.round(values.size/1024)+' KB');
  $('version_file_details').show();
 } catch (e) {alert(e);}
}
function downloadVersionFile(el) {
 url = location.toString();
 parameters = {download_file:true, method:'get'};
 ajaxRequest(el, url, parameters, onDownloadVersionFile, onProcessFailure);

 $('progress_cell').show();
}
function onDownloadVersionFile(el, response) {
 try {
  values = response.evalJSON(true);
  $('progress_message').update('Checking file system permissions...');
  checkPermissions(el);
 } catch (e) {alert(e);}
}
function checkPermissions(el) {
 url = location.toString();
 parameters = {check_filesystem:true, method:'get'};
 ajaxRequest(el, url, parameters, onCheckPermissions, onProcessFailure);
}
function onCheckPermissions(el, response) {
 try {
  values = response.evalJSON(true);
  $('progress_message').update('Locking site and uncompressing downloaded archive...');
  installVersionFile(el);
 } catch (e) {alert(e);}
}
function installVersionFile(el) {
 url = location.toString();
 parameters = {uncompress_file:true, method:'get'};
 ajaxRequest(el, url, parameters, onInstallVersionFile, onProcessFailure);
}
function onInstallVersionFile(el, response) {
 try {
  $('progress_message').update('Upgrading...');
  autoUpgrade(el);
 } catch (e) {alert(e);}
}
function autoUpgrade(el) {
 url = "install/install.php";
 parameters = {unattended:1, upgrade:1, ajax:1, method:'get'};
 ajaxRequest(el, url, parameters, onAutoUpgrade, onProcessFailure);
}
function onAutoUpgrade(el, response) {
 try {
  if (response.evalJSON(true) && response.evalJSON(true).status == 1) {
   $('progress_message').update('Unlocking site...');
   unlockSite(el);
  } else {
   throw(1);
  }
  //location = location.toString()+'&unlock=1&message=Upgrade completed successfully&message_type=success';
 } catch (e) {
  $('progress_message').update('');
  $('progress_cell').hide();
  alert('An unknown problem emerged during upgrade. Please upgrade the system manually');
 }
}
function unlockSite(el) {
 url = location.toString();
 parameters = {unlock:true, method:'get'};
 ajaxRequest(el, url, parameters, onUnlockSite, onProcessFailure);
}
function onUnlockSite(el, response) {
 try {
  $('progress_message').update('');
  $('progress_cell').hide();
  $('finished_cell').show();
 } catch (e) {alert(e);}
}

function onProcessFailure(el, response) {
 try {
  alert(response);
  $('progress_message').update('');
  $('progress_cell').hide();
  $('version_file_details').hide();
  $('version_contact_server').show();
  eF_js_showDivPopup('', '');
 } catch (e) {alert(e);}
}
