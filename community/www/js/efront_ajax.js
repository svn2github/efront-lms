/**
 * Make an AJAX request
 * 
 * You can use this function in order to make AJAX requests. The request is made using 'post' method,
 * in the designated URL. Optionally, 2 additional callback function may be specified, 'callbackSuccess' 
 * and 'callbackFailure', the former to be called on success and the latter on failure. Both these
 * functions are called using the original el parameter and the ajax's responseText
 * NOTE: the onFailure part is called only when the server responds with a header 500.
 * Example:
 * 
 * @param el The element that triggered the AJAX request
 * @param url The URL that the request will be directed to
 * @param parameters The post parameters, as a JSON object
 * @param callbackSuccess a function name to be used as callback after success
 * @param callbackFailure a function name to be used as callback after failure
 * @since 3.6.0
 */
function ajaxRequest(el, url, parameters, callbackSuccess, callbackFailure, asynchronous) {
	if (typeof(asynchronous) == 'undefined') {
		asynchronous = true;
	}
	if (typeof(parameters.ajax) == 'undefined') {
		Object.extend(parameters, {ajax:'ajax'});
	}
	this.showProgress = function (el) {
		var progressImage = new Element('span').addClassName('progress').setStyle({background:'url("themes/default/images/others/progress1.gif")'});
		el.writeAttribute({progressImage: progressImage.identify()});
		
		if (el.tagName.toLowerCase() == 'img') {
			//Bigger progress icon for bigger icons
			if (el.getDimensions().width == '32') {
				var progressImage = new Element('span').addClassName('progress32').setStyle({background:'url("themes/default/images/others/progress_big.gif")'});
				el.writeAttribute({progressImage: progressImage.identify()});
			}
			el.setStyle({visibility:'hidden'}).up().insert(progressImage);
			progressImage.clonePosition(el);
		} else if (el.tagName.toLowerCase() == 'input' || el.tagName.toLowerCase() == 'select') {
			progressImage.setStyle({marginTop:(el.getDimensions().height-16)/2+'px'});
			el.up().insert(progressImage);
		} else if (el.tagName.toLowerCase() == 'a') {/*Do nothing, anchors take care of themselves*/}
		
		
		return progressImage;
	};
	this.hideProgress = function(el) {
		progressImage = $(el.readAttribute('progressImage'));
		if (el.tagName.toLowerCase() == 'img') {
			progressImage.remove();
			el.setStyle({visibility: 'visible'});	
		} else if (el.tagName.toLowerCase() == 'input' || el.tagName.toLowerCase() == 'select') {
			progressImage.setStyle({background:''}).addClassName('sprite16 sprite16-success');
			new Effect.Fade(progressImage, {afterFinish:function (s) {progressImage.remove();}});			
		}
	};
	
	Element.extend(el);	
	showProgress(el);

	if (parameters.method) {
		parameters.method == 'get' ? method = 'get' : method = 'post';
		delete parameters.method;
	} else {
		method = 'post';
	}
	
	new Ajax.Request(url, {		//@todo:validate input client-side
	        method: method,
	        asynchronous:asynchronous,
	        parameters: parameters,
			onFailure: function(transport) {
				hideProgress(el);
				if (callbackFailure) {
					callbackFailure(el, transport.responseText);
				} else {
					alert(decodeURIComponent(transport.responseText));
				}
			},
			onSuccess: function (transport) {
				hideProgress(el);
				if (callbackSuccess) {
					callbackSuccess(el, transport.responseText);
				} 
			}
	});
	
	
}
