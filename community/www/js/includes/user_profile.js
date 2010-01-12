function activateField(el, field) {
	if (el.className.match('red')) {
    	parameters = {activate_field:field, method: 'get'};
	} else {
		parameters = {deactivate_field:field, method: 'get'};
	}
    var url    = location.toString();
    ajaxRequest(el, url, parameters, onActivateField);
}
function onActivateField(el, response) {
    if (response == 0) {
    	setImageSrc(el, 16, "trafficlight_red.png");
        el.writeAttribute({alt:activate, title:activate});
        el.up().up().addClassName('deactivatedTableElement');
    } else if (response == 1) {
    	setImageSrc(el, 16, "trafficlight_green.png");
        el.writeAttribute({alt:deactivate, title:deactivate});
        el.up().up().removeClassName('deactivatedTableElement');
    }
}

function deleteField(el, field) {
	parameters = {delete_field:field, method: 'get'};
	var url    = 'administrator.php?ctg=user_profile';
	ajaxRequest(el, url, parameters, onDeleteField);	
}
function onDeleteField(el, response) {
	new Effect.Fade(el.up().up());
}


function addValue() {
	elementCount++;
	$('text_field').insert({before: new Element('tr')
	.insert(new Element('td'))
	.insert(new Element('td')
	.insert(new Element('input', {type: 'text', name:'values['+elementCount+']', id:'values['+elementCount+']'}).toggleClassName('inputText')).insert('&nbsp')
	.insert(new Element('img', {src:'images/16x16/error_delete.png'}).setStyle({verticalAlign:'middle'}).observe('click', function(event) {event.findElement('tr').remove();elementCount--;}))
	.insert(new Element('br')))});
	$('default_value').insert(new Element('option', {value:elementCount}).update(elementCount));
}
function changeType(type) {
	if (type == 'text') {
		$('select_field').hide();
		$('text_field').show();
		$('textarea_field').hide();
	} else if (type == 'select') {
		$('select_field').show();
		$('text_field').hide();
		$('textarea_field').hide();
	} else {
		$('select_field').hide();
		$('text_field').hide();
		$('textarea_field').show();
	}
}
