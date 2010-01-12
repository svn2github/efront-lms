function activate(el, moduleId) {
	Element.extend(el);
	var src = Element.down(el).src;
	src.match(/_gray/) ? url = 'administrator.php?ctg=social&ajax=1&activate='+moduleId : url = 'administrator.php?ctg=social&ajax=1&deactivate='+moduleId;
	Element.down(el).blur();
	Element.down(el).setAttribute('src', 'images/others/progress_big.gif');
	new Ajax.Request(url, {
		method:'get',
		asynchronous:true,
		onSuccess: function (transport) {
		if (transport.responseText.lastIndexOf("!") > 0) {
			response = transport.responseText.substr(0,1);
			force_sidebar_reload = 1;
		} else {
			response = transport.responseText;
			force_sidebar_reload = 0;
		}

		if (response != "1") {
			table = $('social_tableId');
			var els = table.down().getElementsByTagName("td");
			var elementsArray = new Array();
			for (var i = 0; i < els.length; i++) {
				if(els[i].className == "iconTableTD") {
					elementsArray.push(els[i]);
				}
			}
			if (response == "2") {
				//// to let JS know to change the display of the "all options" icon to activated

				Element.extend(elementsArray[0]);
				elementsArray[0].down().down().setAttribute('src', src.replace(/_gray/, ''));
				elementsArray[0].down().setStyle({color:'black'});
			} else if (response == "3") {
				//// to let JS know to change the display of the "all options" icon to be deactivated
				Element.extend(elementsArray[0]);
				Element.down(elementsArray[0].down()).setAttribute('src', src.replace(/.png/, '_gray.png'));
				elementsArray[0].down().setStyle({color:'gray'});

			} else if (response == "4") {
				//// to let JS know to change display of all icons to activated
				for (i = 0; i < elementsArray.length; i++) {
					Element.extend(elementsArray[i]);
					Element.down(elementsArray[i].down()).setAttribute('src', src.replace(/_gray/, ''));
					elementsArray[i].down().setStyle({color:'black'});
				}
			} else if (response == "5") {
				//// to let JS know to change display of all icons to deactivated
				for (i = 0; i < elementsArray.length; i++) {
					Element.extend(elementsArray[i]);
					Element.down(elementsArray[i].down()).setAttribute('src', src.replace(/.png/, '_gray.png'));
					elementsArray[i].down().setStyle({color:'gray'});
				}
			}

		}

		if (src.match(/_gray/)) {
			Element.down(el).setAttribute('src', src.replace(/_gray/, ''));
			el.setStyle({color:'black'});
		} else {
			Element.down(el).setAttribute('src', src.replace(/.png/, '_gray.png'));
			el.setStyle({color:'gray'});
		}

		// Only a few options trigger sidebar reload for the administrator
		if (force_sidebar_reload) {
			parent.sideframe.location = parent.sideframe.location + '?sbctg=control_panel';
		}

	}
	});
}

var additional_categories_hidden = 1;
var additional_categories_lock   = 1;
function show_hide_social_categories() {
	if (additional_categories_lock) {
		additional_categories_lock = 0;
		if (additional_categories_hidden) {
			additional_categories_hidden = 0;
			$('social_arrow_down').hide();
			$('social_arrow_up').setStyle("display:block;");
			new Effect.toggle( $('social_options'),'BLIND',{queue:{scope:'myscope', position:'end', limit: 2}, duration:1.0});
		} else {
			additional_categories_hidden = 1;
			$('social_arrow_up').hide();
			$('social_arrow_down').setStyle("display:block;");
			new Effect.toggle( $('social_options'),'BLIND',{queue:{scope:'myscope', position:'end', limit: 2}, duration:1.0});
		}
	}
	setTimeout(function(){ additional_categories_lock = 1;}, 1001);

}			

var additional_categories_hidden_r = new Array(); 
var additional_categories_lock_r = new Array(); 
additional_categories_hidden_r['social'] = 1;
additional_categories_lock_r['social'] = 1;
additional_categories_hidden_r['fb'] = 1;
additional_categories_lock_r['fb'] = 1;
function show_hide_categories(category) {
	if (additional_categories_lock_r[category]) {
		additional_categories_lock_r[category] = 0;
		if (additional_categories_hidden_r[category]) {
			additional_categories_hidden_r[category] = 0;
			$(category+'_arrow_down').hide();
			$(category+'_arrow_up').setStyle("display:block;");
			new Effect.toggle( $(category+'_options'),'BLIND',{queue:{scope:'myscope', position:'end', limit: 2}, duration:1.0});
		} else {
			additional_categories_hidden_r[category] = 1;
			$(category+'_arrow_up').hide();
			$(category+'_arrow_down').setStyle("display:block;");
			new Effect.toggle( $(category+'_options'),'BLIND',{queue:{scope:'myscope', position:'end', limit: 2}, duration:1.0});
		}
	}
	setTimeout(function(){ additional_categories_lock_r[category] = 1;}, 1001);

}			



