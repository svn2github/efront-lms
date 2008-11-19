/**
 * $Id: editor_plugin_src.js 162 2007-01-03 16:16:52Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2007, Moxiecode Systems AB, All rights reserved.
 */

/* Import plugin specific language pack */
tinyMCE.importPluginLanguagePack('index_link');

var TinyMCE_InsertIndexLinkPlugin = {
    getInfo : function() {
        return {
            longname : 'Insert index link',
            author : 'makriria',
            authorurl : 'www.efront.gr',
            version : tinyMCE.majorVersion + "." + tinyMCE.minorVersion
        };
    },

    /**
     * Returns the HTML contents of the index_link plugin.
     */
    getControlHTML : function(cn) {
        switch (cn) {
            case "index_link":
                return tinyMCE.getButtonHTML(cn, 'lang_index_link_desc', '{$pluginurl}/images/link.png', 'mceInsertIndexLink');
        }

        return "";
    },

    /**
     * Executes the mceIndexLink command.
     */
    execCommand : function(editor_id, element, command, user_interface, value) {
        /* Adds zeros infront of value */




        // Handle commands
        switch (command) {
            case "mceInsertIndexLink":
                tinyMCE.execInstanceCommand(editor_id, 'mceInsertContent', false, '<a href="index.php?index_efront">'+ tinyMCE.getLang("lang_index_link_linkdescription")+'</a>');
                return true;

        }

        // Pass to next handler in chain
        return false;
    }
};

tinyMCE.addPlugin("index_link", TinyMCE_InsertIndexLinkPlugin);
