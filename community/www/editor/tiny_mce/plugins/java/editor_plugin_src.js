/**
 * $Id: editor_plugin_src.js 677 2009-04-08 23:11:41Z makriria $
 *
 * @author makriria
 * @copyright makriria
 */

(function() {
	tinymce.create('tinymce.plugins.JavaPlugin', {
		init : function(ed, url) {
			// Register commands
			ed.addCommand('mceJava', function() {

				if (ed.dom.getAttrib(ed.selection.getNode(), 'class').indexOf('mceItem') != -1)
					return;

				ed.windowManager.open({
					file : url + '/java.php',
					width : 950 + parseInt(ed.getLang('java.delta_width', 0)),
					height : 300 + parseInt(ed.getLang('java.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('java', {
				title : 'java.image_desc',
				cmd : 'mceJava'
			});
		},

		getInfo : function() {
			return {
				longname : 'Java',
				author : 'makriria',
				authorurl : 'http://www.efrontlearning.net',
				infourl : 'http://www.efrontlearning.net',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('java', tinymce.plugins.JavaPlugin);
})();