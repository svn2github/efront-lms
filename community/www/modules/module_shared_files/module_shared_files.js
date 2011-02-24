function moduleSharedFiles(el, obj) {
 ajaxRequest(el, location.toString(), {'method':'get', 'ajax':1, 'share_file':obj}, onModuleSharedFiles);
}
function onModuleSharedFiles(el, response) {
 if (response.evalJSON(true).status) {
  if (response.evalJSON(true).added) {
   setImageSrc(el, 16, 'trafficlight_green');
  } else {
   setImageSrc(el, 16, 'trafficlight_red');
  }
 }

}
