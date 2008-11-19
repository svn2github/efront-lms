/**
 * $Id: editor_plugin_src.js 163 2007-01-03 16:31:00Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2007, Moxiecode Systems AB, All rights reserved.
 */

/* Import plugin specific language pack */
tinyMCE.importPluginLanguagePack('mathtype');

// Plucin static class
var TinyMCE_MathtypePlugin = {
	getInfo : function() {
		return {
			longname : 'Mathtype',
			author : 'Moxiecode Systems AB',
			authorurl : 'http://tinymce.moxiecode.com',
			infourl : 'http://tinymce.moxiecode.com/tinymce/docs/plugin_mathtype.html',
			version : tinyMCE.majorVersion + "." + tinyMCE.minorVersion
		};
	},

	/**
	 * Returns the HTML contents of the emotions control.
	 */
	getControlHTML : function(cn) {
		switch (cn) {
			case "mathtype":
				return tinyMCE.getButtonHTML(cn, 'lang_mathtype_desc', '{$pluginurl}/images/mathtype.gif', 'mceMathtype');
		}

		return "";
	},

	/**
	 * Executes the mceEmotion command.
	 */
	execCommand : function(editor_id, element, command, user_interface, value) {
		// Handle commands
		switch (command) {
			case "mceMathtype":
				var template = new Array();

				template['file'] = '../../plugins/mathtype/mathtype.php'; // Relative to theme
				template['width'] = 600;
				template['height'] = 600;

				// Language specific width and height addons
				template['width'] += tinyMCE.getLang('lang_mathtype_delta_width', 0);
				template['height'] += tinyMCE.getLang('lang_mathtype_delta_height', 0);

				tinyMCE.openWindow(template, {editor_id : editor_id,resizable : "yes", scrollbars : "yes", inline : "yes"});

				return true;
		}

		// Pass to next handler in chain
		return false;
	}
};

// Register plugin
tinyMCE.addPlugin('mathtype', TinyMCE_MathtypePlugin);
