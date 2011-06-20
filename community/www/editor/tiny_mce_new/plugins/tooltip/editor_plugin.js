/**
 * $Id: editor_plugin_src.js  2011-05-20 16:30:32Z makriria $
 *
 * @author makriria
 * @copyright www.efrontlearning.net
 */

(function() {
	tinymce.create('tinymce.plugins.TooltipPlugin', {
		init : function(ed, url) {
			// Register commands
			ed.addCommand('mceTooltip', function() {
				ed.windowManager.open({
					file : url + '/tooltip.php',
					width : 550 + parseInt(ed.getLang('tooltip.delta_width', 0)),
					height : 260 + parseInt(ed.getLang('tooltip.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('tooltip', {
				title : 'tooltip.tooltip_title',
				cmd : 'mceTooltip',
				image : url + '/img/tooltip.png'
			});
		},

		getInfo : function() {
			return {
				longname : 'Tooltip',
				author : 'makriria',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('tooltip', tinymce.plugins.TooltipPlugin);
})();