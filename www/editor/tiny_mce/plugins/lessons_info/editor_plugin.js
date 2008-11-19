/**
 * $Id: editor_plugin_src.js 162 2007-01-03 16:16:52Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2007, Moxiecode Systems AB, All rights reserved.
 */

/* Import plugin specific language pack */
tinyMCE.importPluginLanguagePack('lessons_info');

var TinyMCE_InsertLessonsInfoPlugin = {
    getInfo : function() {
        return {
            longname : 'Insert lessons info link',
            author : 'makriria',
            authorurl : 'www.efront.gr',
            version : tinyMCE.majorVersion + "." + tinyMCE.minorVersion
        };
    },

    /**
     * Returns the HTML contents of the lessons_info plugin.
     */
    getControlHTML : function(cn) {
        switch (cn) {
            case "lessons_info":
                return tinyMCE.getButtonHTML(cn, 'lang_lessons_info_desc', '{$pluginurl}/images/board.png', 'mceInsertLessonsInfo');
        }

        return "";
    },

    /**
     * Executes the mceLessonsInfo command.
     */
    execCommand : function(editor_id, element, command, user_interface, value) {
        /* Adds zeros infront of value */




        // Handle commands
        switch (command) {
            case "mceInsertLessonsInfo":
                tinyMCE.execInstanceCommand(editor_id, 'mceInsertContent', false, '<a href="lessons_info.php">'+ tinyMCE.getLang("lang_lessons_info_linkdescription")+'</a>');
                return true;

        }

        // Pass to next handler in chain
        return false;
    }
};

tinyMCE.addPlugin("lessons_info", TinyMCE_InsertLessonsInfoPlugin);
