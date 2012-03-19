/**
 * $Id: editor_plugin_src.js 520 2008-01-07 16:30:32Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2008, Moxiecode Systems AB, All rights reserved.
 */

(function() {
	tinymce.create('tinymce.plugins.IframePlugin', {
		init : function(ed, url) {
			// Register commands
			ed.addCommand('mceIframe', function() {
				ed.windowManager.open({
					file : url + '/iframe.php',
					width : 800 + parseInt(ed.getLang('iframe.delta_width', 0)),
					height : 450 + parseInt(ed.getLang('iframe.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('iframe', {
				title : 'iframe.iframe_desc',
				cmd : 'mceIframe',
				image : url + '/img/iframe.png'
			});

			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('iframe', n.nodeName == 'IFRAME');
			});

			ed.onClick.add(function(ed, e) {
				e = e.target;

				if (e.nodeName === 'IFRAME')
					ed.selection.select(e);
			});
		},

		getInfo : function() {
			return {
				longname : 'iframe',
				author : 'Fusion InterMedia',
				authorurl : 'http://wwwfusionintermedia.com',
				infourl : 'http://wwwfusionintermedia.com',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('iframe', tinymce.plugins.IframePlugin);
})();