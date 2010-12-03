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
    parameters: { dashboard:true, firstlist: Sortable.serialize('firstlist'), secondlist: Sortable.serialize('secondlist') },
    onSuccess: function (transport) {Sortable.destroy('firstlist');Sortable.destroy('secondlist');},
    onFailure: function (transport) {Sortable.destroy('firstlist');Sortable.destroy('secondlist');alert(decodeURIComponent(transport.responseText));}
   });
 }
 });
}

// Used for op=people

function changePeopleDisplay(category, el) {
    Element.extend(el);
    if (category == "all") {
        newUrl = phpSelf + "?ctg=social&op=people&display=1&";
        var td_element = el.up();

        td_element.setStyle({backgroundColor:'#D3D3D3', fontWeight:'bold'});


        siblings = td_element.previousSiblings();
        for (i =0 ; i < siblings.length; i++) {
            siblings[i].setStyle({backgroundColor:'#F7F7F7', fontWeight:'normal'});
        }
        siblings = td_element.nextSiblings();
        for (i =0 ; i < siblings.length; i++) {
            siblings[i].setStyle({backgroundColor:'#F7F7F7', fontWeight:'normal'});
        }
        td_element.setStyle({backgroundColor:'#D3D3D3', fontWeight:'bold'});

        sortBy = 'surname';
        sortOrder = 'asc';
    } else if (category == "recently_changed") {
        newUrl = phpSelf + "?ctg=social&op=people&";

        el.up().setStyle({backgroundColor:'#D3D3D3', fontWeight:'bold'});
        var td_element = el.up();
        siblings = td_element.nextSiblings();
        for (i =0 ; i < siblings.length; i++) {
            siblings[i].setStyle({backgroundColor:'#F7F7F7', fontWeight:'normal'});
        }

        sortBy = 'timestamp';
        sortOrder = 'asc';
    } else if (category == "current_lesson") {
        newUrl = phpSelf + "?ctg=social&op=people&display=2&";

        var td_element = el.up();

        siblings = td_element.previousSiblings();
        for (i =0 ; i < siblings.length; i++) {
            siblings[i].setStyle({backgroundColor:'#F7F7F7', fontWeight:'normal'});
        }
        td_element.setStyle({backgroundColor:'#D3D3D3', fontWeight:'bold'});
        sortBy = 'surname';
        sortOrder = 'asc';
    }


    // Update all form tables
    var tables = sortedTables.size();
    var i;
    for (i = 0; i < tables; i++) {
    if (sortedTables[i].id == 'peopleTable') {
        ajaxUrl[i] = newUrl;
        eF_js_rebuildTable(i, 0, sortBy, sortOrder);
    }
    }
}


function updatePeopleInformation(el, user1, user2) {
    Element.extend(el);
    url = 'ask_information.php?common_lessons=1&user1='+user1+'&user2=' + user2;
    el.select('span').each(function (s) {
        if (s.hasClassName('tooltipSpan') && s.empty()) {
            s.insert(new Element('img').writeAttribute({src:'images/others/progress1.gif'}).addClassName('progress')).setStyle({height:'50px'});
            new Ajax.Request(url, {
                method:'get',
                asynchronous:true,
                onSuccess: function (transport) {
                //alert(transport.responseText);
                s.setStyle({height:'auto', width:'auto'}).update(transport.responseText);
                }
            });
        }
    });
}


// Messages functions
function deleteMessage(el, id) {
 parameters = {'delete':id, method: 'get'};
 var url = phpSelf + "?ctg=messages&ajax=1&delete="+id;
 ajaxRequest(el, url, parameters, onDeleteMessage);
}
function onDeleteMessage(el, response) {
 if (location.toString().match('view')) {
  location = location.toString().replace(/&view=\d*/, '');
 } else {
  new Effect.Fade(el.up().up());
 }
}
function onDeleteFolder(el, response) {
 new Effect.Fade(el.up().up());
}
function flag_unflag(el, id) {
 parameters = {flag:id, method: 'get'};
 var url = phpSelf + "?ctg=messages&ajax=1&flag="+id;
 ajaxRequest(el, url, parameters, onFlagUnflag);
}
function onFlagUnflag(el, response) {
 if (response == '1') {
  setImageSrc(el, 16, 'flag_red');
 } else {
  setImageSrc(el, 16, 'flag_green');
 }
}
