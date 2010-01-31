function convertScorm(el, id) {
	var url = location.toString();
	var parameters = {set_type:true, id:id, method: 'get'};
    ajaxRequest(el, url, parameters, onConvertScorm);
}
function onConvertScorm(el, response) {
	response = response.evalJSON(true);

	if ($('tree_image_'+response.id)) {
		if (response.ctg_type == 'scorm_test') {
			setImageSrc($('tree_image_'+response.id), 16, 'tests.png');
			setImageSrc(el, 16, 'theory.png');
		} else if (response.ctg_type == 'scorm') {
			setImageSrc($('tree_image_'+response.id), 16, 'theory.png');
			setImageSrc(el, 16, 'tests.png');
		}
	}	
}

function resetScorm(el, id, login) {
	var url = location.toString();
	var parameters = {reset_scorm:true, id:id, login: login, method: 'get'};
    ajaxRequest(el, url, parameters, onResetScorm);	
}
function onResetScorm(el, response) {
}

function deleteData(el, id) {
	var url = location.toString();
	var parameters = {scorm_review:1, 'delete':id, method: 'get'};
    ajaxRequest(el, url, parameters, onDeleteData);
}
function onDeleteData(el, response) {
	new Effect.Fade(el.up().up());
}
function aaa() {
    Element.extend(el);
    url = 'professor.php?ctg=scorm&scorm_review=1&delete='+id;
    el.down().src = 'images/others/progress1.gif';
    new Ajax.Request(url, {
            method:'get',
            asynchronous:true,
            onFailure: function (transport) {
                el.down().writeAttribute({src:'images/16x16/error_delete.png', title: transport.responseText}).hide();
                new Effect.Appear(el.down().identify());
                window.setTimeout('Effect.Fade("'+el.down().identify()+'")', 10000);
            },
            onSuccess: function (transport) {
                new Effect.Fade(el.up().up());
                }
        });

}
