function showBorders(event) {
 var el = Event.extend(event).element();
 Event.observe(el, 'mousemove', function (s) {
  //The 10 threshold is put here due to an IE bug, which creates a move event along with the mousedown
  if (Math.abs(event.pointerX() - s.pointerX()) > 10) {
   $('first_empty').show();
   $('second_empty').show();
//			$('first_empty').clonePosition(el.up().up(), {setLeft:false, setTop:false}).show();
//			$('second_empty').clonePosition(el.up().up(), {setLeft:false, setTop:false}).show();

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
  //handles: $$('#'+list+' div.block img.blockHeaderTitle1'),
  containment:["firstlist", "secondlist"], constraint:false,
  onUpdate: function() {
  //alert(Sortable.serialize('firstlist'));alert(Sortable.serialize('secondlist'));alert(123);
   new Ajax.Request('set_positions.php', {
    method:'post',
    asynchronous:true,
    parameters: { firstlist: Sortable.serialize('firstlist'), secondlist: Sortable.serialize('secondlist') },
    onSuccess: function (transport) {Sortable.destroy('firstlist');Sortable.destroy('secondlist');},
    onFailure: function (transport) {Sortable.destroy('firstlist');Sortable.destroy('secondlist');alert(decodeURIComponent(transport.responseText));}
   });
   //Sortable.destroy('firstlist');Sortable.destroy('secondlist');
 }
 });
}
//createSortable('firstlist');
//createSortable('secondlist');
