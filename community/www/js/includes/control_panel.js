function showBorders(event) {
	var el = Event.extend(event).element();
	Event.observe(el, 'mousemove', function (s) {
		//The 10 threshold is put here due to an IE bug, which creates a move event along with the mousedown
		if (Math.abs(event.pointerX() - s.pointerX()) > 10) {
			$('first_empty').show();
			$('second_empty').show();
			Event.stopObserving(el, 'mousemove');
		}
	});
}
function hideBorders(event) {
	var el = Event.extend(event).element();
	$('first_empty').hide();
	$('second_empty').hide();
	Event.stopObserving(el, 'mousemove');
}


function createSortable(list) {
	Sortable.create(list, {
		containment:["firstlist", "secondlist"], constraint:false,
		onUpdate: function() {
			new Ajax.Request('set_positions.php', {
				method:'post',
				asynchronous:true,
				parameters: { firstlist: Sortable.serialize('firstlist'), secondlist: Sortable.serialize('secondlist') },
				onSuccess: function (transport) {}
			});
	}});	
}
createSortable('firstlist');
createSortable('secondlist');
